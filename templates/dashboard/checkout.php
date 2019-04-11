<form action="" method="post" class="iwj-form-2 iwj-checkout-form iwj-main-block">
    <div class="iwj-order-payment">
        <div class="iwj-order">
            <?php
            $cart = new IWJ_Cart();
            $item    = $cart->get( 'item' );
            $package = IWJ_Plan::get_package( $item );
            $price = $cart->get_total_price();
            ?>
            <h3><?php echo $package->get_title(); ?></h3>
            <div class="iwj-order-price">
                <?php
                echo '<div>';
                echo sprintf(__('<span class="title">Price</span> <span>%s</span>', 'iwjob'), iwj_system_price($cart->get_price()));
                echo '<br/>';
                $tax_value = iwj_option('tax_value');
                if($tax_value) {
                    $tax_value = iwj_option('tax_value');
                    echo sprintf(__('<span class="title">Tax - %s%%</span> <span>%s</span>', 'iwjob'), $tax_value, iwj_system_price($cart->get_tax_price()));
                    echo '<br/>';

                    echo sprintf(__('<span class="title">Total Price</span> <span>%s</span>', 'iwjob'), iwj_system_price($cart->get_total_price()));
                }

                echo '</div>';
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

    <?php do_action('iwj_checkout_before_payment_btn', $cart); ?>

    <?php wp_nonce_field( 'iwj-checkout', 'iwj-action'); ?>

    <input type="hidden" name="price" value="<?php echo $cart->get_price(); ?>">
    <input type="hidden" name="currency" value="<?php echo $cart->get_currency(); ?>">

    <div class="iwj-respon-msg iwj-hide"></div>
    <div class="iwj-button-loader">
        <button type="button" class="iwj-btn iwj-btn-icon iwj-btn-primary iwj-payment-btn" <?php echo $price > 0 ? 'disabled' : ''; ?>><?php echo __('<i class="ion-android-send"></i> Continue', 'iwjob'); ?></button>
    </div>
</form>