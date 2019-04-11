<?php
/**
 * The Template for displaying all single posts
 * @package injob
 */
$user = IWJ_User::get_user();
$candidate = IWJ_Candidate::get_candidate(get_the_ID());
get_header();
$sidebar_position = Inwave_Helper::getPostOption('sidebar_position', 'sidebar_position');
$candidate_sidebar = iwj_option('candidate_sidebar');
wp_enqueue_style('jquery-fancybox');
wp_enqueue_script('jquery-fancybox');
wp_enqueue_script('google-maps');
wp_enqueue_script('infobox');
$show_candidate_public_profile = iwj_option('show_candidate_public_profile', '');
$login_page_id = get_permalink(iwj_option('login_page_id'));

$candidate_details_version = iwj_option('candidate_details_version');
$template_detail_version = $candidate->get_template_detail_version();
if ($template_detail_version) {
    $details_versions = $template_detail_version;
} else {
    $details_versions = $candidate_details_version;
}
if (!$details_versions) {
    $details_versions = 'v1';
}
?>
<div class="iwj-candicate-detail <?php echo esc_attr($details_versions); ?>">
    <?php
    iwj_get_template_part("parts/candidate-details/candidate-detail-" . $details_versions, array('user' => $user, 'candidate' => $candidate, 'sidebar_position' => $sidebar_position, 'candidate_sidebar' => $candidate_sidebar, 'show_candidate_public_profile' => $show_candidate_public_profile, 'login_page_id' => $login_page_id));
    ?>
</div>

<?php
get_footer();
