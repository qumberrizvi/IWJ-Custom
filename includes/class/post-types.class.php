<?php

class IWJ_Post_Types
{
    static function init(){
        add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
        add_action( 'init', array( __CLASS__, 'register_post_status' ), 9 );
    }

    static function get_job_capabilities(){
        $caps = array(
            // meta caps (don't assign these to roles)
            'edit_post'              => 'edit_iwj_job',
            'read_post'              => 'read_iwj_job',
            'delete_post'            => 'delete_iwj_job',

            // primitive/meta caps
            'create_posts'           => 'create_iwj_jobs',

            // primitive caps used outside of map_meta_cap()
            'edit_posts'             => 'edit_iwj_jobs',
            'edit_others_posts'      => 'edit_others_iwj_jobs',
            'publish_posts'          => 'publish_iwj_jobs',
            'read_private_posts'     => 'read_private_iwj_jobs',

            // primitive caps used inside of map_meta_cap()
            'read'                   => 'read',
            'delete_posts'           => 'delete_iwj_jobs',
            'delete_private_posts'   => 'delete_private_iwj_jobs',
            'delete_published_posts' => 'delete_published_iwj_jobs',
            'delete_others_posts'    => 'delete_others_iwj_jobs',
            'edit_published_posts'   => 'edit_published_iwj_jobs',
            'edit_private_posts'     => 'edit_private_iwj_jobs',
        );

        return $caps;
    }

    static function register_post_types(){

        //Resigter Class Listing
        $labels = array(
            'name' 					=> __('Classes', 'iwjob'),
            'singular_name' 		=> __('Job', 'iwjob'),
            'add_new' 				=> __('Add New', 'iwjob'),
            'all_items' 			=> __('Classes', 'iwjob'),
            'add_new_item' 			=> __('Add New Class', 'iwjob'),
            'edit_item' 			=> __('Edit Job', 'iwjob'),
            'new_item' 				=> __('New Job', 'iwjob'),
            'view_item' 			=> __('View Job', 'iwjob'),
            'search_items' 			=> __('Start Learning', 'iwjob'),
            'not_found' 			=> __('Not found', 'iwjob'),
            'not_found_in_trash' 	=> __('Not found in trash', 'iwjob'),
            'parent_item_colon' 	=> '',
        );

        $args = array(
            'labels' 			=> $labels,
            'public' 			=> true,
            'show_ui' 			=> true,
            'capability_type' 	=> 'page',
            'map_meta_cap'    => true,
            'capabilities'    => self::get_job_capabilities(),
            'taxonomies'        => array( 'iwj_type', ),
            'hierarchical' 		=> true,
            'rewrite' 			=> array('slug' => iwj_option('job_slug', 'job'), 'with_front' => true),
            //'query_var' 		=> true,
            'exclude_from_search' => true,
            'show_in_nav_menus' => true,
            'has_archive'       => false,
            'supports' 			=> array('title', 'editor'),
        );

        register_post_type( 'iwj_job' , $args );

        //Resigter Student
        $labels = array(
            'name' 					=> __('Students', 'iwjob'),
            'singular_name' 		=> __('Student', 'iwjob'),
            'add_new' 				=> __('Add New', 'iwjob'),
            'all_items' 			=> __('Students', 'iwjob'),
            'add_new_item' 			=> __('Add New Student', 'iwjob'),
            'edit_item' 			=> __('Edit Student', 'iwjob'),
            'new_item' 				=> __('New Student', 'iwjob'),
            'view_item' 			=> __('View Student', 'iwjob'),
            'search_items' 			=> __('Search Student', 'iwjob'),
            'not_found' 			=> __('Not found', 'iwjob'),
            'not_found_in_trash' 	=> __('Not found in trash', 'iwjob'),
            'parent_item_colon' 	=> '',
        );

        $args = array(
            'labels' 			=> $labels,
            'public' 			=> true,
            'show_ui' 			=> true,
            'capability_type' 	=> 'page',
            'hierarchical' 		=> false,
            'rewrite' 			=> array('slug' => iwj_option('employer_slug', 'employer'), 'with_front' => true),
            //'query_var' 		=> true,
            'exclude_from_search' => true,
            'show_in_nav_menus' => true,
            'show_in_menu' => 'edit.php?post_type=iwj_job',
            'has_archive'       => false,
            'supports' 			=> array('title', 'thumbnail', 'editor', 'excerpt'),
            'map_meta_cap'        => true,
            'capabilities' => array(
                'create_posts' => false
            )
        );

        register_post_type( 'iwj_employer' , $args );

        //Resigter Teacher
        $labels = array(
            'name' 					=> __('Teachers', 'iwjob'),
            'singular_name' 		=> __('Teacher', 'iwjob'),
            'add_new' 				=> __('Add New', 'iwjob'),
            'all_items' 			=> __('Teachers', 'iwjob'),
            'add_new_item' 			=> __('Add New Teacher', 'iwjob'),
            'edit_item' 			=> __('Edit Teacher', 'iwjob'),
            'new_item' 				=> __('New Teacher', 'iwjob'),
            'view_item' 			=> __('View Teacher', 'iwjob'),
            'search_items' 			=> __('Search Teacher', 'iwjob'),
            'not_found' 			=> __('Not found', 'iwjob'),
            'not_found_in_trash' 	=> __('Not found in trash', 'iwjob'),
            'parent_item_colon' 	=> '',
        );

        $args = array(
            'labels' 			=> $labels,
            'public' 			=> true,
            'show_ui' 			=> true,
            'capability_type' 	=> 'page',
            'hierarchical' 		=> false,
            'rewrite' 			=> array('slug' => iwj_option('candidate_slug', 'candidate'), 'with_front' => true),
            //'query_var' 		=> true,
            'exclude_from_search' => true,
            'show_in_nav_menus' => true,
            'show_in_menu' => 'edit.php?post_type=iwj_job',
            'has_archive'       => false,
            'supports' 			=> array('title', 'thumbnail', 'editor'),
            'map_meta_cap'        => true,
            'capabilities' => array(
                'create_posts' => false
            )
        );

        register_post_type( 'iwj_candidate' , $args );

        //Applications
        $labels = array(
            'name' 					=> __('Applications', 'iwjob'),
            'singular_name' 		=> __('Application', 'iwjob'),
            'add_new' 				=> __('Add New', 'iwjob'),
            'all_items' 			=> __('Applications', 'iwjob'),
            'add_new_item' 			=> __('Add New Application', 'iwjob'),
            'edit_item' 			=> __('Edit Applications', 'iwjob'),
            'new_item' 				=> __('New Applications', 'iwjob'),
            'view_item' 			=> __('View Applications', 'iwjob'),
            'search_items' 			=> __('Search Applications', 'iwjob'),
            'not_found' 			=> __('Not found', 'iwjob'),
            'not_found_in_trash' 	=> __('Not found in trash', 'iwjob'),
            'parent_item_colon' 	=> '',
        );

        $args = array(
            'labels' 			=> $labels,
            'public' 			=> false,
            'show_ui' 			=> true,
            'capability_type' 	=> 'page',
            'hierarchical' 		=> false,
            'rewrite' 			=> false,
            //'query_var' 		=> true,
            'exclude_from_search' => true,
            'show_in_nav_menus' => true,
            'show_in_menu' => 'edit.php?post_type=iwj_job',
            'has_archive'       => true,
            'supports' 			=> array('title', 'editor'),
            'map_meta_cap'        => true,
            'capabilities' => array(
                'create_posts' => false
            )
        );

        register_post_type( 'iwj_application' , $args );

        //Package
        //if(iwj_option('submit_job_mode')){
            $labels = array(
                'name' 					=> __('Job Packages', 'iwjob'),
                'singular_name' 		=> __('Package', 'iwjob'),
                'add_new' 				=> __('Add New', 'iwjob'),
                'all_items' 			=> __('Job Packages', 'iwjob'),
                'add_new_item' 			=> __('Add New Package', 'iwjob'),
                'edit_item' 			=> __('Edit Package', 'iwjob'),
                'new_item' 				=> __('New Package', 'iwjob'),
                'view_item' 			=> __('View Package', 'iwjob'),
                'search_items' 			=> __('Search Package', 'iwjob'),
                'not_found' 			=> __('Not found', 'iwjob'),
                'not_found_in_trash' 	=> __('Not found in trash', 'iwjob'),
                'parent_item_colon' 	=> '',
            );

            $args = array(
                'labels' 			=> $labels,
                'public' 			=> false,
                'show_ui' 			=> true,
                'capability_type' 	=> 'page',
                'hierarchical' 		=> false,
                'rewrite' 			=> false,
                //'query_var' 		=> true,
                'exclude_from_search' => true,
                'show_in_nav_menus' => true,
                'show_in_menu' => iwj_option('submit_job_mode') == '1' ? 'edit.php?post_type=iwj_job' : false,
                'has_archive'       => true,
                'supports' 			=> array('title', 'thumbnail', 'page-attributes'),
            );

            register_post_type( 'iwj_package' , $args );
        //}

        $labels = array(
            'name' 					=> __('Membership Plans', 'iwjob'),
            'singular_name' 		=> __('Plan', 'iwjob'),
            'add_new' 				=> __('Add New', 'iwjob'),
            'all_items' 			=> __('Membership Plans', 'iwjob'),
            'add_new_item' 			=> __('Add New Plan', 'iwjob'),
            'edit_item' 			=> __('Edit Plan', 'iwjob'),
            'new_item' 				=> __('New Plan', 'iwjob'),
            'view_item' 			=> __('View Plan', 'iwjob'),
            'search_items' 			=> __('Search Plan', 'iwjob'),
            'not_found' 			=> __('Not found', 'iwjob'),
            'not_found_in_trash' 	=> __('Not found in trash', 'iwjob'),
            'parent_item_colon' 	=> '',
        );

        $args = array(
            'labels' 			=> $labels,
            'public' 			=> false,
            'show_ui' 			=> true,
            'capability_type' 	=> 'page',
            'hierarchical' 		=> false,
            'rewrite' 			=> false,
            //'query_var' 		=> true,
            'exclude_from_search' => true,
            'show_in_nav_menus' => true,
            'show_in_menu' => iwj_option('submit_job_mode') == '3' ? 'edit.php?post_type=iwj_job' : false,
            'has_archive'       => true,
            'supports' 			=> array('title', 'thumbnail', 'page-attributes'),
        );

        register_post_type( 'iwj_plan' , $args );

        //View Profile Package
        //if(!iwj_option('view_free_resum')) {
            $labels = array(
                'name' => __('Resume Packages', 'iwjob'),
                'singular_name' => __('Package', 'iwjob'),
                'add_new' => __('Add New', 'iwjob'),
                'all_items' => __('Resume Packages', 'iwjob'),
                'add_new_item' => __('Add New Package', 'iwjob'),
                'edit_item' => __('Edit Package', 'iwjob'),
                'new_item' => __('New Package', 'iwjob'),
                'view_item' => __('View Package', 'iwjob'),
                'search_items' => __('Search Package', 'iwjob'),
                'not_found' => __('Not found', 'iwjob'),
                'not_found_in_trash' => __('Not found in trash', 'iwjob'),
                'parent_item_colon' => '',
            );

            $args = array(
                'labels' => $labels,
                'public' => false,
                'show_ui' => true,
                'capability_type' => 'page',
                'hierarchical' => false,
                'rewrite' => false,
                //'query_var' 		=> true,
                'exclude_from_search' => true,
                'show_in_nav_menus' => true,
                'show_in_menu' => 'edit.php?post_type=iwj_job',
                'has_archive' => true,
                'supports' => array('title', 'editor'),
            );

            register_post_type('iwj_resum_package', $args);
        //}

        //User Package
        $labels = array(
            'name' 					=> __('User Packages', 'iwjob'),
            'singular_name' 		=> __('User Package', 'iwjob'),
            'add_new' 				=> __('Add New', 'iwjob'),
            'all_items' 			=> __('User Packages', 'iwjob'),
            'add_new_item' 			=> __('Add New User Package', 'iwjob'),
            'edit_item' 			=> __('Edit User Package', 'iwjob'),
            'new_item' 				=> __('New User Package', 'iwjob'),
            'view_item' 			=> __('View User Package', 'iwjob'),
            'search_items' 			=> __('Search User Package', 'iwjob'),
            'not_found' 			=> __('Not found', 'iwjob'),
            'not_found_in_trash' 	=> __('Not found in trash', 'iwjob'),
            'parent_item_colon' 	=> '',
        );

        $args = array(
            'labels' 			=> $labels,
            'public' 			=> false,
            'show_ui' 			=> true,
            'capability_type' 	=> 'page',
            'hierarchical' 		=> false,
            'rewrite' 			=> false,
            //'query_var' 		=> true,
            'exclude_from_search' => true,
            'show_in_nav_menus' => true,
            'show_in_menu' => iwj_option('submit_job_mode') == '1' ? 'edit.php?post_type=iwj_job' : false,
            'has_archive'       => true,
            'supports' 			=> array('title'),
            'map_meta_cap'        => true,
            'capabilities' => array(
                'create_posts' => false
            )
        );

        register_post_type( 'iwj_u_package' , $args );

        //Teacher package
	    $labels = array(
		    'name' 					=> __('Apply Class Packages', 'iwjob'),
		    'singular_name' 		=> __('Apply Class Package', 'iwjob'),
		    'add_new' 				=> __('Add New', 'iwjob'),
		    'all_items' 			=> __('Apply Class Packages', 'iwjob'),
		    'add_new_item' 			=> __('Add New Apply Class Package', 'iwjob'),
		    'edit_item' 			=> __('Edit Apply Class Package', 'iwjob'),
		    'new_item' 				=> __('New Apply Class Package', 'iwjob'),
		    'view_item' 			=> __('View Apply Class Package', 'iwjob'),
		    'search_items' 			=> __('Search Apply Class Package', 'iwjob'),
		    'not_found' 			=> __('Not found', 'iwjob'),
		    'not_found_in_trash' 	=> __('Not found in trash', 'iwjob'),
		    'parent_item_colon' 	=> '',
	    );

	    $args = array(
		    'labels' 			=> $labels,
		    'public' 			=> false,
		    'show_ui' 			=> true,
		    'capability_type' 	=> 'page',
		    'hierarchical' 		=> false,
		    'rewrite' 			=> false,
		    //'query_var' 		=> true,
		    'exclude_from_search' => true,
		    'show_in_nav_menus' => true,
		    'show_in_menu' => !iwj_option('apply_job_mode') ? 'edit.php?post_type=iwj_job' : false,
		    'has_archive'       => true,
		    'supports' 			=> array('title', 'editor'),
	    );

	    register_post_type( 'iwj_apply_package' , $args );

        //Notifications
	    /*$labels = array(
		    'name'               => __( 'Notifications', 'iwjob' ),
		    'singular_name'      => __( 'Notification', 'iwjob' ),
		    'add_new'            => __( 'Add New', 'iwjob' ),
		    'all_items'          => __( 'Notifications', 'iwjob' ),
		    'add_new_item'       => __( 'Add New Notification', 'iwjob' ),
		    'edit_item'          => __( 'Edit Notification', 'iwjob' ),
		    'new_item'           => __( 'New Notification', 'iwjob' ),
		    'view_item'          => __( 'View Notification', 'iwjob' ),
		    'search_items'       => __( 'Search Notification', 'iwjob' ),
		    'not_found'          => __( 'Not found', 'iwjob' ),
		    'not_found_in_trash' => __( 'Not found in trash', 'iwjob' ),
		    'parent_item_colon'  => '',
	    );
	    $args = array(
		    'labels'              => $labels,
		    'public'              => false,
		    'show_ui'             => true,
		    'capability_type'     => 'post',
		    'hierarchical'        => false,
		    'rewrite'             => false,
		    'exclude_from_search' => true,
		    'show_in_nav_menus'   => true,
		    'show_in_menu'        => 'edit.php?post_type=iwj_job',
		    'has_archive'         => true,
		    'supports'            => array( 'title', 'thumbnail' ),
	    );

	    register_post_type( 'iwj_notification' , $args );*/

        //Order
        $labels = array(
            'name' 					=> __('Orders', 'iwjob'),
            'singular_name' 		=> __('Order', 'iwjob'),
            'add_new' 				=> __('Add New', 'iwjob'),
            'all_items' 			=> __('Orders', 'iwjob'),
            'add_new_item' 			=> __('Add New Order', 'iwjob'),
            'edit_item' 			=> __('Edit Order', 'iwjob'),
            'new_item' 				=> __('New Order', 'iwjob'),
            'view_item' 			=> __('View Order', 'iwjob'),
            'search_items' 			=> __('Search Order', 'iwjob'),
            'not_found' 			=> __('Not found', 'iwjob'),
            'not_found_in_trash' 	=> __('Not found in trash', 'iwjob'),
            'parent_item_colon' 	=> '',
        );

        $args = array(
            'labels' 			=> $labels,
            'public' 			=> false,
            'show_ui' 			=> true,
            'capability_type' 	=> 'page',
            'hierarchical' 		=> false,
            'rewrite' 			=> false,
            //'query_var' 		=> true,
            'show_in_nav_menus' => true,
            'show_in_menu' => 'edit.php?post_type=iwj_job',
            'has_archive'       => true,
            'supports' 			=> array('title'),
            'map_meta_cap'        => true,
            'capabilities' => array(
                'create_posts' => false
            )
        );

        register_post_type( 'iwj_order' , $args );

        //Register Cat
        $labels = array(
            'name' 					=> __('Subjects', 'iwjob'),
            'popular_items' 		=> __('Popular Subjects', 'iwjob'),
            'singular_name' 		=> __('Category', 'iwjob'),
            'add_new' 				=> __('Add New', 'iwjob'),
            'all_items' 			=> __('Subjects', 'iwjob'),
            'add_new_item' 			=> __('Add New Category', 'iwjob'),
            'edit_item' 			=> __('Edit Category', 'iwjob'),
            'new_item' 				=> __('New Category', 'iwjob'),
            'view_item' 			=> __('View Category', 'iwjob'),
            'search_items' 			=> __('Search Category', 'iwjob'),
            'not_found' 			=> __('Not found', 'iwjob'),
            'no_terms' 	            => __('Not found in trash', 'iwjob'),
        );

        register_taxonomy('iwj_cat',
            array('iwj_job', 'iwj_candidate', 'iwj_employer'),
            array(
                'hierarchical' 		=> true,
                'public'            => true,
                'labels' 			=> $labels,
                'show_admin_column'	=> false,
                //'query_var' 		=> true,
                'rewrite'           => array(
                    'slug'                       => iwj_option('category_slug', 'category'),
                    'with_front'                 => true,
                    'hierarchical'               => false,
                ),
            )
        );

        if(!iwj_option('disable_type')){
            //Register Type
            $labels = array(
                'name' 					=> __('Types', 'iwjob'),
                'popular_items' 		=> __('Popular Types', 'iwjob'),
                'singular_name' 		=> __('Type', 'iwjob'),
                'add_new' 				=> __('Add New', 'iwjob'),
                'all_items' 			=> __('Types', 'iwjob'),
                'add_new_item' 			=> __('Add New Type', 'iwjob'),
                'edit_item' 			=> __('Edit Type', 'iwjob'),
                'new_item' 				=> __('New Type', 'iwjob'),
                'view_item' 			=> __('View Type', 'iwjob'),
                'search_items' 			=> __('Search Type', 'iwjob'),
                'not_found' 			=> __('Not found', 'iwjob'),
                'no_terms' 	            => __('Not found in trash', 'iwjob'),
            );

            register_taxonomy('iwj_type',
                array('iwj_job', 'iwj_candidate'),
                array(
                    'hierarchical' 		=> true,
                    'public'            => true,
                    'labels' 			=> $labels,
                    'show_admin_column'	=> false,
                    //'query_var' 		=> true,
                    'rewrite'           => array(
                        'slug'                       => iwj_option('type_slug', 'type'),
                        'with_front'                 => true,
                        'hierarchical'               => false,
                    ),
                )
            );
        }

        //Register Price
        $labels = array(
            'name' 					=> __('Salaries', 'iwjob'),
            'popular_items' 		=> __('Popular Salaries', 'iwjob'),
            'singular_name' 		=> __('Salary', 'iwjob'),
            'add_new' 				=> __('Add New', 'iwjob'),
            'all_items' 			=> __('Salary', 'iwjob'),
            'add_new_item' 			=> __('Add New Salary', 'iwjob'),
            'edit_item' 			=> __('Edit Salary', 'iwjob'),
            'new_item' 				=> __('New Salary', 'iwjob'),
            'view_item' 			=> __('View Salary', 'iwjob'),
            'search_items' 			=> __('Search Salary', 'iwjob'),
            'not_found' 			=> __('Not found', 'iwjob'),
            'no_terms' 	            => __('Not found in trash', 'iwjob'),
        );

        register_taxonomy('iwj_salary',
            array('iwj_job'),
            array(
                'hierarchical' 		=> true,
                'public'            => true,
                'labels' 			=> $labels,
                'show_admin_column'	=> false,
                //'query_var' 		=> true,
                'rewrite'           => array(
                    'slug'                       => iwj_option('salary_slug', 'salary'),
                    'with_front'                 => true,
                    'hierarchical'               => false,
                ),
            )
        );

        if(!iwj_option('disable_skill')) {
            //Register skill
            $labels = array(
                'name' => __('Skills', 'iwjob'),
                'popular_items' => __('Popular Skills', 'iwjob'),
                'singular_name' => __('Skill', 'iwjob'),
                'add_new' => __('Add New', 'iwjob'),
                'all_items' => __('Skills', 'iwjob'),
                'add_new_item' => __('Add New Skill', 'iwjob'),
                'edit_item' => __('Edit Skill', 'iwjob'),
                'new_item' => __('New Skill', 'iwjob'),
                'view_item' => __('View Skill', 'iwjob'),
                'search_items' => __('Search Skill', 'iwjob'),
                'not_found' => __('Not found', 'iwjob'),
                'no_terms' => __('Not found in trash', 'iwjob'),
            );

            register_taxonomy('iwj_skill',
                array('iwj_job', 'iwj_candidate'),
                array(
                    'hierarchical' => true,
                    'public' => true,
                    'labels' => $labels,
                    'show_admin_column' => false,
                    //'query_var' 		=> true,
                    'rewrite' => array(
                        'slug' => iwj_option('skill_slug', 'salary'),
                        'with_front' => true,
                        'hierarchical' => false,
                    ),
                )
            );
        }

        if(!iwj_option('disable_level')) {

            //Register level
            $labels = array(
                'name' => __('Levels', 'iwjob'),
                'popular_items' => __('Popular Levels', 'iwjob'),
                'singular_name' => __('Level', 'iwjob'),
                'add_new' => __('Add New', 'iwjob'),
                'all_items' => __('Levels', 'iwjob'),
                'add_new_item' => __('Add New Level', 'iwjob'),
                'edit_item' => __('Edit Level', 'iwjob'),
                'new_item' => __('New Level', 'iwjob'),
                'view_item' => __('View Level', 'iwjob'),
                'search_items' => __('Search Level', 'iwjob'),
                'not_found' => __('Not found', 'iwjob'),
                'no_terms' => __('Not found in trash', 'iwjob'),
            );

            register_taxonomy('iwj_level',
                array('iwj_job', 'iwj_candidate'),
                array(
                    'hierarchical' => true,
                    'public' => true,
                    'labels' => $labels,
                    'show_admin_column' => false,
                    //'query_var' 		=> true,
                    'rewrite' => array(
                        'slug' => iwj_option('level_slug', 'level'),
                        'with_front' => true,
                        'hierarchical' => false,
                    ),
                )
            );
        }

        //Register keyword
        $labels = array(
            'name' 					=> __('Keywords', 'iwjob'),
            'popular_items' 		=> __('Popular Keywords', 'iwjob'),
            'singular_name' 		=> __('Keyword', 'iwjob'),
            'add_new' 				=> __('Add New', 'iwjob'),
            'all_items' 			=> __('Keywords', 'iwjob'),
            'add_new_item' 			=> __('Add New Keyword', 'iwjob'),
            'edit_item' 			=> __('Edit Keyword', 'iwjob'),
            'new_item' 				=> __('New Keyword', 'iwjob'),
            'view_item' 			=> __('View Keyword', 'iwjob'),
            'search_items' 			=> __('Search Keyword', 'iwjob'),
            'not_found' 			=> __('Not found', 'iwjob'),
            'no_terms' 	            => __('Not found in trash', 'iwjob'),
        );

        register_taxonomy('iwj_keyword',
            array('iwj_job'),
            array(
                'hierarchical' 		=> true,
                'public'            => true,
                'labels' 			=> $labels,
                'show_admin_column'	=> false,
                'meta_box_cb'       => false,
                //'query_var' 		=> true,
                'rewrite'           => array(
                    'slug'                       => iwj_option('keyword', 'keyword'),
                    'with_front'                 => true,
                    'hierarchical'               => false,
                ),
            )
        );

        //Register Location
        $labels = array(
            'name' 					=> __('Locations', 'iwjob'),
            'popular_items' 		=> __('Popular Locations', 'iwjob'),
            'singular_name' 		=> __('Location', 'iwjob'),
            'add_new' 				=> __('Add New', 'iwjob'),
            'all_items' 			=> __('Directory Location', 'iwjob'),
            'add_new_item' 			=> __('Add New Location', 'iwjob'),
            'edit_item' 			=> __('Edit Location', 'iwjob'),
            'new_item' 				=> __('New Location', 'iwjob'),
            'view_item' 			=> __('View Location', 'iwjob'),
            'search_items' 			=> __('Search Location', 'iwjob'),
            'not_found' 			=> __('Not found', 'iwjob'),
            'no_terms' 	            => __('Not found in trash', 'iwjob'),
        );

        register_taxonomy('iwj_location',
            array('iwj_job', 'iwj_candidate', 'iwj_employer'),
            array(
                'hierarchical' 		=> true,
                'public'            => true,
                'labels' 			=> $labels,
                'show_admin_column'	=> false,
                //'query_var' 		=> true,
                'rewrite'           => array(
                    'slug'                       => iwj_option('location_slug', 'location'),
                    'with_front'                 => true,
                    'hierarchical'               => false,
                ),
            )
        );

        //Register Location
        $labels = array(
            'name' 					=> __('Company Sizes', 'iwjob'),
            'popular_items' 		=> __('Company Sizes', 'iwjob'),
            'singular_name' 		=> __('Size', 'iwjob'),
            'add_new' 				=> __('Add New', 'iwjob'),
            'all_items' 			=> __('Size', 'iwjob'),
            'add_new_item' 			=> __('Add New Size', 'iwjob'),
            'edit_item' 			=> __('Edit Size', 'iwjob'),
            'new_item' 				=> __('New Size', 'iwjob'),
            'view_item' 			=> __('View Size', 'iwjob'),
            'search_items' 			=> __('Search Size', 'iwjob'),
            'not_found' 			=> __('Not found', 'iwjob'),
            'no_terms' 	            => __('Not found in trash', 'iwjob'),
        );

        register_taxonomy('iwj_size',
            array('iwj_job', 'iwj_employer'),
            array(
                'hierarchical' 		=> true,
                'public'            => true,
                'labels' 			=> $labels,
                'show_admin_column'	=> false,
                //'query_var' 		=> true,
                'rewrite'           => false,
            )
        );

    }

    static function register_post_status(){
        $order_statuses = apply_filters( 'iwj_register_order_post_statuses',
            array(
                'iwj-pending-payment'    => array(
                    'label'                     => _x( 'Pending Payment', 'Order status', 'iwjob' ),
                    'public'                    => false,
                    'private'                   => 'protected',
                    'exclude_from_search'       => true,
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'label_count'               => _n_noop( 'Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'iwjob' )
                ),
                'iwj-expired'    => array(
                    'label'                     => _x( 'Expired', 'Order status', 'iwjob' ),
                    'public'                    => false,
                    'exclude_from_search'       => true,
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'iwjob' )
                ),
                'iwj-rejected'    => array(
                    'label'                     => _x( 'Reject', 'Order status', 'iwjob' ),
                    'public'                    => false,
                    'exclude_from_search'       => true,
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'label_count'               => _n_noop( 'Reject <span class="count">(%s)</span>', 'Reject <span class="count">(%s)</span>', 'iwjob' )
                ),
                'iwj-incomplete'    => array(
                    'label'                     => _x( 'Incomplete', 'Order status', 'iwjob' ),
                    'public'                    => false,
                    'exclude_from_search'       => true,
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'label_count'               => _n_noop( 'Incomplete <span class="count">(%s)</span>', 'Incomplete <span class="count">(%s)</span>', 'iwjob' )
                ),
                'iwj-completed'    => array(
                    'label'                     => _x( 'Completed', 'Order status', 'iwjob' ),
                    'public'                    => false,
                    'exclude_from_search'       => true,
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'iwjob' )
                ),
                'iwj-hold'    => array(
                    'label'                     => _x( 'Hold', 'Order status', 'iwjob' ),
                    'public'                    => false,
                    'exclude_from_search'       => true,
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'label_count'               => _n_noop( 'Hold <span class="count">(%s)</span>', 'Hold <span class="count">(%s)</span>', 'iwjob' )
                ),
                'iwj-cancelled'    => array(
                    'label'                     => _x( 'Canceled', 'Order status', 'iwjob' ),
                    'public'                    => false,
                    'exclude_from_search'       => true,
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'label_count'               => _n_noop( 'Canceled <span class="count">(%s)</span>', 'Canceled <span class="count">(%s)</span>', 'iwjob' )
                ),
            )
        );

        foreach ( $order_statuses as $order_status => $values ) {
            register_post_status( $order_status, $values );
        }
    }
}

IWJ_Post_Types::init();
?>