<?php
class IWJ_Application{
    static $cache = array();

    public $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    static function get_application($post = null, $force = false){
        $post_id = 0;
        if($post === null){
            $post = get_post();
        }
        if(is_numeric($post)){
            $post = get_post($post);
            if($post && !is_wp_error($post)){
                $post_id = $post->ID;
            }
        }
        elseif(is_object($post))
        {
            $post_id = $post->ID;
        }

        if($post_id){

            if($force){
                clean_post_cache( $post_id );
                $post = get_post($post_id);
            }

            if($force || !isset(self::$cache[$post_id])){
                self::$cache[$post_id] = new IWJ_Application($post);
            }

            return self::$cache[$post_id];
        }

        return null;
    }
    
    public function get_id(){
        return $this->post->ID;
    }

    public function get_status(){
        return $this->post->post_status;
    }

    public function get_title($original = false){
        if($original){
            return $this->post->post_title;
        }

        return get_the_title($this->post->ID);
    }

    public function get_full_name(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'full_name', true);
    }

    public function get_email(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'email', true);
    }

    public function get_cv(){
        $cv = get_post_meta($this->get_id(), IWJ_PREFIX.'cv', true);

        if ( ! $path = get_attached_file( $cv ) ) {
            return false;
        }

        return wp_parse_args( array(
            'ID'    => $cv,
            'name'  => basename( $path ),
            'path'  => $path,
            'url'   => wp_get_attachment_url( $cv ),
            'title' => get_the_title( $cv ),
        ), wp_get_attachment_metadata( $cv ) );
    }

    public function get_created($format = ''){
        if(!$format){
            $format = get_option('date_format');
        }
        if($this->post->post_date){
            return date_i18n($format, strtotime($this->post->post_date));
        }

        return '';
    }

    public function get_message($filter = true){
        $content = $this->post->post_content;

        if($filter){
            $content = strip_shortcodes($content);
            $content = apply_filters('the_content', $content);
        }

        return $content;
    }
    public function get_private_note(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'private_note', true);
    }
    public function get_source(){
        $source = get_post_meta($this->get_id(), IWJ_PREFIX.'source', true);
        if(!$source){
            $source = 'form';
        }

        return $source;
    }

    public function get_author_id(){
        return $this->post->post_author;
    }

    public function get_author(){
        return IWJ_User::get_user($this->get_author_id());
    }

    public function view_permalink(){
        $dashboard = iwj_get_page_permalink('dashboard');
        return add_query_arg(array('iwj_tab'=> 'view-application', 'application-id' => $this->get_id()), $dashboard);
    }

    public function get_job_id(){
        return get_post_meta($this->get_id(), IWJ_PREFIX.'job_id', true);
    }

    public function get_job(){
        return IWJ_Job::get_job($this->get_job_id());
    }

    public function can_update(){
        $job = $this->get_job();
        if($job->get_author_id() != get_current_user_id()){
            return false;
        }

        return true;
    }

    public function can_send_email_to_candidate(){
        $job = $this->get_job();
        if($job->get_author_id() != get_current_user_id()){
            return false;
        }

        return true;
    }

    public function can_view(){
        $job = $this->get_job();
        if($job && $job->get_author_id() == get_current_user_id()){
            return true;
        }

        return false;
    }


    public function update(){
        $private_note = sanitize_text_field($_POST['private_note']);
        update_post_meta($this->get_id(), IWJ_PREFIX.'private_note', $private_note);

        $status = sanitize_text_field($_POST['application_status']);
        if($status == 'pending' || $status == 'publish' || $status == 'iwj-rejected'){
            wp_update_post(array('ID' => $this->get_id(), 'post_status' => $status));
        }
    }

    function get_field_value($field_name){
        $value = get_post_meta($this->get_id(), IWJ_PREFIX.$field_name, true);
        return apply_filters('iwj_application_field_value', $value, $this);
    }

    static function get_status_array(){
        return array(
            'publish' => __('Approved', 'iwjob'),
            'pending' => __('Pending', 'iwjob'),
            'iwj-rejected' => __('Rejected', 'iwjob'),
            //'iwj-trash' => __('Trash', 'iwjob'),
        );
    }

    static function get_status_title($status){
        $status_arr = self::get_status_array();
        if(isset($status_arr[$status])){
            return $status_arr[$status];
        }

        return '';
    }

    static function get_core_fields(){
        $core_fields = array(
            'full_name' => array(
                'name' => 'Fullname',
                'id' => IWJ_PREFIX.'full_name',
                'type' => 'text',
                'required' => '1',
                'pre_value' => '',
            ),
            'email' => array(
                'name' => 'Email',
                'id' => IWJ_PREFIX.'email',
                'type' => 'email',
                'required' => '1',
                'pre_value' => '',
            ),
            'message' => array(
                'name' => 'Message',
                'id' => IWJ_PREFIX.'message',
                'type' => 'wysiwyg',
                'required' => '1',
                'pre_value' => '',
            ),
            'cv' => array(
                'name' => 'Curriculum Vitae',
                'id' => IWJ_PREFIX.'cv',
                'type' => 'file',
                'required' => '1',
                'pre_value' => 'pdf,zip,doc,docx',
            ),
        );

        $form_fields = IWJ_Apply_Form::get_form_fields();
        foreach ($core_fields as $key=>$core_field){
            if(isset($form_fields[$key])){
                $core_fields[$key] = $form_fields[$key];
            }
        }

        return apply_filters('iwj_apply_core_fields', $core_fields);
    }

    static function get_core_field_names(){
        $core_field_names = array(
            'full_name',
            'email',
            'message',
            'cv',
        );

        return apply_filters('iwj_apply_core_field_names', $core_field_names);

    }

    static function get_emails(){
        $application_emails = array();
        $application_emails['interview'] = array(
            'id' => 'interview',
            'title' => __('Interview Letter', 'iwjob'),
            'subject' => iwj_option('email_application_interview_subject'),
            'message' => iwj_option('email_application_interview_message'),
        );
        $application_emails['accept'] = array(
            'id' => 'accept',
            'title' => __('Accept Letter', 'iwjob'),
            'subject' => iwj_option('email_application_accept_subject'),
            'message' => iwj_option('email_application_accept_message'),
        );
        $application_emails['reject'] = array(
            'id' => 'reject',
            'title' => __('Reject Letter', 'iwjob'),
            'subject' => iwj_option('email_application_reject_subject'),
            'message' => iwj_option('email_application_reject_message'),
        );

        return apply_filters('iwj_application_emails', $application_emails);
    }

}