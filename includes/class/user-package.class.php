<?php
class IWJ_User_Package{
    static $cache = array();

    public $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    static function get_user_package($post = null, $force = false){
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
                self::$cache[$post_id] = new IWJ_User_Package($post);
            }

            return self::$cache[$post_id];
        }

        return null;
    }
    
    public function get_id(){
        return $this->post->ID;
    }

    public function get_title(){
        return get_the_title($this->post->ID);
    }

    public function get_package_title(){
        return get_the_title($this->get_package_id());
    }

    public function get_description(){

        return $this->post->post_content;
    }

    public function get_package_id(){

        return get_post_meta($this->get_id(), IWJ_PREFIX.'package_id', true);
    }

    public function get_type(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'package_type', true);
    }

    public function get_order_id(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'order_id', true);
    }

    public function get_type_title(){
        $type = $this->get_type();
        if($type == 'job_package'){
            return __('Job Package', 'iwjob');
        }elseif($type == 'resum_package'){
            return __('Resume Package', 'iwjob');
        }elseif($type == 'apply_job_package'){
	        return __('Apply Class Package', 'iwjob');
        }

        return '';
    }

    public function get_package(){
        $type = $this->get_type();

        if($type == 'job_package'){
            return IWJ_Package::get_package($this->get_package_id());
        }elseif($type == 'resum_package'){
            return IWJ_Resume_Package::get_package($this->get_package_id());
        }elseif($type == 'apply_job_package'){
	        return IWJ_Apply_Job_Package::get_package($this->get_package_id());
        }

        return null;
    }

    public function get_remain_resum(){

        return get_post_meta($this->get_id(), IWJ_PREFIX.'remain_resum', true);
    }

	public function get_remain_apply_job(){

		return get_post_meta($this->get_id(), IWJ_PREFIX.'remain_apply_job', true);
	}

    public function get_remain_job(){

        return get_post_meta($this->get_id(), IWJ_PREFIX.'remain_job', true);
    }

    public function get_remain_renew_job(){

        return get_post_meta($this->get_id(), IWJ_PREFIX.'remain_renew_job', true);
    }

    public function get_remain_featured_job(){

        return get_post_meta($this->get_id(), IWJ_PREFIX.'remain_featured_job', true);
    }

    public function get_max_categories(){

        return get_post_meta($this->post->ID, IWJ_PREFIX.'max_categories', true);
    }

    public function get_user_id(){
        return $this->post->post_author;
    }

    public function get_expiry(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'expiry', true);
    }

    public function can_submit(){
	    if ( ( $this->get_remain_job() <= 0 && $this->get_remain_job() != - 1 ) || $this->get_user_id() != get_current_user_id() ) {
		    return false;
	    }

        return true;
    }

    public function can_make_featured(){
	    if ( ( $this->get_remain_featured_job() <= 0 && $this->get_remain_featured_job() != - 1 ) || $this->get_user_id() != get_current_user_id() ) {
		    return false;
	    }

        return true;
    }

    public function can_renew(){
	    if ( ( $this->get_remain_renew_job() <= 0 && $this->get_remain_renew_job() != - 1 ) || $this->get_user_id() != get_current_user_id() ) {
		    return false;
	    }

        return true;
    }

    public function can_view_resum(){
        if($this->get_remain_resum() <= 0 || $this->get_user_id() != get_current_user_id()){
            return false;
        }

        return true;
    }

	public function can_apply_job(){
		if($this->get_remain_apply_job() <= 0 || $this->get_user_id() != get_current_user_id()){
			return false;
		}

		return true;
	}

    public function purchased(){
        $package = $this->get_package();
        $type = $this->get_type();
        if($package && !$this->has_status('publish')){
            $pre_use = (int)get_post_meta($this->get_id(), IWJ_PREFIX.'pre_use', true);
            if($type == 'job_package'){
                   update_post_meta($this->get_id(), IWJ_PREFIX.'remain_job', ($package->get_number_job() - $pre_use));
                   update_post_meta($this->get_id(), IWJ_PREFIX.'remain_featured_job', $package->get_number_featured_job());
                   update_post_meta($this->get_id(), IWJ_PREFIX.'remain_renew_job', $package->get_number_renew_job());
                   update_post_meta($this->get_id(), IWJ_PREFIX.'max_categories', $package->get_max_categories());
            }elseif($type == 'resum_package') {
                update_post_meta($this->get_id(), IWJ_PREFIX.'remain_resum', ($package->get_number_resume() - $pre_use));
            }elseif($type == 'apply_job_package') {
	            update_post_meta($this->get_id(), IWJ_PREFIX.'remain_apply_job', ($package->get_number_apply() - $pre_use));
            }

            delete_post_meta($this->get_id(), IWJ_PREFIX.'pre_use');

            //update status
            $this->change_status('publish');

            return true;
        }

        return false;
    }

    public function get_status($calculate = true){
        $status = $this->post->post_status;
        $type = $this->get_type();
        if($calculate && $status == 'publish'){
            if($type == 'job_package' && ($this->get_remain_job() <= 0 && $this->get_remain_job() != -1)  && ($this->get_remain_featured_job() <= 0 && $this->get_remain_featured_job() != -1) && ($this->get_remain_renew_job() <= 0 && $this->get_remain_renew_job() != -1)){
                return 'iwj-expired';
            }elseif($type == 'resum_package' && $this->get_remain_resum() <= 0){
                return 'iwj-expired';
            }if($type == 'apply_job_package' && $this->get_remain_apply_job() <= 0){
		        return 'iwj-expired';
	        }else{
                return $status;
            }
        }else{
            return $status;
        }
    }

    public function has_status($check_status){
        $check_status = !is_array($check_status) ? (array)$check_status : $check_status;
        $status = $this->get_status();
        $status = str_replace('iwj-', '', $status);
        if(in_array($status, $check_status)){
            return true;
        }

        return false;
    }

    public function change_status($status){
        global $wpdb;
        $sql = "UPDATE {$wpdb->posts} SET post_status = %s WHERE ID = %d";
        $wpdb->query($wpdb->prepare($sql, $status, $this->get_id()));

        //send email
    }

    static function get_status_array($pending_payment = false, $expired = false){
        $status = array(
            'publish' => __('Active', 'iwjob'),
            'draft' => __('Draft', 'iwjob'),
        );

        if($expired){
            $status['iwj-expired'] = __('Inactive', 'iwjob');
        }
        if($pending_payment){
            $status['iwj-pending-payment'] = __('Pending payment', 'iwjob');
        }

        return $status;
    }

    public static function get_status_title($status){
        $status_array = self::get_status_array(true, true);

        return isset($status_array[$status]) ? $status_array[$status] : '';
    }

    static function add_new($args = array(), $type = 'job_package'){

        $post_data = array(
            'post_title' => isset($args['title']) ? $args['title'] : '',
            'post_type' => 'iwj_u_package',
            'post_status' => isset($args['status']) ? $args['status'] : 'iwj-pending-payment',
            'post_author' => isset($args['user_id']) ? $args['user_id'] : get_current_user_id(),
        );

        $post_id = wp_insert_post($post_data);
        if($post_id){
            wp_update_post(array(
                'ID' => $post_id,
                'post_title' => $args['title'] . ' #'. $post_id
            ));

            update_post_meta($post_id, IWJ_PREFIX.'package_type', $type);

            if(isset($args['pre_use']) && $args['pre_use']){
                update_post_meta($post_id, IWJ_PREFIX.'pre_use', $args['pre_use']);
            }

            if($type == 'job_package'){
                if(isset($args['package_id']) && $args['package_id']){
                    update_post_meta($post_id, IWJ_PREFIX.'package_id', $args['package_id']);
                }
               /* if(isset($args['remain_job'])){
                    update_post_meta($post_id, IWJ_PREFIX.'remain_job', (int)$args['remain_job']);
                }
                if(isset($args['remain_featured_job'])){
                    update_post_meta($post_id, IWJ_PREFIX.'remain_featured_job', (int)$args['remain_featured_job']);
                }
                if(isset($args['max_categories'])){
                    update_post_meta($post_id, IWJ_PREFIX.'max_categories', $args['max_categories']);
                }*/
            }else{
                if(isset($args['package_id']) && $args['package_id']){
                    update_post_meta($post_id, IWJ_PREFIX.'package_id', $args['package_id']);
                }

                /*if(isset($args['remain_resum'])){
                    update_post_meta($post_id, IWJ_PREFIX.'remain_resum', (int)$args['remain_resum']);
                }*/
            }
        }

        return $post_id;
    }
}