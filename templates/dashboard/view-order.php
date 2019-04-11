<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="iwj-order-details iwj-main-block">
    <?php
        $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
        $order = IWJ_Order::get_order($order_id);
        $author = $order->get_author();
        $order_type = $order->get_type();
        if ( $order ) { ?>
            <div class="order-logo">
                <?php
                $order_logo_url = get_template_directory_uri() . "/assets/images/logo-sticky.png";
                $order_logo = iwj_option('order_logo');
                if ($order_logo) {
                    $order_logo_url = esc_url( wp_get_attachment_url($order_logo[0]) );
                }
                ?>
                <img src="<?php echo $order_logo_url ; ?>" alt="">
            </div>
            <div class="order-number-date">
                <div class="order-number"><strong><?php echo __('Order Number : ',' iwjob'); ?></strong><?php echo $order->get_id(); ?></div>
                <div class="order-date"><strong><?php echo __('Date : ', 'iwjob'); ?></strong><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->get_created() ) ); ?></div>
                <div class="order-print"><a class="print-button" href="javascript:window.print()" title="<?php _e( 'Print this page', 'iwjob' ); ?>"><i class="fa fa-print"></i></a></div>
            </div>
            <div class="order-author">
                <div class="order-author-email"><?php echo $author->get_email(); ?></div>
                <div class="order-author-name"><strong><?php echo $author->get_display_name(); ?></strong></div>
            </div>
            <table class="order_details table">
                <tr>
                    <td><strong><?php echo __('Order Type', 'iwjob'); ?></strong></td>
                    <td><?php echo $order->get_type_title($order_type); ?></td>
                </tr>
                <tr>
                    <?php if($order_type == 1 || $order_type == 4 || $order_type == 6){?>
                        <td><strong><?php echo __('Package', 'iwjob'); ?></strong></td>
                        <td><?php echo $order->get_package_title(); ?></td>
                    <?php }elseif($order_type == 2 || $order_type == 3){ ?>
                        <td><strong><?php echo __('For Job', 'iwjob'); ?></strong></td>
                        <td><?php echo $order->get_job_title(); ?></td>
                    <?php } ?>
                </tr>
                <?php if($order->has_tax()){?>
                    <tr>
                        <td><strong><?php echo __('Price', 'iwjob'); ?></strong></td>
                        <td><?php echo iwj_system_price($order->get_sub_price(), $order->get_currency()); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo sprintf(__('Tax Price - %s%%', 'iwjob'), $order->get_tax_value()); ?></strong></td>
                        <td><?php echo iwj_system_price($order->get_tax_price(), $order->get_currency()); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo __('Total Price', 'iwjob'); ?></strong></td>
                        <td><?php echo iwj_system_price($order->get_price(), $order->get_currency()); ?></td>
                    </tr>
                <?php }else{ ?>
                    <tr>
                        <td><strong><?php echo __('Price', 'iwjob'); ?></td>
                        <td><?php echo iwj_system_price($order->get_package_price(), $order->get_currency()); ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td><strong><?php echo __('Status', 'iwjob'); ?></strong></td>
                    <td><?php echo $order->get_status_title($order->get_status()); ?></td>
                </tr>
                <?php if($order->has_status('completed')){ ?>
                    <tr>
                        <td><strong><?php echo __('Payment Method', 'iwjob'); ?></strong></td>
                        <td><?php echo $order->get_payment_method_title(); ?></td>
                    </tr>
                <?php } ?>
            </table>

            <?php if($order->has_status('pending-payment')){?>
                <p class="iwj-order-pay-message"><i><?php printf( __( 'This order has been created on %s. To pay it please use the following link: %2$s', 'iwjob' ), $order->get_created(get_option('date_format')), '<a href="' . esc_url( $order->get_pay_url() ) . '">' . __( 'pay', 'iwjob' ) . '</a>' ); ?></i></p>
            <?php } ?>

            <?php if(iwj_option('order_infomation')){ ?>
                <p><strong><?php echo __('Additional Information:', 'iwjob'); ?></strong></p>
                <p><?php echo iwj_option('order_infomation', 'iwjob'); ?></p>
            <?php } ?>

            <div class="clear"></div>
        <?php } ?>
</div>
