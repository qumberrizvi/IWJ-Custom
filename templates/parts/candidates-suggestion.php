<div class="iwj-content-inner <?php echo $class; ?>">
	<div id="iwajax-load-candidates">
		<div class="iwj-candidates iwj-grid">
			<?php
			$show_candidate_public_profile = iwj_option( 'show_candidate_public_profile', '' );
			$login_page_id                 = get_permalink( iwj_option( 'login_page_id' ) );
			if ( $candidates ) {
				$user = IWJ_User::get_user();
				foreach ( $candidates as $candidate ) {
					$candidate        = IWJ_Candidate::get_candidate( $candidate );
					$image            = iwj_get_avatar( $candidate->get_author_id(), '120', '', $candidate->get_title(), array('img_size'=>'inwave-avatar2') );
					$desc             = $candidate->get_description();
					$col              = 12 / $atts['candidate_on_row']; ?>
					<div class="grid-content col-sm-<?php echo $col; ?>">
						<div class="candidate-item">
							<div class="candidate-bg-image"><?php echo( $image ); ?></div>
							<div class="candidate-info">
								<div class="info-top">
									<div class="candidate-image"><?php echo( $image ); ?></div>
									<h3 class="candidate-title">
										<?php
										if ( ! $show_candidate_public_profile ) {
											$link_profile = get_permalink( $candidate->get_id() );
										} else {
											if ( $user ) {
												if ( $show_candidate_public_profile == 1 ) {
													$link_profile = get_permalink( $candidate->get_id() );
												} else {
													if ( $user->is_employer() ) {
														$link_profile = get_permalink( $candidate->get_id() );
													} else {
														$link_profile = 'javascript:void(0)';
													}
												}
											} else {
												$link_profile = add_query_arg( 'redirect_to', $candidate->permalink(), $login_page_id );
											}
										} ?>
										<a href="<?php echo $link_profile; ?>"><?php echo $candidate->get_title(); ?></a>
									</h3>
									<?php if ( $candidate->get_headline() ) : ?>
										<div class="headline"><?php echo $candidate->get_headline(); ?></div>
									<?php endif; ?>
								</div>
								<div class="info-bottom">
									<?php
									if ( iwj_option( 'view_free_resum' ) || ( $user && $user->can_view_resum( $candidate->get_id() ) ) ) { ?>
										<div class="social-link">
											<ul>
												<?php
												foreach ( $candidate->get_social_media() as $key => $value ) {
													if ( $value != null && $value != '' ) {
														if ( $key == "google_plus" ) {
															echo '<li><a class="google-plus" href="' . $value . '" title="' . $key . '"><i class="ion-social-googleplus"></i></a></li>';
														} else {
															echo '<li><a class="' . $key . '" href="' . $value . '" title="' . $key . '"><i class="ion-social-' . $key . '"></i></a></li>';
														}
													}
												}
												?>
											</ul>
										</div>
									<?php } ?>
									<?php if ( $desc ) : ?>
										<div class="candidate-desc"><?php echo esc_attr( wp_trim_words( $desc, 15 ) ); ?></div>
									<?php endif; ?>
									<a class="view-resume" href="<?php echo $link_profile; ?>"><?php echo __( "View Profile", 'iwjob' ); ?></a>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
			} else {
				echo __( 'No candidate found', 'iwjob' );
			} ?>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<div class="clearfix"></div>