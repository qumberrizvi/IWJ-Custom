<?php

/**
 *  File Type: Authorize.net Gateway

 */
if (!class_exists('IWJ_Gateway_Authorizedotnet')) {

    class IWJ_Gateway_Authorizedotnet extends IWJ_Payment_Gateway {

        function get_title(){
            return __('Authorize.net', 'iwjob');
        }

        function get_description(){
            return $this->get_option('description');
        }

        function get_icon(){
            return IWJ_PLUGIN_URL.'/assets/img/athorizedotnet.png';
        }

        function get_payment_gateway_url(){
            if ($this->get_option('working_mode') == '3') {
                return "https://test.authorize.net/gateway/transact.dll";
            } else {
                return "https://secure.authorize.net/gateway/transact.dll";
            }
        }

        public function get_ipn_url($tab){
            $url = home_url('/'). 'wp-admin/admin-ajax.php?action=iwj_authorizedotnet_ipn&tab='.$tab;
            return $url;
        }

        public function admin_option_fields() {
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
                    'std'		    => 'Pay with your credit card via Authorize.net.',
                ),
                array(
                    'id' 			=> 'login_id',
                    'name'			=> __( 'Login ID' , 'iwjob' ),
                    'type'			=> 'text',
                    'std'		=> '',
                ),
                array(
                    'id' 			=> 'transection_key',
                    'name'			=> __( 'Transection Key' , 'iwjob' ),
                    'type'			=> 'text',
                    'std'		=> '',
                ),
                array(
                    'id' => 'hash_key',
                    'name'        => __('MD5 Hash Key', 'iwjob'),
                    'type'         => 'password',
                    'description'  =>  __('MD5 Hash Key is required to validate the response from Authorize.net. Refer: <a href="http://www.indatos.com/developer-documentation/md5-hash-security-feature-authorize-net/?ref=auth-sim" target="_blank">MD5 Security Feature</a> for help.', 'iwjob')
                ),
                array(
                    'id' => 'working_mode',
                    'name'        => __('API Mode', 'iwjob'),
                    'type'         => 'select',
                    'options'      => array('1'=>'Live/Production Mode', '2' => 'Live/Production API in Test Mode', '3'=>'Sandbox/Developer API Mode'),
                    'description'  => "Live or Production / Sandbox Mode" ),
                array(
                    'id' => 'transaction_mode',
                    'name'        => __('Transaction Mode', 'iwjob'),
                    'type'         => 'select',
                    'options'      => array( 'auth_capture'=>'Authorize and Capture', 'authorize'=>'Authorize Only'),
                    'description'  => "Transaction Mode. If you are not sure what to use set to Authorize and Capture" )
            );
        }

        public function is_available(){

            if($this->get_option('enable') && $this->get_option('login_id') && $this->get_option('transection_key')){
                return true;
            }

            return false;
        }

        function payment_recieved($order, $tab){
            if ( count($_POST) && isset($_POST['x_invoice_num']) && $_POST['x_invoice_num']){
                $order = IWJ_Order::get_order($_POST['x_invoice_num']);
                if($order){
                    if ( $_POST['x_response_code'] != '' &&  ($_POST['x_MD5_Hash'] ==  strtoupper(md5( $this->get_option('hash_key') . $this->get_option('login_id') . $_POST['x_trans_id'] .  $_POST['x_amount']))) ){
                        try{
                            $amount           = $_POST['x_amount'];
                            $hash             = $_POST['x_MD5_Hash'];
                            $transauthorised  = false;

                            if ( !$order->has_status('completed')){

                                if ( $_POST['x_response_code'] == 1 ){
                                    $transauthorised        = true;
                                    if ( $order->has_status('processing')){

                                    }
                                    else{
                                        $order->completed_order('Autorize.net payment successful<br/>Ref Number/Transaction ID: '.$_REQUEST['x_trans_id'].'.');
                                        update_post_meta($order->get_id(), IWJ_PREFIX.'transaction_id', $_REQUEST['x_trans_id']);
                                    }
                                }
                                else{
                                    $order->change_status('iwj-cancelled', __('Your transaction has been declined.', 'iwjob'));
                                }
                            }
                            if ( $transauthorised==false ){
                                $order->change_status('iwj-cancelled', __('Your transaction has been declined.', 'iwjob'));
                            }

                        }
                        catch(Exception $e){
                            // $errorOccurred = true;
                            $msg = "Error";
                        }

                    }else{
                        $order->add_order_note('MD5 hash did not matched for this transaction. Please check documentation to set MD5 String. <a href="http://www.indatos.com/developer-documentation/md5-hash-security-feature-authorize-net/?ref=auth-sim">MD5 String Doc.</a>. Or <a href="http://www.indatos.com/wordpress-support/">contact plugin support</a> for help.');
                    }

                    $tab = isset($_GET['tab']) ? $_GET['tab'] : '';
                    $redirect_url = $order->get_received_url($tab);

                    $this->web_redirect( $redirect_url); exit;
                }
            }

            $this->web_redirect(home_url('/'));
            exit;
        }

        function process_payment($order_id, $tab = 'new-job'){
            $output = '';
            $order = IWJ_Order::get_order($order_id);
            if($order){
                $user = IWJ_User::get_user();
                $login_id = $this->get_option('login_id');
                $transection_key = $this->get_option('transection_key');
                $currency = iwj_get_currency();
                $timeStamp = time();
                $sequence = rand(1, 1000);
                $price = $order->get_price();

                if (phpversion() >= '5.1.2') {
                    $fingerprint = hash_hmac("md5", $login_id . "^" . $sequence . "^" . $timeStamp . "^" . $price . "^" . $currency, $transection_key);
                } else {
                    $fingerprint = bin2hex(mhash(MHASH_MD5, $login_id . "^" . $sequence . "^" . $timeStamp . "^" . $price . "^" . $currency, $transection_key));
                }

                $cancel_url = $order->get_cancel_url('jobs');
                $x_type = $this->get_option('transaction_mode') == 'auth_capture' ? 'AUTH_CAPTURE' : 'AUTH_ONLY';
                $working_mode = $this->get_option('working_mode');
                if($working_mode == '2'){
                    $x_test_request = 'TRUE';
                }else{
                    $x_test_request = 'FALSE';
                }
                $output .= '<form name="AuthorizeForm" id="iwj-authorizedotnet-form" action="' . $this->get_payment_gateway_url() . '" method="post">  
                            <input type="hidden" name="x_login" value="' . $login_id . '">
                            <input type="hidden" name="x_type" value="'.$x_type.'"/>
                            <input type="hidden" name="x_amount" value="' . $price . '">
                            <input type="hidden" name="x_fp_sequence" value="' . $sequence . '" />
                            <input type="hidden" name="x_fp_timestamp" value="' . $timeStamp . '" />
                            <input type="hidden" name="x_fp_hash" value="' . $fingerprint . '" />
                            <input type="hidden" name="x_show_form" value="PAYMENT_FORM" />
                            <input type="hidden" name="x_invoice_num" value="' . sanitize_text_field($order_id) . '">
                            <input type="hidden" name="x_po_num" value="' . sanitize_text_field($order_id . '_' . $currency) . '">
                            <input type="hidden" name="x_cust_id" value="' . sanitize_text_field(get_current_user_id()) . '"/> 

                            <input type="hidden" name="x_first_name" value="' . ($user->get_first_name() ?  $user->get_first_name() : $user->get_display_name()). '"> 
                            <input type="hidden" name="x_last_name" value="' . $user->get_last_name() . '"> 
                            <input type="hidden" name="x_address" value=""> 
                            <input type="hidden" name="x_fax" value=""> 
                            <input type="hidden" name="x_email" value="' . $user->get_email() . '"> 

                            <input type="hidden" name="x_description" value="' . $order->get_title() . '"> 
                            <input type="hidden" name="x_currency_code" value="' . $currency . '" />	
                            <input type="hidden" name="x_cancel_url" value="' . esc_url($cancel_url) . '" />
                            <input type="hidden" name="x_cancel_url_text" value="'.__('Cancel Order', 'iwjob').'" />
                            <input type="hidden" name="x_relay_response" value="TRUE" />
                            <input type="hidden" name="x_relay_url" value="' . esc_url($this->get_received_url($order, $tab)) . '"/> 
                            <input type="hidden" name="x_test_request" value="'.$x_test_request.'"/>
                        </form>';
                echo $output;
                echo '<script>
				  	document.getElementById("iwj-authorizedotnet-form").submit();
				  </script>';
                die;
            }

            die;
        }

        public function web_redirect($url){

            echo "<html><head><script language=\"javascript\">
                <!--
                window.location=\"{$url}\";
                //-->
                </script>
                </head><body><noscript><meta http-equiv=\"refresh\" content=\"0;url={$url}\"></noscript></body></html>";

        }
    }

}