<?php

class IWJ_Admin_Employer{
    static $fields = array();

    static function init(){

        new IWJ_Admin_Radiotax('iwj_size', 'iwj_employer');

        self::$fields = array(
            array(
                'name' => __( 'Author', 'iwjob' ),
                'id'   => 'user_id',
                'type' => 'user',
                'group' => 'general',
                'query_args' => array(
                    'role' => 'iwj_employer'
                ),
                'disabled' => true
            ),
            array(
                'name' => __('Headline', 'iwjob'),
                'id' => IWJ_PREFIX.'headline',
                'type' => 'text',
                'required' => true,
                'group'=> 'general'
            ),
            array(
                'name' => __('Website', 'iwjob'),
                'id' => 'website',
                'type' => 'text',
                'group'=> 'general'
            ),
            array(
                'name' => __('Email', 'iwjob'),
                'id' => 'email',
                'type' => 'email',
                'required' => true,
                'group'=> 'general'
            ),
            array(
                'name' => __('Phone', 'iwjob'),
                'id' => IWJ_PREFIX.'phone',
                'type' => 'text',
                'required' => true,
                'group'=> 'general'
            ),
            array(
                'name' => __('Date of Birth', 'iwjob'),
                'id' => IWJ_PREFIX.'founded_date',
                'type' => 'text',
                'required' => true,
                'group'=> 'general'
            ),
	        array(
		        'name' => __('Featured', 'iwjob'),
		        'id' => IWJ_PREFIX.'featured',
		        'type' => 'checkbox',
		        'group'=> 'general'
	        ),
            array(
                'name' => '',
                'id'   => IWJ_PREFIX.'gallery',
                'type' => 'image_advanced',
                'group' => 'gallery',
            ),
            array(
                'name' => __( 'Cover Image', 'iwjob' ),
                'id'   => IWJ_PREFIX.'cover_image',
                'type' => 'image_single',
                'group' => 'cover_image',
            ),
            array(
                'name' => __( 'Video URL', 'iwjob' ),
                'desc' => __( 'Accept the Youtube or Vimeo url', 'iwjob' ),
                'id'   => IWJ_PREFIX.'video',
                'type' => 'url',
                'group' => 'video',
            ),
            array(
                'name' => __( 'Video Poster', 'iwjob' ),
                'desc' => __( 'Upload image for video poster', 'iwjob' ),
                'id'   => IWJ_PREFIX.'video_poster',
                'type' => 'image_single',
                'group' => 'video',
            ),
            array(
                'name' => __( 'Location', 'iwjob' ),
                'id'   => IWJ_PREFIX.'location',
                'type' => 'taxonomy',
                'placeholder' => __( 'Select location', 'iwjob' ),
                'attributes' => array(
                    'class' => 'iwjmb-select_advanced'
                ),
                'options' => array(
                    'type' => 'select_tree',
                    'taxonomy' => 'iwj_location'
                ),
                'group' => 'location'
            ),
            array(
                'name' => __( 'Address', 'iwjob' ),
                'id'   => IWJ_PREFIX.'address',
                'type' => 'text',
                'group' => 'location'
            ),
            array(
                'name' => __( 'Map', 'iwjob' ),
                'id'   => IWJ_PREFIX.'map',
                'type' => 'map',
                'address_field' => IWJ_PREFIX.'address',
                'group' => 'location'
            ),
            array(
                'name' => __( 'Facebook', 'iwjob' ),
                'id'   => IWJ_PREFIX.'facebook',
                'type' => 'text',
                'group'=> 'social'
            ),
            array(
                'name' => __( 'Twitter', 'iwjob' ),
                'id'   => IWJ_PREFIX.'twitter',
                'type' => 'text',
                'group'=> 'social'
            ),
            array(
                'name' => __( 'Google Plus', 'iwjob' ),
                'id'   => IWJ_PREFIX.'google_plus',
                'type' => 'text',
                'group'=> 'social'
            ),
            array(
                'name' => __( 'Youtube', 'iwjob' ),
                'id'   => IWJ_PREFIX.'youtube',
                'type' => 'text',
                'group'=> 'social'
            ),
            array(
                'name' => __( 'Vimeo', 'iwjob' ),
                'id'   => IWJ_PREFIX.'vimeo',
                'type' => 'text',
                'group'=> 'social'
            ),
            array(
                'name' => __( 'Linkedin', 'iwjob' ),
                'id'   => IWJ_PREFIX.'linkedin',
                'type' => 'text',
                'group'=> 'social'
            ),
            array(
                'name'    => __( 'Template Version', 'iwjob' ),
                'id'      => IWJ_PREFIX . 'template_version',
                'type'    => 'select',
                'options' => array(
                    '' => __('Default', 'iwjob'),
                    'v1' => __('Version 1', 'iwjob'),
                    'v2' => __('Version 2', 'iwjob'),
                ),
                'group'=> 'general'
            ),

        );

	    if(iwj_option( 'show_gdpr_on_profile' )){
		    self::$fields[] = array(
			    'name' => __('I agree GDPR', 'iwjob'),
			    'id'   => IWJ_PREFIX.'gdpr_profile',
			    'type' => 'checkbox',
			    'group'=> 'gdpr'
		    );
		}

        add_action('admin_menu', array( __CLASS__ , 'register_metabox'));

        add_action('save_post', array( __CLASS__ , 'save_post'));
        add_action('pre_get_posts',array( __CLASS__, 'pre_get_posts' ) );
        add_filter('manage_posts_columns' , array(__CLASS__, 'columns_head' ));
        add_filter('manage_posts_custom_column' , array(__CLASS__, 'columns_content' ), 10, 2);

        add_filter('post_row_actions', array(__CLASS__, 'remove_quick_edit'),10,1);
        add_filter('page_row_actions', array(__CLASS__, 'remove_quick_edit'),10,1);
    }

    static function remove_quick_edit( $actions){
        global $current_screen;
        if( $current_screen->post_type != 'iwj_employer' ) return $actions;
        unset($actions['inline hide-if-no-js']);
        return $actions;
    }

    static function register_metabox(){
        remove_meta_box('submitdiv', 'iwj_employer', 'core');
        remove_meta_box('tagsdiv-iwj_size', 'iwj_employer', 'core');
        remove_meta_box('iwj_locationdiv', 'iwj_employer', 'core');
        add_meta_box('submitdiv', __('Publish', 'iwjob'), array( __CLASS__, 'publish_metabox_html'), 'iwj_employer', 'side', 'high');
        add_meta_box('iwj-employer-box', __('Student Info', 'iwjob'), array( __CLASS__, 'metabox_html'), 'iwj_employer', 'normal', 'high');
    }

    static function publish_metabox_html(){
        global $post;
        ?>
        <div class="submitbox iwj-submitbox" id="submitpost">

            <div id="minor-publishing">

                <div id="misc-publishing-actions">

                    <div class="misc-pub-section">
                        <label><strong><?php echo __('Current status: ', 'iwjob'); ?></strong></label>
                        <p>
                            <select name="_post_status" id="iwj-employer-status">
                                <?php
                                $status_arr = IWJ_Employer::get_status_array();
                                foreach ($status_arr as $status => $title){
                                    echo '<option value="'.$status.'" '.selected(get_post_status(), $status).'>'.$title.'</option>';
                                }
                                ?>
                            </select>
                        </p>
                        <div class="iwj-employer-reason hide" id="iwj-employer-reason">
                            <label><strong><?php echo __('Reason: ', 'iwjob'); ?></strong></label>
                            <?php
                            $field = IWJMB_Field::call( 'normalize', array(
                                'id'   => IWJ_PREFIX.'reason',
                                'type' => 'wysiwyg',
                                'parent_tag' => 'div',
                            ) );

                            $field = IWJMB_Field::call( 'normalize', $field );
                            $post = get_post();
                            $meta = IWJMB_Field::call( $field, 'post_meta', $post->ID, true );

                            IWJMB_Field::input($field, $meta );
                            ?>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>

            <div id="major-publishing-actions">
                <div id="delete-action">
                    <a class="submitdelete deletion" href="<?php echo get_delete_post_link(); ?>"><?php echo __('Move to Trash', 'iwjob'); ?></a></div>

                <div id="publishing-action">
                    <span class="spinner"></span>
                    <input name="original_publish" type="hidden" id="original_publish" value="<?php echo __('Update', 'iwjob'); ?>">
                    <input name="save" type="submit" class="button button-primary button-large" id="publish" value="<?php echo __('Update', 'iwjob'); ?>">
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <?php
    }

    static function metabox_html(){
        global $post;
        $post_id = $post->ID;
        $saved = isset($_GET['post']) ? true : false;
        ?>
        <div class="iwj-metabox wp-clearfix">
            <table class="form-table">
            <?php
            $user = get_userdata($post->post_author);
            foreach (self::$fields as $field){
                if($field['group'] != 'general'){
                    continue;
                }

                $field = IWJMB_Field::call( 'normalize', $field );

                if($field['id'] == 'email'){
                    $meta = $user->user_email;
                }elseif($field['id'] == 'user_id'){
                    $meta = $post->post_author;
                }elseif($field['id'] == 'website'){
                    $meta = $user->user_url;
                }else{
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );

                }
                IWJMB_Field::input($field, $meta );
            }
            ?>
            </table>

            <?php do_action('iwj_admin_employer_form_after_general', $post_id); ?>

            <h3><?php echo __('Video', 'iwjob'); ?></h3>
            <table class="form-table">
            <?php
            foreach (self::$fields as $field){
                if($field['group'] != 'video'){
                    continue;
                }

                $field = IWJMB_Field::call( 'normalize', $field );
                $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                IWJMB_Field::input($field, $meta );
            }
            ?>
            </table>

            <?php do_action('iwj_admin_employer_form_after_video', $post_id); ?>

            <h3><?php echo __('Aadhaar', 'iwjob'); ?></h3>
            <table class="form-table">
            <?php
            foreach (self::$fields as $field){
                if($field['group'] != 'gallery'){
                    continue;
                }

                $field = IWJMB_Field::call( 'normalize', $field );
                $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                IWJMB_Field::input($field, $meta );
            }
            ?>
            </table>

            <?php do_action('iwj_admin_employer_form_after_gallery', $post_id); ?>

            <h3><?php echo __('Cover Image', 'iwjob'); ?></h3>
            <table class="form-table">
            <?php
            foreach (self::$fields as $field){
                if(!in_array($field['id'], array(IWJ_PREFIX.'cover_image'))){
                    continue;
                }

                $field = IWJMB_Field::call( 'normalize', $field );
                $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                IWJMB_Field::input($field, $meta );
            }
            ?>
            </table>

            <?php do_action('iwj_admin_employer_form_after_image_single', $post_id); ?>

            <h3><?php echo __('Social Network Information', 'iwjob'); ?></h3>
            <table class="form-table">
            <?php
            foreach (self::$fields as $field){
                if($field['group'] != 'social'){
                    continue;
                }

                $field = IWJMB_Field::call( 'normalize', $field );
                $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                IWJMB_Field::input($field, $meta );
            }
            ?>
            </table>

            <?php do_action('iwj_admin_employer_form_after_socials', $post_id); ?>

            <h3><?php echo __('Location', 'iwjob'); ?></h3>
            <table class="form-table">
                <?php
                foreach (self::$fields as $field){
                    if($field['group'] != 'location') {
                        continue;
                    }

                    if($field['id'] == IWJ_PREFIX.'location' && iwj_option('auto_detect_location')){
                        continue;
                    }
                    $field = IWJMB_Field::call( 'normalize', $field );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                    IWJMB_Field::input($field, $meta );
                }
                ?>
            </table>

            <?php do_action('iwj_admin_employer_form_after_map', $post_id); ?>

			<?php if(iwj_option('show_gdpr_on_profile')){ ?>
					<h3><?php echo __('GDPR AGREEMENT', 'iwjob'); ?></h3>
					<table class="form-table">
						<?php
						foreach (self::$fields as $field){
							if($field['group'] != 'gdpr') {
								continue;
							}

							$field = IWJMB_Field::call( 'normalize', $field );
							$meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
							IWJMB_Field::input($field, $meta );
						}
						?>
					</table>
					<?php
				}
			?>

        </div>
        <?php
    }

    static function save_post($post_id){
        if(isset($_POST) && $_POST && !defined( 'DOING_AJAX' ) && isset($_POST[IWJ_PREFIX.'headline'])){
            if(get_post_type($post_id) == 'iwj_employer'){
                $old_post_status = get_post_status($post_id);
                $new_post_status = sanitize_text_field($_POST['_post_status']);

                $post = get_post($post_id);
                $user = get_userdata($post->post_author);
                $data = array('ID' => $user->ID);
                $new_email = sanitize_email($_POST['email']);
                if($user->user_email != $new_email && !email_exists($new_email) ){
                    $data['user_email'] = $new_email;
                }
                $website = sanitize_text_field($_POST['website']);
                $data['user_url'] = $website;

                $data['display_name'] = $post->post_title;
                if($post->post_title){
                    list($first_name, $last_name) = iwj_parse_name($post->post_title);
                    $data['first_name'] = $first_name;
                    $data['last_name'] = $last_name;
                }

                wp_update_user($data);

                $thumbnail_id = get_post_thumbnail_id($post_id);
                if($thumbnail_id){
                    update_user_meta($user->ID, IWJ_PREFIX.'avatar', $thumbnail_id);
                }

                self::$fields[] = array(
                    'id'   => IWJ_PREFIX.'reason',
                    'type' => 'wysiwyg',
                    'parent_tag' => 'div',
                );

                foreach (self::$fields as $field){
                    if(in_array($field['id'], array('email', 'website'))){
                        continue;
                    }

                    if($field['id'] == IWJ_PREFIX.'location' && iwj_option('auto_detect_location')){
                        continue;
                    }

                    $field = IWJMB_Field::call( 'normalize', $field );

                    $single = $field['clone'] || ! $field['multiple'];
                    $old    = IWJMB_Field::call( $field, 'raw_post_meta', $post_id );
                    $new    = isset( $_POST[ $field['id'] ] ) ? $_POST[ $field['id'] ] : ( $single ? '' : array() );

                    // Allow field class change the value
                    if ( $field['clone'] ) {
                        $new = IWJMB_Clone::value( $new, $old, $post_id, $field );
                    } else {
                        $new = IWJMB_Field::call( $field, 'value', $new, $old, $post_id );
                        $new = IWJMB_Field::call( $field, 'sanitize_value', $new);
                    }

                    // Call defined method to save meta value, if there's no methods, call common one
                    IWJMB_Field::call( $field, 'save_post', $new, $old, $post_id );
                }

                if($new_post_status != $old_post_status) {
                    $employer = IWJ_Employer::get_employer($post_id);
                    $employer->change_status($new_post_status, true);
                }
            }
        }
    }

    static function columns_head( $columns )
    {
        $screen = get_current_screen();
        if ($screen->post_type == 'iwj_employer') {
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'avatar' => '',
                'title' => __('Full Name', 'iwjob'),
                'user_email' => __('User Email', 'iwjob'),
                'user_id' => __('User', 'iwjob'),
                'status' => __('Status', 'iwjob'),
                'packages' => __('Packages', 'iwjob'),
                'jobs' => __('Classes', 'iwjob'),
                'date' => __('Register', 'iwjob'),
            );
        }

        return $columns;
    }

    static function columns_content( $column, $post_ID ) {
        $screen = get_current_screen();
        if($screen->post_type == 'iwj_employer'){
            if ($column == 'user_id') {
                $post = get_post($post_ID);
                $user = get_userdata($post->post_author);
                if($user){
                    echo '<a href="'.get_edit_user_link($user->ID).'">#'.$user->ID.'</a>';
                }
            }
            if ($column == 'user_email') {
                $post = get_post($post_ID);
                $user = get_userdata($post->post_author);
                if($user){
                    echo $user->user_email;
                }
            }
            if ($column == 'avatar') {
                $post = get_post($post_ID);
                $user = get_userdata($post->post_author);
                if($user){
                    echo get_avatar($user->ID);
                }
            }
            if ($column == 'status') {
                $status = get_post_status($post_ID);
                echo '<span class="job-status '.$status.'">'.IWJ_Employer::get_status_title($status).'</span>';
            }
            if ($column == 'jobs') {
                $post = get_post($post_ID);
                $user = IWJ_User::get_user($post->post_author);
                if($user){
                    echo '<a href="edit.php?post_type=iwj_job&user_id='.$user->get_id().'">'.$user->count_jobs(true).'</a>';
                }
            }
            if ($column == 'packages') {
                $post = get_post($post_ID);
                $user = IWJ_User::get_user($post->post_author);
                if($user){
                    echo '<a href="edit.php?post_type=iwj_u_package&user_id='.$user->get_id().'">'.$user->count_packages().'</a>';
                }
            }
        }
    }

    static function pre_get_posts($query){
        if(is_blog_admin() && $query->get('post_type') == 'iwj_employer'){
            if(!isset($_GET['post_status'])){
                $query->set('post_status', array_keys(IWJ_Employer::get_status_array()));
            }
        }
    }
}

IWJ_Admin_Employer::init();