<?php

class IWJ_Social_Login_Linkedin extends IWJ_Social_Login {

    public $oauth;

    function __construct() {
        parent::__construct();
        if (!class_exists('Linkedin_OAuth2Client')) {
            require_once IWJ_PLUGIN_DIR . '/includes/class/socials/linkedin/OAuth2Client.php';
        }

        $this->oauth = new Linkedin_OAuth2Client($this->get_option('client_id'), $this->get_option('client_secret'));
        $this->oauth->redirect_uri = $this->get_oauth_url();
    }

    function get_title() {
        return __('Linkedin', 'iwjob');
    }

    function get_description() {
        return __('Login With Linkedin', 'iwjob');
    }

    function get_fontawesome_icon() {
        return 'fa fa-linkedin';
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
                'name' => __('Client ID', 'iwjob'),
                'type' => 'text',
            ),
            array(
                'id' => 'client_secret',
                'name' => __('Client Secret', 'iwjob'),
                'type' => 'text',
            ),
        );
    }

    function is_available() {
        if (parent::is_available() && $this->get_option('client_id') && $this->get_option('client_secret')) {
            return true;
        }

        return false;
    }

    public function get_login_url() {
        if (!class_exists('Linkedin_OAuth2Client')) {
            require_once IWJ_PLUGIN_DIR . '/includes/class/socials/linkedin/OAuth2Client.php';
        }
        $state = wp_generate_password(12, false);
        return $this->oauth->authorizeUrl(array('scope' => 'r_basicprofile r_emailaddress',
                    'state' => $state));
    }

    public function oauth() {
        if (isset($_REQUEST['code']) && (!isset($_REQUEST['error']) || !$_REQUEST['error'])) {
            $user_data = $this->get_linkedin_profile();
            $email = $user_data['email'];
            $display_name = $user_data['first_name'] . ' ' . $user_data['first_name'];
            $profile_image_url = $user_data['picture_url'];
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
                    'email' => urlencode($email),
                );

                $profile = add_query_arg($args, iwj_get_page_permalink('register'));

                wp_redirect($profile);  // redirect to any page
                exit;
            }
        }
    }

    /*
     * Get the user LinkedIN profile and return it as XML
     */

    private function get_linkedin_profile() {

        // Use GET method since POST isn't working
        $this->oauth->curl_authenticate_method = 'GET';

        // Request access token
        $response = $this->oauth->authenticate($_REQUEST['code']);
        $this->access_token = $response->{'access_token'};

        // Get first name, last name and email address, and load
        // response into XML object
        $xml = simplexml_load_string($this->oauth->get('https://api.linkedin.com/v1/people/~:(id,first-name,last-name,email-address,headline,specialties,positions:(id,title,summary,start-date,end-date,is-current,company),summary,site-standard-profile-request,picture-url,location:(name,country:(code)),industry)'));

        return $this->parse_xml_data($xml);
    }

    private function parse_xml_data($xml) {
        $data = array();
        $data['email'] = isset($xml->{'email-address'}) ? (string) $xml->{'email-address'} : '';
        $data['id'] = isset($xml->{'id'}) ? (string) $xml->{'id'} : '';
        $data['first_name'] = isset($xml->{'first-name'}) ? (string) $xml->{'first-name'} : '';
        $data['last_name'] = isset($xml->{'last-name'}) ? (string) $xml->{'last-name'} : '';
        $data['summary'] = isset($xml->{'summary'}) ? (string) $xml->{'summary'} : '';
        $data['linkedin_url'] = isset($xml->{'site-standard-profile-request'}->url) ? (string) $xml->{'site-standard-profile-request'}->url : '';
        $data['picture_url'] = isset($xml->{'picture-url'}) ? (string) $xml->{'picture-url'} : '';
        $data['location'] = array('name' => (string) $xml->{'location'}->{'name'}, 'country_code' => (string) $xml->{'location'}->{'country'}->{'code'});
        $data['industry'] = isset($xml->{'industry'}) ? (string) $xml->{'industry'} : '';
        $data['headline'] = isset($xml->{'headline'}) ? (string) $xml->{'headline'} : '';
        $data['specialties'] = isset($xml->{'specialties'}) ? (string) $xml->{'specialties'} : '';

        return $data;
    }

}
