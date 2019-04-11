<?php
    $user = IWJ_User::get_user();
    $save_jobs = $user->get_save_jobs();
    $search = isset($_GET['search']) ? $_GET['search']: '';
    $url = iwj_get_page_permalink('dashboard');
?>
<div class="iwj-save-jobs iwj-main-block">
    <div class="iwj-search-form">
        <form action="<?php echo $url; ?>">
			<span class="search-box">
                <input type="text" class="search-text" placeholder="<?php echo __('Search', 'iwjob'); ?>" name="search" value="<?php echo esc_attr($search); ?>">
                <button class="search-button" type="submit"><i class="fa fa-search"></i></button>
            </span>
            <input type="hidden" name="iwj_tab" value="save-jobs">
        </form>
    </div>
    <div class="iwj-table-overflow-x">
        <table class="table">
            <thead>
            <tr>
                <th width="50%"><?php echo __('Job Title', 'iwjob'); ?></th>
                <th width="35%"><?php echo __('Company', 'iwjob'); ?></th>
                <th width="15%" class="text-center"><?php echo __('Action', 'iwjob'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if($save_jobs) {
                if($save_jobs['result']){
                    foreach ($save_jobs['result'] as $item){
                        $job = IWJ_Job::get_job($item->post_id);
                        $author = $job->get_author();
                        ?>
                        <tr id="save-job-<?php echo $job->get_id(); ?>" class="save-job-item">
                            <td>
                                <div class="avatar">
                                    <a href="<?php echo $job->get_indeed_url()?esc_url($job->get_indeed_url()): esc_url($job->permalink()); ?>"><?php echo $author ? get_avatar($author->get_id()) : ''; ?></a>
                                </div>
                                <h3><a href="<?php echo $job->get_indeed_url()?esc_url($job->get_indeed_url()): esc_url($job->permalink()); ?>"><?php echo $job->get_title(); ?></a></h3>
                                <div class="job-meta">
                                    <?php if($job->get_locations_links()){ ?>
                                        <div class="categories">
                                            <span class="meta-title"><i class="fa fa-suitcase"></i></span>
                                            <span class="meta-value">
                                                <?php echo $job->get_categories_links(); ?>
                                            </span>
                                        </div>
                                        <div class="salary">
                                            <span class="meta-title"><i class="iwj-icon-money"></i></span>
                                            <span class="meta-value">
                                                <?php echo $job->get_salary(); ?>
                                            </span>
                                        </div>
                                        <div class="location">
                                            <span class="meta-title"><i class="ion-android-pin"></i></span>
                                            <span class="meta-value">
                                                <?php echo $job->get_locations_links(); ?>
                                            </span>
                                        </div>
                                    <?php } ?>
                                </div>
                            </td>
                            <td>
                                <?php if($author) { ?>
                                    <a href="<?php echo $job->get_indeed_url()?esc_url($job->get_indeed_url()): $author->permalink(); ?>"><?php echo $job->get_indeed_company_name()?esc_html($job->get_indeed_company_name()):$author->get_display_name(); ?></a>
                                <?php } ?>
                            </td>
                            <td>
                                <div class="iwj-menu-action-wrap">
                                    <a tabindex="0" class="iwj-toggle-action collapsed" type="button" data-toggle="collapse" data-trigger="focus" data-target="#nav-collapse<?php echo $job->get_id(); ?>"></a>
                                    <div id="nav-collapse<?php echo $job->get_id(); ?>" class="collapse iwj-menu-action" data-id="nav-collapse<?php echo $job->get_id(); ?>">
                                        <div class="iwj-menu-action-inner">
                                            <div>
                                                <a href="#" class="iwj-undo-save-job" data-id="<?php echo $job->get_id(); ?>"
                                                   data-message="<?php printf(__('Are you sure you want to remove %s?', 'iwjob'), $job->get_title()); ?>"><?php echo __('Delete', 'iwjob'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    <?php
                    }
                }
            }else{ ?>
                <tr class="iwj-empty">
                    <td colspan="3"><?php echo __('No jobs found', 'iwjob'); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="modal fade" id="iwj-confirm-undo-save-job" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo __('Confirm Delete', 'iwjob'); ?></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p></p>
                </div>
                <div class="modal-footer">
                    <div class="iwj-respon-msg"></div>
                    <div class="iwj-button-loader">
                        <button type="button" class="btn btn-primary iwj-agree-undo-save-job"><?php echo __('Continue', 'iwjob'); ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'iwjob'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php if($save_jobs && $save_jobs['total_page'] > 1){ ?>
        <div class="iwj-pagination">
        <?php
        echo paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $save_jobs['total_page'],
            'current' => $save_jobs['current_page']
            ));
        ?>
        </div>
        <div class="clearfix"></div>
    <?php } ?>
</div>