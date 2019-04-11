<?php
$paged = isset($_GET['cpage']) ? $_GET['cpage'] : '1';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$user = IWJ_User::get_user();
$application_query = $user->get_submited_applications();
$url = iwj_get_page_permalink('dashboard');
?>
<div class="iwj-submited-applications iwj-main-block">
    <div class="iwj-search-form">
        <form action="<?php echo $url; ?>">
            <span class="search-box">
                <input type="text" class="search-text" placeholder="<?php echo __('Search by job', 'iwjob'); ?>" name="search" value="<?php echo esc_attr($search); ?>">
                <button class="search-button" type="submit"><i class="fa fa-search"></i></button>
            </span>
            <select class="search-select iwj-jobs-status iwj-select-2-wsearch" name="status">
                <option value="" <?php selected($status, ''); ?>><?php echo __('Status', 'iwjob'); ?></option>
                <?php
                $job_status = IWJ_Application::get_status_array(true, true);
                foreach ($job_status as $key => $title) {
                    echo '<option value="' . $key . '" ' . selected($status, $key, false) . '>' . $title . '</option>';
                }
                ?>
            </select>
            <input type="hidden" name="iwj_tab" value="submited-applications">
        </form>
    </div>
    <div class="iwj-submited-applications-table">
        <div class="iwj-table-overflow-x">
            <table class="table">
                <thead>
                    <tr>
                        <th width="30%"><?php echo __('Applied Job', 'iwjob'); ?></th>
                        <th width="20%"><?php echo __('Student', 'iwjob'); ?></th>
                        <th width="20%"><?php echo __('Applied Date', 'iwjob'); ?></th>
                        <th width="20%"><?php echo __('Download CV', 'iwjob'); ?></th>
                        <th width="10%" class="text-center"><?php echo __('Action', 'iwjob'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($application_query && $application_query->have_posts()) { ?>
                        <?php
                        while ($application_query->have_posts()) {
                            $application_query->the_post();
                            $post = get_post();
                            $application = IWJ_Application::get_application($post);
                            $job = $application->get_job();
                            if ($job != null) {
                                ?>
                                <tr class="application-item application-<?php echo $application->get_id(); ?>">
                                    <td class="application-job">
                                        <?php
                                        $job_link = $job->permalink();
                                        ?>
                                        <h3>
                                            <a href="<?php echo $job_link; ?>"><?php echo $job->get_title(true); ?></a>
                                        </h3>
                                        <?php
                                        ?>
                                    </td>
                                    <td class="application-employer">
                                        <?php
                                        $employer = $job->get_employer('publish');
                                        ?>
                                        <h3>
                                            <?php if ($employer): ?>
                                                <a href="<?php echo $employer->permalink(); ?>"><?php echo $employer->get_title(); ?></a>
                                            <?php else: ?>
                                                <a class="dotted" href="#" data-toggle="tooltip" title="<?php echo __('Student has been deleted or unpublish', 'iwjob');?>"><?php echo __('Unknown', 'iwjob'); ?></a>
                                            <?php endif; ?>
                                        </h3>
                                    </td>
                                    <td class="application-created"><?php echo $application->get_created(); ?></td>
                                    <td class="application-cv">
                                        <?php
                                        $cv = $application->get_cv();
                                        if ($cv && $cv['url']) {
                                            echo '<a href="' . $cv['url'] . '" target="_blank">' . $cv['name'] . '</a>';
                                        }
                                        ?>
                                    </td>
                                    <td class="application-view text-center">
                                        <a class="iwj-view-submited-application" href="#" data-application-id="<?php echo $application->get_id(); ?>" data-remote="false" data-toggle="modal" data-target="#iwj-submited-application-view-modal"><?php echo __('View Application', 'iwjob'); ?></a>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        wp_reset_postdata();
                    } else {
                        ?>
                        <tr class="iwj-empty">
                            <td colspan="4"><?php echo __('No applications found.', 'iwjob'); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="modal fade iwj-application-view-modal" id="iwj-submited-application-view-modal" tabindex="-1" role="dialog" data-loading="<?php echo __('Loading...', 'iwjob'); ?>">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><?php echo __('Application Details', 'iwjob'); ?></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <?php echo __('Loading...', 'iwjob'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php if ($application_query && $application_query->max_num_pages > 1) { ?>
        <div class="iwj-pagination">
            <?php
            $big = 999999999; // need an unlikely integer
            echo paginate_links(array(
                'base' => add_query_arg('cpage', '%#%'),
                'format' => '',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'current' => $paged,
                'total' => $application_query->max_num_pages
            ));
            ?>
        </div>
        <div class="clearfix"></div>
    <?php } ?>
</div>
