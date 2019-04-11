<?php
/**
 * Select tree field class.
 */
class IWJMB_Select_Tree_Field extends IWJMB_Select_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
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
		$walker = new IWJMB_Walker_Select_Tree( $db_fields, $field, $meta );
		return $walker->walk( $options );
	}

	/**
	 * Enqueue scripts and styles
	 */
	public static function enqueue_scripts() {
		wp_register_style( 'iwjmb-select-tree', IWJ_FIELD_ASSETS_URL . 'css/select-tree.css', array( 'iwjmb-select' ) );
		wp_register_script( 'iwjmb-select-tree', IWJ_FIELD_ASSETS_URL . 'js/select-tree.js', array( 'iwjmb-select' ), '', true );
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	public static function normalize( $field ) {
		$field['multiple'] = true;
		$field['size']     = 0;
		$field             = parent::normalize( $field );

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
		$attributes             = parent::get_attributes( $field, $value );
		$attributes['multiple'] = false;
		$attributes['id']       = false;

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
        wp_enqueue_style('iwjmb-select-tree');
        wp_enqueue_script('iwjmb-select-tree');

        return parent::input( $meta, $field );
    }
}

IWJMB_Select_Tree_Field::init();