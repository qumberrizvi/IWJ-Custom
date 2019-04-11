<?php
wp_enqueue_style('owl-carousel');
wp_enqueue_style('owl-theme');
wp_enqueue_style('owl-transitions');
wp_enqueue_script('owl-carousel');

echo $args['before_widget'];

if (isset($instance['title'])) {
    $title = (!empty($instance['title']) ) ? $instance['title'] : '';
    $title = apply_filters('widget_title', $title, $instance, $widget_id);

    if ($title) {
        echo $args['before_title'] . $title . $args['after_title'];
    }
}
$user = IWJ_User::get_user();
$show_candidate_public_profile = iwj_option('show_candidate_public_profile', '');
$login_page_id = get_permalink(iwj_option('login_page_id'));
?>
<div class="iwj-widget-candidates owl-carousel">
    <?php
    foreach ($candidates as $candidate) :
        $candidate = IWJ_Candidate::get_candidate($candidate);
        $desc = $candidate->get_description();
        $image            = iwj_get_avatar( $candidate->get_author_id(), '', '', $candidate->get_title(), array('img_size'=>'thumbnail') );
        ?>
            <div class="candidate-item">
                <div class="candidate-image"><?php echo $image; ?></div>
                <div class="candidate-info">
                    <div class="info-top">
                        <div class="candidate-avatar"><?php echo $image; ?></div>
                        <h3 class="name theme-color"><?php echo $candidate->get_title(); ?></h3>
                        <?php if ($candidate->get_headline()) : ?>
                            <div class="candidate-headline"><?php echo $candidate->get_headline(); ?></div>
                        <?php endif; ?>
                        <?php if (iwj_option('view_free_resum') || ( $user && $user->can_view_resum($candidate->get_id()) )) { ?>
                            <div class="iwj-social-link">
                                <ul>
                                    <?php
                                    foreach ($candidate->get_social_media() as $key => $value) {
                                        if ($value != null && $value != '') {
                                            if ($key == "google_plus") {
                                                echo '<li><a class="google-plus" href="' . $value . '" title="' . $key . '"><i class="ion-social-googleplus"></i></a></li>';
                                            } else {
                                                echo '<li><a class="' . $key . '" href="' . $value . '" title="' . $key . '"><i class="ion-social-' . $key . '"></i></a></li>';
                                            }
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        <?php } ?>
                        <?php if ($desc) : ?>
                            <div class="desc"><?php echo esc_attr(wp_trim_words($desc, 10)); ?></div>
                        <?php endif; ?>
                        <?php
                        if (!$show_candidate_public_profile) {
                            $link_profile = $candidate->permalink();
                        } else {
                            if ($user) {
                                if ($show_candidate_public_profile == 1) {
                                    $link_profile = $candidate->permalink();
                                } else {
                                    if ($user->is_employer()) {
                                        $link_profile = $candidate->permalink();
                                    } else {
                                        $link_profile = '';
                                    }
                                }
                            } else {
                                $link_profile = add_query_arg('redirect_to', $candidate->permalink(), $login_page_id);
                            }
                        }
                        ?>
                        <?php if ($link_profile): ?>
                            <a class="view-candidate" href="<?php echo $link_profile; ?>"><?php echo __("View Profile", "iwjob"); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php
    endforeach;
    ?>
</div>
<?php
echo $args['after_widget'];
