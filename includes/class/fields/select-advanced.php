<?php
/**
 * Select advanced field which uses select2 library.
 */
class IWJMB_Select_Advanced_Field extends IWJMB_Select_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

	/**
	 * Enqueue scripts and styles
	 */
	public static function enqueue_scripts() {
		wp_register_style( 'iwjmb-select2', IWJ_FIELD_ASSETS_URL . 'css/select2.css', array(), '4.0.1' );
        wp_register_style( 'iwjmb-select-advanced', IWJ_FIELD_ASSETS_URL . 'css/select-advanced.css', array() );

		wp_register_script( 'iwjmb-select2', IWJ_FIELD_ASSETS_URL . 'js/select2.min.js', array( 'jquery' ), '', true );

        wp_register_script( 'iwjmb-select', IWJ_FIELD_ASSETS_URL . 'js/select.js', array( 'jquery' ), '', true );
        wp_register_script( 'iwjmb-select-advanced', IWJ_FIELD_ASSETS_URL . 'js/select-advanced.js', array(), '', true );
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	public static function normalize( $field ) {
		$field = wp_parse_args( $field, array(
			'js_options'  => array(),
			'placeholder' => __( 'Select an item', 'iwjob' ),
		));

		$field = parent::normalize( $field );

		$field['js_options'] = wp_parse_args( $field['js_options'], array(
			'allowClear'  => false,
			'width'       => 'none',
			'placeholder' => $field['placeholder'],
			'multiple' => $field['multiple'] ? true : false,
		) );

		return $field;
	}

	/**
	 * Get the attributes for a field
	 *
	 * @param array $field
	 * @param mixed $value
	 * @return array
	 */
	public static function get_attributes( $field, $value = null ) {
		$attributes = parent::get_attributes( $field, $value );
		$attributes = wp_parse_args( $attributes, array(
			'data-options' => wp_json_encode( $field['js_options'] ),
		));

		return $attributes;
	}

    /**
     * Get field HTML
     *
     * @param mixed $meta
     * @param array $field
     * @return string
     */
    public static function input( $meta, $field ) {
        wp_enqueue_style( 'iwjmb-select2');
        wp_enqueue_style( 'iwjmb-select-advanced');
        wp_enqueue_script( 'iwjmb-select2');
        wp_enqueue_script( 'iwjmb-select');
        wp_enqueue_script( 'iwjmb-select-advanced');
        $html = parent::input($meta, $field);
        return $html;
    }
}

IWJMB_Select_Advanced_Field::init();