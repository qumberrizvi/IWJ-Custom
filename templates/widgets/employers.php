<?php

//wp_enqueue_style('owl-carousel');
//wp_enqueue_style('owl-theme');
//wp_enqueue_style('owl-transitions');
//wp_enqueue_script('owl-carousel');

echo $args['before_widget'];

if ( isset( $instance['title'] ) ) {
	$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
	$title = apply_filters( 'widget_title', $title, $instance, $widget_id );

	if ( $title ) {
		echo $args['before_title'] . $title . $args['after_title'];
	}
}
$show_employer_public_profile = iwj_option( 'show_employer_public_profile', '' );
$login_page_id                = get_permalink( iwj_option( 'login_page_id' ) );
?>
	<div class="iwj-widget-employers">
		<!--        <div class="owl-carousel">-->
		<div class="row-items">
			<?php
			$i              = 0;
			foreach ( $employers as $employer ) :
				$employer = IWJ_Employer::get_employer( $employer );
				$author     = $employer->get_author();
				$total_jobs = isset( $employer->post->total_jobs ) ? $employer->post->total_jobs : $author->count_jobs( false, true );
				$image      = get_avatar( $author->get_id(), 100 );
				if ( ! $image ) {
					$img_src = inwave_get_placeholder_image();
					$img_src = inwave_resize( $img_src, 100, 100, true );
					$image   = '<img src="' . esc_url( $img_src ) . '" alt="">';
				}
				if ( $i > 0 && count( $employers ) > $i && $i % 3 == 0 ) {
					echo '</div>
                                        <div class="row-items">';
				}
				?>
				<div class="col-item">
					<?php
					if ( ! $show_employer_public_profile || ( $show_employer_public_profile && is_user_logged_in() ) ) {
						$link_profile = $author->permalink();
					} else {
						$link_profile = add_query_arg( 'redirect_to', $author->permalink(), $login_page_id );
					}
					?>
					<div class="item">
						<?php if ( $image ) { ?>
							<div class="image"><a href="<?php echo esc_url($link_profile); ?>"><?php echo $image; ?></a>
							</div>
						<?php } ?>
						<?php if ( $author->get_display_name() ) { ?>
							<h3 class="employer-title">
								<a href="<?php echo esc_url($link_profile); ?>"><?php echo $author->get_display_name(); ?></a>
							</h3>
						<?php } ?>
						<?php if ( $employer->get_locations_links() ) { ?>
							<div class="employer-locations"><?php echo $employer->get_locations_links(); ?></div>
						<?php } ?>
						<a class="total-job" href="<?php echo esc_url($link_profile); ?>"><span class="number"><?php echo $total_jobs; ?></span> <?php echo _n( 'job', 'jobs', $total_jobs, 'iwjob' ); ?>
						</a>
					</div>
				</div>
				<?php
				$i ++;
			endforeach; ?>
		</div>
		<!--        </div>-->
	</div>
<?php

echo $args['after_widget'];