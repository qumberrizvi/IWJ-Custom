<?php
$user = IWJ_User::get_user();
$msg = isset($_GET['msg']) ? $_GET['msg'] : false;
if($msg){
    if($msg == '2'){
        $text =  __('Thank you, your plan has been unsupcription!', 'iwjob');
    }else{
        $text =  __('Congratulation, you have changed plan successfully!', 'iwjob');
    }
    ?>
    <div class="alert iwj-alert alert-info">
        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
        <i class="fa fa-info-circle" style="margin-right: 5px; font-size: 15px;"></i> <?php echo $text ?>
    </div>
<?php } ?>

<div class="iwj-current-plan iwj-main-block">
    <div class="iwj-current-plan iwj-properties-table">
        <div class="iwj-table-overflow-x">
            <table class="table">
                <thead>
                <tr>
                    <th width="20%"><?php echo __( 'Current Plan', 'iwjob' ); ?></th>
                    <th width="15%" class="text-center"><?php echo __( 'Classes remaining', 'iwjob' ); ?></th>
                    <th width="15%" class="text-center"><?php echo __( 'Featured remaining', 'iwjob' ); ?></th>
                    <th width="15%" class="text-center"><?php echo __( 'Renewal remaining', 'iwjob' ); ?></th>
                    <th width="15%" class="text-center"><?php echo __( 'Expiry Date', 'iwjob' ); ?></th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php
                        if ( $user->has_plan() ) {
                        $plan = $user->get_plan();
                        $jobs = $user->plan_get_jobs() == '-1' ? $user->plan_get_jobs(true) : $user->plan_get_jobs_available();
                        $featured_jobs = $user->plan_get_featured_jobs() == '-1' ? $user->plan_get_featured_jobs(true) : $user->plan_get_featured_jobs_available();
                        $renew_jobs = $user->plan_get_renew_jobs() == '-1' ? $user->plan_get_renew_jobs(true) : $user->plan_get_renew_jobs_available();
                        $plan_expiry = $user->plan_get_expiry_date() ? date_i18n(get_option('date_format'), $user->plan_get_expiry_date()) : __('Always available', 'iwjob');
                        ?>
                        <td><?php echo $plan->get_title(); ?></td>
                        <td class="text-center"><?php echo $jobs; ?></td>
                        <td class="text-center"><?php echo $featured_jobs; ?></td>
                        <td class="text-center"><?php echo $renew_jobs; ?></td>
                        <td class="text-center"><?php echo $plan_expiry; ?></td>
                        <?php }else{ ?>
                            <td colspan="6"><?php __('You do not have a plan yet', 'iwpropety')?></td>
                        <?php } ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="current-plan-btn">
        <?php
        $select_plan_link = add_query_arg(array('iwj_tab' => 'select-plan'), iwj_get_page_permalink('dashboard'));
		if($user->has_plan()){

            if($user->plan_is_active()){
                ?>
                <a href="<?php echo $select_plan_link; ?>" class="iwj-btn-primary iwj-change-plan"><?php echo __( '<i class="ion-ios-cart"></i>Change Plan', 'iwjob' ) ?></a>
                <?php if($user->has_subscription()){ ?>
                    <a href="<?php echo $user->get_cancel_subscription_url(); ?>" class="iwj-btn-primary iwj-cancel-subscription"><?php echo __( 'Cancel Subscription', 'iwjob' ) ?></a>
                <?php } ?>
            <?php }else{ ?>
                <a href="<?php echo add_query_arg(array('iwj-renew-plan'=> true), iwj_get_page_permalink('dashboard')); ?>" class="iwj-btn-primary iwj-renew-plan"><?php echo __( '<i class="ion-ios-cart"></i>Renew Package', 'iwjob' ) ?></a>
                <a href="<?php echo $select_plan_link; ?>" class="iwj-btn-primary iwj-change-plan"><?php echo __( '<i class="ion-ios-cart"></i>Change Plan', 'iwjob' ) ?></a>
            <?php }
        }else{ ?>
            <a href="<?php echo $select_plan_link; ?>" class="iwj-btn-primary iwj-change-plan"><?php echo __( '<i class="ion-ios-cart"></i>Select Plan', 'iwjob' ) ?></a>
        <?php }?>
    </div>

    <?php if($user->has_plan()){
        $plan_order_query = $user->get_current_plan_orders_query();
        if($plan_order_query->have_posts()) {
            ?>
            <h3><?php echo __('Orders history', 'iwjob'); ?></h3>
            <div class="iwj-plan-orders iwj-properties-table">
            <div class="iwj-table-overflow-x">
            <table class="table">
            <thead>
            <tr>
                <th width="10%"><?php echo __('Order ID', 'iwjob'); ?></th>
                <th width="20%" class="text-center"><?php echo __('Package', 'iwjob'); ?></th>
                <th width="15%" class="text-center"><?php echo __('Price', 'iwjob'); ?></th>
                <th width="15%" class="text-center"><?php echo __('Date', 'iwjob'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $plan = $user->get_plan();
            while ($plan_order_query->have_posts()) {
                $plan_order_query->the_post();
                $order = IWJ_Order::get_order();
                ?>
                <tr>
                    <td><a href="<?php echo $order->get_view_link(); ?>">#<?php echo $order->get_id(); ?></a></td>
                    <td class="text-center"><?php echo $plan->get_title(); ?></td>
                    <td class="text-center"><?php echo iwj_system_price($order->get_price(), $order->get_currency()); ?></td>
                    <td class="text-center"><?php echo $order->get_created(get_option('date_format')); ?></td>
                </tr>
                <?php
            }
            wp_reset_postdata();
        ?>

            </tbody>
            </table>
            </div>
            </div>
            <?php
        }
    }
    ?>
</div>
