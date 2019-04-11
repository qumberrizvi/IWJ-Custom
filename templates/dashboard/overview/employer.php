<?php
$user              = IWJ_User::get_user();
if($user){
$name              = $user->get_display_name();
$employer          = $user->get_employer();
$permalink         = $user->permalink();
$headline          = $user->get_headline();
$short_description = $user->get_short_description();
$description       = $user->get_description();
$address           = $user->get_address();
$phone             = $user->get_phone();
$website           = $user->get_website();
$email             = $user->get_email();
$gallery           = $user->get_gallery();
$maps              = $user->get_map();
$job_ids = $user->get_job_ids();
$dashboard_url     = iwj_get_page_permalink( 'dashboard' );

wp_enqueue_script( 'google-maps' );
wp_enqueue_script( 'infobox' );
wp_enqueue_style( 'jquery-fancybox' );
wp_enqueue_script( 'jquery-fancybox' );

do_action('iwj_before_employer_overview');

?>
<div class="info-top-wrap info-top-wrap-employer">
	<div class="main-information employer-info">
		<div class="empl-box-2x employer-contact">
            <div class="empl-box content-info">
                <div class="avatar"><?php echo get_avatar( $user->get_id(), 130 ); ?></div>
                <div class="empl-detail-info">
                    <h4 class="iwj-epl-title"><?php echo $name; ?></h4>
                    <span><?php echo $headline ? $headline : __( 'Headline', 'iwjob' ); ?></span>
                    <div class="empl-action-button">
						<?php if($user->is_active_profile()){ ?>
                        	<a class="iwj-view-profile" href="<?php echo esc_url( $permalink ); ?>"><?php echo __( 'View Profile', 'iwjob' ); ?></a>
						<?php } ?>
                        <a class="iwj-edit-profile" href="<?php echo add_query_arg( array( 'iwj_tab' => 'profile' ), $dashboard_url ); ?>"><?php echo __( 'Edit Profile', 'iwjob' ); ?></a>
                    </div>
                </div>
            </div>
		</div>
        <div class="empl-info-jobs-listing">
            <div class="info-wrap">
                <div class="empl-info-jobs-item">
                    <div class="empl-box jobs-listing">
                        <div class="empl-small-detail">
                            <h5><?php echo $user->count_jobs(true); ?></h5>
                            <a href="<?php echo add_query_arg( array( 'iwj_tab' => 'jobs' ), $dashboard_url ); ?>"><?php echo esc_html__('All class listings', 'iwjob') ?></a>
                        </div>
                    </div>
                </div>
                <div class="empl-info-jobs-item">
                    <div class="empl-box jobs-published">
                        <div class="empl-small-detail">
                            <h5>
                                <?php
                                $total_publish_jobs = $user->count_jobs_with_status('publish');
                                echo $total_publish_jobs;
                                ?>
                            </h5>
                            <a href="<?php echo add_query_arg( array( 'iwj_tab' => 'jobs', 'status' => 'publish' ), $dashboard_url ); ?>"><?php echo _n('Published Job', 'Published Classes', $total_publish_jobs, 'iwjob') ?></a>
                        </div>
                    </div>
                </div>
                <div class="empl-info-jobs-item">
                    <div class="empl-box jobs-expired">
                        <div class="empl-small-detail">
                            <h5><?php
                                $total_expired_jobs = $user->count_jobs_with_status('expired');
                                echo $total_expired_jobs;
                                ?>
                            </h5>
                            <a href="<?php echo add_query_arg( array( 'iwj_tab' => 'jobs', 'status' => 'iwj-expired' ), $dashboard_url ); ?>"><?php echo _n('Expired Job', 'Expired Classes',$total_expired_jobs, 'iwjob') ?></a>
                        </div>
                    </div>
                </div>
                <div class="empl-info-jobs-item">
                    <div class="empl-box jobs-pending">
                        <div class="empl-small-detail">
                            <h5><?php
                                 $total_pending_jobs = $user->count_jobs_with_status('pending,iwj-pending-payment');
                                 echo $total_pending_jobs;
                                 ?>
                            </h5>
                            <a href="<?php echo add_query_arg( array( 'iwj_tab' => 'jobs', 'status' => 'pending' ), $dashboard_url ); ?>"><?php echo _n('Pending Job', 'Pending Classes', $total_pending_jobs, 'iwjob') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
<div class="iw-profile-content">
	<div class="iwj-employerdl-content row">
		<div class="col-md-6 employer-detail-container">
            <?php
            $args = array('posts_per_page' => 10,'orderby' =>  array('date' => 'DESC' ));
            $application_query = $user->get_applications($args);
            ?>
			<div class="employer-recent-applier">
                <div class="title-block">
                    <h3 class="info-title"><?php echo esc_html__( 'Total Applications', 'iwjob' ); ?></h3>
                    <div class="count"><span><?php echo $application_query ? ($application_query->found_posts) : 0; ?></span></div>
                </div>
				<div class="employer-main-applier">
                    <div class="iwj-table-overflow-x">
                        <table class="table">
                            <thead>
                            <tr>
                                <th width="40%"><?php echo esc_html__( 'Name', 'iwjob' ); ?></th>
                                <th width="35%"><?php echo esc_html__( 'Email', 'iwjob' ); ?></th>
                                <th width="25%"><?php echo esc_html__( 'Phone', 'iwjob' ); ?></th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php
                            if ( $application_query && $application_query->have_posts() ) {
                                $k = 0;
                                while ( $application_query->have_posts() ) :
                                    $application_query->the_post();
                                    $post        = get_post();
                                    $application = IWJ_Application::get_application( $post );
                                    $author = $application->get_author();
                                    if ( $k > 5 ) {
                                        break;
                                    } ?>
                                    <tr>
                                        <td>
                                            <div class="avatar-name">
                                                <?php if($author){ ?>
                                                <div class="avatar-candidate">
                                                    <a class="iwj-view-application" href="#" data-application-id="<?php echo $application->get_id(); ?>" data-remote="false" data-toggle="modal" data-target="#iwj-application-view-modal"><?php echo get_avatar( $author->get_id(), 30 ); ?></a>
                                                </div>
                                                <?php } ?>
                                                <h5>
                                                    <a class="iwj-view-application" href="#" data-application-id="<?php echo $application->get_id(); ?>" data-remote="false" data-toggle="modal" data-target="#iwj-application-view-modal"><?php echo $application->get_full_name(); ?></a>
                                                </h5>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mail"><a href="mailto:<?php echo $application->get_email(); ?>"><?php echo $application->get_email(); ?></a></div>
                                        </td>
                                        <td>
                                            <div class="phone"><?php if($author){ ?><a href="tel:<?php echo $author->get_phone(); ?>"><?php echo $author->get_phone(); ?></a><?php } ?></div>
                                        </td>
                                    </tr>
                                    <?php $k ++; endwhile;
                                wp_reset_postdata(); ?>
                                <?php if ($k > 5) { ?>
                                    <tr>
                                        <td colspan="3" class="text-right">
                                            <div class="view-all"><a href="<?php echo add_query_arg( array( 'iwj_tab' => 'applications' ), $dashboard_url ); ?>"><?php echo __( 'View All Applications', 'iwjob' ); ?></a></div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr class="iwj-empty">
                                    <td colspan="3"><?php echo __( 'Recent applications will be displayed here', 'iwjob' ); ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
				</div>

			</div>
		</div>
		<div class="col-md-6 employer-detail-container">
			<div class="employer-avg-daily-viewers"><div class="title-block">
                    <h3 class="info-title"><?php echo esc_html__( 'Total Views', 'iwjob' ); ?></h3>
                    <div class="count"><span><?php echo $user->get_totals_view(); ?></span></div>
                </div>
				<div class="employer-main-avg-views">
					<div class="iwj-table-overflow-x">
						<table class="table">
							<thead>
							<tr>
								<th width="60%"><?php echo esc_html__( 'Title', 'iwjob' ); ?></th>
								<th width="20%" class="text-center"><?php echo esc_html__( 'Applications', 'iwjob' ); ?></th>
								<th width="20%" class="text-center"><?php echo esc_html__( 'View', 'iwjob' ); ?></th>
							</tr>
							</thead>
							<tbody>
							<?php
                            $args = array( );
                            $args['meta_query'] = array('relation' => 'OR');
                            $args['meta_query']['iwj_view_not_exists'] = array(
                                'key' => '_iwj_views',
                                'compare' => 'NOT EXISTS',
                            );
                            $args['meta_query']['iwj_view_exists'] = array(
                                'key' => '_iwj_views',
                                'compare' => '>',
                                'value' => '0',
                                'type' => 'numeric',
                            );
                            $args['orderby'] = array(
                                'iwj_view_exists' => 'DESC',
                                'date' => 'DESC'
                            );
                            $args['posts_per_page'] = 6;

							$job_query = $user->get_jobs($args);
							if ( $job_query->have_posts() ) {
								$j = 0;
								while ( $job_query->have_posts() ) :
									$job_query->the_post();
									$post       = get_post();
									$job        = IWJ_Job::get_job( $post );
									$job_update = $job->get_update();
									if ( $j > 5 ) {
										break;
									} ?>
									<tr>
										<td>
											<a href="<?php echo $job->permalink(); ?>"><?php echo $job->get_title(); ?></a>
										</td>
										<td class="text-center"><?php echo $job->count_applications(); ?></td>
										<td class="text-center"><?php echo (int)$job->get_views(); ?></td>
									</tr>
									<?php
									$j ++;
								endwhile;
								wp_reset_postdata(); ?>
                                <?php if ($j > 5) { ?>
                                    <tr>
                                        <td colspan="3" class="text-right"><div class="view-all"><a href="<?php echo add_query_arg( array( 'iwj_tab' => 'jobs' ), $dashboard_url ); ?>"><?php echo esc_html__('View All Classes', 'iwjob') ?></a></div></td>
                                    </tr>
                                <?php } ?>
							<?php } else { ?>
								<tr class="iwj-empty">
									<td colspan="3"><?php echo __( 'Your classes will be displayed here', 'iwjob' ); ?></td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade iwj-application-view-modal" id="iwj-application-view-modal" tabindex="-1" role="dialog" data-loading="<?php echo __( 'Loading...', 'iwjob' ); ?>">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title"><?php echo __( 'Application Details', 'iwjob' ); ?></h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						<a class="print-button" href="javascript:window.print()" title="<?php _e( 'Print this page', 'iwjob' ); ?>"><i class="ion-android-print"></i></a>
					</div>
					<div class="modal-body">
						<?php echo __( 'Loading...', 'iwjob' ); ?>
					</div>
				</div>
			</div>
		</div>
		<?php if ( iwj_option( 'email_application_enable' ) ) {
			$application_emails = IWJ_Application::get_emails();
			?>
			<div class="modal fade" id="iwj-application-email-modal" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<form class="iwj-application-email-form" action="<?php the_permalink(); ?>" method="post" enctype="multipart/form-data">
							<div class="modal-header">
								<h4 class="modal-title"><?php echo __( 'Send an email to candidate', 'iwjob' ); ?></h4>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span></button>
							</div>
							<div class="modal-body">
								<select name="email_type" id="application_email">
									<option value=""><?php echo __( 'Select A Letter Template', 'iwjob' ); ?></option>
									<?php foreach ( $application_emails as $email ) {
										echo '<option value="' . $email['id'] . '">' . $email['title'] . '</option>';
									} ?>
								</select>
								<textarea id="application_email_value" style="display: none"><?php echo json_encode( $application_emails ); ?></textarea>
								<input type="text" name="subject" placeholder="<?php echo __( 'Enter Email Subject', 'iwjob' ); ?>" value="">
								<?php
								iwj_field_wysiwyg( 'message', '', true, '', '', '', __( 'You can use tags: #employer_name#, #employer_email#, #candidate_name#, #candidate_email#', 'iwjob' ), 'Enter Email Message', array(
									'quicktags'     => false,
									'editor_height' => 250
								) );
								?>
							</div>
							<div class="iwj-respon-msg iwj-hide"></div>
							<div class="modal-footer">
								<input type="hidden" name="application_id" value="">
								<div class="iwj-button-loader">
									<button class="iwj-btn iwj-btn-primary iwj-application-email-btn"><?php echo __( 'Send', 'iwjob' ); ?></button>
								</div>
								<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'Cancel' ); ?></button>
							</div>
						</form>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>

<?php
do_action('iwj_after_employer_overview');
}

