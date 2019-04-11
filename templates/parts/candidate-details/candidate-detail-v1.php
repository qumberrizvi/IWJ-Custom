<div class="iwj-candicate-detail-v1">
    <div class="container">
        <div class="row">
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
                <?php echo sprintf(__('You must be logged in to view this page. <a href="%s">Login here</a>', 'iwjob'), add_query_arg('redirect_to', $candidate->permalink(), $login_page_id)); ?>
            </div>
        <?php
        } else {
            if ($check == 2) {
                ?>
                <div class="iwj-alert-box">
                    <?php echo esc_html__('This profile is not public now.', 'iwjob'); ?>
                </div>
            <?php } elseif ($check == 4) {
                ?>
                <div class="iwj-alert-box">
                    <?php echo esc_html__('This profile is not public or only employers can see.', 'iwjob'); ?>
                </div>
            <?php } else {
                ?>
                <div class="<?php echo esc_attr(inwave_get_classes('container', $candidate_sidebar)); ?>">
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
                <div class="iwj-candidate-info-top" style="background-image: url('<?php echo esc_url($cover_image_url); ?>');" itemprop="customer" itemscope itemtype="http://schema.org/Person">
                    <div class="bg-overlay"></div>
                    <div class="info-top">
                        <div class="candidate-logo">
                            <?php
                            echo iwj_get_avatar($candidate->get_author_id(), '150', '', '', array('img_size'=>'thumbnail'));
                            ?>
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
                        <div class="info-inner">
                            <div class="title-headline">
                                <h3 class="candidate-name" itemprop="name">
                                    <?php echo $candidate->get_title(); ?>
                                </h3>
                                <div class="iwj-employerdl-shortdes">
                                    <?php echo $candidate->get_headline(); ?>
                                </div>
                            </div>

                            <?php if (iwj_option('view_free_resum') || ( $user && $user->can_view_resum($candidate->get_id()) )) { ?>
                                <div class="candidate-info">
                                    <ul>
                                        <?php if ($candidate->get_address()) { ?>
                                            <li class="candidate-location">
                                                <i class="ion-android-pin"></i>
                                                <span class="info-value" itemprop="address"><?php echo $candidate->get_address(); ?></span>
                                            </li>
                                        <?php } ?>
                                        <?php if ($candidate->get_phone()) { ?>
                                            <li class="candidate-phone">
                                                <i class="ion-ios-telephone"></i>
                                                <span class="info-value" itemprop="telephone"><?php echo $candidate->get_phone(); ?></span>
                                            </li>
                                        <?php } ?>
                                        <?php if ($candidate->get_email()) { ?>
                                            <li class="candidate-email">
                                                <i class="ion-android-globe"></i>
                                                <span class="info-value" itemprop="email"><?php echo $candidate->get_email(); ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <div class="iwj-button-loader detail-action-button">
                                    <?php
                                    if (current_user_can('create_iwj_jobs')) {
                                        $saved = $user && $user->is_saved_resum(get_the_ID()) ? true : false;
                                        $save_text = $saved ? __('<i class="fa fa-heart"></i> Saved resume', 'iwjob') : __('<i class="fa fa-heart"></i> Save resume', 'iwjob');
                                        ?>
                                        <a href="#" class="iwj-btn action-button follow iwj-save-resume <?php echo $saved ? 'saved' : ''; ?>" data-id="<?php echo $candidate->get_id(); ?>"><?php echo $save_text; ?></span></a>
                                    <?php } elseif (!$user) { ?>
                                        <a href="#" data-toggle="modal" data-target="#iwj-login-popup" class="iwj-btn action-button follow"><?php echo __('<i class="fa fa-heart"></i>Save resume', 'iwjob'); ?></a>
                                    <?php } ?>
                                    <?php
                                    $cv = $candidate->get_cv();
                                    if (isset($cv['url']) && $cv['url'] != null) {
                                        ?>
                                        <a href="<?php echo $cv['url']; ?>" class="iwj-btn action-button follow iwj-download-cv" target="_blank"><?php echo __('<i class="ion-android-download"></i> Download CV', 'iwjob'); ?></a>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                <div class="view-infomation detail-action-button">
                                    <?php if (current_user_can('create_iwj_jobs')) { ?>
                                        <a href="#" data-toggle="modal" data-target="#iwj-modal-view-<?php echo $candidate->get_id(); ?>"><i class="ion-ios-eye"></i><?php echo __('View full information', 'iwjob'); ?>
                                        </a>
                                    <?php } elseif (!$user) { ?>
                                        <a href="#" data-toggle="modal" data-target="#iwj-login-popup"><i class="ion-ios-eye"></i><?php echo __('View full information', 'iwjob'); ?>
                                        </a>
                                    <?php } else { ?>
                                        <a class="hidden-info" href="#"><?php echo __('Hidden Information', 'iwjob'); ?></a>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="candicate-main-content">
                    <div class="resume-detail-info">
                        <?php
                        if ($experience = $candidate->get_experience()) {
                            ?>
                            <div class="work-experience">
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
                            <div class="education">
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
                            <div class="honors-awards">
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
                            <div class="skills">
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
                        $video_url = $candidate->get_video();
                        if ($video_url || $gallery) {
                            ?>
                            <div class="iwj-gallery">
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
                                    <?php if ($video_url) {
                                        global $wp_embed;
                                        ?>
                                        <div class="videoWrapper">
                                            <?php echo $wp_embed ? $wp_embed->run_shortcode('[embed width="770" height="370"]' . $video_url[0] . '[/embed]') : ''; ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php
                if (current_user_can('create_iwj_jobs')) {
                    iwj_get_template_part('parts/resume-package-modal', array('candidate' => $candidate));
                }
                ?>
                </div>
                <?php if ($candidate_sidebar && is_active_sidebar('sidebar-candidate')) : ?>
                    <div class="iwj-sidebar-sticky <?php echo esc_attr(inwave_get_classes('sidebar', $candidate_sidebar)) ?>">
                        <div class="widget-area">
                            <?php dynamic_sidebar('sidebar-candidate'); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php
            }
        }
        ?>
        </div>
    </div>
</div>