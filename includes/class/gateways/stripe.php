<?php

class IWJ_Gateway_Stripe extends IWJ_Payment_Gateway{

    function __construct()
    {
        parent::__construct();

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        if(iwj_option('submit_job_mode') == '3') {
            add_action('iwj_checkout_before_payment_btn', array($this, 'before_payment_btn'));
            add_action( 'iwj_user_admin_plan_info', array($this, 'user_admin_package_info'));
            add_action( 'iwj_admin_order_after_payment_method', array($this, 'order_after_payment_method'));
            add_action( 'wp_ajax_nopriv_iwj_stripe_listener', array($this, 'stripe_listener') );
            add_action( 'wp_ajax_iwj_stripe_listener', array($this, 'stripe_listener'));
        }
    }

    function enqueue_scripts(){
        if($this->is_available()){
            wp_register_script('stripe-checkout', 'https://checkout.stripe.com/checkout.js');
            wp_localize_script('stripe-checkout', 'stripe_options', array('publish_key' => $this->get_publish_key()));
        }
    }

    function get_title(){
        return __('Stripe', 'iwjob');
    }

    function get_description(){
        wp_enqueue_script('stripe-checkout');

        return $this->get_option('description');
    }

    function get_icon(){
        return IWJ_PLUGIN_URL.'/assets/img/paypal.png';
    }

    function admin_option_fields(){
        return array(
            array(
                'id' 			=> 'enable',
                'name'			=> __( 'Enable' , 'iwjob' ),
                'type'			=> 'select',
                'options'		=> array(
                    '1' => __('Yes', 'iwjob'),
                    '0' => __('No', 'iwjob'),
                ),
                'std'		    => '0',
            ),
            array(
                'id' 			=> 'description',
                'name'			=> __( 'Description' , 'iwjob' ),
                'type'			=> 'textarea',
                'std'		    => 'Pay with your credit card via Stripe.',
            ),
            array(
                'id' 			=> 'test_mode',
                'name'			=> __( 'Test Mode' , 'iwjob' ),
                'type'			=> 'select',
                'options'		=> array(
                    '1' => __('Yes', 'iwjob'),
                    '0' => __('No', 'iwjob'),
                ),
                'std'		    => '1',
            ),
            array(
                'id' 			=> 'test_secret_key',
                'name'			=> __( 'Test Secret Key' , 'iwjob' ),
                'type'			=> 'text',
                'std'		=> '',
            ),
            array(
                'id' 			=> 'test_publish_key',
                'name'			=> __( 'Test Publishable Key' , 'iwjob' ),
                'type'			=> 'text',
                'std'		=> '',
            ),
            array(
                'id' 			=> 'secret_key',
                'name'			=> __( 'Live Secret Key' , 'iwjob' ),
                'type'			=> 'text',
                'std'		=> '',
            ),
            array(
                'id' 			=> 'publish_key',
                'name'			=> __( 'Live Publishable Key' , 'iwjob' ),
                'type'			=> 'text',
                'std'		=> '',
            ),
        );
    }

    public function is_available(){

        if($this->get_option('enable') && $this->get_secret_key() && $this->get_publish_key()){
            return true;
        }

        return false;
    }

    function is_test_mode(){
        return $this->get_option('test_mode') == '1' ? true : false;
    }

    function get_secret_key(){
        if($this->is_test_mode()){
            return $this->get_option('test_secret_key');
        }

        return $this->get_option('secret_key');
    }

    function get_publish_key(){
        if($this->is_test_mode()){
            return $this->get_option('test_publish_key');
        }

        return $this->get_option('publish_key');
    }

    function process_payment($order_id, $tab = 'new-job'){
        // Create Token for Card or Customer
        if(isset($_POST['stripe_token'])) {
            $order = IWJ_Order::get_order($order_id);
            if($order){
                if(!class_exists('Stripe')){
                    require(dirname(__FILE__) . '/stripe/stripe_init.php');
                }

                \Stripe\Stripe::setApiKey($this->get_secret_key());

                if(isset($_POST['stripe_recurring']) && $_POST['stripe_recurring'] && $order->get_type() == 'plan') {
                    $plan_id = $this->checkStripPlanExists($order);
                    if($plan_id === false){
                        $plan_id = $this->createStripePlans($order);
                    }

                    $token_id = $_POST['stripe_token'];
                    $email = $_POST['stripe_email'];
                    try {
                        $customer_id = $this->checkStripCustomerExists($email);
                        if($customer_id === false){
                            $customer_id = $this->createStripCustomer($email);
                        }

                        $subscription = \Stripe\Subscription::create([
                            'customer' => $customer_id,
                            'items' => [['plan' => $plan_id]],
                            'card' => $token_id,
                            "metadata" => [
                                'order_id' => $order_id,
                                'user_id' => $order->get_author_id(),
                                'package_id' => $order->get_package_id(),
                            ]
                        ]);
                        $old_subscription_id = get_user_meta($order->get_author_id(), IWJ_PREFIX.'stripe_subscription_id', true);
                        $old_email = get_user_meta($order->get_author_id(), IWJ_PREFIX.'stripe_email', true);

                        $subscription_id = $subscription->id;
                        if($old_subscription_id && $subscription_id != $old_subscription_id){
                            $this->cancel_strip_subscription($old_subscription_id);
                        }
                        update_user_meta($order->get_author_id(), IWJ_PREFIX.'stripe_subscription_id', $subscription_id);

                        if(!$old_email || $old_email != $email){
                            update_user_meta($order->get_author_id(), IWJ_PREFIX.'stripe_email', $email);
                        }

                        //update_user_meta($order->get_author_id(), IWJ_PREFIX.'stripe_subscription_ended_at', $subscription->ended_at);
                        //update_user_meta($order->get_author_id(), IWJ_PREFIX.'stripe_customer_id', $customer_id);

                        $order->completed_order();

                        //cancel subscription form other gateways
                        $user = $order->get_author();
                        $user->cancel_subscription($this->id);
                    } catch (Exception $e) {
                        $body = $e->getJsonBody();
                        $error = $body['error']['message'];
                        $order->add_order_note(sprintf(__('Stripe Error. %s', 'iwproperty'), $error));
                        wp_redirect(iwj_get_page_permalink('dashboard'));
                        exit;
                    }
                }else{
                    try {
                        $token_id = $_POST['stripe_token'];
                        $chargeparam = $this->charge_array($order, $token_id);
                        $charge = \Stripe\Charge::create($chargeparam);
                        if ($charge->paid == true) {
                            $note = '';
                            $timestamp = date('Y-m-d H:i:s A e', $charge->created);
                            if ($charge->source->object == "card") {
                                $note = __('Charge ' . $charge->status . ' at ' . $timestamp . ',Charge ID=' . $charge->id . ',Card=' . $charge->source->brand . ' : ' . $charge->source->last4 . ' : ' . $charge->source->exp_month . '/' . $charge->source->exp_year, 'iwjob');
                            }

                            $order->completed_order($note);

                        } else {
                            $order->add_order_note('Charge ' . $charge->status);
                        }

                    } catch (Exception $e) {

                        $body = $e->getJsonBody();
                        $error = $body['error']['message'];
                        $order->add_order_note(__('Stripe Error.' . $error, 'iwjob'));
                    }
                }


                wp_redirect($order->get_received_url($tab));
                exit;
            }
        }

        wp_redirect(get_home_url('/'));
        exit;
    }

    function stripe_listener(){
        if(!class_exists('\Stripe\Stripe')){
            require(dirname(__FILE__) . '/stripe/stripe_init.php');
        }
        \Stripe\Stripe::setApiKey($this->get_secret_key());
        // retrieve the request's body and parse it as JSON

        $body = @file_get_contents('php://input');
        $event_json = json_decode($body);
        if($event_json){
            $event_id = $event_json->id;
            $event = \Stripe\Event::retrieve($event_id);
            if($event->type == 'invoice.payment_succeeded'){
                update_option('_stripe_webhook_payment_succeeded', $body);
                $invoice_id = $event->data->object->id;
                $lines = $event->data->object->lines;
                if($lines && $lines->data){
                    foreach ($lines->data as $line){
                        if($line->type == 'subscription'){
                            $order_id = isset($line->metadata->order_id) ? $line->metadata->order_id : 0;
                            $order = IWJ_Order::get_order((int)$order_id);
                            if($order){
                                $order_stripe_invoice_id = get_post_meta($order_id, IWJ_PREFIX.'stripe_invoice_id', true);
                                if(!$order_stripe_invoice_id){
                                    update_post_meta($order_id, IWJ_PREFIX.'stripe_invoice_id', $invoice_id);
                                    if(!$order->has_status('completed')){
                                        $order->completed_order();
                                    }
                                }else{
                                    if($order_stripe_invoice_id != $invoice_id){
                                        $cart = new IWJ_Cart();
                                        $package = $order->get_package();
                                        $cart->set('plan', $package->get_id(), $package->get_price());
                                        $new_order_id = IWJ_Order::create_new($cart, array('post_author' => $order->get_author_id()));
                                        if($new_order_id){
                                            $new_order = IWJ_Order::get_order($new_order_id);
                                            $new_order->completed_order();
                                            update_post_meta($new_order_id, IWJ_PREFIX.'stripe_invoice_id', $invoice_id);
                                        }else{
                                            //IWJ_Log::add('Can not create order for stripe invoice id'.$invoice_id, 'error');
                                        }
                                    }
                                }
                            }
                            else{
                                $package_id = isset($line->metadata->package_id) ? $line->metadata->package_id : 0;
                                $user_id = isset($line->metadata->user_id) ? $line->metadata->user_id : 0;
                                $package = IWJ_Package::get_package((int)$package_id);
                                $user = IWJ_User::get_user((int)$user_id);
                                if($package && $user){
                                    $cart = new IWJ_Cart();
                                    $package = $order->get_package();
                                    $cart->set('plan', $package->get_id(), $package->get_price());
                                    $new_order_id = IWJ_Order::create_new($cart, array('post_author' => $user_id));
                                    if($new_order_id){
                                        $new_order = IWJ_Order::get_order($new_order_id);
                                        $new_order->completed_order();
                                        update_post_meta($new_order_id, IWJ_PREFIX.'stripe_invoice_id', $invoice_id);
                                    }else{
                                        //IWJ_Log::add('Can not create order for stripe invoice id'.$invoice_id, 'error');
                                    }
                                }else{
                                    //IWJ_Log::add('Can not create order for stripe invoice id'.$invoice_id, 'error');
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    function payment_recieved($order, $tab){
    }

    /**
     * @param IWJ_Order $order
     * @param $token_id
     * @return array
     */
    public function charge_array($order, $token_id){

        $chargearray = array(
            'amount'                    => $this->stripe_order_total($order),
            'currency'                  => $order->get_currency(),
            'capture'                   => false,
            'metadata'                  => array(
                'Order #'               => $order->get_id(),
                //'Total Tax'             => $order->get_total_tax(),
                //'Customer IP'           => $this->get_client_ip(),
                'WP customer #'         => $order->get_author_id(),
            ) ,
            'description'               => get_bloginfo('blogname').' Order #'.$order->get_id(),
        );
        $chargearray['card']      = $token_id;

        return $chargearray ;
    }

    /**
     * @param IWJ_Order $order
     * @return numeric|null
     */
    public function stripe_order_total($order)
    {
        $grand_total    = $order->get_price();
        $currency = '' != $order->get_currency() ? $order->get_currency() : iwj_get_system_currency() ;
        $stripe_zerocurrency = array("BIF","CLP","DJF","GNF","JPY","KMF","KRW","MGA","PYG","RWF","VND","VUV","XAF","XOF","XPF");
        if(in_array($currency ,$stripe_zerocurrency ))
        {
            $amount              = number_format($grand_total,0,".","") ;
        }
        else
        {
            $amount              = $grand_total * 100 ;
        }

        return $amount;
    }



    /**
     * Try and create plans in Stripe
     *
     * @param IWJ_Order $order
     */
    protected function createStripePlans($order)
    {
        $package = $order->get_package();
        $plan_name = $order->get_payment_description() .' - '.$package->get_title().' - '.iwj_system_price($order->get_price(), $order->get_currency()).' - '.$package->get_expiry_title();
        $plan_params = array(
            'id'                   => $package->post->post_name,
            'name'                 => $plan_name,
            'amount'               => $order->get_price() * 100,
            'interval'             => strtolower($package->get_expiry_unit()),
            'currency'             => $order->get_currency(),
        );

        if($package->get_expiry() > 1){
            $plan_params['interval_count'] = $package->get_expiry();
        }

        Stripe\Plan::create($plan_params);

        update_post_meta($package->get_id(), IWJ_PREFIX.'stripe_plan_id', $package->post->post_name);

        return $package->post->post_name;
    }
    
    /**
     * Check if a plan already exists
     *
     * @param IWJ_Order $order
     * @return mixed
     */
    private function checkStripPlanExists($order)
    {
        $package = $order->get_package();
        $plan_id = get_post_meta($package->get_id(), IWJ_PREFIX.'stripe_plan_id', true);
        if(!$plan_id){
            $plan_id = $package->post->post_name;
        }

        try {
            \Stripe\Plan::retrieve($plan_id);
            return $plan_id;
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * Check if a customer already exists
     *
     * @param IWJ_Order $order
     * @return mixed
     */
    private function checkStripCustomerExists($stripe_email)
    {
        $customers = \Stripe\Customer::all(array("limit" => 1, "email" => $stripe_email));
        if($customers && count($customers['data'])){
            return $customers['data'][0]->id;
        }

        return false;
    }

    private function createStripCustomer($stripe_email)
    {
        $customer = \Stripe\Customer::create([
            'email' => $stripe_email,
        ]);

        return $customer['id'];
    }
    
    function cancel_strip_subscription($subscr_id){

        if(!class_exists('\Stripe\Stripe')){
            require(dirname(__FILE__) . '/stripe/stripe_init.php');
        }

        \Stripe\Stripe::setApiKey($this->get_secret_key());
        try {
            $subscription = \Stripe\Subscription::retrieve($subscr_id);
            if($subscription && isset($subscription->status) && $subscription->status != 'canceled'){
                $subscription->cancel();
            }
        } catch (Exception $e) {
        }
    }

    function cancel_subscription($user_id){
        $subscr_id = get_user_meta($user_id, IWJ_PREFIX.'stripe_subscription_id', true);
        if($subscr_id){
            $this->cancel_strip_subscription($subscr_id);
            delete_user_meta($user_id, IWJ_PREFIX.'stripe_subscription_id');
        }
    }

    function has_subscription($user_id){
        return get_user_meta($user_id, IWJ_PREFIX.'stripe_subscription_id', true);
    }

    public function before_payment_btn($cart){
        $type = $cart->get('type');
        if($type == 'plan'){
            echo '<label class="stripe-recurring hide"><input name="stripe_recurring" value="1" type="checkbox"> Auto recurring payment</label>';
        }
    }

    function user_admin_package_info($user){
        if($user && $user->has_plan()){
            $subsc_id = get_user_meta($user->get_id(), IWJ_PREFIX.'stripe_subscription_id', true);
            if($subsc_id){
                echo '<tr>';
                echo '<th>'.__('Stripe Subscription ID', 'iwproperty').'</th>';
                echo '<td>'.$subsc_id.'</td>';
                echo '</tr>';
                $email = get_user_meta($user->get_id(), IWJ_PREFIX.'stripe_email', true);
                echo '<tr>';
                echo '<th>'.__('Stripe Email', 'iwproperty').'</th>';
                echo '<td>'.$email.'</td>';
                echo '</tr>';
            }
        }
    }

    function order_after_payment_method($order){
        if($order->get_payment_method_id() == $this->id){
            $order_stripe_invoice_id = get_post_meta($order->get_id(), IWJ_PREFIX.'stripe_invoice_id', true);
            echo '<tr>';
            echo '<th>'.__('Stripe Invoice ID', 'iwproperty').'</th>';
            echo '<td>'.$order_stripe_invoice_id.'</td>';
            echo '</tr>';
        }
    }
}