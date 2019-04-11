<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! defined( 'INJOB_WOOCOMMERCE_SESSION_KEY' ) )
    define( 'INJOB_WOOCOMMERCE_SESSION_KEY', 'injob_session_key' );


class IWJ_Woocommerce {

	function init(){
		add_action('init', array( $this, 'setup'));
	}

	function setup(){

		add_action('woocommerce_before_order_itemmeta', array($this, 'before_order_itemmeta'), 20, 3);
		add_filter('woocommerce_cart_item_name', array( $this, 'cart_item_name'), 20, 3);
		add_filter('woocommerce_order_item_name', array( $this, 'order_item_name'), 20, 3);
		add_action('woocommerce_add_order_item_meta', array( $this, 'add_order_item_meta'), 10, 3);
        add_action('woocommerce_checkout_order_processed', array( $this, 'checkout_order_processed'), 10, 2);
		add_action('woocommerce_order_status_changed', array( $this, 'order_status_changed'), 10, 3 );
		add_action('woocommerce_display_item_meta', array( $this, 'display_item_meta'), 10, 3 );
		add_filter('woocommerce_hidden_order_itemmeta', array( $this, 'hidden_order_itemmeta'));
        add_filter('woocommerce_checkout_fields' , array($this, 'checkout_fields' ));

        add_action( 'woocommerce_thankyou', array($this, 'auto_completed_order' ) );

	}

    function auto_completed_order( $order_id ) { 
        if ( ! $order_id ) {
            return;
        }

        $order = wc_get_order( $order_id );
        if ( $order->get_total() == 0 ) {
            $order->update_status( 'completed' );
        }
    }

	function random_sku($prefix, $len = 6) {

		$str = '';

		for ($i = 0; $i < $len; $i++) {
			$str .= substr('0123456789', mt_rand(0, strlen('0123456789') - 1), 1);
		}

		return $prefix . $str;
	}

    static function create_all_job_products(){
	    global $wpdb;
	    $iwj_wc = new IWJ_Woocommerce();
	    $product_ids = array();
	    $package_ids = $wpdb->get_results("SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type='iwj_package'");
        foreach($package_ids as $package_id){
            if(iwj_option('free_package_id') != $package_id){
                $product_ids[$package_id->ID] = $iwj_wc->get_job_product_id('package', $package_id->ID);
            }
        }
	    $package_ids = $wpdb->get_results("SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type='iwj_plan'");
        foreach($package_ids as $package_id){
            if(iwj_option('free_plan_id') != $package_id){
                $product_ids[$package_id->ID] = $iwj_wc->get_job_product_id('plan', $package_id->ID);
            }
        }
        $package_ids = $wpdb->get_results("SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type='iwj_resum_package'");
        foreach($package_ids as $package_id){
            if(iwj_option('free_resum_package_id') != $package_id){
                $product_ids[$package_id->ID] = $iwj_wc->get_job_product_id('resumepackage', $package_id->ID);
            }
        }
	    $package_ids = $wpdb->get_results("SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type='iwj_apply_package'");
	    foreach($package_ids as $package_id){
		    if(iwj_option('free_apply_job_package_id') != $package_id){
			    $product_ids[$package_id->ID] = $iwj_wc->get_job_product_id('applyjob_package', $package_id->ID);
		    }
	    }
        $product_ids['newjob'] = $iwj_wc->get_job_product_id('newjob');
        $product_ids['renewjob'] = $iwj_wc->get_job_product_id('renewjob');
        $product_ids['featuredjob'] = $iwj_wc->get_job_product_id('featuredjob');
        $product_ids['featuredjob'] = $iwj_wc->get_job_product_id('featuredjob');

        return $product_ids;
    }

    static function get_all_job_products(){
	    global $wpdb;
	    $iwj_wc = new IWJ_Woocommerce();
	    $product_ids = array();
	    $package_ids = $wpdb->get_results("SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type='iwj_package'");
        foreach($package_ids as $package_id){
            $product_ids[$package_id->ID] = $iwj_wc->get_job_product_id('package', $package_id->ID, false);
        }

	    $plan_ids = $wpdb->get_results("SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type='iwj_plan'");
        foreach($plan_ids as $plan_id){
            $product_ids[$plan_id->ID] = $iwj_wc->get_job_product_id('plan', $plan_id->ID, false);
        }

        $package_ids = $wpdb->get_results("SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type='iwj_resum_package'");
        foreach($package_ids as $package_id){
            $product_ids[$package_id->ID] = $iwj_wc->get_job_product_id('resumepackage', $package_id->ID, false);
        }

	    $package_ids = $wpdb->get_results("SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type='iwj_apply_package'");
	    foreach($package_ids as $package_id){
		    $product_ids[$package_id->ID] = $iwj_wc->get_job_product_id('applyjob_package', $package_id->ID, false);
	    }

        $product_ids['newjob'] = $iwj_wc->get_job_product_id('newjob', null, false);
        $product_ids['renewjob'] = $iwj_wc->get_job_product_id('renewjob', null, false);
        $product_ids['featuredjob'] = $iwj_wc->get_job_product_id('featuredjob', null, false);
        $product_ids['featuredjob'] = $iwj_wc->get_job_product_id('featuredjob', null, false);

        return $product_ids;
    }

    static function get_products_list(){
        $products_html = '';
        $product_ids = IWJ_Woocommerce::get_all_job_products();
        if($product_ids){
            $products_html .= '<ul>';
            foreach ($product_ids as $product_id){
                if($product_id){
                    $status = get_post_status($product_id);
                    $products_html .= '<li><a target="_blank" href="'.get_edit_post_link($product_id).'">'.get_the_title($product_id).($status != 'publish' ? ' ['.$status.']' : '').'</a>';
                }
            }
            $products_html .= '</ul>';
        }

        return $products_html;
    }

	function create_job_product($type = 'package', $package_id = null) {
	    switch ($type){
	        case 'package' :
            $title = sprintf(__('Package %s', 'iwjob'), get_the_title($package_id));
            $price = get_post_meta($package_id, IWJ_PREFIX.'price', true);
            break;
	        case 'plan' :
            $title = sprintf(__('Plan %s', 'iwjob'), get_the_title($package_id));
            $price = get_post_meta($package_id, IWJ_PREFIX.'price', true);
            break;
	        case 'resumepackage' :
            $title = sprintf(__('Resume Package %s', 'iwjob'), get_the_title($package_id));
            $price = get_post_meta($package_id, IWJ_PREFIX.'price', true);
            break;
		    case 'applyjob_package' :
			    $title = sprintf(__('Apply Class Package %s', 'iwjob'), get_the_title($package_id));
			    $price = get_post_meta($package_id, IWJ_PREFIX.'price', true);
			    break;
	        case 'newjob' :
            $title = __('New Job', 'iwjob');
            $price = iwj_option('job_price');
            break;
            break;
	        case 'renewjob' :
            $title = __('Renew Job', 'iwjob');
            $price = iwj_option('renew_job_price');
            break;
	        case 'featuredjob' :
            $title = __('Feature Job', 'iwjob');
            $price = iwj_option('featured_job_price');
            break;
	        default :
            $title = '';
            $price = '';
            break;
        }

        if($title){
            $new_post = array(
                'post_title' 		=> $title,
                'post_content' 		=> esc_html__('This is a variable product that used for job processed with WooCommerce', 'iwjob'),
                'post_status' 		=> 'publish',
                'post_name' 		=> sanitize_title($title),
                'post_type' 		=> 'product',
                'comment_status' 	=> 'closed'
            );

            $product_id 			= wp_insert_post($new_post);
            $skuu 					= $this->random_sku('job_sku_', 6);

            update_post_meta($product_id, '_regular_price', $price );
            update_post_meta($product_id, '_sku', $skuu );
            update_post_meta($product_id, '_sold_individually', 	'yes');
            update_post_meta($product_id, IWJ_PREFIX.'product_type', $type);
            if($package_id){
                update_post_meta($product_id, IWJ_PREFIX.'package_id', $package_id);
            }

            if ($product = wc_get_product($product_id)) {
                $product->set_catalog_visibility('hidden');
                $product->set_virtual(true);
                $product->save();
            }

            return $product_id;
        }

        return null;
	}

	function get_job_product_id($type = 'package', $package_id = null, $create_if_not_exists = true) {

		global $wpdb;

		if($package_id){
            $sql = "SELECT DISTINCT pm1.post_id FROM $wpdb->postmeta AS pm1 JOIN $wpdb->postmeta AS pm2 ON pm1.post_id = pm2.post_id WHERE pm1.meta_key='".IWJ_PREFIX.'product_type'."' AND pm2.meta_key='".IWJ_PREFIX.'package_id'."' AND pm1.meta_value='{$type}' AND pm2.meta_value='{$package_id}'";
        }else{
            $sql = "SELECT DISTINCT pm1.post_id FROM $wpdb->postmeta AS pm1 WHERE pm1.meta_key='".IWJ_PREFIX.'product_type'."' AND pm1.meta_value='{$type}'";
        }

		$id = $wpdb->get_var($sql);
		if(!$id && $create_if_not_exists){
            $id = $this->create_job_product($type, $package_id);
        }

		return intval($id);
	}


	public function add_to_cart($type, $job_id = '', $package_id = '') {

		global $woocommerce;
		$product_id = $this->get_job_product_id($type, $package_id);
		if (!isset($product_id) || empty($product_id)) {
            $product_id = $this->create_job_product();
		}

		if ($product_id > 0) {

			$cart_item_key 			= $woocommerce->cart->add_to_cart( $product_id, 1, null, null, null); // $cart_item_data);

			if (!is_user_logged_in()) {
				$woocommerce->session->set_customer_session_cookie(true);
			}

			$session_data = array('job_product_type' => $type);
			if($job_id){
                $session_data['job_id'] = $job_id;
            }
			if($package_id){
                $session_data['package_id'] = $package_id;
            }
			$woocommerce->session->set(INJOB_WOOCOMMERCE_SESSION_KEY . $cart_item_key, $session_data);
        }

        global $woocommerce;
        $checkout_url = wc_get_checkout_url() ;
        wp_redirect($checkout_url);
        exit;
    }

	function cart_item_name($product_title, $cart_item, $cart_item_key){
		global $woocommerce;
		$cart_item_meta = $woocommerce->session->get(INJOB_WOOCOMMERCE_SESSION_KEY . $cart_item_key);
		if (isset($cart_item_meta['job_product_type']) && $cart_item_meta['job_product_type']) {
            switch ($cart_item_meta['job_product_type']){
                case 'newjob':
                case 'renewjob':
                case 'featuredjob':
                    $job_id = isset($cart_item_meta['job_id']) ? $cart_item_meta['job_id'] : '';
                    if($job_id){
                        return $product_title .' '.get_the_title($job_id);
                    }

                    return $product_title;
                default :
                    $product_id = $cart_item['product_id'];
                    return get_the_title($product_id);
            }
        }

        return $product_title;

    }


	function order_item_name($product_title, $item) {
		//$product_id   	= $item['product_id'];
		if (isset($item['job_product_type'])) {
            switch ($item['job_product_type']){
                case 'newjob':
                case 'renewjob':
                case 'featuredjob':
                    $job_id = isset($item['job_id']) ? $item['job_id'] : '';
                    if($job_id){
                        return $product_title .' '.get_the_title($job_id);
                    }

                    return $product_title;
                default :
                    $product_id = $item['product_id'];
                    return get_the_title($product_id);
            }
		}

		return $product_title;
	}

	function add_order_item_meta($item_id, $values, $cart_item_key ) {
		global $woocommerce;
		$cart_item_meta = $woocommerce->session->get(INJOB_WOOCOMMERCE_SESSION_KEY . $cart_item_key);

		if ($cart_item_meta != null) {

			if (isset($cart_item_meta['job_product_type'])) {
				wc_add_order_item_meta($item_id, 'job_product_type', $cart_item_meta['job_product_type'], true);
			}
			if (isset($cart_item_meta['job_id'])) {
				wc_add_order_item_meta($item_id, 'job_id', $cart_item_meta['job_id'], true);
			}
			if (isset($cart_item_meta['package_id'])) {
				wc_add_order_item_meta($item_id, 'package_id', $cart_item_meta['package_id'], true);
			}
		}
	}


	// Show order details (from, to, transport type, dates etc) in order admin when viewing individual orders.
	function before_order_itemmeta($item_id, $item, $_product) {
        if(isset($item['product_id'])){
            $product_id   	= $item['product_id'];
            $job_product_type = wc_get_order_item_meta($item_id, 'job_product_type', true);
            if ($job_product_type) {
                switch ($job_product_type){
                    case 'newjob':
                    case 'renewjob':
                    case 'featuredjob':
                        $job_id = isset($item['job_id']) ? $item['job_id'] : '';
                        if($job_id){
                            echo get_the_title($product_id).' '.get_the_title($item['job_id']);
                        }
                }
            }
        }
	}

	function checkout_order_processed( $order_id, $posted ) {

		global $woocommerce;

		$order = wc_get_order( $order_id );
		if ($order) {
			if ($woocommerce->cart != null) {
				foreach ( $woocommerce->cart->cart_contents as $key => $value ) {
					$cart_item_meta = $woocommerce->session->get(INJOB_WOOCOMMERCE_SESSION_KEY . $key);

					if ($cart_item_meta != null) {
						if (isset($cart_item_meta['job_product_type'])) {
						    switch ($cart_item_meta['job_product_type']){
                                case 'package' :
                                    $package_id = $cart_item_meta['package_id'];
                                    $job_id = isset($cart_item_meta['job_id']) ? $cart_item_meta['job_id'] : '';
                                    $package = IWJ_Package::get_package($package_id);
                                    if($package){
                                        $user_package_id = IWJ_User_Package::add_new( array(
                                            'title'      => $package->get_title(),
                                            'package_id' => $package->get_id(),
                                            'user_id'    => get_current_user_id(),
                                            'pre_use'    => $job_id ? 1 : 0,
                                        ) );

                                        if($job_id){
                                            $job = IWJ_Job::get_job($job_id);
                                            $job->change_status( 'iwj-pending-payment', false );
                                            update_post_meta( $job_id, IWJ_PREFIX . 'user_package_id', $user_package_id );
                                        }

                                        update_post_meta( $user_package_id, IWJ_PREFIX . 'order_id', $order_id );

                                        $order_items = $order->get_items();
                                        if($order_items){
                                            foreach ( $order_items as $item_id => $item ) {
                                                $product_package_id = get_post_meta($item->get_product_id(), IWJ_PREFIX.'package_id', true);
                                                if($product_package_id == $package_id){
                                                    wc_add_order_item_meta($item_id, 'user_package_id', $user_package_id, true);
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                    break;
                                case 'plan' :
                                    $package_id = $cart_item_meta['package_id'];
                                    $job_id = isset($cart_item_meta['job_id']) ? $cart_item_meta['job_id'] : '';
                                    $package = IWJ_Plan::get_package($package_id);
                                    if($package){
                                        if($job_id){
                                            $job = IWJ_Job::get_job($job_id);
                                            $job->change_status( 'iwj-pending-payment', false );
                                        }
                                    }
                                    break;
                                case 'resumepackage' :
                                    $package_id = $cart_item_meta['package_id'];
                                    $package = IWJ_Resume_Package::get_package($package_id);
                                    if($package){
                                        $user_package_id = IWJ_User_Package::add_new(array(
                                            'title' => $package->get_title(),
                                            'package_id' => $package->get_id(),
                                            'user_id' => get_current_user_id(),
                                        ), 'resum_package');
                                        update_post_meta( $user_package_id, IWJ_PREFIX . 'order_id', $order_id );
                                        $order_items = $order->get_items();
                                        if($order_items){
                                            foreach ( $order_items as $item_id => $item ) {
                                                $product_package_id = get_post_meta($item->get_product_id(), IWJ_PREFIX.'package_id', true);
                                                if($product_package_id == $package_id){
                                                    wc_add_order_item_meta($item_id, 'user_package_id', $user_package_id, true);
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                    break;
							    case 'applyjob_package' :
								    $package_id = $cart_item_meta['package_id'];
								    $package = IWJ_Apply_Job_Package::get_package($package_id);
								    if($package){
									    $user_package_id = IWJ_User_Package::add_new(array(
										    'title' => $package->get_title(),
										    'package_id' => $package->get_id(),
										    'user_id' => get_current_user_id(),
									    ), 'apply_job_package');
									    update_post_meta( $user_package_id, IWJ_PREFIX . 'order_id', $order_id );
									    $order_items = $order->get_items();
									    if($order_items){
										    foreach ( $order_items as $item_id => $item ) {
											    $product_package_id = get_post_meta($item->get_product_id(), IWJ_PREFIX.'package_id', true);
											    if($product_package_id == $package_id){
												    wc_add_order_item_meta($item_id, 'user_package_id', $user_package_id, true);
												    break;
											    }
										    }
									    }
								    }
								    break;
                                case 'newjob' :
                                    $job_id = $cart_item_meta['job_id'];
                                    $job = IWJ_Job::get_job($job_id);
                                    if($job){
                                        $job->change_status( 'iwj-pending-payment', false );
                                    }

                                    break;
                            }
						}
					}
				}
			}
		}
	}

	function order_status_changed( $order_id, $old_status, $new_status ) {

		$order = wc_get_order( $order_id );
		$items = $order->get_items();
		if ($items != null) {
		    if($new_status == 'completed'){
                foreach ($items as $item_id => $item) {
                    $job_product_type = wc_get_order_item_meta($item_id, 'job_product_type', true);
                    if($job_product_type){
                        switch ($job_product_type){
                            case 'package':
                                $user_package_id = wc_get_order_item_meta( $item_id, 'user_package_id', true );
                                $user_package = IWJ_User_Package::get_user_package($user_package_id);
                                if($user_package){
                                    $package = $user_package->get_package();
                                    if($package){
                                        $user_package->purchased();
                                        $job_id = wc_get_order_item_meta($item_id, 'job_id', true);
                                        if($job_id){
                                            $job = IWJ_Job::get_job($job_id);
                                            if($job){
                                                if($job->get_status() == 'iwj-pending-payment'){
                                                    update_post_meta($job->get_id(), IWJ_PREFIX.'is_new_publish', '1');
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
                            case 'plan':
                                $package_id = wc_get_order_item_meta( $item_id, 'package_id', true );
                                $package = IWJ_Plan::get_package($package_id);
                                $user_id = $order->get_user_id();
                                $user = IWJ_User::get_user((int)$user_id);
                                if($user && $package){
                                    $user->set_plan($package_id);
                                    $job_id = wc_get_order_item_meta($item_id, 'job_id', true);
                                    if($job_id){
                                        $job = IWJ_Job::get_job($job_id);
                                        if($job){
                                            if($job->get_status() == 'iwj-pending-payment'){
                                                update_post_meta($job->get_id(), IWJ_PREFIX.'is_new_publish', '1');
                                                if(iwj_option('new_job_auto_approved')){
                                                    $job->change_status('publish');
                                                }else{
                                                    $job->change_status('pending');
                                                }
                                            }
                                        }
                                    }
                                }
                            break;
                            case 'resumepackage':
	                        case 'applyjob_package':
                                $user_package_id = wc_get_order_item_meta( $item_id,  'user_package_id', true );
                                $user_package = IWJ_User_Package::get_user_package($user_package_id);
                                if($user_package){
                                    $package = $user_package->get_package();
                                    if($package){
                                        $user_package->purchased();
                                    }
                                }
                                break;
                            case 'featuredjob':
                                $job_id = wc_get_order_item_meta($item_id, 'job_id', true);
                                $job = IWJ_Job::get_job($job_id);
                                if($job){
                                    update_post_meta($job->get_id(), IWJ_PREFIX.'is_new_featured', '1');
                                    if($job->has_status('publish')){
                                        //set featured
                                        $job->set_featured();
                                    }
                                }
                                break;
                            case 'renewjob':
                                $job_id = wc_get_order_item_meta($item_id, 'job_id', true);
                                $job = IWJ_Job::get_job($job_id);
                                if($job){
                                    $job->renew();
                                }
                                break;
                            case 'newjob':
                                $job_id = wc_get_order_item_meta($item_id, 'job_id', true);
                                $job = IWJ_Job::get_job($job_id);
                                if($job){
                                    $job->change_status('publish');
                                }
                        }
                    }
                }
            }elseif($new_status == 'cancelled'){
                foreach ($items as $item_id => $item) {
                    $job_product_type = wc_get_order_item_meta($item_id, 'job_product_type', true);
                    if($job_product_type){
                        switch ($job_product_type){
                            case 'package':
                            case 'resumepackage':
                            case 'applyjob_package':
                                $user_package_id = wc_get_order_item_meta($item_id, 'user_package_id', true );
                                $user_package = IWJ_User_Package::get_user_package($user_package_id);
                                if($user_package){
                                    wp_delete_post($user_package->get_id());
                                }
                                $job_id = wc_get_order_item_meta($item_id, 'job_id', true);
                                $job = IWJ_Job::get_job($job_id);
                                if($job){
                                    delete_post_meta( $job_id, IWJ_PREFIX . 'user_package_id');
                                    $job->change_status( 'draft', false );
                                }
                                break;
                            case 'newjob':
                            case 'plan':
                                $job_id = wc_get_order_item_meta($item_id, 'job_id', true);
                                $job = IWJ_Job::get_job($job_id);
                                if($job){
                                    delete_post_meta( $job_id, IWJ_PREFIX . 'user_package_id');
                                    $job->change_status( 'draft', false );
                                }
                                break;
                        }
                    }

                }
            }
		}
	}

	function display_item_meta($html, $item, $args){
        if($item->get_meta('job_product_type')){
            return '';
        }

        return $html;
    }

    function hidden_order_itemmeta($item_meta){
        $item_meta = array_merge($item_meta, array('job_product_type', 'package_id'));

        return $item_meta;
    }


    function checkout_fields($fields) {
        global $woocommerce;
        if ($woocommerce->cart != null) {

            foreach ( $woocommerce->cart->cart_contents as $key => $value ) {
                $cart_item_meta = $woocommerce->session->get(INJOB_WOOCOMMERCE_SESSION_KEY . $key);
                if ($cart_item_meta != null) {
                    if (isset($cart_item_meta['job_product_type'])) {
                        $user = IWJ_User::get_user();
                        if($user){
                            $display_name = $user->get_display_name();
                            $display_name = explode(' ', $display_name );
                            if(count($display_name) > 1){
                                $first_name = $display_name[0];
                                unset($display_name[0]);
                                $last_name = implode(' ', $display_name);
                            }else{
                                $first_name = $display_name[0];
                                $last_name = '';
                            }
                            $fields['billing']['billing_first_name']['default'] = $first_name;
                            $fields['billing']['billing_last_name']['default'] = $last_name;
                            $fields['billing']['billing_email']['default'] = $user->get_email();
                            $fields['billing']['billing_phone']['default'] = $user->get_phone();
                        }

                        break;
                    }
                }
            }
        }

        return $fields;
    }

}

$IWJ_WC = new IWJ_Woocommerce();
$IWJ_WC->init();