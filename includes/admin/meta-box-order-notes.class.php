<?php
/**
 * Order Notes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * IWJ_Meta_Box_Order_Notes Class.
 */
class IWJ_Meta_Box_Order_Notes {

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {
		global $post;

		$args = array(
			'post_id'   => $post->ID,
			'orderby'   => 'comment_ID',
			'order'     => 'DESC',
			'approve'   => 'approve',
			'type'      => 'order_note',
		);

		//remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

		$notes = get_comments( $args );

		//add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

		echo '<ul class="iwj-order-notes">';

		if ( $notes ) {

			foreach ( $notes as $note ) {

				$note_classes   = array( 'iwj-note' );
				$note_classes[] = get_comment_meta( $note->comment_ID, 'is_customer_note', true ) ? 'customer-note' : '';
				$note_classes[] = ( __( 'iwjob', 'iwjob' ) === $note->comment_author ) ? 'system-note' : '';
				$note_classes   = apply_filters( 'iwj_order_note_class', array_filter( $note_classes ), $note );
				?>
				<li rel="<?php echo absint( $note->comment_ID ); ?>" class="<?php echo esc_attr( implode( ' ', $note_classes ) ); ?>">
					<div class="iwj-note-content">
						<?php echo wpautop( wptexturize( wp_kses_post( $note->comment_content ) ) ); ?>
					</div>
					<p class="meta">
						<abbr class="exact-date" title="<?php echo $note->comment_date; ?>"><?php printf( __( 'added on %1$s at %2$s', 'iwjob' ), date_i18n( get_option('date_format'), strtotime( $note->comment_date ) ), date_i18n( get_option('time_format'), strtotime( $note->comment_date ) ) ); ?></abbr>
						<?php
						if ( __( 'iwjob', 'iwjob' ) !== $note->comment_author ) :
							/* translators: %s: note author */
							printf( ' ' . __( 'by %s', 'iwjob' ), $note->comment_author );
						endif;
						?>
						<a href="#" class="iwj-delete-note" role="button"><?php _e( 'Delete note', 'iwjob' ); ?></a>
					</p>
				</li>
				<?php
			}
		} else {
			echo '<li>' . __( 'There are no notes yet.', 'iwjob' ) . '</li>';
		}

		echo '</ul>';
		?>
		<div class="iwj-add-note">
			<p>
				<label for="iwj-add-order-note"><?php _e( 'Add note', 'iwjob' ); ?> <?php //echo _help_tip( __( 'Add a note for your reference, or add a customer note (the user will be notified).', 'iwjob' ) ); ?></label>
				<textarea type="text" name="order_note" id="iwj-add-order-note" class="input-text" cols="20" rows="5"></textarea>
			</p>
			<p>
				<label for="iwj-order-note-type" class="screen-reader-text"><?php _e( 'Note type', 'iwjob' ); ?></label>
				<select name="order_note_type" id="iwj-order-note-type">
					<option value=""><?php _e( 'Private note', 'iwjob' ); ?></option>
					<option value="customer"><?php _e( 'Note to customer', 'iwjob' ); ?></option>
				</select>
                <input type="hidden" id="iwj-order-note-id" value="<?php echo $post->ID; ?>">
				<button type="button" class="iwj-add-note button"><?php _e( 'Add', 'iwjob' ); ?></button>
			</p>
		</div>
		<?php
	}
}
