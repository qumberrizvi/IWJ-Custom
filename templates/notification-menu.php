<?php
if ( is_user_logged_in() ) {
	$user = IWJ_User::get_user( get_current_user_id() );

	if ( ! $user->is_active_profile() ) {
		if (!current_user_can('administrator')){
			$count = 2;
		}else{
			$count = 1;
		}

	} else {
		if ($user->is_candidate() || $user->is_employer()){
			$count = 1;
		}else{
			$count = 0;
		}
	}

	$user_id = $user->get_id();
} else {
	$count = 2;
	$user_id = 0;
}

$edit_profile_url          = iwj_get_page_permalink( 'dashboard' ) . '?iwj_tab=profile';
$job_suggest               = get_permalink( iwj_option( 'suggest_job_page_id' ) );
$register_page             = get_permalink( iwj_option( 'register_page_id' ) );
$candidate_suggestion_page = get_permalink( iwj_option( 'candidate_suggestion_page_id' ) );
?>
<a href="#" class="iwj_link_notice notice_active <?php echo ($count < 1 || isset( $_COOKIE['iwj_notification_'.$user_id] )) ? 'off-notification' : ''; ?>" data-user_id="<?php echo $user_id; ?>">
	<i class="ion-android-globe"></i>
	<span id="notification-count" class="badge <?php echo ($count < 1 || isset( $_COOKIE['iwj_notification_'.$user_id] )) ? 'hidden' : ''; ?>"><?php echo esc_html( $count ); ?></span>
</a>
<div class="iwj-notification-menu" data-user_id="<?php echo is_user_logged_in() ? esc_attr( get_current_user_id() ) : ''; ?>">
	<?php
	if ( ! is_user_logged_in() || ( is_user_logged_in() && ( ! $user->is_active_profile() || $user->is_candidate() || $user->is_employer() ) ) ) { ?>
		<ul>
			<?php
			if ( is_user_logged_in() ) {
				if ( ! $user->is_active_profile() && !current_user_can('administrator') ) { ?>
					<li>
						<a href="<?php echo esc_url( $edit_profile_url ); ?>" class="iwj_notification_link" data-notice_item="iwj_notify_update_profile">
							<i class="ion-gear-a"></i>
							<span><?php echo __( '<b class="highlight">Update your profile</b> to reach dream jobs easier.', 'iwjob' ); ?></span>
						</a>
					</li>
				<?php }
			} else { ?>
				<li>
					<a href="<?php echo esc_url( $register_page ); ?>" data-notice_item="iwj_notice_register">
						<i class="ion-gear-a"></i>
						<span><?php echo __( '<b class="highlight">Register now</b> to reach dream jobs easier.', 'iwjob' ); ?></span>
					</a>
				</li>
				<?php
			} ?>
			<?php
			if ( (! is_user_logged_in() || ( is_user_logged_in() && $user->is_candidate() ) ) && $job_suggest) { ?>
				<li>
					<a href="<?php echo esc_url( $job_suggest ); ?>" class="iwj_notification_link" data-notice_item="iwj_notify_suggest_job">
						<i class="ion-android-list"></i>
						<span><?php echo __( '<b class="highlight">Job suggestion</b> you might be interested based on your profile.', 'iwjob' ); ?></span>
					</a>
				</li>
			<?php } ?>
			<?php
			if ( is_user_logged_in() && $user->is_employer() && $candidate_suggestion_page) { ?>
				<li>
					<a href="<?php echo esc_url( $candidate_suggestion_page ); ?>" class="iwj_notification_link">
						<i class="ion-android-list"></i>
						<span><?php echo __( '<b class="highlight">Teacher suggestion</b> you might be interested based on your profile.', 'iwjob' ); ?></span>
					</a>
				</li>
				<?php
			}
			?>
		</ul>
	<?php } else {
		?>
		<ul>
			<li>
				<div class="iwj-notification-empty"><?php esc_html_e( 'You do not have any notification.', 'iwjob' ); ?></div>
			</li>
		</ul>
		<?php
	} ?>
</div>