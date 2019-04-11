<?php

class IWJ_Admin_Package{
    static $fields = array();

    static function init(){
        self::$fields = array(
            'sub_title' => array(
                'name' => __( 'Sub Title', 'iwjob' ),
                'id'   => IWJ_PREFIX.'sub_title',
                'type' => 'text',
            ),
            'price' => array(
                'name' => __( 'Price', 'iwjob' ),
                'id'   => IWJ_PREFIX.'price',
                'type' => 'text',
                'required' => true,
                'std' => 10,
            ),
            'number_job' => array(
                'name' => __( 'No of Classes in Package', 'iwjob' ),
                'id'   => IWJ_PREFIX.'number_job',
                'type' => 'text',
                'desc' => __( '-1 is unlimited', 'iwjob' ),
                'required' => true,
                'std' => 5,
            ),
            'number_renew_job' => array(
                'name' => __( 'No of Renew Classes in Package', 'iwjob' ),
                'id'   => IWJ_PREFIX.'number_renew_job',
                'desc' => __( '-1 is unlimited', 'iwjob' ),
                'type' => 'text',
                'required' => true,
                'std' => 5,
            ),
            'number_featured_job' => array(
                'name' => __( 'No of Featured Classes in Package', 'iwjob' ),
                'id'   => IWJ_PREFIX.'number_featured_job',
                'desc' => __( '-1 is unlimited', 'iwjob' ),
                'type' => 'text',
                'required' => true,
                'std' => 2,
            ),
            'max_categories' => array(
                'name' => __( 'Maximum categories in Job', 'iwjob' ),
                'desc' => __( 'Blank is unlimited', 'iwjob' ),
                'id'   => IWJ_PREFIX.'max_categories',
                'type' => 'text',
                'std' => '1',
            ),
            'job_expiry' => array(
                'name' => __( 'Job Duration', 'iwjob' ),
                'id'   => IWJ_PREFIX.'job_expiry',
                'type' => 'text',
                'std' => '1',
            ),
            'job_expiry_unit' => array(
                'name' => '',
                'id'   => IWJ_PREFIX.'job_expiry_unit',
                'type' => 'select',
                'required' => true,
                'options' => array(
                    'day' => __('Days', 'iwjob'),
                    'month' => __('Months', 'iwjob'),
                    'year' => __('Years', 'iwjob'),
                ),
                'std' => 'month'
            ),
            'pricing_table_color'=>array(
                'name' => __( 'Pricing Table Color', 'iwjob' ),
                'id'   => IWJ_PREFIX.'pricing_table_color',
                'desc'   => __('Set Pricing Table Color', 'iwjob'),
                'type' => 'color',
            ),
        );

        add_action('admin_menu', array( __CLASS__ , 'register_metabox'));
        add_action('save_post', array( __CLASS__ , 'save_post'));
        add_filter('manage_posts_columns' , array(__CLASS__, 'columns_head' ));
        add_filter('manage_posts_custom_column' , array(__CLASS__, 'columns_content' ), 10, 2);
        add_filter( 'display_post_states', array(__CLASS__, 'status_label'),10, 2);
    }

    static function register_metabox(){
        add_meta_box('iwj-employer-meta-box', __('Package Metabox Info', 'iwjob'), array( __CLASS__, 'metabox_html'), 'iwj_package', 'normal', 'high');
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
                    //sub_title
                    $field = IWJMB_Field::call( 'normalize', self::$fields['sub_title'] );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                    IWJMB_Field::input($field, $meta );

                    //price
                    $field = IWJMB_Field::call( 'normalize', self::$fields['price'] );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                    IWJMB_Field::input($field, $meta );

                    //number job
                    $field = IWJMB_Field::call( 'normalize', self::$fields['number_job'] );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                    IWJMB_Field::input($field, $meta );

                    //number renew job
                    $field = IWJMB_Field::call( 'normalize', self::$fields['number_renew_job'] );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                    IWJMB_Field::input($field, $meta );

                    //number featured job
                    $field = IWJMB_Field::call( 'normalize', self::$fields['number_featured_job'] );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                    IWJMB_Field::input($field, $meta );

                    //number category job
                    $field = IWJMB_Field::call( 'normalize', self::$fields['max_categories'] );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                    IWJMB_Field::input($field, $meta );

                    //job expiry
                    echo '<tr class="iwjmb-field">';
                    echo '<th class="iwjmb-label"><label>'.__( 'Job Duration', 'iwjob' ).'</label></th>';
                    echo '<td>';
                    $field = IWJMB_Field::call( 'normalize', self::$fields['job_expiry'] );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                    echo IWJMB_Field::call( $field, 'input', $meta );
                    $field = IWJMB_Field::call( 'normalize', self::$fields['job_expiry_unit'] );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                    echo IWJMB_Field::call( $field, 'input', $meta );
                    echo '</td>';
                    echo '</tr>';

                    //number category job
                    $field = IWJMB_Field::call( 'normalize', self::$fields['pricing_table_color'] );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                    IWJMB_Field::input($field, $meta );
                ?>
            </table>
        </div>
        <?php
    }

    static function save_post($post_id){
        if(isset($_POST) && $_POST){
            if(get_post_type($post_id) == 'iwj_package'){
                foreach (self::$fields as $field){
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
            }
        }
    }

    static function columns_head( $columns )
    {
        $screen = get_current_screen();
        if ($screen->post_type == 'iwj_package') {
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'title' => __('Title', 'iwjob'),
                'price' => __('Price', 'iwjob'),
                'job' => __('Classes', 'iwjob'),
                'renew_job' => __('Renew Classes', 'iwjob'),
                'featured_job' => __('Featured Classes', 'iwjob'),
                'duration' => __('Duration', 'iwjob'),
                //'featured' => __('Featured', 'iwjob'),
            );
        }

        return $columns;
    }

    static function columns_content( $column, $post_ID ) {
        $screen = get_current_screen();
        if($screen->post_type == 'iwj_package'){
            if ($column == 'user_id') {
                $job = IWJ_Job::get_job($post_ID);
                $author_id = $job->get_author_id();
                $user = get_userdata($author_id);
                if($user){
                    echo $user->display_name;
                }
            }
            if ($column == 'status') {
                $package = IWJ_Package::get_package($post_ID);
                echo $package->get_status_title($job->get_status());
            }
            if ($column == 'job') {
                $package = IWJ_Package::get_package($post_ID);
	            echo ( $package->get_number_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : $package->get_number_job();
            }
            if ($column == 'price') {
                $package = IWJ_Package::get_package($post_ID);
                echo iwj_system_price($package->get_price());
            }
            if ($column == 'renew_job') {
                $package = IWJ_Package::get_package($post_ID);
                echo ($package->get_number_renew_job() == -1) ? __( 'Unlimited', 'iwjob' ) : $package->get_number_renew_job();
            }
            if ($column == 'featured_job') {
                $package = IWJ_Package::get_package($post_ID);
                echo ($package->get_number_featured_job() == -1) ? __( 'Unlimited', 'iwjob' ) : $package->get_number_featured_job();
            }
            if ($column == 'duration') {
                $package = IWJ_Package::get_package($post_ID);
                echo $package->get_expiry_title();
            }
            if ($column == 'featured') {
                $package = IWJ_Package::get_package($post_ID);
                echo $package->is_featured() ? __('Yes', 'iwjob') : __('No', 'iwjob');
            }
        }
    }

    static function status_label($statuses, $post){
        if($post->post_type == 'iwj_package'){
            if($post->ID == iwj_option('free_package_id')){
                return array(__('Free', 'iwjob'));
            }
            if($post->ID == iwj_option('package_featured_id')){
                return array(__('Featured', 'iwjob'));
            }
        }

        return $statuses;
    }
}

IWJ_Admin_Package::init();