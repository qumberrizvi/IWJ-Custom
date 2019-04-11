<div class="iwj-content-inner">
	<div id="iwajax-load">
		<?php
		if ( isset( $atts['ide_logo_company'] ) && $atts['ide_logo_company'] ) {
			$get_atm_logo_url = wp_get_attachment_image_src( $atts['ide_logo_company'], 'thumbnail' );
			$logo_url         = $get_atm_logo_url[0];
		} else {
			$logo_url = get_template_directory_uri() . '/assets/images/shortcodes/default-company-logo.png';
		}
		if ( isset( $atts['show_filter_bar'] ) && $atts['show_filter_bar'] ) { ?>
			<form action="" name="iwj_indeed_data" class="iwj-job-indeed-loader" method="post" data-publisher_id="<?php echo esc_attr( $atts['ide_publisher_id'] ) ?>" data-max_items="<?php echo esc_attr( $atts['ide_max_item'] ) ?>" data-style="<?php echo esc_attr( $atts['style'] ) ?>" data-logo_url="<?php echo esc_attr( $logo_url ); ?>">
				<div class="row">
					<div class="col-md-4 col-sm-4 col-xs-12">
						<div class="iwjmb-field">
							<input type="text" class="iwjmb-text form-control" name="iwj_ide_query" placeholder="<?php echo esc_attr__( 'All jobs', 'iwjob' ) ?>" value="<?php echo esc_attr( $atts['ide_query'] ) ?>">
						</div>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-12">
						<div class="iwjmb-field">
							<select class="iwj_ide_type default-sorting iwj-select-2 form-control" name="iwj_ide_type">
								<option value="" selected="selected"><?php echo __( "Select type", 'iwjob' ) ?></option>
								<?php
								$list_types = array(
									'fulltime'   => esc_html__( 'Full Time', 'iwjob' ),
									'parttime'   => esc_html__( 'Part Time', 'iwjob' ),
									'contract'   => esc_html__( 'Contract', 'iwjob' ),
									'internship' => esc_html__( 'Internship', 'iwjob' ),
									'temporary'  => esc_html__( 'Temporary', 'iwjob' ),
                                );
								foreach ( $list_types as $key_ct => $type ) {
									echo '<option value="' . $key_ct . '" ' . selected( $atts['ide_job_type'], $key_ct ) . '>' . $type . '</option>';
								} ?>
							</select>
						</div>
					</div>
					<div class="col-md-4 col-sm-6 col-xs-12">
						<div class="iwjmb-field">
							<select class="iwj_ide_location default-sorting iwj-select-2 form-control" name="iwj_ide_location" >
								<option value="" selected="selected"><?php echo __( "Select country", 'iwjob' ) ?></option>
								<?php
								$list_countries = iwj_countries_list();
								foreach ( $list_countries as $key_ct => $list_country ) {
									echo '<option value="' . $key_ct . '" ' . selected( $atts['ide_country'], $key_ct ) . '>' . $list_country . '</option>';
								} ?>
							</select>
						</div>
					</div>
				</div>
			</form>
		<?php } ?>
		<div class="iwj-jobs iwj-listing iwj-jobs-listing-term">
			<div class="iwj-job-items <?php echo $atts['style']; ?>">
				<?php
				if ( $jobs && count( $jobs ) ) {
					$show_company = iwj_option( 'show_company_job' );
					for ( $i = 0; $i < count( $jobs ); $i ++ ) {
						$item_id              = wp_filter_nohtml_kses( $jobs[ $i ]['id'] );
						$item_title           = wp_filter_nohtml_kses( $jobs[ $i ]['title'] );
						$item_url             = wp_filter_nohtml_kses( $jobs[ $i ]['url'] );
						$item_description     = stripslashes( $jobs[ $i ]['description'] );
						$item_city            = wp_filter_nohtml_kses( $jobs[ $i ]['city'] );
						$item_state           = wp_filter_nohtml_kses( $jobs[ $i ]['state'] );
						$item_country         = wp_filter_nohtml_kses( $jobs[ $i ]['country'] );
						$item_f_location_full = wp_filter_nohtml_kses( $jobs[ $i ]['formatted_loc_full'] );
						$item_company         = wp_filter_nohtml_kses( $jobs[ $i ]['company'] );
						$item_relative_time   = wp_filter_nohtml_kses( $jobs[ $i ]['relative_time'] );
						$item_onmousedown     = wp_filter_nohtml_kses( $jobs[ $i ]['onmousedown'] ); ?>
						<div class="grid-content" data-id="<?php echo $item_id; ?>">
							<div class="job-item">
								<?php
								switch ( $atts['style'] ) {
									case 'style1':
									case 'style2':
										if ( $item_company ) { ?>
											<div class="job-image">
												<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo $item_company; ?>" width="150" height="150">
											</div>
										<?php } ?>
										<div class="job-info">
											<h3 class="job-title">
												<a href="<?php echo esc_url( $item_url ); ?>"><?php echo( $item_title ); ?></a>
											</h3>
											<div class="info-company">
												<?php if ( $item_company && ( $show_company == '1' ) ) : ?>
													<div class="company"><i class="fa fa-suitcase"></i>
														<?php echo $item_company; ?>
													</div>
												<?php endif; ?>

												<?php if ( $item_f_location_full ) : ?>
													<div class="address">
														<i class="ion-android-pin"></i><?php echo $item_f_location_full; ?>
													</div>
												<?php endif; ?>
											</div>

											<div class="job-type <?php echo $atts['ide_job_type'] ? $atts['ide_job_type'] : ''; ?>">
												<?php if ( $atts['ide_job_type'] ) { ?>
													<span class="type-name"><?php echo $atts['ide_job_type']; ?></span>
												<?php } ?>
											</div>
										</div>
										<?php
										break;
									case 'style3':
										if ( $item_company ) { ?>
											<div class="job-image">
												<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo $item_company; ?>" width="150" height="150">
											</div>
										<?php } ?>
										<div class="job-info">
											<h3 class="job-title">
												<a href="<?php echo esc_url( $item_url ); ?>"><?php echo( $item_title ); ?></a>
											</h3>
											<div class="info-company">
                                                <?php if ($atts['ide_job_type']) { ?>
                                                    <div class="job-type <?php echo $atts['ide_job_type']; ?>">
                                                        <span class="type-name"><?php echo $atts['ide_job_type']; ?></span>
                                                    </div>
                                                <?php } ?>
												<?php if ( $item_f_location_full ) : ?>
													<div class="address">
														<i class="ion-android-pin"></i><?php echo $item_f_location_full; ?>
													</div>
												<?php endif; ?>
											</div>
											<div class="job-company-time">
												<?php if ( $item_company && ( $show_company == '1' ) ) : ?>
													<div class="company"><i class="fa fa-suitcase"></i>
														<?php echo $item_company; ?>
													</div>
												<?php endif; ?>
												<?php
												if ( $item_relative_time ) { ?>
													<div class="job-posted-time">
														<?php echo $item_relative_time; ?>
													</div>
												<?php }
												?>

											</div>
										</div>
										<?php
										break;
								} ?>
							</div>
						</div>
						<?php
					}

				} else {
					echo '<div class="iwj-alert-box">' . __( 'No class found', 'iwjob' ) . '</div>';
				} ?>
				<div class="clearfix"></div>
            </div>
            <?php if ( isset( $atts['show_load_more'] ) && $atts['show_load_more'] ) { ?>
                <div class="w-pag-load-more iwj-button-loader">
                    <button class="iwj-btn iwj-btn-primary iwj-ide-showmore" data-query="<?php echo esc_attr($atts['ide_query']); ?>" data-style="<?php echo esc_attr($atts['style']); ?>" data-publisher_id="<?php echo esc_attr( $atts['ide_publisher_id'] ) ?>" data-max_items="<?php echo esc_attr( $atts['ide_max_item'] ) ?>" data-logo_url="<?php echo esc_attr( $logo_url ); ?>" data-country="<?php echo esc_attr($atts['ide_country']); ?>" data-location="<?php echo esc_attr($atts['ide_location']); ?>" data-job_type="<?php echo esc_attr($atts['ide_job_type']); ?>"><?php echo __( 'Show More', 'iwjob' ); ?></button>
                </div>
            <?php } ?>
		</div>

	</div>

</div>