<?php
$post = get_post();
$job = IWJ_Job::get_job(get_post());
$author = $job->get_author();
$user = IWJ_User::get_user();

if(is_single() && $post) {
    echo $args['before_widget'];

    if (isset($instance['title'])) {
        $title = (!empty($instance['title'])) ? $instance['title'] : '';
        $title = apply_filters('widget_title', $title, $instance, $widget_id);

        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
    }
    ?>
    <div class="iwj-single-contact-form">
        <form class="iwj-contact-form" action="#" method="post" enctype="multipart/form-data">
            <?php
            iwj_field_text('name', '', true, null, ($user ? $user->get_display_name() : ''), '', '', __('Your name', 'iwjob'));

            iwj_field_email('email', '', true, null, ($user ? $user->get_email() : ''), '', '', __('Your email', 'iwjob'));

            iwj_field_text('subject', '', true, null, null, '', '', __('Subject', 'iwjob'));

            iwj_field_textarea('message', '', true, null, null, '', '', __('Message', 'iwjob'));
            ?>
            <div class="iwj-respon-msg iwj-hide"></div>
            <input type="hidden" name="item_id" value="<?php echo $job->get_id(); ?>">
            <div class="iwj-btn-action">
                <div class="iwj-button-loader">
                    <?php
                    if(in_array('contact', iwj_option('use_recaptcha', array()))) {
                        echo '<div class="g-recaptcha" data-sitekey="'.iwj_option('google_recaptcha_site_key').'"></div>';
                    }
                    ?>
                    <button type="submit" class="iwj-btn iwj-btn-primary iwj-contact-btn"><i class="ion-android-send"></i><?php echo __('Send Now', 'iwjob'); ?></button>
                </div>
            </div>
        </form>
    </div>
    <?php
    echo $args['after_widget'];
}