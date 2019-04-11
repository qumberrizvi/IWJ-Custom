<?php
wp_enqueue_script( 'iwj-rating-custom' );
$user         = IWJ_User::get_user();
$vote_reviews = $user->get_reviews();
$search       = isset( $_GET['search'] ) ? $_GET['search'] : '';
$url          = iwj_get_page_permalink( 'dashboard' ); ?>

<div class="iwj-vote-review iwj-main-block">
	<div class="iwj-search-form">
		<form action="<?php echo $url; ?>">
			<span class="search-box">
                <input type="text" class="search-text" placeholder="<?php echo __( 'Search Title', 'iwjob' ); ?>" name="search" value="<?php echo esc_attr( $search ); ?>">
                <button class="search-button" type="submit"><i class="fa fa-search"></i></button>
            </span>
			<input type="hidden" name="iwj_tab" value="my-reviews">
		</form>
	</div>
	<div class="iwj-respon-msg-approve"></div>
	<div class="iwj-table-overflow-x">
		<table class="table">
			<thead>
			<tr>
				<th width="20%"><?php echo __( 'Company', 'iwjob' ); ?></th>
				<th width="20%"><?php echo __( 'Title', 'iwjob' ); ?></th>
				<th width="35%"><?php echo __( 'Review', 'iwjob' ); ?></th>
				<th width="7%" class="text-center"><?php echo __( 'Rating', 'iwjob' ); ?></th>
				<th width="7%" class="text-center"><?php echo __( 'Status', 'iwjob' ); ?></th>
				<th width="7%" class="text-center"><?php echo __( 'Actions', 'iwjob' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php
			if ( $vote_reviews ) {
				if ( $vote_reviews['result'] ) {
					foreach ( $vote_reviews['result'] as $vote_review ) {
						$review = IWJ_Reviews::get_review( $vote_review->ID ); ?>
						<tr id="review-<?php esc_attr_e( $review->get_id(), 'iwjob' ); ?>" class="review-item">
							<td>
								<h3 class="author-name"><?php esc_html_e( $review->get_employer_name(), 'iwjob' ); ?></h3>
							</td>
							<td>
								<a href="<?php echo esc_attr( $review->review_link() ); ?>"><?php esc_html_e( $review->get_title(), 'iwjob' ); ?></a>
							</td>
							<td><?php esc_html_e( $review->get_content(), 'iwjob' ); ?></td>
							<td class="text-center">
								<div class="iwj-dasb-rate-num">
									<?php esc_html_e( $review->get_rate_star(), 'iwjob' ); ?>
								</div>
							</td>
							<td class="iwj-status text-center">
								<span data-toggle="tooltip" class="<?php echo esc_attr($review->get_status()); ?>" title="<?php echo esc_attr($review->get_status()); ?>"><?php echo iwj_get_status_icon( $review->get_status() ); ?></span>

							</td>
							<td class="text-center">
								<div class="iwj-menu-action-wrap">
									<a href="#" tabindex="0" class="iwj-toggle-action collapsed" type="button" data-toggle="collapse" data-trigger="focus" data-target="#nav-collapse<?php esc_attr_e( $review->get_id(), 'iwjob' ); ?>"></a>
									<div id="nav-collapse<?php esc_attr_e( $review->get_id(), 'iwjob' ) ?>" class="collapse iwj-menu-action" data-id="nav-collapse<?php esc_attr_e( $review->get_id(), 'iwjob' ) ?>">
										<div class="iwj-menu-action-inner">
											<div>
												<a href="#" class="iwj-c-edit-review" data-id="<?php esc_attr_e( $review->get_id(), 'iwjob' ) ?>"
												   data-title="<?php esc_attr_e( $review->get_title(), 'iwjob' ) ?>" data-content="<?php echo esc_attr( $review->get_content() ); ?>"
												   data-vote_for="<?php esc_attr_e( json_encode( $review->get_criterias() ), 'iwjob' ); ?>" data-rate_star="<?php esc_attr_e( $review->get_rate_star(), 'iwjob' ); ?>"
												   data-user_id="<?php echo esc_attr( $review->get_user_id() ); ?>" data-item_id="<?php echo esc_attr( $review->get_item_id() ); ?>"><?php echo __( 'Edit', 'iwjob' ); ?></a>
											</div>
											<div>
												<a href="#" class="iwj-c-delete-review" data-id="<?php echo esc_attr( $review->get_id() ); ?>" data-message="<?php printf( __( 'Are you sure you want to delete %s?', 'iwjob' ), $review->get_title() ); ?>"><?php echo __( 'Delete', 'iwjob' ); ?></a>
											</div>
										</div>
									</div>
								</div>
							</td>
						</tr>
						<?php
					}
				}
			} else { ?>
				<tr class="iwj-empty">
					<td colspan="6"><?php echo __( 'No review found', 'iwjob' ); ?></td>
				</tr>
			<?php }
			?>
			</tbody>
		</table>
	</div>

	<div class="modal fade" id="iwj-confirm-edit-review" role="dialog">
		<div class="modal-dialog">
			<form action="" method="post" class="iwj_candidate_edit_review">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title"><?php echo __( 'Edit Review', 'iwjob' ); ?></h4>
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<div class="iwj-candidate-rev-e iwj-rate-stars">
							<?php $review_options = iwj_option( 'review_options', '' );
							$trim_option          = trim( $review_options ); ?>
							<div class="re-post-form-submit" data-number_criteria="<?php echo ! empty( $trim_option ) ? 'group_vote' : 'simple_vote'; ?>">
								<?php
								if ( ! empty( $trim_option ) ) {
									$arr_reviews = explode( "\n", $review_options ); ?>
									<span class="re-text"><?php esc_html_e( 'Overall Rating:', 'iwjob' ); ?></span>
									<span class="iwj-count-stars">
									<div class="iwj-votes-icon">
										<i class="ion-android-star-outline"></i>
										<i class="ion-android-star-outline"></i>
										<i class="ion-android-star-outline"></i>
										<i class="ion-android-star-outline"></i>
										<i class="ion-android-star-outline"></i>
									</div>
									<div class="iwj-box-each-vote iwj-review-voting" data-total_views="<?php echo esc_attr( count( $arr_reviews ) ); ?>">
										<?php
										if ( count( $arr_reviews ) ) {
											foreach ( $arr_reviews as $key_item => $rev_item ) {
												$rev_item_name = strtolower( str_replace( ' ', '_', trim( $rev_item ) ) ); ?>
												<div class="iwj-line-tc-vote">
													<span class="line-tc-title"><?php echo esc_html__( $rev_item, 'iwjob' ); ?></span>
													<span class="line-tc-star">
														<input type="hidden" class="iwj_num_rate rating " data-size="xs" data-step="1" name="iwj_rate_num_<?php echo esc_attr( $key_item ); ?>" data-criteria_vote="<?php echo esc_attr( $rev_item_name ); ?>">
													</span>
												</div>
												<?php
											}
										} ?>
									</div>
								</span>
									<?php
								} else { ?>
									<span class="re-text"><?php esc_html_e( 'Rating:', 'iwjob' ); ?></span>
									<span class="iwj-count-stars">
									<input type="hidden" class="rating iwj_simple_rate" data-size="xs" data-step="1" name="iwj_simple_rate">
								</span>
								<?php } ?>
							</div>
						</div>
						<div class="iwj-candidate-rev-e">
							<span class="re-text"><?php esc_html_e( 'Title:', 'iwjob' ); ?></span>
							<div class="iwj-cdd-rev-field">
								<input type="text" name="iwj_review_title" value="">
							</div>
						</div>
						<div class="iwj-candidate-rev-e">
							<span class="re-text"><?php esc_html_e( 'Content:', 'iwjob' ); ?></span>
							<div class="iwj-cdd-rev-field">
								<textarea name="iwj_review_content" class="iwjmb-textarea" cols="30" rows="4"></textarea>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<div class="iwj-respon-msg text-left"></div>
						<div class="iwj-button-loader">
							<input type="hidden" name="user_id_rate" value="">
							<input type="hidden" name="rate_item_id" value="">
							<button type="submit" class="btn btn-primary iwj-agree-edit-review" data-id=""><?php echo __( 'Update', 'iwjob' ); ?></button>
							<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'Close', 'iwjob' ); ?></button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="modal fade" id="iwj-confirm-delete-review" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><?php echo __( 'Confirm Delete', 'iwjob' ); ?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<p></p>
				</div>
				<div class="modal-footer">
					<div class="iwj-respon-msg"></div>
					<div class="iwj-button-loader">
						<button type="button" class="btn btn-primary iwj-agree-delete-review"><?php echo __( 'Continue', 'iwjob' ); ?></button>
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'Close', 'iwjob' ); ?></button>
					</div>
				</div>
			</div>

		</div>
	</div>
	<div class="clearfix"></div>
	<?php if ( $vote_reviews && $vote_reviews['total_page'] > 1 ) { ?>
		<div class="iwj-pagination">
			<?php
			echo paginate_links( array(
				'base'      => add_query_arg( 'cpage', '%#%' ),
				'format'    => '',
				'prev_text' => __( '&laquo;' ),
				'next_text' => __( '&raquo;' ),
				'total'     => $vote_reviews['total_page'],
				'current'   => $vote_reviews['current_page']
			) );
			?>
		</div>
		<div class="clearfix"></div>
	<?php } ?>
</div>
