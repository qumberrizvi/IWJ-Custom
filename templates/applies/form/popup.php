<div class="modal fade" id="iwj-modal-apply-<?php echo get_the_ID(); ?>" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="iwj-application-form iwj-popup-form" action="<?php the_permalink(); ?>" method="post" enctype="multipart/form-data">
				<div class="modal-header">
					<h4 class="modal-title"><?php echo __( 'Application Form', 'iwjob' ); ?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<?php
					$user          = IWJ_User::get_user();
					$dashboard_url = iwj_get_page_permalink( 'dashboard' );
					$show_form     = true;
					if ( iwj_option( 'verify_account' ) && $user && ! $user->is_verified() ) {
						$show_form = false;
						?>
						<div class="iwj-alert-box">
							<?php echo sprintf( __( 'You must verify account before apply job. <a href="%s">verify now</a>', 'iwjob' ), iwj_get_page_permalink( 'verify_account' ) ); ?>
						</div>
						<?php
					} elseif ( $user && iwj_option( 'allow_candidate_apply_job' ) ) {
						$candidate   = $user->get_candidate();
						$profile_url = add_query_arg( array( 'iwj_tab' => 'profile' ), $dashboard_url );

						if ( $candidate ) {
							if ( iwj_option( 'allow_candidate_apply_job' ) == '1' && ! $candidate->is_active() ) {
								$show_form = false;
								?>
								<div class="iwj-alert-box">
									<?php echo sprintf( __( 'Your profile must be activated before apply job. <a href="%s">update profile</a>', 'iwjob' ), $profile_url ); ?>
								</div>
								<?php
							} else {
								$status = $candidate->get_status();
								if ( ! in_array( $status, array( 'pending', 'publish' ) ) ) {
									$show_form = false;
									?>
									<div class="iwj-alert-box">
										<?php echo sprintf( __( 'You must submit profile before apply job. <a href="%s">submit now</a>', 'iwjob' ), $profile_url ); ?>
									</div>
									<?php
								}
							}
						}
					}

					if ( $show_form ) {
						$login_url = iwj_get_page_permalink( 'login' );

						$apply_form = 1;
						if ( ! iwj_option( 'apply_job_mode' ) ) {
							if ( ! $user ) {
								$apply_form = 0; // must login
							} else {
								$user_package_ids = $user->get_user_package_ids( 'apply_job_package' );
								if ( ! $user_package_ids ) {
									$apply_form = 2; // must buy apply package
								} else {
									$can_view_packages = array();
									foreach ( $user_package_ids as $user_package_id ) {
										$user_package = IWJ_User_Package::get_user_package( $user_package_id );
										if ( $user_package->get_remain_apply_job() > 0 && $user_package->get_status() == 'publish' ) {
											$can_view_packages[] = $user_package;
										}
									}
									if ( count( $can_view_packages ) < 1 ) {
										$apply_form = 3; // package is pending payment or expired
									} else {
										$apply_form = 4;
									}
								}
							}
						}

						switch ( $apply_form ) {
							case 0: ?>
								<div class="iwj-alert-box">
									<?php echo sprintf( __( 'You must login before apply job. <a href="%s">Login now</a>', 'iwjob' ), $login_url ); ?>
								</div>
								<?php
								break;
							case 2: ?>
								<div class="iwj-alert-box">
									<?php echo sprintf( __( 'You must buy Apply Class package before apply job. <a href="%s">Buy now</a>', 'iwjob' ), add_query_arg( array( 'iwj_tab' => 'new-apply-job-package' ), $dashboard_url ) ); ?>
								</div>
								<?php
								break;
							case 3: ?>
								<div class="iwj-alert-box">
									<?php echo sprintf( __( 'Your Apply Class package is pending or expired. <a href="%s">Check now</a>', 'iwjob' ), add_query_arg( array( 'iwj_tab' => 'apply-job-package' ), $dashboard_url ) ); ?>
								</div>
								<?php
								break;
							case 1:
							case 4:

								$fields = $self->get_form_fields();
								foreach ( $fields as $field ) {
									$field = IWJMB_Field::call( 'normalize', $field );
									$meta  = IWJMB_Field::call( $field, 'post_meta', 0, false );
									IWJMB_Field::input( $field, $meta );
								}
                            if(in_array('apply_job', (array)iwj_option('use_recaptcha'))) {
                                echo '<div class="g-recaptcha" data-sitekey="' . iwj_option('google_recaptcha_site_key') . '"></div>';
                            }
							if ( iwj_option( 'show_terms_services_on_apply_job' ) ) {
								$terms_services_label = iwj_option( 'apply_job_terms_services_label' ) ? iwj_option( 'apply_job_terms_services_label' ) : __( 'I have read and agree to the <a href="#apply_job_terms_services">Terms and Services</a>', 'iwjob' );
								$terms_services_desc  = iwj_option( 'apply_job_terms_services_desc' );
								?>
								<div class="iwjmb-field iwjmb-gdpr__applyjob iwj-input-checkbox">
                                    <input id="lb_terms_and_services_check" type="checkbox" name="iwj_apply_terms_and_services">
								    <label for="lb_terms_and_services_check" class="lb_terms_and_services"><?php echo $terms_services_label; ?></label>
                                    <?php if ( $terms_services_desc ) { ?>
                                        <textarea readonly name="terms_and_services_desc" class="hide"><?php echo $terms_services_desc; ?></textarea>
                                    <?php } ?>
								</div>
								<?php
							}
								?>

								<div class="iwj-respon-msg iwj-hide"></div>
								<input type="hidden" name="job_id" value="<?php echo get_the_ID(); ?>">
								<input type="hidden" name="action" value="iwj_submit_application">
								<div class="iwj-btn-action">
									<button type="button" class="iwj-btn" data-dismiss="modal"><?php echo __( 'Close', 'iwjob' ); ?></button>
									<div class="iwj-button-loader">
										<button type="submit" class="iwj-btn iwj-btn-primary iwj-application-btn" <?php echo iwj_option( 'show_terms_services_on_apply_job' ) ? 'disabled' : ''; ?>><?php echo __( 'Apply Now', 'iwjob' ); ?></button>
									</div>
								</div>
								<?php
								break;
						}
					} ?>
				</div>
			</form>
		</div>
	</div>
</div>