<?php

//include_once IWJ_PLUGIN_DIR.'includes/class/field.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/post-types.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/cart.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/controller.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/user.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/job.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/employer.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/candidate.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/package.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/package.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/plan.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/resume-package.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/user-package.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/apply-job-package.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/order.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/shortcodes.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/query.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/email.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/email-queue.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/reviews.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/template-loader.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/application.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/alert.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/currency.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/price.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/woocommerce.class.php';
include_once IWJ_PLUGIN_DIR.'includes/class/wpml.helper.php';
include_once IWJ_PLUGIN_DIR.'includes/class/wpml.php';

//includes fields
include_once IWJ_PLUGIN_DIR.'includes/class/fields/field.php';

//payment gateways
include_once IWJ_PLUGIN_DIR.'includes/class/gateways/base.php';
include_once IWJ_PLUGIN_DIR.'includes/class/gateways/direct_bank.php';
include_once IWJ_PLUGIN_DIR.'includes/class/gateways/paypal.php';
include_once IWJ_PLUGIN_DIR.'includes/class/gateways/authorize.net.php';
include_once IWJ_PLUGIN_DIR.'includes/class/gateways/stripe.php';
include_once IWJ_PLUGIN_DIR.'includes/class/gateways/skrill.php';
include_once IWJ_PLUGIN_DIR.'includes/class/payment-gateways.php';

//socials
include_once IWJ_PLUGIN_DIR.'includes/class/socials/base.php';
include_once IWJ_PLUGIN_DIR.'includes/class/socials/facebook.php';
include_once IWJ_PLUGIN_DIR.'includes/class/socials/google.php';
include_once IWJ_PLUGIN_DIR.'includes/class/socials/twitter.php';
include_once IWJ_PLUGIN_DIR.'includes/class/socials/linkedin.php';
include_once IWJ_PLUGIN_DIR.'includes/class/social-logins.php';

//applies
include_once IWJ_PLUGIN_DIR.'includes/class/applies/base.php';
include_once IWJ_PLUGIN_DIR.'includes/class/applies/form.php';
include_once IWJ_PLUGIN_DIR.'includes/class/applies/linkedin.php';
include_once IWJ_PLUGIN_DIR.'includes/class/applies/facebook.php';
include_once IWJ_PLUGIN_DIR.'includes/class/applies.php';

//Emogrifier using for email content
if(!class_exists('Emogrifier')){
    include_once IWJ_PLUGIN_DIR.'includes/libs/class-emogrifier.php';
}

class IWJ_Front{
    static function init(){
        add_filter( 'ajax_query_attachments_args', array(__CLASS__, 'attachments_args'), 99 ,1 );
        add_action( 'iwj_message', array(__CLASS__, 'print_message'), 10 ,1 );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        //add_filter( 'the_title', array(__CLASS__, 'tab_title'), 10 , 2);
        add_filter( 'document_title_parts', array(__CLASS__, 'wp_title'));
        add_filter( 'body_class', array(__CLASS__,'body_class'));

        add_action( 'wp_footer', array( __CLASS__, 'print_templates' ) );

        add_filter( 'cron_schedules', 'iwj_add_new_intervals');

        add_action('iwj_alert_job_daily', 'iwj_alert_job_daily');
        add_action('iwj_alert_job_weekly', 'iwj_alert_job_weekly');
        add_action('wp_ajax_iwj_alert_job_daily', 'iwj_alert_job_daily');
        add_action('wp_ajax_iwj_alert_job_weekly', 'iwj_alert_job_weekly');

	    add_action( 'iwj_job_expiry_notice', array( 'IWJ_Controller', 'iwj_job_expiry_notice' ) );
	    add_action( 'wp_ajax_iwj_job_expiry_notice', array( 'IWJ_Controller', 'iwj_job_expiry_notice' ) );
        add_action('iwj_membership_expiry_notice', array( 'IWJ_Controller','membership_expiry_notice'));
        add_action('iwj_membership_expired_notice', array( 'IWJ_Controller','membership_expired_notice'));

        add_action('iwj_check_featured_job', 'iwj_check_featured_job');
        add_action('wp_ajax_iwj_check_featured_job', 'iwj_check_featured_job');

        add_action('iwj_delete_draft_job', 'iwj_delete_draft_job');
        add_action('wp_ajax_iwj_delete_draft_job', 'iwj_delete_draft_job');

        add_action('iwj_delete_pending_order', 'iwj_delete_pending_order');
        add_action('wp_ajax_iwj_delete_pending_order', 'iwj_delete_pending_order');

        //register feed
        add_action('init', array(__CLASS__, 'add_feeds'));

        // Secure order notes
        add_filter( 'comments_clauses', array( __CLASS__, 'exclude_order_comments' ), 10, 1 );
        add_action( 'comment_feed_join', array( __CLASS__, 'exclude_order_comments_from_feed_join' ) );
        add_action( 'comment_feed_where', array( __CLASS__, 'exclude_order_comments_from_feed_where' ) );

        add_action( 'delete_post', array('IWJ_Job', 'remove_job_reference') );
        add_action( 'delete_post', array('IWJ_Employer', 'remove_employer_reference') );
        add_action( 'delete_post', array('IWJ_Candidate', 'remove_candidate_reference') );
        add_action( 'delete_post', array('IWJ_Order', 'remove_order_reference') );

        add_action( 'init', array(__CLASS__, 'blockusers_init'));
        add_filter('show_admin_bar', array(__CLASS__, 'hide_admin_bar'));

        //add_filter( 'autoptimize_filter_noptimize', array(__CLASS__, 'autoptimize_filter_noptimize'));
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
    }

    static function blockusers_init() {
        if (is_user_logged_in() && is_blog_admin() && (current_user_can( 'iwj_candidate' ) || current_user_can( 'iwj_employer' )) && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
            wp_redirect( iwj_get_page_permalink('dashboard') );
            exit;
        }
    }

    static function hide_admin_bar($content) {
        if (is_user_logged_in() && (in_array('iwj_candidate', wp_get_current_user()->roles) || in_array('iwj_employer', wp_get_current_user()->roles))){
            return false;
        }

        return $content;
    }

    static function attachments_args($query){
        if(is_user_logged_in() && (current_user_can('iwj_candidate') || current_user_can('iwj_employer'))){
            $query['author'] = '';
        }

        return $query;
    }

    static function enqueue_scripts(){

        global $wp_query;
        $google_api_key = iwj_get_map_api_key();
        $js_version = false;

        wp_register_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key='.$google_api_key.'&libraries=places&sensor=false&language='. get_locale(), array('jquery'), $js_version, true);
        wp_register_script('infobox', 'https://cdn.rawgit.com/googlemaps/v3-utility-library/master/infobox/src/infobox.js', array('jquery'), $js_version, true);
        wp_register_script('markerclusterer', IWJ_PLUGIN_URL . '/assets/js/markerclusterer.js', array('jquery'), INWAVE_COMMON_VERSION, true);
        wp_register_script( 'infobubble', IWJ_PLUGIN_URL . '/assets/js/infobubble.js', array('jquery'), $js_version, true);
        wp_register_script('iw-spin', IWJ_PLUGIN_URL . '/assets/js/spin.js', array('jquery'), $js_version, true);
        wp_register_script( 'range-slide-js', IWJ_PLUGIN_URL . '/assets/js/jquery-asRange.min.js', array('jquery'), $js_version, true);

        wp_enqueue_script('iwj', IWJ_PLUGIN_URL . '/assets/js/script.js', array('jquery'), '', true);
        $user_captchas = array_flip(iwj_option('use_recaptcha'));
        foreach ($user_captchas as $key => $user_captcha) {
            $user_captchas[$key] = 1;
        }
        
        wp_localize_script('iwj', 'iwj', array(
            'site_url' => admin_url(),
            'base_url' => site_url(),
            'ajax_url' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce( "iwj-security" ),
            'query_vars' => json_encode( $wp_query->query ),
            'map_styles' => stripslashes(iwj_option('map_styles')),
            //allways send if user not logged in . avoid cache page
            'total_email_queue' => (is_user_logged_in() || !defined( 'WP_CACHE' ) || !WP_CACHE) ? IWJ_Email_Queue::count_emails() : 1,
            'woocommerce_checkout' => iwj_woocommerce_checkout() ? true : false,
            'lang' => (defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : ''),
            'use_recaptcha'=> $user_captchas
        ));
        //$js_version = rand(1, 1000);
        //if(iwj_option('use_recaptcha', array())) {
            wp_enqueue_script('recaptcha', 'https://www.google.com/recaptcha/api.js?onload=iwj_recaptcha&render=explicit', array(), $js_version, true);
        //}
        wp_enqueue_script('iw-serialize-form-json', IWJ_PLUGIN_URL . '/assets/js/iwj_serialize_form_json.js', array('jquery'), $js_version, true);
        wp_enqueue_script('iw-cookie', IWJ_PLUGIN_URL . '/assets/js/iwj_cookie.js', array('jquery'), $js_version, true);
        wp_enqueue_script('iw-filter-common-functions', IWJ_PLUGIN_URL . '/assets/js/iwj_filter_common.js', array('jquery'), $js_version, true);
        wp_enqueue_script('iw-filter-job'   , IWJ_PLUGIN_URL . '/assets/js/filter_jobs.js', array('jquery'), $js_version, true);
        wp_enqueue_script('iw-filter-candidates', IWJ_PLUGIN_URL . '/assets/js/filter_candidates.js', array('jquery'), $js_version, true);
        wp_enqueue_script('iw-filter-employers', IWJ_PLUGIN_URL . '/assets/js/filter_employers.js', array('jquery'), $js_version, true);
        wp_enqueue_script('iw-show-less-more', IWJ_PLUGIN_URL . '/assets/js/show_less_more.js', array('jquery'), $js_version, true);
        wp_enqueue_script('iw-ajax-pagination', IWJ_PLUGIN_URL . '/assets/js/ajax_pagination.js', array('jquery'), $js_version, true);
        wp_enqueue_script('iw-widget-filter-search', IWJ_PLUGIN_URL . '/assets/js/widget_filter_search.js', array('jquery'), $js_version, true);
        wp_enqueue_script('iw-send-ajax-mail-queue', IWJ_PLUGIN_URL . '/assets/js/send_ajax_mail_queue.js', array('jquery'), $js_version, true);
        wp_enqueue_script('iw-filter-alpha', IWJ_PLUGIN_URL . '/assets/js/filter_alpha.js', array('jquery'), $js_version, true);
        wp_enqueue_script('iw-switch-layout', IWJ_PLUGIN_URL . '/assets/js/switch_layout.js', array('jquery'), $js_version, true);
        wp_enqueue_script('iw-filter-box', IWJ_PLUGIN_URL . '/assets/js/iwj_filter_box.js', array('jquery'), $js_version, true);
        wp_enqueue_script('match-height', IWJ_PLUGIN_URL . '/assets/js/jquery.matchHeight.js', array('jquery'), $js_version, true);
        wp_register_script('bxslider', IWJ_PLUGIN_URL . '/assets/js/jquery.bxslider.js', array('jquery'), $js_version, true);
        wp_register_script('isotope', IWJ_PLUGIN_URL . '/assets/js/isotope.pkgd.min.js', array('jquery'), $js_version, true);
        wp_enqueue_script('jquery-fancybox', IWJ_PLUGIN_URL . '/assets/fancybox/jquery.fancybox.js', array('jquery'), $js_version, true);
	    wp_enqueue_script('iwj-rating-custom2',IWJ_PLUGIN_URL.'/assets/js/star-rating.js',array('jquery'),$js_version,true);
	    wp_register_script('iwj-rating-custom',IWJ_PLUGIN_URL.'/assets/js/rating-custom.js',array('jquery'),$js_version,true);
	    wp_register_script('job-single',IWJ_PLUGIN_URL.'/assets/js/job_single.js',array('jquery'),$js_version,true);
	    wp_register_script('candidate-single',IWJ_PLUGIN_URL.'/assets/js/candidate_single.js',array('jquery'),$js_version,true);
	    wp_register_script('employer-single',IWJ_PLUGIN_URL.'/assets/js/employer_single.js',array('jquery'),$js_version,true);

        wp_enqueue_style('iw-filter-job-load', IWJ_PLUGIN_URL . '/assets/css/load.css', array(), $js_version);
        wp_register_style('bxslider', IWJ_PLUGIN_URL . '/assets/css/jquery.bxslider.css', array(), $js_version);
        wp_register_style('jquery-fancybox', IWJ_PLUGIN_URL . '/assets/fancybox/jquery.fancybox.css', array(), $js_version);
        wp_register_style('ionicons', IWJ_PLUGIN_URL . '/assets/css/ionicons/css/ionicons.min.css', array(), $js_version);
        wp_enqueue_style('iwj-rating-style',IWJ_PLUGIN_URL . '/assets/css/star-rating.css', array(), $js_version);
        wp_register_style( 'range-slide-css', IWJ_PLUGIN_URL . '/assets/css/asRange.min.css', array(), $js_version);

        wp_enqueue_style('iwj', IWJ_PLUGIN_URL . '/assets/css/style.css');

        if(is_rtl()){
	        wp_enqueue_style('iwj-rtl', IWJ_PLUGIN_URL . '/assets/css/rtl.css');
        }
    }

    static function wp_title($title){

        if ( is_feed() ) {
            return $title;
        }

        global $post;

        if($post && $post->ID == iwj_get_page_id('dashboard')){
            $tab_title = self::tab_title();
            if($tab_title){
                $title ['title'] = $tab_title;
            }
        }

        return $title;
    }

    static function tab_title() {
        $title = '';
        if (isset($_GET['iwj_tab']) &&  $_GET['iwj_tab'] ) {
            $iwj_tab_titles = array(
                'new-job' => __('Add New Class', 'iwjob'),
                'new-package' => __('Add New Package', 'iwjob'),
                'new-resume-package' => __('Add New Resume Package', 'iwjob'),
                'follows' => __('Follows', 'iwjob'),
                'alerts' => __('Alerts', 'iwjob'),
                'save-jobs' => __('Saved Classes', 'iwjob'),
                'jobs' => __('All Classes', 'iwjob'),
                'applications' => __('Applications', 'iwjob'),
                'packages' => __('Packages', 'iwjob'),
                'orders' => __('Orders', 'iwjob'),
                'save-resumes' => __('Saved Profiles', 'iwjob'),
                'view-resumes' => __('Viewed Resumes', 'iwjob'),
                'view-application' => __('Application Details', 'iwjob'),
                'view-order' => __('Order Details', 'iwjob'),
                'profile' => __('Edit Profile', 'iwjob'),
	            'my-review' => __('My Reviews', 'iwjob'),
	            'reviews' => __('Reviews', 'iwjob'),
            );

            $iwj_tab_titles = apply_filters('iwj_dashboard_tab_title', $iwj_tab_titles);

            if(isset($iwj_tab_titles[$_GET['iwj_tab']])){
                $title = $iwj_tab_titles[$_GET['iwj_tab']];
            }
        }

        return $title;
    }

    static function print_message($tab){
        $message = get_option('_iwj_front_messsage');
        if($message){
            echo $message;
            update_option('_iwj_front_messsage', '');
        }
    }

    static function print_templates(){
        if(!is_user_logged_in()){
            iwj_get_template_part('login-popup');
            iwj_get_template_part('register-popup');
        }

        global $post;
        if(!$post || $post && $post->ID != iwj_get_page_id('dashboard')){
            iwj_get_template_part('job-alert-popup/form');
        }
    }

    public static function exclude_order_comments( $clauses ) {
        global $wpdb, $typenow;

        if ( is_admin() && $typenow == 'iwj_order' && current_user_can( 'manage_woocommerce' ) ) {
            return $clauses; // Don't hide when viewing orders in admin
        }

        if ( ! $clauses['join'] ) {
            $clauses['join'] = '';
        }

        if ( ! stristr( $clauses['join'], "JOIN $wpdb->posts ON" ) ) {
            $clauses['join'] .= " LEFT JOIN $wpdb->posts ON comment_post_ID = $wpdb->posts.ID ";
        }

        if ( $clauses['where'] ) {
            $clauses['where'] .= ' AND ';
        }

        $clauses['where'] .= " $wpdb->posts.post_type NOT IN ('iwj_order') ";

        return $clauses;
    }

    /**
     * Exclude order comments from queries and RSS.
     * @param  string $join
     * @return string
     */
    public static function exclude_order_comments_from_feed_join( $join ) {
        global $wpdb;

        if ( ! stristr( $join, "JOIN $wpdb->posts ON" ) ) {
            $join = " LEFT JOIN $wpdb->posts ON $wpdb->comments.comment_post_ID = $wpdb->posts.ID ";
        }

        return $join;
    }

    /**
     * Exclude order comments from queries and RSS.
     * @param  string $where
     * @return string
     */
    public static function exclude_order_comments_from_feed_where( $where ) {
        global $wpdb;

        if ( $where ) {
            $where .= ' AND ';
        }

        $where .= " $wpdb->posts.post_type NOT IN ('iwj_order') ";

        return $where;
    }

    /* add body class: support white color and boxed layout */
    static function body_class($classes){

        if(is_page()){
            $setting_pages = array(
                'login' => __('Login Page', 'iwjob'),
                'register' => __('Register Page', 'iwjob'),
                'lostpass' => __('Lost Password Page', 'iwjob'),
                'dashboard' => __('Dashboard Page', 'iwjob'),
                'jobs' => __('Classes Page', 'iwjob'),
                'candidates' => __('Teachers Page', 'iwjob'),
                'employers' => __('Students Page', 'iwjob'),
	            'suggest_job' => __('Job Suggestion Page', 'iwjob'),
	            'candidate_suggestion' => __('Teacher Suggestion Page', 'iwjob'),
            );
            foreach ($setting_pages as $page_id => $title){
                if(iwj_get_page_id($page_id) == get_the_ID()){
                    $classes[] = 'iwj-'.$page_id.'-page';
                }
            }
        }

        if(is_tax(iwj_get_job_taxonomies())){
            $classes[] = 'iwj-job-taxonomy-page';
        }

        if(defined('ICL_LANGUAGE_CODE')){
            $classes[] = 'language-'.ICL_LANGUAGE_CODE;
        }

        return $classes;
    }

    static function autoptimize_filter_noptimize($ao_noptimize){
        if($ao_noptimize == false){
            if(is_user_logged_in()){
                $ao_noptimize = true;
            }
        }

        return $ao_noptimize;
    }

    static function add_feeds(){
        add_feed('job_feed', array(__CLASS__, 'job_feed_fn'));
        add_feed('employer_feed', array(__CLASS__, 'employer_feed_fn'));
        add_feed('candidate_feed', array(__CLASS__, 'candidate_feed_fn'));
    }

    static function job_feed_fn(){
        iwj_get_template_part('feed/job_feed');
    }
    static function employer_feed_fn(){
        iwj_get_template_part('feed/employer_feed');
    }
    static function candidate_feed_fn(){
        iwj_get_template_part('feed/candidate_feed');
    }
}

IWJ_Front::init();
