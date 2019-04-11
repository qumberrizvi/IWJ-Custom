<?php
    $search = isset($_GET['search']) ? $_GET['search']: '';
    $paged = isset($_GET['cpage']) ? $_GET['cpage']: '1';
    $status = isset($_GET['status']) ? $_GET['status']: '';
    $orderby = isset($_GET['orderby']) ? $_GET['orderby']: '';
    $user = IWJ_User::get_user();
    $job_query = $user->get_jobs();
	$url = iwj_get_page_permalink('dashboard');
?>
<div class="iwj-jobs iwj-main-block">
	<div class="iwj-search-form">
		<form action="<?php echo $url; ?>">
            <span class="search-box">
                <input type="text" class="search-text" placeholder="<?php echo __('Search', 'iwjob'); ?>" name="search" value="<?php echo esc_attr($search); ?>">
                <button class="search-button" type="submit"><i class="fa fa-search"></i></button>
            </span>
            <select class="search-select iwj-jobs-status iwj-select-2-wsearch" name="status">
				<option value="" <?php selected($status, ''); ?>><?php echo __('Status', 'iwjob'); ?></option>
				<?php
				$job_status = IWJ_Job::get_status_array(true, true);
				foreach ($job_status as $key=>$title){
					echo '<option value="'.$key.'" '.selected($status, $key, false).'>'.$title.'</option>';
				}
				?>
			</select>
            <select class="search-select iwj-jobs-orderby iwj-select-2-wsearch" name="orderby">
				<option value="" <?php selected($orderby, ''); ?>><?php echo __('Default Sorting', 'iwjob'); ?></option>
				<option value="title_asc" <?php selected($orderby, 'title_asc'); ?>><?php echo __('Title', 'iwjob'); ?></option>
				<option value="date_desc" <?php selected($orderby, 'date_desc'); ?>><?php echo __('Date', 'iwjob'); ?></option>
				<option value="modified_desc" <?php selected($orderby, 'modified_desc'); ?>><?php echo __('Modified', 'iwjob'); ?></option>
			</select>
			<input type="hidden" name="iwj_tab" value="jobs">
		</form>
	</div>
	
	<div class="iwj-jobs-table">
        <div class="iwj-table-overflow-x">
            <table class="table">
                <thead>
                <tr>
                    <th width="30%" class=""><?php echo __('Title', 'iwjob'); ?></th>
                    <th width="18%" class="text-center"><?php echo __('Applications', 'iwjob'); ?></th>
                    <th width="12%" class=""><?php echo __('Created', 'iwjob'); ?></th>
                    <th width="8%" class=""><?php echo __('Expired', 'iwjob'); ?></th>
                    <th width="8%" class="text-center"><?php echo __('Views', 'iwjob'); ?></th>
                    <th width="8%" class="text-center"><?php echo __('Status', 'iwjob'); ?></th>
                    <th width="8%" class="text-center"><?php echo __('Featured', 'iwjob'); ?></th>
                    <th width="8%" class="text-center"><?php echo __('Action', 'iwjob'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if($job_query->have_posts()) { ?>
                    <?php
                    while ($job_query->have_posts()){
                        $job_query->the_post();
                        $post = get_post();
                        $job = IWJ_Job::get_job($post);
                        $job_update = $job->get_update();

                        ?>
                        <tr class="job-item status-<?php echo $job->get_status(); ?>" id="job-<?php echo $job->get_id(); ?>">
                            <td class="job-title-meta">
                                <h3 class="job-title">
                                    <a href="<?php echo $job->permalink(); ?>"><?php echo $job->get_title(); ?></a>
                                    <?php if($job_update){
                                        if($job_update->has_status('pending')){
                                            echo '<i data-toggle="tooltip" title="'.__('Pending Update', 'iwjob').'" class="fa fa-clock-o"></i>';
                                        }else{
                                            echo '<i data-toggle="tooltip" title="'.__('Rejected Update', 'iwjob').'" class="fa fa-exclamation-triangle"></i>';
                                        }
                                    } ?>
                                </h3>
                                <ul class="job-meta">
                                    <?php if ($job->get_categories_links()) { ?>
                                        <li class="categories"><i class="fa fa-suitcase"></i> <?php echo $job->get_categories_links(); ?></li>
                                    <?php } ?>
                                    <?php if ($job->get_salary()) { ?>
                                        <li class="sallary"><i class="iwj-icon-money"></i><?php echo $job->get_salary(); ?></li>
                                    <?php } ?>
                                    <?php if ($job->get_locations_links()) { ?>
                                        <li class="locations"><i class="ion-android-pin"></i> <?php echo $job->get_locations_links(); ?></li>
                                    <?php } ?>
                                </ul>
                            </td>
                            <td class="job-aplication text-center">
                                <?php
                                if($count_applications = $job->count_applications()){
                                    $application_ids = $job->get_application_ids(3);
                                    foreach ($application_ids as $application_id){
                                        $application = IWJ_Application::get_application($application_id);
                                        $author_id = $application->get_author_id();
                                        $avatar_url = get_avatar_url($author_id);
                                        ?>
                                        <a data-toggle="tooltip" href="<?php echo $application->view_permalink(); ?>" class="application-item" title="<?php echo $application->get_full_name(); ?>"><img src="<?php echo $avatar_url; ?>" alt=""></a>
                                    <?php
                                    }
                                    ?>
                                    <a href="<?php echo $job->applications_permalink(); ?>" class="applications">
                                        <?php
                                        if($count_applications > 2) {
                                            echo sprintf(__('<span class="counter">%d+</span> Applied ', 'iwjob'),$count_applications);
                                        }else{
                                            echo sprintf(__('<span class="counter">%d</span> Applied ', 'iwjob'),$count_applications);
                                        }
                                        ?>
                                    </a>
                                <?php }else{
                                    echo '<div class="text-center">'.__('N/A', 'iwjob').'</div>';
                                } ?>
                            </td>

                            <td class="iwj-created">
                                <?php echo date_i18n(get_option('date_format'), strtotime($job->get_created())); ?>
                            </td>
                            <td class="iwj-expiry">
                                <?php
                                    $expiry = $job->get_expiry();
                                    if($expiry){
                                        echo date_i18n(get_option('date_format'), $expiry);
                                    }elseif($job->has_status('publish')){
                                        echo __('Open', 'iwjob');
                                    }else{
                                        echo __('N/A', 'iwjob');
                                    }
                                ?>
                            </td>
                            <td class="iwj-views text-center">
                                <?php if($job->has_status(array('publish', 'expired'))){
                                    echo $job->get_views();
                                }else{
                                    echo __('N/A', 'iwjob');
                                } ?>
                            </td>
                            <td class="iwj-status text-center">
                                <?php if($job->can_publish_draft()){ ?>
                                    <a href="<?php echo $job->publish_draft_link(); ?>"><span data-toggle="tooltip" class="<?php echo $job->get_status(); ?>" title="<?php echo __('Click to publish this job', 'iwjob'); ?>"><?php echo iwj_get_status_icon($job->get_status()); ?></span></a>
                                <?php }elseif($job->can_unpublish()){ ?>
                                    <a href="<?php echo $job->unpublish_link(); ?>"><span data-toggle="tooltip" class="<?php echo $job->get_status(); ?>" title="<?php echo __('Click to unpublish this job', 'iwjob'); ?>"><?php echo iwj_get_status_icon($job->get_status()); ?></span></a>
                                <?php }else{ ?>
                                    <span data-toggle="tooltip" class="<?php echo $job->get_status(); ?>" title="<?php echo IWJ_Job::get_status_title($job->get_status()); ?>"><?php echo iwj_get_status_icon($job->get_status()); ?></span>
                                <?php } ?>
                            </td>
                            <td class="job-featured <?php echo ($job->is_featured() || $job->is_pending_featured()) ? 'featured' : ''; ?>">
                                <?php if ($job->is_pending_featured()){ ?>
                                    <i class="fa fa-star"></i>
                                <?php }else ?>
                                <?php if ($job->is_featured()){ ?>
                                    <a href="<?php echo $job->unfeatured_link(); ?>" data-toggle="tooltip" title="<?php echo __('Remove featured', 'iwjob'); ?>"><i class="fa fa-star"></i></a>
                                <?php } elseif($job->can_make_featured()) { ?>
                                    <a href="<?php echo $job->make_featured_link(); ?>" data-toggle="tooltip" title="<?php echo __('Make featured', 'iwjob'); ?>"><i class="fa fa-star-o"></i></a>
                                <?php }else{?>
                                    <i class="fa fa-star-o"></i>
                                <?php } ?>
                            </td>
                            <td class="job-action">
                                <div class="iwj-menu-action-wrap">
                                    <a tabindex="0" class="iwj-toggle-action collapsed" type="button" data-toggle="collapse" data-trigger="focus" data-target="#nav-collapse<?php echo $post->ID; ?>"></a>
                                    <div id="nav-collapse<?php echo $post->ID; ?>" class="collapse iwj-menu-action" data-id="nav-collapse<?php echo $post->ID; ?>">
                                        <div class="iwj-menu-action-inner">
                                            <?php if($job->can_edit()){ ?>
                                                <div><a href="<?php echo $job->edit_link(); ?>"><?php echo __('Edit', 'iwjob'); ?></a></div>
                                            <?php } ?>
                                            <?php if($job->can_publish_draft()){ ?>
                                                <div><a href="<?php echo $job->edit_draft_link(); ?>"><?php echo __('Edit', 'iwjob'); ?></a></div>
                                                <div><a href="<?php echo $job->publish_draft_link(); ?>"><?php echo __('Publish', 'iwjob'); ?></a></div>
                                            <?php } ?>
                                            <?php if($job->can_renew()){ ?>
                                                <div><a href="<?php echo $job->renew_link(); ?>"><?php echo __('Renew', 'iwjob'); ?></a></div>
                                            <?php } ?>
                                            <?php if($job->can_make_featured()){ ?>
                                                <div><a href="<?php echo $job->make_featured_link(); ?>"><?php echo __('Make featured', 'iwjob'); ?></a></div>
                                            <?php } ?>
                                            <?php if($job->can_delete()){ ?>
                                                <div><a href="#" class="iwj-delete-job" data-id="<?php echo $job->get_id(); ?>" data-message="<?php printf(__('Are you sure you want to delete %s?', 'iwjob'), $job->get_title()); ?>"><?php echo __('Delete', 'iwjob'); ?></a></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php
                    }
                    wp_reset_postdata();
                }else{ ?>
                    <tr class="iwj-empty">
                        <td colspan="8"><?php echo __('No class found', 'iwjob'); ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="modal fade" id="iwj-confirm-delete-job" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><?php echo __('Confirm Delete', 'iwjob'); ?></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <p></p>
                    </div>
                    <div class="modal-footer">
                        <div class="iwj-respon-msg"></div>
                        <div class="iwj-button-loader">
                            <button type="button" class="btn btn-primary iwj-agree-delete-job"><?php echo __('Continue', 'iwjob'); ?></button>
                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'iwjob'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
    <div class="clearfix"></div>
	<?php
        if($job_query->max_num_pages > 1) { ?>
            <div class="iwj-pagination">
                <?php
                $big = 999999999; // need an unlikely integer
                echo paginate_links(array(
                    'base' => add_query_arg( 'cpage', '%#%' ),
                    'format' => '',
                    'prev_text' => __('&laquo;'),
                    'next_text' => __('&raquo;'),
                    'current' => $paged,
                    'total' => $job_query->max_num_pages
                ));
                ?>
            </div>
            <div class="clearfix"></div>

        <?php } ?>
</div>