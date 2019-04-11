<div class="iwj-content-inner <?php echo $class; ?>">

	<div id="iwajax-load">
		<div class="iwj-jobs iwj-listing">
			<div class="iwj-job-items">
				<?php
				if ( $jobs ) {
					foreach ( $jobs as $job ) {
						$job           = IWJ_Job::get_job( $job );
						$permalink     = $job->permalink();
						$is_featured   = $job->is_featured();
						$type          = $job->get_type();
						$author        = $job->get_author();
						$employer      = $job->get_author();
						$user          = IWJ_User::get_user();
						$show_company  = iwj_option( 'show_company_job' );
						$show_salary   = iwj_option( 'show_salary_job' );
						$show_location = iwj_option( 'show_location_job' );
						?>
						<div class="grid-content" data-id="<?php echo $job->get_id() ?>">
							<div class="job-item <?php echo $is_featured ? 'featured-item' : '' ?>">
								<?php if ( $author ) { ?>
									<div class="job-image"><?php echo iwj_get_avatar( $author->get_id() ); ?></div>
								<?php } ?>
								<div class="job-info">
									<h3 class="job-title">
										<a href="<?php echo $job->get_indeed_url() ? esc_url( $job->get_indeed_url() ) :esc_url( $permalink ); ?>"><?php echo( $job->get_title() ); ?></a>
									</h3>
									<div class="info-company">
										<?php if ( $author && ( $show_company == '1' ) ) : ?>
											<div class="company"><i class="fa fa-suitcase"></i>
												<?php if ( $job->get_indeed_company_name() ) { ?>
													<a href="<?php echo $job->get_indeed_url(); ?>"><?php echo $job->get_indeed_company_name(); ?></a>
												<?php } elseif ( $author->is_active_profile() ) { ?>
													<a href="<?php echo $author->permalink(); ?>"><?php echo $author->get_display_name(); ?></a>
												<?php } else {
													echo $author->get_display_name();
												} ?>
											</div>
										<?php endif; ?>
										<?php if ( ( $job->get_salary() ) && ( $show_salary == '1' ) ) { ?>
											<div class="sallary">
												<i class="iwj-icon-money"></i><?php echo $job->get_salary(); ?></div>
										<?php } ?>
										<?php if ( ( $locations = $job->get_locations_links() ) && ( $show_location == '1' ) ) : ?>
											<div class="address">
												<i class="ion-android-pin"></i><?php echo $locations; ?>
											</div>
										<?php endif; ?>
									</div>
									<div class="job-type <?php echo $type ? $type->slug : ''; ?>">
										<?php if ( $type ) {
											$color = get_term_meta( $type->term_id, IWJ_PREFIX . 'color', true ); ?>
											<a class="type-name" href="<?php echo get_term_link( $type->term_id, 'iwj_type' ); ?>" <?php echo $color ? 'data-color="' . $color . '" style="color: ' . $color . '"' : ''; ?>><?php echo $type->name; ?></a>
										<?php } ?>
										<?php if ( ! is_user_logged_in() ) { ?>
											<button class="save-job" data-toggle="modal" data-target="#iwj-login-popup">
												<i class="fa fa-heart"></i></button>
										<?php } else if ( current_user_can( 'apply_job' ) ) { ?>
											<a href="#" class="iwj-save-job <?php echo $user->is_saved_job( $job->get_id() ) ? 'saved' : ''; ?>" data-id="<?php echo $job->get_id(); ?>" data-in-list="true"><i class="fa fa-heart"></i></a>
										<?php } ?>
									</div>
								</div>
								<?php if ( $is_featured ) : ?>
									<div class="iwj-featured"></div>
								<?php endif; ?>
							</div>
						</div>
						<?php
					}
				} else {
					echo __( 'No class found', 'iwjob' );
				} ?>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>

</div>