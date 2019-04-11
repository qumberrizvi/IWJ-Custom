<div class="iwj-social-register">
    <?php if(!is_user_logged_in()) { ?>
        <form action="" method="post" class="iwj-form iwj-register-form iwj-social-register">
            <div class="confirm-div">
                <?php if($profile_image_url){ ?>
                    <img class="avatar" src="<?php echo esc_url($profile_image_url); ?>" alt="<?php echo __('Avatar', 'iwjob'); ?>">
                <?php } ?>
                <h2><?php echo __('You\'re almost done!', 'iwjob'); ?></h2>
                <div class="social-confirm-newuser">
                    <p><?php printf(__('You are about to create a new account on <strong>%s</strong></span> using a login from', 'iwjob'), get_bloginfo( 'name', 'display' )); ?></p>
                    <?php if($social){ ?>
                    <div class="social-block">
                        <span class="social-icon <?php echo $social->get_fontawesome_icon(); ?>"></span>
                        <span class="social-name"><?php echo $social->get_title() ?>(<?php echo $email; ?>)</span>
                    </div>
                    <?php } ?>
                </div>
                <p></p>
            </div>
            <div class="iwj-field">
                <label><?php echo __('Phone Number', 'iwjob'); ?></label>
                <div class="iwj-input">
                    <i class="fa fa-user"></i>
                    <input type="text" name="display_name" placeholder="<?php echo __('Enter Your Full Name.', 'iwjob'); ?>" value="<?php echo $display_name; ?>" required>
                </div>
            </div>
            <div class="iwj-field">
                <label><?php echo __('Email', 'iwjob'); ?></label>
                <div class="iwj-input">
                    <i class="fa fa-envelope-o"></i>
                    <input type="email" name="email" placeholder="<?php echo __('Enter Your Email Address.', 'iwjob'); ?>" value="<?php echo $email; ?>" required>
                </div>
            </div>
            <?php
            $disable_candidate_register = iwj_option('disable_candidate_register');
            $disable_employer_register = iwj_option('disable_employer_register');
            if($disable_candidate_register || $disable_employer_register){
                echo '<input type="hidden" name="role" value="'.($disable_candidate_register ? 'employer' : 'candidate').'">';
            }else{?>
                <div class="iwj-field">
                    <label><?php echo __('Register as', 'iwjob'); ?></label>
                    <div class="iwj-input">
                        <i class="fa fa-envelope-o"></i>
                        <select type="select" name="role" class="iwj-role iwj-select-2-wsearch">
                            <option value="candidate"><?php echo __('Teacher', 'iwjob'); ?></option>
                            <option value="employer"><?php echo __('Student', 'iwjob'); ?></option>
                        </select>
                    </div>
                </div>
            <?php }
            $class =  !$disable_candidate_register ? 'hide' : '';
            ?>
            <div class="iwj-field company-field <?php echo $class; ?>">
                <label><?php echo __('Full Name', 'iwjob'); ?></label>
                <div class="iwj-input">
                    <i class="fa fa-vcard-o"></i>
                    <input type="number" name="company" placeholder="<?php echo __('Enter Full Name.', 'iwjob'); ?>">
                </div>
            </div>
            <div class="iwj-respon-msg iwj-hide"></div>
            <div class="iwj-button-loader">
                <input type="hidden" name="username" value="<?php echo $user_name; ?>">
                <input type="hidden" name="profile_image_url" value="<?php echo $profile_image_url; ?>">
                <button type="submit" name="register" class="iwj-btn iwj-btn-primary iwj-register-btn"><?php echo __('Create My Account', 'iwjob'); ?></button>
            </div>
        </form>
    <?php }else{
        $user = IWJ_User::get_user();
        ?>
        <div class="logged-in">
            <p><?php echo sprintf(__('You are loggedin as <strong>%s</strong>', 'iwjob'), $user->get_display_name()); ?></p>
            <p><?php echo sprintf(__('Click <a href="%s">here</a> to go to Dashboard Manager', 'iwjob'), iwj_get_page_permalink('dashboard')); ?></p>
        </div>
    <?php } ?>
</div>
