<?php

class IWJ_Social_Login_Twitter extends IWJ_Social_Login {

    function __construct() {

        parent::__construct();

        add_action('wp_loaded', array($this, 'login_twitter'));
    }

    function login_twitter() {
        if (isset($_GET['iwj_login_twitter']) && $_GET['iwj_login_twitter']) {
            $this->login_oauth();
        }
    }

    function get_title() {
        return __('Twitter', 'iwjob');
    }

    function get_description() {
        return __('Login With Twitter', 'iwjob');
    }

    function get_fontawesome_icon() {
        return 'fa fa-twitter';
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
                'id' => 'consumer_key',
                'name' => __('Consumer Key', 'iwjob'),
                'type' => 'text',
            ),
            array(
                'id' => 'consumer_secret',
                'name' => __('Consumer Secret', 'iwjob'),
                'type' => 'text',
            ),
        );
    }

    function is_available() {
        if (parent::is_available() && $this->get_option('consumer_key') && $this->get_option('consumer_secret')) {
            return true;
        }

        return false;
    }

    public function get_login_url() {
        return home_url('/') . '?iwj_login_twitter=true';
    }

    public function login_oauth() {

        if (!class_exists('TwitterOAuth')) {
            require_once IWJ_PLUGIN_DIR . '/includes/class/socials/twitter/twitteroauth.php';
        }

        $consumer_key = $this->get_option('consumer_key');
        $consumer_secret = $this->get_option('consumer_secret');

        //Fresh authentication
        $connection = new TwitterOAuth($consumer_key, $consumer_secret);
        $request_token = $connection->getRequestToken($this->get_oauth_url());

        //Received token info from twitter
        $_SESSION['token'] = $request_token['oauth_token'];
        $_SESSION['token_secret'] = $request_token['oauth_token_secret'];

        //Any value other than 200 is failure, so continue only if http code is 200
        if ($connection->http_code == '200') {
            //redirect user to twitter
            $twitter_url = $connection->getAuthorizeURL($request_token['oauth_token']);
            header('Location: ' . $twitter_url);
            exit;
        } else {
            die("error connecting to twitter! try again later!");
        }
    }

    public function oauth() {
        if (isset($_REQUEST['oauth_token']) && $_SESSION['token'] == $_REQUEST['oauth_token']) {

            if (!class_exists('TwitterOAuth')) {
                require_once IWJ_PLUGIN_DIR . '/includes/class/socials/twitter/twitteroauth.php';
            }

            $consumer_key = $this->get_option('consumer_key');
            $consumer_secret = $this->get_option('consumer_secret');

            $connection = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION['token'], $_SESSION['token_secret']);
            $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
            if ($connection->http_code == '200') {
                $params = array('include_email' => 'true', 'include_entities' => 'false', 'skip_status' => 'true');
                $user_data = $connection->get('account/verify_credentials', $params);
                $email = isset($user_data['email']) ? $user_data['email'] : '';
                $display_name = isset($user_data['name']) ? $user_data['name'] : '';
                $profile_image_url = isset($user_data['profile_background_image_url']) ? $user_data['profile_background_image_url'] : '';
                $username = iwj_get_username($user_data['id']);
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
                    if(!$login_redirect){
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
                        'email' => urlencode($email),
                    );

                    $profile = add_query_arg($args, iwj_get_page_permalink('register'));

                    wp_redirect($profile);  // redirect to any page
                    exit;
                }
            } else {
                die("error, try again later!");
            }
            exit;
        }
    }

}
