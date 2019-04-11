<?php
extract( $args );

$job            = IWJ_Job::get_job( get_the_ID() );
$author         = $job->get_author();
$jobs_by_author = $job->get_jobs_by_author( array( 'posts_per_page' => $limit, 'orderby' => $orderby, 'order' => $order ) );
$user          = IWJ_User::get_user();
$show_location = iwj_option( 'show_location_job' );
$show_posted_date_job = iwj_option('show_posted_date_job');

if ( $jobs_by_author ) {

	$title = sprintf( __( 'Classes by %s', 'iwjob' ), $author->get_display_name() );
	echo wp_kses_post( $before_widget );
	?>
	<div class="iwj-job-by-employer">
		<h3 class="widget-title"><?php echo $title; ?></h3>
		<?php
		foreach ( $jobs_by_author as $key => $job_by_author ) {
			$type = $job_by_author->get_type(); ?>
			<div class="job-info">
				<h4 class="job-title">
					<a href="<?php echo $job_by_author->get_indeed_url() ? esc_url( $job_by_author->get_indeed_url() ) : esc_url( $job_by_author->permalink() ); ?>"><?php echo( $job_by_author->get_title() ); ?></a>
				</h4>
				<div class="job-address-time">
					<?php if ( ( $locations = $job_by_author->get_locations_links() ) && ( $show_location == '1' ) ) : ?>
						<div class="address">
							<i class="ion-android-pin"></i><?php echo $locations; ?>
						</div>
					<?php endif; ?>
					<?php if ($show_posted_date_job == '1') { ?>
						<div class="time-ago"><i class="fa fa-calendar theme-color"></i><?php printf( _x( '%s ago', '%s = human-readable time difference', 'iwjob' ), human_time_diff( strtotime( $job_by_author->get_created() ), current_time( 'timestamp' ) ) ); ?></div>
					<?php } ?>
				</div>
				<div class="job-type <?php echo $type ? $type->slug : ''; ?>">
					<?php if ( $type ) {
						$color = get_term_meta( $type->term_id, IWJ_PREFIX . 'color', true ); ?>
						<a class="type-name" href="<?php echo get_term_link( $type->term_id, 'iwj_type' ); ?>" <?php echo $color ? 'style="color: '.$color.'; border-color: '.$color.'; background-color: '.$color.'"' : ''; ?>><?php echo $type->name; ?></a>
					<?php } ?>
					<?php

					if ( ! is_user_logged_in() ) { ?>
						<button class="save-job" data-toggle="modal" data-target="#iwj-login-popup">
							<i class="fa fa-heart"></i></button>
					<?php } else if ( current_user_can( 'apply_job' ) ) { ?>
						<a href="#" class="iwj-save-job <?php echo $user->is_saved_job( $job_by_author->get_id() ) ? 'saved' : ''; ?>" data-id="<?php echo $job_by_author->get_id(); ?>" data-in-list="true"><i class="fa fa-heart"></i></a>
					<?php } ?>
				</div>
			</div>
			<?php
		} ?>
	</div>
	<?php
	echo wp_kses_post( $after_widget ); ?>
	<?php
	// End show widget

}