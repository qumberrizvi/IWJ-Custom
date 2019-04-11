<?php

/**
 * File field class which uses HTML <input type="file"> to upload file.
 */
class IWJMB_File_Field extends IWJMB_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'post_edit_form_tag', array( __CLASS__, 'post_edit_form_tag' ) );
        add_action( 'wp_ajax_iwjmb_delete_file', array( __CLASS__, 'wp_ajax_delete_file' ) );
        add_action( 'wp_ajax_iwjmb_reorder_files', array( __CLASS__, 'wp_ajax_reorder_files' ) );
    }

    /**
	 * Enqueue scripts and styles
	 */
	public static function enqueue_scripts() {
		wp_register_style( 'iwjmb-file', IWJ_FIELD_ASSETS_URL . 'css/file.css', array() );
		wp_register_script( 'iwjmb-file', IWJ_FIELD_ASSETS_URL . 'js/file.js', array( 'jquery' ), '', true );

		self::localize_script( 'iwjmb-file', 'iwjmbFile', array(
			'maxFileUploadsSingle' => __( 'You may only upload maximum %d file', 'iwjob' ),
			'maxFileUploadsPlural' => __( 'You may only upload maximum %d files', 'iwjob' ),
		) );
	}

	/**
	 * Add data encoding type for file uploading
	 */
	public static function post_edit_form_tag() {
		echo ' enctype="multipart/form-data"';
	}

	/**
	 * Ajax callback for reordering images
	 */
	public static function wp_ajax_reorder_files() {
		$post_id  = (int) filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		$field_id = (string) filter_input( INPUT_POST, 'field_id' );
		$order    = (string) filter_input( INPUT_POST, 'order' );

		check_ajax_referer( "iwjmb-reorder-files_{$field_id}" );
		parse_str( $order, $items );
		delete_post_meta( $post_id, $field_id );
		foreach ( $items['item'] as $item ) {
			add_post_meta( $post_id, $field_id, $item, false );
		}
		wp_send_json_success();
	}

	/**
	 * Ajax callback for deleting files.
	 * Modified from a function used by "Verve Meta Boxes" plugin
	 *
	 * @link http://goo.gl/LzYSq
	 */
	public static function wp_ajax_delete_file() {
		$post_id       = (int) filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		$field_id      = (string) filter_input( INPUT_POST, 'field_id' );
		$attachment_id = (int) filter_input( INPUT_POST, 'attachment_id', FILTER_SANITIZE_NUMBER_INT );
		$force_delete  = (int) filter_input( INPUT_POST, 'force_delete', FILTER_SANITIZE_NUMBER_INT );

		check_ajax_referer( "iwjmb-delete-file_{$field_id}" );
		delete_post_meta( $post_id, $field_id, $attachment_id );
		$success = $force_delete ? wp_delete_attachment( $attachment_id ) : true;

		if ( $success ) {
			wp_send_json_success();
		}
		wp_send_json_error( __( 'Error: Cannot delete file', 'iwjob' ) );
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
        wp_enqueue_style('iwjmb-file');
        wp_enqueue_script('iwjmb-file');
		$i18n_title = apply_filters( 'iwjmb_file_upload_string', _x( 'Upload Files', 'file upload', 'iwjob' ), $field );
		$i18n_more  = apply_filters( 'iwjmb_file_add_string', _x( '+ Add a new file', 'file upload', 'iwjob' ), $field );

		// Uploaded files
		$html    = self::get_uploaded_files( $meta, $field );
		$classes = 'new-files';
		if ( ! empty( $field['max_file_uploads'] ) && count( $meta ) >= (int) $field['max_file_uploads'] ) {
			$classes .= ' hidden';
		}

		// Show form upload
        $html .= sprintf(
            '<div class="%s">
            <div class="add-file">
                <div class="file-input">
                <input type="file" name="%s[]" %s/>
                </div>
                <a class="iwjmb-add-file" href="#"><strong>%s</strong></a>
            </div>
        </div>',
            $classes,
            $field['id'],
            (isset($field['accept']) ? 'accept="'.$field['accept'].'"' : ''),
            $i18n_more
        );

		return $html;
	}

	/**
	 * Get HTML for uploaded files.
	 *
	 * @param array $files List of uploaded files
	 * @param array $field Field parameters
	 * @return string
	 */
	protected static function get_uploaded_files( $files, $field ) {
		$reorder_nonce = wp_create_nonce( "iwjmb-reorder-files_{$field['id']}" );
		$delete_nonce  = wp_create_nonce( "iwjmb-delete-file_{$field['id']}" );

		$classes = 'iwjmb-uploaded';
		if ( count( $files ) <= 0 ) {
			$classes .= ' hidden';
		}

		foreach ( (array) $files as $k => $file ) {
			$files[ $k ] = self::call( $field, 'file_html', $file );
		}
		return sprintf(
			'<ul class="%s" data-field_id="%s" data-delete_nonce="%s" data-reorder_nonce="%s" data-force_delete="%s" data-max_file_uploads="%s" data-mime_type="%s">%s</ul>',
			$classes,
			$field['id'],
			$delete_nonce,
			$reorder_nonce,
			$field['force_delete'] ? 1 : 0,
			$field['max_file_uploads'],
			$field['mime_type'],
			implode( '', $files )
		);
	}

	/**
	 * Get HTML for uploaded file.
	 *
	 * @param int $file Attachment (file) ID
	 * @return string
	 */
	protected static function file_html( $file ) {
		$i18n_delete = apply_filters( 'iwjmb_file_delete_string', _x( 'Delete', 'file upload', 'iwjob' ) );
		$i18n_edit   = apply_filters( 'iwjmb_file_edit_string', _x( 'Edit', 'file upload', 'iwjob' ) );
		$mime_type   = get_post_mime_type( $file );

		return sprintf(
			'<li id="item_%s">
				<div class="iwjmb-icon">%s</div>
				<div class="iwjmb-info">
					<a href="%s" target="_blank">%s</a>
					<p>%s</p>
					<a href="%s" target="_blank">%s</a> |
					<a class="iwjmb-delete-file" href="#" data-attachment_id="%s">%s</a>
				</div>
			</li>',
			$file,
			wp_get_attachment_image( $file, array( 60, 60 ), true ),
			wp_get_attachment_url( $file ),
			get_the_title( $file ),
			$mime_type,
			get_edit_post_link( $file ),
			$i18n_edit,
			$file,
			$i18n_delete
		);
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
		if ( empty( $_FILES[ $field['id'] ] ) ) {
			return $new;
		}
		$new   = array();
		$files = self::transform( $_FILES[ $field['id'] ] );

        foreach ( $files as $file ) {
			$new[] = self::upload( $file, $post_id, $field );
		}

		return array_filter( array_unique( array_merge( (array) $old, $new ) ) );
	}

	/**
	 * Handle upload file.
	 *
	 * @param array $file
	 * @param int   $post Post parent ID
	 * @return int Attachment ID on success, false on failure.
	 */
	protected static function upload( $file, $post, $field ) {
	    $overides = array( 'test_form' => false);
	    if($field['mimes']){
            $overides['mimes'] = $field['mimes'];
        }
		$file = wp_handle_upload( $file, $overides );

        if ( ! isset( $file['file'] ) ) {
			return false;
		}

		$attachment = wp_insert_attachment( array(
			'post_mime_type' => $file['type'],
			'guid'           => $file['url'],
			'post_parent'    => $post,
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file['file'] ) ),
			'post_content'   => '',
		), $file['file'], $post );
		if ( is_wp_error( $attachment ) || ! $attachment ) {

            return false;
		}
		wp_update_attachment_metadata( $attachment, wp_generate_attachment_metadata( $attachment, $file['file'] ) );
		return $attachment;
	}

	/**
	 * Transform $_FILES from $_FILES['field']['key']['index'] to $_FILES['field']['index']['key']
	 *
	 * @param array $files
	 * @return array
	 */
	public static function transform( $files ) {
		$output = array();
		foreach ( $files as $key => $list ) {
			foreach ( $list as $index => $value ) {
				$output[ $index ][ $key ] = $value;
			}
		}

		return $output;
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	public static function normalize( $field ) {
		$field             = parent::normalize( $field );
		$field             = wp_parse_args( $field, array(
			'std'              => array(),
			'force_delete'     => false,
			'max_file_uploads' => 0,
			'mime_type'        => '',
			'mimes'        => array(),
		) );
		$field['multiple'] = true;

		return $field;
	}

	/**
	 * Get the field value. Return meaningful info of the files.
	 *
	 * @param  array    $field   Field parameters
	 * @param  array    $args    Not used for this field
	 * @param  int|null $post_id Post ID. null for current post. Optional.
	 *
	 * @return mixed Full info of uploaded files
	 */
	public static function get_value( $field, $args = array(), $post_id = null ) {
		$value = parent::get_value( $field, $args, $post_id );
		if ( ! $field['clone'] ) {
			$value = self::call( 'files_info', $field, $value, $args );
		} else {
			$return = array();
			foreach ( $value as $subvalue ) {
				$return[] = self::call( 'files_info', $field, $subvalue, $args );
			}
			$value = $return;
		}
		if ( isset( $args['limit'] ) ) {
			$value = array_slice( $value, 0, intval( $args['limit'] ) );
		}
		return $value;
	}

	/**
	 * Get uploaded files information
	 *
	 * @param array $field Field parameter
	 * @param array $files Files IDs
	 * @param array $args  Additional arguments (for image size)
	 * @return array
	 */
	public static function files_info( $field, $files, $args ) {
		$return = array();
		foreach ( (array) $files as $file ) {
			if ( $info = self::call( $field, 'file_info', $file, $args ) ) {
				$return[ $file ] = $info;
			}
		}
		return $return;
	}

	/**
	 * Get uploaded file information
	 *
	 * @param int   $file Attachment file ID (post ID). Required.
	 * @param array $args Array of arguments (for size).
	 *
	 * @return array|bool False if file not found. Array of (id, name, path, url) on success
	 */
	public static function file_info( $file, $args = array() ) {
		if ( ! $path = get_attached_file( $file ) ) {
			return false;
		}

		return wp_parse_args( array(
			'ID'    => $file,
			'name'  => basename( $path ),
			'path'  => $path,
			'url'   => wp_get_attachment_url( $file ),
			'title' => get_the_title( $file ),
		), wp_get_attachment_metadata( $file ) );
	}

	/**
	 * Format value for the helper functions.
	 *
	 * @param array        $field Field parameter
	 * @param string|array $value The field meta value
	 * @return string
	 */
	public static function format_value( $field, $value ) {
		if ( ! $field['clone'] ) {
			return self::call( 'format_single_value', $field, $value );
		}
		$output = '<ul>';
		foreach ( $value as $subvalue ) {
			$output .= '<li>' . self::call( 'format_single_value', $field, $subvalue ) . '</li>';
		}
		$output .= '</ul>';
		return $output;
	}

	/**
	 * Format a single value for the helper functions.
	 *
	 * @param array $field Field parameter
	 * @param array $value The value
	 * @return string
	 */
	public static function format_single_value( $field, $value ) {
		$output = '<ul>';
		foreach ( $value as $file ) {
			$output .= sprintf( '<li><a href="%s" target="_blank">%s</a></li>', $file['url'], $file['title'] );
		}
		$output .= '</ul>';
		return $output;
	}
}

IWJMB_File_Field::init();