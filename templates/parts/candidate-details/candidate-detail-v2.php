<?php
wp_enqueue_script('jquery-parallax');
wp_enqueue_script('candidate-single');
?>
<div class="iwj-candicate-detail-v2 iwj-single-parallax" itemprop="customer" itemscope itemtype="http://schema.org/Person">

        <?php
        $cover_image_url = '';
        $cover_image = $candidate->get_cover_image();
        if ($cover_image) {
            $cover_image = wp_get_attachment_url( $cover_image[0] );
            $cover_image_url = $cover_image;
        }else {
            $cover_image_url = IWJ_PLUGIN_URL . '/assets/img/candidate-parallax-default.jpg';
        }
        ?>
        <?php
        if (!$show_candidate_public_profile) {
            if ($candidate->get_public_account() || get_current_user_id() == $candidate->get_author_id()) {
                $check = 1;
            } else {
                $check = 2;
            }
        } else {
            if (is_user_logged_in()) {
                if ($show_candidate_public_profile == 2) {
                    if ($user->is_employer() && $candidate->get_public_account()) {
                        $check = 3;
                    } else {
                        $check = 4;
                    }
                } else {
                    if ($candidate->get_public_account() || get_current_user_id() == $candidate->get_author_id()) {
                        $check = 1;
                    } else {
                        $check = 2;
                    }
                }
            } else {
                $check = 0;
            }
        }

        if (!$check) {
            ?>
            <div class="iwj-alert-box">
                <div class="container">
                    <span>
                        <?php echo sprintf(__('You must be logged in to view this page. <a href="%s">Login here</a>', 'iwjob'), add_query_arg('redirect_to', $candidate->permalink(), $login_page_id)); ?>
                    </span>
                </div>
            </div>
        <?php
        } else {
            if ($check == 2) {
                ?>
                <div class="iwj-alert-box">
                    <div class="container">
                        <span>
                            <?php echo esc_html__('This profile is not public now.', 'iwjob'); ?>
                        </span>
                    </div>
                </div>
            <?php } elseif ($check == 4) {
                ?>
                <div class="iwj-alert-box">
                    <div class="container">
                        <span>
                            <?php echo esc_html__('This profile is not public or only employers can see.', 'iwjob'); ?>
                        </span>
                    </div>
                </div>
            <?php } else {
                ?>
                <div class="iw-parallax" data-iw-paraspeed="0.1" style="background-image: url('<?php echo esc_url($cover_image_url); ?>');"></div>
                <div class="iw-parallax-overlay"></div>
                <div class="content-top">
                    <div class="container">
                        <div class="info-top">
                            <div class="candidate-logo">
                                <?php echo $candidate->get_avatar(120); ?>
                            </div>
                            <div class="info-inner">
                                <div class="title-headline">
                                    <h3 class="candidate-name" itemprop="name">
                                        <?php echo $candidate->get_title(); ?>
                                    </h3>
                                    <h4 class="candidate-headline">
                                        <?php echo $candidate->get_headline(); ?>
                                    </h4>
                                </div>

                                <?php if (iwj_option('view_free_resum') || ( $user && $user->can_view_resum($candidate->get_id()) )) { ?>
                                    <div class="can-view-resum">
                                        <div class="candidate-info">
											<meta itemprop="address" content="<?php echo $candidate->get_address(); ?>" />
                                            <ul>
                                                <?php if ($candidate->get_email()) { ?>
                                                    <li class="candidate-email">
                                                        <i class="ion-android-mail"></i>
                                                        <h6 class="info-value" itemprop="email"><?php echo $candidate->get_email(); ?></h6>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($candidate->get_phone()) { ?>
                                                    <li class="candidate-phone">
                                                        <i class="ion-ios-telephone"></i>
                                                        <h6 class="info-value" itemprop="telephone"><?php echo $candidate->get_phone(); ?></h6>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                        <div class="iwj-button-loader detail-action-button">
                                            <?php
                                            $cv = $candidate->get_cv();
                                            if (isset($cv['url']) && $cv['url'] != null) {
                                                ?>
                                                <a href="<?php echo $cv['url']; ?>" class="iwj-btn action-button follow iwj-download-cv" target="_blank"><?php echo __('<i class="ion-android-download"></i> Download CV', 'iwjob'); ?></a>
                                            <?php } ?>
                                            <?php
                                            if (current_user_can('create_iwj_jobs')) {
                                                $saved = $user && $user->is_saved_resum(get_the_ID()) ? true : false;
                                                $save_text = $saved ? __('<i class="fa fa-heart"></i> Saved resume', 'iwjob') : __('<i class="fa fa-heart"></i> Save resume', 'iwjob');
                                                ?>
                                                <a href="#" class="iwj-btn action-button follow iwj-save-resume <?php echo $saved ? 'saved' : ''; ?>" data-id="<?php echo $candidate->get_id(); ?>"><?php echo $save_text; ?></span></a>
                                            <?php } elseif (!$user) { ?>
                                                <a href="#" data-toggle="modal" data-target="#iwj-login-popup" class="iwj-btn action-button follow"><?php echo __('<i class="fa fa-heart"></i> Save resume', 'iwjob'); ?></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="view-infomation detail-action-button">
                                        <h6>
                                            <?php if (current_user_can('create_iwj_jobs')) { ?>
                                                <a href="#" data-toggle="modal" data-target="#iwj-modal-view-<?php echo $candidate->get_id(); ?>"><i class="ion-ios-eye"></i> <?php echo __('View full information', 'iwjob'); ?>
                                                </a>
                                            <?php } elseif (!$user) { ?>
                                                <a href="#" data-toggle="modal" data-target="#iwj-login-popup"><i class="ion-ios-eye"></i> <?php echo __('View full information', 'iwjob'); ?>
                                                </a>
                                            <?php } else { ?>
                                                <a class="hidden-info" href="#"><?php echo __('Hidden Information', 'iwjob'); ?></a>
                                            <?php } ?>
                                        </h6>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div id="iwj-detail-menu" class="candidate-detail-menu">
                        <div class="container">
                            <div class="iwj-detail-menu">
                                <ul class="menu">
                                    <li><a href="#about-me"><?php echo __('About Me', 'iwjob'); ?></a></li>
                                    <li><a href="#work-experience"><?php echo __('Work Experience', 'iwjob'); ?></a></li>
                                    <li><a href="#education"><?php echo __('Education', 'iwjob'); ?></a></li>
                                    <li><a href="#honors-awards"><?php echo __('Honors & Awards', 'iwjob'); ?></a></li>
                                    <li><a href="#skills"><?php echo __('Skills', 'iwjob'); ?></a></li>
                                    <li><a href="#portfolio"><?php echo __('Portfolio', 'iwjob'); ?></a></li>
                                </ul>
                                <?php if (iwj_option('view_free_resum') || ( $user && $user->can_view_resum($candidate->get_id()) )) { ?>
                                    <div class="social-link">
                                        <ul class="iw-social-all hover-bg">
                                            <?php
                                            foreach ($candidate->get_social_media() as $key => $value) {
                                                if ($value != null && $value != '') {
                                                    if ($key == "google_plus") {
                                                        echo '<li><a class="' . $key . '" href="' . $value . '" title="' . $key . '"><i class="ion-social-googleplus"></i></a></li>';
                                                    } else {
                                                        echo '<li><a class="' . $key . '" href="' . $value . '" title="' . $key . '"><i class="ion-social-' . $key . '"></i></a></li>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-bottom">
                    <div class="container">
                        <div class="row">
                            <div class="<?php echo esc_attr(inwave_get_classes('container', $candidate_sidebar)); ?>">
                                <div class="candicate-main-content">
                                    <?php if ($candidate->get_description() || $candidate->get_video()) { ?>
                                        <div id="about-me" class="candidate-detail-video-desc">
                                            <?php if ($candidate->get_video()) {

                                                $candidate_video = $candidate->get_video();
                                                $iframe_video_string = wp_oembed_get( $candidate_video[0]);
                                                preg_match('/src="([^"]+)"/', $iframe_video_string, $match_video);
                                                $url_video = $match_video[1];

                                                $video_poster_url = '';
                                                $video_poster = $candidate->get_video_poster();
                                                if ($video_poster) {
                                                    $video_poster = wp_get_attachment_url( $video_poster[0] );
                                                    $video_poster = inwave_resize( $video_poster, 770, 435, true );
                                                    $video_poster_url = $video_poster;
                                                }else {
                                                    $video_poster_url = IWJ_PLUGIN_URL . '/assets/img/video-poster-candidate.jpg';
                                                }
                                                ?>
                                                <div class="iwj-candidate-video" data-embed="<?php echo esc_attr($url_video); ?>" style="background-image: url('<?php echo esc_url($video_poster_url); ?>')">
                                                    <div class="play-button"><span><i class="ion-ios-play-outline"></i></span><h6 class="title-button"><?php echo __('Watch our story', 'iwjob'); ?></h6></div>
                                                </div>
                                            <?php } ?>
                                            <?php if ($candidate->get_description()) { ?>
                                                <div class="candidate-detail-desc">
                                                    <h3 class="title-block-desc"><?php echo __('ABOUT ME', 'iwjob'); ?></h3>
                                                    <p><?php echo esc_html($candidate->get_description()); ?></p>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                    <div class="resume-detail-info">
                                        <?php
                                        if ($experience = $candidate->get_experience()) {
                                            ?>
                                            <div id="work-experience" class="work-experience">
                                                <div class="title"><?php echo __('Work experience', 'iwjob'); ?></div>
                                                <ul class="time-line">
                                                    <?php
                                                    foreach ($experience as $item) {
                                                        ?>
                                                        <li>
                                                            <div class="position-company top">
                                                                <span class="position"><?php echo $item['title']; ?></span><?php echo $item['company'] ?>
                                                            </div>
                                                            <div class="date"><?php echo $item['date']; ?></div>
                                                            <div class="desc"><?php echo apply_filters('the_content',$item['description']); ?></div>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        <?php } ?>

                                        <?php if ($education = $candidate->get_education()) { ?>
                                            <div id="education" class="education">
                                                <div class="title"><?php echo __('Education', 'iwjob'); ?></div>
                                                <ul class="time-line">
                                                    <?php
                                                    foreach ($education as $item) {
                                                        ?>
                                                        <li>
                                                            <div class="speciality-school top">
                                                                <span class="speciality"><?php echo $item['title']; ?></span><?php echo $item['school_name']; ?>
                                                            </div>
                                                            <div class="date"><?php echo $item['date']; ?></div>
                                                            <div class="desc"><?php echo apply_filters('the_content',$item['description']); ?></div>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        <?php } ?>

                                        <?php if ($award = $candidate->get_award()) { ?>
                                            <div id="honors-awards" class="honors-awards">
                                                <div class="title"><?php echo __('Honors & awards', 'iwjob'); ?></div>
                                                <ul class="time-line">
                                                    <?php
                                                    foreach ($award as $item) {
                                                        ?>
                                                        <li>
                                                            <div class="title"><?php echo $item['title']; ?></div>
                                                            <div class="date"><?php echo $item['year']; ?></div>
                                                            <div class="desc"><?php echo apply_filters('the_content',$item['description']); ?></div>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        <?php } ?>

                                        <?php if ($skill = $candidate->get_skill_showcase()) { ?>
                                            <div id="skills" class="skills">
                                                <div class="title"><?php echo __('Skills', 'iwjob'); ?></div>
                                                <ul class="theme-color">
                                                    <?php
                                                    foreach ($skill as $item) {
                                                        ?>
                                                        <li>
                                                            <div class="title-skill"><?php echo $item['title']; ?></div>
                                                            <div class="scoring">
                                                                <span class="line theme-bg" style="width: <?php echo $item['value']; ?>%;"></span>
                                                                <span class="percent"><?php echo $item['value'] . '%'; ?></span>
                                                            </div>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        <?php } ?>

                                        <?php
                                        $gallery = $candidate->get_gallery();
                                        if ($gallery) {
                                            ?>
                                            <div id="portfolio" class="iwj-gallery">
                                                <div class="title"><?php echo __('Portfolio', 'iwjob'); ?></div>
                                                <div class="content-wrap">
                                                    <div class="image-list">
                                                        <?php
                                                        foreach ($gallery as $image) {
                                                            $image_url = wp_get_attachment_url($image);
                                                            $image_url_img = inwave_resize($image_url, 150, 75, true);
                                                            if ($image_url) {
                                                                echo '
                                                                    <a rel="example_group" href="' . $image_url . '">
                                                                        <img class="srch-photo" src="' . $image_url_img . '" alt="">
                                                                    </a>';
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php if ($candidate_sidebar && is_active_sidebar('sidebar-candidate-v2')) : ?>
                                <div class="iwj-sidebar-sticky <?php echo esc_attr(inwave_get_classes('sidebar', $candidate_sidebar)) ?>">
                                    <div class="widget-area">
                                        <?php dynamic_sidebar('sidebar-candidate-v2'); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php
            }
        }
        ?>
        <?php
        if (current_user_can('create_iwj_jobs')) {
            iwj_get_template_part('parts/resume-package-modal', array('candidate' => $candidate));
        }
        ?>
</div>