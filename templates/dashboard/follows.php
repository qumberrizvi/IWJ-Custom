<?php
    $user = IWJ_User::get_user();
    $follows = $user->get_follows();
    $search = isset($_GET['search']) ? $_GET['search']: '';
    $url = iwj_get_page_permalink('dashboard');
?>
<div class="iwj-follows iwj-main-block">
    <div class="iwj-search-form">
        <form action="<?php echo $url; ?>">
			<span class="search-box">
                <input type="text" class="search-text" placeholder="<?php echo __('Search', 'iwjob'); ?>" name="search" value="<?php echo esc_attr($search); ?>">
                <button class="search-button" type="submit"><i class="fa fa-search"></i></button>
            </span>
            <input type="hidden" name="iwj_tab" value="follows">
        </form>
    </div>
    <div class="iwj-table-overflow-x">
        <table class="table">
            <thead>
            <tr>
                <th width="40%"><?php echo __('Company', 'iwjob'); ?></th>
                <th width="40%" class="text-center"><?php echo __('Opening Classes', 'iwjob'); ?></th>
                <th width="15%" class="text-center"><?php echo __('Action', 'iwjob'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if($follows) {
                if($follows['result']){
                    foreach ($follows['result'] as $item){
                        $employer = IWJ_Employer::get_employer($item->post_id);
                        $author = $employer->get_author();
                        ?>
                        <tr class="follow-item" id="follow-<?php echo $item->post_id; ?>">
                            <td>
                                <div class="avatar">
                                    <a href="<?php echo $employer->permalink(); ?>"><?php echo get_avatar($author->get_id()); ?></a>
                                </div>
                                <div class="follow-content">
                                    <h3><a href="<?php echo $employer->permalink(); ?>"><?php echo $employer->get_display_name(); ?></a></h3>
                                    <div class="employer-meta">
                                        <?php if($category_links = $employer->get_categories_links()){ ?>
                                            <div class="categories">
                                                <span class="meta-title"><i class="fa fa-suitcase"></i></span>
                                            <span class="meta-value">
                                                <?php echo $category_links; ?>
                                            </span>
                                            </div>
                                        <?php } ?>
                                        <?php if($location_links = $employer->get_locations_links()){ ?>
                                            <div class="location">
                                                <span class="meta-title"><i class="ion-android-pin"></i></span>
                                            <span class="meta-value">
                                                <?php echo $location_links; ?>
                                            </span>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <td class="text-center">
                                <?php
                                $opening_jobs = $author->get_open_jobs();
                                if($opening_jobs){
                                    $opening_jobs = count($opening_jobs);
                                    echo '<a href="'.$employer->permalink().'">'.sprintf(_n('%d Open Position', '%d Open Positions', $opening_jobs, 'iwjob'), $opening_jobs).'</a>';
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <div class="iwj-menu-action-wrap">
                                    <a tabindex="0" class="iwj-toggle-action collapsed" type="button" data-toggle="collapse" data-trigger="focus" data-target="#nav-collapse<?php echo $item->post_id; ?>"></a>
                                    <div id="nav-collapse<?php echo $item->post_id; ?>" class="collapse iwj-menu-action" data-id="nav-collapse<?php echo $item->post_id; ?>">
                                        <div class="iwj-menu-action-inner">
                                            <div>
                                                <a href="#" class="iwj-unfollow" data-id="<?php echo $item->post_id; ?>"
                                                   data-message="<?php printf(__('Are you sure you want to remove %s?', 'iwjob'), $employer->get_display_name()); ?>"><?php echo __('Delete', 'iwjob'); ?>
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
                    <td colspan="3"><?php echo __('No companies found', 'iwjob'); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="iwj-confirm-unfollow" role="dialog">
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
                        <button type="button" class="btn btn-primary iwj-agree-unfollow"><?php echo __('Continue', 'iwjob'); ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'iwjob'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php if($follows && $follows['total_page'] > 1){ ?>
        <div class="iwj-pagination">
        <?php
        echo paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $follows['total_page'],
            'current' => $follows['current_page']
            ));
        ?>
        </div>
        <div class="clearfix"></div>
    <?php } ?>
</div>