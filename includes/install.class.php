<?php
class IWJ_Install{
    static function install($networkwide){
        global $wpdb;

        if (is_multisite() && $networkwide) {
                $old_blog =  $wpdb->blogid;
                //Get all blog ids
                $blogids =  $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
                foreach ( $blogids as $blog_id ) {
                    switch_to_blog($blog_id);
                    //Create database table if not exists
                    self::create_tables();
                    self::create_options();

                }
                switch_to_blog( $old_blog );
        }else{
            self::create_tables();
            self::create_options();
        }

        self::create_roles();
        self::create_cron_jobs();

        update_option('iwj_version', IWJ()->version);

        IWJ_Post_Types::register_post_types();
        //IWJ_Post_Types::register_post_status();

        // Flush rules after install
        flush_rewrite_rules();

        do_action( 'iwj_installed' );
    }

    static function new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ){
        if ( is_plugin_active_for_network( 'iwjob/iwjob.php' ) ) {
            switch_to_blog( $blog_id );
            self::create_tables();
            self::create_options();
            self::create_roles();
            self::create_cron_jobs();
            IWJ_Post_Types::register_post_types();
            flush_rewrite_rules();

        restore_current_blog();
        }
    }

    static function create_tables(){
        global $wpdb;

        //Create Tables
        require_once(ABSPATH . '/wp-admin/includes/upgrade.php');

        $collate = '';

        if ( $wpdb->has_cap( 'collation' ) ) {
            $collate = $wpdb->get_charset_collate();
        }

        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}iwj_view_resums (
                  `ID` int(11) NOT NULL AUTO_INCREMENT,
                  `post_id` int(11) NOT NULL,
                  `user_id` int(11) NOT NULL,
                  PRIMARY KEY (`ID`),
                  KEY `Post ID` (`post_id`),
                  KEY `User ID` (`user_id`)
                ) $collate;";
        dbDelta($sql);

        $sql = "CREATE TABLE IF NOT EXISTS  {$wpdb->prefix}iwj_save_resums (
                  `ID` int(11) NOT NULL AUTO_INCREMENT,
                  `post_id` int(11) NOT NULL,
                  `user_id` int(11) NOT NULL,
                  PRIMARY KEY (`ID`),
                  KEY `Post ID` (`post_id`),
                  KEY `User ID` (`user_id`)
                ) $collate;";
        dbDelta($sql);

        $sql = "CREATE TABLE IF NOT EXISTS  {$wpdb->prefix}iwj_save_jobs (
                  `ID` int(11) NOT NULL AUTO_INCREMENT,
                  `post_id` int(11) NOT NULL,
                  `user_id` int(11) NOT NULL,
                  PRIMARY KEY (`ID`),
                  KEY `Post ID` (`post_id`),
                  KEY `User ID` (`user_id`)
                ) $collate;";
        dbDelta($sql);

        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}iwj_save_jobs (
                  `ID` int(11) NOT NULL AUTO_INCREMENT,
                  `post_id` int(11) NOT NULL,
                  `user_id` int(11) NOT NULL,
                  PRIMARY KEY (`ID`),
                  KEY `Post ID` (`post_id`),
                  KEY `User ID` (`user_id`)
                ) $collate;";
        dbDelta($sql);

        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}iwj_follows (
                  `ID` int(11) NOT NULL AUTO_INCREMENT,
                  `post_id` int(11) NOT NULL,
                  `user_id` int(11) NOT NULL,
                  PRIMARY KEY (`ID`),
                  KEY `Post ID` (`post_id`),
                  KEY `User ID` (`user_id`)
                ) $collate;";
        dbDelta($sql);

        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}iwj_email_queue (
                  `ID` int(11) NOT NULL AUTO_INCREMENT,
                  `send_time` int(11) NOT NULL,
                  `priority` tinyint(3) NOT NULL,
                  `attemp` tinyint(3) NOT NULL,
                  `from_name` varchar(255) NOT NULL,
                  `from_address` varchar(255) NOT NULL,
                  `recipients` varchar(255) NOT NULL,
                  `subject` varchar(255) NOT NULL,
                  `content` text NOT NULL,
                  PRIMARY KEY (`ID`)
                ) $collate;";
        dbDelta($sql);

        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}iwj_alerts (
                  `ID` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `name` varchar(255) NOT NULL,
                  `email` varchar(255) NOT NULL,
                  `position` varchar(255) NOT NULL,
                  `salary_from` int(11) DEFAULT NULL,
                  `frequency` varchar(10) NOT NULL,
                  `status` tinyint  NOT NULL,
                  `created` datetime NOT NULL,
                  PRIMARY KEY (`ID`),
                  KEY `User` (`user_id`)
                ) $collate;";

        dbDelta($sql);

        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}iwj_alert_relationships (
                  `ID` int(11) NOT NULL AUTO_INCREMENT,
                  `alert_id` int(11) NOT NULL,
                  `term_id` int(11) NOT NULL,
                  PRIMARY KEY (`ID`),
                  KEY `Notification` (`alert_id`),
                  KEY `Term` (`term_id`)
                ) $collate;";
        dbDelta($sql);

        //from 2.0.0
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}iwj_reviews (
                    `ID`  int(11) NOT NULL AUTO_INCREMENT ,
                    `user_id`  int(11) NOT NULL ,
                    `rating`  varchar(5) NOT NULL ,
                    `item_id`  int(11) NOT NULL ,
                    `title`  varchar(255) NOT NULL ,
                    `content`  text NOT NULL ,
                    `time`  int(11) NOT NULL ,
                    `status`  varchar(20) NOT NULL ,
                    `criterias`  text NOT NULL ,
                    `read`  tinyint(3) NOT NULL ,
                    PRIMARY KEY (`ID`),
                    KEY `User` (`user_id`),
                    KEY `Item Id` (`item_id`) 
                ) $collate;";
        dbDelta($sql);

        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}iwj_reviews_reply(
                    `ID` int(11) NOT NULL AUTO_INCREMENT,
                      `review_id` int(11) NOT NULL,
                      `user_id` int(11) NOT NULL,
                      `reply_content` text NOT NULL,
                      `time` int(11) NOT NULL,
                      PRIMARY KEY (`ID`),
                      KEY `Reviews` (`review_id`)
                ) $collate;";
        dbDelta($sql);

        //from 2.7.0

        $sql = "CREATE TABLE {$wpdb->prefix}iwj_term_translates (
                  `ID` int(11) NOT NULL AUTO_INCREMENT,
                  `term_id` int(11) NOT NULL,
                  `lang_code` varchar(3) NOT NULL,
                  `translate_key` varchar(255) NOT NULL,
                  `translate_string` text NOT NULL,
                  PRIMARY KEY (`ID`),
                  KEY `term_id` (`term_id`)
               ) $collate;";
        dbDelta($sql);

        $sql = "CREATE TABLE {$wpdb->prefix}iwj_post_translates (
                  `ID` int(11) NOT NULL AUTO_INCREMENT,
                  `post_id` int(11) NOT NULL,
                  `lang_code` varchar(3) NOT NULL,
                  `translate_key` varchar(255) NOT NULL,
                  `translate_string` text NOT NULL,
                  PRIMARY KEY (`ID`),
                  KEY `post_id` (`post_id`)
                ) $collate;";

        dbDelta($sql);
    }

    static function create_roles(){
        add_role( 'iwj_employer', __('Student', 'iwjob'),
            array(
                'read' => true,
                'level_0' => true,
                'upload_files' =>true,
                'edit_attachments' =>true,
                'delete_attachments' =>true,
                'edit_iwj_jobs' => true,
                'edit_published_iwj_jobs' => true,
                'delete_iwj_jobs' => true,
                'delete_published_iwj_jobs' => true,
                'create_iwj_jobs' => true,
            )
        );

        add_role( 'iwj_candidate', __('Teacher', 'iwjob'),
            array(
                'read' => true,
                'level_0' => true,
                'upload_files' =>true,
                'edit_attachments' =>true,
                'delete_attachments' =>true,
                'apply_job' =>true,
            )
        );

        $roles = array( 'administrator'/*, 'editor'*/);
        foreach ( $roles as $roleName ) {
            // Get role
            $role = get_role( $roleName );

            // Check role exists
            if ( is_null( $role) ) {
                continue;
            }

            $customCaps = array('privilege_view_resum');
            // Iterate through our custom capabilities, adding them
            // to this role if they are enabled
            foreach ( $customCaps as $capability ) {
                // Add capability
                $role->add_cap( $capability );
            }

            $job_caps = array(
                'create_iwj_jobs',
                'delete_iwj_jobs',
                'delete_private_iwj_jobs',
                'delete_published_iwj_jobs',
                'delete_others_iwj_jobs',
                'edit_iwj_jobs',
                'edit_others_iwj_jobs',
                'edit_published_iwj_jobs',
                'edit_private_iwj_jobs',
                'manage_iwj_jobs',
                'publish_iwj_jobs'
            );

            foreach ( $job_caps as $capability ) {
                // Add capability
                $role->add_cap( $capability );
            }
        }
    }

    static function create_options($force = false){

        if(!$force){
            $options = get_option('iwj_settings');
            if($options){
                return false;
            }
        }

        $settings = 'a:296:{s:13:"login_page_id";s:3:"143";s:16:"register_page_id";s:3:"145";s:22:"verify_account_page_id";s:4:"2317";s:16:"lostpass_page_id";s:3:"147";s:17:"dashboard_page_id";s:3:"141";s:12:"jobs_page_id";s:3:"656";s:17:"employers_page_id";s:3:"530";s:18:"candidates_page_id";s:3:"526";s:19:"suggest_job_page_id";s:4:"3374";s:28:"candidate_suggestion_page_id";s:4:"3430";s:25:"terms_and_conditions_page";s:4:"2378";s:19:"privacy_policy_page";s:0:"";s:8:"job_slug";s:3:"job";s:13:"employer_slug";s:8:"employer";s:14:"candidate_slug";s:9:"candidate";s:13:"category_slug";s:3:"cat";s:9:"type_slug";s:4:"type";s:12:"sallary_slug";s:7:"sallary";s:10:"skill_slug";s:5:"skill";s:10:"level_slug";s:5:"level";s:13:"location_slug";s:8:"location";s:25:"disable_employer_register";s:0:"";s:26:"disable_candidate_register";s:0:"";s:14:"verify_account";s:0:"";s:30:"registration_generate_password";s:0:"";s:20:"woocommerce_checkout";s:0:"";s:30:"include_my_account_woocommerce";s:0:"";s:20:"disable_notification";a:0:{}s:12:"disable_type";s:0:"";s:13:"disable_skill";s:0:"";s:13:"disable_level";s:0:"";s:14:"disable_gender";s:0:"";s:15:"submit_job_mode";s:1:"1";s:22:"edit_job_auto_approved";s:1:"1";s:27:"edit_free_job_auto_approved";s:1:"1";s:21:"new_job_auto_approved";s:1:"1";s:26:"new_free_job_auto_approved";s:0:"";s:22:"delete_draft_job_hours";s:2:"24";s:21:"keep_jobs_delete_user";s:0:"";s:17:"keep_jobs_user_id";s:0:"";s:20:"auto_detect_location";s:1:"1";s:18:"allow_adress_types";a:4:{i:0;s:7:"country";i:1;s:27:"administrative_area_level_1";i:2;s:27:"administrative_area_level_2";i:3;s:27:"administrative_area_level_3";}s:16:"disable_language";s:0:"";s:15:"allow_languages";a:0:{}s:14:"exclude_skills";s:16:"abc,fuck,sex,xxx";s:25:"job_suggestion_conditions";a:4:{i:0;s:8:"category";i:1;s:4:"type";i:2;s:5:"level";i:3;s:5:"skill";}s:19:"default_job_content";s:1375:"<h4>Overview</h4>
<blockquote><p>Lorem ipsum dolor sit amet consectetur adipiscing, elit vehicula semper velit vestibulum felis purus, gravida rhoncus vulputate aliquet cras. Conubia libero morbi tristique rutrum elementum dapibus per cras volutpat, semper consequat nisl aenean urna ultricies tincidunt etiam senectus. Rhoncus blandit neque vivamus nullam sodales maecenas felis faucibus, lectus suspendisse vitae donec hendrerit montes ultrices fames, penatibus est pulvinar sagittis proin phareultrices fringilla.</p></blockquote>
<hr />
<h4>What You Will Do</h4>
<ol>
<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>
<li>Auctor class pellentesque augue dignissim venenatis, turpis vestibulum lacinia dignissim venenatis.</li>
<li>Mus arcu euismod ad hac dui, vivamus platea netus.</li>
<li>Neque per nisl posuere sagittis, id platea dui.</li>
<li>A enim magnis dapibus, nullam odio porta, nisl class.</li>
<li>Turpis leo pellentesque per nam, nostra fringilla id.</li>
</ol>
<hr />
<h4>What we can offer you</h4>
<ul>
<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>
<li>Mus arcu euismod ad hac dui, vivamus platea netus.</li>
<li>Neque per nisl posuere sagittis, id platea dui.</li>
<li>A enim magnis dapibus, nullam odio porta, nisl class.</li>
<li>Turpis leo pellentesque per nam, nostra fringilla id.</li>
</ul>
";s:9:"job_price";s:1:"5";s:15:"renew_job_price";s:1:"5";s:10:"job_expiry";s:1:"1";s:15:"job_expiry_unit";s:4:"year";s:18:"featured_job_price";s:1:"5";s:19:"featured_job_expiry";s:1:"1";s:24:"featured_job_expiry_unit";s:4:"year";s:19:"package_featured_id";s:4:"1577";s:15:"free_package_id";s:3:"198";s:18:"free_package_times";s:1:"1";s:22:"employer_auto_approved";s:1:"1";s:28:"show_employer_public_profile";s:0:"";s:23:"candidate_auto_approved";s:1:"1";s:15:"view_free_resum";s:0:"";s:21:"free_resum_package_id";s:0:"";s:24:"free_resum_package_times";s:0:"";s:31:"candidate_suggestion_conditions";a:1:{i:0;s:16:"profile_category";}s:29:"show_candidate_public_profile";s:0:"";s:14:"disable_review";s:0:"";s:14:"review_options";s:72:"Work / Life Balance
Comp & Benefits
Senior Management
Culture & Value";s:25:"edit_review_auto_approved";s:0:"";s:26:"delete_pending_order_hours";s:2:"48";s:16:"order_infomation";s:217:"My name is imran  typically a scrambled section of De finibus bonorum et malorum, a 1st-century BC Latin text by Cicero, with words altered, added, and removed to make it nonsensical, improper Latin.[citation needed]";s:10:"order_logo";a:0:{}s:13:"use_recaptcha";a:0:{}s:27:"google_recaptcha_secret_key";s:40:"6LcKgzAUAAAAALAXAJkrXIrl-mbfcGlbUlLHzdlP";s:25:"google_recaptcha_site_key";s:40:"6LcKgzAUAAAAAOJkmMXFgGMpGCN7xHLn1Zhj7B-A";s:24:"dashboard_items_per_page";s:2:"12";s:20:"sorting_jobs_default";s:8:"featured";s:24:"prioritize_featured_jobs";s:0:"";s:16:"show_expired_job";s:1:"1";s:13:"jobs_per_page";s:2:"12";s:16:"show_company_job";s:1:"1";s:15:"show_salary_job";s:1:"1";s:17:"show_location_job";s:1:"1";s:12:"jobs_sidebar";s:4:"both";s:11:"job_sidebar";s:5:"right";s:18:"limit_item_related";s:1:"3";s:14:"show_find_jobs";s:1:"1";s:22:"search_form_jobs_style";s:0:"";s:15:"find_jobs_style";s:6:"style2";s:17:"limit_keyword_job";s:1:"9";s:30:"advanced_search_white_job_type";s:2:"no";s:31:"advanced_search_white_job_level";s:2:"no";s:26:"show_filter_alpha_employer";s:1:"1";s:18:"employers_per_page";s:2:"12";s:16:"employer_sidebar";s:5:"right";s:21:"employer_avatar_width";s:0:"";s:22:"employer_avatar_height";s:0:"";s:19:"candidates_per_page";s:2:"12";s:31:"show_advanced_search_candidates";s:0:"";s:17:"candidate_sidebar";s:5:"right";s:22:"candidate_avatar_width";s:0:"";s:23:"candidate_avatar_height";s:0:"";s:14:"google_api_key";s:0:"";s:12:"map_latitude";s:0:"";s:13:"map_logtitude";s:0:"";s:8:"map_zoom";s:0:"";s:10:"map_styles";s:0:"";s:13:"iwj_map_maker";a:0:{}s:8:"currency";s:3:"USD";s:16:"allow_currencies";a:2:{i:0;s:3:"EUR";i:1;s:3:"USD";}s:15:"system_currency";s:3:"USD";s:16:"price_trim_zeros";s:1:"1";s:8:"tax_used";s:0:"";s:9:"tax_value";s:0:"";s:21:"email_register_enable";s:1:"1";s:22:"email_register_subject";s:16:"New Registration";s:22:"email_register_heading";s:16:"New Registration";s:22:"email_register_content";s:500:"Congratulations! Your account has been registered at {$site_title} 
Your username : {$user_login}
{if $auto_generate_password eq true}
Your password has been automatically generated : {$user_password}
{else}
Your password : {$user_password}
{/if}

{if $verify_account eq true}
Please click <a href="{$activation_url}" target="_blank">here</a> to verify your account
{else}
You can manage your account <a href="{$dashboard_url}">here</a>
{/if}

Thank you for choosing InJob!
InJob Team.";s:27:"email_admin_register_enable";s:1:"1";s:28:"email_admin_register_subject";s:16:"New Registration";s:28:"email_admin_register_heading";s:16:"New Registration";s:28:"email_admin_register_content";s:229:"Hi Admin,
A new user has registered account on {$site_title}

following account infomation:
- Display Name: {$display_name}
- Email: {$email}
- Role: {$role}

You can manager <a href="{$edit_url}" target="_blank">here</a>";s:27:"email_verify_account_enable";s:1:"1";s:28:"email_verify_account_subject";s:14:"Verify account";s:28:"email_verify_account_heading";s:14:"Verify account";s:28:"email_verify_account_content";s:150:"Dear {$display_name},
You registed account on {$site_title}, Please click <a href="{$activation_url}" target="_blank">here</a> to verify your account";s:22:"email_resetpass_enable";s:1:"1";s:23:"email_resetpass_subject";s:22:"Reset Password Request";s:23:"email_resetpass_heading";s:14:"Reset Password";s:23:"email_resetpass_content";s:304:"Dear {$user_login} , 

You recently requested to reset your password for your {$site_title} account. 
Click <a class="link" href="{$resetpass_url}">here</a> to reset your password

If you did not request a password reset, please ignore this email or reply to let us know. 

Thank you!
InJob Team.";s:27:"email_delete_account_enable";s:1:"1";s:28:"email_delete_account_subject";s:28:"Account successfully deleted";s:28:"email_delete_account_heading";s:28:"Account successfully deleted";s:28:"email_delete_account_content";s:197:"Dear {$display_name},
We are so sorry about your cancellation and we want to say thank you for your time with us those days over. We wish you have a good time ahead!

Thank you and Best Regards,";s:20:"email_new_job_enable";s:1:"1";s:21:"email_new_job_subject";s:27:"New Class From {$author_name}";s:21:"email_new_job_heading";s:27:"New Class From {$author_name}";s:21:"email_new_job_content";s:725:"Dear {$follower_name}, {$author_name} submited a job <a href="{$job->permalink()}">job</a> on {$site_title}

<div class="job-item">
    <img src="{get_avatar_url($author->get_id())}" alt="{$job->get_title()}">
    <h3>{$job->get_title()}</h3>
    <ul class="job-meta">
        <li class="author">
            <span>Author : </span>
            <span><a href="{$author->permalink()}">{$author->get_display_name()}</a></span>
        </li>
        <li class="salary">
            <span>Sallary : </span>
            <span>{$job->get_salary()}</span>
        </li>
        <li class="address">
            <span>Address : </span>
            <span>{$job->get_address()}</span>
        </li>
    </ul>
</div>
";s:23:"email_review_job_enable";s:1:"1";s:24:"email_review_job_subject";s:10:"Review Job";s:24:"email_review_job_heading";s:10:"Review Job";s:24:"email_review_job_content";s:376:"{if $is_child_job eq true}
Hi Admin {$author_name} has been submited an update of <a href="{$parent_job->permalink()}">{$parent_job->get_title()}</a> you should go to browse it
{else}
Hi Admin {$author_name} has been submited job <a href="{$job->permalink()}">{$job->get_title()}</a> you should go to browse it
{/if}
You can review <a href="{$job->admin_link()}">here</a>";s:25:"email_approved_job_enable";s:1:"1";s:26:"email_approved_job_subject";s:25:"Approved Class {$job_title}";s:26:"email_approved_job_heading";s:27:"Your Class has been approved!";s:26:"email_approved_job_content";s:395:"{if $is_child_job eq true}
Dear {$author_name}, an update of <a href="{$parent_job->permalink()}">{$parent_job->get_title()}</a> has been approved

You can view your job <a href="{$parent_job->permalink()}">here</a>
{else}
Dear {$author_name}, job <a href="{$job->permalink()}">{$job->get_title()}</a> has been approved

You can view your job <a href="{$job->permalink()}">here</a>
{/if}";s:25:"email_rejected_job_enable";s:1:"1";s:26:"email_rejected_job_subject";s:25:"Rejected Class {$job_title}";s:26:"email_rejected_job_heading";s:27:"Your job has been rejected!";s:26:"email_rejected_job_content";s:340:"{if $is_child_job eq true}
Dear {$author_name}, the update of <a href="{$parent_job->permalink()}">{$parent_job->get_title()}</a> has been rejected. 
Please see the reason below:
{else} 
Dear {$author_name}, <a href="{$job->permalink()}">{$job->get_title()}</a> job has been rejected.
Please see the reason below:
{/if}
{$reason}

";s:22:"email_alert_job_enable";s:1:"1";s:23:"email_alert_job_subject";s:81:"Hi {$display_name}, {$total_jobs}  new jobs for {$position} position is available";s:23:"email_alert_job_heading";s:38:"{$total_jobs} new jobs for {$position}";s:23:"email_alert_job_content";s:1129:"Dear {$display_name}, These are new jobs for you

<div class="job-list job-list-email">
{foreach from=$jobs item=job}
    {assign var="author" value=$job->get_author()}
   <div class="job-item">
            <div class="image">
                <img src="{get_avatar_url($author->get_id())}" alt="{$job->get_title()}">
            </div>
            <div class="info-wrap">
                <h3>{$job->get_title()}</h3>
                <ul class="job-meta">
                    <li class="author">
                        <span>Author</span>:<span><a href="{$author->permalink()}">{$author->get_display_name()}</a></span>
                    </li>
                    <li class="salary">
                        <span>Sallary</span>:<span>{$job->get_salary()}</span>
                    </li>
                    <li class="address">
                       <span>Address</span>:<span>{$job->get_full_address()}</span>
                    </li>
                </ul>
            </div>
        </div>
{/foreach}
</div>

Don\'t want to receive new jobs anymore? <a href="{$unsubscribe_link}">Unsubscribe</a>.";s:30:"email_confirm_alert_job_enable";s:1:"1";s:31:"email_confirm_alert_job_subject";s:38:"Confirm registration for new Class Alert";s:31:"email_confirm_alert_job_heading";s:38:"Confirm registration for new Class Alert";s:31:"email_confirm_alert_job_content";s:501:"Dear Admin, We will send the work to you according to the following criteria:

{if !empty($categories)}- Categorie(s): {$categories}{/if}

{if !empty($types)}– Type(s): {$types}{/if}

{if !empty($types)}– Level(s): {$levels}{/if}

{if !empty($locations)}– Location(s): {$loctions}{/if}

{if !empty($salary_from)}– Salary from(s): {$salary_from}{/if}
– Frequency(s): {$frequency}

Please confirm by clicking on the link: <a href="{$confirm_link}" target="_blank">confirm link</a>";s:27:"email_review_profile_enable";s:1:"1";s:28:"email_review_profile_subject";s:14:"Review Profile";s:28:"email_review_profile_heading";s:14:"Review Profile";s:28:"email_review_profile_content";s:304:"{if $profile_author->is_employer() eq true}
Hi Admin,
 User {$author_name} has been submited profile you should go to browse it
{else}
Hi Admin,
 User {$author_name} has been submited resum you should go to browse it
{/if}

You can review <a href="{$profile_admin_url}" target="_blank">here</a>
";s:29:"email_approved_profile_enable";s:1:"1";s:30:"email_approved_profile_subject";s:16:"Approved Profile";s:30:"email_approved_profile_heading";s:31:"Your profile has been approved.";s:30:"email_approved_profile_content";s:190:"Dear {$author_name}
Your profile has been approved. Now you can click <a href="{$dashboard_url}">here</a> to login and carry out actions

Thank you for visiting {$site_title}!
InJob team";s:29:"email_rejected_profile_enable";s:1:"1";s:30:"email_rejected_profile_subject";s:16:"Rejected Profile";s:30:"email_rejected_profile_heading";s:30:"Your profile has been rejected";s:30:"email_rejected_profile_content";s:223:"Dear {$author_name},
Your profile has been rejected please see the following reason:

{$reason}

Click <a href="{$profile_edit_url}">here</a> to update your profile.

Thank you for visiting {$site_title}!
InJob team";s:22:"email_new_order_enable";s:1:"1";s:23:"email_new_order_subject";s:23:"Thank you for ordering!";s:23:"email_new_order_heading";s:23:"Thank you for ordering!";s:23:"email_new_order_content";s:516:"Hi {$author_name},
You have create an order #{$order_number} on {$site_title} please see following details:

=================================================
{$order_description}
Order Number : {$order_number}
Created : {$order_date}
Total Price : {iwj_system_price($order->get_price(), $order->get_currency())}
Order Status : {$order->get_status_title($order->get_status())}
Payment Method : {$order->get_payment_method_title()}
=================================================

Thank you!
InJob team";s:28:"email_new_order_admin_enable";s:1:"1";s:29:"email_new_order_admin_subject";s:9:"New Order";s:29:"email_new_order_admin_heading";s:9:"New Order";s:29:"email_new_order_admin_content";s:503:"Hi Admin,
{$author_name} has been made an order #{$order_number} please see following details:

=================================================
{$order_description}
Order Number : {$order_number}
Created : {$order_date}
Total Price : {iwj_system_price($order->get_price(), $order->get_currency())}
Order Status : {$order->get_status_title($order->get_status())}
Payment Method : {$order->get_payment_method_title()}
=================================================

Thank you!
InJob team";s:23:"email_hold_order_enable";s:1:"1";s:24:"email_hold_order_subject";s:33:"Order #{$order_number} is on hold";s:24:"email_hold_order_heading";s:23:"Thank you for ordering!";s:24:"email_hold_order_content";s:502:"Hi {$author_name},
Your order #{$order_number} has been on hold please see following details:

=================================================
{$order_description}
Order Number : {$order_number}
Created : {$order_date}
Total Price : {iwj_system_price($order->get_price(), $order->get_currency())}
Order Status : {$order->get_status_title($order->get_status())}
Payment Method : {$order->get_payment_method_title()}
=================================================

Thank you!
InJob team";s:28:"email_completed_order_enable";s:1:"1";s:29:"email_completed_order_subject";s:41:"Order #{$order_number} has been completed";s:29:"email_completed_order_heading";s:23:"Thank you for ordering!";s:29:"email_completed_order_content";s:499:"Dear {$author_name},
Your order #{$order_number} has been completed.
Your detailed order:

=================================================
{$order_description}
Order Number : {$order_number}
Created : {$order_date}
Total Price : {iwj_system_price($order->get_price(), $order->get_currency())}
Order Status : {$order->get_status_title($order->get_status())}
Payment Method : {$order->get_payment_method_title()}
=================================================

Thank you!
InJob team";s:26:"email_customer_note_enable";s:1:"1";s:27:"email_customer_note_subject";s:47:"{$site_title} Notification for #{$order_number}";s:27:"email_customer_note_heading";s:26:"Greetings from InJob Team!";s:27:"email_customer_note_content";s:640:"Hello {$author_name}
We would love to say somethings about your order #{$order_number} on {$site_title}

<blockquote>{wpautop( wptexturize( $customer_note ) )}</blockquote>

For your reference, your order details are shown below:
=================================================
{$order_description}
Order Number : {$order_number}
Created : {$order_date}
Total Price : {iwj_system_price($order->get_price(), $order->get_currency())}
Order Status : {$order->get_status_title($order->get_status())}
Payment Method : {$order->get_payment_method_title()}
=================================================

Thank you!
InJob team";s:29:"email_customer_invoice_enable";s:1:"1";s:30:"email_customer_invoice_subject";s:70:"Invoice For Order #{$order_number} from {$order_date} on {$site_title}";s:30:"email_customer_invoice_heading";s:34:"Invoice For Order #{$order_number}";s:35:"email_customer_invoice_paid_subject";s:70:"Invoice For Order #{$order_number} from {$order_date} on {$site_title}";s:35:"email_customer_invoice_paid_heading";s:34:"Invoice For Order #{$order_number}";s:30:"email_customer_invoice_content";s:715:"Dear {$author_name},

{if $order->has_status(\'pending-payment\') eq true } 
Your order has been created on {$site_title} and at the pending status.
Click <a href="{$order_pay_url}">here</a> to carry out your order payment.
{/if}

Thank you for choosing Injob. Here are your detailed order information:

=================================================
{$order_description}
Order Number : {$order_number}
Created : {$order_date}
Total Price : {iwj_system_price($order->get_price(), $order->get_currency())}
Order Status : {$order->get_status_title($order->get_status())}
Payment Method : {$order->get_payment_method_title()}
=================================================

Thank you!
InJob team";s:28:"email_new_application_enable";s:1:"1";s:29:"email_new_application_subject";s:58:"You have been applied for {$job_title} at {$employer_name}";s:29:"email_new_application_heading";s:30:"Thank you for your application";s:29:"email_new_application_content";s:609:"Dear {$candidate_name},
Your application has been sent to {$employer_name}. The employer will consider and contact to you as soon as possible if your abilities match their requirement.

Your detailed application:

=================================================
Fullname :  {$application->get_full_name()}
Email : {$application->get_email()}
{assign var="cv" value=$application->get_cv()}
Teacher CV :  {if isset($cv)}<a href="{$cv[\'url\']}">Download full CV</a>{/if}
Teacher Message : 
{$application->get_message()}
=================================================

Thank you!
InJob team";s:37:"email_new_application_employer_enable";s:1:"1";s:38:"email_new_application_employer_subject";s:32:"New application for {$job_title}";s:38:"email_new_application_employer_heading";s:15:"New application";s:38:"email_new_application_employer_content";s:540:"Dear {$employer_name},
{$candidate_name} applied for your job <a href="{$job->permalink()}">{$job->get_title()}</a> please see following details:

=================================================
Fullname :  {$application->get_full_name()}
Email : {$application->get_email()}
{assign var="cv" value=$application->get_cv()}
Teacher CV :  {if isset($cv)}<a href="{$cv[\'url\']}">Download full CV</a>{/if}
Teacher Message : 
{$application->get_message()}
=================================================

Thank you!
InJob team";s:24:"email_application_enable";s:1:"1";s:25:"email_application_subject";s:40:"Email From {$employer_name} [{$subject}]";s:25:"email_application_heading";s:10:"{$subject}";s:25:"email_application_content";s:10:"{$message}";s:35:"email_application_interview_subject";s:16:"Interview letter";s:35:"email_application_interview_message";s:511:"<p>Hi. #candidate_name#.</p>
<p>Lacinia fusce nam nibh diam rhoncus sodales, vestibulum blandit viverra facilisis velit, ante auctor sociis et ornare. Sociis condimentum massa suscipit nisl parturient platea hac, in iaculis congue nec ridiculus mus, himenaeos consequat vulputate lacus velit natoque. Eleifend euismod interdum sem imperdiet consequat tristique augue per condimentum nam platea feugiat cum, parturient ligula enim ullamcorper vivamus commodo purus.</p>
<p>Best Regard,</p>
<p>InJob inc.</p>
";s:32:"email_application_accept_subject";s:60:"Congratulations! Your resum has passed our application round";s:32:"email_application_accept_message";s:511:"<p>Hi. #candidate_name#.</p>
<p>Lacinia fusce nam nibh diam rhoncus sodales, vestibulum blandit viverra facilisis velit, ante auctor sociis et ornare. Sociis condimentum massa suscipit nisl parturient platea hac, in iaculis congue nec ridiculus mus, himenaeos consequat vulputate lacus velit natoque. Eleifend euismod interdum sem imperdiet consequat tristique augue per condimentum nam platea feugiat cum, parturient ligula enim ullamcorper vivamus commodo purus.</p>
<p>Best Regard,</p>
<p>InJob inc.</p>
";s:32:"email_application_reject_subject";s:62:"Unfortunately! Your resume didn\'t passed our application round";s:32:"email_application_reject_message";s:511:"<p>Hi. #candidate_name#.</p>
<p>Lacinia fusce nam nibh diam rhoncus sodales, vestibulum blandit viverra facilisis velit, ante auctor sociis et ornare. Sociis condimentum massa suscipit nisl parturient platea hac, in iaculis congue nec ridiculus mus, himenaeos consequat vulputate lacus velit natoque. Eleifend euismod interdum sem imperdiet consequat tristique augue per condimentum nam platea feugiat cum, parturient ligula enim ullamcorper vivamus commodo purus.</p>
<p>Best Regard,</p>
<p>InJob inc.</p>
";s:23:"email_new_review_enable";s:1:"1";s:24:"email_new_review_subject";s:10:"New Review";s:24:"email_new_review_heading";s:10:"New Review";s:24:"email_new_review_content";s:265:"Hi Admin,
User {$candidate_name} has wirtten a revriew for {$employer_name}

Following details:
Review Title: {$review_title}
Total Rating: {$rating}
Review Content: {$review_content}

You can review it <a href="{$admin_review_url}" target="_blank">here</a>";s:28:"email_approved_review_enable";s:1:"1";s:29:"email_approved_review_subject";s:10:"New Review";s:29:"email_approved_review_heading";s:10:"New Review";s:29:"email_approved_review_content";s:260:"Dear {$employer_name},
{$candidate_name} has written a review for your company

Following details:
Review Title: {$review_title}
Total Rating: {$rating}
Review Content: {$review_content}

You can view it <a href="{$review_url}" target="_blank">here</a>";s:38:"email_candidate_approved_review_enable";s:1:"1";s:39:"email_candidate_approved_review_subject";s:15:"Approved Review";s:39:"email_candidate_approved_review_heading";s:15:"Approved Review";s:39:"email_candidate_approved_review_content";s:143:"Dear {$candidate_name},
Your review for {$employer_name} has been approved

You can view it <a href="{$review_url}" target="_blank">here</a>";s:28:"email_rejected_review_enable";s:1:"1";s:29:"email_rejected_review_subject";s:15:"Rejected Review";s:29:"email_rejected_review_heading";s:15:"Rejected Review";s:29:"email_rejected_review_content";s:215:"Dear {$candidate_name},

Your Review for {$employer_name} has been rejected.
Following reason:
{$reason}

You can edit it <a  href="{$edit_review_url}" target="_blank">here</a> and send back to us.
Thank you!";s:20:"email_contact_enable";s:1:"1";s:21:"email_contact_subject";s:39:"Message From {$from_email} [{$subject}]";s:21:"email_contact_heading";s:10:"{$subject}";s:21:"email_contact_content";s:181:"Dear {$to_name}, {$from_name} sent to you a message

===================================
{$message}
===================================

This email was sent from {$site_title}";s:22:"email_background_color";s:7:"#eeeeee";s:27:"email_body_background_color";s:7:"#ffffff";s:16:"email_base_color";s:7:"#33427a";s:16:"email_text_color";s:7:"#aaaaaa";s:21:"email_body_text_color";s:7:"#666666";s:14:"email_template";s:1826:"<!DOCTYPE html>
<html {$language_attributes} >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
		<title>{$site_title}</title>
	</head>
	<body {if $is_rtl eq 1}rightmargin{else}leftmargin{/if}="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
		<div id="wrapper" dir="{if $is_rtl eq 1}rtl{else}ltr{/if}">
		    <table id="template_container">
                <tr>
                    <td id="template_top_header">
                        <!-- custom you logo here -->
                    </td>
                </tr>
                <tr>
                    <td>
                        <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="template_table">
                            <thead id="template_header">
                                <tr>
                                    <td align="center" valign="top">
                                        <h1>#email_heading#</h1>
                                    </td>
                                </tr>
                            </thead>
                            <tbody id="template_body">
                                <!-- Content -->
                                <tr>
                                    <td valign="top" id="body_content">
                                        <div id="body_content_inner">
											#email_body_content#
										 </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </td>
                </tr>
				<tr>
					<td id="template_footer">
						<center>Copyright © 2017 InwaveThemes Inc., All rights reserved.</center>
					</td>
				</tr>
            </table>
		</div>
	</body>
</html>";s:12:"email_styles";s:0:"";s:17:"apply_form_enable";s:1:"1";s:10:"apply_form";a:4:{s:9:"full_name";a:5:{s:5:"title";s:8:"Fullname";s:4:"name";s:9:"full_name";s:4:"type";s:4:"text";s:8:"required";s:1:"1";s:9:"pre_value";s:0:"";}s:5:"email";a:5:{s:5:"title";s:5:"Email";s:4:"name";s:5:"email";s:4:"type";s:5:"email";s:8:"required";s:1:"1";s:9:"pre_value";s:0:"";}s:7:"message";a:5:{s:5:"title";s:7:"Message";s:4:"name";s:7:"message";s:4:"type";s:7:"wysiwyg";s:8:"required";s:1:"1";s:9:"pre_value";s:0:"";}s:2:"cv";a:5:{s:5:"title";s:16:"Curriculum Vitae";s:4:"name";s:2:"cv";s:4:"type";s:4:"file";s:8:"required";s:1:"0";s:9:"pre_value";s:16:"pdf,zip,doc,docx";}}s:21:"allow_guest_apply_job";s:1:"0";s:25:"allow_candidate_apply_job";s:1:"2";s:21:"apply_linkedin_enable";s:1:"1";s:24:"apply_linkedin_client_id";s:14:"81ywf2ggtzlet4";s:28:"apply_linkedin_client_secret";s:16:"v3v4LNZOf9fERIgT";s:39:"apply_linkedin_allow_input_cover_letter";s:1:"1";s:38:"apply_linkedin_cover_letter_field_type";s:8:"textarea";s:26:"apply_linkedin_create_user";s:1:"0";s:21:"apply_facebook_enable";s:1:"0";s:22:"apply_facebook_api_key";s:0:"";s:21:"apply_facebook_secret";s:0:"";s:39:"apply_facebook_allow_input_cover_letter";s:1:"1";s:38:"apply_facebook_cover_letter_field_type";s:8:"textarea";s:26:"apply_facebook_create_user";s:1:"0";s:26:"gateway_direct_bank_enable";s:1:"1";s:31:"gateway_direct_bank_description";s:94:"Make your payment direct into your bank account. Please use order ID as the payment reference.";s:21:"gateway_paypal_enable";s:1:"1";s:26:"gateway_paypal_description";s:85:"Pay via PayPal; you can pay with your credit card if you don\'t have a PayPal account.";s:22:"gateway_paypal_sandbox";s:1:"1";s:20:"gateway_paypal_email";s:28:"hoak34-facilitator@gmail.com";s:29:"gateway_paypal_identity_token";s:59:"NmOBSWWDfZU9tmpGiIz0atKE7ydWJuA_4eatU9RPhYkBAc39xRv8svmX-K0";s:30:"gateway_authorizedotnet_enable";s:1:"1";s:35:"gateway_authorizedotnet_description";s:44:"Pay with your credit card via Authorize.net.";s:32:"gateway_authorizedotnet_login_id";s:10:"5j9BfBb6uj";s:39:"gateway_authorizedotnet_transection_key";s:16:"7k8yU85JrJ8b2dWU";s:32:"gateway_authorizedotnet_hash_key";s:3:"hoa";s:36:"gateway_authorizedotnet_working_mode";s:1:"3";s:40:"gateway_authorizedotnet_transaction_mode";s:12:"auth_capture";s:21:"gateway_stripe_enable";s:1:"1";s:26:"gateway_stripe_description";s:37:"Pay with your credit card via Stripe.";s:24:"gateway_stripe_test_mode";s:1:"1";s:30:"gateway_stripe_test_secret_key";s:32:"sk_test_zrL0X8x4owJk6RfnOpjREYuW";s:31:"gateway_stripe_test_publish_key";s:32:"pk_test_QWNaDT5jMiqXS8pA7oBc2bxl";s:25:"gateway_stripe_secret_key";s:0:"";s:26:"gateway_stripe_publish_key";s:0:"";s:21:"gateway_skrill_enable";s:1:"0";s:26:"gateway_skrill_description";s:94:"Make your payment direct into your bank account. Please use order ID as the payment reference.";s:20:"gateway_skrill_email";s:0:"";s:26:"gateway_skrill_merchant_id";s:0:"";s:26:"gateway_skrill_secret_word";s:0:"";s:24:"gateway_skrill_test_mode";s:1:"0";s:22:"social_facebook_enable";s:1:"1";s:23:"social_facebook_api_key";s:16:"1957660671115622";s:22:"social_facebook_secret";s:32:"0d03f1d100c80f42f44fac8049d120d6";s:20:"social_google_enable";s:1:"1";s:23:"social_google_client_id";s:72:"260326072701-inr7bev2okb15bne3ispeug74l4vcsh0.apps.googleusercontent.com";s:21:"social_google_api_key";s:39:"AIzaSyDsZCt-5TRDuu4cZMb6z8ZU6g1tgtmn2Pc";s:20:"social_google_secret";s:24:"gu_EI85teibd9dWYzTc0RZZF";s:21:"social_twitter_enable";s:1:"1";s:27:"social_twitter_consumer_key";s:25:"rbE6i9VNCls8F2SOKeUCFiVZK";s:30:"social_twitter_consumer_secret";s:50:"Ue21i8WMhhwcrNWK8nVUf1ZniY5rYpghYs7IdKTNzE50p213TY";s:22:"social_linkedin_enable";s:1:"1";s:25:"social_linkedin_client_id";s:14:"81ywf2ggtzlet4";s:29:"social_linkedin_client_secret";s:16:"v3v4LNZOf9fERIgT";}';

        $settings = unserialize($settings);
        $settings = self::add_membership_options($settings);

        update_option('iwj_settings',  $settings);
    }

    static function import_options(){

        //will update option
        $options = 'a:340:{s:13:"login_page_id";s:3:"143";s:16:"register_page_id";s:3:"145";s:22:"verify_account_page_id";s:4:"2317";s:16:"lostpass_page_id";s:3:"147";s:17:"dashboard_page_id";s:3:"141";s:12:"jobs_page_id";s:3:"656";s:17:"employers_page_id";s:3:"530";s:18:"candidates_page_id";s:3:"526";s:19:"suggest_job_page_id";s:4:"3374";s:28:"candidate_suggestion_page_id";s:4:"3430";s:25:"terms_and_conditions_page";s:4:"2378";s:19:"privacy_policy_page";s:0:"";s:8:"job_slug";s:3:"job";s:13:"employer_slug";s:8:"employer";s:14:"candidate_slug";s:9:"candidate";s:13:"category_slug";s:3:"cat";s:9:"type_slug";s:4:"type";s:12:"sallary_slug";s:7:"sallary";s:10:"skill_slug";s:5:"skill";s:10:"level_slug";s:5:"level";s:13:"location_slug";s:8:"location";s:25:"disable_employer_register";s:0:"";s:26:"disable_candidate_register";s:1:"1";s:14:"verify_account";s:1:"1";s:30:"registration_generate_password";s:0:"";s:20:"woocommerce_checkout";s:1:"1";s:30:"include_my_account_woocommerce";s:0:"";s:20:"disable_notification";a:0:{}s:19:"display_date_format";s:5:"d/m/Y";s:20:"terms_and_conditions";s:239:"By hitting the <span class="theme-color">"Register"</span> button, you agree to the <a target="_blank" href="{link_terms_and_conditions_page}">Terms conditions</a> and <a target="_blank" href="{link_privacy_policy_page}">Privacy Policy</a>";s:20:"show_gdpr_on_profile";s:1:"1";s:21:"gdpr_on_profile_label";s:87:"I agree to having this website store my submitted infomation, see more infomation below";s:20:"gdpr_on_profile_desc";s:1066:"Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. 

Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.

The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.";s:32:"show_terms_services_on_apply_job";s:1:"1";s:30:"apply_job_terms_services_label";s:87:"I have read and agree to the <a href="#apply_job_terms_services">Terms and Services</a>";s:29:"apply_job_terms_services_desc";s:1066:"Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. 

Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.

The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.";s:37:"show_terms_services_on_c_contact_form";s:0:"";s:33:"candidate_cf_terms_services_label";s:0:"";s:32:"candidate_cf_terms_services_desc";s:0:"";s:37:"show_terms_services_on_e_contact_form";s:0:"";s:32:"employer_cf_terms_services_label";s:0:"";s:31:"employer_cf_terms_services_desc";s:0:"";s:12:"disable_type";s:0:"";s:13:"disable_skill";s:0:"";s:13:"disable_level";s:0:"";s:14:"disable_gender";s:1:"1";s:15:"submit_job_mode";s:1:"1";s:22:"edit_job_auto_approved";s:1:"1";s:27:"edit_free_job_auto_approved";s:0:"";s:21:"new_job_auto_approved";s:1:"1";s:26:"new_free_job_auto_approved";s:0:"";s:22:"delete_draft_job_hours";s:2:"24";s:21:"keep_jobs_delete_user";s:0:"";s:17:"keep_jobs_user_id";s:0:"";s:20:"auto_detect_location";s:0:"";s:18:"allow_adress_types";a:4:{i:0;s:7:"country";i:1;s:27:"administrative_area_level_1";i:2;s:27:"administrative_area_level_2";i:3;s:27:"administrative_area_level_3";}s:16:"disable_language";s:0:"";s:15:"allow_languages";a:0:{}s:14:"exclude_skills";s:16:"abc,fuck,sex,xxx";s:25:"job_suggestion_conditions";a:4:{i:0;s:8:"category";i:1;s:4:"type";i:2;s:5:"level";i:3;s:5:"skill";}s:19:"default_job_content";s:1375:"<h4>Overview</h4>
<blockquote><p>Lorem ipsum dolor sit amet consectetur adipiscing, elit vehicula semper velit vestibulum felis purus, gravida rhoncus vulputate aliquet cras. Conubia libero morbi tristique rutrum elementum dapibus per cras volutpat, semper consequat nisl aenean urna ultricies tincidunt etiam senectus. Rhoncus blandit neque vivamus nullam sodales maecenas felis faucibus, lectus suspendisse vitae donec hendrerit montes ultrices fames, penatibus est pulvinar sagittis proin phareultrices fringilla.</p></blockquote>
<hr />
<h4>What You Will Do</h4>
<ol>
<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>
<li>Auctor class pellentesque augue dignissim venenatis, turpis vestibulum lacinia dignissim venenatis.</li>
<li>Mus arcu euismod ad hac dui, vivamus platea netus.</li>
<li>Neque per nisl posuere sagittis, id platea dui.</li>
<li>A enim magnis dapibus, nullam odio porta, nisl class.</li>
<li>Turpis leo pellentesque per nam, nostra fringilla id.</li>
</ol>
<hr />
<h4>What we can offer you</h4>
<ul>
<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>
<li>Mus arcu euismod ad hac dui, vivamus platea netus.</li>
<li>Neque per nisl posuere sagittis, id platea dui.</li>
<li>A enim magnis dapibus, nullam odio porta, nisl class.</li>
<li>Turpis leo pellentesque per nam, nostra fringilla id.</li>
</ul>
";s:16:"custom_apply_url";s:1:"1";s:25:"allow_post_job_multi_cats";s:1:"1";s:34:"maximum_number_categories_selected";s:1:"3";s:9:"job_price";s:1:"5";s:15:"renew_job_price";s:1:"5";s:10:"job_expiry";s:1:"1";s:15:"job_expiry_unit";s:4:"year";s:18:"featured_job_price";s:1:"5";s:19:"featured_job_expiry";s:1:"1";s:24:"featured_job_expiry_unit";s:4:"year";s:19:"package_featured_id";s:4:"1577";s:15:"free_package_id";s:3:"198";s:18:"free_package_times";s:1:"1";s:23:"employer_login_redirect";s:0:"";s:22:"employer_auto_approved";s:0:"";s:28:"show_employer_public_profile";s:0:"";s:31:"employer_can_delete_application";s:1:"1";s:24:"candidate_login_redirect";s:0:"";s:23:"candidate_auto_approved";s:0:"";s:15:"view_free_resum";s:0:"";s:21:"free_resum_package_id";s:0:"";s:24:"free_resum_package_times";s:1:"5";s:14:"apply_job_mode";s:1:"1";s:25:"free_apply_job_package_id";s:0:"";s:28:"free_apply_job_package_times";s:0:"";s:31:"candidate_suggestion_conditions";a:1:{i:0;s:16:"profile_category";}s:29:"show_candidate_public_profile";s:0:"";s:20:"maximum_file_size_cv";s:1:"5";s:14:"disable_review";s:0:"";s:14:"review_options";s:72:"Work / Life Balance
Comp & Benefits
Senior Management
Culture & Value";s:25:"edit_review_auto_approved";s:0:"";s:26:"delete_pending_order_hours";s:2:"48";s:16:"order_infomation";s:217:"My name is imran  typically a scrambled section of De finibus bonorum et malorum, a 1st-century BC Latin text by Cicero, with words altered, added, and removed to make it nonsensical, improper Latin.[citation needed]";s:10:"order_logo";a:0:{}s:13:"use_recaptcha";a:0:{}s:27:"google_recaptcha_secret_key";s:40:"6LcKgzAUAAAAALAXAJkrXIrl-mbfcGlbUlLHzdlP";s:25:"google_recaptcha_site_key";s:40:"6LcKgzAUAAAAAOJkmMXFgGMpGCN7xHLn1Zhj7B-A";s:24:"dashboard_items_per_page";s:2:"12";s:21:"jobs_taxonomy_version";s:6:"style1";s:27:"number_column_grid_taxonomy";s:1:"2";s:19:"job_details_version";s:2:"v1";s:20:"sorting_jobs_default";s:8:"featured";s:24:"prioritize_featured_jobs";s:0:"";s:16:"show_expired_job";s:1:"1";s:13:"jobs_per_page";s:2:"12";s:16:"show_company_job";s:1:"1";s:21:"show_company_logo_job";s:1:"1";s:19:"show_categories_job";s:1:"1";s:15:"show_salary_job";s:0:"";s:17:"show_location_job";s:1:"1";s:15:"show_skills_job";s:1:"1";s:20:"show_posted_date_job";s:1:"1";s:12:"jobs_sidebar";s:4:"left";s:11:"job_sidebar";s:5:"right";s:18:"limit_item_related";s:1:"3";s:21:"show_search_form_jobs";s:1:"1";s:22:"search_form_jobs_style";s:6:"style1";s:15:"find_jobs_style";s:6:"style2";s:26:"advanced_search_jobs_style";s:6:"style2";s:17:"limit_keyword_job";s:1:"9";s:14:"show_print_job";s:1:"1";s:17:"show_rss_feed_job";s:1:"1";s:26:"show_filter_alpha_employer";s:1:"1";s:18:"employers_per_page";s:2:"12";s:24:"employer_details_version";s:2:"v2";s:16:"employer_sidebar";s:5:"right";s:22:"show_rss_feed_employer";s:1:"1";s:21:"employer_avatar_width";s:0:"";s:22:"employer_avatar_height";s:0:"";s:19:"candidates_per_page";s:2:"12";s:31:"show_advanced_search_candidates";s:0:"";s:25:"candidate_details_version";s:2:"v2";s:17:"candidate_sidebar";s:5:"right";s:23:"show_rss_feed_candidate";s:1:"1";s:22:"candidate_avatar_width";s:0:"";s:23:"candidate_avatar_height";s:0:"";s:14:"google_api_key";s:0:"";s:12:"map_latitude";s:9:"21.027964";s:13:"map_logtitude";s:10:"105.851013";s:8:"map_zoom";s:0:"";s:10:"map_styles";s:0:"";s:13:"iwj_map_maker";a:0:{}s:8:"currency";s:3:"USD";s:16:"allow_currencies";a:2:{i:0;s:3:"EUR";i:1;s:3:"USD";}s:15:"system_currency";s:3:"USD";s:16:"price_trim_zeros";s:1:"1";s:8:"tax_used";s:0:"";s:9:"tax_value";s:0:"";s:21:"email_register_enable";s:1:"1";s:22:"email_register_subject";s:16:"New Registration";s:22:"email_register_heading";s:16:"New Registration";s:22:"email_register_content";s:500:"Congratulations! Your account has been registered at {$site_title} 
Your username : {$user_login}
{if $auto_generate_password eq true}
Your password has been automatically generated : {$user_password}
{else}
Your password : {$user_password}
{/if}

{if $verify_account eq true}
Please click <a href="{$activation_url}" target="_blank">here</a> to verify your account
{else}
You can manage your account <a href="{$dashboard_url}">here</a>
{/if}

Thank you for choosing InJob!
InJob Team.";s:27:"email_admin_register_enable";s:1:"1";s:28:"email_admin_register_subject";s:16:"New Registration";s:28:"email_admin_register_heading";s:16:"New Registration";s:28:"email_admin_register_content";s:229:"Hi Admin,
A new user has registered account on {$site_title}

following account infomation:
- Display Name: {$display_name}
- Email: {$email}
- Role: {$role}

You can manager <a href="{$edit_url}" target="_blank">here</a>";s:27:"email_verify_account_enable";s:1:"1";s:28:"email_verify_account_subject";s:14:"Verify account";s:28:"email_verify_account_heading";s:14:"Verify account";s:28:"email_verify_account_content";s:150:"Dear {$display_name},
You registed account on {$site_title}, Please click <a href="{$activation_url}" target="_blank">here</a> to verify your account";s:22:"email_resetpass_enable";s:1:"1";s:23:"email_resetpass_subject";s:22:"Reset Password Request";s:23:"email_resetpass_heading";s:14:"Reset Password";s:23:"email_resetpass_content";s:304:"Dear {$user_login} , 

You recently requested to reset your password for your {$site_title} account. 
Click <a class="link" href="{$resetpass_url}">here</a> to reset your password

If you did not request a password reset, please ignore this email or reply to let us know. 

Thank you!
InJob Team.";s:27:"email_delete_account_enable";s:1:"1";s:28:"email_delete_account_subject";s:28:"Account successfully deleted";s:28:"email_delete_account_heading";s:28:"Account successfully deleted";s:28:"email_delete_account_content";s:197:"Dear {$display_name},
We are so sorry about your cancellation and we want to say thank you for your time with us those days over. We wish you have a good time ahead!

Thank you and Best Regards,";s:20:"email_new_job_enable";s:1:"1";s:21:"email_new_job_subject";s:27:"New Class From {$author_name}";s:21:"email_new_job_heading";s:27:"New Class From {$author_name}";s:21:"email_new_job_content";s:725:"Dear {$follower_name}, {$author_name} submited a job <a href="{$job->permalink()}">job</a> on {$site_title}

<div class="job-item">
    <img src="{get_avatar_url($author->get_id())}" alt="{$job->get_title()}">
    <h3>{$job->get_title()}</h3>
    <ul class="job-meta">
        <li class="author">
            <span>Author : </span>
            <span><a href="{$author->permalink()}">{$author->get_display_name()}</a></span>
        </li>
        <li class="salary">
            <span>Sallary : </span>
            <span>{$job->get_salary()}</span>
        </li>
        <li class="address">
            <span>Address : </span>
            <span>{$job->get_address()}</span>
        </li>
    </ul>
</div>
";s:23:"email_review_job_enable";s:1:"1";s:24:"email_review_job_subject";s:10:"Review Job";s:24:"email_review_job_heading";s:10:"Review Job";s:24:"email_review_job_content";s:376:"{if $is_child_job eq true}
Hi Admin {$author_name} has been submited an update of <a href="{$parent_job->permalink()}">{$parent_job->get_title()}</a> you should go to browse it
{else}
Hi Admin {$author_name} has been submited job <a href="{$job->permalink()}">{$job->get_title()}</a> you should go to browse it
{/if}
You can review <a href="{$job->admin_link()}">here</a>";s:25:"email_approved_job_enable";s:1:"1";s:26:"email_approved_job_subject";s:25:"Approved Class {$job_title}";s:26:"email_approved_job_heading";s:27:"Your Class has been approved!";s:26:"email_approved_job_content";s:395:"{if $is_child_job eq true}
Dear {$author_name}, an update of <a href="{$parent_job->permalink()}">{$parent_job->get_title()}</a> has been approved

You can view your job <a href="{$parent_job->permalink()}">here</a>
{else}
Dear {$author_name}, job <a href="{$job->permalink()}">{$job->get_title()}</a> has been approved

You can view your job <a href="{$job->permalink()}">here</a>
{/if}";s:25:"email_rejected_job_enable";s:1:"1";s:26:"email_rejected_job_subject";s:25:"Rejected Class {$job_title}";s:26:"email_rejected_job_heading";s:27:"Your job has been rejected!";s:26:"email_rejected_job_content";s:340:"{if $is_child_job eq true}
Dear {$author_name}, the update of <a href="{$parent_job->permalink()}">{$parent_job->get_title()}</a> has been rejected. 
Please see the reason below:
{else} 
Dear {$author_name}, <a href="{$job->permalink()}">{$job->get_title()}</a> job has been rejected.
Please see the reason below:
{/if}
{$reason}

";s:22:"email_alert_job_enable";s:1:"1";s:23:"email_alert_job_subject";s:81:"Hi {$display_name}, {$total_jobs}  new jobs for {$position} position is available";s:23:"email_alert_job_heading";s:38:"{$total_jobs} new jobs for {$position}";s:23:"email_alert_job_content";s:1129:"Dear {$display_name}, These are new jobs for you

<div class="job-list job-list-email">
{foreach from=$jobs item=job}
    {assign var="author" value=$job->get_author()}
   <div class="job-item">
            <div class="image">
                <img src="{get_avatar_url($author->get_id())}" alt="{$job->get_title()}">
            </div>
            <div class="info-wrap">
                <h3>{$job->get_title()}</h3>
                <ul class="job-meta">
                    <li class="author">
                        <span>Author</span>:<span><a href="{$author->permalink()}">{$author->get_display_name()}</a></span>
                    </li>
                    <li class="salary">
                        <span>Sallary</span>:<span>{$job->get_salary()}</span>
                    </li>
                    <li class="address">
                       <span>Address</span>:<span>{$job->get_full_address()}</span>
                    </li>
                </ul>
            </div>
        </div>
{/foreach}
</div>

Don\'t want to receive new jobs anymore? <a href="{$unsubscribe_link}">Unsubscribe</a>.";s:30:"email_confirm_alert_job_enable";s:1:"1";s:31:"email_confirm_alert_job_subject";s:38:"Confirm registration for new Class Alert";s:31:"email_confirm_alert_job_heading";s:38:"Confirm registration for new Class Alert";s:31:"email_confirm_alert_job_content";s:501:"Dear Admin, We will send the work to you according to the following criteria:

{if !empty($categories)}- Categorie(s): {$categories}{/if}

{if !empty($types)}– Type(s): {$types}{/if}

{if !empty($types)}– Level(s): {$levels}{/if}

{if !empty($locations)}– Location(s): {$loctions}{/if}

{if !empty($salary_from)}– Salary from(s): {$salary_from}{/if}
– Frequency(s): {$frequency}

Please confirm by clicking on the link: <a href="{$confirm_link}" target="_blank">confirm link</a>";s:30:"email_job_expiry_notice_enable";s:1:"1";s:31:"email_job_expiry_notice_subject";s:33:"[{$site_title}] Class expiry notice";s:31:"email_job_expiry_notice_content";s:349:"Hi {$user_name},
We would like to inform you that your package {$job_title} is about to expire on {$expiry_date}.
{if $can_renew eq true}
Please <a href="{$renew_job_url}" target="_blank">click here</a> to renew your job now.
{else}
Please <a href="{$edit_job_url}" target="_blank">click here</a> to edit your job.
{/if}
Regards,
InJob Team.";s:29:"send_job_expiry_notice_before";s:1:"5";s:27:"send_job_expiry_notice_days";s:1:"3";s:27:"email_review_profile_enable";s:1:"1";s:28:"email_review_profile_subject";s:14:"Review Profile";s:28:"email_review_profile_heading";s:14:"Review Profile";s:28:"email_review_profile_content";s:304:"{if $profile_author->is_employer() eq true}
Hi Admin,
 User {$author_name} has been submited profile you should go to browse it
{else}
Hi Admin,
 User {$author_name} has been submited resum you should go to browse it
{/if}

You can review <a href="{$profile_admin_url}" target="_blank">here</a>
";s:29:"email_approved_profile_enable";s:1:"1";s:30:"email_approved_profile_subject";s:16:"Approved Profile";s:30:"email_approved_profile_heading";s:31:"Your profile has been approved.";s:30:"email_approved_profile_content";s:190:"Dear {$author_name}
Your profile has been approved. Now you can click <a href="{$dashboard_url}">here</a> to login and carry out actions

Thank you for visiting {$site_title}!
InJob team";s:29:"email_rejected_profile_enable";s:1:"1";s:30:"email_rejected_profile_subject";s:16:"Rejected Profile";s:30:"email_rejected_profile_heading";s:30:"Your profile has been rejected";s:30:"email_rejected_profile_content";s:223:"Dear {$author_name},
Your profile has been rejected please see the following reason:

{$reason}

Click <a href="{$profile_edit_url}">here</a> to update your profile.

Thank you for visiting {$site_title}!
InJob team";s:22:"email_new_order_enable";s:1:"1";s:23:"email_new_order_subject";s:23:"Thank you for ordering!";s:23:"email_new_order_heading";s:23:"Thank you for ordering!";s:23:"email_new_order_content";s:516:"Hi {$author_name},
You have create an order #{$order_number} on {$site_title} please see following details:

=================================================
{$order_description}
Order Number : {$order_number}
Created : {$order_date}
Total Price : {iwj_system_price($order->get_price(), $order->get_currency())}
Order Status : {$order->get_status_title($order->get_status())}
Payment Method : {$order->get_payment_method_title()}
=================================================

Thank you!
InJob team";s:28:"email_new_order_admin_enable";s:1:"1";s:29:"email_new_order_admin_subject";s:9:"New Order";s:29:"email_new_order_admin_heading";s:9:"New Order";s:29:"email_new_order_admin_content";s:503:"Hi Admin,
{$author_name} has been made an order #{$order_number} please see following details:

=================================================
{$order_description}
Order Number : {$order_number}
Created : {$order_date}
Total Price : {iwj_system_price($order->get_price(), $order->get_currency())}
Order Status : {$order->get_status_title($order->get_status())}
Payment Method : {$order->get_payment_method_title()}
=================================================

Thank you!
InJob team";s:23:"email_hold_order_enable";s:1:"1";s:24:"email_hold_order_subject";s:33:"Order #{$order_number} is on hold";s:24:"email_hold_order_heading";s:23:"Thank you for ordering!";s:24:"email_hold_order_content";s:502:"Hi {$author_name},
Your order #{$order_number} has been on hold please see following details:

=================================================
{$order_description}
Order Number : {$order_number}
Created : {$order_date}
Total Price : {iwj_system_price($order->get_price(), $order->get_currency())}
Order Status : {$order->get_status_title($order->get_status())}
Payment Method : {$order->get_payment_method_title()}
=================================================

Thank you!
InJob team";s:28:"email_completed_order_enable";s:1:"1";s:29:"email_completed_order_subject";s:41:"Order #{$order_number} has been completed";s:29:"email_completed_order_heading";s:23:"Thank you for ordering!";s:29:"email_completed_order_content";s:499:"Dear {$author_name},
Your order #{$order_number} has been completed.
Your detailed order:

=================================================
{$order_description}
Order Number : {$order_number}
Created : {$order_date}
Total Price : {iwj_system_price($order->get_price(), $order->get_currency())}
Order Status : {$order->get_status_title($order->get_status())}
Payment Method : {$order->get_payment_method_title()}
=================================================

Thank you!
InJob team";s:26:"email_customer_note_enable";s:1:"1";s:27:"email_customer_note_subject";s:47:"{$site_title} Notification for #{$order_number}";s:27:"email_customer_note_heading";s:26:"Greetings from InJob Team!";s:27:"email_customer_note_content";s:640:"Hello {$author_name}
We would love to say somethings about your order #{$order_number} on {$site_title}

<blockquote>{wpautop( wptexturize( $customer_note ) )}</blockquote>

For your reference, your order details are shown below:
=================================================
{$order_description}
Order Number : {$order_number}
Created : {$order_date}
Total Price : {iwj_system_price($order->get_price(), $order->get_currency())}
Order Status : {$order->get_status_title($order->get_status())}
Payment Method : {$order->get_payment_method_title()}
=================================================

Thank you!
InJob team";s:29:"email_customer_invoice_enable";s:1:"1";s:30:"email_customer_invoice_subject";s:70:"Invoice For Order #{$order_number} from {$order_date} on {$site_title}";s:30:"email_customer_invoice_heading";s:34:"Invoice For Order #{$order_number}";s:35:"email_customer_invoice_paid_subject";s:70:"Invoice For Order #{$order_number} from {$order_date} on {$site_title}";s:35:"email_customer_invoice_paid_heading";s:34:"Invoice For Order #{$order_number}";s:30:"email_customer_invoice_content";s:715:"Dear {$author_name},

{if $order->has_status(\'pending-payment\') eq true } 
Your order has been created on {$site_title} and at the pending status.
Click <a href="{$order_pay_url}">here</a> to carry out your order payment.
{/if}

Thank you for choosing Injob. Here are your detailed order information:

=================================================
{$order_description}
Order Number : {$order_number}
Created : {$order_date}
Total Price : {iwj_system_price($order->get_price(), $order->get_currency())}
Order Status : {$order->get_status_title($order->get_status())}
Payment Method : {$order->get_payment_method_title()}
=================================================

Thank you!
InJob team";s:28:"email_new_application_enable";s:1:"1";s:29:"email_new_application_subject";s:58:"You have been applied for {$job_title} at {$employer_name}";s:29:"email_new_application_heading";s:30:"Thank you for your application";s:29:"email_new_application_content";s:609:"Dear {$candidate_name},
Your application has been sent to {$employer_name}. The employer will consider and contact to you as soon as possible if your abilities match their requirement.

Your detailed application:

=================================================
Fullname :  {$application->get_full_name()}
Email : {$application->get_email()}
{assign var="cv" value=$application->get_cv()}
Teacher CV :  {if isset($cv)}<a href="{$cv[\'url\']}">Download full CV</a>{/if}
Teacher Message : 
{$application->get_message()}
=================================================

Thank you!
InJob team";s:37:"email_new_application_employer_enable";s:1:"1";s:38:"email_new_application_employer_subject";s:32:"New application for {$job_title}";s:38:"email_new_application_employer_heading";s:15:"New application";s:38:"email_new_application_employer_content";s:540:"Dear {$employer_name},
{$candidate_name} applied for your job <a href="{$job->permalink()}">{$job->get_title()}</a> please see following details:

=================================================
Fullname :  {$application->get_full_name()}
Email : {$application->get_email()}
{assign var="cv" value=$application->get_cv()}
Teacher CV :  {if isset($cv)}<a href="{$cv[\'url\']}">Download full CV</a>{/if}
Teacher Message : 
{$application->get_message()}
=================================================

Thank you!
InJob team";s:24:"email_application_enable";s:1:"1";s:25:"email_application_subject";s:40:"Email From {$employer_name} [{$subject}]";s:25:"email_application_heading";s:33:"{$subject}Email From {$from_name}";s:25:"email_application_content";s:10:"{$message}";s:35:"email_application_interview_subject";s:16:"Interview letter";s:35:"email_application_interview_message";s:511:"<p>Hi. #candidate_name#.</p>
<p>Lacinia fusce nam nibh diam rhoncus sodales, vestibulum blandit viverra facilisis velit, ante auctor sociis et ornare. Sociis condimentum massa suscipit nisl parturient platea hac, in iaculis congue nec ridiculus mus, himenaeos consequat vulputate lacus velit natoque. Eleifend euismod interdum sem imperdiet consequat tristique augue per condimentum nam platea feugiat cum, parturient ligula enim ullamcorper vivamus commodo purus.</p>
<p>Best Regard,</p>
<p>InJob inc.</p>
";s:32:"email_application_accept_subject";s:60:"Congratulations! Your resum has passed our application round";s:32:"email_application_accept_message";s:511:"<p>Hi. #candidate_name#.</p>
<p>Lacinia fusce nam nibh diam rhoncus sodales, vestibulum blandit viverra facilisis velit, ante auctor sociis et ornare. Sociis condimentum massa suscipit nisl parturient platea hac, in iaculis congue nec ridiculus mus, himenaeos consequat vulputate lacus velit natoque. Eleifend euismod interdum sem imperdiet consequat tristique augue per condimentum nam platea feugiat cum, parturient ligula enim ullamcorper vivamus commodo purus.</p>
<p>Best Regard,</p>
<p>InJob inc.</p>
";s:32:"email_application_reject_subject";s:62:"Unfortunately! Your resume didn\'t passed our application round";s:32:"email_application_reject_message";s:511:"<p>Hi. #candidate_name#.</p>
<p>Lacinia fusce nam nibh diam rhoncus sodales, vestibulum blandit viverra facilisis velit, ante auctor sociis et ornare. Sociis condimentum massa suscipit nisl parturient platea hac, in iaculis congue nec ridiculus mus, himenaeos consequat vulputate lacus velit natoque. Eleifend euismod interdum sem imperdiet consequat tristique augue per condimentum nam platea feugiat cum, parturient ligula enim ullamcorper vivamus commodo purus.</p>
<p>Best Regard,</p>
<p>InJob inc.</p>
";s:23:"email_new_review_enable";s:1:"1";s:24:"email_new_review_subject";s:10:"New Review";s:24:"email_new_review_heading";s:10:"New Review";s:24:"email_new_review_content";s:265:"Hi Admin,
User {$candidate_name} has wirtten a revriew for {$employer_name}

Following details:
Review Title: {$review_title}
Total Rating: {$rating}
Review Content: {$review_content}

You can review it <a href="{$admin_review_url}" target="_blank">here</a>";s:28:"email_approved_review_enable";s:1:"1";s:29:"email_approved_review_subject";s:10:"New Review";s:29:"email_approved_review_heading";s:10:"New Review";s:29:"email_approved_review_content";s:260:"Dear {$employer_name},
{$candidate_name} has written a review for your company

Following details:
Review Title: {$review_title}
Total Rating: {$rating}
Review Content: {$review_content}

You can view it <a href="{$review_url}" target="_blank">here</a>";s:38:"email_candidate_approved_review_enable";s:1:"1";s:39:"email_candidate_approved_review_subject";s:15:"Approved Review";s:39:"email_candidate_approved_review_heading";s:15:"Approved Review";s:39:"email_candidate_approved_review_content";s:143:"Dear {$candidate_name},
Your review for {$employer_name} has been approved

You can view it <a href="{$review_url}" target="_blank">here</a>";s:28:"email_rejected_review_enable";s:1:"1";s:29:"email_rejected_review_subject";s:15:"Rejected Review";s:29:"email_rejected_review_heading";s:15:"Rejected Review";s:29:"email_rejected_review_content";s:215:"Dear {$candidate_name},

Your Review for {$employer_name} has been rejected.
Following reason:
{$reason}

You can edit it <a  href="{$edit_review_url}" target="_blank">here</a> and send back to us.
Thank you!";s:20:"email_contact_enable";s:1:"1";s:21:"email_contact_subject";s:39:"Message From {$from_email} [{$subject}]";s:21:"email_contact_heading";s:10:"{$subject}";s:21:"email_contact_content";s:181:"Dear {$to_name}, {$from_name} sent to you a message

===================================
{$message}
===================================

This email was sent from {$site_title}";s:15:"email_from_name";s:0:"";s:18:"email_from_address";s:0:"";s:20:"admin_email_receiver";s:0:"";s:22:"email_background_color";s:7:"#eeeeee";s:27:"email_body_background_color";s:7:"#ffffff";s:16:"email_base_color";s:7:"#33427a";s:16:"email_text_color";s:7:"#aaaaaa";s:21:"email_body_text_color";s:7:"#666666";s:14:"email_template";s:1826:"<!DOCTYPE html>
<html {$language_attributes} >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
		<title>{$site_title}</title>
	</head>
	<body {if $is_rtl eq 1}rightmargin{else}leftmargin{/if}="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
		<div id="wrapper" dir="{if $is_rtl eq 1}rtl{else}ltr{/if}">
		    <table id="template_container">
                <tr>
                    <td id="template_top_header">
                        <!-- custom you logo here -->
                    </td>
                </tr>
                <tr>
                    <td>
                        <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="template_table">
                            <thead id="template_header">
                                <tr>
                                    <td align="center" valign="top">
                                        <h1>#email_heading#</h1>
                                    </td>
                                </tr>
                            </thead>
                            <tbody id="template_body">
                                <!-- Content -->
                                <tr>
                                    <td valign="top" id="body_content">
                                        <div id="body_content_inner">
											#email_body_content#
										 </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </td>
                </tr>
				<tr>
					<td id="template_footer">
						<center>Copyright © 2017 InwaveThemes Inc., All rights reserved.</center>
					</td>
				</tr>
            </table>
		</div>
	</body>
</html>";s:12:"email_styles";s:0:"";s:17:"apply_form_enable";s:1:"1";s:10:"apply_form";a:4:{s:9:"full_name";a:5:{s:5:"title";s:8:"Fullname";s:4:"name";s:9:"full_name";s:4:"type";s:4:"text";s:8:"required";s:1:"1";s:9:"pre_value";s:0:"";}s:5:"email";a:5:{s:5:"title";s:5:"Email";s:4:"name";s:5:"email";s:4:"type";s:5:"email";s:8:"required";s:1:"1";s:9:"pre_value";s:0:"";}s:7:"message";a:5:{s:5:"title";s:7:"Message";s:4:"name";s:7:"message";s:4:"type";s:7:"wysiwyg";s:8:"required";s:1:"1";s:9:"pre_value";s:0:"";}s:2:"cv";a:5:{s:5:"title";s:16:"Curriculum Vitae";s:4:"name";s:2:"cv";s:4:"type";s:4:"file";s:8:"required";s:1:"0";s:9:"pre_value";s:16:"pdf,zip,doc,docx";}}s:21:"allow_guest_apply_job";s:1:"1";s:25:"allow_candidate_apply_job";s:1:"2";s:21:"apply_linkedin_enable";s:1:"1";s:24:"apply_linkedin_client_id";s:14:"81ywf2ggtzlet4";s:28:"apply_linkedin_client_secret";s:16:"v3v4LNZOf9fERIgT";s:39:"apply_linkedin_allow_input_cover_letter";s:1:"1";s:38:"apply_linkedin_cover_letter_field_type";s:8:"textarea";s:26:"apply_linkedin_create_user";s:1:"0";s:21:"apply_facebook_enable";s:1:"0";s:22:"apply_facebook_api_key";s:0:"";s:21:"apply_facebook_secret";s:0:"";s:39:"apply_facebook_allow_input_cover_letter";s:1:"1";s:38:"apply_facebook_cover_letter_field_type";s:8:"textarea";s:26:"apply_facebook_create_user";s:1:"0";s:26:"gateway_direct_bank_enable";s:1:"1";s:31:"gateway_direct_bank_description";s:94:"Make your payment direct into your bank account. Please use order ID as the payment reference.";s:21:"gateway_paypal_enable";s:1:"1";s:26:"gateway_paypal_description";s:85:"Pay via PayPal; you can pay with your credit card if you don\'t have a PayPal account.";s:22:"gateway_paypal_sandbox";s:1:"1";s:20:"gateway_paypal_email";s:28:"hoak34-facilitator@gmail.com";s:29:"gateway_paypal_identity_token";s:59:"NmOBSWWDfZU9tmpGiIz0atKE7ydWJuA_4eatU9RPhYkBAc39xRv8svmX-K0";s:30:"gateway_authorizedotnet_enable";s:1:"1";s:35:"gateway_authorizedotnet_description";s:44:"Pay with your credit card via Authorize.net.";s:32:"gateway_authorizedotnet_login_id";s:10:"5j9BfBb6uj";s:39:"gateway_authorizedotnet_transection_key";s:16:"7k8yU85JrJ8b2dWU";s:32:"gateway_authorizedotnet_hash_key";s:3:"hoa";s:36:"gateway_authorizedotnet_working_mode";s:1:"3";s:40:"gateway_authorizedotnet_transaction_mode";s:12:"auth_capture";s:21:"gateway_stripe_enable";s:1:"1";s:26:"gateway_stripe_description";s:37:"Pay with your credit card via Stripe.";s:24:"gateway_stripe_test_mode";s:1:"1";s:30:"gateway_stripe_test_secret_key";s:32:"sk_test_zrL0X8x4owJk6RfnOpjREYuW";s:31:"gateway_stripe_test_publish_key";s:32:"pk_test_QWNaDT5jMiqXS8pA7oBc2bxl";s:25:"gateway_stripe_secret_key";s:0:"";s:26:"gateway_stripe_publish_key";s:0:"";s:21:"gateway_skrill_enable";s:1:"0";s:26:"gateway_skrill_description";s:94:"Make your payment direct into your bank account. Please use order ID as the payment reference.";s:20:"gateway_skrill_email";s:0:"";s:26:"gateway_skrill_merchant_id";s:0:"";s:26:"gateway_skrill_secret_word";s:0:"";s:24:"gateway_skrill_test_mode";s:1:"0";s:22:"social_facebook_enable";s:1:"1";s:23:"social_facebook_api_key";s:16:"1957660671115622";s:22:"social_facebook_secret";s:32:"0d03f1d100c80f42f44fac8049d120d6";s:20:"social_google_enable";s:1:"1";s:23:"social_google_client_id";s:72:"260326072701-inr7bev2okb15bne3ispeug74l4vcsh0.apps.googleusercontent.com";s:21:"social_google_api_key";s:39:"AIzaSyDsZCt-5TRDuu4cZMb6z8ZU6g1tgtmn2Pc";s:20:"social_google_secret";s:24:"gu_EI85teibd9dWYzTc0RZZF";s:21:"social_twitter_enable";s:1:"1";s:27:"social_twitter_consumer_key";s:25:"rbE6i9VNCls8F2SOKeUCFiVZK";s:30:"social_twitter_consumer_secret";s:50:"Ue21i8WMhhwcrNWK8nVUf1ZniY5rYpghYs7IdKTNzE50p213TY";s:22:"social_linkedin_enable";s:1:"1";s:25:"social_linkedin_client_id";s:14:"81ywf2ggtzlet4";s:29:"social_linkedin_client_secret";s:16:"v3v4LNZOf9fERIgT";}';

        if(is_string($options)){
            $options = unserialize($options);
        }

        $login = get_page_by_title('Login');
        $register = get_page_by_title('Register');
        $lostpass = get_page_by_title('Lost Password');
        $dashboard = get_page_by_title('Dashboard');
        $jobs = get_page_by_title('Classes');
        $employers = get_page_by_title('Students');
        $candidates = get_page_by_title('Teachers');
        $job_suggestion = get_page_by_title('Job Suggestion');
        $candidate_suggestion = get_page_by_title('Teacher Suggestion');
        $term_and_conditions = get_page_by_title('Term And Conditions');

        if($login){
            $options['login_page_id'] = $login->ID;
        }
        if($register){
            $options['register_page_id'] = $register->ID;
        }
        if($lostpass){
            $options['lostpass_page_id'] = $lostpass->ID;
        }
        if($dashboard){
            $options['dashboard_page_id'] = $dashboard->ID;
        }
        if($jobs){
            $options['jobs_page_id'] = $jobs->ID;
        }
        if($employers){
            $options['employers_page_id'] = $employers->ID;
        }
        if($candidates){
            $options['candidates_page_id'] = $candidates->ID;
        }
        if($job_suggestion){
            $options['suggest_job_page_id'] = $job_suggestion->ID;
        }
        if($candidate_suggestion){
            $options['candidate_suggestion_page_id'] = $candidate_suggestion->ID;
        }
        if($term_and_conditions){
            $options['terms_and_conditions_page'] = $term_and_conditions->ID;
        }

        $featured_package = get_page_by_title('Premium', 'OBJECT', 'iwj_package');
        if($featured_package){
            $options['package_featured_id'] = $featured_package->ID;
        }

        $freepackage = get_page_by_title('Free', 'OBJECT', 'iwj_package');
        if($freepackage){
            $options['free_package_id'] = $freepackage->ID;
        }

        $options = self::add_membership_options($options);

        update_option('iwj_settings', $options);
    }

    static function create_cron_jobs(){
        if ( !wp_next_scheduled( 'iwj_alert_job_daily' ) ) {
            wp_schedule_event( current_time( 'timestamp' ), 'daily', 'iwj_alert_job_daily');
        }
        if ( !wp_next_scheduled( 'iwj_alert_job_weekly' ) ) {
            wp_schedule_event( current_time( 'timestamp' ), 'weekly', 'iwj_alert_job_weekly');
        }
        if ( !wp_next_scheduled( 'iwj_check_featured_job' ) ) {
            wp_schedule_event( current_time( 'timestamp' ), 'daily', 'iwj_check_featured_job');
        }

        wp_clear_scheduled_hook( 'iwj_membership_expiry_notice' );
        wp_schedule_event( current_time( 'timestamp' ), 'daily', 'iwj_membership_expiry_notice');

        wp_clear_scheduled_hook( 'iwj_membership_expired_notice' );
        wp_schedule_event( current_time( 'timestamp' ), 'daily', 'iwj_membership_expired_notice');

	    wp_clear_scheduled_hook( 'iwj_job_expiry_notice' );
	    wp_schedule_event( current_time( 'timestamp' ), 'daily', 'iwj_job_expiry_notice');

        wp_clear_scheduled_hook( 'iwj_delete_draft_job' );
        $held_duration = iwj_option( 'delete_draft_job_hours', '24' );
        if ( $held_duration >= 1 ) {
            wp_schedule_single_event( time() + ( absint( $held_duration ) * 60 * 60), 'iwj_delete_draft_job' );
        }

        wp_clear_scheduled_hook( 'iwj_delete_pending_order' );
        $held_duration = iwj_option( 'delete_pending_order_hours', '48' );
        if ( $held_duration >= 1 ) {
            wp_schedule_single_event( time() + ( absint( $held_duration ) * 60 * 60), 'iwj_delete_pending_order' );
        }
    }

    static function deactive($networkwide){
        global $wpdb;

        if (is_multisite() && $networkwide) {
            $old_blog =  $wpdb->blogid;
            //Get all blog ids
            $blogids =  $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
            foreach ( $blogids as $blog_id ) {
                switch_to_blog($blog_id);
                self::clear_scheduled_hook();
            }
            switch_to_blog( $old_blog );
        }else{
            self::clear_scheduled_hook();
        }
    }

    static function delete_blog($blog_id, $drop){
        self::delete_tables();
    }

    static function uninstall(){
        self::remove_role();
        self::clear_scheduled_hook();
        self::delete_tables();

        include_once( 'includes/helper.function.php' );
        $login_page_id = iwj_get_page_id('login');
        if($login_page_id){
            wp_trash_post($login_page_id);
        }

        $register_page_id = iwj_get_page_id('register');
        if($register_page_id){
            wp_trash_post($register_page_id);
        }

        $lostpass_page_id = iwj_get_page_id('lostpass');
        if($lostpass_page_id){
            wp_trash_post($lostpass_page_id);
        }

        $dashboard_page_id = iwj_get_page_id('dashboard');
        if($dashboard_page_id){
            wp_trash_post($dashboard_page_id);
        }

        $jobs_page_id = iwj_get_page_id('jobs');
        if($jobs_page_id){
            wp_trash_post($jobs_page_id);
        }

        $employers_page_id = iwj_get_page_id('employers');
        if($employers_page_id){
            wp_trash_post($employers_page_id);
        }

        $candidates_page_id = iwj_get_page_id('candidates');
        if($candidates_page_id){
            wp_trash_post($candidates_page_id);
        }

        global $wpdb;

        // Delete options.
        $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_iwj\_%';");

        // Delete posts + data.
        $wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'iwj_job', 'iwj_package', 'iwj_ressum_package', 'iwj_u_package' , 'iwj_order', 'iwj_application', 'iwj_employer', 'iwj_candidate' );" );
        $wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );

        // Delete taxonomy.
        $wpdb->query( "DELETE FROM {$wpdb->term_taxonomy} WHERE taxonomy IN ( 'iwj_cat', 'iwj_type', 'iwj_salary', 'iwj_skill', 'iwj_level', 'iwj_keyword' );" );
        $wpdb->query( "DELETE terms FROM {$wpdb->terms} terms LEFT JOIN {$wpdb->term_taxonomy} term_taxonomy ON terms.term_id = term_taxonomy.term_id WHERE term_taxonomy.ID IS NULL;" );
        $wpdb->query( "DELETE meta FROM {$wpdb->termmeta} meta LEFT JOIN {$wpdb->term_taxonomy} term_taxonomy ON terms.term_id = meta.term_id WHERE term_taxonomy.term_id IS NULL;" );

        //Delete user meta
        $wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key LIKE '_iwj\_%';");

    }

    static function remove_role(){
        remove_role( 'iwj_employer');
        remove_role( 'iwj_candidate');
    }

    static function clear_scheduled_hook(){
        wp_clear_scheduled_hook('iwj_alert_job_daily');
        wp_clear_scheduled_hook('iwj_alert_job_weekly');
	    wp_clear_scheduled_hook( 'iwj_job_expiry_notice' );
	    wp_clear_scheduled_hook( 'iwj_membership_expiry_notice' );
	    wp_clear_scheduled_hook( 'iwj_membership_expired_notice' );
        wp_clear_scheduled_hook('iwj_check_featured_job');
        wp_clear_scheduled_hook('iwj_delete_draft_job');
    }

    static function delete_tables(){
        global $wpdb;

        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}iwj_view_resums" );
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}iwj_save_resums" );
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}iwj_save_jobs" );
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}iwj_follows" );
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}iwj_email_queue" );
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}iwj_alerts" );
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}iwj_alert_relationships" );
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}iwj_reviews" );
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}iwj_reviews_reply" );
    }

    static function update(){
        $current_version = get_option('iwj_version');
        if($current_version && version_compare($current_version, '1.1', '<')){
            //alter iwj_alerts table
            global $wpdb;
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $sql = "ALTER table {$wpdb->prefix}iwj_alerts  
                        ADD `name` varchar(255) NOT NULL AFTER `user_id`,
                        ADD `email` varchar(255) NOT NULL AFTER `user_id`,
                        ADD `status` tinyint NOT NULL AFTER `frequency`;";
            $wpdb->query($sql);

            //update status = 1
            $update_alerts = "UPDATE {$wpdb->prefix}iwj_alerts SET status = 1";
            $wpdb->query($update_alerts);

            //update settings
            $options = iwj_option();
            if(is_string($options)){
                $options = unserialize($options);
            }

            $options['show_company_job'] = 1;
            $options['show_salary_job'] = 1;
            $options['show_location_job'] = 1;
            $options['email_confirm_alert_job_enable'] = 1;
            $options['email_confirm_alert_job_subject'] = 'Confirm registration for new Class Alert';
            $options['email_confirm_alert_job_heading'] = 'Confirm registration for new Class Alert';
            $options['email_confirm_alert_job_content'] = 'Dear Admin, We will send the work to you according to the following criteria:

{if !empty($categories)}- Categorie(s): {$categories}{/if}

{if !empty($types)}– Type(s): {$types}{/if}

{if !empty($types)}– Level(s): {$levels}{/if}

{if !empty($locations)}– Location(s): {$loctions}{/if}

{if !empty($salary_from)}– Salary from(s): {$salary_from}{/if}
– Frequency(s): {$frequency}

Please confirm by clicking on the link: <a href="{$confirm_link}" target="_blank">confirm link</a>';

            $options['email_new_application_content'] = 'Dear {$candidate_name},
Your application has been sent to {$employer_name}. The employer will consider and contact to you as soon as possible if your abilities match their requirement.

Your detailed application:

=================================================
Fullname :  {$application->get_full_name()}
Email : {$application->get_email()}
{assign var="cv" value=$application->get_cv()}
Teacher CV :  {if isset($cv)}<a href="{$cv[\'url\']}">Download full CV</a>{/if}
Teacher Message : 
{$application->get_message()}
=================================================

Thank you!
InJob team';
           $options['email_new_application_employer_content'] = 'Dear {$employer_name},
{$candidate_name} applied for your job <a href="{$job->permalink()}">{$job->get_title()}</a> please see following details:

=================================================
Fullname :  {$application->get_full_name()}
Email : {$application->get_email()}
{assign var="cv" value=$application->get_cv()}
Teacher CV :  {if isset($cv)}<a href="{$cv[\'url\']}">Download full CV</a>{/if}
Teacher Message : 
{$application->get_message()}
=================================================

Thank you!
InJob team';

           $options['apply_form_enable'] = 1;

            update_option('iwj_settings', $options);

            update_option('iwj_version', '1.1');
        }

        if($current_version && version_compare($current_version, '1.1.1', '<')){
            $roles = array( 'iwj_employer', 'iwj_candidate');
            foreach ( $roles as $roleName ) {
                // Get role
                $role = get_role( $roleName );

                // Check role exists
                if ( is_null( $role) ) {
                    continue;
                }

                $customCaps = array('edit_attachments', 'delete_attachments');
                foreach ( $customCaps as $capability ) {
                    // Add capability
                    $role->add_cap( $capability );
                }
            }

            $options = iwj_option();
            $options['submit_job_mode'] = 1;
            update_option('iwj_settings', $options);
            update_option('iwj_version', '1.1.1');
        }

        if($current_version && version_compare($current_version, '2.0.0', '<')){

            global $wpdb;
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            //from 2.0.0
            $collate = '';

            if ( $wpdb->has_cap( 'collation' ) ) {
                $collate = $wpdb->get_charset_collate();
            }
            $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}iwj_reviews (
                    `ID`  int(11) NOT NULL AUTO_INCREMENT ,
                    `user_id`  int(11) NOT NULL ,
                    `rating`  varchar(5) NOT NULL ,
                    `item_id`  int(11) NOT NULL ,
                    `title`  varchar(255) NOT NULL ,
                    `content`  text NOT NULL ,
                    `time`  int(11) NOT NULL ,
                    `status`  varchar(20) NOT NULL ,
                    `criterias`  text NOT NULL ,
                    `read`  tinyint(3) NOT NULL ,
                    PRIMARY KEY (`ID`),
                    KEY `User` (`user_id`),
                    KEY `Item Id` (`item_id`) 
                ) $collate;";
            $wpdb->query($sql);

            $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}iwj_reviews_reply(
                    `ID` int(11) NOT NULL AUTO_INCREMENT,
                      `review_id` int(11) NOT NULL,
                      `user_id` int(11) NOT NULL,
                      `reply_content` text NOT NULL,
                      `time` int(11) NOT NULL,
                      PRIMARY KEY (`ID`),
                      KEY `Reviews` (`review_id`)
                ) $collate;";
            $wpdb->query($sql);

            $options = iwj_option();
            $options['email_admin_register_subject'] = 'New Registration';
            $options['email_admin_register_heading'] = 'New Registration';
            $options['email_admin_register_content'] = 'Hi Admin,
A new user has registered account on {$site_title}

following account infomation:
- Display Name: {$display_name}
- Email: {$email}
- Role: {$role}

You can manager <a href="{$edit_url}" target="_blank">here</a>';

            $options['email_verify_account_enable'] = '1';
            $options['email_verify_account_subject'] = 'Verify account';
            $options['email_verify_account_heading'] = 'Verify account';
            $options['email_verify_account_content'] = 'Dear {$display_name},
You registed account on {$site_title}, Please click <a href="{$activation_url}" target="_blank">here</a> to verify your account';

            $options['email_delete_account_enable'] = '1';
            $options['email_delete_account_subject'] = 'Account successfully deleted';
            $options['email_delete_account_heading'] = 'Account successfully deleted';
            $options['email_delete_account_content'] = 'Dear {$display_name},
We are so sorry about your cancellation and we want to say thank you for your time with us those days over. We wish you have a good time ahead!

Thank you and Best Regards,';

            $options['email_confirm_alert_job_enable'] = '1';
            $options['email_confirm_alert_job_subject'] = 'Confirm registration for new Class Alert';
            $options['email_confirm_alert_job_heading'] = 'Confirm registration for new Class Alert';
            $options['email_confirm_alert_job_content'] = 'Dear Admin, We will send the work to you according to the following criteria:

{if !empty($categories)}- Categorie(s): {$categories}{/if}

{if !empty($types)}– Type(s): {$types}{/if}

{if !empty($types)}– Level(s): {$levels}{/if}

{if !empty($locations)}– Location(s): {$loctions}{/if}

{if !empty($salary_from)}– Salary from(s): {$salary_from}{/if}
– Frequency(s): {$frequency}

Please confirm by clicking on the link: <a href="{$confirm_link}" target="_blank">confirm link</a>';


            $options['email_new_review_enable'] = '1';
            $options['email_new_review_subject'] = 'New Review';
            $options['email_new_review_heading'] = 'New Review';
            $options['email_new_review_content'] = 'Hi Admin,
User {$candidate_name} has wirtten a revriew for {$employer_name}

Following details:
Review Title: {$review_title}
Total Rating: {$rating}
Review Content: {$review_content}

You can review it <a href="{$admin_review_url}" target="_blank">here</a>';

            $options['email_approved_review_enable'] = '1';
            $options['email_approved_review_subject'] = 'New Review';
            $options['email_approved_review_heading'] = 'New Review';
            $options['email_approved_review_content'] = 'Dear {$employer_name},
{$candidate_name} has written a review for your company

Following details:
Review Title: {$review_title}
Total Rating: {$rating}
Review Content: {$review_content}

You can view it <a href="{$review_url}" target="_blank">here</a>';

            $options['email_candidate_approved_review_enable'] = '1';
            $options['email_candidate_approved_review_subject'] = 'Approved Review';
            $options['email_candidate_approved_review_heading'] = 'Approved Review';
            $options['email_candidate_approved_review_content'] = 'Dear {$candidate_name},
Your review for {$employer_name} has been approved

You can view it <a href="{$review_url}" target="_blank">here</a>';

            $options['email_rejected_review_enable'] = '1';
            $options['email_rejected_review_subject'] = 'Rejected Review';
            $options['email_rejected_review_heading'] = 'Rejected Review';
            $options['email_rejected_review_content'] = 'Dear {$candidate_name},

Your Review for {$employer_name} has been rejected.
Following reason:
{$reason}

Thank you!';

            update_option('iwj_settings', $options);
            update_option('iwj_version', '2.0.0');
        }

        if($current_version && version_compare($current_version, '2.0.2', '<')) {
            global $wpdb;
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            $sql = "SHOW COLUMNS FROM {$wpdb->prefix}iwj_reviews LIKE 'read';";
            if(!$wpdb->get_results($sql)){
                $sql = "ALTER table {$wpdb->prefix}iwj_reviews  
                        ADD `read`  tinyint(3) NOT NULL;";
                $wpdb->query($sql);
            }

            update_option('iwj_version', '2.0.2');
        }

        if($current_version && version_compare($current_version, '3.2.0', '<')) {
            if ( !wp_next_scheduled( 'iwj_membership_expiry_notice' ) ) {
                wp_schedule_event(current_time('timestamp'), 'daily', 'iwj_membership_expiry_notice');
            }
            if ( !wp_next_scheduled( 'iwj_membership_expired_notice' ) ) {
                wp_schedule_event(current_time('timestamp'), 'daily', 'iwj_membership_expired_notice');
            }

            $options = iwj_option();
            $options = self::add_membership_options($options);
            update_option('iwj_settings', $options);
            update_option('iwj_version', '3.2.0');
        }
    }

    static function update2(){
        $current_version = get_option('iwj_version');
        if($current_version && version_compare($current_version, '2.5.0', '<')){
            if(!function_exists('get_current_screen')){
                require_once(ABSPATH . 'wp-admin/includes/screen.php');
            }

            $options = iwj_option();

            $post_args = array(
                'post_title' => 'Job suggestion',
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_content' => '[vc_row][vc_column][iwj_jobs_suggestion filter="recommend"][iwj_recommend_adv title="Update your profile like skill, category, etc…to recieve job recommendation."][/vc_column][/vc_row]',
            );

            $post_id = wp_insert_post($post_args);
            $options['suggest_job_page_id'] = $post_id;

            $post_args = array(
                'post_title' => 'Teacher Suggestion',
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_content' => '[vc_row][vc_column][iwj_candidates_suggestion filter="recommend" candidate_on_row="3"][/vc_column][/vc_row]',
            );

            $post_id = wp_insert_post($post_args);
            $options['candidate_suggestion_page_id'] = $post_id;

            $options['woocommerce_checkout'] = 0;
            $options['include_my_account_woocommerce'] = 0;
            $options['job_suggestion_conditions'] = array(
                'category', 'type', 'level', 'skill', 'location'
            );
            $options['candidate_suggestion_conditions'] = array(
                'profile_category'
            );

            $options['disable_review'] = 0;
            $options['edit_review_auto_approved'] = 0;

            update_option('iwj_settings', $options);
            update_option('iwj_version', '2.5.0');
        }

        if($current_version && version_compare($current_version, '2.6.0', '<')){
            $args = array(
                'post_type' => 'iwj_candidate',
                'post_status' => array('publish','pending', 'iwj-incomplete'),
                'posts_per_page' => -1,
                'fields'        => 'ids'
            );
            $candidates = get_posts($args);
            if($candidates){
                foreach ( $candidates as $candidate ) {
                    update_post_meta($candidate,IWJ_PREFIX.'public_account',1);
                }
            }

            update_option('iwj_version', '2.6.0');
        }

        if($current_version && version_compare($current_version, '2.7.0', '<')) {
            global $wpdb;
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $collate = '';
            if ( $wpdb->has_cap( 'collation' ) ) {
                $collate = $wpdb->get_charset_collate();
            }

            $sql = "CREATE TABLE {$wpdb->prefix}iwj_term_translates (
                  `ID` int(11) NOT NULL AUTO_INCREMENT,
                  `term_id` int(11) NOT NULL,
                  `lang_code` varchar(3) NOT NULL,
                  `translate_key` varchar(255) NOT NULL,
                  `translate_string` text NOT NULL,
                  PRIMARY KEY (`ID`),
                  KEY `term_id` (`term_id`)
               ) $collate;";
            $wpdb->query($sql);

            $sql = "CREATE TABLE {$wpdb->prefix}iwj_post_translates (
                  `ID` int(11) NOT NULL AUTO_INCREMENT,
                  `post_id` int(11) NOT NULL,
                  `lang_code` varchar(3) NOT NULL,
                  `translate_key` varchar(255) NOT NULL,
                  `translate_string` text NOT NULL,
                  PRIMARY KEY (`ID`),
                  KEY `post_id` (`post_id`)
                ) $collate;";

            $wpdb->query($sql);

            $options = iwj_option();
            $options['default_job_content'] = iwj_get_desc_job();

            update_option('iwj_settings', $options);

            $sql = "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s";
            $jobs = $wpdb->get_results($wpdb->prepare($sql, 'iwj_job'));
            if($jobs){
                foreach ($jobs as $job){
                    $salary_from = get_post_meta($job->ID, IWJ_PREFIX.'salary_from', true);
                    $salary_to = get_post_meta($job->ID, IWJ_PREFIX.'salary_to', true);
                    if(!$salary_from){
                        update_post_meta($job->ID, IWJ_PREFIX.'salary_from', '');
                    }
                    if(!$salary_to){
                        update_post_meta($job->ID, IWJ_PREFIX.'salary_to', '');
                    }

                    $featured = get_post_meta($job->ID, IWJ_PREFIX.'featured', true);
                    if(!$featured){
                        update_post_meta($job->ID, IWJ_PREFIX.'featured_date', '');
                    }else{
                        $featured_date = get_post_meta($job->ID, IWJ_PREFIX.'featured_date', true);
                        if(!$featured_date){
                            update_post_meta($job->ID, IWJ_PREFIX.'featured_date', current_time('timestamp'));
                        }
                    }
                }
            }

            update_option('iwj_version', '2.7.0');
        }

        if($current_version && version_compare($current_version, '2.8.0', '<')) {
            global $wpdb;
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $options = iwj_option();
            $options['search_form_jobs_style'] = '';
            $options['show_print_job'] = '1';
            $options['show_rss_feed_job'] = '1';
            $options['show_rss_feed_candidate'] = '1';
            $options['show_rss_feed_employer'] = '1';
            update_option('iwj_settings', $options);

            $sql = "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s";
            $jobs = $wpdb->get_results($wpdb->prepare($sql, 'iwj_job'));
            if($jobs){
                foreach ($jobs as $job){
                    $featured_date = get_post_meta($job->ID, IWJ_PREFIX.'featured_date', true);
                    if(!$featured_date){
                        update_post_meta($job->ID, IWJ_PREFIX.'featured_date', '');
                    }
                }
            }

            update_option('iwj_version', '2.8.0');
        }

        if($current_version && version_compare($current_version, '2.9.0', '<')) {
            global $wpdb;
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $options = iwj_option();
            $options['maximum_file_size_cv'] = '5';
            $options['apply_job_mode'] = '1';

            update_option('iwj_version', '2.9.0');
        }

        if($current_version && version_compare($current_version, '3.1.0', '<')){
            Inwave_Customizer::store_customize_file();
            update_option('iwj_version', '3.1.0');
        }
    }

    static function add_membership_options($options){
        $options['free_plan_times'] = 1;
        $options['email_membership_notice_enable'] = 1;
        $options['email_membership_notice_subject'] = '[{$site_title}] Membership plan expiry notice';
        $options['email_membership_notice_heading'] = '[{$site_title}] Membership plan expiry notice';
        $options['email_membership_notice_content'] = 'Hi {$user_name},
We would like to inform you that your plan {$plan_title} is about to expire on {$expiry_date}.
{if $can_renew eq true}
Please <a href="{$renew_plan_url}" target="_blank">click here</a> to renew your plan now.
{else}
Please <a href="{$select_plan_url}" target="_blank">click here</a> to select another plan.
{/if}
Regards,
Reality Team.';

        $options['send_membership_notice_before'] = 5;
        $options['send_membership_notice_days'] = 2;

        $options['email_membership_expired_enable'] = 1;
        $options['email_membership_expired_subject'] = '[{$site_title}] Your membership plan expired';
        $options['email_membership_expired_heading'] = 'Membership plan expired';
        $options['email_membership_expired_content'] = 'Hi {$user_name},
Your plan {$plan_title} expired on {$expiry_date}.
All of your jobs will not be lost, it will move to an expired state
{if $can_renew eq true}
<a href="{$renew_plan_url}" target="_blank">click here</a> if you want to renew your plan.
{else}
<a href="{$select_plan_url}" target="_blank">click here</a> if you want to select another plan.
{/if}

Regards,
Reality Team.';

        return $options;
    }
}