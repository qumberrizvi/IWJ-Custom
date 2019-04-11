<?php

/**
 * Autocomplete field class.
 */
class IWJMB_Tagable_Field extends IWJMB_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

    /**
	 * Enqueue scripts and styles.
	 */
	static function enqueue_scripts() {
		wp_register_style( 'bootstrap-tokenfield', IWJ_FIELD_ASSETS_URL . 'css/bootstrap-tokenfield.css');
        //wp_register_style( 'typeahead', IWJ_FIELD_ASSETS_URL . 'css/tokenfield-typeahead.css');
        //wp_register_script( 'typeahead', IWJ_FIELD_ASSETS_URL . 'js/typeahead.bundle.min.js', array( 'jquery' ), '', true );
        wp_register_script( 'bootstrap-tokenfield', IWJ_FIELD_ASSETS_URL . 'js/bootstrap-tokenfield.js', array('jquery-ui-autocomplete'), '', true );
		wp_register_script( 'iwjmb-tagable', IWJ_FIELD_ASSETS_URL . 'js/tagable.js', array( 'bootstrap-tokenfield' ), '', true );

        if(!iwj_option( 'disable_skill' )){
	        $exclude_skills  = iwj_option( 'exclude_skills' );
	        $trim_exclude    = trim( $exclude_skills );
	        $array_ecl_skill = array();
	        if ( ! empty( $trim_exclude ) ) {
		        $array_ecl_skill = explode( ',', $trim_exclude );
	        }
	        $trimmed_array_ecl = array_map( 'trim', $array_ecl_skill );
	        wp_localize_script( 'iwjmb-tagable', 'iwjmb_tagable_exclude_skills', array_map( 'strtolower', $trimmed_array_ecl ) );
        }
	}

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @return string
	 */
	static function input( $meta, $field ) {
        $meta = (array)$meta;
        wp_enqueue_style( 'bootstrap-tokenfield');
       // wp_enqueue_style( 'typeahead');
        //wp_enqueue_script( 'typeahead');
        wp_enqueue_script( 'bootstrap-tokenfield');
        wp_enqueue_script( 'iwjmb-tagable');

		$options = $field['options'];

		if ( ! is_string( $field['options'] ) ) {
			$options = wp_json_encode( $options );
		}

		// Input field that triggers autocomplete.
		// This field doesn't store field values, so it doesn't have "name" attribute.
		// The value(s) of the field is store in hidden input(s). See below.
		$html = sprintf(
			'<input type="text" class="iwjmb-tagable" value="%s" size="%s" name="%s" placeholder="%s" data-options="%s">',
            implode(",", $meta),
			$field['size'],
			$field['field_name'],
			$field['placeholder'],
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
            'options' => array()
		) );
		return $field;
	}
}

IWJMB_Tagable_Field::init();