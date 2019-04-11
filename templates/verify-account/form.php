<div class="iwj-verify-account iwj-form <?php echo $atts['class']; ?>">
    <div class="confirmation-icon"><i class="ion-email-unread"></i></div>
    <h3 class="title"><?php echo __('your email is not verified!', 'iwjob'); ?></h3>
    <div class="desc"><?php echo __('To continue the task on the site, you must verify your account with the link sent to your email address. If you did not receive this email, please check your junk/spam folder.', 'iwjob'); ?></div>
    <div class="resend-email">
        <?php echo sprintf(__("Click %s to get activation link to verify your account", 'iwjob'), '<a href="#" class="iwj-resend-verification" data-sending-text="'.__('sending', 'iwjob').'">'.__('send', 'iwjob').'</a>');?>
        <div class="resend-email-message"></div>
    </div>
    <div class="desc-form"><?php echo __('If you entered an incorrect email address, you will need to update your account with the correct email address.', 'iwjob'); ?></div>
    <form class="iwj-change-email-form">
        <div class="iwj-field">
            <label><?php echo __('Email' ,'iwjob'); ?></label>
            <div class="iwj-input">
                <i class="fa fa-envelope-o"></i>
                <input name="email" placeholder="<?php echo __('Enter Your Email Address.' ,'iwjob'); ?>" type="email">
            </div>
        </div>
        <div class="iwj-button-loader">
            <button class="iwj-btn theme-bg iwj-change-email-btn" type="submit" name="submit"><i class="ion-android-arrow-forward"></i></button>
        </div>
        <div class="iwj-respon-msg"></div>
    </form>
</div>