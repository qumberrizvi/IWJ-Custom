<?php

class IWJ_Admin_Cat {
	static $fields = array();
	static public function init(){
        add_filter('manage_edit-iwj_cat_columns' , array(__CLASS__, 'manage_columns'));
		//new IWJ_Admin_Radiotax('iwj_cat', 'iwj_job');

        self::$fields = array(
            array(
                'name' => __( 'Icon Class', 'iwjob' ),
                'id'   => IWJ_PREFIX.'icon_class',
                'desc'   => __('You can use fontawesome icon class <a href="http://fontawesome.io/cheatsheet/">here</a> OR Ion icon class <a href="http://ionicons.com/cheatsheet.html">here</a> OR Inwave icon class <a href="http://www.inwavethemes.com/docs/inwave-job/3-inwave-jobs-plugin/3-10-inwave-custom-icon/">here</a>', 'iwjob'),
                'type' => 'text',
            ),
	        array(
		        'name' => __( 'Icon image', 'iwjob' ),
		        'id'   => IWJ_PREFIX.'icon_image',
		        'type' => 'image_advanced',
		        'max_file_uploads' => '1',
	        ),
            array(
                'name' => __( 'Background image', 'iwjob' ),
                'id'   => IWJ_PREFIX.'bg_image',
                'type' => 'image_advanced',
                'max_file_uploads' => '1',
            )
        );

        add_action( 'iwj_cat_add_form_fields', array(__CLASS__, 'add_form_fields'), 10, 1 );
        add_action( 'iwj_cat_edit_form_fields', array(__CLASS__, 'edit_form_fields'), 10, 2 );

        add_action( 'edited_iwj_cat', array(__CLASS__, 'save_custom_meta'), 10, 2 );
        add_action( 'create_iwj_cat', array(__CLASS__, 'save_custom_meta'), 10, 2 );

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

IWJ_Admin_Cat::init();
?>
