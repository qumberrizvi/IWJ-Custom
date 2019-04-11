<?php

/**
 * Autocomplete field class.
 */
class IWJMB_Simple_Autocomplete_Field extends IWJMB_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

    /**
	 * Enqueue scripts and styles.
	 */
	static function enqueue_scripts() {
		wp_register_script( 'iwjmb-simple-autocomplete', IWJ_FIELD_ASSETS_URL . 'js/simple-autocomplete.js', array( 'jquery-ui-autocomplete' ), '', true );
	}

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @return string
	 */
	static function input( $meta, $field ) {

        wp_enqueue_script( 'iwjmb-simple-autocomplete');

		$options = $field['options'];

		if ( ! is_string( $field['options'] ) ) {
			$options = wp_json_encode( $options );
		}

		// Input field that triggers autocomplete.
		// This field doesn't store field values, so it doesn't have "name" attribute.
		// The value(s) of the field is store in hidden input(s). See below.
		$html = sprintf(
			'<input type="text" class="iwjmb-simple-autocomplete" value="%s" size="%s" name="%s" data-options="%s">',
            $meta,
			$field['size'],
			$field['field_name'],
			esc_attr( $options )
		);

		return $html;
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	static function normalize( $field ) {
		$field = parent::normalize( $field );
		$field = wp_parse_args( $field, array(
			'size' => 30,
		) );
		return $field;
	}
}

IWJMB_Simple_Autocomplete_Field::init();