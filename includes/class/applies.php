<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class IWJ_Applies {

	/** @var array Array of apply classes. */
	public $applies;

	/**
	 * @var IWJ_Applies The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Main IWJ_Applies Instance.
	 *
	 * Ensures only one instance of IWJ_Applies is loaded or can be loaded.
	 *
	 * @static
	 * @return IWJ_Applies Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Initialize applies.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Load gateways and hook in functions.
	 */
	public function init() {
		$load_applies = array(
			'IWJ_Apply_Form',
			'IWJ_Apply_Linkedin',
			'IWJ_Apply_Facebook',
		);

		// Filter
		$load_applies = apply_filters( 'iwj_applies', $load_applies );

		// Get sort apply option
		$ordering  = (array) get_option( 'iwj_social_apply_order' );
		$order_end = 999;

		// Load applies in order
		foreach ( $load_applies as $apply ) {
			$load_apply = is_string( $apply ) ? new $apply() : $apply;

			if ( isset( $ordering[ $load_apply->id ] ) && is_numeric( $ordering[ $load_apply->id ] ) ) {
				// Add in position
				$this->applies[ $ordering[ $load_apply->id ] ] = $load_apply;
			} else {
				// Add to end of the array
				$this->applies[ $order_end ] = $load_apply;
				$order_end++;
			}
		}
		ksort( $this->applies );
	}

	/**
	 * Get applies.
	 * @return array
	 */
	public function applies() {
		$_available_applies = array();

		if ( sizeof( $this->applies ) > 0 ) {
			foreach ( $this->applies as $apply ) {
				$_available_applies[ $apply->id ] = $apply;
			}
		}

		return $_available_applies;
	}

	/**
	 * Get array of registered applies ids
	 * @since 2.6.0
	 * @return array of strings
	 */
	public function get_apply_ids() {
		return wp_list_pluck( $this->applies, 'id' );
	}

	/**
	 * Get available gateways.
	 *
	 * @return array
	 */
	public function get_available_applies() {
		$_available_applies = array();

		foreach ( $this->applies as $apply ) {
			if ( $apply->is_available() ) {
				$_available_applies[ $apply->id ] = $apply;
			}
		}

		return apply_filters( 'iwj_available_applies', $_available_applies );
	}

	/**
	 * Get available apply.
	 *
	 * @return array
	 */
	public function get_apply($apply) {
		foreach ( $this->applies as $_apply ) {
		    if($_apply->id == $apply){
		        return $_apply;
            }
		}

		return null;
	}

}

IWJ_Applies::instance();