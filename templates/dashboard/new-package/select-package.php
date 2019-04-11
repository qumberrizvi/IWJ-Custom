<?php
    $package_query = IWJ_Package::get_query_packages();
    $select_package_name = isset($_GET['package']) ? $_GET['package'] : '';
    $select_package = null;
?>
<div class="iwj-select-package">
	
	<form method="post" action="" class="iwj-select-package-form">
		<h3 class="iwj-title-table"><?php echo __('Choose package', 'iwjob');?></h3>
        <div class="iwj-table-overflow-x">
            <table class="table">
                <tr class="package-heading">
                    <th class="package-id"><?php echo __('Select', 'iwjob'); ?></th>
                    <th colspan="2"><?php echo __('Title', 'iwjob'); ?></th>
                    <th><?php echo __('Price', 'iwjob'); ?></th>
                    <th><?php echo __('Classes', 'iwjob'); ?></th>
                    <th><?php echo __('Feature Classes', 'iwjob'); ?></th>
                    <th><?php echo __('Durations', 'iwjob'); ?></th>
                </tr>
                <?php if($package_query->have_posts()) { ?>
                    <?php
                    while ($package_query->have_posts()) {
                        $package_query->the_post();
                        $post = get_post();
                        $package = IWJ_Package::get_package($post);
                        $id = 'input-radio-' . rand(100, 99999);
                        if($package->can_buy()){
                            if($post->post_name == $select_package_name){
                                $checked = $post->post_name == $select_package_name ? 'checked="checked"' : '';
                                $select_package = $package;
                            }else{
                                $checked = '';
                            }
                            ?>
                            <tr class="package-item">
                                <td class="package-id iwj-input-radio">
                                    <input id="<?php echo esc_attr($id); ?>" class="custom-input-radio" type="radio" name="package" value="<?php echo $package->get_id(); ?>" <?php echo $checked; ?>><label for="<?php echo esc_attr($id); ?>"></label>
                                </td>
                                <td class="package-title" colspan="2">
                                    <h3 class="title"><?php echo $package->get_title(); ?></h3>
                                </td>
                                <td class="package-price">
                                    <?php
                                    echo iwj_system_price($package->get_price());
                                    ?>
                                </td>
                                <td class="package-job">
                                    <?php
                                    echo ( $package->get_number_job() == - 1 ) ? esc_html__( 'Unlimited', 'iwjob' ) : (int) $package->get_number_job();
                                    ?>
                                </td>
                                <td class="package-featured-job">
                                    <?php
                                    echo ( $package->get_number_featured_job() == -1 ) ? esc_html__( 'Unlimited', 'iwjob' ) : (int) $package->get_number_featured_job();
                                    ?>
                                </td>
                                <td class="package-duration">
                                    <?php
                                    echo $package->get_expiry_title();
                                    ?>
                                </td>
                            </tr>
                        <?php
                        }
                    }
                    wp_reset_postdata();
                }else{ ?>
                    <tr class="iwj-empty">
                        <td colspan="6"><?php echo __('No Package found', 'iwjob'); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
        <?php if(!iwj_woocommerce_checkout()){ ?>
        <div class="iwj-order-payment <?php echo $select_package ? '' : 'iwj-hide'; ?>">
            <div class="iwj-order">
                <h3><?php echo __('Order Summary', 'iwjob'); ?></h3>
                <div class="iwj-order-price">
                    <?php
                    if($select_package){
                        iwj_get_template_part('parts/order-price', array('package' => $select_package));
                    }
                    ?>
                </div>
            </div>
            <div class="iwj-payments <?php echo $select_package ? '' : 'iwj-hide'; ?>">
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
        </div>
        <?php } ?>

        <div class="iwj-respon-msg iwj-hide"></div>
		<?php wp_nonce_field( 'iwj-new-package', 'iwj-security'); ?>
        <input type="hidden" name="price" value="<?php echo $select_package ? $select_package->get_price() : ''; ?>">
        <input type="hidden" name="currency" value="<?php echo iwj_get_system_currency(); ?>">
        <input type="hidden" name="order_name" value="<?php echo __('Package Payment', 'iwjob'); ?>">
        <div class="iwj-button-loader">
            <button type="button" class="iwj-btn-shadow iwj-btn-primary iwj-btn-icon iwj-payment-btn" <?php echo !iwj_woocommerce_checkout() ? 'disabled' : ''; ?>><?php echo __('<i class="ion-android-send"></i> Continue', 'iwjob'); ?></button>
        </div>
	</form>
</div>