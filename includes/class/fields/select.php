<?php
/**
 * Select field class.
 */
class IWJMB_Select_Field extends IWJMB_Choice_Field {


    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

	/**
	 * Enqueue scripts and styles
	 */
	public static function enqueue_scripts() {
		wp_register_style( 'iwjmb-select', IWJ_FIELD_ASSETS_URL . 'css/select.css', array() );
		wp_register_script( 'iwjmb-select', IWJ_FIELD_ASSETS_URL . 'js/select.js', array(), '', true );
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
		$attributes = self::call( 'get_attributes', $field, $meta );
		$walker     = new IWJMB_Walker_Select( $db_fields, $field, $meta );
		$output     = sprintf(
			'<select %s>',
			self::render_attributes( $attributes )
		);
		if ( false === $field['multiple'] ) {
			$output .= $field['placeholder'] ? '<option value="">' . esc_html( $field['placeholder'] ) . '</option>' : '';
		}
		$output .= $walker->walk( $options, $field['flatten'] ? - 1 : 0 );
		$output .= '</select>';
		$output .= self::get_select_all_html( $field );
		return $output;
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	public static function normalize( $field ) {
		$field = parent::normalize( $field );
		$field = $field['multiple'] ? IWJMB_Multiple_Values_Field::normalize( $field ) : $field;
		$field = wp_parse_args( $field, array(
			'size'            => $field['multiple'] ? 5 : 0,
			'select_all_none' => false,
		) );

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
		$attributes = parent::get_attributes( $field, $value );
		$attributes = wp_parse_args( $attributes, array(
			'multiple' => $field['multiple'],
			'size'     => $field['size'],
		) );

		return $attributes;
	}

	/**
	 * Get html for select all|none for multiple select
	 *
	 * @param array $field
	 * @return string
	 */
	public static function get_select_all_html( $field ) {
		if ( $field['multiple'] && $field['select_all_none'] ) {
			return '<div class="iwjmb-select-all-none">' . __( 'Select', 'iwjob' ) . ': <a data-type="all" href="#">' . __( 'All', 'iwjob' ) . '</a> | <a data-type="none" href="#">' . __( 'None', 'iwjob' ) . '</a></div>';
		}
		return '';
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
        wp_enqueue_style('iwjmb-select');
        wp_enqueue_script('iwjmb-select');

        return parent::input( $meta, $field );
    }
}

IWJMB_Select_Field::init();