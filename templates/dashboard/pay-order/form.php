<div class="iwj-select-payment">
    <form method="post" action="" class="iwj-pay-order-form">
        <div class="iwj-order-payment">
            <div class="iwj-order">
                <h3><?php echo __('Order Summary', 'iwjob'); ?></h3>
                <div class="iwj-order-price">
                    <?php if($order->get_type() == 1){
                        $package = $order->get_package();
                        ?>
                        <div class="package-price">
                            <span class="title"><?php echo sprintf(__('Package %s', 'iwjob'), $package->get_title()); ?>:</span>
                            <span><?php echo iwj_system_price($order->get_package_price(), $order->get_currency()); ?></span>
                        </div>
                    <?php } ?>
                    <?php if($order->get_type() == 2 || ($order->get_type() == 1 && $order->get_featured_price() > 0)){
                        $job = $order->get_job();
                        ?>
                        <div class="featured-price">
                            <span class="title"><?php echo sprintf(__('Make Featured %s', 'iwjob'), '<a href="'.$job->permalink().'">'.$job->get_title().'</a>'); ?>:</span>
                            <span><?php echo iwj_system_price($order->get_featured_price(), $order->get_currency()); ?></span>
                        </div>
                    <?php } ?>
                    <?php if($order->get_type() == 3){
                        $job = $order->get_job();
                        ?>
                        <div class="renew-price">
                            <span class="title"><?php echo sprintf(__('Renew %s', 'iwjob'), '<a href="'.$job->permalink().'">'.$job->get_title().'</a>'); ?>:</span>
                            <span><?php echo iwj_system_price($order->get_renew_price(), $order->get_currency()); ?></span>
                        </div>
                    <?php } ?>
                    <?php
                    //resume package
                    if($order->get_type() == '4'){
                        $package = $order->get_package();
                        ?>
                        <div class="package-price"><span class="title"><?php echo sprintf(__('Resume Package %s', 'iwjob'), $package->get_title()); ?>:</span><span><?php echo iwj_system_price($order->get_package_price(), $order->get_currency()); ?></span></div>
                        <?php
                    }

                    //apply job package
                    if($order->get_type() == '6'){
	                    $package = $order->get_package();
	                    ?>
						<div class="package-price"><span class="title"><?php echo sprintf(__('Resume Package %s', 'iwjob'), $package->get_title()); ?>:</span><span><?php echo iwj_system_price($order->get_package_price(), $order->get_currency()); ?></span></div>
	                    <?php
                    }
                    ?>
                    <div class="total-price"><span class="title"><?php echo __('Total price', 'iwjob'); ?>:</span><span><?php echo iwj_system_price($order->get_price(), $order->get_currency()); ?></span></div>
                </div>
            </div>
            <?php if($order->get_price() > 0){ ?>
            <div class="iwj-payments">
                <h3><?php echo __('Choose Payment', 'iwjob'); ?></h3>
                <div class="iwj-payments-content">
                    <?php
                    $payment_gateways = IWJ()->payment_gateways->get_available_payment_gateways();
                    if($payment_gateways) {
                        foreach ($payment_gateways as $payment_gateway) {
                            $id = 'payment-' . $payment_gateway->id;
                            ?>
                            <div class="payment-method">
                                <input id="<?php echo esc_attr($id); ?>" class="custom-input-radio" type="radio"
                                       name="payment_method" value="<?php echo $payment_gateway->id; ?>">
                                <label for="<?php echo esc_attr($id); ?>"></label><span><?php echo $payment_gateway->get_title(); ?></span>
                                <div class="payment-description">
                                    <?php echo $payment_gateway->get_description(); ?>
                                </div>
                            </div>
                        <?php
                        }
                    }else{
                        ?>
                        <div class="payment-method">
                            <?php
                            echo __('Please active payment gateway.', 'iwjob');
                            ?>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <?php } ?>
        </div>
		<?php wp_nonce_field( 'iwj-pay-order', 'iwj-security'); ?>
        <input type="hidden" name="order_id" value="<?php echo $order->get_id(); ?>">
        <input type="hidden" name="order_key" value="<?php echo isset($_GET['key']) ? esc_attr($_GET['key']) : ''; ?>">
        <input type="hidden" name="price" value="<?php echo $order->get_price(); ?>">
        <input type="hidden" name="currency" value="<?php echo $order->get_currency(); ?>">
        <input type="hidden" name="order_name" value="<?php echo $order->get_title(); ?>">

        <div class="iwj-respon-msg iwj-hide"></div>
        <div class="iwj-button-loader">
            <button type="button" class="iwj-btn iwj-btn-icon iwj-btn-primary iwj-payment-btn" <?php echo $order->get_price() > 0 ? 'disabled' : ''; ?>><?php echo __('<i class="ion-android-send"></i> Continue', 'iwjob'); ?></button>
        </div>
	</form>
</div>