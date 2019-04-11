<?php

/**
 * File field class which uses HTML <input type="file"> to upload file.
 */
class IWJMB_Avatar_Field extends IWJMB_Field {

    static function init(){
        add_action( 'wp_ajax_iwj_upload_avatar', array( __CLASS__, 'upload_avatar' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

    static function get_avatar_size(){
        $user = IWJ_User::get_user();
        if($user && $user->is_employer()){
            $image_width = iwj_option('employer_avatar_width');
            $image_height = iwj_option('employer_avatar_height');
        }else{
            $image_width = iwj_option('candidate_avatar_width');
            $image_height = iwj_option('candidate_avatar_height');
        }

        if(!$image_width){
            $image_width = 150;
        }
        if(!$image_height){
            $image_height = 150;
        }

        return array($image_width, $image_height);
    }
    /**
     * Enqueue scripts and styles
     */
    public static function enqueue_scripts() {
        wp_register_style( 'jquery-cropper', IWJ_FIELD_ASSETS_URL . 'css/cropper.css' );
        wp_register_script( 'jquery-cropper', IWJ_FIELD_ASSETS_URL . 'js/cropper.js', array(), false, true);
        wp_register_style( 'iwjmb-avatar', IWJ_FIELD_ASSETS_URL . 'css/avatar.css' );
        wp_register_script( 'iwjmb-avatar', IWJ_FIELD_ASSETS_URL . 'js/avatar.js', array( ), false, true );

        $avatar_size = self::get_avatar_size();
        self::localize_script( 'iwjmb-avatar', 'iwjmbAvatar', array(
            'max_upload_image_size' => wp_max_upload_size() . 'b',
            'image_width' => $avatar_size[0],
            'image_height' => $avatar_size[1],
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
        wp_enqueue_style('jquery-cropper');
        wp_enqueue_script('jquery-cropper');
        wp_enqueue_style('iwjmb-avatar');
        wp_enqueue_script('iwjmb-avatar');

        $user_id = get_current_user_id();
        $avatar_id = get_user_meta( $user_id, IWJ_PREFIX.'avatar', true );
        $avatar_src = $avatar_data = '';
        if($avatar_id){
            $avatar_src = get_post_meta( $avatar_id, IWJ_PREFIX.'avatar_src', true );
            if(strpos($avatar_src, 'http') !== 0){
                $upload = wp_upload_dir();
                $avatar_src = $upload['baseurl'].'/avatar_original/'.$avatar_src;
            }
            $avatar_data = get_post_meta( $avatar_id, IWJ_PREFIX.'avatar_data', true );
        }
        $avatar_url = get_avatar_url($user_id);
        if(!$avatar_src){
            $avatar_src = $avatar_url;
        }
		// Show form upload
        ob_start();
            $user = IWJ_User::get_user();
            ?>
            <div id="<?php echo $field['id']; ?>" class="iwj-avatar-container">
                <!-- Current avatar -->
                <div class="avatar-view">
                    <img src="<?php echo $avatar_url; ?>" alt="Avatar">
                </div>
                <div class="desc-change-image">
                    <p class="avatar-description"><?php echo __('Update your photo manually, If the photo is not set, the default avatar will be the same as your login email account', 'iwjob'); ?></p>
                    <div class="change-image-btn">
                        <button type="button"><?php echo __('Upload Photo', 'iwjob'); ?></button>
                    </div>
                </div>
                <!-- Cropping modal -->
                <div class="modal fade" id="avatar-modal" aria-hidden="true" aria-labelledby="avatar-modal-label" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="avatar-form">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title" id="avatar-modal-label"><?php echo __('Change Avatar', 'iwjob'); ?></h4>
                                </div>
                                <div class="modal-body">
                                    <div class="avatar-body">

                                        <!-- Upload image and data -->
                                        <!-- Crop and preview -->
                                        <div class="row">
                                            <div class="col-md-9">
                                                <div class="iwj-avatar-wrapper">
                                                    <div class="avatar-wrapper"></div>
                                                    <!-- Loading state -->
                                                    <div class="loading" aria-label="Loading" role="img" tabindex="-1"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="avatar-preview preview-lg"></div>
                                            </div>
                                        </div>

                                        <div class="row avatar-btns">
                                            <div class="col-md-9">
                                                <div class="avatar-upload">
                                                    <input type="hidden" class="avatar-src" name="avatar_src" value="<?php echo $avatar_src; ?>">
                                                    <input type="hidden" class="avatar-data" name="avatar_data" value=''>
                                                    <input type="hidden" class="avatar-canvas-data" name="avatar_canvas" value='<?php echo $avatar_data; ?>'>
                                                    <label class="avatar-input-label"><?php echo __('Upload Photo', 'iwjob'); ?></label>
                                                    <input type="file" class="avatar-input" name="avatar_file" accept="image/gif, image/jpeg, image/png" >
                                                </div>
                                                <button type="button" class="btn btn-primary btn-block avatar-save"><?php echo __('Save Avatar', 'iwjob'); ?></button>
                                            </div>
                                            <div class="col-md-3">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal -->

            </div>
            <?php
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

    static function upload_avatar(){
        check_ajax_referer('iwj-security');
        
        $crop = new IWJCropAvatar(
            isset($_POST['avatar_src']) ? $_POST['avatar_src'] : null,
            isset($_POST['avatar_data']) ? $_POST['avatar_data'] : null,
            isset($_FILES['avatar_file']) ? $_FILES['avatar_file'] : null
        );

        $file = $crop->getDst();
        $src = $crop->getSrc_url();
        $upload = wp_upload_dir();
        $src = str_replace($upload['baseurl'].'/avatar_original/', '', $src);
        $data = new stdClass();
        if(isset($_POST['avatar_canvas'])){
            $data = json_decode(stripslashes($_POST['avatar_canvas']));
        }
        $user_id = get_current_user_id();
        $avatar_id = get_user_meta( $user_id, IWJ_PREFIX.'avatar', true );

        $save_avatar_mode = apply_filters('iwj_save_avatar_mode', 1);

        if($avatar_id){
            if($save_avatar_mode == 2){
                wp_delete_attachment($avatar_id, true);
            }else{
                WP_Filesystem();
                global $wp_filesystem;
                $avatar_path = get_attached_file( $avatar_id );
                $wp_filesystem->delete($avatar_path);
                $wp_filesystem->move($file, $avatar_path);
                wp_update_attachment_metadata( $avatar_id, wp_generate_attachment_metadata( $avatar_id, $avatar_path ) );
            }
        }

        if($save_avatar_mode == 2 || ($save_avatar_mode == 1 && !$avatar_id)){
            $filename = basename($file);
            $user = IWJ_User::get_user();
            $post_id = 0;
            if($user->is_employer()){
                $post_id = $user->get_employer_id();
            }elseif($user->is_candidate()){
                $post_id = $user->get_candidate_id();
            }

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
                $avatar_id = wp_insert_attachment( $attachment, $upload_file['file'], $post_id );
                if (!is_wp_error($avatar_id)) {
                    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                    $attachment_data = wp_generate_attachment_metadata( $avatar_id, $upload_file['file'] );
                    wp_update_attachment_metadata( $avatar_id,  $attachment_data );
                    update_user_meta( $user_id, IWJ_PREFIX.'avatar', $avatar_id );
                }
            }
        }

        if($avatar_id){
            update_post_meta($avatar_id, IWJ_PREFIX.'avatar_src', $src );
            update_post_meta($avatar_id, IWJ_PREFIX.'avatar_data', json_encode($data) );
        }

        $response = array(
            'state'  => 200,
            'message' => $crop -> getMsg(),
            'avatar_url' => add_query_arg(array('time'=>current_time('timestamp')), get_avatar_url($user_id)),
            'avatar_src' => $crop->getSrc_url()
        );

        echo json_encode($response);exit;
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

        return false;
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

       return false;
    }

}

class IWJCropAvatar {
    private $src;
    private $data;
    private $dst;
    private $type;
    private $extension;
    private $msg;

    function __construct($src, $data, $file) {
        $this -> setSrc($src);
        $this -> setData($data);
        $this -> setFile($file);
        $this -> crop($this -> src, $this -> dst, $this -> data);
    }

    private function setSrc($src) {
        if (!empty($src)) {
            $type = exif_imagetype($src);
            if ($type) {
                $this -> src = $src;
                $this -> type = $type;
                $this -> extension = image_type_to_extension($type);

                $name = basename($src);

                $name = explode( '.', sanitize_file_name($name));
                $ext = array_pop( $name );
                $name = implode( '.', $name );
                $user_id = get_current_user_id();
                $name = str_replace('-'.$user_id.'.original', '', $name);
                $upload = wp_upload_dir();
                $this ->dst = $upload['basedir'].'/avatar_original/'.$name.'.png';
            }
        }
    }

    private function setData($data) {
        if (!empty($data)) {
            $this -> data = json_decode(stripslashes($data));
        }
    }

    private function setFile($file) {
        if($file && $file['tmp_name']){
            $errorCode = $file['error'];

            if ($errorCode === UPLOAD_ERR_OK) {

                $type = exif_imagetype($file['tmp_name']);
                $upload = wp_upload_dir();
                if ($type) {
                    $extension = image_type_to_extension($type);
                    WP_Filesystem();
                    global $wp_filesystem;
                    if ( ! $wp_filesystem->is_dir($upload['basedir'].'/avatar_original' ) ) {
                        if ( ! $wp_filesystem->mkdir($upload['basedir'].'/avatar_original' ) ) {
                            $this -> msg = 'Can not create folder '.$upload['basedir'].'/avatar_original';
                        }
                    }
                    $src = $src_url = '';

                    $file_name = explode( '.', sanitize_file_name($file['name']));
                    $ext = array_pop( $file_name );
                    $file_name = implode( '.', $file_name );

                    $src_name = $file_name.'-'.get_current_user_id(). '.original' . $extension;
                    $dst_name = $file_name.'-'.get_current_user_id(). '.png';
                    $src = $upload['basedir'].'/avatar_original/'. $src_name;
                    $src_url = $upload['baseurl'].'/avatar_original/'. $src_name;

                    if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_JPEG || $type == IMAGETYPE_PNG) {
                        if (file_exists($src)) {
                            unlink($src);
                        }
                        $result = move_uploaded_file($file['tmp_name'], $src);
                        if ($result) {
                            $user_id = get_current_user_id();
                            $avatar_id = get_user_meta( $user_id, IWJ_PREFIX.'avatar', true );
                            if($avatar_id){
                                $avatar_src = get_post_meta($avatar_id, IWJ_PREFIX.'avatar_src', true);
                                if($avatar_src && strpos($avatar_src, 'http') !== 0 ){
                                    $old_src = $upload['basedir'].'/avatar_original/'. basename($avatar_src);
                                    if (file_exists($old_src) && $old_src == $avatar_src) {
                                        unlink($old_src);
                                    }
                                }
                            }

                            $this -> src_url = $src_url;
                            $this -> src = $src;
                            $this -> dst = $upload['basedir'].'/avatar_original/'. $dst_name;
                            $this -> type = $type;
                            $this -> extension = $extension;
                        } else {
                            $this -> msg = 'Failed to save file';
                        }
                    } else {
                        $this -> msg = 'Please upload image with the following types: JPG, PNG, GIF';
                    }
                } else {
                    $this -> msg = 'Please upload image file';
                }
            } else {
                $this -> msg = $this -> codeToMessage($errorCode);
            }
        }
    }

    private function crop($src, $dst, $data) {
        if (!empty($src) && !empty($dst) && !empty($data)) {
            WP_Filesystem();
            global $wp_filesystem;
            $upload = wp_upload_dir();
            if ( ! $wp_filesystem->is_dir($upload['basedir'].'/avatar_original' ) ) {
                if ( ! $wp_filesystem->mkdir($upload['basedir'].'/avatar_original' ) ) {
                    $this -> msg = 'Can not create folder '.$upload['basedir'].'/avatar_original';
                }
            }

            $src_img = '';
            switch ($this -> type) {
                case IMAGETYPE_GIF:
                    $src_img = imagecreatefromgif($src);
                    break;

                case IMAGETYPE_JPEG:
                    $src_img = imagecreatefromjpeg($src);
                    break;

                case IMAGETYPE_PNG:
                    $src_img = imagecreatefrompng($src);
                    break;
            }

            if (!$src_img) {
                $this -> msg = "Failed to read the image file";
                return;
            }
            $size = getimagesize($src);
            $size_w = $size[0]; // natural width
            $size_h = $size[1]; // natural height

            $src_img_w = $size_w;
            $src_img_h = $size_h;

            $degrees = $data -> rotate;

            // Rotate the source image
            if (is_numeric($degrees) && $degrees != 0) {
                // PHP's degrees is opposite to CSS's degrees
                $new_img = imagerotate( $src_img, -$degrees, imagecolorallocatealpha($src_img, 0, 0, 0, 127) );

                imagedestroy($src_img);
                $src_img = $new_img;

                $deg = abs($degrees) % 180;
                $arc = ($deg > 90 ? (180 - $deg) : $deg) * M_PI / 180;

                $src_img_w = $size_w * cos($arc) + $size_h * sin($arc);
                $src_img_h = $size_w * sin($arc) + $size_h * cos($arc);

                // Fix rotated image miss 1px issue when degrees < 0
                $src_img_w -= 1;
                $src_img_h -= 1;
            }

            $tmp_img_w = $data -> width;
            $tmp_img_h = $data -> height;
            $avatar_size = IWJMB_Avatar_Field::get_avatar_size();
            $dst_img_w = $avatar_size[0];
            $dst_img_h = $avatar_size[1];

            $src_x = $data -> x;
            $src_y = $data -> y;

            if ($src_x <= -$tmp_img_w || $src_x > $src_img_w) {
                $src_x = $src_w = $dst_x = $dst_w = 0;
            } else if ($src_x <= 0) {
                $dst_x = -$src_x;
                $src_x = 0;
                $src_w = $dst_w = min($src_img_w, $tmp_img_w + $src_x);
            } else if ($src_x <= $src_img_w) {
                $dst_x = 0;
                $src_w = $dst_w = min($tmp_img_w, $src_img_w - $src_x);
            }

            if ($src_w <= 0 || $src_y <= -$tmp_img_h || $src_y > $src_img_h) {
                $src_y = $src_h = $dst_y = $dst_h = 0;
            } else if ($src_y <= 0) {
                $dst_y = -$src_y;
                $src_y = 0;
                $src_h = $dst_h = min($src_img_h, $tmp_img_h + $src_y);
            } else if ($src_y <= $src_img_h) {
                $dst_y = 0;
                $src_h = $dst_h = min($tmp_img_h, $src_img_h - $src_y);
            }

            // Scale to destination position and size
            $ratio = $tmp_img_w / $dst_img_w;
            $dst_x /= $ratio;
            $dst_y /= $ratio;
            $dst_w /= $ratio;
            $dst_h /= $ratio;

            $dst_img = imagecreatetruecolor($dst_img_w, $dst_img_h);

            // Add transparent background to destination image
            imagefill($dst_img, 0, 0, imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
            imagesavealpha($dst_img, true);

            $result = imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

            if ($result) {
                if (!imagepng($dst_img, $dst)) {
                    $this -> msg = "Failed to save the cropped image file";
                }
            } else {
                $this -> msg = "Failed to crop the image file";
            }

            imagedestroy($src_img);
            imagedestroy($dst_img);
        }
    }

    private function codeToMessage($code) {
        $errors = array(
            UPLOAD_ERR_INI_SIZE =>'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE =>'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL =>'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE =>'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR =>'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE =>'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION =>'File upload stopped by extension',
        );

        if (array_key_exists($code, $errors)) {
            return $errors[$code];
        }

        return 'Unknown upload error';
    }

    public function getData() {
        return $this -> data;
    }
    public function getDst() {
        return $this -> dst;
    }

    public function getSrc() {
        return $this -> src;
    }
    public function getSrc_url() {
        return $this -> src_url;
    }

    public function getResult() {
        return !empty($this -> data) ? $this -> dst : $this -> src;
    }

    public function getMsg() {
        return $this -> msg;
    }
}


IWJMB_Avatar_Field::init();
