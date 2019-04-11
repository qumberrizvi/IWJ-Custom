<?php

/**
 * Image select field class which uses images as radio options.
 */
class IWJMB_Image_Select_Field extends IWJMB_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

	/**
	 * Enqueue scripts and styles
	 */
	static function enqueue_scripts() {
		wp_register_style( 'iwjmb-image-select', IWJ_FIELD_ASSETS_URL . 'css/image-select.css', array());
		wp_register_script( 'iwjmb-image-select', IWJ_FIELD_ASSETS_URL . 'js/image-select.js', array( 'jquery' ), '', true );
	}

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @return string
	 */
	static function input( $meta, $field ) {
        wp_enqueue_style( 'iwjmb-image-select');
        wp_enqueue_script( 'iwjmb-image-select');

		$html = array();
		$tpl  = '<label class="iwjmb-image-select"><img src="%s"><input type="%s" class="iwjmb-image_select hidden" name="%s" value="%s"%s></label>';

		$meta = (array) $meta;
		foreach ( $field['options'] as $value => $image ) {
			$html[] = sprintf(
				$tpl,
				$image,
				$field['multiple'] ? 'checkbox' : 'radio',
				$field['field_name'],
				$value,
				checked( in_array( $value, $meta ), true, false )
			);
		}

		return implode( ' ', $html );
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	static function normalize( $field ) {
		$field = parent::normalize( $field );
		$field['field_name'] .= $field['multiple'] ? '[]' : '';

		return $field;
	}

	/**
	 * Format a single value for the helper functions.
	 *
	 * @param array  $field Field parameter
	 * @param string $value The value
	 * @return string
	 */
	static function format_single_value( $field, $value ) {
		return sprintf( '<img src="%s">', esc_url( $field['options'][ $value ] ) );
	}
}

IWJMB_Image_Select_Field::init();