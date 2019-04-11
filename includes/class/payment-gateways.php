<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class IWJ_Payment_Gateways {

	/** @var array Array of payment gateway classes. */
	public $payment_gateways;

	/**
	 * @var IWJ_Payment_Gateways The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Main IWJ_Payment_Gateways Instance.
	 *
	 * Ensures only one instance of IWJ_Payment_Gateways is loaded or can be loaded.
	 *
	 * @static
	 * @return IWJ_Payment_Gateways Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Initialize payment gateways.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Load gateways and hook in functions.
	 */
	public function init() {
		$load_gateways = array(
			'IWJ_Gateway_Direct_Bank',
			'IWJ_Gateway_Paypal',
			'IWJ_Gateway_Authorizedotnet',
			'IWJ_Gateway_Stripe',
			'IWJ_Gateway_Skrill',
		);

		// Filter
		$load_gateways = apply_filters( 'iwj_payment_gateways', $load_gateways );

		// Get sort order option
		$ordering  = (array) get_option( 'iwj_gateway_order' );
		$order_end = 999;

		// Load gateways in order
		foreach ( $load_gateways as $gateway ) {
			$load_gateway = is_string( $gateway ) ? new $gateway() : $gateway;

			if ( isset( $ordering[ $load_gateway->id ] ) && is_numeric( $ordering[ $load_gateway->id ] ) ) {
				// Add in position
				$this->payment_gateways[ $ordering[ $load_gateway->id ] ] = $load_gateway;
			} else {
				// Add to end of the array
				$this->payment_gateways[ $order_end ] = $load_gateway;
				$order_end++;
			}
		}

		ksort( $this->payment_gateways );
	}

	/**
	 * Get gateways.
	 * @return array
	 */
	public function payment_gateways() {
		$_available_gateways = array();

		if ( sizeof( $this->payment_gateways ) > 0 ) {
			foreach ( $this->payment_gateways as $gateway ) {
				$_available_gateways[ $gateway->id ] = $gateway;
			}
		}

		return $_available_gateways;
	}

	/**
	 * Get array of registered gateway ids
	 * @since 2.6.0
	 * @return array of strings
	 */
	public function get_payment_gateway_ids() {
		return wp_list_pluck( $this->payment_gateways, 'id' );
	}

	/**
	 * Get available gateways.
	 *
	 * @return array
	 */
	public function get_available_payment_gateways() {
		$_available_gateways = array();

		foreach ( $this->payment_gateways as $gateway ) {
			if ( $gateway->is_available() ) {
				$_available_gateways[ $gateway->id ] = $gateway;
			}
		}

		return apply_filters( 'iwj_available_payment_gateways', $_available_gateways );
	}

	/**
	 * Get available gateway.
	 *
	 * @return array
	 */
	public function get_payment_gateway($gateway) {
		foreach ( $this->payment_gateways as $_gateway ) {
		    if($_gateway->id == $gateway){
		        return $_gateway;
            }
		}

		return null;
	}

}

IWJ_Payment_Gateways::instance();