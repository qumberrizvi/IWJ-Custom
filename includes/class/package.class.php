<?php
class IWJ_Package{
    static $cache = array();

    public $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    static function get_package($post = null, $force = false){
        $post_id = 0;
        if($post === null){
            $post = get_post();
        }

        if(is_numeric($post)){
            $post = get_post($post);
            if($post && !is_wp_error($post)){
                $post_id = $post->ID;
            }
        }
        elseif(is_object($post))
        {
            $post_id = $post->ID;
        }

        if($post_id){
            if($force){
                clean_post_cache( $post_id );
                $post = get_post($post_id);
            }

            if($force || !isset(self::$cache[$post_id])){
                self::$cache[$post_id] = new IWJ_Package($post);
            }

            return self::$cache[$post_id];
        }

        return null;
    }
    
    public function get_id(){

        return $this->post->ID;
    }

    public function get_title($original = false){
        if($original){
            return $this->post->post_title;
        }

        return get_the_title($this->post->ID);
    }

    public function get_sub_title(){
        return apply_filters('iwj_package_sub_title', get_post_meta($this->post->ID, IWJ_PREFIX.'sub_title', true), $this->post->ID);
    }

    public function get_description(){

        return $this->post->post_content;
    }

    public function get_price(){
        if($this->is_free()){
            return 0;
        }

        return get_post_meta($this->post->ID, IWJ_PREFIX.'price', true);
    }

    public function get_number_job(){

        return (int)get_post_meta($this->post->ID, IWJ_PREFIX.'number_job', true);
    }

    public function get_number_renew_job(){

        return (int)get_post_meta($this->post->ID, IWJ_PREFIX.'number_renew_job', true);
    }

    public function get_number_featured_job(){

        return (int)get_post_meta($this->post->ID, IWJ_PREFIX.'number_featured_job', true);
    }

    public function get_max_categories(){

        return get_post_meta($this->post->ID, IWJ_PREFIX.'max_categories', true);
    }

    public function get_expiry(){

        return get_post_meta($this->post->ID, IWJ_PREFIX.'expiry', true);
    }

    public function get_expiry_unit(){

        return get_post_meta($this->post->ID, IWJ_PREFIX.'expiry_unit', true);
    }

    public function get_job_expiry(){

        return get_post_meta($this->post->ID, IWJ_PREFIX.'job_expiry', true);
    }

    public function get_job_expiry_unit(){

        return get_post_meta($this->post->ID, IWJ_PREFIX.'job_expiry_unit', true);
    }

    public function is_featured(){

        return $this->get_id() == iwj_option('package_featured_id') ? true : false;
    }

    public function is_free(){

        return $this->get_id() == iwj_option('free_package_id') ? true : false;
    }

    public function get_time_expiry(){
        $seconds = 0;
        $unit = $this->get_job_expiry_unit();
        $expiry = $this->get_job_expiry();
        switch ( $unit ) {
            case 'day':
                $seconds = $expiry * 60*60*24 + current_time('timestamp');
                break;
            case 'week':
                $seconds = $expiry * 60*60*24*7 + current_time('timestamp');
                break;
            case 'month':
                $seconds = strtotime('+'.$expiry.' month', current_time('timestamp'));
                break;
            case 'year':
                $seconds = strtotime('+'.$expiry.' year', current_time('timestamp'));
                break;
        }

        return $seconds;
    }


    public function get_expiry_title(){
        $unit = $this->get_job_expiry_unit();
        $expiry = $this->get_job_expiry();
        switch ($unit){
            case 'day':
                return sprintf(_n('%d day', '%d days', $expiry, 'iwjob'), $expiry);
            case 'month':
                return sprintf(_n('%d month', '%d months', $expiry, 'iwjob'), $expiry);
            case 'year':
                return sprintf(_n('%d year', '%d years', $expiry, 'iwjob'), $expiry);
        }

        return '';
    }
    public function get_job_support(){

        return get_post_meta($this->post->ID, IWJ_PREFIX.'support_service', true);
    }
    public function get_type_package(){
        return get_post_meta($this->post->ID, IWJ_PREFIX.'type_package', true);
    }

    public function can_buy($user_id = ''){
        if(!$user_id){
            $user_id = get_current_user_id();
        }
        if($this->is_free() && iwj_option('free_package_times') > 0){
            global $wpdb;
            $sql = "SELECT count(1) FROM {$wpdb->posts} AS post 
                    JOIN {$wpdb->postmeta} AS postmeta ON post.ID = postmeta.post_id
                    WHERE post.post_type = %s AND post.post_status = %s AND post.post_author = %d AND postmeta.meta_key = %s AND postmeta.meta_value = %s";
            $sql = $wpdb->prepare($sql, 'iwj_u_package', 'publish', $user_id, IWJ_PREFIX.'package_id', $this->get_id());
            $total = $wpdb->get_var($sql);
            if($total >= iwj_option('free_package_times')){
                return false;
            }
        }

        return true;
    }

    public function get_buy_url(){
        $dashboard = iwj_get_page_permalink('dashboard');
        $pay_url = add_query_arg(array('iwj_tab' => 'new-package', 'package' => $this->post->post_name), $dashboard);

        return $pay_url;
    }

    public function get_submit_job_url(){
        $dashboard = iwj_get_page_permalink('dashboard');
        $url = add_query_arg(array('iwj_tab' => 'new-job'), $dashboard);

        return $url;
    }

    public function is_active(){
        if(is_user_logged_in() && current_user_can('create_iwj_jobs') && !current_user_can('administrator')){
            global $wpdb;
            $sql = "SELECT COUNT(p.ID) FROM {$wpdb->posts} AS p 
                JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id 
                JOIN {$wpdb->postmeta} AS pm1 ON p.ID = pm1.post_id 
                WHERE p.post_type = %s AND p.post_status = %s AND p.post_author = %d AND pm.meta_key = %s AND pm.meta_value = %d AND pm1.meta_key = %s AND CAST(pm1.meta_value AS UNSIGNED) > 0";

            $user_packages = $wpdb->get_var($wpdb->prepare($sql, 'iwj_u_package', 'publish', get_current_user_id(), IWJ_PREFIX.'package_id', $this->get_id(), IWJ_PREFIX.'remain_job'));
            return $user_packages ? true : false;
        }else{
            return false;
        }
    }

    static function get_query_packages($args = array()){

        $default_args = array(
            'post_type' => 'iwj_package',
            'post_status' => array('publish'),
            'posts_per_page' => '10',
        );

        $args = wp_parse_args($args, $default_args);

        return new WP_Query( $args );
    }

    static function get_packages($args = array()){

        $default_args = array(
            'post_type' => 'iwj_package',
            'post_status' => array('publish'),
            'orderby' => 'menu_order',
        );

        $args = wp_parse_args($args, $default_args);

        return get_posts( $args );
    }

    static function get_status_array(){
        return array(
            'publish' => __('Publish', 'iwjob'),
            'iwj-pending-payment' => __('Pending Payment', 'iwjob'),
            'iwj-expired' => __('Expired', 'iwjob'),
            'iwj-trash' => __('Trash', 'iwjob'),
        );
    }

    static function get_status_title($status){
        $status_arr = self::get_status_array();
        if(isset($status_arr[$status])){
            return $status_arr[$status];
        }

        return '';
    }
}