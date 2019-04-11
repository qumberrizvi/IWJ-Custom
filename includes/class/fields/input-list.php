<?php
/**
 * Input list field.
 */
class IWJMB_Input_List_Field extends IWJMB_Choice_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

	/**
	 * Enqueue scripts and styles
	 */
	public static function enqueue_scripts() {
		wp_register_style( 'iwjmb-input-list', IWJ_FIELD_ASSETS_URL . 'css/input-list.css', array() );
		wp_register_script( 'iwjmb-input-list', IWJ_FIELD_ASSETS_URL . 'js/input-list.js', array(), '', true );
	}

	/**
	 * Walk options
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @param mixed $options
	 * @param mixed $db_fields
	 *
	 * @return string
	 */
	public static function walk( $field, $options, $db_fields, $meta ) {
		$walker = new IWJMB_Walker_Input_List( $db_fields, $field, $meta );
		$output = sprintf( '<ul class="iwjmb-input-list %s %s">',
			$field['collapse'] ? 'collapse' : '',
		 	$field['inline']   ? 'inline'   : ''
		);
		$output .= $walker->walk( $options, $field['flatten'] ? - 1 : 0 );
		$output .= '</ul>';

		return $output;
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	public static function normalize( $field ) {
		$field = $field['multiple'] ? IWJMB_Multiple_Values_Field::normalize( $field ) : $field;
		$field = IWJMB_Input_Field::normalize( $field );
		$field = parent::normalize( $field );
		$field = wp_parse_args( $field, array(
			'collapse' => true,
			'inline'   => null,
		) );

		$field['flatten'] = $field['multiple'] ? $field['flatten'] : true;
		$field['inline'] = ! $field['multiple'] && ! isset( $field['inline'] ) ? true : $field['inline'];

		return $field;
	}

	/**
	 * Get the attributes for a field
	 *
	 * @param array $field
	 * @param mixed $value
	 *
	 * @return array
	 */
	public static function get_attributes( $field, $value = null ) {
		$attributes           = IWJMB_Input_Field::get_attributes( $field, $value );
		$attributes['id']     = false;
		$attributes['type']   = $field['multiple'] ? 'checkbox' : 'radio';
		$attributes['value']  = $value;

		return $attributes;
	}

    /**
     * Get field HTML
     *
     * @param mixed $meta
     * @param array $field
     *
     * @return string
     */
    public static function input( $meta, $field )
    {
        wp_enqueue_style('iwjmb-input-list');
        wp_enqueue_script('iwjmb-input-list');

        return parent::input( $meta, $field );
    }
}

IWJMB_Input_List_Field::init();