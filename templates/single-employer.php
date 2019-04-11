<?php
/**
 * The Template for displaying all single posts
 * @package injob
 */
get_header();
//wp_enqueue_style('jquery-fancybox');
//wp_enqueue_script('jquery-fancybox');
wp_enqueue_style('ionicons');
wp_enqueue_script('google-maps');
wp_enqueue_script('infobox');
wp_enqueue_script('iwj-rating-custom');

$user = IWJ_User::get_user();
$employer_sidebar = iwj_option('employer_sidebar');
$disable_review = iwj_option('disable_review');
$show_employer_public_profile = iwj_option('show_employer_public_profile', '');
$login_page_id = get_permalink(iwj_option('login_page_id'));

while (have_posts()) : the_post();
    $employer = IWJ_Employer::get_employer(get_the_ID());
    $employer_details_version = iwj_option('employer_details_version');
    $template_detail_version = $employer->get_template_detail_version();
    if ($template_detail_version) {
        $details_versions = $template_detail_version;
    } else {
        $details_versions = $employer_details_version;
    }
    if (!$details_versions) {
        $details_versions = 'v1';
    }
    ?>
    <div class="iwj-employer-detail <?php echo esc_attr($details_versions); ?>">
        <?php
        iwj_get_template_part("parts/employer-details/employer-detail-" . $details_versions, array('user' => $user, 'employer' => $employer, 'disable_review' => $disable_review, 'employer_sidebar' => $employer_sidebar, 'show_employer_public_profile' => $show_employer_public_profile, 'login_page_id' => $login_page_id));
        ?>
    </div>
    <?php
endwhile;
?>


<?php get_footer(); ?>