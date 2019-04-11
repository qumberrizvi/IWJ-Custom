<?php
if($employers){
wp_enqueue_style('owl-carousel');
wp_enqueue_style('owl-theme');
wp_enqueue_style('owl-transitions');
wp_enqueue_script('owl-carousel');
$style = $atts['style'];
?>
    <?php
    switch ($style) {
        case 'style1':
            $data_plugin_options = array(
                "navigation"=>false,
                "autoHeight"=>true,
                "pagination"=>true,
                "autoPlay"=>($atts['auto_play'] ? true : false),
                "paginationNumbers"=>false,
                "singleItem"=>true,
                "navigationText" => array('<i class="ion-android-arrow-back"></i>', '<i class="ion-android-arrow-forward"></i>')
            );
            ?>
            <div class="iwj-employers-slider <?php echo $atts['class']; echo $atts['style']; ?>">
                <div class="iwj-employers-slider-inner">
                    <div class=" owl-carousel navigation-text-v1" data-plugin-options="<?php echo htmlspecialchars(json_encode($data_plugin_options)); ?>">
                        <div class="employer-items items-1">
                            <div class="row">
                                <?php
                                $item_per_slider = $atts['employers_per_slider'] ? $atts['employers_per_slider'] : 8;
                                $item_class =  'col-item col-md-3 col-sm-6 col-xs-12';
                                if($item_per_slider == '1'){
                                    $item_class =  'col-item col-md-12 col-sm-12 col-xs-12';
                                }elseif($item_per_slider == '2'){
                                    $item_class =  'col-item col-md-6 col-sm-6 col-xs-12';
                                }elseif($item_per_slider == '3'){
                                    $item_class =  'col-item col-md-4 col-sm-6 col-xs-12';
                                }elseif($item_per_slider == '4'){
                                    $item_class =  'col-item col-md-3 col-sm-6 col-xs-12';
                                }elseif($item_per_slider == '6'){
                                    $item_class =  'col-item col-md-4 col-sm-6 col-xs-12';
                                }

                                $i = 0;
                                $number_item = 12 % $item_per_slider;
                                foreach ($employers as $employer) :
                                    $clear = '';
                                    if(($i > 0) && ($item_per_slider >= 5) && $number_item != 0) {
                                        if($i % $number_item == 0){
                                            $clear = " clear";
                                        }
                                    }
                                    $total_jobs = $employer->total_jobs;
                                    $employer = IWJ_Employer::get_employer($employer);
                                    $author = $employer->get_author();

                                    $image = iwj_get_avatar( $author->get_id(), '120', '', $author->get_display_name(), array('img_size'=>'inwave-avatar2') );
                                    if($i > 0 && count($employers) > $i && $i % $item_per_slider == 0){
                                        echo '</div>
                                </div>
                                <div class="employer-items items-'.($i+1).'">
                                <div class="row">';
                                    }
                                    ?>
                                    <div class="<?php echo $item_class; echo $clear; ?>">
                                        <div class="employer-item" itemscope itemtype="http://schema.org/Organization">
                                            <div class="employer-image" itemprop="url"><a href="<?php echo $author->permalink(); ?>"><?php echo $image; ?></a></div>
                                            <h3 class="employer-title" itemprop="name"><a class="theme-color" href="<?php echo $author->permalink(); ?>"><?php echo $author->get_display_name(); ?></a></h3>
                                            <?php if ( $employer->get_headline() ) { ?>
                                                <h6 class="employer-headline">
                                                    <?php echo $employer->get_headline(); ?>
                                                </h6>
                                            <?php } ?>
                                            <div class="employer-locations"><?php echo $employer->get_locations_links(); ?></div>
                                            <h6 class="total-jobs"><a class="theme-bg-hover" href="<?php echo $author->permalink(); ?>"><strong class="number"><?php echo $total_jobs; ?></strong> <?php echo _n('Open Position', 'Open Positions', $total_jobs, 'iwjob'); ?></a></h6>
											<meta itemprop="address" content="<?php echo $employer->get_address(); ?>" />
											<meta itemprop="email" content="<?php echo $employer->get_email(); ?>" />
											<meta itemprop="telephone" content="<?php echo $employer->get_phone(); ?>" />
                                        </div>
                                    </div>
                                    <?php
                                    $i ++;
                                endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            break;

        case 'style2':
            $data_plugin_options = array(
                "navigation"=>true,
                "autoHeight"=>false,
                "pagination"=>false,
                "autoPlay"=>($atts['auto_play'] ? true : false),
                "paginationNumbers"=>false,
                "items"             => ($atts['items_desktop'] ? $atts['items_desktop'] : 1),
                "itemsDesktop"      => array( 1400, $atts['items_desktop'] ? $atts['items_desktop'] : 1 ),
                "itemsDesktopSmall" => array( 991, $atts['items_desktop_small'] ? $atts['items_desktop_small'] : 1 ),
                "itemsTablet"       => array( 768, $atts['items_tablet'] ? $atts['items_tablet'] : 1 ),
                "itemsMobile"       => array( 480, $atts['items_mobile'] ? $atts['items_mobile'] : 1 ),
                "navigationText" => array('<i class="ion-android-arrow-back"></i>', '<i class="ion-android-arrow-forward"></i>')
            );
            ?>
            <div class="iwj-employers-slider <?php echo $atts['class']; echo $atts['style']; ?>">
                <div class="title-block-carousel"><?php
                    $title = $atts['title_block'] ? $atts['title_block'] : 'Students';
                    _e($title, 'iwjob'); ?>
                </div>
                <div class="iwj-employers-slider-inner">
                    <div class=" owl-carousel navigation-text-v2" data-plugin-options="<?php echo htmlspecialchars(json_encode($data_plugin_options)); ?>">
                        <?php
                        $i = 0;
                        foreach ($employers as $employer) :
                            $total_jobs = $employer->total_jobs;
                            $employer = IWJ_Employer::get_employer($employer);
                            $author = $employer->get_author();
                            $image = iwj_get_avatar( $author->get_id(), '120', '', $author->get_display_name(), array('img_size'=>'inwave-avatar2') );
                            ?>
                            <div class="employer-item" itemscope itemtype="http://schema.org/Organization">
                                <div class="employer-image"><a href="<?php echo $author->permalink(); ?>"><?php echo $image; ?></a></div>
                                <h3 class="employer-title" itemprop="name"><a href="<?php echo $author->permalink(); ?>"><?php echo $author->get_display_name(); ?></a></h3>
								<meta itemprop="address" content="<?php echo $employer->get_address(); ?>" />
								<meta itemprop="email" content="<?php echo $employer->get_email(); ?>" />
								<meta itemprop="telephone" content="<?php echo $employer->get_phone(); ?>" />
                                <?php if ( $employer->get_headline() ) { ?>
                                    <h6 class="employer-headline">
                                        <?php echo $employer->get_headline(); ?>
                                    </h6>
                                <?php } ?>
                                <div class="employer-locations"><?php echo $employer->get_locations_links(); ?></div>
                                <?php
                                $average_rate = IWJ_Reviews::get_average_rate( $employer->get_id() );
                                if ( count( $average_rate ) ) { ?>
                                    <div class="iwj-box-rating" itemprop="aggregateRating"
										 itemscope itemtype="http://schema.org/AggregateRating">
										<meta itemprop="ratingValue" content="<?php echo $average_rate['average']; ?>" />
										<meta itemprop="reviewCount" content="<?php echo $average_rate['totals']; ?>" />
										<div class="iwj-count-rate" title="<?php esc_attr_e( $average_rate['average'], 'iwjob' ); ?>">
											<?php echo IWJ_Reviews::get_number_stars( $average_rate['average'] ); ?>
										</div>
										<div class="iwj-text-totals-rate">
											<?php echo sprintf(_n('rating %d', 'ratings %d',  $average_rate['totals'], 'iwjob'), $average_rate['totals']); ?>
										</div>
                                    </div>
                                <?php } ?>
                                <h6 class="total-jobs"><a class="theme-bg-hover" href="<?php echo $author->permalink(); ?>"><strong class="number"><?php echo $total_jobs; ?></strong> <?php echo _n('Open Position', 'Open Positions', $total_jobs, 'iwjob'); ?></a></h6>
                            </div>
                            <?php
                            $i ++;
                        endforeach; ?>
                    </div>
                </div>
            </div>
        <?php
        break;

        case 'style3':
            $data_plugin_options_v3 = array(
                "navigation"=>false,
                "autoHeight"=>false,
                "pagination"=>true,
                "autoPlay"=>($atts['auto_play'] ? true : false),
                "paginationNumbers"=>false,
                "items"             => ($atts['items_desktop'] ? $atts['items_desktop'] : 3),
                "itemsDesktop"      => array( 1400, $atts['items_desktop'] ? $atts['items_desktop'] : 3 ),
                "itemsDesktopSmall" => array( 991, $atts['items_desktop_small'] ? $atts['items_desktop_small'] : 2 ),
                "itemsTablet"       => array( 768, $atts['items_tablet'] ? $atts['items_tablet'] : 2 ),
                "itemsMobile"       => array( 640, $atts['items_mobile'] ? $atts['items_mobile'] : 1 ),
                "navigationText" => array('<i class="ion-android-arrow-back"></i>', '<i class="ion-android-arrow-forward"></i>')
            );
            ?>
            <div class="iwj-employers-slider <?php echo $atts['class']; echo $atts['style']; ?>">
                <div class="iwj-employers-slider-inner">
                    <div class=" owl-carousel" data-plugin-options="<?php echo htmlspecialchars(json_encode($data_plugin_options_v3)); ?>">
                        <?php
                        $i = 0;
                        foreach ($employers as $employer) :
                            $total_jobs = $employer->total_jobs;
                            $employer = IWJ_Employer::get_employer($employer);
                            $author = $employer->get_author();
                            $image = iwj_get_avatar( $author->get_id(), '120', '', $author->get_display_name(), array('img_size'=>'inwave-avatar2') );
                            $cover_image_url = '';
                            $cover_image = $employer->get_cover_image();
                            if ($cover_image) {
                                $cover_image = wp_get_attachment_image_src($cover_image, 'inwave-370-245');
                                $cover_image_url = $cover_image ? $cover_image[0] : '';
                            }
                            ?>
                            <div class="iwj-employer-item" itemscope itemtype="http://schema.org/Organization">
                                <div class="employer-item">
                                    <div class="employer-cover-image" style="background-image: url(<?php echo esc_url($cover_image_url); ?>)">
                                        <div class="employer-image"><a href="<?php echo $author->permalink(); ?>"><?php echo $image; ?></a></div>
                                    </div>
                                    <div class="employer-content">
                                        <div class="employer-content-inner">
                                            <h3 class="employer-title" itemprop="name"><a class="theme-color" href="<?php echo $author->permalink(); ?>"><?php echo $author->get_display_name(); ?></a></h3>
                                            <?php
                                            $average_rate = IWJ_Reviews::get_average_rate( $employer->get_id() );
                                            if ( count( $average_rate ) ) { ?>
                                                <div class="iwj-box-rating" itemprop="aggregateRating"
													 itemscope itemtype="http://schema.org/AggregateRating">
													<meta itemprop="ratingValue" content="<?php echo $average_rate['average']; ?>" />
													<meta itemprop="reviewCount" content="<?php echo $average_rate['totals']; ?>" />
                                                    <div class="iwj-count-rate" title="<?php esc_attr_e( $average_rate['average'], 'iwjob' ); ?>">
                                                        <?php echo IWJ_Reviews::get_number_stars( $average_rate['average'] ); ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <?php if ($employer->get_locations_links() || $employer->get_phone()) { ?>
                                            <div class="employer-meta">
												<meta itemprop="address" content="<?php echo $employer->get_address(); ?>" />
												<meta itemprop="email" content="<?php echo $employer->get_email(); ?>" />
												<meta itemprop="telephone" content="<?php echo $employer->get_phone(); ?>" />
                                                <?php if ($employer->get_locations_links()) { ?>
                                                    <div class="employer-locations"><i class="ion-android-pin"></i><?php echo $employer->get_locations_links(); ?></div>
                                                <?php } ?>
                                                <?php if ($employer->get_phone()) { ?>
                                                    <div class="employer-phone"><i class="ion-android-call"></i><?php echo $employer->get_phone(); ?></div>
                                                <?php } ?>
                                            </div>
                                            <?php } ?>
                                            <a class="total-jobs theme-color" href="<?php echo $author->permalink(); ?>"><?php echo __( 'View all', 'iwjob' ); ?> <?php echo $total_jobs; ?> <?php echo _n('Job Position', 'Job Positions', $total_jobs, 'iwjob'); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $i ++;
                        endforeach; ?>
                    </div>
                </div>
            </div>
    <?php
    break;

        case 'style4':
            $data_plugin_options_v3 = array(
                "navigation"=>false,
                "autoHeight"=>false,
                "pagination"=>true,
                "autoPlay"=>($atts['auto_play'] ? true : false),
                "paginationNumbers"=>false,
                "items"             => ($atts['items_desktop'] ? $atts['items_desktop'] : 3),
                "itemsDesktop"      => array( 1400, $atts['items_desktop'] ? $atts['items_desktop'] : 3 ),
                "itemsDesktopSmall" => array( 991, $atts['items_desktop_small'] ? $atts['items_desktop_small'] : 2 ),
                "itemsTablet"       => array( 768, $atts['items_tablet'] ? $atts['items_tablet'] : 2 ),
                "itemsMobile"       => array( 640, $atts['items_mobile'] ? $atts['items_mobile'] : 1 ),
                "navigationText" => array('<i class="ion-android-arrow-back"></i>', '<i class="ion-android-arrow-forward"></i>')
            );
            ?>
            <div class="iwj-employers-slider <?php echo $atts['class']; echo $atts['style']; ?>">
                <div class="iwj-employers-slider-inner">
                    <div class=" owl-carousel" data-plugin-options="<?php echo htmlspecialchars(json_encode($data_plugin_options_v3)); ?>">
                        <?php
                        $i = 0;
                        foreach ($employers as $employer) :
                            $total_jobs = $employer->total_jobs;
                            $employer = IWJ_Employer::get_employer($employer);
                            $author = $employer->get_author();
                            $image = iwj_get_avatar( $author->get_id(), '120', '', $author->get_display_name(), array('img_size'=>'inwave-avatar2') );
                            $cover_image_url = '';
                            $cover_image = $employer->get_cover_image();
                            if ($cover_image) {
                                $cover_image = wp_get_attachment_image_src($cover_image, 'inwave-570-330');
                                $cover_image_url = $cover_image ? $cover_image[0] : '';
                            }
                            ?>
                            <div class="iwj-employer-item" itemscope itemtype="http://schema.org/Organization">
                                <div class="employer-item">
                                    <div class="employer-cover-image" style="background-image: url(<?php echo esc_url($cover_image_url); ?>)">
                                    </div>
                                    <div class="employer-content">
                                        <div class="employer-content-inner">
                                            <div class="employer-image-rating">
                                                <div class="employer-image"><a href="<?php echo $author->permalink(); ?>"><?php echo $image; ?></a></div>
                                                <?php
                                                $average_rate = IWJ_Reviews::get_average_rate( $employer->get_id() );
                                                if ( count( $average_rate ) ) { ?>
                                                    <div class="iwj-box-rating" itemprop="aggregateRating"
														 itemscope itemtype="http://schema.org/AggregateRating">
														<meta itemprop="ratingValue" content="<?php echo $average_rate['average']; ?>" />
														<meta itemprop="reviewCount" content="<?php echo $average_rate['totals']; ?>" />
                                                        <div class="iwj-count-rate" title="<?php esc_attr_e( $average_rate['average'], 'iwjob' ); ?>">
                                                            <?php echo IWJ_Reviews::get_number_stars( $average_rate['average'] ); ?>
                                                        </div>
                                                        <span class="text-totals-rate">
                                                            <?php echo sprintf( _n( '%d rating', '%d ratings', $average_rate['totals'], 'iwjob' ), $average_rate['totals'] ); ?>
                                                        </span>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div class="content-right">
                                                <h3 class="employer-title" itemprop="name"><a class="theme-color" href="<?php echo $author->permalink(); ?>"><?php echo $author->get_display_name(); ?></a></h3>
												<meta itemprop="address" content="<?php echo $employer->get_address(); ?>" />
												<meta itemprop="email" content="<?php echo $employer->get_email(); ?>" />
												<meta itemprop="telephone" content="<?php echo $employer->get_phone(); ?>" />
                                                <?php if ($employer->get_locations_links()) { ?>
                                                    <div class="employer-locations"><i class="ion-android-pin"></i><?php echo $employer->get_locations_links(); ?></div>
                                                <?php } ?>
                                                <?php if ( $description = $employer->get_description( true ) ) : ?>
                                                    <div class="employerdl-desc">
                                                        <?php echo wp_trim_words($description, 15); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $i ++;
                        endforeach; ?>
                    </div>
                </div>
            </div>
    <?php
    break;
    }
    ?>

<?php } ?>