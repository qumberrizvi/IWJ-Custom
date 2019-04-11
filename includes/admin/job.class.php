<?php

class IWJ_Admin_Job{
    static $fields = array();

    static function init(){
        $fields = array(
            array(
                'name' => __( 'Author', 'iwjob' ),
                'id'   => 'user_id',
                'type' => 'user_ajax',
                'group' => 'general',
                'placeholder' => __('Select an Student', 'iwjob'),
                'required' => true
            ),
            array(
                'name' => __( 'Posted Date', 'iwjob' ),
                'id'   => IWJ_PREFIX.'created_date',
                'type' => 'datetime',
                'group' => 'general'
            ),
            array(
                'name' => __( 'Modified Date', 'iwjob' ),
                'id'   => IWJ_PREFIX.'modified_date',
                'type' => 'datetime',
                'group' => 'general'
            ),
            array(
                'name' => __( 'Expiry Date', 'iwjob' ),
                'desc' => __( 'If leave empty, it will be never expire', 'iwjob' ),
                'id'   => IWJ_PREFIX.'expiry',
                'type' => 'datetime',
                'group' => 'general',
            ),
            array(
                'name' => __( 'Deadline for submission', 'iwjob' ),
                'id'   => IWJ_PREFIX.'deadline',
                'type' => 'date',
                'group' => 'general'
            ),
            array(
                'name' => __( 'Featured', 'iwjob' ),
                'id'   => IWJ_PREFIX.'featured',
                'type' => 'select',
                'options' => array('0' => __('No', 'iwjob'), '1' => __('Yes', 'iwjob')),
                'group' => 'general',
                //Always save to the database even when empty
                'allways_save'=>true
            ),
            array(
                'name' => __( 'Featured Date', 'iwjob' ),
                'desc' => __( 'Will be used to sort by featured', 'iwjob' ),
                'id'   => IWJ_PREFIX.'featured_date',
                'type' => 'datetime',
                'group' => 'general',
                'allways_save'=>true
            ),
            array(
                'name' => __( 'Featured Expiry Date', 'iwjob' ),
                'desc' => __( 'The value will automatically check by cronjob. If leave empty, it will be never expire', 'iwjob' ),
                'id'   => IWJ_PREFIX.'featured_expiry',
                'type' => 'datetime',
                'group' => 'general',
                //Always save to the database even when empty
                'allways_save'=>true
            ),
	        array(
		        'name'  => __( 'Email for Application', 'iwjob' ),
		        'id'    => IWJ_PREFIX . 'email_application',
		        'type'  => 'text',
		        'group' => 'general',
		        'desc'  => __( 'Enter multiple email addresses separated by comma', 'iwjob' ),
	        ),
            array(
                'name' => __( 'Salary From', 'iwjob' ),
                'id'   => IWJ_PREFIX.'salary_from',
                'type' => 'text',
                'group' => 'salary',
                //Always save to the database even when empty
                'allways_save'=>true
            ),
            array(
                'name' => __( 'Salary To', 'iwjob' ),
                'id'   => IWJ_PREFIX.'salary_to',
                'type' => 'text',
                'group' => 'salary',
                 //Always save to the database even when empty
                'allways_save'=>true
            ),
	        array(
		        'name' => __( 'Salary Postfix Text', 'iwjob' ),
		        'id'   => IWJ_PREFIX.'salary_postfix',
		        'type' => 'text',
		        'group' => 'salary'
	        ),
            array(
                'name' => __( 'Currency', 'iwjob' ),
                'id'   => IWJ_PREFIX.'currency',
                'type' => 'select',
                'options' => iwj_get_job_currencies(),
                'std' => iwj_get_currency(),
                'group' => 'salary'
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
                'name' => __( 'Template version', 'iwjob' ),
                'id'   => IWJ_PREFIX.'template_version',
                'type' => 'select',
                'options' => array(
                    '' => __('Default', 'iwproperty'),
                    'v1' => __('Version 1', 'iwproperty'),
                    'v2' => __('Version 2', 'iwproperty'),
                ),
                'group' => 'general'
            ),
            /*array(
                'name' => '',
                'id'   => IWJ_PREFIX.'gallery',
                'type' => 'image_upload',
                'group' => 'gallery'
            ),*/

        );

	    if ( ! iwj_option( 'disable_gender' ) ) {
		    $fields[] = array(
			    'name'     => __( 'Gender', 'iwjob' ),
			    'id'       => IWJ_PREFIX . 'job_gender',
			    'type'     => 'select_advanced',
			    'options'  => iwj_genders(),
			    'std'      => iwj_genders(),
			    'multiple' => true,
			    'group'    => 'general'
		    );
	    }
	    if ( ! iwj_option( 'disable_language' ) ) {
		    $fields[] = array(
			    'name'     => __( 'Languages', 'iwjob' ),
			    'id'       => IWJ_PREFIX . 'job_languages',
			    'type'     => 'select_advanced',
			    'options'  => iwj_get_available_languages(),
			    'multiple' => true,
			    'group'    => 'general'
		    );
	    }

	    if ( iwj_option( 'custom_apply_url' ) ) {
		    $fields[] = array(
			    'name'  => __( 'Custom Apply URL', 'iwjob' ),
			    'id'    => IWJ_PREFIX . 'custom_apply_url',
			    'type'  => 'text',
			    'group' => 'general'
		    );
	    }

        self::$fields = apply_filters('iwj_admin_job_fields', $fields);

        add_filter('wp_count_posts', array( __CLASS__, 'count_posts' ), 10, 3);
        add_action('pre_get_posts',array( __CLASS__, 'pre_get_posts' ) );
        add_action('admin_menu', array( __CLASS__ , 'register_metabox'));
        add_action('save_post', array( __CLASS__ , 'save_post'), 99);
        add_filter('manage_iwj_job_posts_columns' , array(__CLASS__, 'columns_head' ));
        add_filter('manage_iwj_job_posts_custom_column' , array(__CLASS__, 'columns_content' ), 10, 2);
        add_filter( 'display_post_states', array(__CLASS__, 'status_label'),10, 2);

        add_filter('post_row_actions', array(__CLASS__, 'remove_quick_edit'),10,2);
        add_filter('page_row_actions', array(__CLASS__, 'remove_quick_edit'),10,2);

	    add_filter( 'get_sample_permalink_html', array( __CLASS__, 'indeed_sample_permalink' ), 10, 1 );
	    add_action( 'wp_before_admin_bar_render', array( __CLASS__, 'indeed_change_job_link_render' ), 50 );
    }

    static function remove_quick_edit( $actions, $post){
        global $current_screen;
        if( $post->post_type != 'iwj_job' ) return $actions;
        unset($actions['inline hide-if-no-js']);
        $job = IWJ_Job::get_job($post);
        if($job->has_status('publish')){
            $title = __('View', 'iwjob');
        }else{
            $title = __('Preview');
        }
	    $permalink = $job->get_indeed_url() ? $job->get_indeed_url() : $job->permalink();

        $actions['view'] = '<a href="'.$permalink.'" title="'.$title.'" target="_blank">'.$title.'</a>';

        return $actions;
    }

	static function indeed_sample_permalink( $return ) {
		global $post;

		$job = IWJ_Job::get_job($post->ID);
		if($post->post_type=='iwj_job' && $job->get_indeed_url()){
			$return = '<strong>'.esc_html__('Permalink: ','iwjob').'</strong><span id="sample-permalink"><a target="_blank" href="'.esc_url($job->get_indeed_url()).'">'.__('View job','iwjob').'</a></span>';
		}

		return $return;
	}

	static function indeed_change_job_link_render() {
		global $wp_admin_bar, $post;
		if ( $post ) {
			$job = IWJ_Job::get_job( $post->ID );
			if ( $post->post_type == 'iwj_job' && $job->get_indeed_url() ) {
				$wp_admin_bar->remove_menu( 'view' );
				$args = array(
					'id'     => 'view',
					'title'  => 'View Job',
					'parent' => '',
					'href'   => $job->get_indeed_url(),
					'group'  => '',
					'meta'   => array()
				);
				$wp_admin_bar->add_menu( $args );
			}
		}
	}

    static function register_metabox(){
        global $submenu;
        unset($submenu['edit.php?post_type=iwj_job'][10]);

        remove_meta_box('submitdiv', 'iwj_job', 'side');
        remove_meta_box('iwj_sizediv', 'iwj_job', 'core');
        remove_meta_box('iwj_locationdiv', 'iwj_job', 'core');
        add_meta_box('submitdiv', __('Publish', 'iwjob'), array( __CLASS__, 'publish_metabox_html'), 'iwj_job', 'side', 'high');
        add_meta_box('iwj-job-box', __('Job Info', 'iwjob'), array( __CLASS__, 'metabox_html'), 'iwj_job', 'normal', 'high');
    }

    static function metabox_html(){
        global $post;
        $post_id = $post->ID;
        $saved = isset($_GET['post']) ? true : false;
        ?>
        <div class="iwj-metabox wp-clearfix">
            <table class="form-table">
            <?php
            foreach (self::$fields as $field){
                if($field['group'] != 'general') {
                    continue;
                }

                if($post->post_parent > 0 && in_array(
                        $field['id'], array(
                            'user_id', IWJ_PREFIX.'created_date',
                            IWJ_PREFIX.'modified_date', IWJ_PREFIX.'expiry',
                            IWJ_PREFIX.'featured', IWJ_PREFIX.'featured_date',
                            IWJ_PREFIX.'featured_expiry',
			                IWJ_PREFIX.'job_gender',IWJ_PREFIX.'job_languages',
			                IWJ_PREFIX.'custom_apply_url'
                        )
                    )
                ){
                    continue;
                }

                $field = IWJMB_Field::call( 'normalize', $field );
                if($field['id'] == 'user_id'){
                    $meta = $saved ? $post->post_author : '';
                }elseif($field['id'] == IWJ_PREFIX.'created_date'){
                    $meta = date($field['format'], strtotime($post->post_date));
                }elseif($field['id'] == IWJ_PREFIX.'modified_date'){
                    $meta = (int)$post->post_modified == 0 ? '' : date($field['format'], strtotime($post->post_modified));
                }else{
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                }
                IWJMB_Field::input($field, $meta );
            }
            ?>
            </table>

            <?php do_action('iwj_admin_job_form_after_general', $post_id); ?>

            <?php
            $salary_fields = array();
            foreach (self::$fields as $field){
                if($field['group'] != 'salary') {
                    continue;
                }
                else{
                    $salary_fields[] = $field;
                }
            }
            ?>

            <?php if($salary_fields){ ?>
                <h3><?php echo __('Salary', 'iwjob'); ?></h3>
                <table class="form-table">
                <?php
                foreach ($salary_fields as $field){
                    $field = IWJMB_Field::call( 'normalize', $field );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                    IWJMB_Field::input($field, $meta );
                }
                ?>
                </table>
                <?php do_action('iwj_admin_job_form_after_salary', $post_id); ?>
            <?php } ?>


            <?php
            $location_fields = array();
            foreach (self::$fields as $field){
                if($field['group'] != 'location') {
                    continue;
                }
                else{
                    $location_fields[] = $field;
                }
            }
            ?>

            <?php if($location_fields){ ?>
            <h3><?php echo __('Location', 'iwjob'); ?></h3>
            <table class="form-table">
            <?php
            foreach ($location_fields as $field){
                if($field['id'] == IWJ_PREFIX.'location' && iwj_option('auto_detect_location')){
                    continue;
                }

                $field = IWJMB_Field::call( 'normalize', $field );
                $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                IWJMB_Field::input($field, $meta );
            }
            ?>
            </table>
            <?php do_action('iwj_admin_job_form_after_location', $post_id); ?>
            <?php } ?>

        </div>
        <?php
    }

    static function publish_metabox_html(){
        global $post;
        ?>
        <div class="submitbox iwj-submitbox" id="submitpost">

            <div id="minor-publishing">

                <div id="misc-publishing-actions">

                    <div class="misc-pub-section">
                        <label><strong><?php echo __('Current status: ', 'iwjob'); ?></strong></label>
                        <?php if($post->post_status == 'iwj-pending-payment') { ?>
                        <?php echo IWJ_Job::get_status_title('iwj-pending-payment'); ?>
                        <div class="iwj-pending-payment-area">
                            <div class="user-package">
                                <label><strong><?php echo __('User Package ID: ', 'iwjob')?></strong></label>
                                <span>#<?php echo $user_package_id = get_post_meta($post->ID, IWJ_PREFIX.'user_package_id', true)?></span>
                            </div>
                            <div class="order-id">
                                <label><strong><?php echo __('Order ID: ', 'iwjob')?></label></strong>
                                <?php
                                $order_id = get_post_meta($user_package_id, IWJ_PREFIX.'order_id', true);
                                if($order_id){
                                ?>
                                <span><a href="<?php echo get_edit_post_link($order_id); ?>">#<?php echo $order_id?></a></span>
                                <?php } ?>
                            </div>

                            <?php if($order_id){ ?>
                                <p><i><?php printf(__('To publish job please completed order #%s', 'iwjob'), $order_id); ?></i></p>
                            <?php } ?>
                        </div>
                        <?php }elseif($post->post_status == 'pending') { ?>
                            <p>
                                <select name="_post_status" id="iwj-job-status">
                                    <?php
                                    $status_arr = IWJ_Job::get_status_array();
                                    foreach ($status_arr as $status => $title){
                                        if(in_array($status, array('pending', 'iwj-rejected', 'publish')))
                                        echo '<option value="'.$status.'" '.selected('pending', $status).'>'.$title.'</option>';
                                    }
                                    ?>
                                </select>
                            </p>
                        <?php }else{ ?>
                            <p>
                                <select name="_post_status" id="iwj-job-status">
                                    <?php
                                    $status_arr = IWJ_Job::get_status_array();
                                    foreach ($status_arr as $status => $title){
                                        if(in_array($status, array('iwj-pending-payment'))){
                                            continue;
                                        }
                                        echo '<option value="'.$status.'" '.selected(get_post_status(), $status).'>'.$title.'</option>';
                                    }
                                    ?>
                                </select>
                            </p>
                        <?php } ?>

                        <div class="iwj-job-reason hide" id="iwj-job-reason">
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

    static function save_post($post_id){
        if(isset($_POST) && $_POST && !defined( 'DOING_AJAX' )) {
            if (get_post_type($post_id) == 'iwj_job') {
				global $post;

                $job = IWJ_Job::get_job($post_id);

                $original_salary_from = get_post_meta($post_id, IWJ_PREFIX.'salary_from', true);
                $original_salary_to = get_post_meta($post_id, IWJ_PREFIX.'salary_to', true);

                self::$fields[] = array(
                    'id' => IWJ_PREFIX . 'reason',
                    'type' => 'wysiwyg',
                    'parent_tag' => 'div',
                );

                foreach (self::$fields as $field) {

                    if($field['id'] == IWJ_PREFIX.'location' && iwj_option('auto_detect_location')){
                        continue;
                    }

                    $field = IWJMB_Field::call('normalize', $field);
                    if ($field['id'] == IWJ_PREFIX . 'country') {
                        continue;
                    }

                    if($job->get_parent_id() > 0 && in_array(
                            $field['id'], array(
                                        'user_id', IWJ_PREFIX.'created_date',
                                        IWJ_PREFIX.'modified_date', IWJ_PREFIX.'expiry',
                                        IWJ_PREFIX.'featured', IWJ_PREFIX.'featured_date',
                                        IWJ_PREFIX.'featured_expiry'
                                        )
                            )
                    ){
                        continue;
                    }

                    $single = $field['clone'] || !$field['multiple'];
                    $old = IWJMB_Field::call($field, 'raw_post_meta', $post_id);
                    $new = isset($_POST[$field['id']]) ? $_POST[$field['id']] : ($single ? '' : array());

                    // Allow field class change the value
                    if ($field['clone']) {
                        $new = IWJMB_Clone::value($new, $old, $post_id, $field);
                    } else {
                        $new = IWJMB_Field::call($field, 'value', $new, $old, $post_id);
                        $new = IWJMB_Field::call($field, 'sanitize_value', $new);
                    }

                    global $wpdb;
                    // Call defined method to save meta value, if there's no methods, call common one
                    if ($field['id'] == IWJ_PREFIX . 'created_date') {
                        if($new){
                            $sql = "UPDATE {$wpdb->posts} SET post_date = %s, post_date_gmt = %s WHERE ID = %d";
                            $wpdb->query($wpdb->prepare($sql, date('Y-m-d H:i:s', $new), date('Y-m-d H:i:s', $new), $post_id));
                        }
                    } elseif ($field['id'] == IWJ_PREFIX . 'modified_date') {
                        if($new){
                            $sql = "UPDATE {$wpdb->posts} SET post_modified = %s, post_modified_gmt = %s WHERE ID = %d";
                            $wpdb->query($wpdb->prepare($sql, date('Y-m-d H:i:s', $new), date('Y-m-d H:i:s', $new), $post_id));
                        }
                    } elseif ($field['id'] == 'user_id') {
                        if($new){
                            $sql = "UPDATE {$wpdb->posts} SET post_author = %s WHERE ID = %d";
                            $wpdb->query($wpdb->prepare($sql, $new, $post_id));
                        }
                    }elseif ($field['id'] == IWJ_PREFIX. 'featured_date'){
                        if($_POST[IWJ_PREFIX.'featured']){
                            if(!$new){
                                $new = current_time('timestamp');
                            }
                        }else{
                            $new = '';
                        }

                        IWJMB_Field::call($field, 'save_post', $new, $old, $post_id);
                    }else {
                        IWJMB_Field::call($field, 'save_post', $new, $old, $post_id);
                    }
                }

                if($job->get_parent_id() == 0){
                    if(!isset($_POST[IWJ_PREFIX.'expiry']) || !$_POST[IWJ_PREFIX.'expiry']){
                        update_post_meta($post_id, IWJ_PREFIX.'expiry', '');
                    }
                }

                if(isset($_POST[IWJ_PREFIX.'salary_from'])){
                    $salary_from = sanitize_text_field($_POST[IWJ_PREFIX.'salary_from']);
                    $salary_to = sanitize_text_field($_POST[IWJ_PREFIX.'salary_to']);

                    if($salary_to !== '' && $salary_to < $salary_from){
                        $salary_to = $salary_from;
                    }

                    if($original_salary_from != $salary_from || $original_salary_to != $salary_to){
                        IWJ_Job::set_salary($post_id, $salary_from, $salary_to);
                    }
                }
	            // add job post to parent category when selected child categories
	            $cat_terms = wp_get_post_terms( $post_id, 'iwj_cat' );
	            foreach ( $cat_terms as $term ) {
		            while ( $term->parent != 0 && ! has_term( $term->parent, 'iwj_cat', $post ) ) {
			            // move upward until we get to 0 level terms
			            wp_set_post_terms( $post_id, array( $term->parent ), 'iwj_cat', true );
		            }
	            }

                if (isset($_POST['_post_status']) && $_POST['_post_status']) {
                    $old_status = get_post_status($post_id);
                    $new_status = sanitize_text_field($_POST['_post_status']);
                    if ($old_status != $new_status) {
                        $send_email = $new_status == 'pending' ? false : true;
                        $job->change_status($new_status, $send_email);

                        if($new_status == 'publish' && $job->get_parent_id() > 0){
                            $job->approve_update();
                            wp_redirect(admin_url('post.php?post='.$job->get_parent_id().'&action=edit'));
                            exit;
                        }
                    }
                }
            }
        }
    }

    static function columns_head( $columns )
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'avatar' => '',
            'title' => __('Title', 'iwjob'),
            'status' => __('Status', 'iwjob'),
            'user_id' => __('Posted by', 'iwjob'),
            'taxonomy-iwj_cat' => __('Category', 'iwjob'),
            //'taxonomy-iwj_type' => __('Type', 'iwjob'),
            //'taxonomy-iwj_location' => __('Location', 'iwjob'),
            'featured' => __('Featured', 'iwjob'),
            'expiry' => __('Expiry', 'iwjob'),
            'date' => __('Posted date', 'iwjob'),
        );

        return apply_filters('iwj_manage_column_header',$columns);
    }

    static function columns_content( $column, $post_ID ) {
        do_action('iwj_manage_column_content', $column, $post_ID);
        if ($column == 'user_id') {
            $job = IWJ_Job::get_job($post_ID);
            $author_id = $job->get_author_id();
            $user = get_userdata($author_id);
            if($user){
                echo '<a href="'.admin_url('edit.php?post_type=iwj_job&author_id='.$user->ID).'" >'.$user->display_name;
            }
        }
        if ($column == 'avatar') {
            $job = IWJ_Job::get_job($post_ID);
            $author_id = $job->get_author_id();
            if($author_id){
                echo get_avatar($author_id);
            }
        }
        if ($column == 'status') {
            $job = IWJ_Job::get_job($post_ID);
            echo '<span class="job-status '.$job->get_status().'">'.$job->get_status_title($job->get_status()).'</span>';
        }
        if ($column == 'featured') {
            $job = IWJ_Job::get_job($post_ID);
            if($job->get_status() == 'publish'){
                echo $job->is_featured() ? __('Yes') : __('No');
            }
            else{
                echo __('N/A', 'iwjob');
            }
        }
        if ($column == 'expiry') {
            $job = IWJ_Job::get_job($post_ID);
            if($job->get_status() == 'publish'){
                $expiry = get_post_meta($post_ID, IWJ_PREFIX.'expiry', true);
                if($expiry){
                    echo date_i18n(get_option('date_format'), $expiry);
                }else{
                    echo __('Never', 'iwjob');
                }
            }else{
                echo __('N/A', 'iwjob');
            }

        }
    }

    static function pre_get_posts($query){
        if(is_blog_admin() && $query->get('post_type') == 'iwj_job'){
            if(!isset($_GET['post_status'])){
                $query->set('post_status', array_keys(IWJ_Job::get_status_array()));
            }elseif($_GET['post_status'] == 'iwj-expired'){
                $query->set('post_status', 'publish');
                $query->set('meta_query', array(
                    'relation' => 'AND',
                    array(
                        'key' => IWJ_PREFIX.'expiry',
                        'value' => '',
                        'compare' => '!='
                    ),
                    array(
                        'key' => IWJ_PREFIX.'expiry',
                        'value' => current_time('timestamp'),
                        'type' => 'numeric',
                        'compare' => '<='
                    )
                ));
            }elseif($_GET['post_status'] == 'publish'){
                $query->set('meta_query', array(
                    'relation' => 'OR',
                    array(
                        'key' => IWJ_PREFIX.'expiry',
                        'value' => '',
                        'compare' => '='
                    ),
                    array(
                        'key' => IWJ_PREFIX.'expiry',
                        'value' => current_time('timestamp'),
                        'type' => 'numeric',
                        'compare' => '>'
                    )
                ));
            }

            if(isset($_GET['user_id']) && $_GET['user_id']){
                $query->set('author', $_GET['user_id']);
            }

            if(!isset($_GET['orderby'])){
                $query->set('orderby', 'date');
                $query->set('order', 'desc');
            }

            if(isset($_GET['author_id']) && $_GET['author_id']){
                $query->set('author', $_GET['author_id']);
            }
        }
    }

    static function count_posts( $counts, $type, $perm ){
        if($type == 'iwj_job'){
            if($counts->publish > 0){
                global $wpdb;
                $sql = "SELECT COUNT(p.ID) FROM {$wpdb->posts} as p JOIN {$wpdb->postmeta} as pm ON p.ID = pm.post_id WHERE post_type = %s AND post_status = %s AND pm.meta_key = %s AND pm.meta_value != '' AND CAST( pm.meta_value AS UNSIGNED ) <= %d";
                $total = $wpdb->get_var($wpdb->prepare($sql, 'iwj_job', 'publish', IWJ_PREFIX.'expiry', current_time('timestamp')));
                if($total){
                    $counts->publish = $counts->publish - $total;
                    $counts->{'iwj-expired'} = $counts->{'iwj-expired'} + $total;
                }
            }
        }
        return $counts;
    }

    static function status_label($statuses, $post){
        if($post->post_type == 'iwj_job'){
            if($post->post_parent > 0){
                return array('update');
            }
            else{
                return array();
            }
        }

        return $statuses;
    }
}

IWJ_Admin_Job::init();