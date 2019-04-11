<?php

/**
 * Datetime field class.
 */
class IWJMB_Datetime_Field extends IWJMB_Text_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

	/**
	 * Register scripts and styles
	 */
	public static function enqueue_scripts() {
		wp_register_style( 'datetimepicker', IWJ_FIELD_ASSETS_URL . 'css/jquery.datetimepicker.css', array() );
		wp_register_script( 'datetimepicker', IWJ_FIELD_ASSETS_URL . 'js/jquery.datetimepicker.full.min.js', array( 'jquery' ), '', true );
		wp_register_script( 'datetimepicker-settup', IWJ_FIELD_ASSETS_URL . 'js/datetimepicker-settup.js', array( 'datetimepicker' ), '', true );
		$locale_short = substr( get_locale(), 0, 2 );
        self::localize_script('datetimepicker-settup', 'iwjmbDateTime', array(
            'locale_short' => $locale_short,
        ) );
	}


	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	public static function input( $meta, $field ) {
        wp_enqueue_style('datetimepicker');
        wp_enqueue_script('datetimepicker');
        wp_enqueue_script('datetimepicker-settup');
		return parent::input( $meta, $field );

	}

	/**
	 * Calculates the timestamp from the datetime string and returns it
	 * if $field['timestamp'] is set or the datetime string if not
	 *
	 * @param mixed $new
	 * @param mixed $old
	 * @param int   $post_id
	 * @param array $field
	 *
	 * @return string|int
	 */
	public static function value( $new, $old, $post_id, $field ) {
	    if($new){
            $new = str_replace('/', '-', $new);
        }

		$time =  $new ? strtotime($new) : '';

        return $time;
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
		$meta = parent::post_meta( $post_id, $saved, $field );
        $meta = self::prepare_meta( $meta, $field );

		return $meta;
	}

	/**
	 * Format meta value if set 'timestamp'
	 *
	 * @param array|string $meta  The meta value
	 * @param array        $field Field parameter
	 * @return array
	 */
	protected static function prepare_meta( $meta, $field ) {
		if ( is_array( $meta ) ) {
			return array_map( __METHOD__, $meta );
		}

		return $meta ? date( $field['format'], intval( $meta ) ) : '';
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	public static function normalize( $field ) {
		$field = wp_parse_args( $field, array(
			'format'  => 'Y/m/d H:i:s',
			'js_options' => array(),
		) );

                $field['js_options'] = apply_filters('iwj_datetime_options', $field['js_options']);
		// Deprecate 'format', but keep it for backward compatible
		// Use 'js_options' instead
		$field['js_options'] = wp_parse_args( $field['js_options'], array(
			'format'      => empty( $field['format'] ) ? 'Y/m/d H:i:s' : $field['format'],
			'inline'      => false,
		) );

		$field = parent::normalize( $field );

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
			'data-options' => wp_json_encode( $field['js_options'] ),
		) );
		$attributes['type'] = 'text';

		return $attributes;
	}
}

IWJMB_Datetime_Field::init();