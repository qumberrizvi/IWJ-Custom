<?php
$post = get_post();
$job = IWJ_Job::get_job(get_post());
$author = $job->get_author();
$user = IWJ_User::get_user();

if(is_single() && $post) {
    echo $args['before_widget'];
    $style = 'style1';
    if ( isset( $instance['style'] ) ) {
        $style = ( ! empty( $instance['style'] ) ) ? $instance['style'] : 'style1';
    }

    if (isset($instance['title'])) {
        $title = (!empty($instance['title'])) ? $instance['title'] : '';
        $title = apply_filters('widget_title', $title, $instance, $widget_id);

        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
    }
    $employer = $author->get_employer();
    ?>
    <div class="iwj-job-widget-wrap">
        <?php if ( $style== 'style1' ) {?>
        <div class="info-top">
            <div class="company-logo"><a href="<?php echo esc_url($author->permalink()); ?>">
                    <?php
                    echo iwj_get_avatar($author->get_id(), '', '', '', array('img_size'=> 'thumbnail'));
                    ?>
                </a>
            </div>
            <h3 class="iwj-title">
                <?php
                if($employer && $employer->is_active()){
                    ?>
                    <a class="theme-color" href="<?php echo esc_url($author->permalink()); ?>"><?php echo $author->get_display_name() ?></a>
                <?php }else{ ?>
                    <?php echo $author->get_display_name() ?>
                <?php } ?>
            </h3>
            <?php if($author->get_headline()){ ?>
                <div class="headline"><?php echo $author->get_headline(); ?></div>
            <?php } ?>
        </div>
        <div class="iwj-sidebar-bottom info-bottom">
            <?php if ($author->get_short_description()) : ?>
                <div class="description"><?php echo $author->get_short_description(); ?></div>
            <?php endif; ?>
            <div class="company-link">
                <?php if($author->get_website()){ ?>
                    <a class="website" href="<?php echo esc_url($author->get_website()); ?>"><i class="ion-link"></i><span><?php _e('website company', 'iwjob'); ?></span></a>
                    <a class="link-detail" href="<?php echo esc_url($author->permalink()); ?>"><i class="ion-ios-list-outline"></i><span><?php _e('Company info', 'iwjob'); ?></span></a>
                <?php } ?>
            </div>

        </div>
        <?php } else {?>
            <div class="iwj-job-infomation-v2-widget">
            <ul>
                <?php
                    if ( $categories = $job->get_categories() ) {
                    $categories_links = $job->get_categories_links();
                ?>
                <li class="job-categories">
                    <i class="icon-injob-layers"></i>
                    <div class="content">
                        <span class="title"><?php _e('Job Subjects:', 'iwjob'); ?></span>
                        <span><?php echo sprintf( __( '%s', '%s', count( $categories ), 'iwjob' ), $categories_links ); ?></span>
                    </div>
                </li>
                <?php }
                $type = $job->get_type();
                if ($type) {
                    $color = get_term_meta( $type->term_id, IWJ_PREFIX . 'color', true );
                    ?>
                    <li class="job-type">
                        <i class="icon-injob-briefcase2"></i>
                        <div class="content">
                            <span class="title"><?php _e('Job types:', 'iwjob'); ?></span>
                            <a class="type-name" href="<?php echo get_term_link( $type->term_id, 'iwj_type' ); ?>" <?php echo $color ? 'style="color: '.$color.';"' : ''; ?>><?php echo $type->name; ?></a>
                        </div>
                    </li>
                <?php } ?>
                <?php
                if ($levels = $job->get_all_levels()) {
                    ?>
                    <li class="job-levels">
                        <i class="icon-injob-bookmark"></i>
                        <div class="content">
                            <span class="title"><?php _e('Job Level:', 'iwjob'); ?></span>
                            <?php foreach ( $levels as $level ) : ?>
                                <a class="theme-color" href="<?php echo get_term_link( $level->term_id ); ?>">
                                    <?php echo $level->name; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </li>
                <?php }?>
                <li class="posted">
                    <i class="icon-injob-clock"></i>
                    <div class="content">
                        <span class="title"><?php _e('Published date:', 'iwjob'); ?></span>
                        <p><?php printf(_x('%s ago', '%s = human-readable time difference', 'iwjob'), human_time_diff(get_the_time('U'), current_time('timestamp'))); ?></p>
                    </div>
                </li>
                <?php
                $languages = $job->get_languages();
                if ($languages) {
                    $language_titles = iwj_get_language_titles($languages);
                    ?>
                    <li class="job-languages">
                                <i class="icon-injob-globe"></i>
                            <div class="content">
                                <span class="title"><?php _e('Languages:', 'iwjob'); ?></span>
                                <p><?php echo implode(", ", $language_titles); ?></p>
                            </div>
                    </li>
                    <?php
                    if ($skills = $job->get_all_skills()) {
                    ?>
                    <li class="job-skill">
                        <i class="icon-injob-tag"></i>
                        <div class="content">
                            <span class="title"><?php _e('Skills:', 'iwjob'); ?></span>
                            <div class="skills">
                                <?php foreach ( $skills as $skill ) : ?>
                                    <a class="theme-color" href="<?php echo get_term_link( $skill->term_id ); ?>">
                                        <?php echo $skill->name; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </li>
                        <?php }?>
                <?php } ?>
            </ul>
            </div>
        <?php }?>
    </div>
    <?php

    echo $args['after_widget'];
}