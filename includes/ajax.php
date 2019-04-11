<?php
add_action( 'wp_ajax_iwj_send_queue_email', 'iwj_send_queue_email' );
add_action( 'wp_ajax_nopriv_iwj_send_queue_email', 'iwj_send_queue_email' );

function iwj_send_queue_email() {
    if(!defined( 'WP_CACHE' ) || !WP_CACHE){
        check_ajax_referer('iwj-security');
    }

    if(IWJ_Email_Queue::count_emails() > 0){
        IWJ_Email_Queue::send_emails();
    }

    exit;
}

add_action( 'wp_ajax_iwj_send_all_email', 'iwj_send_all_email' );
add_action( 'wp_ajax_nopriv_iwj_send_all_email', 'iwj_send_all_email' );

function iwj_send_all_email() {

    global $wpdb;
    $max_email_send_in_one = iwj_option('max_email_send_in_one', 5);
    $max_attemp = iwj_option('max_email_attemp', 5);

    $step = isset($_POST['step']) ? $_POST['step'] : 1 ;

    $current = (int)($max_email_send_in_one * $step);

    if ($step == 1) {
        $sql = "SELECT COUNT(*) AS total FROM {$wpdb->prefix}iwj_email_queue WHERE attemp <= %d";
        $sql = $wpdb->prepare($sql, $max_attemp);

        $total = $wpdb->get_var($sql);

        $rest = $total;

    }else{

        $total = (int) isset($_POST['total']) ? $_POST['total'] : 0;

        $rest = $total - $current;
    }

    if ($rest > 0) {
        IWJ_Email_Queue::send_emails(array(), $current);
    }

    $return = array( 'status' => 1, 'total' => $total, 'step' => (int)$step);

    if ($rest > 0) {
        $return['next'] = 1;
    } else {
        $return['next'] = 0;
    }

    $return['current'] = $current;

    if ($total > 0) {
        if ($current < $total) {
            $return['percent'] = ($current/$total) * 100;
        } else {
            $return['percent'] = 100;
        }

    } else {
        $return['percent'] = 0;
    }

    $return['percent'] = ($return['percent'] > 100) ? 100 : $return['percent'];

    echo json_encode($return); die();

}

//
add_action( 'wp_ajax_iwj_filter_and_count_jobs', 'iwj_filter_and_count_jobs' );
add_action( 'wp_ajax_nopriv_iwj_filter_and_count_jobs', 'iwj_filter_and_count_jobs' );

add_action( 'wp_ajax_iwj_filter_and_count_candidates', 'iwj_filter_and_count_candidates' );
add_action( 'wp_ajax_nopriv_iwj_filter_and_count_candidates', 'iwj_filter_and_count_candidates' );

add_action( 'wp_ajax_iwj_filter_and_count_employers', 'iwj_filter_and_count_employers' );
add_action( 'wp_ajax_nopriv_iwj_filter_and_count_employers', 'iwj_filter_and_count_employers' );


if ( !function_exists('iwj_filter_and_count_jobs')) {
    function iwj_filter_and_count_jobs() {

        check_ajax_referer('iwj-security');

        ob_start();

        $data_filters = IWJ_Job_Listing::get_data_filters(false);

        $query = IWJ_Job_Listing::get_query_jobs($data_filters);

        iwj_get_template_part('parts/jobs/jobs', array('query' => $query, 'paged' => $data_filters['paged']));

        $html = ob_get_contents();

        ob_end_clean();

        $url = IWJ_Job_Listing::get_request_url( $data_filters );
        $feed_url = IWJ_Job_Listing::get_feed_url( $data_filters );

        $count_jobs = IWJ_Job_Listing::count_jobs_in_taxonomy($data_filters);

        $return = array( 'status' => 1, 'html' => $html, 'count_jobs' => $count_jobs, 'url' => $url, 'feed_url' => $feed_url);
        echo json_encode($return); die();
    }
}

if ( !function_exists('iwj_filter_and_count_candidates')) {
    function iwj_filter_and_count_candidates() {

        check_ajax_referer('iwj-security');

        ob_start();

        $data_filters = IWJ_Candidate_Listing::get_data_filters(false);

        $query = IWJ_Candidate_Listing::get_query_candidates($data_filters);

        $view_mode = isset($_POST['mode']) ? $_POST['mode'] : 'list';
        if ($view_mode == 'grid') {
            iwj_get_template_part('parts/candidates/candidates-grid', array('query' => $query, 'paged' => $data_filters['paged']));
        } else {
            iwj_get_template_part('parts/candidates/candidates-list', array('query' => $query, 'paged' => $data_filters['paged']));
        }

        $html = ob_get_contents();

        ob_clean();

        $url = IWJ_Candidate_Listing::get_request_url( $data_filters );
        $feed_url = IWJ_Candidate_Listing::get_feed_url( $data_filters );

        $count_candidates = IWJ_Candidate_Listing::count_candidates_in_taxonomy( $data_filters );

        $return = array( 'status' => 1, 'html' => $html, 'count_candidates' => $count_candidates, 'url' => $url, 'feed_url' => $feed_url);
        echo json_encode($return); die();

    }
}

if ( !function_exists('iwj_filter_and_count_employers')) {
    function iwj_filter_and_count_employers() {

        check_ajax_referer('iwj-security');

        ob_start();

        $data_filters = IWJ_Employer_Listing::get_data_filters();
        $query = IWJ_Employer_Listing::get_query_employers($data_filters);

        $view_mode = isset($_POST['mode']) ? $_POST['mode'] : 'list';
        if ($view_mode == 'grid') {
            iwj_get_template_part('parts/employers/employers-grid', array('query' => $query, 'paged' => $data_filters['paged']));
        } else {
            iwj_get_template_part('parts/employers/employers-list', array('query' => $query, 'paged' => $data_filters['paged']));
        }

        $html = ob_get_contents();

        ob_clean();

        $url = IWJ_Employer_Listing::get_request_url($data_filters);
        $feed_url = IWJ_Employer_Listing::get_feed_url( $data_filters );

        $count_employers = IWJ_Employer_Listing::count_employers_in_taxonomy( $data_filters );

        $return = array( 'status' => 1, 'html' => $html, 'count_employers' => $count_employers, 'url' => $url, 'feed_url' => $feed_url);
        echo json_encode($return); die();

    }
}

add_action( 'wp_ajax_get_locations_data', 'get_locations_data' );
add_action( 'wp_ajax_nopriv_get_locations_data', 'get_locations_data' );

function get_locations_data(){
    if(!defined( 'WP_CACHE' ) || !WP_CACHE){
        check_ajax_referer( 'iwj-security' );
    }

    $data_filters = IWJ_Job_Listing::get_data_filters();
    $paged = isset($data_filters['paged']) ? $data_filters['paged'] : 1;
    $query = IWJ_Job_Listing::get_query_jobs($data_filters);

    ob_start();
    if($query->have_posts()){
        iwj_get_template_part('parts/jobs/jobs', array('query' => $query, 'paged' => $paged, 'type' => 2 ) );
    } else {
        ?>
         <div class="alert alert-danger" role="alert"><?php _e('No result found ', 'iwjob') ?></div>
        <?php
    }

    $html = ob_get_contents();
    ob_end_clean();
    if ( $query->have_posts() ) {
        $result['status'] = 1;
    }
    $result['html'] = $html;
    $user_login = 0;
    if ($query->have_posts()) :
        $user = IWJ_User::get_user();
        while ($query->have_posts()) :
            $query->the_post();
            $job = IWJ_Job::get_job( get_the_ID() );
            $latlng = $job->get_map();
            $author = $job->get_author();
            $latlng = array_slice( $latlng, 0, 2 );
            $type = $job->get_type();
            $color = get_term_meta( $type->term_id, IWJ_PREFIX.'color', true );
            $link_type = get_term_link( $type->term_id, 'iwj_type' );
            if ( is_user_logged_in() ){
                $savejobclass = $user->is_saved_job( $job->get_id() ) ? 'saved' : '';
                $user_login = 1;
            }else{
                $savejobclass = '';
            }

            $result['data'][] = array(
                'location' =>  array(
                    'lat' => (float)$latlng[0],
                    'lng' => (float)$latlng[1],
                ),
                'ID'        => $job->get_id(),
                'salary'    => $job->get_salary(),
                'company_name' => $author->get_display_name(),
                'company_link' => $author->permalink(),
                'address'   => $job->get_locations_links(),
                'color'     => $color,
                'link_type' => $link_type,
                'type_name' => $type->name,
                'type'      => $type,
                'title'     => $job->get_title(),
                'link'      => esc_url( $job->permalink () ),
                'savejobclass'=> $savejobclass,
                'user_login' => $user_login,
            );
        endwhile;
        wp_reset_postdata();
    endif;
    wp_send_json($result);
}

//add_action( 'wp_ajax_iwj_membership_expiry_notice', array( 'IWJ_Controller','membership_expiry_notice') );
//add_action( 'wp_ajax_iwj_membership_expired_notice', array( 'IWJ_Controller','membership_expired_notice') );