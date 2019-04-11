<?php
/**
 * File advanced field class which users WordPress media popup to upload and select files.
 */
class IWJMB_Image_Single_Field extends IWJMB_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_ajax_iwj_upload_single_image', array( __CLASS__, 'upload_image' ) );
    }

	/**
	 * Enqueue scripts and styles
	 */
	public static function enqueue_scripts() {
		wp_register_script( 'iwjmb-image-single', IWJ_FIELD_ASSETS_URL . 'js/image-single.js', array( ), '', true );
		$image_type = new stdClass();
        $image_type->title = __('Allowed Images', 'iwjob');
        $image_type->extensions = 'jpg,jpeg,png,gif,bmp';
        self::localize_script( 'iwjmb-image-single', 'iwjmbSingleImage', array(
            'max_file_size' => wp_max_upload_size() . 'b',
            'url' => admin_url('admin-ajax.php'),
            'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),
            'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
            'loading_image' => IWJ_PLUGIN_URL . '/assets/img/ring.gif',
            'security' => wp_create_nonce( "iwj-security" ),
            'filter' => $image_type
        ) );
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
            'default_image_url'    => IWJ_FIELD_ASSETS_URL .'img/placeholder-image.png',
            'button_text'     =>  '<i class="fa fa-edit"></i>'.__(' Change Image', 'iwjob'),
        ) );

        return $field;
    }

    public static function input( $meta, $field ) {
        wp_enqueue_media();
        wp_enqueue_script( 'iwjmb-image-single');
        ob_start();
        $image_url = '';
        if($meta){
            $image = wp_get_attachment_image_src($meta);
            $image_url = $image[0];
        }
        if(!$image_url){
            $image_url = $field['default_image_url'];
        }
        ?>
        <div id="<?php echo $field['id']; ?>" class="iwj-select-image-container">
            <div class="iwj-select-image-wrap">
                <img src="<?php echo $image_url; ?>" class="iwj-select-image-img">
                <input type="hidden" name="<?php echo $field['field_name']; ?>" value="<?php echo $meta; ?>">
            </div>

            <div class="iwj-select-image-button">
                <?php if(isset($field['button_desc'])){ echo '<p>'.$field['button_desc'].'</p>'; } ?>
                <a href="#" class="iwj-select-image" id="<?php echo $field['id']; ?>-button"><?php echo $field['button_text']; ?></a>
            </div>

        </div>
        <?php
        $html = ob_get_clean();
        return $html;
    }

    static function upload_image(){
        check_ajax_referer('iwj-security');
        // handle file upload
        $file = $_FILES['async-upload'];
        $mimes = array(
            // Image formats.
            'jpg|jpeg|jpe' => 'image/jpeg',
            'gif' => 'image/gif',
            'png' => 'image/png',
            'bmp' => 'image/bmp',
        );
        $overides = array( 'test_form' => false, 'mimes'=>$mimes);

        $status = wp_handle_upload($file, $overides);
        if($status){
            $user = IWJ_User::get_user();
            $post_id = 0;
            if($user){
                if($user->is_employer()){
                    $post_id = (int)$user->get_employer_id();
                }else{
                    $post_id = (int)$user->get_candidate_id();
                }
            }

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
            $status['thumbnail_url'] = wp_get_attachment_thumb_url($id);
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
     * Save post meta value
     *
     * @param $new
     * @param $old
     * @param $post_id
     * @param $field
     */
    public static function save_post( $new, $old, $post_id, $field ) {

        if($old && $old != $new && current_user_can( 'delete_attachments', $old)){
            wp_delete_attachment($old, true);
        }

        parent::save_post($new, $old, $post_id, $field);
    }
}

IWJMB_Image_Single_Field::init();