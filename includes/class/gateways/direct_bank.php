<?php

class IWJ_Gateway_Direct_Bank extends IWJ_Payment_Gateway{

    function get_title(){
        return __('Direct Bank Transfer', 'iwjob');
    }

    function get_description(){
        $description = $this->get_option('description');

        return $description;
    }

    function get_icon(){
        return IWJ_PLUGIN_URL.'/assets/img/bank.png';
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
               'std'		    => '1',
           ),
           array(
               'id' 			=> 'description',
               'name'			=> __( 'Description' , 'iwjob' ),
               'desc'	        => __( 'The description of payment.', 'iwjob' ),
               'type'			=> 'textarea',
               'std'		    => 'Make your payment direct into your bank account. Please use order ID as the payment reference.',
               'allow_translate'=>true
           ),
       );
    }

    function process_payment($order_id, $tab = 'new-job'){
        $order = IWJ_Order::get_order($order_id);
        if($order){
            $order->change_status('iwj-hold', '', false, true);
        }
        $url = $order->get_received_url($tab);
        wp_redirect($url);

        die;
    }

    function payment_recieved($order, $tab){
        return false;
    }
}