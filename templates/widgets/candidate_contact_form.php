<?php
$post = get_post();
$user = IWJ_User::get_user();
$candidate = IWJ_Candidate::get_candidate($post);

if(is_single() && $post && $post->post_type == 'iwj_candidate' && (iwj_option('view_free_resum')  || ($user && $user->can_view_resum($candidate->get_id())))) {
    echo $args['before_widget'];

    if (isset($instance['title'])) {
        $title = (!empty($instance['title'])) ? $instance['title'] : '';
        $title = apply_filters('widget_title', $title, $instance, $widget_id);

        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
    }
    ?>
    <div class="iwj-candicate-widget-wrap">
        <?php if(iwj_option('view_free_resum')  || ($user && $user->can_view_resum($candidate->get_id()))){ ?>
            <div class="candidate-action-button iwj-single-widget">
                <?php if(iwj_option('email_contact_enable')){ ?>
                    <div class="iwj-employer-widget-wrap">
                        <div class="iwj-single-contact-form">
                            <form class="iwj-contact-form" action="#" method="post" enctype="multipart/form-data">
                                <?php
                                iwj_field_text('name', '', true, null, ($user ? $user->get_display_name() : ''), '', '', __('Your name', 'iwjob'));

                                iwj_field_email('email', '', true, null, ($user ? $user->get_email() : ''), '', '', __('Your email', 'iwjob'));

                                iwj_field_text('subject', '', true, null, null, '', '', __('Subject', 'iwjob'));

                                iwj_field_textarea('message', '', true, null, null, '', '', __('Message', 'iwjob'));

                                if ( iwj_option( 'show_terms_services_on_c_contact_form' ) ) {
	                                $terms_services_label = iwj_option( 'candidate_cf_terms_services_label' ) ? iwj_option( 'candidate_cf_terms_services_label' ) : __( 'I have read and agree to the <a href="#candidate_cf_terms_services">Terms and Services</a>', 'iwjob' );
	                                $terms_services_desc  = iwj_option( 'candidate_cf_terms_services_desc' );
	                                ?>
									<div class="iwjmb-field iwjmb-gdpr__candidate">
										<label class="lb_terms_and_services"><input type="checkbox" name="iwj_candidate_cf_terms_and_services"> <?php echo $terms_services_label; ?></label>
		                                <?php if ( $terms_services_desc ) { ?>
											<textarea readonly name="terms_and_services_desc" class="hide"><?php echo $terms_services_desc; ?></textarea>
		                                <?php } ?>
									</div>
	                                <?php
                                } ?>
                                <div class="iwj-respon-msg iwj-hide"></div>
                                <input type="hidden" name="item_id" value="<?php echo $candidate->get_id(); ?>">
                                <div class="iwj-btn-action">
                                    <div class="iwj-button-loader">
                                        <?php
                                        if(in_array('contact', iwj_option('use_recaptcha', array()))) {
                                            echo '<div class="g-recaptcha" data-sitekey="'.iwj_option('google_recaptcha_site_key').'"></div>';
                                        }
                                        ?>
                                        <button type="submit" class="iwj-btn iwj-btn-primary iwj-contact-btn" <?php echo iwj_option( 'show_terms_services_on_c_contact_form' ) ? 'disabled' : ''; ?>><i class="ion-android-send"></i><?php echo __('Send Now', 'iwjob'); ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

    <?php

    echo $args['after_widget'];
}