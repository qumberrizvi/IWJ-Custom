<?php

/**
 * Color field class.
 */
class IWJMB_Color_Field extends IWJMB_Text_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

	/**
	 * Enqueue scripts and styles
	 */
	static function enqueue_scripts() {
        wp_register_style( 'iwjmb-color', IWJ_FIELD_ASSETS_URL . 'css/color.css', array( 'wp-color-picker' ) );
		wp_register_script( 'iwjmb-color', IWJ_FIELD_ASSETS_URL . 'js/color.js', array( 'wp-color-picker' ), '', true );
	}

	/**
	 * Normalize parameters for field.
	 *
	 * @param array $field
	 * @return array
	 */
	static function normalize( $field ) {
		$field = wp_parse_args( $field, array(
			'size'       => 7,
			'maxlength'  => 7,
			'pattern'    => '^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$',
			'js_options' => array(),
		) );

		$field['js_options'] = wp_parse_args( $field['js_options'], array(
			'defaultColor' => false,
			'hide'         => true,
			'palettes'     => true,
		) );

		$field = parent::normalize( $field );

		return $field;
	}

	/**
	 * Get the attributes for a field
	 *
	 * @param array $field
	 * @param mixed $value
	 * @return array
	 */
	static function get_attributes( $field, $value = null ) {
		$attributes = parent::get_attributes( $field, $value );
		$attributes = wp_parse_args( $attributes, array(
			'data-options' => wp_json_encode( $field['js_options'] ),
		) );
		$attributes['type'] = 'text';

		return $attributes;
	}

	/**
	 * Format a single value for the helper functions.
	 *
	 * @param array  $field Field parameter
	 * @param string $value The value
	 * @return string
	 */
	static function format_single_value( $field, $value ) {
		return sprintf( "<span style='display:inline-block;width:20px;height:20px;border-radius:50%%;background:%s;'></span>", $value );
	}

    public static function input( $meta, $field ) {
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_style( 'iwjmb-color');
        wp_enqueue_script( 'iwjmb-color');

        return parent::input($meta, $field);
    }

}

IWJMB_Color_Field::init();