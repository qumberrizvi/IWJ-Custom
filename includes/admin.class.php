<?php
class IWJ_Admin{
    static function init(){
        include_once IWJ_PLUGIN_DIR.'/includes/admin/radiotax.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/job.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/user.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/employer.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/candidate.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/application.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/package.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/plan.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/resume-package.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/user-package.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/apply-job-package.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/cat.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/type.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/salary.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/level.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/meta-box-order-notes.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/order.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/keyword.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/alerts-list-table.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/alerts.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/email-queue-list-table.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/email-queue.class.php';
	    include_once IWJ_PLUGIN_DIR.'/includes/admin/reviews-list-table.php';
	    include_once IWJ_PLUGIN_DIR.'/includes/admin/reviews.class.php';
	    include_once IWJ_PLUGIN_DIR.'/includes/admin/indeed-imports.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/setting.class.php';
        include_once IWJ_PLUGIN_DIR.'/includes/admin/wpml.php';

        add_action('admin_menu', array(__CLASS__, 'admin_menu'));
        add_action('admin_notices', array(__CLASS__, 'admin_notices'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_load_scripts'));
    }

    static function admin_load_scripts(){
        $google_api_key = iwj_get_map_api_key();
        $js_version = false;
        wp_register_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key='.$google_api_key.'&libraries=places&sensor=false&language='. get_locale(), array('jquery'), $js_version, true);
        wp_enqueue_style('iwj-admin-rating', IWJ_PLUGIN_URL . '/assets/css/star-rating.css', array());
	    wp_enqueue_script('iwj-rating-custom2',IWJ_PLUGIN_URL.'/assets/js/star-rating.js',array('jquery'),$js_version,true);
        wp_enqueue_script('jquery-blockUI',  IWJ_PLUGIN_URL . '/assets/js/jquery.blockUI.min.js', array(), $js_version, true);
        wp_enqueue_style('iwj-admin', IWJ_PLUGIN_URL . '/assets/css/admin.css', array());
        wp_enqueue_script('iwj-admin',  IWJ_PLUGIN_URL . '/assets/js/admin.js', array(), rand(1,100), true);
        wp_localize_script('iwj-admin', 'iwjadmin', array(
            'i18n_delete_note' => __('Are you sure to delete this note? This action cannot be undone.', 'iwjob'),
            'ajax_url' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce( "iwj-security" ),
        ));
    }

    static function admin_menu(){
	    $edit_rev = add_submenu_page('edit.php?post_type=iwj_job', __('Reviews', 'iwjob'), __('Reviews', 'iwjob'), 'manage_options', 'iwj-reviews', array('IWJ_Admin_Reviews', 'management_page') );
	    add_action( 'load-' . $edit_rev, array('IWJ_Admin_Reviews', 'load_form_admin'));

        $edit = add_submenu_page('edit.php?post_type=iwj_job', __('Alerts', 'iwjob'), __('Alerts', 'iwjob'), 'manage_options', 'iwj-alerts', array('IWJ_Admin_Alerts', 'management_page') );
        add_action( 'load-' . $edit, array('IWJ_Admin_Alerts', 'load_form_admin'));
        $edit = add_submenu_page('edit.php?post_type=iwj_job', __('Email Queue', 'iwjob'), __('Email Queue', 'iwjob'), 'manage_options', 'iwj-email-queue', array('IWJ_Admin_Email_queue', 'management_page') );
        add_action( 'load-' . $edit, array('IWJ_Admin_Email_queue', 'load_form_admin'));

	    add_submenu_page('edit.php?post_type=iwj_job', __('Tools', 'iwjob'), __('Tools', 'iwjob'), 'manage_options', 'iwj-jobs-tools', array('IWJ_Admin_Classes_Tools', 'management_page') );

        add_submenu_page('edit.php?post_type=iwj_job', __('Job Settings', 'iwjob'), __('Job Settings', 'iwjob'), 'manage_options', 'iwj-setting-page', array('IWJ_Admin_Setting', 'setting_page') );
	    add_menu_page(__('Indeed Class Imports', 'iwjob'), __('Indeed Class Imports', 'iwjob'), 'manage_options', 'iwj-indeed-imports', array('IWJ_Admin_Indeed_Imports', 'management_page'),'dashicons-vault', 26);
    }

    static function admin_notices(){

        $setting_pages = array(
            'login' => __('Login Page', 'iwjob'),
            'register' => __('Register Page', 'iwjob'),
            'verify_account' => __('Verify Account Page', 'iwjob'),
            'lostpass' => __('Lost Password Page', 'iwjob'),
            'dashboard' => __('Dashboard Page', 'iwjob'),
            'jobs' => __('Classes Page', 'iwjob'),
            'candidates' => __('Teachers Page', 'iwjob'),
            'employers' => __('Students Page', 'iwjob'),
            'suggest_job' => __('Job Suggestion Page', 'iwjob'),
            'candidate_suggestion' => __('Teacher Suggestion Page', 'iwjob'),
        );

        if(!iwj_option('verify_account')){
            unset($setting_pages['verify_account']);
        }

        $setting_page_titles = array();
        foreach ($setting_pages as $page_id => $title){
            if(!iwj_get_page_id($page_id)){
                $setting_page_titles[] = $title;
            }
        }

        if($setting_page_titles){
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    <strong><?php printf(__( 'Please select %s', 'iwjob' ), implode(", ", $setting_page_titles)); ?>
                    <a href="<?php echo admin_url('/edit.php?post_type=iwj_job&page=iwj-setting-page') ; ?>"><?php echo __('Settings', 'iwjob'); ?></a></strong>
                </p>
            </div>
            <?php
        }
    }
}

IWJ_Admin::init();