<?php
$user = IWJ_User::get_user();
$alerts = $user->get_alerts();
?>
<div class="iwj-alerts iwj-main-block">
    <div class="add-new-alert">
        <a class="iwj-btn iwj-btn-primary" href="<?php echo add_query_arg(array('iwj_tab' => 'new-alert'), iwj_get_page_permalink('dashboard')) ; ?>"><?php echo __('Add New Alert', 'iwjob'); ?></a>
    </div>
    <div class="iwj-table-overflow-x">
        <table class="table">
            <thead>
            <tr>
                <th width="20%"><?php echo __('Position', 'iwjob'); ?></th>
                <th width="50%"><?php echo __('Criteria ', 'iwjob'); ?></th>
                <th width="15%"><?php echo __('Created', 'iwjob'); ?></th>
                <th width="15%"><?php echo __('Action', 'iwjob'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if($alerts) {
                if($alerts['result']){
                    foreach ($alerts['result'] as $item){
                        $alert = IWJ_Alert::get_alert($item->ID);
                        ?>
                        <tr id="alert-<?php echo $alert->get_id(); ?>">
                            <td><?php echo $alert->get_position(); ?></td>
                            <td><?php
                                $criterias = $alert->get_relationship_titles();
                                if($criterias) echo implode(", ", $criterias);
                                ?>
                            </td>
                            <td>
                                <?php
                                echo $alert->get_created();
                                ?>
                            </td>
                            <td>
                                <a href="<?php echo $alert->edit_link(); ?>"><?php echo __('Edit', 'iwjob'); ?></a> |
                                <a href="#" class="iwj-delete-alert" data-id="<?php echo $alert->get_id(); ?>"
                                   data-message="<?php printf(__('Are you sure you want to delete %s?', 'iwjob'), $alert->get_position()); ?>"><?php echo __('Delete', 'iwjob'); ?></a>
                            </td>
                        </tr>
                    <?php
                    }
                }
            }else{ ?>
                <tr class="iwj-empty">
                    <td colspan="5"><?php echo __('No alerts found', 'iwjob'); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="modal fade" id="iwj-confirm-delete-alert" role="dialog">
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
                        <button type="button" class="btn btn-primary iwj-agree-delete-alert"><?php echo __('Continue', 'iwjob'); ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'iwjob'); ?></button>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="clearfix"></div>
    <?php if($alerts && $alerts['total_page'] > 1){ ?>
        <div class="iwj-pagination">
            <?php
            echo paginate_links( array(
                'base' => add_query_arg( 'cpage', '%#%' ),
                'format' => '',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => $alerts['total_page'],
                'current' => $alerts['current_page']
            ));
            ?>
        </div>
        <div class="clearfix"></div>
    <?php } ?>
</div>