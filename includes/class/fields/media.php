<?php

/**
 * Media field class which users WordPress media popup to upload and select files.
 */
class IWJMB_Media_Field extends IWJMB_File_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'print_media_templates', array( __CLASS__, 'print_templates' ) );
    }

	/**
	 * Enqueue scripts and styles
	 */
	public static function enqueue_scripts() {
		//wp_enqueue_media();
        //has custom
        if(!is_blog_admin()){
            wp_register_script('media-grid', includes_url()."/js/media-grid.js", array( 'media-editor' ), '', true);
        }
		wp_register_style( 'iwjmb-media', IWJ_FIELD_ASSETS_URL . 'css/media.css', array() );
		wp_register_script( 'iwjmb-media', IWJ_FIELD_ASSETS_URL . 'js/media.js', array( 'jquery-ui-sortable', 'underscore', 'backbone', 'media-grid' ), '', true );

		self::localize_script( 'iwjmb-media', 'i18niwjmbMedia', array(
			'add'                => _x( '+ Add Media', 'media', 'iwjob' ) ,
			'single'             => _x( ' file', 'media', 'iwjob' ) ,
			'multiple'           => _x( ' files', 'media', 'iwjob' ) ,
			'remove'             => _x( 'Remove', 'media', 'iwjob' ),
			'edit'               => _x( 'Edit', 'media', 'iwjob' ),
			'view'               => _x( 'View', 'media', 'iwjob' ),
			'noTitle'            => _x( 'No Title', 'media', 'iwjob' ),
			'loadingUrl'         => IWJ_FIELD_ASSETS_URL . 'img/loader.gif',
			'extensions'         => self::get_mime_extensions(),
			'select'             => apply_filters( 'iwjmb_media_select_string', _x( 'Select Files', 'media', 'iwjob' ) ),
			'or'                 => apply_filters( 'iwjmb_media_or_string', _x( 'or', 'media', 'iwjob' ) ),
			'uploadInstructions' => apply_filters( 'iwjmb_media_upload_instructions_string', _x( 'Drop files here to upload', 'media', 'iwjob' ) ),
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
        wp_enqueue_media();
        wp_enqueue_style( 'iwjmb-media');
        wp_enqueue_script( 'iwjmb-media');

		$meta       = (array) $meta;
		$meta       = implode( ',', $meta );
		$attributes = $load_test_attr = self::get_attributes( $field, $meta );

		$html = sprintf(
			'<input %s>
			<div class="iwjmb-media-view" data-mime-type="%s" data-max-files="%s" data-force-delete="%s" data-show-status="%s"></div>',
			self::render_attributes( $attributes ),
			$field['mime_type'],
			$field['max_file_uploads'],
			$field['force_delete'] ? 'true' : 'false',
			$field['max_status']
		);

		return $html;
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	public static function normalize( $field ) {
		$field = parent::normalize( $field );
		$field = wp_parse_args( $field, array(
			'std'              => array(),
			'mime_type'        => '',
			'max_file_uploads' => 0,
			'force_delete'     => false,
			'max_status'       => true,
		) );

		$field['multiple'] = true;

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
		$attributes         = parent::get_attributes( $field, $value );
		$attributes['type'] = 'hidden';
		$attributes['name'] .= ! $field['clone'] && $field['multiple'] ? '[]' : '';
		$attributes['disabled'] = true;
		$attributes['id']       = false;
		$attributes['value']    = $value;

		return $attributes;
	}

	/**
	 * Get supported mime extensions.
	 *
	 * @return array
	 */
	protected static function get_mime_extensions() {
		$mime_types = wp_get_mime_types();
		$extensions = array();
		foreach ( $mime_types as $ext => $mime ) {
			$ext               = explode( '|', $ext );
			$extensions[ $mime ] = $ext;

			$mime_parts = explode( '/', $mime );
			if ( empty( $extensions[ $mime_parts[0] ] ) ) {
				$extensions[ $mime_parts[0] ] = array();
			}
			$extensions[ $mime_parts[0] ] = $extensions[ $mime_parts[0] . '/*' ] = array_merge( $extensions[ $mime_parts[0] ], $ext );

		}

		return $extensions;
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
		return array_filter( array_unique( $new ) );
	}

	/**
	 * Save meta value
	 *
	 * @param $new
	 * @param $old
	 * @param $post_id
	 * @param $field
	 */
	public static function save_post( $new, $old, $post_id, $field ) {
		delete_post_meta( $post_id, $field['id'] );
		parent::save_post( $new, array(), $post_id, $field );
	}

    public static function save_term( $new, $old, $term_id, $field ) {
        delete_term_meta( $term_id, $field['id'] );
        parent::save_term( $new, array(), $term_id, $field );
    }

	/**
	 * Template for media item
	 */
	public static function print_templates() {
		require_once IWJ_FIELD_DIR . 'templates/media.php';
	}
}

IWJMB_Media_Field::init();