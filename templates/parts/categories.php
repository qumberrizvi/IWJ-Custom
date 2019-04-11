<?php if ($categories || $categories_parent) {
    ?>
    <div class="iwj-categories <?php echo $atts['style'] .' '.$atts['class']; ?>">
        <?php
        switch ($atts['style']){
            case 'style1':
                $i = 1;
                $cats_per_row = $atts['cats_per_row'] ? $atts['cats_per_row'] : 5;
                foreach ($categories as $category) {
                    $icon_class = get_term_meta($category->term_id, IWJ_PREFIX.'icon_class', true);
                    if(!$icon_class){
                        $icon_class = 'ion-android-contacts';
                    }
	                $icon_image = get_term_meta($category->term_id, IWJ_PREFIX.'icon_image', true);
	                $img_attachment = $icon_image ? wp_get_attachment_image_url( $icon_image ) : ''; ?>
                    <div class="item-category">
                        <div class="item-category-inner">
							<a href="<?php echo get_term_link( $category->slug, 'iwj_cat' ); ?>" class="category-icon">
		                        <?php if ( $img_attachment ) {
			                        echo '<img src="' . $img_attachment . '" width="56" height="52" class="item-category-image" />';
		                        } else { ?>
									<i class="<?php echo $icon_class; ?>"></i>
			                        <?php
		                        } ?>
							</a>
                            <h3 class="category-title"><a href="<?php echo get_term_link($category->slug, 'iwj_cat'); ?>"><?php echo $category->name; ?></a></h3>
                            <div class="total-jobs"><?php echo sprintf(_n('( %d job )', '( %d jobs )', $category->total,'iwjob'), $category->total); ?></div>
                        </div>
                    </div>
                <?php
                    if($atts['cats_per_row'] && $i > 1 && ($i % $cats_per_row  == 0) && count($categories) > $i){
                        echo '<div class="clearfix"></div>';
                    }
                $i++;
                }

                if($atts['show_categories_btn']){
                    ?>
                    <div class="item-category all-categories">
                        <div class="item-category-inner">
                            <h3 class="category-title"><a href="<?php echo $atts['link_all_cats']; ?>"><?php echo $atts['text_link_all_cats']; ?></a></h3>
                        </div>
                    </div>
                    <?php
                }
                break;

            case 'style3':
            case 'style4':
            $item_class =  'col-md-3 col-sm-3 col-xs-12';
            if($atts['cats_per_row'] == '1'){
                $item_class =  'col-md-12 col-sm-12';
            }elseif($atts['cats_per_row'] == '2'){
                $item_class =  'col-md-6 col-sm-6 col-xs-12';
            }elseif($atts['cats_per_row'] == '3'){
                $item_class =  'col-md-4 col-sm-4 col-xs-12';
            }elseif($atts['cats_per_row'] == '4'){
                $item_class =  'col-md-3 col-sm-3 col-xs-12';
            }elseif($atts['cats_per_row'] == '6'){
                $item_class =  'col-md-2 col-sm-2 col-xs-12';
            }
        ?>
            <div class="row">
                <?php foreach ($categories as $category) {
                    ?>
                        <div class="<?php echo $item_class; ?>">
                            <div class="item-category">
                                <a href="<?php echo get_term_link($category->slug, 'iwj_cat'); ?>"><?php echo $category->name; ?></a>
                                <span><?php echo $category->total ?></span>
                            </div>
                        </div>
                <?php } ?>
            </div>

        <?php
            if($atts['show_categories_btn']){
                ?>
                <div class="all-categories">
                    <a  class="iwj-btn iwj-btn-primary" href="<?php echo $atts['link_all_cats']; ?>"><?php echo $atts['text_link_all_cats']; ?></a>
                </div>
                <?php
            }
        break;

        case 'style5':
            $item_class =  'col-md-3 col-sm-4 col-xs-12';
            if($atts['cats_per_row'] == '1'){
                $item_class =  'col-md-12 col-sm-12';
            }elseif($atts['cats_per_row'] == '2'){
                $item_class =  'col-md-6 col-sm-6 col-xs-12';
            }elseif($atts['cats_per_row'] == '3'){
                $item_class =  'col-md-4 col-sm-4 col-xs-12';
            }elseif($atts['cats_per_row'] == '4'){
                $item_class =  'col-md-3 col-sm-4 col-xs-12';
            }elseif($atts['cats_per_row'] == '6'){
                $item_class =  'col-md-2 col-sm-2 col-xs-12';
            }
            wp_enqueue_script('isotope');
            echo '<div class="iwj-isotope-main isotope"><div class="row">';
            foreach ($categories_parent as $category) {
                $icon_class = get_term_meta($category->term_id, IWJ_PREFIX.'icon_class', true);
                if(!$icon_class){
                    $icon_class = 'ion-android-contacts';
                }

	            $icon_image = get_term_meta($category->term_id, IWJ_PREFIX.'icon_image', true);
	            $img_attachment = $icon_image ? wp_get_attachment_image_url( $icon_image ) : '';

                $img_src = '';
                $bg_image = get_term_meta($category->term_id, IWJ_PREFIX.'bg_image', true);
                if($bg_image){
                    $image = wp_get_attachment_image_src($bg_image, 'full');
                    $img_src = count($image) ? $image[0] : '';
                }
                if (!$img_src) {
                    $img_src = IWJ_PLUGIN_URL.'/assets/img/cat_bg.png';
                }

                $category_child_ids = get_term_children( $category->term_id, 'iwj_cat' );

                ?>
                <div class="<?php echo $item_class; ?> element-item 123">
                    <div class="item-category">
                        <div class="category-image" style="background-image: url(<?php echo $img_src; ?>)"></div>
                        <a href="<?php echo get_term_link($category->slug, 'iwj_cat'); ?>" class="category-icon">
	                        <?php if ( $img_attachment ) {
		                        echo '<img src="' . $img_attachment . '" width="56" height="56" class="item-category-image" />';
	                        } else { ?>
								<i class="<?php echo $icon_class; ?>"></i>
		                        <?php
	                        } ?>
						</a>
                        <h3 class="category-title"><a href="<?php echo get_term_link($category->slug, 'iwj_cat'); ?>"><?php echo $category->name; ?></a></h3>
                        <?php if ($category_child_ids && $atts['show_categories_child'] == '1') {
                            $categories_child = iwj_get_cats($cat_ids = $category_child_ids, $exclude_ids = array(), $hide_empty = false, $limit = '', $order_by = 'count', $oder = 'DESC');
                            ?>
                            <ul class="categories-child">
                                <?php foreach ($categories_child as $category_child) {
                                    ?>
                                    <li class="category-child-title"><a href="<?php echo get_term_link($category_child->slug, 'iwj_cat'); ?>"><?php echo $category_child->name; ?> <span>(<?php echo esc_html($category_child->total); ?>)</span></a></li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </div>
                </div>
            <?php
            }
            echo '</div></div>';

            if($atts['show_categories_btn']){
                ?>
                <div class="all-categories">
                    <a  class="iwj-btn iwj-btn-primary" href="<?php echo $atts['link_all_cats']; ?>"><?php echo $atts['text_link_all_cats']; ?></a>
                </div>
            <?php
            }
            break;

		case 'style6':
			$item_class =  'col-md-3 col-sm-3 col-xs-12';
			if($atts['cats_per_row'] == '1'){
				$item_class =  'col-md-12 col-sm-12';
			}elseif($atts['cats_per_row'] == '2'){
				$item_class =  'col-md-6 col-sm-6 col-xs-12';
			}elseif($atts['cats_per_row'] == '3'){
				$item_class =  'col-md-4 col-sm-4 col-xs-12';
			}elseif($atts['cats_per_row'] == '4'){
				$item_class =  'col-md-3 col-sm-3 col-xs-12';
			}elseif($atts['cats_per_row'] == '6'){
				$item_class =  'col-md-2 col-sm-2 col-xs-12';
			}
			wp_enqueue_script('isotope');
			?>
			<div class="iwj-isotope-main isotope">
				<div class="row">
					<?php
					$i = 1;
					foreach ($categories_parent as $category) {
						$icon_class = get_term_meta($category->term_id, IWJ_PREFIX.'icon_class', true);
						if(!$icon_class){
							$icon_class = 'ion-android-contacts';
						}

						$icon_image = get_term_meta($category->term_id, IWJ_PREFIX.'icon_image', true);
						$img_attachment = $icon_image ? wp_get_attachment_image_url( $icon_image ) : '';

						$img_src = '';
						$bg_image = get_term_meta($category->term_id, IWJ_PREFIX.'bg_image', true);
						if($bg_image){
							$image = wp_get_attachment_image_src($bg_image, 'full');
							$img_src = count($image) ? $image[0] : '';
						}
						if (!$img_src) {
							$img_src = IWJ_PLUGIN_URL.'/assets/img/cat_bg.png';
						}

						$category_child_ids = get_term_children( $category->term_id, 'iwj_cat' );

						?>
						<div class="style6-1 <?php echo $item_class; ?> element-item">
							<div class="item-category" style="background-image: url(<?php echo $img_src; ?>)">
								<a href="<?php echo get_term_link($category->slug, 'iwj_cat'); ?>" class="category-icon">
									<?php if ( $img_attachment ) {
										echo '<img src="' . $img_attachment . '" width="56" height="56" class="item-category-image" />';
									} else { ?>
										<i class="<?php echo $icon_class; ?>"></i>
										<?php
									} ?>
								</a>
								<div class="category-info">
									<h3 class="category-title"><a href="<?php echo get_term_link($category->slug, 'iwj_cat'); ?>"><?php echo $category->name; ?></a></h3>
									<?php if ($category_child_ids && $atts['show_categories_child'] == '1') {
										$categories_child = iwj_get_cats($cat_ids = $category_child_ids, $exclude_ids = array(), $hide_empty = false, $limit = '', $order_by = 'count', $oder = 'DESC');
										?>
										<ul class="categories-child">
											<?php foreach ($categories_child as $category_child) {
												?>
												<li class="category-child-title"><a href="<?php echo get_term_link($category_child->slug, 'iwj_cat'); ?>"><?php echo $category_child->name; ?> <span>(<?php echo esc_html($category_child->total); ?>)</span></a></li>
											<?php } ?>
										</ul>
									<?php } ?>
									<div><a class="view-link" href="<?php echo get_term_link($category->slug, 'iwj_cat'); ?>"><?php echo __('View Classes', 'iwjob') ?></a></div>
								</div>
							</div>
						</div>
						<?php
						$i++;
					} ?>
				</div>
			</div>
			<?php
			if($atts['show_categories_btn']){
				?>
				<div class="all-categories">
					<a  class="iwj-btn iwj-btn-primary" href="<?php echo $atts['link_all_cats']; ?>"><?php echo $atts['text_link_all_cats']; ?></a>
				</div>
			<?php
			}
			break;

		case 'style7':
			$item_class =  'col-md-3 col-sm-3 col-xs-12';
			if($atts['cats_per_row'] == '1'){
				$item_class =  'col-md-12 col-sm-12';
			}elseif($atts['cats_per_row'] == '2'){
				$item_class =  'col-md-6 col-sm-6 col-xs-12';
			}elseif($atts['cats_per_row'] == '3'){
				$item_class =  'col-md-4 col-sm-4 col-xs-12';
			}elseif($atts['cats_per_row'] == '4'){
				$item_class =  'col-md-3 col-sm-3 col-xs-12';
			}elseif($atts['cats_per_row'] == '6'){
				$item_class =  'col-md-2 col-sm-2 col-xs-12';
			}
			wp_enqueue_script('isotope');
			?>
			<div class="iwj-isotope-main isotope">
				<div class="row">
					<?php
					$i = 1;
					foreach ($categories_parent as $category) {
						$icon_class = get_term_meta($category->term_id, IWJ_PREFIX.'icon_class', true);
						if(!$icon_class){
							$icon_class = 'ion-android-contacts';
						}

						$icon_image = get_term_meta($category->term_id, IWJ_PREFIX.'icon_image', true);
						$img_attachment = $icon_image ? wp_get_attachment_image_url( $icon_image ) : '';

						$category_child_ids = get_term_children( $category->term_id, 'iwj_cat' );

						?>
						<div class="<?php echo $item_class; ?> element-item">
							<div class="item-category">
								<a href="<?php echo get_term_link($category->slug, 'iwj_cat'); ?>" class="category-icon theme-color">
									<?php if ( $img_attachment ) {
										echo '<img src="' . $img_attachment . '" width="56" height="56" class="item-category-image" />';
									} else { ?>
										<i class="<?php echo $icon_class; ?>"></i>
										<?php
									} ?>
								</a>
								<div class="category-info">
									<h3 class="category-title"><a href="<?php echo get_term_link($category->slug, 'iwj_cat'); ?>"><?php echo $category->name; ?></a></h3>
									<?php if ($category_child_ids && $atts['show_categories_child'] == '1') {
										$categories_child = iwj_get_cats($cat_ids = $category_child_ids, $exclude_ids = array(), $hide_empty = false, $limit = '', $order_by = 'count', $oder = 'DESC');
										?>
										<ul class="categories-child">
											<?php foreach ($categories_child as $category_child) {
												?>
												<li class="category-child-title"><a href="<?php echo get_term_link($category_child->slug, 'iwj_cat'); ?>"><?php echo $category_child->name; ?> <span>(<?php echo esc_html($category_child->total); ?>)</span></a></li>
											<?php } ?>
										</ul>
									<?php } ?>
								</div>
							</div>
						</div>
						<?php
						$i++;
					} ?>
				</div>
			</div>
			<?php
			if($atts['show_categories_btn']){
				?>
				<div class="all-categories">
					<a  class="iwj-btn iwj-btn-primary" href="<?php echo $atts['link_all_cats']; ?>"><?php echo $atts['text_link_all_cats']; ?></a>
				</div>
			<?php
			}

			break;

		case 'style8':
			?>
			<div class="items-category">
				<?php
				$i = 1;
				foreach ($categories_parent as $category) {
					$icon_class = get_term_meta($category->term_id, IWJ_PREFIX.'icon_class', true);
					if(!$icon_class){
						$icon_class = 'ion-android-contacts';
					}

					$icon_image = get_term_meta($category->term_id, IWJ_PREFIX.'icon_image', true);
					$img_attachment = $icon_image ? wp_get_attachment_image_url( $icon_image ) : '';

					$category_child_ids = get_term_children( $category->term_id, 'iwj_cat' );

					?>
					<div class="item-category item-category-match-height">
						<a href="<?php echo get_term_link($category->slug, 'iwj_cat'); ?>" class="category-icon theme-color">
							<?php if ( $img_attachment ) {
								echo '<img src="' . $img_attachment . '" width="56" height="56" class="item-category-image" />';
							} else { ?>
								<i class="<?php echo $icon_class; ?>"></i>
								<?php
							} ?>
						</a>
						<h3 class="category-title"><a href="<?php echo get_term_link($category->slug, 'iwj_cat'); ?>"><?php echo $category->name; ?></a></h3>
						<?php if ($category_child_ids && $atts['show_categories_child'] == '1') {
							$categories_child = iwj_get_cats($cat_ids = $category_child_ids, $exclude_ids = array(), $hide_empty = false, $limit = '', $order_by = 'count', $oder = 'DESC');
							?>
							<ul class="categories-child">
								<?php foreach ($categories_child as $category_child) {
									?>
									<li class="category-child-title"><a href="<?php echo get_term_link($category_child->slug, 'iwj_cat'); ?>"><?php echo $category_child->name; ?> <span>(<?php echo esc_html($category_child->total); ?>)</span></a></li>
								<?php } ?>
							</ul>
						<?php } ?>
					</div>
					<?php
					$i++;
				} ?>
			</div>
			<?php
			if($atts['show_categories_btn']){
				?>
				<div class="all-categories">
					<a  class="iwj-btn iwj-btn-primary" href="<?php echo $atts['link_all_cats']; ?>"><?php echo $atts['text_link_all_cats']; ?></a>
				</div>
			<?php
			}

			break;

		case 'style9':
			$item_class =  'col-md-3 col-sm-3 col-xs-12';
			if($atts['cats_per_row'] == '1'){
				$item_class =  'col-md-12 col-sm-12';
			}elseif($atts['cats_per_row'] == '2'){
				$item_class =  'col-md-6 col-sm-6 col-xs-12';
			}elseif($atts['cats_per_row'] == '3'){
				$item_class =  'col-md-4 col-sm-4 col-xs-12';
			}elseif($atts['cats_per_row'] == '4'){
				$item_class =  'col-md-3 col-sm-3 col-xs-12';
			}elseif($atts['cats_per_row'] == '6'){
				$item_class =  'col-md-2 col-sm-3 col-xs-12';
			}
			wp_enqueue_script('isotope');
			?>
			<div class="iwj-isotope-main">
				<div class="row">
					<?php
					$i = 1;
					foreach ($categories_parent as $category) {
						$icon_class = get_term_meta($category->term_id, IWJ_PREFIX.'icon_class', true);
						if(!$icon_class){
							$icon_class = 'ion-android-contacts';
						}

						$icon_image = get_term_meta($category->term_id, IWJ_PREFIX.'icon_image', true);
						$img_attachment = $icon_image ? wp_get_attachment_image_url( $icon_image ) : '';

						$category_child_ids = get_term_children( $category->term_id, 'iwj_cat' );

						?>
						<div class="<?php echo $item_class; ?> element-item">
							<div class="item-category">
								<span class="category-icon"><a href="<?php echo get_term_link( $category->slug, 'iwj_cat' ); ?>">
										<?php if ( $img_attachment ) {
											echo '<img src="' . $img_attachment . '" width="56" height="56" class="item-category-image" />';
										} else { ?>
											<i class="<?php echo $icon_class; ?>"></i>
											<?php
										} ?>
									</a></span>
								<div class="content">
									<h3 class="category-title">
										<a href="<?php echo get_term_link( $category->slug, 'iwj_cat' ); ?>"><?php echo $category->name; ?></a>
									</h3>
									<?php if ( $category_child_ids && $atts['show_categories_child'] == '1' ) {
										$categories_child = iwj_get_cats( $cat_ids = $category_child_ids, $exclude_ids = array(), $hide_empty = false, $limit = '', $order_by = 'count', $oder = 'DESC' );
										?>
										<ul class="categories-child">
											<?php foreach ( $categories_child as $category_child ) {
												?>
												<li class="category-child-title">
													<a href="<?php echo get_term_link( $category_child->slug, 'iwj_cat' ); ?>"><?php echo $category_child->name; ?>
														<span>(<?php echo esc_html( $category_child->total ); ?>)</span></a>
												</li>
											<?php } ?>
										</ul>
									<?php } ?>
								</div>
							</div>
						</div>
						<?php
						$i++;
					} ?>
				</div>
			</div>
			<?php
			if($atts['show_categories_btn']){
				?>
				<div class="all-categories">
					<a  class="iwj-btn iwj-btn-primary" href="<?php echo $atts['link_all_cats']; ?>"><?php echo $atts['text_link_all_cats']; ?></a>
				</div>
			<?php }

			break;

		case 'style10':
			$item_class =  'col-md-3 col-sm-6 col-xs-12';
			if($atts['cats_per_row'] == '1'){
				$item_class =  'col-md-12 col-sm-12 col-xs-12';
			}elseif($atts['cats_per_row'] == '2'){
				$item_class =  'col-md-6 col-sm-6 col-xs-12';
			}elseif($atts['cats_per_row'] == '3'){
				$item_class =  'col-md-4 col-sm-6 col-xs-12';
			}elseif($atts['cats_per_row'] == '4'){
				$item_class =  'col-md-3 col-sm-6 col-xs-12';
			}elseif($atts['cats_per_row'] == '6'){
				$item_class =  'col-md-2 col-sm-6 col-xs-12';
			}
			echo '<div class="row">';
			foreach ($categories_parent as $category) {
				$icon_class = get_term_meta($category->term_id, IWJ_PREFIX.'icon_class', true);
				if(!$icon_class){
					$icon_class = 'ion-android-contacts';
				}
				$icon_image = get_term_meta($category->term_id, IWJ_PREFIX.'icon_image', true);
				$img_attachment = $icon_image ? wp_get_attachment_image_url( $icon_image ) : '';

				$category_child_ids = get_term_children( $category->term_id, 'iwj_cat' );
				?>
				<div class="<?php echo $item_class; ?>">
					<div class="item-category item-category-match-height">
						<div class="category-icon"><a class="theme-color" href="<?php echo get_term_link($category->slug, 'iwj_cat'); ?>">
								<?php if ( $img_attachment ) {
									echo '<img src="' . $img_attachment . '" width="56" height="56" class="item-category-image" />';
								} else { ?>
									<i class="<?php echo $icon_class; ?>"></i>
									<?php
								} ?>
							</a></div>
						<div class="category-content">
							<h3 class="category-title"><a class="theme-color" href="<?php echo get_term_link($category->slug, 'iwj_cat'); ?>"><?php echo $category->name; ?></a></h3>
							<?php if ($category_child_ids && $atts['show_categories_child'] == '1') {
								$categories_child = iwj_get_cats($cat_ids = $category_child_ids, $exclude_ids = array(), $hide_empty = false, $limit = '', $order_by = 'count', $oder = 'DESC');
								?>
								<ul class="categories-child">
									<?php foreach ($categories_child as $category_child) {
									?>
										<li class="category-child-title"><a class="theme-color-hover" href="<?php echo get_term_link($category_child->slug, 'iwj_cat'); ?>"><?php echo $category_child->name; ?> <span>(<?php echo esc_html($category_child->total); ?>)</span></a></li>
									<?php } ?>
								</ul>
							<?php } ?>
							<h4 class="view-all-jobs"><a class="theme-color" href="<?php echo get_term_link($category->slug, 'iwj_cat'); ?>"><?php echo __( 'View all Classes', 'iwjob' ); ?> <i class="fa fa-arrow-circle-right"></i></a></h4>
						</div>
					</div>
				</div>
			<?php
			}
			echo '</div>';
			break;

		case 'style11':
			$item_class =  'col-md-3 col-sm-6 col-xs-12';
			if($atts['cats_per_row'] == '1'){
				$item_class =  'item-col col-md-12 col-sm-12 col-xs-12';
			}elseif($atts['cats_per_row'] == '2'){
				$item_class =  'item-col col-md-6 col-sm-6 col-xs-12';
			}elseif($atts['cats_per_row'] == '3'){
				$item_class =  'item-col col-md-4 col-sm-6 col-xs-12';
			}elseif($atts['cats_per_row'] == '4'){
				$item_class =  'item-col col-md-3 col-sm-6 col-xs-12';
			}elseif($atts['cats_per_row'] == '6'){
				$item_class =  'item-col col-md-2 col-sm-6 col-xs-12';
			}
			echo '<div class="row">';
			foreach ($categories_parent as $category) {
				$icon_class = get_term_meta($category->term_id, IWJ_PREFIX.'icon_class', true);
				if(!$icon_class){
					$icon_class = 'ion-android-contacts';
				}
				$icon_image = get_term_meta($category->term_id, IWJ_PREFIX.'icon_image', true);
				$img_attachment = $icon_image ? wp_get_attachment_image_url( $icon_image ) : '';

				$category_child_ids = get_term_children( $category->term_id, 'iwj_cat' );
				?>
				<div class="<?php echo $item_class; ?>">
					<div class="item-category item-category-match-height <?php echo esc_attr($atts['cats_per_row']); ?>">
						<div class="category-icon"><a href="<?php echo get_term_link($category->slug, 'iwj_cat'); ?>">
								<?php if ( $img_attachment ) {
									echo '<img src="' . $img_attachment . '" class="item-category-image" />';
								} else { ?>
									<i class="<?php echo $icon_class; ?>"></i>
									<?php
								} ?>
							</a></div>
						<div class="category-content">
							<h3 class="category-title"><a href="<?php echo get_term_link($category->slug, 'iwj_cat'); ?>"><?php echo $category->name; ?></a></h3>
							<?php if ($category_child_ids && $atts['show_categories_child'] == '1') {
								$categories_child = iwj_get_cats($cat_ids = $category_child_ids, $exclude_ids = array(), $hide_empty = false, $limit = '', $order_by = 'count', $oder = 'DESC');
								?>
								<ul class="categories-child">
									<?php
									$cats_child = array();
									foreach ($categories_child as $category_child) {
										$cats_child[] = '<li><a href="' . esc_url(get_term_link($category_child->slug, 'iwj_cat')) . '">' . $category_child->name . '<span>( ' .esc_html($category_child->total). ' )</span></a></li>';
									} ?>
									<?php if ($cats_child) {
										echo implode(' , ', $cats_child);
									} ?>
								</ul>
							<?php } ?>
						</div>
					</div>
				</div>
			<?php
			}
			echo '</div>';
			break;

		case 'style12':

			wp_enqueue_style('owl-carousel');
			wp_enqueue_style('owl-theme');
			wp_enqueue_style('owl-transitions');
			wp_enqueue_script('owl-carousel');

			$data_plugin_options = array(
				"navigation"=>false,
				"autoHeight"=>false,
				"pagination"=>true,
				"autoPlay"=>($atts['auto_play'] ? true : false),
				"paginationNumbers"=>false,
				"items"             => ($atts['items_desktop'] ? $atts['items_desktop'] : 3),
				"itemsDesktop"      => array( 1400, $atts['items_desktop'] ? $atts['items_desktop'] : 3 ),
				"itemsDesktopSmall" => array( 991, $atts['items_desktop_small'] ? $atts['items_desktop_small'] : 3 ),
				"itemsTablet"       => array( 768, $atts['items_tablet'] ? $atts['items_tablet'] : 2 ),
				"itemsMobile"       => array( 480, $atts['items_mobile'] ? $atts['items_mobile'] : 1 ),
				"navigationText" => array('<i class="ion-android-arrow-back"></i>', '<i class="ion-android-arrow-forward"></i>')
			);
			?>
			<div class="iwj-categories-slider">
				<div class="owl-carousel pagination-dot" data-plugin-options="<?php echo htmlspecialchars(json_encode($data_plugin_options)); ?>">
				<?php foreach ($categories_parent as $category) {
					$icon_class = get_term_meta($category->term_id, IWJ_PREFIX.'icon_class', true);
					if(!$icon_class){
						$icon_class = 'ion-android-contacts';
					}

					$icon_image = get_term_meta($category->term_id, IWJ_PREFIX.'icon_image', true);
					$img_attachment = $icon_image ? wp_get_attachment_image_url( $icon_image ) : '';

					$img_src = '';
					$bg_image = get_term_meta($category->term_id, IWJ_PREFIX.'bg_image', true);
					if($bg_image){
						$image = wp_get_attachment_image_src($bg_image, 'full');
						$img_src = count($image) ? $image[0] : '';
					}
					if (!$img_src) {
						$img_src = IWJ_PLUGIN_URL.'/assets/img/cat_bg.png';
					}
					$category_child_ids = get_term_children( $category->term_id, 'iwj_cat' );
					?>
					<div class="item-category">
						<div class="category-image" style="background-image: url(<?php echo $img_src; ?>)">
						</div>
						<div class="category-content-wrap">
							<div class="category-icon"><a href="<?php echo get_term_link($category->slug, 'iwj_cat'); ?>">
									<?php if ( $img_attachment ) {
										echo '<img src="' . $img_attachment . '" class="item-category-image" />';
									} else { ?>
										<i class="<?php echo $icon_class; ?>"></i>
										<?php
									} ?>
								</a></div>
							<div class="category-content">
								<h3 class="category-title"><a href="<?php echo get_term_link($category->slug, 'iwj_cat'); ?>"><?php echo $category->name; ?></a></h3>
								<div class="total-jobs"><?php echo sprintf(_n('%d job Available', '%d Classes Available', $category->total,'iwjob'), $category->total); ?></div>
								<div class="content-bttom">
									<?php if ($category_child_ids && $atts['show_categories_child'] == '1') {
										$categories_child = iwj_get_cats($cat_ids = $category_child_ids, $exclude_ids = array(), $hide_empty = false, $limit = '', $order_by = 'count', $oder = 'DESC');
										?>
										<ul class="categories-child">
											<?php foreach ($categories_child as $category_child) {
												?>
												<li class="category-child-title"><a href=""><?php echo $category_child->name; ?> <span>(<?php echo esc_html($category_child->total); ?>)</span></a></li>
											<?php } ?>
										</ul>
									<?php } ?>
									<h4 class="view-all-jobs"><a href="<?php echo get_term_link($category->slug, 'iwj_cat'); ?>"><?php echo __( 'View all Classes', 'iwjob' ); ?> <i class="fa fa-arrow-circle-right"></i></a></h4>
								</div>
							</div>
						</div>
					</div>
				<?php
				}
			echo '</div></div>';
			break;
        }
        ?>
    </div>
<?php } ?>