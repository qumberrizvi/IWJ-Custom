<?php

class IWJ_Admin_User_Package{
    static $fields = array();
    static $fields_job_package = array();
    static $fields_resum_package = array();
    static $fields_apply_job_package = array();

    static function init(){
        self::$fields = array(
            'package' => array(
                'name' => __( 'Package', 'iwjob' ),
                'id'   => IWJ_PREFIX.'package_id',
                'type' => 'post',
                'post_type' => 'iwj_package',
                'required' => true,
                'disabled' => true
            ),
            'user' => array(
                'name' => __( 'User', 'iwjob' ),
                'id'   => IWJ_PREFIX.'user_id',
                'type' => 'user',
                'post_type' => 'iwj_package',
                'required' => true,
                'disabled' => true
            ),
        );

        self::$fields_job_package = array(
            'remain_job' => array(
                'name' => __( 'Classes Remain', 'iwjob' ),
                'id'   => IWJ_PREFIX.'remain_job',
                'type' => 'text',
                'desc' => __( '-1 if you wish set unlimited jobs', 'iwjob' ),
                'required' => true,
            ),
            'remain_renew_job' => array(
                'name' => __( 'Renew Classes Remaining', 'iwjob' ),
                'id'   => IWJ_PREFIX.'remain_renew_job',
                'type' => 'text',
                'desc' => __( '-1 if you wish set unlimited jobs', 'iwjob' ),
                'required' => true,
            ),
            'remain_featured_job' => array(
                'name' => __( 'Featured Classes Remaining', 'iwjob' ),
                'id'   => IWJ_PREFIX.'remain_featured_job',
                'desc' => __( '-1 if you wish set unlimited jobs', 'iwjob' ),
                'type' => 'text',
                'required' => true,
            ),
            'max_categories' => array(
                'name' => __( 'Maximum Subjects in Job', 'iwjob' ),
                'desc' => __( 'Empty if you wish set unlimited categories', 'iwjob' ),
                'id'   => IWJ_PREFIX.'max_categories',
                'type' => 'text',
            ),
        );

        self::$fields_resum_package = array(
            'remain_resum' => array(
                'name' => __( 'Remain Resume', 'iwjob' ),
                'id'   => IWJ_PREFIX.'remain_resum',
                'type' => 'text',
                'required' => true,
            ),
        );

	    self::$fields_apply_job_package = array(
		    'remain_apply_job' => array(
			    'name' => __( 'Remain Apply Job', 'iwjob' ),
			    'id'   => IWJ_PREFIX.'remain_apply_job',
			    'type' => 'text',
			    'required' => true,
		    ),
	    );

        add_filter('wp_count_posts', array( __CLASS__, 'count_posts' ), 10, 3);
        add_action('pre_get_posts',array( __CLASS__, 'pre_get_posts' ) );
        add_action('admin_menu', array( __CLASS__ , 'register_metabox'));
        add_action('save_post', array( __CLASS__ , 'save_post'), 99, 1);
        add_filter('manage_posts_columns' , array(__CLASS__, 'columns_head' ));
        add_filter('manage_posts_custom_column' , array(__CLASS__, 'columns_content' ), 10, 2);

        add_filter('post_row_actions', array(__CLASS__, 'remove_quick_edit'),10,1);
        add_filter('page_row_actions', array(__CLASS__, 'remove_quick_edit'),10,1);
    }

    static function remove_quick_edit( $actions){
        global $current_screen;
        if( $current_screen->post_type != 'iwj_u_package' ) return $actions;
        unset($actions['inline hide-if-no-js']);
        return $actions;
    }

    static function register_metabox(){
        remove_meta_box('submitdiv', 'iwj_u_package', 'core');
        add_meta_box('iwj-u-package-meta-box', __('Package Metabox Info', 'iwjob'), array( __CLASS__, 'metabox_html'), 'iwj_u_package', 'normal', 'high');
    }

    static function metabox_html(){
        global $post;
        $post_id = $post->ID;
        $saved = isset($_GET['post']) ? true : false;
        ?>
        <div class="iwj-metabox wp-clearfix">
            <table class="form-table">
                <tr class="iwjmb-field">
                    <th class="iwjmb-label"><label><?php echo __('Order ID', 'iwjob'); ?></label></th>
                    <td colspan="1">
                        <?php
                        $order_id = get_post_meta($post_id, IWJ_PREFIX.'order_id', true);
                        if($order_id){
                        ?>
                            <a href="<?php echo get_edit_post_link($order_id); ?>">#<?php echo $order_id; ?></a>
                        <?php } ?>
                        </td>
                </tr>
                <?php
                    $status = $post->post_status;
                    if($status == 'iwj-pending-payment'){
                        $user_package = IWJ_User_Package::get_user_package($post);
                        if($user_package->get_type() == 'resum_package'){
                            self::$fields['package']['post_type'] = 'iwj_resum_package';
                        }
	                    if($user_package->get_type() == 'apply_job_package'){
		                    self::$fields['package']['post_type'] = 'iwj_apply_package';
	                    }

                        $field = IWJMB_Field::call( 'normalize', self::$fields['package'] );
                        $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                        IWJMB_Field::input($field, $meta );

                        $field = IWJMB_Field::call( 'normalize', self::$fields['user'] );
                        $meta = $post->post_author;
                        IWJMB_Field::input($field, $meta );
                    }
                    else{
                        $user_package = IWJ_User_Package::get_user_package($post);
                        if($user_package->get_type() == 'resum_package'){
                            self::$fields['package']['post_type'] = 'iwj_resum_package';
                        }
	                    if($user_package->get_type() == 'apply_job_package'){
		                    self::$fields['package']['post_type'] = 'iwj_apply_package';
	                    }
                        foreach (self::$fields as $key => $field){
                            $field = IWJMB_Field::call( 'normalize', $field );
                            if($key == 'user'){
                                $meta = $post->post_author;
                            }else{
                                $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );
                            }
                            IWJMB_Field::input($field, $meta );
                        }

                        if($user_package->get_type() == 'job_package'){
                            foreach (self::$fields_job_package as $key => $field){

                                $field = IWJMB_Field::call( 'normalize', $field );
                                $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );

                                IWJMB_Field::input($field, $meta );
                            }
                        }elseif($user_package->get_type() == 'resum_package'){
                            foreach (self::$fields_resum_package as $key => $field){

                                    $field = IWJMB_Field::call( 'normalize', $field );
                                    $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );

                                IWJMB_Field::input($field, $meta );
                            }
                        }elseif($user_package->get_type() == 'apply_job_package'){
	                        foreach (self::$fields_apply_job_package as $key => $field){

		                        $field = IWJMB_Field::call( 'normalize', $field );
		                        $meta = IWJMB_Field::call( $field, 'post_meta', $post_id, $saved );

		                        IWJMB_Field::input($field, $meta );
	                        }
						}
                    }
                ?>
                <tr class="iwjmb-field">
                    <th class="iwjmb-label"><label></label></th>
                    <td><button type="submit" class="button button-primary"><?php echo __('Update', 'iwjob'); ?></button></td>
                </tr>
            </table>
        </div>
        <?php
    }

    static function save_post($post_id){
        if(isset($_POST) && $_POST){
            if(get_post_type($post_id) == 'iwj_u_package'){
                $user_package = IWJ_User_Package::get_user_package($post_id);

               /* if(isset($_POST[IWJ_PREFIX.'status']) && $_POST[IWJ_PREFIX.'status']){
                    $old_status = get_post_status($post_id);
                    $new_status = $_POST[IWJ_PREFIX.'status'];
                    if($old_status != $new_status){
                        $user_package->change_status($new_status);
                    }
                }*/

                if($user_package->get_type() == 'resum_package'){
                    self::$fields['package']['post_type'] = 'iwj_resum_package';
                }
	            if($user_package->get_type() == 'apply_job_package'){
		            self::$fields['package']['post_type'] = 'iwj_apply_package';
	            }

                foreach (self::$fields as $key=>$field){
                    $field = IWJMB_Field::call( 'normalize', $field );
                    if($field['disabled'] || $field['readonly']){
                        continue;
                    }

                    if(isset($_POST[$field['field_name']])){
                        $single = $field['clone'] || ! $field['multiple'];
                        $old    = IWJMB_Field::call( $field, 'raw_post_meta', $post_id );
                        $new    = isset( $_POST[ $field['id'] ] ) ? $_POST[ $field['id'] ] : ( $single ? '' : array() );
                        $$field['id'] = $new;
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
                }

                if($user_package->get_type() == 'job_package'){
                    foreach (self::$fields_job_package as $key=>$field){
                        $field = IWJMB_Field::call( 'normalize', $field );
                        if($field['disabled'] || $field['readonly']){
                            continue;
                        }

                        if(isset($_POST[$field['field_name']])){
                            $single = $field['clone'] || ! $field['multiple'];
                            $old    = IWJMB_Field::call( $field, 'raw_post_meta', $post_id );
                            $new    = isset( $_POST[ $field['id'] ] ) ? $_POST[ $field['id'] ] : ( $single ? '' : array() );
                            $$field['id'] = $new;
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
                    }
                }
                elseif($user_package->get_type() == 'resum_package'){
                    foreach (self::$fields_resum_package as $key=>$field){
                        $field = IWJMB_Field::call( 'normalize', $field );
                        if($field['disabled'] || $field['readonly']){
                            continue;
                        }

                        if(isset($_POST[$field['field_name']])){
                            $single = $field['clone'] || ! $field['multiple'];
                            $old    = IWJMB_Field::call( $field, 'raw_post_meta', $post_id );
                            $new    = isset( $_POST[ $field['id'] ] ) ? $_POST[ $field['id'] ] : ( $single ? '' : array() );
                            $$field['id'] = $new;
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
                    }
                }elseif($user_package->get_type() == 'apply_job_package'){
	                foreach (self::$fields_apply_job_package as $key=>$field){
		                $field = IWJMB_Field::call( 'normalize', $field );
		                if($field['disabled'] || $field['readonly']){
			                continue;
		                }

		                if(isset($_POST[$field['field_name']])){
			                $single = $field['clone'] || ! $field['multiple'];
			                $old    = IWJMB_Field::call( $field, 'raw_post_meta', $post_id );
			                $new    = isset( $_POST[ $field['id'] ] ) ? $_POST[ $field['id'] ] : ( $single ? '' : array() );
			                $$field['id'] = $new;
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
	                }
                }
            }
        }
    }

    static function columns_head( $columns )
    {
        $screen = get_current_screen();
        if ($screen->post_type == 'iwj_u_package') {
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'title' => __('Title', 'iwjob'),
                'status' => __('Status', 'iwjob'),
                'package_type' => __('Package Type', 'iwjob'),
                'user_id' => __('User ', 'iwjob'),
                'info' => __('Package Info ', 'iwjob'),
                'order_id' => __('Order ID ', 'iwjob'),
            );
        }

        return $columns;
    }

    static function columns_content( $column, $post_ID ) {
        $screen = get_current_screen();
        if($screen->post_type == 'iwj_u_package'){
            if ($column == 'user_id') {
                $post = get_post($post_ID);
                $user = get_userdata($post->post_author);
                if($user){
                    echo '<a title="'.__('Click to filter', 'iwjob').'" href="'.admin_url().'edit.php?post_type=iwj_u_package&user_id='.$user->ID.'">'.$user->display_name.'</a>';
                }
            }
            if ($column == 'info') {
                $user_package = IWJ_User_Package::get_user_package($post_ID);
                if($user_package){
                    $type = $user_package->get_type();
                    if($type == 'job_package'){
	                    $remain_job = ( $user_package->get_remain_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : $user_package->get_remain_job();
	                    $remain_featured_job = ( $user_package->get_remain_featured_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : $user_package->get_remain_featured_job();
	                    $remain_renew_job = ( $user_package->get_remain_renew_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : $user_package->get_remain_renew_job();
                        echo sprintf(__('Classes Remaining: <strong>%s</strong>, Features Remaining: <strong>%s</strong>, Renews Remaining: <strong>%s</strong>', 'iwjob'), $remain_job, $remain_featured_job, $remain_renew_job);
                    }elseif($type == 'resum_package'){
                        echo sprintf(__('Resume Remaining: <strong>%d</strong>', 'iwjob'), $user_package->get_remain_resum());
                    }elseif($type == 'apply_job_package'){
	                    echo sprintf(__('Apply Remaining: <strong>%d</strong>', 'iwjob'), $user_package->get_remain_apply_job());
                    }
                }
            }
            if ($column == 'status') {
                $user_package = IWJ_User_Package::get_user_package($post_ID);
                echo IWJ_User_Package::get_status_title($user_package->get_status());
            }
            if ($column == 'package_type') {
                $user_package = IWJ_User_Package::get_user_package($post_ID);
                echo $user_package->get_type_title();
            }
            if ($column == 'order_id') {
                $order_id = get_post_meta($post_ID, IWJ_PREFIX.'order_id', true);
                if($order_id){
                    echo '<a target="_blank" title="'.__('Click to view order', 'iwjob').'" href="'.admin_url().'post.php?post='.$order_id.'&action=edit">'.'#'.$order_id.'</a>';
                }
            }
        }
    }

    static function count_posts( $counts, $type, $perm ){
        if($type == 'iwj_u_package'){
            if($counts->publish > 0){
                global $wpdb;
                $sql = "SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} as p 
                        LEFT JOIN {$wpdb->postmeta} as pm ON p.ID = pm.post_id 
                        LEFT JOIN {$wpdb->postmeta} as pm1 ON p.ID = pm1.post_id 
                        LEFT JOIN {$wpdb->postmeta} as pm2 ON p.ID = pm2.post_id 
                        LEFT JOIN {$wpdb->postmeta} as pm3 ON p.ID = pm3.post_id 
                        WHERE post_type = %s AND post_status = %s 
                        AND (
                          (
                            pm.meta_key = %s AND pm.meta_value = %s AND 
                            pm1.meta_key = %s AND CAST( pm1.meta_value AS UNSIGNED ) <= 0
                          )
                          OR
                          (
                            pm.meta_key = %s AND pm.meta_value = %s AND 
                            pm1.meta_key = %s AND CAST( pm1.meta_value AS UNSIGNED ) <= 0
                          )
                          OR 
                          (
                            pm.meta_key = %s AND pm.meta_value = %s AND 
                            pm1.meta_key = %s AND CAST( pm1.meta_value AS UNSIGNED ) <= 0 AND 
                            pm2.meta_key = %s AND CAST( pm2.meta_value AS UNSIGNED ) <= 0 AND 
                            pm3.meta_key = %s AND CAST( pm3.meta_value AS UNSIGNED ) <= 0 
                          )
                        )";

                $total = $wpdb->get_var($wpdb->prepare(
                        $sql, 'iwj_u_package', 'publish',
                        IWJ_PREFIX.'package_type', 'resum_package', IWJ_PREFIX.'remain_resum',
                        IWJ_PREFIX.'package_type', 'apply_job_package', IWJ_PREFIX.'remain_apply_job',
                        IWJ_PREFIX.'package_type', 'job_package', IWJ_PREFIX.'remain_job',
						IWJ_PREFIX.'remain_featured_job', IWJ_PREFIX.'remain_renew_job'
                    )
                );
                if($total){
                    $counts->publish = $counts->publish - $total;
                    $counts->{'iwj-expired'} = $counts->{'iwj-expired'} + $total;
                }
            }
        }
        return $counts;
    }

    static function pre_get_posts($query){
        if(is_blog_admin() && $query->get('post_type') == 'iwj_u_package'){
            if(!isset($_GET['post_status'])){
                $query->set('post_status', array_keys(IWJ_User_Package::get_status_array(true)));
            }elseif($_GET['post_status'] == 'iwj-expired'){
                $query->set('post_status', 'publish');
                $query->set('meta_query', array(
                    'relation' => 'OR',
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => IWJ_PREFIX.'package_type',
                            'value' => 'job_package',
                            'compare' => '='
                        ),
                        array(
                            'key' => IWJ_PREFIX.'remain_job',
                            'value' => 0,
                            'type' => 'numeric',
                            'compare' => '<='
                        ),
                        array(
                            'key' => IWJ_PREFIX.'remain_featured_job',
                            'value' => 0,
                            'type' => 'numeric',
                            'compare' => '<='
                        ),
                        array(
                            'key' => IWJ_PREFIX.'remain_renew_job',
                            'value' => 0,
                            'type' => 'numeric',
                            'compare' => '<='
                        )
                    ),
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => IWJ_PREFIX.'package_type',
                            'value' => 'resum_package',
                            'compare' => '='
                        ),
                        array(
                            'key' => IWJ_PREFIX.'remain_resum',
                            'value' => 0,
                            'type' => 'numeric',
                            'compare' => '<='
                        ),
                    ),
	                array(
		                'relation' => 'AND',
		                array(
			                'key' => IWJ_PREFIX.'package_type',
			                'value' => 'apply_job_package',
			                'compare' => '='
		                ),
		                array(
			                'key' => IWJ_PREFIX.'remain_apply_job',
			                'value' => 0,
			                'type' => 'numeric',
			                'compare' => '<='
		                ),
	                )
                ));
            }

            if(isset($_GET['user_id']) && $_GET['user_id']){
                $query->set('author', $_GET['user_id']);
            }
        }
    }
}

IWJ_Admin_User_Package::init();