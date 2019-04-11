<?php

class IWJ_Gateway_Paypal extends IWJ_Payment_Gateway{

    function __construct(){

        parent::__construct();

        add_action( 'wp_ajax_iwj_paypal_ipn', array($this, 'ipn_check_response'));
        add_action( 'wp_ajax_nopriv_iwj_paypal_ipn', array($this, 'ipn_check_response') );
        if(iwj_option('submit_job_mode') == '3'){
            add_action( 'iwj_checkout_before_payment_btn', array( $this, 'before_payment_btn' ) );
            add_action( 'iwj_user_admin_plan_info', array($this, 'user_admin_package_info'));
            add_action( 'iwj_admin_order_after_payment_method', array($this, 'order_after_payment_method'));
        }
    }

    function get_title(){
        return __('Paypal', 'iwjob');
    }

    function get_description(){
        return $this->get_option('description');
    }

    function get_icon(){
        return IWJ_PLUGIN_URL.'/assets/img/paypal.png';
    }

    function admin_option_fields(){
        $settings = array(
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
                'std'		    => 'Pay via PayPal; you can pay with your credit card if you don\'t have a PayPal account.',
            ),
            array(
                'id' 			=> 'sandbox',
                'name'			=> __( 'Sandbox' , 'iwjob' ),
                'desc'	        => __( 'Sandbox method.', 'iwjob' ),
                'type'			=> 'select',
                'options'		=> array(
                    '1' => __('Yes', 'iwjob'),
                    '0' => __('No', 'iwjob'),
                ),
                'std'		    => '1',
            ),
            array(
                'id' 			=> 'email',
                'name'			=> __( 'Paypal email' , 'iwjob' ),
                'type'			=> 'text',
                'std'		=> '',
            ),
            array(
                'id' 			=> 'identity_token',
                'name'			=> __( 'Identity Token' , 'iwjob' ),
                'desc'	=> __( 'Using for PDT Notification', 'iwjob' ),
                'type'			=> 'text',
                'std'		=> '',
            ),
        );

        if(iwj_option('submit_job_mode') == '3'){
            $settings = array_merge($settings, array(
                array(
                    'id' 			=> 'username',
                    'name'			=> __( 'Paypal API Full Name' , 'iwproperty' ),
                    'type'			=> 'text',
                    'std'		=> '',
                ),
                array(
                    'id' 			=> 'password',
                    'name'			=> __( 'Paypal API Password' , 'iwproperty' ),
                    'type'			=> 'text',
                    'std'		=> '',
                ),
                array(
                    'id' 			=> 'signature',
                    'name'			=> __( 'Paypal API Signature' , 'iwproperty' ),
                    'type'			=> 'text',
                    'std'		=> ''
                ),
            ));
        }

        return $settings;
    }

    public function is_available(){

        if($this->get_option('enable') && $this->get_option('email')){
            return true;
        }

        return false;
    }

    function get_payment_gateway_url(){
        if ($this->get_option('sandbox')) {
            return "https://www.sandbox.paypal.com/cgi-bin/webscr";
        } else {
            return "https://www.paypal.com/cgi-bin/webscr";
        }
    }

    public function get_ipn_url(){
        $url = home_url('/'). 'wp-admin/admin-ajax.php?action=iwj_paypal_ipn';
        return $url;
    }

    function process_payment($order_id, $tab = 'new-job'){
        $output = '';
        $order = IWJ_Order::get_order($order_id);
        if($order){
            if(isset($_POST['paypal_recurring']) && $_POST['paypal_recurring'] && $order->get_type() == 'plan'){
                $custom[] = $order->get_author_id();
                $custom[] = $order->get_package_id();
                $package = $order->get_package();
                $p3 = $package->get_expiry();
                $_t3 = $package->get_expiry_unit();
                $t3 = $_t3 == 'year' ? 'Y' : ($_t3 == 'month' ? 'M' : 'D');
                $product_name = $order->get_payment_description() .' - '.$package->get_title().' - '.iwj_system_price($order->get_price(), $order->get_currency()).' - '.$package->get_expiry_title();
                $output .= '<form name="PayPalForm" id="iwp-paypal-form" action="' . $this->get_payment_gateway_url() . '" method="post">  
                            <input type="hidden" name="cmd" value="_xclick-subscriptions">  
                            <input type="hidden" name="business" value="' . sanitize_email($this->get_option('email')) . '">
                            <input type="hidden" name="item_name" value="' . sanitize_text_field($product_name) . '"> 
                            <input type="hidden" name="currency_code" value="' . $order->get_currency() . '">
                            <input type="hidden" name="item_number" value="' . $order->get_id() . '">  
                            <input type="hidden" name="custom" value="' . sanitize_text_field(implode("_",$custom)). '">  
                            <input type="hidden" name="no_note" value="1">  
                            <input type="hidden" name="notify_url" value="' . sanitize_text_field($this->get_ipn_url()) . '">
                            <input type="hidden" name="a3" value="' . $order->get_price() . '">
                            <input type="hidden" name="p3" value="'.$p3.'">
                            <input type="hidden" name="t3" value="'.$t3.'">
                            <input type="hidden" name="src" value="1">
                            <input type="hidden" name="cancel_return" value="' . esc_url($order->get_cancel_url()) . '">  
                            <input type="hidden" name="return" value="' . esc_url($this->get_received_url($order)) . '">  
                        </form>';
                echo $output;
                echo '<script>
				  	document.getElementById("iwp-paypal-form").submit();
				  </script>';
            }else{
                $business_email = $this->get_option('email');
                $currency = iwj_get_currency();
                $price = $order->get_price();
                $title = $order->get_title();
                $return_url = $this->get_received_url($order, $tab);
                $cancel_url = $order->get_cancel_url('jobs');
                $output .= '<form name="PayPalForm" id="iwj-paypal-form" action="' . $this->get_payment_gateway_url() . '" method="post">  
                            <input type="hidden" name="cmd" value="_xclick">  
                            <input type="hidden" name="business" value="' . sanitize_email($business_email) . '">
                            <input type="hidden" name="amount" value="' . $price . '">
                            <input type="hidden" name="item_name" value="' . sanitize_text_field($title) . '"> 
                            <input type="hidden" name="currency_code" value="' . $currency . '">
                            <input type="hidden" name="item_number" value="' . $order->get_id() . '">  
                            <input type="hidden" name="custom" value="' . $order->get_key(). '">  
                            <input name="cancel_return" value="' . esc_url($cancel_url) . '" type="hidden">  
                            <input type="hidden" name="no_note" value="1">  
                            <input type="hidden" name="notify_url" value="' . sanitize_text_field($this->get_ipn_url()) . '">
                            <input type="hidden" name="lc">
                            <input type="hidden" name="rm" value="'.(is_ssl() ? 2 : 1).'">
                            <input type="hidden" name="return" value="' . esc_url($return_url) . '">  
                        </form>';
                echo $output;
                echo '<script>
				  	document.getElementById("iwj-paypal-form").submit();
				  </script>';
            }
        }

        die;
    }

    /**
     * Check Response for PDT.
     */
    public function pdt_check_response() {
        if ( empty( $_REQUEST['item_number'] ) || empty( $_REQUEST['tx'] ) || empty( $_REQUEST['st'] ) ) {
            return false;
        }

        $order_id    = iwj_clean( stripslashes( $_REQUEST['item_number'] ) );
        $status      = iwj_clean( strtolower( stripslashes( $_REQUEST['st'] ) ) );
        $amount      = iwj_clean( stripslashes( $_REQUEST['amt'] ) );
        $transaction = iwj_clean( stripslashes( $_REQUEST['tx'] ) );

        if ( ! ( $order = IWJ_Order::get_order( $order_id ) ) || $order->get_status() != 'iwj-pending-payment' ) {
            return false;
        }

        $transaction_info = $this->get_paypal_transaction_info($transaction);
        if($transaction_info){
            update_post_meta( $order->get_id(), '_transaction_id', $transaction );
            $status = strtolower($transaction_info['PAYMENTSTATUS']);
            update_post_meta( $order->get_id(), '_paypal_status', $status );
            if ( 'completed' === $status ) {
                $order->completed_order();
                if(isset($transaction_info['SUBSCRIPTIONID']) && $transaction_info['SUBSCRIPTIONID'] && $order->get_type() == 'plan'){
                    $old_subscr_id = get_user_meta($order->get_author_id(), IWJ_PREFIX.'paypal_subscr_id', true);
                    $old_payer_email = get_user_meta($order->get_author_id(), IWJ_PREFIX.'paypal_payer_email', true);

                    if($old_payer_email != $transaction_info['EMAIL']){
                        update_user_meta($order->get_author_id(), IWJ_PREFIX.'paypal_payer_email', $transaction_info['EMAIL']);
                    }

                    if($old_subscr_id &&  $old_subscr_id != $transaction_info['SUBSCRIPTIONID']){
                        $this->cancel_paypal_subscription($old_subscr_id);
                        update_user_meta($order->get_author_id(), IWJ_PREFIX.'paypal_subscr_id', $transaction_info['SUBSCRIPTIONID']);
                    }elseif(!$old_subscr_id){
                        update_user_meta($order->get_author_id(), IWJ_PREFIX.'paypal_subscr_id', $transaction_info['SUBSCRIPTIONID']);
                    }

                    //cancel subscription form other gateways
                    $user = $order->get_author();
                    $user->cancel_subscription($this->id);
                }
            }
        }else{
            $transaction_result = $this->pdt_validate_transaction( $transaction );
            update_post_meta( $order->get_id(), '_paypal_status', $status );
            update_post_meta( $order->get_id(), '_transaction_id', $transaction );
            if ( $transaction_result ) {
                if ( 'completed' === $status ) {
                    if ( $order->get_price() != $amount ) {
                        //IWJ_Log::add( 'Payment error: Amounts do not match (amt ' . $amount . ')', 'error' );
                        $order->change_status('iwj-hold', __( 'Validation error: PayPal amounts do not match (amt %s).', 'iwjob' ));
                    } else {
                        $order->completed_order(__( 'PDT payment completed.', 'iwjob' ));
                    }
                } else {
                    if ( 'authorization' === $transaction_result['pending_reason'] ) {
                        $order->change_status('iwj-hold', __( 'Payment authorized. Change payment status to processing or complete to capture funds.', 'iwjob' ));
                    } else {
                        $order->change_status('iwj-hold', sprintf( __( 'Payment pending (%s).', 'iwjob' ), $transaction_result['pending_reason'] ));
                    }
                }
            }
        }

        return true;
    }

    //pdt validate
    protected function pdt_validate_transaction( $transaction ) {
        $pdt = array(
            'body' 			=> array(
                'cmd' => '_notify-synch',
                'tx'  => $transaction,
                'at'  => $this->get_option('identity_token'),
            ),
            'timeout' 		=> 60,
            'httpversion'   => '1.1',
            'user-agent'	=> 'IWJ/',
        );

        // Post back to get a response.
        $response = wp_safe_remote_post( $this->get_option('sandbox') ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr', $pdt );

        if ( is_wp_error( $response ) || ! strpos( $response['body'], "SUCCESS" ) === 0 ) {
            return false;
        }

        // Parse transaction result data
        $transaction_result  = array_map( 'iwj_clean', array_map( 'urldecode', explode( "\n", $response['body'] ) ) );
        $transaction_results = array();

        foreach ( $transaction_result as $line ) {
            $line                            = explode( "=", $line );
            $transaction_results[ $line[0] ] = isset( $line[1] ) ? $line[1] : '';
        }

        if ( ! empty( $transaction_results['charset'] ) && function_exists( 'iconv' ) ) {
            foreach ( $transaction_results as $key => $value ) {
                $transaction_results[ $key ] = iconv( $transaction_results['charset'], 'utf-8', $value );
            }
        }

        return $transaction_results;
    }

    protected function get_paypal_order( $raw_custom ) {
        // We have the data in the correct format, so get the order.
        if ( ( $custom = json_decode( $raw_custom ) ) && is_object( $custom ) ) {
            $order_id  = $custom->order_id;
            $order_key = $custom->order_key;

            // Fallback to serialized data if safe. This is @deprecated in 2.3.11
        } elseif ( preg_match( '/^a:2:{/', $raw_custom ) && ! preg_match( '/[CO]:\+?[0-9]+:"/', $raw_custom ) && ( $custom = maybe_unserialize( $raw_custom ) ) ) {
            $order_id  = $custom[0];
            $order_key = $custom[1];

            // Nothing was found.
        } else {
            IWJ_Log::add( 'Order ID and key were not found in "custom".', 'error' );
            return false;
        }

        $order = IWJ_Order::get_order( $order_id );
        if ( !$order  ) {
            IWJ_Log::add( 'Order '.$order_id.' not found.', 'error' );
        }

        if ( ! $order || $order->get_order_key() !== $order_key ) {
            IWJ_Log::add( 'Order '.$order_id.' Keys do not match.', 'error' );
            return false;
        }

        return $order;
    }

    public function ipn_check_response() {
        if ( ! empty( $_POST ) && $this->validate_ipn() ) {
            $posted = wp_unslash( $_POST );

            // @codingStandardsIgnoreStart
            $this->ipn_valid_response($posted );
            // @codingStandardsIgnoreEnd
            exit;
        }

        wp_die( 'PayPal IPN Request Failure', 'PayPal IPN', array( 'response' => 500 ) );
    }

    public function validate_ipn() {
        // Get received values from post data
        $validate_ipn        = wp_unslash( $_POST );
        $validate_ipn['cmd'] = '_notify-validate';

        // Send back post vars to paypal
        $params = array(
            'body'        => $validate_ipn,
            'timeout'     => 60,
            'httpversion' => '1.1',
            'compress'    => false,
            'decompress'  => false,
            'user-agent'  => 'iwjob',
        );

        // Post back to get a response.
        $response = wp_safe_remote_post( $this->get_option('sanbox') ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr', $params );

        // Check to see if the request was valid.
        if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && strstr( $response['body'], 'VERIFIED' ) ) {
            return true;
        }

        //IWJ_Log::add( 'Received invalid response from PayPal : '.$response->get_error_message(), 'error' );

        return false;
    }

    public function ipn_valid_response( $posted ) {

        $txn_type = $posted['txn_type'];
        if($txn_type == 'subscr_payment' && iwj_option('submit_job_mode') == '3'){
            if ( ! empty( $posted['custom'] )){
                // Sandbox fix.
                if ( isset( $posted['test_ipn'] ) && 1 == $posted['test_ipn'] && 'pending' == $posted['payment_status'] ) {
                    $posted['payment_status'] = 'Completed';
                }

                $order = $this->get_paypal_order( $posted['custom']);

                //if has order
                if($order){
                    switch ($posted['payment_status']) {
                        case 'Completed':
                            $old_txn_id = get_post_meta($order->get_id(), IWJ_PREFIX.'paypal_txn_id', true);
                            $old_payer_email = get_user_meta($order->get_author_id(), IWJ_PREFIX.'paypal_payer_email', true);
                            $old_subscr_id = get_user_meta($order->get_author_id(), IWJ_PREFIX.'paypal_subscr_id', true);
                            //in case ipn run before pdt (pdt not settup)
                            if(!$old_txn_id){
                                if(!$order->has_status('completed')){
                                    $order->completed_order();
                                }
                                update_post_meta($order->get_id(), IWJ_PREFIX.'paypal_txn_id', $posted['txn_id']);
                                //pdt run before. update subscr id for user
                            }elseif($old_txn_id == $posted['txn_id']){
                                if(!$order->has_status('completed')){
                                    $order->completed_order();
                                }
                                //continuing paid subscription
                            }else{
                                $cart = new IWJ_Cart();
                                $package = $order->get_package();
                                $cart->set('plan',$package->get_id(), $package->get_price());
                                $new_order_id = IWJ_Order::create_new($cart, array('post_author' => $order->get_author_id()));
                                if($new_order_id){
                                    $new_order = IWJ_Order::get_order($new_order_id);
                                    $new_order->completed_order();
                                }
                            }

                            if($old_payer_email != $posted['payer_email']){
                                update_user_meta($order->get_author_id(), IWJ_PREFIX.'paypal_payer_email', $posted['payer_email']);
                            }

                            if($old_subscr_id && $old_subscr_id != $posted['subscr_id']){
                                $this->cancel_paypal_subscription($old_subscr_id);
                                update_user_meta($order->get_author_id(), IWJ_PREFIX.'paypal_subscr_id', $posted['subscr_id']);
                            }elseif(!$old_subscr_id){
                                update_user_meta($order->get_author_id(), IWJ_PREFIX.'paypal_subscr_id', $posted['subscr_id']);
                            }

                            if(!$old_txn_id) {
                                //cancel subscription form other gateways
                                $user = $order->get_author();
                                $user->cancel_subscription($this->id);
                            }
                            break;
                        case 'Pending':
                            break;
                        case 'Voided':
                            break;
                        case 'Refunded':
                            break;
                    }
                    //create new order for user
                }else{
                    $custom = explode("_", $posted['custom']);
                    if(count($custom) >= 4){
                        $user_id = $custom[3];
                        $package_id = $custom[4];
                        if($user_id && $package_id){
                            $user = IWJ_User::get_user($user_id);
                            $package = IWJ_Package::get_package($package_id);
                            if($user && $package){
                                $old_payer_email = get_user_meta($user_id, IWJ_PREFIX.'paypal_payer_email', true);
                                $old_subscr_id = get_user_meta($user_id, IWJ_PREFIX.'paypal_subscr_id', true);

                                $cart = new IWJ_Cart();
                                $cart->set('plan',$package->get_id(), $package->get_price());
                                $new_order_id = IWJ_Order::create_new($cart, array('post_author' => $order->get_author_id()));
                                if($new_order_id){
                                    $new_order = IWJ_Order::get_order($new_order_id);
                                    $new_order->completed_order();
                                    update_post_meta($new_order_id, IWJ_PREFIX.'paypal_txn_id', $posted['txn_id']);
                                }

                                if($old_payer_email != $posted['payer_email']){
                                    update_user_meta($order->get_author_id(), IWJ_PREFIX.'paypal_payer_email', $posted['payer_email']);
                                }
                                if($old_subscr_id != $posted['subscr_id']){
                                    $this->cancel_paypal_subscription($old_subscr_id);
                                    update_user_meta($order->get_author_id(), IWJ_PREFIX.'paypal_subscr_id', $posted['subscr_id']);
                                }

                                //cancel subscription form other gateways
                                $user->cancel_subscription($this->id);
                            }
                        }
                    }

                }
            }
        }else{
            if ( ! empty( $posted['custom'] ) && ( $order = $this->get_paypal_order( $posted['custom'] ) ) && $order->has_status('pending-payment')) {
                // Lowercase returned variables.
                $posted['payment_status'] = strtolower( $posted['payment_status'] );

                // Sandbox fix.
                if ( isset( $posted['test_ipn'] ) && 1 == $posted['test_ipn'] && 'pending' == $posted['payment_status'] ) {
                    $posted['payment_status'] = 'completed';
                }

                switch ($posted['payment_status']) {
                    case 'Completed':
                        $order->completed_order(__( 'IPN payment completed.', 'iwjob' ));
                        break;
                    case 'Pending':
                        if ( 'authorization' === $posted['pending_reason'] ) {
                            $order->change_status('iwj-hold', __( 'Payment authorized. Change payment status to processing or complete to capture funds.', 'iwjob' ));
                        } else {
                            $order->change_status('iwj-hold', sprintf( __( 'Payment pending (%s).', 'iwjob' ), $posted['pending_reason'] ));
                        }
                        break;
                    case 'Voided':
                        $order->cancelled_order();
                        break;
                    case 'Refunded':
                        break;
                }
            }
        }
    }

    function payment_recieved($order, $tab){
        //txn_type
        if ( !empty( $_REQUEST['item_number'])) {
            $order_id    = iwj_clean( stripslashes( $_REQUEST['item_number'] ) );
            if($this->get_option('identity_token')){
                $this->pdt_check_response();
            }

            $order = IWJ_Order::get_order($order_id);
            $dashboard = iwj_get_page_permalink('dashboard');
            if($order){
                wp_redirect($order->get_received_url($tab));
            }else{
                wp_redirect($dashboard);
            }

            exit;
        }
    }


    function get_paypal_transaction_info($transaction_id){
        if(!$transaction_id || !$this->get_option('username') || !$this->get_option('password') || !$this->get_option('signature')){
            return false;
        }

        $req = array(
            'USER'      => urlencode($this->get_option('username')),
            'PWD'  => urlencode($this->get_option('password')),
            'SIGNATURE' => urlencode($this->get_option('signature')),
            'VERSION'   => '76.0',
            'METHOD'    => 'GetTransactionDetails',
            'TRANSACTIONID' => urlencode($transaction_id),
        );

        $ch = curl_init();

        // Swap these if you're testing with the sandbox
        if($this->get_option('sandbox')){
            curl_setopt($ch, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
        }else{
            curl_setopt($ch, CURLOPT_URL, 'https://api-3t.paypal.com/nvp');
        }
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($req));
        $response = curl_exec($ch);
        curl_close($ch);

        parse_str( $response, $result );

        if(isset($result['ACK']) && $result['ACK'] == 'Success'){
            return $result;
        }
        else{
            return false;
        }
    }

    function cancel_paypal_subscription($paypal_subscr_id){
        if(!$paypal_subscr_id){
            return false;
        }

        $req = array(
            'USER'      => urlencode($this->get_option('username')),
            'PWD'  => urlencode($this->get_option('password')),
            'SIGNATURE' => urlencode($this->get_option('signature')),
            'VERSION'   => '76.0',
            'METHOD'    => 'ManageRecurringPaymentsProfileStatus',
            'PROFILEID' => urlencode($paypal_subscr_id),
            'ACTION'    => 'Cancel',
            'NOTE'      => urlencode(sprintf(__('User cancelled membership on %s', 'iwproperty'), get_bloginfo('name'))),
        );

        $ch = curl_init();

        // Swap these if you're testing with the sandbox
        if($this->get_option('sandbox')){
            curl_setopt($ch, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
        }else{
            curl_setopt($ch, CURLOPT_URL, 'https://api-3t.paypal.com/nvp');
        }
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($req));
        $response = curl_exec($ch);
        curl_close($ch);

        parse_str( $response, $result );
        if(isset($result['ACK']) && $result['ACK'] == 'Success'){
            return $result;
        }
        else{
            return false;
        }
    }

    function cancel_subscription($user_id){
        $subscr_id = get_user_meta($user_id, IWJ_PREFIX.'paypal_subscr_id', true);
        if($subscr_id){
            if($this->cancel_paypal_subscription($subscr_id)){
                delete_user_meta($user_id, IWJ_PREFIX.'paypal_subscr_id');
            }
        }
    }

    function has_subscription($user_id){
        return get_user_meta($user_id, IWJ_PREFIX.'paypal_subscr_id', true);
    }

    public function before_payment_btn($cart){
        $type = $cart->get('type');
        if($type == 'plan'){
            echo '<label class="paypal-recurring hide"><input name="paypal_recurring" value="1" type="checkbox"> Auto recurring payment</label>';
        }
    }

    function user_admin_package_info($user){
        if($user && $user->has_plan()){
            $subsc_id = get_user_meta($user->get_id(), IWJ_PREFIX.'paypal_subscr_id', true);
            if($subsc_id){
                echo '<tr>';
                echo '<th>'.__('Paypal Subscription ID', 'iwproperty').'</th>';
                echo '<td>'.$subsc_id.'</td>';
                echo '</tr>';
                $email = get_user_meta($user->get_id(), IWJ_PREFIX.'paypal_payer_email', true);
                echo '<tr>';
                echo '<th>'.__('Paypal Email', 'iwproperty').'</th>';
                echo '<td>'.$email.'</td>';
                echo '</tr>';
                echo '<tr>';
            }
        }
    }

    function order_after_payment_method($order){
        if($order->get_payment_method_id() == $this->id){
            $order_invoice_id = get_post_meta($order->get_id(), IWJ_PREFIX.'paypal_txn_id', true);
            echo '<tr>';
            echo '<th>'.__('Paypal Transaction ID', 'iwproperty').'</th>';
            echo '<td>'.$order_invoice_id.'</td>';
            echo '</tr>';
        }
    }
}