<div id="iwj-login-popup" class="modal-popup modal fade iwj-login-form-popup">
    <div class="modal-dialog">
        <div class="modal-header">
            <h4 class="modal-title"><?php echo __('Login to your account', 'iwjob'); ?></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-content">
            <form action="<?php echo esc_url(get_permalink()); ?>" method="post" class="iwj-form iwj-login-form">
                <div class="iwj-respon-msg hide"></div>
                <div class="iwj-field">
                    <label><?php echo __('Phone / Email', 'iwjob'); ?></label>
                    <div class="iwj-input">
                        <i class="fa fa-user"></i>
                        <input type="text" name="username" placeholder="<?php echo __('Enter Your Phone or Email.', 'iwjob'); ?>">
                    </div>
                </div>
                <div class="iwj-field">
                    <label><?php echo __('Password', 'iwjob'); ?></label>
                    <div class="iwj-input">
                        <i class="fa fa-keyboard-o"></i>
                        <input type="password" name="password" placeholder="<?php echo __('Enter Password.', 'iwjob'); ?>">
                    </div>
                </div>
                <div class="form-field iwj-button-loader">
                    <?php
                    /*$redirect_url = get_the_permalink();
                    $post = get_post();
                    if($post){
                        $login_id = iwj_get_page_id('login');
                        $register_id = iwj_get_page_id('register');
                        if($post->ID == $login_id || $post->ID == $register_id){
                            $redirect_url = '';
                        }
                    }*/
                    ?>
                    <input type="hidden" name="redirect_to" value="<?php echo esc_url(get_the_permalink()); ?>">
                    <input type="hidden" name="fallback_action" value="">
                    <?php
                    if(in_array('login', iwj_option('use_recaptcha', array()))) {
                        echo '<div class="g-recaptcha" data-sitekey="'.iwj_option('google_recaptcha_site_key').'"></div>';
                    }
                    ?>
                    <button type="submit" name="login" class="iwj-btn iwj-btn-primary iwj-btn-large iwj-btn-full iwj-login-btn"><?php echo __('Login', 'iwjob'); ?></button>
                </div>
                <div class="text-center lost-password">
                    <a href="<?php echo iwj_get_page_permalink('lostpass'); ?>"><?php echo __('Lost Password?', 'iwjob'); ?></a>
                </div>
                <div class="iwj-divider">
                    <span class="line"></span>
                    <span class="circle"><?php echo __('Or', 'iwjob');?></span>
                </div>
                <?php
                $socials = IWJ()->social_logins->get_available_socials();
                if($socials){
                    echo '<div class="social-login row">';
                    foreach ($socials as $social){
                        if($social && $social->is_available()){
                            echo '<div class="col-md-6">
                                <a href="'.$social->get_login_url().'" class="iwj-btn iwj-btn-primary iwj-btn-medium iwj-btn-full iwj-btn-icon social-login-'.$social->id.'"><i class="'.$social->get_fontawesome_icon().'"></i>'.$social->get_title().'</a>
                                </div>';
                        }
                    }
                    echo '</div>';
                }

                $disable_candidate_register = iwj_option('disable_candidate_register');
                $disable_employer_register = iwj_option('disable_employer_register');
                if(!$disable_candidate_register || !$disable_employer_register){
                ?>
                <div class="text-center register-account">
                    <?php echo __('Do not you have an account? ','iwjob')?><a href="<?php echo iwj_get_page_permalink('register'); ?>"><?php echo __('Register', 'iwjob'); ?></a>
                </div>
                <?php } ?>
            </form>
        </div>
    </div>
</div>


