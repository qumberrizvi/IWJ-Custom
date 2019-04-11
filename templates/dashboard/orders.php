<div class="iwj-orders iwj-main-block">
    <div class="iwj-table-overflow-x">
        <?php

        $user = IWJ_User::get_user();
        $order_query = $user->get_orders();
        $status = isset($_GET['order_status']) ? $_GET['order_status'] : '';
        $type = isset($_GET['order_type']) ? $_GET['order_type'] : '';
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $url = iwj_get_page_permalink('dashboard');
        ?>
        <div class="iwj-search-form">
            <form action="<?php echo $url; ?>">
            <span class="search-box">
                <input type="text" class="search-text" placeholder="<?php echo __('Search', 'iwjob'); ?>" name="search" value="<?php echo esc_attr($search); ?>">
                <button class="search-button" type="submit"><i class="fa fa-search"></i></button>
            </span>
                <select class="search-select iwj-order-status iwj-select-2-wsearch" name="order_status">
                    <option value="" <?php selected($status, '', false); ?>><?php echo __('All Status', 'iwjob'); ?></option>
                    <?php
                    $status_arr = IWJ_Order::get_status_array();
                    foreach ($status_arr as $_status_id => $_status_title){
                        echo '<option value="'.$_status_id.'" '.selected($status, $_status_id, false).'>'.$_status_title.'</option>';
                    }
                    ?>
                </select>
                <select class="search-select iwj-order-type iwj-select-2-wsearch" name="order_type">
                    <option value="" <?php selected($type, '', false); ?>><?php echo __('All Types', 'iwjob'); ?></option>
                    <?php
                    $type_arr = IWJ_Order::get_type_array();
                    foreach ($type_arr as $_type_id => $_type_title){
                        echo '<option value="'.$_type_id.'" '.selected($type, $_type_id, false).'>'.$_type_title.'</option>';
                    }
                    ?>
                </select>
                <input type="hidden" name="iwj_tab" value="orders">
            </form>
        </div>

        <div class="iwj-orders-table">
            <div class="iwj-table-overflow-x">
                <table class="table">
                    <thead>
                    <tr>
                        <th width="15%"><?php echo __('ID', 'iwjob'); ?></th>
                        <th width="20%"><?php echo __('Type', 'iwjob'); ?></th>
                        <th width="20%"><?php echo __('Created Date', 'iwjob'); ?></th>
                        <th width="15%" class="text-center"><?php echo __('Status', 'iwjob'); ?></th>
                        <th width="15%" class="text-center"><?php echo __('Action', 'iwjob'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if($order_query->have_posts()) {
                        while ($order_query->have_posts()){
                            $order_query->the_post();
                            $post = get_post();
                            $order = IWJ_Order::get_order($post);
                            ?>
                            <tr>
                                <td>#<?php echo $order->get_id(); ?></td>
                                <td><?php echo $order->get_type_title($order->get_type()); ?></td>
                                <td><?php echo date(get_option('date_format'), strtotime($order->get_created())); ?></td>
                                <td class="iwj-status text-center">
                                    <span data-toggle="tooltip" class="<?php echo $order->get_status(); ?>" title="<?php echo IWJ_Order::get_status_title($order->get_status()); ?>"><?php echo iwj_get_status_icon($order->get_status()); ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="iwj-menu-action-wrap">
                                        <a tabindex="0" class="iwj-toggle-action collapsed" type="button" data-toggle="collapse" data-trigger="focus" data-target="#nav-collapse<?php echo $post->ID; ?>"></a>
                                        <div id="nav-collapse<?php echo $post->ID; ?>" class="collapse iwj-menu-action" data-id="nav-collapse<?php echo $post->ID; ?>">
                                            <div class="iwj-menu-action-inner">
                                                <div><a class="iwj-view-order" href="#" data-order-id="<?php echo $order->get_id(); ?>" data-remote="false" data-toggle="modal" data-target="#iwj-order-view-modal"><?php echo __('View Order', 'iwjob'); ?></a></div>
                                                <?php
                                                if($order->has_status('pending-payment')){
                                                    ?>
                                                    <div>
                                                        <a class="iwj-pay-order" href="<?php echo $order->get_pay_url(); ?>"><?php echo __('Payment Order', 'iwjob'); ?></a>
                                                    </div>
                                                    <div>
                                                        <a class="iwj-cancel-order" href="<?php echo $order->get_cancel_url(); ?>"><?php echo __('Cancel Order', 'iwjob'); ?></a>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                        wp_reset_postdata();
                        ?>
                    <?php }else{ ?>
                        <tr>
                            <td class="iwj-empty" colspan="5">
                                <?php echo __('No order found', 'iwjob'); ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal fade" id="iwj-order-view-modal" tabindex="-1" role="dialog" data-loading="<?php echo __('loading...', 'iwjob'); ?>">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><?php echo __('Order Details','iwjob'); ?></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <?php echo __('loading...', 'iwjob'); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal"><?php echo __('Close', 'iwjob'); ?></button>
                        <a href="javascript:window.print()" class="btn btn-primary" ><?php echo __('Print', 'iwjob'); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if($order_query->max_num_pages > 1) { ?>
            <div class="iwj-pagination">
                <?php
                echo paginate_links(array(
                    'base' => add_query_arg( 'cpage', '%#%'),
                    'format' => '',
                    'prev_text' => __('&laquo;'),
                    'next_text' => __('&raquo;'),
                    'total' => $order_query->max_num_pages,
                    'current' => isset($_GET['cpage']) ? $_GET['cpage']: '1'
                ));
                ?>
            </div>
        <?php } ?>
    </div>
</div>