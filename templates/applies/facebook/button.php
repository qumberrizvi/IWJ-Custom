<?php
$cookie = 'iwj_facebook_apply_' . $job->get_id();
if(isset($_COOKIE[$cookie]) && $_COOKIE[$cookie]){
    echo '<a href="javascript:void(0)" class="apply-job apply-with-facebook applied" data-toggle="tooltip" title="' . esc_html__( 'FACEBOOK APPLIED', 'iwjob' ) . '"><i class="fa fa-facebook"></i></a>';
}else{
    wp_enqueue_style('iwj-apply-facebook');
    wp_enqueue_script('iwj-apply-facebook');
    $url = $self->get_apply_url($job);
    echo '<a href="'.esc_url($url).'" class="apply-job apply-with-facebook" data-toggle="tooltip" title="' . esc_html__( 'FACEBOOK APPLIED', 'iwjob' ) . '" ><i class="fa fa-facebook"></i></a>';
}
?>
