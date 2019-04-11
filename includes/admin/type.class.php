<?php

class IWJ_Admin_Type {
    static $fields = array();

    static public function init(){
        add_filter('manage_edit-iwj_type_columns' , array(__CLASS__, 'manage_columns'));
        self::$fields = array(
            array(
                'name' => __( 'Main Color', 'iwjob' ),
                'id'   => IWJ_PREFIX.'color',
                'type' => 'color',
            )
        );

        if(!iwj_option('disable_type')){
            new IWJ_Admin_Radiotax('iwj_type', 'iwj_job');
        }

        add_action( 'iwj_type_add_form_fields', array(__CLASS__, 'add_form_fields'), 10, 1 );
        add_action( 'iwj_type_edit_form_fields', array(__CLASS__, 'edit_form_fields'), 10, 2 );
        add_action( 'edited_iwj_type', array(__CLASS__, 'save_custom_meta'), 10, 2 );
        add_action( 'create_iwj_type', array(__CLASS__, 'save_custom_meta'), 10, 2 );
	}

    static function manage_columns($columns){
        unset($columns['posts']);
        return $columns;
    }

    static function add_form_fields($taxonomy){
        foreach (self::$fields as $field){
            $field['parent_tag'] = 'div';
            $field = IWJMB_Field::call( 'normalize', $field );
            $meta = IWJMB_Field::call( $field, 'term_meta', 0, false );
            IWJMB_Field::input($field, $meta );
        }
    }

    static function edit_form_fields($term, $taxonomy){
        foreach (self::$fields as $field){
            $field = IWJMB_Field::call( 'normalize', $field );
            $meta = IWJMB_Field::call( $field, 'term_meta', $term->term_id, true );
            IWJMB_Field::input($field, $meta );
        }
    }

    static function save_custom_meta($term_id, $taxonomy){
        foreach (self::$fields as $field){
            $field = IWJMB_Field::call( 'normalize', $field );

            $single = $field['clone'] || ! $field['multiple'];
            $old    = IWJMB_Field::call( $field, 'raw_term_meta', $term_id );
            $new    = isset( $_POST[ $field['id'] ] ) ? $_POST[ $field['id'] ] : ( $single ? '' : array() );
            // Allow field class change the value
            if ( $field['clone'] ) {
                $new = IWJMB_Clone::value( $new, $old, $term_id, $field );
            } else {
                $new = IWJMB_Field::call( $field, 'value', $new, $old, $term_id );
                $new = IWJMB_Field::call( $field, 'sanitize_value', $new);
            }

            // Call defined method to save meta value, if there's no methods, call common one
            IWJMB_Field::call( $field, 'save_term', $new, $old, $term_id );
        }
    }
}

IWJ_Admin_Type::init();
?>
