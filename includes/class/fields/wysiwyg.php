<?php

/**
 * WYSIWYG (editor) field class.
 */
class IWJMB_Wysiwyg_Field extends IWJMB_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

	/**
	 * Array of cloneable editors.
	 *
	 * @var array
	 */
	static $cloneable_editors = array();

	/**
	 * Enqueue scripts and styles.
	 */
	static function enqueue_scripts() {
		wp_register_style( 'iwjmb-wysiwyg', IWJ_FIELD_ASSETS_URL . 'css/wysiwyg.css', array() );
		wp_register_script( 'iwjmb-wysiwyg', IWJ_FIELD_ASSETS_URL . 'js/wysiwyg.js', array( 'jquery' ), '', true );
	}

	/**
	 * Change field value on save
	 *
	 * @param mixed $new
	 * @param mixed $old
	 * @param int   $post_id
	 * @param array $field
	 * @return string
	 */
	static function value( $new, $old, $post_id, $field ) {
		return  $field['raw'] ? $new : wpautop( $new );
	}

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @return string
	 */
	static function input( $meta, $field ) {
		// Using output buffering because wp_editor() echos directly
        wp_enqueue_style('iwjmb-wysiwyg');
        wp_enqueue_script('iwjmb-wysiwyg');
		ob_start();

		$field['options']['textarea_name'] = $field['field_name'];
		$attributes = self::get_attributes( $field );

		// Use new wp_editor() since WP 3.3
		wp_editor( $meta, $attributes['id'], $field['options'] );

		return ob_get_clean();
	}

	/**
	 * Escape meta for field output
	 *
	 * @param mixed $meta
	 * @return mixed
	 */
	static function esc_meta( $meta ) {
		return $meta;
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	static function normalize( $field ) {
		$field = parent::normalize( $field );
		$field = wp_parse_args( $field, array(
			'raw'     => false,
			'options' => array(),
		) );

		$field['options'] = wp_parse_args( $field['options'], array(
			'editor_class' => 'iwjmb-wysiwyg',
			'dfw'          => true, // Use default WordPress full screen UI
            'media_buttons' => is_blog_admin() ? true : false
		) );

		// Keep the filter to be compatible with previous versions
		$field['options'] = apply_filters( 'iwjmb_wysiwyg_settings', $field['options'] );

		return $field;
	}
}

IWJMB_Wysiwyg_Field::init();