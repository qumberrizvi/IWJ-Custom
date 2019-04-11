<div class="employer-detail-v1">
    <div class="container">
        <div class="row">
            <?php
            if ( ! $show_employer_public_profile || ( $show_employer_public_profile && is_user_logged_in() ) ) { ?>
                <div class="<?php echo esc_attr( inwave_get_classes( 'container', $employer_sidebar ) ) ?>">
                <div class="iwj-employerdl-content">
                <div class="employer-info-top" itemscope itemtype="http://schema.org/Organization">
                    <div class="bg-overlay"></div>
                    <div class="info-top">
                        <div class="employer-logo">
                            <?php echo iwj_get_avatar($employer->get_author_id(), '150', '', '', array('img_size'=> 'thumbnail')); ?>
                            <div class="social-link">
                                <ul class="iw-social-all hover-bg">
                                    <?php
                                    foreach ( $employer->get_social_media() as $key => $value ) {
                                        if ( $value != null && $value != '' ) {
                                            if ( strchr( $value, "http" ) ) {
                                                $link = $value;
                                            } else {
                                                $link = 'http://' . $value;
                                            }
                                            if ( $key == "google_plus" ) {
                                                echo '<li><a class="' . $key . '" href="' . $link . '" title="' . $key . '" target="_blank"><i class="ion-social-googleplus"></i></a></li>';
                                            } else {
                                                echo '<li><a class="' . $key . '" href="' . $link . '" title="' . $key . '" target="_blank"><i class="ion-social-' . $key . '"></i></a></li>';
                                            }
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <div class="conttent-right">
                            <div class="title-location">
                                <h3 class="title" itemprop="name"><?php echo $employer->get_title(); ?></h3>
                                <?php if ( $employer->get_headline() ) { ?>
                                    <div class="employer-headline">
                                        <?php echo $employer->get_headline(); ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="employer-info">
                                <ul>
                                    <?php if ( $employer->get_phone() ) { ?>
                                        <li class="employer-phone">
                                            <i class="ion-ios-telephone"></i><?php echo __( 'Hotline: ', 'iwjob' );
                                            echo $employer->get_phone(); ?>
											<meta itemprop="telephone" content="<?php echo $employer->get_phone(); ?>" />
										</li>
                                    <?php } ?>
                                    <?php if ( $employer->get_address() ) { ?>
                                        <li class="employer-address">
                                            <i class="ion-android-pin"></i><?php echo __( 'Address: ', 'iwjob' );
                                            echo $employer->get_address(); ?>
											<meta itemprop="address" content="<?php echo $employer->get_address(); ?>" />
										</li>
                                    <?php } ?>
                                    <?php if ( $employer->get_email() ) { ?>
                                        <li class="employer-email">
                                            <i class="ion-email"></i><span><?php echo __( 'Email: ', 'iwjob' );
                                                echo $employer->get_email(); ?></span>
											<meta itemprop="email" content="<?php echo $employer->get_email(); ?>" />
										</li>
                                    <?php } ?>
                                    <?php if ( $employer->get_website() ) { ?>
                                        <li class="employer-website">
                                            <i class="ion-link"></i><a href="<?php echo $employer->get_website(); ?>"><?php echo $employer->get_website(); ?></a>
											<meta itemprop="url" content="<?php echo $employer->get_website(); ?>" />
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <div class="follow-button">
                            <?php if ( is_user_logged_in() ) { ?>
                                <?php
                                if ( current_user_can( 'apply_job' ) ) {
                                    $followed      = $user && $user->is_followed( get_the_ID() ) ? true : false;
                                    $followed_text = $followed ? __( '<i class="ion-android-send"></i> Followed', 'iwjob' ) : __( '<i class="ion-android-send"></i> Follow Us', 'iwjob' );
                                    ?>
                                    <div class="iwj-button-loader">
                                        <a href="#" class="action-button follow iwj-follow iwj-btn <?php echo $followed ? 'followed' : ''; ?>" data-id="<?php echo $employer->get_id(); ?>"><?php echo $followed_text; ?></a>
                                        <div class="iwj-follow-msg iwj-hide"></div>
                                    </div>
                                <?php } ?>
                            <?php } else { ?>
                                <a href="#" class="action-button follow iwj-follow iwj-btn" data-toggle="modal" data-target="#iwj-login-popup"><span data-hover="<?php echo __( 'Follow Us', 'iwjob' ); ?>"><i class="ion-android-send"></i><?php echo __( 'Follow Us', 'iwjob' ); ?></span></a>
                            <?php } ?>
                            <?php
                            $average_rate = IWJ_Reviews::get_average_rate( $employer->get_id() );
                            if ( ! $disable_review && count( $average_rate ) ) { ?>
                                <div class="iwj-box-rating" itemprop="aggregateRating"
									 itemscope itemtype="http://schema.org/AggregateRating">
									<meta itemprop="ratingValue" content="<?php echo $average_rate['average']; ?>" />
									<meta itemprop="reviewCount" content="<?php echo $average_rate['totals']; ?>" />
									<span class="iwj-count-rate" title="<?php esc_attr_e( $average_rate['average'], 'iwjob' ); ?>">
										<?php echo IWJ_Reviews::get_number_stars( $average_rate['average'] ); ?>
									</span>
										<span class="iwj-text-totals-rate">
										<?php echo sprintf( _n( 'rating %d', 'ratings %d', $average_rate['totals'], 'iwjob' ), $average_rate['totals'] ); ?>
									</span>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <?php
                $gallery   = $employer->get_gallery();
                $video_url = $employer->get_video();
                if ( $gallery || $video_url ) {
                    wp_enqueue_script( 'bxslider' );
                    wp_enqueue_style( 'bxslider' );
                    ?>
                    <div class="iwj-gallery-detail">
                        <ul class="bxslider">
                            <?php
                            if ( $gallery ) {
                                foreach ( $gallery as $image ) {
                                    $image_url = wp_get_attachment_url( $image );
                                    $image_url = inwave_resize( $image_url, 770, 370, true );
                                    if ( $image_url ) {
                                        echo '
                                                                <li>
                                                                    <img src="' . $image_url . '" alt="image" />
                                                                </li>';
                                    }
                                }
                            }
                            ?>
                            <?php if ( $video_url ) {
                                global $wp_embed;
                                ?>
                                <li>
                                    <div class="videoWrapper">
                                        <?php echo $wp_embed ? $wp_embed->run_shortcode( '[embed width="770" height="370"]' . $video_url[0] . '[/embed]' ) : ''; ?>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
                <div class="employer-detail-info">
                    <?php if ( $description = $employer->get_description( true ) ) : ?>
                        <div class="iwj-employerdl-des">
                            <div class="title"><h3><?php echo __( 'Company Detail', 'iwjob' ); ?></h3></div>
                            <div class="content"><?php echo $description; ?></div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php
                if ( ! $disable_review ) {
                    $employer_review = IWJ_Reviews::get_employer_reviews( $employer->get_id() );
                    if ( is_user_logged_in() ) {
                        $user_review = $user->check_user_review( $employer->get_id() );
                    } else {
                        $user_review = 0;
                    }

                    if ( ( $employer_review && count( $employer_review['result'] ) ) || ( is_user_logged_in() && current_user_can( 'apply_job' ) ) ) {
                        ?>
                        <div class="iwj-employer-review ">
                        <div class="iwj-review-container">
                        <div class="title"><h3><?php echo __( 'Company Review:', 'iwjob' ); ?></h3>
                        </div>
                        <div class="iwj-review-content">
                            <?php
                            if ( $employer_review && count( $employer_review['result'] ) ) {
                                foreach ( $employer_review['result'] as $item_review ) {
                                    if ( $item_review->status == 'approved' ) {
                                        $user_post = IWJ_User::get_user( $item_review->user_id ); ?>
                                        <div class="iwj-review-item" id="vote-employer-id-<?php echo esc_attr( $item_review->ID ); ?>">
                                            <div class="review-avatar">
                                                <?php
                                                echo iwj_get_avatar( $item_review->user_id, '120', '', '', array('img_size'=>'inwave-avatar2') );
                                                ?>
                                            </div>
                                            <div class="employer-review-details">
                                                <div class="er-review-title">
                                                    <span class="er-title-bold"><?php esc_html_e( '"' . $item_review->title . '"', 'iwjob' ); ?></span>
                                                                                    <span class="er-rate-content">
                                                                                    <span class="er-rate-count" title="<?php echo esc_attr( $item_review->rating ); ?>">
                                                                                        <?php echo IWJ_Reviews::get_number_stars( $item_review->rating ); ?>
                                                                                    </span>
                                                                                        <?php
                                                                                        $criterias = IWJ_Reviews::get_total_criterias( $item_review->ID );
                                                                                        if ( $criterias ) {
                                                                                            $uns_votes = unserialize( $criterias );
                                                                                            if ( count( $uns_votes ) ) { ?>
                                                                                                <span class="iwj-box-reviewed" data-num_criteria="<?php echo count( $uns_votes ); ?>">
                                                                                        <span class="iwj-reviewed-box-icon">
                                                                                            <i class="ion-android-arrow-dropdown"></i>
                                                                                        </span>
                                                                                        <div class="iwj-box-each-vote iwj-review-voted">
                                                                                            <?php
                                                                                            foreach ( $uns_votes as $key_uns => $uns_vote ) {
                                                                                                $str_key_vote = str_replace( '_', ' ', $key_uns ); ?>
                                                                                                <div class="iwj-line-tc-vote">
                                                                                                    <span class="line-tc-title"><?php echo $str_key_vote; ?></span>
                                                                                                    <span class="line-tc-star"><?php echo IWJ_Reviews::get_number_stars( $uns_vote ); ?></span>
                                                                                                </div>
                                                                                            <?php } ?>
                                                                                        </div>
                                                                                    </span>
                                                                                            <?php
                                                                                            }
                                                                                        }
                                                                                        if ( $user_review && $item_review->user_id == $user->get_id() && current_user_can( 'apply_job' ) ) { ?>
                                                                                            <span class="iwj-edit-reviewed pull-right" data-review_id="<?php echo esc_attr( $item_review->ID ); ?>" data-rate_star="<?php echo esc_attr( $item_review->rating ); ?>">
                                                                                        <i class="ion-edit"></i>
                                                                                    </span>
                                                                                        <?php } ?>
                                                                                </span>
                                                </div>
                                                <div class="er-review-author">
                                                    <?php esc_html_e( 'By: ', 'iwjob' ); ?>
                                                    <a href="<?php echo esc_url( $user_post->permalink() ); ?>"><?php echo esc_html( $user_post->get_display_name() ); ?></a>
                                                </div>
                                                <div class="er-review-des"><?php echo $item_review->content; ?></div>
                                                <?php
                                                $check_reply_review = IWJ_Reviews::check_reply_review( $item_review->ID );
                                                if ( $check_reply_review ) { ?>
                                                    <div class="iwj-author-reply">
                                                        <div class="iwj-reply-author-avatar">
                                                            <?php
                                                            echo iwj_get_avatar( $employer->get_author_id(), '', '', '', array('img_size'=>'inwave-avatar') );
                                                            ?>
                                                        </div>
                                                        <div class="iwj-reply-author-content">
                                                            <h4><?php echo sprintf(__('%s response', 'iwjob' ), $employer->get_display_name()); ?></h4>
                                                            <p class="iwj-reply-main-content"><?php echo $check_reply_review->reply_content; ?></p>
                                                            <textarea name="iwj_employer_update_rep" id="iwj_employer_update_rep" class="iwjmb-textarea" cols="30" rows="3" style="display: none;"><?php echo $check_reply_review->reply_content; ?></textarea>
                                                            <div class="iwj-respon-msg iwj-hide"></div>
                                                            <div class="iwj-button-loader" style="display: none;">
                                                                <button class="iwj-btn iwj-btn-primary iwj-btn-update-reply" type="button" disabled data-id="<?php echo esc_attr( $check_reply_review->ID ); ?>"><?php esc_html_e( 'Update', 'iwjob' ); ?></button>
                                                                <button class="iwj-btn iwj-cancel-edit-reply-btn" type="button"><?php echo esc_html__( 'Cancel', 'iwjob' ); ?></button>
                                                            </div>
                                                        </div>
                                                        <?php
                                                        if ( $user && $user->get_id() == $employer->get_author_id() ) { ?>
                                                            <span class="iwj-reply-review-btn iwj-edit-reply-reviewed pull-right" data-id="<?php echo esc_attr( $check_reply_review->ID ); ?>">
                                                                                            <i class="ion-edit"></i>
                                                                                        </span>
                                                        <?php } ?>
                                                    </div>
                                                <?php
                                                } else {
                                                    if ( $user && $user->get_id() == $employer->get_author_id() ) { ?>
                                                        <form action="#" class="iwj-reply-rate-form" method="post">
                                                            <div class="iwj-rate-reply">
                                                                <input type="hidden" name="iwj_reply_review_id" value="<?php echo esc_attr( $item_review->ID ); ?>">
                                                                <textarea id="" cols="40" rows="3" name="iwj_reply_review"></textarea>
                                                                <div class="iwj-rate-reply-respon-msg iwj-hide"></div>
                                                                <input type="hidden" name="iwj_rep_employer_url" value="<?php echo get_avatar_url( $employer->get_author_id(), array( 'size' => 60 ) ); ?>">
                                                                <div class="iwj-rate-btn-reply">
                                                                    <div class="iwj-button-loader">
                                                                        <button class="iwj-btn iwj-btn-primary iwj-reply-review-btn" type="submit"><?php esc_html_e( 'Reply Review', 'iwjob' ); ?></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    <?php
                                                    }
                                                } ?>
                                            </div>
                                        </div>
                                    <?php
                                    } else {
                                        if ( is_user_logged_in() && $user->get_id() == $item_review->user_id ) { ?>
                                            <div class="iwj-review-item wating-approve">
                                                <?php
                                                if ( $item_review->status == 'pending' ) {
                                                    esc_attr_e( 'Your review is awaiting admin approval.', 'iwjob' );
                                                } elseif ( $item_review->status == 'reject' ) {
                                                    esc_attr_e( 'Your review is rejected.', 'iwjob' );
                                                }
                                                ?>
                                            </div>
                                        <?php
                                        }
                                    }
                                }
                                if ( $employer_review && $employer_review['total_page'] > 1 ) { ?>
                                    <div class="review-pagination">
                                        <?php
                                        echo paginate_links( array(
                                            'base'      => add_query_arg( 'rpage', '%#%' ),
                                            'format'    => '',
                                            'prev_text' => __( '<i class="ion-android-arrow-dropleft"></i>' ),
                                            'next_text' => __( '<i class="ion-android-arrow-dropright"></i>' ),
                                            'total'     => $employer_review['total_page'],
                                            'current'   => $employer_review['current_page']
                                        ) );
                                        ?>
                                    </div>
                                <?php }
                            } ?>
                        </div>
                        <?php
                        if ( is_user_logged_in() && current_user_can( 'apply_job' ) ) { ?>
                            <div class="form-review-employer <?php echo $user_review ? 'iwj-job-reviewed' : ''; ?>">
                                <h4 class="title"><?php echo sprintf(__( 'Work at %s ? Share Your Experiences', 'iwjob' ), $employer->get_title()) ?></h4>
                                <form action="#" method="post" enctype="multipart/form-data" class="iwj-rating-form">
                                    <div class="re-form-container">
                                        <div class="rve-avatar">
                                            <img src="<?php echo get_avatar_url( $user->get_id() ); ?>" alt="Avatar" />
                                        </div>
                                        <div class="re-post-form-submit">
                                            <?php $review_option = iwj_option( 'review_options', '' );
                                            $trim_rate           = trim( $review_option ); ?>
                                            <div class="iwj-rate-stars" data-rate_type="<?php echo ! empty( $trim_rate ) ? 'group_rate' : 'simple_rate'; ?>">
                                                <?php
                                                if ( ! empty( $trim_rate ) ) {
                                                    $arr_reviews = explode( "\n", $review_option );
                                                    ?>
                                                    <span class="re-text"><?php esc_html_e( 'Overall Rating:', 'iwjob' ); ?></span>
                                                    <span class="iwj-count-stars">

                                                                                <div class="iwj-votes-icon">
                                                                                    <i class="ion-android-star-outline"></i>
                                                                                    <i class="ion-android-star-outline"></i>
                                                                                    <i class="ion-android-star-outline"></i>
                                                                                    <i class="ion-android-star-outline"></i>
                                                                                    <i class="ion-android-star-outline"></i>
                                                                                </div>
                                                                                <div class="iwj-box-each-vote iwj-review-voting" data-total_views="<?php echo esc_attr( count( $arr_reviews ) ); ?>">
                                                                                    <?php
                                                                                    if ( count( $arr_reviews ) ) {
                                                                                        foreach ( $arr_reviews as $key_item => $rev_item ) {
                                                                                            $rev_item_name = strtolower( str_replace( ' ', '_', trim( $rev_item ) ) ); ?>
                                                                                            <div class="iwj-line-tc-vote">
                                                                                                <span class="line-tc-title"><?php echo esc_html__( $rev_item, 'iwjob' ); ?></span>
                                                                                                <span class="line-tc-star">
                                                                                                    <input type="hidden" class="iwj_num_rate rating " data-size="xs" data-step="1" name="iwj_rate_num_<?php echo esc_attr( $key_item ); ?>" data-criteria_vote="<?php echo esc_attr( $rev_item_name ); ?>">
                                                                                                </span>
                                                                                            </div>
                                                                                        <?php
                                                                                        }
                                                                                    } ?>
                                                                                </div>
                                                                            </span>
                                                <?php
                                                } else { ?>
                                                    <span class="re-text"><?php esc_html_e( 'Rating:', 'iwjob' ); ?></span>
                                                    <span class="iwj-count-stars">
                                                                                    <input type="hidden" class="rating iwj_simple_rate" data-size="xs" data-step="1" name="iwj_simple_rate">
                                                                                </span>
                                                <?php } ?>
                                            </div>
                                            <div class="iwj-rate-title">
                                                <span class="re-text"><?php esc_html_e( 'Review Title:', 'iwjob' ); ?></span>
                                                <input type="text" name="iwj_review_title" value="">
                                            </div>
                                            <div class="iwj-rate-content">
                                                <span class="re-text"><?php esc_html_e( 'Review Content:', 'iwjob' ); ?></span>
                                                <textarea name="iwj_review_content" class="iwjmb-textarea" cols="30" rows="4"></textarea>
                                            </div>
                                            <div class="iwj-rate-respon-msg iwj-hide"></div>
                                            <input type="hidden" name="rate_item_id" value="<?php echo $employer->get_id(); ?>">
                                            <div class="iwj-rate-btn-submit">
                                                <div class="iwj-button-loader">
                                                    <button type="submit" class="iwj-btn iwj-btn-primary iwj-review-btn" data-type_post_review="<?php echo $user_review ? 'update_review' : 'add_review'; ?>"><?php esc_html_e( 'Submit Review', 'iwjob' ); ?></button>
                                                    <?php if ( $user_review ) { ?>
                                                        <button type="button" class="iwj-btn iwj-cancel-review-btn"><?php esc_html_e( 'Cancel', 'iwjob' ); ?></button>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php }
                        ?>
                        </div>
                        </div>
                    <?php
                    }
                }
                $author         = $employer->get_author();
                $open_positions = $author->get_open_jobs();
                if ( $open_positions ) { ?>
                    <div class="iwj-open-position" id="iwj-open-position">
                        <h3 class="iw-title-border"><?php echo __( 'Open Positions', 'iwjob' ); ?></h3>
                        <div class="iwj-jobs">
                            <?php
                            foreach ( $open_positions as $job ) {
                                ?>
                                <div class="job-item <?php echo $job->is_featured() ? 'featured-item' : ''; ?>">
                                    <?php if ( $author ) { ?>
                                        <div class="job-image"><?php echo get_avatar( $author->get_id() ); ?></div>
                                    <?php } ?>
                                    <div class="job-info">
                                        <h3 class="job-title">
                                            <a href="<?php echo $job->permalink(); ?>"><?php echo $job->get_title(); ?></a>
                                        </h3>
                                        <div class="info-company">
                                            <div class="sallary">
                                                <?php
                                                $postfix = $job->get_salary_postfix();
                                                ?>
                                                <i class="iwj-icon-money"></i><?php echo $job->get_salary(); echo $postfix ? _x(' / ', 'Salary Postsfix', 'iwjob') . $postfix : '';?>
                                            </div>
                                            <?php if ( $locations = $job->get_locations_links() ) : ?>
                                                <div class="address">
                                                    <i class="ion-android-pin"></i><?php echo $locations; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php
                                        $type = $job->get_type();
                                        if ( $type ) { ?>
                                            <div class="job-type">
                                                <?php if ( $type ) {
                                                    $color = get_term_meta( $type->term_id, IWJ_PREFIX . 'color', true ); ?>
                                                    <a class="type-name" href="<?php echo get_term_link( $type->term_id, 'iwj_type' ); ?>" <?php echo $color ? 'style="color: '.$color.'; border-color: '.$color.'; background-color: '.$color.'"' : ''; ?>><?php echo $type->name; ?></a>
                                                <?php } ?>
                                                <?php if ( ! is_user_logged_in() ) { ?>
                                                    <button class="save-job" data-toggle="modal" data-target="#iwj-login-popup">
                                                        <i class="fa fa-heart"></i></button>
                                                <?php } else { ?>
                                                    <a href="#" class="iwj-save-job <?php echo $user->is_saved_job( $job->get_id() ) ? 'saved' : ''; ?>" data-id="<?php echo $job->get_id(); ?>" data-in-list="true"><i class="fa fa-heart"></i></a>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php
                            }
                            wp_reset_postdata();
                            ?>
                        </div>
                    </div>
                <?php } ?>
                </div>
                <?php if ( $employer_sidebar && is_active_sidebar( 'sidebar-employer' ) ) : ?>
                    <div class="iwj-sidebar-sticky <?php echo esc_attr( inwave_get_classes( 'sidebar', $employer_sidebar ) ) ?>">
                        <div class="widget-area" role="complementary">
                            <?php dynamic_sidebar( 'sidebar-employer' ); ?>
                        </div>
                    </div>
                <?php endif;
            } else {
                ?>
                <div class="iwj-alert-box">
                    <?php echo sprintf( __('You must be logged in to view this page. <a href="%s">Login here</a>','iwjob'), add_query_arg('redirect_to',$employer->permalink(),$login_page_id )); ?>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>