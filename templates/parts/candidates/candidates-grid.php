<?php
$mode_view_class = 'iwj-grid';
$show_candidate_public_profile = iwj_option('show_candidate_public_profile', '');
$login_page_id = get_permalink(iwj_option('login_page_id'));
?>

<div class="iwj-candidates <?php echo $mode_view_class; ?>">
    <div class="row">
        <?php
        if ($query->have_posts()) :
            $user = IWJ_User::get_user();
            while ($query->have_posts()) :
                $query->the_post();
                $candidate = IWJ_Candidate::get_candidate(get_the_ID());
                $user_candidate = IWJ_User::get_user($candidate->get_author_id());
                $image = iwj_get_avatar( $candidate->get_author_id(), '120', '', $candidate->get_title(), array('img_size'=>'inwave-avatar2') );
                $desc = $candidate->get_description();
                ?>
                <div class="grid-content" itemprop="customer" itemscope itemtype="http://schema.org/Person">
                    <div class="candidate-item">
                        <div class="candidate-bg-image theme-bg"></div>
                        <div class="candidate-info">
                            <div class="info-top">
								<meta itemprop="address" content="<?php echo $candidate->get_address(); ?>" />
								<meta itemprop="email" content="<?php echo $candidate->get_email(); ?>" />
								<meta itemprop="telephone" content="<?php echo $candidate->get_phone(); ?>" />

                                <div class="candidate-image"><?php echo( $image ); ?></div>
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
                                        <a class="theme-color" href="<?php echo $link_profile; ?>"><?php echo $candidate->get_title(); ?></a>
                                    <?php else: ?>
                                        <span class="theme-color"><?php echo $candidate->get_title(); ?></span>
                                    <?php endif; ?>
                                </h3>
                                <?php if ( $candidate->get_headline() ) : ?>
                                    <div class="candidate-headline">
                                        <?php echo $candidate->get_headline(); ?></div>
                                <?php endif; ?>
                                <?php if ($candidate->get_locations_links()) { ?>
                                    <div class="resumes-address">
                                        <i class="ion-android-pin"></i>
                                        <span><?php echo $candidate->get_locations_links(); ?></span>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="info-bottom">
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
                                <?php if ($link_profile): ?>
                                    <a class="view-resume" href="<?php echo $link_profile; ?>"><?php echo __("View Profile", 'iwjob'); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            endwhile;
            wp_reset_postdata();
        endif;
        ?>
    </div>
</div>


<?php if ($query->max_num_pages > 1): ?>
    <?php
    if (!isset($paged)) :
        $paged = ( get_query_var('page') ) ? get_query_var('page') : 1;
    endif;
    ?>
    <div class="clearfix"></div>
    <div class="w-pagination ajax-candidate-pagination"><?php iwj_ajax_pagination($query->max_num_pages, $paged); ?></div>
<?php endif; ?>