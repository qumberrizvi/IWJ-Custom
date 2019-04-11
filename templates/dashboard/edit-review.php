<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
wp_enqueue_script( 'iwj-rating-custom' );
$review_id = isset( $_GET['review-id'] ) ? $_GET['review-id'] : '';
$review    = IWJ_Reviews::get_review( $review_id );
$user      = IWJ_User::get_user(); ?>
<div class="iwj-user-edit-review">
	<div class="iwj-main">
		<?php
		if ( $review && $user ) {
			if ( $review->get_user_id() == $user->get_id() ) { ?>
				<form action="" method="POST" class="iwj-form-2 iwj-user-update-review iwj-block">
					<div class="basic iwj-block-inner">
						<div class="row">
							<div class="col-md-12">
								<?php
								$criterias      = $review->get_criterias();
								$review_options = iwj_option( 'review_options', '' );
								$trim_option    = trim( $review_options );
								?>
								<div class="re-post-form-submit iwjmb-field" data-number_criteria="<?php echo ! empty( $trim_option ) ? 'group_vote' : 'simple_vote'; ?>">
									<?php
									if ( ! empty( $trim_option ) ) {
										$arr_reviews = explode( "\n", $review_options ); ?>
										<span class="re-text iwjmb-label"><label for=""><?php esc_html_e( 'Ovaral Rating *', 'iwjob' ); ?></label></span>
										<span class="iwj-count-stars">
											<div class="iwj-votes-icon">
												<?php echo $review->get_number_stars( $review->get_rate_star() ); ?>
											</div>
											<div class="iwj-box-each-vote iwj-review-voting" data-total_views="<?php echo esc_attr( count( $arr_reviews ) ); ?>">
												<?php
												if ( count( $arr_reviews ) ) {
													foreach ( $arr_reviews as $key_item => $rev_item ) {
														$rev_item_name = strtolower( str_replace( ' ', '_', trim( $rev_item ) ) ); ?>
														<div class="iwj-line-tc-vote">
															<span class="line-tc-title"><?php echo esc_html__( $rev_item, 'iwjob' ); ?></span>
															<span class="line-tc-star">
																<input type="hidden" class="iwj_num_rate rating " data-size="xs" data-step="1" name="iwj_rate_num_<?php echo esc_attr( $key_item ); ?>" data-criteria_vote="<?php echo esc_attr( $rev_item_name ); ?>" value="<?php echo array_key_exists( $rev_item_name, $criterias ) ? $criterias[ $rev_item_name ] : ''; ?>">
															</span>
														</div>
														<?php
													}
												} ?>
											</div>
										</span>
										<?php
									} else { ?>
										<span class="re-text iwjmb-label"><label for=""><?php esc_html_e( 'Rating *', 'iwjob' ); ?></label></span>
										<span class="iwj-count-stars">
											<input type="hidden" class="rating iwj_simple_rate" data-size="xs" data-step="1" name="iwj_simple_rate" value="<?php echo $review->get_rate_star(); ?>">
										</span>
									<?php } ?>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<?php iwj_field_text( 'iwj_review_title', __( 'Title *', 'iwjob' ), true, $review_id, $review->get_title( true ), '', '', __( 'Enter Title', 'iwjob' ) ); ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<?php iwj_field_textarea( 'iwj_review_content', __( 'Content *', 'iwjob' ), true, $review_id, $review->get_content( true ), '', '', __( 'Enter Content', 'iwjob' ) ); ?>
							</div>
						</div>
					</div>
					<div class="iwj-respon-msg iwj-hide"></div>
					<div class="iwj-button-loader text-right">
						<button type="submit" class="iwj-btn iwj-btn-primary iwj-edit-review-btn"><?php echo __( 'Update Review', 'iwjob' ); ?></button>
					</div>
					<input type="hidden" name="review_id" value="<?php echo esc_attr( $review_id ); ?>">
				</form>
				<?php
			} else {
				echo __( 'You do not have permission to access here.', 'iwjob' );
			}
		} else {
			echo __( 'Review is not exist', 'iwjob' );
		}
		?>
	</div>
</div>
