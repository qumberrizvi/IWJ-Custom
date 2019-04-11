<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}


/*
Plugin Name: InWave Classes
Plugin URI: http://inwavethemes.com/inwavejob
Description:
Version: 3.2.5
Author: inwavethemes
Author URI: http://inwavethemes.com
License: GPLv2 or later
Text Domain: iwjob
*/

class IWJ_Class{

    /**
     * inDirectory version.
     *
     * @var string
     */
    public $version = '3.2.5';

    /**
     * The single instance of the class.
     *
     */
    protected static $_instance = null;

    static $jobs_alert = array();

    /**
     * Main inDirectory Instance.
     *
     * Ensures only one instance of inDirectory is loaded or can be loaded.
     *
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    /**
     * Intravel Constructor.
     */
    public function __construct() {

        $this->define_constants();
        $this->includes();
        $this->init_hooks();

        do_action( 'iwj_loaded' );
    }

    /**
     * Define WC Constants.
     */
    private function define_constants() {
	    $upload_dir = wp_upload_dir();
        define( 'IWJ_VERSION', $this->version );
        define( 'IWJ_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        define( 'IWJ_PLUGIN_URL', plugins_url().'/iwjob' );
        define( 'IWJ_PREFIX', '_iwj_' );
	    define( 'IWJ_IMPORT_DIR', $upload_dir['basedir'] . '/jobs_import_export/imports' );
	    define( 'IWJ_IMPORT_URL', $upload_dir['baseurl'] . '/jobs_import_export/imports' );
	    define( 'IWJ_IMPORT_LOG', $upload_dir['basedir'] . '/jobs_import_export.log' );

        if(!defined('IWJ_PREVIEW_MODE')){
            define( 'IWJ_PREVIEW_MODE', false );
        }
    }

    public function includes() {

        include_once IWJ_PLUGIN_DIR.'includes/helper.function.php';
        include_once IWJ_PLUGIN_DIR.'includes/helper2.php';
        include_once IWJ_PLUGIN_DIR.'includes/add_hook.php';
        include_once IWJ_PLUGIN_DIR.'includes/libs/parsers/SmackCSVParser.php';
	    include_once IWJ_PLUGIN_DIR.'includes/tools.php';
        include_once IWJ_PLUGIN_DIR.'includes/front.class.php';
        include_once IWJ_PLUGIN_DIR.'includes/install.class.php';

        if(is_blog_admin()){
            include_once IWJ_PLUGIN_DIR.'includes/admin.class.php';
        }

        include_once IWJ_PLUGIN_DIR.'includes/ajax.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/listing-job.class.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/listing-candidate.class.php';
        include_once IWJ_PLUGIN_DIR.'includes/class/listing-employer.class.php';

        // widget
        include_once IWJ_PLUGIN_DIR.'/includes/widgets/job-filter.php';
        include_once IWJ_PLUGIN_DIR.'/includes/widgets/candidate-filter.php';
        include_once IWJ_PLUGIN_DIR.'/includes/widgets/employer-filter.php';
        include_once IWJ_PLUGIN_DIR.'/includes/widgets/job-search.php';
        include_once IWJ_PLUGIN_DIR.'/includes/widgets/jobs-by-author.php';
        include_once IWJ_PLUGIN_DIR.'/includes/widgets/candidates.php';
        include_once IWJ_PLUGIN_DIR.'/includes/widgets/employers.php';
        include_once IWJ_PLUGIN_DIR.'/includes/widgets/overall-statistics.php';
        include_once IWJ_PLUGIN_DIR.'/includes/widgets/jobs.php';
        include_once IWJ_PLUGIN_DIR.'/includes/widgets/job_infomation.php';
        include_once IWJ_PLUGIN_DIR.'/includes/widgets/job_contact_form.php';
        include_once IWJ_PLUGIN_DIR.'/includes/widgets/employer_infomation.php';
        include_once IWJ_PLUGIN_DIR.'/includes/widgets/employer_map.php';
        include_once IWJ_PLUGIN_DIR.'/includes/widgets/employer_contact_form.php';
        include_once IWJ_PLUGIN_DIR.'/includes/widgets/candidate_infomation.php';
        include_once IWJ_PLUGIN_DIR.'/includes/widgets/candidate_contact_form.php';
    }

    /**
     * Hook into actions and filters.
     */
    private function init_hooks() {

        register_activation_hook( __FILE__, array( 'IWJ_Install', 'install' ) );
        register_deactivation_hook( __FILE__, array('IWJ_Install', 'deactive') );

        add_action('plugins_loaded', array('IWJ_Install', 'update'));
        add_action('wp_loaded', array('IWJ_Install', 'update2'));

        add_action( 'wpmu_new_blog', array( 'IWJ_Install', 'new_blog' ), 10, 6 );
        add_action( 'delete_blog', array( 'IWJ_Install', 'delete_blog' ), 10, 2 );

        add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );
        add_action( 'init', array( $this, 'init' ), 0 );

        add_action('wp_logout', array($this, 'end_session'));
        add_action('wp_login', array($this, 'end_session'));

        //add_action('activated_plugin',array($this, 'active_plugin_error'));
    }

    function active_plugin_error()
    {
        file_put_contents(dirname(__file__).'/error_activation.txt', ob_get_contents());
    }

    /**
     * Ensure theme and server variable compatibility and setup image sizes.
     */
    public function setup_environment() {

        define( 'IWJ_TEMPLATE_PATH', $this->template_path() );

        //$this->add_thumbnail_support();
        //$this->add_image_sizes();
    }

    /**
     * Ensure post thumbnail support is turned on.
     */
    private function add_thumbnail_support() {
        if ( ! current_theme_supports( 'post-thumbnails' ) ) {
            add_theme_support( 'post-thumbnails' );
        }
        add_post_type_support( 'iwj_job', 'thumbnail' );
    }

    /**
     * Load Localisation files.
     *
     */
    public function load_plugin_textdomain() {
        $locale = apply_filters( 'plugin_locale', get_locale(), 'iwjob' );

        load_textdomain( 'iwjob', IWJ_PLUGIN_DIR . '/iwj-' . $locale . '.mo' );
        load_plugin_textdomain( 'iwjob', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
    }
 
    /**
     * Init inDirectory when WordPress Initialises.
     */
    public function init() {
        // Before init action.
        do_action( 'before_iwj_init' );

        // Set up localisation.
        $this->load_plugin_textdomain();
        $this->start_session();

        /*add_filter( 'ajax_query_attachments_args', 'show_current_user_attachments', 10, 1 );
        function show_current_user_attachments( $query = array() ) {
            $user_id = get_current_user_id();
            if( $user_id && !is_super_admin($user_id)) {
                $query['author'] = $user_id;
            }
            return $query;
        }*/

        // Init action.
        do_action( 'iwj_init' );
    }

    function start_session() {
        if(!session_id()) {
            session_start();
        }
    }

    function end_session() {
    }

    /**
     * Get the template path.
     * @return string
     */
    public function template_path() {
        return apply_filters( 'iwj_template_path', 'iwj/' );
    }

    /**
     * Get the plugin path.
     * @return string
     */
    public function plugin_path() {
        return untrailingslashit( IWJ_PLUGIN_DIR );
    }

    /**
     * Auto-load in-accessible properties on demand.
     * @param mixed $key
     * @return mixed
     */
    public function __get( $key ) {
        if ( in_array( $key, array( 'payment_gateways', 'social_logins', 'applies') ) ) {
            return $this->$key();
        }
    }

    /**
     * Get gateways class.
     * @return IWJ_Payment_Gateways
     */
    public function payment_gateways() {
        return IWJ_Payment_Gateways::instance();
    }

    /**
     * Get Social Login class.
     * @return IWJ_Social_Logins
     */
    public function social_logins() {
        return IWJ_Social_Logins::instance();
    }

    /**
     * Get Apply method class.
     * @return IWJ_Applies
     */
    public function applies() {
        return IWJ_Applies::instance();
    }

}

if(!function_exists('iwjob')){
    function IWJ(){
        return IWJ_Class::instance();
    }
}

// Global for backwards compatibility.
$GLOBALS['iwjob'] = IWJ();
