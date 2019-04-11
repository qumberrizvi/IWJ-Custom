<?php

/**
 * Base field class which defines all necessary methods.
 * Fields must inherit this class and overwrite methods with its own.
 */

define( 'IWJ_FIELD_DIR', IWJ_PLUGIN_DIR.'/includes/class/fields/' );
define( 'IWJ_FIELD_ASSETS_URL', IWJ_PLUGIN_URL.'/includes/class/fields/assets/' );

abstract class IWJMB_Field {

	/**
	 * Add actions
	 */
	public static function init() {

        include_once IWJ_PLUGIN_DIR.'includes/class/fields/walkers/base.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/walkers/input-list.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/walkers/select.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/walkers/select-tree.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/clone.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/input.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/button.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/choice.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/object-choice.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/multiple-values.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/input-list.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/text.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/textarea.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/radio.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/checkbox.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/checkbox-list.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/select.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/select-advanced.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/select-tree.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/wysiwyg.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/file.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/media.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/file-input.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/file-upload.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/image.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/image-advanced.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/image-select.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/image-upload.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/image-single.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/datetime.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/date.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/color.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/taxonomy.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/taxonomy-advanced.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/map.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/map_address.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/group.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/post.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/user.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/autocomplete.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/simple-autocomplete.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/tagable.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/user-ajax.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/taxonomy2.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/cv.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/fields/avatar.php';
        //include_once IWJ_PLUGIN_DIR.'includes/class/fields/oembed.php';

        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

	}

	static function enqueue_scripts(){
        wp_register_style( 'iwjob', IWJ_FIELD_ASSETS_URL . 'css/style.css', array() );
        wp_register_script( 'iwjmb-clone', IWJ_FIELD_ASSETS_URL . 'js/clone.js', array('jquery', 'jquery-ui-sortable'), '', true);
    }

	/**
	 * Add actions
	 */
	public static function add_actions() {
	}

	/**
	 * ELocalize scripts
	 */
	public static function localize_script( $handle, $name, $data ) {
		/**
		 * Prevent loading localized string twice.
		 *
		 * @link https://github.com/rilwis/meta-box/issues/850
		 */
		$wp_scripts = wp_scripts();
		if ( ! $wp_scripts->get_data( $handle, 'data' ) ) {
			wp_localize_script( $handle, $name, $data );
		}
	}

	/**
	 * Input field HTML
	 * Filters are put inside this method, not inside methods such as "meta", "html", "begin_input", etc.
	 * That ensures the returned value are always been applied filters
	 * This method is not meant to be overwritten in specific fields
	 *
	 * @param array $field
	 * @param bool  $saved
	 */
	public static function input( $field, $meta ) {

        wp_enqueue_style( 'iwjob' );
        wp_enqueue_script( 'iwjmb-clone');

		$begin = self::call( $field, 'begin_input', $meta);

		// Separate code for cloneable and non-cloneable fields to make easy to maintain
		// Cloneable fields
		if ( $field['clone'] ) {
			$field_html = IWJMB_Clone::input( $meta, $field );
		} // End if().
		else {
			// Call separated methods for displaying each type of field
			$field_html = self::call( $field, 'input', $meta );
		}

		$end = self::call( $field, 'end_input', $meta);

		$html = "$begin$field_html$end";

		// Display label and input in DIV and allow user-defined classes to be appended
		$classes = "iwjmb-field iwjmb-{$field['type']}-wrapper " . $field['class'];
		if ( 'hidden' === $field['type'] ) {
			$classes .= ' hidden';
		}
		if ( ! empty( $field['required'] ) ) {
			$classes .= ' required';
		}

		$markup_html = $field['parent_tag'] == 'div' ? '<div class="%s">%s</div>' : '<tr class="%s">%s</tr>';
		$outer_html = sprintf(
			$field['before'] . $markup_html . $field['after'],
			trim( $classes ),
			$html
		);

		echo $outer_html;
	}

	/**
	 * Show begin HTML markup for fields
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	public static function begin_input( $meta, $field ) {
		$field_label = '';
		if ( $field['name'] ) {
            if(isset($field['no_for_label']) && $field['no_for_label']){
                $markup_html = $field['parent_tag'] == 'div' ? '<div class="iwjmb-label"><label>%s</label></div>' : '<th class="iwjmb-label"><label>%s</label></th>';
                $field_label = sprintf(
                    $markup_html,
                    $field['name']
                );
            }else{
                $markup_html = $field['parent_tag'] == 'div' ? '<div class="iwjmb-label"><label for="%s">%s</label></div>' : '<th class="iwjmb-label"><label for="%s">%s</label></th>';
                $field_label = sprintf(
                    $markup_html,
                    $field['id'],
                    $field['name']
                );
            }
        }

		$data_max_clone = is_numeric( $field['max_clone'] ) && $field['max_clone'] > 1 ? ' data-max-clone=' . $field['max_clone'] : '';

        $markup_html = $field['parent_tag'] == 'div' ? '<div class="iwjmb-input"%s>' : '<td colspan="'.($field['name'] ? 1 : 2).'"><div class="iwjmb-input"%s>';
        $input_open = sprintf(
            $markup_html,
			$data_max_clone
		);

		return $field_label . $input_open;
	}

	/**
	 * Show end HTML markup for fields
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	public static function end_input( $meta, $field ) {
		return IWJMB_Clone::add_clone_button( $field ) . self::call( 'element_description', $field ) . '</div>'.($field['parent_tag'] == 'div' ? '' : '</td>');
	}

	/**
	 * Display field description.
	 *
	 * @param array $field
	 * @return string
	 */
	public static function element_description( $field ) {
		$id = $field['id'] ? " id='{$field['id']}-description'" : '';
		return $field['desc'] ? "<p{$id} class='description'>{$field['desc']}</p>" : '';
	}

	/**
	 * Get raw meta value for term
	 *
	 * @param int   $post_id
	 * @param array $field
	 *
	 * @return mixed
	 */
	public static function raw_term_meta( $term_id, $field ) {
		if ( empty( $field['id'] ) ) {
			return '';
		}

		$single = $field['clone'] || ! $field['multiple'];

		return get_term_meta($term_id, $field['id'], $single );
	}

    /**
     * Get term meta value
     *
     * @param int   $term_id
     * @param bool  $saved
     * @param array $field
     *
     * @return mixed
     */
    public static function term_meta( $term_id, $saved, $field ) {
        /**
         * For special fields like 'divider', 'heading' which don't have ID, just return empty string
         * to prevent notice error when displaying fields
         */
        if ( empty( $field['id'] ) ) {
            return '';
        }

        // Get raw meta
        $meta = self::call( $field, 'raw_term_meta', $term_id );

        // Use $field['std'] only when the meta box hasn't been saved (i.e. the first time we run)
        $meta = ! $saved ? $field['std'] : $meta;
        //has custom

        if(isset($field['no_save'])){
            $meta = $field['std'];
        }

        // Escape attributes
        $meta = self::call( $field, 'esc_meta', $meta );

        // Make sure meta value is an array for clonable and multiple fields
        if ( $field['clone'] || $field['multiple'] ) {
            if ( empty( $meta ) || ! is_array( $meta ) ) {
                /**
                 * Note: if field is clonable, $meta must be an array with values
                 * so that the foreach loop in self::show() runs properly
                 *
                 * @see self::show()
                 */
                $meta = $field['clone'] ? array( '' ) : array();
            }
        }

        return $meta;
    }

	/**
	 * Get raw meta value
	 *
	 * @param int   $post_id
	 * @param array $field
	 *
	 * @return mixed
	 */
	public static function raw_post_meta( $post_id, $field ) {
		if ( empty( $field['id'] ) ) {
			return '';
		}

		$single = $field['clone'] || ! $field['multiple'];
		return get_post_meta( $post_id, $field['id'], $single );
	}

	/**
	 * Get meta value
	 *
	 * @param int   $post_id
	 * @param bool  $saved
	 * @param array $field
	 *
	 * @return mixed
	 */
	public static function post_meta( $post_id, $saved, $field ) {
		/**
		 * For special fields like 'divider', 'heading' which don't have ID, just return empty string
		 * to prevent notice error when displaying fields
		 */
		if ( empty( $field['id'] ) ) {
			return '';
		}

		// Get raw meta
        if($saved){
            $meta = self::call( $field, 'raw_post_meta', $post_id );
        }else{
            // Use $field['std'] only when the meta box hasn't been saved (i.e. the first time we run)
            $meta = $field['std'];

        }

        //has custom
        if(isset($field['no_save'])){
            $meta = $field['std'];
        }

		// Escape attributes
		$meta = self::call( $field, 'esc_meta', $meta );

		// Make sure meta value is an array for clonable and multiple fields
		if ( $field['clone'] || $field['multiple'] ) {
			if ( empty( $meta ) || ! is_array( $meta ) ) {
				/**
				 * Note: if field is clonable, $meta must be an array with values
				 * so that the foreach loop in self::show() runs properly
				 *
				 * @see self::show()
				 */
				$meta = $field['clone'] ? array( '' ) : array();
			}
		}

		return $meta;
	}

	/**
	 * Escape meta for field output
	 *
	 * @param mixed $meta
	 *
	 * @return mixed
	 */
	public static function esc_meta( $meta ) {
		return is_array( $meta ) ? array_map( __METHOD__, $meta ) : esc_attr( $meta );
	}

	/**
	 * Set value of meta before saving into database
	 *
	 * @param mixed $new
	 * @param mixed $old
	 * @param int   $post_id
	 * @param array $field
	 *
	 * @return int
	 */
	public static function value( $new, $old, $post_id, $field ) {

        return $new;
	}

	/**
	 * Save post meta value
	 *
	 * @param $new
	 * @param $old
	 * @param $post_id
	 * @param $field
	 */
	public static function save_post( $new, $old, $post_id, $field ) {
		$name = $field['id'];

		// Remove post meta if it's empty
		if ( ('' === $new && (!isset($field['allways_save']) || !$field['allways_save'])) || array() === $new ) {
			delete_post_meta( $post_id, $name );
			return;
		}

		// If field is cloneable, value is saved as a single entry in the database
		if ( $field['clone'] ) {
			// Remove empty values
			$new = (array) $new;
			foreach ( $new as $k => $v ) {
				if ( '' === $v || array() === $v ) {
					unset( $new[ $k ] );
				}
			}
			// Reset indexes
			$new = array_values( $new );
			update_post_meta( $post_id, $name, $new );
			return;
		}

		// If field is multiple, value is saved as multiple entries in the database (WordPress behaviour)
		if ( $field['multiple'] ) {
			$new_values = array_diff( $new, $old );
			foreach ( $new_values as $new_value ) {
				add_post_meta( $post_id, $name, $new_value, false );
			}
			$old_values = array_diff( $old, $new );
			foreach ( $old_values as $old_value ) {
				delete_post_meta( $post_id, $name, $old_value );
			}
			return;
		}

		// Default: just update post meta
		update_post_meta( $post_id, $name, $new );
	}

	/**
	 * Save term meta value
	 *
	 * @param $new
	 * @param $old
	 * @param $term_id
	 * @param $field
	 */
	public static function save_term( $new, $old, $term_id, $field ) {
		$name = $field['id'];

		// Remove post meta if it's empty
		if ( '' === $new || array() === $new ) {
			delete_term_meta( $term_id, $name );
			return;
		}

		// If field is cloneable, value is saved as a single entry in the database
		if ( $field['clone'] ) {
			// Remove empty values
			$new = (array) $new;
			foreach ( $new as $k => $v ) {
				if ( '' === $v || array() === $v ) {
					unset( $new[ $k ] );
				}
			}
			// Reset indexes
			$new = array_values( $new );
			update_term_meta( $term_id, $name, $new );
			return;
		}

		// If field is multiple, value is saved as multiple entries in the database (WordPress behaviour)
		if ( $field['multiple'] ) {
			$new_values = array_diff( $new, $old );
			foreach ( $new_values as $new_value ) {
				add_term_meta( $term_id, $name, $new_value, false );
			}
			$old_values = array_diff( $old, $new );
			foreach ( $old_values as $old_value ) {
				delete_term_meta( $term_id, $name, $old_value );
			}
			return;
		}

		// Default: just update post meta
		update_term_meta( $term_id, $name, $new );
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	public static function normalize( $field ) {
		$field = wp_parse_args( $field, array(
			'id'          => '',
			'name'        => '',
			'multiple'    => false,
			'std'         => '',
			'desc'        => '',
			'format'      => '',
			'before'      => '',
			'after'       => '',
			'field_name'  => isset( $field['id'] ) ? $field['id'] : '',
			'placeholder' => '',
			'parent_tag' => is_blog_admin() ? 'tr' : 'div' ,

			'clone'      => false,
			'max_clone'  => 0,
			'sort_clone' => false,

			'class'      => '',
			'disabled'   => false,
			'required'   => false,
			'attributes' => array(),
		) );

		//consider use for multiple language
		//$field = self::disable_translate_field($field);

		return $field;
	}

	static function disable_translate_field($field){
	    static $disable_translate = null;
	    if($disable_translate === null && function_exists('get_current_screen')){
            $current_screen = get_current_screen();
            if($current_screen && isset($current_screen->base) && $current_screen->base == 'iwj_job_page_iwj-setting-page'){
                if(defined('ICL_LANGUAGE_CODE')){
                    global $sitepress;
                    if(ICL_LANGUAGE_CODE != $sitepress->get_default_language()){
                        $disable_translate = true;
                    }
                }
            }

            if($disable_translate === null){
                $disable_translate = false;
            }
        }
        if($disable_translate){
            if(!isset($field['allow_translate']) || !$field['allow_translate']){
                $field['disabled'] = true;
            }
        }

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
		$attributes = wp_parse_args( $field['attributes'], array(
			'disabled' => $field['disabled'],
			'required' => $field['required'],
			'id'       => $field['id'],
			'class'    => '',
			'name'     => $field['field_name'],
		) );

		$attributes['class'] = implode( ' ', array_merge( array( "iwjmb-{$field['type']}" ), (array) $attributes['class'] ) );

		return $attributes;
	}

	/**
	 * Renders an attribute array into an html attributes string
	 *
	 * @param array $attributes
	 *
	 * @return string
	 */
	public static function render_attributes( $attributes ) {
		$output = '';

		foreach ( $attributes as $key => $value ) {
			if ( false === $value || '' === $value ) {
				continue;
			}

			if ( is_array( $value ) ) {
				$value = json_encode( $value );
			}

			$output .= sprintf( true === $value ? ' %s' : ' %s="%s"', $key, esc_attr( $value ) );
		}

		return $output;
	}

	/**
	 * Get the field value
	 * The difference between this function and 'meta' function is 'meta' function always returns the escaped value
	 * of the field saved in the database, while this function returns more meaningful value of the field, for ex.:
	 * for file/image: return array of file/image information instead of file/image IDs
	 *
	 * Each field can extend this function and add more data to the returned value.
	 * See specific field classes for details.
	 *
	 * @param  array    $field   Field parameters
	 * @param  array    $args    Additional arguments. Rarely used. See specific fields for details
	 * @param  int|null $post_id Post ID. null for current post. Optional.
	 *
	 * @return mixed Field value
	 */
	public static function get_value( $field, $args = array(), $post_id = null ) {
		// Some fields does not have ID like heading, custom HTML, etc.
		if ( empty( $field['id'] ) ) {
			return '';
		}

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		// Get raw meta value in the database, no escape
		$value  = IWJMB_Field::call( $field, 'raw_post_meta', $post_id );

		// Make sure meta value is an array for cloneable and multiple fields
		if ( $field['clone'] || $field['multiple'] ) {
			$value = is_array( $value ) && $value ? $value : array();
		}

		return $value;
	}

	/**
	 * Output the field value
	 * Depends on field value and field types, each field can extend this method to output its value in its own way
	 * See specific field classes for details.
	 *
	 * Note: we don't echo the field value directly. We return the output HTML of field, which will be used in
	 * iwjmb_the_field function later.
	 *
	 * @use self::get_value()
	 * @see iwjmb_the_value()
	 *
	 * @param  array    $field   Field parameters
	 * @param  array    $args    Additional arguments. Rarely used. See specific fields for details
	 * @param  int|null $post_id Post ID. null for current post. Optional.
	 *
	 * @return string HTML output of the field
	 */
	public static function the_value( $field, $args = array(), $post_id = null ) {
		$value = self::call( 'get_value', $field, $args, $post_id );
		return self::call( 'format_value', $field, $value );
	}

	/**
	 * Format value for the helper functions.
	 *
	 * @param array        $field Field parameter
	 * @param string|array $value The field meta value
	 * @return string
	 */
	public static function format_value( $field, $value ) {
		if ( ! is_array( $value ) ) {
			return self::call( 'format_single_value', $field, $value );
		}
		$output = '<ul>';
		foreach ( $value as $subvalue ) {
			$output .= '<li>' . self::call( 'format_value', $field, $subvalue ) . '</li>';
		}
		$output .= '</ul>';
		return $output;
	}

	/**
	 * Format a single value for the helper functions. Sub-fields should overwrite this method if necessary.
	 *
	 * @param array  $field Field parameter
	 * @param string $value The value
	 * @return string
	 */
	public static function format_single_value( $field, $value ) {
		return $value;
	}

    /**
     * Sanitize value for the helper functions.
     *
     * @param array        $field Field parameter
     * @param string|array $value The field meta value
     * @return string
     */
    public static function sanitize_value( $value, $field ) {
        switch ($field['type']){
            case 'email':
                $value = is_array($value) ? array_map('sanitize_email', $value) : sanitize_email($value);
                break;
            case 'file_input':
            case 'oembed':
            case 'url':
                $value = is_array($value) ? array_map('esc_url_raw', $value) : esc_url_raw($value);
                break;
            case 'wysiwyg' :
                $value = is_array($value) ? array_map('wp_kses_post', $value) : wp_kses_post($value);
                break;
            default:
            break;
        }

        return $value;
    }


    /**
	 * Call a method of a field.
	 * This should be replaced by static::$method( $args ) in PHP 5.3.
	 *
	 * @return mixed
	 */
	public static function call() {
		$args = func_get_args();

		$check = reset( $args );

		// Params: method name, field, other params.
		if ( is_string( $check ) ) {
			$method = array_shift( $args );
			$field  = reset( $args ); // Keep field as 1st param
		} // End if().
		else {
			$field  = array_shift( $args );
			$method = array_shift( $args );
			$args[] = $field; // Add field as last param
		}

		return call_user_func_array( array( self::get_class_name( $field ), $method ), $args );
	}

	/**
	 * Get field class name
	 *
	 * @param array $field Field array
	 * @return string Field class name
	 */
	public static function get_class_name( $field ) {
		$type = $field['type'];
		if ( 'file_advanced' == $field['type'] ) {
			$type = 'media';
		}
		if ( 'plupload_image' == $field['type'] ) {
			$type = 'image_upload';
		}
		$type  = str_replace( array( '-', '_' ), ' ', $type );
		$class = 'IWJMB_' . ucwords( $type ) . '_Field';
		$class = str_replace( ' ', '_', $class );
		return class_exists( $class ) ? $class : 'IWJMB_Input_Field';
	}

	/**
	 * Apply various filters based on field type, id.
	 * Filters:
	 * - iwjmb_{$name}
	 * - iwjmb_{$field['type']}_{$name}
	 * - iwjmb_{$field['id']}_{$name}
	 *
	 * @return mixed
	 */
	public static function filter() {
		$args = func_get_args();

		// 3 first params must be: filter name, value, field. Other params will be used for filters.
		$name  = array_shift( $args );
		$value = array_shift( $args );
		$field = array_shift( $args );

		// List of filters
		$filters = array(
			'iwjmb_' . $name,
			'iwjmb_' . $field['type'] . '_' . $name,
		);
		if ( isset( $field['id'] ) ) {
			$filters[] = 'iwjmb_' . $field['id'] . '_' . $name;
		}

		// Filter params: value, field, other params. Note: value is changed after each run.
		array_unshift( $args, $field );
		foreach ( $filters as $filter ) {
			$filter_args = $args;
			array_unshift( $filter_args, $value );
			$value = apply_filters_ref_array( $filter, $filter_args );
		}

		return $value;
	}
}

IWJMB_Field::init();