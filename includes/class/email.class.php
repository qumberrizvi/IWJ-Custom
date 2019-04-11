<?php

if (!defined('ABSPATH'))
    exit;

class IWJ_Email {
    public static function get_blogname() {
        return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
    }

    static public function send_email($type, $args, $method = '') {

        $emails = self::get_emails($type, $args);
        if($emails){
            $email_queue_types = (array)apply_filters('iwj_email_queue_types', array('new_job', 'alert_job', 'membership_notice', 'membership_expired'));
            foreach ($emails as $email){
                if ($email['content'] && $email['recipients'] && $email['subject']) {
                    //add mail queue
                    $from_name = iwj_option('email_from_name', self::get_blogname());
                    $from_address = iwj_option('email_from_address', get_option('admin_email',''));
                    //$mail_content = apply_filters('the_content', $email['content']);

                    $mailqueue =  ($method && $method == 'mailqueue') ? true : ($method == 'direct' ? false : in_array($type, $email_queue_types));
                    do_action('iwj_before_send_email', $email, $mailqueue, $type, $args);
                    if($mailqueue){
                        IWJ_Email_Queue::add($from_name, $from_address, $email['recipients'], $email['subject'], $email['content']);
                    }else{
                        //send directly
                        $headers = array();
                        $headers[] = 'Content-Type: text/html; charset=UTF-8';

                        $headers[] = 'From: ' . $from_name . ' <' . $from_address . '>';
                        if(isset($email['reply_name']) && isset($email['reply_to'])){
                            $headers[] = 'Reply-To: ' . $email['reply_name'] . ' <' . $email['reply_to'] . '>';
                        }elseif(isset($email['reply_to'])){
                            $headers[] = 'Reply-To: <' . $email['reply_to'] . '>';
                        }

                        wp_mail($email['recipients'], $email['subject'], $email['content'], $headers);
                    }
                    do_action('iwj_after_send_email', $email, $mailqueue, $type, $args);
                }
            }
        }
    }

    static public function get_emails($type, $args) {
        $emails = array();
        if ($type) {
            $function = 'get_email_'.$type;
            if(method_exists('IWJ_Email',$function)){
                $emails = self::$function($args);
                if($emails){
                    foreach ($emails as $key=>$email){
                        self::email_content($email, $type);
                        $emails[$key] = $email;
                    }
                }
            }else{
                $emails = (array)apply_filters('iwj_get_emails_'.$type, $emails, $args);
            }
        }

        apply_filters('iwj_get_emails', $emails, $type, $args);

        return $emails;
    }

    static public function email_content(&$email, $type) {
        if(!class_exists('Smarty')){
            require_once IWJ_PLUGIN_DIR.'/includes/libs/smarty/Smarty.class.php';
        }

        ob_start();
        iwj_get_template_part( 'email-styles');
        $css = apply_filters( 'iwj_email_styles', ob_get_clean() );
        // apply CSS styles inline for picky email clients

        $email_template = iwj_option('email_template');
        $smarty = new Smarty();
        $upload = wp_upload_dir();
        $smarty->setCompileDir($upload['basedir'].'/templates_c');
        $smarty->assign('language_attributes', get_language_attributes());
        $smarty->assign('charset', get_bloginfo( 'charset' ));
        $smarty->assign('site_title', self::get_blogname());
        $smarty->assign('is_rtl', is_rtl());
        $smarty->assign('email_type', $type);
        if(isset($email['args']) && $email['args']){
            foreach ($email['args'] as $param => $value){
                $smarty->assign($param, $value);
            }
        }
         
        $email_template = str_replace('#email_heading#', $email['heading'], $email_template);
        $email_template = str_replace('#email_body_content#', $email['content'], $email_template);

        try {
            $email_template = $smarty->fetch('string:'.$email_template);
            $email_subject_fetch = $smarty->fetch('string:'.$email['subject']);
//            $email_template = wpautop( $email_template, true );
            $email_template = preg_replace('~^.*?(?=\<\!DOCTYPE)~i', '', $email_template);
            $emogrifier = new Emogrifier( $email_template, $css );
            $email_content_fetch = $emogrifier->emogrify();
            $email['subject'] = $email_subject_fetch;
            $email['content'] = wpautop( $email_content_fetch ,false);
        } catch ( Exception $e ) {
            echo $e->getMessage();
        }
    }

    static public function get_email_resetpass($args) {

        $type = 'resetpass';

        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $user = $args;
            $recipient = $user->get_email();
            $heading = iwj_option('email_'.$type.'_heading');
            $subject = iwj_option('email_'.$type.'_subject');
            $content = iwj_option('email_'.$type.'_content');

            $args = array(
                'user' => $user,
                'user_login' => $user->get_login(),
                'resetpass_url' => $user->get_resetpass_url(),
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }

        return array();
    }

    static public function get_email_register($args) {
        $type = 'register';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $user = IWJ_User::get_user($args['user_id']);
            $password = isset($args['password']) ? $args['password'] : '';
            $auto_generate_password = isset($args['auto_generate_password']) ? $args['auto_generate_password'] : false;

            $recipient = $user->get_email();
            $heading = iwj_option('email_' . $type . '_heading');
            $subject = iwj_option('email_' . $type . '_subject');
            $content = iwj_option('email_' . $type . '_content');

            $verify_account = iwj_option('verify_account') ? true : false;
            if($verify_account){
                $verify_account = $user->is_verified() ? false : true;
            }
            $args = array(
                'user' => $user,
                'user_id' => $args['user_id'],
                'auto_generate_password' => $auto_generate_password,
                'user_password' => $password,
                'user_login' => $user->get_login(),
                'dashboard_url' => iwj_get_page_permalink('dashboard'),
                'verify_account' => $verify_account,
                'activation_url' => $verify_account ? $user->get_activation_url(false) : '',
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }

        return array();
    }

    static public function get_email_admin_register($args) {
        $type = 'admin_register';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $user = IWJ_User::get_user($args['user_id'], true);
            $recipient = get_bloginfo('admin_email');
            $heading = iwj_option('email_' . $type . '_heading');
            $subject = iwj_option('email_' . $type . '_subject');
            $content = iwj_option('email_' . $type . '_content');

            $args = array(
                'user' => $user,
                'user_id' => $args['user_id'],
                'user_login' => $user->get_login(),
                'display_name' => $user->get_display_name(),
                'email' => $user->get_email(),
                'role' => in_array('iwj_employer', (array)$user->user->roles) ? _x('Student', 'Role for email', 'iwjob') :  (in_array('iwj_candidate', (array)$user->user->roles) ? _x('Teacher', 'Role for email', 'iwjob') : implode(', ', $user->user->roles)),
                'edit_url' => admin_url('user-edit.php?user_id='.$args['user_id']),
            );
            //get_edit_user_link()
            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }

        return array();
    }

    static public function get_email_review_profile($args) {
        $type = 'review_profile';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $profile = $args;
            $recipient = get_bloginfo('admin_email');
            $heading = iwj_option('email_' . $type . '_heading');
            $subject = iwj_option('email_' . $type . '_subject');
            $content = iwj_option('email_' . $type . '_content');

            $args = array(
                'profile' => $profile,
                'profile_author' => $profile->get_author(),
                'profile_admin_url' => $profile->admin_link(),
                'author_name' => $profile->get_title(),
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }

        return array();
    }

    static public function get_email_approved_profile($args) {
        $type = 'approved_profile';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $profile = $args;
            $author = $profile->get_author();
            $recipient = $author->get_email();
            $heading = iwj_option('email_' . $type . '_heading');
            $subject = iwj_option('email_' . $type . '_subject');
            $content = iwj_option('email_' . $type . '_content');

            $args = array(
                'profile' => $profile,
                'author' => $profile->get_author(),
                'author_name' => $profile->get_title(),
                'dashboard_url' => iwj_get_page_permalink('dashboard'),
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }

        return array();
    }

    static public function get_email_rejected_profile($args) {
        $type = 'rejected_profile';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $profile = $args;
            $recipient = $profile->get_email();
            $heading = iwj_option('email_' . $type . '_heading');
            $subject = iwj_option('email_' . $type . '_subject');
            $content = iwj_option('email_' . $type . '_content');

            $args = array(
                'profile' => $profile,
                'profile_edit_url' => $profile->get_edit_url(),
                'author' => $profile->get_author(),
                'author_name' => $profile->get_title(),
                'reason' => $profile->get_reason(),
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }

        return array();
    }

    static public function get_email_review_job($args) {
        $type = 'review_job';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $job = $args;
            $author = $job->get_author();
            $recipient = get_bloginfo('admin_email');
            $heading = iwj_option('email_' . $type . '_heading');
            $subject = iwj_option('email_' . $type . '_subject');
            $content = iwj_option('email_' . $type . '_content');

            $args = array(
                'job' => $job,
                'job_title' => $job->get_title(),
                'is_child_job' => $job->get_parent_id() > 0 ? true : false,
                'parent_job' => $job->get_parent(),
                'author' => $author,
                'author_name' => $author->get_display_name(),
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }

        return array();
    }

    static public function get_email_new_job($args) {
        $type = 'new_job';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $job = $args;
            $author = $job->get_author();
            $emails = array();
            if ($author && $follower_ids = $author->get_follower_ids()) {
                $heading = iwj_option('email_' . $type . '_heading');
                $subject = iwj_option('email_' . $type . '_subject');
                $content = iwj_option('email_' . $type . '_content');

                foreach ($follower_ids as $follower_id) {
                    if ($follower = IWJ_User::get_user($follower_id)) {
                        $recipient = $follower->get_email();
                        $args = array(
                            'job' => $job,
                            'author' => $author,
                            'author_name' => $author->get_display_name(),
                            'follower' => $follower,
                            'follower_name' => $follower->get_display_name(),
                        );

                        $email = array(
                            'subject' => $subject,
                            'heading' => $heading,
                            'content' => $content,
                            'recipients' => $recipient,
                            'args' => $args,
                        );

                        $emails[] = $email;
                    }
                }
            }

            return $emails;
        }

        return array();
    }

    static public function get_email_approved_job($args) {
        $type = 'approved_job';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $job = $args;
            $author = $job->get_author();
            $recipient = $author->get_email();
            $heading = iwj_option('email_' . $type . '_heading');
            $subject = iwj_option('email_' . $type . '_subject');
            $content = iwj_option('email_' . $type . '_content');

            $args = array(
                'job' => $job,
                'job_title' => $job->get_title(),
                'is_child_job' => $job->get_parent_id() > 0 ? true : false,
                'parent_job' => $job->get_parent(),
                'author' => $author,
                'author_name' => $author->get_display_name(),
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }

        return array();
    }

    static public function get_email_alert_job($args) {
        $type = 'alert_job';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $jobs = $args['jobs'];
            $user = $args['user'];
            $alert = $args['alert'];
            $emails = array();
            if ($jobs) {
                $heading = iwj_option('email_' . $type . '_heading');
                $subject = iwj_option('email_' . $type . '_subject');
                $content = iwj_option('email_' . $type . '_content');
                $recipient = $alert->get_email();

                $args = array(
                    'jobs' => $jobs,
                    'total_jobs' => count($jobs),
                    'user' => $user,
                    'display_name' => $alert->get_name(),
                    'email' => $alert->get_email(),
                    'alert' => $alert,
                    'all_criterials' => $alert->get_relationship_titles(),
                    'unsubscribe_link' => $alert->get_unsubscribe_link(),
                    'position' => $alert->position,
                    'title' => $alert->position,
                );

                $email = array(
                    'subject' => $subject,
                    'heading' => $heading,
                    'content' => $content,
                    'recipients' => $recipient,
                    'args' => $args,
                );

                $emails[] = $email;

            }

            return $emails;
        }

        return array();
    }

    static public function get_email_confirm_alert_job($args) {
        $type = 'confirm_alert_job';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $alert_id = $args['alert_id'];
            $alert = IWJ_Alert::get_alert($alert_id);
            $emails = array();

            if ($alert) {
                $args = array(
                    'alert' => $alert,
                    'alert_id' => $alert_id,
                    'name' => $args['name'],
                    'email' => $args['email'],
                    'position' => $alert->get_position(),
                    'salary_from' => $alert->get_salary_from(),
                    'frequency' => $alert->get_frequency(),
                    'categories' => $alert->get_relationship_titles('cat', ','),
                    'types' => $alert->get_relationship_titles('type', ','),
                    'locations' => $alert->get_relationship_titles('location', ','),
                    'skills' => $alert->get_relationship_titles('skill', ','),
                    'levels' => $alert->get_relationship_titles('level', ','),
                    'confirm_link' => $alert->get_confirm_link(),
                    'all_criterials' => $alert->get_relationship_titles(),
                );

                $heading = iwj_option('email_' . $type . '_heading');
                $subject = iwj_option('email_' . $type . '_subject');
                $content = iwj_option('email_' . $type . '_content');
                $recipient = $args['email'];

                $email = array(
                    'subject' => $subject,
                    'heading' => $heading,
                    'content' => $content,
                    'recipients' => $recipient,
                    'args' => $args,
                );

                $emails[] = $email;
            }

            return $emails;
        }

        return array();
    }

    static public function get_email_rejected_job($args) {
        $type = 'rejected_job';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $job = $args;
            $author = $job->get_author();
            $recipient = $author->get_email();
            $heading = iwj_option('email_' . $type . '_heading');
            $subject = iwj_option('email_' . $type . '_subject');
            $content = iwj_option('email_' . $type . '_content');

            $args = array(
                'job' => $job,
                'job_title' => $job->get_title(),
                'reason' => $job->get_reason(),
                'is_child_job' => $job->get_parent_id() > 0 ? true : false,
                'parent_job' => $job->get_parent(),
                'author' => $author,
                'author_name' => $author->get_display_name(),
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }

        return array();
    }

    static public function get_email_job_expiry_notice($args) {
        $type = 'job_expiry_notice';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $job_id = $args['job_id'];
            $job    = IWJ_Job::get_job( $job_id );
            if ( $job ) {
                $author    = $job->get_author();
                $recipient = $author->get_email();
                $subject   = iwj_option( 'email_' . $type . '_subject' );
                $content   = iwj_option( 'email_' . $type . '_content' );

                $renew_job_url = $job->renew_link();
                $edit_job_url  = $job->edit_link();
                $expiry_date   = date_i18n( get_option( 'date_format' ), $job->get_expiry() );
                $args          = array(
                    'job'           => $job,
                    'user'          => $author,
                    'user_name'     => $author->get_display_name(),
                    'job_title'     => $job->get_title(),
                    'expiry_date'   => $expiry_date,
                    'can_renew'     => $job->can_renew() ? true : false,
                    'renew_job_url' => $renew_job_url,
                    'edit_job_url'  => $edit_job_url,
                );

                $email = array(
                    'subject'    => $subject,
                    'content'    => $content,
                    'recipients' => $recipient,
                    'args'       => $args,
                );

                return array( $email );
            }
        }

        return array();
    }

    static public function get_email_new_order($args) {
        $type = 'new_order';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $order = $args;
            $author = $order->get_author();
            $recipient = $author->get_email();
            $heading = iwj_option('email_' . $type . '_heading');
            $subject = iwj_option('email_' . $type . '_subject');
            $content = iwj_option('email_' . $type . '_content');

            $args = array(
                'order_number' => $order->get_id(),
                'order_date' => $order->get_created(get_option('date_format')),
                'order' => $order,
                'order_status' => $order->get_status(),
                'order_pay_url' => $order->get_pay_url(),
                'order_admin_url' => $order->get_admin_url(),
                'order_type' => $order->get_type(),
                'order_type_title' => $order->get_type_title($order->get_type()),
                'order_description' => $order->get_payment_description(),
                'author' => $author,
                'author_name' => $author->get_display_name(),
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }

        return array();
    }

    static public function get_email_new_order_admin($args) {
        $type = 'new_order_admin';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $order = $args;
            $author = $order->get_author();
            $recipient = get_bloginfo('admin_email');
            $heading = iwj_option('email_' . $type . '_heading');
            $subject = iwj_option('email_' . $type . '_subject');
            $content = iwj_option('email_' . $type . '_content');

            $args = array(
                'order_number' => $order->get_id(),
                'order_date' => $order->get_created(get_option('date_format')),
                'order' => $order,
                'order_status' => $order->get_status(),
                'order_pay_url' => $order->get_pay_url(),
                'order_admin_url' => $order->get_admin_url(),
                'order_type' => $order->get_type(),
                'order_type_title' => $order->get_type_title($order->get_type()),
                'order_description' => $order->get_payment_description(),
                'author' => $author,
                'author_name' => $author->get_display_name(),
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }

        return array();
    }

    static public function get_email_hold_order($args) {
        $type = 'hold_order';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $order = $args;
            $author = $order->get_author();
            $recipient = $author->get_email();
            $heading = iwj_option('email_' . $type . '_heading');
            $subject = iwj_option('email_' . $type . '_subject');
            $content = iwj_option('email_' . $type . '_content');

            $args = array(
                'order_number' => $order->get_id(),
                'order_date' => $order->get_created(get_option('date_format')),
                'order' => $order,
                'order_status' => $order->get_status(),
                'order_type' => $order->get_type(),
                'order_type_title' => $order->get_type_title($order->get_type()),
                'order_description' => $order->get_payment_description(),
                'author' => $author,
                'author_name' => $author->get_display_name(),
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }

        return array();
    }

    static public function get_email_completed_order($args) {
        $type = 'completed_order';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $order = $args;
            $author = $order->get_author();
            $recipient = $author->get_email();
            $heading = iwj_option('email_' . $type . '_heading');
            $subject = iwj_option('email_' . $type . '_subject');
            $content = iwj_option('email_' . $type . '_content');

            $args = array(
                'order_number' => $order->get_id(),
                'order_date' => $order->get_created(get_option('date_format')),
                'order' => $order,
                'order_status' => $order->get_status(),
                'order_type' => $order->get_type(),
                'order_type_title' => $order->get_type_title($order->get_type()),
                'order_description' => $order->get_payment_description(),
                'author' => $author,
                'author_name' => $author->get_display_name(),
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }

        return array();
    }

    static public function get_email_new_application($args) {
        $type = 'new_application';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $application = $args;
            $job = $application->get_job();
            $job_author = $job->get_author();
            $user = IWJ_User::get_user();
            $user_name = $application->get_full_name();
            $user_email = $application->get_email();

            $recipient = $user_email;
            $heading = iwj_option('email_' . $type . '_heading');
            $subject = iwj_option('email_' . $type . '_subject');
            $content = iwj_option('email_' . $type . '_content');

            $args = array(
                'job' => $job,
                'job_title' => $job->get_title(),
                'job_url' => $job->permalink(),
                'employer' => $job_author,
                'employer_name' => $job_author->get_display_name(),
                'employer_email' => $job_author->get_email(),
                'candidate' => $user,
                'candidate_name' => $user_name,
                'candidate_email' => $user_email,
                'application' => $application,
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }

        return array();
    }

    static public function get_email_new_application_employer($args) {
        $type = 'new_application_employer';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $application = $args;
            $job = $application->get_job();
            $job_author = $job->get_author();
            $user = IWJ_User::get_user();
            $user_name = $application->get_full_name();
            $user_email = $application->get_email();

            $recipient = $job->get_email_for_apply();
            $heading = iwj_option('email_' . $type . '_heading');
            $subject = iwj_option('email_' . $type . '_subject');
            $content = iwj_option('email_' . $type . '_content');

            $args = array(
                'job' => $job,
                'job_title' => $job->get_title(),
                'employer' => $job_author,
                'employer_name' => $job_author->get_display_name(),
                'employer_email' => $job_author->get_email(),
                'candidate' => $user,
                'candidate_name' => $user_name,
                'candidate_email' => $user_email,
                'application' => $application,
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }

        return array();
    }

    static public function get_email_customer_note($args) {
        $type = 'customer_note';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $order = $args['order'];
            $note = $args['note'];
            $author = $order->get_author();
            $recipient = $author->get_email();
            $heading = iwj_option('email_'.$type.'_heading');
            $subject = iwj_option('email_'.$type.'_subject');
            $content = iwj_option('email_'.$type.'_content');

            $args = array(
                'order_number' => $order->get_id(),
                'order_date' => $order->get_created(get_option('date_format')),
                'order' => $order,
                'order_status' => $order->get_status(),
                'order_pay_url' => $order->get_pay_url(),
                'order_type' => $order->get_type(),
                'order_type_title' => $order->get_type_title($order->get_type()),
                'author' => $author,
                'author_name' => $author->get_display_name(),
                'customer_note' => $note,
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }

        return array();
    }

    static public function get_email_customer_invoice($args) {

        $type = 'customer_invoice';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $order = $args;
            $author = $order->get_author();

            if($order->has_status('completed')){
                $heading = iwj_option('email_'.$type.'_paid_heading');
                $subject = iwj_option('email_'.$type.'_paid_subject');
            }else{
                $heading = iwj_option('email_'.$type.'_heading');
                $subject = iwj_option('email_'.$type.'_subject');
            }
            $content = iwj_option('email_'.$type.'_content');

            $recipient = $author->get_email();

            $args = array(
                'order_number' => $order->get_id(),
                'order_date' => $order->get_created(get_option('date_format')),
                'order' => $order,
                'order_status' => $order->get_status(),
                'order_pay_url' => $order->get_pay_url(),
                'order_type' => $order->get_type(),
                'order_type_title' => $order->get_type_title($order->get_type()),
                'author' => $author,
                'author_name' => $author->get_display_name(),
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }

        return array();
    }

    static public function get_email_application($args) {

        $type = 'application';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $application_id = $args['application_id'];
            $application = IWJ_Application::get_application($application_id);
            $job = $application->get_job();
            $job_author = $job->get_author();

            $heading = iwj_option('email_' . $type . '_heading');
            $subject = iwj_option('email_' . $type . '_subject');
            $content = iwj_option('email_' . $type . '_content');

            $author_name = $job_author->get_display_name();
            $author_email = $job_author->get_email();
            $candidate_name = $application->get_full_name();
            $candidate_email = $application->get_email();

            $_subject = str_replace(array('#employer_name#', '#employer_email#', '#candidate_name#', '#candidate_email#'), array($author_name, $author_email, $candidate_name, $candidate_email), $args['subject']);
            $_message = str_replace(array('#employer_name#', '#employer_email#', '#candidate_name#', '#candidate_email#'), array($author_name, $author_email, $candidate_name, $candidate_email), $args['message']);

            $recipient = $application->get_email();
            $reply_to = $job_author->get_email();
            $reply_name = $job_author->get_display_name();

            $args = array(
                'application' => $application,
                'job' => $job,
                'employer' => $job_author,
                'employer_name' => $author_name,
                'employer_email' => $author_email,
                'candidate' => $application->get_author(),
                'candidate_name' => $candidate_name,
                'candidate_email' => $candidate_email,
                'subject' => $_subject,
                'message' => $_message,
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'reply_to' => $reply_to,
                'reply_name' => $reply_name,
                'args' => $args,
            );

            return array($email);
        }

        return array();
    }

    static public function get_email_contact($args){
        $type = 'contact';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $item_id = $args['item_id'];
            $from_name = $args['name'];
            $from_email = $args['email'];
            $_subject = $args['subject'];
            $mesage = $args['message'];
            $post = get_post($item_id);
            if($post){
                $author = IWJ_User::get_user($post->post_author);
                $current_user_id = get_current_user_id();
                if($current_user_id != 0){
                    $user = IWJ_User::get_user($current_user_id);
                }

                if($author){
                    $subject = iwj_option('email_'.$type.'_subject');
                    $heading = iwj_option('email_'.$type.'_heading');
                    $content = iwj_option('email_'.$type.'_content');
                    $recipient = $author->get_email();

                    if($current_user_id != 0){
                        $reply_to = $user->get_email();
                        $reply_name = $user->get_display_name();

                        $args = array(
                            'user' => $user,
                            'item_id' => $item_id,
                            'author' => $author,
                            'from_name' => $from_name,
                            'from_email' => $from_email,
                            'to_name' => $author->get_display_name(),
                            'to_email' => $author->get_email(),
                            'subject' => $_subject,
                            'message' => $mesage,
                        );
                        $email = array(
                            'subject' => $subject,
                            'heading' => $heading,
                            'content' => $content,
                            'recipients' => $recipient,
                            'reply_to' => $reply_to,
                            'reply_name' => $reply_name,
                            'args' => $args,
                        );
                    }else{
                        $args = array(
                            'item_id' => $item_id,
                            'author' => $author,
                            'from_name' => $from_name,
                            'from_email' => $from_email,
                            'to_name' => $author->get_display_name(),
                            'to_email' => $author->get_email(),
                            'subject' => $_subject,
                            'message' => $mesage,
                        );
                        $email = array(
                            'subject' => $subject,
                            'heading' => $heading,
                            'content' => $content,
                            'recipients' => $recipient,
                            'reply_to' => $from_email,
                            'reply_name' => $from_name,
                            'args' => $args,
                        );
                    }
                    return array($email);
                }
            }
        }

        return array();
    }

    static public function get_email_verify_account($args){
        $type = 'verify_account';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $user = $args['user'];
            if($user){
                $subject = iwj_option('email_'.$type.'_subject');
                $heading = iwj_option('email_'.$type.'_heading');
                $content = iwj_option('email_'.$type.'_content');
                $recipient = $user->get_email();

                $args = array(
                    'user' => $user,
                    'activation_url' => $user->get_activation_url(),
                    'display_name' => $user->get_display_name(),
                );

                $email = array(
                    'subject' => $subject,
                    'heading' => $heading,
                    'content' => $content,
                    'recipients' => $recipient,
                    'args' => $args,
                );

                return array($email);
            }
        }

        return array();
    }

    static public function get_email_delete_account($args){
        $type = 'delete_account';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $user = $args['user'];
            if($user){

                $subject = iwj_option('email_'.$type.'_subject');
                $heading = iwj_option('email_'.$type.'_heading');
                $content = iwj_option('email_'.$type.'_content');
                $recipient = $user->get_email();

                $args = array(
                    'display_name' => $user->get_display_name(),
                    'email' => $user->get_email(),
                );

                $email = array(
                    'subject' => $subject,
                    'heading' => $heading,
                    'content' => $content,
                    'recipients' => $recipient,
                    'args' => $args,
                );

                return array($email);
            }
        }

        return array();
    }

    static public function get_email_new_review($args){
        $type = 'new_review';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $review = $args;
            $user_post = $review->get_user_post();
            $recipient = get_bloginfo('admin_email');
            $heading = iwj_option('email_' . $type . '_heading');
            $subject = iwj_option('email_' . $type . '_subject');
            $content = iwj_option('email_' . $type . '_content');
            $args = array(
                'review' => $review,
                'review_title' => $review->get_title(),
                'rating' => $review->get_rate_star(),
                'review_content' =>$review->get_content(),
                'candidate_name' => $user_post->get_display_name(),
                'employer_name' => $review->get_employer_name(),
                'admin_review_url' => $review ->admin_link(),
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }
        return array();
    }

    static public function get_email_approved_review($args){
        $type = 'approved_review';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email){
            $review = $args;
            $user_post = $review->get_user_post();
            $employer = $review->get_employer();
            $recipient = $employer->get_email();
            $heading = iwj_option('email_' . $type . '_heading');
            $subject = iwj_option('email_' . $type . '_subject');
            $content = iwj_option('email_' . $type . '_content');

            $args = array(
                'review'         => $review,
                'review_title'   => $review->get_title(),
                'review_content' =>$review->get_content(),
                'rating'        => $review->get_rate_star(),
                'author'         => $user_post,
                'employer'       => $employer,
                'candidate_name' => $user_post->get_display_name(),
                'employer_name'  => $employer->get_display_name(),
                'review_url'     => $review->review_link(),
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }
        return array();
    }

    static public function get_email_candidate_approved_review($args){
        $type = 'candidate_approved_review';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email){
            $review = $args;
            $user_post = $review->get_user_post();
            $employer = $review->get_employer();
            $recipient = $user_post->get_email();
            $heading = iwj_option('email_' . $type . '_heading');
            $subject = iwj_option('email_' . $type . '_subject');
            $content = iwj_option('email_' . $type . '_content');

            $args = array(
                'review'         => $review,
                'review_title'   => $review->get_title(),
                'review_content' =>$review->get_content(),
                'rating'        => $review->get_rate_star(),
                'author'         => $user_post,
                'employer_name'  => $employer->get_display_name(),
                'candidate_name' => $user_post->get_display_name(),
                'review_url'     => $review->review_link(),
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }
        return array();
    }

    static public function get_email_rejected_review($args){
        $type = 'rejected_review';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $review = $args;
            $user_post = $review->get_user_post();
            $employer = $review->get_employer();
            $recipient = $user_post->get_email();
            $heading = iwj_option('email_' . $type . '_heading');
            $subject = iwj_option('email_' . $type . '_subject');
            $content = iwj_option('email_' . $type . '_content');

            $args = array(
                'review'          => $review,
                'review_title'    => $review->get_title(),
                'reason'          => $review->get_reason(),
                'author'          => $user_post,
                'candidate_name'  => $user_post->get_display_name(),
                'employer_name'   => $employer->get_display_name(),
                'edit_review_url' => $review->user_link_candidate_edit_review(),
            );

            $email = array(
                'subject' => $subject,
                'heading' => $heading,
                'content' => $content,
                'recipients' => $recipient,
                'args' => $args,
            );

            return array($email);
        }

        return array();
    }


    static public function get_email_membership_notice($args) {
        $type = 'membership_notice';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $user_id = $args['user_id'];
            $user = IWJ_User::get_user($user_id);
            if($user && $user->has_plan() && $user->plan_is_active()){
                $recipient = $user->get_email();
                $heading = iwj_option('email_' . $type . '_heading');
                $subject = iwj_option('email_' . $type . '_subject');
                $content = iwj_option('email_' . $type . '_content');
                $plan = $user->get_plan();
                if($plan){
                    $renew_plan_url = add_query_arg(array('iwj-renew-plan'=> true), iwj_get_page_permalink('dashboard'));
                    $expiry_date = date_i18n(get_option('date_format'), $user->plan_get_expiry_date());
                    $args = array(
                        'user' => $user,
                        'user_name' => $user->get_display_name(),
                        'plan' => $plan,
                        'plan_title' => $plan->get_title(),
                        'expiry_date' => $expiry_date,
                        'can_renew' => $plan->can_buy() ? true : false,
                        'renew_plan_url' => $renew_plan_url,
                    );
                }

                $email = array(
                    'subject' => $subject,
                    'heading' => $heading,
                    'content' => $content,
                    'recipients' => $recipient,
                    'args' => $args,
                );

                return array($email);
            }
        }

        return array();
    }

    static public function get_email_membership_expired($args) {
        $type = 'membership_expired';
        $enable_email = iwj_option('email_'.$type.'_enable');
        if ($enable_email) {
            $user_id = $args['user_id'];
            $user = IWJ_User::get_user($user_id);
            if($user && $user->has_plan()){
                $recipient = $user->get_email();
                $heading = iwj_option('email_' . $type . '_heading');
                $subject = iwj_option('email_' . $type . '_subject');
                $content = iwj_option('email_' . $type . '_content');
                $plan = $user->get_plan();
                if($plan){
                    $renew_plan_url = add_query_arg(array('iwj-renew-plan'=> true), iwj_get_page_permalink('dashboard'));
                    $select_plan_url = add_query_arg(array('iwj_tab'=> 'select-plan'), iwj_get_page_permalink('dashboard'));
                    $expiry_date = date_i18n(get_option('date_format'), $user->plan_get_expiry_date());
                    $args = array(
                        'user' => $user,
                        'user_name' => $user->get_display_name(),
                        'plan' => $plan,
                        'plan_title' => $plan->get_title(),
                        'expiry_date' => $expiry_date,
                        'can_renew' => $plan->can_buy() ? true : false,
                        'renew_plan_url' => $renew_plan_url,
                        'select_plan_url' => $select_plan_url,
                    );
                }

                $email = array(
                    'subject' => $subject,
                    'heading' => $heading,
                    'content' => $content,
                    'recipients' => $recipient,
                    'args' => $args,
                );

                return array($email);
            }
        }

        return array();
    }
}
