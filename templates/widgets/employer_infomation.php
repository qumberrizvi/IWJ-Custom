<?php
$post     = get_post();
$employer = IWJ_Employer::get_employer( $post );
$author   = $employer->get_author();
if ( is_single() && $post && $post->post_type == 'iwj_employer' ) {
	$show_employer_public_profile = iwj_option( 'show_employer_public_profile', '' );
	if ( ! $show_employer_public_profile || ( $show_employer_public_profile && is_user_logged_in() ) ) {
		echo $args['before_widget'];
        if ( isset( $instance['style'] ) ) {
            $style = ( ! empty( $instance['style'] ) ) ? $instance['style'] : 'style1';
        }

		if ( isset( $instance['title'] ) ) {
			$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
			$title = apply_filters( 'widget_title', $title, $instance, $widget_id );

			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
		}
		?>

		<div class="iwj-employer-widget-wrap">
            <?php if ($style == 'style1') {?>
			<div class="iwj-widget-information iwj-single-widget <?php echo esc_attr($style); ?>">
				<p class="employer-desc"><?php echo $employer->get_short_description(); ?></p>
				<ul>
					<?php if ( $viewed = $employer->get_views() ) { ?>
						<li>
							<i class="ion-ios-eye"></i>
							<span><?php echo sprintf( __( 'Viewed : %s', 'iwjob' ), $viewed ); ?></span>
						</li>
					<?php } ?>
					<?php if ( $posted_jobs = $author->count_jobs( false, true ) ) { ?>
						<li>
							<i class="ion-android-checkbox-outline"></i>
							<span><?php echo sprintf( __( 'Posted Classes : %s', 'iwjob' ), $posted_jobs ); ?></span>
						</li>
					<?php } ?>
					<?php if ( $location_links = $employer->get_locations_links() ) { ?>
						<li>
							<i class="ion-android-pin"></i>
							<span><?php echo sprintf( __( 'Locations : %s', 'iwjob' ), $location_links ); ?></span>
						</li>
					<?php } ?>
					<?php if ( $categories_links = $employer->get_categories_links() ) { ?>
						<li>
							<i class="ion-android-folder-open"></i>
							<span><?php echo sprintf( __( 'Subjects : %s', 'iwjob' ), $categories_links ); ?></span>
						</li>
					<?php } ?>
					<?php if ( $size = $employer->get_size() ) { ?>
						<li>
							<i class="ion-android-contacts"></i>
							<span><?php echo sprintf( __( 'Your Class : %s', 'iwjob' ), $size ); ?></span>
						</li>
					<?php } ?>
					<?php if ( $followers = $author->count_followers() ) { ?>
						<li>
							<i class="ion-android-star"></i>
							<span><?php echo sprintf( __( 'Followers : %s', 'iwjob' ), $followers ); ?></span>
						</li>
					<?php } ?>
				</ul>
			</div>
            <?php } else {
            $user_employer = IWJ_User::get_user($employer->get_author_id());
            ?>
            <div class="iwj-widget-information iwj-single-widget <?php echo esc_attr($style); ?>">
                <ul>
                    <?php if ( $locations = $employer->get_locations() ) {
                        $address_links = $employer->get_address();
                        ?>
                        <li>
                            <i class="icon-injob-map-pin"></i>
                            <div class="content">
                                <label><?php echo __( 'Address', 'iwjob' ); ?></label>
                                <span class="map"><?php echo sprintf( __( '%s', '%s', count( $locations ), 'iwjob' ), $address_links ); ?></span>
                            </div>
                        </li>
                    <?php } ?>

                    <?php if ( $categories = $employer->get_categories() ) {
                        $categories_links = $employer->get_categories_links();
                        ?>
                        <li>
                            <i class="icon-injob-layers"></i>
                            <div class="content">
                                <label><?php echo __( 'Subjects', 'iwjob' ); ?></label>
                                <span><?php echo sprintf( __( '%s', '%s', count( $categories ), 'iwjob' ), $categories_links ); ?></span>
                            </div>
                        </li>
                    <?php } ?>

                    <?php if ( $phone = $employer->get_phone() ) { ?>
                        <li>
                            <i class="icon-injob-phone-call"></i>
                            <div class="content">
                                <span><?php echo sprintf( __( 'Hotline : %s', 'iwjob' ), $phone ); ?></span>
                                <?php if ( $website = $employer->get_website() ){
                                    echo '<a class="'.esc_html__('website','injob').'" href="'.$website.'">'.esc_html__('Visit our website','iwjob').'</a>';
                                }?>
                            </div>
                        </li>
                    <?php } ?>

                    <?php if ( $size = $employer->get_size() ) { ?>
                        <li>
                            <i class="icon-injob-home"></i>
                            <div class="content">
                                <label><?php echo __( 'Your Class', 'iwjob' ); ?></label>
                                <span><?php echo $size; ?></span>
                            </div>
                        </li>
                    <?php } ?>

                    <?php if ( $followers = $author->count_followers() ) { ?>
                        <li>
                            <i class="icon-injob-bell"></i>
                            <div class="content">
                                <label><?php echo __( 'Followers', 'iwjob' ); ?></label>
                                <span><?php echo $followers; ?></span>
                            </div>
                        </li>
                    <?php } ?>

                    <?php if ( $viewed = $employer->get_views() ) { ?>
                        <li>
                            <i class="icon-injob-eye"></i>
                            <div class="content">
                                <label><?php echo __( 'Viewed', 'iwjob' ); ?></label>
                                <span><?php echo $viewed; ?></span>
                            </div>
                        </li>
                    <?php } ?>

                </ul>
            </div>
            <?php }?>
		</div>

		<?php
		echo $args['after_widget'];
	}
}