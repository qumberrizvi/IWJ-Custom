<?php
$user = IWJ_User::get_user();
?>
<div class="iwj-edit-candidate-profile iwj-edit-profile-page">
    <form method="post" action="" class="iwj-form-2 iwj-user-form">

        <?php do_action('iwj_before_user_form', $user); ?>

        <div class="basic iwj-block">
            <?php iwj_field_avatar(IWJ_PREFIX.'avatar', '', false, 0, null, '', ''); ?>

            <?php
            //Phone Number
            iwj_field_text('your_name', __('Phone Number *', 'iwjob'), true, 0, $user->get_display_name(), '', '', __('Enter your phone', 'iwjob'));

            //email
            iwj_field_email('email', __('Email *', 'iwjob'), true, 0, $user->get_email(), '', '', __('Enter your email', 'iwjob'));

            iwj_field_textarea('description', __('Description', 'iwjob'), false, 0, $user->get_description());

            //website url
            iwj_field_url('website', __('Website', 'iwjob'), false, 0, $user->get_website(), '', '', __('Enter your website', 'iwjob'));
            ?>

	        <?php
	        if ( iwj_option( 'show_gdpr_on_profile' ) ) { ?>
				<div class="gdpr_profile-area iwj-block-inner">
					<h3 class=""><?php echo __( 'GDPR Agreement *', 'iwjob' ); ?></h3>
					<div>
						<div class="row">
							<div class="col-md-12">
						        <?php
						        $label_gdpr = iwj_option( 'gdpr_on_profile_label' ) ? iwj_option( 'gdpr_on_profile_label' ) : __( 'I agree to let this website to save my submitted information.', 'inmag' );
						        iwj_field_input( 'checkbox', IWJ_PREFIX . 'gdpr_profile', $label_gdpr, true, $post_id );
						        ?>
							</div>
					        <?php
					        if ( iwj_option( 'gdpr_on_profile_desc' ) ) { ?>
								<div class="col-md-12 ">
									<div class="iwjmb-field iwjmb-textarea-wrapper">
										<div class="iwjmb-input ui-sortable">
											<textarea cols="60" rows="3" id="<?php esc_attr_e( IWJ_PREFIX . 'gdpr_profile_desc' ); ?>" class="iwjmb-textarea  large-text" name="<?php esc_attr_e( IWJ_PREFIX . 'gdpr_profile_desc' ); ?>" readonly="readonly"><?php echo iwj_option( 'gdpr_on_profile_desc' ); ?></textarea>
										</div>
									</div>
								</div>
					        <?php } ?>
						</div>
					</div>
				</div>

		        <?php do_action( 'iwj_employer_form_after_gdpr', $employer ); ?>

	        <?php } ?>

            <div class="iwj-respon-msg iwj-hide"></div>
            <div class="iwj-button-loader">
                <button type="submit" class="iwj-btn iwj-btn-primary iwj-user-btn"><?php echo __('Update Profile', 'iwjob');?></button>
            </div>
        </div>

        <?php
        do_action('iwj_after_user_form', $user);
        ?>
    </form>

    <?php
    iwj_get_template_part('dashboard/profile/change-password');
    ?>
</div>