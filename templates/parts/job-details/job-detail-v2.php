<?php
wp_enqueue_script('jquery-parallax');
wp_enqueue_script('job-single');
$show_breadcrums = Inwave_Helper::getThemeOption('breadcrumbs');

$employer = $job->get_employer();
$is_featured = $job->is_featured();
$type = $job->get_type();
?>
<div class="iwj-job-detail-v2 iwj-single-parallax">
    <?php
    $cover_image_url = '';
    $cover_image = $employer ? $employer->get_cover_image() : '';
    $cover_image_url = $cover_image ? wp_get_attachment_url($cover_image) : IWJ_PLUGIN_URL . '/assets/img/job-detail-v2-cover-photo.jpg';
    ?>
    <div class="iw-parallax" data-iw-paraspeed="0.1" style="background-image: url('<?php echo esc_url($cover_image_url); ?>');"></div>
    <div class="iw-parallax-overlay"></div>
    <div class="content-wrap">
        <div class="content-inner">
            <div class="job-detail-page-heading">
                <div class="container">
                    <div class="page-heading-content">
                        <?php if ($is_featured) { ?>
                            <div class="iwj-featured"></div>
                        <?php } ?>
                        <div class="row">
                            <div class="col-md-8 col-sm-12 col-xs-12">
                                <div class="info-job-detail">
                                    <div class="info-company">
                                        <div class="company-logo"><a href="<?php echo esc_url($author->permalink()); ?>" >
                                                <?php echo iwj_get_avatar($author->get_id(), '120', '', '', array('img_size' => 'inwave-avatar2')); ?>
                                            </a>
                                        </div>
                                        <h3 class="company-name theme-color">
                                            <?php
                                            if ($employer && $employer->is_active()) {
                                                ?>
                                                <a class="theme-color" href="<?php echo esc_url($author->permalink()); ?>"><?php echo $author->get_display_name() ?></a>
                                            <?php } else { ?>
                                                <?php echo $author->get_display_name() ?>
                                            <?php } ?>
                                        </h3>
                                    </div>
                                    <div class="info-job">
                                        <?php if ($show_breadcrums && $show_breadcrums != 'no') { ?>
                                            <div class="breadcrumbs-top"><?php get_template_part('blocks/breadcrumbs'); ?></div>
                                        <?php } ?>
                                        <div class="page-title">
                                            <div class="iw-heading-title">
                                                <h2 class="theme-color"><?php echo $job->get_title(); ?></h2>
                                            </div>
                                            <?php if ($job->get_address()) { ?>
                                                <div class="property-address"><i class="ion-android-pin"></i><span><?php echo $job->get_address(); ?></span></div>
                                                <div style="display: none;"><?php echo $job->get_locations_links(); ?></div>
                                            <?php } ?>
                                            <div class="iw-heading-meta">
                                                <?php $postfix = $job->get_salary_postfix(); ?>
                                                <span class="meta-salary">
                                                    <i class="fa fa-briefcase theme-color"></i><?php
                                                    echo $job->get_salary();
                                                    echo $postfix ? _x(' / ', 'Salary Postsfix', 'iwjob') . $postfix : '';
                                                    ?>
                                                </span>
                                                <?php
                                                if ($job->get_expiry()) {
                                                    if ($job->can_apply() !== 0) {
                                                        ?>
                                                        <span class="meta-date-expiry">
                                                            <i class="fa fa-calendar theme-color"></i><?php echo esc_html__('Expires in', 'iwjob'); ?> <?php printf(_x('%s', '%s = human-readable time difference', 'iwjob'), human_time_diff($job->get_deadline(), current_time('timestamp'))); ?>
                                                        </span>
                                                    <?php } else { ?>
                                                        <span class="meta-date-expiry">
                                                            <i class="fa fa-calendar theme-color"></i><?php echo esc_html__('Expired ', 'iwjob'); ?> <?php printf(_x('%s ago', '%s = human-readable time difference', 'iwjob'), human_time_diff($job->get_deadline(), current_time('timestamp'))); ?>
                                                        </span>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12 col-xs-12">
                                <div class="action-button">
                                    <?php
                                    $can_apply = $job->can_apply();
                                    if ($can_apply === 0) {
                                        echo '<span class="job-expired">' . __('This job has expired.', 'iwjob') . '</span>';
                                    } elseif ($user && !$user->can_apply()) {
                                        echo '<span class="job-expired">' . __('Students do not have permission to apply to classes yet.', 'iwjob') . '</span>';
                                    } else {
                                        if ($job->get_indeed_url()) {
                                            ?>
                                            <a href="<?php echo esc_url($job->get_indeed_url()); ?>" class="apply-job">
                                                <i class="ion-android-checkbox-outline"></i><?php echo esc_html__('Apply for Class', 'iwjob'); ?></a>
                                        <?php } elseif ($job->get_custom_apply_url()) {
                                            ?>
                                            <a href="<?php echo esc_url($job->get_custom_apply_url()); ?>" class="apply-job">
                                                <i class="ion-android-checkbox-outline"></i><?php echo esc_html__('Apply for Class', 'iwjob'); ?></a>
                                            <?php
                                        } else {
                                            $applies = IWJ()->applies->applies();
                                            if ($applies) {
                                                foreach ($applies as $apply) {
                                                    if ($apply->is_available()) {
                                                        $apply->apply_button($job);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    ?>

                                    <?php if (!is_user_logged_in()) { ?>
                                        <a href="#" class="save-job iwj-save-job" data-toggle="modal" data-target="#iwj-login-popup"><i class="ion-heart"></i></a>
                                        <?php
                                    } elseif (current_user_can('apply_job')) {
                                        $saved_job = $user && $user->is_saved_job(get_the_ID()) ? true : false;
                                        ?>
                                        <div class="iwj-button-loader">
                                            <a href="#" class="save-job iwj-save-job <?php echo $saved_job ? 'saved' : ''; ?>" data-toggle="tooltip" title="<?php echo $saved_job ? 'SAVED' : 'SAVE'; ?>" data-id="<?php echo get_the_ID(); ?>"><i class="ion-heart"></i></a>
                                            <div class="iwj-respon-msg iwj-hide"></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="<?php echo esc_attr(inwave_get_classes('container', $job_sidebar)) ?>">
                        <div class="job-detail">
                            <div class="job-detail-content" itemprop="JobPosting" itemscope itemtype="http://schema.org/JobPosting">
                                <div id="job-detail-content">
                                    <div class="job-detail-about">
                                        <h3 class="title-block-content theme-color"><?php echo esc_html__('JOB DETAIL', 'iwjob'); ?></h3>
                                        <meta itemprop="title" content="<?php echo $job->get_title(); ?>" />
                                        <meta itemprop="hiringOrganization" content="<?php echo $author->get_display_name(); ?>" />
                                        <meta itemprop="validThrough" content="<?php echo get_the_date('c'); ?>" />
                                        <meta itemprop="datePosted" content="<?php echo get_the_date('c'); ?>" />
                                        <?php if ($description = $job->get_description(true)) : ?>
                                            <div class="job-detail-desc item" itemprop="description">
                                                <?php echo $description; ?>
                                            </div>
                                        <?php endif; ?>
                                        <meta property="salaryCurrency" content="<?php echo $job->get_currency(); ?>" />
                                        <meta itemprop="baseSalary" content="<?php echo $job->get_salary_to(); ?>" />
                                        <?php if ($type) { ?>
                                            <meta itemprop="employmentType" content="<?php echo $type->name; ?>">
                                        <?php } ?>
                                        <span style="display: none;"><?php echo $job->get_locations_links(); ?></span>
                                        <?php
                                        $data_map_maker = IWJ_PLUGIN_URL . '/assets/images/map-marker-job.png';
                                        $map_maker = iwj_option('iwj_map_maker');
                                        if ($map_maker) {
                                            $data_map_maker = esc_url(wp_get_attachment_url($map_maker[0]));
                                        }
                                        $maps = $job->get_map();
                                        $lat = $maps[0];
                                        $lng = $maps[1];
                                        $zoom = $maps[2];
                                        ?>
                                        <?php if ($lat && $lng) : ?>
                                            <div class="location iwj-map item">
                                                <h4 class="title"><?php _e('location', 'iwjob'); ?></h4>
                                                <div id="job-detail-map" class="job-detail-map" data-lat="<?php echo esc_attr($lat); ?>" data-lng="<?php echo esc_attr($lng); ?>" data-zoom="<?php echo esc_attr($zoom); ?>" data-maker="<?php echo esc_attr($data_map_maker); ?>"
                                                     data-address="<?php echo esc_attr($job->get_address()); ?>" style="height: 332px;">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="action-button">
                                    <div class="button">
                                        <?php
                                        $can_apply = $job->can_apply();
                                        if ($can_apply === 0) {
                                            echo '<span class="job-expired">' . __('This job has expired.', 'iwjob') . '</span>';
                                        } elseif ($user && !$user->can_apply()) {
                                            echo '<span class="job-expired">' . __('Students do not have permission to apply to classes yet.', 'iwjob') . '</span>';
                                        } else {
                                            if ($job->get_indeed_url()) {
                                                ?>
                                                <a href="<?php echo esc_url($job->get_indeed_url()); ?>" class="apply-job">
                                                    <i class="ion-android-checkbox-outline"></i><?php echo esc_html__('Apply for Class', 'iwjob'); ?></a>
                                            <?php } elseif ($job->get_custom_apply_url()) {
                                                ?>
                                                <a href="<?php echo esc_url($job->get_custom_apply_url()); ?>" class="apply-job">
                                                    <i class="ion-android-checkbox-outline"></i><?php echo esc_html__('Apply for Class', 'iwjob'); ?></a>
                                                <?php
                                            } else {
                                                $applies = IWJ()->applies->applies();
                                                if ($applies) {
                                                    foreach ($applies as $apply) {
                                                        if ($apply->is_available()) {
                                                            $apply->apply_button($job);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        ?>

                                        <?php if (!is_user_logged_in()) { ?>
                                            <a href="#" class="save-job iwj-save-job" data-toggle="modal" data-target="#iwj-login-popup"><i class="ion-heart"></i></a>
                                            <?php
                                        } elseif (current_user_can('apply_job')) {
                                            $saved_job = $user && $user->is_saved_job(get_the_ID()) ? true : false;
                                            ?>
                                            <div class="iwj-button-loader">
                                                <a href="#" class="save-job iwj-save-job <?php echo $saved_job ? 'saved' : ''; ?>" data-toggle="tooltip" title="<?php echo $saved_job ? 'SAVED' : 'SAVE'; ?>" data-id="<?php echo get_the_ID(); ?>"><i class="ion-heart"></i></a>
                                                <div class="iwj-respon-msg iwj-hide"></div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="post-social-share">
                                    <h4 class="post-share-title"><?php echo esc_html__('Share:', 'iwjob'); ?></h4>
                                    <div class="post-share-buttons-inner">
                                        <?php
                                        inwave_social_sharing(get_permalink(), Inwave_Helper::substrword($job->get_description(true), 10), get_the_title());
                                        ?>
                                        <?php if (iwj_option('show_print_job')) { ?>
                                            <div class="iwj-print-job">
                                                <h4 class="post-share-title"><?php echo esc_html__('Print:', 'iwjob'); ?></h4>
                                                <a href="javascript:void(0);" class="iwj-button-print-job" data-title="<?php echo $job->get_title(); ?>" data-author="<?php echo $author->get_display_name(); ?>" data-author_avatar="<?php echo iwj_get_avatar_url($author->get_id()); ?>">
                                                    <i class="fa fa-print"></i>
                                                </a>
                                            </div>
                                        <?php } ?>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <?php
                            $related_jobs = $job->get_related();
                            if ($related_jobs) {
                                ?>
                                <div class="job-related iwj-jobs-style style2">
                                    <div class="title theme-color"><?php echo __('JOBS YOU WILL LOVE', 'iwjob'); ?></div>
                                    <div class="iwj-jobs iwj-listing">
                                        <?php
                                        foreach ($related_jobs as $related_job) {
                                            $related_author = $related_job->get_author();
                                            $is_featured = $related_job->is_featured();
                                            $type = $related_job->get_type();
                                            $permalink = $related_job->permalink();
                                            $author = $related_job->get_author();
                                            $show_company = iwj_option('show_company_job');
                                            $show_company_logo_job = iwj_option('show_company_logo_job');
                                            $show_categories_job = iwj_option('show_categories_job');
                                            $show_salary = iwj_option('show_salary_job');
                                            $show_location = iwj_option('show_location_job');
                                            $show_skills_job = iwj_option('show_skills_job');
                                            $show_posted_date_job = iwj_option('show_posted_date_job');
                                            ?>
                                            <div class="job-item <?php echo $is_featured ? 'featured-item' : '' ?>">
                                                <?php if ($author) { ?>
                                                    <?php if ($show_company_logo_job == '1' || $show_company == '1') { ?>
                                                        <div class="job-image">
                                                            <?php
                                                            if ($show_company_logo_job == '1') {
                                                                echo '<div class="img-avatar">';
                                                                echo iwj_get_avatar($author->get_id());
                                                                echo '</div>';
                                                            }

                                                            if ($author && ( $show_company == '1' )) :
                                                                ?>
                                                                <div class="company">
                                                                    <h6>
                                                                        <?php if ($related_job->get_indeed_company_name()) { ?>
                                                                            <a class="theme-color" href="<?php echo $related_job->get_indeed_url(); ?>"><?php echo $related_job->get_indeed_company_name(); ?></a>
                                                                        <?php } elseif ($author->is_active_profile()) { ?>
                                                                            <a class="theme-color" href="<?php echo $author->permalink(); ?>"><?php echo $author->get_display_name(); ?></a>
                                                                            <?php
                                                                        } else {
                                                                            echo $author->get_display_name();
                                                                        }
                                                                        ?>
                                                                    </h6>
                                                                </div>
                                                        <?php endif; ?>
                                                        </div>
                                                    <?php }
                                                }
                                                ?>
                                                <div class="job-content-wrap">
                                                    <div class="job-info">
                                                        <h3 class="job-title">
                                                            <a href="<?php echo $related_job->get_indeed_url() ? esc_url($related_job->get_indeed_url()) : esc_url($permalink); ?>"><?php echo( $related_job->get_title() ); ?></a>
                                                        </h3>
                                                        <div class="info-company">
                                                            <?php
                                                            $categories = $related_job->get_categories();
                                                            if ($categories && $show_categories_job == '1') {
                                                                $categories_links = $related_job->get_categories_links();
                                                                ?>
                                                                <div class="categories">
                                                                    <i class="fa fa-suitcase theme-color"></i>
                                                                    <span><?php echo sprintf(__('%s', '%s', count($categories), 'iwjob'), $categories_links); ?></span>
                                                                </div>
                                                            <?php } ?>
                                                            <?php if (( $locations = $related_job->get_locations_links() ) && ( $show_location == '1' )) : ?>
                                                                <div class="address"><i class="ion-android-pin"></i><?php echo $locations; ?></div>
                                                            <?php endif; ?>
                                                            <?php if ($show_posted_date_job == '1') { ?>
                                                                <div class="time-ago"><i class="fa fa-calendar theme-color"></i><?php printf(_x('%s ago', '%s = human-readable time difference', 'iwjob'), human_time_diff(strtotime($related_job->get_created()), current_time('timestamp'))); ?></div>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="job-type <?php echo $type ? $type->slug : ''; ?>">
                                                            <?php
                                                            if ($type) {
                                                                $color = get_term_meta($type->term_id, IWJ_PREFIX . 'color', true);
                                                                ?>
                                                                <a class="type-name" href="<?php echo get_term_link($type->term_id, 'iwj_type'); ?>" <?php echo $color ? 'style="color: ' . $color . '; border-color: ' . $color . '; background-color: ' . $color . '"' : ''; ?>><?php echo $type->name; ?></a>
        <?php } ?>
                                                            <?php if (!is_user_logged_in()) { ?>
                                                                <button class="save-job" data-toggle="modal" data-target="#iwj-login-popup">
                                                                    <i class="fa fa-heart"></i></button>
                                                            <?php } else if (current_user_can('apply_job')) { ?>
                                                                <a href="#" class="iwj-save-job <?php echo $user->is_saved_job($related_job->get_id()) ? 'saved' : ''; ?>" data-id="<?php echo $related_job->get_id(); ?>" data-in-list="true"><i class="fa fa-heart"></i></a>
                                                    <?php } ?>
                                                        </div>
                                                    </div>
                                                        <?php if (($skills = $related_job->get_all_skills()) && $show_skills_job == '1'): ?>
                                                        <div class="job-skill">
            <?php foreach ($skills as $skill) : ?>
                                                                <a href="<?php echo get_term_link($skill->term_id); ?>">
                                                                    <i class="ion-pricetag"></i><?php echo $skill->name; ?>
                                                                </a>
                                                        <?php endforeach; ?>
                                                        </div>
                                                <?php endif; ?>
                                                </div>
                                                <?php if ($is_featured) : ?>
                                                    <div class="iwj-featured"></div>
                                            <?php endif; ?>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                    <?php } ?>
                        </div>
                    </div>
                            <?php if ($job_sidebar && is_active_sidebar('sidebar-job-v2')) : ?>
                        <div class="iw-job-detail-sidebar iwj-sidebar-sticky <?php echo esc_attr(inwave_get_classes('sidebar', $job_sidebar)) ?>">
                            <div class="widget-area" role="complementary">
                        <?php dynamic_sidebar('sidebar-job-v2'); ?>
                            </div>
                        </div>
<?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    $apply_cls = new IWJ_Apply_Form();
    iwj_get_template_part('applies/form/popup', array('job' => $job, 'self' => $apply_cls));
    ?>
</div>
