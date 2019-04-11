<?php
/**
 * Image upload field which uses thickbox library to upload.
 *
 * @deprecated Use image_advanced instead
 */
class IWJMB_Thickbox_Image_Field extends IWJMB_Image_Field {

	/**
	 * Add custom actions for the field.
	 */
	public static function add_actions() {
		parent::add_actions();
		add_filter( 'get_media_item_args', array( __CLASS__, 'allow_img_insertion' ) );
	}

	/**
	 * Always enable insert to post button in the popup
	 *
	 * @link https://github.com/rilwis/meta-box/issues/809
	 * @link http://wordpress.stackexchange.com/q/22175/2051
	 * @param array $vars
	 * @return array
	 */
	public static function allow_img_insertion( $vars ) {
		$vars['send'] = true; // 'send' as in "Send to Editor"
		return $vars;
	}

	/**
	 * Enqueue scripts and styles
	 */
	public static function enqueue_scripts() {
		parent::admin_enqueue_scripts();

		add_thickbox();
		wp_enqueue_script( 'media-upload' );

		wp_enqueue_script( 'iwjmb-thickbox-image', IWJMB_JS_URL . 'thickbox-image.js', array( 'jquery' ), IWJMB_VER, true );
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
		$i18n_title = apply_filters( 'iwjmb_thickbox_image_upload_string', _x( 'Upload Images', 'image upload', 'iwjob' ), $field );

		// Uploaded images
		$html = parent::get_uploaded_files( $meta, $field );

		// Show form upload
		$html .= "<a href='#' class='button iwjmb-thickbox-upload' data-field_id='{$field['id']}'>{$i18n_title}</a>";

		return $html;
	}

	/**
	 * Get field value
	 * It's the combination of new (uploaded) images and saved images
	 *
	 * @param array $new
	 * @param array $old
	 * @param int   $post_id
	 * @param array $field
	 *
	 * @return array
	 */
	public static function value( $new, $old, $post_id, $field ) {
		return array_filter( array_unique( array_merge( (array) $old, (array) $new ) ) );
	}
}
