<?php

class IWJ_Employer {

    static $cache;
    public $post;

    public function __construct($post) {
        $this->post = $post;
    }

    static function get_employer($post = null, $force = false) {
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
                self::$cache[$post_id] = new IWJ_Employer($post);
            }

            return self::$cache[$post_id];
        }

        return null;
    }

    function get_id() {

        return $this->post->ID;
    }

    function get_title($orgiginal = false) {
        if ($orgiginal) {
            return $this->post->post_title;
        }

        return get_the_title($this->get_id());
    }

    function get_display_name() {

        return $this->get_title();
    }

    function get_description($filter = false) {

        $content = $this->post->post_content;

        if ($filter) {
            $content = strip_shortcodes($content);
            $content = apply_filters('the_content', $content);
        }

        return $content;
    }

    function get_short_description() {

        return stripslashes($this->post->post_excerpt);
    }

    function get_thumbnail($size_2x = false) {
        $width = 46;
        $height = 46;
        if ($size_2x) {
            $width = $width * 2;
            $height = $height * 2;
        }
        $images = wp_get_attachment_image_src($this->post, 'full');
        if (empty($images[0])) {
            $thumnail_url = iwj_get_placeholder_image(array($width, $height));
        } else {
            $thumnail_url = iwj_resize_image($images[0], $width, $height, true);
        }

        return $thumnail_url;
    }

    function get_status() {

        return $this->post->post_status;
    }

    function permalink() {
        $link = get_the_permalink($this->get_id());
        if (in_array($this->get_status(), array('publish'))) {
            return $link;
        } else {
            return add_query_arg('preview', 'true', $link);
        }
    }

    public function admin_link() {
        return get_admin_url() . 'post.php?post=' . $this->get_id() . '&action=edit';
    }

    public function get_edit_url() {
        $dashboard = iwj_get_page_permalink('dashboard');
        return add_query_arg(array('iwj_tab' => 'profile'), $dashboard);
    }

    function get_avatar($size = '') {
        $author_id = $this->get_author_id();
        return iwj_get_avatar($author_id, $size);
    }

    function get_headline() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'headline', true);
    }

    function get_views() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'views', true);
    }

    function get_size() {
        $size = wp_get_post_terms($this->get_id(), 'iwj_size');
        if ($size) {
            $size = $size[0];
            return $size->name;
        }

        return '';
    }

    function get_map() {
        $map = get_post_meta($this->get_id(), IWJ_PREFIX . 'map', true);
        if ($map) {
            return explode(",", $map);
        }

        return null;
    }

    function get_address() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'address', true);
    }

    function get_category() {
        $cats = $this->get_categories();
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

    function get_category_titles() {
        $category_titles = array();
        $cats = $this->get_categories();
        if ($cats) {
            foreach ($cats as $cat) {
                $category_titles[] = $cat->name;
            }
        }

        return $category_titles;
    }

    function get_reason() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'reason', true);
    }

    function get_phone() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'phone', true);
    }

    function get_website() {
        $author = $this->get_author();
        if ($author) {
            return $author->get_website();
        }

        return '';
    }

    function get_locations() {
        $locations = wp_get_post_terms($this->get_id(), 'iwj_location');

        return $locations;
    }

    function get_locations_links() {
        $location_links = array();
        $locations = $this->get_locations();
        if ($locations) {
            $locations = array_reverse($locations);
            foreach ($locations as $location) {
                $location_links[] = '<a href="' . get_term_link($location->term_id, 'iwj_location') . '">' . $location->name . '</a>';
            }
        }

        if ($location_links) {
            return implode(', ', $location_links);
        } else {
            return '';
        }
    }

    function founded_date() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'founded_date', true);
    }

    function get_social_media() {
        $social_media = array();
        $social_media['facebook'] = get_post_meta($this->get_id(), IWJ_PREFIX . 'facebook', true);
        $social_media['twitter'] = get_post_meta($this->get_id(), IWJ_PREFIX . 'twitter', true);
        $social_media['google_plus'] = get_post_meta($this->get_id(), IWJ_PREFIX . 'google_plus', true);
        $social_media['youtube'] = get_post_meta($this->get_id(), IWJ_PREFIX . 'youtube', true);
        $social_media['vimeo'] = get_post_meta($this->get_id(), IWJ_PREFIX . 'vimeo', true);
        $social_media['linkedin'] = get_post_meta($this->get_id(), IWJ_PREFIX . 'linkedin', true);
        
        return apply_filters('iwj_get_employer_social', $social_media, $this->get_id());
    }

    function get_email() {
        $user = IWJ_User::get_user($this->get_author_id());
        return $user->get_email();
    }

    function get_gallery() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'gallery');
    }

    function get_cover_image() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'cover_image', true);
    }

    function get_video() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'video');
    }

    function get_video_poster() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'video_poster');
    }

    function get_template_detail_version() {
        return get_post_meta($this->get_id(), IWJ_PREFIX . 'template_version', true);
    }

    function can_update() {
        if (get_current_user_id() == $this->get_author_id()) {
            return true;
        }

        return true;
    }

    function update() {
        $title = sanitize_text_field($_POST['company_name']);
        $description = stripslashes(wp_kses_post($_POST['description']));
        $short_description = stripslashes(sanitize_textarea_field($_POST['short_description']));
        //$thumbnail = sanitize_text_field($_POST['thumbnail']);
        $email = sanitize_email($_POST['email']);
        $website = sanitize_text_field($_POST['website']);
        $user = IWJ_User::get_user($this->get_author_id());

        $update_user_data = array(
            'ID' => $this->get_author_id(),
            'display_name' => $title,
            'user_url' => $website,
        );

        if ($title) {
            list($first_name, $last_name) = iwj_parse_name($title);
            $update_user_data['first_name'] = $first_name;
            $update_user_data['last_name'] = $last_name;
        }

        if ($email && $email != $user->get_email()) {
            $update_user_data['user_email'] = $email;
        }

        wp_update_user($update_user_data);

        /* if($thumbnail){
          set_post_thumbnail($this->get_id(), $thumbnail);
          update_user_meta($user->get_id(), IWJ_PREFIX.'avatar', $thumbnail);
          } */

        wp_update_post(array(
            'ID' => $this->get_id(),
            'post_title' => $title,
            'post_name' => sanitize_title($title),
            'post_content' => $description,
            'post_excerpt' => $short_description,
        ));

        $fields = array(
            array(
                'id' => IWJ_PREFIX . 'headline',
                'type' => 'text',
            ),
            array(
                'id' => IWJ_PREFIX . 'phone',
                'type' => 'text',
            ),
            array(
                'id' => IWJ_PREFIX . 'founded_date',
                'type' => 'text',
            ),
            array(
                'id' => IWJ_PREFIX . 'address',
                'type' => 'text',
            ),
            array(
                'id' => 'size',
                'type' => 'taxonomy',
                'options' => array(
                    'taxonomy' => 'iwj_size'
                ),
                'multiple' => false,
            ),
            array(
                'id' => 'categories',
                'type' => 'taxonomy',
                'options' => array(
                    'taxonomy' => 'iwj_cat'
                ),
                'multiple' => true,
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
                'name' => '',
                'id' => IWJ_PREFIX . 'cover_image',
                'type' => 'image_single',
            ),
            array(
                'name' => '',
                'id' => IWJ_PREFIX . 'gallery',
                'type' => 'image_upload',
            ),
            array(
                'name' => '',
                'id' => IWJ_PREFIX . 'video',
                'type' => 'url',
            ),
            array(
                'name' => '',
                'id' => IWJ_PREFIX . 'video_poster',
                'type' => 'image_single',
            ),
            array(
                'id' => IWJ_PREFIX . 'facebook',
                'type' => 'text',
            ),
            array(
                'id' => IWJ_PREFIX . 'twitter',
                'type' => 'text',
            ),
            array(
                'id' => IWJ_PREFIX . 'google_plus',
                'type' => 'text',
            ),
            array(
                'id' => IWJ_PREFIX . 'youtube',
                'type' => 'text',
            ),
            array(
                'id' => IWJ_PREFIX . 'vimeo',
                'type' => 'text',
            ),
            array(
                'id' => IWJ_PREFIX . 'linkedin',
                'type' => 'text',
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

        if (iwj_option('show_gdpr_on_profile')) {
            $fields[] = array(
                'id' => IWJ_PREFIX . 'gdpr_profile',
                'type' => 'checkbox',
            );
        }

        $post_id = $this->get_id();
        foreach ($fields as $field) {
            $field = IWJMB_Field::call('normalize', $field);

            $single = $field['clone'] || !$field['multiple'];
            $old = IWJMB_Field::call($field, 'raw_post_meta', $post_id);
            $new = isset($_POST[$field['id']]) ? $_POST[$field['id']] : ( $single ? '' : array() );

            // Allow field class change the value
            if ($field['clone']) {
                $new = IWJMB_Clone::value($new, $old, $post_id, $field);
            } else {
                $new = IWJMB_Field::call($field, 'value', $new, $old, $post_id);
                $new = IWJMB_Field::call($field, 'sanitize_value', $new);
            }

            // Call defined method to save meta value, if there's no methods, call common one
            IWJMB_Field::call($field, 'save_post', $new, $old, $post_id);
        }

        do_action('iwj_update_employer_profile', $post_id);

        return true;
    }

    public function change_status($status, $send_email = true) {
        global $wpdb;

        $sql = "UPDATE {$wpdb->posts} SET post_status = %s WHERE ID = %d";
        $wpdb->query($wpdb->prepare($sql, $status, $this->get_id()));

        $old_status = $this->get_status();
        if ($old_status == 'publish' || $status == 'publish') {
            delete_transient('iwj_count_employers');
        }

        clean_post_cache($this->get_id());

        //send email
        if ($send_email) {
            if ($status == 'publish') {
                IWJ_Email::send_email('approved_profile', $this);
            } elseif ($status == 'pending') {
                IWJ_Email::send_email('review_profile', $this);
            } elseif ($status == 'iwj-incomplete') {
                IWJ_Email::send_email('rejected_profile', $this);
            }
        }
    }

    public function is_active() {
        if ($this->get_status() == 'publish') {
            return true;
        }

        return false;
    }

    public function is_incomplete() {
        if ($this->get_status() == 'iwj-incomplete') {
            return true;
        }

        return false;
    }

    public function is_pending() {
        if ($this->get_status() == 'pending') {
            return true;
        }

        return false;
    }

    function get_author_id() {
        return $this->post->post_author;
    }

    function get_author() {
        return IWJ_User::get_user($this->get_author_id());
    }

    function get_admin_url() {
        return get_admin_url() . 'post.php?post=' . $this->get_id() . '&action=edit';
    }

    static function get_status_array() {
        return array(
            'publish' => __('Publish', 'iwjob'),
            'pending' => __('Pending', 'iwjob'),
            'iwj-incomplete' => __('Incomplete', 'iwjob'),
        );
    }

    static function get_status_title($status) {
        $status_arr = self::get_status_array();
        if (isset($status_arr[$status])) {
            return $status_arr[$status];
        }

        return '';
    }

    static function remove_employer_reference($id) {
        if ($id && get_post_type($id) == 'iwj_employer') {
            $post = get_post($id);
            if ($post->post_status == 'publish') {
                delete_transient('iwj_count_employers');
            }

            global $wpdb;

            $wpdb->delete($wpdb->prefix . 'iwj_follows', array('post_id' => $id,), array('%d'));

            delete_user_meta($post->post_author, IWJ_PREFIX . 'employer_post');
        }
    }

}
