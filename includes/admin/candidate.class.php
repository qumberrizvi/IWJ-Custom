<?php

class IWJ_Admin_Candidate{
    static $fields = array();

    static function init(){
        self::$fields = array(
            array(
                'name' => __( 'Author', 'iwjob' ),
                'id'   => 'user_id',
                'type' => 'user',
                'group' => 'general',
                'query_args' => array(
                    'role' => 'iwj_candidate'
                ),
                'disabled' => true
            ),
            array(
                'name' => __('Birthday', 'iwjob'),
                'id' => IWJ_PREFIX.'birthday',
                'type' => 'date',
                'group'=> 'general'
            ),
            array(
                'name' => __('Full Name', 'iwjob'),
                'id' => IWJ_PREFIX.'phone',
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
		        'name'    => __( 'Gender', 'iwjob' ),
		        'id'      => IWJ_PREFIX . 'gender',
		        'type'    => 'select_advanced',
		        'options' => iwj_genders(),
		        'group'=> 'general'
	        ),
            array(
                'name' => __( 'Headline', 'iwjob' ),
                'id'   => IWJ_PREFIX.'headline',
                'type' => 'text',
                'group'=> 'general'
            ),
            array(
                'name' => __( 'CV', 'iwjob' ),
                'id'   => IWJ_PREFIX.'cv',
                'type' => 'file_upload',
                'max_file_uploads' => 1,
                'group'=> 'general',
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
                'group'=> 'location'
	        ),
            array(
                'name' => __( 'Map', 'iwjob' ),
                'id'   => IWJ_PREFIX.'map',
                'type' => 'map',
                'address_field' => IWJ_PREFIX.'address',
                'group' => 'location'
            ),
	        array(
		        'name' => __('Featured', 'iwjob'),
		        'id' => IWJ_PREFIX.'featured',
		        'type' => 'checkbox',
		        'group'=> 'general'
	        ),
	        array(
		        'name'    => __( 'Show Profile', 'iwjob' ),
		        'id'      => IWJ_PREFIX . 'public_account',
		        'type'    => 'select_advanced',
		        'group'   => 'general',
		        'options' => array(
			        '1' => __( 'Yes', 'iwjob' ),
			        '0' => __( 'No', 'iwjob' )
		        ),
		        //'disabled' => true
	        ),
            array(
                'name' => __( 'Experience', 'iwjob' ),
                'id'   => IWJ_PREFIX . 'experience_text',
                'type' => 'text',
            ),
            array(
                'name' => __( 'Facebook', 'iwjob' ),
                'id'   => IWJ_PREFIX.'facebook',
                'type' => 'text',
            ),
            array(
                'name' => __( 'Twitter', 'iwjob' ),
                'id'   => IWJ_PREFIX.'twitter',
                'type' => 'text',
            ),
            array(
                'name' => __( 'Google Plus', 'iwjob' ),
                'id'   => IWJ_PREFIX.'google_plus',
                'type' => 'text',
            ),
            array(
                'name' => __( 'Youtube', 'iwjob' ),
                'id'   => IWJ_PREFIX.'youtube',
                'type' => 'text',
            ),
            array(
                'name' => __( 'Vimeo', 'iwjob' ),
                'id'   => IWJ_PREFIX.'vimeo',
                'type' => 'text',
            ),
            array(
                'name' => __( 'Linkedin', 'iwjob' ),
                'id'   => IWJ_PREFIX.'linkedin',
                'type' => 'text',
            ),
            array(
                'name' => '',
                'id'   => IWJ_PREFIX.'education',
                'type' => 'group',
                'clone' => true,
                'sort_clone' => true,
                'fields' => array(
                    array(
                        'name' => __( 'Title', 'iwjob' ),
                        'id'   => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'name' => __( 'School Name', 'iwjob' ),
                        'id'   => 'school_name',
                        'type' => 'text',
                    ),
                    array(
                        'name' => __( 'Date In - Date Out', 'iwjob' ),
                        'id'   => 'date',
                        'type' => 'text',
                    ),
                    array(
                        'name' => __( 'Description', 'iwjob' ),
                        'id'   => 'description',
                        'type' => 'textarea',
                    ),
                )
            ),
            array(
                'name' => '',
                'id'   => IWJ_PREFIX.'experience',
                'type' => 'group',
                'clone' => true,
                'sort_clone' => true,
                'fields' => array(
                    array(
                        'name' => __( 'Title', 'iwjob' ),
                        'id'   => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'name' => __( 'Full Name', 'iwjob' ),
                        'id'   => 'company',
                        'type' => 'text',
                    ),
                    array(
                        'name' => __( 'Date In - Date Out', 'iwjob' ),
                        'id'   => 'date',
                        'type' => 'text',
                    ),
                    array(
                        'name' => __( 'Description', 'iwjob' ),
                        'id'   => 'description',
                        'type' => 'textarea',
                    ),
                )
            ),
            array(
                'name' => 'Aadhaar',
                'id'   => IWJ_PREFIX.'gallery',
                'type' => 'image_advanced',
            ),
            array(
                'name' => __( 'Cover Image', 'iwjob' ),
                'desc' => __( 'Upload cover image file', 'iwjob' ),
                'id'   => IWJ_PREFIX.'cover_image',
                'type' => 'image_single',
            ),
            array(
                'name' => __( 'Video URL', 'iwjob' ),
                'desc' => __( 'Accept the Youtube or Vimeo url', 'iwjob' ),
                'id'   => IWJ_PREFIX.'video',
                'type' => 'url',
            ),
            array(
                'name' => __( 'Video Poster', 'iwjob' ),
                'desc' => __( 'Upload image for video poster', 'iwjob' ),
                'id'   => IWJ_PREFIX.'video_poster',
                'type' => 'image_single',
            ),
            array(
                'name' => '',
                'id'   => IWJ_PREFIX.'skill_showcase',
                'type' => 'group',
                'clone' => true,
                'sort_clone' => true,
                'fields' => array(
                    array(
                        'name' => __( 'Title', 'iwjob' ),
                        'id'   => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'name' => __( 'Value (percent)', 'iwjob' ),
                        'id'   => 'value',
                        'type' => 'number',
                        'class' => 'iwj_lim_skill_showcase',
                    ),
                )
            ),
            array(
                'name' => '',
                'id'   => IWJ_PREFIX.'award',
                'type' => 'group',
                'clone' => true,
                'sort_clone' => true,
                'fields' => array(
                    array(
                        'name' => __( 'Title', 'iwjob' ),
                        'id'   => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'name' => __( 'Year', 'iwjob' ),
                        'id'   => 'year',
                        'type' => 'text',
                    ),
                    array(
                        'name' => __( 'Description', 'iwjob' ),
                        'id'   => 'description',
                        'type' => 'textarea',
                    ),
                )
            ),
            array(
                'name'    => __( 'Template Version', 'iwjob' ),
                'id'      => IWJ_PREFIX . 'template_version',
                'type'    => 'select',
                'group'   => 'general',
                'options' => array(
                    '' => __('Default', 'iwjob'),
                    'v1' => __('Version 1', 'iwjob'),
                    'v2' => __('Version 2', 'iwjob'),
                ),
            ),
        );

	    if ( ! iwj_option( 'disable_language' ) ) {
		    self::$fields[] = array(
			    'name'     => __( 'Languages', 'iwjob' ),
			    'id'       => IWJ_PREFIX . 'languages',
			    'type'     => 'select_advanced',
			    'multiple' => true,
			    'options'  => iwj_get_available_languages(),
			    'group'    => 'general'
		    );
	    }

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
        if( $current_screen->post_type != 'iwj_candidate' ) return $actions;
        unset($actions['inline hide-if-no-js']);
        return $actions;
    }

    static function register_metabox(){
        remove_meta_box('submitdiv', 'iwj_candidate', 'core');
        remove_meta_box('iwj_locationdiv', 'iwj_candidate', 'core');
        add_meta_box('submitdiv', __('Publish', 'iwjob'), array( __CLASS__, 'publish_metabox_html'), 'iwj_candidate', 'side', 'high');
        add_meta_box('iwj-employer-box', __('Teacher Metabox Info', 'iwjob'), array( __CLASS__, 'metabox_html'), 'iwj_candidate', 'normal', 'high');
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
                            <select name="_post_status" id="iwj-candidate-status">
                                <?php
                                $status_arr = IWJ_Candidate::get_status_array();
                                foreach ($status_arr as $status => $title){
                                    echo '<option value="'.$status.'" '.selected(get_post_status(), $status).'>'.$title.'</option>';
                                }
                                ?>
                            </select>
                        </p>
                        <div class="iwj-candidate-reason hide" id="iwj-candidate-reason">
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

        wp_enqueue_script( 'jquery-ui-sortable');

        ?>
        <div class="iwj-metabox wp-clearfix">
            <table class="form-table">
                <?php
                $user = get_userdata($post->post_author);
                foreach (self::$fields as $field){
                    if(!in_array($field['id'], array('user_id','website', IWJ_PREFIX.'phone', 'email', IWJ_PREFIX.'birthday',IWJ_PREFIX . 'gender',IWJ_PREFIX . 'languages', IWJ_PREFIX . 'featured', IWJ_PREFIX.'headline', IWJ_PREFIX.'cv', IWJ_PREFIX.'public_account', IWJ_PREFIX.'experience_text', IWJ_PREFIX.'template_version'))){
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

            <?php do_action('iwj_admin_candidate_form_after_general', $post_id); ?>

            <h3><?php echo __('Education', 'iwjob'); ?></h3>
            <table class="form-table">
                <?php
                foreach (self::$fields as $field){
                    if(!in_array($field['id'], array(IWJ_PREFIX.'education'))){
                        continue;
                    }
                    $field = IWJMB_Field::call( 'normalize', $field );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                    IWJMB_Field::input($field, $meta );
                }
                ?>
            </table>

            <?php do_action('iwj_admin_candidate_form_after_educations', $post_id); ?>

            <h3><?php echo __('Work Experience', 'iwjob'); ?></h3>
            <table class="form-table">
                <?php
                foreach (self::$fields as $field){
                    if(!in_array($field['id'], array(IWJ_PREFIX.'experience'))){
                        continue;
                    }
                    $field = IWJMB_Field::call( 'normalize', $field );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );

                    IWJMB_Field::input($field, $meta );
                }
                ?>
            </table>

            <?php do_action('iwj_admin_candidate_form_after_experiences', $post_id); ?>

            <h3><?php echo __('Skill Showcase', 'iwjob'); ?></h3>
            <table class="form-table">
                <?php
                foreach (self::$fields as $field){
                    if(!in_array($field['id'], array(IWJ_PREFIX.'skill_showcase'))){
                        continue;
                    }
                    $field = IWJMB_Field::call( 'normalize', $field );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );

                    IWJMB_Field::input($field, $meta );
                }
                ?>
            </table>

            <?php do_action('iwj_admin_candidate_form_after_skills', $post_id); ?>

            <h3><?php echo __('Award', 'iwjob'); ?></h3>
            <table class="form-table">
                <?php
                foreach (self::$fields as $field){
                    if(!in_array($field['id'], array(IWJ_PREFIX.'award'))){
                        continue;
                    }
                    $field = IWJMB_Field::call( 'normalize', $field );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );

                    IWJMB_Field::input($field, $meta );
                }
                ?>
            </table>

            <?php do_action('iwj_admin_candidate_form_after_honors_awards', $post_id); ?>

            <h3><?php echo __('Images', 'iwjob'); ?></h3>
            <table class="form-table">
                <?php
                foreach (self::$fields as $field){
                    if(!in_array($field['id'], array(IWJ_PREFIX.'gallery'))){
                        continue;
                    }
                    $field = IWJMB_Field::call( 'normalize', $field );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );

                    IWJMB_Field::input($field, $meta );
                }
                ?>
            </table>

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

            <?php do_action('iwj_admin_candidate_form_after_images', $post_id); ?>

            <h3><?php echo __('Video', 'iwjob'); ?></h3>
            <table class="form-table">
                <?php
                foreach (self::$fields as $field){
                    if(!in_array($field['id'], array(IWJ_PREFIX.'video'))){
                        continue;
                    }
                    $field = IWJMB_Field::call( 'normalize', $field );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );

                    IWJMB_Field::input($field, $meta );
                }
                ?>
            </table>
            <table class="form-table">
                <?php
                foreach (self::$fields as $field){
                    if(!in_array($field['id'], array(IWJ_PREFIX.'video_poster'))){
                        continue;
                    }
                    $field = IWJMB_Field::call( 'normalize', $field );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );

                    IWJMB_Field::input($field, $meta );
                }
                ?>
            </table>

            <?php do_action('iwj_admin_candidate_form_after_video', $post_id); ?>

            <h3><?php echo __('Social Network Information', 'iwjob'); ?></h3>
            <table class="form-table">
                <?php
                foreach (self::$fields as $field){
                    if(!in_array($field['id'], array(IWJ_PREFIX.'facebook', IWJ_PREFIX.'twitter', IWJ_PREFIX.'google_plus', IWJ_PREFIX.'youtube', IWJ_PREFIX.'vimeo', IWJ_PREFIX.'linkedin'))){
                        continue;
                    }
                    $field = IWJMB_Field::call( 'normalize', $field );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                    IWJMB_Field::input($field, $meta );
                }
                ?>
            </table>

            <?php do_action('iwj_admin_candidate_form_after_socials', $post_id); ?>

            <h3><?php echo __('Location', 'iwjob'); ?></h3>
            <table class="form-table">
                <?php
                foreach (self::$fields as $field){
                    if(!in_array($field['id'], array(IWJ_PREFIX.'location', IWJ_PREFIX.'address', IWJ_PREFIX.'map'))){
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
                        if(!in_array($field['id'], array(IWJ_PREFIX.'gdpr_profile'))){
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
        require_once(ABSPATH . 'wp-admin/includes/screen.php');
        $screen = get_current_screen();
        if(isset($_POST) && $_POST && !defined( 'DOING_AJAX' ) && isset($_POST[IWJ_PREFIX.'headline']) && is_blog_admin()){
            if(get_post_type($post_id) == 'iwj_candidate'){
                $old_post_status = get_post_status($post_id);
                $new_post_status = sanitize_text_field($_POST['_post_status']);

                $post = get_post($post_id);
                $user = get_userdata($post->post_author);
                $data = array('ID' => $user->ID);
                $new_email = sanitize_email($_POST['email']);
                if($user->user_email != $new_email && !email_exists($new_email) ){
                    $data['user_email'] = $new_email;
                }

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

                /*$first_name = sanitize_text_field($_POST[IWJ_PREFIX.'first_name']);
                $last_name = sanitize_text_field($_POST[IWJ_PREFIX.'last_name']);

                global $wpdb;
                $sql = "UPDATE {$wpdb->posts} SET post_title = %s WHERE ID = %d";
                $wpdb->query($wpdb->prepare($sql, $first_name.' '.$last_name, $post_id));*/

                self::$fields[] = array(
                    'id'   => IWJ_PREFIX.'reason',
                    'type' => 'wysiwyg',
                    'parent_tag' => 'div',
                );

                foreach (self::$fields as $field){

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
                    $candidate = IWJ_Candidate::get_candidate($post_id);
                    $candidate->change_status($new_post_status, true);
                }
            }
        }
    }

    static function columns_head( $columns )
    {
        $screen = get_current_screen();
        if ($screen->post_type == 'iwj_candidate') {
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'avatar' => '',
                'title' => __('Display Name', 'iwjob'),
                'user_email' => __('User Email', 'iwjob'),
                'user_id' => __('User ID', 'iwjob'),
                'status' => __('Status', 'iwjob'),
                'date' => __('Register', 'iwjob'),
            );
        }

        return $columns;
    }

    static function columns_content( $column, $post_ID ) {
        $screen = get_current_screen();
        if($screen->post_type == 'iwj_candidate'){
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
                echo '<span class="job-status '.$status.'">'.IWJ_Candidate::get_status_title($status).'</span>';
            }
        }
    }

    static function pre_get_posts($query){
        if(is_blog_admin() && $query->get('post_type') == 'iwj_candidate'){
            if(!isset($_GET['post_status'])){
                $query->set('post_status', array_keys(IWJ_Employer::get_status_array()));
            }
        }
    }
}

IWJ_Admin_Candidate::init();