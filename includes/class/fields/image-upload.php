<?php
/**
 * File advanced field class which users WordPress media popup to upload and select files.
 */
class IWJMB_Image_Upload_Field extends IWJMB_Image_Advanced_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'print_media_templates', array( __CLASS__, 'print_templates' ) );
    }

	/**
	 * Enqueue scripts and styles
	 */
	public static function enqueue_scripts() {
		wp_register_script( 'iwjmb-image-upload', IWJ_FIELD_ASSETS_URL . 'js/image-upload.js', array( 'iwjmb-file-upload', 'iwjmb-image-advanced' ), '', true );
	}

	/**
	 * Template for media item
	 */
	public static function print_templates() {
		IWJMB_File_Upload_Field::print_templates();
	}

    public static function input( $meta, $field ) {
        wp_enqueue_style( 'iwjmb-upload');
        wp_enqueue_script('iwjmb-file-upload');
        wp_enqueue_style( 'iwjmb-image-advanced');
        wp_enqueue_script( 'iwjmb-image-advanced');
        wp_enqueue_script( 'iwjmb-image-upload');
        return parent::input( $meta, $field );
    }

    /**
     * Get meta values to save
     *
     * @param mixed $new
     * @param mixed $old
     * @param int   $post_id
     * @param array $field
     *
     * @return array|mixed
     */
    public static function value( $new, $old, $post_id, $field ) {
        array_walk( $new, 'absint' );
        if($old){
            $remove = array_diff($old, $new);
            if($remove){
                foreach ($remove as $attachment){
                    if( current_user_can('delete_attachments', $attachment)){
                        wp_delete_attachment($attachment, true);
                    }
                }
            }
        }

        return array_filter( array_unique( $new ) );
    }
}

IWJMB_Image_Upload_Field::init();