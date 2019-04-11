<?php

class IWJ_Admin_Reviews {

	static function current_action() {
		if ( isset( $_REQUEST['action'] ) && - 1 != $_REQUEST['action'] ) {
			return $_REQUEST['action'];
		}

		if ( isset( $_REQUEST['action2'] ) && - 1 != $_REQUEST['action2'] ) {
			return $_REQUEST['action2'];
		}

		return false;
	}

	static function load_form_admin() {
		$action         = self::current_action();
		$current_screen = get_current_screen();
		if ( $action == 'delete' ) {
			$review_ids = empty( $_POST['review_ID'] )
				? (array) $_REQUEST['review']
				: (array) $_POST['review_ID'];
			if ( $review_ids ) {
				IWJ_Reviews::delete_review( $review_ids );
			}
			update_option( IWJ_PREFIX . 'review_messagess', 'deleted' );
			wp_redirect( htmlspecialchars_decode( menu_page_url( 'iwj-reviews', false ) ) );
			exit;
		} elseif ( $action == 'pending' ) {
			$review_ids = empty( $_POST['review_ID'] )
				? (array) $_REQUEST['review']
				: (array) $_POST['review_ID'];
			if ( $review_ids ) {
				IWJ_Reviews::update_review_status( $review_ids, $action );
			}
			update_option( IWJ_PREFIX . 'review_messagess', 'pending' );
			wp_redirect( htmlspecialchars_decode( menu_page_url( 'iwj-reviews', false ) ) );
			exit;
		} elseif ( $action == 'approved' ) {
			$review_ids = empty( $_POST['review_ID'] )
				? (array) $_REQUEST['review']
				: (array) $_POST['review_ID'];
			if ( $review_ids ) {
				IWJ_Reviews::update_review_status( $review_ids, 'approved' );
				foreach ( $review_ids as $review_id ) {
					$obj_rev = IWJ_Reviews::get_review( $review_id );
					IWJ_Email::send_email( 'approved_review', $obj_rev );
					IWJ_Email::send_email( 'candidate_approved_review', $obj_rev );
				}
			}
			update_option( IWJ_PREFIX . 'review_messagess', 'approved' );
			wp_redirect( htmlspecialchars_decode( menu_page_url( 'iwj-reviews', false ) ) );
			exit;
		}

		add_filter( 'manage_' . $current_screen->id . '_columns',
			array( 'IWJ_Reviews_Form_List_Table', 'define_columns' ) );

		if ( isset( $_POST['_iwjeditreview_nonce'] ) && isset( $_POST['iwj_ad_update_review'] ) ) {
			$rev_id      = sanitize_text_field( $_POST['iwj_admin_rv_id'] );
			$user_id     = sanitize_text_field( $_POST['iwj_admin_rv_user'] );
			$employer_id = sanitize_text_field( $_POST['iwj_admin_rv_employer'] );
			$title       = sanitize_text_field( $_POST['iwj_admin_rev_title'] );
			$content     = sanitize_textarea_field( $_POST['iwj_admin_rev_content'] );
			$status      = sanitize_text_field( $_POST['iwj_admin_rev_status'] );

			$review_options = iwj_option( 'review_options', '' );
			$vote_value     = array();
			$trim_option    = trim( $review_options );
			if ( ! empty( $trim_option ) ) {
				$arr_reviews = explode( "\n", $review_options );
				foreach ( $arr_reviews as $key_val => $review_option ) {
					$rev_item_name = strtolower( str_replace( ' ', '_', trim( $review_option ) ) );
					if ( stripslashes( sanitize_text_field( $_POST[ 'iwj_rate_num_' . $key_val ] ) ) ) {
						$vote_value[ $rev_item_name ] = stripslashes( sanitize_text_field( $_POST[ 'iwj_rate_num_' . $key_val ] ) );
					} else {
						$vote_value[ $rev_item_name ] = 0;
					}
				}

				for ( $j = 0; $j < count( $arr_reviews ); $j ++ ) {
					$iwj_rate_num = sanitize_text_field( $_POST[ 'iwj_rate_num_' . $j ] );
					if ( ! $iwj_rate_num ) {
						update_option( IWJ_PREFIX . 'edit_review_msg', 'miss_criteria' );

						return false;
					}
				}

				$rate_number = count( $arr_reviews ) ? array_sum( $vote_value ) / count( $arr_reviews ) : 0;
			} else {
				$rate_number = stripslashes( sanitize_text_field( $_POST['iwj_simple_rate'] ) ) ? stripslashes( sanitize_text_field( $_POST['iwj_simple_rate'] ) ) : 0;
			}

			if ( $rate_number == 0 ) {
				update_option( IWJ_PREFIX . 'edit_review_msg', 'miss_criteria' );

				return false;
			}

			if ( ! $rev_id ) {
				update_option( IWJ_PREFIX . 'edit_review_msg', 'miss_id' );

				return false;
			}
			if ( ! $user_id ) {
				update_option( IWJ_PREFIX . 'edit_review_msg', 'miss_user_id' );

				return false;
			}
			if ( ! $employer_id ) {
				update_option( IWJ_PREFIX . 'edit_review_msg', 'miss_employer_id' );

				return false;
			}
			if ( ! $title ) {
				update_option( IWJ_PREFIX . 'edit_review_msg', 'miss_title' );

				return false;
			}
			if ( ! $content ) {
				update_option( IWJ_PREFIX . 'edit_review_msg', 'miss_content' );

				return false;
			}

			$args = array(
				'ID'        => $rev_id,
				'user_id'   => $user_id,
				'item_id'   => $employer_id,
				'rating'    => $rate_number,
				'title'     => $title,
				'content'   => $content,
				'status'    => $status,
				'criterias' => serialize( $vote_value ),
				'read'      => 1
			);
			if ( isset( $_POST[ 'iwj_rev_reject_reason_' . $rev_id ] ) ) {
				$reject_reason = trim( $_POST[ 'iwj_rev_reject_reason_' . $rev_id ] );
				if ( ! empty( $reject_reason ) ) {
					update_option( 'iwj_rev_reject_reason_' . $rev_id, $_POST[ 'iwj_rev_reject_reason_' . $rev_id ] );
				}
			}
			IWJ_Reviews::update_review( $args );
			update_option( IWJ_PREFIX . 'review_messagess', 'updated' );
			wp_redirect( htmlspecialchars_decode( menu_page_url( 'iwj-reviews', false ) ) );
			exit;
		}
	}

	static function management_page() {
		wp_enqueue_script( 'iwj-rating-custom' );
		$list_table = new IWJ_Reviews_Form_List_Table();
		$list_table->prepare_items();
		$message = get_option( IWJ_PREFIX . 'review_messagess' ); ?>

		<div class="wrap iwj-review-setting-page">
			<?php
			if ( isset( $_REQUEST['action'] ) && ( $_REQUEST['action'] == 'edit' ) && isset( $_REQUEST['review'] ) ) {
                IWJ_Reviews::set_read($_REQUEST['review']);
				$reviews = IWJ_Reviews::get_review( $_REQUEST['review'] );
				?>
				<h1><?php echo __( 'Edit review of ' . $reviews->get_user_name(), 'iwjob' ); ?></h1>
				<?php
				$miss_review = get_option( IWJ_PREFIX . 'edit_review_msg' );
				if ( $miss_review ) {
					?>
					<div class="notice notice-error is-dismissible">
						<p><?php
							if ( $miss_review == 'miss_criteria' ) {
								echo __( 'Please select rating.', 'iwjob' );
							}
							if ( $miss_review == 'miss_id' ) {
								echo __( 'Review is not exist.', 'iwjob' );
							}
							if ( $miss_review == 'miss_user_id' ) {
								echo __( 'User review does not exist.', 'iwjob' );
							}
							if ( $miss_review == 'miss_employer_id' ) {
								echo __( 'The employer does not exist.', 'iwjob' );
							}
							if ( $miss_review == 'miss_title' ) {
								echo __( 'Please enter the title.', 'iwjob' );
							}
							if ( $miss_review == 'miss_content' ) {
								echo __( 'Please enter the content.', 'iwjob' );
							}
							?>
						</p>
					</div>
					<?php
					delete_option( IWJ_PREFIX . 'edit_review_msg' );
				} ?>
				<form method="POST" action="">
					<?php wp_nonce_field( 'iwjeditreview_action_nonce', '_iwjeditreview_nonce' ); ?>
					<table class="form-table iwj-edit-review-form">
						<tr>
							<th width="200px"><?php esc_html_e( 'Overal Rating', 'iwjob' ); ?></th>
							<td>
								<?php
								$vote_for       = $reviews->get_criterias();
								$review_options = iwj_option( 'review_options', '' );
								$trim_option    = trim( $review_options );
								if ( ! empty( $trim_option ) ) {
									$arr_reviews = explode( "\n", $review_options );
									if ( count( $arr_reviews ) ) {
										foreach ( $arr_reviews as $key_cri => $arr_review ) {
											$rev_item_name = strtolower( str_replace( ' ', '_', trim( $arr_review ) ) );
											?>
											<div class="iwj-line-tc-vote">
												<span class="line-tc-title"><?php echo esc_html__( $arr_review, 'iwjob' ); ?></span>
												<span class="line-tc-star">
													<input type="hidden" class="iwj_num_rate rating" data-size="xs" data-step="1" name="iwj_rate_num_<?php echo esc_attr( $key_cri ); ?>" value="<?php echo array_key_exists( $rev_item_name, $vote_for ) ? $vote_for[ $rev_item_name ] : ''; ?>">
												</span>
											</div>
											<?php
										}
									}
								} else { ?>
									<input type="hidden" class="rating iwj_simple_rate" data-size="xs" data-step="1" name="iwj_simple_rate" value="<?php echo $reviews->get_rate_star(); ?>">
									<?php
								} ?>
							</td>
						</tr>
						<tr>
							<th width="200px"><?php esc_html_e( 'Title', 'iwjob' ); ?></th>
							<td>
								<input type="text" name="iwj_admin_rev_title" value="<?php echo esc_attr( $reviews->get_title() ) ?>" style="width: 300px;" required>
							</td>
						</tr>
						<tr>
							<th width="200px"><?php esc_html_e( 'Content', 'iwjob' ); ?></th>
							<td>
								<textarea name="iwj_admin_rev_content" style="width: 300px;" rows="4"><?php echo esc_html( $reviews->get_content() ) ?></textarea>
							</td>
						</tr>
						<tr>
							<th width="200px"><?php esc_html_e( 'Status', 'iwjob' ); ?></th>
							<td>
								<select name="iwj_admin_rev_status">
									<?php
									if ( $reviews->get_status() !== "approved" ) { ?>
										<option value="pending" <?php echo $reviews->get_status() == "pending" ? 'selected' : ''; ?>><?php esc_html_e( 'Pending', 'iwjob' ); ?></option>
										<option value="reject" <?php echo $reviews->get_status() == "reject" ? 'selected' : ''; ?>><?php esc_html_e( 'Reject', 'iwjob' ); ?></option>
										<?php
									} ?>
									<option value="approved" <?php echo $reviews->get_status() == "approved" ? 'selected' : ''; ?>><?php esc_html_e( 'Approve', 'iwjob' ); ?></option>
								</select>
							</td>
						</tr>
						<tr class="iwj_reason_reject_rev <?php echo $reviews->get_status() !== 'reject' ? 'hidden' : ''; ?>">
							<th width="200px"><?php esc_html_e( 'Reason', 'iwjob' ); ?></th>
							<td>
								<textarea name="iwj_rev_reject_reason_<?php echo esc_attr( $reviews->get_id() ); ?>" style="width: 300px;" rows="4"></textarea>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="hidden" name="iwj_admin_rv_id" value="<?php echo esc_attr( $_REQUEST['review'] ); ?>">
								<input type="hidden" name="iwj_admin_rv_user" value="<?php echo esc_attr( $reviews->get_user_id() ); ?>">
								<input type="hidden" name="iwj_admin_rv_employer" value="<?php echo esc_attr( $reviews->get_item_id() ); ?>">
								<input type="submit" class="button button-primary" name="iwj_ad_update_review" value="<?php esc_html_e( 'Update', 'iwjob' ); ?>">
							</td>
						</tr>
					</table>
				</form>
				<?php
			} else {
				?>
				<h1><?php
					echo esc_html( __( 'Reviews', 'iwjob' ) );
					if ( ! empty( $_REQUEST['s'] ) ) {
						echo sprintf( '<span class="subtitle">'
						              . __( 'Search results for &#8220;%s&#8221;', 'iwjob' )
						              . '</span>', esc_html( $_REQUEST['s'] ) );
					}
					?>
				</h1>

				<?php if ( $message ) { ?>
					<div class="notice notice-success is-dismissible">
						<p><?php
							if ( $message == 'deleted' ) {
								echo __( 'Deleted Review Successfully.', 'iwjob' );
							}
							if ( $message == 'pending' ) {
								echo __( 'Pending Review Successfully.', 'iwjob' );
							}
							if ( $message == 'approved' ) {
								echo __( 'Approved Review Successfully.', 'iwjob' );
							}
							if ( $message == 'updated' ) {
								echo __( 'Updated Review Successfully.', 'iwjob' );
							}
							?>
						</p>
					</div>
					<?php
					delete_option( IWJ_PREFIX . 'review_messagess' );
				} ?>

				<form method="get" action="">
					<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
					<input type="hidden" name="post_type" value="<?php echo esc_attr( $_REQUEST['post_type'] ); ?>" />
					<?php $list_table->search_box( __( 'Search Name', 'iwjob' ), 'iwjob' ); ?>

					<?php $list_table->display(); ?>
				</form>
				<?php
			}
			?>


		</div>
		<?php
	}

}