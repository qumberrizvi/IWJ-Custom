<?php

class IWJ_Admin_Keyword {
    static $fields;

	static public function init(){
        add_filter('manage_edit-iwj_keyword_columns' , array(__CLASS__, 'manage_columns'));
        add_filter('manage_iwj_keyword_custom_column' , array(__CLASS__, 'manage_columns_content'), 10, 3);
        add_action( 'create_iwj_keyword', array(__CLASS__, 'save_custom_meta'), 10, 2 );

        self::$fields = array(
            array(
                'name' => __( 'Searched', 'iwjob' ),
                'id'   => IWJ_PREFIX.'searched',
                'type' => 'number',
                'std' => 0,
                'required' => true,
            ),
        );

        add_action( 'iwj_keyword_add_form_fields', array(__CLASS__, 'add_form_fields'), 10, 1 );
        add_action( 'iwj_keyword_edit_form_fields', array(__CLASS__, 'edit_form_fields'), 10, 2 );

        add_action( 'edited_iwj_keyword', array(__CLASS__, 'save_custom_meta'), 10, 2 );
        add_action( 'create_iwj_keyword', array(__CLASS__, 'save_custom_meta'), 10, 2 );
    }

    static function manage_columns($columns){
	    unset($columns['posts']);
	    $columns['searched'] = __('Searched', 'iwjob');

        return $columns;
	}

    static function manage_columns_content($content, $column_name, $term_id){
        if ( 'searched' == $column_name ) {
            $content = (int)get_term_meta($term_id, IWJ_PREFIX.'searched', true);
        }

        return $content;
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

IWJ_Admin_Keyword::init();
?>
