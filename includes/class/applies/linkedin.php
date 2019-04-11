<?php
class IWJ_Apply_Linkedin extends IWJ_Apply {

    public $oauth;

    function __construct()
    {
        parent::__construct();
        if(!class_exists('Linkedin_OAuth2Client')){
            require_once IWJ_PLUGIN_DIR . '/includes/class/socials/linkedin/OAuth2Client.php';
        }

        $this->oauth = new Linkedin_OAuth2Client($this->get_option('client_id'), $this->get_option('client_secret'));

        add_action( 'wp_ajax_iwj_submit_application_linkedin', array($this, 'submit_application'));
        add_action( 'wp_ajax_nopriv_iwj_submit_application_linkedin', array($this, 'submit_application'));

        add_action( 'wp_footer', array( $this, 'cover_letter_popup' ));

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    function get_title(){
        return __('Apply With Linkedin', 'iwjob');
    }

    function get_description(){
        return '';
    }

    function is_available()
    {
        if(parent::is_available() && $this->get_option('client_id') && $this->get_option('client_secret')){
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
                'id' 			=> 'client_id',
                'name'			=> __( 'Client ID' , 'iwjob' ),
                'type'			=> 'text',
            ),
            array(
                'id' 			=> 'client_secret',
                'name'			=> __( 'Client Secret' , 'iwjob' ),
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
                'desc'			=> __( 'Will automatically create user. If they log in with the same linkedin account they can view their applications' , 'iwjob' ),
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
        wp_register_style('iwj-apply-linkedin', IWJ_PLUGIN_URL.'/includes/class/applies/assets/linkedin-apply.css');
        wp_register_script('iwj-apply-linkedin', IWJ_PLUGIN_URL.'/includes/class/applies/assets/linkedin-apply.js', array('jquery'), false, true);
    }

    function apply_button($job){
        iwj_get_template_part('applies/linkedin/button', array('job'=>$job, 'self'=>$this));
    }

    public function get_apply_url($job){
        $this->oauth->redirect_uri = add_query_arg(array('iwj_social_apply'=> $this->id, 'job_id'=>$job->get_id()), home_url('/'));
        $state = wp_generate_password(12, false);
        return $this->oauth->authorizeUrl(array('scope' => 'r_basicprofile r_emailaddress', 'state' => $state));
    }

    public function oauth(){
        if(isset($_REQUEST['code']) && (!isset($_REQUEST['error']) || !$_REQUEST['error'])){
            $job_id = $_REQUEST['job_id'];
            $job = IWJ_Job::get_job($job_id);
            if($job && $job->can_apply() && (!isset($_COOKIE['iwj_linkedin_apply_' . $job_id]) || !$_COOKIE['iwj_linkedin_apply_' . $job_id])){
                $user_data = $this->get_linkedin_profile($job_id);
                if($this->get_option('allow_input_cover_letter')){
                    $_SESSION['iwj_linkedin_profile'] = $user_data;
                    $_SESSION['iwj_ld_input_cover_letter'] = true;
                    $link = get_the_permalink($job_id);
                    $link = add_query_arg(array('apply'=>'linkedin'), $link);

                    wp_redirect($link);
                    exit;
                }else{
                    $this->create_application($user_data, $job_id, $user_data->summary);
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

        if($_SESSION['iwj_linkedin_profile']){
            $job_id = $_REQUEST['job_id'];
            $message = wp_kses_post($_REQUEST['message']);
            $application_id = $this->create_application($_SESSION['iwj_linkedin_profile'], $job_id, $message);
            unset($_SESSION['iwj_linkedin_profile']);
            ob_start();
            iwj_get_template_part('applies/linkedin/thankyou', array('job_id'=>$job_id, 'application_id' => $application_id));
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
        $pdf->Cell(null, 10, "{$user_data->firstName} {$user_data->lastName}", 0, 1);
        $pdf->SetFont('DejaVu', '', 11);
        $pdf->Cell(null, 10, $user_data->headline, 0, 1);
        $pdf->Write(5, "{$user_data->emailAddress}, ");
        $pdf->SetTextColor(0, 0, 255);
        $pdf->SetFont('DejaVu', '', 9);
        $pdf->Write(5, $user_data->publicProfileUrl, $user_data->publicProfileUrl);

        $pdf->WriteHTML("<br /><br /><hr><br />");
        // End Block

        /* Start Block Summary */
        $pdf->SetFont('DejaVuBold', '', 17);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->Cell(null, 10, "Summary", 0, 1);

        $pdf->SetFont('DejaVu', '', 11);
        $pdf->SetTextColor(0, 0, 0);
        if (isset($user_data->summary)) {
            $pdf->WriteHTML("<br />" . preg_replace('/[\x00-\x1F\x80-\xFF]/', '', nl2br($user_data->summary)));
        } else {
            $pdf->WriteHTML("<br />No summary");
        }

        if($user_data->pictureUrl) {
            $pdf->Image($user_data->pictureUrl, 165, 10, null, null, "JPG");
        }
        $pdf->WriteHTML("<br /><br /><hr><br />");
        /* End Block Summary */

        /* Start Block Experience */
        $pdf->SetFont('DejaVuBold', '', 17);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->Cell(null, 10, "Experience", 0, 1);

        if (!empty($user_data->positions->values) && is_array($user_data->positions->values)) {
            foreach ($user_data->positions->values as $position) {
                $title = $position->title . ' at ' . $position->company->name;
                $start_date = date("F Y", mktime(0, 0, 0, $position->startDate->month, 0, $position->startDate->year));
                $end_date = ($position->isCurrent) ? "Today" : date("F Y", mktime(0, 0, 0, $position->endDate->month, 0, $position->endDate->year));
                $working_period = "$start_date - $end_date";

                $pdf->SetFont('DejaVuBold', '', 13);
                $pdf->SetTextColor(0, 0, 0);

                $pdf->Cell(null, 10, $title, 0, 1);

                $pdf->SetFont('DejaVu', '', 11);
                $pdf->SetTextColor(0, 0, 0);

                $pdf->Cell(null, 10, $working_period, 0, 1);

                if (isset($position->summary)) {
                    $pdf->WriteHTML("<br />{$position->summary}<br /><br />");
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

        if (!empty($user_data->educations->values) && is_array($user_data->educations->values)) {
            foreach ($user_data->educations->values as $education) {
                $title = $education->schoolName;
                $start_date = $education->startDate->year;
                $end_date = (empty($education->endDate)) ? "Today" : $education->endDate->year;
                $school_data = "{$education->degree}, {$education->fieldOfStudy} ($start_date - $end_date)";

                $pdf->SetFont('DejaVuBold', '', 13);
                $pdf->SetTextColor(0, 0, 0);

                $pdf->Cell(null, 10, $title, 0, 1);

                $pdf->SetFont('DejaVu', '', 11);
                $pdf->SetTextColor(0, 0, 0);

                $pdf->Cell(null, 10, $school_data, 0, 1);

                if (isset($education->notes)) {
                    $pdf->WriteHTML("<br />{$education->notes}<br /><br />");
                }
                $pdf->WriteHTML("<br />");
            }
        }

        $pdf->WriteHTML("<hr><br />");
        /* End Block Educations */

        /* Start Block Skills */
        $pdf->SetFont('DejaVuBold', '', 17);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->Cell(null, 10, "Skills", 0, 1);

        $pdf->SetFont('DejaVu', '', 11);
        $pdf->SetTextColor(0, 0, 0);

        //Create the list with skills
        $list = array();
        $list['bullet'] = chr(149);
        $list['margin'] = ' ';
        $list['indent'] = 0;
        $list['spacer'] = 0;
        $list['text'] = array();

        $i = 0;

        if (!empty($user_data->skills->values) && is_array($user_data->skills->values)) {
            foreach ($user_data->skills->values as $skill) {
                $list['text'][$i] = $skill->skill->name;
                $i++;
            }
        }

        $column_width = $pdf->w - 30;
        $pdf->SetX(10);
        $pdf->MultiCellBltArray($column_width - $pdf->x, 6, $list);

        $pdf->WriteHTML("<br /><hr><br />");
        /* End Block Skills */

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
        if (!empty($user_data->languages->values) && is_array($user_data->languages->values)) {
            foreach ($user_data->languages->values as $language) {
                $list['text'][$i] = $language->language->name;
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
        $file_name = uniqid('resume-' . $user_data->firstName . '-' . $user_data->lastName . '-') . '.pdf';
        $fn = $dir . $file_name;
        $fn_url = $upload_dir['baseurl'] . '/apply-source/'.$file_name;
        $pdf->Output($fn, 'F');
        /* generate pdf: end */

        //get_user_id

        $email = $user_data->emailAddress;

        $username=  $user_data->id;
        if($email){
            $user = get_user_by('email', $email);
        }else{
            $email = $username.'@linkedin.com';
            $username = $username.'.'.$this->id;
            $user = get_user_by('login', $username);
        }

        $user_id = 0;
        if($user){
            $user_id = $user->ID;
        }elseif(
            $this->get_option('create_user')){
            $user_data = array();
            $user_data['user_login'] = $username;
            $user_data['first_name'] = $user_data->firstName;
            $user_data['last_name'] = $user_data->lastName;
            $user_data['display_name'] = $user_data->firstName . ' ' . $user_data->lastName;
            $user_data['user_pass'] = wp_generate_password();;
            $user_data['user_email'] = $email;
            $user_data['role'] = 'iwj_candidate';
            $user_id = wp_insert_user($user_data);
            if ($user_data->pictureUrl) {
                update_user_meta($user_id, IWJ_PREFIX . 'avatar', $user_data->pictureUrl);
            }
        }

        $post_data = array(
            'post_title' => $user_data->firstName . ' ' . $user_data->lastName,
            'post_content' => $message,
            'post_type' => 'iwj_application',
            'post_status' => 'pending',
            'post_author' => $user_id,
        );

        $post_id = wp_insert_post($post_data);
        if($post_id){

            update_post_meta($post_id, IWJ_PREFIX.'job_id', $job_id);
            update_post_meta($post_id, IWJ_PREFIX.'full_name', $user_data->firstName . ' ' . $user_data->lastName);
            update_post_meta($post_id, IWJ_PREFIX.'email', $user_data->emailAddress);
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

            setcookie('iwj_linkedin_apply_' . $job_id, 1, time() + 60 * 60 * 24 * 30, SITECOOKIEPATH);

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

    private function get_linkedin_profile($job_id) {

        // Use GET method since POST isn't working
        $this->oauth->redirect_uri = add_query_arg(array('iwj_social_apply'=> $this->id, 'job_id'=>$job_id), home_url('/'));
        $this->oauth->curl_authenticate_method = 'GET';

        // Request access token
        $response = $this->oauth->authenticate($_REQUEST['code']);
        $this->access_token = $response->{'access_token'};

        $opts = array(
            'http' => array(
                'method' => 'GET',
                'header' => "Authorization: Bearer " . $this->access_token . "\r\n" . "x-li-format: json\r\n"
            )
        );

        $resource = '/v1/people/~:(id,email-address,first-name,last-name,picture-url,phone-numbers,main-address,headline,date-of-birth,location:(name,country:(code)),industry,summary,specialties,positions,educations,site-standard-profile-request,public-profile-url,interests,publications,languages,skills,certifications,courses,volunteer,honors-awards,last-modified-timestamp,recommendations-received)';
        // Need to use HTTPS
        $url = 'https://api.linkedin.com' . $resource;

        // Append query parameters (if there are any)
        //if (count($params)) { $url .= '?' . http_build_query($params); }

        // Tell streams to make a (GET, POST, PUT, or DELETE) request
        // And use OAuth 2 access token as Authorization
        $context = stream_context_create($opts);

        // Hocus Pocus
        $response = file_get_contents($url, false, $context);
        // Native PHP object, please

        return json_decode($response);
    }

    public function cover_letter_popup(){
        if(isset($_SESSION['iwj_ld_input_cover_letter']) && $_SESSION['iwj_ld_input_cover_letter']){
            iwj_get_template_part('applies/linkedin/popup');
            ?>
            <script type="text/javascript">
                jQuery(window).on('load',function(){
                    jQuery('#iwj-modal-linkedin-apply-<?php echo get_the_ID(); ?>').modal('show');
                });
            </script>
            <?php
            unset($_SESSION['iwj_ld_input_cover_letter']);
        }
    }
}