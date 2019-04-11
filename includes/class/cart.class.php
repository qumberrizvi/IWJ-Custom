<?php
class IWJ_Cart{
    function set($type, $item, $price, $args = array(), $order_id = ''){
        $_SESSION['IWJ_CART'] = array(
            'type' => $type,
            'item' => $item,
            'price' => $price,
            'args' => $args,
            'order_id' => $order_id
        );
    }

    function set_order($order_id){
        $order = IWJ_Order::get_order($order_id);
        if($order){
            $type = $order->get_type();
            switch ($type){
                case 'plan' :
                    $package = $order->get_package();
                    if($package){
                        $job_id = $order->get_job_id();
                        $args = array();
                        if($job_id){
                            $args['job_id'] = $job_id;
                        }
                        $this->set($type, $package->get_id(), $package->get_price(), $args, $order_id);
                    }
                    break;
            }
        }
    }

    function get($property = 'type'){
        if(isset($_SESSION['IWJ_CART'][$property])){
            return $_SESSION['IWJ_CART'][$property];
        }elseif(isset($_SESSION['IWJ_CART']['args'][$property])){
            return $_SESSION['IWJ_CART']['args'][$property];
        }

        return null;
    }

    function is_empty(){
        if(!isset($_SESSION['IWJ_CART']) || !$_SESSION['IWJ_CART']){
            return true;
        }

        return false;
    }

    function empty_cart(){
        $_SESSION['IWJ_CART'] = array();
    }

    function get_tax_price(){
        $use_tax = iwj_option('tax_used');
        if($use_tax){
            $tax_value = iwj_option('tax_value');
            $price = $this->get('price');
            return ($tax_value * $price)/100;
        }

        return 0;
    }

    function get_tax(){
        $use_tax = iwj_option('tax_used');
        if($use_tax){
            $tax_value = iwj_option('tax_value');
            return $tax_value;
        }

        return 0;
    }

    function get_total_price(){
        $price = $this->get('price');
        $tax_price = $this->get_tax_price();

        return $price+$tax_price;
    }

	function has_tax() {
		$use_tax = iwj_option( 'tax_used' );
		if ( $use_tax ) {
			return true;
		}

		return false;
	}

	function get_price(){
		$price = $this->get('price');
		return $price;
	}

	function get_currency(){
		$currency = $this->get('currency');
		return $currency ? $currency : iwj_get_system_currency();
	}
}