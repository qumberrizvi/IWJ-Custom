<?php
switch ($style) {
    case 'style1':
        ?>
        <div class="iw-recent-resumes style1 shortcode <?php echo $class; ?>">
            <div class="iwj-items">
                <?php foreach ($recent_resumes as $recent_resume) :
                    $recent_resume = IWJ_Candidate::get_candidate($recent_resume);
                    $user = IWJ_User::get_user();
                    $desc = $recent_resume->get_description();
                    $image = iwj_get_avatar( $recent_resume->get_author_id(), '', '', $recent_resume->get_title(), array('img_size'=>'inwave-avatar2') );
                    ?>
                    <div class="recent-resume-item iwj-item">
                        <div class="resumes-image theme-bg"></div>
                        <div class="resumes-info">
                            <div class="info-top">
                                <div class="resumes-avatar"><?php echo ($image); ?></div>
                                <h3 class="name"><a class="theme-color" href="<?php echo get_permalink($recent_resume->get_id()); ?>"><?php echo $recent_resume->get_title();?></a></h3>
                                <?php if ($recent_resume->get_address()) { ?>
                                    <div class="resumes-address">
                                        <i class="ion-android-bulb"></i>
                                        <span><?php echo $recent_resume->get_headline(); ?></span>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="info-bottom">
                                <?php if($desc) : ?>
                                    <div class="desc"><?php echo esc_attr(wp_trim_words($desc, 10)); ?></div>
                                <?php endif; ?>
                                <a class="view-resume" href="<?php echo get_permalink($recent_resume->get_id()); ?>"><?php echo __("View Profile", 'iwjob'); ?></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="clear"></div>
            </div>
        </div>
        <?php
        break;

    case 'style2':
        ?>
        <div class="iw-recent-resumes-style2 shortcode <?php echo $class; ?>">
            <div class="iwj-items">
                <div class="row">
                    <?php foreach ($recent_resumes as $recent_resume) :
                        $recent_resume = IWJ_Candidate::get_candidate($recent_resume);
                        $user = IWJ_User::get_user();
                        $desc = $recent_resume->get_description();
                        $image = iwj_get_avatar( $recent_resume->get_author_id(), '', '', $recent_resume->get_title(), array('img_size'=>'inwave-avatar2') );
                        $col = 12 / $number_column;
                        $is_featured = $recent_resume->is_featured();
                        ?>
                        <div class="col-md-<?php echo esc_attr($col); ?> col-sm-6 col-xs-12">
                            <div class="recent-resume-item iwj-item">
                                <div class="resumes-info">
                                    <div class="resumes-avatar"><?php echo ($image); ?></div>
                                    <div class="info-content">
                                        <h3 class="name"><a class="theme-color" href="<?php echo get_permalink($recent_resume->get_id()); ?>"><?php echo $recent_resume->get_title();?></a></h3>
                                        <?php if ($recent_resume->get_address() || $recent_resume->get_categories()) { ?>
                                            <ul class="resumes-meta">
                                                <?php if ($recent_resume->get_address()) { ?>
                                                    <li class="resumes-address">
                                                        <i class="ion-android-bulb"></i>
                                                        <span><?php echo $recent_resume->get_headline(); ?></span>
                                                    </li>
                                                <?php } ?>
                                                <?php if ( $categories = $recent_resume->get_categories() ) {
                                                    $categories_links = $recent_resume->get_categories_links();
                                                    ?>
                                                    <li>
                                                        <i class="ion-android-folder-open theme-color"></i>
                                                        <span><?php echo count( $categories ); ?></span>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        <?php } ?>
                                        <?php if($desc) { ?>
                                            <div class="desc"><?php echo esc_attr(wp_trim_words($desc, 10)); ?></div>
                                        <?php } ?>
                                        <div class="iwj-button-loader">
                                            <?php
                                            if (current_user_can('create_iwj_jobs')) {
                                                $saved = $user && $user->is_saved_resum($recent_resume->get_id()) ? true : false;
                                                ?>
                                                <a href="#" class="iwj-btn action-button follow iwj-save-resume <?php echo $saved ? 'saved' : ''; ?>" data-id="<?php echo $recent_resume->get_id(); ?>"><i class="fa fa-heart"></i></a>
                                            <?php } elseif (!$user) { ?>
                                                <a href="#" data-toggle="modal" data-target="#iwj-login-popup" class="iwj-btn action-button follow"><i class="fa fa-heart"></i></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($is_featured) { ?>
                                    <div class="iwj-featured"></div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
        break;
}

?>