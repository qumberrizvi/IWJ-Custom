<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class IWJ_Social_Logins {

	/** @var array Array of social login classes. */
	public $social_logins;

	/**
	 * @var IWJ_Social_Logins The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Main IWJ_Social_Logins Instance.
	 *
	 * Ensures only one instance of IWJ_Social_Logins is loaded or can be loaded.
	 *
	 * @static
	 * @return IWJ_Social_Logins Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Initialize social logins.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Load gateways and hook in functions.
	 */
	public function init() {
		$load_socials = array(
			'IWJ_Social_Login_Facebook',
			'IWJ_Social_Login_Google',
			'IWJ_Social_Login_Twitter',
			'IWJ_Social_Login_Linkedin',
		);

		// Filter
		$load_socials = apply_filters( 'iwj_social_logins', $load_socials );

		// Get sort social option
		$ordering  = (array) get_option( 'iwj_social_login_order' );
		$order_end = 999;

		// Load socials in order
		foreach ( $load_socials as $social ) {
			$load_social = is_string( $social ) ? new $social() : $social;

			if ( isset( $ordering[ $load_social->id ] ) && is_numeric( $ordering[ $load_social->id ] ) ) {
				// Add in position
				$this->social_logins[ $ordering[ $load_social->id ] ] = $load_social;
			} else {
				// Add to end of the array
				$this->social_logins[ $order_end ] = $load_social;
				$order_end++;
			}
		}
		ksort( $this->social_logins );
	}

	/**
	 * Get socials.
	 * @return array
	 */
	public function social_logins() {
		$_available_socials = array();

		if ( sizeof( $this->social_logins ) > 0 ) {
			foreach ( $this->social_logins as $social ) {
				$_available_socials[ $social->id ] = $social;
			}
		}

		return $_available_socials;
	}

	/**
	 * Get array of registered socials ids
	 * @since 2.6.0
	 * @return array of strings
	 */
	public function get_social_ids() {
		return wp_list_pluck( $this->social_logins, 'id' );
	}

	/**
	 * Get available gateways.
	 *
	 * @return array
	 */
	public function get_available_socials() {
		$_available_socials = array();

		foreach ( $this->social_logins as $social ) {
			if ( $social->is_available() ) {
				$_available_socials[ $social->id ] = $social;
			}
		}

		return apply_filters( 'iwj_available_social_logins', $_available_socials );
	}

	/**
	 * Get available social.
	 *
	 * @return array
	 */
	public function get_social($social) {
		foreach ( $this->social_logins as $_social ) {
		    if($_social->id == $social){
		        return $_social;
            }
		}

		return null;
	}

}

IWJ_Social_Logins::instance();