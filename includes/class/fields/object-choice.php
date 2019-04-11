<?php

/**
 * Abstract field to select an object: post, user, taxonomy, etc.
 */
abstract class IWJMB_Object_Choice_Field extends IWJMB_Choice_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

    /**
     * Enqueue scripts and styles
     */
    public static function enqueue_scripts() {
        IWJMB_Input_List_Field::enqueue_scripts();
        IWJMB_Select_Field::enqueue_scripts();
        IWJMB_Select_Tree_Field::enqueue_scripts();
        IWJMB_Select_Advanced_Field::enqueue_scripts();
    }

	/**
	 * Get field HTML
	 *
	 * @param mixed $options
	 * @param mixed $db_fields
	 * @param mixed $meta
	 * @param array $field
	 * @return string
	 */
	public static function walk( $field, $options, $db_fields, $meta ) {
		return call_user_func( array( self::get_type_class( $field ), 'walk' ), $field, $options, $db_fields, $meta );
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	public static function normalize( $field ) {
		$field = parent::normalize( $field );
		$field = wp_parse_args( $field, array(
			'flatten'    => true,
			'query_args' => array(),
			'field_type' => 'select_advanced',
		) );

		if ( 'checkbox_tree' === $field['field_type'] ) {
			$field['field_type'] = 'checkbox_list';
			$field['flatten']    = false;
		}
		if ( 'radio_list' == $field['field_type'] ) {
			$field['multiple'] = false;
		}
		if ( 'checkbox_list' == $field['field_type'] ) {
			$field['multiple'] = true;
		}
		return call_user_func( array( self::get_type_class( $field ), 'normalize' ), $field );
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
		$attributes = call_user_func( array( self::get_type_class( $field ), 'get_attributes' ), $field, $value );
		if ( 'select_advanced' == $field['field_type'] ) {
			$attributes['class'] .= ' iwjmb-select_advanced';
		}
		return $attributes;
	}

	/**
	 * Get field names of object to be used by walker
	 *
	 * @return array
	 */
	public static function get_db_fields() {
		return array(
			'parent' => '',
			'id'     => '',
			'label'  => '',
		);
	}

	/**
	 * Get correct rendering class for the field.
	 *
	 * @param array $field Field parameter
	 * @return string
	 */
	protected static function get_type_class( $field ) {
		if ( in_array( $field['field_type'], array( 'checkbox_list', 'radio_list' ) ) ) {
			return 'IWJMB_Input_List_Field';
		}
		return self::get_class_name( array( 'type' => $field['field_type'] ) );
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

        wp_enqueue_style('iwjmb-select');
        wp_enqueue_script('iwjmb-select');

        wp_enqueue_style('iwjmb-select-tree');
        wp_enqueue_script('iwjmb-select-tree');

        wp_enqueue_style( 'iwjmb-select2');
        wp_enqueue_style( 'iwjmb-select-advanced');
        wp_enqueue_script( 'iwjmb-select2');
        wp_enqueue_script( 'iwjmb-select');
        wp_enqueue_script( 'iwjmb-select-advanced');

        return parent::input( $meta, $field );
    }


}

IWJMB_Object_Choice_Field::init();