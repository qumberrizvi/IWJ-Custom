<?php
$cookie = 'iwj_linkedin_apply_' . $job->get_id();
if(isset($_COOKIE[$cookie]) && $_COOKIE[$cookie]){
	echo '<a href="javascript:void(0)" class="apply-job applied" data-toggle="tooltip" title="' . esc_html__( 'LINKEDIN APPLIED', 'iwjob' ) . '" ><i class="fa fa-linkedin"></i></a>';
}else{
    wp_enqueue_style('iwj-apply-linkedin');
    wp_enqueue_script('iwj-apply-linkedin');
    $url = $self->get_apply_url($job);
    echo '<a href="'.esc_url($url).'" class="apply-job apply-with-linkedin" data-toggle="tooltip" title="' . esc_html__( 'LINKEDIN APPLY', 'iwjob' ) . '"><i class="fa fa-linkedin"></i></a>';
}
?>
