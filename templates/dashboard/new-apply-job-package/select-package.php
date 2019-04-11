<?php
$package_query = IWJ_Apply_Job_Package::get_packages();
?>
<div class="iwj-select-package">
	<form method="post" action="" class="iwj-select-package-form">
		<h3 class="iwj-title-table"><?php echo __( 'Choose new apply job package', 'iwjob' ); ?></h3>
		<div class="iwj-table-overflow-x">
			<table>
				<tr class="package-heading">
					<th class="package-id"><?php echo __( 'Select', 'iwjob' ); ?></th>
					<th colspan="2"><?php echo __( 'Title', 'iwjob' ); ?></th>
					<th><?php echo __( 'Price', 'iwjob' ); ?></th>
					<th><?php echo __( 'Apply Job', 'iwjob' ); ?></th>
				</tr>
				<?php if ( $package_query->have_posts() ) { ?>
					<?php
					while ( $package_query->have_posts() ) {
						$package_query->the_post();
						$post    = get_post();
						$package = IWJ_Apply_Job_Package::get_package( $post );
						$id      = 'input-radio-' . rand( 100, 99999 );
						if ( $package->can_buy() ) { ?>
							<tr class="package-item">
								<td class="package-id">
									<input id="<?php echo esc_attr( $id ); ?>" class="custom-input-radio" type="radio" name="package" value="<?php echo $package->get_id(); ?>"><label for="<?php echo esc_attr( $id ); ?>"></label>
								</td>
								<td class="package-title" colspan="2">
									<h3 class="title"><?php echo $package->get_title(); ?></h3>
								</td>
								<td class="package-price">
									<?php echo iwj_system_price( $package->get_price() ); ?>
								</td>
								<td class="package-job">
									<?php echo (int) $package->get_number_apply(); ?>
								</td>
							</tr>
							<?php
						}
					}
					wp_reset_postdata();
				} else { ?>
					<tr class="iwj-empty">
						<td colspan="4"><?php echo __( 'No Package found', 'iwjob' ); ?></td>
					</tr>
				<?php } ?>
			</table>
		</div>
		<div class="iwj-order-payment iwj-hide">
			<div class="iwj-order">
				<h3><?php echo __( 'Order Summary', 'iwjob' ); ?></h3>
				<div class="iwj-order-price">
				</div>
			</div>
			<div class="iwj-payments iwj-hide">
				<h3><?php echo __( 'Choose Payment', 'iwjob' ); ?></h3>
				<div class="iwj-payments-content">
					<?php
					$payment_gateways = IWJ()->payment_gateways->get_available_payment_gateways();
					if ( $payment_gateways ) {
						foreach ( $payment_gateways as $payment_gateway ) {
							$id = 'payment-' . $payment_gateway->id;
							?>
							<div class="payment-method">
								<input id="<?php echo esc_attr( $id ); ?>" class="custom-input-radio" type="radio"
									   name="payment_method" value="<?php echo $payment_gateway->id; ?>">
								<label for="<?php echo esc_attr( $id ); ?>"></label><span><?php echo $payment_gateway->get_title(); ?></span>
								<div class="payment-description">
									<?php echo $payment_gateway->get_description(); ?>
								</div>
							</div>
							<?php
						}
					} else { ?>
						<div class="payment-method">
							<?php echo __( 'Please active payment gateway.', 'iwjob' ); ?>
						</div>
						<?php
					} ?>
				</div>
			</div>
		</div>
		<div class="iwj-respon-msg iwj-hide"></div>
		<?php wp_nonce_field( 'iwj-new-apply-job-package', 'iwj-security' ); ?>
		<input type="hidden" name="price" value="">
		<input type="hidden" name="currency" value="<?php echo iwj_get_system_currency(); ?>">
		<input type="hidden" name="order_name" value="<?php echo __( 'Package Payment', 'iwjob' ); ?>">
		<div class="iwj-button-loader">
			<button type="button" class="iwj-btn iwj-btn-icon iwj-btn-primary iwj-payment-btn" <?php echo ! iwj_woocommerce_checkout() ? 'disabled' : ''; ?>><?php echo __( '<i class="ion-android-send"></i> Continue', 'iwjob' ); ?></button>
		</div>
	</form>
</div>