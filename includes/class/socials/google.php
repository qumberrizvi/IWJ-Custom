<?php

class IWJ_Social_Login_Google extends IWJ_Social_Login {

    function get_title() {
        return __('Google', 'iwjob');
    }

    function get_description() {
        return __('Login With Google', 'iwjob');
    }

    function get_fontawesome_icon() {
        return 'fa fa-google-plus';
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
                'id' => 'client_id',
                'name' => __('Google Client ID', 'iwjob'),
                'type' => 'text',
            ),
            array(
                'id' => 'api_key',
                'name' => __('Google API Key', 'iwjob'),
                'type' => 'text',
            ),
            array(
                'id' => 'secret',
                'name' => __('Google Secret', 'iwjob'),
                'type' => 'text',
            ),
        );
    }

    function is_available() {
        if (parent::is_available() && $this->get_option('client_id') && $this->get_option('api_key') && $this->get_option('secret')) {
            return true;
        }

        return false;
    }

    public function get_login_url() {
        
        require_once IWJ_PLUGIN_DIR . '/includes/class/socials/google/Google_Client.php';
        require_once IWJ_PLUGIN_DIR . '/includes/class/socials/google/contrib/Google_Oauth2Service.php';

        $google_client_id = $this->get_option('client_id');
        $google_client_secret = $this->get_option('secret');
        $google_developer_key = $this->get_option('api_key');

        $gClient = new Google_Client();
        $gClient->setApplicationName('Login with Google');
        $gClient->setClientId($google_client_id);
        $gClient->setClientSecret($google_client_secret);
        $gClient->setRedirectUri($this->get_oauth_url());
        $gClient->setDeveloperKey($google_developer_key);
        $gClient->setScopes('email');
        $google_oauthV2 = new Google_Oauth2Service($gClient);
        return $gClient->createAuthUrl();
    }

    public function oauth() {
        require_once IWJ_PLUGIN_DIR . '/includes/class/socials/google/Google_Client.php';
        require_once IWJ_PLUGIN_DIR . '/includes/class/socials/google/contrib/Google_Oauth2Service.php';

        $google_client_id = $this->get_option('client_id');
        $google_client_secret = $this->get_option('secret');
        $google_developer_key = $this->get_option('api_key');

        $gClient = new Google_Client();
        $gClient->setApplicationName('Login to Houzez');
        $gClient->setClientId($google_client_id);
        $gClient->setClientSecret($google_client_secret);
        $gClient->setRedirectUri($this->get_oauth_url());
        $gClient->setDeveloperKey($google_developer_key);
        $google_oauthV2 = new Google_Oauth2Service($gClient);

        $allowed_html = array();
        if (isset($_GET['code'])) {
            $code = wp_kses($_GET['code'], $allowed_html);
            $gClient->authenticate($code);
        }

        if ($gClient->getAccessToken()) {
            $user = $google_oauthV2->userinfo->get();
            $display_name = wp_kses($user['name'], $allowed_html);
            $email = wp_kses($user['email'], $allowed_html);
            $profile_image_url = filter_var($user['picture'], FILTER_VALIDATE_URL);
            $username = iwj_get_username($user['id']);
            if ($email) {
                $user = get_user_by('email', $email);
                if (!$user) {
                    $username = iwj_get_username($email);
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

                $_SESSION['iwj_verified_email'] = $email;

                $args = array(
                    'social_register' => $this->id,
                    'user_name' => $username,
                    'display_name' => urlencode($display_name),
                    'profile_image_url' => urlencode($profile_image_url),
                    'email' => urlencode($email)
                );

                $profile = add_query_arg($args, iwj_get_page_permalink('register'));

                wp_redirect($profile);  // redirect to any page
                exit;
            }
        }
    }

}
