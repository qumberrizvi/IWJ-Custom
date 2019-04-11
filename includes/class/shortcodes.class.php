<?php
class IWJ_Shortcodes
{
    static function init()
    {
        add_shortcode('iwj_dashboard', array(__CLASS__, 'dashboard'));
        add_shortcode('iwj_login', array(__CLASS__, 'login'));
        add_shortcode('iwj_register', array(__CLASS__, 'register'));
        add_shortcode('iwj_lostpassword', array(__CLASS__, 'lostpassword'));
        add_shortcode('iwj_jobs', array(__CLASS__, 'jobs'));
        add_shortcode('iwj_jobs_carousel', array(__CLASS__, 'jobs_carousel'));
        add_shortcode('iwj_jobs_list', array(__CLASS__, 'jobs_list'));
        add_shortcode('iwj_jobs_suggestion', array(__CLASS__, 'jobs_suggestion'));
        add_shortcode('iwj_candidates_suggestion', array(__CLASS__, 'candidates_suggestion'));
        add_shortcode('iwj_find_jobs', array(__CLASS__, 'find_jobs'));
        add_shortcode('iwj_candidates', array(__CLASS__, 'candidates'));
        add_shortcode('iwj_recent_resumes', array(__CLASS__, 'recent_resumes'));
        add_shortcode('iwj_resumes_slider', array(__CLASS__, 'resumes_slider'));
        add_shortcode('iwj_categories', array(__CLASS__, 'categories'));
        add_shortcode('iwj_employers', array(__CLASS__, 'employers'));
        add_shortcode('iwj_employers_slider', array(__CLASS__, 'employers_slider'));
        add_shortcode('iwj_pricing_tables', array(__CLASS__, 'pricing_tables'));
        add_shortcode('iwj_plan_pricing_tables', array(__CLASS__, 'plan_pricing_tables'));
        add_shortcode('iwj_verify_account', array(__CLASS__, 'verify_account'));
        add_shortcode('iwj_recommend_adv', array(__CLASS__, 'recommend_adv'));
        add_shortcode('iwj_advanced_search', array(__CLASS__, 'advanced_search'));
        add_shortcode('iwj_advanced_search_white', array(__CLASS__, 'advanced_search_white'));
        add_shortcode('iwj_advanced_search_with_radius', array(__CLASS__, 'advanced_search_with_radius'));
        add_shortcode('iwj_advanced_search_candidates', array(__CLASS__, 'advanced_search_candidates'));
        add_shortcode('iwj_search_map', array(__CLASS__, 'search_map'));
        add_shortcode('iwj_candidate_with_map', array(__CLASS__, 'candidate_with_map'));
        add_shortcode('inwave_map_find_job', array(__CLASS__, 'map_find_job'));
        add_shortcode('iwj_jobs_indeed', array(__CLASS__, 'jobs_indeed'));

    }

    static function jobs($atts) {

        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_jobs', $atts ) : $atts;

        extract(shortcode_atts(array(
            "style_jobs_page" => "style1",
            "number_column_grid" => "2",
        ), $atts, 'iwj_jobs'));
        extract($atts);
        ob_start();

        $id_uniqid = uniqid();

        $data_ajax_filter = IWJ_Job_Listing::get_data_filters();

        $query = IWJ_Job_Listing::get_query_jobs($data_ajax_filter);

        iwj_get_template_part('parts/jobs', array('query' => $query, 'id_uniqid' => $id_uniqid,  'atts' => $atts));

        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    static function jobs_carousel($atts) {

        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_jobs_carousel', $atts ) : $atts;

        $atts = shortcode_atts(array(
            "class" => "",
            "filter" => "",
            "include_ids" => "",
            "exclude_ids" => "",
            "order_by" => "",
            "order" => "",
            "limit" => "",
            "jobs_per_page" => "",
            "style" => "",
            "title_block" => "",
            "taxonomies_filter" => "",
            "cat" => "",
            "type" => "",
            "level" => "",
            "skill" => "",
            "location" => "",
            "salary" => "",

        ), $atts, 'iwj_jobs_carousel');

        extract($atts);
        $taxonomies_filter = $taxonomies_filter ? explode(',',$taxonomies_filter) : array();
        $taxonomies = array();
        if(in_array('cat', $taxonomies_filter) && $cat){
            $taxonomies['iwj_cat'] = explode(',', $cat);
        }
        if(in_array('type', $taxonomies_filter) && $type && !iwj_option('disable_type')){
            $taxonomies['iwj_type'] = explode(',', $type);
        }
        if(in_array('level', $taxonomies_filter) && $level && !iwj_option('disable_level')){
            $taxonomies['iwj_level'] = explode(',', $level);
        }
        if(in_array('skill', $taxonomies_filter) && $skill && !iwj_option('disable_skill')){
            $taxonomies['iwj_skill'] = explode(',', $skill);
        }
        if(in_array('location', $taxonomies_filter) && $location){
            $taxonomies['iwj_location'] = explode(',', $location);
        }
        if(in_array('salary', $taxonomies_filter) && $salary){
            $taxonomies['iwj_salary'] = explode(',', $salary);
        }

        ob_start();

        $jobs = iwj_get_jobs($filter, $include_ids, $exclude_ids, $limit, $order_by, $order, $taxonomies);

        iwj_get_template_part('parts/jobs-carousel', array('jobs' => $jobs, 'atts' => $atts));

        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    static function jobs_list($atts){
	    $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_jobs_list', $atts ) : $atts;
	    $atts = shortcode_atts(array(
		    "class" => "",
		    "include_ids" => "",
		    "exclude_ids" => "",
		    "order_by" => "",
		    "order" => "",
		    "limit" => "",
		    "number_column" => "",
		    "style" => "",
		    "filter" => "",
		    "taxonomies_filter" => "",
		    "show_load_more" => "0",
		    "cat" => "",
		    "type" => "",
		    "level" => "",
		    "skill" => "",
		    "location" => "",
		    "salary" => "",
	    ), $atts, 'iwj_jobs_list');
	    extract($atts);
        $taxonomies_filter = $taxonomies_filter ? explode(',',$taxonomies_filter) : array();
        $taxonomies = array();
        if(in_array('cat', $taxonomies_filter) && $cat){
            $taxonomies['iwj_cat'] = explode(',', $cat);
        }
        if(in_array('type', $taxonomies_filter) && $type && !iwj_option('disable_type')){
            $taxonomies['iwj_type'] = explode(',', $type);
        }
        if(in_array('level', $taxonomies_filter) && $level && !iwj_option('disable_level')){
            $taxonomies['iwj_level'] = explode(',', $level);
        }
        if(in_array('skill', $taxonomies_filter) && $skill && !iwj_option('disable_skill')){
            $taxonomies['iwj_skill'] = explode(',', $skill);
        }
        if(in_array('location', $taxonomies_filter) && $location){
            $taxonomies['iwj_location'] = explode(',', $location);
        }
        if(in_array('salary', $taxonomies_filter) && $salary){
            $taxonomies['iwj_salary'] = explode(',', $salary);
        }
        ob_start();
	    $query = iwj_get_jobs($filter, $include_ids, $exclude_ids, $limit, $order_by, $order, $taxonomies,$load_more = true);
	    iwj_get_template_part('parts/jobs-list', array('query' => $query, 'atts' => $atts, 'taxonomies' => json_encode($taxonomies)));
	    $html = ob_get_contents();
	    ob_end_clean();
	    return $html;
    }

    static function jobs_suggestion($atts) {
	    $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_jobs_suggestion', $atts ) : $atts;

	    $atts = shortcode_atts(array(
		    "filter" => "",
		    "include_ids" => "",
		    "exclude_ids" => "",
		    "order_by" => "",
		    "order" => "",
		    "limit" => "",
		    "class" => "",
	    ), $atts, 'iwj_jobs_suggestion');

	    extract($atts);

	    ob_start();

        $jobs = iwj_get_jobs($filter, $include_ids, $exclude_ids, $limit, $order_by, $order);
	    $empty_cls = $jobs?'':'iwj_empty_cls';
	    iwj_get_template_part('parts/jobs-suggestion', array('jobs' => $jobs, 'atts' => $atts, 'class' => $empty_cls));

	    $html = ob_get_contents();
	    ob_end_clean();
	    return $html;
    }

    static function candidates_suggestion($atts) {
	    $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_candidates_suggestion', $atts ) : $atts;
	    $atts = shortcode_atts( array(
		    "filter"           => "",
		    "candidate_on_row" => "2",
		    "include_ids"      => "",
		    "exclude_ids"      => "",
		    "order_by"         => "",
		    "order"            => "",
		    "limit"            => "",
		    "class"            => "",
	    ), $atts, 'iwj_candidates_suggestion' );

	    extract($atts);

	    ob_start();

	    $candidates = iwj_get_candidates_suggestion( $filter, $include_ids, $exclude_ids, $limit, $order_by, $order);

	    $empty_cls = $candidates ? '' : 'iwj_empty_cls';
	    iwj_get_template_part('parts/candidates-suggestion', array('candidates' => $candidates, 'atts' => $atts, 'class' => $empty_cls));

	    $html = ob_get_contents();
	    ob_end_clean();
	    return $html;
    }

    static function find_jobs($atts) {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_find_jobs', $atts ) : $atts;
        extract(shortcode_atts(array(
	        "style"               => "",
	        "limit_keyword"       => "",
	        "hide_location_empty" => "1",
	        "limit_location"      => "",
	        "hide_category_empty" => "1",
	        "limit_category"      => "",
        ), $atts, 'iwj_find_jobs'));
        ob_start();

        if (!$limit_keyword) {
            $limit_keyword = 0;
        }


        $find_jobs_style = iwj_option('limit_keyword_job');
        if (!$find_jobs_style) {
            $find_jobs_style = 0;
        }

        /*$terms_categories = get_terms( array(
            'taxonomy' => 'iwj_cat',
            'hide_empty' => true,
        ) );

        $categories = array();
        if($terms_categories){
            foreach ($terms_categories as $terms_categorie){
                $count = iwj_count_job_with_term($terms_categorie->term_id, 'iwj_cat');
                if($count > 0){
                    $terms_categorie->count = $count;
                    $categories[] = $terms_categorie;
                }
            }
        }*/

	    $hide_category_empty = isset( $hide_category_empty ) ? $hide_category_empty : 1;
	    $limit_category = isset( $limit_category ) ? $limit_category : '';
	    $hide_location_empty = isset( $hide_location_empty ) ? $hide_location_empty : 1;
	    $limit_location = isset( $limit_location ) ? $limit_location : '';
	    $args_cat = array( 'hide_empty' => $hide_category_empty, 'number' => $limit_category );
	    $args_location = array( 'hide_empty' => $hide_location_empty, 'number' => $limit_location );
	    $categories = iwj_get_term_hierarchy( 'iwj_cat', 0, 0, $args_cat );
        $terms_locations = iwj_get_term_hierarchy('iwj_location', 0, 0, $args_location );

        $key_words = get_terms( array(
            'taxonomy' => 'iwj_keyword',
            'number' => ((is_tax(iwj_get_job_taxonomies()) || is_page(iwj_get_page_id('jobs'))) ? $find_jobs_style : $limit_keyword),
            'meta_key' => IWJ_PREFIX.'searched',
            'orderby' => 'meta_value_num',
            'order' => 'desc',
            'hide_empty' => false,

        ) );

        iwj_get_template_part('parts/find-jobs', array('categories' => $categories, 'terms_locations' => $terms_locations, 'hierarchy' => array(), 'key_words' => $key_words, 'style' => $style));
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    static function candidates($atts) {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_candidates', $atts ) : $atts;
        $fixcols = $class = '';
        extract(shortcode_atts(array(
            "class" => "",
            "fixcols" => "2",
        ), $atts, 'iwj_candidates'));
        ob_start();

        $filters = IWJ_Candidate_Listing::get_data_filters();
        $query = IWJ_Candidate_Listing::get_query_candidates($filters);

        iwj_get_template_part('parts/candidates', array('query' => $query, 'class' => $class, 'fixcols' => $fixcols));
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    static function recent_resumes($atts) {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_recent_resumes', $atts ) : $atts;

        extract(shortcode_atts(array(
            "style" => "",
            "number_column" => "",
            "candidate_ids" => "",
            "filter_candidates" => "",
            "class" => "",
            "limit" => ""
        ), $atts, 'iwj_recent_resumes'));
        ob_start();

        if($candidate_ids) {
            $array_ids = array();
            $array_ids = explode(',', $candidate_ids);
            $args = array(
                'post__in' => $array_ids,
                'post_type' => 'iwj_candidate',
                'orderby' => 'post_date',
            );
        }else{
            $args = array(
                'posts_per_page' => ($limit ? $limit : -1),
                'post_type' => 'iwj_candidate',
                'orderby' => 'post_date',
            );
        }

        $args['meta_query'][] = array(
		    'relation' => 'OR',
		    array(
			    'key' => IWJ_PREFIX . 'public_account',
			    'compare' => 'NOT EXISTS' // doesn't work
		    ),
		    array(
			    'key'     => IWJ_PREFIX . 'public_account',
			    'value'   => 1,
			    'compare' => '='
		    )
	    );

        if ($filter_candidates == 'featured') {
            $args['meta_query'][] = array(
                'key'     => IWJ_PREFIX.'featured',
                'value'   => '1',
                'compare' => '='
            );
        }

        $recent_resumes = get_posts($args);

        iwj_get_template_part('parts/recent-resumes', array('recent_resumes' => $recent_resumes, 'style' =>$style, 'number_column' => $number_column, 'class' => $class));
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    static function resumes_slider($atts) {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_resumes_slider', $atts ) : $atts;

        extract(shortcode_atts(array(
            "title_block" => "",
            "description_block" => "",
            "candidate_ids" => "",
            "class" => "",
            "limit" => "",
            "candidates_per_slider" => "",
            "auto_play" => "",
            "button_text" => "",
            "button_link" => "",
        ), $atts, 'iwj_resumes_slider'));
        ob_start();

        if($candidate_ids) {
            $array_ids = array();
            $array_ids = explode(',', $candidate_ids);
            $args = array(
                'post__in' => $array_ids,
                'post_type' => 'iwj_candidate',
                'orderby' => 'post_date',
            );
        }else{
            $args = array(
                'posts_per_page' => ($limit ? $limit : -1),
                'post_type' => 'iwj_candidate',
                'orderby' => 'post_date',
            );
        }

	    $args['meta_query'] = array(
		    'relation' => 'OR',
		    array(
			    'key' => IWJ_PREFIX . 'public_account',
			    'compare' => 'NOT EXISTS' // doesn't work
		    ),
		    array(
			    'key'     => IWJ_PREFIX . 'public_account',
			    'value'   => 1,
			    'compare' => '='
		    )
	    );

        $resumes_slider = get_posts($args);

        iwj_get_template_part('parts/resumes-slider', array('resumes_slider' => $resumes_slider, 'title_block' => $title_block, 'description_block' => $description_block, 'candidates_per_slider' => $candidates_per_slider, 'auto_play' => $auto_play, 'button_text' => $button_text, 'button_link' => $button_link, 'class' => $class));
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    static function employers($atts) {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_employers', $atts ) : $atts;
        $fixcols = $class = '';
        extract(shortcode_atts(array(
            "class" => "",
            "fixcols" => "2",
        ), $atts, 'iwj_employers'));
        ob_start();

        $filters = IWJ_Employer_Listing::get_data_filters();
        $query = IWJ_Employer_Listing::get_query_employers($filters);

        iwj_get_template_part('parts/employers', array('query' => $query, 'class' => $class, 'fixcols' => $fixcols));
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    static function employers_slider($atts) {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_employers_slider', $atts ) : $atts;

        $atts = shortcode_atts(array(
            'class' => '',
            'style' => '',
            'title_block' => '',
            'items_desktop' => '1',
            'items_desktop_small' => '1',
            'items_tablet' => '1',
            'items_mobile' => '1',
            'auto_play' => 'no',
            'limit' => '',
            'show_featured_employers' => '',
            'employer_ids' => '',
            'exclude_ids' => '',
            'employers_per_slider' => '',
            'hide_empty' => '',
            'order_by' => '',
            'order' => '',
        ), $atts, 'iwj_employers_slider');

        extract($atts);

        ob_start();
        $employer_ids = $employer_ids ? explode(',', $employer_ids) : array();
        $exclude_ids = $exclude_ids ? explode(',', $exclude_ids) : array();
        $employers = iwj_get_employers($employer_ids, $exclude_ids, $show_featured_employers, $hide_empty, $limit, $order_by, $order);
        iwj_get_template_part('parts/employers-slider', array('employers' => $employers, 'atts' => $atts));
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    static function categories($atts) {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_categories', $atts ) : $atts;

        $atts = shortcode_atts(array(
            'style' => '',
            'cats_per_row' => '',
            'show_all_cats' => '',
            'hide_empty' => '',
            'exclude_ids' => '',
            'cat_ids' => '',
            'limit' => '',
            'order_by' => '',
            'order' => '',
            'show_categories_child' => '',
            'show_categories_btn' => '',
            'link_all_cats' => '',
            'text_link_all_cats' => '',
            'items_desktop' => '',
            'items_desktop_small' => '',
            'items_tablet' => '',
            'items_mobile' => '',
            'auto_play' => '',
            'class' => '',
        ), $atts, 'iwj_categories');
        ob_start();

        extract($atts);
        if($cat_ids){
            $cat_ids = explode(',', $cat_ids);
        }
        if($exclude_ids){
            $exclude_ids = explode(',', $exclude_ids);
        }

        $categories = iwj_get_cats($cat_ids, $exclude_ids, $hide_empty, $limit, $order_by, $order);
        $categories_parent = iwj_get_cats_parent($cat_ids, $exclude_ids, $hide_empty, $limit, $order_by, $order);

        iwj_get_template_part('parts/categories', array('categories' => $categories, 'categories_parent' => $categories_parent, 'atts' => $atts));
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    static function dashboard($atts)
    {
        ob_start();
        $tab = isset($_GET['iwj_tab']) ? $_GET['iwj_tab'] : '';
        if(!$tab){
            $tab = 'overview';
        }

        ob_start();
        switch ($tab) {
            case 'overview':
                ob_start();
                if (current_user_can('create_iwj_jobs')) {
                    iwj_get_template_part('dashboard/overview/employer');
                }else if(current_user_can('apply_job')) {
                    iwj_get_template_part('dashboard/overview/candidate');
                }else{
                    iwj_get_template_part('dashboard/overview/user');
                }
                $sub_content = ob_get_contents();
                ob_clean();
                iwj_get_template_part('dashboard/overview', array('tab' => $tab, 'content' => $sub_content));
                break;
            case 'profile':
                ob_start();
                if (current_user_can('create_iwj_jobs')) {
                    iwj_get_template_part('dashboard/profile/employer-form');
                }else if(current_user_can('apply_job')) {
                    iwj_get_template_part('dashboard/profile/candidate-form');
                }else{
                    iwj_get_template_part('dashboard/profile/user-form');
                }
                $sub_content = ob_get_contents();
                ob_clean();
                iwj_get_template_part('dashboard/profile', array('tab' => $tab, 'content' => $sub_content));
                break;
            case 'new-job':
                $step = isset($_GET['step']) ? $_GET['step'] : 'form';
                $job_id = isset($_GET['job-id']) ? $_GET['job-id'] : '';
                $job = IWJ_Job::get_job($job_id);
                ob_start();
                iwj_get_template_part('dashboard/' . $tab . '/' . $step, array('step' => $step, 'job' => $job));
                $step_content = ob_get_contents();
                ob_clean();

                iwj_get_template_part('dashboard/' . $tab, array('tab' => $tab, 'job'=>$job, 'step' => $step, 'content' => $step_content));
                break;
            case 'edit-job':
                $job_id = isset($_GET['job-id']) ? $_GET['job-id'] : '';
                $job = IWJ_Job::get_job($job_id);
                iwj_get_template_part('dashboard/' . $tab, array('job' => $job));
                break;

            case 'new-package':
            case 'new-resume-package':
            case 'new-apply-job-package':
                $step = isset($_GET['step']) ? $_GET['step'] : 'select-package';
                ob_start();
                iwj_get_template_part('dashboard/' . $tab . '/' . $step, array('step' => $step));
                $step_content = ob_get_contents();
                ob_clean();
                iwj_get_template_part('dashboard/' . $tab, array('step' => $step, 'content' => $step_content));
                break;

            case 'renew-job':
            case 'make-featured':
                $step = isset($_GET['step']) ? $_GET['step'] : 'form';
                $job_id = isset($_GET['job-id']) ? $_GET['job-id'] : '';
                $job = IWJ_Job::get_job($job_id);
                ob_start();
                if ($step == 'form') {
                    $user_package = $job->get_user_package();
                    iwj_get_template_part('dashboard/' . $tab . '/' . $step, array('job' => $job, 'user_package' => $user_package));
                } elseif ($step == 'done') {
                    $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
                    $order = IWJ_Order::get_order($order_id);
                    iwj_get_template_part('dashboard/' . $tab . '/' . $step, array('job' => $job, 'order' => $order));
                } else {
                    iwj_get_template_part('dashboard/' . $tab . '/' . $step);
                }
                $step_content = ob_get_contents();
                ob_clean();

                iwj_get_template_part('dashboard/' . $tab, array('step' => $step, 'job'=>$job, 'content' => $step_content));
                break;

            case 'pay-order':
                $step = isset($_GET['step']) ? $_GET['step'] : 'form';
                $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
                $key = isset($_GET['key']) ? $_GET['key'] : '';
                $order = IWJ_Order::get_order($order_id);
                if($order){
                    if($step == 'payment'){
                        $can_pay = $order->can_pay($key);
                        if(is_wp_error($can_pay)){
                            $step_content = iwj_get_alert($can_pay->get_error_message(), 'danger');
                        }
                        else{
                            ob_start();
                            iwj_get_template_part('dashboard/' . $tab . '/' . $step, array('order' => $order));
                            $step_content = ob_get_clean();
                            ob_clean();
                        }
                    }else{
                        ob_start();
                        iwj_get_template_part('dashboard/' . $tab . '/' . $step, array('order' => $order));
                        $step_content = ob_get_contents();
                        ob_clean();
                    }
                }
                else{
                    $step_content = iwj_get_alert(__('Invalid Order.', 'iwjob'), 'danger');
                }

                iwj_get_template_part('dashboard/' . $tab, array('tab' => $tab, 'step' => $step, 'content' => $step_content));
                break;
            case 'view-application' :
                $appplication_id = isset($_GET['application-id']) ?  $_GET['application-id'] : '';
                $appplication = IWJ_Application::get_application($appplication_id);
                iwj_get_template_part('dashboard/' . $tab, array('tab' => $tab, 'application' => $appplication));
                break;
	        case 'edit-review':
		        $review_id = isset($_GET['review-id']) ?  $_GET['review-id'] : '';
		        $review = IWJ_Reviews::get_review($review_id);
		        iwj_get_template_part('dashboard/' . $tab, array('tab' => $tab, 'review' => $review));
	        	break;
            case 'thankyou':
                echo '<div class="iwj-main-block">';
                iwj_get_template_part('parts/thankyou-page', array('tab' => ''));
                echo '</div>';
                break;
            default :
                iwj_get_template_part('dashboard/' . $tab, array('tab' => $tab));
                break;
        }

        $tab_content = ob_get_contents();
        ob_clean();

        $tab_content = apply_filters('iwj_dashboard_content', $tab_content, $tab);

        iwj_get_template_part('dashboard', array('tab' => $tab, 'tab_content' => $tab_content));

        $html = ob_get_contents();
        ob_clean();

        return $html;
    }

    static function login($atts)
    {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_login', $atts ) : $atts;

        $atts = shortcode_atts(array(
            'pre_text' => '',
            'redirect_to' => '',
        ), $atts, 'iwj_login');
        ob_start();

        if(isset($_GET['redirect_to']) && $_GET['redirect_to']){
            $atts['redirect_to'] = $_GET['redirect_to'];
        }

        iwj_get_template_part('login', $atts);
        $html = ob_get_contents();
        ob_clean();

        return $html;
    }

    static function register($atts)
    {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_register', $atts ) : $atts;
        $atts = shortcode_atts(array(), $atts, 'iwj_register');

        ob_start();
        if(isset($_GET['social_register']) && $_GET['social_register']){
            $args = array(
                'user_name' => isset($_GET['user_name']) ? sanitize_text_field($_GET['user_name']) : '' ,
                'display_name' => isset($_GET['display_name']) ? urldecode($_GET['display_name']) : '',
                'profile_image_url' => isset($_GET['profile_image_url']) ? urldecode($_GET['profile_image_url']) : '',
                'email' => isset($_GET['email']) ? urldecode($_GET['email']) : '' ,
                'social' => IWJ()->social_logins()->get_social($_GET['social_register']),
                'redirect_to' => ''
            );

            if(isset($_GET['redirect_to']) && $_GET['redirect_to']){
                $atts['redirect_to'] = $_GET['redirect_to'];
            }

            iwj_get_template_part('social-register', $args);
        }else{
            $_SESSION['iwj_verified_email'] = '';
            $args = array(
                'redirect_to' => ''
            );
            if(isset($_GET['redirect_to']) && $_GET['redirect_to']){
                $atts['redirect_to'] = $_GET['redirect_to'];
            }

            iwj_get_template_part('register', $args);
        }
        $html = ob_get_contents();
        ob_clean();

        return $html;
    }

    static function lostpassword($atts)
    {
        ob_start();
        iwj_get_template_part('lostpassword');
        $html = ob_get_contents();
        ob_clean();

        return $html;
    }

    static function pricing_tables($atts)
    {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_pricing_tables', $atts ) : $atts;

        $atts = shortcode_atts(array(
            'style' => '',
            'hide_free_package' => '',
            'class' => '',
        ), $atts, 'iwj_pricing_tables');

        $_packages = IWJ_Package::get_packages(array('order'=>'ASC'));
        $packages = array();
        if($_packages){
            foreach ($_packages as $package){
                $package = IWJ_Package::get_package($package);
                if($package->can_buy()){
                    if(!$atts['hide_free_package'] || !$package->is_free()){
                        $packages[] = $package;
                    }
                }
            }
        }

        ob_start();
        iwj_get_template_part('parts/pricing-tables', array('packages' => $packages, 'atts' => $atts));
        $html = ob_get_contents();
        ob_clean();

        return $html;
    }

    static function plan_pricing_tables($atts)
    {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_plan_pricing_tables', $atts ) : $atts;

        $atts = shortcode_atts(array(
            'style' => '',
            'hide_free_plan' => '',
            'class' => '',
        ), $atts, 'iwj_plan_pricing_tables');

        $_plans = IWJ_Plan::get_plans(array('order'=>'ASC'));
        $plans = array();
        if($_plans){
            foreach ($_plans as $plan){
                $plan = IWJ_Plan::get_package($plan);
                if($plan->can_buy()){
                    if(!$atts['hide_free_plan'] || !$plan->is_free()){
                        $plans[] = $plan;
                    }
                }
            }
        }

        ob_start();
        iwj_get_template_part('parts/plan-pricing-tables', array('plans' => $plans, 'atts' => $atts));
        $html = ob_get_contents();
        ob_clean();

        return $html;
    }

    static function verify_account($atts)
    {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_verify_account', $atts ) : $atts;

        $atts = shortcode_atts(array(
            'class' => '',
        ), $atts, 'iwj_verify_account');

        $html = '';
        $user = IWJ_User::get_user();
        if($user){
            ob_start();
            if($user->is_verified()){
                iwj_get_template_part('verify-account/thankyou', array('atts' => $atts));
            }else{
                iwj_get_template_part('verify-account/form', array('atts' => $atts));
            }
            $html = ob_get_contents();
            ob_clean();
        }

        return $html;
    }

    static function recommend_adv($atts)
    {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_recommend_adv', $atts ) : $atts;

        $atts = shortcode_atts(array(
            'title' => '',
            'class' => '',
        ), $atts, 'iwj_recommend_adv');

        $html = '';
        ob_start();
        iwj_get_template_part('parts/recommend-adv', array('atts' => $atts));
        $html = ob_get_contents();
        ob_clean();

        return $html;
    }

    static function advanced_search_with_radius($atts)
    {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_advanced_search_with_radius', $atts ) : $atts;

        $atts = shortcode_atts(array(
	        'unit'                => 'Km',
	        'min_radius'          => '15',
	        'max_radius'          => '100',
	        'default_radius'      => '40',
	        "hide_category_empty" => "1",
	        "limit_category"      => "",
	        'class'               => ''
        ), $atts, 'iwj_advanced_search_with_radius');

        wp_enqueue_script( 'range-slide-js' );
        wp_enqueue_style( 'range-slide-css' );
        wp_enqueue_script( 'google-maps' );

        wp_enqueue_script('iwj-search-advanced',IWJ_PLUGIN_URL.'/assets/js/search-advanced.js',array('jquery'),false,true);
        wp_enqueue_script('iwj-search-advanced-radius',IWJ_PLUGIN_URL.'/assets/js/search-advanced-radius.js',array('jquery'),false,true);
        wp_enqueue_style( 'search-map-css', IWJ_PLUGIN_URL.'/assets/css/search-map.css', array('iwjmb-select2' ), false);
        wp_localize_script( 'iwj-search-advanced', 'iwj_search_advanced', array(
            'show_advance_text' => __('Show advanced search', 'iwjob'),
            'hide_advance_text' => __('Hide advanced search', 'iwjob'),
        ));

        wp_localize_script( 'iwj-search-advanced-radius', 'iwj_search_advanced_radius', array(
            'min_radius' => $atts['min_radius'],
            'max_radius' => $atts['max_radius'],
            'radius' => isset($_GET['radius']) ? (int)$_GET['radius'] : $atts['default_radius'],
        ));

	    $hide_category_empty = isset( $hide_category_empty ) ? $hide_category_empty : 1;
	    $limit_category = isset( $limit_category ) ? $limit_category : '';
	    $args_cat = array( 'hide_empty' => $hide_category_empty, 'number' => $limit_category );
	    $terms_categories = iwj_get_term_hierarchy( 'iwj_cat', 0, 0, $args_cat );

        $html = '';
        ob_start();
        iwj_get_template_part('parts/advanced_search_radius', array('atts' => $atts, 'terms_categories' => $terms_categories));
        $html = ob_get_contents();
        ob_clean();

        return $html;
    }

    static function advanced_search($atts)
    {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_advanced_search', $atts ) : $atts;

        extract(shortcode_atts(array(
	        'style'               => '',
	        "limit_keyword"       => "10",
	        "hide_location_empty" => "1",
	        "limit_location"      => "",
	        "hide_category_empty" => "1",
	        "limit_category"      => "",
	        "bg_opacity"          => "0.38",
	        'class'               => ''
        ), $atts, 'iwj_advanced_search'));
        ob_start();

        wp_enqueue_script( 'range-slide-js' );
        wp_enqueue_style( 'range-slide-css' );
      //  wp_enqueue_script( 'google-maps' );

        wp_enqueue_script('iwj-search-advanced',IWJ_PLUGIN_URL.'/assets/js/search-advanced.js',array('jquery'),false,true);
        wp_enqueue_style( 'search-map-css', IWJ_PLUGIN_URL.'/assets/css/search-map.css', array('iwjmb-select2' ), false);

        wp_localize_script( 'iwj-search-advanced', 'iwj_search_advanced', array(
            'show_advance_text' => __('Show advanced search', 'iwjob'),
            'hide_advance_text' => __('Hide advanced search', 'iwjob'),
        ));

        if (!$atts['limit_keyword']) {
            $atts['limit_keyword'] = 0;
        }

        $find_jobs_style = iwj_option('limit_keyword_job');
        if (!$find_jobs_style) {
            $find_jobs_style = 0;
        }

        $key_words = get_terms( array(
            'taxonomy' => 'iwj_keyword',
            'number' => ((is_tax(iwj_get_job_taxonomies()) || is_page(iwj_get_page_id('jobs'))) ? $find_jobs_style : $atts['limit_keyword']),
            'meta_key' => IWJ_PREFIX.'searched',
            'orderby' => 'meta_value_num',
            'order' => 'desc',
            'hide_empty' => false,

        ) );

	    $hide_category_empty = isset( $hide_category_empty ) ? $hide_category_empty : 1;
	    $limit_category = isset( $limit_category ) ? $limit_category : '';
	    $hide_location_empty = isset( $hide_location_empty ) ? $hide_location_empty : 1;
	    $limit_location = isset( $limit_location ) ? $limit_location : '';
	    $args_cat = array( 'hide_empty' => $hide_category_empty, 'number' => $limit_category );
	    $args_location = array( 'hide_empty' => $hide_location_empty, 'number' => $limit_location );
	    $terms_categories = iwj_get_term_hierarchy( 'iwj_cat', 0, 0, $args_cat );
	    $terms_locations = iwj_get_term_hierarchy('iwj_location', 0, 0, $args_location );

        $html = '';

        iwj_get_template_part('parts/advanced_search', array('atts' => $atts, 'key_words' => $key_words, 'bg_opacity' => $bg_opacity, 'style' => $style, 'terms_categories' => $terms_categories, 'terms_locations' => $terms_locations));
        $html = ob_get_contents();
        ob_clean();

        return $html;
    }

    static function advanced_search_white($atts)
    {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_advanced_search_white', $atts ) : $atts;

        $atts = shortcode_atts(array(
            'class'             => ''
        ), $atts, 'iwj_advanced_search_white');

        wp_enqueue_script( 'range-slide-js' );
        wp_enqueue_style( 'range-slide-css' );
        wp_enqueue_script( 'google-maps' );

        wp_enqueue_script('iwj-search-advanced',IWJ_PLUGIN_URL.'/assets/js/search-advanced.js',array('jquery'),false,true);
        wp_enqueue_style( 'search-map-css', IWJ_PLUGIN_URL.'/assets/css/search-map.css', array('iwjmb-select2' ), false);

        wp_localize_script( 'iwj-search-advanced', 'iwj_search_advanced', array(
            'show_advance_text' => __('Show advanced search', 'iwjob'),
            'hide_advance_text' => __('Hide advanced search', 'iwjob'),
        ));

        $html = '';
        ob_start();
        iwj_get_template_part('parts/advanced_search_white', array('atts' => $atts));
        $html = ob_get_contents();
        ob_clean();

        return $html;
    }

    static function search_map($atts)
    {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_search_map', $atts ) : $atts;

        $atts = shortcode_atts(array(
            'map_info_title'    => '',
            'latitude' => iwj_option('map_latitude'),
            'longitude' => iwj_option('map_longitude'),
            'marker_icon'       => '',
            'marker_icon_hover' => '',
            'zoom'              => iwj_option('map_zoom'),
            'unit'              => 'Km',
            'min_radius'        => '15',
            'max_radius'        => '100',
            'default_radius'    => '40',
            'class'             => ''
        ), $atts, 'iwj_search_map');

        if(!$atts['latitude']){
            $atts['latitude'] = 40.6700;
        }
        if(!$atts['longitude']){
            $atts['longitude'] = -73.9400;
        }
        if(!$atts['zoom']){
            $atts['zoom'] = 11;
        }

        wp_enqueue_script( 'google-maps' );
        // wp_enqueue_script( 'infobox' );
        wp_enqueue_script( 'markerclusterer' );
        wp_enqueue_script( 'iw-spin' );
        wp_enqueue_script( 'infobubble' );
        wp_enqueue_script( 'range-slide-js' );
        wp_enqueue_style( 'range-slide-css' );

        wp_enqueue_script( 'search-map-js', IWJ_PLUGIN_URL.'/assets/js/search-map.js', array('jquery'), false, true);
        wp_enqueue_style( 'search-map-css', IWJ_PLUGIN_URL.'/assets/css/search-map.css', array('iwjmb-select2' ), false);

        $icon_url = '';
        if( $atts['marker_icon'] ) {
            $img = wp_get_attachment_image_src( $atts['marker_icon'], 'large' );
            $icon_url = count( $img ) ? $img[0] : '';
        }
        $icon_url_hover = '';
        if( $atts['marker_icon_hover'] ) {
            $img = wp_get_attachment_image_src( $atts['marker_icon_hover'], 'large' );
            $icon_url_hover = count( $img ) ? $img[0] : '';
        }

        wp_localize_script( 'search-map-js', 'iwj_search_map', array(
            'map_styles'    => Inwave_Helper::getThemeOption( 'map_styles' ) ? stripslashes(Inwave_Helper::getThemeOption( 'map_styles' )) : '',
            'ajax_nonce'    => wp_create_nonce('get_map_data'),
            'lat'           => (float)$atts['latitude'],
            'lng'           => (float)$atts['longitude'],
            'zoom'          => $atts['zoom'],
            'marker_icon'   => $icon_url,
            'marker_icon_hover'   => $icon_url_hover,
            'close_icon' => IWJ_PLUGIN_URL.'/assets/img/close.png',
            'show_advance_text' => __('Show advanced search', 'iwjob'),
            'hide_advance_text' => __('Hide advanced search', 'iwjob'),
            'min_radius' => $atts['min_radius'],
            'max_radius' => $atts['max_radius'],
            'default_radius' => $atts['default_radius'],
        ));

        $html = '';
        ob_start();
        iwj_get_template_part('parts/search-map', array('atts' => $atts));
        $html = ob_get_contents();
        ob_clean();

        return $html;
    }

    static function map_find_job($atts)
    {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'inwave_map_find_job', $atts ) : $atts;

        $atts = shortcode_atts(array(
            'marker_icon' => '',
            'auto_center' => 'yes',
            'latitude' => iwj_option('map_latitude'),
            'longitude' => iwj_option('map_longitude'),
            'height' => '',
            'zoom' => iwj_option('map_zoom'),
            'limit_keyword' => '6',
            'limit_find_job' => '-1',
            'style' => '',
            'class' => ''

        ), $atts, 'inwave_map_find_job');

        if(!$atts['latitude']){
            $atts['latitude'] = 40.6700;
        }
        if(!$atts['longitude']){
            $atts['longitude'] = -73.9400;
        }
        if(!$atts['zoom']){
            $atts['zoom'] = 11;
        }

        $icon_url = $marker_icon = $latitude = $longitude = $height = $zoom = $limit_find_job = $limit_keyword = $class = $style = '';

        extract($atts);

        wp_enqueue_script('google-maps');
        wp_enqueue_script('infobubble');
        wp_enqueue_script('markerclusterer');
        wp_enqueue_script('iw-spin');

        wp_enqueue_script('iw-findjob-map', IWJ_PLUGIN_URL.'/assets/js/iw-findjob-map.js', array('jquery'), false, true);

        if($marker_icon != ''){
            $img = wp_get_attachment_image_src($marker_icon, 'large');
            $icon_url = count($img) ? $img[0] : '';
        }

        $path_image_google =  IWJ_PLUGIN_URL.'/assets/images/m/';

        $show_company = iwj_option('show_company_job');
        $show_salary = iwj_option('show_salary_job');
        $show_location = iwj_option('show_location_job');

        $filters = IWJ_Job_Listing::get_data_filters();
        $filters['jobs_per_page'] = $limit_find_job;
        $query = IWJ_Job_Listing::get_query_jobs($filters);
        $array_data = array();
        $check_login = false;
        if(is_user_logged_in()){
            $check_login = '1';
        }
        if ($query->have_posts()) :
            $i = 0;
            $user = IWJ_User::get_user();
            while ($query->have_posts()) :
                $query->the_post();
                $job = IWJ_Job::get_job(get_the_ID());
                $maps = $job->get_map();
                $types = $job->get_type();
                $author = $job->get_author();
	            $lat = $maps[0] * iwj_random_number( 0.999999, 1.000001 );
	            $lng = $maps[1] * iwj_random_number( 0.999999, 1.000001 );
                $array_data[$i]['lat'] = $lat;
                $array_data[$i]['lng'] = $lng;
                $array_data[$i]['id'] = $job->get_id();
                $array_data[$i]['link'] = $job->permalink ();
                $array_data[$i]['title'] = $job->get_title();
                if (($job->get_salary()) && ($show_salary == '1')) {
                    $array_data[$i]['salary'] = $job->get_salary();
                }else{
                    $array_data[$i]['salary'] = '';
                }
                if (($job->get_locations_links()) && ($show_location == '1')){
                    $array_data[$i]['location'] = $job->get_locations_links();
                }else{
                    $array_data[$i]['location'] = '';
                }
                if ($author && ($show_company == '1')){
                    $array_data[$i]['company_name'] = $author->get_display_name();
                    $array_data[$i]['company_link'] = $author->permalink();
                }else{
                    $array_data[$i]['company_name'] = '';
                    $array_data[$i]['company_link'] = '';
                }
                $array_data[$i]['check_login'] = $check_login;
                if($types){
                    $array_data[$i]['type'] = $types->name;
                    $array_data[$i]['link_type'] = get_term_link($types->term_id, 'iwj_type');
                    $array_data[$i]['color'] = get_term_meta($types->term_id, IWJ_PREFIX.'color', true);
                }else{
                    $array_data[$i]['type'] = '';
                    $array_data[$i]['link_type'] = '';
                    $array_data[$i]['color'] = '';
                }
                if($check_login == 1){
                    $array_data[$i]['savejobclass'] = $user->is_saved_job($job->get_id()) ? 'saved' : '';
                }else{
                    $array_data[$i]['savejobclass'] = '';
                }
                $i ++;
            endwhile;
            wp_reset_postdata();
        endif;
        wp_localize_script('iw-findjob-map', 'iwj_findjob_map', array(
            'map_styles' => Inwave_Helper::getThemeOption('map_styles') ? stripslashes(Inwave_Helper::getThemeOption('map_styles')) : '',
            'close_icon' => IWJ_PLUGIN_URL.'/assets/img/close.png',
            'marker_icon' => $icon_url,
            'auto_center' => $auto_center,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'zoom' => $zoom,
            'path_image_google' => $path_image_google,
            'js_array_map' => $array_data,
        ));

        if($height != ''){
            if(is_numeric($height)){
                $height = 'style="height:'.esc_attr($height).'px"';
            }
            else
            {
                $height = 'style="height:'.esc_attr($height).'"';
            }
        }
        $module = 'map_find_job';

        $find_jobs_style = iwj_option('limit_keyword_job');
        if (!$find_jobs_style) {
            $find_jobs_style = 0;
        }

        $terms_categories = get_terms( array(
            'taxonomy' => 'iwj_cat',
            'hide_empty' => true,
        ) );

        $categories = array();
        if($terms_categories){
            foreach ($terms_categories as $terms_categorie){
                $count = iwj_count_job_with_term($terms_categorie->term_id, 'iwj_cat');
                if($count > 0){
                    $terms_categorie->count = $count;
                    $categories[] = $terms_categorie;
                }
            }
        }
        $terms_locations = iwj_get_term_hierarchy('iwj_location');

        $key_words = get_terms( array(
            'taxonomy' => 'iwj_keyword',
            'number' => ((is_tax(iwj_get_job_taxonomies()) || is_page(iwj_get_page_id('jobs'))) ? $find_jobs_style : $limit_keyword),
            'meta_key' => IWJ_PREFIX.'searched',
            'orderby' => 'meta_value_num',
            'order' => 'desc',
            'hide_empty' => false,

        ) );

        ob_start();
        echo '<div class="iw-map-find-jobs">';
        $output = '';
        $output .= '<div id="spin-map" class="inwave-map-contact" '.$height.'>';
        $output .= '<div class="inwave-map">';

        $output .= '<div class="map-contain-find-job" >';
        $output .= '<div class="map-view map-frame" '.$height.'></div>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';

        echo  $output;
        echo '<div id="iw-form-map-find-jobs">';
        echo '<div class="container">';
        iwj_get_template_part('parts/find-jobs-map', array('categories' => $categories, 'terms_locations' => $terms_locations, 'hierarchy' => array(), 'key_words' => $key_words, 'style' => $style, 'module' => $module));
        echo '</div>';
        echo '</div>';
        echo '</div>';
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    static function candidate_with_map($atts)
    {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_candidate_with_map', $atts ) : $atts;

        $atts = shortcode_atts(array(
            'marker_icon' => '',
            'auto_center' => 'yes',
            'latitude' => iwj_option('map_latitude'),
            'longitude' => iwj_option('map_longitude'),
            'height' => '',
            'zoom' => iwj_option('map_zoom'),
            'limit_keyword' => '6',
            'limit_find_job' => '-1',
            'class' => ''

        ), $atts, 'iwj_candidate_with_map');

        if(!$atts['latitude']){
            $atts['latitude'] = 40.6700;
        }
        if(!$atts['longitude']){
            $atts['longitude'] = -73.9400;
        }
        if(!$atts['zoom']){
            $atts['zoom'] = 11;
        }

        $icon_url = $marker_icon = $latitude = $longitude = $height = $zoom = $limit_find_job = $limit_keyword = $class = '';

        extract($atts);

        wp_enqueue_script('google-maps');
        wp_enqueue_script('infobubble');
        wp_enqueue_script('markerclusterer');
        wp_enqueue_script('iw-spin');

        wp_enqueue_script('iw-candidate-map', IWJ_PLUGIN_URL.'/assets/js/iw-candidate-map.js', array('jquery'), false, true);

        if($marker_icon != ''){
            $img = wp_get_attachment_image_src($marker_icon, 'large');
            $atts['icon_url'] = count($img) ? $img[0] : '';
        }

        $atts['path_image_google'] =  IWJ_PLUGIN_URL.'/assets/images/m/';

        $filters = IWJ_Candidate_Listing::get_data_filters();
        $query = IWJ_Candidate_Listing::get_query_candidates($filters);
        
        ob_start();
        iwj_get_template_part('parts/candidates-with-map', array('query' => $query, 'atts' => $atts));
        $html = ob_get_contents();
        ob_clean();

        return $html;
    }

    static function jobs_indeed($atts){
	    $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_jobs_indeed', $atts ) : $atts;
	    $atts = shortcode_atts( array(
		    "class"            => "",
		    "style"            => "",
		    "ide_publisher_id" => "",
		    "show_filter_bar"  => "",
		    "show_load_more"  => "",
		    "ide_query"        => "",
		    "ide_location"     => "",
		    "ide_job_type"     => "",
		    "ide_country"      => "",
		    "ide_from_item"    => "",
		    "ide_max_item"     => "",
		    "ide_logo_company" => "",
	    ), $atts, 'iwj_jobs_indeed' );

	    extract($atts);
	    ob_start();

	    $jobs = iwj_get_jobs_indeed($ide_publisher_id, $ide_query, $ide_location, $ide_job_type, $ide_country, $ide_from_item, $ide_max_item);

	    iwj_get_template_part('parts/jobs-indeed', array('jobs' => $jobs, 'atts' => $atts));

	    $html = ob_get_contents();
	    ob_end_clean();
	    return $html;
    }

    static function advanced_search_candidates($atts)
    {
        $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'iwj_advanced_search_candidates', $atts ) : $atts;

        $atts = shortcode_atts(array(
            'class'             => ''
        ), $atts, 'iwj_advanced_search_candidates');

        wp_enqueue_script( 'range-slide-js' );
        wp_enqueue_style( 'range-slide-css' );

        wp_enqueue_script('iwj-search-advanced',IWJ_PLUGIN_URL.'/assets/js/search-advanced.js',array('jquery'),false,true);
        wp_enqueue_style( 'search-map-css', IWJ_PLUGIN_URL.'/assets/css/search-map.css', array('iwjmb-select2' ), false);

        wp_localize_script( 'iwj-search-advanced', 'iwj_search_advanced', array(
            'show_advance_text' => __('Show advanced search', 'iwjob'),
            'hide_advance_text' => __('Hide advanced search', 'iwjob'),
        ));

        $html = '';
        ob_start();
        iwj_get_template_part('parts/advanced_search_candidates', array('atts' => $atts));
        $html = ob_get_contents();
        ob_clean();

        return $html;
    }
}

IWJ_Shortcodes::init();
