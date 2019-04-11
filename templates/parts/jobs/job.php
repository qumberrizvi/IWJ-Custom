<?php
$job = IWJ_Job::get_job(get_the_ID());
$permalink = $job->permalink();
$is_featured = $job->is_featured();
$type = $job->get_type();
$author = $job->get_author();
$employer = $job->get_author();
$user = IWJ_User::get_user();
$show_company = iwj_option('show_company_job');
$show_company_logo_job = iwj_option('show_company_logo_job');
$show_categories_job = iwj_option('show_categories_job');
$show_salary = iwj_option('show_salary_job');
$show_location = iwj_option('show_location_job');
$show_skills_job = iwj_option('show_skills_job');
$show_posted_date_job = iwj_option('show_posted_date_job');
?>
<div class="grid-content" data-id="<?php echo $job->get_id() ?>">
    <div class="job-item <?php echo $is_featured ? 'featured-item' : '' ?>">
        <?php
        if (isset($style) && $style) {
            switch ($style) {
                case 'style1':
                case 'style2':
                    if ($author && $show_company_logo_job == '1') {
                        ?>
                        <div class="job-image"><?php echo iwj_get_avatar($author->get_id()); ?></div>
                    <?php }
                    ?>
                    <div class="job-info <?php echo $show_company_logo_job == '1' ? 'yes-logo' : 'no-logo' ?>">
                        <h3 class="job-title">
                            <a href="<?php echo $job->get_indeed_url() ? esc_url($job->get_indeed_url()) : esc_url($permalink); ?>"><?php echo( $job->get_title() ); ?></a>
                        </h3>

                        <div></div>
                        <div class="info-company">
                            <?php if ($author && ( $show_company == '1' )) : ?>
                                <div class="company"><i class="fa fa-suitcase"></i>
                                    <?php if ($job->get_indeed_company_name()) { ?>
                                        <a href="<?php echo $job->get_indeed_url(); ?>"><?php echo $job->get_indeed_company_name(); ?></a>
                                    <?php } elseif ($author->is_active_profile()) { ?>
                                        <a href="<?php echo $author->permalink(); ?>"><?php echo $author->get_display_name(); ?></a>
                                        <?php
                                    } else {
                                        echo $author->get_display_name();
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                            <?php if (( $job->get_salary() ) && ( $show_salary == '1' )) { ?>
                            <?php $postfix = $job->get_salary_postfix(); ?>
                                <div class="sallary">
                                    <i class="iwj-icon-money"></i><?php echo $job->get_salary(); echo $postfix ? _x(' / ', 'Salary Postsfix', 'iwjob') . $postfix : '';?>
                                </div>
                            <?php } ?>
                            <?php if (( $locations = $job->get_locations_links() ) && ( $show_location == '1' )) : ?>
                                <div class="address">
                                    <i class="ion-android-pin"></i><?php echo $locations; ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($show_posted_date_job == '1' && $style == 'style1') { ?>
                                <div class="time-ago"><i class="fa fa-calendar theme-color"></i><?php printf(_x('%s ago', '%s = human-readable time difference', 'iwjob'), human_time_diff(strtotime($job->get_created()), current_time('timestamp'))); ?></div>
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
                                <a href="#" class="iwj-save-job <?php echo $user->is_saved_job($job->get_id()) ? 'saved' : ''; ?>" data-id="<?php echo $job->get_id(); ?>" data-in-list="true"><i class="fa fa-heart"></i></a>
                            <?php } ?>
                            <?php if ($style == 'style2') { ?>
                                <div class="job-posted-time">
                                    <?php printf(_x('%s ago', '%s = human-readable time difference', 'iwjob'), human_time_diff(strtotime($job->get_created()), current_time('timestamp'))); ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                    if ($is_featured) :
                        echo '<div class="iwj-featured"></div>';
                    endif;

                    break;

                case 'style3':
                    ?>
                    <div class="job-save-left">
                        <?php if (!is_user_logged_in()) { ?>
                            <button class="save-job" data-toggle="modal" data-target="#iwj-login-popup">
                                <i class="fa fa-heart"></i></button>
                        <?php } else if (current_user_can('apply_job')) { ?>
                            <a href="#" class="iwj-save-job <?php echo $user->is_saved_job($job->get_id()) ? 'saved' : ''; ?>" data-id="<?php echo $job->get_id(); ?>" data-in-list="true"><i class="fa fa-heart"></i></a>
                        <?php } ?>
                    </div>

                    <?php if ($author && $show_company_logo_job == '1') { ?>
                        <div class="job-image"><?php echo iwj_get_avatar($author->get_id()); ?></div>
                    <?php } ?>

                    <div class="job-info <?php echo $show_company_logo_job == '1' ? 'yes-logo' : 'no-logo' ?>">
                        <h3 class="job-title">
                            <a href="<?php echo $job->get_indeed_url() ? esc_url($job->get_indeed_url()) : esc_url($permalink); ?>"><?php echo( $job->get_title() ); ?></a>
                        </h3>
                        <div style="display: none;"><?php echo $job->get_description(false) ?></div>
                        <div class="info-company">
                            <div class="job-type <?php echo $type ? $type->slug : ''; ?>">
                                <?php
                                if ($type) {
                                    $color = get_term_meta($type->term_id, IWJ_PREFIX . 'color', true);
                                    ?>
                                    <a class="type-name" href="<?php echo get_term_link($type->term_id, 'iwj_type'); ?>" <?php echo $color ? 'style="color: ' . $color . '; border-color: ' . $color . '; background-color: ' . $color . '"' : ''; ?>><?php echo $type->name; ?></a>
                                <?php } ?>
                            </div>
                            <?php
                            if (( $job->get_salary() ) && ( $show_salary == '1' )) {
                                $postfix = $job->get_salary_postfix();
                                ?>
                                <div class="sallary">
                                    <?php
                                    echo $job->get_salary();
                                    echo $postfix ? _x(' / ', 'Salary Postsfix', 'iwjob') . $postfix : '';
                                    ?>
                                </div>
                            <?php } ?>
                            <?php if (( $locations = $job->get_locations_links() ) && ( $show_location == '1' )) : ?>
                                <div class="address">
                                    <?php echo $locations; ?>
                                </div>
                            <?php endif; ?>
                            <?php
                            $skills = $job->get_all_skills();
                            if ($skills && $show_skills_job == '1'):
                                ?>
                                <?php foreach ($skills as $skill) : ?>
                                    <div class="skill">
                                        <a href="<?php echo get_term_link($skill->term_id); ?>">
                                            <i class="ion-pricetag"></i><?php echo $skill->name; ?>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="job-company-time">
                            <?php if ($author && ( $show_company == '1' )) : ?>
                                <div class="company">
                                    <?php if ($job->get_indeed_company_name()) { ?>
                                        <a href="<?php echo $job->get_indeed_url(); ?>"><?php echo $job->get_indeed_company_name(); ?></a>
                                    <?php } elseif ($author->is_active_profile()) { ?>
                                        <a href="<?php echo $author->permalink(); ?>"><?php echo $author->get_display_name(); ?></a>
                                        <?php
                                    } else {
                                        echo $author->get_display_name();
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                            <div class="job-posted-time">
                                <?php printf(_x('%s ago', '%s = human-readable time difference', 'iwjob'), human_time_diff(strtotime($job->get_created()), current_time('timestamp'))); ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    if ($is_featured) :
                        echo '<div class="iwj-featured"></div>';
                    endif;

                    break;
            }
        } else {
            if ($show_company_logo_job == '1' || $show_company == '1') {
                ?>
                <div class="job-image">
                    <?php
                    if ($show_company_logo_job == '1') {
                        echo '<div class="img-avatar">';
                        echo iwj_get_avatar($author->get_id());
                        echo '</div>';
                    }
                    ?>
                    <?php if ($author && ( $show_company == '1' )) : ?>
                        <div class="company iwj-job-page">
                            <h6>
                                <?php if ($job->get_indeed_company_name()) { ?>
                                    <a class="theme-color" href="<?php echo $job->get_indeed_url(); ?>"><?php echo $job->get_indeed_company_name(); ?></a>
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
            <?php } ?>
            <div class="job-content-wrap">
                <div class="job-info <?php echo $show_company_logo_job == '1' ? 'yes-logo' : 'no-logo' ?>">
                    <h3 class="job-title">
                        <a href="<?php echo $job->get_indeed_url() ? esc_url($job->get_indeed_url()) : esc_url($permalink); ?>"><?php echo( $job->get_title() ); ?></a>
                    </h3>
                    <div class="info-company">
                        <?php if (( $locations = $job->get_locations_links() ) && ( $show_location == '1' )) : ?>
                            <div class="address"><i class="ion-android-pin"></i><?php echo $locations; ?></div>
                        <?php endif; ?>
                        <?php if ($author && ( $show_company == '1' )) : ?>
                            <div class="company"><i class="fa fa-suitcase"></i>
                                <?php if ($job->get_indeed_company_name()) { ?>
                                    <a href="<?php echo $job->get_indeed_url(); ?>"><?php echo $job->get_indeed_company_name(); ?></a>
                                <?php } elseif ($author->is_active_profile()) { ?>
                                    <a href="<?php echo $author->permalink(); ?>"><?php echo $author->get_display_name(); ?></a>
                                    <?php
                                } else {
                                    echo $author->get_display_name();
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                        <?php
                        $categories = $job->get_categories();
                        if ($categories && $show_categories_job == '1') {
                            $categories_links = $job->get_categories_links();
                            ?>
                            <div class="categories iwj-job-page">
                                <i class="fa fa-suitcase theme-color"></i>
                                <span><?php echo sprintf(__('%s', '%s', count($categories), 'iwjob'), $categories_links); ?></span>
                            </div>
                        <?php } ?>
                        <?php if (( $job->get_salary() ) && ( $show_salary == '1' )) { ?>
                            <div class="sallary"><i class="iwj-icon-money"></i>
                                <?php 
                                $postfix = $job->get_salary_postfix();
                                echo $job->get_salary(); 
                                echo $postfix ? _x(' / ', 'Salary Postsfix', 'iwjob') . $postfix : '';
                                ?>
                            </div>
                        <?php } ?>

                        <?php if ($show_posted_date_job == '1') { ?>
                            <div class="time-ago"><i class="fa fa-calendar theme-color"></i><?php printf(_x('%s ago', '%s = human-readable time difference', 'iwjob'), human_time_diff(strtotime($job->get_created()), current_time('timestamp'))); ?></div>
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
                            <a href="#" class="iwj-save-job <?php echo $user->is_saved_job($job->get_id()) ? 'saved' : ''; ?>" data-id="<?php echo $job->get_id(); ?>" data-in-list="true"><i class="fa fa-heart"></i></a>
                        <?php } ?>
                    </div>
                </div>
                <?php if (($skills = $job->get_all_skills()) && $show_skills_job == '1'): ?>
                    <div class="job-skill iwj-job-page">
                        <div class="skills">
                            <?php foreach ($skills as $skill) : ?>
                                <a href="<?php echo get_term_link($skill->term_id); ?>">
                                    <i class="ion-pricetag"></i><?php echo $skill->name; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($is_featured) : ?>
                <div class="iwj-featured"></div>
            <?php endif; ?>

        <?php }
        ?>

    </div>
</div>

