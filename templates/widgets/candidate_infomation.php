<?php
$post      = get_post();
$user      = IWJ_User::get_user();
$candidate = IWJ_Candidate::get_candidate( $post );

if ( is_single() && $post && $post->post_type == 'iwj_candidate' ) {
	$show_candidate_public_profile = iwj_option( 'show_candidate_public_profile', '' );
	if ( ! $show_candidate_public_profile || ( is_user_logged_in() && ( $show_candidate_public_profile == 1 || ( $show_candidate_public_profile == 2 && $user->is_employer() ) ) ) ) {
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
		<div class="iwj-candicate-widget-wrap">
            <?php if ($style == 'style1') { ?>
                <div class="iwj-widget-information iwj-single-widget <?php echo esc_attr($style); ?>">
                    <p class="candidate-desc"><?php echo $candidate->get_description(); ?></p>
                    <ul>
                        <?php if ( $viewed = $candidate->get_views() ) { ?>
                            <li>
                                <i class="ion-ios-eye"></i>
                                <span><?php echo sprintf( __( 'Viewed : %s', 'iwjob' ), $viewed ); ?></span>
                            </li>
                        <?php } ?>
                        <?php if ( $age = $candidate->get_age() ) { ?>
                            <li>
                                <i class="ion-android-calendar"></i>
                                <span><?php echo sprintf( __( 'Age : %s', 'iwjob' ), $age ); ?></span>
                            </li>
                        <?php } ?>
                        <?php if ( $gender = $candidate->get_gender() ) {
                            $gender_title = iwj_gender_titles( $gender );
                            ?>
                            <li>
                                <i class="ion-transgender"></i>
                                <span><?php echo sprintf( __( 'Gender : %s', 'iwjob' ), $gender_title ); ?></span>
                            </li>
                        <?php } ?>
                        <?php if ( $languages = $candidate->get_languages() ) {
                            $language_titles = iwj_get_language_titles( $languages );
                            ?>
                            <li>
                                <i class="ion-android-pin"></i>
                                <span><?php echo sprintf( _n( 'Language : %s', 'Languages : %s', count( $languages ), 'iwjob' ), implode( ', ', $language_titles ) ); ?></span>
                            </li>
                        <?php } ?>
                        <?php if ( $levels = $candidate->get_levels() ) {
                            $levels_links = $candidate->get_levels_links();
                            ?>
                            <li>
                                <i class="ion-levels"></i>
                                <span><?php echo sprintf( _n( 'Level : %s', 'Levels : %s', count( $levels ), 'iwjob' ), $levels_links ); ?></span>
                            </li>
                        <?php } ?>
                        <?php if ( $types = $candidate->get_types() ) {
                            $types_links = $candidate->get_types_links();
                            ?>
                            <li>
                                <i class="ion-levels"></i>
                                <span><?php echo sprintf( _n( 'Type : %s', 'Types : %s', count( $types ), 'iwjob' ), $types_links ); ?></span>
                            </li>
                        <?php } ?>
                        <?php if ( $categories = $candidate->get_categories() ) {
                            $categories_links = $candidate->get_categories_links();
                            ?>
                            <li>
                                <i class="ion-android-folder-open"></i>
                                <span><?php echo sprintf( _n( 'Category : %s', 'Subjects : %s', count( $categories ), 'iwjob' ), $categories_links ); ?></span>
                            </li>
                        <?php } ?>
                        <?php if ( $skills = $candidate->get_skills() ) {
                            $skills_links = $candidate->get_skills_links();
                            ?>
                            <li>
                                <i class="ion-android-bulb"></i>
                                <span><?php echo sprintf( _n( 'Skill : %s', 'Skills : %s', count( $skills ), 'iwjob' ), $skills_links ); ?></span>
                            </li>
                        <?php } ?>
                        <?php if ( $locations = $candidate->get_locations() ) {
                            $location_links = $candidate->get_locations_links();
                            ?>
                            <li>
                                <i class="ion-android-pin"></i>
                                <span><?php echo sprintf( __( 'Locations : %s', 'Location : %s', count( $locations ), 'iwjob' ), $location_links ); ?></span>
                            </li>
                        <?php } ?>

                    </ul>
                </div>
            <?php } else {
                $user_candidate = IWJ_User::get_user($candidate->get_author_id());
                ?>
                <div class="iwj-widget-information iwj-single-widget <?php echo esc_attr($style); ?>">
                    <ul>
                        <?php
                        if ( $user_candidate ) { ?>
                            <li>
                                <i class="icon-injob-edit"></i>
                                <div class="content">
                                    <label><?php echo __( 'Member Since', 'iwjob' ); ?></label>
                                    <span><?php echo date("j F, Y", strtotime($user_candidate->user->user_registered)); ?></span>
                                </div>
                            </li>
                        <?php } ?>
                        <?php if ( $user_candidate ) {
                            $last_login = get_user_meta($user_candidate->get_id(), '_last_login');
                            $diff = '';
                            if ($last_login) {
                                $to = time();
                                $diff = (int) abs( $to - $last_login[0] );
                            }
                            ?>
                            <li>
                                <i class="icon-injob-feather"></i>
                                <div class="content">
                                    <label><?php echo __( 'Latest Activities', 'iwjob' ); ?></label>
                                    <?php if ( $last_login ) { ?>
                                        <?php if ($diff && $diff <= MONTH_IN_SECONDS) { ?>
                                            <span><?php echo human_time_diff( $last_login[0] ) ; echo __( ' ago', 'iwjob' ); ?></span>
                                        <?php } else { ?>
                                            <span><?php echo date("j F, Y", $last_login[0]); ?></span>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <span><?php echo date("j F, Y", strtotime($user_candidate->user->user_registered)); ?></span>
                                    <?php } ?>
                                </div>
                            </li>
                        <?php } ?>
                        <?php if ( $locations = $candidate->get_locations() ) {
                            $location_links = $candidate->get_locations_links();
                            ?>
                            <li>
                                <i class="icon-injob-map-pin"></i>
                                <div class="content">
                                    <label><?php echo __( 'Location', 'iwjob' ); ?></label>
                                    <span><?php echo sprintf( __( '%s', '%s', count( $locations ), 'iwjob' ), $location_links ); ?></span>
                                </div>
                            </li>
                        <?php } ?>
                        <?php if ( $categories = $candidate->get_categories() ) {
                            $categories_links = $candidate->get_categories_links();
                            ?>
                            <li>
                                <i class="icon-injob-briefcase2"></i>
                                <div class="content">
                                    <label><?php echo __( 'Subjects', 'iwjob' ); ?></label>
                                    <span><?php echo sprintf( __( '%s', '%s', count( $categories ), 'iwjob' ), $categories_links ); ?></span>
                                </div>
                            </li>
                        <?php } ?>
                        <?php if ( $get_experience_text = $candidate->get_experience_text() ) { ?>
                            <li>
                                <i class="icon-injob-settings"></i>
                                <div class="content">
                                    <label><?php echo __( 'Experience', 'iwjob' ); ?></label>
                                    <span><?php echo esc_html($get_experience_text); ?></span>
                                </div>
                            </li>
                        <?php } ?>
                        <?php if ( $age = $candidate->get_age() ) { ?>
                            <li>
                                <i class="icon-injob-user"></i>
                                <div class="content">
                                    <label><?php echo __( 'Age', 'iwjob' ); ?></label>
                                    <span><?php echo esc_html($age); echo __( ' year old', 'iwjob' ); ?></span>
                                </div>
                            </li>
                        <?php } ?>
                        <?php if ( $levels = $candidate->get_levels() ) {
                            $levels_links = $candidate->get_levels_links();
                            ?>
                            <li>
                                <i class="icon-injob-layers"></i>
                                <div class="content">
                                    <label><?php echo __( 'Education Levels', 'iwjob' ); ?></label>
                                    <span><?php echo sprintf( __( '%s', '%s', count( $levels ), 'iwjob' ), $levels_links ); ?></span>
                                </div>
                            </li>
                        <?php } ?>
	                    <?php if ( $skills = $candidate->get_skills() ) {
		                    $skills_links = $candidate->get_skills_links();
		                    ?>
							<li>
								<i class="ion-android-bulb"></i>
								<div class="content">
									<label><?php echo __( 'Skills', 'iwjob' ); ?></label>
									<span><?php echo sprintf( __( '%s', '%s', count( $levels ), 'iwjob' ), $skills_links ); ?></span>
								</div>
							</li>
	                    <?php } ?>
	                    <?php if ( $types = $candidate->get_types() ) {
		                    $types_links = $candidate->get_types_links();
		                    ?>
							<li>
								<i class="ion-levels"></i>
								<div class="content">
									<label><?php echo __( 'Types', 'iwjob' ); ?></label>
									<span><?php echo sprintf( __( '%s', '%s', count( $types ), 'iwjob' ), $types_links ); ?></span>
								</div>

							</li>
	                    <?php } ?>
	                    <?php if ( $gender = $candidate->get_gender() ) {
		                    $gender_title = iwj_gender_titles( $gender );
		                    ?>
							<li>
								<i class="ion-transgender"></i>
								<div class="content">
									<label><?php echo __( 'Gender', 'iwjob' ); ?></label>
									<span><?php echo esc_html($gender_title); ?></span>
								</div>
							</li>
	                    <?php } ?>
	                    <?php if ( $languages = $candidate->get_languages() ) {
		                    $language_titles = iwj_get_language_titles( $languages );
		                    ?>
							<li>
								<i class="ion-android-pin"></i>
								<div class="content">
									<label><?php echo __( 'Languages', 'iwjob' ); ?></label>
									<span><?php echo sprintf( _n( ' %s', '%s', count( $languages ), 'iwjob' ), implode( ', ', $language_titles ) ); ?></span>
								</div>
							</li>
	                    <?php } ?>
                    </ul>
                </div>
            <?php } ?>
		</div>

		<?php

		echo $args['after_widget'];
	}
}