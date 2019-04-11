<?php

class IWJ_User {

    static $cache;
    public $user;

    static function init() {
        add_action('user_register', array(__CLASS__, 'registration_save'), 10, 1);
        add_action('profile_update', array(__CLASS__, 'profile_update'), 10, 2);
        add_action('delete_user', array(__CLASS__, 'delete_user'), 10, 2);
        add_filter('get_avatar', array(__CLASS__, 'get_avatar'), 99, 5);
        add_filter('get_avatar_url', array(__CLASS__, 'get_avatar_url'), 99, 3);
    }

    static function delete_user($user_id, $reassign) {
        global $wpdb;
        $post_types_to_delete['job'] = 'iwj_job';
        $post_types_to_delete['application'] = 'iwj_application';
        $post_types_to_delete[] = 'iwj_employer';
        $post_types_to_delete[] = 'iwj_candidate';
        $post_types_to_delete[] = 'iwj_u_package';
        $post_types_to_delete[] = 'iwj_order';

        if ($reassign) {
            unset($post_types_to_delete['job']);
            if (iwj_option('keep_jobs_delete_user') == '2') {
                unset($post_types_to_delete['application']);
            }
        }

        $post_types_to_delete = array_values($post_types_to_delete);

        $post_types_to_delete = implode("', '", $post_types_to_delete);
        $post_ids = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_author = %d AND post_type IN ('$post_types_to_delete')", $user_id));
        if ($post_ids) {
            foreach ($post_ids as $post_id)
                wp_delete_post($post_id);
        }

        $sql = "SELECT ID FROM {$wpdb->prefix}iwj_alerts WHERE user_id = %d";
        $alerts = $wpdb->get_results($wpdb->prepare($sql, $user_id));
        $alert_ids = array();
        if ($alerts) {
            foreach ($alerts as $alert) {
                $alert_ids[] = $alert->ID;
            }
        }
        if ($alert_ids) {
            $wpdb->query("DELETE FROM {$wpdb->prefix}iwj_alerts WHERE ID IN(" . implode(',', $alert_ids) . ")");
            $wpdb->query("DELETE FROM {$wpdb->prefix}iwj_alert_relationships WHERE alert_id IN(" . implode(',', $alert_ids) . ")");
        }

        $sql_review = "SELECT ID FROM {$wpdb->prefix}iwj_reviews WHERE user_id = %d";
        $reviews = $wpdb->get_results($wpdb->prepare($sql_review, $user_id));
        $review_ids = array();
        if ($reviews) {
            foreach ($reviews as $review) {
                $review_ids[] = $review->ID;
            }
        }
        if ($review_ids) {
            $wpdb->query("DELETE FROM {$wpdb->prefix}iwj_reviews WHERE ID IN(" . implode(',', $review_ids) . ")");
            $wpdb->query("DELETE FROM {$wpdb->prefix}iwj_reviews_reply WHERE review_id IN(" . implode(',', $review_ids) . ")");
        }

        $wpdb->delete($wpdb->prefix . 'iwj_follows', array('user_id' => $user_id), array('%d'));
        $wpdb->delete($wpdb->prefix . 'iwj_save_jobs', array('user_id' => $user_id), array('%d'));
        $wpdb->delete($wpdb->prefix . 'iwj_save_resums', array('user_id' => $user_id), array('%d'));
        $wpdb->delete($wpdb->prefix . 'iwj_view_resums', array('user_id' => $user_id), array('%d'));
    }

    static function add_employer($user_id) {
        $user_info = get_userdata($user_id);
        $post_data = array(
            'post_title' => isset($_POST['company']) ? sanitize_text_field($_POST['company']) : $user_info->display_name,
            'post_type' => 'iwj_employer',
            'post_content' => $user_info->description,
            'post_status' => 'iwj-incomplete',
            'post_author' => $user_id,
        );

        $post_id = wp_insert_post($post_data);
        if ($post_id) {
            update_user_meta($user_id, IWJ_PREFIX . 'employer_post', $post_id);
        }

        return $post_id;
    }

    static function add_candidate($user_id) {
        $user_info = get_userdata($user_id);
        $post_data = array(
            'post_title' => $user_info->display_name,
            'post_type' => 'iwj_candidate',
            'post_content' => $user_info->description,
            'post_status' => 'iwj-incomplete',
            'post_author' => $user_id,
        );

        $post_id = wp_insert_post($post_data);
        if ($post_id) {
            update_user_meta($user_id, IWJ_PREFIX . 'candidate_post', $post_id);
        }

        return $post_id;
    }

    static function registration_save($user_id) {
        if (user_can($user_id, 'create_iwj_jobs') || (isset($_POST['role']) && $_POST['role'] == 'employer')) {
            $user = self::get_user($user_id);
            if (!$user->get_employer(true)) {
                //add employer post type
                self::add_employer($user_id);
            }
        } elseif (user_can($user_id, 'apply_job') || (isset($_POST['role']) && $_POST['role'] == 'candidate')) {
            $user = self::get_user($user_id);
            if (!$user->get_candidate(true)) {
                //add employer post type
                self::add_candidate($user_id);
            }
        }
    }

    static function profile_update($user_id, $old_user_data) {

        if (user_can($user_id, 'create_iwj_jobs')) {
            $user = self::get_user($user_id);
            if (!$user->get_employer()) {
                //add employer post type
                self::add_employer($user_id);
            }
        } elseif (user_can($user_id, 'apply_job')) {
            $user = self::get_user($user_id);
            if (!$user->get_candidate()) {
                //add employer post type
                self::add_candidate($user_id);
            }
        }

        if (isset($_POST) && $_POST && is_blog_admin() && !defined('DOING_AJAX')) {

            $screen = get_current_screen();
            if ($screen && isset($screen->base) && ($screen->base == 'user-edit' || $screen->base == 'profile')) {
                if (isset($_POST[IWJ_PREFIX . 'avatar']) && $_POST[IWJ_PREFIX . 'avatar']) {
                    $thumbnail_id = $_POST[IWJ_PREFIX . 'avatar'][0];
                    update_user_meta($user_id, IWJ_PREFIX . 'avatar', $thumbnail_id);
                    $employer_id = get_user_meta($user_id, IWJ_PREFIX . 'employer_post', true);
                    $candidate_id = get_user_meta($user_id, IWJ_PREFIX . 'candidate_post', true);
                    if ($employer_id) {
                        set_post_thumbnail($employer_id, $thumbnail_id);
                    } elseif ($candidate_id) {
                        set_post_thumbnail($candidate_id, $thumbnail_id);
                    }
                } else {
                    delete_user_meta($user_id, IWJ_PREFIX . 'avatar');
                    $employer_id = get_user_meta($user_id, IWJ_PREFIX . 'employer_post', true);
                    $candidate_id = get_user_meta($user_id, IWJ_PREFIX . 'candidate_post', true);

                    if ($employer_id) {
                        delete_post_thumbnail($employer_id);
                    } elseif ($candidate_id) {
                        delete_post_thumbnail($candidate_id);
                    }
                }
            }
        }
    }

    static function can_reset_password($user_name, $code) {
        $current_user = wp_get_current_user();
        $user = get_user_by('login', $user_name);
        if ($user && (!$current_user->ID || $current_user->user_login == $user_name)) {
            $reset_code = get_user_meta($user->ID, IWJ_PREFIX . 'resetpass_code', true);
            if ($reset_code === $code) {
                return true;
            }
        }

        return false;
    }

    public function __construct($user) {
        $this->user = $user;
    }

    /**
     * @param null $user
     * @param bool $force
     * @return IWJ_User
     */
    static function get_user($user = null, $force = false) {
        if ($user === null) {
            if (!function_exists('wp_get_current_user')) {
                include(ABSPATH . "wp-includes/pluggable.php");
            }
            $user = wp_get_current_user();
        }
        $user_id = 0;
        if (is_numeric($user)) {
            $user = get_userdata($user);
            if ($user && !is_wp_error($user)) {
                $user_id = $user->ID;
            }
        } elseif (is_object($user)) {
            $user_id = $user->ID;
        }

        if ($user_id) {
            if ($force) {
                clean_user_cache($user_id);
                $user = get_userdata($user_id);
            }

            if ($force || !isset(self::$cache[$user_id])) {
                self::$cache[$user_id] = new IWJ_User($user);
            }

            return self::$cache[$user_id];
        }


        return null;
    }

    function get_id() {
        return $this->user->ID;
    }

    function get_display_name() {
        return $this->user->display_name;
    }

    function get_headline() {
        if ($this->is_employer() && $employer = $this->get_employer()) {
            return $employer->get_headline();
        } elseif ($this->is_candidate() && $candidate = $this->get_candidate()) {
            return $candidate->get_headline();
        } else {
            return $this->user->roles[0];
        }
    }

    function get_locations_links() {
        if ($this->is_employer() && $employer = $this->get_employer()) {
            return $employer->get_locations_links();
        } elseif ($this->is_candidate() && $candidate = $this->get_candidate()) {
            return $candidate->get_locations_links();
        } else {
            return '';
        }
    }

    function get_first_name() {
        return $this->user->user_firstname;
    }

    function get_last_name() {
        return $this->user->user_lastname;
    }

    function get_description() {
        if ($this->is_employer() && $employer = $this->get_employer()) {
            return $employer->get_description(true);
        } elseif ($this->is_candidate() && $candidate = $this->get_candidate()) {
            return $candidate->get_description(true);
        } else {
            return $this->user->description;
        }
    }

    function get_short_description() {
        if ($this->is_employer() && $employer = $this->get_employer()) {
            return $employer->get_short_description();
        } elseif ($this->is_candidate() && $candidate = $this->get_candidate()) {
            return $candidate->get_description();
        } else {
            return $this->user->description;
        }
    }

    function get_full_address() {
        if ($this->is_candidate() && $candidate = $this->get_candidate()) {
            return $candidate->get_full_address();
        } elseif ($this->is_employer() && $employer = $this->get_employer()) {
            return $employer->get_full_address();
        } else {
            return '';
        }
    }

    function get_address() {
        if ($this->is_candidate() && $candidate = $this->get_candidate()) {
            return $candidate->get_address();
        } elseif ($this->is_employer() && $employer = $this->get_employer()) {
            return $employer->get_address();
        } else {
            return '';
        }
    }

    function get_gallery() {
        if ($this->is_candidate() && $candidate = $this->get_candidate()) {
            return $candidate->get_gallery();
        } elseif ($this->is_employer() && $employer = $this->get_employer()) {
            return $employer->get_gallery();
        } else {
            return '';
        }
    }

    function get_map() {
        if ($this->is_candidate() && $candidate = $this->get_candidate()) {
            return $candidate->get_map();
        } elseif ($this->is_employer() && $employer = $this->get_employer()) {
            return $employer->get_map();
        } else {
            return '';
        }
    }

    function get_phone() {
        if ($this->is_candidate() && $candidate = $this->get_candidate()) {
            return $candidate->get_phone();
        } elseif ($this->is_employer() && $employer = $this->get_employer()) {
            return $employer->get_phone();
        } else {
            return '';
        }
    }

    function get_website() {
        return $this->user->user_url;
    }

    function get_roles() {
        return $this->user->roles;
    }

    function get_email() {
        return $this->user->user_email;
    }

    function get_login() {
        return $this->user->user_login;
    }

    function permalink() {
        if ($this->is_employer() && $employer = $this->get_employer()) {
            return $employer->permalink();
        } elseif ($this->is_candidate() && $candidate = $this->get_candidate()) {
            return $candidate->permalink();
        } else {
            return get_author_posts_url($this->get_id());
        }
    }

    static function get_avatar($avatar = '', $id_or_email, $size = 96, $default = '', $alt = '') {
        if (is_numeric($id_or_email))
            $user_id = (int) $id_or_email;
        elseif (is_string($id_or_email) && ( $user = get_user_by('email', $id_or_email) ))
            $user_id = $user->ID;
        elseif (is_object($id_or_email) && !empty($id_or_email->user_id))
            $user_id = (int) $id_or_email->user_id;

        if (empty($user_id))
            return $avatar;

        // fetch local avatar from meta and make sure it's properly ste
        $avatars = get_user_meta($user_id, IWJ_PREFIX . 'avatar', true);

        if (empty($avatars))
            return $avatar;

        if (is_numeric($avatars)) {
            $avatars = wp_get_attachment_image_src($avatars, 'full');
            if (empty($avatars[0]))
                return $avatar;

            $size = (int) $size;

            if (empty($alt))
                $alt = get_the_author_meta('display_name', $user_id);

            // generate a new size
            $avatar_url = iwj_resize_image($avatars[0], $size, $size, true);
            $avatar_url2x = iwj_resize_image($avatars[0], $size * 2, $size * 2, true);
        }else {
            $avatar_url = $avatar_url2x = $avatars;
        }

        if ('http' != substr($avatar_url, 0, 4))
            $avatar_url = home_url($avatar_url);

        $author_class = is_author($user_id) ? ' current-author' : '';
        $avatar = "<img alt='" . esc_attr($alt) . "' src='" . ( $avatar_url ) . "' srcset='" . $avatar_url2x . " 2x' class='avatar avatar-{$size}{$author_class} photo' height='{$size}' width='{$size}' />";

        return apply_filters('iwj_avatar', $avatar);

        /* if(!$url){
          $url = IWJ_PLUGIN_URL . '/assets/img/placehold-image.png';
          }

          $url = iwj_resize_image($url, 175, 175 ,true);

          return $url; */
    }

    static function get_avatar_url($url, $id_or_email, $args) {
        if (is_numeric($id_or_email))
            $user_id = (int) $id_or_email;
        elseif (is_string($id_or_email) && ( $user = get_user_by('email', $id_or_email) ))
            $user_id = $user->ID;
        elseif (is_object($id_or_email) && !empty($id_or_email->user_id))
            $user_id = (int) $id_or_email->user_id;

        if (empty($user_id))
            return $url;

        // fetch local avatar from meta and make sure it's properly ste
        $avatars = get_user_meta($user_id, IWJ_PREFIX . 'avatar', true);

        if (empty($avatars))
            return $url;

        if (is_numeric($avatars)) {
            if (isset($args['img_size']) && $args['img_size']) {
                if ($args['img_size'] == 'inwave-avatar') {
                    if ($args['size'] == 120) {
                        $avatars = wp_get_attachment_image_src($avatars, 'inwave-avatar2');
                    } else {
                        $avatars = wp_get_attachment_image_src($avatars, 'inwave-avatar');
                    }
                } else {
                    $avatars = wp_get_attachment_image_src($avatars, $args['img_size']);
                }
            } else {
                $avatars = wp_get_attachment_image_src($avatars, 'thumbnail');
            }
            if (empty($avatars[0]))
                return $url;
            // generate a new size
            $avatar_url = $avatars[0];
        }else {
            $avatar_url = $avatars;
        }

        return $avatar_url;
    }

    function is_active_profile() {
        if ($this->is_employer()) {
            $employer = $this->get_employer();
            if ($employer && $employer->is_active()) {
                return true;
            }
        }

        if ($this->is_candidate()) {
            $candidate = $this->get_candidate();
            if ($candidate && $candidate->is_active()) {
                return true;
            }
        }

        return false;
    }

    function is_employer() {

        if (user_can($this->user, 'create_iwj_jobs')) {
            return true;
        }

        return false;
    }

    function get_employer_id() {
        return get_user_meta($this->get_id(), IWJ_PREFIX . 'employer_post', true);
    }

    function get_employer($force = false, $status = '') {
        $employer_id = $this->get_employer_id();
        if ($employer_id) {
            $employer = IWJ_Employer::get_employer($employer_id, $force);
            if (($status && $employer->post->post_status == $status) || !$status) {
                return $employer;
            }
        }

        return null;
    }

    function is_candidate() {
        if ($this->is_employer()) {
            return false;
        }

        if (user_can($this->user, 'apply_job')) {
            return true;
        }

        return false;
    }

    function get_candidate_id() {
        return get_user_meta($this->get_id(), IWJ_PREFIX . 'candidate_post', true);
    }

    function get_candidate($force = false) {
        $candidate_id = $this->get_candidate_id();
        if ($candidate_id) {
            return IWJ_Candidate::get_candidate($candidate_id, $force);
        }

        return null;
    }

    function get_user_package_ids($type = 'job_package', $status = array('publish', 'iwj-pending-payment')) {
        global $wpdb;

        $sql = "SELECT DISTINCT ID FROM {$wpdb->posts} AS p 
                JOIN {$wpdb->postmeta} as pm ON p.ID = pm.post_id 
                WHERE p.post_type = %s AND p.post_author = %d AND p.post_status IN ('" . implode("','", $status) . "')
                AND pm.meta_key = %s AND pm.meta_value = %s";

        $sql = $wpdb->prepare(
                $sql, 'iwj_u_package', $this->get_id(), IWJ_PREFIX . 'package_type', $type);

        $user_packages = $wpdb->get_results($sql);

        $user_package_ids = array();
        if ($user_packages) {
            foreach ($user_packages as $user_package) {
                $user_package_ids[] = $user_package->ID;
            }
        }
        return $user_package_ids;
    }

    function get_user_packages($type = 'job_package', $args = array()) {
        $default_args = array(
            'posts_per_page' => iwj_option('dashboard_items_per_page', get_option('posts_per_page', 20)),
            'post_type' => 'iwj_u_package',
            'post_status' => 'publish',
            'author' => $this->get_id(),
            'paged' => isset($_GET['cpage']) ? $_GET['cpage'] : '1',
            'meta_query' => array(
                array(
                    'key' => IWJ_PREFIX . 'package_type',
                    'value' => $type,
                    'compare' => '='
                )
            )
        );

        $args = wp_parse_args($args, $default_args);
        return new WP_Query($args);
    }

    function get_plan_id() {
        return get_user_meta($this->get_id(), IWJ_PREFIX . 'plan_id', true);
    }

    /**
     * @return IWJ_Plan|null
     */
    function get_plan() {
        return IWJ_Plan::get_package($this->get_plan_id());
    }

    function has_plan() {
        $plan_id = $this->get_plan_id();
        if ($plan_id) {
            return true;
        }

        return false;
    }

    function plan_get_jobs($show_unlimited = false) {
        $listings = get_user_meta($this->get_id(), IWJ_PREFIX . 'plan_jobs', true);
        if ($listings === '') {
            $package = $this->get_plan();
            if ($package) {
                $listings = $package->get_jobs($show_unlimited);
            }
        } elseif ($show_unlimited && $listings == '-1') {
            $listings = __('Unlimited', 'iwproperty');
        }

        return $listings;
    }

    function plan_get_jobs_used() {
        static $total = null;
        if (is_null($total)) {
            global $wpdb;
            $sql = "SELECT COUNT(1) FROM {$wpdb->posts} WHERE post_type = %s AND post_author = %d AND post_status IN ('publish', 'pending', 'iwp-rejected')";
            $sql = $wpdb->prepare($sql, 'iwj_job', $this->get_id());
            $total = $wpdb->get_var($sql);
        }

        return $total;
    }

    function plan_get_jobs_available() {
        $listings = $this->plan_get_jobs() - $this->plan_get_jobs_used();
        return $listings > 0 ? $listings : 0;
    }

    function plan_jobs_is_available() {
        if ($this->plan_get_jobs() === '-1') {
            return true;
        }

        $listings_available = $this->plan_get_jobs_available();
        if ($listings_available > 0) {
            return true;
        }

        return false;
    }

    function plan_get_featured_jobs($show_unlimited = false) {
        $listings = get_user_meta($this->get_id(), IWJ_PREFIX . 'plan_featured_jobs', true);
        if ($listings === '') {
            $package = $this->get_plan();
            if ($package) {
                $listings = $package->get_featured_jobs($show_unlimited);
            }
        } elseif ($show_unlimited && $listings == '-1') {
            $listings = __('Unlimited', 'iwproperty');
        }

        return $listings;
    }

    function plan_get_featured_jobs_used() {
        static $total = null;
        if (is_null($total)) {
            global $wpdb;
            $sql = "SELECT COUNT(1) FROM {$wpdb->posts} AS p JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id WHERE p.post_type = %s AND p.post_author = %d AND ((pm.meta_key = %s AND pm.meta_value = %d) OR (pm.meta_key = %s AND pm.meta_value = %d))";
            $sql = $wpdb->prepare($sql, 'iwj_job', $this->get_id(), IWJ_PREFIX . 'featured', 1, IWJ_PREFIX . 'make_featured', 1);
            $total = $wpdb->get_var($sql);
        }

        return $total;
    }

    function plan_get_featured_jobs_available() {
        $listings = $this->plan_get_featured_jobs() - $this->plan_get_featured_jobs_used();
        return $listings > 0 ? $listings : 0;
    }

    function plan_featured_jobs_is_available() {
        if ($this->plan_get_featured_jobs() === '-1') {
            return true;
        }

        $listings_available = $this->plan_get_featured_jobs_available();
        if ($listings_available > 0) {
            return true;
        }

        return false;
    }

    function plan_get_renew_jobs($show_unlimited = false) {
        $listings = get_user_meta($this->get_id(), IWJ_PREFIX . 'plan_renew_jobs', true);
        if ($listings === '') {
            $package = $this->get_plan();
            if ($package) {
                $listings = $package->get_renew_jobs($show_unlimited);
            }
        } elseif ($show_unlimited && $listings == '-1') {
            $listings = __('Unlimited', 'iwproperty');
        }

        return $listings;
    }

    function plan_get_renew_jobs_used() {
        return get_user_meta($this->get_id(), IWJ_PREFIX . 'plan_renew_jobs_used', true);
    }

    function plan_get_renew_jobs_available() {
        $listings = $this->plan_get_renew_jobs() - $this->plan_get_renew_jobs_used();
        return $listings > 0 ? $listings : 0;
    }

    function plan_renew_jobs_is_available() {
        if ($this->plan_get_renew_jobs() === '-1') {
            return true;
        }

        $listings_available = $this->plan_get_renew_jobs_available();
        if ($listings_available > 0) {
            return true;
        }

        return false;
    }

    function plan_get_expiry_date() {
        return get_user_meta($this->get_id(), IWJ_PREFIX . 'plan_expiry_date', true);
    }

    function plan_is_active() {
        $package = $this->get_plan();
        if ($package) {
            $expiry_date = $this->plan_get_expiry_date();
            if (($expiry_date === '' || ($expiry_date && $expiry_date > current_time('timestamp')))) {
                return true;
            }
        }

        return false;
    }

    function change_plan($new_package_id, $new_plan_expiry_date) {
        if ($new_package_id) {
            $new_package = IWJ_Plan::get_package($new_package_id);
            $old_package_id = $this->get_plan_id();
            $old_plan_expiry_date = $this->plan_get_expiry_date();
            if ($new_package_id != $old_package_id || $new_plan_expiry_date != $old_plan_expiry_date) {
                $post_will_be_expiry = 0;
                $post_will_be_unfeatured = 0;
                if ($new_package && $new_package->get_jobs() != '-1') {
                    $total_used_post = $this->plan_get_jobs_used();
                    $total_featured_used = $this->plan_get_featured_jobs_used();
                    if ($total_used_post > $new_package->get_jobs()) {
                        $post_will_be_expiry = $total_used_post - $new_package->get_jobs();
                    }
                    if ($total_featured_used > $new_package->get_featured_jobs()) {
                        $post_will_be_unfeatured = $total_featured_used - $new_package->get_featured_jobs();
                    }
                }
                global $wpdb;
                $sql = "SELECT p.ID,p.post_status FROM {$wpdb->posts} AS p WHERE p.post_type = %s AND p.post_author = %d AND p.post_status NOT IN ('auto-draft') ORDER BY p.post_date";
                $jobs = $wpdb->get_results($wpdb->prepare($sql, 'iwj_job', $this->get_id()));
                if ($jobs) {
                    foreach ($jobs as $job) {
                        if ($new_package_id != $old_package_id) {
                            update_post_meta($job->ID, IWJ_PREFIX . 'plan_id', $new_package_id);
                        }
                        if ($post_will_be_unfeatured > 0) {
                            update_post_meta($job->ID, IWJ_PREFIX . 'featured', 0);
                            $post_will_be_unfeatured--;
                        }

                        if ($new_plan_expiry_date && $new_plan_expiry_date != '-1' && $new_plan_expiry_date <= (current_time('timestamp') + 86400)) {
                            $sql = "UPDATE {$wpdb->posts} SET post_status = %s WHERE ID = %d";
                            $wpdb->query($wpdb->prepare($sql, 'iwp-expired', $job->ID));
                            update_post_meta($this->get_id(), IWJ_PREFIX . 'expiry', $new_plan_expiry_date);
                        } elseif ($post_will_be_expiry > 0 && $job->post_status == 'publish') {
                            $sql = "UPDATE {$wpdb->posts} SET post_status = %s WHERE ID = %d";
                            $wpdb->query($wpdb->prepare($sql, 'iwp-expired', $job->ID));
                            update_post_meta($this->get_id(), IWJ_PREFIX . 'expiry', current_time('timestamp'));
                            $post_will_be_expiry--;
                        } elseif ($new_plan_expiry_date != $old_plan_expiry_date) {
                            $current_expiry = get_post_meta($job->ID, IWJ_PREFIX . 'expiry', true);
                            if (!$current_expiry || ($current_expiry && $current_expiry != '-1')) {
                                update_post_meta($job->ID, IWJ_PREFIX . 'expiry', $new_plan_expiry_date);
                            }
                        }
                    }
                }

                if ($new_package_id != $old_package_id) {
                    update_user_meta($this->get_id(), IWJ_PREFIX . 'plan_id', $new_package_id);
                }
                if ($new_plan_expiry_date != $old_plan_expiry_date) {
                    update_user_meta($this->get_id(), IWJ_PREFIX . 'plan_expiry_date', $new_plan_expiry_date);
                    if ($new_plan_expiry_date > current_time('timestamp')) {
                        delete_user_meta($this->get_id(), IWJ_PREFIX . 'sent_expired_email');
                    }
                }
            }
        } else {
            update_user_meta($this->get_id(), IWJ_PREFIX . 'plan_id', $new_package_id);
        }
    }

    function set_plan($plan_id) {
        $plan = IWJ_Plan::get_package($plan_id);
        if ($plan) {
            $old_plan_id = $this->get_plan_id();
            update_user_meta($this->get_id(), IWJ_PREFIX . 'plan_id', $plan_id);
            if ($old_plan_id && $old_plan_id == $plan_id) {
                $old_plan_expiry_time = $this->plan_get_expiry_date();
                if ($old_plan_expiry_time > current_time('timestamp')) {
                    $this->change_plan($plan_id, $plan->get_time_expiry($old_plan_expiry_time));
                } else {
                    $this->change_plan($plan_id, $plan->get_time_expiry());
                }
            } else {
                update_user_meta($this->get_id(), IWJ_PREFIX . 'plan_expiry_date', $plan->get_time_expiry());
            }

            update_user_meta($this->get_id(), IWJ_PREFIX . 'plan_jobs', '');
            update_user_meta($this->get_id(), IWJ_PREFIX . 'plan_featured_jobs', '');
            update_user_meta($this->get_id(), IWJ_PREFIX . 'plan_renew_jobs', '');
            update_user_meta($this->get_id(), IWJ_PREFIX . 'plan_renew_jobs_used', '0');
            if ($plan->is_free()) {
                $free_plan_used = (int) get_user_meta($this->get_id(), IWJ_PREFIX . 'plan_free_used', true);
                update_user_meta($this->get_id(), IWJ_PREFIX . 'plan_free_used', $free_plan_used + 1);
            }
        }
    }

    function plan_can_submit() {
        $plan_id = $this->get_plan_id();
        if (!$plan_id || !$this->plan_jobs_is_available()) {
            return false;
        }

        return true;
    }

    function get_plan_id_for_submition() {
        if ($this->plan_can_submit()) {
            $plan_id = $this->get_plan_id();
        } elseif (isset($_GET['plan-id'])) {
            $plan_id = $_GET['plan-id'];
        } elseif (isset($_POST['plan_id'])) {
            $plan_id = $_POST['plan_id'];
        } else {
            $plan_id = 0;
        }

        return $plan_id;
    }

    function get_plan_for_submition() {
        $plan_id = $this->get_plan_id_for_submition();

        if ($plan_id) {
            return IWJ_Plan::get_package($plan_id);
        }

        return false;
    }

    function get_orders($args = array()) {
        $default_args = array(
            'posts_per_page' => iwj_option('dashboard_items_per_page', get_option('posts_per_page', 20)),
            'post_type' => 'iwj_order',
            'post_status' => (isset($_GET['order_status']) && $_GET['order_status']) ? $_GET['order_status'] : array_keys(IWJ_Order::get_status_array()),
            'paged' => isset($_GET['cpage']) ? $_GET['cpage'] : '1',
            'author' => $this->get_id(),
        );

        if (isset($_GET['seach']) && $_GET['seach']) {
            $default_args['s'] = $_GET['seach'];
        }

        if (isset($_GET['order_type']) && $_GET['order_type']) {
            $default_args['meta_key'] = IWJ_PREFIX . 'type';
            $default_args['meta_value'] = $_GET['order_type'];
            $default_args['meta_compare'] = '=';
        }

        $args = wp_parse_args($args, $default_args);

        return new WP_Query($args);
    }

    function get_jobs($args = array()) {
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $paged = isset($_GET['cpage']) ? $_GET['cpage'] : '1';
        $status = isset($_GET['status']) && $_GET['status'] ? $_GET['status'] : array_keys(IWJ_Job::get_status_array());
        $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';
        $default_args = array(
            'post_type' => 'iwj_job',
            'post_parent' => 0,
            'post_status' => $status,
            'posts_per_page' => iwj_option('dashboard_item_per_page', get_option('posts_per_page', 20)),
            'author' => $this->get_id(),
            'paged' => $paged,
            's' => $search,
        );

        if ($orderby) {
            $orderby = explode("_", $orderby);
            $default_args['orderby'] = $orderby[0];
            $default_args['order'] = strtoupper($orderby[1]);
        }

        $args = wp_parse_args($args, $default_args);
        if ($args['post_status'] == 'iwj-expired') {
            $args['post_status'] = 'publish';
            $args['meta_query'] = array(
                'relation' => 'AND',
                array(
                    'key' => IWJ_PREFIX . 'expiry',
                    'value' => '',
                    'compare' => '!='
                ),
                array(
                    'key' => IWJ_PREFIX . 'expiry',
                    'value' => current_time('timestamp'),
                    'type' => 'numeric',
                    'compare' => '<='
                )
            );
        } elseif ($args['post_status'] == 'publish') {
            $args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key' => IWJ_PREFIX . 'expiry',
                    'value' => '',
                    'compare' => '='
                ),
                array(
                    'key' => IWJ_PREFIX . 'expiry',
                    'value' => current_time('timestamp'),
                    'type' => 'numeric',
                    'compare' => '>'
                )
            );
        }

        return new WP_Query($args);
    }

    function get_job_ids() {
        global $wpdb;
        $sql = "SELECT ID FROM {$wpdb->posts} WHERE post_author = %d AND post_type = %s AND post_status NOT IN ('trash', 'revisions', 'auto-draft')";
        $sql = $wpdb->prepare($sql, $this->get_id(), 'iwj_job');
        $jobs = $wpdb->get_results($sql);
        $job_ids = array();
        if ($jobs) {
            foreach ($jobs AS $job) {
                $job_ids[] = $job->ID;
            }
        }

        return $job_ids;
    }

    function get_totals_view() {
        $job_ids = $this->get_job_ids();
        $view = 0;
        if (count($job_ids)) {
            foreach ($job_ids as $job_id) {
                $job = IWJ_Job::get_job($job_id);
                $view += intval($job->get_views());
            }
        }

        return $view;
    }

    function get_applications($args = array()) {
        if (isset($args['job_ids'])) {
            $job_ids = (array) $args['job_ids'];
        } elseif (isset($_GET['job-id']) && $_GET['job-id']) {
            $job_ids = array($_GET['job-id']);
        } else {
            $job_ids = $this->get_job_ids();
        }

        if ($job_ids) {
            $default_args = array(
                'posts_per_page' => iwj_option('dashboard_items_per_page', get_option('posts_per_page', 20)),
                'post_type' => 'iwj_application',
                'post_status' => isset($_GET['status']) ? $_GET['status'] : array_keys(IWJ_Application::get_status_array()),
                'paged' => isset($_GET['cpage']) ? $_GET['cpage'] : '1',
                's' => isset($_GET['search']) ? $_GET['search'] : '',
                'meta_query' => array(
                    array(
                        'key' => IWJ_PREFIX . 'job_id',
                        'value' => $job_ids,
                        'compare' => 'IN'
                    )
                )
            );

            $args = wp_parse_args($args, $default_args);

            return new WP_Query($args);
        }

        return null;
    }

    function get_submited_applications($args = array()) {

        $default_args = array(
            'posts_per_page' => iwj_option('dashboard_items_per_page', get_option('posts_per_page', 20)),
            'post_type' => 'iwj_application',
            'post_status' => (isset($_GET['status']) && $_GET['status']) ? $_GET['status'] : array_keys(IWJ_Application::get_status_array()),
            'author' => $this->get_id(),
            'paged' => isset($_GET['cpage']) ? $_GET['cpage'] : '1',
        );

        if (isset($_GET['search']) && $_GET['search']) {
            $posts = get_posts(array(
                'post_type' => 'iwj_job',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                's' => $_GET['search']
            ));
            if ($posts) {
                $post_ids = array();
                foreach ($posts as $post) {
                    $post_ids[] = $post->ID;
                }

                $default_args['meta_query'] = array(
                    array(
                        'key' => IWJ_PREFIX . 'job_id',
                        'value' => $post_ids,
                        'compare' => 'IN'
                    )
                );
            } else {
                return null;
            }
        }

        $args = wp_parse_args($args, $default_args);

        return new WP_Query($args);
    }

    function count_packages() {
        global $wpdb;
        $sql = "SELECT COUNT(1) FROM {$wpdb->posts} AS p JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id WHERE p.post_type = %s AND p.post_status NOT IN ('trash', 'revisions', 'auto-draft')
         AND pm.meta_key = %s AND pm.meta_value = %d";
        $sql = $wpdb->prepare($sql, 'iwj_u_package', IWJ_PREFIX . 'user_id', $this->get_id());
        $jobs = $wpdb->get_var($sql);
        return $jobs;
    }

    function count_jobs($all = false, $expired_job = null) {
        if ($all) {
            global $wpdb;
            $sql = "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_author = %d AND post_type = %s AND post_status NOT IN ('trash', 'revisions', 'auto-draft')";
            $sql = $wpdb->prepare($sql, $this->get_id(), 'iwj_job');
            $jobs = $wpdb->get_var($sql);
        } else {
            global $wpdb;
            $expired_job = $expired_job === null ? iwj_option('show_expired_job') : $expired_job;
            if (!$expired_job) {
                $sql = "SELECT COUNT(1) FROM {$wpdb->posts} AS p JOIN {$wpdb->postmeta} AS pm ON pm.post_id = p.ID
                        WHERE pm.meta_key = '" . IWJ_PREFIX . "expiry' AND (pm.meta_value = '' OR (pm.meta_value != '' AND CAST(pm.meta_value AS UNSIGNED) > " . current_time('timestamp') . ")) AND  p.post_author = %d AND p.post_type = %s AND p.post_status IN ('publish')";
                $sql = $wpdb->prepare($sql, $this->get_id(), 'iwj_job');
            } else {
                $sql = "SELECT COUNT(1) FROM {$wpdb->posts} WHERE post_author = %d AND post_type = %s AND post_status IN ('publish')";
                $sql = $wpdb->prepare($sql, $this->get_id(), 'iwj_job');
            }

            $jobs = $wpdb->get_var($sql);
        }

        return $jobs;
    }

    function count_jobs_with_status($status = '') {
        global $wpdb;
        if ($status == 'expired') {
            $sql = "SELECT COUNT(1) FROM {$wpdb->posts} AS p JOIN {$wpdb->postmeta} AS pm ON pm.post_id = p.ID
                        WHERE pm.meta_key = '" . IWJ_PREFIX . "expiry' AND pm.meta_value != '' AND (pm.meta_value != '' AND CAST(pm.meta_value AS UNSIGNED) < " . current_time('timestamp') . ") AND  p.post_author = %d AND p.post_type = %s AND p.post_status IN ('publish')";
            $sql = $wpdb->prepare($sql, $this->get_id(), 'iwj_job');
            $jobs = $wpdb->get_var($sql);
        } else if ($status == 'publish') {
            $sql = "SELECT COUNT(ID) FROM {$wpdb->posts} AS p JOIN {$wpdb->postmeta} AS pm ON pm.post_id = p.ID
                        WHERE ((pm.meta_key = '" . IWJ_PREFIX . "expiry' AND pm.meta_value = '') OR (pm.meta_key = '" . IWJ_PREFIX . "expiry' AND CAST(pm.meta_value AS SIGNED) > " . current_time('timestamp') . ")) AND  p.post_author = %d AND p.post_type = %s AND p.post_status IN ('publish')";
            $sql = $wpdb->prepare($sql, $this->get_id(), 'iwj_job');
            $jobs = $wpdb->get_var($sql);
        } else {
            $status = explode(",", $status);
            $jobs = 0;
            foreach ($status as $st) {
                $sql = "SELECT COUNT(1) FROM {$wpdb->posts} WHERE post_author = %d AND post_type = %s AND post_status IN (%s)";
                $sql = $wpdb->prepare($sql, $this->get_id(), 'iwj_job', $st);
                $jobs += $wpdb->get_var($sql);
            }
        }

        return $jobs;
    }

    function count_orders() {
        global $wpdb;
        $sql = "SELECT COUNT(1) FROM {$wpdb->posts} WHERE post_author = %d AND post_type = %s AND post_status NOT IN ('trash', 'revisions', 'auto-draft')";
        $sql = $wpdb->prepare($sql, $this->get_id(), 'iwj_order');
        $orders = $wpdb->get_var($sql);

        return $orders;
    }

    function get_open_jobs($args = array()) {
        $id = $this->get_id();
        $default_args = array(
            'posts_per_page' => -1,
            'post_type' => 'iwj_job',
            'post_status' => 'publish',
            'post__not_in' => array($id),
            'author' => $this->get_id(),
        );

        if (!iwj_option('show_expired_job')) {
            $default_args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key' => IWJ_PREFIX . 'expiry',
                    'value' => current_time('timestamp'),
                    'compare' => '>',
                ),
                array(
                    'key' => IWJ_PREFIX . 'expiry',
                    'value' => '',
                    'compare' => '=',
                )
            );
        }

        $args = wp_parse_args($args, $default_args);
        $employer_jobs = get_posts($args);
        if ($employer_jobs) {
            foreach ($employer_jobs as $key => $employer_job) {
                $employer_jobs[$key] = IWJ_Job::get_job($employer_job);
            }
        }

        return $employer_jobs;
    }

    function get_follower_ids() {
        $employer_post_id = $this->get_employer_id();
        $user_ids = array();

        if ($employer_post_id) {
            global $wpdb;
            $sql = "SELECT user_id FROM {$wpdb->prefix}iwj_follows WHERE post_id = %d";
            $records = $wpdb->get_results($wpdb->prepare($sql, $employer_post_id));
            if ($records) {
                foreach ($records as $record) {
                    $user_ids[] = $record->user_id;
                }
            }
        }

        return $user_ids;
    }

    function count_followers() {
        $employer_post_id = $this->get_employer_id();
        $total = 0;

        if ($employer_post_id) {
            global $wpdb;
            $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}iwj_follows WHERE post_id = %d";
            $total = $wpdb->get_var($wpdb->prepare($sql, $employer_post_id));
        }

        return $total;
    }

    function count_following() {
        global $wpdb;
        $sql = "SELECT COUNT(f.*) FROM {$wpdb->prefix}iwj_follows AS f JOIN {$wpdb->posts} AS p ON f.post_id = p.ID WHERE f.user_id = %d AND p.post_status = 'publish'";
        $total = $wpdb->get_var($wpdb->prepare($sql, $this->get_id()));

        return $total;
    }

    function generate_resetpass_code() {
        $code = wp_generate_password(50, false);
        update_user_meta($this->get_id(), IWJ_PREFIX . 'resetpass_code', $code);
        return $code;
    }

    function delete_resetpass_code() {
        return delete_user_meta($this->get_id(), IWJ_PREFIX . 'resetpass_code');
    }

    function get_resetpass_url() {
        $url = iwj_get_page_permalink('lostpass');
        $url = add_query_arg(array('user' => $this->get_login(), 'code' => get_user_meta($this->get_id(), IWJ_PREFIX . 'resetpass_code', true)), $url);
        return $url;
    }

    public function get_follows($args = array()) {
        global $wpdb;

        $default_args = array(
            'posts_per_page' => iwj_option('dashboard_items_per_page', get_option('posts_per_page', 20)),
            'search' => isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '',
        );
        $args = wp_parse_args($args, $default_args);
        $query = "SELECT f.* FROM {$wpdb->prefix}iwj_follows AS f JOIN {$wpdb->posts} AS p ON f.post_id = p.ID WHERE p.post_status = 'publish' AND f.user_id = '" . $this->get_id() . "'";
        if ($args['search']) {
            $query .= ' AND p.post_title LIKE "%' . $args['search'] . '%"';
        }
        //$query             = "SELECT * FROM {$wpdb->prefix}iwj_follows";
        $total_query = "SELECT COUNT(1) FROM (${query}) AS combined_table";
        $total = $wpdb->get_var($total_query);
        $items_per_page = $args['posts_per_page'];
        $page = isset($_GET['cpage']) ? abs((int) $_GET['cpage']) : 1;
        $offset = ( $page * $items_per_page ) - $items_per_page;
        $result = $wpdb->get_results($query . " ORDER BY f.ID DESC LIMIT ${offset}, ${items_per_page}");
        $total_page = ceil($total / $items_per_page);
        if ($total) {
            return array(
                'result' => $result,
                'total_page' => $total_page,
                'current_page' => $page,
            );
        } else {
            return null;
        }
    }

    public function get_save_jobs($args = array()) {
        global $wpdb;

        $default_args = array(
            'posts_per_page' => iwj_option('dashboard_items_per_page', get_option('posts_per_page', 20)),
            'search' => isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '',
        );
        $args = wp_parse_args($args, $default_args);
        if (iwj_option('show_expired_job')) {
            $query = "SELECT DISTINCT s.* FROM {$wpdb->prefix}iwj_save_jobs AS s JOIN {$wpdb->posts} AS p ON s.post_id = p.ID WHERE p.post_status = 'publish' AND s.user_id = '" . $this->get_id() . "'";
        } else {
            $query = "SELECT DISTINCT s.* FROM {$wpdb->prefix}iwj_save_jobs AS s JOIN {$wpdb->posts} AS p ON s.post_id = p.ID 
                      JOIN {$wpdb->postmeta} AS pm ON (pm.post_id = p.ID)
                      WHERE p.post_status = 'publish' AND s.user_id = '" . $this->get_id() . "' AND pm.meta_key = '" . IWJ_PREFIX . 'expiry' . "' AND (pm.meta_value = '' OR (pm.meta_value != '' AND CAST(pm.meta_value AS SIGNED) > " . current_time('timestamp') . ") )";
        }

        if ($args['search']) {
            $query .= ' AND p.post_title LIKE "%' . $args['search'] . '%"';
        }

        //$query             = "SELECT * FROM {$wpdb->prefix}iwj_save_jobs";
        $total_query = "SELECT COUNT(1) FROM (${query}) AS combined_table";
        $total = $wpdb->get_var($total_query);
        $items_per_page = $args['posts_per_page'];
        $page = isset($_GET['cpage']) ? abs((int) $_GET['cpage']) : 1;
        $offset = ( $page * $items_per_page ) - $items_per_page;
        $result = $wpdb->get_results($query . " ORDER BY s.ID DESC LIMIT ${offset}, ${items_per_page}");
        $total_page = ceil($total / $items_per_page);
        if ($total) {
            return array(
                'result' => $result,
                'total_page' => $total_page,
                'current_page' => $page,
            );
        } else {
            return null;
        }
    }

    public function get_view_resumes($args = array()) {
        global $wpdb;

        $default_args = array(
            'posts_per_page' => iwj_option('dashboard_items_per_page', get_option('posts_per_page', 20)),
            'search' => isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '',
        );
        $args = wp_parse_args($args, $default_args);
        $query = "SELECT v.* FROM {$wpdb->prefix}iwj_view_resums AS v JOIN {$wpdb->posts} AS p ON v.post_id = p.ID WHERE p.post_status = 'publish' AND v.user_id = '" . $this->get_id() . "'";
        if ($args['search']) {
            if (is_email($args['search'])) {
                $search_user = get_user_by('email', $args['search']);
                if ($search_user) {
                    $query .= ' AND p.post_author = "' . $search_user->ID . '"';
                } else {
                    $query .= ' AND p.post_author = "-1"';
                }
            } else {
                $query .= ' AND p.post_title LIKE "%' . $args['search'] . '%"';
            }
        }
        $total_query = "SELECT COUNT(1) FROM (${query}) AS combined_table";
        $total = $wpdb->get_var($total_query);
        $items_per_page = $args['posts_per_page'];
        $page = isset($_GET['cpage']) ? abs((int) $_GET['cpage']) : 1;
        $offset = ( $page * $items_per_page ) - $items_per_page;
        $result = $wpdb->get_results($query . " ORDER BY v.ID DESC LIMIT ${offset}, ${items_per_page}");
        $total_page = ceil($total / $items_per_page);
        if ($total) {
            return array(
                'result' => $result,
                'total_page' => $total_page,
                'current_page' => $page,
            );
        } else {
            return null;
        }
    }

    public function get_save_resumes($args = array()) {
        global $wpdb;

        $default_args = array(
            'posts_per_page' => iwj_option('dashboard_items_per_page', get_option('posts_per_page', 20)),
            'search' => isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '',
        );
        $args = wp_parse_args($args, $default_args);
        $query = "SELECT s.* FROM {$wpdb->prefix}iwj_save_resums AS s JOIN {$wpdb->posts} AS p ON s.post_id = p.ID WHERE p.post_status = 'publish' AND s.user_id = '" . $this->get_id() . "'";
        if ($args['search']) {
            if (is_email($args['search'])) {
                $search_user = get_user_by('email', $args['search']);
                if ($search_user) {
                    $query .= ' AND p.post_author = "' . $search_user->ID . '"';
                } else {
                    $query .= ' AND p.post_author = "-1"';
                }
            } else {
                $query .= ' AND p.post_title LIKE "%' . $args['search'] . '%"';
            }
        }
        $total_query = "SELECT COUNT(1) FROM (${query}) AS combined_table";
        $total = $wpdb->get_var($total_query);
        $items_per_page = $args['posts_per_page'];
        $page = isset($_GET['cpage']) ? abs((int) $_GET['cpage']) : 1;
        $offset = ( $page * $items_per_page ) - $items_per_page;
        $result = $wpdb->get_results($query . " ORDER BY ID DESC LIMIT ${offset}, ${items_per_page}");
        $total_page = ceil($total / $items_per_page);
        if ($total) {
            return array(
                'result' => $result,
                'total_page' => $total_page,
                'current_page' => $page,
            );
        } else {
            return null;
        }
    }

    public function get_reviews($args = array()) {
        global $wpdb;
        $default_args = array(
            'posts_per_page' => iwj_option('dashboard_items_per_page', get_option('posts_per_page', 20)),
            'search' => isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '',
        );
        $args = wp_parse_args($args, $default_args);
        $query = "SELECT * FROM {$wpdb->prefix}iwj_reviews WHERE user_id = '" . $this->get_id() . "'";
        if ($args['search']) {
            $query .= ' AND title LIKE "%' . $args['search'] . '%"';
        }
        $total_query = "SELECT COUNT(1) FROM (${query}) AS combined_table";
        $total = $wpdb->get_var($total_query);
        $items_per_page = $args['posts_per_page'];
        $page = isset($_GET['cpage']) ? abs((int) $_GET['cpage']) : 1;
        $offset = ( $page * $items_per_page ) - $items_per_page;
        $result = $wpdb->get_results($query . " ORDER BY ID DESC LIMIT ${offset}, ${items_per_page}");
        $total_page = ceil($total / $items_per_page);
        if ($total) {
            return array(
                'result' => $result,
                'total_page' => $total_page,
                'current_page' => $page,
            );
        } else {
            return null;
        }
    }

    public function get_reviews_employer($args = array()) {
        global $wpdb;
        $default_args = array(
            'posts_per_page' => iwj_option('dashboard_items_per_page', get_option('posts_per_page', 20)),
            'search' => isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '',
        );
        $args = wp_parse_args($args, $default_args);
        $query = "SELECT r.* FROM {$wpdb->prefix}iwj_reviews AS r JOIN {$wpdb->posts} AS p ON r.item_id = p.ID WHERE p.post_status = 'publish' AND p.post_type= 'iwj_employer' AND r.status = 'approved' AND p.post_author = '" . $this->get_id() . "'";
        if ($args['search']) {
            $query .= ' AND r.title LIKE "%' . $args['search'] . '%"';
        }
        $total_query = "SELECT COUNT(1) FROM (${query}) AS combined_table";
        $total = $wpdb->get_var($total_query);
        $items_per_page = $args['posts_per_page'];
        $page = isset($_GET['cpage']) ? abs((int) $_GET['cpage']) : 1;
        $offset = ( $page * $items_per_page ) - $items_per_page;
        $result = $wpdb->get_results($query . " ORDER BY ID DESC LIMIT ${offset}, ${items_per_page}");
        $total_page = ceil($total / $items_per_page);
        if ($total) {
            return array(
                'result' => $result,
                'total_page' => $total_page,
                'current_page' => $page,
            );
        } else {
            return null;
        }
    }

    public function get_alerts($args = array()) {
        global $wpdb;

        $default_args = array(
            'posts_per_page' => iwj_option('dashboard_items_per_page', get_option('posts_per_page', 20)),
        );
        $args = wp_parse_args($args, $default_args);
        $query = "SELECT * FROM {$wpdb->prefix}iwj_alerts WHERE user_id = %d";
        $query = $wpdb->prepare($query, $this->get_id());
        $total_query = "SELECT COUNT(1) FROM (${query}) AS combined_table";
        $total = $wpdb->get_var($total_query);
        $items_per_page = $args['posts_per_page'];
        $page = isset($_GET['cpage']) ? abs((int) $_GET['cpage']) : 1;
        $offset = ( $page * $items_per_page ) - $items_per_page;
        $result = $wpdb->get_results($query . " ORDER BY ID DESC LIMIT ${offset}, ${items_per_page}");
        $total_page = ceil($total / $items_per_page);
        if ($total) {
            return array(
                'result' => $result,
                'total_page' => $total_page,
                'current_page' => $page,
            );
        } else {
            return null;
        }
    }

    public function is_followed($id) {
        global $wpdb;
        $query = "SELECT COUNT(1) FROM {$wpdb->prefix}iwj_follows WHERE post_id = %d AND user_id = %d";
        return $wpdb->get_var($wpdb->prepare($query, $id, $this->get_id()));
    }

    public function follow($id) {
        global $wpdb;
        if ($wpdb->insert($wpdb->prefix . 'iwj_follows', array('post_id' => $id, 'user_id' => $this->get_id()), array('%d', '%d'))) {
            return $wpdb->insert_id;
        }

        return false;
    }

    public function unfollow($id) {
        global $wpdb;
        if ($wpdb->delete($wpdb->prefix . 'iwj_follows', array('post_id' => $id, 'user_id' => $this->get_id()), array('%d', '%d'))) {
            return true;
        }

        return false;
    }

    public function is_saved_job($id) {
        global $wpdb;
        $query = "SELECT COUNT(1) FROM {$wpdb->prefix}iwj_save_jobs WHERE post_id = %d AND user_id = %d";
        return $wpdb->get_var($wpdb->prepare($query, $id, $this->get_id()));
    }

    public function save_job($id) {
        global $wpdb;
        if ($wpdb->insert($wpdb->prefix . 'iwj_save_jobs', array('post_id' => $id, 'user_id' => $this->get_id()), array('%d', '%d'))) {
            return $wpdb->insert_id;
        }

        return false;
    }

    public function undo_save_job($id) {
        global $wpdb;
        if ($wpdb->delete($wpdb->prefix . 'iwj_save_jobs', array('post_id' => $id, 'user_id' => $this->get_id()), array('%d', '%d'))) {
            return true;
        }

        return false;
    }

    public function check_user_review($item_id) {
        global $wpdb;
        $query = "SELECT COUNT(1) FROM {$wpdb->prefix}iwj_reviews WHERE item_id = %d AND user_id = %d";
        return $wpdb->get_var($wpdb->prepare($query, $item_id, $this->get_id()));
    }

    public function get_user_review($item_id) {
        global $wpdb;
        if ($this->check_user_review($item_id)) {
            $query = "SELECT * FROM {$wpdb->prefix}iwj_reviews WHERE item_id = %d AND user_id = %d";
            return $wpdb->get_results($wpdb->prepare($query, $item_id, $this->get_id()));
        } else {
            return null;
        }
    }

    public function can_apply() {
        // Check user don't must employer permission
        $allow = true;
        if (!user_can($this->user, 'apply_job')) {
            $allow = false;
        }

        return apply_filters('iwj_user_can_apply_job', $allow);
    }

    public function is_applied($job_id) {
        global $wpdb;
        $query = "SELECT COUNT(1) FROM {$wpdb->posts} AS p JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id WHERE p.post_type = %s AND p.post_author = %d AND pm.meta_key = %s AND pm.meta_value = %d";
        return $wpdb->get_var($wpdb->prepare($query, 'iwj_application', $this->get_id(), IWJ_PREFIX . 'job_id', $job_id));
    }

    /* public function can_apply_job( $id ) {
      $post = get_post($id);
      if(iwj_option('apply_job_mode')
      || user_can($this->user,'apply_job')
      || ($post->post_author != $this->get_id())
      || (!user_can($this->user,'create_iwj_jobs') && !$this->is_applied($id))
      ){
      return true;
      }

      return false;
      } */

    public function is_viewed_resum($id) {
        global $wpdb;
        $query = "SELECT COUNT(1) FROM {$wpdb->prefix}iwj_view_resums WHERE post_id = %d AND user_id = %d";
        return $wpdb->get_var($wpdb->prepare($query, $id, $this->get_id()));
    }

    public function can_view_resum($id) {
        $post = get_post($id);
        if (iwj_option('view_free_resum') || user_can($this->user, 'privilege_view_resum') || ($post->post_author == $this->get_id()) || (user_can($this->user, 'create_iwj_jobs') && $this->is_viewed_resum($id))
        ) {
            return true;
        }

        return false;
    }

    public function is_saved_resum($id) {
        global $wpdb;
        $query = "SELECT COUNT(1) FROM {$wpdb->prefix}iwj_save_resums WHERE post_id = %d AND user_id = %d";
        return $wpdb->get_var($wpdb->prepare($query, $id, $this->get_id()));
    }

    public function view_resum($id) {
        global $wpdb;
        if ($wpdb->insert($wpdb->prefix . 'iwj_view_resums', array('post_id' => $id, 'user_id' => $this->get_id()), array('%d', '%d'))) {
            return $wpdb->insert_id;
        }

        return false;
    }

    public function delete_view_resum($id) {
        global $wpdb;
        if ($wpdb->delete($wpdb->prefix . 'iwj_view_resums', array('post_id' => $id, 'user_id' => $this->get_id()), array('%d', '%d'))) {
            return true;
        }

        return false;
    }

    public function save_resum($id) {
        global $wpdb;
        if ($wpdb->insert($wpdb->prefix . 'iwj_save_resums', array('post_id' => $id, 'user_id' => $this->get_id()), array('%d', '%d'))) {
            return $wpdb->insert_id;
        }

        return false;
    }

    public function delete_save_resum($id) {
        global $wpdb;
        if ($wpdb->delete($wpdb->prefix . 'iwj_save_resums', array('post_id' => $id, 'user_id' => $this->get_id()), array('%d', '%d'))) {
            return true;
        }

        return false;
    }

    public function undo_save_resum($id) {
        global $wpdb;
        if ($wpdb->delete($wpdb->prefix . 'iwj_save_resums', array('post_id' => $id, 'user_id' => $this->get_id()), array('%d', '%d'))) {
            return true;
        }

        return false;
    }

    public function get_cv() {
        if ($this->is_candidate()) {
            $candidate = $this->get_candidate();
            if ($candidate) {
                return $candidate->get_cv();
            }
        }

        return false;
    }

    public function get_cover_letter() {
        if ($this->is_candidate()) {
            $candidate = $this->get_candidate();
            if ($candidate) {
                return $candidate->get_cover_letter();
            }
        }

        return false;
    }

    function is_verified() {
        $verify_code = get_user_meta($this->get_id(), IWJ_PREFIX . 'verify_code', true);
        if (empty($verify_code)) {
            return true;
        }

        return false;
    }

    function get_activation_url($new_code = true) {
        if ($new_code) {
            $code = wp_generate_password(20, false);
            update_user_meta($this->get_id(), IWJ_PREFIX . 'verify_code', $code);
        } else {
            $code = get_user_meta($this->get_id(), IWJ_PREFIX . 'verify_code', true);
        }

        $verify_url = home_url('/');
        $verify_url = add_query_arg(array('iwj_verify_account' => $this->get_id(), 'verify_code' => $code), $verify_url);

        return $verify_url;
    }

    function update_profile() {
        $your_name = sanitize_text_field($_POST['your_name']);
        $description = stripslashes(sanitize_textarea_field($_POST['description']));
        $thumbnail = sanitize_text_field($_POST['thumbnail']);
        $email = sanitize_email($_POST['email']);
        $website = sanitize_text_field($_POST['website']);

        $update_user_data = array(
            'ID' => $this->get_id(),
            'display_name' => $your_name,
            'description' => $description,
            'user_url' => $website,
        );

        if ($your_name) {
            list($first_name, $last_name) = iwj_parse_name($your_name);
            $update_user_data['first_name'] = $first_name;
            $update_user_data['last_name'] = $last_name;
        }

        if ($email && $email != $this->get_email()) {
            $update_user_data['user_email'] = $email;
        }

        wp_update_user($update_user_data);

        if ($thumbnail) {
            update_user_meta($this->get_id(), IWJ_PREFIX . 'avatar', $thumbnail);
        }

        do_action('iwj_update_user_profile', $this->get_id());

        return true;
    }

    function get_current_plan_orders_query($args = array()) {
        $paged = isset($_GET['cpage']) ? $_GET['cpage'] : '1';
        $status = isset($_GET['order_status']) && $_GET['order_status'] ? $_GET['order_status'] : 'iwj-completed';
        $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';
        $default_args = array(
            'post_type' => 'iwj_order',
            'post_parent' => 0,
            'post_status' => $status,
            'posts_per_page' => iwj_option('dashboard_items_per_page', get_option('posts_per_page', 20)),
            'author' => $this->get_id(),
            'paged' => $paged,
            'meta_query' => array(
                array(
                    'key' => IWJ_PREFIX . 'type',
                    'value' => 'plan',
                    'compare' => '='
                ),
                array(
                    'key' => IWJ_PREFIX . 'package_id',
                    'value' => $this->get_plan_id(),
                    'compare' => '='
                ),
            )
        );

        if ($orderby) {
            $orderby = explode("_", $orderby);
            $default_args['orderby'] = $orderby[0];
            $default_args['order'] = strtoupper($orderby[1]);
        }

        $args = wp_parse_args($args, $default_args);

        return new WP_Query($args);
    }

    function get_cancel_subscription_url() {
        return add_query_arg(array('iwj-cancel-subscription' => '1'), iwj_get_page_permalink('dashboard'));
    }

    function cancel_subscription($keep_gateways = '') {
        $keep_gateways = (array) $keep_gateways;
        $payment_gateways = IWJ()->payment_gateways->get_available_payment_gateways();
        if ($payment_gateways) {
            foreach ($payment_gateways as $payment_gateway) {
                if ($keep_gateways && in_array($payment_gateway->id, $keep_gateways)) {
                    continue;
                }
                $payment_gateway->cancel_subscription($this->get_id());
            }
        }
    }

    function has_subscription() {
        $payment_gateways = IWJ()->payment_gateways->get_available_payment_gateways();
        if ($payment_gateways) {
            foreach ($payment_gateways as $payment_gateway) {
                if ($payment_gateway->has_subscription($this->get_id())) {
                    return true;
                }
            }
        }

        return false;
    }

}

IWJ_User::init();
