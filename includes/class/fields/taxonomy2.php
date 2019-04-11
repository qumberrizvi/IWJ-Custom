<?php
/**
 * Taxonomy field class which set post terms when saving.
 */
class IWJMB_Taxonomy2_Field extends IWJMB_Choice_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

    /**
     * Enqueue scripts and styles
     */
    public static function enqueue_scripts() {
        wp_register_style( 'bootstrap-multiselect', IWJ_FIELD_ASSETS_URL . 'css/bootstrap-multiselect.css', array() );
        wp_register_script( 'bootstrap-multiselect', IWJ_FIELD_ASSETS_URL . 'js/bootstrap-multiselect.js', array(), '', true );
        wp_register_style( 'iwjmb-taxonomy2', IWJ_FIELD_ASSETS_URL . 'css/taxonomy2.css', array() );
        wp_register_script( 'iwjmb-taxonomy2', IWJ_FIELD_ASSETS_URL . 'js/taxonomy2.js', array(), '', true );
        self::localize_script( 'iwjmb-taxonomy2', 'i18niwjmbTaxonomy2', array(
            'selectAllText'                => _x( ' Select all', 'taxonomy2', 'iwjob' ) ,
            'filterPlaceholder'             => _x( ' Search', 'taxonomy2', 'iwjob' ) ,
            'nonSelectedText'           => _x( ' None selected', 'taxonomy2', 'iwjob' ) ,
            'allSelectedText'             => _x( 'All selected', 'taxonomy2', 'iwjob' ),
        ) );
        self::localize_script( 'bootstrap-multiselect', 'i18niwjmbmultiselect', array(
            'selectAllText'                => _x( ' Select all', 'taxonomy2', 'iwjob' ) ,
            'filterPlaceholder'             => _x( ' Search', 'taxonomy2', 'iwjob' ) ,
            'nonSelectedText'           => _x( ' None selected', 'taxonomy2', 'iwjob' ) ,
            'allSelectedText'             => _x( 'All selected', 'taxonomy2', 'iwjob' ),
        ) );
    }

    /**
	 * Add default value for 'taxonomy' field
	 *
	 * @param $field
	 * @return array
	 */
	public static function normalize( $field ) {
		/**
		 * Backwards compatibility with field args
		 */
		if ( isset( $field['options']['args'] ) ) {
			$field['query_args'] = $field['options']['args'];
		}
		if ( isset( $field['options']['taxonomy'] ) ) {
			$field['taxonomy'] = $field['options']['taxonomy'];
		}
		/**
		 * Set default field args
		 */
		$field = parent::normalize( $field );
		$field = wp_parse_args( $field, array(
			'taxonomy'   => 'category',
			'hierarchy'   => false,
		) );

		/**
		 * Set default query args
		 */
		$field['query_args'] = wp_parse_args( $field['query_args'], array(
			'hide_empty' => false,
		) );

		/**
		 * Set default placeholder
		 * - If multiple taxonomies: show 'Select a term'
		 * - If single taxonomy: show 'Select a %taxonomy_name%'
		 */
		if ( empty( $field['placeholder'] ) ) {
			$field['placeholder'] = __( 'Select a term', 'iwjob' );
			if ( is_string( $field['taxonomy'] ) && taxonomy_exists( $field['taxonomy'] ) ) {
				$taxonomy_object      = get_taxonomy( $field['taxonomy'] );
				$field['placeholder'] = sprintf( __( 'Select a %s', 'iwjob' ), $taxonomy_object->labels->singular_name );
			}
		}

        $field['js_options'] = wp_parse_args( $field['js_options'], array(
            'enableFiltering'  => true,
            'enableCaseInsensitiveFiltering' => true,
            'numberDisplayed' => 3,
            'placeholder' => $field['placeholder'],
        ) );

		/**
		 * Prevent cloning for taxonomy field
		 */
		$field['clone'] = false;

        $field = $field['multiple'] ? IWJMB_Multiple_Values_Field::normalize( $field ) : $field;

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
        $attributes = parent::get_attributes($field, $value);
        if($field['multiple']){
            $attributes['multiple'] = 'multiple';
        }

        $attributes = wp_parse_args( $attributes, array(
            'data-options' => wp_json_encode( $field['js_options'] ),
        ));

        return $attributes;
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
        $output .= $walker->walk( $options, $field['flatten'] ? - 1 : 0 );
        $output .= '</select>';
        return $output;
    }

    /**
	 * Get options for selects, checkbox list, etc via the terms
	 *
	 * @param array $field Field parameters
	 *
	 * @return array
	 */
	public static function get_options( $field ) {
		$options = get_terms( $field['taxonomy'], $field['query_args'] );
		return $options;
	}

	/**
	 * Save post meta value
	 *
	 * @param mixed $new
	 * @param mixed $old
	 * @param int   $post_id
	 * @param array $field
	 */
	public static function save_post( $new, $old, $post_id, $field ) {
		$new = array_unique( array_map( 'intval', (array) $new ) );
		$new = empty( $new ) ? null : $new;

		wp_set_object_terms( $post_id, $new, $field['taxonomy'] );
	}

	/**
	 * Save term meta value
	 *
	 * @param mixed $new
	 * @param mixed $old
	 * @param int   $post_id
	 * @param array $field
	 */
	public static function save_term( $new, $old, $term_id, $field ) {
        if ( $new ) {
            update_term_meta( $term_id, $field['id'], $new );
        } else { delete_term_meta( $term_id, $field['id'] );
        }
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

		$meta = get_the_terms( $post_id, $field['taxonomy'] );
		$meta = (array) $meta;
		return wp_list_pluck( $meta, 'term_id' );
	}

	/**
	 * Get raw meta value
	 *
	 * @param int   $term_id
	 * @param array $field
	 *
	 * @return mixed
	 */
	public static function raw_term_meta( $term_id, $field ) {
        $meta = get_term_meta( $term_id, $field['id'], true );
        $meta = wp_parse_id_list( $meta );
        return array_filter( $meta );
	}

	/**
	 * Get the field value
	 * Return list of post term objects
	 *
	 * @param  array    $field   Field parameters
	 * @param  array    $args    Additional arguments. Rarely used. See specific fields for details
	 * @param  int|null $post_id Post ID. null for current post. Optional.
	 *
	 * @return array List of post term objects
	 */
	public static function get_value( $field, $args = array(), $post_id = null ) {
		$value = get_the_terms( $post_id, $field['taxonomy'] );

		// Get single value if necessary
		if ( ! $field['clone'] && ! $field['multiple'] && is_array( $value ) ) {
			$value = reset( $value );
		}
		return $value;
	}

	/**
	 * Get option label
	 *
	 * @param string $value Option value
	 * @param array  $field Field parameter
	 *
	 * @return string
	 */
	public static function get_option_label( $field, $value ) {
		return sprintf(
			'<a href="%s" title="%s">%s</a>',
			esc_url( get_term_link( $value ) ),
			esc_attr( $value->name ),
			$value->name
		);
	}


    /**
     * Get field names of object to be used by walker
     *
     * @return array
     */
    public static function get_db_fields() {
        return array(
            'parent' => 'parent',
            'id'     => 'term_id',
            'label'  => 'name',
        );
    }

    public static function input( $meta, $field )
    {
        wp_enqueue_style( 'bootstrap-multiselect');
        wp_enqueue_script( 'bootstrap-multiselect');
        wp_enqueue_style( 'iwjmb-taxonomy2');
        wp_enqueue_script( 'iwjmb-taxonomy2');

        if($field['hierarchy']){
            $meta = (array)$meta;
            $options = iwj_get_term_hierarchy($field['taxonomy'], 0, 0, $field['query_args']);
            $attributes = IWJMB_Field::call( 'get_attributes', $field, $meta);

            $output   = sprintf(
                '<select %s>',
                IWJMB_Field::render_attributes( $attributes )
            );
            if($options){
                foreach ($options as $option){
                    $output .= '<option value="'.$option->term_id.'" data-level="'.$option->level.'" '.(in_array($option->term_id, $meta) ? 'selected' : '').'>'.$option->name.'</option>';
                }
            }
            $output .= '</select>';

            return $output;
        }else{
            return parent::input( $meta, $field );
        }

    }
}

IWJMB_Taxonomy2_Field::init();