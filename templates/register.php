<div class="iwj-register">
    <?php
    if (!is_user_logged_in()) {
        $active_tab = isset($_GET['role']) ? $_GET['role'] : 'candidate';
        $active_tab = apply_filters('iwj_register_active_tab', $active_tab);
        $disable_candidate_register = iwj_option('disable_candidate_register');
        $disable_employer_register = iwj_option('disable_employer_register');
        if (!$disable_candidate_register || !$disable_employer_register) {
            ?>
            <form action="<?php echo esc_url(get_permalink()); ?>" method="post" class="iwj-form iwj-register-form">
			<label class="abovetab"><p class="abovetabtext" style="text-align:center;">I am a:</p></label>
                <?php if (!$disable_candidate_register && !$disable_employer_register) { ?>
                    <div class="iwj-magic-line-wrap">
                        <div class="iwj-magic-line">
                            <p class="<?php echo 'employer' == $active_tab ? '' : 'active'; ?> iwj-toggle iwj-candidate-toggle"><?php echo __('Teacher', 'iwjob'); ?></p>
                            <p class="<?php echo 'employer' == $active_tab ? 'active' : ''; ?> iwj-toggle iwj-employer-toggle"><?php echo __('Student', 'iwjob'); ?></p>
                        </div>
                    </div>
                <?php } ?>
                <div class="iwj-field">
                    <label><?php echo __('Phone Number', 'iwjob'); ?></label>
                    <div class="iwj-input">
                        <i class="fa fa-user"></i>
                        <input type="text" name="username" placeholder="<?php echo __('Enter Your Phone Number', 'iwjob'); ?>">
                    </div>
                </div>
                <div class="iwj-field">
                    <label><?php echo __('Email', 'iwjob'); ?></label>
                    <div class="iwj-input">
                        <i class="fa fa-envelope-o"></i>
                        <input type="email" name="email" placeholder="<?php echo __('Enter Your Email Address', 'iwjob'); ?>">
                    </div>
                </div>
                <?php
                if (!$disable_employer_register) {
                    $class = $disable_candidate_register || 'employer' == $active_tab ? '' : 'hide';
                    ?>
                    <div class="iwj-field <?php echo $class; ?> company-field">
                        <label><?php echo __('Full Name', 'iwjob'); ?></label>
                        <div class="iwj-input">
                            <i class="fa fa-vcard-o"></i>
                            <input type="text" name="company" placeholder="<?php echo __('Enter Full Name', 'iwjob'); ?>">
                        </div>
                    </div>
                <?php } ?>
                <?php if (!iwj_option('registration_generate_password')) { ?>
                    <div class="iwj-field">
                        <label><?php echo __('Password', 'iwjob'); ?></label>
                        <div class="iwj-input">
                            <i class="fa fa-keyboard-o"></i>
                            <input type="password" name="password" placeholder="<?php echo __('Enter Your Password', 'iwjob'); ?>" required>
                        </div>
                    </div>
                <?php } ?>
	            <?php if (iwj_option('terms_and_conditions') && (iwj_option('terms_and_conditions_page') || iwj_option('privacy_policy_page'))) { ?>
					<div class="register-account text-center">
			            <?php
			            $messages = iwj_option( 'terms_and_conditions' );
			            $terms_and_conditions_url     = iwj_option( 'terms_and_conditions_page' ) ? get_the_permalink( iwj_option( 'terms_and_conditions_page' ) ) : '';
			            $privacy_policy_url           = iwj_option( 'privacy_policy_page' ) ? get_the_permalink( iwj_option( 'privacy_policy_page' ) ) : '';
			            $str_key                      = array(
				            '{link_terms_and_conditions_page}',
				            '{link_privacy_policy_page}'
			            );
			            $str_replaced                 = array( $terms_and_conditions_url, $privacy_policy_url );
			            $messages_replaced = str_replace( $str_key, $str_replaced, $messages);
			            echo $messages_replaced;
			            ?>
					</div>
	            <?php } ?>
                <div class="iwj-respon-msg iwj-hide"></div>
                <div class="iwj-button-loader">
                    <?php
                    $role = !$disable_candidate_register ? 'candidate' : 'employer';
                    if ('employer' == $active_tab) {
                        $role = 'employer';
                    }
                    ?>
                    <input type="hidden" name="role" value="<?php echo $role; ?>">
                    <?php
                    if (in_array('register', iwj_option('use_recaptcha', array()))) {
                        echo '<div class="g-recaptcha" data-sitekey="' . iwj_option('google_recaptcha_site_key') . '"></div>';
                    }
                    ?>
                    <button type="submit" name="register" class="iwj-btn iwj-btn-primary iwj-btn-full iwj-btn-large iwj-register-btn"><?php echo __('Register', 'iwjob'); ?></button>
                </div>
                <div class="iwj-divider">
                    <span class="line"></span>
                    <span class="circle"><?php echo __('Or', 'iwjob'); ?></span>
                </div>
                <?php
                $socials = IWJ()->social_logins->get_available_socials();
                if ($socials) {
                    echo '<div class="social-login row">';
                    foreach ($socials as $social) {
                        if ($social && $social->is_available()) {
                            echo '<div class="col-md-6">
                                <a href="' . $social->get_login_url() . '" class="iwj-btn iwj-btn-primary iwj-btn-medium iwj-btn-full iwj-btn-icon social-login-' . $social->id . '"><i class="' . $social->get_fontawesome_icon() . '"></i>' . $social->get_title() . '</a>
                                </div>';
                        }
                    }
                    echo '</div>';
                }
                ?>
                <input type="hidden" name="redirect_to" value="<?php echo $redirect_to; ?>">
            </form>
            <?php
        } else {
            echo __('Sorry, the registration function is temporarily unavailable, please come back later', 'iwjob');
        }
        ?>
        <?php
    } else {
        $user = IWJ_User::get_user();
        ?>
        <div class="logged-in">
            <p><?php echo sprintf(__('You are logged in as <strong>%s</strong>', 'iwjob'), $user->get_display_name()); ?></p>
            <p><?php echo sprintf(__('Click <a href="%s">here</a> to go to Dashboard Manager', 'iwjob'), iwj_get_page_permalink('dashboard')); ?></p>
        </div>
    <?php } ?>
</div>
