<?php
do_action('iwj_message', 'make-featured');
?>
<form action="" method="post" class="iwj-form-2 iwj-renew-job-form">
    <div class="iwj-order-payment">
        <div class="iwj-order">
            <h3><?php echo sprintf(__('Renew Job: %s', 'iwjob'), $job->get_title()); ?></h3>
            <div class="iwj-order-price">
                <?php
                if($user_package && ($user_package->get_remain_renew_job() > 0 || $user_package->get_remain_renew_job() == -1)){
                    $price = 0;
	                $remain_renew_job = ( $user_package->get_remain_renew_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : $user_package->get_remain_renew_job();
                    ?>
                    <div class="iwj-your-package">
                        <div><?php echo sprintf(__('Job Package: %s', 'iwjob'), $user_package->get_package_title()); ?></div>
                        <div><?php echo sprintf(__('Renew Classes Remaining : %s', 'iwjob'), $remain_renew_job); ?></div>
                    </div>
                    <?php
                }else{
                    echo '<div>';
                    $total_price = $price = (int)iwj_option('renew_job_price');
                    $tax_price = iwj_get_tax_price($price);
                    if($tax_price !== false){
                        $total_price += $tax_price;
                    }
                    echo sprintf(__('<span class="title">Price</span> <span>%s</span>', 'iwjob'), iwj_system_price($price));
                    echo '<br/>';
                    if($tax_price !== false) {
                        $tax_value = iwj_option('tax_value');
                        echo sprintf(__('<span class="title">Tax - %s%%</span> <span>%s</span>', 'iwjob'), $tax_value, iwj_system_price($tax_price));
                        echo '<br/>';
                    }
                    echo sprintf(__('<span class="title">Total Price</span> <span>%s</span>', 'iwjob'), iwj_system_price($total_price));
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        <?php if($price > 0){ ?>
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

    <input type="hidden" name="id" value="<?php echo $job->get_id(); ?>">
    <?php wp_nonce_field( 'iwj-renew-job', 'iwj-security'); ?>

    <div class="iwj-respon-msg iwj-hide"></div>
    <div class="iwj-button-loader">
        <button type="button" class="iwj-btn iwj-btn-icon iwj-btn-primary iwj-payment-btn" <?php echo $price > 0 ? 'disabled' : ''; ?>><?php echo __('<i class="ion-android-send"></i> Continue', 'iwjob'); ?></button>
    </div>
</form>