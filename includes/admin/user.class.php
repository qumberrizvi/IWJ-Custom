<?php
class IWJ_Admin_User{
    static $cache;

    static function init(){
        add_action( 'edit_user_profile', array( __CLASS__ , 'extra_profile_fields') );
        add_action( 'show_user_profile', array( __CLASS__, 'extra_profile_fields' ) );
        add_action( 'profile_update', array( __CLASS__ , 'profile_update'), 10, 2 );
        add_filter( 'manage_users_columns', array( __CLASS__ , 'users_columns'));
        add_filter( 'manage_users_custom_column', array( __CLASS__ , 'users_columns_content'), 10, 3 );
        add_filter('user_row_actions',  array( __CLASS__ , 'row_actions'), 10, 2);
        add_filter('wp_loaded',  array( __CLASS__ , 'verify_account'));
    }

    static function get_plan_fields(){
        $fields = array(
            array(
                'name' => __('Plan ID', 'iwproperty'),
                'id' => IWJ_PREFIX.'plan_id',
                'type' => 'text',
            ),
            array(
                'name' => __('Plan Expiry Date', 'iwproperty'),
                'desc' => __('-1 is never expiry', 'iwproperty'),
                'id' => IWJ_PREFIX.'plan_expiry_date',
                'type' => 'date',
                'js_options' => array(
                    'validateOnBlur' => false
                ),
            ),
            array(
                'name' => __('Classes', 'iwproperty'),
                'desc' => __('Number of jobs. Blank is inherited from package. -1 is Unlimited', 'iwproperty'),
                'id' => IWJ_PREFIX.'plan_jobs',
                'type' => 'text',
            ),
            array(
                'name' => __('Featured Classes', 'iwproperty'),
                'desc' => __('Number of Featured jobs. Blank is inherited from package. -1 is Unlimited', 'iwproperty'),
                'id' => IWJ_PREFIX.'plan_featured_jobs',
                'type' => 'text',
            ),
            array(
                'name' => __('Renewal Classes', 'iwproperty'),
                'desc' => __('Number of Renewal jobs. Blank is inherited from package. -1 is Unlimited', 'iwproperty'),
                'id' => IWJ_PREFIX.'plan_renew_jobs',
                'type' => 'text',
            ),
            array(
                'name' => __('Renewal Classes Used', 'iwproperty'),
                'desc' => __('Number of Renewal jobs What the user has used', 'iwproperty'),
                'id' => IWJ_PREFIX.'plan_renew_jobs_used',
                'type' => 'text',
            ),
        );

        return $fields;
    }
    static function extra_profile_fields($user){
        ?>
        <h3><?php echo __('Job Information', 'iwjob'); ?></h3>

        <table class="form-table">
            <?php
                $field = array(
                    'name' => __('Avatar', 'iwjob'),
                    'id' => IWJ_PREFIX.'avatar',
                    'type' => 'image_advanced',
                    'max_file_uploads' => 1
                );

            $field = IWJMB_Field::call( 'normalize', $field );
            $meta = get_user_meta($user->ID, IWJ_PREFIX.'avatar', true);
            IWJMB_Field::input($field, $meta );
            ?>

            <?php
            if(user_can($user->ID, 'create_iwj_jobs')){
            ?>
            <tr>
                <th><label><?php echo __('Student', 'iwjob'); ?></label></th>
                <td>
                    <?php
                    $employer = get_user_meta($user->ID, IWJ_PREFIX . 'employer_post', true);
                    if ($employer) {
                        $link = '<a href="'.get_edit_post_link($employer).'">'.__('here', 'iwjob').'</a>';
                        echo sprintf(__('Click %s to manager.', 'iwjob'), $link);
                        ?>
                    <?php } ?>
                </td>
            </tr>
            <?php }elseif(user_can($user->ID, 'apply_job')){ ?>
            <tr>
                <th><label><?php echo __('Teacher', 'iwjob'); ?></label></th>
                <td>
                    <?php
                    $candidate = get_user_meta($user->ID, IWJ_PREFIX . 'candidate_post', true);
                    if ($candidate) {
                        $link = '<a href="'.get_edit_post_link($candidate).'">'.__('here', 'iwjob').'</a>';
                        echo sprintf(__('Click %s to manager.', 'iwjob'), $link);
                    }
                    ?>
                </td>
            </tr>
            <?php } ?>

            <?php
            if(iwj_option('submit_job_mode') == '3') {
                $user_obj = IWJ_User::get_user($user);
                $plan_fields = self::get_plan_fields();
                foreach ($plan_fields as $field) {
                    $field = IWJMB_Field::call('normalize', $field);

                    $meta = get_user_meta($user->ID, $field['id'], true);

                    // Escape attributes
                    $meta = IWJMB_Field::call($field, 'esc_meta', $meta);

                    if ($field['type'] == 'date' || $field['type'] == 'datetime') {
                        $meta = $meta ? date($field['format'], intval($meta)) : '';
                    }

                    // Make sure meta value is an array for clonable and multiple fields
                    if ($field['clone'] || $field['multiple']) {
                        if (empty($meta) || !is_array($meta)) {
                            /**
                             * Note: if field is clonable, $meta must be an array with values
                             * so that the foreach loop in self::show() runs properly
                             *
                             * @see self::show()
                             */
                            $meta = $field['clone'] ? array('') : array();
                        }
                    }

                    IWJMB_Field::input($field, $meta);

                    if ($field['id'] == IWJ_PREFIX . 'plan_renew_jobs_used') {
                        if ($user_obj->has_plan()) {
                            $total_jobs = $user_obj->plan_get_jobs(true);
                            $total_featured_jobs = $user_obj->plan_get_featured_jobs(true);
                            $total_renew_jobs = $user_obj->plan_get_renew_jobs(true);
                            $jobs_used = $user_obj->plan_get_jobs_used();
                            $jobs_featured_used = $user_obj->plan_get_featured_jobs_used();
                            $jobs_renew_used = $user_obj->plan_get_renew_jobs_used();
                            echo '<tr>';
                            echo '<th>' . __('Classes used', 'iwproperty') . '</th>';
                            echo '<td>' . (int)$jobs_used . '/' . $total_jobs . '</td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<th>' . __('Featured Classes used', 'iwproperty') . '</th>';
                            echo '<td>' . (int)$jobs_featured_used . '/' . $total_featured_jobs . '</td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<th>' . __('Renew Classes used', 'iwproperty') . '</th>';
                            echo '<td>' . (int)$jobs_renew_used . '/' . $total_renew_jobs . '</td>';
                            echo '</tr>';
                        }
                    }
                }
                do_action('iwj_user_admin_plan_info', $user_obj);
            }
            ?>
        </table>
        <?php
    }

    static function profile_update( $user_id, $old_user_data){
        if(isset($_POST) && $_POST && is_blog_admin() && !defined( 'DOING_AJAX' )){
            if(iwj_option('submit_job_mode') == '3') {
                $fields = self::get_plan_fields();
                $new_plan_expiry_date = '';
                foreach ($fields as $field) {
                    $field = IWJMB_Field::call('normalize', $field);

                    $single = $field['clone'] || !$field['multiple'];
                    $old = IWJMB_Field::call($field, 'raw_post_meta', $user_id, 'user');
                    $new = isset($_POST[$field['id']]) ? $_POST[$field['id']] : ($single ? '' : array());
                    // Allow field class change the value
                    $new = IWJMB_Field::call($field, 'value', $new, $old, $user_id, 'user');
                    $new = IWJMB_Field::call($field, 'sanitize_value', $new);
                    if ($field['id'] == IWJ_PREFIX . 'plan_expiry_date') {
                        $new_plan_expiry_date = $new;
                    } else {
                        update_user_meta($user_id, $field['id'], $new);
                    }
                }

                if (isset($_POST[IWJ_PREFIX . 'plan_id'])) {
                    $new_plan_id = isset($_POST[IWJ_PREFIX . 'plan_id']) ? sanitize_text_field($_POST[IWJ_PREFIX . 'plan_id']) : '';
                    $user = IWJ_User::get_user($user_id);
                    $user->change_plan($new_plan_id, $new_plan_expiry_date);
                }
            }
        }
    }

    static function users_columns( $column ) {
        $new_column = array();
        foreach ($column as $key => $title){
            $new_column[$key] = $title;
            if($key == 'role'){
                $new_column['iwj_profile'] = __('Profile', 'iwjob');
                if(iwj_option('verify_account')){
                    $new_column['iwj_verified'] = __('Verified', 'iwjob');
                }
            }
        }

        return $new_column;
    }

    static function users_columns_content( $val, $column_name, $user_id ) {
        switch ($column_name) {
            case 'iwj_profile' :
                $user = IWJ_User::get_user($user_id);
                $profile_id = 0;
                if($user->is_employer()){
                    $profile_id = $user->get_employer_id();
                }elseif($user->is_candidate()){
                    $profile_id = $user->get_candidate_id();
                }

                if($profile_id){
                    $val .= '<a href="'.get_permalink($profile_id).'" target="_blank">'.__('View', 'iwjob').'</a> | ';
                    $val .= '<a href="'.get_edit_post_link($profile_id).'" target="_blank">'.__('Edit', 'iwjob').'</a>';
                }
                break;
            case 'iwj_verified' :
                $user = IWJ_User::get_user($user_id);
                if($user->is_verified()){
                    $val = __('Yes', 'iwjob');
                }else{
                    $val = __('No', 'iwjob');
                }
                break;
            default:
        }

        return $val;
    }

    static function row_actions($actions, $user_object) {
        if(iwj_option('verify_account') && current_user_can('edit_users', $user_object->ID)){
            $user = IWJ_User::get_user($user_object->ID);
            if(!$user->is_verified()){
                $actions['verify_account'] = "<a class='iwj_verify_account' href='" . admin_url( "users.php?iwj_verify_account=$user_object->ID") . "'>" . __( 'Verify', 'iwjob' ) . "</a>";
            }
        }
        return $actions;
    }

    static function verify_account(){
        if(is_blog_admin() && isset($_GET['iwj_verify_account']) && $_GET['iwj_verify_account']){
            $user_id = $_GET['iwj_verify_account'];
            if(current_user_can('edit_users', $user_id)){
                delete_user_meta($user_id, IWJ_PREFIX.'verify_code');
            }

            wp_redirect(admin_url( "users.php"));
            exit;
        }
    }
}

IWJ_Admin_User::init();