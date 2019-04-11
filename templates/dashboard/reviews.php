<?php
$user                  = IWJ_User::get_user();
$vote_reviews_employer = $user->get_reviews_employer();
$search                = isset( $_GET['search'] ) ? $_GET['search'] : '';
$url                   = iwj_get_page_permalink( 'dashboard' );
?>
<div class="iwj-vote-review iwj-main-block">
	<div class="iwj-search-form">
		<form action="<?php echo $url; ?>">
			<span class="search-box">
                <input type="text" class="search-text" placeholder="<?php echo __( 'Search Title', 'iwjob' ); ?>" name="search" value="<?php echo esc_attr( $search ); ?>">
                <button class="search-button" type="submit"><i class="fa fa-search"></i></button>
            </span>
			<input type="hidden" name="iwj_tab" value="reviews">
		</form>
	</div>
	<div class="iwj-respon-msg-approve"></div>
	<div class="iwj-table-overflow-x">
		<table class="table">
			<thead>
			<tr>
				<th width="15%"><?php echo __( 'Reviewer', 'iwjob' ); ?></th>
				<th width="20%"><?php echo __( 'Title', 'iwjob' ); ?></th>
				<th width="30%"><?php echo __( 'Review', 'iwjob' ); ?></th>
				<th width="13%" class="text-center"><?php echo __( 'Rating', 'iwjob' ); ?></th>
				<th width="10%" class="text-center"><?php echo __( 'Status', 'iwjob' ) ?></th>
				<th width="12%" class="text-center"><?php echo __( 'Action', 'iwjob' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php
			if ( $vote_reviews_employer ) {
				if ( $vote_reviews_employer['result'] ) {
					foreach ( $vote_reviews_employer['result'] as $vote_review_employer ) {
						$review_e     = IWJ_Reviews::get_review( $vote_review_employer->ID );
						$user_list    = IWJ_User::get_user( $vote_review_employer->user_id );
						$review_reply = IWJ_Reviews::check_reply_review( $review_e->get_id() );
						?>
						<tr id="review-<?php esc_attr_e( $review_e->get_id(), 'iwjob' ); ?>" class="review-item">
							<td>
								<a href="<?php echo esc_url( $user_list->permalink() ); ?>">
									<h3 class="author-name"><?php esc_html_e( $review_e->get_user_name(), 'iwjob' ); ?></h3>
								</a>
							</td>
							<td>
								<a href="<?php echo esc_url( $review_e->review_link() ); ?>"><?php esc_html_e( $review_e->get_title(), 'iwjob' ); ?></a>
							</td>
							<td><?php esc_html_e( $review_e->get_content(), 'iwjob' ); ?></td>
							<td class="text-center"><?php esc_html_e( $review_e->get_rate_star(), 'iwjob' ); ?></td>
							<td class="iwj-status text-center">
								<?php if ( $review_reply ) { ?>
									<span class="approved" data-toggle="tooltip" data-original-title="Replied">
									<i class="ion-checkmark-circled"></i>
								</span>
								<?php } else { ?>
									<span class="pending" data-toggle="tooltip" data-original-title="UnReplied">
									<i class="ion-checkmark-circled"></i>
								</span>
								<?php } ?>
							</td>
							<td class="text-center">
								<div class="iwj-menu-action-wrap">
									<a href="#" tabindex="0" class="iwj-toggle-action collapsed" type="button" data-toggle="collapse" data-trigger="focus" data-target="#nav-collapse<?php esc_attr_e( $review_e->get_id(), 'iwjob' ); ?>"></a>
									<div id="nav-collapse<?php esc_attr_e( $review_e->get_id(), 'iwjob' ) ?>" class="collapse iwj-menu-action" data-id="nav-collapse<?php esc_attr_e( $review_e->get_id(), 'iwjob' ) ?>">
										<div class="iwj-menu-action-inner">
											<?php if ( ! $review_reply ) { ?>
												<div>
													<a href="#" class="iwj-reply-review" data-id="<?php esc_attr_e( $review_e->get_id(), 'iwjob' ) ?>" data-item_id="<?php esc_attr_e( $review_e->get_item_id() ); ?>"><?php echo __( 'Reply', 'iwjob' ); ?></a>
												</div>
											<?php } else { ?>
												<div>
													<a href="#" class="iwj-edit-reply-review" data-id="<?php echo esc_attr( $review_reply->ID ); ?>" data-review_id="<?php echo esc_attr( $review_reply->review_id ); ?>" data-user_id="<?php echo esc_attr( $review_reply->user_id ); ?>" data-message="<?php echo esc_attr( $review_reply->reply_content ); ?>"><?php echo __( 'Edit Reply', 'iwjob' ); ?></a>
												</div>
												<div>
													<a href="#" class="iwj-delete-reply" data-id="<?php echo esc_attr( $review_reply->ID ); ?>" data-review_id="<?php echo esc_attr( $review_reply->review_id ); ?>" data-message="<?php printf( __( 'Are you sure to delete <b> %s </b>"?', 'iwjob' ), $review_reply->reply_content ); ?>"><?php echo __( 'Delete Reply', 'iwjob' ); ?></a>
												</div>
											<?php } ?>
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
	<div class="modal fade" id="iwj-confirm-reply-review" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><?php echo __( 'Reply content', 'iwjob' ); ?></h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<textarea name="iwj_employer_review_content" class="iwjmb-textarea" cols="30" rows="3" id="iwj_employer_review_content" placeholder="<?php echo __( 'Enter your reply message', 'iwjob' ); ?>"></textarea>
				</div>
				<div class="modal-footer">
					<div class="iwj-respon-msg text-left"></div>
					<div class="iwj-button-loader">
						<button type="button" class="btn btn-primary iwj-agree-reply-review" data-id=""><?php echo __( 'Reply', 'iwjob' ); ?></button>
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'Close', 'iwjob' ); ?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="modal fade" id="iwj-confirm-delete-reply" role="dialog">
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
						<button type="button" class="btn btn-primary iwj-agree-delete-reply"><?php echo __( 'Continue', 'iwjob' ); ?></button>
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'Close', 'iwjob' ); ?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
	<?php if ( $vote_reviews_employer && $vote_reviews_employer['total_page'] > 1 ) { ?>
		<div class="iwj-pagination">
			<?php
			echo paginate_links( array(
				'base'      => add_query_arg( 'cpage', '%#%' ),
				'format'    => '',
				'prev_text' => __( '&laquo;' ),
				'next_text' => __( '&raquo;' ),
				'total'     => $vote_reviews_employer['total_page'],
				'current'   => $vote_reviews_employer['current_page']
			) );
			?>
		</div>
		<div class="clearfix"></div>
	<?php } ?>
</div>
