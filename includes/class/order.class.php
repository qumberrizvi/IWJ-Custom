<?php
class IWJ_Order{
    static $cache = array();

    public $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    /**
     * @param null $post
     * @param bool $force
     * @return IWJ_Order|null
     */
    static function get_order($post = null, $force = false){
        $post_id = 0;
        if($post === null){
            $post = get_post();
        }
        if(is_numeric($post)){
            $post = get_post($post);
            if($post && !is_wp_error($post)){
                $post_id = $post->ID;
            }
        }
        elseif(is_object($post))
        {
            $post_id = $post->ID;
        }

        if(isset($post->post_type) && $post->post_type != 'iwj_order'){
            return null;
        }

        if($post_id){

            if($force){
                clean_post_cache( $post_id );
                $post = get_post($post_id);
            }

            if($force || !isset(self::$cache[$post_id])){
                self::$cache[$post_id] = new IWJ_Order($post);
            }

            return self::$cache[$post_id];
        }

        return null;
    }
    
    public function get_id(){

        return $this->post->ID;
    }

    public function get_title(){

        return get_the_title($this->post->ID);
    }

    public function get_type(){

        return get_post_meta($this->post->ID, IWJ_PREFIX.'type', true);
    }

    public function get_price(){

        return get_post_meta($this->post->ID, IWJ_PREFIX.'price', true);
    }

    public function get_tax_price(){
        return get_post_meta($this->post->ID, IWJ_PREFIX.'tax_price', true);
    }

    public function get_tax_value(){
        return get_post_meta($this->post->ID, IWJ_PREFIX.'tax_value', true);
    }

    public function has_tax(){
        return get_post_meta($this->post->ID, IWJ_PREFIX.'use_tax', true);
    }

    public function get_sub_price(){
        $type = $this->get_type();
        if($type == '1' || $type == '4' || $type == '6'){
            return $this->get_package_price();
        }
        if($type == '2'){
            return $this->get_featured_price();
        }
        if($type == '3'){
            return $this->get_renew_price();
        }

        return 0;
    }

    public function get_currency(){

        return get_post_meta($this->post->ID, IWJ_PREFIX.'currency', true);
    }

    public function get_package_price(){

        return get_post_meta($this->post->ID, IWJ_PREFIX.'package_price', true);
    }

    public function get_featured_price(){

        return get_post_meta($this->post->ID, IWJ_PREFIX.'featured_price', true);
    }

    public function get_renew_price(){

        return get_post_meta($this->post->ID, IWJ_PREFIX.'renew_price', true);
    }

    public function get_package_id(){
        return get_post_meta($this->post->ID, IWJ_PREFIX.'package_id', true);
    }

    public function get_package(){
        $package_id = $this->get_package_id();

        if($this->get_type() == '4'){
            return IWJ_Resume_Package::get_package($package_id);
        }

	    if($this->get_type() == '6'){
		    return IWJ_Apply_Job_Package::get_package($package_id);
	    }

	    if($this->get_type() == 'plan'){
		    return IWJ_Plan::get_package($package_id);
	    }

        return IWJ_Package::get_package($package_id);
    }

    public function get_package_title(){
        return get_post_meta($this->post->ID, IWJ_PREFIX.'package_title', true);
    }

    public function get_job_id(){

        return get_post_meta($this->post->ID, IWJ_PREFIX.'job_id', true);
    }

    public function get_job(){

        $job_id = $this->get_job_id();
        return IWJ_Job::get_job($job_id);
    }

    public function get_job_title(){

        $job = $this->get_job();
        if($job){
            return $job->get_title();
        }

        return '';
    }

    public function get_user_package_id(){

        return get_post_meta($this->post->ID, IWJ_PREFIX.'user_package_id', true);
    }

    public function get_user_package(){

        return IWJ_User_Package::get_user_package($this->get_user_package_id());
    }

    public function get_author_id(){
        return $this->post->post_author;
    }

    public function get_author(){
        return IWJ_User::get_user($this->get_author_id());
    }

    public function get_created( $format = '' ){
        $created = $this->post->post_date;
        if($created && $format){
            return date_i18n($format, strtotime($created));
        }

        return $created;
    }

    public function get_purchased_date(){
        return get_post_meta($this->post->ID, IWJ_PREFIX.'purchased_date', true);
    }

    public function get_key(){
        return get_post_meta($this->post->ID, IWJ_PREFIX.'key', true);
    }

    public function featured_job(){
        return get_post_meta($this->post->ID, IWJ_PREFIX.'featured_job', true);
    }

    public function completed_order($note = '', $send_email = true, $send_admin_email = true){
        $this->change_status('iwj-completed', $note, $send_email, $send_admin_email);
        update_post_meta($this->get_id(), IWJ_PREFIX.'purchased_date', current_time('timestamp'));
        $type = $this->get_type();
        switch ($type){
            case '1':
                $user_package = IWJ_User_Package::get_user_package($this->get_user_package_id());
                $job = $this->get_job();
                if($user_package){
                    $user_package->purchased();
                    $package = $user_package->get_package();
                    if($package){
                        if($job){
                            if($job->get_status() != 'publish'){
                                update_post_meta( $job->get_id(), IWJ_PREFIX . 'is_new_publish', '1' );
                                if ( $package->is_free() ) {
                                    update_post_meta( $job->get_id(), IWJ_PREFIX . 'free_job', '1' );
                                    if ( iwj_option( 'new_free_job_auto_approved' ) ) {
                                        $job->change_status( 'publish', false );
                                    } else {
                                        $job->change_status( 'pending' );
                                    }
                                }else{
                                    if(iwj_option('new_job_auto_approved')){
                                        $job->change_status('publish');
                                    }else{
                                        $job->change_status('pending');
                                    }
                                }
                            }
                        }
                    }
                }

                break;
            case '2':
                $job = $this->get_job();
                if($job){
                    update_post_meta($job->get_id(), IWJ_PREFIX.'is_new_featured', '1');
                    if($job->has_status('publish')){
                        //set featured
                        $job->set_featured();
                    }
                }
                break;
            case '3':
                $job = $this->get_job();
                if($job){
                    $job->renew();
                    $job->set_publish(true);
                }
            break;
            case '4':
            case '6':
                $user_package = IWJ_User_Package::get_user_package($this->get_user_package_id());
                if($user_package){
                    $package = $user_package->get_package();
                    if($package){
                        $user_package->purchased();
                    }
                }
                break;
            case '5':
                $job = $this->get_job();
                if($job){
                    $job->change_status('publish');
                }
                break;
            case 'plan':
                $package = $this->get_package();
                if($package){
                    $user_id = $this->get_author_id();
                    $user = IWJ_User::get_user($user_id);
                    if($user){
                        $user->set_plan($package->get_id());
                        $job = $this->get_job();
                        if($job && $job->get_status() != 'publish'){
                            update_post_meta( $job->get_id(), IWJ_PREFIX . 'is_new_publish', '1' );
                            if(iwj_option('new_job_auto_approved')){
                                $job->change_status('publish', false);
                            }else{
                                $job->change_status('pending');
                            }
                        }
                    }
                }
                break;
        }
    }

    public function cancelled_order($note = '', $send_email = true){
        $this->change_status('iwj-cancelled', $note, $send_email);
        $type = $this->get_type();
        switch ($type){
            case '1':
                $user_package = IWJ_User_Package::get_user_package($this->get_user_package_id());
                if($user_package){
                    wp_delete_post($user_package->get_id());
                }

                if($this->get_job_id()){
                    wp_delete_post($this->get_job_id());
                }

                break;
            case '4':
            case '6':
                $user_package = IWJ_User_Package::get_user_package($this->get_user_package_id());
                if($user_package){
                    wp_delete_post($user_package->get_id());
                }
                break;
        }
    }

    public function get_status(){
        return $this->post->post_status;
    }

    public function has_status($check_status){
        $check_status = !is_array($check_status) ? (array)$check_status : $check_status;
        $status = $this->get_status();
        $status = str_replace('iwj-', '', $status);
        if(in_array($status, $check_status)){
            return true;
        }

        return false;
    }

    public function change_status($status, $note = '' , $send_email = true, $send_email_admin = true){
        $old_status = $this->get_status();

        global $wpdb;
        $sql = "UPDATE {$wpdb->posts} SET post_status = %s WHERE ID = %d";
        $wpdb->query($wpdb->prepare($sql, $status, $this->get_id()));
        if($old_status != $status){
            $transition_note = sprintf( __( 'Order status changed from %1$s to %2$s.', 'iwjob' ), $this->get_status_title($old_status), $this->get_status_title($status) );
        }else{
            $transition_note = sprintf( __( 'Order status set to %s.', 'iwjob' ), $this->get_status_title($status));
        }

        $note = $note ? $note . ' '.$transition_note : $transition_note;
        $this->add_order_note($note);

        //send email
        if($send_email){
            if($status == 'iwj-completed'){
                IWJ_Email::send_email('completed_order', $this);
            }elseif($status == 'iwj-hold'){
                IWJ_Email::send_email('hold_order', $this);
            }
        }

        if($send_email_admin){
            if($status == 'iwj-completed' || $status == 'iwj-hold'){
                IWJ_Email::send_email('new_order_admin', $this);
            }
        }
    }

    public function can_pay($key){
        if($this->get_author_id() != get_current_user_id()){
            return new WP_Error(1, __('You do not have permission to pay this order.', 'iwjob'));
        }

        if($key != $this->get_key()){
            return new WP_Error(2, __('Invalid key.', 'iwjob'));
        }

        if(($this->get_type() == 2 || $this->get_type() == 3) && !$this->get_job()){
            return new WP_Error(3, __('Sorry, you can not pay this order because miss job related.', 'iwjob'));
        }

        if(($this->get_type() == 1 || $this->get_type() == 4 || $this->get_type() == 6) && !$this->get_package() && !$this->get_user_package()){
            return new WP_Error(3, __('Sorry can not pay this order because miss package related.', 'iwjob'));
        }

        return true;
    }

    public function can_view(){
        if($this->get_author_id() == get_current_user_id()){
            return true;
        }

        return false;
    }
    
    public function get_received_url($tab) {
        if($tab){
            $args = array(
                'iwj_tab' => $tab,
                'step' => 'done',
                'order_id'=> $this->get_id(),
                'key'=> $this->get_key(),
            );
            switch ($tab){
                case 'new-job':
                    $args['job-id'] = $this->get_job_id();
                    break;
                case 'new-package':
                case 'new-resume-package':
                case 'new-apply-job-package':
                    $args['user_package_id'] = $this->get_user_package_id();
                    break;
            }
        }else{
            $args = array(
                'iwj_tab' => 'thankyou',
                'order_id'=> $this->get_id(),
                'key'=> $this->get_key(),
            );
        }

        $order_received_url = add_query_arg( $args, iwj_get_page_permalink( 'dashboard' ) );

        return $order_received_url;
    }

    public function get_pay_url(){
        $pay_url = add_query_arg( array(
            'iwj_tab'=>'pay-order',
            'order_id'=> $this->get_id(),
            'key'=> $this->get_key(),
        ), iwj_get_page_permalink( 'dashboard' ) );

        return $pay_url;
    }

    public function get_cancel_url($redirect = '') {
        $cancel_url = add_query_arg( array(
            'iwj_cancel_order'=> 'true',
            'order_id'=> $this->get_id(),
            'key'=> $this->get_key(),
            'redirect'=> $redirect ? '?iwj_tab='.$redirect : add_query_arg(array('iwj_tab'=>'orders'), iwj_get_page_permalink( 'dashboard' )),
        ), iwj_get_page_permalink( 'dashboard' ) );

        return $cancel_url;
    }

    public function add_order_note( $note, $is_customer_note = 0, $added_by_user = false ) {
        if ( ! $this->get_id() ) {
            return 0;
        }

        if ( is_user_logged_in() && current_user_can('manage_options') && $added_by_user ) {
            $user                 = get_user_by( 'id', get_current_user_id() );
            $comment_author       = $user->display_name;
            $comment_author_email = $user->user_email;
        } else {
            $comment_author       = __( 'iwjob', 'iwjob' );
            $comment_author_email = strtolower( __( 'iwjob', 'iwjob' ) ) . '@';
            $comment_author_email .= isset( $_SERVER['HTTP_HOST'] ) ? str_replace( 'www.', '', $_SERVER['HTTP_HOST'] ) : 'noreply.com';
            $comment_author_email = sanitize_email( $comment_author_email );
        }
        $commentdata = apply_filters( 'iwj_new_order_note_data', array(
            'comment_post_ID'      => $this->get_id(),
            'comment_author'       => $comment_author,
            'comment_author_email' => $comment_author_email,
            'comment_author_url'   => '',
            'comment_content'      => $note,
            'comment_agent'        => 'iwjob',
            'comment_type'         => 'order_note',
            'comment_parent'       => 0,
            'comment_approved'     => 1,
        ), array( 'order_id' => $this->get_id(), 'is_customer_note' => $is_customer_note ) );

        $comment_id = wp_insert_comment( $commentdata );

        if ( $is_customer_note ) {
            add_comment_meta( $comment_id, 'is_customer_note', 1 );
            IWJ_Email::send_email('customer_note', array('order' => $this, 'note' => $note));
        }

        return $comment_id;
    }

    static function get_status_array(){
        return array(
            'iwj-pending-payment' => __('Pending payment', 'iwjob'),
            'iwj-completed' => __('Compeleted', 'iwjob'),
            'iwj-cancelled' => __('Canceled', 'iwjob'),
            'iwj-hold' => __('Hold', 'iwjob'),
        );
    }

    static public function get_status_title($status){
        $status_array = self::get_status_array();
        return isset($status_array[$status]) ? $status_array[$status] : '';
    }

    static public function get_type_array(){
        $types = array(
            '1' => __('Package', 'iwjob'),
            '5' => __('Submit Job', 'iwjob'),
            '2' => __('Feature Job', 'iwjob'),
            '3' => __('Renew Job', 'iwjob'),
            '4' => __('Resume Package', 'iwjob'),
            '6' => __('Apply Class Package', 'iwjob'),
            'plan' => __('Plan', 'iwjob'),
        );

        return $types;
    }

    static public function get_type_title($type){
        $types = self::get_type_array();
        if(isset($types[$type])){
            return $types[$type];
        }

        return '';
    }

    public function get_payment_description(){
        $type = $this->get_type();
        switch ($type){
            case '1' :
                return sprintf(__('Package %s', 'iwjob'), $this->get_package_title());
            case '2' :
                return sprintf(__('Feature job %s', 'iwjob'), $this->get_job_title());
            case '3' :
                return sprintf(__('Renew job %s', 'iwjob'), $this->get_job_title());
            case '4' :
            case '6' :
                return sprintf(__('Package %s', 'iwjob'), $this->get_package_title());
        }

        return sprintf(__('Payment Order %s', 'iwjob'), $this->get_id());
    }

    public function get_payment_method_id(){
        return get_post_meta($this->post->ID, IWJ_PREFIX.'payment_method', true);
    }

    public function get_payment_method(){
        return IWJ()->payment_gateways->get_payment_gateway($this->get_payment_method_id());
    }

    public function get_payment_method_title(){
        return get_post_meta($this->post->ID, IWJ_PREFIX.'payment_method_title', true);
    }

    public function get_view_link(){
        $dashboard_url = iwj_get_page_permalink('dashboard');
        return add_query_arg(array('iwj_tab' => 'view-order', 'order_id'=>$this->get_id()), $dashboard_url);
    }

    public function get_admin_url(){
        return get_admin_url().'post.php?post='.$this->get_id().'&action=edit';
    }

    public function use_package_featured(){
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'use_package_featured', true) ? true : false;
    }

    static function add_new($args = array()){
        $default_args = array(
            'type' => '1',
            'package_id' => '',
            'job_id' => '',
            'package_price' => '',
            'featured_price' => '',
            'user_package_id' => '',
            'use_package_featured' => '',
            'status' => 'iwj-pending-payment',
        );
        $args = wp_parse_args($args, $default_args);

        $post_args = array(
            'post_title' => '',
            'post_type' => 'iwj_order',
            'post_status' => $args['status'],
        );

        $post_id = wp_insert_post($post_args);
        if($post_id) {
            wp_update_post(array(
                'ID' => $post_id,
                'post_title' => sprintf(__('Order #%d', 'iwjob'), $post_id)
            ));

            update_post_meta($post_id, IWJ_PREFIX . 'status', '');
            update_post_meta($post_id, IWJ_PREFIX . 'type', $args['type']);
            update_post_meta($post_id, IWJ_PREFIX . 'key', wp_generate_password(12, false));
            update_post_meta($post_id, IWJ_PREFIX . 'currency', iwj_get_system_currency());

            //1 package price
            //2 featured price
            //3 renew job
            //4 view resume package
            //5 single job price
            if($args['type'] == '1'){
                $total_price = $args['package_price'];
                $tax_price = iwj_get_tax_price($total_price);
                if($tax_price !== false){
                    $total_price += $tax_price;
                    $tax_value = iwj_option('tax_value');
                    update_post_meta($post_id, IWJ_PREFIX . 'use_tax', 1);
                    update_post_meta($post_id, IWJ_PREFIX . 'tax_value', $tax_value);
                    update_post_meta($post_id, IWJ_PREFIX . 'tax_price', $tax_price);
                }
                update_post_meta($post_id, IWJ_PREFIX . 'price', $total_price);
                update_post_meta($post_id, IWJ_PREFIX . 'package_price', $args['package_price']);
                update_post_meta($post_id, IWJ_PREFIX . 'package_id', $args['package_id']);
                update_post_meta($post_id, IWJ_PREFIX . 'package_title', sanitize_text_field(get_the_title($args['package_id'])));
                update_post_meta($post_id, IWJ_PREFIX . 'user_package_id', $args['user_package_id']);
                update_post_meta($post_id, IWJ_PREFIX . 'job_id', $args['job_id']);
            }elseif($args['type'] == '2'){
                $total_price = $args['featured_price'];
                $tax_price = iwj_get_tax_price($total_price);
                if($tax_price !== false){
                    $total_price += $tax_price;
                    $tax_value = iwj_option('tax_value');
                    update_post_meta($post_id, IWJ_PREFIX . 'use_tax', 1);
                    update_post_meta($post_id, IWJ_PREFIX . 'tax_value', $tax_value);
                    update_post_meta($post_id, IWJ_PREFIX . 'tax_price', $tax_price);
                }
                update_post_meta($post_id, IWJ_PREFIX . 'price', $total_price);
                update_post_meta($post_id, IWJ_PREFIX . 'featured_price', $args['featured_price']);
                update_post_meta($post_id, IWJ_PREFIX . 'job_id', $args['job_id']);
            }elseif($args['type'] == '3'){
                $total_price = $args['job_price'];
                $tax_price = iwj_get_tax_price($total_price);
                if($tax_price !== false){
                    $total_price += $tax_price;
                    $tax_value = iwj_option('tax_value');
                    update_post_meta($post_id, IWJ_PREFIX . 'use_tax', 1);
                    update_post_meta($post_id, IWJ_PREFIX . 'tax_value', $tax_value);
                    update_post_meta($post_id, IWJ_PREFIX . 'tax_price', $tax_price);
                }
                update_post_meta($post_id, IWJ_PREFIX . 'price', $total_price);
                update_post_meta($post_id, IWJ_PREFIX . 'renew_price', $args['job_price']);
                update_post_meta($post_id, IWJ_PREFIX . 'job_id', $args['job_id']);
            }elseif($args['type'] == '4' || $args['type'] == '6'){
                $total_price = $args['package_price'];
                $tax_price = iwj_get_tax_price($total_price);
                if($tax_price !== false){
                    $total_price += $tax_price;
                    $tax_value = iwj_option('tax_value');
                    update_post_meta($post_id, IWJ_PREFIX . 'use_tax', 1);
                    update_post_meta($post_id, IWJ_PREFIX . 'tax_value', $tax_value);
                    update_post_meta($post_id, IWJ_PREFIX . 'tax_price', $tax_price);
                }
                update_post_meta($post_id, IWJ_PREFIX . 'price', $total_price);
                update_post_meta($post_id, IWJ_PREFIX . 'package_price', $args['package_price']);
                update_post_meta($post_id, IWJ_PREFIX . 'package_id', $args['package_id']);
                update_post_meta($post_id, IWJ_PREFIX . 'package_title', sanitize_text_field(get_the_title($args['package_id'])));
                update_post_meta($post_id, IWJ_PREFIX . 'user_package_id', $args['user_package_id']);
            }elseif($args['type'] == '5'){
                $total_price = iwj_option('job_price');
                $tax_price = iwj_get_tax_price($total_price);
                if($tax_price !== false){
                    $total_price += $tax_price;
                    $tax_value = iwj_option('tax_value');
                    update_post_meta($post_id, IWJ_PREFIX . 'use_tax', 1);
                    update_post_meta($post_id, IWJ_PREFIX . 'tax_value', $tax_value);
                    update_post_meta($post_id, IWJ_PREFIX . 'tax_price', $tax_price);
                }
                update_post_meta($post_id, IWJ_PREFIX . 'price', $total_price);
                update_post_meta($post_id, IWJ_PREFIX . 'job_price', iwj_option('job_price'));
                update_post_meta($post_id, IWJ_PREFIX . 'job_id', $args['job_id']);
            }

            $order = IWJ_Order::get_order($post_id);
            IWJ_Email::send_email('new_order', $order);
        }

        return $post_id;
    }

    /**
     * @param IWJ_Cart $cart
     * @return int|WP_Error
     */
    static function create_new($cart, $args = array()){

        $post_args = array(
            'post_title' => '',
            'post_type' => 'iwj_order',
            'post_status' =>  isset($args['post_status']) ? $args['post_status'] : 'iwj-pending-payment',
            'post_author' => isset($args['post_author']) ? $args['post_author'] : get_current_user_id()
        );

        $post_id = wp_insert_post($post_args);
        if($post_id) {
            wp_update_post(array(
                'ID' => $post_id,
                'post_title' => sprintf(__('Order #%d', 'iwproperty'), $post_id)
            ));


            $order = self::get_order($post_id);
            $order->update_cart_info($cart);

        }

        return $post_id;
    }

    /**
     * @param IWJ_Cart $cart
     */
    function update_cart_info($cart){
        $post_id = $this->get_id();
        $order_type = $cart->get('type');
        update_post_meta($post_id, IWJ_PREFIX . 'type', $order_type);
        update_post_meta($post_id, IWJ_PREFIX . 'package_id', $cart->get('item'));
        update_post_meta($post_id, IWJ_PREFIX . 'key', wp_generate_password(12, false));
        update_post_meta($post_id, IWJ_PREFIX . 'currency', iwj_get_system_currency());
        update_post_meta($post_id, IWJ_PREFIX . 'price', $cart->get('price'));
        update_post_meta($post_id, IWJ_PREFIX . 'total_price', $cart->get_total_price());
        $use_tax = iwj_option('tax_used');
        if($use_tax){
            update_post_meta($post_id, IWJ_PREFIX . 'use_tax', 1);
            update_post_meta($post_id, IWJ_PREFIX . 'tax_price', $cart->get_tax_price());
        }

        switch ($order_type){
            case 'plan' :
                $package = IWJ_Plan::get_package($cart->get('item'));
                if($package){
                    update_post_meta($post_id, IWJ_PREFIX . 'package_title', $package->get_title());
                }

                $job_id = $cart->get('job_id');
                if($job_id){
                    update_post_meta($post_id, IWJ_PREFIX . 'job_id', $job_id);
                    update_post_meta($job_id, IWJ_PREFIX . 'order_id', $post_id);
                }
                break;
        }
    }
    
    static function remove_order_reference($order_id){
        if($order_id && get_post_type($order_id) == 'iwj_order'){
           $post = get_post($order_id);
           if($post->post_status == 'iwj-pending-payment'){
               $order = IWJ_Order::get_order($post);
               if($order->get_job_id()){
                   wp_delete_post($order->get_job_id());
               }

               $get_user_package_id = $order->get_user_package_id();
               if($get_user_package_id){
                   wp_delete_post($get_user_package_id);
               }

               return true;
           }
        }

        return false;
    }
}