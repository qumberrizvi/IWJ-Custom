<?php

class IWJ_Admin_Application{
    static function init(){
        add_action('add_meta_boxes', array( __CLASS__ , 'register_metabox'));
        add_action('save_post', array( __CLASS__ , 'save_post'));
        add_filter('manage_posts_columns' , array(__CLASS__, 'columns_head' ));
        add_filter('manage_posts_custom_column' , array(__CLASS__, 'columns_content' ), 10, 2);

        add_action('pre_get_posts',array( __CLASS__, 'pre_get_posts' ) );
    }

    static function register_metabox(){
        add_meta_box('iwj-application-box', __('Application Metabox Info', 'iwjob'), array( __CLASS__, 'metabox_html'), 'iwj_application', 'normal', 'high');
    }

    static function metabox_html(){
        global $post;
        $post_id = $post->ID;
        $application = IWJ_Application::get_application($post_id);
        $saved = isset($_GET['post']) ? true : false;
        ?>
        <div class="iwj-metabox wp-clearfix">
            <table class="form-table">
                <?php
                $source = $application->get_source();
                if($source == 'form'){
                    $fields = IWJ_Apply_Form::get_form_fields();
                }
                else{
                    $fields = IWJ_Application::get_core_fields();
                }

                array_unshift($fields, array(
                    'name' => __( 'Posted User', 'iwjob' ),
                    'id'   => 'user_id',
                    'type' => 'user_ajax',
                    'group' => 'general',
                    //'role' => 'iwj_candidate',
                    'placeholder' => __('Select an User', 'iwjob'),
                    'required' => true
                ));
                foreach($fields as $field){
                    if($field['id'] == IWJ_PREFIX.'message') continue;
                    $field['readonly'] = false;
                    $field = IWJMB_Field::call( 'normalize', $field );
                    if($field['id'] == 'user_id'){
                        $meta = $post->post_author;
                    }else{
                        $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                    }
                    IWJMB_Field::input($field, $meta );
                }
                do_action('iwj_admin_metabox_application', $post_id);
                ?>
            </table>
        </div>
        <?php
    }

    static function save_post($post_id){
        if(get_post_type($post_id) == 'iwj_application'){
            $application = IWJ_Application::get_application($post_id);
            $source = $application->get_source();
            if($source == 'form'){
                $fields = IWJ_Apply_Form::get_form_fields();
            }
            else{
                $fields = IWJ_Application::get_core_fields();
            }

            array_unshift($fields, array(
                'name' => __( 'Posted User', 'iwjob' ),
                'id'   => 'user_id',
                'type' => 'user_ajax',
                'group' => 'general',
                //'role' => 'iwj_candidate',
                'placeholder' => __('Select an User', 'iwjob'),
                'required' => true
            ));

            foreach ($fields as $field){
                if($field['id'] == IWJ_PREFIX.'message') continue;
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

            do_action('iwj_admin_save_application', $post_id);
        }
    }

    static function columns_head( $columns )
    {
        $screen = get_current_screen();
        if ($screen->post_type == 'iwj_application') {
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'title' => __('Title', 'iwjob'),
                'full_name' => __('Name', 'iwjob'),
                'email' => __('Email', 'iwjob'),
                'user' => __('Posted User', 'iwjob'),
                'job' => __('Job', 'iwjob'),
                'source' => __('Source', 'iwjob'),
                'date' => __('Date', 'iwjob'),
            );

            $columns = apply_filters('iwj_admin_application_columns_head', $columns);
        }

        return $columns;
    }

    static function columns_content( $column, $post_ID ) {
        $screen = get_current_screen();
        if($screen->post_type == 'iwj_application'){
            if ($column == 'user') {
                $post = get_post($post_ID);
                $user = get_userdata($post->post_author);
                if($user){
                    echo '<a target="_blank" title="'.__('Click to view', 'iwjob').'" href="'.get_edit_user_link($user->ID).'">'.$user->display_name.'</a>';
                }
            }
            if ($column == 'full_name') {
                $application = IWJ_Application::get_application($post_ID);
                echo '<a title="'.__('Click to filter', 'iwjob').'" href="'.admin_url().'edit.php?post_type=iwj_application&user_id='.$application->get_author_id().'">'.$application->get_full_name().'</a>';
            }
            if ($column == 'email') {
                $application = IWJ_Application::get_application($post_ID);
                echo '<a title="'.__('Click to filter', 'iwjob').'" href="'.admin_url().'edit.php?post_type=iwj_application&user_id='.$application->get_author_id().'">'.$application->get_email().'</a>';
            }
            if ($column == 'job') {
                $application = IWJ_Application::get_application($post_ID);
                $job = $application->get_job();
                if($job){
                    echo '<a title="'.__('Click to filter', 'iwjob').'" href="'.admin_url().'edit.php?post_type=iwj_application&job_id='.$job->get_id().'">'.$job->get_title().'</a>';
                }
            }
            if ($column == 'source') {
                $application = IWJ_Application::get_application($post_ID);
                echo $application->get_source();
            }

            do_action('iwj_admin_application_columns_content', $post_ID);
        }
    }

    static function pre_get_posts($query){
        if(is_blog_admin() && $query->get('post_type') == 'iwj_application'){
            if(!isset($_GET['post_status'])){
                $query->set('post_status', array_keys(IWJ_Application::get_status_array()));
            }

            if(isset($_GET['user_id']) && $_GET['user_id']){
                $query->set('author', $_GET['user_id']);
            }

            if(isset($_GET['job_id']) && $_GET['job_id']){
                $query->set('meta_query', array(
                    array(
                        'key' => IWJ_PREFIX.'job_id',
                        'value' => $_GET['job_id'],
                        'compare' => '='
                    )
                ));
            }
        }
    }
}

IWJ_Admin_Application::init();