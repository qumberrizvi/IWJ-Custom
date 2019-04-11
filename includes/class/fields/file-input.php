<?php

/**
 * File input field class which uses an input for file URL.
 */
class IWJMB_File_Input_Field extends IWJMB_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	static function enqueue_scripts() {
		//wp_enqueue_media();
		wp_register_script( 'iwjmb-file-input', IWJ_FIELD_ASSETS_URL . 'js/file-input.js', array( 'jquery' ), '', true );
		self::localize_script('iwjmb-file-input', 'iwjmbFileInput', array(
			'frameTitle' => __( 'Select File', 'iwjob' ),
		) );
	}

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	static function input( $meta, $field ) {
        wp_enqueue_script( 'iwjmb-file-input');
		return sprintf(
			'<input type="text" class="iwjmb-file-input" name="%s" id="%s" value="%s" placeholder="%s" size="%s">
			<a href="#" class="iwjmb-file-input-select button-primary">%s</a>
			<a href="#" class="iwjmb-file-input-remove button %s">%s</a>',
			$field['field_name'],
			$field['id'],
			$meta,
			$field['placeholder'],
			$field['size'],
			__( 'Select', 'iwjob' ),
			$meta ? '' : 'hidden',
			__( 'Remove', 'iwjob' )
		);
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	static function normalize( $field ) {
		$field = parent::normalize( $field );
		$field = wp_parse_args( $field, array(
			'size'        => 30,
			'placeholder' => '',
		) );

		return $field;
	}
}

IWJMB_File_Input_Field::init();