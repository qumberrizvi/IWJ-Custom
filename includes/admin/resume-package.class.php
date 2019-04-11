<?php

class IWJ_Admin_Resum_Package{
    static $fields = array();

    static function init(){
        self::$fields = array(
            'price' => array(
                'name' => __( 'Price', 'iwjob' ),
                'id'   => IWJ_PREFIX.'price',
                'type' => 'text',
                'required' => true,
                'std' => 10,
            ),
            'number_resum' => array(
                'name' => __( 'No of Resumes in Package', 'iwjob' ),
                'id'   => IWJ_PREFIX.'number_resum',
                'type' => 'text',
                'required' => true,
                'std' => 5,
            ),
        );
        add_action('admin_menu', array( __CLASS__ , 'register_metabox'));
        add_action('save_post', array( __CLASS__ , 'save_post'));
        add_filter('manage_posts_columns' , array(__CLASS__, 'columns_head' ));
        add_filter('manage_posts_custom_column' , array(__CLASS__, 'columns_content' ), 10, 2);
        add_filter( 'display_post_states', array(__CLASS__, 'status_label'),10, 2);
    }

    static function register_metabox(){
        add_meta_box('iwj-employer-meta-box', __('Package Metabox Info', 'iwjob'), array( __CLASS__, 'metabox_html'), 'iwj_resum_package', 'advanced', 'high');
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
                    //price
                    $field = IWJMB_Field::call( 'normalize', self::$fields['price'] );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                    IWJMB_Field::input($field, $meta );

                    //number resumes
                    $field = IWJMB_Field::call( 'normalize', self::$fields['number_resum'] );
                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                    IWJMB_Field::input($field, $meta );

                ?>
            </table>
        </div>
        <?php
    }

    static function save_post($post_id){
        if(isset($_POST) && $_POST){
            if(get_post_type($post_id) == 'iwj_resum_package'){
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
        if ($screen->post_type == 'iwj_resum_package') {
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'title' => __('Title', 'iwjob'),
                'price' => __('Price', 'iwjob'),
                'resumes' => __('Resumes', 'iwjob'),
                'featured' => __('Featured', 'iwjob'),
            );
        }

        return $columns;
    }

    static function columns_content( $column, $post_ID ) {
        $screen = get_current_screen();
        if($screen->post_type == 'iwj_resum_package'){
            if ($column == 'status') {
                $package = IWJ_Resume_Package::get_package($post_ID);
                echo $package->get_status_title($package->get_status());
            }
            if ($column == 'resumes') {
                $package = IWJ_Resume_Package::get_package($post_ID);
                echo $package->get_number_resume();
            }
            if ($column == 'price'){
                $package = IWJ_Resume_Package::get_package($post_ID);
                echo iwj_system_price($package->get_price());
            }
            if ($column == 'featured') {
                $package = IWJ_Resume_Package::get_package($post_ID);
                echo $package->is_featured() ? __('Yes', 'iwjob') : __('No', 'iwjob');
            }
        }
    }

    static function status_label($statuses, $post){
        if($post->post_type == 'iwj_package'){
            if($post->ID == iwj_option('free_package_id')){
                return array(__('Free', 'iwjob'));
            }
        }

        return $statuses;
    }
}

IWJ_Admin_Resum_Package::init();