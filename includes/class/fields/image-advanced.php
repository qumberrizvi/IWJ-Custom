<?php
/**
 * Image advanced field class which users WordPress media popup to upload and select images.
 */
class IWJMB_Image_Advanced_Field extends IWJMB_Media_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'print_media_templates', array( __CLASS__, 'print_templates' ) );
    }

    /**
	 * Enqueue scripts and styles
	 */
	static function enqueue_scripts() {
		wp_register_style( 'iwjmb-image-advanced', IWJ_FIELD_ASSETS_URL . 'css/image-advanced.css', array( 'iwjmb-media' ) );
		wp_register_script( 'iwjmb-image-advanced', IWJ_FIELD_ASSETS_URL . 'js/image-advanced.js', array( 'iwjmb-media' ), '', true );
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	static function normalize( $field ) {
		$field              = parent::normalize( $field );
		$field['mime_type'] = 'image';
		return $field;
	}

	/**
	 * Get the field value.
	 *
	 * @param array $field
	 * @param array $args
	 * @param null  $post_id
	 * @return mixed
	 */
	static function get_value( $field, $args = array(), $post_id = null ) {
		return IWJMB_Image_Field::get_value( $field, $args, $post_id );
	}

	/**
	 * Get uploaded file information.
	 *
	 * @param int   $file Attachment image ID (post ID). Required.
	 * @param array $args Array of arguments (for size).
	 * @return array|bool False if file not found. Array of image info on success
	 */
	static function file_info( $file, $args = array() ) {
		return IWJMB_Image_Field::file_info( $file, $args );
	}

	/**
	 * Format value for the helper functions.
	 *
	 * @param array        $field Field parameter
	 * @param string|array $value The field meta value
	 * @return string
	 */
	public static function format_value( $field, $value ) {
		return IWJMB_Image_Field::format_value( $field, $value );
	}

	/**
	 * Format a single value for the helper functions.
	 *
	 * @param array $field Field parameter
	 * @param array $value The value
	 * @return string
	 */
	public static function format_single_value( $field, $value ) {
		return IWJMB_Image_Field::format_single_value( $field, $value );
	}

	/**
	 * Template for media item
	 *
	 * @return void
	 */
	public static function print_templates() {
		parent::print_templates();
		require_once IWJ_FIELD_DIR . 'templates/image-advanced.php';
	}

    public static function input( $meta, $field ) {
        wp_enqueue_style( 'iwjmb-image-advanced');
        wp_enqueue_script( 'iwjmb-image-advanced');
        return parent::input( $meta, $field );
    }
}

IWJMB_Image_Advanced_Field::init();