<?php
    $user = IWJ_User::get_user();
	$user_package_query = $user->get_user_packages('resum_package');
?>
<div class="iwj-user-packages iwj-main-block">
	<div class="add-new-package">
		<a class="iwj-btn iwj-btn-primary" href="<?php echo add_query_arg(array('iwj_tab' => 'new-resume-package'), iwj_get_page_permalink('dashboard')) ; ?>"><?php echo __('New Resume Package', 'iwjob'); ?></a>
	</div>
    <div class="iwj-user-packages-table iwj-table-overflow-x">
        <table class="table">
            <thead>
                <tr class="package-heading">
                    <th width="10%"><?php echo __('Order', 'iwjob');?></th>
                    <th width="20%"><?php echo __('Package', 'iwjob');?></th>
                    <th width="20%"><?php echo __('Resume Remaining', 'iwjob');?></th>
                    <th width="10%" class="text-center"><?php echo __('Status', 'iwjob');?></th>
                </tr>
            </thead>
            <tbody>
            <?php if($user_package_query->have_posts()) {
                while ($user_package_query->have_posts()){
                    $user_package_query->the_post();
                    $post = get_post();
                    $user_package = IWJ_User_Package::get_user_package($post);
                    ?>
                    <tr class="package-item">
                        <td class="package-order">
                            <?php
                            $order_id = $user_package->get_order_id();
                            $order = IWJ_Order::get_order($order_id);
                            if($order){
                                echo '<a href="'.$order->get_view_link().'">#'.$order_id.'</a>';
                            }else{
                                if(function_exists('wc_get_order')){
                                    $order = wc_get_order($order_id);
                                }
                                if($order){
                                    $dashboard = iwj_get_page_permalink( 'dashboard' );
                                    $view_url = add_query_arg(array('iwj_tab'=>'view-w-order', 'order_id'=>$order->get_id()), $dashboard);
                                    echo '<a href="'.$view_url.'">#'.$order_id.'</a>';
                                }else{
                                    echo '#'.$order_id;
                                }
                            }
                            ?>
                        </td>
                        <td class="package-title">
                            <h3 class="title"><?php echo $user_package->get_package_title(); ?></h3>
                        </td>
                        <td class="package-remain">
                            <span><?php echo $user_package->get_remain_resum() ?></span>
                        </td>
                        <td class="package-status iwj-status text-center">
                            <span data-toggle="tooltip" class="<?php echo $user_package->get_status(); ?>" title="<?php echo IWJ_User_Package::get_status_title($user_package->get_status()); ?>"><?php echo iwj_get_status_icon($user_package->get_status()); ?></span>
                        </td>
                    </tr>
                    <?php
                }
                wp_reset_postdata();
            }else{ ?>
                <tr class="iwj-empty">
                    <td colspan="4"><?php echo __('You do not have any packages yet.', 'iwjob'); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <div class="clearfix"></div>
    </div>
    <?php
    if($user_package_query->max_num_pages > 1) { ?>
        <div class="iwj-pagination">
            <?php
            echo paginate_links(array(
                'base' => add_query_arg( 'cpage', '%#%'),
                'format' => '',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => $user_package_query->max_num_pages,
                'current' => isset($_GET['cpage']) ? $_GET['cpage']: '1'
            ));
            ?>
        </div>
        <div class="clearfix"></div>
    <?php } ?>
</div>