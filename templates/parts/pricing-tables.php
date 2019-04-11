<?php if ($packages) {
    if ($atts['hide_free_package']) $item = 3; else $item = 4;
    $data_plugin_options = array(
        "navigation"=>false,
        "paginationNumbers"=>false,
        "items"=>$item,
        "itemsDesktop"=>array( 1199,$item),
        "itemsDesktopSmall"=>array( 1199,3),
        "itemsTablet"=>array( 768,2),
        "itemsMobile"=>array( 600,1),
    );

    wp_enqueue_style('owl-carousel');
    wp_enqueue_style('owl-theme');
    wp_enqueue_style('owl-transitions');
    wp_enqueue_script('owl-carousel');
    $style = (isset($atts['style']) && $atts['style']) ?$atts['style']:'';

    ?>
    <?php switch ($style){
        case 'style1' :
            ?>
            <div class="iwj-pricing-tables <?php echo $atts['class']; echo $atts['style']; ?>">
                <div class="owl-carousel" data-plugin-options="<?php echo htmlspecialchars(json_encode($data_plugin_options)); ?>">
                    <?php
                    foreach ($packages as $package){
                        $img_src = '';
                        $thumbnail_id = get_post_thumbnail_id($package->get_id());
                        if($thumbnail_id){
                            $image = wp_get_attachment_image_src($bg_image, 'full');
                            if ( $image ) {
                                $img_src = $image[0];
                            }
                        }
                        if (!$img_src) {
                            $img_src = IWJ_PLUGIN_URL.'/assets/img/package_bg.png';
                        }

                        $pricing_table_color = get_post_meta($package->get_id(), IWJ_PREFIX.'pricing_table_color', true);
                        if(!$pricing_table_color){
                            $pricing_table_color = '#34495E';
                        }

                        $active = $package->is_active();

                        echo '<div class="pricing-item '.($package->is_featured() ? 'featured-item' : '').' '.($active ? 'active-item' : '').'">';
                        echo '<div class="item-top" style="background-image: url('.$img_src.')">';
                        echo '<div class="item-top-bg" style=" background-color: '.$pricing_table_color.'"></div>';
                        echo '<div class="item-top-content">';
                        if($active){
                            echo '<span class="active-label">'.__('Active', 'iwjob').'</span>';
                        }
                        if ($package->is_featured()) {
                            echo '<div class="star"><i class="ion-android-star"></i><i class="ion-android-star"></i><i class="ion-android-star"></i></div>';
                        }
                        if ($package->get_sub_title()) {
                            echo '<div class="sub-title">'.$package->get_sub_title().'</div>';
                        }
                        if ($package->get_title()) {
                            echo '<h3 class="title"> '.$package->get_title().'</h3>';
                        }
                        if ($package->get_price()) {
                            echo '<div class="price"> '.iwj_system_price($package->get_price()).'</div>';
                        }
                        echo '</div>';
                        echo '</div>';
                        echo '<div class="item-bottom">';
	                    $jobs = ( $package->get_number_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : $package->get_number_job();
	                    $features = ( $package->get_number_featured_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : $package->get_number_featured_job();
	                    $renews = ( $package->get_number_renew_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : $package->get_number_renew_job();
                        $max_cat = $package->get_max_categories();
                        echo '<ul>';
                            echo '<li class="package-posting">'.sprintf(_n('<strong>%d</strong> Class posting', '<strong>%d</strong> Classes posting', $jobs, 'iwjob'), $jobs).'</li>';
                            echo '<li class="package-features">'.sprintf(_n('<strong>%d</strong> Feature job', '<strong>%d</strong> Feature Classes', $features, 'iwjob'), $features).'</li>';
                            echo '<li class="package-renews">'.sprintf(_n('<strong>%d</strong> Renew job', '<strong>%d</strong> Renew Classes', $renews, 'iwjob'), $renews).'</li>';

                        $unit = $package->get_job_expiry_unit();
                        $expiry = $package->get_job_expiry();
                        if ($expiry) {
                            echo '<li class="package-duration">';
                            switch ($unit){
                                case 'day':
                                    echo sprintf(_n('<strong>%d</strong> Day duration', '<strong>%d</strong> Days duration', $expiry, 'iwjob'), $expiry);
                                    break;
                                case 'month':
                                    echo sprintf(_n('<strong>%d</strong> Month duration', '<strong>%d</strong> Months duration', $expiry, 'iwjob'), $expiry);
                                    break;
                                case 'year':
                                    echo sprintf(_n('<strong>%d</strong> Year duration', '<strong>%d</strong> Years duration', $expiry, 'iwjob'), $expiry);
                                    break;
                            }
                            echo '</li>';
                        }
                        if($max_cat){
                            echo '<li class="package-categories">'.sprintf(_n('<strong>%d</strong> Category', '<strong>%d</strong> Subjects', $max_cat, 'iwjob'), $max_cat).'</li>';
                        }else{
                            echo '<li class="package-categories infinty">'.__('<strong class="ion-ios-infinite"></strong> Subjects', 'iwjob').'</li>';
                        }
                        echo '</ul>';

                        if($active){
                            echo '<a class="buy-now" href="'.$package->get_submit_job_url().'" style=" background-color: '.$pricing_table_color.'">'.__("Submit Now", 'iwjob').'</a>';
                        }else{
                            if($package->is_free()){
                                echo '<a class="buy-now" href="'.$package->get_buy_url().'" style=" background-color: '.$pricing_table_color.'">'.__("Choose Now", 'iwjob').'</a>';
                            }else{
                                echo '<a class="buy-now" href="'.$package->get_buy_url().'" style=" background-color: '.$pricing_table_color.'">'.__("Buy Now", 'iwjob').'</a>';
                            }
                        }

                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
            <?php break;
        case 'style2' :
            ?>
            <div class="iwj-pricing-tables <?php echo $atts['class']; echo $atts['style']; ?>">
                <div class="owl-carousel" data-plugin-options="<?php echo htmlspecialchars(json_encode($data_plugin_options)); ?>">
                    <?php
                    foreach ($packages as $package){

                        $type_package = $package->get_type_package();

                        echo '<div class="pricing-item '.$type_package.'">';
                        echo '<div class="item-top text-center">';
                        echo '<div class="item-top-content">';

                        if ( $type_package == 'popular'){
                            echo '<div class="package_label">' . esc_html__( "Hot", "iwjob" ) . '</div>';
                        }
	                    if ( $package->get_price() ) {
		                    echo '<div class="price"> ' . iwj_system_price( $package->get_price() ) . '</div>';
	                    } else {
		                    echo '<div class="price"> ' . iwj_system_price( 0 ) . '</div>';
	                    }
	                    echo '</div>';

                        if ($package->get_title()) {
                            echo '<h3 class="title"> '.$package->get_title().'</h3>';
                        }

                        echo '</div>';
                        echo '<div class="item-bottom">';
                        $jobs = ( $package->get_number_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : $package->get_number_job();
                        $jobs_class = '';
	                    $features        = ( $package->get_number_featured_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : $package->get_number_featured_job();
	                    $renews          = ( $package->get_number_renew_job() == - 1 ) ? __( 'Unlimited', 'iwjob' ) : $package->get_number_renew_job();
                        $support_service = $package->get_job_support();
                        echo '<ul>';
	                    if ( $jobs == 'Unlimited' ) {
		                    $jobs_class .= 'unlimited';
		                    echo '<li class="package-posting">Listing posting: <span class="' . $jobs_class . '">' . esc_html__( 'Unlimited', 'iwjob' ) . '</span></li>';
	                    } else {
		                    echo '<li class="package-posting">Listing posting: <span class="' . $jobs_class . '">' . esc_attr__( $jobs ) . '</span></li>';
	                    }
                        $unit = $package->get_job_expiry_unit();
                        $expiry = $package->get_job_expiry();
                        if ($expiry) {
                            echo '<li class="package-duration">';
                            switch ($unit){
                                case 'day':
                                    echo sprintf(_n('Listing duration: <span>%d Day</span>', 'Listing duration: <span>%d Days</span>', $expiry,  'iwjob'), $expiry);
                                    break;
                                case 'month':
                                    echo sprintf(_n('Listing duration: <span>%d Month</span>', 'Listing duration: <span>%d Months</span>', $expiry, 'iwjob'), $expiry);
                                    break;
                                case 'year':
                                    echo sprintf(_n('Listing duration: <span>%d Year</span>', 'Listing duration: <span>%d Years</span>', $expiry, 'iwjob'), $expiry);
                                    break;
                            }
                            echo '</li>';
                        }

	                    $listing_f_class = ( $features == 'Unlimited' ) ? 'unlimited' : '';
                        echo '<li class="package-features">'.sprintf(_n('Listing Feature: <span class="'.$listing_f_class.'">%d</span>', 'Listing Feature: <span class="'.$listing_f_class.'">%d</span>', $features, 'iwjob'), $features).'</li>';

	                    $listing_renew_class = ( $renews == 'Unlimited' ) ? 'unlimited' : '';
                        if ($renews) {
                            echo '<li class="package-renews">' . sprintf(_n('Listing Renew: <span class="'.$listing_renew_class.'">%d</span>', 'Listing Renew: <span class="'.$listing_renew_class.'">%d</span>', $renews, 'iwjob'), $renews) . '</li>';
                        } else {
                            echo '<li class="package-renews">'.esc_html__('Listing Renew: ', 'iwjob').'<span><i class="ion-android-cancel"></i></span></li>';
                        }
                        if($support_service){
                            echo '<li class="package-support">'.esc_html__('Support Service: ', 'iwjob').'<span>24/7</span></li>';
                        }else{
                            echo '<li class="package-support">'.esc_html__('Support Service: ', 'iwjob').'<span><i class="ion-android-cancel"></i></span></li>';
                        }
                        echo '</ul>';

                        echo '<div class="choose-package"><a class="buy-now" href="'.$package->get_buy_url().'">'.esc_html__("Choose Package", 'iwjob').'</a></div>';

                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        <?php
            break;
    } ?>
<?php } ?>