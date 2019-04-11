<div class="iwj-lostpass">
    <?php if((isset($_GET['user']) && isset($_GET['code'])  && IWJ_User::can_reset_password($_GET['user'], $_GET['code']))) {?>
        <form action="<?php echo esc_url(get_permalink()); ?>" method="post" class="iwj-form iwj-resetpass-form">
            <div class="iwj-respon-msg hide"></div>
            <div class="iwj-field">
                <label><?php echo __('Password', 'iwjob'); ?></label>
                <div class="iwj-input">
                    <i class="fa fa-keyboard-o"></i>
                    <input type="password" name="password" placeholder="<?php echo __('Enter Password.', 'iwjob'); ?>">
                </div>
            </div>
            <div class="iwj-field">
                <label><?php echo __('Confirm Password', 'iwjob'); ?></label>
                <div class="iwj-input">
                    <i class="fa fa-keyboard-o"></i>
                    <input type="password" name="password_confirm" placeholder="<?php echo __('Enter Confirm Password.', 'iwjob'); ?>">
                </div>
            </div>
            <div class="iwj-button-loader">
                <input type="hidden" name="user" value="<?php echo $_GET['user']; ?>">
                <input type="hidden" name="code" value="<?php echo $_GET['code']; ?>">
                <button type="submit" class="iwj-btn iwj-btn-primary iwj-btn-full iwj-btn-large iwj-resetpass-btn"><?php echo __('Reset Password', 'iwjob'); ?></button>
            </div>
        </form>
    <?php }else{ ?>
        <form action="<?php echo esc_url(get_permalink()); ?>" method="post" class="iwj-form iwj-lostpass-form">
            <?php if(isset($pre_text) && $pre_text) {?>
                <div class="pre-text"><?php echo $pre_text; ?></div>
            <?php } ?>
            <div class="iwj-respon-msg hide"></div>
            <div class="iwj-field">
                <label><?php echo __('Email OR Phone ', 'iwjob'); ?></label>
                <div class="iwj-input">
                    <i class="fa fa-user"></i>
                    <input type="text" name="user_login" placeholder="<?php echo __('Enter email or phone number.', 'iwjob'); ?>">
                </div>
            </div>
            <div class="iwj-button-loader">
                <button type="submit" class="iwj-btn iwj-btn-primary iwj-btn-full iwj-btn-large iwj-lostpass-btn"><?php echo __('Get New Password', 'iwjob'); ?></button>
            </div>
            <div class="login-register-account">
                <a href="<?php echo iwj_get_page_permalink('login'); ?>"><?php echo __('Login', 'iwjob'); ?></a>
                <a href="<?php echo iwj_get_page_permalink('register'); ?>"><?php echo __('Register', 'iwjob'); ?></a>
            </div>
        </form>
    <?php } ?>
</div>
