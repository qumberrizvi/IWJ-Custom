<?php
    $package_query = IWJ_Package::get_query_packages();
	$user = IWJ_User::get_user();
	$user_package_ids = $user->get_user_package_ids();
	$has_user_package = false;
	$job_id = isset($_GET['job-id']) ? $_GET['job-id'] : '';
?>
<div class="iwj-sjob-step-package">
	<form method="post" action="" class="iwj-select-package-form">
        <?php ob_start(); ?>
        <h3 class="iwj-title-table"><?php echo __('Your packages', 'iwjob');?></h3>
        <div class="iwj-table-overflow-x">
            <table>
                <tr>
                    <th class="text-center"><?php echo __('Select', 'iwjob');?></th>
                    <th><?php echo __('Title', 'iwjob');?></th>
                    <th class="text-center"><?php echo __('Classes Remaining', 'iwjob');?></th>
                    <th class="text-center"><?php echo __('Features Remaining', 'iwjob');?></th>
                    <th class="text-center"><?php echo __('Renew Remaining', 'iwjob');?></th>
                    <th><?php echo __('Duration', 'iwjob');?></th>
                </tr>
                <?php
                if($user_package_ids){ ?>
                    <?php
                    foreach ($user_package_ids as $user_package_id){
                        $user_package = IWJ_User_Package::get_user_package($user_package_id);
                        $package = $user_package->get_package();
                        $id = 'package-' . $user_package->get_id();
                        if($user_package->can_submit()) {
                            $has_user_package = true;
                            ?>
                            <tr class="package-item">
                                <td class="package-title iwj-input-radio">
                                    <input id="<?php echo esc_attr($id); ?>" class="custom-input-radio" type="radio" name="user_package" value="<?php echo $user_package->get_id(); ?>"><label for="<?php echo esc_attr($id); ?>"></label>
                                </td>
                                <td class="package-title">
                                    <h3 class="title"><?php echo $package ? $package->get_title() : __('N/A', 'iwjob'); ?></h3>
                                </td>
                                <td class="package-remain-job text-center">
                                    <?php
                                    echo ( $user_package->get_remain_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : (int) $user_package->get_remain_job();
                                    ?>
                                </td>
                                <td class="package-remain-featured-job text-center">
                                    <?php
                                    echo ( $user_package->get_remain_featured_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : (int) $user_package->get_remain_featured_job();
                                    ?>
                                </td>
                                <td class="package-remain-renew-job text-center">
                                    <?php
                                    echo ( $user_package->get_remain_renew_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : (int) $user_package->get_remain_renew_job();
                                    ?>
                                </td>
                                <td class="package-duration">
                                    <?php echo $package ? $package->get_expiry_title() : __('N/A', 'iwjob'); ?>
                                </td>
                            </tr>
                        <?php
                        }
                    }
                    ?>
                <?php
                }
                ?>
            </table>
        </div>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        if($has_user_package){
            echo $html;
        }
        ?>

        <h3 class="iwj-title-table"><?php echo __('Select Package', 'iwjob');?></h3>
        <div class="iwj-table-overflow-x">
            <table>
                <tr>
                    <th class="text-center"><?php echo __('Select', 'iwjob'); ?></th>
                    <th><?php echo __('Title', 'iwjob'); ?></th>
                    <th><?php echo __('Price', 'iwjob'); ?></th>
                    <th class="text-center"><?php echo __('Classes Posting', 'iwjob'); ?></th>
                    <th class="text-center"><?php echo __('Feature Classes', 'iwjob'); ?></th>
                    <th class="text-center"><?php echo __('Renew Classes', 'iwjob'); ?></th>
                    <th><?php echo __('Duration', 'iwjob'); ?></th>
                </tr>
                <?php if($package_query->have_posts()) { ?>
                    <?php
                    while ($package_query->have_posts()) {
                        $package_query->the_post();
                        $post = get_post();
                        $package = IWJ_Package::get_package($post);
                        $id = 'input-radio-2' . rand(100, 99999);
                        if($package->can_buy()){
                            ?>
                            <tr class="package-item">
                                <td class="package-id iwj-input-radio">
                                    <input id="<?php echo esc_attr($id); ?>" class="custom-input-radio" type="radio" name="package" value="<?php echo $package->get_id(); ?>"><label for="<?php echo esc_attr($id); ?>"></label>
                                </td>
                                <td class="package-title">
                                    <h3 class="title"><?php echo $package->get_title(); ?></h3>
                                </td>
                                <td class="package-price">
                                    <?php
                                    echo iwj_system_price($package->get_price());
                                    ?>
                                </td>
                                <td class="package-job text-center">
                                    <?php
                                    echo ( $package->get_number_job() == - 1 ) ? esc_html__( 'Unlimited', 'iwjob' ) : (int) $package->get_number_job();
                                    ?>
                                </td>
                                <td class="package-featured-job text-center">
                                    <?php
                                    echo ( $package->get_number_featured_job() == - 1 ) ? esc_html__( 'Unlimited', 'iwjob' ) : (int) $package->get_number_featured_job();
                                    ?>
                                </td>
                                <td class="package-renew-job text-center">
                                    <?php
                                    echo ( $package->get_number_renew_job() == - 1 ) ? esc_html__( 'Unlimited', 'iwjob' ) : (int) $package->get_number_renew_job();
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
        <div class="iwj-order-payment iwj-hide">
            <div class="iwj-order">
                <h3><?php echo __('Order Summary', 'iwjob'); ?></h3>
                <div class="iwj-order-price">

                </div>
            </div>
            <div class="iwj-payments iwj-hide">
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

		<?php wp_nonce_field( 'iwj-select-package', 'iwj-submit-job'); ?>
        <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
        <input type="hidden" name="price" value="">
        <input type="hidden" name="currency" value="<?php echo iwj_get_system_currency(); ?>">
        <input type="hidden" name="order_name" value="<?php echo __('Package Payment', 'iwjob'); ?>">
        <div class="iwj-respon-msg iwj-hide"></div>
        <div class="iwj-button-loader">
            <button type="button" class="iwj-btn iwj-btn-primary iwj-btn-icon iwj-payment-btn" <?php echo !iwj_woocommerce_checkout() ? 'disabled' : ''; ?>><?php echo __('<i class="ion-android-send"></i> Continue', 'iwjob'); ?></button>
        </div>
	</form>
</div>