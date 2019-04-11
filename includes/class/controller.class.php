<?php

class IWJ_Controller {

    static function init() {
        add_action('wp', array(__CLASS__, 'form_action'), 20);
        add_action('wp', array(__CLASS__, 'check_permission'), 20);
        add_action('wp_loaded', array(__CLASS__, 'cancel_order'), 20);
        add_action('wp_loaded', array(__CLASS__, 'social_login'), 20);
        add_action('wp_loaded', array(__CLASS__, 'social_apply'), 20);
        add_action('wp_loaded', array(__CLASS__, 'received_payment'), 20);
        add_action('wp_loaded', array(__CLASS__, 'publish_job'), 20);
        add_action('wp_loaded', array(__CLASS__, 'unpublish_job'), 20);
        add_action('wp_loaded', array(__CLASS__, 'featured_job'), 20);
        add_action('wp_loaded', array(__CLASS__, 'unfeatured_job'), 20);
        add_action('wp_loaded', array(__CLASS__, 'renewal_job'), 20);
        if (iwj_option('submit_job_mode' == '3')) {
            add_action('wp_loaded', array(__CLASS__, 'renew_plan'), 20);
            add_action('wp_loaded', array(__CLASS__, 'cancel_subscription'), 20);
        }
        add_action('wp_loaded', array(__CLASS__, 'verify_account'), 20);
        add_action('wp_loaded', array(__CLASS__, 'send_mail_queue'), 99);
        add_action('wp_footer', array(__CLASS__, 'confirm_job_alert'));
        add_action('wp_footer', array(__CLASS__, 'unsubscribe_job_alert'));
        add_action('wp_footer', array(__CLASS__, 'show_apply_form'));

        add_action('wp_head', array(__CLASS__, 'post_views'));
        add_action('wp_ajax_iwj_radio_tax_add_taxterm', array(__CLASS__, 'ajax_add_term'));
        add_action('wp_ajax_nopriv_iwj_view_post', array(__CLASS__, 'set_view_post'));

        add_action('wp_ajax_iwj_reset_settings', array(__CLASS__, 'reset_settings'));

        add_action('wp_ajax_nopriv_iwj_login', array(__CLASS__, 'login'));
        add_action('wp_ajax_nopriv_iwj_register', array(__CLASS__, 'register'));
        add_action('wp_ajax_iwj_lostpass', array(__CLASS__, 'lostpass'));
        add_action('wp_ajax_nopriv_iwj_lostpass', array(__CLASS__, 'lostpass'));
        add_action('wp_ajax_iwj_resetpass', array(__CLASS__, 'resetpass'));
        add_action('wp_ajax_nopriv_iwj_resetpass', array(__CLASS__, 'resetpass'));
        add_action('wp_ajax_iwj_change_email', array(__CLASS__, 'change_email'));
        add_action('wp_ajax_iwj_delete_account', array(__CLASS__, 'delete_account'));

        add_action('wp_ajax_iwj_update_profile', array(__CLASS__, 'update_profile'));
        add_action('wp_ajax_iwj_change_password', array(__CLASS__, 'change_password'));

        add_action('wp_ajax_iwj_submit_job', array(__CLASS__, 'submit_job'));
        add_action('wp_ajax_iwj_renew_job', array(__CLASS__, 'renew_job'));
        add_action('wp_ajax_iwj_edit_job', array(__CLASS__, 'edit_job'));
        add_action('wp_ajax_iwj_get_order_price', array(__CLASS__, 'get_order_price'));
        add_action('wp_ajax_iwj_check_pay_order', array(__CLASS__, 'check_pay_order'));

        add_action('wp_ajax_iwj_follow', array(__CLASS__, 'follow'));
        add_action('wp_ajax_nopriv_iwj_follow', array(__CLASS__, 'follow'));

        add_action('wp_ajax_iwj_unfollow', array(__CLASS__, 'unfollow'));
        add_action('wp_ajax_nopriv_iwj_unfollow', array(__CLASS__, 'unfollow'));

        add_action('wp_ajax_iwj_save_job', array(__CLASS__, 'save_job'));
        add_action('wp_ajax_nopriv_iwj_save_job', array(__CLASS__, 'save_job'));

        add_action('wp_ajax_iwj_undo_save_job', array(__CLASS__, 'undo_save_job'));
        add_action('wp_ajax_nopriv_iwj_undo_save_job', array(__CLASS__, 'undo_save_job'));

        add_action('wp_ajax_iwj_view_resum', array(__CLASS__, 'view_resum'));
        add_action('wp_ajax_iwj_delete_view_resum', array(__CLASS__, 'delete_view_resum'));

        add_action('wp_ajax_iwj_save_resum', array(__CLASS__, 'save_resum'));
        add_action('wp_ajax_nopriv_iwj_save_resum', array(__CLASS__, 'save_resum'));
        add_action('wp_ajax_iwj_delete_save_resum', array(__CLASS__, 'delete_save_resum'));

        add_action('wp_ajax_iwj_undo_save_resum', array(__CLASS__, 'undo_save_resum'));
        add_action('wp_ajax_nopriv_iwj_undo_save_resum', array(__CLASS__, 'undo_save_resum'));

        add_action('wp_ajax_iwj_submit_alert', array(__CLASS__, 'submit_alert'));
        add_action('wp_ajax_nopriv_iwj_submit_alert', array(__CLASS__, 'submit_alert'));
        add_action('wp_ajax_iwj_edit_alert', array(__CLASS__, 'edit_alert'));
        add_action('wp_ajax_iwj_delete_alert', array(__CLASS__, 'delete_alert'));
        add_action('wp_ajax_iwj_delete_job', array(__CLASS__, 'delete_job'));
        add_action('wp_ajax_iwj_confirm_apply_job', array(__CLASS__, 'confirm_apply_job'));

        add_action('wp_ajax_iwj_update_application', array(__CLASS__, 'update_application'));
        add_action('wp_ajax_iwj_delete_application', array(__CLASS__, 'delete_application'));
        add_action('wp_ajax_iwj_application_email', array(__CLASS__, 'application_email'));
        add_action('wp_ajax_iwj_contact', array(__CLASS__, 'contact'));
        add_action('wp_ajax_nopriv_iwj_contact', array(__CLASS__, 'contact'));
        add_action('wp_ajax_iwj_review', array(__CLASS__, 'review'));
        add_action('wp_ajax_iwj_edit_review', array(__CLASS__, 'edit_review'));
        add_action('wp_ajax_iwj_update_review', array(__CLASS__, 'update_review'));
        add_action('wp_ajax_iwj_reply_review', array(__CLASS__, 'reply_review'));
        add_action('wp_ajax_iwj_edit_reply_review', array(__CLASS__, 'edit_reply_review'));
        add_action('wp_ajax_iwj_delete_review', array(__CLASS__, 'delete_review'));
        add_action('wp_ajax_iwj_delete_reply', array(__CLASS__, 'delete_reply'));

        add_action('wp_ajax_iwj_add_order_note', array(__CLASS__, 'add_order_note'));
        add_action('wp_ajax_iwj_delete_order_note', array(__CLASS__, 'delete_order_note'));
        add_action('wp_ajax_iwj_send_customer_invoice', array(__CLASS__, 'send_customer_invoice'));

        add_action('wp_ajax_iwj_get_application_details', array(__CLASS__, 'get_application_details'));
        add_action('wp_ajax_iwj_get_submited_application_details', array(
            __CLASS__,
            'get_submited_application_details'
        ));
        add_action('wp_ajax_iwj_get_order_details', array(__CLASS__, 'get_order_details'));

        add_action('wp_ajax_iwj_resend_verification', array(__CLASS__, 'resend_verification'));
        add_action('wp_ajax_iwj_findjob_map', array(__CLASS__, 'findjob_map'));
        add_action('wp_ajax_nopriv_iwj_findjob_map', array(__CLASS__, 'findjob_map'));

        add_action('wp_ajax_iwj_candidate_map', array(__CLASS__, 'candidate_map'));
        add_action('wp_ajax_nopriv_iwj_candidate_map', array(__CLASS__, 'candidate_map'));

        add_action('wp_ajax_nopriv_iwj_loadmore_jobs', array(__CLASS__, 'loadmore_jobs'));
        add_action('wp_ajax_iwj_loadmore_jobs', array(__CLASS__, 'loadmore_jobs'));
        add_action('wp_ajax_nopriv_iwj_indeed_load_data', array(__CLASS__, 'indeed_load_data'));
        add_action('wp_ajax_iwj_indeed_load_data', array(__CLASS__, 'indeed_load_data'));
        add_action('wp_ajax_nopriv_iwj_loadmore_indeed_jobs', array(__CLASS__, 'indeed_loadmore_jobs'));
        add_action('wp_ajax_iwj_loadmore_indeed_jobs', array(__CLASS__, 'indeed_loadmore_jobs'));
        add_action('wp_ajax_iwj_jobs_export', array(__CLASS__, 'jobs_export'));
        add_action('wp_ajax_iwj_upload_csv_actions', array(__CLASS__, 'upload_csv_actions'));
        add_action('wp_ajax_iwj_set_post_types', array(__CLASS__, 'set_post_types'));
        add_action('wp_ajax_iwj_parse_data_to_import', array(__CLASS__, 'parse_data_to_import'));

        add_action('wp_ajax_iwj_create_all_job_products', array(__CLASS__, 'create_all_job_products'));

        add_action('iwj_logged_in', array(__CLASS__, 'logged_in_fallback'));
    }

    public static function ajax_add_term() {
        $taxonomy = !empty($_POST['taxonomy']) ? $_POST['taxonomy'] : '';
        $term = !empty($_POST['term']) ? $_POST['term'] : '';
        $tax = get_taxonomy($taxonomy);

        check_ajax_referer('radio-tax-add-' . $taxonomy, '_wpnonce_radio-add-tag');

        if (!$tax || empty($term))
            exit();

        if (!current_user_can($tax->cap->edit_terms))
            die('-1');

        $tag = wp_insert_term($term, $taxonomy);

        if (!$tag || is_wp_error($tag) || (!$tag = get_term($tag['term_id'], $taxonomy))) {
            //TODO Error handling
            exit();
        }

        $id = $taxonomy . '-' . $tag->term_id;
        $name = 'tax_input[' . $taxonomy . ']';
        $value = 'value="' . $tag->term_id . '"';

        $html = '<li id="' . $id . '"><label class="selectit"><input type="radio" id="in-' . $id . '" name="' . $name . '" ' . $value . ' />' . $tag->name . '</label></li>';

        echo json_encode(array('term' => $tag->term_id, 'html' => $html));
        exit();
    }

    static function findjob_map() {
        check_ajax_referer('iwj-security');
        $check_login = '0';
        if (is_user_logged_in()) {
            $check_login = '1';
        }
        $show_company = iwj_option('show_company_job');
        $show_salary = iwj_option('show_salary_job');
        $show_location = iwj_option('show_location_job');

        $filters = IWJ_Job_Listing::get_data_filters();
        $query = IWJ_Job_Listing::get_query_jobs($filters);

        $array_data = array();
        if ($query->have_posts()) :
            $i = 0;
            $user = IWJ_User::get_user();
            while ($query->have_posts()) :
                $query->the_post();
                $job = IWJ_Job::get_job(get_the_ID());
                $maps = $job->get_map();
                $types = $job->get_type();
                $author = $job->get_author();
                $lat = $maps[0];
                $lng = $maps[1];
                $array_data[$i]['lat'] = $lat;
                $array_data[$i]['lng'] = $lng;
                $array_data[$i]['id'] = $job->get_id();
                $array_data[$i]['link'] = $job->permalink();
                $array_data[$i]['title'] = $job->get_title();
                if (($job->get_salary()) && ($show_salary == '1')) {
                    $array_data[$i]['salary'] = $job->get_salary();
                } else {
                    $array_data[$i]['salary'] = '';
                }
                if (($job->get_locations_links()) && ($show_location == '1')) {
                    $array_data[$i]['location'] = $job->get_locations_links();
                } else {
                    $array_data[$i]['location'] = '';
                }
                if ($author && ($show_company == '1')) {
                    $array_data[$i]['company_name'] = $author->get_display_name();
                    $array_data[$i]['company_link'] = $author->permalink();
                } else {
                    $array_data[$i]['company_name'] = '';
                    $array_data[$i]['company_link'] = '';
                }
                $array_data[$i]['check_login'] = $check_login;
                if ($types) {
                    $array_data[$i]['type'] = $types->name;
                    $array_data[$i]['link_type'] = get_term_link($types->term_id, 'iwj_type');
                    $array_data[$i]['color'] = get_term_meta($types->term_id, IWJ_PREFIX . 'color', true);
                } else {
                    $array_data[$i]['type'] = '';
                    $array_data[$i]['link_type'] = '';
                    $array_data[$i]['color'] = '';
                }
                if ($check_login == 1) {
                    $array_data[$i]['savejobclass'] = $user->is_saved_job($job->get_id()) ? 'saved' : '';
                } else {
                    $array_data[$i]['savejobclass'] = '';
                }
                $i ++;
            endwhile;
            wp_reset_postdata();
        endif;
        
        if ($array_data && count($array_data) != '0') {
            echo json_encode($array_data);
        } else {
            $data_none['none_data'] = '0';
            if (isset($_GET['iwj_location']) && $_GET['iwj_location']) {
				if(is_numeric($_GET['iwj_location'])){
					$term = get_term($_GET['iwj_location']);
					$location_name = $term->name;
				}else{
					$location_name = $_GET['iwj_location'];
				}
                $data_none['location'] = $location_name;
            }
            echo json_encode($data_none);
        }


        exit();
    }

    static function check_recaptcha() {
        if (isset($_POST['g-recaptcha-response'])) {
            $captcha = $_POST['g-recaptcha-response'];
            $response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=" . iwj_option('google_recaptcha_secret_key') . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR'], array('decompress' => false));
            if ($response) {
                $response = wp_remote_retrieve_body($response);
                if ($response) {
                    $response = json_decode($response);
                    if (!$response->success || $response->success === false) {
                        $err = $response->{'error-codes'};
                        if (is_array($err)) {
                            return implode(', ', $err);
                        } else {
                            return $err;
                        }
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        return true;
    }

    static function check_permission() {
        global $post;
        if ($post && $post->ID == iwj_get_page_id('dashboard')) {
            if (!is_user_logged_in()) {
                $url = iwj_get_page_permalink('login');
                $redirect = $_SERVER['REQUEST_URI'];
                if ($redirect) {
                    $url = add_query_arg('redirect_to', urlencode($redirect), $url);
                }
                wp_redirect($url);
                exit;
            } elseif (iwj_option('verify_account')) {
                $user = IWJ_User::get_user();
                if (!$user->is_verified()) {
                    $url = iwj_get_page_permalink('verify_account');
                    wp_redirect($url);
                    exit;
                }
            }

            $tab = isset($_GET['iwj_tab']) ? $_GET['iwj_tab'] : '';
            $dashboard = iwj_get_page_permalink('dashboard');
            switch ($tab) {
                case 'jobs':
                case 'applications':
                case 'packages':
                case 'new-package':
                case 'save-resumes':
                case 'view-resumes':
                    if (!current_user_can('create_iwj_jobs')) {
                        wp_redirect($dashboard);
                        exit;
                    }
                    break;
                case 'new-job':
                    $job_id = isset($_GET['job-id']) ? $_GET['job-id'] : 0;
                    if ($job_id) {
                        $job = IWJ_Job::get_job($job_id);
                        if (!$job || $job->get_author_id() != get_current_user_id()) {
                            //update_option('_iwj_front_messsage', iwj_get_alert(__('You do not have permission to this.', 'iwjob'), 'error'));
                            wp_redirect($dashboard);
                            exit;
                        }
                    } else {
                        if (!current_user_can('create_iwj_jobs')) {
                            wp_redirect($dashboard);
                            exit;
                        }
                    }
                    break;
                case 'edit-job':
                    $job_id = isset($_GET['job-id']) ? $_GET['job-id'] : 0;
                    $job = IWJ_Job::get_job($job_id);
                    if (!$job || !$job->can_edit()) {
                        //update_option('_iwj_front_messsage', iwj_get_alert(__('You do not have permission to this.', 'iwjob'), 'error'));
                        wp_redirect($dashboard);
                        exit;
                    }
                    break;
                case 'apply-job-package':
                case 'new-apply-job-package':
                    if (!current_user_can('apply_job')) {
                        wp_redirect($dashboard);
                        exit;
                    }
                    break;
                case 'orders':
                    if (!( current_user_can('create_iwj_jobs') || current_user_can('apply_job') )) {
                        wp_redirect($dashboard);
                        exit;
                    }
                    break;
                case 'follows':
                case 'save-jobs':
                case 'alerts':
                    if (!current_user_can('apply_job')) {
                        wp_redirect($dashboard);
                        exit;
                    }
                    break;
                case 'edit-alert':
                    $alert_id = isset($_GET['alert-id']) ? $_GET['alert-id'] : 0;
                    $alert = IWJ_Alert::get_alert($alert_id);
                    if (!$alert || !$alert->can_edit()) {
                        //update_option('_iwj_front_messsage', iwj_get_alert(__('You do not have permission to this.', 'iwjob'), 'error'));
                        wp_redirect($dashboard);
                        exit;
                    }
                    break;
                case 'view-application':
                    $application_id = isset($_GET['application-id']) ? $_GET['application-id'] : 0;
                    $application = IWJ_Application::get_application($application_id);
                    if (!$application || !$application->can_view()) {
                        //update_option('_iwj_front_messsage', iwj_get_alert(__('You do not have permission to this.', 'iwjob'), 'error'));
                        wp_redirect($dashboard);
                        exit;
                    }
                    break;
                case 'view-order':
                    $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : 0;
                    $order = IWJ_Order::get_order($order_id);
                    if (!$order || !$order->can_view()) {
                        wp_redirect($dashboard);
                        exit;
                    }
                    break;
                case 'view-u-order':
                    $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : 0;
                    $order = wc_get_order($order_id);
                    if (!$order || $order->get_user_id() != get_current_user_id()) {
                        wp_redirect($dashboard);
                        exit;
                    }
                    break;
                default :
                    do_action('iwj_dashboard_tab_permission', $tab);
                    break;
            }

            $step = isset($_GET['step']) ? $_GET['step'] : '';

            if (iwj_option('submit_job_mode') == '3' && isset($_GET['iwj_tab']) && $_GET['iwj_tab']) {
                $job_id = isset($_GET['job-id']) ? $_GET['job-id'] : 0;
                if ($tab == 'new-job' && !$job_id /* && iwj_option('submit_job_mode') == '2' */) {
                    $user = IWJ_User::get_user();
                    if (!$user->has_plan() && (!isset($_GET['plan-id']) || !$_GET['plan-id'])) {
                        wp_redirect(add_query_arg(array('iwj_tab' => 'select-plan', 'new-job' => '1'), $dashboard));
                        exit;
                    } elseif ($user->has_plan()) {
                        if (!$user->plan_jobs_is_available()) {
                            wp_redirect(add_query_arg(array('iwj_tab' => 'upgrade-plan', 'notice' => 1), $dashboard));
                            exit;
                        } elseif (!$user->plan_is_active()) {
                            wp_redirect(add_query_arg(array('iwj_tab' => 'select-plan', 'notice' => 1), $dashboard));
                            exit;
                        }
                    }
                }
            }
        }
    }

    static function cancel_order() {
        if (isset($_GET['iwj_cancel_order']) && $_GET['iwj_cancel_order'] == 'true' && $_GET['order_id'] && $_GET['key']) {
            $order_id = (int) $_GET['order_id'];
            $key = sanitize_text_field($_GET['key']);
            $order = IWJ_Order::get_order($order_id);
            if ($order && $order->get_author_id() == get_current_user_id() && $order->get_key() == $key) {
                $order->cancelled_order();
            }

            $redirect = $_GET['redirect'];
            if ($redirect) {
                wp_safe_redirect($redirect);
                exit;
            }
        }
    }

    static function social_login() {
        if (isset($_GET['iwj_social_login']) && $_GET['iwj_social_login']) {
            $social = IWJ()->social_logins()->get_social($_GET['iwj_social_login']);
            if ($social) {
                $social->oauth();
            }
        }
    }

    static function social_apply() {
        if (isset($_GET['iwj_social_apply']) && $_GET['iwj_social_apply']) {
            $apply = IWJ()->applies()->get_apply($_GET['iwj_social_apply']);
            if ($apply) {
                $apply->oauth();
            }
        }
    }

    static function publish_job() {
        //is admin or free job or single price with woocommerce mode or plan mode
        if (isset($_GET['iwj_publish_job']) && $_GET['iwj_publish_job']) {
            $job_id = $_GET['iwj_publish_job'];
            $job = IWJ_Job::get_job($job_id);
            $dashboard_url = iwj_get_page_permalink('dashboard');
            if ($job && $job->get_author_id() == get_current_user_id() && $job->has_status('draft')) {

                $expiry_date = $job->get_expiry();
                if (current_user_can('administrator') || ($expiry_date > current_time('timestamp'))) {
                    $job->change_status('publish', false);

                    $referer = wp_get_referer();
                    if ($referer) {
                        wp_redirect($referer);
                        exit;
                    } else {

                        $url = add_query_arg(array('iwj_tab' => 'jobs'), $dashboard_url);
                        wp_redirect($url);
                        exit;
                    }
                    die();
                    //package
                } elseif (iwj_option('submit_job_mode') == '1') {
                    $url = add_query_arg(array('iwj_tab' => 'new-job', 'step' => 'select-package', 'job-id' => $job_id), $dashboard_url);
                    wp_redirect($url);
                    exit;
                    //single
                } elseif (iwj_option('submit_job_mode') == '2') {
                    if (iwj_woocommerce_checkout()) {
                        $iwj_woocommerce = new IWJ_Woocommerce();
                        $iwj_woocommerce->add_to_cart('newjob', $job_id);
                    } else {
                        $url = add_query_arg(array('iwj_tab' => 'new-job', 'step' => 'payment', 'job-id' => $job_id), $dashboard_url);
                        wp_redirect($url);
                        exit;
                    }
                    //plan mode
                } elseif (iwj_option('submit_job_mode') == '3') {

                    $job->change_status('iwj-pending-payment', false);

                    $dashboard_url = iwj_get_page_permalink('dashboard');
                    $user = IWJ_User::get_user();
                    if ($user->has_plan()) {
                        if ($user->plan_is_active() && $user->plan_jobs_is_available()) {
                            if (iwj_option('new_job_auto_approved')) {
                                $job->change_status('publish', false);
                            } else {
                                $job->change_status('pending', true);
                            }

                            $url = add_query_arg(array(
                                'iwj_tab' => 'new-job',
                                'step' => 'done',
                                'job-id' => $job_id
                                    ), $dashboard_url);
                            wp_redirect($url);
                            exit;
                        } else {
                            if (!$user->plan_jobs_is_available()) {
                                $url = add_query_arg(array(
                                    'iwj_tab' => 'upgrade-plan',
                                    'notice' => 1
                                        ), $dashboard_url);
                                wp_redirect($url);
                                exit;
                            } elseif (!$user->plan_is_active()) {
                                $url = add_query_arg(array(
                                    'iwj_tab' => 'select-plan',
                                    'notice' => 1
                                        ), $dashboard_url);
                                wp_redirect($url);
                                exit;
                            }
                        }
                    } else {
                        $plan_id = $job->get_plan_id();
                        $plan = $job->get_plan();
                        if ($plan && $plan->can_buy()) {
                            if ((float) $plan->get_price() > 0) {
                                if (iwj_woocommerce_checkout()) {
                                    $iwj_woocommerce = new IWJ_Woocommerce();
                                    $iwj_woocommerce->add_to_cart('plan', $job_id, $plan_id);
                                    global $woocommerce;
                                    $checkout_url = $woocommerce->cart->get_checkout_url();
                                    wp_redirect($checkout_url);
                                    exit;
                                } else {
                                    $order_id = $job->get_order_id();
                                    $order = IWJ_Order::get_order($order_id);
                                    if ($order) {
                                        if ($order->has_status('pending-payment')) {
                                            $cart = new IWJ_Cart();
                                            $cart->set_order($order_id);
                                            $url = add_query_arg(array(
                                                'iwj_tab' => 'checkout',
                                                    ), $dashboard_url);
                                            wp_redirect($url);
                                            exit;
                                        } else {
                                            update_post_meta($order_id, IWJ_PREFIX . 'job_id', '');
                                            clean_post_cache($order_id);
                                            wp_delete_post($order_id);
                                        }
                                    }

                                    $cart = new IWJ_Cart();
                                    $cart->set('plan', $plan_id, $plan->get_price(), array('job_id' => $job_id));
                                    $url = add_query_arg(array(
                                        'iwj_tab' => 'checkout',
                                            ), $dashboard_url);
                                    wp_redirect($url);
                                    exit;
                                }
                            } else {
                                $user->set_plan($plan->get_id());
                                update_post_meta($job_id, IWJ_PREFIX . 'is_free', '1');
                                if (iwj_option('new_free_job_auto_approved')) {
                                    $job->change_status('publish', false);
                                } else {
                                    $job->change_status('pending');
                                }

                                $url = add_query_arg(array(
                                    'iwj_tab' => 'new-job',
                                    'step' => 'done',
                                    'job-id' => $job_id
                                        ), $dashboard_url);
                                wp_redirect($url);
                                exit;
                            }
                        } else {
                            $url = add_query_arg(array(
                                'iwj_tab' => 'select-plan',
                                    ), $dashboard_url);
                            wp_redirect($url);
                            exit;
                        }
                    }
                } else {
                    //is free job mode
                    update_post_meta($job_id, IWJ_PREFIX . 'free_job', '1');
                    if (iwj_option('new_free_job_auto_approved')) {
                        $job->change_status('publish', false);
                    } else {
                        $job->change_status('pending');
                    }

                    $url = add_query_arg(array(
                        'iwj_tab' => 'new-job',
                        'step' => 'done',
                        'job-id' => $job_id
                            ), $dashboard_url);
                    wp_redirect($url);
                    die();
                }
            }
        }
    }

    static function unpublish_job() {
        if (isset($_GET['iwj_unpublish_job']) && $_GET['iwj_unpublish_job']) {
            $job_id = $_GET['iwj_unpublish_job'];
            $job = IWJ_Job::get_job($job_id);
            if ($job && $job->get_author_id() == get_current_user_id() && $job->has_status('publish')) {
                $job->change_status('draft', false);
                $referer = wp_get_referer();
                if ($referer) {
                    wp_redirect($referer);
                    exit;
                } else {
                    $dashboard_url = iwj_get_page_permalink('dashboard');
                    $url = add_query_arg(array('iwj_tab' => 'jobs'), $dashboard_url);
                    wp_redirect($url);
                    exit;
                }
            }
        }
    }

    static function featured_job() {
        if (isset($_GET['iwj_featured_job']) && $_GET['iwj_featured_job']) {
            $job_id = $_GET['iwj_featured_job'];
            $job = IWJ_Job::get_job($job_id);
            if ($job && $job->get_author_id() == get_current_user_id() && !$job->is_featured()) {
                $dashboard_url = iwj_get_page_permalink('dashboard');
                if (current_user_can('administrator')) {
                    $job->set_featured(true);
                    $referer = wp_get_referer();
                    if ($referer) {
                        wp_redirect($referer);
                        exit;
                    } else {
                        $url = add_query_arg(array('iwj_tab' => 'jobs'), $dashboard_url);
                        wp_redirect($url);
                        exit;
                    }
                } elseif (iwj_option('submit_job_mode') == '3') {
                    $user = IWJ_User::get_user();
                    if ($user->has_plan()) {
                        if ($user->plan_is_active() && $user->plan_featured_jobs_is_available()) {
                            $job->set_featured(true, $user->plan_get_expiry_date());
                            $referer = wp_get_referer();
                            if ($referer) {
                                wp_redirect($referer);
                                exit;
                            } else {
                                $url = add_query_arg(array('iwj_tab' => 'jobs'), $dashboard_url);
                                wp_redirect($url);
                                exit;
                            }
                        } else {
                            if (!$user->plan_featured_jobs_is_available()) {
                                $url = add_query_arg(array(
                                    'iwj_tab' => 'upgrade-plan',
                                    'type' => 'featured',
                                    'notice' => 1
                                        ), $dashboard_url);
                                wp_redirect($url);
                                exit;
                            } elseif (!$user->plan_is_active()) {
                                $url = add_query_arg(array(
                                    'iwj_tab' => 'select-plan',
                                    'notice' => 1
                                        ), $dashboard_url);
                                wp_redirect($url);
                                exit;
                            }
                        }
                    } else {
                        $url = add_query_arg(array(
                            'iwj_tab' => 'select-plan',
                                ), $dashboard_url);
                        wp_redirect($url);
                        exit;
                    }
                } else {
                    $expiry_date = $job->get_featured_expiry();
                    if ($expiry_date > current_time('timestamp')) {
                        $job->set_featured(true, false);
                        $referer = wp_get_referer();
                        if ($referer) {
                            wp_redirect($referer);
                            exit;
                        } else {
                            $dashboard_url = iwj_get_page_permalink('dashboard');
                            $url = add_query_arg(array('iwj_tab' => 'jobs'), $dashboard_url);
                            wp_redirect($url);
                            exit;
                        }
                    } else {
                        $job = IWJ_Job::get_job($job_id);
                        $user_package = $job->get_user_package();
                        if (!$user_package || !$user_package->can_make_featured() && iwj_option('featured_job_price') > 0) {
                            if ($job->get_author_id() == get_current_user_id()) {
                                if(iwj_woocommerce_checkout()){
                                        $iwj_woocommerce = new IWJ_Woocommerce();
                                        $iwj_woocommerce->add_to_cart('featuredjob', $job_id);
                                }else{
                                        $dashboard_url = iwj_get_page_permalink('dashboard');
                                        $url = add_query_arg(array('iwj_tab' => 'make-featured', 'job-id' => $job_id), $dashboard_url);
                                        wp_redirect($url);
                                        exit;
                                }
                            }
                        } else {
                            $dashboard_url = iwj_get_page_permalink('dashboard');
                            $url = add_query_arg(array('iwj_tab' => 'make-featured', 'job-id' => $job_id), $dashboard_url);
                            wp_redirect($url);
                            exit;
                        }
                    }
                }
            }
        }
    }

    static function unfeatured_job() {
        if (isset($_GET['iwj_unfeatured_job']) && $_GET['iwj_unfeatured_job']) {
            $job_id = $_GET['iwj_unfeatured_job'];
            $job = IWJ_Job::get_job($job_id);
            if ($job && $job->get_author_id() == get_current_user_id() && $job->is_featured()) {
                $job->unfeatured();
                $referer = wp_get_referer();
                if ($referer) {
                    wp_redirect($referer);
                    exit;
                } else {
                    $dashboard_url = iwj_get_page_permalink('dashboard');
                    $url = add_query_arg(array('iwj_tab' => 'jobs'), $dashboard_url);
                    wp_redirect($url);
                    exit;
                }
            }
        }
    }

    static function renewal_job() {
        if (isset($_GET['iwj_renew_job']) && $_GET['iwj_renew_job']) {
            $dashboard_url = iwj_get_page_permalink('dashboard');
            $job_id = $_GET['iwj_renew_job'];
            $job = IWJ_Job::get_job($job_id);
            if ($job && $job->get_author_id() == get_current_user_id()) {
                if (iwj_option('submit_job_mode') == '3') {
                    if (current_user_can('administrator')) {
                        $job->renew();
                        $referer = wp_get_referer();
                        if ($referer) {
                            wp_redirect($referer);
                            exit;
                        } else {
                            $url = add_query_arg(array('iwj_tab' => 'jobs'), $dashboard_url);
                            wp_redirect($url);
                            exit;
                        }
                    } else {
                        $user = IWJ_User::get_user();
                        if ($user->has_plan()) {
                            if ($user->plan_is_active() && $user->plan_renew_jobs_is_available()) {
                                $plan = $job->get_plan();
                                $job->renew(array('time'=>$plan->get_expiry(),'unit'=>$plan->get_expiry_unit()));
                                $referer = wp_get_referer();
                                if ($referer) {
                                    wp_redirect($referer);
                                    exit;
                                } else {
                                    $url = add_query_arg(array('iwj_tab' => 'jobs'), $dashboard_url);
                                    wp_redirect($url);
                                    exit;
                                }
                            } else {
                                if (!$user->plan_renew_jobs_is_available()) {
                                    $url = add_query_arg(array(
                                        'iwj_tab' => 'select-plan',
                                        'type' => 'renew',
                                        'notice' => 2
                                            ), $dashboard_url);
                                    wp_redirect($url);
                                    exit;
                                } elseif (!$user->plan_is_active()) {
                                    $url = add_query_arg(array(
                                        'iwj_tab' => 'select-plan',
                                        'notice' => 1
                                            ), $dashboard_url);
                                    wp_redirect($url);
                                    exit;
                                }
                            }
                        } else {
                            $url = add_query_arg(array(
                                'iwj_tab' => 'select-plan',
                                    ), $dashboard_url);
                            wp_redirect($url);
                            exit;
                        }
                    }
                } else {
                    if (iwj_option('renew_job_price') <= 0) {
                        $job->renew();
                        $url = add_query_arg(array('iwj_tab' => 'renew-job', 'step' => 'done', 'job-id' => $job_id), $dashboard_url);
                        wp_redirect($url);
                        exit;
                    } else {
                        $user_package = $job->get_user_package();
                        $is_package_expiry = ($user_package && $user_package->get_expiry()) ? $user_package->get_expiry() < time() : false;
                        if ($user_package && !$is_package_expiry && ($user_package->get_remain_renew_job() > 0 || $user_package->get_remain_renew_job() == -1)) {
                            $package = $user_package->get_package();
                            $job->renew(array('time'=>$package->get_job_expiry(), 'unit'=>$package->get_job_expiry_unit()));
                            if ($user_package->get_remain_renew_job() > 0) {
                                update_post_meta($user_package->get_id(), IWJ_PREFIX . 'remain_renew_job', $user_package->get_remain_renew_job() - 1);
                            }
                            $url = add_query_arg(array('iwj_tab' => 'renew-job', 'step' => 'done', 'job-id' => $job_id), $dashboard_url);
                            wp_redirect($url);
                            exit;
                        } else {
                            if (iwj_woocommerce_checkout()) {
                                $iwj_woocommerce = new IWJ_Woocommerce();
                                $iwj_woocommerce->add_to_cart('renewjob', $job_id);
                            } else {
                                $dashboard_url = iwj_get_page_permalink('dashboard');
                                $url = add_query_arg(array('iwj_tab' => 'renew-job', 'job-id' => $job_id), $dashboard_url);
                                wp_redirect($url);
                                exit;
                            }
                        }
                    }
                }
            }
        }
    }

    static function verify_account() {
        if (isset($_GET['iwj_verify_account']) && $_GET['iwj_verify_account']) {
            $user_id = $_GET['iwj_verify_account'];
            $verify_code = $_GET['verify_code'];
            if ($user_id && $verify_code) {
                $_verify_code = get_user_meta($user_id, IWJ_PREFIX . 'verify_code', true);
                if ($verify_code === $_verify_code) {
                    delete_user_meta($user_id, IWJ_PREFIX . 'verify_code');
                    if (!is_user_logged_in()) {
                        wp_clear_auth_cookie();
                        wp_set_current_user($user_id);
                        wp_set_auth_cookie($user_id);
                    }

                    wp_redirect(iwj_get_page_permalink('verify_account'));
                    exit;
                }
            }
        }
    }

    static function received_payment() {
        if (isset($_GET['iwj_payment']) && $_GET['iwj_payment']) {

            $payment = IWJ()->payment_gateways->get_payment_gateway($_GET['iwj_payment']);
            if ($payment) {
                $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
                $key = isset($_GET['key']) ? $_GET['key'] : '';
                $tab = isset($_GET['tab']) ? $_GET['tab'] : '';
                $order = IWJ_Order::get_order($order_id);
                if ($order && $order->get_key() == $key) {
                    $payment->payment_recieved($order, $tab);
                } else {
                    echo 'Invalid Order';
                }
                exit;
            }
        }
    }

    static function post_views() {
        if (is_blog_admin()) {
            return false;
        }

        if (is_user_logged_in() || !defined('WP_CACHE') || !WP_CACHE) {
            global $user_ID, $post;
            if (is_single() && $post) {
                if (iwj_set_view_post($post->ID, $user_ID)) {
                    echo "<script type='text/javascript'>
                     var IWJsetCookie = function(cname, cvalue, exdays) {
                            var d = new Date();
                            d.setTime(d.getTime() + (exdays*24*60*60*1000));
                            var expires = \"expires=\"+ d.toUTCString();
                            document.cookie = cname + \"=\" + cvalue + \";\" + expires + \";path=" . SITECOOKIEPATH . "\";
                     };
                    IWJsetCookie('iwj_view_post_{$post->ID}', 1, 1);
                </script>";
                }
            }
        } else {
            if (is_single()) {
                global $user_ID, $post;
                if (is_single() && $post && in_array($post->post_type, array(
                            'iwj_job',
                            'iwj_candidate',
                            'iwj_employer'
                        )) && $post->post_status == 'publish') {
                    echo "<script type='text/javascript'>
                        var IWJsetCookie = function(cname, cvalue, exdays) {
                            var d = new Date();
                            d.setTime(d.getTime() + (exdays*24*60*60*1000));
                            var expires = \"expires=\"+ d.toUTCString();
                            document.cookie = cname + \"=\" + cvalue + \";\" + expires + \";path=" . SITECOOKIEPATH . "\";
                        };
                        jQuery(document).ready(function($) {
                            var data = 'action=iwj_view_post&post_id=" . $post->ID . "';
                            $.ajax({
                                url       : '" . admin_url('admin-ajax.php') . "',
                                type      : 'POST',
                                data      : data,
                                success   : function (result) {
                                    if (result && result == 1) {
                                        IWJsetCookie('iwj_view_post_{$post->ID}', 1, 1);
                                    }
                                }
                            });
                        }); 
                    </script>";
                }
            }
        }
    }

    static function set_view_post() {
        $post_id = sanitize_text_field($_POST['post_id']);
        if (true || iwj_set_view_post($post_id)) {
            echo 1;
        }

        echo false;

        exit;
    }

    static function send_mail_queue() {

        //IWJ_Email_Queue::send_emails();
    }

    static function confirm_job_alert() {
        if (isset($_GET['confirm_job_alert']) && $_GET['confirm_job_alert']) {
            $alert_id = sanitize_text_field($_GET['alert-id']);
            $code = sanitize_text_field($_GET['code']);
            $alert = IWJ_Alert::get_alert($alert_id);
            if ($alert && !$alert->get_status() && $alert->check_confirm_link($code) === true) {
                $alert->change_status(1);
                iwj_get_template_part('job-alert-popup/confirmed');
                ?>
                <script type="text/javascript">
                    jQuery(window).on('load', function () {
                        jQuery('#iwj-job-alert-confirmed-popup').modal('show');
                    });
                </script>
                <?php
            }
        }
    }

    static function unsubscribe_job_alert() {
        if (isset($_GET['unsubscribe_job_alert']) && $_GET['unsubscribe_job_alert']) {
            $alert_id = sanitize_text_field($_GET['alert-id']);
            $code = sanitize_text_field($_GET['code']);
            $alert = IWJ_Alert::get_alert($alert_id);
            if ($alert && $alert->check_unsubscribe_link($code) === true) {
                IWJ_Alert::delete($alert_id);
                iwj_get_template_part('job-alert-popup/unsubscribed');
                ?>
                <script type="text/javascript">
                    jQuery(window).on('load', function () {
                        jQuery('#iwj-job-alert-unsubscribed-popup').modal('show');
                    });
                </script>
                <?php
            }
        }
    }

    static function form_action() {
        if (isset($_POST) && $_POST) {
            if (isset($_POST['iwj-action']) && wp_verify_nonce($_POST['iwj-action'], 'iwj-select-plan')) {
                $is_new_job = isset($_POST['is_new_job']) ? $_POST['is_new_job'] : '';
                $is_upgrade = isset($_POST['is_upgrade_package']) ? $_POST['is_upgrade_package'] : '';
                $plan_id = isset($_POST['plan_id']) ? (int) $_POST['plan_id'] : 0;
                $package = IWJ_Plan::get_package($plan_id);
                $dashboard_url = iwj_get_page_permalink('dashboard');
                if ($package && $package->can_buy()) {
                    $user = IWJ_User::get_user();
                    if ($is_new_job) {

                        $url = add_query_arg(array('iwj_tab' => 'new-job', 'plan-id' => $plan_id), $dashboard_url);
                        wp_redirect($url);
                        exit;
                    } else {
                        if ($user->has_plan()) {
                            $dashboard_url = iwj_get_page_permalink('dashboard');
                            if ($package->is_free()) {
                                $old_package = $user->get_plan();
                                if ($old_package->is_free()) {
                                    $user->set_plan($plan_id);
                                    $_SESSION['iwj_message'] = __('Congratulation, you have just renewed your package successfully!', 'iwjroperty');
                                    $url = add_query_arg(array('iwj_tab' => 'current-plan'), $dashboard_url);
                                    wp_redirect($url);
                                    exit;
                                } else {
                                    $url = add_query_arg(array('iwj_tab' => 'change-plan', 'package-id' => $plan_id), $dashboard_url);
                                    wp_redirect($url);
                                    exit;
                                }
                            } else {
                                if ((float) $package->get_price() === 0) {
                                    $user->set_plan($plan_id);
                                    $url = add_query_arg(array('iwj_tab' => 'current-plan', 'msg' => '1'), $dashboard_url);
                                    wp_redirect($url);
                                    exit;
                                } else {
                                    if (iwj_woocommerce_checkout()) {
                                        $iwj_woocommerce = new IWJ_Woocommerce();
                                        $iwj_woocommerce->add_to_cart('plan', '', $plan_id);
                                        global $woocommerce;
                                        $checkout_url = $woocommerce->cart->get_checkout_url();
                                        wp_redirect($checkout_url);
                                        exit;
                                    } else {
                                        $cart = new IWJ_Cart();
                                        $cart->set('plan', $plan_id, $package->get_price());
                                        $url = add_query_arg(array('iwj_tab' => 'checkout'), $dashboard_url);
                                        wp_redirect($url);
                                        exit;
                                    }
                                }
                            }
                        } else {
                            if ($package->is_free() || (float) $package->get_price() === 0) {
                                $user->set_plan($plan_id);
                                $_SESSION['iwj_message'] = __('Congratulation, you have got a package successfully!', 'iwjroperty');
                                $url = add_query_arg(array('iwj_tab' => 'current-plan'), $dashboard_url);
                                wp_redirect($url);
                                exit;
                            } else {
                                if (iwj_woocommerce_checkout()) {
                                    $iwj_woocommerce = new IWJ_Woocommerce();
                                    $iwj_woocommerce->add_to_cart('plan', '', $plan_id);
                                    global $woocommerce;
                                    $checkout_url = $woocommerce->cart->get_checkout_url();
                                    wp_redirect($checkout_url);
                                    exit;
                                } else {
                                    $cart = new IWJ_Cart();
                                    $cart->set('plan', $plan_id, $package->get_price());
                                    wp_redirect(add_query_arg(array('iwj_tab' => 'checkout'), $dashboard_url));
                                    exit;
                                }
                            }
                        }
                    }
                } elseif ($is_upgrade) {
                    wp_redirect(add_query_arg(array('iwj_tab' => 'upgrade-plan'), $dashboard_url));
                    exit;
                } else {
                    wp_redirect(add_query_arg(array('iwj_tab' => 'select-plan'), $dashboard_url));
                    exit;
                }
            }

            if (isset($_POST['iwj-action']) && wp_verify_nonce($_POST['iwj-action'], 'iwj-checkout')) {
                $cart = new IWJ_Cart();
                if (!$cart->is_empty()) {
                    $order = $order_id = '';
                    $dashboard_url = iwj_get_page_permalink('dashboard');
                    if ($cart->get('order_id')) {
                        $order_id = $cart->get('order_id');
                        $order = IWJ_Order::get_order($order_id);
                        if ($order) {
                            $order->update($cart);
                            $order = IWJ_Order::get_order($order_id, true);
                        }
                    }

                    if (!$order) {
                        $order_id = IWJ_Order::create_new($cart);
                        $order = IWJ_Order::get_order($order_id);
                    }

                    if ($order->get_price() <= 0) {
                        $order->completed_order('', false, false);
                        update_post_meta($order->get_id(), IWJ_PREFIX . 'payment_method_title', 'Auto', 'iwjroperty');
                        wp_redirect(add_query_arg(array('iwj_tab' => 'thankyou', 'order-id' => $order_id, 'key' => $order->get_key()), $dashboard_url));
                        exit;
                    } else {
                        $payment_method = $_POST['payment_method'];
                        $payment_gateway = IWJ()->payment_gateways->get_payment_gateway($_POST['payment_method']);
                        if ($payment_gateway) {
                            update_post_meta($order_id, IWJ_PREFIX . 'payment_method', $payment_method);
                            update_post_meta($order_id, IWJ_PREFIX . 'payment_method_title', $payment_gateway->get_title());

                            $payment_gateway->process_payment($order_id, '');
                        } else {
                            wp_redirect(add_query_arg(array('iwj_tab' => 'checkout'), $dashboard_url));
                            exit;
                        }
                        exit;
                    }
                }
            }

            if (isset($_POST['iwj-submit-job']) && wp_verify_nonce($_POST['iwj-submit-job'], 'iwj-select-package') && iwj_option('submit_job_mode') == '1') {
                $job_id = $_POST['job_id'];
                $job = IWJ_Job::get_job($job_id);
                if (!$job) {
                    update_option('_iwj_front_messsage', iwj_get_alert(__('Invalid Job.', 'iwjob'), 'error'));
                    $dashboard_url = iwj_get_page_permalink('dashboard');
                    $url = add_query_arg(array('iwj_tab' => 'jobs'), $dashboard_url);
                    wp_redirect($url);
                    exit;
                }

                //save job
                if (isset($_POST['package']) && $_POST['package']) {
                    $package_id = $_POST['package'];
                    $package = IWJ_Package::get_package($package_id);
                    if ($package->can_buy()) {
                        if (!$package->is_free() && iwj_option('woocommerce_checkout')) {
                            $iwj_woocommerce = new IWJ_Woocommerce();
                            $iwj_woocommerce->add_to_cart('package', $job_id, $package_id);
                        } else {
                            $user_package_id = IWJ_User_Package::add_new(array(
                                        'title' => $package->get_title(true),
                                        'package_id' => $package->get_id(),
                                        'user_id' => get_current_user_id(),
                                        'pre_use' => 1,
                            ));

                            update_post_meta($job_id, IWJ_PREFIX . 'user_package_id', $user_package_id);
                            $order_id = IWJ_Order::add_new(
                                            array(
                                                'type' => '1',
                                                'package_id' => $package_id,
                                                'user_package_id' => $user_package_id,
                                                'job_id' => $job_id,
                                                'package_price' => $package->get_price(),
                                            )
                            );

                            $job->change_status('iwj-pending-payment', false);

                            update_post_meta($user_package_id, IWJ_PREFIX . 'order_id', $order_id);

                            $order = IWJ_Order::get_order($order_id);
                            $dashboard_url = iwj_get_page_permalink('dashboard');
                            if ($order->get_price() <= 0) {
                                $order->completed_order(__('Auto Completed', 'iwjob'), false);
                                /* if ( iwj_option( 'new_free_job_auto_approved' ) ) { //fixed free packages error
                                  $remain_job = get_post_meta( $user_package_id, IWJ_PREFIX . 'remain_job', true );
                                  update_post_meta( $user_package_id, IWJ_PREFIX . 'remain_job', $remain_job - 1 );
                                  } */
                                update_post_meta($order->get_id(), IWJ_PREFIX . 'payment_method_title', 'Auto');
                                $url = add_query_arg(array(
                                    'iwj_tab' => 'new-job',
                                    'step' => 'done',
                                    'job-id' => $job_id
                                        ), $dashboard_url);
                                wp_redirect($url);
                                exit;
                            } else {
                                $payment_method = $_POST['payment_method'];
                                $payment_gateway = IWJ()->payment_gateways->get_payment_gateway($_POST['payment_method']);
                                if ($payment_gateway) {
                                    update_post_meta($order_id, IWJ_PREFIX . 'payment_method', $payment_method);
                                    update_post_meta($order_id, IWJ_PREFIX . 'payment_method_title', $payment_gateway->get_title());
                                    $payment_gateway->process_payment($order_id, 'new-job');
                                }
                                exit;
                            }
                        }
                    }
                } elseif (isset($_POST['user_package']) && $_POST['user_package']) {
                    $user_package = IWJ_User_Package::get_user_package($_POST['user_package']);
                    if ($user_package->can_submit()) {

                        update_post_meta($job_id, IWJ_PREFIX . 'user_package_id', $_POST['user_package']);
                        update_post_meta($job_id, IWJ_PREFIX . 'is_new_publish', '1');

                        //decrement remain job of package
                        $remain_job= $user_package->get_remain_job();
                        if ($remain_job && $remain_job != -1) {
                            update_post_meta($_POST['user_package'], IWJ_PREFIX . 'remain_job', $remain_job - 1);
                        }

                        $package = $user_package->get_package();
                        if ($package && $package->is_free()) {
                            update_post_meta($job_id, IWJ_PREFIX . 'free_job', '1');
                        }

                        if (!$package || $package->is_free()) {
                            if (iwj_option('new_free_job_auto_approved')) {
                                $job->change_status('publish', false);
                            } else {
                                $job->change_status('pending');
                            }
                        } else {
                            if (iwj_option('new_job_auto_approved')) {
                                $job->change_status('publish', false);
                            } else {
                                $job->change_status('pending');
                            }
                        }

                        $dashboard_url = iwj_get_page_permalink('dashboard');
                        $url = add_query_arg(array(
                            'iwj_tab' => 'new-job',
                            'step' => 'done',
                            'job-id' => $job_id
                                ), $dashboard_url);
                        wp_redirect($url);
                    }
                }

                exit;
            } elseif (isset($_POST['iwj-submit-job']) && wp_verify_nonce($_POST['iwj-submit-job'], 'iwj-payment-job') && iwj_option('submit_job_mode') == '2') {
                $job_id = $_POST['job_id'];
                $job = IWJ_Job::get_job($job_id);
                if (!$job) {
                    update_option('_iwj_front_messsage', iwj_get_alert(__('Invalid Job.', 'iwjob'), 'error'));
                    $dashboard_url = iwj_get_page_permalink('dashboard');
                    $url = add_query_arg(array('iwj_tab' => 'jobs'), $dashboard_url);
                    wp_redirect($url);
                    exit;
                }

                if (iwj_woocommerce_checkout() && iwj_option('job_price') > 0) {
                    $iwj_woocommerce = new IWJ_Woocommerce();
                    $iwj_woocommerce->add_to_cart('newjob', $job_id);
                } else {
                    $order_id = IWJ_Order::add_new(
                                    array(
                                        'type' => '5',
                                        'job_id' => $job_id,
                                    )
                    );

                    $job->change_status('iwj-pending-payment', false);

                    $dashboard_url = iwj_get_page_permalink('dashboard');
                    $order = IWJ_Order::get_order($order_id);
                    if ($order->get_price() <= 0) {
                        $order->completed_order(__('Auto Completed', 'iwjob'), false);
                        update_post_meta($order->get_id(), IWJ_PREFIX . 'payment_method_title', 'Auto');
                        $url = add_query_arg(array(
                            'iwj_tab' => 'new-job',
                            'step' => 'done',
                            'job-id' => $job_id
                                ), $dashboard_url);
                        wp_redirect($url);
                        exit;
                    } else {
                        $payment_method = $_POST['payment_method'];
                        $payment_gateway = IWJ()->payment_gateways->get_payment_gateway($_POST['payment_method']);
                        if ($payment_gateway) {
                            update_post_meta($order_id, IWJ_PREFIX . 'payment_method', $payment_method);
                            update_post_meta($order_id, IWJ_PREFIX . 'payment_method_title', $payment_gateway->get_title());
                            $payment_gateway->process_payment($order_id, 'new-job');
                        }
                        exit;
                    }
                }
            } elseif (isset($_POST['iwj-security']) && wp_verify_nonce($_POST['iwj-security'], 'iwj-renew-job')) {

                $job_id = $_POST['id'];
                $dashboard_url = iwj_get_page_permalink('dashboard');
                $jobs_url = add_query_arg(array('iwj_tab' => 'jobs'), $dashboard_url);
                $job = IWJ_Job::get_job($job_id);
                if ($job) {
                    $user_package = $job->get_user_package();
                    if ($user_package && $user_package->get_remain_renew_job() > 0) {
                        update_post_meta($user_package->get_id(), IWJ_PREFIX . 'remain_renew_job', $user_package->get_remain_renew_job() - 1);
                        $job->set_publish(true);

                        $return_url = add_query_arg(array(
                            'iwj_tab' => 'renew-job',
                            'step' => 'done',
                            'job-id' => $job_id
                                ), $dashboard_url);
                        wp_redirect($return_url);
                        exit;
                    } else {
                        if (iwj_woocommerce_checkout() && iwj_option('renew_job_price') > 0) {
                            $iwj_woocommerce = new IWJ_Woocommerce();
                            $iwj_woocommerce->add_to_cart('renewjob', $job_id);
                        } else {
                            $order_id = IWJ_Order::add_new(
                                            array(
                                                'type' => '3',
                                                'job_id' => $job_id,
                                                'job_price' => iwj_option('renew_job_price'),
                                            )
                            );

                            $order = IWJ_Order::get_order($order_id);
                            if ($order->get_price() <= 0) {
                                $order->completed_order(__('Auto Completed', 'iwjob'), false);
                                update_post_meta($order->get_id(), IWJ_PREFIX . 'payment_method_title', 'Auto');
                                $dashboard_url = iwj_get_page_permalink('dashboard');
                                $url = add_query_arg(array(
                                    'iwj_tab' => 'renew-job',
                                    'step' => 'done',
                                    'order_id' => $order_id
                                        ), $dashboard_url);
                                wp_redirect($url);
                            } else {
                                //payment
                                $payment_method = $_POST['payment_method'];
                                $payment_gateway = IWJ()->payment_gateways->get_payment_gateway($_POST['payment_method']);
                                if ($payment_gateway) {
                                    update_post_meta($order_id, IWJ_PREFIX . 'payment_method', $payment_method);
                                    update_post_meta($order_id, IWJ_PREFIX . 'payment_method_title', $payment_gateway->get_title());
                                    $payment_gateway->process_payment($order_id, 'renew-job');
                                }
                            }
                        }
                        exit;
                    }
                } else {
                    update_option('_iwj_front_messsage', iwj_get_alert(__('There was an error processing please try again.', 'iwjob'), 'error'));
                    wp_redirect($jobs_url);
                    exit;
                }

                exit;
            } elseif (isset($_POST['iwj-security']) && wp_verify_nonce($_POST['iwj-security'], 'iwj-new-package')) {

                $dashboard_url = iwj_get_page_permalink('dashboard');

                if (isset($_POST['package']) && $_POST['package']) {
                    $package_id = $_POST['package'];
                    $package = IWJ_Package::get_package($package_id);
                    if ($package->can_buy()) {
                        if( !$package->is_free() && iwj_woocommerce_checkout() ){
                            $iwj_woocommerce = new IWJ_Woocommerce();
                            $iwj_woocommerce->add_to_cart('package', null, $package_id);
                        } else {
                            $user_package_id = IWJ_User_Package::add_new(array(
                                        'title' => $package->get_title(true),
                                        'package_id' => $package->get_id(),
                                        'user_id' => get_current_user_id(),
                            ));

                            $order_id = IWJ_Order::add_new(array(
                                        'type' => '1',
                                        'package_id' => $package_id,
                                        'package_price' => $package->get_price(),
                                        'user_package_id' => $user_package_id
                                            )
                            );

                            update_post_meta($user_package_id, IWJ_PREFIX . 'order_id', $order_id);

                            $order = IWJ_Order::get_order($order_id);
                            if ($order->get_price() <= 0) {
                                update_post_meta($order->get_id(), IWJ_PREFIX . 'payment_method_title', 'Auto');
                                $order->completed_order(__('Auto Completed', 'iwjob'), false);
                                $dashboard_url = iwj_get_page_permalink('dashboard');
                                $url = add_query_arg(array(
                                    'iwj_tab' => 'new-package',
                                    'step' => 'done',
                                    'order_id' => $order_id,
                                    'user_package_id' => $user_package_id
                                        ), $dashboard_url);
                                wp_redirect($url);
                            } else {
                                $payment_method = $_POST['payment_method'];
                                $payment_gateway = IWJ()->payment_gateways->get_payment_gateway($_POST['payment_method']);
                                if ($payment_gateway) {
                                    update_post_meta($order_id, IWJ_PREFIX . 'payment_method', $payment_method);
                                    update_post_meta($order->get_id(), IWJ_PREFIX . 'payment_method_title', $payment_gateway->get_title());
                                    $payment_gateway->process_payment($order_id, 'new-package');
                                }
                            }

                            exit;
                        }
                    }
                }

                wp_redirect($dashboard_url);
                exit;
            } elseif (isset($_POST['iwj-security']) && wp_verify_nonce($_POST['iwj-security'], 'iwj-new-resume-package')) {
                $dashboard_url = iwj_get_page_permalink('dashboard');

                if (isset($_POST['package']) && $_POST['package']) {
                    $package_id = $_POST['package'];
                    $package = IWJ_Resume_Package::get_package($package_id);
                    if ($package->can_buy()) {
                        if (iwj_woocommerce_checkout() && $package->get_price() > 0) {
                            $iwj_woocommerce = new IWJ_Woocommerce();
                            $iwj_woocommerce->add_to_cart('resumepackage', '', $package_id);
                        } else {
                            $user_package_id = IWJ_User_Package::add_new(array(
                                        'title' => $package->get_title(true),
                                        'package_id' => $package->get_id(),
                                        'user_id' => get_current_user_id(),
                                            ), 'resum_package');

                            $order_id = IWJ_Order::add_new(array(
                                        'type' => '4',
                                        'package_id' => $package_id,
                                        'package_price' => $package->get_price(),
                                        'user_package_id' => $user_package_id
                                            )
                            );
                            $order = IWJ_Order::get_order($order_id);
                            update_post_meta($user_package_id, IWJ_PREFIX . 'order_id', $order_id);

                            if ($order->get_price() <= 0) {
                                update_post_meta($order->get_id(), IWJ_PREFIX . 'payment_method_title', 'Auto');
                                $order->completed_order(__('Auto Completed', 'iwjob'), false);
                                $dashboard_url = iwj_get_page_permalink('dashboard');
                                $url = add_query_arg(array(
                                    'iwj_tab' => 'new-resume-package',
                                    'step' => 'done',
                                    'order_id' => $order_id
                                        ), $dashboard_url);
                                wp_redirect($url);
                            } else {
                                $payment_method = $_POST['payment_method'];
                                $payment_gateway = IWJ()->payment_gateways->get_payment_gateway($_POST['payment_method']);
                                if ($payment_gateway) {
                                    update_post_meta($order_id, IWJ_PREFIX . 'payment_method', $payment_method);
                                    update_post_meta($order->get_id(), IWJ_PREFIX . 'payment_method_title', $payment_gateway->get_title());
                                    $payment_gateway->process_payment($order_id, 'new-resume-package');
                                }
                            }
                        }
                    }
                }
                wp_redirect($dashboard_url);
                exit;
            } elseif (isset($_POST['iwj-security']) && wp_verify_nonce($_POST['iwj-security'], 'iwj-make-featured')) {
                $job_id = $_POST['id'];
                $dashboard_url = iwj_get_page_permalink('dashboard');
                $jobs_url = add_query_arg(array('iwj_tab' => 'jobs'), $dashboard_url);
                $job = IWJ_Job::get_job($job_id);
                if ($job) {
                    $user_package = $job->get_user_package();
                    if ($user_package && $user_package->can_make_featured()) {
                        if ($user_package->get_remain_featured_job() > 0) {
                            update_post_meta($user_package->get_id(), IWJ_PREFIX . 'remain_featured_job', $user_package->get_remain_featured_job() - 1);
                        }

                        if ($job->has_status('publish')) {
                            $job->set_featured(true);
                        } else {
                            update_post_meta($job_id, IWJ_PREFIX . 'is_new_featured', 1);
                        }

                        $return_url = add_query_arg(array(
                            'iwj_tab' => 'make-featured',
                            'step' => 'done',
                            'job-id' => $job_id
                                ), $dashboard_url);
                        wp_redirect($return_url);
                        exit;
                    } else {
                        if (iwj_woocommerce_checkout() && iwj_option('featured_job_price') > 0) {
                            $iwj_woocommerce = new IWJ_Woocommerce();
                            $iwj_woocommerce->add_to_cart('featuredjob', $job_id);
                        } else {
                            $order_id = IWJ_Order::add_new(
                                            array(
                                                'type' => '2',
                                                'job_id' => $job_id,
                                                'featured_price' => iwj_option('featured_job_price'),
                                            )
                            );

                            $order = IWJ_Order::get_order($order_id);
                            if ($order->get_price() <= 0) {
                                $order->completed_order(__('Auto Completed', 'iwjob'), false);
                                update_post_meta($order->get_id(), IWJ_PREFIX . 'payment_method_title', 'Auto');
                                $dashboard_url = iwj_get_page_permalink('dashboard');
                                $url = add_query_arg(array(
                                    'iwj_tab' => 'make-featured',
                                    'step' => 'done',
                                    'order_id' => $order_id
                                        ), $dashboard_url);
                                wp_redirect($url);
                            } else {
                                //payment
                                $payment_method = $_POST['payment_method'];
                                $payment_gateway = IWJ()->payment_gateways->get_payment_gateway($_POST['payment_method']);
                                if ($payment_gateway) {
                                    update_post_meta($order_id, IWJ_PREFIX . 'payment_method', $payment_method);
                                    update_post_meta($order_id, IWJ_PREFIX . 'payment_method_title', $payment_gateway->get_title());
                                    $payment_gateway->process_payment($order_id, 'make-featured');
                                }
                            }
                        }

                        exit;
                    }
                } else {
                    update_option('_iwj_front_messsage', iwj_get_alert(__('There was an error processing please try again.', 'iwjob'), 'error'));
                    wp_redirect($jobs_url);
                    exit;
                }
            } elseif (isset($_POST['iwj-security']) && wp_verify_nonce($_POST['iwj-security'], 'iwj-pay-order')) {
                $dashboard_url = iwj_get_page_permalink('dashboard');
                if (isset($_POST['order_id']) && $_POST['order_id'] && $_POST['order_key']) {
                    $order_id = sanitize_text_field($_POST['order_id']);
                    $order_key = sanitize_text_field($_POST['order_key']);
                    $order = IWJ_Order::get_order($order_id);
                    if ($order && $order->can_pay($order_key)) {
                        if ($order->get_price() <= 0) {
                            $order->completed_order(__('Auto Completed', 'iwjob'), false);
                            update_post_meta($order->get_id(), IWJ_PREFIX . 'payment_method_title', 'Auto');
                            $dashboard_url = iwj_get_page_permalink('dashboard');
                            $url = add_query_arg(array(
                                'iwj_tab' => 'pay-order',
                                'step' => 'done',
                                'order_id' => $order->get_id()
                                    ), $dashboard_url);
                            wp_redirect($url);
                        } else {
                            $payment_method = $_POST['payment_method'];
                            $payment_gateway = IWJ()->payment_gateways->get_payment_gateway($_POST['payment_method']);
                            if ($payment_gateway) {
                                update_post_meta($order->get_id(), IWJ_PREFIX . 'payment_method', $payment_method);
                                update_post_meta($order->get_id(), IWJ_PREFIX . 'payment_method_title', $payment_gateway->get_title());
                                $payment_gateway->process_payment($order->get_id(), 'pay-order');
                            }
                        }
                    }
                }
                wp_redirect($dashboard_url);
                exit;
            } elseif (isset($_POST['iwj-security']) && wp_verify_nonce($_POST['iwj-security'], 'iwj-new-apply-job-package')) {
                $dashboard_url = iwj_get_page_permalink('dashboard');

                if (isset($_POST['package']) && $_POST['package']) {
                    $package_id = $_POST['package'];
                    $package = IWJ_Apply_Job_Package::get_package($package_id);
                    if ($package->can_buy()) {
                        if (iwj_woocommerce_checkout() && $package->get_price() > 0) {
                            $iwj_woocommerce = new IWJ_Woocommerce();
                            $iwj_woocommerce->add_to_cart('applyjob_package', '', $package_id);
                        } else {
                            $user_package_id = IWJ_User_Package::add_new(array(
                                        'title' => $package->get_title(true),
                                        'package_id' => $package->get_id(),
                                        'user_id' => get_current_user_id(),
                                            ), 'apply_job_package');

                            $order_id = IWJ_Order::add_new(array(
                                        'type' => '6',
                                        'package_id' => $package_id,
                                        'package_price' => $package->get_price(),
                                        'user_package_id' => $user_package_id
                                            )
                            );
                            $order = IWJ_Order::get_order($order_id);
                            update_post_meta($user_package_id, IWJ_PREFIX . 'order_id', $order_id);

                            if ($order->get_price() <= 0) {
                                update_post_meta($order->get_id(), IWJ_PREFIX . 'payment_method_title', 'Auto');
                                $order->completed_order(__('Auto Completed', 'iwjob'), false);
                                $dashboard_url = iwj_get_page_permalink('dashboard');
                                $url = add_query_arg(array(
                                    'iwj_tab' => 'new-apply-job-package',
                                    'step' => 'done',
                                    'order_id' => $order_id
                                        ), $dashboard_url);
                                wp_redirect($url);
                            } else {
                                $payment_method = $_POST['payment_method'];
                                $payment_gateway = IWJ()->payment_gateways->get_payment_gateway($_POST['payment_method']);
                                if ($payment_gateway) {
                                    update_post_meta($order_id, IWJ_PREFIX . 'payment_method', $payment_method);
                                    update_post_meta($order->get_id(), IWJ_PREFIX . 'payment_method_title', $payment_gateway->get_title());
                                    $payment_gateway->process_payment($order_id, 'new-apply-job-package');
                                }
                            }
                        }
                    }
                }
                wp_redirect($dashboard_url);
                exit;
            }
        }
    }

    static function reset_settings() {
        check_ajax_referer('iwj-security');
        IWJ_Install::create_options(true);
    }

    static function login() {

        if (!defined('WP_CACHE') || !WP_CACHE) {
            check_ajax_referer('iwj-security');
        }

        $captcha_respon = self::check_recaptcha();

        if ($captcha_respon !== true) {
            echo json_encode(array(
                'loggedin' => false,
                'message' => iwj_get_alert(sprintf(__('Wrong captcha %s.', 'iwjob'), $captcha_respon), 'danger')
            ));
            die();
        }

        $info = array();
        $info['user_login'] = sanitize_user($_POST['username'], true);
        $info['user_password'] = sanitize_text_field($_POST['password']);
        $info['remember'] = true;
        do_action('iwj_before_login', $info['user_login']);
        $user_signon = wp_signon($info, is_ssl());
        if (is_wp_error($user_signon)) {
            echo json_encode(array(
                'loggedin' => false,
                'message' => iwj_get_alert($user_signon->get_error_message(), 'danger')
            ));
        } else {

            wp_set_current_user($user_signon->ID, $user_signon->user_login);

            if (in_array('iwj_employer', $user_signon->roles)) {
                $login_redirect = get_permalink(iwj_option('employer_login_redirect'));
            } elseif (in_array('iwj_candidate', $user_signon->roles)) {
                $login_redirect = get_permalink(iwj_option('candidate_login_redirect'));
            }
            if (!$login_redirect) {
                $login_redirect = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : '';
            }

            $redirect_url = apply_filters('iwj_redirect_to', $login_redirect, $user_signon);
            $redirect_url = apply_filters('iwj_login_redirect_to', $redirect_url, $user_signon);

            if (!$redirect_url || $redirect_url == iwj_get_page_permalink('login') || $redirect_url == iwj_get_page_permalink('register')) {
                $redirect_url = iwj_get_page_permalink('dashboard');
            }

            $redirect_url = add_query_arg(array('loggedin' => 'true'), $redirect_url);

            if (iwj_option('verify_account')) {
                $user = IWJ_User::get_user();
                if (!$user->is_verified()) {
                    $redirect_url = iwj_get_page_permalink('verify_account');
                }
            }

            do_action('iwj_logged_in', $info);

            echo json_encode(array(
                'loggedin' => true,
                'message' => iwj_get_alert(__('Login successful, redirecting...', 'iwjob'), 'success'),
                'redirect_url' => $redirect_url
            ));
        }

        die();
    }

    static function logged_in_fallback() {
        if (isset($_POST['fallback_action']) && $_POST['fallback_action'] == 'show_apply_form') {
            $_SESSION['show_apply_form'] = true;
        }
    }

    static function show_apply_form() {
        if (isset($_SESSION['show_apply_form']) && $_SESSION['show_apply_form']) {
            $user = IWJ_User::get_user();
            $post = get_post();
            if ($post && $post->post_type == 'iwj_job' && $user->is_candidate()) {
                ?>
                <script type="text/javascript">
                    jQuery(window).on('load', function () {
                        jQuery('#iwj-modal-apply-<?php echo $post->ID; ?>').modal('show');
                    });
                </script>
                <?php
            }
            unset($_SESSION['show_apply_form']);
        }
    }

    static function register() {
        if (!defined('WP_CACHE') || !WP_CACHE) {
            check_ajax_referer('iwj-security');
        }
        $return = array();
        $user_name = sanitize_user($_POST['username'], true);
        $email = sanitize_email($_POST['email']);
        $company = sanitize_text_field($_POST['company']);
        $display_name = isset($_POST['display_name']) ? sanitize_text_field($_POST['display_name']) : '';
        $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';
        $profile_image_url = isset($_POST['profile_image_url']) ? sanitize_text_field($_POST['profile_image_url']) : '';
        $role = $_POST['role'];

        if (!$user_name) {
            $return['message'] = iwj_get_alert(__('Invalid username.', 'iwjob'), 'danger');
            $return['success'] = false;
            echo json_encode($return);
            wp_die();
        }

        $username = preg_replace('/[^a-zA-Z0-9.-_@]/', '', $user_name);
        if ($username !== $user_name) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Invalid username (do not use special characters or spaces)!', 'iwjob'), 'danger')
            ));
            wp_die();
        }

        if (strlen($user_name) < 5) {
            $return['message'] = iwj_get_alert(__('Full Name must be at least 5 characters.', 'iwjob'), 'danger');
            $return['success'] = false;
            echo json_encode($return);
            wp_die();
        }

        if (username_exists($user_name)) {
            $return['message'] = iwj_get_alert(__('Phone number already exists.', 'iwjob'), 'danger');
            $return['success'] = false;
            echo json_encode($return);
            wp_die();
        }

        if (!$email) {
            $return['message'] = iwj_get_alert(__('Invalid User Email.', 'iwjob'), 'danger');
            $return['success'] = false;
            echo json_encode($return);
            wp_die();
        }

        if ($password) {
            if (strlen($password) < 6) {
                $return['message'] = iwj_get_alert(__('Password must be at least 6 characters.', 'iwjob'), 'danger');
                $return['success'] = false;
                echo json_encode($return);
                wp_die();
            }
            $auto_generate_password = false;
        } else {
            $password = wp_generate_password();
            $auto_generate_password = true;
        }

        if (email_exists($email)) {
            $return['message'] = iwj_get_alert(__('User Email already exists.', 'iwjob'), 'danger');
            $return['success'] = false;
            echo json_encode($return);
            exit;
        }

        if ($role == 'employer') {
            global $wpdb;
            $id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type = 'iwj_employder'", $company));
            if ($id) {
                $return['message'] = iwj_get_alert(__('Phone number already exists.', 'iwjob'), 'danger');
                $return['success'] = false;
                echo json_encode($return);
                wp_die();
            }
        }

        $captcha_respon = self::check_recaptcha();
        if ($captcha_respon !== true) {
            echo json_encode(array(
                'loggedin' => false,
                'message' => iwj_get_alert(sprintf(__('Wrong captcha %s.', 'iwjob'), $captcha_respon), 'danger')
            ));
            die();
        }

        $user_data = array();
        $user_data['user_login'] = $user_name;
        $user_data['first_name'] = $user_name;
        $user_data['display_name'] = $display_name ? $display_name : $user_name;
        $user_data['user_pass'] = $password;
        $user_data['user_email'] = $email;
        $user_data['role'] = ( $role == 'employer' ? 'iwj_employer' : 'iwj_candidate' );
        $user_id = wp_insert_user($user_data);
        if (!is_wp_error($user_id)) {
            if ($profile_image_url) {
                update_user_meta($user_id, IWJ_PREFIX . 'avatar', $profile_image_url);
            }

            $verified = true;
            if (iwj_option('verify_account') && (!isset($_SESSION['iwj_verified_email']) || $_SESSION['iwj_verified_email'] !== $email )) {
                $code = wp_generate_password(20, false);
                update_user_meta($user_id, IWJ_PREFIX . 'verify_code', $code);
                $verified = false;
            }
            do_action('iwj_register_process', $user_id);

            IWJ_Email::send_email('register', array(
                'user_id' => $user_id,
                'password' => $user_data['user_pass'],
                'auto_generate_password' => $auto_generate_password
            ));
            IWJ_Email::send_email('admin_register', array(
                'user_id' => $user_id,
                'password' => $user_data['user_pass'],
                'auto_generate_password' => $auto_generate_password
            ));


            $info = array();
            $info['user_login'] = $user_name;
            $info['user_password'] = $password;
            $info['remember'] = true;
            $user_signon = wp_signon($info, is_ssl());

            if ($verified) {

                if (in_array('iwj_employer', $user_signon->roles)) {
                    $login_redirect = get_permalink(iwj_option('employer_login_redirect'));
                } elseif (in_array('iwj_candidate', $user_signon->roles)) {
                    $login_redirect = get_permalink(iwj_option('candidate_login_redirect'));
                }
                if (!$login_redirect) {
                    $login_redirect = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : '';
                }

                if (!$login_redirect || $login_redirect == iwj_get_page_permalink('login') || $login_redirect == iwj_get_page_permalink('register')) {
                    $login_redirect = iwj_get_page_permalink('dashboard');
                }

                $redirect_url = apply_filters('iwj_redirect_to', $login_redirect, $user_signon);
                $redirect_url = apply_filters('iwj_register_redirect_to', $redirect_url, $user_signon);
                $redirect_url = add_query_arg(array('registed' => 'true'), $redirect_url);
            } else {
                $redirect_url = iwj_get_page_permalink('verify_account');
            }

            echo json_encode(array(
                'success' => true,
                'message' => iwj_get_alert(__('Registered successfully, redirecting...', 'iwjob'), 'success'),
                'redirect_url' => $redirect_url
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Can\'t register please refresh the page and try again.', 'iwjob'), 'danger')
            ));
        }

        wp_die();
    }

    static function change_password() {
        check_ajax_referer('iwj-security');
        $return = array();
        $user = wp_get_current_user();
        $current_password = sanitize_text_field($_POST['current_password']);
        $new_password = sanitize_text_field($_POST['new_password']);

        if (IWJ_PREVIEW_MODE && ($user->user_login == 'employer' || $user->user_login == 'candidate')) {
            $return['message'] = iwj_get_alert(__('Sorry, you can not change the password for this account.', 'iwjob'), 'danger');
            $return['success'] = false;
            echo json_encode($return);
            exit;
        }

        if (!$current_password) {
            $return['message'] = iwj_get_alert(__('Please enter your current password.', 'iwjob'), 'danger');
            $return['success'] = false;
            echo json_encode($return);
            exit;
        }
        if (!$new_password) {
            $return['message'] = iwj_get_alert(__('Please enter new password.', 'iwjob'), 'danger');
            $return['success'] = false;
            echo json_encode($return);
            exit;
        }
        if (strlen($new_password) < 6) {
            $return['message'] = iwj_get_alert(__('Password must be at least 6 characters.', 'iwjob'), 'danger');
            $return['success'] = false;
            echo json_encode($return);
            exit;
        }

        if (!wp_check_password($current_password, $user->user_pass, $user->ID)) {
            $return['message'] = iwj_get_alert(__('Wrong current password.', 'iwjob'), 'danger');
            $return['success'] = false;
            echo json_encode($return);
            exit;
        }

        wp_update_user(array(
            'ID' => $user->ID,
            'user_pass' => $new_password
        ));

        $return['message'] = iwj_get_alert(__('Changed password successfully.', 'iwjob'), 'success');
        $return['success'] = false;
        $info = array();
        $info['user_login'] = $user->user_login;
        $info['user_password'] = $new_password;
        $info['remember'] = true;
        wp_signon($info, false);

        echo json_encode($return);
        exit;
    }

    static function lostpass() {
        check_ajax_referer('iwj-security');
        $user_login = sanitize_text_field($_POST['user_login']);
        if (!$user_login) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please enter Email Or Full Name.', 'iwjob'), 'danger')
            ));
            exit;
        }

        if (is_email($user_login)) {
            if (!email_exists($user_login)) {
                echo json_encode(array(
                    'success' => false,
                    'message' => iwj_get_alert(__('There is no user registered with that email address.', 'iwjob'), 'danger')
                ));
                exit;
            } else {
                $user = get_user_by('email', $user_login);
            }
        } else {
            if (!username_exists($user_login)) {
                echo json_encode(array(
                    'success' => false,
                    'message' => iwj_get_alert(__('Invalid Full Name.', 'iwjob'), 'danger')
                ));
                exit;
            } else {
                $user = get_user_by('login', $user_login);
            }
        }

        $user = IWJ_User::get_user($user);
        $user->generate_resetpass_code();
        IWJ_Email::send_email('resetpass', $user);
        echo json_encode(array(
            'success' => true,
            'message' => iwj_get_alert(__('Please Check your email for the confirmation link.', 'iwjob'), 'success')
        ));
        exit;
    }

    static function change_email() {
        check_ajax_referer('iwj-security');

        $email = sanitize_email($_POST['email']);

        if (!$email) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please enter your email.', 'iwjob'), 'danger')
            ));
            exit;
        }

        if (email_exists($email)) {
            $return['message'] = iwj_get_alert(__('This email has already been used by another user.', 'iwjob'), 'danger');
            $return['success'] = false;
            echo json_encode($return);
            exit;
        }

        $update_user_data = array(
            'ID' => get_current_user_id(),
            'user_email' => $email,
        );

        wp_update_user($update_user_data);
        clean_user_cache(get_current_user_id());

        $user = IWJ_User::get_user(null, true);

        IWJ_Email::send_email('verify_account', array('user' => $user));

        echo json_encode(array(
            'success' => true,
            'message' => iwj_get_alert(__('An email was sent to your email please click the activation link to verify your account!', 'iwjob'), 'success')
        ));
        exit;
    }

    static function delete_account() {
        check_ajax_referer('iwj-security');
        $user = wp_get_current_user();
        if (IWJ_PREVIEW_MODE && ($user->user_login == 'employer' || $user->user_login == 'candidate')) {
            $return['message'] = iwj_get_alert(__('Sorry you cannot delete this account.', 'iwjob'), 'danger');
            $return['success'] = false;
            echo json_encode($return);
            exit;
        }

        $user = IWJ_User::get_user();
        $move_to_user = '';
        if (iwj_option('keep_jobs_delete_user')) {
            $move_to_user = iwj_option('keep_jobs_user_id', 1);
        }

        if (wp_delete_user(get_current_user_id(), $move_to_user)) {

            IWJ_Email::send_email('delete_account', array('user' => $user));

            echo json_encode(array(
                'success' => true,
                'redirect' => add_query_arg(array('deleted_user' => 'true'), home_url('/')),
                'message' => iwj_get_alert(__('An email was sent to your email please click the activation link to verify your account!', 'iwjob'), 'success')
            ));
            exit;
        }

        echo json_encode(array(
            'success' => false,
            'message' => iwj_get_alert(__('Can not delete account please reload the page and try again.', 'iwjob'), 'success')
        ));
        exit;
    }

    static function resetpass() {
        check_ajax_referer('iwj-security');
        $password = sanitize_text_field($_POST['password']);
        $password_confirm = sanitize_text_field($_POST['password_confirm']);
        $user = sanitize_text_field($_POST['user']);
        $code = sanitize_text_field($_POST['code']);
        if (!IWJ_User::can_reset_password($user, $code)) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('You do not have permission to change the password.', 'iwjob'), 'danger')
            ));
            exit;
        }

        if ($password == '' || $password_confirm == '') {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please enter password.', 'iwjob'), 'danger')
            ));
            exit;
        }

        if (strlen($password) < 6) {
            $return['message'] = iwj_get_alert(__('Password length of at least 6 characters.', 'iwjob'), 'danger');
            $return['success'] = false;
            echo json_encode($return);
            exit;
        }

        if ($password !== $password_confirm) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Confirm password don\'t match.', 'iwjob'), 'danger')
            ));
            exit;
        }

        $user = get_user_by('login', $user);
        $update_user_data = array(
            'ID' => $user->ID,
            'user_pass' => $password
        );
        wp_update_user($update_user_data);

        $user = IWJ_User::get_user($user);
        $user->delete_resetpass_code();

        echo json_encode(array(
            'success' => true,
            'redirect_url' => iwj_get_page_permalink('login'),
            'message' => iwj_get_alert(__('You have successfully changed your password.', 'iwjob'), 'success')
        ));
        exit;
    }

    static function update_profile() {
        check_ajax_referer('iwj-security');
        $user = IWJ_User::get_user();
        if (current_user_can('create_iwj_jobs')) {
            $employer = $user->get_employer();
            if (!$employer) {
                $employer_id = IWJ_User::add_employer($user->get_id());
                $employer = IWJ_Employer::get_employer($employer_id);
            }

            if ($employer->can_update()) {
                $employer->update();
                if ($employer->is_incomplete()) {
                    if (current_user_can('administrator') || iwj_option('employer_auto_approved')) {
                        $employer->change_status('publish', false);
                    } else {
                        $employer->change_status('pending');
                    }
                }

                $employer = $user->get_employer(true);

                echo json_encode(array(
                    'success' => true,
                    'message' => $employer->is_pending() ? iwj_get_alert(__('Your profile is currently awaiting approval.', 'iwjob'), 'info') : iwj_get_alert(__('You have successfully updated your profile.', 'iwjob'), 'success')
                ));
                exit;
            } else {
                echo json_encode(array(
                    'success' => false,
                    'message' => __('You do not have permission.', 'iwjob')
                ));
                exit;
            }
        } elseif (current_user_can('apply_job')) {

            $candidate = $user->get_candidate();
            if (!$candidate) {
                $candidate_id = IWJ_User::add_candidate($user->get_id());
                $candidate = IWJ_Candidate::get_candidate($candidate_id);
            }

            if ($candidate->can_update()) {
                $candidate->update();
                if ($candidate->is_incomplete()) {
                    if (iwj_option('candidate_auto_approved')) {
                        $candidate->change_status('publish', false);
                    } else {
                        $candidate->change_status('pending');
                    }
                }

                $candidate = $user->get_candidate(true);

                echo json_encode(array(
                    'success' => true,
                    'message' => $candidate->is_pending() ? iwj_get_alert(__('Your profile is currently awaiting approval.', 'iwjob'), 'info') : iwj_get_alert(__('You have successfully updated your profile.', 'iwjob'), 'success')
                ));
                exit;
            } else {
                echo json_encode(array(
                    'success' => false,
                    'message' => __('You do not have permission.', 'iwjob')
                ));
                exit;
            }
        } else {
            $user->update_profile();
            echo json_encode(array(
                'success' => true,
                'message' => iwj_get_alert(__('You have successfully updated your profile.', 'iwjob'), 'success')
            ));
            exit;
        }
    }

    static function submit_job() {
        check_ajax_referer('iwj-security');
        $id = $_POST['id'];
        $job = '';

        if ($id) {
            $job = IWJ_Job::get_job($id);
        }

        if ($job) {
            if ($job->can_publish_draft()) {
                $job->update($_POST);
            } else {
                echo json_encode(array(
                    'sucess' => false,
                    'message' => iwj_get_alert(__('Sorry, You do not have permission save job.', 'iwjob'), 'danger'),
                    'redirect' => false,
                    'id' => $id,
                ));
                exit;
            }
        } else {
            $id = IWJ_Job::add_new(array(
                        'title' => sanitize_text_field($_POST['title']),
                        'content' => wp_kses_post($_POST['description']),
                        'extra_data' => $_POST,
            ));

            $job = IWJ_Job::get_job($id);
        }

        echo json_encode(array(
            'sucess' => true,
            'redirect' => defined('ICL_LANGUAGE_CODE') ? iwj_force_wpml_url($job->permalink()) : $job->permalink(),
            'id' => $id,
        ));

        exit;
    }

    static function renew_job() {
        check_ajax_referer('iwj-security');
        $id = $_POST['id'];
        $job = '';

        if ($id) {
            $job = IWJ_Job::get_job($id);
        }

        if ($job) {
            if ($job->can_renew()) {
                $_SESSION['renew-job-data'] = $_POST;

                $dashboard_url = iwj_get_page_permalink('dashboard');
                $url = add_query_arg(array(
                    'iwj_tab' => 'renew-job',
                    'step' => 'select-package',
                    'job-id' => $job->get_id()
                        ), $dashboard_url);

                echo json_encode(array(
                    'sucess' => true,
                    'redirect_url' => $url,
                ));
                exit;
            } else {
                echo json_encode(array(
                    'sucess' => false,
                    'message' => iwj_get_alert(__('You do not have permission to do it.', 'iwjob'), 'danger'),
                    'id' => $id,
                ));
                exit;
            }
        }

        echo json_encode(array(
            'sucess' => false,
            'message' => iwj_get_alert(__('The job does not exist.', 'iwjob'), 'danger'),
            'redirect' => false,
            'id' => $id,
        ));
        exit;
    }

    static function edit_job() {
        check_ajax_referer('iwj-security');
        $id = $_POST['id'];
        $job = '';
        if ($id) {
            $job = IWJ_Job::get_job($id);
        }

        if ($job) {
            $dashboard_url = iwj_get_page_permalink('dashboard');
            if ($job->can_edit()) {
                if ($job->has_status('publish')) {
                    $job_update = $job->get_update();
                    //update rejected to pending
                    if ($job_update) {
                        $job_update->update($_POST);
                        $job_update->change_status('pending');
                        $url = add_query_arg(array('iwj_tab' => 'jobs'), $dashboard_url);
                        echo json_encode(array(
                            'sucess' => true,
                            'redirect' => $url,
                            'delay' => 1000,
                            'message' => iwj_get_alert(__('Updated Successfully, this post is being admin review.', 'iwjob'), 'info'),
                        ));
                        exit;
                    } else {
                        $is_free = $job->is_free();
                        $auto_approved = $is_free ? iwj_option('edit_free_job_auto_approved') : iwj_option('edit_job_auto_approved');
                        $url = add_query_arg(array('iwj_tab' => 'jobs'), $dashboard_url);
                        if ($auto_approved) {
                            $job->update($_POST);
                            echo json_encode(array(
                                'sucess' => true,
                                'redirect' => $url,
                                'delay' => 1000,
                                'message' => iwj_get_alert(__('Updated Successfully.', 'iwjob'), 'info'),
                            ));
                            exit;
                        } else {
                            $new_job_id = iwj_clone_post($id);
                            $new_job = IWJ_Job::get_job($new_job_id);
                            $new_job->update($_POST);
                            IWJ_Email::send_email('review_job', $new_job);
                            $url = add_query_arg(array('iwj_tab' => 'jobs'), $dashboard_url);
                            echo json_encode(array(
                                'sucess' => true,
                                'redirect' => $url,
                                'delay' => 1000,
                                'message' => iwj_get_alert(__('An update has been sent to admin. We will browse it as soon as possible.', 'iwjob'), 'info'),
                            ));
                            exit;
                        }
                    }
                } else {
                    $job->update($_POST);
                    if ($job->has_status('rejected')) {
                        $job->change_status('pending');
                        $url = add_query_arg(array('iwj_tab' => 'jobs'), $dashboard_url);
                        echo json_encode(array(
                            'sucess' => true,
                            'redirect' => $url,
                            'delay' => 1000,
                            'message' => iwj_get_alert(__('Updated Successfully, this post is being admin review.', 'iwjob'), 'info'),
                        ));
                        exit;
                    }

                    $url = add_query_arg(array('iwj_tab' => 'jobs'), $dashboard_url);
                    echo json_encode(array(
                        'sucess' => true,
                        'redirect' => $url,
                        'delay' => 1000,
                        'message' => iwj_get_alert(__('Updated Successfully.', 'iwjob'), 'success'),
                    ));
                    exit;
                }
            } else {
                $url = add_query_arg(array('iwj_tab' => 'jobs'), $dashboard_url);
                echo json_encode(array(
                    'sucess' => false,
                    'message' => iwj_get_alert(__('You do not have permission to do it.', 'iwjob'), 'danger'),
                    'redirect' => $url,
                    'delay' => 1000,
                    'id' => $id,
                ));
                exit;
            }
        }

        echo json_encode(array(
            'sucess' => false,
            'message' => iwj_get_alert(__('The job does not exist.', 'iwjob'), 'danger'),
            'redirect' => false,
            'id' => $id,
        ));
        exit;
    }

    static function get_order_price() {
        check_ajax_referer('iwj-security');
        $package_id = isset($_POST['package_id']) ? $_POST['package_id'] : '';
        $user_package_id = isset($_POST['user_package_id']) ? $_POST['user_package_id'] : '';
        $package = $user_package = $total_price = '';
        if ($package_id) {
            $post = get_post($package_id);
            if ($post->post_type == 'iwj_package') {
                $package = IWJ_Package::get_package($post);
                $total_price = $package->get_price();
            } else {
                $package = IWJ_Resume_Package::get_package($post);
                $total_price = $package->get_price();
            }
        } else {
            $user_package = IWJ_User_Package::get_user_package($user_package_id);
        }

        ob_start();
        iwj_get_template_part('parts/order-price', array('user_package' => $user_package, 'package' => $package));
        $html = ob_get_clean();

        echo json_encode(array(
            'total_price' => $total_price,
            'html' => $html
        ));

        exit;
    }

    static function check_pay_order() {
        check_ajax_referer('iwj-security');
        $order_id = $_POST['order_id'];
        $order_key = $_POST['order_key'];
        $order = IWJ_Order::get_order($order_id);
        if ($order) {
            $can_pay = $order->can_pay($order_key);
            if ($can_pay === true) {
                if ($order->get_price() > 0 && !$_POST['payment_method']) {
                    echo json_encode(array(
                        'success' => false,
                        'message' => iwj_get_alert(__('Please select the payment method.', 'iwjob'), 'warning')
                    ));
                    exit;
                } else {
                    echo json_encode(array(
                        'success' => true,
                    ));
                    exit;
                }
            } else {
                echo json_encode(array(
                    'success' => false,
                    'message' => iwj_get_alert($can_pay->get_error_message(), 'danger')
                ));
                exit;
            }
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Invalid Order.', 'iwjob'), 'danger')
            ));
            exit;
        }
    }

    static function follow() {
        check_ajax_referer('iwj-security');
        $id = $_POST['id'];
        $user = IWJ_User::get_user();
        if (!$user) {
            echo json_encode(array(
                'success' => false,
                'message' => __('Please login to use this function.', 'iwjob')
            ));

            exit;
        }

        if (!current_user_can('apply_job')) {
            echo json_encode(array(
                'success' => false,
                'message' => __('Sorry, You do not have permission to follow company.', 'iwjob')
            ));

            exit;
        }

        $employer = IWJ_Employer::get_employer($id);
        if (!$employer) {
            echo json_encode(array(
                'success' => false,
                'message' => __('Invalid company.', 'iwjob')
            ));
            exit;
        }

        if ($user->follow($id)) {
            echo json_encode(array(
                'success' => true,
                'message' => __('<i class="ion-android-send"></i> Followed', 'iwjob')
            ));

            exit;
        }
    }

    static function unfollow() {
        check_ajax_referer('iwj-security');
        $id = $_POST['id'];
        $user = IWJ_User::get_user();
        if (!$user) {
            echo json_encode(array(
                'success' => false,
                'message' => __('Please login to use this function', 'iwjob')
            ));

            exit;
        }

        if (!current_user_can('apply_job')) {
            echo json_encode(array(
                'success' => false,
                'message' => __('Sorry, You do not have permission to follow company.', 'iwjob')
            ));

            exit;
        }

        if ($user->unfollow($id)) {
            echo json_encode(array(
                'success' => true,
                'message' => __('<i class="ion-android-send"></i> Follow Us', 'iwjob')
            ));

            exit;
        }
    }

    static function save_job() {
        check_ajax_referer('iwj-security');
        $id = $_POST['id'];
        $user = IWJ_User::get_user();
        if (!$user) {
            echo json_encode(array(
                'success' => false,
                'message' => __('Please login to use this function.', 'iwjob')
            ));

            exit;
        }

        if (!current_user_can('apply_job')) {
            echo json_encode(array(
                'success' => false,
                'message' => __('Sorry, You do not have permission to save job.', 'iwjob')
            ));

            exit;
        }

        if ($user->save_job($id)) {
            echo json_encode(array(
                'success' => true,
                'message' => __('SAVED', 'iwjob')
            ));

            exit;
        }
    }

    static function undo_save_job() {
        check_ajax_referer('iwj-security');
        $id = $_POST['id'];
        $user = IWJ_User::get_user();
        if (!$user) {
            echo json_encode(array(
                'success' => false,
                'message' => __('Please login to use this function.', 'iwjob')
            ));

            exit;
        }

        if (!current_user_can('apply_job')) {
            echo json_encode(array(
                'success' => false,
                'message' => __('Sorry, You do not have permission to save job.', 'iwjob')
            ));

            exit;
        }

        if ($user->undo_save_job($id)) {
            echo json_encode(array(
                'success' => true,
                'message' => __('SAVE', 'iwjob')
            ));

            exit;
        }
    }

    static function view_resum() {
        check_ajax_referer('iwj-security');
        $user = IWJ_User::get_user();

        if (!$user) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please login to use this function.', 'iwjob'), 'warning')
            ));

            exit;
        }

        if (!iwj_option('view_free_resum')) {
            if (!current_user_can('create_iwj_jobs')) {
                echo json_encode(array(
                    'success' => false,
                    'message' => iwj_get_alert(__('Sorry, You do not have permission to view resume.', 'iwjob'), 'danger')
                ));

                exit;
            }

            $user_package_id = sanitize_text_field($_POST['user_package']);
            $user_package = IWJ_User_Package::get_user_package($user_package_id);
            $resum_id = sanitize_text_field($_POST['resum_id']);
            if ($user->is_viewed_resum($resum_id)) {
                echo json_encode(array(
                    'success' => false,
                    'message' => iwj_get_alert(__('You have viewed resume.', 'iwjob'), 'danger')
                ));
                exit;
            }

            if (!$user_package->can_view_resum()) {
                echo json_encode(array(
                    'success' => false,
                    'message' => iwj_get_alert(__('Sorry, You do not have permission to view resume.', 'iwjob'), 'danger')
                ));

                exit;
            }
        }

        if ($user->view_resum($resum_id)) {
            update_post_meta($user_package->get_id(), IWJ_PREFIX . 'remain_resum', $user_package->get_remain_resum() - 1);
            echo json_encode(array(
                'success' => true,
            ));

            exit;
        }

        exit;
    }

    static function delete_view_resum() {
        check_ajax_referer('iwj-security');
        $id = $_POST['id'];
        $user = IWJ_User::get_user();
        if (!$user) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please login to use this function.', 'iwjob'), 'danger')
            ));

            exit;
        }

        if ($user->delete_view_resum($id)) {
            echo json_encode(array(
                'success' => true,
                'message' => iwj_get_alert(__('Deleted successfully.', 'iwjob'), 'success')
            ));

            exit;
        }
    }

    static function confirm_apply_job() {
        check_ajax_referer('iwj-security');
        $user = IWJ_User::get_user();

        if (!$user) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please login to use this function.', 'iwjob'), 'warning')
            ));

            exit;
        }

        ob_start();
        iwj_get_template_part('dashboard/');
        $step_content = ob_get_contents();
        ob_clean();

        echo json_encode(array(
            'success' => false,
            'content' => $step_content,
            'message' => iwj_get_alert(__('Please login to use this function.', 'iwjob'), 'danger')
        ));

        exit;
    }

    static function save_resum() {
        check_ajax_referer('iwj-security');
        $user = IWJ_User::get_user();

        if (!$user) {
            echo json_encode(array(
                'success' => false,
                'message' => __('Please login to use this function.', 'iwjob')
            ));

            exit;
        }

        $resum_id = sanitize_text_field($_POST['id']);
        if (!iwj_option('view_free_resum')) {
            if (!current_user_can('create_iwj_jobs')) {
                echo json_encode(array(
                    'success' => false,
                    'message' => __('Sorry, You do not have permission to save resume.', 'iwjob')
                ));

                exit;
            }
        }

        if ($user->save_resum($resum_id)) {
            echo json_encode(array(
                'success' => true,
                'message' => __('<i class="fa fa-heart"></i> <span>Saved resume</span>', 'iwjob')
            ));

            exit;
        }

        exit;
    }

    static function delete_save_resum() {
        check_ajax_referer('iwj-security');
        $id = $_POST['id'];
        $user = IWJ_User::get_user();
        if (!$user) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please login to use this function.', 'iwjob'), 'danger')
            ));

            exit;
        }

        if (!iwj_option('view_free_resum')) {
            if (!current_user_can('create_iwj_jobs') || !current_user_can('privilege_view_resum')) {
                echo json_encode(array(
                    'success' => false,
                    'message' => iwj_get_alert(__('Sorry, You do not have permission to view resume.', 'iwjob'), 'danger')
                ));

                exit;
            }
        }

        if ($user->delete_save_resum($id)) {
            echo json_encode(array(
                'success' => true,
                'message' => iwj_get_alert(__('Deleted successfully.', 'iwjob'), 'success')
            ));

            exit;
        }
    }

    static function undo_save_resum() {
        check_ajax_referer('iwj-security');
        $user = IWJ_User::get_user();

        if (!$user) {
            echo json_encode(array(
                'success' => false,
                'message' => __('Please login to use this function.', 'iwjob')
            ));

            exit;
        }

        if (!iwj_option('view_free_resum')) {
            if (!current_user_can('create_iwj_jobs')) {
                echo json_encode(array(
                    'success' => false,
                    'message' => __('Sorry, You do not have permission to save resume.', 'iwjob')
                ));

                exit;
            }
        }

        $resum_id = sanitize_text_field($_POST['id']);
        if ($user->undo_save_resum($resum_id)) {
            echo json_encode(array(
                'success' => true,
                'message' => __('<i class="fa fa-heart"></i> <span>Save resume</span>', 'iwjob')
            ));

            exit;
        }

        exit;
    }

    static function submit_alert() {
        check_ajax_referer('iwj-security');
        //if true: sign up at fontend with popup, false: sign up in dashboard
        $is_popup = sanitize_text_field($_POST['is_popup']);
        $position = sanitize_text_field($_POST['position']);
        $salary_from = sanitize_text_field($_POST['salary_from']);
        $frequency = sanitize_text_field($_POST['frequency']);
        $categories = (array) $_POST['categories'];
        $types = (array) $_POST['types'];
        $skills = (array) $_POST['skills'];
        $levels = (array) $_POST['levels'];
        $locations = (array) $_POST['locations'];
        $locations = array_filter($locations);
        if (count($locations) > 1) {
            $locations = array($locations[count($locations) - 1]);
        }

        $user_id = get_current_user_id();
        $name = $email = '';

        if (!$user_id) {
            $name = sanitize_text_field($_POST['name']);
            if (!$name) {
                echo json_encode(array(
                    'success' => false,
                    'message' => iwj_get_alert(__('Please enter your name', 'iwjob'), 'danger')
                ));
                exit;
            }
            $email = sanitize_email($_POST['email']);
            if (!$email) {
                echo json_encode(array(
                    'success' => false,
                    'message' => iwj_get_alert(__('Please enter your email', 'iwjob'), 'danger')
                ));
                exit;
            }
        }

        $relationships = array_filter(array_merge($categories, $types, $levels, $locations, $skills));

        if (empty($relationships)) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please select at least one criteria', 'iwjob'), 'danger')
            ));
            exit;
        }

        $captcha_respon = self::check_recaptcha();
        if ($captcha_respon !== true) {
            echo json_encode(array(
                'loggedin' => false,
                'message' => iwj_get_alert(sprintf(__('Wrong captcha %s.', 'iwjob'), $captcha_respon), 'danger')
            ));
            die();
        }

        $alert_id = IWJ_Alert::add($position, $user_id, $name, $email, $salary_from, $frequency, $relationships);
        if ($alert_id) {
            if ($user_id || !iwj_option('email_confirm_alert_job_enable')) {
                ob_start();
                iwj_get_template_part('job-alert-popup/thankyou', array('alert_id' => $alert_id));
                $message = ob_get_contents();
                ob_end_clean();
            } else {
                ob_start();
                iwj_get_template_part('job-alert-popup/confirm', array('alert_id' => $alert_id));
                $message = ob_get_contents();
                ob_end_clean();

                IWJ_Email::send_email('confirm_alert_job', array(
                    'alert_id' => $alert_id,
                    'name' => $name,
                    'email' => $email,
                ));
            }

            if ($is_popup) {
                echo json_encode(array(
                    'success' => true,
                    'message' => $message
                ));
                exit;
            } else {
                $dashboard = iwj_get_page_permalink('dashboard');
                echo json_encode(array(
                    'success' => true,
                    'redirect_url' => add_query_arg(array('iwj_tab' => 'alerts'), $dashboard),
                    'message' => iwj_get_alert(__('Added successfully, redirecting ...', 'iwjob'), 'success')
                ));
                exit;
            }
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('An error occurred during the execution please reload the page and try again.', 'iwjob'), 'danger')
            ));
            exit;
        }
    }

    static function edit_alert() {
        check_ajax_referer('iwj-security');
        $alert_id = sanitize_text_field($_POST['alert_id']);
        $alert = IWJ_Alert::get_alert($alert_id);
        if (!$alert) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Invalid Alert.', 'iwjob'), 'danger')
            ));
            exit;
        }

        if (!$alert->can_edit()) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('You do not have permission to edit this alert.', 'iwjob'), 'danger')
            ));
            exit;
        }

        $position = sanitize_text_field($_POST['position']);
        $salary_from = sanitize_text_field($_POST['salary_from']);
        $frequency = sanitize_text_field($_POST['frequency']);
        $categories = (array) $_POST['categories'];
        $types = (array) $_POST['types'];
        $levels = (array) $_POST['levels'];
        $skills = (array) $_POST['skills'];
        $locations = (array) $_POST['locations'];

        /* $locations = array_filter($locations);
          if(count($locations) > 1){
          $locations = array($locations[count($locations) - 1]);
          } */

        $relationships = array_filter(array_merge($categories, $types, $levels, $locations, $skills));

        if (empty($relationships)) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please select at least one criteria', 'iwjob'), 'danger')
            ));
            exit;
        }

        if ($alert->update($position, get_current_user_id(), $salary_from, $frequency, $relationships)) {
            $dashboard = iwj_get_page_permalink('dashboard');
            echo json_encode(array(
                'success' => true,
                'redirect_url' => add_query_arg(array('iwj_tab' => 'alerts'), $dashboard),
                'message' => iwj_get_alert(__('Updated successfully, redirecting ...', 'iwjob'), 'success')
            ));
            exit;
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('An error occurred during the execution please reload the page and try again.', 'iwjob'), 'danger')
            ));
            exit;
        }
    }

    static function delete_alert() {
        check_ajax_referer('iwj-security');
        $alert_id = sanitize_text_field($_POST['alert_id']);
        $alert = IWJ_Alert::get_alert($alert_id);
        if (!$alert) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Invalid Alert.', 'iwjob'), 'danger')
            ));
            exit;
        }

        if (!$alert->can_delete()) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('You do not have permission to delete this alert.', 'iwjob'), 'danger')
            ));
            exit;
        }

        if (IWJ_Alert::delete($alert_id)) {
            echo json_encode(array(
                'success' => true,
                'message' => iwj_get_alert(__('Deleted successfully.', 'iwjob'), 'success')
            ));
            exit;
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('An error occurred during the execution please reload the page and try again.', 'iwjob'), 'danger')
            ));
            exit;
        }
    }

    static function delete_job() {
        check_ajax_referer('iwj-security');
        $id = sanitize_text_field($_POST['id']);
        $job = IWJ_Job::get_job($id);
        if (!$job) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Invalid Job.', 'iwjob'), 'danger')
            ));
            exit;
        }

        if (!$job->can_delete()) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('You do not have permission to delete this job.', 'iwjob'), 'danger')
            ));
            exit;
        }

        if (wp_delete_post($id)) {
            echo json_encode(array(
                'success' => true,
                'message' => iwj_get_alert(__('Deleted successfully.', 'iwjob'), 'success')
            ));
            exit;
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('An error occurred during the execution please reload the page and try again.', 'iwjob'), 'danger')
            ));
            exit;
        }
    }

    static function update_application() {
        check_ajax_referer('iwj-security');
        $application_id = sanitize_text_field($_POST['application_id']);
        $application = IWJ_Application::get_application($application_id);
        if (!$application) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Invalid Application.', 'iwjob'), 'danger')
            ));
            exit;
        }

        if (!$application->can_update()) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('You do not have permission to update this application.', 'iwjob'), 'danger')
            ));
            exit;
        }

        $application->update();

        $status = sanitize_text_field($_POST['application_status']);
        echo json_encode(array(
            'success' => true,
            'status' => $status == 'pending' ? 'interview' : ( $status == 'publish' ? 'accept' : 'reject' ),
            'status_icon' => iwj_get_status_icon($status),
            'status_title' => IWJ_Application::get_status_title($status),
            'status_class' => $status,
            'application_id' => $application_id,
            'message' => iwj_get_alert(__('Updated successfully.', 'iwjob'), 'success')
        ));
        exit;
    }

    static function delete_application() {
        check_ajax_referer('iwj-security');
        $applied_id = sanitize_text_field($_POST['id']);
        $applied = IWJ_Application::get_application($applied_id);
        if (!$applied) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Invalid Application.', 'iwjob'), 'danger')
            ));
            exit;
        }

        if (!$applied->can_update()) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('You do not have permission to delete this application.', 'iwjob'), 'danger')
            ));
            exit;
        }

        if (wp_delete_post($applied_id)) {
            echo json_encode(array(
                'success' => true,
                'message' => iwj_get_alert(__('Deleted successfully.', 'iwjob'), 'success')
            ));
            exit;
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('An error occurred during the execution please reload the page and try again.', 'iwjob'), 'danger')
            ));
            exit;
        }
    }

    static function application_email() {
        check_ajax_referer('iwj-security');
        $application_id = sanitize_text_field($_POST['application_id']);
        $application = IWJ_Application::get_application($application_id);
        if (!$application) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Invalid Application.', 'iwjob'), 'danger')
            ));
            exit;
        }

        if (!$application->can_send_email_to_candidate()) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('You do not have permission to send this messages.', 'iwjob'), 'danger')
            ));
            exit;
        }

        if (!iwj_option('allow_guest_apply_job')) {
            $author = $application->get_author();
            if (!$author) {
                echo json_encode(array(
                    'success' => false,
                    'message' => iwj_get_alert(__('Invalid candidate.', 'iwjob'), 'danger')
                ));
                exit;
            }
        }

        $subject = stripslashes(sanitize_text_field($_POST['subject']));
        $message = stripslashes(wp_kses_post($_POST['message']));
        if (!$subject) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please enter email subject', 'iwjob'), 'danger')
            ));
            exit;
        }
        if (!$message) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please enter email message', 'iwjob'), 'danger')
            ));
            exit;
        }

        $email_data = array(
            'application_id' => $application_id,
            'subject' => $subject,
            'message' => $message,
        );

        IWJ_Email::send_email('application', $email_data);

        echo json_encode(array(
            'success' => true,
            'message' => iwj_get_alert(__('Sent successfully.', 'iwjob'), 'success')
        ));

        exit;
    }

    static function contact() {
        check_ajax_referer('iwj-security');

        $item_id = sanitize_text_field($_POST['item_id']);
        if (!$item_id) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Invalid Item.', 'iwjob'), 'danger')
            ));
            exit;
        }

        $subject = stripslashes(sanitize_text_field($_POST['subject']));
        $name = stripslashes(sanitize_text_field($_POST['name']));
        $email = stripslashes(sanitize_email($_POST['email']));
        $message = stripslashes(wp_kses_post($_POST['message']));

        if (!$subject) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please enter a subject.', 'iwjob'), 'danger')
            ));
            exit;
        }

        if (!$message) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please enter a message.', 'iwjob'), 'danger')
            ));
            exit;
        }


        $captcha_respon = self::check_recaptcha();
        if ($captcha_respon !== true) {
            echo json_encode(array(
                'loggedin' => false,
                'message' => iwj_get_alert(sprintf(__('Wrong captcha %s.', 'iwjob'), $captcha_respon), 'danger')
            ));
            die();
        }

        $email_data = array(
            'item_id' => $item_id,
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
        );

        IWJ_Email::send_email('contact', $email_data);

        echo json_encode(array(
            'success' => true,
            'message' => iwj_get_alert(__('Sent successfully.', 'iwjob'), 'success')
        ));

        exit;
    }

    static function review() {
        check_ajax_referer('iwj-security');
        $rate_item_id = sanitize_text_field($_POST['rate_item_id']);
        $user_id = get_current_user_id();

        if (!$rate_item_id) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Invalid Item.', 'iwjob'), 'danger')
            ));
            exit;
        }

        global $wpdb;
        $sql = "SELECT COUNT(1) FROM {$wpdb->prefix}iwj_reviews WHERE user_id = %d AND item_id = %d";
        if ($wpdb->get_var($wpdb->prepare($sql, $user_id, $rate_item_id))) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Sorry, You have already reviewed.', 'iwjob'), 'danger')
            ));
            exit;
        }

        $review_options = iwj_option('review_options', '');
        $vote_value = array();
        $trim_option = trim($review_options);
        if (!empty($trim_option)) {
            $arr_reviews = explode("\n", $review_options);
            foreach ($arr_reviews as $key_val => $review_option) {
                $rev_item_name = strtolower(str_replace(' ', '_', trim($review_option)));
                if (stripslashes(sanitize_text_field($_POST['iwj_rate_num_' . $key_val]))) {
                    $vote_value[$rev_item_name] = stripslashes(sanitize_text_field($_POST['iwj_rate_num_' . $key_val]));
                } else {
                    $vote_value[$rev_item_name] = 0;
                }
            }

            for ($j = 0; $j < count($arr_reviews); $j ++) {
                $iwj_rate_num = sanitize_text_field($_POST['iwj_rate_num_' . $j]);
                if (!$iwj_rate_num) {
                    echo json_encode(array(
                        'success' => false,
                        'message' => iwj_get_alert(__('Please select rating', 'iwjob'), 'danger')
                    ));
                    exit;
                }
            }

            $rate_number = count($arr_reviews) ? array_sum($vote_value) / count($arr_reviews) : 0;
        } else {
            $rate_number = stripslashes(sanitize_text_field($_POST['iwj_simple_rate'])) ? stripslashes(sanitize_text_field($_POST['iwj_simple_rate'])) : 0;
        }

        $title = stripslashes(sanitize_text_field($_POST['iwj_review_title']));
        $content = stripslashes(wp_kses_post($_POST['iwj_review_content']));

        if ($rate_number == 0) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please select rating.', 'iwjob'), 'danger')
            ));
            exit;
        }
        if (!$title) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please enter a title.', 'iwjob'), 'danger')
            ));
            exit;
        }
        if (!$content) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please enter content.', 'iwjob'), 'danger')
            ));
            exit;
        }
        $rating_data = array(
            'criterias' => serialize($vote_value),
            'user_id' => $user_id,
            'item_id' => $rate_item_id,
            'rating' => $rate_number,
            'title' => $title,
            'content' => $content,
        );
        IWJ_Reviews::send_review($rating_data);
        echo json_encode(array(
            'rate_num' => $rate_number,
            'success' => true,
            'message' => iwj_get_alert(__('Sent review successfully, please wait admin approve.', 'iwjob'), 'success')
        ));

        exit;
    }

    static function update_review() {
        check_ajax_referer('iwj-security');

        $review_id = sanitize_text_field($_POST['review_id']);
        if (!$review_id) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Invalid Item.', 'iwjob'), 'danger')
            ));
            exit;
        }
        $review = IWJ_Reviews::get_review($review_id);
        $rate_item_id = $review->get_item_id();
        $user_id = $review->get_user_id();
        $permalink = $review->permalink();
        $edit_review_auto_approved = iwj_option('edit_review_auto_approved');
        if (!$edit_review_auto_approved) {
            $status = 'pending';
        } else {
            $status = $review->get_status();
        }

        $review_options = iwj_option('review_options', '');
        $vote_value = array();
        $trim_option = trim($review_options);
        if (!empty($trim_option)) {
            $arr_reviews = explode("\n", $review_options);
            foreach ($arr_reviews as $key_val => $review_option) {
                $rev_item_name = strtolower(str_replace(' ', '_', trim($review_option)));
                if (stripslashes(sanitize_text_field($_POST['iwj_rate_num_' . $key_val]))) {
                    $vote_value[$rev_item_name] = stripslashes(sanitize_text_field($_POST['iwj_rate_num_' . $key_val]));
                } else {
                    $vote_value[$rev_item_name] = 0;
                }
            }

            for ($j = 0; $j < count($arr_reviews); $j ++) {
                $iwj_rate_num = sanitize_text_field($_POST['iwj_rate_num_' . $j]);
                if (!$iwj_rate_num) {
                    echo json_encode(array(
                        'success' => false,
                        'message' => iwj_get_alert(__('Empty a criteria rate.', 'iwjob'), 'danger')
                    ));
                    exit;
                }
            }

            $rate_number = count($arr_reviews) ? array_sum($vote_value) / count($arr_reviews) : 0;
        } else {
            $rate_number = stripslashes(sanitize_text_field($_POST['iwj_simple_rate'])) ? stripslashes(sanitize_text_field($_POST['iwj_simple_rate'])) : 0;
        }

        $title = stripslashes(sanitize_text_field($_POST['iwj_review_title']));
        $content = stripslashes(wp_kses_post($_POST['iwj_review_content']));

        if ($rate_number == 0) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please select number rate.', 'iwjob'), 'danger')
            ));
            exit;
        }
        if (!$title) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please enter a title.', 'iwjob'), 'danger')
            ));
            exit;
        }
        if (!$content) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please enter content.', 'iwjob'), 'danger')
            ));
            exit;
        }

        $review_data = array(
            'ID' => $review_id,
            'user_id' => $user_id,
            'item_id' => $rate_item_id,
            'rating' => $rate_number,
            'title' => $title,
            'content' => $content,
            'status' => $status,
            'criterias' => serialize($vote_value),
            'read' => 0,
        );
        if (!$edit_review_auto_approved) {
            $review_data['update'] = 1;
        }

        IWJ_Reviews::update_review($review_data);

        if ($edit_review_auto_approved) {
            echo json_encode(array(
                'rate_num' => $rate_number,
                'permalink' => $permalink,
                'success' => true,
                'message' => iwj_get_alert(__('Updated review successfully, please wait admin approve.', 'iwjob'), 'success'),
                'auto_approved' => false,
            ));
        } else {
            echo json_encode(array(
                'rate_num' => $rate_number,
                'permalink' => $permalink,
                'success' => true,
                'message' => iwj_get_alert(__('Updated review successfully.', 'iwjob'), 'success'),
                'auto_approved' => true,
            ));
        }

        exit;
    }

    static function edit_review() {
        check_ajax_referer('iwj-security');
        $review_id = sanitize_text_field($_POST['review_id']);
        if (!$review_id || !current_user_can('apply_job')) {
            exit;
        }

        $data = IWJ_Reviews::get_review_by_id($review_id);
        if ($data) {
            echo json_encode(array(
                'data' => array(
                    'title' => $data->title,
                    'content' => $data->content,
                    'criterias' => unserialize($data->criterias)
                )
            ));
            exit;
        }
    }

    static function reply_review() {
        check_ajax_referer('iwj-security');

        $review_id = sanitize_text_field($_POST['iwj_reply_review_id']);
        $content = sanitize_textarea_field($_POST['iwj_reply_review']);
        $review = IWJ_Reviews::get_review($review_id);
        $item_id = $review->get_item_id();
        $employer_name = $review->get_employer_name();
        $employer_url = get_avatar_url($review->get_author_item_id());
        if (!$review_id) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Review is not exist.', 'iwjob'), 'danger')
            ));

            exit;
        }
        $trim_content = trim($content);
        if (!$content || empty($trim_content)) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please enter your content to reply review.', 'iwjob'), 'danger')
            ));

            exit;
        }

        $reply_data = array(
            'review_id' => $review_id,
            'user_id' => $item_id,
            'reply_content' => $content
        );
        IWJ_Reviews::reply_review($reply_data);
        echo json_encode(array(
            'success' => true,
            'employer_name' => $employer_name,
            'employer_url' => $employer_url,
            'reply_content' => $content,
            'message' => iwj_get_alert(__('Reply successfully.', 'iwjob'), 'success')
        ));

        exit;
    }

    static function edit_reply_review() {
        check_ajax_referer('iwj-security');

        $reply_id = sanitize_text_field($_POST['iwj_reply_id']);
        $reply_content = sanitize_textarea_field($_POST['iwj_reply_content']);

        if (!$reply_id) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('The reply does not exist.', 'iwjob'), 'danger')
            ));

            exit;
        }
        $trim_content = trim($reply_content);
        if (!$reply_content || empty($trim_content)) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please enter your content to reply review.', 'iwjob'), 'danger')
            ));

            exit;
        }

        $reply_data = array(
            'ID' => $reply_id,
            'reply_content' => $reply_content
        );

        IWJ_Reviews::update_reply_review($reply_data);
        echo json_encode(array(
            'success' => true,
            'reply_content' => $reply_content,
            'message' => iwj_get_alert(__('Update reply successfully.', 'iwjob'), 'success')
        ));

        exit;
    }

    static function delete_review() {
        check_ajax_referer('iwj-security');
        $review_id = sanitize_text_field($_POST['review_id']);
        $review = IWJ_Reviews::get_review($review_id);
        if (!$review) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Invalid Review.', 'iwjob'), 'danger')
            ));
            exit;
        }

        if (!$review->can_delete()) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('You do not have permission to delete this review.', 'iwjob'), 'danger')
            ));
            exit;
        }

        if (IWJ_Reviews::delete_review($review_id)) {
            echo json_encode(array(
                'success' => true,
                'message' => iwj_get_alert(__('Deleted successfully.', 'iwjob'), 'success')
            ));
            exit;
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('An error occurred during the execution please reload the page and try again.', 'iwjob'), 'danger')
            ));
            exit;
        }
    }

    static function delete_reply() {
        check_ajax_referer('iwj-security');
        $id = sanitize_text_field($_POST['reply_id']);
        if (!$id) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Invalid Review.', 'iwjob'), 'danger')
            ));
            exit;
        }

        if (IWJ_Reviews::delete_reply($id)) {
            echo json_encode(array(
                'success' => true,
                'message' => iwj_get_alert(__('Deleted successfully.', 'iwjob'), 'success')
            ));
            exit;
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('An error occurred during the execution please reload the page and try again.', 'iwjob'), 'danger')
            ));
            exit;
        }
    }

    /**
     * Add order note via ajax.
     */
    public static function add_order_note() {
        check_ajax_referer('iwj-security');

        if (!current_user_can('manage_options')) {
            wp_die(- 1);
        }

        $post_id = absint($_POST['post_id']);
        $note = wp_kses_post(trim(stripslashes($_POST['note'])));
        $note_type = $_POST['note_type'];

        $is_customer_note = ( 'customer' === $note_type ) ? 1 : 0;

        if ($post_id > 0) {
            $order = IWJ_Order::get_order($post_id);
            $comment_id = $order->add_order_note($note, $is_customer_note, true);

            echo '<li rel="' . esc_attr($comment_id) . '" class="iwj-note ';
            if ($is_customer_note) {
                echo 'customer-note';
            }
            echo '"><div class="iwj-note-content">';
            echo wpautop(wptexturize($note));
            echo '</div><p class="meta"><a href="#" class="iwj-delete-note">' . __('Delete note', 'iwjob') . '</a></p>';
            echo '</li>';
        }
        wp_die();
    }

    public static function delete_order_note() {
        check_ajax_referer('iwj-security');

        if (!current_user_can('manage_options')) {
            wp_die(- 1);
        }

        $note_id = (int) $_POST['note_id'];

        if ($note_id > 0) {
            wp_delete_comment($note_id);
        }
        wp_die();
    }

    public static function send_customer_invoice() {
        check_ajax_referer('iwj-security');

        if (!current_user_can('manage_options')) {
            wp_die(- 1);
        }

        $order_id = (int) $_POST['order_id'];

        if ($order_id) {
            $order = IWJ_Order::get_order($order_id);
            IWJ_Email::send_email('customer_invoice', $order);
        }

        wp_die();
    }

    function get_application_details() {
        check_ajax_referer('iwj-security');
        $application_id = $_REQUEST['application_id'];
        $application = IWJ_Application::get_application($application_id);
        ob_start();
        if ($application) {
            if ($application->can_view()) {
                iwj_get_template_part('dashboard/view-application-popup', array('application' => $application));
            } else {
                echo __('Sorry, you do not have permission to view this application.', 'iwjob');
            }
        } else {
            echo __('Invalid Application', 'iwjob');
        }
        $html = ob_get_contents();
        ob_clean();
        echo $html;
        exit;
    }

    function get_submited_application_details() {
        check_ajax_referer('iwj-security');
        $application_id = $_REQUEST['application_id'];
        $application = IWJ_Application::get_application($application_id);
        ob_start();
        if ($application) {
            if ($application->get_author_id() == get_current_user_id()) {
                iwj_get_template_part('dashboard/view-submited-application-popup', array('application' => $application));
            } else {
                echo __('Sorry, you do not have permission to view this application.', 'iwjob');
            }
        } else {
            echo __('Invalid Application', 'iwjob');
        }
        $html = ob_get_contents();
        ob_clean();
        echo $html;
        exit;
    }

    function get_order_details() {
        check_ajax_referer('iwj-security');
        $order_id = $_REQUEST['order_id'];
        $order = IWJ_Order::get_order($order_id);
        ob_start();
        if ($order) {
            if ($order->can_view()) {
                iwj_get_template_part('dashboard/view-order-popup', array('order' => $order));
            } else {
                echo __('Sorry, you do not have permission to view this order.', 'iwjob');
            }
        } else {
            echo __('Invalid order', 'iwjob');
        }
        $html = ob_get_contents();
        ob_clean();
        echo $html;
        exit;
    }

    function resend_verification() {
        check_ajax_referer('iwj-security');

        $user = IWJ_User::get_user();

        IWJ_Email::send_email('verify_account', array('user' => $user));

        echo json_encode(array(
            'success' => true,
            'message' => __('An email was sent to your email please click the activation link to verify your account!', 'iwjob')
        ));

        exit;
    }

    function create_all_job_products() {
        check_ajax_referer('iwj-security');
        if (!class_exists('WooCommerce')) {
            echo json_encode(array(
                'success' => false,
                'message' => __('Please enable woocommerce plugin first.', 'iwjob')
            ));
            exit;
        } else {
            IWJ_Woocommerce::create_all_job_products();
            $products_list = IWJ_Woocommerce::get_products_list();
            echo json_encode(array(
                'success' => true,
                'html' => $products_list
            ));
            exit;
        }
    }

    static function iwj_job_expiry_notice() {
        if (iwj_option('submit_job_mode') == '1' && iwj_option('email_job_expiry_notice_enable')) {
            $before_days = iwj_option('send_job_expiry_notice_before');
            $in_days = iwj_option('send_job_expiry_notice_days');
            $before_days = $before_days ? $before_days : 1;
            $in_days = $in_days ? $in_days : 1;
            if ($in_days > $before_days) {
                $in_days = $before_days;
            }

            $first_time = current_time('timestamp') - ( $before_days - $in_days - 1 ) * 86400;
            $second_time = current_time('timestamp') + $before_days * 86400;
            global $wpdb;
            $sql = "SELECT p.ID FROM {$wpdb->users} AS p  
            JOIN {$wpdb->postmeta} AS pm ON pm.post_id = p.ID 
            WHERE pm.meta_key = %s AND pm.meta_value != '-1' AND CAST(pm.meta_value AS UNSIGNED) >= %d AND CAST(pm.meta_value AS UNSIGNED) <= %d";
            $jobs = $wpdb->get_results($wpdb->prepare($sql, IWJ_PREFIX . 'expiry', $first_time, $second_time));
            if ($jobs) {
                foreach ($jobs AS $job) {
                    IWJ_Email::send_email('job_expiry_notice', array('job_id' => $job->ID));
                }
            }
        }
    }

    public static function loadmore_jobs() {
        check_ajax_referer('iwj-security');
        $posts_per_page = sanitize_text_field($_POST['posts_per_page']);
        $exclude_id = sanitize_text_field($_POST['exclude_id']);
        $include_id = sanitize_text_field($_POST['include_id']);
        $offset = sanitize_text_field($_POST['offset']);
        $taxonomies = sanitize_text_field($_POST['taxonomies']);
        $style = sanitize_text_field($_POST['style']);
        $args = array(
            'suppress_filters' => true,
            'post_type' => 'iwj_job',
            'post_status' => 'publish',
            'include' => $include_id,
            'exclude' => $exclude_id,
            'posts_per_page' => $posts_per_page,
            'offset' => $offset,
        );

        if ($taxonomies) {
            $args['tax_query'] = array();
            $strip_taxonomies = json_decode(stripslashes($taxonomies));
            $array_tax = (array) $strip_taxonomies;
            foreach ($array_tax as $taxonomy => $array_taxonomy) {
                if (is_array($array_taxonomy)) {
                    $args['tax_query'][] = array(
                        'taxonomy' => $taxonomy,
                        'field' => 'term_id',
                        'terms' => $array_taxonomy,
                    );
                }
            }
        }
        $query = new WP_Query($args);
        $out = '';

        if ($query->have_posts()) :
            while ($query->have_posts()) : $query->the_post();
                $out .= iwj_get_template_part('parts/jobs/job', array('style' => $style));
            endwhile;
            wp_reset_postdata();

        endif;

        die();
    }

    static function indeed_load_data() {
        check_ajax_referer('iwj-security');
        $ide_query = sanitize_text_field($_POST['query']);
        $iwj_ide_country = sanitize_text_field($_POST['location']);
        $iwj_ide_type = sanitize_text_field($_POST['job_type']);
        $publisher_id = sanitize_text_field($_POST['publisher_id']);
        $max_items = sanitize_text_field($_POST['max_items']);
        $logo_url = sanitize_text_field($_POST['logo_url']);
        $style = sanitize_text_field($_POST['style']);

        $api_url = 'http://api.indeed.com/ads/apisearch?publisher=' . $publisher_id . '&q=' . urlencode($ide_query) . '&l=&sort=relevance&radius=&st=&jt=' . urlencode($iwj_ide_type) . '&start=1&limit=' . urlencode($max_items) . '&fromage=%20&filter=&latlong=&co=' . urlencode($iwj_ide_country) . '&&userip=' . urlencode(iwj_indeed_job_importer_user_ip_address()) . '&v=2&useragent=' . urlencode(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "Mozilla/5.0 (compatible; Konqueror/4.0; Microsoft Windows) KHTML/4.0.80 (like Gecko)");
        $get_content = iwj_indeed_job_importer_readFeeds($api_url);
        $out_put = '';
        if (count($get_content)) {
            $show_company = iwj_option('show_company_job');
            for ($i = 0; $i < count($get_content); $i ++) {
                $item_id = wp_filter_nohtml_kses($get_content[$i]['id']);
                $item_title = wp_filter_nohtml_kses($get_content[$i]['title']);
                $item_url = wp_filter_nohtml_kses($get_content[$i]['url']);
                $item_f_location_full = wp_filter_nohtml_kses($get_content[$i]['formatted_loc_full']);
                $item_company = wp_filter_nohtml_kses($get_content[$i]['company']);
                $item_relative_time = wp_filter_nohtml_kses($get_content[$i]['relative_time']);

                $out_put .= '<div class="grid-content" data-id="' . $item_id . '"><div class="job-item">';
                switch ($style) {
                    case 'style1':
                    case 'style2':
                        if ($item_company) {
                            $out_put .= '<div class="job-image"><img src="' . esc_url($logo_url) . '" alt="' . $item_company . '" width="150" height="150"></div>';
                        }
                        $out_put .= '<div class="job-info">';
                        $out_put .= '<h3 class="job-title"><a href="' . esc_url($item_url) . '">' . $item_title . '</a></h3>';
                        $out_put .= '<div class="info-company">';
                        if ($item_company && ( $show_company == '1' )) :
                            $out_put .= '<div class="company"><i class="fa fa-suitcase"></i>' . $item_company . '</div>';
                        endif;
                        if ($item_f_location_full) :
                            $out_put .= '<div class="address"><i class="fa fa-map-marker"></i>' . $item_f_location_full . '</div>';
                        endif;
                        $out_put .= '</div></div>';
                        break;

                    case 'style3':
                        if ($item_company) {
                            $out_put .= '<div class="job-image"><img src="' . esc_url($logo_url) . '" alt="' . $item_company . '" width="150" height="150"></div>';
                        }
                        $out_put .= '<div class="job-info">';
                        $out_put .= '<h3 class="job-title"><a href="' . esc_url($item_url) . '">' . $item_title . '</a></h3>';
                        $out_put .= '<div class="info-company">';
                        if ($item_f_location_full) :
                            $out_put .= '<div class="address"><i class="fa fa-map-marker"></i>' . $item_f_location_full . '</div>';
                        endif;
                        $out_put .= '</div>';
                        $out_put .= '<div class="job-company-time">';
                        if ($item_company && ( $show_company == '1' )) :
                            $out_put .= '<div class="company"><i class="fa fa-suitcase"></i>' . $item_company . '</div>';
                        endif;
                        if ($item_relative_time) {
                            $out_put .= '<div class="job-posted-time">' . $item_relative_time . '</div>';
                        }

                        $out_put .= '</div></div>';
                        break;
                }
                $out_put .= '</div></div>';
            }
            echo json_encode(array(
                'success' => true,
                'data' => $out_put,
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('No class found.', 'iwjob'), 'danger')
            ));

            exit;
        }

        exit;
    }

    static function indeed_loadmore_jobs() {
        check_ajax_referer('iwj-security');
        $query = sanitize_text_field($_POST['query']);
        $publisher_id = sanitize_text_field($_POST['publisher_id']);
        $max_items = sanitize_text_field($_POST['max_items']);
        $offset = sanitize_text_field($_POST['offset']);
        $style = sanitize_text_field($_POST['style']);
        $logo_url = sanitize_text_field($_POST['logo_url']);
        $country = sanitize_text_field($_POST['country']);
        $location = sanitize_text_field($_POST['location']);
        $job_type = sanitize_text_field($_POST['job_type']);
        $offset = intval($offset) + 1;

        $api_url = 'http://api.indeed.com/ads/apisearch?publisher=' . $publisher_id . '&q=' . urlencode($query) . '&l=' . urlencode($location) . '&sort=relevance&radius=&st=&jt=' . urlencode($job_type) . '&start=' . urlencode($offset) . '&limit=' . urlencode($max_items) . '&fromage=%20&filter=&latlong=&co=' . urlencode($country) . '&&userip=' . urlencode(iwj_indeed_job_importer_user_ip_address()) . '&v=2&useragent=' . urlencode(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "Mozilla/5.0 (compatible; Konqueror/4.0; Microsoft Windows) KHTML/4.0.80 (like Gecko)");
        $jobs = iwj_indeed_job_importer_readFeeds($api_url);

        $out_put = '';
        if (count($jobs)) {
            $show_company = iwj_option('show_company_job');
            for ($i = 0; $i < count($jobs); $i ++) {
                $item_id = wp_filter_nohtml_kses($jobs[$i]['id']);
                $item_title = wp_filter_nohtml_kses($jobs[$i]['title']);
                $item_url = wp_filter_nohtml_kses($jobs[$i]['url']);
                $item_f_location_full = wp_filter_nohtml_kses($jobs[$i]['formatted_loc_full']);
                $item_company = wp_filter_nohtml_kses($jobs[$i]['company']);
                $item_relative_time = wp_filter_nohtml_kses($jobs[$i]['relative_time']);

                $out_put .= '<div class="grid-content" data-id="' . $item_id . '"><div class="job-item">';
                switch ($style) {
                    case 'style1':
                    case 'style2':
                        if ($item_company) {
                            $out_put .= '<div class="job-image"><img src="' . esc_url($logo_url) . '" alt="' . $item_company . '" width="150" height="150"></div>';
                        }
                        $out_put .= '<div class="job-info">';
                        $out_put .= '<h3 class="job-title"><a href="' . esc_url($item_url) . '">' . $item_title . '</a></h3>';
                        $out_put .= '<div class="info-company">';
                        if ($item_company && ( $show_company == '1' )) :
                            $out_put .= '<div class="company"><i class="fa fa-suitcase"></i>' . $item_company . '</div>';
                        endif;
                        if ($item_f_location_full) :
                            $out_put .= '<div class="address"><i class="fa fa-map-marker"></i>' . $item_f_location_full . '</div>';
                        endif;
                        $out_put .= '</div></div>';
                        break;

                    case 'style3':
                        if ($item_company) {
                            $out_put .= '<div class="job-image"><img src="' . esc_url($logo_url) . '" alt="' . $item_company . '" width="150" height="150"></div>';
                        }
                        $out_put .= '<div class="job-info">';
                        $out_put .= '<h3 class="job-title"><a href="' . esc_url($item_url) . '">' . $item_title . '</a></h3>';
                        $out_put .= '<div class="info-company">';
                        if ($item_f_location_full) :
                            $out_put .= '<div class="address"><i class="fa fa-map-marker"></i>' . $item_f_location_full . '</div>';
                        endif;
                        $out_put .= '</div>';
                        $out_put .= '<div class="job-company-time">';
                        if ($item_company && ( $show_company == '1' )) :
                            $out_put .= '<div class="company"><i class="fa fa-suitcase"></i>' . $item_company . '</div>';
                        endif;
                        if ($item_relative_time) {
                            $out_put .= '<div class="job-posted-time">' . $item_relative_time . '</div>';
                        }

                        $out_put .= '</div></div>';
                        break;
                }
                $out_put .= '</div></div>';
            }
            echo json_encode(array(
                'success' => true,
                'data_opt' => $out_put,
                'url' => $api_url
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('No class found.', 'iwjob'), 'danger')
            ));

            exit;
        }

        exit;
    }

    static function jobs_export() {
        check_ajax_referer('iwj-security');
        $job_types = $_POST['job_type'];
        if (!$job_types) {
            exit;
        }
        $job_type = explode(',', $job_types);

        $upload = wp_upload_dir();
        $targetDir = $upload['basedir'];
        $targetUrl = $upload['baseurl'];
        $targetDir = $targetDir . '/jobs_import_export/exports';
        $targetUrl = $targetUrl . '/jobs_import_export/exports';
        if (!is_dir($targetDir)) {
            wp_mkdir_p($targetDir);
        }

        if (count($job_type) > 1) {
            $files = array();

            foreach ($job_type as $type_job) {
                $args = array(
                    'posts_per_page' => - 1,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'post_type' => $type_job
                );
                switch ($type_job) {
                    case 'iwj_job':
                        $args['post_status'] = array(
                            'publish',
                            'pending',
                            'draft',
                            'iwj-rejected',
                            'iwj-pending-payment'
                        );
                        break;
                    case 'iwj_candidate':
                    case 'iwj_employer':
                        $args['post_status'] = array(
                            'publish',
                            'pending',
                            'iwj-incomplete'
                        );
                        break;
                }

                $getposts = get_posts($args);

                if (!count($getposts)) {
                    continue;
                }

                $filename = '';
                $data_rows = array();
                switch ($type_job) {
                    case 'iwj_job':
                        $filename = 'all-jobs-' . time() . '.csv';
                        foreach ($getposts as $post) : setup_postdata($post);
                            $post_id = $post->ID;

                            $job_locations = wp_get_post_terms($post_id, 'iwj_location');
                            if ($job_locations) {
                                $arr_locations = array();
                                foreach ($job_locations as $job_location) {
                                    $arr_locations[$job_location->parent] = $job_location->slug;
                                }
                                ksort($arr_locations);
                                $locate = implode('>', $arr_locations);
                            } else {
                                $locate = '';
                            }

                            $skills = wp_get_post_terms($post_id, 'iwj_skill');
                            if ($skills) {
                                $arr_skills = array();
                                foreach ($skills as $skill) {
                                    $arr_skills[] = $skill->slug;
                                }
                                $job_skill = implode('|', $arr_skills);
                            } else {
                                $job_skill = '';
                            }

                            $types = wp_get_post_terms($post_id, 'iwj_type');
                            if ($types) {
                                $arr_types = array();
                                foreach ($types as $type) {
                                    $arr_types[] = $type->slug;
                                }
                                $job_type_now = implode('|', $arr_types);
                            } else {
                                $job_type_now = '';
                            }

                            $levels = wp_get_post_terms($post_id, 'iwj_level');
                            if ($levels) {
                                $arr_levels = array();
                                foreach ($levels as $level) {
                                    $arr_levels[] = $level->slug;
                                }
                                $job_level = implode('|', $arr_levels);
                            } else {
                                $job_level = '';
                            }

                            $cats = wp_get_post_terms($post_id, 'iwj_cat');
                            if ($cats) {
                                $arr_cats = array();
                                foreach ($cats as $cat) {
                                    $arr_cats[] = $cat->slug;
                                }
                                $job_cat = implode('|', $arr_cats);
                            } else {
                                $job_cat = '';
                            }

                            $_job_expiry = get_post_meta($post_id, '_iwj_expiry', true);
                            $_iwj_expiry = $_job_expiry ? date('Y-m-d H:i:s', $_job_expiry) : '';
                            $_job_featured_date = get_post_meta($post_id, '_iwj_featured_date', true);
                            $_iwj_featured_date = $_job_featured_date ? date('Y-m-d H:i:s', $_job_featured_date) : '';
                            $_job_deadline = get_post_meta($post_id, '_iwj_deadline', true);
                            $_iwj_deadline = $_job_deadline ? date('Y-m-d H:i:s', $_job_deadline) : '';

                            $row = array();
                            $row[0] = $post_id;
                            $row[1] = $post->post_author;
                            $row[2] = $post->post_date;
                            $row[3] = $post->post_date_gmt;
                            $row[4] = $post->post_content;
                            $row[5] = $post->post_title;
                            $row[6] = $post->post_excerpt;
                            $row[7] = $post->post_status;
                            $row[8] = $post->post_name;
                            $row[9] = $post->post_modified;
                            $row[10] = $post->post_modified_gmt;
                            $row[11] = $post->post_content_filtered;
                            $row[12] = $post->post_parent;
                            $row[13] = $post->guid;
                            $row[14] = $post->menu_order;
                            $row[15] = $post->post_type;
                            $row[16] = $post->post_mime_type;
                            $row[17] = $post->comment_count;
                            $row[18] = $_iwj_expiry;
                            $row[19] = $_iwj_deadline;
                            $row[20] = get_post_meta($post_id, '_iwj_featured', true);
                            $row[21] = $_iwj_featured_date;
                            $row[22] = get_post_meta($post_id, '_iwj_email_application', true);
                            $row[23] = json_encode(get_post_meta($post_id, '_iwj_job_gender'));
                            $row[24] = json_encode(get_post_meta($post_id, '_iwj_job_languages'));
                            $row[25] = get_post_meta($post_id, '_iwj_salary_from', true);
                            $row[26] = get_post_meta($post_id, '_iwj_salary_to', true);
                            $row[27] = get_post_meta($post_id, '_iwj_salary_postfix', true);
                            $row[28] = get_post_meta($post_id, '_iwj_currency', true);
                            $row[29] = get_post_meta($post_id, '_iwj_address', true);
                            $row[30] = get_post_meta($post_id, '_iwj_map', true);
                            $row[31] = get_post_meta($post_id, '_iwj_reason', true);
                            $row[32] = get_post_meta($post_id, '_iwj_views', true);
                            $row[33] = get_post_meta($post_id, '_iwj_custom_apply_url', true);
                            $row[34] = get_post_meta($post_id, '_iwj_user_package_id', true);
                            $row[35] = get_post_meta($post_id, 'import_source', true);
                            $row[36] = get_post_meta($post_id, 'import_url', true);
                            $row[37] = get_post_meta($post_id, 'import_company', true);
                            $row[38] = get_post_meta($post_id, '_iwj_free_job', true);
                            $row[39] = get_post_meta($post_id, '_iwj_is_new_featured', true);
                            $row[40] = get_post_meta($post_id, '_iwj_is_new_publish', true);
                            $row[41] = $job_type_now;
                            $row[42] = $job_cat;
                            $row[43] = $job_skill;
                            $row[44] = $job_level;
                            $row[45] = $locate;

                            $data_rows[] = $row;
                        endforeach;

                        break;
                    case 'iwj_candidate':
                        $filename = 'all-candidates-' . time() . '.csv';
                        foreach ($getposts as $post) : setup_postdata($post);
                            $post_id = $post->ID;
                            $candidate_id = $post->post_author;
                            $user_info = get_userdata($candidate_id);

                            $thumbnail_id = get_post_meta($post_id, '_thumbnail_id', true);
                            $thumbnail_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
                            $candidate_galleries = get_post_meta($post_id, '_iwj_gallery');
                            if ($candidate_galleries) {
                                $gallery_arr = array();
                                foreach ($candidate_galleries as $candidate_gallery) {
                                    $gallery_arr[] = wp_get_attachment_url($candidate_gallery);
                                }
                                $gallery_url = implode('|', $gallery_arr);
                            } else {
                                $gallery_url = '';
                            }
                            $cv_id = get_post_meta($post_id, '_iwj_cv', true);
                            $cv_url = $cv_id ? wp_get_attachment_url($cv_id) : '';

                            $types = wp_get_post_terms($post_id, 'iwj_type');
                            if ($types) {
                                $arr_types = array();
                                foreach ($types as $type) {
                                    $arr_types[] = $type->slug;
                                }
                                $candidate_type = implode('|', $arr_types);
                            } else {
                                $candidate_type = '';
                            }

                            $cats = wp_get_post_terms($post_id, 'iwj_cat');
                            if ($cats) {
                                $arr_cats = array();
                                foreach ($cats as $cat) {
                                    $arr_cats[] = $cat->slug;
                                }
                                $candidate_cat = implode('|', $arr_cats);
                            } else {
                                $candidate_cat = '';
                            }

                            $skills = wp_get_post_terms($post_id, 'iwj_skill');
                            if ($skills) {
                                $arr_skills = array();
                                foreach ($skills as $skill) {
                                    $arr_skills[] = $skill->slug;
                                }
                                $candidate_skill = implode('|', $arr_skills);
                            } else {
                                $candidate_skill = '';
                            }

                            $levels = wp_get_post_terms($post_id, 'iwj_level');
                            if ($levels) {
                                $arr_levels = array();
                                foreach ($levels as $level) {
                                    $arr_levels[] = $level->slug;
                                }
                                $candidate_level = implode('|', $arr_levels);
                            } else {
                                $candidate_level = '';
                            }

                            $candidate_locations = wp_get_post_terms($post_id, 'iwj_location');
                            if ($candidate_locations) {
                                $arr_locations = array();
                                foreach ($candidate_locations as $candidate_location) {
                                    $arr_locations[$candidate_location->parent] = $candidate_location->name;
                                }
                                ksort($arr_locations);
                                $locate = implode('>', $arr_locations);
                            } else {
                                $locate = '';
                            }

                            $candidate_birthday = get_post_meta($post_id, '_iwj_birthday', true);
                            $_iwj_birthday = $candidate_birthday ? date('Y-m-d H:i:s', $candidate_birthday) : '';

                            $row = array();
                            $row[0] = $post_id;
                            $row[1] = $post->post_author;
                            $row[2] = $post->post_date;
                            $row[3] = $post->post_date_gmt;
                            $row[4] = $post->post_content;
                            $row[5] = $post->post_title;
                            $row[6] = $post->post_excerpt;
                            $row[7] = $post->post_status;
                            $row[8] = $post->post_name;
                            $row[9] = $post->post_modified;
                            $row[10] = $post->post_modified_gmt;
                            $row[11] = $post->post_content_filtered;
                            $row[12] = $post->post_parent;
                            $row[13] = $post->guid;
                            $row[14] = $post->menu_order;
                            $row[15] = $post->post_type;
                            $row[16] = $post->post_mime_type;
                            $row[17] = $post->comment_count;
                            $row[18] = $user_info->user_login;
                            $row[19] = $user_info->user_pass;
                            $row[20] = $user_info->user_nicename;
                            $row[21] = $user_info->user_email;
                            $row[22] = $user_info->user_url;
                            $row[23] = $user_info->user_registered;
                            $row[24] = $user_info->user_activation_key;
                            $row[25] = $user_info->user_status;
                            $row[26] = $user_info->display_name;
                            $row[27] = get_post_meta($post_id, '_iwj_headline', true);
                            $row[28] = $_iwj_birthday;
                            $row[29] = get_post_meta($post_id, '_iwj_views', true);
                            $row[30] = get_post_meta($post_id, '_iwj_address', true);
                            $row[31] = get_post_meta($post_id, '_iwj_gender', true);
                            $row[32] = json_encode(get_post_meta($post_id, '_iwj_languages'));
                            $row[33] = get_post_meta($post_id, '_iwj_reason', true);
                            $row[34] = get_post_meta($post_id, '_iwj_public_account', true);
                            $row[35] = get_post_meta($post_id, '_iwj_phone', true);
                            $row[36] = get_post_meta($post_id, '_iwj_map', true);
                            $row[37] = get_post_meta($post_id, '_iwj_facebook', true);
                            $row[38] = get_post_meta($post_id, '_iwj_twitter', true);
                            $row[39] = get_post_meta($post_id, '_iwj_google_plus', true);
                            $row[40] = get_post_meta($post_id, '_iwj_youtube', true);
                            $row[41] = get_post_meta($post_id, '_iwj_vimeo', true);
                            $row[42] = get_post_meta($post_id, '_iwj_linkedin', true);
                            $row[43] = serialize(get_post_meta($post_id, '_iwj_experience', true));
                            $row[44] = serialize(get_post_meta($post_id, '_iwj_education', true));
                            $row[45] = serialize(get_post_meta($post_id, '_iwj_skill_showcase', true));
                            $row[46] = $gallery_url;
                            $row[47] = get_post_meta($post_id, '_iwj_video', true);
                            $row[48] = serialize(get_post_meta($post_id, '_iwj_award', true));
                            $row[49] = $cv_url;
                            $row[50] = get_post_meta($post_id, '_iwj_cover_letter', true);
                            $row[51] = $thumbnail_url;
                            $row[52] = $candidate_type;
                            $row[53] = $candidate_cat;
                            $row[54] = $candidate_skill;
                            $row[55] = $candidate_level;
                            $row[56] = $locate;

                            $data_rows[] = $row;
                        endforeach;

                        break;
                    case 'iwj_employer':
                        $filename = 'all-employers-' . time() . '.csv';
                        foreach ($getposts as $post) : setup_postdata($post);
                            $post_id = $post->ID;
                            $employer_id = $post->post_author;
                            $user_info = get_userdata($employer_id);

                            $thumbnail_id = get_post_meta($post_id, '_thumbnail_id', true);
                            $thumbnail_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
                            $employer_galleries = get_post_meta($post_id, '_iwj_gallery');
                            if ($employer_galleries) {
                                $gallery_arr = array();
                                foreach ($employer_galleries as $employer_gallery) {
                                    $gallery_arr[] = wp_get_attachment_url($employer_gallery);
                                }
                                $gallery_url = implode('|', $gallery_arr);
                            } else {
                                $gallery_url = '';
                            }

                            $company_sizes = wp_get_post_terms($post_id, 'iwj_size');
                            if ($company_sizes) {
                                $company_sizes = $company_sizes[0];
                                $company_size = $company_sizes->slug;
                            } else {
                                $company_size = '';
                            }

                            $cats = wp_get_post_terms($post_id, 'iwj_cat');
                            if ($cats) {
                                $arr_cats = array();
                                foreach ($cats as $cat) {
                                    $arr_cats[] = $cat->slug;
                                }
                                $employer_cat = implode('|', $arr_cats);
                            } else {
                                $employer_cat = '';
                            }

                            $employer_locations = wp_get_post_terms($post_id, 'iwj_location');
                            if ($employer_locations) {
                                $arr_locations = array();
                                foreach ($employer_locations as $employer_location) {
                                    $arr_locations[$employer_location->parent] = $employer_location->slug;
                                }
                                ksort($arr_locations);
                                $locate = implode('>', $arr_locations);
                            } else {
                                $locate = '';
                            }

                            $row = array();
                            $row[0] = $post_id;
                            $row[1] = $post->post_author;
                            $row[2] = $post->post_date;
                            $row[3] = $post->post_date_gmt;
                            $row[4] = $post->post_content;
                            $row[5] = $post->post_title;
                            $row[6] = $post->post_excerpt;
                            $row[7] = $post->post_status;
                            $row[8] = $post->post_name;
                            $row[9] = $post->post_modified;
                            $row[10] = $post->post_modified_gmt;
                            $row[11] = $post->post_content_filtered;
                            $row[12] = $post->post_parent;
                            $row[13] = $post->guid;
                            $row[14] = $post->menu_order;
                            $row[15] = $post->post_type;
                            $row[16] = $post->post_mime_type;
                            $row[17] = $post->comment_count;
                            $row[18] = $user_info->user_login;
                            $row[19] = $user_info->user_pass;
                            $row[20] = $user_info->user_nicename;
                            $row[21] = $user_info->user_email;
                            $row[22] = $user_info->user_url;
                            $row[23] = $user_info->user_registered;
                            $row[24] = $user_info->user_activation_key;
                            $row[25] = $user_info->user_status;
                            $row[26] = $user_info->display_name;
                            $row[27] = get_post_meta($post_id, '_iwj_headline', true);
                            $row[28] = get_post_meta($post_id, '_iwj_views', true);
                            $row[29] = get_post_meta($post_id, '_iwj_map', true);
                            $row[30] = get_post_meta($post_id, '_iwj_address', true);
                            $row[31] = get_post_meta($post_id, '_iwj_reason', true);
                            $row[32] = get_post_meta($post_id, '_iwj_phone', true);
                            $row[33] = get_post_meta($post_id, '_iwj_founded_date', true);
                            $row[34] = get_post_meta($post_id, '_iwj_facebook', true);
                            $row[35] = get_post_meta($post_id, '_iwj_twitter', true);
                            $row[36] = get_post_meta($post_id, '_iwj_google_plus', true);
                            $row[37] = get_post_meta($post_id, '_iwj_youtube', true);
                            $row[38] = get_post_meta($post_id, '_iwj_vimeo', true);
                            $row[39] = get_post_meta($post_id, '_iwj_linkedin', true);
                            $row[40] = $gallery_url;
                            $row[41] = get_post_meta($post_id, '_iwj_video', true);
                            $row[42] = $thumbnail_url;
                            $row[43] = $company_size;
                            $row[44] = $employer_cat;
                            $row[45] = $locate;

                            $data_rows[] = $row;
                        endforeach;

                        break;
                }
                $corefield_head = IWJ_Admin_Classes_Tools::coreFields($type_job);
                $extrafield_head = IWJ_Admin_Classes_Tools::extraFields($type_job);
                $termfield_head = IWJ_Admin_Classes_Tools::termFields($type_job);
                $header_row = array_merge($corefield_head, $extrafield_head, $termfield_head);

                $dir_export = $targetDir . DIRECTORY_SEPARATOR . $filename;
                $files[] = $dir_export;
                $fh = @fopen($dir_export, 'w');
                //fprintf( $fh, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );
                fputcsv($fh, $header_row, ',', chr(0));
                foreach ($data_rows as $data_row) {
                    fputcsv($fh, $data_row);
                }
                fclose($fh);
            }

            $bundle_dir = $targetDir . DIRECTORY_SEPARATOR . 'jobs-' . time();
            @mkdir($bundle_dir);
            $bundle_zip = $targetDir . DIRECTORY_SEPARATOR . 'jobs-' . time() . '.zip';

            foreach ($files as $file) {
                @copy($file, $bundle_dir . DIRECTORY_SEPARATOR . basename($file));
                unlink($file);
            }

            iwj_zipDir($bundle_dir, $bundle_zip);

            $download_zip = $targetUrl . DIRECTORY_SEPARATOR . 'jobs-' . time() . '.zip';
            array_map('unlink', glob("$bundle_dir/*.*"));
            rmdir($bundle_dir);
            echo json_encode(array(
                'success' => true,
                'message' => __('Export successfully.', 'iwjob'),
                'path_down' => $download_zip,
            ));
        } else {
            $args = array(
                'posts_per_page' => - 1,
                'orderby' => 'date',
                'order' => 'DESC',
                'post_type' => $job_type[0]
            );

            switch ($job_type[0]) {
                case 'iwj_job':
                    $args['post_status'] = array(
                        'publish',
                        'pending',
                        'draft',
                        'iwj-rejected',
                        'iwj-pending-payment'
                    );
                    break;
                case 'iwj_candidate':
                case 'iwj_employer':
                    $args['post_status'] = array(
                        'publish',
                        'pending',
                        'iwj-incomplete'
                    );
                    break;
            }

            $getposts = get_posts($args);

            if (!count($getposts)) {
                return false;
            }

            $filename = '';
            $data_rows = array();
            switch ($job_type[0]) {
                case 'iwj_job':
                    $filename = 'all-jobs-' . time() . '.csv';
                    foreach ($getposts as $post) : setup_postdata($post);
                        $post_id = $post->ID;

                        $job_locations = wp_get_post_terms($post_id, 'iwj_location');
                        if ($job_locations) {
                            $arr_locations = array();
                            foreach ($job_locations as $job_location) {
                                $arr_locations[$job_location->parent] = $job_location->slug;
                            }
                            ksort($arr_locations);
                            $locate = implode('>', $arr_locations);
                        } else {
                            $locate = '';
                        }

                        $skills = wp_get_post_terms($post_id, 'iwj_skill');
                        if ($skills) {
                            $arr_skills = array();
                            foreach ($skills as $skill) {
                                $arr_skills[] = $skill->slug;
                            }
                            $job_skill = implode('|', $arr_skills);
                        } else {
                            $job_skill = '';
                        }

                        $types = wp_get_post_terms($post_id, 'iwj_type');
                        if ($types) {
                            $arr_types = array();
                            foreach ($types as $type) {
                                $arr_types[] = $type->slug;
                            }
                            $job_type_now = implode('|', $arr_types);
                        } else {
                            $job_type_now = '';
                        }

                        $levels = wp_get_post_terms($post_id, 'iwj_level');
                        if ($levels) {
                            $arr_levels = array();
                            foreach ($levels as $level) {
                                $arr_levels[] = $level->slug;
                            }
                            $job_level = implode('|', $arr_levels);
                        } else {
                            $job_level = '';
                        }

                        $cats = wp_get_post_terms($post_id, 'iwj_cat');
                        if ($cats) {
                            $arr_cats = array();
                            foreach ($cats as $cat) {
                                $arr_cats[] = $cat->slug;
                            }
                            $job_cat = implode('|', $arr_cats);
                        } else {
                            $job_cat = '';
                        }

                        $_job_expiry = get_post_meta($post_id, '_iwj_expiry', true);
                        $_iwj_expiry = $_job_expiry ? date('Y-m-d H:i:s', $_job_expiry) : '';
                        $_job_featured_date = get_post_meta($post_id, '_iwj_featured_date', true);
                        $_iwj_featured_date = $_job_featured_date ? date('Y-m-d H:i:s', $_job_featured_date) : '';
                        $_job_deadline = get_post_meta($post_id, '_iwj_deadline', true);
                        $_iwj_deadline = $_job_deadline ? date('Y-m-d H:i:s', $_job_deadline) : '';

                        $row = array();
                        $row[0] = $post_id;
                        $row[1] = $post->post_author;
                        $row[2] = $post->post_date;
                        $row[3] = $post->post_date_gmt;
                        $row[4] = $post->post_content;
                        $row[5] = $post->post_title;
                        $row[6] = $post->post_excerpt;
                        $row[7] = $post->post_status;
                        $row[8] = $post->post_name;
                        $row[9] = $post->post_modified;
                        $row[10] = $post->post_modified_gmt;
                        $row[11] = $post->post_content_filtered;
                        $row[12] = $post->post_parent;
                        $row[13] = $post->guid;
                        $row[14] = $post->menu_order;
                        $row[15] = $post->post_type;
                        $row[16] = $post->post_mime_type;
                        $row[17] = $post->comment_count;
                        $row[18] = $_iwj_expiry;
                        $row[19] = $_iwj_deadline;
                        $row[20] = get_post_meta($post_id, '_iwj_featured', true);
                        $row[21] = $_iwj_featured_date;
                        $row[22] = get_post_meta($post_id, '_iwj_email_application', true);
                        $row[23] = json_encode(get_post_meta($post_id, '_iwj_job_gender'));
                        $row[24] = json_encode(get_post_meta($post_id, '_iwj_job_languages'));
                        $row[25] = get_post_meta($post_id, '_iwj_salary_from', true);
                        $row[26] = get_post_meta($post_id, '_iwj_salary_to', true);
                        $row[27] = get_post_meta($post_id, '_iwj_salary_postfix', true);
                        $row[28] = get_post_meta($post_id, '_iwj_currency', true);
                        $row[29] = get_post_meta($post_id, '_iwj_address', true);
                        $row[30] = get_post_meta($post_id, '_iwj_map', true);
                        $row[31] = get_post_meta($post_id, '_iwj_reason', true);
                        $row[32] = get_post_meta($post_id, '_iwj_views', true);
                        $row[33] = get_post_meta($post_id, '_iwj_custom_apply_url', true);
                        $row[34] = get_post_meta($post_id, '_iwj_user_package_id', true);
                        $row[35] = get_post_meta($post_id, 'import_source', true);
                        $row[36] = get_post_meta($post_id, 'import_url', true);
                        $row[37] = get_post_meta($post_id, 'import_company', true);
                        $row[38] = get_post_meta($post_id, '_iwj_free_job', true);
                        $row[39] = get_post_meta($post_id, '_iwj_is_new_featured', true);
                        $row[40] = get_post_meta($post_id, '_iwj_is_new_publish', true);
                        $row[41] = $job_type_now;
                        $row[42] = $job_cat;
                        $row[43] = $job_skill;
                        $row[44] = $job_level;
                        $row[45] = $locate;

                        $data_rows[] = $row;
                    endforeach;

                    break;
                case 'iwj_candidate':
                    $filename = 'all-candidates-' . time() . '.csv';
                    foreach ($getposts as $post) : setup_postdata($post);
                        $post_id = $post->ID;
                        $candidate_id = $post->post_author;
                        $user_info = get_userdata($candidate_id);

                        $thumbnail_id = get_post_meta($post_id, '_thumbnail_id', true);
                        $thumbnail_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
                        $candidate_galleries = get_post_meta($post_id, '_iwj_gallery');
                        if ($candidate_galleries) {
                            $gallery_arr = array();
                            foreach ($candidate_galleries as $candidate_gallery) {
                                $gallery_arr[] = wp_get_attachment_url($candidate_gallery);
                            }
                            $gallery_url = implode('|', $gallery_arr);
                        } else {
                            $gallery_url = '';
                        }
                        $cv_id = get_post_meta($post_id, '_iwj_cv', true);
                        $cv_url = $cv_id ? wp_get_attachment_url($cv_id) : '';

                        $types = wp_get_post_terms($post_id, 'iwj_type');
                        if ($types) {
                            $arr_types = array();
                            foreach ($types as $type) {
                                $arr_types[] = $type->slug;
                            }
                            $candidate_type = implode('|', $arr_types);
                        } else {
                            $candidate_type = '';
                        }

                        $cats = wp_get_post_terms($post_id, 'iwj_cat');
                        if ($cats) {
                            $arr_cats = array();
                            foreach ($cats as $cat) {
                                $arr_cats[] = $cat->slug;
                            }
                            $candidate_cat = implode('|', $arr_cats);
                        } else {
                            $candidate_cat = '';
                        }

                        $skills = wp_get_post_terms($post_id, 'iwj_skill');
                        if ($skills) {
                            $arr_skills = array();
                            foreach ($skills as $skill) {
                                $arr_skills[] = $skill->slug;
                            }
                            $candidate_skill = implode('|', $arr_skills);
                        } else {
                            $candidate_skill = '';
                        }

                        $levels = wp_get_post_terms($post_id, 'iwj_level');
                        if ($levels) {
                            $arr_levels = array();
                            foreach ($levels as $level) {
                                $arr_levels[] = $level->slug;
                            }
                            $candidate_level = implode('|', $arr_levels);
                        } else {
                            $candidate_level = '';
                        }

                        $candidate_locations = wp_get_post_terms($post_id, 'iwj_location');
                        if ($candidate_locations) {
                            $arr_locations = array();
                            foreach ($candidate_locations as $candidate_location) {
                                $arr_locations[$candidate_location->parent] = $candidate_location->name;
                            }
                            ksort($arr_locations);
                            $locate = implode('>', $arr_locations);
                        } else {
                            $locate = '';
                        }

                        $candidate_birthday = get_post_meta($post_id, '_iwj_birthday', true);
                        $_iwj_birthday = $candidate_birthday ? date('Y-m-d H:i:s', $candidate_birthday) : '';

                        $row = array();
                        $row[0] = $post_id;
                        $row[1] = $post->post_author;
                        $row[2] = $post->post_date;
                        $row[3] = $post->post_date_gmt;
                        $row[4] = $post->post_content;
                        $row[5] = $post->post_title;
                        $row[6] = $post->post_excerpt;
                        $row[7] = $post->post_status;
                        $row[8] = $post->post_name;
                        $row[9] = $post->post_modified;
                        $row[10] = $post->post_modified_gmt;
                        $row[11] = $post->post_content_filtered;
                        $row[12] = $post->post_parent;
                        $row[13] = $post->guid;
                        $row[14] = $post->menu_order;
                        $row[15] = $post->post_type;
                        $row[16] = $post->post_mime_type;
                        $row[17] = $post->comment_count;
                        $row[18] = $user_info->user_login;
                        $row[19] = $user_info->user_pass;
                        $row[20] = $user_info->user_nicename;
                        $row[21] = $user_info->user_email;
                        $row[22] = $user_info->user_url;
                        $row[23] = $user_info->user_registered;
                        $row[24] = $user_info->user_activation_key;
                        $row[25] = $user_info->user_status;
                        $row[26] = $user_info->display_name;
                        $row[27] = get_post_meta($post_id, '_iwj_headline', true);
                        $row[28] = $_iwj_birthday;
                        $row[29] = get_post_meta($post_id, '_iwj_views', true);
                        $row[30] = get_post_meta($post_id, '_iwj_address', true);
                        $row[31] = get_post_meta($post_id, '_iwj_gender', true);
                        $row[32] = json_encode(get_post_meta($post_id, '_iwj_languages'));
                        $row[33] = get_post_meta($post_id, '_iwj_reason', true);
                        $row[34] = get_post_meta($post_id, '_iwj_public_account', true);
                        $row[35] = get_post_meta($post_id, '_iwj_phone', true);
                        $row[36] = get_post_meta($post_id, '_iwj_map', true);
                        $row[37] = get_post_meta($post_id, '_iwj_facebook', true);
                        $row[38] = get_post_meta($post_id, '_iwj_twitter', true);
                        $row[39] = get_post_meta($post_id, '_iwj_google_plus', true);
                        $row[40] = get_post_meta($post_id, '_iwj_youtube', true);
                        $row[41] = get_post_meta($post_id, '_iwj_vimeo', true);
                        $row[42] = get_post_meta($post_id, '_iwj_linkedin', true);
                        $row[43] = serialize(get_post_meta($post_id, '_iwj_experience', true));
                        $row[44] = serialize(get_post_meta($post_id, '_iwj_education', true));
                        $row[45] = serialize(get_post_meta($post_id, '_iwj_skill_showcase', true));
                        $row[46] = $gallery_url;
                        $row[47] = get_post_meta($post_id, '_iwj_video', true);
                        $row[48] = serialize(get_post_meta($post_id, '_iwj_award', true));
                        $row[49] = $cv_url;
                        $row[50] = get_post_meta($post_id, '_iwj_cover_letter', true);
                        $row[51] = $thumbnail_url;
                        $row[52] = $candidate_type;
                        $row[53] = $candidate_cat;
                        $row[54] = $candidate_skill;
                        $row[55] = $candidate_level;
                        $row[56] = $locate;

                        $data_rows[] = $row;
                    endforeach;

                    break;
                case 'iwj_employer':
                    $filename = 'all-employers-' . time() . '.csv';
                    foreach ($getposts as $post) : setup_postdata($post);
                        $post_id = $post->ID;
                        $employer_id = $post->post_author;
                        $user_info_e = get_userdata($employer_id);
                        $thumbnail_id = get_post_meta($post_id, '_thumbnail_id', true);
                        $thumbnail_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';

                        $employer_galleries = get_post_meta($post_id, '_iwj_gallery');
                        if ($employer_galleries) {
                            $gallery_arr = array();
                            foreach ($employer_galleries as $employer_gallery) {
                                $gallery_arr[] = wp_get_attachment_url($employer_gallery);
                            }
                            $gallery_url = implode('|', $gallery_arr);
                        } else {
                            $gallery_url = '';
                        }

                        $company_sizes = wp_get_post_terms($post_id, 'iwj_size');
                        if ($company_sizes) {
                            $company_sizes = $company_sizes[0];
                            $company_size = $company_sizes->slug;
                        } else {
                            $company_size = '';
                        }

                        $cats = wp_get_post_terms($post_id, 'iwj_cat');
                        if ($cats) {
                            $arr_cats = array();
                            foreach ($cats as $cat) {
                                $arr_cats[] = $cat->slug;
                            }
                            $employer_cat = implode('|', $arr_cats);
                        } else {
                            $employer_cat = '';
                        }

                        $employer_locations = wp_get_post_terms($post_id, 'iwj_location');
                        if ($employer_locations) {
                            $arr_locations = array();
                            foreach ($employer_locations as $employer_location) {
                                $arr_locations[$employer_location->parent] = $employer_location->slug;
                            }
                            ksort($arr_locations);
                            $locate = implode('>', $arr_locations);
                        } else {
                            $locate = '';
                        }

                        $row = array();
                        $row[0] = $post_id;
                        $row[1] = $post->post_author;
                        $row[2] = $post->post_date;
                        $row[3] = $post->post_date_gmt;
                        $row[4] = $post->post_content;
                        $row[5] = $post->post_title;
                        $row[6] = $post->post_excerpt;
                        $row[7] = $post->post_status;
                        $row[8] = $post->post_name;
                        $row[9] = $post->post_modified;
                        $row[10] = $post->post_modified_gmt;
                        $row[11] = $post->post_content_filtered;
                        $row[12] = $post->post_parent;
                        $row[13] = $post->guid;
                        $row[14] = $post->menu_order;
                        $row[15] = $post->post_type;
                        $row[16] = $post->post_mime_type;
                        $row[17] = $post->comment_count;
                        $row[18] = $user_info_e->user_login;
                        $row[19] = $user_info_e->user_pass;
                        $row[20] = $user_info_e->user_nicename;
                        $row[21] = $user_info_e->user_email;
                        $row[22] = $user_info_e->user_url;
                        $row[23] = $user_info_e->user_registered;
                        $row[24] = $user_info_e->user_activation_key;
                        $row[25] = $user_info_e->user_status;
                        $row[26] = $user_info_e->display_name;
                        $row[27] = get_post_meta($post_id, '_iwj_headline', true);
                        $row[28] = get_post_meta($post_id, '_iwj_views', true);
                        $row[29] = get_post_meta($post_id, '_iwj_map', true);
                        $row[30] = get_post_meta($post_id, '_iwj_address', true);
                        $row[31] = get_post_meta($post_id, '_iwj_reason', true);
                        $row[32] = get_post_meta($post_id, '_iwj_phone', true);
                        $row[33] = get_post_meta($post_id, '_iwj_founded_date', true);
                        $row[34] = get_post_meta($post_id, '_iwj_facebook', true);
                        $row[35] = get_post_meta($post_id, '_iwj_twitter', true);
                        $row[36] = get_post_meta($post_id, '_iwj_google_plus', true);
                        $row[37] = get_post_meta($post_id, '_iwj_youtube', true);
                        $row[38] = get_post_meta($post_id, '_iwj_vimeo', true);
                        $row[39] = get_post_meta($post_id, '_iwj_linkedin', true);
                        $row[40] = $gallery_url;
                        $row[41] = get_post_meta($post_id, '_iwj_video', true);
                        $row[42] = $thumbnail_url;
                        $row[43] = $company_size;
                        $row[44] = $employer_cat;
                        $row[45] = $locate;

                        $data_rows[] = $row;
                    endforeach;

                    break;
            }
            $corefield_head = IWJ_Admin_Classes_Tools::coreFields($job_type[0]);
            $extrafield_head = IWJ_Admin_Classes_Tools::extraFields($job_type[0]);
            $termfield_head = IWJ_Admin_Classes_Tools::termFields($job_type[0]);
            $header_row = array_merge($corefield_head, $extrafield_head, $termfield_head);

            $dir_export = $targetDir . DIRECTORY_SEPARATOR . $filename;
            $fh = @fopen($dir_export, 'w');
            //fprintf( $fh, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );
            fputcsv($fh, $header_row, ',', chr(0));
            foreach ($data_rows as $data_row) {
                fputcsv($fh, $data_row);
            }
            fclose($fh);

            $download_url = $targetUrl . DIRECTORY_SEPARATOR . $filename;

            echo json_encode(array(
                'success' => true,
                'message' => __('Export successfully.', 'iwjob'),
                'path_down' => $download_url
            ));
        }

        exit;
    }

    static function upload_csv_actions() {
        include_once 'job-imports-upload-handler.php';
        die;
    }

    static function set_post_types() {
        $parserObj = new SmackCSVParser();
        $eventKey = isset($_POST['filekey']) ? sanitize_key($_POST['filekey']) : '';
        //$uploadedname = isset( $_POST['uploadedname'] ) ? sanitize_text_field( $_POST['uploadedname'] ) : '';
        $file = IWJ_IMPORT_DIR . '/' . $eventKey . '/' . $eventKey;
        $parserObj->parseCSV($file, 0, - 1);
        $Headers = $parserObj->get_CSVheaders();
        $Headers = $Headers[0];
        $type = 'iwj_job';
        if (in_array('iwj_candidate', $Headers)) {
            $type = 'iwj_candidate';
        } elseif (in_array('iwj_employer', $Headers)) {
            $type = 'iwj_employer';
        }

        echo json_encode(array(
            'success' => true,
            'type' => $type
        ));
        die();
    }

    static function parse_data_to_import() {

        $startLimit = intval($_POST['postData']['startLimit']);
        $endLimit = intval($_POST['postData']['endLimit']);
        $limit = intval($_POST['postData']['Limit']);
        $totalCount = intval($_POST['postData']['totalcount']);

        $importType = sanitize_text_field($_POST['postData']['import_type']);
        $import_mode = sanitize_text_field($_POST['postData']['import_mode']);
        $eventKey = sanitize_text_field($_POST['postData']['event_key']);
        $parserObj = new SmackCSVParser();
        $get_screen_info = IWJ_Admin_Classes_Tools::getPostValues(sanitize_key($eventKey));
        $mapping_config = $get_screen_info[$eventKey]['mapping_config'];
        $eventDir = IWJ_IMPORT_DIR . '/' . $eventKey;
        $eventFile = $eventDir . '/' . $eventKey;
        $core_fields_count = $mapping_config['core_fields_count'];
        $extra_fields_count = $mapping_config['extra_fields_count'];
        $term_fields_count = $mapping_config['term_fields_count'];
        $number_fields = intval($core_fields_count) + intval($extra_fields_count) + intval($term_fields_count);

        $data = $parserObj->parseCSV($eventFile, $startLimit, $limit);

        for ($i = $startLimit; $i < $endLimit; $i ++) {
            try {
                $args_cores = array();
                $args_extras = array();
                $terms_fs = array();
                for ($j = 0; $j < $number_fields; $j ++) {
                    if (isset($mapping_config['iwj_fieldname_core_fields' . $j]) && !empty($mapping_config['iwj_fieldname_core_fields' . $j]) && isset($mapping_config['iwj_mapping_core_fields' . $j])) {
                        $args_cores[$mapping_config['iwj_fieldname_core_fields' . $j]] = $data[$i][$mapping_config['iwj_mapping_core_fields' . $j]];
                    }
                    if (isset($mapping_config['iwj_fieldname_extra_fields' . $j]) && !empty($mapping_config['iwj_fieldname_extra_fields' . $j]) && isset($mapping_config['iwj_mapping_extra_fields' . $j])) {
                        $args_extras[$mapping_config['iwj_fieldname_extra_fields' . $j]] = $data[$i][$mapping_config['iwj_mapping_extra_fields' . $j]];
                    }
                    if (isset($mapping_config['iwj_fieldname_term_fields' . $j]) && !empty($mapping_config['iwj_fieldname_term_fields' . $j]) && isset($mapping_config['iwj_fieldname_term_fields' . $j])) {
                        $terms_fs[$mapping_config['iwj_fieldname_term_fields' . $j]] = $data[$i][$mapping_config['iwj_fieldname_term_fields' . $j]];
                    }
                }

                $check = 0;
                switch ($importType) {
                    case 'iwj_candidate':
                        if ($args_cores['user_login'] && $args_cores['user_email']) {
                            if (username_exists($args_cores['user_login']) || email_exists($args_cores['user_email'])) {
                                if ($import_mode == 'update_existing_items') {
                                    $check = 1;
                                }
                            } else {
                                $check = 2;
                            }
                        } else {
                            $check = 2;
                        }
                        $core_users = array(
                            'ID' => $args_cores['post_author'],
                            'user_login' => $args_cores['user_login'],
                            'user_pass' => $args_cores['user_pass'],
                            'user_nicename' => $args_cores['user_nicename'],
                            'user_email' => $args_cores['user_email'],
                            'user_url' => $args_cores['user_url'],
                            'user_registered' => $args_cores['user_registered'],
                            'user_activation_key' => $args_cores['user_activation_key'],
                            'user_status' => $args_cores['user_status'],
                            'display_name' => $args_cores['display_name'],
                        );

                        if ($check == 1) {
                            $user_id = wp_update_user($core_users);
                            if ($user_id) {
                                $post_type_id = get_user_meta($user_id, IWJ_PREFIX . 'candidate_post', true);
                                $args_cores['ID'] = $post_type_id;
                                $args_cores['post_author'] = $user_id;
                                wp_update_post($args_cores);
                                foreach ($args_extras as $key_extra => $extra_field) {
                                    if ($key_extra == '_iwj_experience' || $key_extra == '_iwj_education' || $key_extra == '_iwj_skill_showcase' || $key_extra == '_iwj_award') {
                                        add_post_meta($post_type_id, $key_extra, unserialize($extra_field));
                                        continue;
                                    }
                                    if ($key_extra == '_iwj_languages') {
                                        $extra_field1 = json_decode($extra_field);
                                        if ($extra_field1) {
                                            delete_post_meta($args_cores['ID'], $key_extra);
                                            foreach ($extra_field1 as $ex_json) {
                                                add_post_meta($args_cores['ID'], $key_extra, $ex_json);
                                            }
                                        }
                                        continue;
                                    }
                                    if ($key_extra == '_iwj_gallery') {
                                        $galleries_url = explode('|', $extra_field);
                                        $galleries_url = array_filter($galleries_url);
                                        if ($galleries_url) {
                                            foreach ($galleries_url as $gallery_url) {
                                                if (is_numeric($galleries_url)) {
                                                    add_post_meta($args_cores['ID'], $key_extra, $gallery_url);
                                                } else {
                                                    $image_exist = wp_get_image_editor($gallery_url);
                                                    if (!is_wp_error($image_exist)) {
                                                        $attachment_id = iwj_get_image_id_by_url($gallery_url);
                                                        if ($attachment_id) {
                                                            add_post_meta($args_cores['ID'], $key_extra, $attachment_id);
                                                        }
                                                    } else {
                                                        $file_name = basename($gallery_url);
                                                        $upload_file = wp_upload_bits($file_name, null, file_get_contents($gallery_url));
                                                        if (!$upload_file['error']) {
                                                            $wp_filetype = wp_check_filetype($file_name, null);
                                                            $attachment = array(
                                                                'post_mime_type' => $wp_filetype['type'],
                                                                'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
                                                                'post_content' => '',
                                                                'post_status' => 'inherit'
                                                            );
                                                            $gallery_id = wp_insert_attachment($attachment, $upload_file['file'], $args_cores['ID']);
                                                            if (!is_wp_error($gallery_id)) {
                                                                require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
                                                                $attachment_data = wp_generate_attachment_metadata($gallery_id, $upload_file['file']);
                                                                wp_update_attachment_metadata($gallery_id, $attachment_data);
                                                                add_post_meta($args_cores['ID'], $key_extra, $gallery_id);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        continue;
                                    }
                                    if ($key_extra == '_thumbnail_id' && $extra_field) {
                                        if (is_numeric($extra_field)) {
                                            update_user_meta($user_id, IWJ_PREFIX . 'avatar', $extra_field);
                                            update_post_meta($post_type_id, $key_extra, $extra_field);
                                        } else {
                                            $image_exist = wp_get_image_editor($extra_field);
                                            if (!is_wp_error($image_exist)) {
                                                $attachment_id = iwj_get_image_id_by_url($extra_field);
                                                if ($attachment_id) {
                                                    update_user_meta($user_id, IWJ_PREFIX . 'avatar', $attachment_id);
                                                    update_post_meta($post_type_id, $key_extra, $attachment_id);
                                                }
                                            } else {
                                                $file_name = basename($extra_field);
                                                $upload_file = wp_upload_bits($file_name, null, file_get_contents($extra_field));
                                                if (!$upload_file['error']) {
                                                    $wp_filetype = wp_check_filetype($file_name, null);
                                                    $attachment = array(
                                                        'post_mime_type' => $wp_filetype['type'],
                                                        'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
                                                        'post_content' => '',
                                                        'post_status' => 'inherit'
                                                    );
                                                    $avatar_id = wp_insert_attachment($attachment, $upload_file['file'], $args_cores['ID']);
                                                    if (!is_wp_error($avatar_id)) {
                                                        require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
                                                        $attachment_data = wp_generate_attachment_metadata($avatar_id, $upload_file['file']);
                                                        wp_update_attachment_metadata($avatar_id, $attachment_data);
                                                        update_user_meta($user_id, IWJ_PREFIX . 'avatar', $avatar_id);
                                                        update_post_meta($post_type_id, $key_extra, $avatar_id);
                                                    }
                                                }
                                            }
                                        }
                                        continue;
                                    }
                                    if ($key_extra == '_iwj_cv' && $extra_field) {
                                        if (is_numeric($extra_field)) {
                                            update_post_meta($post_type_id, '_iwj_cv', $extra_field);
                                        } else {
                                            $attachment_id = iwj_get_image_id_by_url($extra_field);
                                            if ($attachment_id) {
                                                update_post_meta($post_type_id, '_iwj_cv', $attachment_id);
                                            } else {
                                                $file_name = basename($extra_field);
                                                $upload_file = wp_upload_bits($file_name, null, file_get_contents($extra_field));
                                                if (!$upload_file['error']) {
                                                    $wp_filetype = wp_check_filetype($file_name, null);
                                                    $attachment = array(
                                                        'post_mime_type' => $wp_filetype['type'],
                                                        'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
                                                        'post_content' => '',
                                                        'post_status' => 'inherit'
                                                    );
                                                    $avatar_id = wp_insert_attachment($attachment, $upload_file['file'], $args_cores['ID']);
                                                    if (!is_wp_error($avatar_id)) {
                                                        require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
                                                        $attachment_data = wp_generate_attachment_metadata($avatar_id, $upload_file['file']);
                                                        wp_update_attachment_metadata($avatar_id, $attachment_data);
                                                        update_post_meta($post_type_id, '_iwj_cv', $avatar_id);
                                                    }
                                                }
                                            }
                                        }
                                        continue;
                                    }
                                    if ($key_extra == '_iwj_birthday' && $extra_field) {
                                        $birthday_meta = strtotime($extra_field);
                                        update_post_meta($args_cores['ID'], '_iwj_birthday', $birthday_meta);
                                        continue;
                                    }

                                    update_post_meta($args_cores['ID'], $key_extra, $extra_field);
                                }
                                update_post_meta($args_cores['ID'], 'email', $args_cores['user_email']);
                                if ($terms_fs['iwj_type'] && !iwj_option('disable_type')) {
                                    $iwj_job_types = explode('|', $terms_fs['iwj_type']);
                                    if ($iwj_job_types) {
                                        $term_typearr = array();
                                        foreach ($iwj_job_types as $iwj_job_type) {
                                            $ide_term = get_term_by('slug', $iwj_job_type, 'iwj_type');
                                            if (!$ide_term) {
                                                $new_term = wp_insert_term($iwj_job_type, 'iwj_type');
                                                $ide_term_id = $new_term['term_id'];
                                            } else {
                                                $ide_term_id = $ide_term->term_id;
                                            }
                                            $term_typearr[] = $ide_term_id;
                                        }
                                        wp_set_post_terms($args_cores['ID'], $term_typearr, 'iwj_type');
                                    }
                                }
                                if ($terms_fs['iwj_cat']) {
                                    $iwj_job_cats = explode('|', $terms_fs['iwj_cat']);
                                    if ($iwj_job_cats) {
                                        $terms_catarr = array();
                                        foreach ($iwj_job_cats as $iwj_job_cat) {
                                            $ide_term = get_term_by('slug', $iwj_job_cat, 'iwj_cat');
                                            if (!$ide_term) {
                                                $new_term = wp_insert_term($iwj_job_cat, 'iwj_cat');
                                                $ide_term_id = $new_term['term_id'];
                                            } else {
                                                $ide_term_id = $ide_term->term_id;
                                            }
                                            $terms_catarr[] = $ide_term_id;
                                        }
                                        wp_set_post_terms($args_cores['ID'], $terms_catarr, 'iwj_cat');
                                    }
                                }
                                if ($terms_fs['iwj_skill'] && !iwj_option('disable_skill')) {
                                    $iwj_job_skills = explode('|', $terms_fs['iwj_skill']);
                                    if ($iwj_job_skills) {
                                        $terms_skillarr = array();
                                        foreach ($iwj_job_skills as $iwj_job_skill) {
                                            $ide_term = get_term_by('slug', $iwj_job_skill, 'iwj_skill');
                                            if (!$ide_term) {
                                                $new_term = wp_insert_term($iwj_job_skill, 'iwj_skill');
                                                $ide_term_id = $new_term['term_id'];
                                            } else {
                                                $ide_term_id = $ide_term->term_id;
                                            }
                                            $terms_skillarr[] = $ide_term_id;
                                        }
                                        wp_set_post_terms($args_cores['ID'], $terms_skillarr, 'iwj_skill');
                                    }
                                }
                                if ($terms_fs['iwj_level'] && !iwj_option('disable_level')) {
                                    $iwj_job_levels = explode('|', $terms_fs['iwj_level']);
                                    if ($iwj_job_levels) {
                                        $terms_levelarr = array();
                                        foreach ($iwj_job_levels as $iwj_job_level) {
                                            $ide_term = get_term_by('slug', $iwj_job_level, 'iwj_level');
                                            if (!$ide_term) {
                                                $new_term = wp_insert_term($iwj_job_level, 'iwj_level');
                                                $ide_term_id = $new_term['term_id'];
                                            } else {
                                                $ide_term_id = $ide_term->term_id;
                                            }
                                            $terms_levelarr[] = $ide_term_id;
                                        }
                                        wp_set_post_terms($args_cores['ID'], $terms_levelarr, 'iwj_level');
                                    }
                                }
                                if ($terms_fs['iwj_location']) {
                                    $iwj_job_locations = explode('>', $terms_fs['iwj_location']);
                                    if ($iwj_job_locations) {
                                        $terms_locatearr = array();
                                        foreach ($iwj_job_locations as $iwj_job_location) {
                                            $ide_term = get_term_by('slug', $iwj_job_location, 'iwj_location');
                                            if (!$ide_term) {
                                                $new_term = wp_insert_term($iwj_job_location, 'iwj_location');
                                                $ide_term_id = $new_term['term_id'];
                                            } else {
                                                $ide_term_id = $ide_term->term_id;
                                            }
                                            $terms_locatearr[] = $ide_term_id;
                                        }
                                        wp_set_post_terms($args_cores['ID'], $terms_locatearr, 'iwj_location');
                                    }
                                }
                            }
                        } elseif ($check == 2) {
                            if ($core_users['ID']) {
                                unset($core_users['ID']);
                            }
                            $core_users['role'] = 'iwj_candidate';
                            $candidate_id = wp_insert_user($core_users);
                            if ($candidate_id) {
                                $post_type_id = get_user_meta($candidate_id, IWJ_PREFIX . 'candidate_post', true);
                                if ($post_type_id) {
                                    $args_cores['ID'] = $post_type_id;
                                    $args_cores['post_author'] = $candidate_id;
                                    wp_update_post($args_cores);
                                    foreach ($args_extras as $key_extra => $extra_field) {
                                        if ($key_extra == '_iwj_experience' || $key_extra == '_iwj_education' || $key_extra == '_iwj_skill_showcase' || $key_extra == '_iwj_award') {
                                            add_post_meta($post_type_id, $key_extra, unserialize($extra_field));
                                            continue;
                                        }
                                        if ($key_extra == '_iwj_languages') {
                                            $extra_field1 = json_decode($extra_field);
                                            if ($extra_field1) {
                                                delete_post_meta($post_type_id, $key_extra);
                                                foreach ($extra_field1 as $ex_json) {
                                                    add_post_meta($post_type_id, $key_extra, $ex_json);
                                                }
                                            }
                                            continue;
                                        }
                                        if ($key_extra == '_iwj_gallery') {
                                            $galleries_url = explode('|', $extra_field);
                                            $galleries_url = array_filter($galleries_url);
                                            if ($galleries_url) {
                                                foreach ($galleries_url as $gallery_url) {
                                                    if (is_numeric($galleries_url)) {
                                                        add_post_meta($post_type_id, $key_extra, $gallery_url);
                                                    } else {
                                                        $image_exist = wp_get_image_editor($gallery_url);
                                                        if (!is_wp_error($image_exist)) {
                                                            $attachment_id = iwj_get_image_id_by_url($gallery_url);
                                                            if ($attachment_id) {
                                                                add_post_meta($post_type_id, $key_extra, $attachment_id);
                                                            }
                                                        } else {
                                                            $file_name = basename($gallery_url);
                                                            $upload_file = wp_upload_bits($file_name, null, file_get_contents($gallery_url));
                                                            if (!$upload_file['error']) {
                                                                $wp_filetype = wp_check_filetype($file_name, null);
                                                                $attachment = array(
                                                                    'post_mime_type' => $wp_filetype['type'],
                                                                    'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
                                                                    'post_content' => '',
                                                                    'post_status' => 'inherit'
                                                                );
                                                                $gallery_id = wp_insert_attachment($attachment, $upload_file['file'], $args_cores['ID']);
                                                                if (!is_wp_error($gallery_id)) {
                                                                    require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
                                                                    $attachment_data = wp_generate_attachment_metadata($gallery_id, $upload_file['file']);
                                                                    wp_update_attachment_metadata($gallery_id, $attachment_data);
                                                                    add_post_meta($post_type_id, $key_extra, $gallery_id);
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            continue;
                                        }
                                        if ($key_extra == '_thumbnail_id' && $extra_field) {
                                            if (is_numeric($extra_field)) {
                                                update_user_meta($candidate_id, IWJ_PREFIX . 'avatar', $extra_field);
                                                update_post_meta($post_type_id, '_thumbnail_id', $extra_field);
                                            } else {
                                                $image_exist = wp_get_image_editor($extra_field);
                                                if (!is_wp_error($image_exist)) {
                                                    $attachment_id = iwj_get_image_id_by_url($extra_field);
                                                    if ($attachment_id) {
                                                        update_user_meta($candidate_id, IWJ_PREFIX . 'avatar', $attachment_id);
                                                        update_post_meta($post_type_id, '_thumbnail_id', $attachment_id);
                                                    }
                                                } else {
                                                    $file_name = basename($extra_field);
                                                    $upload_file = wp_upload_bits($file_name, null, file_get_contents($extra_field));
                                                    if (!$upload_file['error']) {
                                                        $wp_filetype = wp_check_filetype($file_name, null);
                                                        $attachment = array(
                                                            'post_mime_type' => $wp_filetype['type'],
                                                            'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
                                                            'post_content' => '',
                                                            'post_status' => 'inherit'
                                                        );
                                                        $avatar_id = wp_insert_attachment($attachment, $upload_file['file'], $args_cores['ID']);
                                                        if (!is_wp_error($avatar_id)) {
                                                            require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
                                                            $attachment_data = wp_generate_attachment_metadata($avatar_id, $upload_file['file']);
                                                            wp_update_attachment_metadata($avatar_id, $attachment_data);
                                                            update_user_meta($candidate_id, IWJ_PREFIX . 'avatar', $avatar_id);
                                                            update_post_meta($post_type_id, $key_extra, $avatar_id);
                                                        }
                                                    }
                                                }
                                            }
                                            continue;
                                        }
                                        if ($key_extra == '_iwj_cv' && $extra_field) {
                                            if (is_numeric($extra_field)) {
                                                update_post_meta($post_type_id, '_iwj_cv', $extra_field);
                                            } else {
                                                $attachment_id = iwj_get_image_id_by_url($extra_field);
                                                if ($attachment_id) {
                                                    update_post_meta($post_type_id, '_iwj_cv', $attachment_id);
                                                } else {
                                                    $file_name = basename($extra_field);
                                                    $upload_file = wp_upload_bits($file_name, null, file_get_contents($extra_field));
                                                    if (!$upload_file['error']) {
                                                        $wp_filetype = wp_check_filetype($file_name, null);
                                                        $attachment = array(
                                                            'post_mime_type' => $wp_filetype['type'],
                                                            'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
                                                            'post_content' => '',
                                                            'post_status' => 'inherit'
                                                        );
                                                        $avatar_id = wp_insert_attachment($attachment, $upload_file['file'], $args_cores['ID']);
                                                        if (!is_wp_error($avatar_id)) {
                                                            require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
                                                            $attachment_data = wp_generate_attachment_metadata($avatar_id, $upload_file['file']);
                                                            wp_update_attachment_metadata($avatar_id, $attachment_data);
                                                            update_post_meta($post_type_id, '_iwj_cv', $avatar_id);
                                                        }
                                                    }
                                                }
                                            }
                                            continue;
                                        }
                                        if ($key_extra == '_iwj_birthday' && $extra_field) {
                                            $birthday_meta = strtotime($extra_field);
                                            update_post_meta($post_type_id, '_iwj_birthday', $birthday_meta);
                                            continue;
                                        }

                                        update_post_meta($post_type_id, $key_extra, $extra_field);
                                    }
                                    update_post_meta($post_type_id, 'email', $args_cores['user_email']);

                                    if ($terms_fs['iwj_type'] && !iwj_option('disable_type')) {
                                        $iwj_job_types = explode('|', $terms_fs['iwj_type']);
                                        if ($iwj_job_types) {
                                            $term_typearr = array();
                                            foreach ($iwj_job_types as $iwj_job_type) {
                                                $ide_term = get_term_by('slug', $iwj_job_type, 'iwj_type');
                                                if (!$ide_term) {
                                                    $new_term = wp_insert_term($iwj_job_type, 'iwj_type');
                                                    $ide_term_id = $new_term['term_id'];
                                                } else {
                                                    $ide_term_id = $ide_term->term_id;
                                                }
                                                $term_typearr[] = $ide_term_id;
                                            }
                                            wp_set_post_terms($post_type_id, $term_typearr, 'iwj_type');
                                        }
                                    }
                                    if ($terms_fs['iwj_cat']) {
                                        $iwj_job_cats = explode('|', $terms_fs['iwj_cat']);
                                        if ($iwj_job_cats) {
                                            $terms_arr = array();
                                            foreach ($iwj_job_cats as $iwj_job_cat) {
                                                $ide_term = get_term_by('slug', $iwj_job_cat, 'iwj_cat');
                                                if (!$ide_term) {
                                                    $new_term = wp_insert_term($iwj_job_cat, 'iwj_cat');
                                                    $ide_term_id = $new_term['term_id'];
                                                } else {
                                                    $ide_term_id = $ide_term->term_id;
                                                }
                                                $terms_arr[] = $ide_term_id;
                                            }
                                            wp_set_post_terms($post_type_id, $terms_arr, 'iwj_cat');
                                        }
                                    }
                                    if ($terms_fs['iwj_skill'] && !iwj_option('disable_skill')) {
                                        $iwj_job_skills = explode('|', $terms_fs['iwj_skill']);
                                        if ($iwj_job_skills) {
                                            $terms_skillarr = array();
                                            foreach ($iwj_job_skills as $iwj_job_skill) {
                                                $ide_term = get_term_by('slug', $iwj_job_skill, 'iwj_skill');
                                                if (!$ide_term) {
                                                    $new_term = wp_insert_term($iwj_job_skill, 'iwj_skill');
                                                    $ide_term_id = $new_term['term_id'];
                                                } else {
                                                    $ide_term_id = $ide_term->term_id;
                                                }
                                                $terms_skillarr[] = $ide_term_id;
                                            }
                                            wp_set_post_terms($post_type_id, $terms_skillarr, 'iwj_skill');
                                        }
                                    }
                                    if ($terms_fs['iwj_level'] && !iwj_option('disable_level')) {
                                        $iwj_job_levels = explode('|', $terms_fs['iwj_level']);
                                        if ($iwj_job_levels) {
                                            $terms_levelarr = array();
                                            foreach ($iwj_job_levels as $iwj_job_level) {
                                                $ide_term = get_term_by('slug', $iwj_job_level, 'iwj_level');
                                                if (!$ide_term) {
                                                    $new_term = wp_insert_term($iwj_job_level, 'iwj_level');
                                                    $ide_term_id = $new_term['term_id'];
                                                } else {
                                                    $ide_term_id = $ide_term->term_id;
                                                }
                                                $terms_levelarr[] = $ide_term_id;
                                            }
                                            wp_set_post_terms($post_type_id, $terms_levelarr, 'iwj_level');
                                        }
                                    }
                                    if ($terms_fs['iwj_location']) {
                                        $iwj_job_locations = explode('>', $terms_fs['iwj_location']);
                                        if ($iwj_job_locations) {
                                            $terms_locatearr = array();
                                            foreach ($iwj_job_locations as $iwj_job_location) {
                                                $ide_term = get_term_by('slug', $iwj_job_location, 'iwj_location');
                                                if (!$ide_term) {
                                                    $new_term = wp_insert_term($iwj_job_location, 'iwj_location');
                                                    $ide_term_id = $new_term['term_id'];
                                                } else {
                                                    $ide_term_id = $ide_term->term_id;
                                                }
                                                $terms_locatearr[] = $ide_term_id;
                                            }
                                            wp_set_post_terms($post_type_id, $terms_locatearr, 'iwj_location');
                                        }
                                    }
                                }
                            }
                        }
                        break;
                    case 'iwj_employer':
                        if ($args_cores['user_login'] && $args_cores['user_email']) {
                            if (username_exists($args_cores['user_login']) || email_exists($args_cores['user_email'])) {
                                if ($import_mode == 'update_existing_items') {
                                    $check = 1;
                                }
                            } else {
                                $check = 2;
                            }
                        } else {
                            $check = 2;
                        }
                        $core_users = array(
                            'ID' => $args_cores['post_author'],
                            'user_login' => $args_cores['user_login'],
                            'user_pass' => $args_cores['user_pass'],
                            'user_nicename' => $args_cores['user_nicename'],
                            'user_email' => $args_cores['user_email'],
                            'user_url' => $args_cores['user_url'],
                            'user_registered' => $args_cores['user_registered'],
                            'user_activation_key' => $args_cores['user_activation_key'],
                            'user_status' => $args_cores['user_status'],
                            'display_name' => $args_cores['display_name'],
                        );

                        if ($check == 1) {
                            $user_id = wp_update_user($core_users);

                            if ($user_id) {
                                $post_type_id = get_user_meta($user_id, IWJ_PREFIX . 'employer_post', true);
                                $args_cores['ID'] = $post_type_id;
                                $args_cores['post_author'] = $user_id;
                                wp_update_post($args_cores);
                                foreach ($args_extras as $key_extra => $extra_field) {

                                    if ($key_extra == '_iwj_gallery') {
                                        $galleries_url = explode('|', $extra_field);
                                        $galleries_url = array_filter($galleries_url);
                                        if ($galleries_url) {
                                            foreach ($galleries_url as $gallery_url) {
                                                if (is_numeric($galleries_url)) {
                                                    add_post_meta($args_cores['ID'], $key_extra, $gallery_url);
                                                } else {
                                                    $image_exist = wp_get_image_editor($gallery_url);
                                                    if (!is_wp_error($image_exist)) {
                                                        $attachment_id = iwj_get_image_id_by_url($gallery_url);
                                                        if ($attachment_id) {
                                                            add_post_meta($args_cores['ID'], $key_extra, $attachment_id);
                                                        }
                                                    } else {
                                                        $file_name = basename($gallery_url);
                                                        $upload_file = wp_upload_bits($file_name, null, file_get_contents($gallery_url));
                                                        if (!$upload_file['error']) {
                                                            $wp_filetype = wp_check_filetype($file_name, null);
                                                            $attachment = array(
                                                                'post_mime_type' => $wp_filetype['type'],
                                                                'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
                                                                'post_content' => '',
                                                                'post_status' => 'inherit'
                                                            );
                                                            $gallery_id = wp_insert_attachment($attachment, $upload_file['file'], $args_cores['ID']);
                                                            if (!is_wp_error($gallery_id)) {
                                                                require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
                                                                $attachment_data = wp_generate_attachment_metadata($gallery_id, $upload_file['file']);
                                                                wp_update_attachment_metadata($gallery_id, $attachment_data);
                                                                add_post_meta($args_cores['ID'], $key_extra, $gallery_id);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        continue;
                                    }

                                    if ($key_extra == '_thumbnail_id' && $extra_field) {
                                        if (is_numeric($extra_field)) {
                                            update_user_meta($user_id, IWJ_PREFIX . 'avatar', $extra_field);
                                            update_post_meta($post_type_id, $key_extra, $extra_field);
                                        } else {
                                            $image_exist = wp_get_image_editor($extra_field);
                                            if (!is_wp_error($image_exist)) {
                                                $attachment_id = iwj_get_image_id_by_url($extra_field);
                                                if ($attachment_id) {
                                                    update_user_meta($user_id, IWJ_PREFIX . 'avatar', $attachment_id);
                                                    update_post_meta($post_type_id, $key_extra, $attachment_id);
                                                }
                                            } else {
                                                $file_name = basename($extra_field);
                                                $upload_file = wp_upload_bits($file_name, null, file_get_contents($extra_field));
                                                if (!$upload_file['error']) {
                                                    $wp_filetype = wp_check_filetype($file_name, null);
                                                    $attachment = array(
                                                        'post_mime_type' => $wp_filetype['type'],
                                                        'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
                                                        'post_content' => '',
                                                        'post_status' => 'inherit'
                                                    );
                                                    $avatar_id = wp_insert_attachment($attachment, $upload_file['file'], $args_cores['ID']);
                                                    if (!is_wp_error($avatar_id)) {
                                                        require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
                                                        $attachment_data = wp_generate_attachment_metadata($avatar_id, $upload_file['file']);
                                                        wp_update_attachment_metadata($avatar_id, $attachment_data);
                                                        update_user_meta($user_id, IWJ_PREFIX . 'avatar', $avatar_id);
                                                        update_post_meta($post_type_id, $key_extra, $avatar_id);
                                                    }
                                                }
                                            }
                                        }
                                        continue;
                                    }

                                    update_post_meta($args_cores['ID'], $key_extra, $extra_field);
                                }
                                if ($terms_fs['iwj_size']) {
                                    $ide_term = get_term_by('slug', $terms_fs['iwj_size'], 'iwj_size');
                                    if (!$ide_term) {
                                        $new_term = wp_insert_term($terms_fs['iwj_size'], 'iwj_size');
                                        $ide_term_id = $new_term['term_id'];
                                    } else {
                                        $ide_term_id = $ide_term->term_id;
                                    }
                                    wp_set_post_terms($args_cores['ID'], $ide_term_id, 'iwj_size');
                                }
                                if ($terms_fs['iwj_cat']) {
                                    $iwj_job_cats = explode('|', $terms_fs['iwj_cat']);
                                    if ($iwj_job_cats) {
                                        $terms_e_cates = array();
                                        foreach ($iwj_job_cats as $iwj_job_cat) {
                                            $ide_term = get_term_by('slug', $iwj_job_cat, 'iwj_cat');
                                            if (!$ide_term) {
                                                $new_term = wp_insert_term($iwj_job_cat, 'iwj_cat');
                                                $ide_term_id = $new_term['term_id'];
                                            } else {
                                                $ide_term_id = $ide_term->term_id;
                                            }
                                            $terms_e_cates[] = $ide_term_id;
                                        }
                                        wp_set_post_terms($args_cores['ID'], $terms_e_cates, 'iwj_cat');
                                    }
                                }
                                if ($terms_fs['iwj_location']) {
                                    $iwj_job_locations = explode('>', $terms_fs['iwj_location']);
                                    if ($iwj_job_locations) {
                                        $terms_e_locates = array();
                                        foreach ($iwj_job_locations as $iwj_job_location) {
                                            $ide_term = get_term_by('slug', $iwj_job_location, 'iwj_location');
                                            if (!$ide_term) {
                                                $new_term = wp_insert_term($iwj_job_location, 'iwj_location');
                                                $ide_term_id = $new_term['term_id'];
                                            } else {
                                                $ide_term_id = $ide_term->term_id;
                                            }
                                            $terms_e_locates[] = $ide_term_id;
                                        }
                                        wp_set_post_terms($args_cores['ID'], $terms_e_locates, 'iwj_location');
                                    }
                                }
                            }
                        } elseif ($check == 2) {
                            if ($core_users['ID']) {
                                unset($core_users['ID']);
                            }
                            $core_users['role'] = 'iwj_employer';
                            $employer_id = wp_insert_user($core_users);
                            if ($employer_id) {
                                $post_type_id = get_user_meta($employer_id, IWJ_PREFIX . 'employer_post', true);
                                //update_user_meta( $employer_id, '_iwj_avatar', $args_extras['_thumbnail_id'] );
                                if ($post_type_id) {
                                    $args_cores['ID'] = $post_type_id;
                                    $args_cores['post_author'] = $employer_id;
                                    wp_update_post($args_cores);
                                    foreach ($args_extras as $key_extra => $extra_field) {

                                        if ($key_extra == '_iwj_gallery') {
                                            $galleries_url = explode('|', $extra_field);
                                            $galleries_url = array_filter($galleries_url);
                                            if ($galleries_url) {
                                                foreach ($galleries_url as $gallery_url) {
                                                    if (is_numeric($galleries_url)) {
                                                        add_post_meta($post_type_id, $key_extra, $gallery_url);
                                                    } else {
                                                        $image_exist = wp_get_image_editor($gallery_url);
                                                        if (!is_wp_error($image_exist)) {
                                                            $attachment_id = iwj_get_image_id_by_url($gallery_url);
                                                            if ($attachment_id) {
                                                                add_post_meta($post_type_id, $key_extra, $attachment_id);
                                                            }
                                                        } else {
                                                            $file_name = basename($gallery_url);
                                                            $upload_file = wp_upload_bits($file_name, null, file_get_contents($gallery_url));
                                                            if (!$upload_file['error']) {
                                                                $wp_filetype = wp_check_filetype($file_name, null);
                                                                $attachment = array(
                                                                    'post_mime_type' => $wp_filetype['type'],
                                                                    'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
                                                                    'post_content' => '',
                                                                    'post_status' => 'inherit'
                                                                );
                                                                $gallery_id = wp_insert_attachment($attachment, $upload_file['file'], $args_cores['ID']);
                                                                if (!is_wp_error($gallery_id)) {
                                                                    require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
                                                                    $attachment_data = wp_generate_attachment_metadata($gallery_id, $upload_file['file']);
                                                                    wp_update_attachment_metadata($gallery_id, $attachment_data);
                                                                    add_post_meta($post_type_id, $key_extra, $gallery_id);
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            continue;
                                        }

                                        if ($key_extra == '_thumbnail_id' && $extra_field) {
                                            if (is_numeric($extra_field)) {
                                                update_user_meta($employer_id, IWJ_PREFIX . 'avatar', $extra_field);
                                                update_post_meta($post_type_id, $key_extra, $extra_field);
                                            } else {
                                                $image_exist = wp_get_image_editor($extra_field);
                                                if (!is_wp_error($image_exist)) {
                                                    $attachment_id = iwj_get_image_id_by_url($extra_field);
                                                    if ($attachment_id) {
                                                        update_user_meta($employer_id, IWJ_PREFIX . 'avatar', $attachment_id);
                                                        update_post_meta($post_type_id, $key_extra, $attachment_id);
                                                    }
                                                } else {
                                                    $file_name = basename($extra_field);
                                                    $upload_file = wp_upload_bits($file_name, null, file_get_contents($extra_field));
                                                    if (!$upload_file['error']) {
                                                        $wp_filetype = wp_check_filetype($file_name, null);
                                                        $attachment = array(
                                                            'post_mime_type' => $wp_filetype['type'],
                                                            'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
                                                            'post_content' => '',
                                                            'post_status' => 'inherit'
                                                        );
                                                        $avatar_id = wp_insert_attachment($attachment, $upload_file['file'], $args_cores['ID']);
                                                        if (!is_wp_error($avatar_id)) {
                                                            require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
                                                            $attachment_data = wp_generate_attachment_metadata($avatar_id, $upload_file['file']);
                                                            wp_update_attachment_metadata($avatar_id, $attachment_data);
                                                            update_user_meta($employer_id, IWJ_PREFIX . 'avatar', $avatar_id);
                                                            update_post_meta($post_type_id, $key_extra, $avatar_id);
                                                        }
                                                    }
                                                }
                                            }
                                            continue;
                                        }

                                        update_post_meta($post_type_id, $key_extra, $extra_field);
                                    }
                                    if ($terms_fs['iwj_size']) {
                                        $ide_term = get_term_by('slug', $terms_fs['iwj_size'], 'iwj_size');
                                        if (!$ide_term) {
                                            $new_term = wp_insert_term($terms_fs['iwj_size'], 'iwj_size');
                                            $ide_term_id = $new_term['term_id'];
                                        } else {
                                            $ide_term_id = $ide_term->term_id;
                                        }
                                        wp_set_post_terms($post_type_id, $ide_term_id, 'iwj_size');
                                    }
                                    if ($terms_fs['iwj_cat']) {
                                        $iwj_job_cats = explode('|', $terms_fs['iwj_cat']);
                                        if ($iwj_job_cats) {
                                            $terms_e_cats = array();
                                            foreach ($iwj_job_cats as $iwj_job_cat) {
                                                $ide_term = get_term_by('slug', $iwj_job_cat, 'iwj_cat');
                                                if (!$ide_term) {
                                                    $new_term = wp_insert_term($iwj_job_cat, 'iwj_cat');
                                                    $ide_term_id = $new_term['term_id'];
                                                } else {
                                                    $ide_term_id = $ide_term->term_id;
                                                }
                                                $terms_e_cats[] = $ide_term_id;
                                            }
                                            wp_set_post_terms($post_type_id, $terms_e_cats, 'iwj_cat');
                                        }
                                    }
                                    if ($terms_fs['iwj_location']) {
                                        $iwj_job_locations = explode('>', $terms_fs['iwj_location']);
                                        if ($iwj_job_locations) {
                                            $terms_e_locats = array();
                                            foreach ($iwj_job_locations as $iwj_job_location) {
                                                $ide_term = get_term_by('slug', $iwj_job_location, 'iwj_location');
                                                if (!$ide_term) {
                                                    $new_term = wp_insert_term($iwj_job_location, 'iwj_location');
                                                    $ide_term_id = $new_term['term_id'];
                                                } else {
                                                    $ide_term_id = $ide_term->term_id;
                                                }
                                                $terms_e_locats[] = $ide_term_id;
                                            }
                                            wp_set_post_terms($post_type_id, $terms_e_locats, 'iwj_location');
                                        }
                                    }
                                }
                            }
                        }
                        break;
                    case 'iwj_job':
                        if ($args_cores['ID']) {
                            if (get_post_status($args_cores['ID']) && get_post_type($args_cores['ID']) == 'iwj_job') {
                                if ($import_mode == 'update_existing_items') {
                                    $check = 1;
                                }
                            } else {
                                $check = 2;
                            }
                        } else {
                            $check = 2;
                        }

                        if ($check == 1) {
                            wp_update_post($args_cores);
                            if (!array_key_exists('_iwj_expiry', $args_extras)) {
                                update_post_meta($args_cores['ID'], '_iwj_expiry', '');
                            }
                            if (!array_key_exists('_iwj_featured', $args_extras)) {
                                update_post_meta($args_cores['ID'], '_iwj_featured', 0);
                            }
                            if (!array_key_exists('_iwj_featured_date', $args_extras)) {
                                update_post_meta($args_cores['ID'], '_iwj_featured_date', '');
                            }
                            if (!array_key_exists('_iwj_salary_from', $args_extras)) {
                                update_post_meta($args_cores['ID'], '_iwj_salary_from', '');
                            }
                            if (!array_key_exists('_iwj_salary_to', $args_extras)) {
                                update_post_meta($args_cores['ID'], '_iwj_salary_to', '');
                            }

                            foreach ($args_extras as $key_extra => $extra_field) {
                                if ($key_extra == '_iwj_job_gender' || $key_extra == '_iwj_job_languages') {
                                    $extra_field1 = json_decode($extra_field);
                                    if ($extra_field1) {
                                        delete_post_meta($args_cores['ID'], $key_extra);
                                        foreach ($extra_field1 as $ex_json) {
                                            add_post_meta($args_cores['ID'], $key_extra, $ex_json);
                                        }
                                    } else {
                                        update_post_meta($args_cores['ID'], $key_extra, $extra_field1);
                                    }
                                    continue;
                                }
                                if ($key_extra == '_iwj_deadline' || $key_extra == '_iwj_featured_date' || $key_extra == '_iwj_expiry') {
                                    update_post_meta($args_cores['ID'], $key_extra, strtotime($extra_field));
                                    continue;
                                }
                                update_post_meta($args_cores['ID'], $key_extra, $extra_field);
                            }

                            if ($terms_fs['iwj_type'] && !iwj_option('disable_type')) {
                                $iwj_job_types = explode('|', $terms_fs['iwj_type']);
                                if ($iwj_job_types) {
                                    $terms_j_type = array();
                                    foreach ($iwj_job_types as $iwj_job_type) {
                                        $ide_term = get_term_by('slug', $iwj_job_type, 'iwj_type');
                                        if (!$ide_term) {
                                            $new_term = wp_insert_term($iwj_job_type, 'iwj_type');
                                            $ide_term_id = $new_term['term_id'];
                                        } else {
                                            $ide_term_id = $ide_term->term_id;
                                        }
                                        $terms_j_type[] = $ide_term_id;
                                    }
                                    wp_set_post_terms($args_cores['ID'], $terms_j_type, 'iwj_type');
                                }
                            }
                            if ($terms_fs['iwj_cat']) {
                                $iwj_job_cats = explode('|', $terms_fs['iwj_cat']);
                                if ($iwj_job_cats) {
                                    $terms_j_cat = array();
                                    foreach ($iwj_job_cats as $iwj_job_cat) {
                                        $ide_term = get_term_by('slug', $iwj_job_cat, 'iwj_cat');
                                        if (!$ide_term) {
                                            $new_term = wp_insert_term($iwj_job_cat, 'iwj_cat');
                                            $ide_term_id = $new_term['term_id'];
                                        } else {
                                            $ide_term_id = $ide_term->term_id;
                                        }
                                        $terms_j_cat[] = $ide_term_id;
                                    }
                                    wp_set_post_terms($args_cores['ID'], $terms_j_cat, 'iwj_cat');
                                }
                            }
                            if ($terms_fs['iwj_skill'] && !iwj_option('disable_skill')) {
                                $iwj_job_skills = explode('|', $terms_fs['iwj_skill']);
                                if ($iwj_job_skills) {
                                    $terms_j_skill = array();
                                    foreach ($iwj_job_skills as $iwj_job_skill) {
                                        $ide_term = get_term_by('slug', $iwj_job_skill, 'iwj_skill');
                                        if (!$ide_term) {
                                            $new_term = wp_insert_term($iwj_job_skill, 'iwj_skill');
                                            $ide_term_id = $new_term['term_id'];
                                        } else {
                                            $ide_term_id = $ide_term->term_id;
                                        }
                                        $terms_j_skill[] = $ide_term_id;
                                    }
                                    wp_set_post_terms($args_cores['ID'], $terms_j_skill, 'iwj_skill');
                                }
                            }
                            if ($terms_fs['iwj_level'] && !iwj_option('disable_level')) {
                                $iwj_job_levels = explode('|', $terms_fs['iwj_level']);
                                if ($iwj_job_levels) {
                                    $terms_j_level = array();
                                    foreach ($iwj_job_levels as $iwj_job_level) {
                                        $ide_term = get_term_by('slug', $iwj_job_level, 'iwj_level');
                                        if (!$ide_term) {
                                            $new_term = wp_insert_term($iwj_job_level, 'iwj_level');
                                            $ide_term_id = $new_term['term_id'];
                                        } else {
                                            $ide_term_id = $ide_term->term_id;
                                        }
                                        $terms_j_level[] = $ide_term_id;
                                    }
                                    wp_set_post_terms($args_cores['ID'], $terms_j_level, 'iwj_level');
                                }
                            }
                            if ($terms_fs['iwj_location']) {
                                $iwj_job_locations = explode('>', $terms_fs['iwj_location']);
                                if ($iwj_job_locations) {
                                    $terms_j_location = array();
                                    foreach ($iwj_job_locations as $iwj_job_location) {
                                        $ide_term = get_term_by('slug', $iwj_job_location, 'iwj_location');
                                        if (!$ide_term) {
                                            $new_term = wp_insert_term($iwj_job_location, 'iwj_location');
                                            $ide_term_id = $new_term['term_id'];
                                        } else {
                                            $ide_term_id = $ide_term->term_id;
                                        }
                                        $terms_j_location[] = $ide_term_id;
                                    }
                                    wp_set_post_terms($args_cores['ID'], $terms_j_location, 'iwj_location');
                                }
                            }
                        } elseif ($check == 2) {
                            if ($args_cores['ID']) {
                                unset($args_cores['ID']);
                            }
                            $job_id = wp_insert_post($args_cores);
                            if ($job_id) {
                                $args_cores['ID'] = $job_id;
                                wp_update_post($args_cores);
                                if (!array_key_exists('_iwj_expiry', $args_extras)) {
                                    update_post_meta($job_id, '_iwj_expiry', '');
                                }
                                if (!array_key_exists('_iwj_featured', $args_extras)) {
                                    update_post_meta($job_id, '_iwj_featured', 0);
                                }
                                if (!array_key_exists('_iwj_featured_date', $args_extras)) {
                                    update_post_meta($job_id, '_iwj_featured_date', '');
                                }
                                if (!array_key_exists('_iwj_salary_from', $args_extras)) {
                                    update_post_meta($job_id, '_iwj_salary_from', '');
                                }
                                if (!array_key_exists('_iwj_salary_to', $args_extras)) {
                                    update_post_meta($job_id, '_iwj_salary_to', '');
                                }

                                foreach ($args_extras as $key_extra => $extra_field) {
                                    if ($key_extra == '_iwj_job_gender' || $key_extra == '_iwj_job_languages') {
                                        $extra_field1 = json_decode($extra_field);
                                        if ($extra_field1) {
                                            delete_post_meta($job_id, $key_extra);
                                            foreach ($extra_field1 as $ex_json) {
                                                add_post_meta($job_id, $key_extra, $ex_json);
                                            }
                                        } else {
                                            update_post_meta($job_id, $key_extra, $extra_field1);
                                        }

                                        continue;
                                    }

                                    if ($key_extra == '_iwj_deadline' || $key_extra == '_iwj_featured_date' || $key_extra == '_iwj_expiry') {
                                        update_post_meta($job_id, $key_extra, strtotime($extra_field));

                                        continue;
                                    }

                                    update_post_meta($job_id, $key_extra, $extra_field);
                                }

                                if ($terms_fs['iwj_type'] && !iwj_option('disable_type')) {
                                    $iwj_job_types = explode('|', $terms_fs['iwj_type']);
                                    if ($iwj_job_types) {
                                        $terms_j_type = array();
                                        foreach ($iwj_job_types as $iwj_job_type) {
                                            $ide_term = get_term_by('slug', $iwj_job_type, 'iwj_type');
                                            if (!$ide_term) {
                                                $new_term = wp_insert_term($iwj_job_type, 'iwj_type');
                                                $ide_term_id = $new_term['term_id'];
                                            } else {
                                                $ide_term_id = $ide_term->term_id;
                                            }
                                            $terms_j_type[] = $ide_term_id;
                                        }
                                        wp_set_post_terms($job_id, $terms_j_type, 'iwj_type');
                                    }
                                }
                                if ($terms_fs['iwj_cat']) {
                                    $iwj_job_cats = explode('|', $terms_fs['iwj_cat']);
                                    if ($iwj_job_cats) {
                                        $terms_j_cat = array();
                                        foreach ($iwj_job_cats as $iwj_job_cat) {
                                            $ide_term = get_term_by('slug', $iwj_job_cat, 'iwj_cat');
                                            if (!$ide_term) {
                                                $new_term = wp_insert_term($iwj_job_cat, 'iwj_cat');
                                                $ide_term_id = $new_term['term_id'];
                                            } else {
                                                $ide_term_id = $ide_term->term_id;
                                            }
                                            $terms_j_cat[] = $ide_term_id;
                                        }
                                        wp_set_post_terms($job_id, $terms_j_cat, 'iwj_cat');
                                    }
                                }
                                if ($terms_fs['iwj_skill'] && !iwj_option('disable_skill')) {
                                    $iwj_job_skills = explode('|', $terms_fs['iwj_skill']);
                                    if ($iwj_job_skills) {
                                        $terms_j_skill = array();
                                        foreach ($iwj_job_skills as $iwj_job_skill) {
                                            $ide_term = get_term_by('slug', $iwj_job_skill, 'iwj_skill');
                                            if (!$ide_term) {
                                                $new_term = wp_insert_term($iwj_job_skill, 'iwj_skill');
                                                $ide_term_id = $new_term['term_id'];
                                            } else {
                                                $ide_term_id = $ide_term->term_id;
                                            }
                                            $terms_j_skill[] = $ide_term_id;
                                        }
                                        wp_set_post_terms($job_id, $terms_j_skill, 'iwj_skill');
                                    }
                                }
                                if ($terms_fs['iwj_level'] && !iwj_option('disable_level')) {
                                    $iwj_job_levels = explode('|', $terms_fs['iwj_level']);
                                    if ($iwj_job_levels) {
                                        $terms_j_level = array();
                                        foreach ($iwj_job_levels as $iwj_job_level) {
                                            $ide_term = get_term_by('slug', $iwj_job_level, 'iwj_level');
                                            if (!$ide_term) {
                                                $new_term = wp_insert_term($iwj_job_level, 'iwj_level');
                                                $ide_term_id = $new_term['term_id'];
                                            } else {
                                                $ide_term_id = $ide_term->term_id;
                                            }
                                            $terms_j_level[] = $ide_term_id;
                                        }
                                        wp_set_post_terms($job_id, $terms_j_level, 'iwj_level');
                                    }
                                }
                                if ($terms_fs['iwj_location']) {
                                    $iwj_job_locations = explode('>', $terms_fs['iwj_location']);
                                    if ($iwj_job_locations) {
                                        $terms_j_location = array();
                                        foreach ($iwj_job_locations as $iwj_job_location) {
                                            $ide_term = get_term_by('slug', $iwj_job_location, 'iwj_location');
                                            if (!$ide_term) {
                                                $new_term = wp_insert_term($iwj_job_location, 'iwj_location');
                                                $ide_term_id = $new_term['term_id'];
                                            } else {
                                                $ide_term_id = $ide_term->term_id;
                                            }
                                            $terms_j_location[] = $ide_term_id;
                                        }
                                        wp_set_post_terms($job_id, $terms_j_location, 'iwj_location');
                                    }
                                }
                            }
                        }
                        break;
                }

                echo json_encode(array(
                    'total_no_of_rows' => $totalCount,
                    'processed' => $i + 1,
                    'inserted' => $i,
                        )
                );
            } catch (Exception $e) {
                $parserObj->logE('ERROR:', $e);
            }
        }
        die;
    }

    static function renew_plan() {
        if (isset($_GET['iwj-renew-plan'])) {
            $user = IWJ_User::get_user();
            $plan = $user ? $user->get_plan() : false;
            if ($plan && $plan->can_buy()) {
                if ($plan->is_free() || (float) $plan->get_price() === 0) {
                    $user->set_plan($plan->get_id());
                    $_SESSION['iwp_message'] = __('Congratulation, you have just renewed your plan successfully!', 'iwproperty');
                    wp_redirect(add_query_arg(array('iwj_tab' => 'current-plan'), iwj_get_page_permalink('dashboard')));
                    exit;
                } else {
                    if (iwj_woocommerce_checkout()) {
                        $iwj_woocommerce = new IWJ_Woocommerce();
                        $iwj_woocommerce->add_to_cart('plan', '', $plan->get_id());
                        global $woocommerce;
                        $checkout_url = $woocommerce->cart->get_checkout_url();
                        wp_redirect($checkout_url);
                        exit;
                    } else {
                        $cart = new IWJ_Cart();
                        $cart->set('plan', $plan->get_id(), $plan->get_price());
                        wp_redirect(add_query_arg(array('iwj_tab' => 'checkout'), iwj_get_page_permalink('dashboard')));
                        exit;
                    }
                }
            }
        }
    }

    static function cancel_subscription() {
        if (isset($_GET['iwj-cancel-subscription'])) {
            $user = IWJ_User::get_user();
            if ($user && $user->has_subscription()) {
                $user->cancel_subscription();
            }

            wp_redirect(add_query_arg(array('iwj_tab' => 'current-plan', 'msg' => '2'), iwj_get_page_permalink('dashboard')));
            exit;
        }
    }

    static function membership_expiry_notice() {
        if (iwj_option('submit_job_mode') == '3' && iwj_option('email_membership_notice_enable')) {
            $before_days = iwj_option('send_membership_notice_before');
            $in_days = iwj_option('send_membership_notice_days');
            $before_days = $before_days ? $before_days : 1;
            $in_days = $in_days ? $in_days : 1;
            if ($in_days > $before_days) {
                $in_days = $before_days;
            }

            $first_time = current_time('timestamp') - ($before_days - $in_days - 1) * 86400;
            $second_time = current_time('timestamp') + $before_days * 86400;
            global $wpdb;
            $sql = "SELECT u.ID FROM {$wpdb->users} AS u 
            JOIN {$wpdb->usermeta} AS um ON um.user_id = u.ID 
            JOIN {$wpdb->usermeta} AS um1 ON um1.user_id = u.ID 
            WHERE um.meta_key = %s AND um1.meta_key = %s AND um.meta_value != '' AND um1.meta_value != '-1' AND CAST(um1.meta_value AS UNSIGNED) >= %d AND CAST(um1.meta_value AS UNSIGNED) <= %d";
            $users = $wpdb->get_results($wpdb->prepare($sql, IWJ_PREFIX . 'plan_id', IWJ_PREFIX . 'plan_expiry_date', $first_time, $second_time));
            if ($users) {
                foreach ($users AS $user) {
                    IWJ_Email::send_email('membership_notice', array('user_id' => $user->ID));
                }
            }
        }
    }

    static function membership_expired_notice() {

        if (iwj_option('submit_job_mode') == '3' && iwj_option('email_membership_expired_enable')) {
            $time = mktime(0, 0, 0, date("m"), date("d"), date("Y")) + 86400;
            global $wpdb;
            $sql = "SELECT u.ID FROM {$wpdb->users} AS u 
            JOIN {$wpdb->usermeta} AS um ON um.user_id = u.ID 
            JOIN {$wpdb->usermeta} AS um1 ON um1.user_id = u.ID 
            WHERE um.meta_key = %s AND um1.meta_key = %s AND um.meta_value != '' AND um1.meta_value != '-1' AND CAST(um1.meta_value AS UNSIGNED) <= %d AND NOT EXISTS ( 
                 SELECT 
                  user_id 
                 FROM 
                  {$wpdb->usermeta}
                 WHERE 
                  {$wpdb->usermeta}.user_id = u.ID 
                 AND 
                  {$wpdb->usermeta}.meta_key = %s
            )";

            $users = $wpdb->get_results($wpdb->prepare($sql, IWJ_PREFIX . 'plan_id', IWJ_PREFIX . 'plan_expiry_date', $time, IWJ_PREFIX . 'sent_expired_email'));

            if ($users) {
                foreach ($users AS $user) {
                    IWJ_Email::send_email('membership_expired', array('user_id' => $user->ID));
                    update_user_meta($user->ID, IWJ_PREFIX . 'sent_expired_email', '1');
                }
            }
        }
    }

}

IWJ_Controller::init();
