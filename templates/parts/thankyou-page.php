<?php
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
$order = IWJ_Order::get_order($order_id);
?>
<div class="iwj-thankyou-page">
    <div class="thankyou-icon"><img src="<?php echo IWJ_PLUGIN_URL; ?>/assets/img/thankyou.jpg" alt=""></div>
    <div class="thankyou-panel">
        <h3><?php echo __('Thank you for payment','iwjob');?></h3>
        <div class="success-txt">
            <span><i class="ion-ios-email-outline"></i></span>
            <p><?php echo __('An email receipt with details about your order has been sent to email address provided. Please keep it for your record','iwjob');?></p>
        </div>
        <ul>
            <li>
                <a class="iwj-btn-shadow iwj-btn-primary" href="<?php echo iwj_get_page_permalink('dashboard'); ?>"><i class="ion-home"></i> <?php echo __('Dashboard','iwjob'); ?></a>
            </li>
            <?php if($order){ ?>
            <li>
                <a class="iwj-btn-shadow iwj-btn-secondary" href="<?php echo $order->get_view_link(); ?>"><i class="ion-ios-eye"></i> <?php echo __('View Order','iwjob'); ?></a>
            </li>
            <?php }else{ ?>
            <li>
                <a class="iwj-btn-shadow iwj-btn-secondary" href="<?php echo get_home_url('/'); ?>"><i class="ion-home"></i> <?php echo __('Home','iwjob'); ?></a>
            </li>
            <?php } ?>
        </ul>
    </div>
</div>
