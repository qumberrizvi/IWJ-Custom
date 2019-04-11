<?php

/**
 *  File Type: Skrill Gateway

 */
if (!class_exists('IWJ_Gateway_Skrill')) {

    class IWJ_Gateway_Skrill extends IWJ_Payment_Gateway {

        function __construct(){

            parent::__construct();

            add_action( 'wp_ajax_iwj_skrill_ipn', array($this, 'ipn_check_response'));
            add_action( 'wp_ajax_nopriv_iwj_skrill_ipn', array($this, 'ipn_check_response') );
        }

        function get_title(){
            return __('Skrill', 'iwjob');
        }

        function get_description(){
            return $this->get_option('description');
        }

        function get_icon(){
            return IWJ_PLUGIN_URL.'/assets/img/skrill.png';
        }

        public function is_available(){

            if($this->get_option('enable') && $this->get_option('email') && $this->get_option('secret_word')){
                return true;
            }

            return false;
        }

        function get_payment_gateway_url(){
            if ($this->get_option('test_mode') == '1') {
                return "http://www.moneybookers.com/app/test_payment.pl";
            } else {
                return "https://www.moneybookers.com/app/payment.pl";
            }
        }

        public function get_ipn_url(){
            $url = home_url('/'). 'wp-admin/admin-ajax.php?action=iwj_skrill_ipn';
            return $url;
        }

        public function admin_option_fields() {
            return array(
                array(
                    'id' 			=> 'enable',
                    'name'			=> __( 'Enable' , 'iwjob' ),
                    'desc'	        => __( 'Enable Skrill.', 'iwjob' ),
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
                    'std'		    => 'Pay with your credit card via Moneybookers.',
                ),
                array(
                    'id' 			=> 'email',
                    'name'			=> __( 'Skrill Email' , 'iwjob' ),
                    'desc'	        => __( 'Enter Skrill Email', 'iwjob' ),
                    'type'			=> 'text',
                    'std'		=> '',
                ),
                array(
                    'id' 			=> 'merchant_id',
                    'name'			=> __( 'Merchant ID' , 'iwjob' ),
                    'desc'	        => __( 'Enter Merchant ID', 'iwjob' ),
                    'type'			=> 'text',
                    'std'		=> '',
                ),
                array(
                    'id' 			=> 'secret_word',
                    'name'			=> __( 'Secret Word' , 'iwjob' ),
                    'desc'	=> __( 'Enter Secret Word', 'iwjob' ),
                    'type'			=> 'password',
                    'std'		=> '',
                ),
                array(
                    'id' => 'test_mode',
                    'name'        => __('Test Mode', 'iwjob'),
                    'type'         => 'select',
                    'options'      => array('1'=> __('Yes'), '0' => __('No')),
                    'std'		   => '1',
                    'description'  => "Use Skrill test portal" ),
            );
        }

        function process_payment($order_id, $tab = 'new-job'){
            $output = '';
            $order = IWJ_Order::get_order($order_id);
            if($order){
                $currency = iwj_get_currency();
                $price = $order->get_price();

                $output .= '<form name="SkrillForm" id="iwj-skrill-form" action="' . $this->get_payment_gateway_url() . '" method="post">  
                            <input type="hidden" name="pay_to_email" value="' . sanitize_email($this->get_option('email')) . '">
                            <input type="hidden" value="EN" name="language">
                            <input type="hidden" name="amount" value="' . $price . '">
                            <input type="hidden" name="detail1_description" value="'.esc_attr($order->get_payment_description()).'"/>
                            <input type="hidden" name="detail1_text" value="' . sprintf(__('Order #%s', 'iwjob'), $order_id) . '"/>
                            <input type="hidden" value="' . $currency . '" name="currency">
                            <input type="hidden" name="status_url" value="' . esc_url($this->get_ipn_url()) . '">
                            <input name="cancel_url" value="' . esc_url($order->get_cancel_url($tab)) . '" type="hidden">  
                            <input type="hidden" name="transaction_id" value="' . sanitize_text_field($order_id) . '">
                            <input type="hidden" name="return_url" value="' . esc_url($this->get_received_url($order, $tab)) . '"> 
                        </form>';
                echo $output;
                echo '<script>
				  	document.getElementById("iwj-skrill-form").submit();
				  </script>';
            }

            die;
        }

        function ipn_check_response(){
            if (isset($_POST['merchant_id'])) {
                // Validate the Moneybookers signature
                $concatFields = $_POST['merchant_id']
                    . $_POST['transaction_id']
                    . strtoupper(md5($this->get_option('secret_word')))
                    . $_POST['mb_amount']
                    . $_POST['mb_currency']
                    . $_POST['status'];


                $MBEmail = $this->get_option('email');

                /* Ensure the signature is valid, the status code == 2,
                  and that the money is going to you */
                if (strtoupper(md5($concatFields)) == $_POST['md5sig']
                    && $_POST['status'] == 2
                    && $_POST['pay_to_email'] == $MBEmail)
                {

                    $order_id = $_POST['transaction_id'];
                    $order = IWJ_Order::get_order($order_id);
                    if($order){
                        if(!$order->has_status('completed')){
                            $order->completed_order(__('Payment completed.', 'iwjob'));
                        }
                        return $order;
                    }
                }
            }

            return false;
        }

        function payment_recieved($order, $tab){
            if($this->get_option('merchant_id') && isset($_GET['msid']) && $_GET['msid'] && isset($_GET['transaction_id']) && $_GET['transaction_id']) {
                $concatFields = $this->get_option('merchant_id')
                    . $_GET['transaction_id']
                    . strtoupper(md5($this->get_option('secret_word')));

                if (strtoupper(md5($concatFields)) == $_GET['msid']){
                    $order_id = $_GET['transaction_id'];
                    $order = IWJ_Order::get_order($order_id);
                    if ($order) {
                        if(!$order->has_status('completed')){
                            $order->completed_order(__('Payment completed.', 'iwjob'));
                        }
                        wp_redirect($order->get_received_url($tab));
                        exit;
                    }
                }

            }

            $dashboard = iwj_get_page_permalink('dashboard');
            wp_redirect($dashboard);
            exit;
        }
    }
}