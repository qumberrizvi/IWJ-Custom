<?php
    $user = IWJ_User::get_user();
	$user_package_query = $user->get_user_packages();
?>
<div class="iwj-user-packages iwj-main-block">
	<div class="add-new-package">
		<a class="iwj-btn iwj-btn-primary" href="<?php echo add_query_arg(array('iwj_tab' => 'new-package'), iwj_get_page_permalink('dashboard')) ; ?>"><?php echo __('New Package', 'iwjob'); ?></a>
	</div>
    <div class="iwj-user-packages-table iwj-table-overflow-x">
        <table class="table">
            <thead>
                <tr class="package-heading">
                    <th width="12%"><?php echo __('Order', 'iwjob');?></th>
                    <th width="18%"><?php echo __('Package', 'iwjob');?></th>
                    <th width="15%" class="text-center"><?php echo __('Classes Remaining', 'iwjob');?></th>
                    <th width="15%" class="text-center"><?php echo __('Renews Remaining', 'iwjob');?></th>
                    <th width="15%" class="text-center"><?php echo __('Features Remaining', 'iwjob');?></th>
                    <th width="15%" class="text-center"><?php echo __('Job Duration', 'iwjob');?></th>
                    <th width="10%" class="text-center"><?php echo __('Status', 'iwjob');?></th>
                </tr>
            </thead>
            <tbody>
            <?php if($user_package_query->have_posts()) {
                $dashboard = iwj_get_page_permalink('dashboard');
                while ($user_package_query->have_posts()){
                    $user_package_query->the_post();
                    $post = get_post();
                    $user_package = IWJ_User_Package::get_user_package($post);
                    $status = $user_package->get_status();
                    $package = $user_package->get_package();
                    if($user_package->has_status('pending-payment')){
                        if($package){
	                        $remain_job          = ( $package->get_number_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : $package->get_number_job();
	                        $remain_renew_job    = ( $package->get_number_renew_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : $package->get_number_renew_job();
	                        $remain_featured_job = ( $package->get_number_featured_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : $package->get_number_featured_job();
                        }else{
                            $remain_job = $remain_renew_job = $remain_featured_job = __('N/A', 'iwjob');
                        }
                    }else{
	                    $remain_job          = ( $user_package->get_remain_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : $user_package->get_remain_job();
	                    $remain_renew_job    = ( $user_package->get_remain_renew_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : $user_package->get_remain_renew_job();
	                    $remain_featured_job = ( $user_package->get_remain_featured_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : $user_package->get_remain_featured_job();
                    }

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
                        <td class="package-job-remain text-center">
                            <span><?php echo $remain_job; ?></span>
                        </td>
                        <td class="package-job-remain text-center">
                            <span><?php echo $remain_renew_job; ?></span>
                        </td>
                        <td class="package-feature-remain text-center">
                           <span><?php echo $remain_featured_job; ?></span>
                        </td>
                        <td class="package-duration text-center">
                            <?php
                            if($package){
                                echo $package->get_expiry_title();
                            }else{
                                echo __('N/A', 'iwjob');
                            }
                            ?>
                        </td>
                        <td class="package-status iwj-status text-center">
                            <span data-toggle="tooltip" class="<?php echo $user_package->get_status(); ?>" title="<?php echo IWJ_User_Package::get_status_title($status); ?>"><?php echo iwj_get_status_icon($status); ?></span>
                        </td>
                    </tr>
                <?php
                }
                wp_reset_postdata();
            }else{ ?>
                <tr class="iwj-empty">
                    <td colspan="7"><?php echo __('You do not have any packages yet.', 'iwjob'); ?></td>
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