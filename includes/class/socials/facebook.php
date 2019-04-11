<?php

class IWJ_Social_Login_Facebook extends IWJ_Social_Login {

    function get_title() {
        return __('Facebook', 'iwjob');
    }

    function get_description() {
        return __('Login With Facebook', 'iwjob');
    }

    function get_fontawesome_icon() {
        return 'fa fa-facebook';
    }

    function admin_option_fields() {
        return array(
            array(
                'id' => 'enable',
                'name' => __('Enable', 'iwjob'),
                'type' => 'select',
                'options' => array(
                    '1' => __('Yes', 'iwjob'),
                    '0' => __('No', 'iwjob'),
                ),
                'std' => '0',
            ),
            array(
                'id' => 'api_key',
                'name' => __('Facebook API Key', 'iwjob'),
                'type' => 'text',
            ),
            array(
                'id' => 'secret',
                'name' => __('Facebook Secret', 'iwjob'),
                'type' => 'text',
            ),
        );
    }

    function is_available() {
        if (parent::is_available() && $this->get_option('api_key') && $this->get_option('secret')) {
            return true;
        }

        return false;
    }

    public function get_login_url() {
        if (!class_exists('Facebook')) {
            require_once IWJ_PLUGIN_DIR . '/includes/class/socials/Facebook/vendor/autoload.php';
        }

        $facebook_api = $this->get_option('api_key');
        $facebook_secret = $this->get_option('secret');

        $fb = new Facebook\Facebook([
            'app_id' => $facebook_api,
            'app_secret' => $facebook_secret,
            'default_graph_version' => 'v2.7',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['email']; // Optional permissions
        $loginUrl = $helper->getLoginUrl($this->get_oauth_url(), $permissions);

        return $loginUrl;
    }

    public function oauth() {
        if (!class_exists('Facebook')) {
            require_once IWJ_PLUGIN_DIR . '/includes/class/socials/Facebook/vendor/autoload.php';
        }

        $facebook_api = $this->get_option('api_key');
        $facebook_secret = $this->get_option('secret');

        $fb = new Facebook\Facebook([
            'app_id' => $facebook_api,
            'app_secret' => $facebook_secret,
            'default_graph_version' => 'v2.7',
            'http_client_handler' => 'curl', // can be changed to stream or guzzle
            'persistent_data_handler' => 'session' // make sure session has started
        ]);

        $get_vars = $_GET;

        if (isset($get_vars['code'])) {
            $helper = $fb->getRedirectLoginHelper();
            // Trick below will avoid "Cross-site request forgery validation failed. Required param "state" missing." from Facebook
            $_SESSION['FBRLH_state'] = $_REQUEST['state'];
        } else {
            // login helper with redirect_uri
            $helper = $fb->getRedirectLoginHelper($this->get_oauth_url());
        }


        // see if we have a code in the URL
        if (isset($get_vars['code'])) {
            // get new access token if we've been redirected from login page
            try {
                // get access token
                $access_token = $helper->getAccessToken();
                // save access token to persistent data store
                $helper->getPersistentDataHandler()->set('access_token', $access_token);
            } catch (Exception $e) {
                // error occured
                echo 'Exception 1: ' . $e->getMessage() . '';
            }

            // get stored access token
            $access_token = $helper->getPersistentDataHandler()->get('access_token');
        }

        // check if we have an access_token, and that it's valid
        if (isset($access_token) && $access_token && !$access_token->isExpired()) {
            // set default access_token so we can use it in any requests
            $fb->setDefaultAccessToken($access_token);
            try {
                // Returns a `Facebook\FacebookResponse` object
                $response = $fb->get('/me?fields=first_name,last_name,email', $access_token);
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }

            $user = $response->getGraphObject()->asArray();

            $profile_image_url = 'https://graph.facebook.com/' . $user['id'] . '/picture?width=999&height=999';

            $fb_email = $user['email'];
            $fb_firstname = $user['first_name'];
            $fb_lastname = $user['last_name'];

            $username = iwj_get_username($user['id']);


            $display_name = $fb_firstname . ' ' . $fb_lastname;
            if ($fb_email) {
                $user = get_user_by('email', $fb_email);
                if (!$user) {
                    $username = iwj_get_username($fb_email);
                }
            } else {
                $user = get_user_by('login', $username);
            }

            if ($user && !is_wp_error($user)) {
                wp_clear_auth_cookie();
                wp_set_current_user($user->ID);
                wp_set_auth_cookie($user->ID);
                if (in_array('iwj_employer', $user->roles)) {
                    $login_redirect = get_permalink(iwj_option('employer_login_redirect'));
                } elseif (in_array('iwj_candidate', $user->roles)) {
                    $login_redirect = get_permalink(iwj_option('candidate_login_redirect'));
                }
                if (!$login_redirect) {
                    $login_redirect = home_url();
                }
                $redirect = apply_filters('iwj_redirect_to', $login_redirect, $user);
                wp_redirect($redirect);  // redirect to any page
                exit;
            } else {

                $_SESSION['iwj_verified_email'] = $fb_email;
                $args = array(
                    'social_register' => $this->id,
                    'user_name' => $username,
                    'display_name' => urlencode($display_name),
                    'profile_image_url' => urlencode($profile_image_url),
                    'email' => urlencode($fb_email),
                );

                $profile = add_query_arg($args, iwj_get_page_permalink('register'));

                wp_redirect($profile);  // redirect to any page
                exit;
            }
        }
    }

}
