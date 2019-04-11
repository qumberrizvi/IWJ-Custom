<?php
/**
 * File advanced field class which users WordPress media popup to upload and select files.
 */
class IWJMB_File_Upload_Field extends IWJMB_Media_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'print_media_templates', array( __CLASS__, 'print_templates' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

	/**
	 * Enqueue scripts and styles
	 */
	public static function enqueue_scripts() {
		wp_register_style( 'iwjmb-upload', IWJ_FIELD_ASSETS_URL . 'css/upload.css', array( 'iwjmb-media' ) );
        wp_register_script('iwjmb-file-upload', IWJ_FIELD_ASSETS_URL . 'js/file-upload.js', array( 'iwjmb-media' ), '', true );
	}

	/**
	 * Template for media item
	 */
	public static function print_templates() {
		parent::print_templates();
		require_once IWJ_FIELD_DIR . 'templates/upload.php';
	}

    public static function input( $meta, $field ) {
        wp_enqueue_style( 'iwjmb-upload');
        wp_enqueue_script( 'iwjmb-file-upload');
        return parent::input( $meta, $field );
    }
}

IWJMB_File_Upload_Field::init();