<?php
class IWJ_Candidate{
    static $cache;
    public $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    static function get_candidate($post = null, $force = false){
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
                self::$cache[$post_id] = new IWJ_Candidate($post);
            }

            return self::$cache[$post_id];
        }

        return null;
    }

    function get_id(){

        return $this->post->ID;
    }

    function get_title($orgiginal = false){
        if($orgiginal){
            return $this->post->post_title;
        }

        return get_the_title($this->get_id());
    }

    function get_display_name(){

        return $this->get_title();
    }

    function get_description($filter = false){

        $content = $this->post->post_content;

        if($filter){
            $content = strip_shortcodes($content);
            $content = apply_filters('the_content', $content);
        }

        return $content;
    }

    function get_thumbnail($size_2x = false){
	    $width  = 46;
	    $height = 46;
	    if ( $size_2x ) {
		    $width  = $width * 2;
		    $height = $height * 2;
	    }
	    $images = wp_get_attachment_image_src( $this->post, 'full' );
	    if ( empty( $images[0] ) ) {
		    $thumnail_url = iwj_get_placeholder_image( array( $width, $height ) );
	    } else {
		    $thumnail_url = iwj_resize_image( $images[0], $width, $height, true );
	    }

	    return $thumnail_url;
    }

    function permalink(){
        $link = get_the_permalink($this->get_id());
        if(in_array($this->get_status(), array('publish'))){
            return $link;
        }
        else{
            return add_query_arg('preview', 'true', $link);
        }
    }

    public function admin_link(){
        return get_admin_url().'post.php?post='.$this->get_id().'&action=edit';
    }

    public function get_edit_url(){
        $dashboard = iwj_get_page_permalink('dashboard');
        return add_query_arg(array('iwj_tab'=>'profile'), $dashboard);
    }

    function get_avatar($size = ''){
        $author_id = $this->get_author_id();
        return iwj_get_avatar($author_id, $size);
    }

    function get_headline(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'headline', true);
    }

    function get_birthday(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'birthday', true);
    }

    function get_age(){
        $birthday = $this->get_birthday();
        if($birthday){
            return date('Y') - date('Y', $birthday);
        }
        return '';
    }

    function get_views(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'views', true);
    }

    function get_type(){
        if(iwj_option('disable_type')){
            return null;
        }

        $types = wp_get_post_terms( $this->get_id(), 'iwj_type');
        if($types && !is_wp_error($types)){
            return $types[0];
        }

        return null;
    }

    function get_address(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'address', true);
    }

    function get_gender(){
	    return get_post_meta( $this->get_id(), IWJ_PREFIX . 'gender', true );
    }

    public function get_featured(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'featured', true) ? true : false;
    }

    public function is_featured(){
        $featured = $this->get_featured();
        return $featured;
    }

	function get_languages() {
		if ( iwj_option( 'disable_language' ) ) {
			return null;
		}
		$languages = get_post_meta( $this->get_id(), IWJ_PREFIX . 'languages' );
		if ( $languages ) {
			return array_filter($languages);
		}

		return null;
	}

    function get_reason(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'reason', true);
    }

    function get_full_address(){
        $address = array();
        if($this->get_address()){
            $address[] = $this->get_address();
        }

        $locations = $this->get_locations();
        if($locations){
            $locations = array_reverse($locations);
            foreach ($locations as $location){
                $address[] = $location->name;
            }
        }

        if($address){
            return implode(', ', $address);
        }
        else{
            return '';
        }
    }

    function get_public_account(){
	    $public_account = get_post_meta( $this->get_id(), IWJ_PREFIX . 'public_account' );
	    if (!$public_account){
	    	return true;
	    }else{
	    	if(in_array('1',$public_account)){
	    		return true;
		    }else{
	    		return false;
		    }
	    }
    }

    function get_category(){
        $cats = $this->get_categories();
        if($cats){
            return $cats[0];
        }

        return null;
    }

    function get_categories(){
        $cats = wp_get_post_terms( $this->get_id(), 'iwj_cat');
        return $cats;
    }

    function get_categories_links(){
        $category_links = array();
        $categories = $this->get_categories();
        if($categories){
            foreach ($categories as $category){
                $category_links[] = '<a href="'.get_term_link($category->term_id, 'iwj_cat').'">'.$category->name.'</a>';
            }
        }

        if($category_links){
            return implode(', ', $category_links);
        }
        else{
            return '';
        }
    }

    function get_category_titles(){
        $category_titles = array();
        $cats = $this->get_categories();
        if($cats){
            foreach ($cats as $cat){
                $category_titles[] = $cat->name;
            }
        }

        return $category_titles;
    }


    function get_skills(){
        if(iwj_option('disable_skill')){
            return null;
        }

        $skills = wp_get_post_terms( $this->get_id(), 'iwj_skill');
        if($skills && !is_wp_error($skills)){
            return $skills;
        }

        return null;
    }

    function get_skills_links(){
        $skill_links = array();
        $skills = $this->get_skills();
        if($skills){
            foreach ($skills as $skill){
                $skill_links[] = '<a href="'.get_term_link($skill->term_id, 'iwj_skill').'">'.$skill->name.'</a>';
            }
        }

        if($skill_links){
            return implode(', ', $skill_links);
        }
        else{
            return '';
        }
    }

    function get_level(){
        if(iwj_option('disable_level')){
            return null;
        }

        $levels = wp_get_post_terms( $this->get_id(), 'iwj_level');
        if($levels && !is_wp_error($levels)){
            return $levels[0];
        }
        return null;
    }

    function get_level_link(){
        $level = $this->get_level();
        if($level){
            return '<a href="'.get_term_link($level->term_id, 'iwj_level').'">'.$level->name.'</a>';
        }
        return null;
    }

    function get_levels(){
        if(iwj_option('disable_level')){
            return null;
        }

        $levels = wp_get_post_terms($this->get_id(), 'iwj_level');

        return $levels;
    }

    function get_levels_links(){
        $level_links = array();
        $levels = $this->get_levels();
        if($levels){
            foreach ($levels as $level){
                $level_links[] = '<a href="'.get_term_link($level->term_id, 'iwj_level').'">'.$level->name.'</a>';
            }
        }

        if($level_links){
            return implode(', ', $level_links);
        }
        else{
            return '';
        }
    }

    function get_types(){
        if(iwj_option('disable_type')){
            return null;
        }

        $types = wp_get_post_terms($this->get_id(), 'iwj_type');

        return $types;
    }

    function get_types_links(){
        $type_links = array();
        $types = $this->get_types();
        if($types){
            foreach ($types as $type){
                $type_links[] = '<a href="'.get_term_link($type->term_id, 'iwj_type').'">'.$type->name.'</a>';
            }
        }

        if($type_links){
            return implode(', ', $type_links);
        }
        else{
            return '';
        }
    }

    function get_locations(){
        $locations = wp_get_post_terms($this->get_id(), 'iwj_location');

        return $locations;
    }

    function get_locations_links(){
        $location_links = array();
        $locations = $this->get_locations();
        if($locations){
            $locations = array_reverse($locations);
            foreach ($locations as $location){
                $location_links[] = '<a href="'.get_term_link($location->term_id, 'iwj_location').'">'.$location->name.'</a>';
            }
        }

        if($location_links){
            return implode(', ', $location_links);
        }
        else{
            return '';
        }
    }

    function get_experience_text(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'experience_text', true);
    }

    function get_salary_from(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'salary_from', true);
    }

    function get_salary_to(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'salary_to', true);
    }

    function get_phone(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'phone', true);
    }

    function get_website(){
        $author = $this->get_author();
        if($author){
            return $author->get_website();
        }

        return '';
    }

    function get_map(){
        $map = get_post_meta($this->get_id(), IWJ_PREFIX.'map', true);
        if($map){
            return explode(",", $map);
        }

        return null;
    }

    function get_social_media(){
        $social_media = array();
        $social_media['facebook'] = get_post_meta($this->get_id(), IWJ_PREFIX.'facebook', true);
        $social_media['twitter'] = get_post_meta($this->get_id(), IWJ_PREFIX.'twitter', true);
        $social_media['google_plus'] = get_post_meta($this->get_id(), IWJ_PREFIX.'google_plus', true);
        $social_media['youtube'] = get_post_meta($this->get_id(), IWJ_PREFIX.'youtube', true);
        $social_media['vimeo'] = get_post_meta($this->get_id(), IWJ_PREFIX.'vimeo', true);
        $social_media['linkedin'] = get_post_meta($this->get_id(), IWJ_PREFIX.'linkedin', true);
        
        return apply_filters('iwj_get_candidate_social', $social_media, $this->get_id());
    }

    function get_email(){
        $user = IWJ_User::get_user($this->get_author_id());
        return $user->get_email();
    }

    function get_status(){
        return $this->post->post_status;
    }

    function can_update(){
        if(get_current_user_id() == $this->get_author_id()){
            return true;
        }

        return false;
    }

    function get_author_id(){
        return $this->post->post_author;
    }

    function get_author(){
        return IWJ_User::get_user($this->get_author_id());
    }

    function get_experience(){
        $_experiences = get_post_meta($this->get_id(), IWJ_PREFIX.'experience', true);
        $experiences = array();
        if($_experiences){
            foreach ($_experiences as $experience){
                $empty = array_filter($experience);
                if(!empty($empty)){
                    $experiences[] = $experience;
                }
            }
        }

        return $experiences;
    }

    function get_education(){
        $_educations = get_post_meta($this->get_id(), IWJ_PREFIX.'education', true);
        $educations = array();
        if($_educations){

            foreach ($_educations as $education){
                $empty = array_filter($education);
                if(!empty($empty)){
                    $educations[] = $education;
                }
            }
        }

        return $educations;
    }

    function get_skill_showcase(){
        $_skills = get_post_meta($this->get_id(), IWJ_PREFIX.'skill_showcase', true);
        $skills = array();
        if($_skills){

            foreach ($_skills as $skill){
                $empty = array_filter($skill);
                if(!empty($empty)){
                    $skills[] = $skill;
                }
            }
        }

        return $skills;
    }

    function get_gallery(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'gallery');
    }

    function get_cover_image(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'cover_image');
    }

    function get_video(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'video');
    }

    function get_video_poster(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'video_poster');
    }

    function get_template_detail_version(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'template_version', true);
    }

    function get_award(){
        $_awars = get_post_meta($this->get_id(), IWJ_PREFIX.'award', true);
        $awars = array();
        if($_awars){

            foreach ($_awars as $awar){
                $empty = array_filter($awar);
                if(!empty($empty)){
                    $awars[] = $awar;
                }
            }
        }

        return $awars;
    }

    function get_cv(){
        $cv = get_post_meta($this->get_id(), IWJ_PREFIX.'cv', true);

        if ( ! $path = get_attached_file( $cv ) ) {
            return false;
        }

        return wp_parse_args( array(
            'ID'    => $cv,
            'name'  => basename( $path ),
            'path'  => $path,
            'url'   => wp_get_attachment_url( $cv ),
            'title' => get_the_title( $cv ),
        ), wp_get_attachment_metadata( $cv ) );
    }

    function get_cover_letter(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'cover_letter', true);
    }

    function update(){
        $description = stripslashes(sanitize_textarea_field($_POST['description']));
        //$thumbnail = sanitize_text_field($_POST['thumbnail']);
        $email = sanitize_email($_POST['email']);
        $your_name = sanitize_text_field($_POST['your_name']);
        $website = sanitize_text_field($_POST['website']);
        $user = IWJ_User::get_user($this->get_author_id());

        $update_user_data = array(
            'ID' => $this->get_author_id(),
            'display_name' => $your_name,
            'user_url' => $website,
        );

        if($your_name){
            list($first_name, $last_name) = iwj_parse_name($your_name);
            $update_user_data['first_name'] = $first_name;
            $update_user_data['last_name'] = $last_name;
        }

        if($email && $email != $user->get_email()){
            $update_user_data['user_email'] = $email;
        }

        wp_update_user($update_user_data);

        /*if($thumbnail){
            set_post_thumbnail($this->get_id(), $thumbnail);
            update_user_meta($user->get_id(), IWJ_PREFIX.'avatar', $thumbnail);
        }*/

        wp_update_post(array(
            'ID'=>$this->get_id(),
            'post_title'=>$your_name,
            'post_name'=> sanitize_title($your_name),
            'post_content'=>$description,
        ));

        $fields = array(
            array(
                'id'   => IWJ_PREFIX.'phone',
                'type' => 'text',
            ),
            array(
                'id'   => IWJ_PREFIX.'address',
                'type' => 'text',
            ),
            array(
                'id'   => IWJ_PREFIX.'map',
                'type' => 'map',
                'address_field' => IWJ_PREFIX.'address',
            ),
            array(
                'id' => IWJ_PREFIX.'headline',
                'type' => 'text',
                'required' => true
            ),
	        array(
		        'id'   => IWJ_PREFIX.'gender',
		        'type' => 'select',
		        'options' => iwj_genders(),
	        ),
	        array(
		        'id'   => IWJ_PREFIX.'languages',
		        'type' => 'select_advanced',
		        'options' => iwj_get_available_languages(),
		        'multiple' => true,
	        ),
            array(
                'id' => IWJ_PREFIX.'birthday',
                'type' => 'date',
                'required' => true
            ),
	        array(
		        'id' => IWJ_PREFIX.'public_account',
		        'type' => 'select',
		        'options' => array(
			        '1' => __( 'Yes', 'iwjob' ),
			        '0' => __( 'No', 'iwjob' )
		        ),
		        'required' => true
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
                'id'   => IWJ_PREFIX.'experience_text',
                'type' => 'text',
            ),
            array(
                'id'   => IWJ_PREFIX.'salary_from',
                'type' => 'text',
            ),
            array(
                'id'   => IWJ_PREFIX.'salary_to',
                'type' => 'text',
            ),
            array(
                'id'   => IWJ_PREFIX.'currency',
                'type' => 'select',
                'options' => iwj_get_currencies(),
                'std' => iwj_get_currency(),
            ),
            array(
                'id'   => IWJ_PREFIX.'cv',
                'type' => 'cv',
                'is_profile' => true
            ),
            array(
                'id'   => IWJ_PREFIX.'education',
                'type' => 'group',
                'clone' => true,
                'sort_clone' => true,
                'fields' => array(
                    array(
                        'id'   => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'id'   => 'school_name',
                        'type' => 'text',
                    ),
                    array(
                        'id'   => 'date',
                        'type' => 'text',
                    ),
                    array(
                        'id'   => 'description',
                        'type' => 'textarea',
                    ),
                )
            ),
            array(
                'id'   => IWJ_PREFIX.'experience',
                'type' => 'group',
                'clone' => true,
                'sort_clone' => true,
                'fields' => array(
                    array(
                        'id'   => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'id'   => 'company',
                        'type' => 'text',
                    ),
                    array(
                        'id'   => 'date',
                        'type' => 'text',
                    ),
                    array(
                        'id'   => 'description',
                        'type' => 'textarea',
                    ),
                )
            ),
            array(
                'id'   => IWJ_PREFIX.'skill_showcase',
                'type' => 'group',
                'clone' => true,
                'sort_clone' => true,
                'fields' => array(
                    array(
                        'id'   => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'id'   => 'value',
                        'type' => 'text',
                    ),
                )
            ),
            array(
                'id'   => IWJ_PREFIX.'award',
                'type' => 'group',
                'clone' => true,
                'sort_clone' => true,
                'fields' => array(
                    array(
                        'id'   => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'id'   => 'year',
                        'type' => 'text',
                    ),
                    array(
                        'id'   => 'description',
                        'type' => 'textarea',
                    ),
                )
            ),
            array(
                'id'   => IWJ_PREFIX.'gallery',
                'type' => 'image_upload',
            ),
            array(
                'name' => '',
                'id'   => IWJ_PREFIX.'cover_image',
                'type' => 'image_single',
            ),
            array(
                'name' => '',
                'id'   => IWJ_PREFIX.'video',
                'type' => 'url',
            ),
            array(
                'name' => '',
                'id'   => IWJ_PREFIX.'video_poster',
                'type' => 'image_single',
            ),
            array(
                'id'   => IWJ_PREFIX.'cover_letter',
                'type' => 'wysiwyg',
            ),
            array(
                'id'   => IWJ_PREFIX.'facebook',
                'type' => 'text',
            ),
            array(
                'id'   => IWJ_PREFIX.'twitter',
                'type' => 'text',
            ),
            array(
                'id'   => IWJ_PREFIX.'google_plus',
                'type' => 'text',
            ),
            array(
                'id'   => IWJ_PREFIX.'youtube',
                'type' => 'text',
            ),
            array(
                'id'   => IWJ_PREFIX.'vimeo',
                'type' => 'text',
            ),
            array(
                'id'   => IWJ_PREFIX.'linkedin',
                'type' => 'text',
            ),
        );

        if(!iwj_option('disable_level')) {
            $fields[] = array(
                'id' => 'levels',
                'type' => 'taxonomy',
                'options' => array(
                    'taxonomy' => 'iwj_level'
                ),
                'multiple' => true,
            );
        }
        if(!iwj_option('disable_type')) {
            $fields[] = array(
                'id' => 'types',
                'type' => 'taxonomy',
                'options' => array(
                    'taxonomy' => 'iwj_type'
                ),
                'multiple' => true,
            );
        }

        if(!iwj_option('auto_detect_location')){
            $fields[] = array(
                'id'   => 'locations',
                'type' => 'taxonomy',
                'options' => array(
                    /*'type' => 'select_tree',*/
                    'taxonomy' => 'iwj_location'
                ),
            );
        }

	    if(iwj_option( 'show_gdpr_on_profile' )){
		    $fields[] = array(
			    'id'   => IWJ_PREFIX.'gdpr_profile',
			    'type' => 'checkbox',
		    );
	    }

        $post_id = $this->get_id();

        foreach ($fields as $field){
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

        if(!iwj_option('disable_skill') && isset($_POST[IWJ_PREFIX . 'skill'])) {
            $skills = $_POST[IWJ_PREFIX . 'skill'];
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

        do_action('iwj_update_candidate_profile', $post_id);

        return true;
    }

    public function change_status($status, $send_email = true){
        global $wpdb;

        $sql = "UPDATE {$wpdb->posts} SET post_status = %s WHERE ID = %d";
        $wpdb->query($wpdb->prepare($sql, $status, $this->get_id()));

        $old_status = $this->get_status();
        if($old_status == 'publish' || $status == 'publish'){
            delete_transient( 'iwj_count_candidates' );
        }

        clean_post_cache( $this->get_id() );

        //send email
        if($send_email){
            if($status == 'publish'){
                IWJ_Email::send_email('approved_profile', $this);
            }elseif($status == 'pending'){
                IWJ_Email::send_email('review_profile', $this);

            }elseif($status == 'iwj-incomplete'){
                IWJ_Email::send_email('rejected_profile', $this);
            }
        }
    }

    public function is_active(){
        if($this->get_status() == 'publish'){
            return true;
        }

        return false;
    }

    public function is_incomplete(){
        if($this->get_status() == 'iwj-incomplete'){
            return true;
        }

        return false;
    }
    public function is_pending(){
        if($this->get_status() == 'pending'){
            return true;
        }

        return false;
    }

    function get_admin_url() {

        $action = '&action=edit';

        $post_type_object = get_post_type_object( $this->post->post_type );

        if ( $post_type_object->_edit_link ) {
            $link = admin_url( sprintf( $post_type_object->_edit_link . $action, $this->get_id() ) );
        } else {
            $link = '';
        }

        return $link;
    }

    static function get_status_array(){
        return array(
            'publish' => __('Publish', 'iwjob'),
            'pending' => __('Pending', 'iwjob'),
            'iwj-incomplete' => __('Incomplete', 'iwjob'),
        );
    }

    static function get_status_title($status){
        $status_arr = self::get_status_array();
        if(isset($status_arr[$status])){
            return $status_arr[$status];
        }

        return '';
    }


    static function remove_candidate_reference($candidate_id){
        if($candidate_id && get_post_type($candidate_id) == 'iwj_candidate'){
            if(get_post_status() == 'publish'){
                delete_transient( 'iwj_count_candidates');
            }
            global $wpdb;
            $wpdb->delete( $wpdb->prefix.'iwj_view_resums', array( 'post_id' => $candidate_id,), array( '%d' ) );
            $wpdb->delete( $wpdb->prefix.'iwj_save_resums', array( 'post_id' => $candidate_id,), array( '%d' ) );

            return true;
        }

        return false;
    }
}
