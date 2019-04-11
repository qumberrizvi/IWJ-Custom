<?php

/**
 * Class IWJ_Package
 */
class IWJ_Plan{
    static $cache = array();

    public $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    /**
     * @param null $post
     * @param bool $force
     * @return IWJ_Plan|null
     */
    static function get_package($post = null, $force = false){
        $post_id = 0;
        if($post === null){
            $post = get_post();
        }

        if(is_numeric($post) && $post > 0){
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
                self::$cache[$post_id] = new IWJ_Plan($post);
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
    	$post_content = $this->post->post_content;

        return do_shortcode($post_content);
    }

    public function is_active(){
        if(is_user_logged_in() && current_user_can('create_iwj_jobs') && !current_user_can('administrator')){
            $current_plan_id = get_user_meta(get_current_user_id(), IWJ_PREFIX.'plan_id', true);
            return $current_plan_id && $current_plan_id == $this->get_id() ? true : false;
        }else{
            return false;
        }
    }

    public function get_price(){
        if($this->is_free()){
            return 0;
        }

        return get_post_meta($this->post->ID, IWJ_PREFIX.'price', true);
    }

    public function get_jobs($show_unlimited = false){

        $listings = get_post_meta($this->post->ID, IWJ_PREFIX.'number_job', true);
        if($show_unlimited && $listings == '-1'){
            return __('Unlimited', 'iwproperty');
        }
        return $listings;
    }

    public function get_renew_jobs($show_unlimited = false){
        $listings = get_post_meta($this->post->ID, IWJ_PREFIX.'number_renew_job', true);
        if($show_unlimited && $listings == '-1'){
            return __('Unlimited', 'iwproperty');
        }
        return $listings;
    }

    public function get_featured_jobs($show_unlimited = false){
        $listings = get_post_meta($this->post->ID, IWJ_PREFIX.'number_featured_job', true);
        if($show_unlimited && $listings == '-1'){
            return __('Unlimited', 'iwproperty');
        }
        return $listings;
    }

    public function get_number_image($show_unlimited = false){
	    $images = get_post_meta($this->post->ID, IWJ_PREFIX.'images', true);
	    if($images === ''){
            $images = iwj_option('max_gallery_images');
            if($show_unlimited && $images === ''){
                return __('Unlimited', 'iwproperty');
            }
        }elseif($show_unlimited && $images == '-1'){
            return __('Unlimited', 'iwproperty');
        }

        return $images;
    }

    public function get_expiry(){

        return get_post_meta($this->post->ID, IWJ_PREFIX.'expiry', true);
    }

    public function get_expiry_unit(){

        return get_post_meta($this->post->ID, IWJ_PREFIX.'expiry_unit', true);
    }

    public function is_featured(){

        return $this->get_id() == iwj_option('featured_plan_id') ? true : false;
    }

    public function is_free(){

        return $this->get_id() == iwj_option('free_plan_id') ? true : false;
    }

    public function get_time_expiry($start_time = 'now'){
        $seconds = 0;
        $unit = $this->get_expiry_unit();
        $expiry = $this->get_expiry();
        $start_time = $start_time == 'now' ? current_time('timestamp') : (int)$start_time;
        switch ( $unit ) {
            case 'day':
                $seconds = $expiry * 60*60*24 + $start_time;
                break;
            case 'week':
                $seconds = $expiry * 60*60*24*7 + $start_time;
                break;
            case 'month':
                $seconds = strtotime('+'.$expiry.' month', $start_time);
                break;
            case 'year':
                $seconds = strtotime('+'.$expiry.' year', $start_time);
                break;
        }

        return $seconds;
    }


    public function get_expiry_title(){
        $unit = $this->get_expiry_unit();
        $expiry = $this->get_expiry();
        switch ($unit){
            case 'day':
                return sprintf(_n('%d day', '%d days', $expiry, 'iwproperty'), $expiry);
            case 'month':
                return sprintf(_n('%d month', '%d months', $expiry, 'iwproperty'), $expiry);
            case 'year':
                return sprintf(_n('%d year', '%d years', $expiry, 'iwproperty'), $expiry);
        }

        return '';
    }

    public function can_buy($user_id = null){
        if($this->is_free()){
            $user_id = is_null($user_id) ? get_current_user_id() : $user_id;
            $free_package_used = get_user_meta($user_id, IWJ_PREFIX.'plan_free_used', true);
            if($free_package_used >= iwj_option('free_plan_times')){
                return false;
            }
        }

        return true;
    }

    /**
     * @param IWJ_User $user
     * @param string $type
     * @return bool
     */
    public function can_upgrade($user, $type = 'submit'){
        if(!$this->can_buy($user->get_id())){
            return false;
        }

        $user_plan    = $user->get_plan();
        $job_limit = 0;
        if($user_plan){
            if ( $type == 'featured' ) {
                $job_limit = $user_plan->get_featured_jobs();
            } else {
                $job_limit = $user_plan->get_jobs();
            }
        }

        if ( $type == 'featured' ) {
            $jobs = $this->get_featured_jobs();
        } elseif($type == 'renew') {
            $jobs = $this->get_renew_jobs();
        } else {
            $jobs = $this->get_jobs();
        }

        if ( $job_limit === '-1' ) {
            return false;
        } else if ( $jobs !== '-1' && $jobs <= $job_limit ) {
            return false;
        }

        return true;
    }

    public function get_buy_url(){
        $dashboard = iwj_get_page_permalink('dashboard');
        $pay_url = add_query_arg(array('iwj_tab' => 'new-package', 'plan-id' => $this->post->post_name), $dashboard);

        return $pay_url;
    }

    public function get_type_package(){
        return get_post_meta($this->post->ID, IWJ_PREFIX.'type_package', true);
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
    public function get_job_support(){

        return get_post_meta($this->post->ID, IWJ_PREFIX.'support_service', true);
    }

    static function get_query_plans($args = array()){

        $default_args = array(
            'post_type' => 'iwj_plan',
            'post_status' => array('publish'),
            'posts_per_page' => -1,
        );

        $args = wp_parse_args($args, $default_args);

        return new WP_Query( $args );
    }

    static function get_upgrade_plans_query($args = array(), $type = ''){
        $user = IWJ_User::get_user();
        $default_args = array(
            'post_type' => 'iwj_plan',
            'post_status' => array('publish'),
        );
        if($type == 'featured'){
            $jobs = 0;
            if($user->has_plan()){
                $package = $user->get_plan();
                $jobs = $package->get_featured_jobs();
            }
            $default_args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key' => IWJ_PREFIX.'number_featured_job',
                    'value' => '',
                    'compare' => '=',
                    'type' => 'NUMERIC'
                ),
                array(
                    'key' => IWJ_PREFIX.'number_featured_job',
                    'value' => $jobs,
                    'compare' => '>',
                    'type' => 'NUMERIC'
                )
            );
        }elseif($type == 'renew'){
            $jobs = 0;
            if($user->has_plan()){
                $package = $user->get_plan();
                $jobs = $package->get_featured_jobs();
            }
            $default_args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key' => IWJ_PREFIX.'number_renew_job',
                    'value' => '',
                    'compare' => '=',
                    'type' => 'NUMERIC'
                ),
                array(
                    'key' => IWJ_PREFIX.'number_renew_job',
                    'value' => $jobs,
                    'compare' => '>',
                    'type' => 'NUMERIC'
                )
            );
        }else{
            $jobs = 0;
            if($user->has_plan()){
                $package = $user->get_plan();
                $jobs = $package->get_jobs();
            }
            $default_args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key' => IWJ_PREFIX.'number_job',
                    'value' => '',
                    'compare' => '=',
                    'type' => 'NUMERIC'
                ),
                array(
                    'key' => IWJ_PREFIX.'number_job',
                    'value' => $jobs,
                    'compare' => '>',
                    'type' => 'NUMERIC'
                )
            );
        }

        $args = wp_parse_args($args, $default_args);

        $query = new WP_Query( $args );
        //echo $query->request;

        return $query;
    }

    static function get_plans($args = array()){

        $default_args = array(
            'post_type' => 'iwj_plan',
            'post_status' => array('publish'),
            'orderby' => 'menu_order',
        );

        $args = wp_parse_args($args, $default_args);

        return get_posts( $args );
    }
}