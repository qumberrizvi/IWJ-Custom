<?php
class IWJ_Apply_Facebook extends IWJ_Apply {

    public $oauth;

    function __construct()
    {
        parent::__construct();
        
        add_action( 'wp_ajax_iwj_submit_application_facebook', array($this, 'submit_application'));
        add_action( 'wp_ajax_nopriv_iwj_submit_application_facebook', array($this, 'submit_application'));

        add_action( 'wp_footer', array( $this, 'cover_letter_popup' ));

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    function get_title(){
        return __('Apply With Facebook', 'iwjob');
    }

    function get_description(){
        return '';
    }

    function is_available()
    {
        if(parent::is_available() && $this->get_option('api_key') && $this->get_option('secret')){
            return true;
        }

        return false;
    }

    function admin_option_fields(){
        return array(
            array(
                'id' 			=> 'enable',
                'name'			=> __( 'Enable' , 'iwjob' ),
                'type'			=> 'select',
                'options'		=> array(
                    '1' => __('Yes', 'iwjob'),
                    '0' => __('No', 'iwjob'),
                ),
                'std'		    => '0',
            ),
            array(
                'id' 			=> 'api_key',
                'name'			=> __( 'Facebook API Key' , 'iwjob' ),
                'type'			=> 'text',
            ),
            array(
                'id' 			=> 'secret',
                'name'			=> __( 'Facebook Secret' , 'iwjob' ),
                'type'			=> 'text',
            ),
            array(
                'id' 			=> 'allow_input_cover_letter',
                'name'			=> __( 'Allow Teacher Input Cover Letter' , 'iwjob' ),
                'type'			=> 'select',
                'options'		=> array(
                    '1' => __('Yes', 'iwjob'),
                    '0' => __('No', 'iwjob'),
                ),
                'std'		    => '1',
            ),
            array(
                'id' 			=> 'cover_letter_field_type',
                'name'			=> __( 'Cover Letter Field Type' , 'iwjob' ),
                'type'			=> 'select',
                'options'		=> array(
                    'textarea' => __('Textarea', 'iwjob'),
                    'editor' => __('Editor', 'iwjob'),
                ),
                'std'		    => 'textarea',
            ),
            array(
                'id' 			=> 'create_user',
                'name'			=> __( 'Create User' , 'iwjob' ),
                'desc'			=> __( 'Will automatically create user. If they log in with the same facebook account they can view their applications' , 'iwjob' ),
                'type'			=> 'select',
                'options'		=> array(
                    '1' => __('Yes', 'iwjob'),
                    '0' => __('No', 'iwjob'),
                ),
                'std'		    => '0',
            ),
        );
    }

    function enqueue_scripts(){
        wp_register_style('iwj-apply-facebook', IWJ_PLUGIN_URL.'/includes/class/applies/assets/facebook-apply.css');
        wp_register_script('iwj-apply-facebook', IWJ_PLUGIN_URL.'/includes/class/applies/assets/facebook-apply.js', array('jquery'), false, true);
    }

    function apply_button($job){
        iwj_get_template_part('applies/facebook/button', array('job'=>$job, 'self'=>$this));
    }

    public function get_apply_url($job){
        $_SESSION['apply_job_ID'] = $job->get_id();
         if(!class_exists('Facebook')) {
            require_once IWJ_PLUGIN_DIR . '/includes/class/socials/Facebook/vendor/autoload.php';
        }

        $facebook_api    =  $this->get_option('api_key');
        $facebook_secret =  $this->get_option('secret');

        $fb = new Facebook\Facebook([
            'app_id' => $facebook_api,
            'app_secret' => $facebook_secret,
            'default_graph_version' => 'v2.7',
        ]);

        $helper = $fb->getRedirectLoginHelper();
        //https://developers.facebook.com/docs/facebook-login/permissions
        $permissions = ['email', 'public_profile', 'user_birthday', 'user_location', 'user_website', 'user_about_me','user_education_history', 'user_work_history' ]; // Optional permissions
        $link = $helper->getLoginUrl(
            add_query_arg( 
                array(
                    'iwj_social_apply'=> $this->id, 
                    'job_id'=>$job->get_id()
                ),
                home_url('/')
            ),
            $permissions
        );

        return $link;
    }

    public function oauth(){
        if(isset($_REQUEST['code']) && (!isset($_REQUEST['error']) || !$_REQUEST['error'])){
            $job_id = $_REQUEST['job_id'];
            $job = IWJ_Job::get_job($job_id);
            if($job && $job->can_apply() && (!isset($_COOKIE['iwj_facebook_apply_' . $job_id]) || !$_COOKIE['iwj_facebook_apply_' . $job_id])){
                $user_data = $this->get_facebook_profile($job_id);
                if($this->get_option('allow_input_cover_letter')){
                    $_SESSION['iwj_facebook_profile'] = $user_data;
                    $_SESSION['iwj_fb_input_cover_letter'] = true;
                    $link = get_the_permalink($job_id);
                    $link = add_query_arg(array('apply'=>'facebook'), $link);
                    wp_redirect($link);
                    exit;
                }else{
                    $this->create_application($user_data, $job_id, $user_data['about']);
                    wp_redirect(get_the_permalink($job_id));
                    exit;
                }
            }else{
                wp_redirect(get_the_permalink($job_id));
                exit;
            }
        }
    }

    function submit_application(){
        check_ajax_referer('iwj-security');

        if($_SESSION['iwj_facebook_profile']){
            $job_id = $_REQUEST['job_id'];
            $message = wp_kses_post($_REQUEST['message_apply_fb']);
            $application_id = $this->create_application($_SESSION['iwj_facebook_profile'], $job_id, $message);
            unset($_SESSION['iwj_facebook_profile']);
            ob_start();
            iwj_get_template_part('applies/facebook/thankyou', array('job_id'=>$job_id, 'application_id' => $application_id));
            $message = ob_get_contents();
            ob_end_clean();

            echo json_encode(array(
                'success' => true,
                'message' => $message
            ));
            exit;
        }

        echo json_encode(array(
            'success' => false,
            'message' => iwj_get_alert(__('An error occurred please reload the page and try again.', 'iwjob'), 'danger')
        ));
        exit;
    }

    public function create_application($user_data, $job_id, $message = ''){
        require_once IWJ_PLUGIN_DIR . 'includes/libs/fpdf/fpdf.php';
        require_once IWJ_PLUGIN_DIR . 'includes/libs/fpdf/fpdf-html.php';

        ob_start();
        /* generate pdf: begin */
        $pdf = new FPDF_HTML();

        $pdf->AddPage();
        $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
        $pdf->AddFont('DejaVuBold','','DejaVuSansCondensed-Bold.ttf',true);

        // Start PDF page Block with Name, headline, email and linked in profile link
        $pdf->SetFont('DejaVu', '', 22);
        $pdf->Cell(null, 10, "{$user_data['name']}", 0, 1);
        $pdf->SetFont('DejaVu', '', 11);
        // $pdf->Cell(null, 10, $user_data[''], 0, 1);
        $pdf->Write(5, "{$user_data['email']}, ");
        $pdf->SetTextColor(0, 0, 255);
        $pdf->SetFont('DejaVu', '', 9);
        $pdf->Write(5, "https://facebook.com/{$user_data['id']}", "https://facebook.com/{$user_data['id']}");

        $pdf->WriteHTML("<br /><br /><hr><br />");
        // End Block

        /* Start Block Summary */
        $pdf->SetFont('DejaVuBold', '', 17);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->Cell(null, 10, "Summary", 0, 1);

        $pdf->SetFont('DejaVu', '', 11);
        $pdf->SetTextColor(0, 0, 0);
        if (isset($user_data['about'])) {
            $pdf->WriteHTML("<br />" . preg_replace('/[\x00-\x1F\x80-\xFF]/', '', nl2br($user_data['about'])));
        } else {
            $pdf->WriteHTML("<br />No summary");
        }

        if($user_data['picture']['url']) {
            $pdf->Image($user_data['picture']['url'], 165, 10, null, null, "JPG");
        }
        $pdf->WriteHTML("<br /><br /><hr><br />");
        /* End Block Summary */

        /* Start Block Experience */
        $pdf->SetFont('DejaVuBold', '', 17);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->Cell(null, 10, "Experience", 0, 1);

        if ( !empty( $user_data['work'] ) && is_array($user_data['work'] ) ) {
            foreach ( $user_data['work'] as $jobs ) {
                if(isset($jobs[ 'position' ][ 'name' ])){
                    $title = $jobs[ 'position' ][ 'name' ] . ' at ' . $jobs[ 'employer' ][ 'name' ];
                }else{
                    $title = $jobs[ 'employer' ][ 'name' ];
                }
                $start_date = ( $jobs['start_date'] == '0000-00' ) ? '' : date(" F, Y", strtotime( $jobs[ 'start_date' ] ) );
                $end_date = ( $jobs['end_date'] == '0000-00' || !isset( $jobs['end_date'] ) ) ? "Today" : date("F Y",  strtotime( $jobs[ 'end_date' ] ));
                $working_period = "$start_date - $end_date";

                $pdf->SetFont( 'DejaVuBold', '', 13 );
                $pdf->SetTextColor( 0, 0, 0 );

                $pdf->Cell( null, 10, $title, 0, 1 );

                $pdf->SetFont( 'DejaVu', '', 11 );
                $pdf->SetTextColor( 0, 0, 0 );

                $pdf->Cell( null, 10, $working_period, 0, 1 );

                if ( isset ( $jobs['description'] ) ) {
                    $pdf->WriteHTML( "<br />{$jobs['description']}<br /><br />" );
                }
                $pdf->WriteHTML("<br />");
            }
        }
        $pdf->WriteHTML("<hr><br />");
        /* End Block Experience */

        /* Start Block Educations */
        $pdf->SetFont('DejaVuBold', '', 17);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->Cell(null, 10, "Education", 0, 1);

        if ( !empty( $user_data[ 'education' ] ) && is_array( $user_data[ 'education' ] ) ) {
            foreach ($user_data[ 'education' ] as $education) {
                $title = $education[ 'school' ][ 'name' ];
                $concentration = '';
                if( isset( $education['concentration'] ) && is_array( $education['concentration'] ) ) {
                    $concentration .= ' - ';
                    foreach ( $education['concentration'] as $concentrations ) {
                        $concentration .= $concentrations['name'] .',';
                    }
                }
                $type = ( isset($education['type']) ) ? $education['type'] : '';
                $year = ( isset($education['year']['name']) ) ? __('Class of ', 'iwjob') . $education['year']['name'] : '';
                $school_data =  "$type $concentration $year";;

                $pdf->SetFont('DejaVuBold', '', 13);
                $pdf->SetTextColor(0, 0, 0);

                $pdf->Cell(null, 10, $title, 0, 1);

                $pdf->SetFont('DejaVu', '', 11);
                $pdf->SetTextColor(0, 0, 0);

                $pdf->Cell(null, 10, $school_data, 0, 1);

                $pdf->WriteHTML("<br />");
            }
        }

        $pdf->WriteHTML("<hr><br />");
        /* End Block Educations */

        /* Start Block Languages */
        $pdf->SetFont('DejaVuBold', '', 17);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->Cell(null, 10, "Languages", 0, 1);

        $pdf->SetFont('DejaVu', '', 11);
        $pdf->SetTextColor(0, 0, 0);

        //Create the list with Languages
        $list = array();
        $list['bullet'] = chr(149);
        $list['margin'] = ' ';
        $list['indent'] = 0;
        $list['spacer'] = 0;
        $list['text'] = array();

        $i = 0;
        if (!empty($user_data['languages']) && is_array($user_data['languages'])) {
            foreach ($user_data['languages'] as $language) {
                $list['text'][$i] = $language['name'];
                $i++;
            }
        }

        $column_width = $pdf->w - 30;
        $pdf->SetX(10);
        $pdf->MultiCellBltArray($column_width - $pdf->x, 6, $list);

        $pdf->WriteHTML("<br /><hr><br />");
        /* End Block Languages */

        $upload_dir = wp_upload_dir();

        $dir = $upload_dir['basedir'] . '/apply-source/';
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $file_name = uniqid('resume-' . $user_data['name'] . '-') . '.pdf';
        $fn = $dir . $file_name;
        $fn_url = $upload_dir['baseurl'] . '/apply-source/'.$file_name;
        $pdf->Output($fn, 'F');
        /* generate pdf: end */

        //get_user_id

        $email = $user_data['email'];

        $username =  $user_data['id'];
        if($email){
            $user = get_user_by('email', $email);
        }else{
            $email = $username.'@facebook.com';
            $username = $username.'.'.$this->id;
            $user = get_user_by('login', $username);
        }

        $user_id = 0;
        if($user){
            $user_id = $user->ID;
        }elseif(
            $this->get_option('create_user')){
            $user_data_insert = array();
            $user_data_insert['user_login'] = $username;
            $user_data_insert['first_name'] = $user_data['first_name'];
            $user_data_insert['last_name'] = $user_data['last_name'];
            $user_data_insert['display_name'] = $user_data['name'];
            $user_data_insert['user_pass'] = wp_generate_password();;
            $user_data_insert['user_email'] = $email;
            $user_data_insert['role'] = 'iwj_candidate';
            $user_id = wp_insert_user($user_data_insert);
            if ($user_data['picture']) {
                update_user_meta($user_id, IWJ_PREFIX . 'avatar', $user_data['picture']['url']);
            }
        }

        $post_data = array(
            'post_title' => $user_data['name'],
            'post_content' => $message,
            'post_type' => 'iwj_application',
            'post_status' => 'pending',
            'post_author' => $user_id,
        );

        $post_id = wp_insert_post($post_data);
        if($post_id){

            update_post_meta($post_id, IWJ_PREFIX.'job_id', $job_id);
            update_post_meta($post_id, IWJ_PREFIX.'full_name', $user_data['name']);
            update_post_meta($post_id, IWJ_PREFIX.'email', $user_data['email']);
            update_post_meta($post_id, IWJ_PREFIX.'source', $this->id);

            $attachment = array(
                'guid'           => $fn_url,
                'post_mime_type' => 'application/pdf',
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_name ) ),
                'post_content'   => '',
                'post_status'    => 'inherit',
            );


            $id = wp_insert_attachment( $attachment, $fn, $post_id );
            if ( ! is_wp_error( $id ) )
            {
                if(!function_exists('wp_generate_attachment_metadata')){
                    include_once( ABSPATH . 'wp-admin/includes/image.php' );
                }

                wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $fn ) );
            }

            update_post_meta($post_id, IWJ_PREFIX.'cv', $id);

            setcookie('iwj_facebook_apply_' . $job_id, 1, time() + 60 * 60 * 24 * 30, SITECOOKIEPATH);

            //send mail
            $application = IWJ_Application::get_application($post_id, true);
            if(class_exists('\W3TC\DbCache_Plugin')){
                $dbcache = new W3TC\DbCache_Plugin();
                $dbcache->on_post_change();
            }
            IWJ_Email::send_email('new_application', $application);
            IWJ_Email::send_email('new_application_employer', $application);
        }

        ob_get_clean();
       
        return $post_id;
    }

    private function get_facebook_profile($job_id) {

        if(!class_exists('Facebook')) {
            require_once IWJ_PLUGIN_DIR . '/includes/class/socials/Facebook/vendor/autoload.php';
        }

        $facebook_api    =  $this->get_option('api_key');
        $facebook_secret =  $this->get_option('secret');

        $this->oauth = new Facebook\Facebook([
            'app_id' => $facebook_api,
            'app_secret' => $facebook_secret,
            'default_graph_version' => 'v2.7',
            'http_client_handler' => 'curl', // can be changed to stream or guzzle
            'persistent_data_handler' => 'session' // make sure session has started
        ]);
        // var_dump($this->oauth);
        // die();

        $get_vars = $_GET;

        if( isset( $get_vars['code'] ) )
        {
            $helper = $this->oauth->getRedirectLoginHelper();
            // Trick below will avoid "Cross-site request forgery validation failed. Required param "state" missing." from Facebook
            $_SESSION['FBRLH_state'] = $_REQUEST['state'];
        }
        else
        {
            // login helper with redirect_uri
            $helper = $this->oauth->getRedirectLoginHelper( $this->get_oauth_url() );
        }

         // get new access token if we've been redirected from login page
        try {
            // get access token
            $access_token = $helper->getAccessToken();
            // save access token to persistent data store
            $helper->getPersistentDataHandler()->set( 'access_token', $access_token );
        } catch ( Exception $e ) {
            // error occured
            echo 'Exception 1: ' . $e->getMessage() . '';
        }

        // get stored access token
        $access_token = $helper->getPersistentDataHandler()->get( 'access_token' );

        if ( isset($access_token) && $access_token && !$access_token->isExpired() )
        {
            $this->oauth->setDefaultAccessToken( $access_token );
            try {
                // Returns a `Facebook\FacebookResponse` object
                $response = $this->oauth->get('/me?fields=first_name,last_name,email,education,work,about,location,name,languages,picture{url}', $access_token);
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }

            $user = $response->getGraphObject()->asArray();
            //$user['avatar'] = 'https://graph.facebook.com/'.$user['id'].'/picture?width=999&height=999';
           // $profile_image_url = 'https://graph.facebook.com/'.$user['id'].'/picture?width=999&height=999';

        }

        return $user;
    }

    public function get_oauth_url(){
        return add_query_arg(array('iwj_social_apply'=> $this->id), home_url('/'));
    }

    public function cover_letter_popup(){
        if(isset($_SESSION['iwj_fb_input_cover_letter']) && $_SESSION['iwj_fb_input_cover_letter']){
            iwj_get_template_part('applies/facebook/popup');
            ?>
            <script type="text/javascript">
                jQuery(window).on('load',function(){
                    jQuery('#iwj-modal-facebook-apply-<?php echo get_the_ID(); ?>').modal('show');
                });
            </script>
            <?php
            unset($_SESSION['iwj_fb_input_cover_letter']);
        }
    }
}