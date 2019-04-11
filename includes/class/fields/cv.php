<?php

/**
 * File field class which uses HTML <input type="file"> to upload file.
 */
class IWJMB_CV_Field extends IWJMB_Field {

    static function init(){
        add_action( 'wp_ajax_iwj_upload_cv', array( __CLASS__, 'upload_cv' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

    /**
     * Enqueue scripts and styles
     */
    public static function enqueue_scripts() {
        wp_register_style( 'iwjmb-cv', IWJ_FIELD_ASSETS_URL . 'css/cv.css' );
        wp_register_script( 'iwjmb-cv', IWJ_FIELD_ASSETS_URL . 'js/cv.js', array( ), '', true );
        $files = new stdClass();
        $files->title = __('Allowed Files', 'iwjob');
        $files->extensions = self::get_extensions();
	    $max_upload_cv = iwj_option( 'maximum_file_size_cv' ) ? intval( iwj_option( 'maximum_file_size_cv' ) ) * 1024 * 1024 : wp_max_upload_size();
        self::localize_script( 'iwjmb-cv', 'iwjmbCV', array(
            'max_file_size' => $max_upload_cv.'b',
            'url' => admin_url('admin-ajax.php'),
            'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),
            'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
            'loading_image' => IWJ_PLUGIN_URL . '/assets/img/ring.gif',
            'security' => wp_create_nonce( "iwj-security" ),
            'filter' => $files
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
        wp_enqueue_style('iwjmb-cv');
        wp_enqueue_script('iwjmb-cv');

		// Show form upload
        ob_start();
		if ( isset( $field['is_profile'] ) && $field['is_profile'] ) {
			?>
			<div id="<?php echo $field['id']; ?>" class="iwj-select-cv-container">
				<div class="iwj-select-cv-wrap">
					<?php if ( $meta ) {
						echo '<a href="' . esc_url( wp_get_attachment_url( $meta ) ) . '" target="_blank" title="' . $field['download_cv_text'] . '">';
					}
					?>
					<input type="text" readonly value="<?php echo $meta ? basename( get_attached_file( $meta ) ) : ''; ?>">
					<?php if ( $meta ) {
						echo '</a>';
					}
					?>
					<input type="hidden" name="<?php echo $field['field_name']; ?>" value="<?php echo $meta; ?>" />
					<a href="#" class="iwj-select-cv" id="<?php echo $field['id']; ?>-button"><?php echo $meta ? $field['change_cv_text'] : $field['select_cv_text']; ?></a>
					<?php if ( $meta ) { ?>
						<a href="#" class="iwj-remove-cv"><?php echo $field['remove_cv_text']; ?></a>
					<?php } ?>
					<div class="upload-error alert alert-danger" style="display: none;"></div>
				</div>
			</div>
			<?php
		} else {
			$user                 = IWJ_User::get_user();
			$maximum_file_size_cv = iwj_option( 'maximum_file_size_cv', '' ) ? iwj_option( 'maximum_file_size_cv' ) . 'M' : ini_get( "upload_max_filesize" );

			if ( $user ) {
				$cv = $user->get_cv();
			} else {
				$cv = false;
			}
			?>
			<div class="iwj-cv-area">
				<?php if ( $cv ) { ?>
					<div class="current-cv">
						<label>
							<input type="radio" name="<?php echo $field['id']; ?>" value="current_cv" checked>
							<span class="custom-radio"></span>
							<span class="cv_named"><?php echo $cv['name']; ?></span>
						</label>
						<p class="cv_uploaded"><?php echo sprintf( __( 'Uploaded %s' ), get_the_date( 'm/d/Y H:i:s', $cv['ID'] ) ); ?></p>
					</div>
				<?php } ?>
				<div class="add-new-cv">
					<label>
						<input type="radio" name="<?php echo $field['id']; ?>" value="add_new_cv" <?php echo ! $cv['ID'] ? 'checked' : ''; ?>>
						<span class="custom-radio"></span>
						<input type="file" name="<?php echo $field['id']; ?>_new_cv" accept="<?php echo $field['accept']; ?>" />
                                                <span class="select_cv"><?php echo sprintf(__( 'Upload new %s', 'iwjob' ), $field['name']); ?></span>
						<span class="select_cv_named"></span>
					</label>
					<p><?php echo sprintf( __( 'Allowed file: %s, maximum upload file size: %s', 'iwjob' ), $field['pre_value'], apply_filters( 'iwj_cv_max_file_size', $maximum_file_size_cv ) ); ?></p>
				</div>
			</div>
			<?php
		}
        $html = ob_get_contents();
        ob_end_clean();

		return $html;
	}

	static function get_mimes(){
        $mimes = array();
        $fields = IWJ_Apply_Form::get_form_fields();
        foreach ($fields as $field){
            if($field['id'] == IWJ_PREFIX.'cv'){
                $mimes = $field['mimes'];
                break;
            }
        }
        if(!$mimes){
            $mimes = array('application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip');
        }

        return $mimes;
    }

	static function get_extensions(){
        $extensions = '';
        $fields = IWJ_Apply_Form::get_form_fields();
        foreach ($fields as $field){
            if($field['id'] == IWJ_PREFIX.'cv'){
                $extensions = $field['extensions'];
                break;
            }
        }
        if(!$extensions){
            $extensions = 'pdf,doc,docs,zip';
        }

        return $extensions;
    }

    static function upload_cv(){
        check_ajax_referer('iwj-security');
        // handle file upload
        $file = $_FILES['async-upload'];
        $mimes = self::get_mimes();
        $overides = array( 'test_form' => false, 'mimes'=>$mimes);

        $status = wp_handle_upload($file, $overides);

        if($status){
            $user = IWJ_User::get_user();
            $post_id = $user ? (int)$user->get_candidate_id() : 0;

            $attachment = array(
                'guid'           => $status['url'],
                'post_mime_type' => $status['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file['name'] ) ),
                'post_content'   => '',
                'post_parent'    => $post_id,
                'post_status'    => 'inherit',
            );

            // Adds file as attachment to WordPress
            $id = wp_insert_attachment( $attachment, $status['file'], $post_id );

            if ( ! is_wp_error( $id ) )
            {
                wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $status['file'] ) );
            }

            $status['ID'] = $id;
            //$status['file_url'] = wp_get_attachment_url($id);
            $status['file_name'] = basename( get_attached_file( $id ) );

            $remove_file_id  = (int)$_POST['remove_file_id'];
            if($remove_file_id && current_user_can( 'delete_attachments', $remove_file_id)){
                wp_delete_attachment($remove_file_id, true);
            }
        }

        // send the uploaded file url in response
        echo json_encode($status);
        exit;
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
			'select_cv_text'     => __('Select File', 'iwjob'),
			'change_cv_text'     => __('Change File', 'iwjob'),
			'download_cv_text'        => __('Download File', 'iwjob'),
			'remove_cv_text'        => __('Remove File', 'iwjob'),
		) );

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
			return self::call( $field, 'file_info', $value, $args );
		} else {
            return self::call( $field, 'file_info', $value, $args );
		}
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
     * Save post meta value
     *
     * @param $new
     * @param $old
     * @param $post_id
     * @param $field
     */
    public static function save_post( $new, $old, $post_id, $field ) {

        if(isset($field['is_profile']) && $field['is_profile']) {
            if ($old && $old != $new && current_user_can('delete_attachments', $old)) {
                wp_delete_attachment($old, true);
            }
        }

        parent::save_post($new, $old, $post_id, $field);
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

        if(isset($field['is_profile']) && $field['is_profile']){
            return parent::value( $new, $old, $post_id, $field );
        }else{
            if($new == 'current_cv'){
                $new = self::get_attached_cv($post_id);
            }else{
                if ( empty( $_FILES[ $field['id'].'_new_cv' ] ) ) {
                    return '';
                }

                $new = self::upload($_FILES[ $field['id'].'_new_cv' ], $post_id, $field);
            }

            return $new;
        }
    }

    static function get_attached_cv($post_id){
        $user = IWJ_User::get_user();
        if($user){
            $cv = $user->get_cv();
            if($cv){
                $file = get_attached_file( $cv['ID'] );
                if($file){
                    $filename = basename($file);
                    $upload_file = wp_upload_bits($filename, null, file_get_contents($file));
                    if (!$upload_file['error']) {
                        $wp_filetype = wp_check_filetype($filename, null );
                        $attachment = array(
                            'post_mime_type' => $wp_filetype['type'],
                            'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
                            'post_parent'    => $post_id,
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );
                        $attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $post_id );
                        if (!is_wp_error($attachment_id)) {
                            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                            $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
                            wp_update_attachment_metadata( $attachment_id,  $attachment_data );
                        }

                        return $attachment_id;
                    }
                }
            }
        }

        return null;
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
}

IWJMB_CV_Field::init();