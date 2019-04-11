<?php

class IWJ_Apply_Job_Package {
	static $cache = array();

	public $post;

	public function __construct( $post ) {
		$this->post = $post;
	}

	static function get_package( $post = '', $force = false ) {
		$post_id = 0;
		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
			if ( ! is_wp_error( $post ) ) {
				$post_id = $post->ID;
			}
		} else {
			$post_id = $post->ID;
		}

		if ( $post_id ) {
			if ( $force ) {
				clean_post_cache( $post_id );
				$post = get_post( $post_id );
			}

			if ( $force || ! isset( self::$cache[ $post_id ] ) ) {
				self::$cache[ $post_id ] = new IWJ_Apply_Job_Package( $post );
			}

			return self::$cache[ $post_id ];
		}

		return null;
	}

	public function get_id() {

		return $this->post->ID;
	}

	public function get_title() {

		return get_the_title( $this->post->ID );
	}

	public function get_description() {

		return $this->post->post_content;
	}

	public function get_price() {
		if ( $this->is_free() ) {
			return 0;
		}

		return get_post_meta( $this->get_id(), IWJ_PREFIX . 'price', true );
	}

	public function get_number_apply() {

		return get_post_meta( $this->get_id(), IWJ_PREFIX . 'number_apply', true );
	}

	public function is_free() {

		return $this->get_id() == iwj_option( 'free_apply_job_package_id' ) ? true : false;
	}

	public function can_buy(){
		if($this->is_free() && iwj_option('free_apply_job_package_times') > 0){
			global $wpdb;
			$sql = "SELECT count(1) FROM {$wpdb->posts} AS post 
                    JOIN {$wpdb->postmeta} AS postmeta ON post.ID = postmeta.post_id
                    WHERE post.post_type = %s AND post.post_status = %s AND postmeta.meta_key = %s AND postmeta.meta_value = %s";
			$sql = $wpdb->prepare($sql, 'iwj_apply_package', 'publish', IWJ_PREFIX.'package_id', $this->get_id());
			$total = $wpdb->get_var($sql);
			if($total >= iwj_option('free_apply_job_package_times')){
				return false;
			}
		}

		return true;
	}

	static function get_packages($args = array()){

		$default_args = array(
			'post_type' => 'iwj_apply_package',
			'post_status' => array('publish'),
			'posts_per_page' => '10',
		);

		$args = wp_parse_args($args, $default_args);

		return new WP_Query( $args );
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