<?php
$mode_view_class = 'iwj-listing';
$show_candidate_public_profile = iwj_option('show_candidate_public_profile', '');
$login_page_id = get_permalink(iwj_option('login_page_id'));
?>

<div class="iwj-candidates <?php echo $mode_view_class; ?>">
    <?php
    if ($query->have_posts()) :
        $user = IWJ_User::get_user();
        while ($query->have_posts()) :
            $query->the_post();
            $candidate = IWJ_Candidate::get_candidate(get_the_ID());
            $user_candidate = IWJ_User::get_user($candidate->get_author_id());
            $image = iwj_get_avatar( $candidate->get_author_id(), '120', '', $candidate->get_title(), array('img_size'=>'inwave-avatar2') );
            ?>
            <div class="candidate-item" itemprop="customer" itemscope itemtype="http://schema.org/Person">
                <?php if ($image) : ?>
                    <div class="candidate-image"><?php echo $image; ?></div>
                <?php endif; ?>
                <div class="candidate-info">
                    <div class="candidate-info-top">
                        <div class="candidate-info-left">
                            <div class="info-item">
                                <h3 class="candidate-title" itemprop="name">
                                    <?php
                                    if (!$show_candidate_public_profile) {
                                        $link_profile = $candidate->permalink();
                                    } else {
                                        if ($user) {
                                            if ($show_candidate_public_profile == 1) {
                                                $link_profile = $candidate->permalink();
                                            } else {
                                                if ($user->is_employer()) {
                                                    $link_profile = $candidate->permalink();
                                                } else {
                                                    $link_profile = '';
                                                }
                                            }
                                        } else {
                                            $link_profile = add_query_arg('redirect_to', $candidate->permalink(), $login_page_id);
                                        }
                                    }
                                    ?>
                                    <?php if ($link_profile): ?>
                                        <a href="<?php echo $link_profile; ?>"><?php echo $candidate->get_title(); ?></a>
                                    <?php else: ?>
                                        <span><?php echo $candidate->get_title(); ?></span>
                                    <?php endif; ?>
                                </h3>
                            </div>
                            <div class="info-item">
								<meta itemprop="address" content="<?php echo $candidate->get_address(); ?>" />
								<meta itemprop="email" content="<?php echo $candidate->get_email(); ?>" />
								<meta itemprop="telephone" content="<?php echo $candidate->get_phone(); ?>" />

                                <?php if ($candidate->get_headline()) : ?>
                                    <div class="categories">
                                        <?php echo $candidate->get_headline(); ?></div>
                                <?php endif; ?>
                                <?php if ($candidate->get_locations_links()) : ?>
                                    <div class="address">
                                        <i class="ion-android-pin"></i><?php echo $candidate->get_locations_links(); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ( $user_candidate ) {
                                    $last_login = get_user_meta($user_candidate->get_id(), '_last_login');
                                    $diff = '';
                                    if ($last_login) {
                                        $to = time();
                                        $diff = (int) abs( $to - $last_login[0] );
                                    }
                                ?>
                                    <div class="latest-activities">
                                        <i class="fa fa-edit"></i>
                                        <span class="content">
                                            <label><?php echo __( 'Latest Activities:', 'iwjob' ); ?></label>
                                            <?php if ( $last_login ) { ?>
                                                <?php if ($diff && $diff <= MONTH_IN_SECONDS) { ?>
                                                    <span><?php echo human_time_diff( $last_login[0] ) ; echo __( ' ago', 'iwjob' ); ?></span>
                                                <?php } else { ?>
                                                    <span><?php echo date("j F, Y", $last_login[0]); ?></span>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <span><?php echo date("j F, Y", strtotime($user_candidate->user->user_registered)); ?></span>
                                            <?php } ?>
                                        </span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="candidate-info-right"><a href="<?php echo ($link_profile)? $link_profile : '#' ;?>"><?php echo __("View resume", 'iwjob');?> <i class="fa fa-arrow-circle-right"></i></a></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="candidate-info-bottom">
                        <?php $skills_links = $candidate->get_skills_links();
                        echo $skills_links;
                        ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>
            <?php
        endwhile;
        wp_reset_postdata();
    endif;
    ?>
    <div class="clear"></div>
</div>

<?php if ($query->max_num_pages > 1): ?>
    <?php
    if (!isset($paged)) :
        $paged = ( get_query_var('page') ) ? get_query_var('page') : 1;
    endif;
    ?>

    <div class="w-pagination ajax-candidate-pagination"><?php iwj_ajax_pagination($query->max_num_pages, $paged); ?></div>

<?php endif; ?>


