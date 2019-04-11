<?php

class IWJ_Job {

    static $cache = array();
    public $post;

    public function __construct($post) {
        $this->post = $post;
    }

    /**
     * @param null $post
     * @param bool $force
     * @return IWJ_Job|null
     */
    static function get_job($post = null, $force = false) {
        $post_id = 0;
        if ($post === null) {
            $post = get_post();
        }

        if (is_numeric($post)) {
            $post = get_post($post);
            if ($post && !is_wp_error($post)) {
                $post_id = $post->ID;
            }
        } elseif (is_object($post)) {
            $post_id = $post->ID;
        }

        if ($post_id) {

            if ($force) {
                clean_post_cache($post_id);
                $post = get_post($post_id);
            }

            if ($force || !isset(self::$cache[$post_id])) {
                self::$cache[$post_id] = new IWJ_Job($post);
            }

            return self::$cache[$post_id];
        }

        return null;
    }

    public function get_id() {
        return $this->post->ID;
    }

    public function get_status($calculate = true) {
        $status = $this->post->post_status;
        if ($calculate && $status == 'publish' && $this->get_expiry() != '' && $this->get_expiry() <= current_time('timestamp')) {
            return 'iwj-expired';
        } else {
            return $status;
        }
    }

    public function has_status($check_status) {
        $check_status = !is_array($check_status) ? (array) $check_status : $check_status;
        $status = $this->get_status();
        $status = str_replace('iwj-', '', $status);
        if (in_array($status, $check_status)) {
            return true;
        }

        return false;
    }

    public function get_parent_id() {
        return $this->post->post_parent;
    }

    public function get_parent() {
        return IWJ_Job::get_job($this->get_parent_id());
    }

    public function get_update() {
        $args = array(
            'post_parent' => $this->get_id(),
            'post_type' => 'iwj_job',
            'numberposts' => 1,
            'post_status' => array('iwj-rejected', 'pending')
        );

        $children = get_children($args);
        if ($children) {
            $post = reset($children);
            return IWJ_Job::get_job($post);
        }

        return null;
    }

    public function get_title($original = false) {
        if ($original) {
            return $this->post->post_title;
        }

        return get_the_title($this->post->ID);
    }

    function get_views() {
        $views = get_post_meta($this->get_id(), IWJ_PREFIX . 'views', true);

        return apply_filters('iwj_job_views', $views, $this->get_id());
    }

    public function get_created() {
        $date = $this->post->post_date;
        return $date;
    }

    public function get_description($filter = false) {
        $content = $this->post->post_content;

        if ($filter) {

            $content = strip_shortcodes($content);
            $content = apply_filters('the_content', $content);
        }

        return $content;
    }

    function get_address() {

        $address = get_post_meta($this->get_id(), IWJ_PREFIX . 'address', true);

        return apply_filters('iwj_job_views', $address, $this->get_id());
    }

    function get_locations() {
        $args = array('orderby' => 'ID');
        $locations = wp_get_post_terms($this->get_id(), 'iwj_location', $args);
		$locs=array();
		$end_location = null;
		foreach($locations as $loc){
			if(!get_term_children( $loc->term_id, 'iwj_location')){
				$end_location = $loc;
				$locs[] = $loc;
				break;
			}
		}
		for($i=0; 1>0; $i++){
			$location = end($locs);
			if($location && $location->parent){
				$locs[] = get_term_by('id', $location->parent, 'iwj_location');
			}else{
				break;
			}
		}

        return $locs;
    }

    function get_locations_links() {
        $location_links = array();
        $locations = $this->get_locations();
        if ($locations) {
            foreach ($locations as $location) {
                $location_links[] = '<span itemprop="jobLocation" itemscope itemtype="http://schema.org/Place">
                    <span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                        <span itemprop="addressLocality">' . $location->name . '</span>
                        <span itemprop="addressRegion" style="display: none;">' . $location->name . '</span>
                        <span itemprop="postalCode" style="display: none;">' . $location->name . '</span>
                        <span itemprop="streetAddress" style="display: none;">' . $location->name . '</span>
                    </span>
                </span>';
            }
        }

        if ($location_links) {
            return implode(', ', $location_links);
        } else {
            return '';
        }
    }

    function get_more_details() {
        $_more_details = get_post_meta($this->get_id(), IWJ_PREFIX . 'more_details', true);
        $more_details = array();
        if ($_more_details) {

            foreach ($_more_details as $detail) {
                $empty = array_filter($detail);
                if (!empty($empty)) {
                    $more_details[] = $detail;
                }
            }
        }

        return $more_details;
    }

    function get_map() {
        $map = get_post_meta($this->get_id(), IWJ_PREFIX . 'map', true);
        if ($map) {
            return explode(",", $map);
        }

        return null;
    }

    function get_thumbnail($size_2x = false) {
        $width = 46;
        $height = 46;
        if ($size_2x) {
            $width = $width * 2;
            $height = $height * 2;
        }
        $images = wp_get_attachment_image_src($this->get_id(), 'full');
        if (empty($images[0])) {
            $thumnail_url = iwj_get_placeholder_image(array($width, $height));
        } else {
            $thumnail_url = iwj_resize_image($images[0], $width, $height, true);
        }

        return $thumnail_url;
    }

    function get_full_address($link = false) {
        $address = array();
        if ($this->get_address()) {
            $address[] = $this->get_address();
        }

        $locations = $this->get_locations();
        if ($locations) {
            $locations = array_reverse($locations);
            foreach ($locations as $location) {
                $link = get_term_link($location, 'location');
                if ($link) {
                    $address[] = '<a class="theme-color-hover" href="' . $link . '">' . $location->name . '</a>';
                } else {
                    $address[] = $location->name;
                }
            }
        }

        if ($address) {
            return implode(', ', $address);
        } else {
            return '';
        }
    }

    function get_reason() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'reason', true);
    }

    function get_salary() {
        $salary_from = $this->get_salary_from();
        $salary_to = $this->get_salary_to();
        if ($salary_from === '' && $salary_to === '') {
            $salary = __('negotiable', 'iwjob');
        } elseif ($salary_from === '') {
            $salary = iwj_price($salary_to, $this->get_currency());
        } elseif ($salary_to === '') {
            $salary = iwj_price($salary_from, $this->get_currency());
        } elseif ($salary_from == $salary_to) {
            $salary = iwj_price($salary_from, $this->get_currency());
        } else {
            $salary = iwj_price($salary_from, $this->get_currency()) . ' - ' . iwj_price($salary_to, $this->get_currency());
        }

        return apply_filters('iwj_job_salary', $salary, $this->get_id());
    }

    function get_salary_from() {
        $salary_from = get_post_meta($this->get_id(), IWJ_PREFIX . 'salary_from', true);

        return apply_filters('iwj_job_salary_from', $salary_from, $this->get_id());
    }

    function get_salary_to() {
        $salary_to = get_post_meta($this->get_id(), IWJ_PREFIX . 'salary_to', true);

        return apply_filters('iwj_job_salary_to', $salary_to, $this->get_id());
    }

    function get_salary_postfix() {
        $postfix = get_post_meta($this->get_id(), IWJ_PREFIX . 'salary_postfix', true);

        return apply_filters('iwj_job_slary_postfix', $postfix, $this->get_id());
    }

    function get_currency() {
        $currency = get_post_meta($this->get_id(), IWJ_PREFIX . 'currency', true);

        return apply_filters('iwj_job_currency', $currency, $this->get_id());
    }

    function get_deadline() {
        $deadline = get_post_meta($this->get_id(), IWJ_PREFIX . 'deadline', true);
        $expiry = $this->get_expiry();
        if (!$deadline) {
            $deadline = $expiry;
        } elseif ($expiry && $deadline > $expiry) {
            $deadline = $expiry;
        }

        return apply_filters('iwj_job_deadline', $deadline, $this->get_id());
    }

    function get_email_for_apply() {
        $employer = $this->get_author();
        $email_employer = $employer->get_email();
        $emails = get_post_meta($this->get_id(), IWJ_PREFIX . 'email_application', true);
        if ($emails) {
            $emails_array = array_map('trim', explode(',', $emails));
            return $emails_array;
        } else {
            return $email_employer;
        }
    }

    function get_languages() {
        if (iwj_option('disable_language')) {
            return null;
        }
        $languages = get_post_meta($this->get_id(), IWJ_PREFIX . 'job_languages');
        if ($languages) {
            return array_filter($languages);
        }

        return null;
    }

    function get_genders() {
        if (iwj_option('disable_gender')) {
            return null;
        }
        $genders = get_post_meta($this->get_id(), IWJ_PREFIX . 'job_gender');
        if ($genders) {
            return array_filter($genders);
        }

        return null;
    }

    function get_type() {
        if (iwj_option('disable_type')) {
            return null;
        }

        $types = wp_get_post_terms($this->get_id(), 'iwj_type');
        if ($types && !is_wp_error($types)) {
            return $types[0];
        }

        return null;
    }

    function get_types() {
        if (iwj_option('disable_type')) {
            return null;
        }

        $types = wp_get_post_terms($this->get_id(), 'iwj_type');
        if ($types && !is_wp_error($types)) {
            return $types;
        }

        return null;
    }

    function get_custom_apply_url() {
        if (!iwj_option('custom_apply_url')) {
            return null;
        }
        $custom_apply_url = get_post_meta($this->get_id(), IWJ_PREFIX . 'custom_apply_url');
        if ($custom_apply_url) {
            return $custom_apply_url[0];
        }

        return null;
    }

    function get_template_detail_version() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'template_version', true);
    }

    function get_source_import() {
        $import_source = get_post_meta($this->get_id(), IWJ_PREFIX . 'import_source', true);
        if ($import_source) {
            return $import_source;
        }

        return null;
    }

    function get_indeed_url() {
        if ($this->get_source_import() && $this->get_source_import() == 'indeed') {
            $indeed_url = get_post_meta($this->get_id(), IWJ_PREFIX . 'import_url', true);
            if ($indeed_url) {
                return $indeed_url;
            }

            return null;
        }

        return null;
    }

    function get_indeed_company_name() {
        if ($this->get_source_import() && $this->get_source_import() == 'indeed') {
            $indeed_company = get_post_meta($this->get_id(), IWJ_PREFIX . 'import_company', true);
            if ($indeed_company) {
                return $indeed_company;
            }

            return null;
        }

        return null;
    }

    function get_skills() {
        if (iwj_option('disable_skill')) {
            return null;
        }

        $skills = wp_get_post_terms($this->get_id(), 'iwj_skill');
        if ($skills && !is_wp_error($skills)) {
            return $skills[0];
        }

        return null;
    }

    function get_all_skills() {
        if (iwj_option('disable_skill')) {
            return null;
        }

        $skills = wp_get_post_terms($this->get_id(), 'iwj_skill');
        if ($skills && !is_wp_error($skills)) {
            return $skills;
        }

        return null;
    }

    function get_levels() {
        if (iwj_option('disable_level')) {
            return null;
        }

        $levels = wp_get_post_terms($this->get_id(), 'iwj_level');
        if ($levels && !is_wp_error($levels)) {
            return $levels[0];
        }

        return null;
    }

    function get_all_levels() {
        if (iwj_option('disable_level')) {
            return null;
        }

        $levels = wp_get_post_terms($this->get_id(), 'iwj_level');
        if ($levels && !is_wp_error($levels)) {
            return $levels;
        }

        return null;
    }

    function get_category() {
        $cats = wp_get_post_terms($this->get_id(), 'iwj_cat');
        if ($cats) {
            return $cats[0];
        }

        return null;
    }

    function get_categories() {
        $cats = wp_get_post_terms($this->get_id(), 'iwj_cat');
        return $cats;
    }

    function get_categories_links() {
        $category_links = array();
        $categories = $this->get_categories();
        if ($categories) {
            foreach ($categories as $category) {
                $category_links[] = '<a href="' . get_term_link($category->term_id, 'iwj_cat') . '">' . $category->name . '</a>';
            }
        }

        if ($category_links) {
            return implode(', ', $category_links);
        } else {
            return '';
        }
    }

    function get_related($args = array()) {

        $limit_item_related = iwj_option('limit_item_related');
        $id = $this->get_id();
        $categories = $this->get_categories();

        $default_args = array(
            'posts_per_page' => $limit_item_related,
            'post_type' => 'iwj_job',
            'post_status' => 'publish',
            'post__not_in' => array($id),
            'post_author' => $this->get_author_id(),
        );
        if ($categories) {
            $default_args['tax_query'] = array('relation' => 'OR');
            foreach ($categories as $category) {
                $default_args['tax_query'][] = array(
                    'taxonomy' => 'iwj_cat',
                    'field' => 'term_id',
                    'terms' => $category->term_id
                );
            }
        }

        if (!iwj_option('show_expired_job')) {
            $default_args['meta_query'] = array('relation' => 'OR');
            $default_args['meta_query'][] = array(
                'key' => IWJ_PREFIX . 'expiry',
                'value' => current_time('timestamp'),
                'compare' => '>',
            );
            $default_args['meta_query'][] = array(
                'key' => IWJ_PREFIX . 'expiry',
                'value' => '',
                'compare' => '=',
            );
        }

        $args = wp_parse_args($args, $default_args);
        $jobs = get_posts($args);
        if ($jobs) {
            foreach ($jobs as $key => $job) {
                $jobs[$key] = IWJ_Job::get_job($job);
            }
        }

        return $jobs;
    }

    function get_jobs_by_author($args = array()) {
        $id = $this->get_id();

        $default_args = array(
            'posts_per_page' => -1,
            'post_type' => 'iwj_job',
            'post_status' => 'publish',
            'post__not_in' => array($id),
            'post_author' => $this->get_author_id(),
        );

        if (!iwj_option('show_expired_job')) {
            $default_args['meta_query'] = array('relation' => 'OR');
            $default_args['meta_query'][] = array(
                'key' => IWJ_PREFIX . 'expiry',
                'value' => current_time('timestamp'),
                'compare' => '>',
            );
            $default_args['meta_query'][] = array(
                'key' => IWJ_PREFIX . 'expiry',
                'value' => '',
                'compare' => '=',
            );
        }

        $args = wp_parse_args($args, $default_args);
        $jobs = get_posts($args);
        if ($jobs) {
            foreach ($jobs as $key => $job) {
                $jobs[$key] = IWJ_Job::get_job($job);
            }
        }

        return $jobs;
    }

    public function get_user_package_id() {

        return get_post_meta($this->get_id(), IWJ_PREFIX . 'user_package_id', true);
    }

    /**
     * @return IWJ_User_Package|null
     */
    public function get_user_package() {
        return IWJ_User_Package::get_user_package($this->get_user_package_id());
    }

    public function get_plan_id() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'plan_id', true);
    }

    public function get_plan() {
        $plan_id = $this->get_plan_id();
        if ($plan_id) {
            return IWJ_Plan::get_package($plan_id);
        } else {
            return null;
        }
    }

    public function get_author_id() {

        return $this->post->post_author;
    }

    public function get_author() {
        if ($this->get_author_id()) {
            return IWJ_User::get_user($this->get_author_id());
        }

        return null;
    }

    public function get_employer($status = '') {
        $author = $this->get_author();
        return $author->get_employer(false, $status);
    }

    public function get_expiry() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'expiry', true);
    }

    public function applications_permalink() {
        $dashboard = iwj_get_page_permalink('dashboard');
        return add_query_arg(array('iwj_tab' => 'applications', 'job-id' => $this->get_id()), $dashboard);
    }

    public function count_applications() {
        global $wpdb;
        $sql = "SELECT COUNT(1) FROM {$wpdb->posts} AS p JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id
                  WHERE p.post_type = %s AND pm.meta_key = %s AND pm.meta_value = %s";
        $sql = $wpdb->prepare($sql, 'iwj_application', IWJ_PREFIX . 'job_id', $this->get_id());

        return $wpdb->get_var($sql);
    }

    public function get_application_ids($limit = 0) {
        global $wpdb;
        $sql = "SELECT ID FROM {$wpdb->posts} AS p JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id
                  WHERE p.post_type = %s AND pm.meta_key = %s AND pm.meta_value = %s";
        if ($limit) {
            $sql .= ' LIMIT 0,%d';
            $sql = $wpdb->prepare($sql, 'iwj_application', IWJ_PREFIX . 'job_id', $this->get_id(), $limit);
        } else {
            $sql = $wpdb->prepare($sql, 'iwj_application', IWJ_PREFIX . 'job_id', $this->get_id());
        }

        $applications = $wpdb->get_results($sql);
        $ids = array();
        if ($applications) {
            foreach ($applications as $application) {
                $ids[] = $application->ID;
            }
        }

        return $ids;
    }

    public function get_featured_expiry() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'featured_expiry', true);
    }

    public function renew_link() {
        $url = add_query_arg(array('iwj_renew_job' => $this->get_id()), iwj_get_page_permalink('dashboard'));
        return $url;
    }

    public function edit_link() {
        $dashboard_url = iwj_get_page_permalink('dashboard');
        $url = add_query_arg(array('iwj_tab' => 'edit-job', 'job-id' => $this->get_id()), $dashboard_url);
        return $url;
    }

    public function edit_draft_link() {
        $dashboard_url = iwj_get_page_permalink('dashboard');
        $url = add_query_arg(array('iwj_tab' => 'new-job', 'job-id' => $this->get_id()), $dashboard_url);
        return $url;
    }

    public function publish_draft_link() {
        $dashboard_url = iwj_get_page_permalink('dashboard');
        $url = add_query_arg(array('iwj_publish_job' => $this->get_id()), $dashboard_url);

        return $url;
    }

    public function unpublish_link() {

        $url = add_query_arg(array('iwj_unpublish_job' => $this->get_id()), home_url('/'));

        return $url;
    }

    public function make_featured_link() {
        $url = add_query_arg(array('iwj_featured_job' => $this->get_id()), home_url('/'));
        return $url;
    }

    public function unfeatured_link() {
        $url = add_query_arg(array('iwj_unfeatured_job' => $this->get_id()), home_url('/'));
        return $url;
    }

    public function has_flag($flag) {
        $_flag = get_post_meta($this->get_id(), IWJ_PREFIX . 'flag', true);
        if ($flag == $_flag) {
            return true;
        }

        return false;
    }

    public function permalink() {
        $link = get_the_permalink($this->get_id());
        if (!in_array($this->get_status(), array('publish'))) {
            $link = add_query_arg('preview', 'true', $link);
        }

        return apply_filters('iwj_job_permalink', $link);
    }

    public function admin_link() {
        return get_admin_url() . 'post.php?post=' . $this->get_id() . '&action=edit';
    }

    public function get_featured() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'featured', true) ? true : false;
    }

    public function is_featured() {
        $featured = $this->get_featured();
        return $featured;
    }

    public function is_free() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'free_job', true) ? true : false;
    }

    public function is_pending_featured() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'is_new_featured', true) ? true : false;
    }

    public function set_featured($force = false, $exipry = null) {
        if ($force || get_post_meta($this->get_id(), IWJ_PREFIX . 'is_new_featured', true)) {
            update_post_meta($this->get_id(), IWJ_PREFIX . 'featured', '1');
            update_post_meta($this->get_id(), IWJ_PREFIX . 'featured_date', current_time('timestamp'));
            if (is_null($exipry) || $exipry) {
                $expiry = iw_get_featured_job_expirty();
                update_post_meta($this->get_id(), IWJ_PREFIX . 'featured_expiry', $expiry);
            }
            delete_post_meta($this->get_id(), IWJ_PREFIX . 'is_new_featured');
        }
    }

    public function unfeatured($exipry = null) {
        update_post_meta($this->get_id(), IWJ_PREFIX . 'featured', '0');
        if ($exipry) {
            update_post_meta($this->get_id(), IWJ_PREFIX . 'featured_expiry', $expiry);
        }
    }

    public function change_status($status, $send_email = true) {
        global $wpdb;
        $post = get_post($this->get_id());
        $old_status = $this->get_status();
        if ($status != 'draft' && $status != 'pending' && !$post->post_name) {
            $post_name = sanitize_title($post->post_title);
            $post_name = wp_unique_post_slug($post_name, $this->get_id(), $status, 'iwj_job', 0);

            $sql = "UPDATE {$wpdb->posts} SET post_status = %s, post_name = %s WHERE ID = %d";
            $wpdb->query($wpdb->prepare($sql, $status, $post_name, $this->get_id()));
        } else {
            $sql = "UPDATE {$wpdb->posts} SET post_status = %s WHERE ID = %d";
            $wpdb->query($wpdb->prepare($sql, $status, $this->get_id()));
        }

        if ($status == 'publish') {
            $this->set_publish();
            $this->set_featured();
        }

        if (($old_status != 'publish' && $status == 'publish' ) || ($old_status == 'publish' && $status != 'publish' )) {
            delete_transient('iwj_count_jobs');
        }

        //clean cache
        $job = IWJ_Job::get_job($this->get_id(), true);

        //send email
        if ($send_email) {
            if ($status == 'publish') {
                IWJ_Email::send_email('approved_job', $job);
            } elseif ($status == 'pending') {
                IWJ_Email::send_email('review_job', $job);
            } elseif ($status == 'iwj-rejected') {
                IWJ_Email::send_email('rejected_job', $job);
            }
        }

        //send to followers
        if ($status == 'publish' && !$this->get_parent_id()) {
            IWJ_Email::send_email('new_job', $job);
        }
    }

    public function set_publish($force = true) {
        if ($force == true || get_post_meta($this->get_id(), IWJ_PREFIX . 'is_new_publish', true)) {
            $user_package = $this->get_user_package();
            $has_package = false;
            if ($user_package) {
                $package = $user_package->get_package();
                if ($package) {
                    $expiry = $package->get_time_expiry();
                    update_post_meta($this->get_id(), IWJ_PREFIX . 'expiry', $expiry);
                    $has_package = true;
                }
            }

            if (!$has_package) {
                $expiry = iw_get_job_expirty();
                update_post_meta($this->get_id(), IWJ_PREFIX . 'expiry', $expiry);
            }

            // global $wpdb;
            // $sql = "UPDATE {$wpdb->posts} SET post_date = %s, post_date_gmt = %s, post_modified = %s, post_modified_gmt = %s WHERE ID = %d";
            // $wpdb->query($wpdb->prepare($sql, date('Y-m-d H:i:s', current_time('timestamp')), date('Y-m-d H:i:s', current_time('timestamp')), '0000-00-00 00:00:00', '0000-00-00 00:00:00', $this->get_id()));

            delete_post_meta($this->get_id(), IWJ_PREFIX . 'is_new_publish');
        }
    }

    public function renew($duration = '') {
        global $wpdb;
        $time_offset = get_option('gmt_offset') * 3600;
        $default_time = iwj_option('job_expiry', 1);
        $default_unit = iwj_option('job_expiry_unit', 'year');
        if ($duration) {
            $default_time = $duration['time'];
            $default_unit = $duration['unit'];
        }
        switch ($default_unit) {
            case 'year':
                $new_expiry_date = strtotime('+' . $default_time . ' years') + $time_offset;
                break;
            case 'month':
                $new_expiry_date = strtotime('+' . $default_time . ' months') + $time_offset;
                break;

            default:
                $new_expiry_date = strtotime('+' . $default_time . ' days') + $time_offset;
                break;
        }
        update_post_meta($this->get_id(), IWJ_PREFIX . 'expiry', $new_expiry_date);
        $wpdb->update($wpdb->posts, array('post_date' => current_time('mysql'), 'post_date_gmt' => current_time('mysql', true), 'post_modified' => current_time('mysql'), 'post_modified_gmt' => current_time('mysql', true)), array('ID' => $this->get_id()));
    }

    public function approve_update() {
        if ($this->get_parent_id()) {

            //delete post meta so can not copy to parent
            delete_post_meta($this->get_id(), IWJ_PREFIX . 'expiry');
            delete_post_meta($this->get_id(), IWJ_PREFIX . 'featured');
            delete_post_meta($this->get_id(), IWJ_PREFIX . 'featured_date');
            delete_post_meta($this->get_id(), IWJ_PREFIX . 'featured_expiry');

            iwj_move_post($this->get_id(), $this->get_parent_id());

            global $wpdb;
            $sql = "UPDATE {$wpdb->posts} SET post_modified = %s, post_modified_gmt = %s WHERE ID = %d";
            $wpdb->query($wpdb->prepare($sql, date('Y-m-d H:i:s', current_time('timestamp')), date('Y-m-d H:i:s', current_time('timestamp')), $this->get_parent_id()));

            wp_delete_post($this->get_id());

            clean_post_cache($this->get_parent_id());
        }
    }

    public function get_order_id() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'order_id', true);
    }

    public function can_edit() {
        if (current_user_can('edit_iwj_job', $this->get_id())) {
            if ($this->has_status(array('rejected', 'pending-payment'))) {
                return true;
            } elseif ($this->has_status('publish')) {
                $job_update = $this->get_update();
                if (!$job_update || $job_update->has_status('rejected')) {
                    return true;
                }
            }
        }

        return false;
    }

    public function can_publish_draft() {
        if (current_user_can('edit_iwj_job', $this->get_id())) {
            if ($this->has_status(array('draft'))) {
                return true;
            }
        }

        return false;
    }

    public function can_unpublish() {
        if (current_user_can('edit_iwj_job', $this->get_id())) {
            if ($this->has_status(array('publish'))) {
                return true;
            }
        }

        return false;
    }

    public function can_apply() {
        if ($this->has_status('publish')) {
            $deadline = $this->get_deadline();
            if (!$deadline || ($deadline && ($deadline + 86400) > current_time('timestamp'))) {
                return true;
            }
        }

        if ($this->has_status('expired')) {
            return 0;
        }

        return false;
    }

    public function can_delete() {
        if (
                current_user_can('delete_iwj_job', $this->get_id()) && $this->has_status(array('publish', 'pending', 'expired', 'reject', 'draft'))
        ) {
            return true;
        }

        return false;
    }

    public function can_renew() {
        if (
                current_user_can('edit_iwj_job', $this->get_id()) && $this->has_status(array('publish', 'expired'))
        ) {
            return true;
        }

        return false;
    }

    public function can_make_featured() {
        if (
                current_user_can('edit_iwj_job', $this->get_id())
        ) {
            if ($this->has_status(array('publish')) && !$this->is_featured() && !$this->is_pending_featured()) {
                return true;
            }
        }

        return false;
    }

    static public function set_salary($job_id, $salary_from, $salary_to) {
        global $wpdb;

        if (!$salary_from && !$salary_to) {
            $sql = "SELECT DISTINCT t.term_id FROM {$wpdb->term_taxonomy} as t JOIN {$wpdb->termmeta} as tm ON t.term_id = tm.term_id 
                                    JOIN {$wpdb->termmeta} as tm1 ON t.term_id = tm1.term_id 
                                    WHERE t.taxonomy = %s AND tm.meta_key = %s AND tm1.meta_key = %s AND tm.meta_value = '' AND tm1.meta_value = ''";
            $sql = $wpdb->prepare($sql, 'iwj_salary', IWJ_PREFIX . 'salary_from', IWJ_PREFIX . 'salary_to');
        } elseif ($salary_from && !$salary_to) {
            $sql = "SELECT DISTINCT t.term_id FROM {$wpdb->term_taxonomy} as t LEFT JOIN {$wpdb->termmeta} as tm ON t.term_id = tm.term_id 
                                    JOIN {$wpdb->termmeta} as tm1 ON t.term_id = tm1.term_id 
                                    WHERE t.taxonomy = %s AND tm.meta_key = %s AND tm1.meta_key = %s 
                                    AND (
                                      (tm.meta_value != '' AND tm1.meta_value != '' AND (
                                        (CAST(tm1.meta_value AS SIGNED) <= %d AND CAST(tm.meta_value AS SIGNED) >= %d) 
                                         OR (CAST(tm.meta_value AS SIGNED) >= %d) AND CAST(tm1.meta_value AS SIGNED) >= %d)
                                        ) 
                                      OR (tm1.meta_value = '' AND tm.meta_value != '' AND CAST(tm.meta_value AS SIGNED) > %d) 
                                      OR (tm.meta_value = '' AND tm1.meta_value != '')
                                     )";
            $sql = $wpdb->prepare($sql, 'iwj_salary', IWJ_PREFIX . 'salary_to', IWJ_PREFIX . 'salary_from', $salary_from, $salary_from, $salary_from, $salary_from, $salary_from, $salary_from);
        } elseif (!$salary_from && $salary_to) {
            $sql = "SELECT DISTINCT t.term_id FROM {$wpdb->term_taxonomy} as t LEFT JOIN {$wpdb->termmeta} as tm ON t.term_id = tm.term_id 
                                    JOIN {$wpdb->termmeta} as tm1 ON t.term_id = tm1.term_id 
                                    WHERE t.taxonomy = %s AND tm.meta_key = %s AND tm1.meta_key = %s 
                                    AND (
                                      (tm.meta_value != '' AND tm1.meta_value != '' AND (
                                        (CAST(tm1.meta_value AS SIGNED) <= %d AND CAST(tm.meta_value AS SIGNED) >= %d) 
                                         OR (CAST(tm.meta_value AS SIGNED) <= %d) AND CAST(tm1.meta_value AS SIGNED) <= %d)
                                      ) 
                                      OR (tm1.meta_value = '' AND tm.meta_value != '') 
                                      OR (tm.meta_value = '' AND tm1.meta_value != '' AND CAST(tm1.meta_value AS SIGNED) <= %d)
                                    )";
            $sql = $wpdb->prepare($sql, 'iwj_salary', IWJ_PREFIX . 'salary_to', IWJ_PREFIX . 'salary_from', $salary_to, $salary_to, $salary_to, $salary_to, $salary_to, $salary_to);
        } else {
            $sql = "SELECT DISTINCT t.term_id FROM {$wpdb->term_taxonomy} as t 
                                    JOIN {$wpdb->termmeta} as tm ON t.term_id = tm.term_id
                                    JOIN {$wpdb->termmeta} as tm1 ON t.term_id = tm1.term_id
                                    WHERE t.taxonomy = %s AND tm.meta_key = %s AND tm1.meta_key = %s
                                    AND (
                                      (tm.meta_value != '' && tm1.meta_value != '' AND CAST(tm.meta_value AS SIGNED) <= %d AND CAST(tm1.meta_value AS SIGNED) >= %d) 
                                      OR (tm.meta_value = '' AND tm1.meta_value != '' AND CAST(tm1.meta_value AS SIGNED) > %d) 
                                      OR (tm1.meta_value = '' AND tm.meta_value != ''  AND CAST(tm.meta_value AS SIGNED) < %d)
                                      )";

            $sql = $wpdb->prepare($sql, 'iwj_salary', IWJ_PREFIX . 'salary_from', IWJ_PREFIX . 'salary_to', $salary_to, $salary_from, $salary_from, $salary_to);
        }
        $terms = $wpdb->get_results($sql);
        $terms_ids = array();
        if ($terms) {
            foreach ($terms as $term) {
                $terms_ids[] = $term->term_id;
            }
            wp_set_post_terms($job_id, $terms_ids, 'iwj_salary');
        } else {
            wp_delete_object_term_relationships($job_id, 'iwj_salary');
        }
    }

    public function update($data) {
        $ori_salary_from = $this->get_salary_from();
        $ori_salary_to = $this->get_salary_to();

        $allow_post_job_multi_cats = iwj_option('allow_post_job_multi_cats', '');
        $max_selected_cats = iwj_option('maximum_number_categories_selected', '');

        if ($allow_post_job_multi_cats && $max_selected_cats > 1) {
            $max_categories = $max_selected_cats;
        } else {
            $user_package = $this->get_user_package();
            $max_categories = $user_package ? $user_package->get_max_categories() : 1;
        }

        $fields = array(
            array(
                'id' => 'job_category',
                'type' => 'taxonomy',
                'options' => array(
                    'taxonomy' => 'iwj_cat'
                ),
                'multiple' => $max_categories == 1 ? false : true,
                'required' => true
            ),
            array(
                'id' => IWJ_PREFIX . 'salary_postfix',
                'type' => 'text',
            ),
            array(
                'id' => IWJ_PREFIX . 'currency',
                'type' => 'select',
                'options' => iwj_get_currencies(),
                'std' => iwj_get_currency()
            ),
            array(
                'id' => IWJ_PREFIX . 'deadline',
                'type' => 'date',
                'required' => true
            ),
            array(
                'id' => IWJ_PREFIX . 'email_application',
                'type' => 'text',
            ),
            array(
                'id' => IWJ_PREFIX . 'address',
                'type' => 'text',
            ),
            array(
                'id' => IWJ_PREFIX . 'map',
                'type' => 'map',
                'address_field' => IWJ_PREFIX . 'address',
            ),
            array(
                'id' => IWJ_PREFIX . 'job_gender',
                'type' => 'select_advanced',
                'options' => iwj_genders(),
                'multiple' => true,
            ),
            array(
                'id' => IWJ_PREFIX . 'job_languages',
                'type' => 'select_advanced',
                'options' => iwj_get_available_languages(),
                'multiple' => true,
            ),
        );

        if (!iwj_option('auto_detect_location')) {
            $fields[] = array(
                'id' => IWJ_PREFIX . 'location',
                'type' => 'taxonomy',
                'options' => array(
                    'type' => 'select_tree',
                    'taxonomy' => 'iwj_location'
                ),
            );
        }

        if (!iwj_option('disable_type')) {
            $fields[] = array(
                'id' => 'job_type',
                'type' => 'taxonomy',
                'options' => array(
                    'taxonomy' => 'iwj_type'
                ),
                'required' => true
            );
        }

        if (!iwj_option('disable_level')) {
            $fields[] = array(
                'id' => 'job_level',
                'type' => 'taxonomy',
                'options' => array(
                    'taxonomy' => 'iwj_level'
                ),
                'required' => true
            );
        }

        if (iwj_option('custom_apply_url')) {
            $fields[] = array(
                'id' => IWJ_PREFIX . 'custom_apply_url',
                'type' => 'text',
            );
        }

        $title = stripslashes(sanitize_text_field($data['title']));
        $description = stripslashes(wp_kses_post($data['description']));

        $post_id = $this->get_id();

        wp_update_post(array(
            'ID' => $post_id,
            'post_title' => $title,
            'post_content' => $description,
            'post_modified' => current_time('mysql'),
            'post_modified_gmt' => get_gmt_from_date(current_time('mysql')),
        ));

        foreach ($fields as $field) {
            $field = IWJMB_Field::call('normalize', $field);

            $single = $field['clone'] || !$field['multiple'];
            $old = IWJMB_Field::call($field, 'raw_post_meta', $post_id);
            $new = isset($data[$field['id']]) ? $data[$field['id']] : ( $single ? '' : array() );

            // Allow field class change the value
            if ($field['clone']) {
                $new = IWJMB_Clone::value($new, $old, $post_id, $field);
            } else {
                $new = IWJMB_Field::call($field, 'value', $new, $old, $post_id);
                $new = IWJMB_Field::call($field, 'sanitize_value', $new);
            }

            /* if($field['id'] == IWJ_PREFIX.'gallery' && $new){
              set_post_thumbnail($post_id, $new[0]);
              } */

            // Call defined method to save meta value, if there's no methods, call common one
            IWJMB_Field::call($field, 'save_post', $new, $old, $post_id);
        }

        if (!iwj_option('disable_skill') && isset($data[IWJ_PREFIX . 'skill'])) {
            $skills = $data[IWJ_PREFIX . 'skill'];
            if ($skills) {
                $skills = explode(', ', $skills);
            }

            if ($skills) {
                $skill_ids = array();

                foreach ($skills as $skill) {

                    $term = get_term_by('name', $skill, 'iwj_skill');

                    if (!$term) {
                        $new_term = wp_insert_term($skill, 'iwj_skill');
                        $skill_ids[] = $new_term['term_id'];
                    } else {
                        $skill_ids[] = $term->term_id;
                    }
                }
                wp_set_post_terms($post_id, $skill_ids, 'iwj_skill');
            } else {
                $current_terms = wp_get_object_terms($post_id, 'iwj_skill', array('fields' => 'ids'));
                wp_remove_object_terms($post_id, $current_terms, 'iwj_skill');
            }
        }

        if (isset($data['job_category']) && $max_categories != 1) {
            // add job post to parent category when selected child categories
            foreach ($data['job_category'] as $term_cat) {
                $term = get_term_by('term_id', $term_cat, 'iwj_cat'); //print_r($term); die;
                while ($term->parent != 0 && !has_term($term->parent, 'iwj_cat', $this->post)) {
                    // move upward until we get to 0 level terms
                    wp_set_post_terms($post_id, array($term->parent), 'iwj_cat', true);
                }
            }
        }

        $salary_from = isset($data[IWJ_PREFIX . 'salary_from']) ? $data[IWJ_PREFIX . 'salary_from'] : '';
        $salary_to = isset($data[IWJ_PREFIX . 'salary_to']) ? $data[IWJ_PREFIX . 'salary_to'] : '';

        if ($salary_to !== '' && $salary_to < $salary_from) {
            $salary_to = $salary_from;
        }

        update_post_meta($post_id, IWJ_PREFIX . 'salary_to', $salary_to);
        update_post_meta($post_id, IWJ_PREFIX . 'salary_from', $salary_from);

        if (isset($data[IWJ_PREFIX . 'salary_from']) && ($salary_from != $ori_salary_from || $salary_to != $ori_salary_to)) {
            self::set_salary($post_id, $salary_from, $salary_to);
        }

        do_action('iwj_update_job', $post_id);

        clean_post_cache($post_id);

        return true;
    }

    static function add_new($args = array()) {

        $title = stripslashes(sanitize_text_field($args['title']));
        //$post_name = sanitize_title($title);
        //$post_name = wp_unique_post_slug( $post_name, 0, 'publish', 'iwj_job', 0 );
        $description = stripslashes(wp_kses_post($args['content']));

        $post_data = array(
            'post_title' => $title,
            'post_content' => $description,
            'post_type' => 'iwj_job',
            'post_status' => isset($args['status']) ? $args['status'] : 'draft',
            'post_author' => isset($args['user_id']) ? $args['user_id'] : get_current_user_id(),
        );

        $post_id = wp_insert_post($post_data);

        $extra_data = isset($args['extra_data']) ? $args['extra_data'] : array();

        $allow_post_job_multi_cats = iwj_option('allow_post_job_multi_cats', '');
        $max_selected_cats = iwj_option('maximum_number_categories_selected', '');

        if ($post_id) {

            if (iwj_option('submit_job_mode') == '3') {
                $user = IWJ_User::get_user();
                update_post_meta($post_id, IWJ_PREFIX . 'plan_id', $user->get_plan_id_for_submition());
            }

            //fields
            $fields = array(
                array(
                    'id' => 'job_category',
                    'type' => 'taxonomy',
                    'options' => array(
                        'taxonomy' => 'iwj_cat'
                    ),
                    'multiple' => ( $allow_post_job_multi_cats && $max_selected_cats > 1 ) ? true : false,
                    'required' => true
                ),
                array(
                    'id' => IWJ_PREFIX . 'salary_postfix',
                    'type' => 'text',
                ),
                array(
                    'id' => IWJ_PREFIX . 'currency',
                    'type' => 'select',
                    'options' => iwj_get_currencies(),
                    'std' => iwj_get_currency()
                ),
                array(
                    'id' => IWJ_PREFIX . 'deadline',
                    'type' => 'date',
                    'required' => true
                ),
                array(
                    'id' => IWJ_PREFIX . 'email_application',
                    'type' => 'text',
                ),
                array(
                    'id' => IWJ_PREFIX . 'address',
                    'type' => 'text',
                ),
                array(
                    'id' => IWJ_PREFIX . 'map',
                    'type' => 'map',
                    'address_field' => IWJ_PREFIX . 'address',
                ),
                array(
                    'id' => IWJ_PREFIX . 'job_gender',
                    'type' => 'select_advanced',
                    'options' => iwj_genders(),
                    'multiple' => true,
                ),
                array(
                    'id' => IWJ_PREFIX . 'job_languages',
                    'type' => 'select_advanced',
                    'options' => iwj_get_available_languages(),
                    'multiple' => true,
                ),
            );

            if (!iwj_option('auto_detect_location')) {
                $fields[] = array(
                    'id' => IWJ_PREFIX . 'location',
                    'type' => 'taxonomy',
                    'options' => array(
                        'type' => 'select_tree',
                        'taxonomy' => 'iwj_location'
                    ),
                );
            }

            if (!iwj_option('disable_type')) {
                $fields[] = array(
                    'id' => 'job_type',
                    'type' => 'taxonomy',
                    'options' => array(
                        'taxonomy' => 'iwj_type'
                    ),
                    'required' => true
                );
            }

            if (!iwj_option('disable_level')) {
                $fields[] = array(
                    'id' => 'job_level',
                    'type' => 'taxonomy',
                    'options' => array(
                        'taxonomy' => 'iwj_level'
                    ),
                    'required' => true
                );
            }

            if (iwj_option('custom_apply_url')) {
                $fields[] = array(
                    'id' => IWJ_PREFIX . 'custom_apply_url',
                    'type' => 'text',
                );
            }


            foreach ($fields as $field) {
                $field = IWJMB_Field::call('normalize', $field);

                $single = $field['clone'] || !$field['multiple'];
                $old = IWJMB_Field::call($field, 'raw_post_meta', $post_id);
                $new = isset($extra_data[$field['id']]) ? $extra_data[$field['id']] : ( $single ? '' : array() );

                // Allow field class change the value
                if ($field['clone']) {
                    $new = IWJMB_Clone::value($new, $old, $post_id, $field);
                } else {
                    $new = IWJMB_Field::call($field, 'value', $new, $old, $post_id);
                    $new = IWJMB_Field::call($field, 'sanitize_value', $new);
                }

                /* if($field['id'] == IWJ_PREFIX.'gallery' && $new){
                  set_post_thumbnail($post_id, $new[0]);
                  } */

                // Call defined method to save meta value, if there's no methods, call common one
                IWJMB_Field::call($field, 'save_post', $new, $old, $post_id);
            }

            if (!iwj_option('disable_skill') && isset($extra_data[IWJ_PREFIX . 'skill'])) {
                $skills = $extra_data[IWJ_PREFIX . 'skill'];
                if ($skills) {
                    $skills = explode(', ', $skills);
                }

                if ($skills) {
                    $skill_ids = array();
                    foreach ($skills as $skill) {
                        $term = get_term_by('name', $skill, 'iwj_skill');
                        if (!$term) {
                            $new_term = wp_insert_term($skill, 'iwj_skill');
                            $skill_ids[] = $new_term['term_id'];
                        } else {
                            $skill_ids[] = $term->term_id;
                        }
                    }

                    wp_set_post_terms($post_id, $skill_ids, 'iwj_skill');
                } else {
                    $current_terms = wp_get_object_terms($post_id, 'iwj_skill', array('fields' => 'ids'));
                    wp_remove_object_terms($post_id, $current_terms, 'iwj_skill');
                }
            }

            if (isset($extra_data['job_category']) && $allow_post_job_multi_cats && $max_selected_cats > 1) {
                $newest_job = IWJ_Job::get_job($post_id);

                // add job post to parent category when selected child categories
                $cat_terms = wp_get_post_terms($post_id, 'iwj_cat');
                foreach ($cat_terms as $term) {
                    if ($term->parent != 0 && !has_term($term->parent, 'iwj_cat', $newest_job)) {
                        // move upward until we get to 0 level terms
                        wp_set_post_terms($post_id, array($term->parent), 'iwj_cat', true);
                    }
                }
            }

            //required meta
            update_post_meta($post_id, IWJ_PREFIX . 'expiry', '');

            update_post_meta($post_id, IWJ_PREFIX . 'featured', '0');
            update_post_meta($post_id, IWJ_PREFIX . 'featured_date', '');

            $salary_from = isset($extra_data[IWJ_PREFIX . 'salary_from']) ? $extra_data[IWJ_PREFIX . 'salary_from'] : '';
            $salary_to = isset($extra_data[IWJ_PREFIX . 'salary_to']) ? $extra_data[IWJ_PREFIX . 'salary_to'] : '';

            if ($salary_to !== '' && $salary_to < $salary_from) {
                $salary_to = $salary_from;
            }

            update_post_meta($post_id, IWJ_PREFIX . 'salary_to', $salary_to);
            update_post_meta($post_id, IWJ_PREFIX . 'salary_from', $salary_from);

            if (isset($extra_data[IWJ_PREFIX . 'salary_from'])) {
                self::set_salary($post_id, $salary_from, $salary_to);
            }
        }

        do_action('iwj_add_new_job', $post_id);

        return $post_id;
    }

    static function get_status_array($draft = true, $expired = false) {
        $status = array(
            'publish' => __('Publish', 'iwjob'),
            'pending' => __('Pending', 'iwjob'),
            'iwj-pending-payment' => __('Pending Payment', 'iwjob'),
            'iwj-rejected' => __('Rejected', 'iwjob'),
            'iwj-expired' => __('Expired', 'iwjob'),
        );

        if ($expired) {
            $status['iwj-expired'] = __('Expired', 'iwjob');
        }

        if ($draft) {
            $status['draft'] = __('Draft', 'iwjob');
        }

        return $status;
    }

    static function get_status_title($status) {
        $status_arr = self::get_status_array(true);
        if (isset($status_arr[$status])) {
            return $status_arr[$status];
        }

        return '';
    }

    static function remove_job_reference($job_id) {
        if ($job_id && get_post_type($job_id) == 'iwj_job') {
            if (get_post_status($job_id) == 'publish') {
                delete_transient('iwj_count_jobs');
            }

            global $wpdb;
            $wpdb->delete($wpdb->prefix . 'iwj_save_jobs', array('post_id' => $job_id,), array('%d'));
        }
    }

}
