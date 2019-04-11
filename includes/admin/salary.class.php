<?php

class IWJ_Admin_Salary {
	static $fields = array();
	static public function init(){
        add_filter('manage_edit-iwj_salary_columns' , array(__CLASS__, 'manage_columns'));
		add_filter( 'manage_iwj_salary_custom_column', array(__CLASS__, 'manage_columns_content'), 10, 3 );
        self::$fields = array(
            array(
                'name' => __( 'Salary from', 'iwjob' ),
                'id'   => IWJ_PREFIX.'salary_from',
                'type' => 'text',
            ),
            array(
                'name' => __( 'Salary to', 'iwjob' ),
                'id'   => IWJ_PREFIX.'salary_to',
                'type' => 'text',
            ),
	        array(
		        'name' => __( 'Order', 'iwjob' ),
		        'id'   => IWJ_PREFIX.'salary_order',
		        'type' => 'number',
	        )
        );

        add_action( 'iwj_salary_add_form_fields', array(__CLASS__, 'add_form_fields'), 10, 1 );
        add_action( 'iwj_salary_edit_form_fields', array(__CLASS__, 'edit_form_fields'), 10, 2 );

        add_action( 'edited_iwj_salary', array(__CLASS__, 'save_custom_meta'), 10, 2 );
        add_action( 'create_iwj_salary', array(__CLASS__, 'save_custom_meta'), 10, 2 );

	}

    static function manage_columns($columns){
        unset($columns['posts']);
	    $columns['salary_order'] = __('Order', 'iwjob');
	    return $columns;
    }

    static function manage_columns_content($content, $column_name, $term_id){

	    if ( 'salary_order' == $column_name ) {
		    $term_meta = get_term_meta( $term_id, IWJ_PREFIX . 'salary_order', true );
		    if($term_meta){
			    $content = $term_meta;
		    }else{
			    $content = __('N/A', 'iwjob');
		    }

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
            //$field['readonly'] = true;
			$meta = IWJMB_Field::call( $field, 'term_meta', $term->term_id, true );
            IWJMB_Field::input($field, $meta );
		}
	}

	static function save_custom_meta($term_id, $taxonomy){
        /*foreach (self::$fields as $field){
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
        }*/

		$salary_order = isset($_POST[IWJ_PREFIX.'salary_order']) ? sanitize_text_field($_POST[IWJ_PREFIX.'salary_order']) : '';
		update_term_meta($term_id, IWJ_PREFIX.'salary_order', $salary_order);

        $salary_from = isset($_POST[IWJ_PREFIX.'salary_from']) ? sanitize_text_field($_POST[IWJ_PREFIX.'salary_from']) : '';
        $salary_to = isset($_POST[IWJ_PREFIX.'salary_to']) ? sanitize_text_field($_POST[IWJ_PREFIX.'salary_to']) : '';
        if($salary_to !== '' && $salary_to < $salary_from){
            $salary_to = $salary_from;
        }
        update_term_meta($term_id, IWJ_PREFIX.'salary_from', $salary_from);
        update_term_meta($term_id, IWJ_PREFIX.'salary_to', $salary_to);

        global $wpdb;
        if(!$salary_from && !$salary_to){
            $sql = "SELECT DISTINCT p.ID FROM {$wpdb->posts} AS p JOIN {$wpdb->postmeta} AS pm ON pm.post_id = p.ID 
                    JOIN {$wpdb->postmeta} AS pm2 ON pm2.post_id = p.ID 
                    WHERE p.post_type = %s AND p.post_status NOT IN ('auto-draft', 'revision') AND pm.meta_key = %s AND pm2.meta_key = %s  AND 
                     pm.meta_value = %s AND pm2.meta_value = %s";
            $sql = $wpdb->prepare($sql, 'iwj_job', IWJ_PREFIX.'salary_from', IWJ_PREFIX.'salary_to', '', '');

        }elseif($salary_from && !$salary_to){
            $sql = "SELECT DISTINCT p.ID FROM {$wpdb->posts} AS p JOIN {$wpdb->postmeta} AS pm ON pm.post_id = p.ID JOIN {$wpdb->postmeta} AS pm1 ON pm1.post_id = p.ID 
                    WHERE p.post_type = %s AND p.post_status NOT IN ('auto-draft', 'revision') AND pm.meta_key = %s AND pm1.meta_key = %s
                    AND (
                          (pm.meta_value != '' AND pm1.meta_value != '' AND (
                            (CAST(pm1.meta_value AS SIGNED) <= %d AND CAST(pm.meta_value AS SIGNED) >= %d) 
                             OR (CAST(pm1.meta_value AS SIGNED) >= %d) AND CAST(pm1.meta_value AS SIGNED) >= %d)
                            ) 
                          OR (pm1.meta_value = '' AND pm.meta_value != '' AND CAST(pm.meta_value AS SIGNED) > %d) 
                          OR (pm.meta_value = '' AND pm1.meta_value != '')
                    )";
            $sql = $wpdb->prepare($sql, 'iwj_job', IWJ_PREFIX.'salary_to', IWJ_PREFIX.'salary_from', $salary_from, $salary_from, $salary_from, $salary_from, $salary_from);
        }elseif(!$salary_from && $salary_to){
            $sql = "SELECT DISTINCT p.ID FROM {$wpdb->posts} AS p JOIN {$wpdb->postmeta} AS pm ON pm.post_id = p.ID JOIN {$wpdb->postmeta} AS pm1 ON pm1.post_id = p.ID 
                    WHERE p.post_type = %s AND p.post_status NOT IN ('auto-draft', 'revision') AND pm.meta_key = %s 
                   AND (
                      (pm.meta_value != '' AND pm1.meta_value != '' AND (
                        (CAST(pm1.meta_value AS SIGNED) <= %d AND CAST(pm.meta_value AS SIGNED) >= %d) 
                         OR (CAST(pm.meta_value AS SIGNED) <= %d) AND CAST(pm1.meta_value AS SIGNED) <= %d)
                      ) 
                      OR (pm1.meta_value = '' AND pm.meta_value != '') 
                      OR (pm.meta_value = '' AND pm1.meta_value != '' AND CAST(pm1.meta_value AS SIGNED) <= %d)
                   )";
            $sql = $wpdb->prepare($sql, 'iwj_job', IWJ_PREFIX.'salary_to', IWJ_PREFIX.'salary_from', $salary_to, $salary_to, $salary_to, $salary_to, $salary_to );
            echo $sql;exit;
        }else{
            $sql = "SELECT DISTINCT p.ID FROM {$wpdb->posts} AS p JOIN {$wpdb->postmeta} AS pm ON pm.post_id = p.ID 
                    JOIN {$wpdb->postmeta} AS pm2 ON pm2.post_id = p.ID 
                    WHERE p.post_type = %s AND p.post_status NOT IN ('auto-draft', 'revision') AND pm.meta_key = %s AND pm2.meta_key = %s  
                    AND CAST(pm.meta_value AS SIGNED) >= %d AND CAST(pm2.meta_value AS SIGNED) <= %d";

            $sql = $wpdb->prepare($sql,  'iwj_job', IWJ_PREFIX.'salary_from', IWJ_PREFIX.'salary_to', $salary_from, $salary_to);
        }

        $jobs = $wpdb->get_results($sql);
        if($jobs){
            foreach ($jobs as $job){
                wp_set_post_terms($job->ID, $term_id, 'iwj_salary', true);
            }
        }
	}
}

IWJ_Admin_Salary::init();
?>
