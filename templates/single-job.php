<?php
/**
 * The Template for displaying all single posts
 * @package injob
 */
get_header();
$job = IWJ_Job::get_job(get_post());
$author = $job->get_author();
$get_more_details = $job->get_more_details();
$user = IWJ_User::get_user();
$job_sidebar = iwj_option('job_sidebar');
wp_enqueue_script('google-maps');
wp_enqueue_script('infobox');

$job_details_version     = iwj_option( 'job_details_version', 'v1' );
$template_detail_version = $job->get_template_detail_version();
$details_versions        = $template_detail_version ? $template_detail_version : $job_details_version;
?>

<div class="contents-main iw-job-content iw-job-detail <?php echo esc_attr($details_versions); ?>" id="contents-main">
    <?php iwj_get_template_part( "parts/job-details/job-detail-" . $details_versions, array( 'user' => $user, 'job' => $job, 'author' => $author, 'job_sidebar' => $job_sidebar, 'get_more_details' => $get_more_details ) ); ?>
    <?php if ($job->has_status('draft')) { ?>
        <div class="iwj-job-action-btn">
            <a class="edit-job iwj-btn-shadow iwj-btn-icon iwj-btn-danger" href="<?php echo $job->edit_draft_link(); ?>"><?php echo __('<i class="ion-ios-compose"></i> Edit', 'iwjob'); ?></a>
            <a class="publish-job iwj-btn-shadow iwj-btn-icon iwj-btn-primary" href="<?php echo $job->publish_draft_link(); ?>"><?php echo __('<i class="ion-android-send"></i> Publish', 'iwjob'); ?></a>
        </div>
    <?php } ?>
</div>

<?php get_footer(); ?>