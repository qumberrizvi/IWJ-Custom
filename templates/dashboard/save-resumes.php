<?php
$user = IWJ_User::get_user();
$save_resums = $user->get_save_resumes();
$url = iwj_get_page_permalink('dashboard');
$search = isset($_GET['search']) ? $_GET['search']: '';
?>
<div class="iwj-save-resumes iwj-main-block">
    <div class="iwj-search-form">
        <form action="<?php echo $url; ?>">
			<span class="search-box">
                <input type="text" class="search-text" placeholder="<?php echo __('Search', 'iwjob'); ?>" name="search" value="<?php echo esc_attr($search); ?>">
                <button class="search-button" type="submit"><i class="fa fa-search"></i></button>
            </span>
            <input type="hidden" name="iwj_tab" value="save-resumes">
        </form>
    </div>

    <div class="iwj-table-overflow-x">
        <table class="table">
            <thead>
                <tr>
                    <th width="45%"><?php echo __('Resume', 'iwjob'); ?></th>
                    <th width="40%"><?php echo __('Email', 'iwjob'); ?></th>
                    <th width="15%" class="text-center"><?php echo __('Action', 'iwjob'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php if($save_resums) {
                if($save_resums['result']){
                    foreach ($save_resums['result'] as $item){
                        $candidate = IWJ_Candidate::get_candidate($item->post_id);
                        ?>
                        <tr id="save-resume-<?php echo $item->post_id; ?>" class="save-resume-item">
                            <td>
                                <div class="avatar">
                                    <a href="<?php echo $candidate->permalink(); ?>"><?php echo $candidate->get_avatar(); ?></a>
                                </div>
                                <h3><a href="<?php echo $candidate->permalink(); ?>"><?php echo $candidate->get_display_name(); ?></a></h3>
                                <div class="candidate-meta">
                                    <?php if($candidate->get_locations_links()){ ?>
                                        <div class="location">
                                            <span class="meta-title"><i class="ion-android-pin"></i></span>
                                            <span class="meta-value">
                                            <?php echo $candidate->get_locations_links(); ?>
                                            </span>
                                        </div>
                                        <?php if($phone = $candidate->get_phone()){ ?>
                                            <div class="phone"><span class="meta-title"><i class="iwj-icon-mobile"></i></span><span class="meta-value"><a href="tel:<?php echo $phone; ?>" title="<?php echo $phone; ?>"><?php echo $phone; ?></a></span></div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </td>
                            <td>
                                <a href="mailto:<?php echo $candidate->get_email(); ?>"><?php echo $candidate->get_email(); ?></a>
                            </td>
                            <td class="text-center">
                                <div class="iwj-menu-action-wrap">
                                    <a tabindex="0" class="iwj-toggle-action collapsed" type="button" data-toggle="collapse" data-trigger="focus" data-target="#nav-collapse<?php echo $candidate->get_id(); ?>"></a>
                                    <div id="nav-collapse<?php echo $candidate->get_id(); ?>" class="collapse iwj-menu-action" data-id="nav-collapse<?php echo $candidate->get_id(); ?>">
                                        <div class="iwj-menu-action-inner">
                                            <div>
                                                <a href="#" class="iwj-delete-save-resume" data-id="<?php echo $item->post_id; ?>"
                                                   data-message="<?php printf(__('Are you sure you want to remove %s?', 'iwjob'), get_the_title($item->post_id)); ?>">
                                                    <?php echo __('Delete', 'iwjob'); ?>
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
                    <td colspan="3"><?php echo __('No profiles found.', 'iwjob'); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="modal fade" id="iwj-confirm-delete-save-resume" role="dialog">
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
                        <button type="button" class="btn btn-primary iwj-agree-delete-save-resume"><?php echo __('Continue', 'iwjob'); ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'iwjob'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php if($save_resums && $save_resums['total_page'] > 1){ ?>
        <div class="iwj-pagination">
            <?php
            echo paginate_links( array(
                'base' => add_query_arg( 'cpage', '%#%' ),
                'format' => '',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => $save_resums['total_page'],
                'current' => $save_resums['current_page']
            ));
            ?>
        </div>
        <div class="clearfix"></div>
    <?php } ?>
</div>