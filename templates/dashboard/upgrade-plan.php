<?php
    $user = IWJ_User::get_user();
	$packages_query = IWJ_Plan::get_query_plans();
    $data_plugin_options = array(
        "navigation"=>false,
        "paginationNumbers"=>false,
        "items"=>4,
        "itemsDesktop"=>array( 1199,4),
        "itemsDesktopSmall"=>array( 1199,3),
        "itemsTablet"=>array( 768,2),
        "itemsMobile"=>array( 600,1),
    );

    wp_enqueue_style('owl-carousel');
    wp_enqueue_style('owl-theme');
    wp_enqueue_style('owl-transitions');
    wp_enqueue_script('owl-carousel');

    $type = isset( $_GET['type'] ) ? $_GET['type'] : '';
    $notice = isset( $_GET['notice'] ) ? $_GET['notice'] : '';
	if ( $notice ) {
		if ( $type == 'featured' ) {
			$message = __( 'You have reached the limit of featured listings. Please upgrade membership plan to continuing', 'iwjob' );
		} else {
			$message = __( 'You have reached the limit of listings. Please upgrade membership plan to continuing', 'iwjob' );
		}
		?>
		<div class="alert iwj-alert alert-info">
			<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
			<i class="fa fa-info-circle" style="margin-right: 5px; font-size: 15px;"></i> <?php echo $message; ?>
		</div>
	<?php } ?>
<div class="iwj-user-packages iwj-main-block">
    <div class="iwj-plans-table iwj-table-overflow-x">
        <form action="" method="POST">
        <div class="iwj-pricing-tables style2">
            <div class="owl-carousel" data-plugin-options="<?php echo htmlspecialchars(json_encode($data_plugin_options)); ?>">
                <?php
                if($packages_query->have_posts()) {

                    while ($packages_query->have_posts()) {
                        $packages_query->the_post();
                        $package = IWJ_Plan::get_package(get_post());
                        $type_package = $package->get_type_package();
                        $button_disable = false;
                        if ( !$package->can_upgrade($user, $type)) {
                            $button_disable = true;
                        }

                        echo '<div class="pricing-item ' . $type_package . '">';
                        echo '<div class="item-top text-center">';
                        echo '<div class="item-top-content">';

                        if ($type_package == 'popular') {
                            echo '<div class="package_label">' . esc_html__( "Hot", "iwjob" ) . '</div>';
                        }

	                    if ($package->get_price()) {
		                    echo '<div class="price"> ' . iwj_system_price($package->get_price()) . '</div>';
	                    } else echo '<div class="price"> ' . iwj_system_price(0) . '</div>';

	                    echo '</div>';

                        if ($package->get_title()) {
                            echo '<h3 class="title"> ' . $package->get_title() . '</h3>';
                        }

                        echo '</div>';
                        echo '<div class="item-bottom">';
	                    $jobs = ( $package->get_number_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : $package->get_number_job();
                        $jobs_class = '';
                        $features = ( $package->get_number_featured_job() == -1 ) ? __( 'Unlimited', 'iwjob' ) : $package->get_number_featured_job();
	                    $renews = ( $package->get_number_renew_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : $package->get_number_renew_job();
                        $support_service = $package->get_job_support();
	                    echo '<ul>';
	                    if ( $jobs == 'Unlimited' ) {
		                    $jobs_class .= 'unlimited';
		                    echo '<li class="package-posting">Listing posting: <span class="' . $jobs_class . '">' . esc_html__( 'Unlimited', 'iwjob' ) . '</span></li>';
	                    } else {
		                    echo '<li class="package-posting">Listing posting: <span class="' . $jobs_class . '">' . esc_attr__( $jobs ) . '</span></li>';
	                    }
                        $unit = $package->get_expiry_unit();
                        $expiry = $package->get_expiry();
                        if ($expiry) {
                            echo '<li class="package-duration">';
                            switch ($unit) {
                                case 'day':
                                    echo sprintf(_n('Duration: <span>%d Day</span>', 'Duration: <span>%d Days</span>', $expiry, 'iwjob'), $expiry);
                                    break;
                                case 'month':
                                    echo sprintf(_n('Duration: <span>%d Month</span>', 'Duration: <span>%d Months</span>', $expiry, 'iwjob'), $expiry);
                                    break;
                                case 'year':
                                    echo sprintf(_n('Duration: <span>%d Year</span>', 'Duration: <span>%d Years</span>', $expiry, 'iwjob'), $expiry);
                                    break;
                            }
                            echo '</li>';
                        }

	                    $listing_f_class = ( $features == 'Unlimited' ) ? 'unlimited' : '';
                        echo '<li class="package-features">' . sprintf(_n('Listing Feature: <span class="'.$listing_f_class.'">%s</span>', 'Listing Feature: <span class="'.$listing_f_class.'">%s</span>', $features, 'iwjob'), $features) . '</li>';

	                    $listing_renew_class = ( $renews == 'Unlimited' ) ? 'unlimited' : '';
                        if ($renews) {
                            echo '<li class="package-renews">' . sprintf(_n('Listing Renew: <span class="'.$listing_renew_class.'">%s</span>', 'Listing Renew: <span class="'.$listing_renew_class.'">%s</span>', $renews, 'iwjob'), $renews) . '</li>';
                        } else {
                            echo '<li class="package-renews">' . esc_html__('Listing Renew: ', 'iwjob') . '<span><i class="ion-android-cancel"></i></span></li>';
                        }
                        if ($support_service) {
                            echo '<li class="package-support">' . esc_html__('Support Service: ', 'iwjob') . '<span>24/7</span></li>';
                        } else {
                            echo '<li class="package-support">' . esc_html__('Support Service: ', 'iwjob') . '<span><i class="ion-android-cancel"></i></span></li>';
                        }
                        echo '</ul>';

                        echo '<div class="choose-package"><button type="submit" class="buy-now" name="plan_id" value="' . $package->get_id() . '" '.($button_disable ? 'disabled' : '').' >' . esc_html__("Choose Package", 'iwjob') . '</button></div>';

                        echo '</div>';
                        echo '</div>';
                    }
                    wp_reset_postdata();
                }
                ?>
            </div>
        </div>
        <?php wp_nonce_field( 'iwj-select-plan', 'iwj-action'); ?>
        </form>
        <div class="clearfix"></div>
    </div>
</div>