<?php
abstract class IWJ_Payment_Gateway{
    public $id;

    function __construct()
    {
        $this->id = strtolower(str_replace('IWJ_Gateway_', '', get_class($this)));
    }

    abstract function get_title();

    abstract function get_description();

    abstract function admin_option_fields();

    public function admin_saved_fields($options){
        return $options;
    }

    public function get_option($key, $default = ''){
        $option = iwj_option('gateway_'.$this->id.'_'.$key, $default);
        return $option;
    }

    public function is_available(){

        if($this->get_option('enable')){
            return true;
        }

        return false;
    }

    public function get_received_url($order, $tab = 'new-job'){
        return add_query_arg(array('iwj_payment'=> $this->id, 'order_id'=> $order->get_id(), 'key' => $order->get_key(), 'tab' => $tab), home_url('/'));
    }

    public function has_subscription($user_id){
        return false;
    }

    public function cancel_subscription($user_id){
        return false;
    }

}

