<?php
wp_enqueue_style('owl-carousel');
wp_enqueue_style('owl-theme');
wp_enqueue_style('owl-transitions');
wp_enqueue_script('owl-carousel');

$style = $atts['style'];

$data_plugin_options = array();

if ($style == 'style1') {
    $data_plugin_options = array(
        "navigation"=>false,
        "autoHeight"=>true,
        "pagination"=>true,
        "autoPlay"=>false,
        "paginationNumbers"=>true,
        "singleItem"=>true,
    );
}
elseif ($style == 'style2' || $style == 'style3') {
    $data_plugin_options = array(
        "navigation"=>true,
        "autoHeight"=>true,
        "pagination"=>false,
        "autoPlay"=>false,
        "item"=>1,
        "paginationNumbers"=>true,
        "singleItem"=>true,
        "navigationText" => array("<i class=\"ion-android-arrow-back\"></i>", "<i class=\"ion-android-arrow-forward\"></i>")
    );
} else {
    $data_plugin_options = array(
        "navigation"=>false,
        "autoHeight"=>true,
        "pagination"=>true,
        "autoPlay"=>false,
        "paginationNumbers"=>false,
        "singleItem"=>true,
    );
}
$show_company = iwj_option('show_company_job');
$show_company_logo_job  = iwj_option( 'show_company_logo_job' );
$show_salary = iwj_option('show_salary_job');
$show_location = iwj_option('show_location_job');
$show_posted_date_job = iwj_option('show_posted_date_job');
$show_categories_job  = iwj_option( 'show_categories_job' );

switch ($style) {
    case 'style1':
    case 'style2':
        ?>
        <?php if ($style == 'style2') { ?>
            <div class="title-block-carousel"><?php
                $title = $atts['title_block'] ? $atts['title_block'] : 'jobs';
                _e($title, 'iwjob'); ?>
            </div>
        <?php } ?>
        <div class="iwj-jobs-carousel <?php echo $atts['class']; echo $atts['style']; ?>">
            <div class="owl-carousel <?php echo (($style == 'style2') ? 'navigation-text-v2' : '') ?>" data-plugin-options="<?php echo htmlspecialchars(json_encode($data_plugin_options)); ?>">
                <div class="iwj-items">
                    <div class="row">
                        <?php
                        $i = 0;
                        $jobs_per_page = $atts['jobs_per_page'] ? $atts['jobs_per_page'] : 6;
                        foreach ($jobs as $job) :
                            $job = IWJ_Job::get_job($job);
                            $author = $job->get_author();
                            $permalink = $job->permalink();
                            $is_featured = $job->is_featured();
                            $type = $job->get_type();
                            $user = IWJ_User::get_user();
                            if($i > 0 && count($jobs) > $i && $i % $jobs_per_page == 0){
                                echo '</div>
                        </div>
                        <div class="iwj-items">
                        <div class="row">';
                            }
                            ?>
                            <div class="iwj-item <?php echo (($style == 'style1') ? 'col-md-6 col-sm-6 col-xs-12' : 'style2 col-md-12 col-sm-12 col-xs-12') ?>">
                                <div class="job-item <?php echo $is_featured ? 'featured-item' : ''; ?>">
                                    <?php if($author && $show_company_logo_job == '1') : ?>
                                        <div class="job-image"><?php echo iwj_get_avatar( $author->get_id()); ?></div>
                                    <?php endif; ?>
                                    <div class="job-info <?php echo $show_company_logo_job == '1' ? 'yes-logo' : 'no-logo' ?>">
                                        <h3 class="job-title"><a href="<?php echo $job->get_indeed_url() ? esc_url( $job->get_indeed_url() ) :esc_url($permalink); ?>"><?php echo $job->get_title(); ?></a></h3>
                                        <?php if ($author && ($show_company == '1')) : ?>
                                            <div class="company"><i class="fa fa-suitcase"></i>
                                                <?php if ( $job->get_indeed_company_name() ) { ?>
													<a href="<?php echo $job->get_indeed_url(); ?>"><?php echo $job->get_indeed_company_name(); ?></a>
                                                <?php } elseif($author->is_active_profile()){ ?>
                                                <a href="<?php echo $author->permalink(); ?>"><?php echo $author->get_display_name(); ?></a>
                                                <?php }else{
                                                    echo $author->get_display_name();
                                                } ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (($job->get_salary()) && ($show_salary == '1')) : ?>
                                            <div class="sallary"><i class="iwj-icon-money"></i><?php echo $job->get_salary(); ?></div>
                                        <?php endif; ?>
                                        <?php if (($locations = $job->get_locations_links()) && ($show_location == '1')) : ?>
                                            <div class="address"><i class="ion-android-pin"></i><?php echo $locations; ?></div>
                                        <?php endif; ?>
                                        <?php if ($show_posted_date_job == '1') { ?>
                                            <div class="time-ago"><i class="fa fa-calendar theme-color"></i><?php printf( _x( '%s ago', '%s = human-readable time difference', 'iwjob' ), human_time_diff( strtotime( $job->get_created() ), current_time( 'timestamp' ) ) ); ?></div>
                                        <?php } ?>
                                        <div class="job-type <?php echo $type ? $type->slug : ''; ?>">
                                            <?php if($type) {
                                                $color = get_term_meta($type->term_id, IWJ_PREFIX.'color', true);
                                                ?>
                                                <a class="type-name" href="<?php echo get_term_link($type->term_id, 'iwj_type'); ?>" <?php echo $color ? 'style="color: '.$color.'; border-color: '.$color.'; background-color: '.$color.'"' : ''; ?>><?php echo $type->name; ?></a>
                                            <?php } ?>
                                            <?php if(!is_user_logged_in()){ ?>
                                                <button class="save-job" data-toggle="modal" data-target="#iwj-login-popup"><i class="fa fa-heart"></i></button>
                                            <?php }else if(current_user_can('apply_job')) { ?>
                                                <a href="#" class="iwj-save-job <?php echo $user->is_saved_job($job->get_id()) ? 'saved' : ''; ?>" data-id="<?php echo $job->get_id(); ?>" data-in-list="true"><i class="fa fa-heart"></i></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php if($is_featured) : ?>
                                        <div class="iwj-featured"></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php
                            $i ++;
                        endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;

    case 'style3':
        ?>
        <div class="iwj-jobs-carousel-v3 <?php echo $atts['class']; echo $atts['style']; ?>">
            <div class="title-block-carousel"><?php
                $title = $atts['title_block'] ? $atts['title_block'] : 'jobs';
                _e($title, 'iwjob'); ?>
            </div>
            <div class="owl-carousel navigation-text-v2" data-plugin-options="<?php echo htmlspecialchars(json_encode($data_plugin_options)); ?>">
                <?php
                $i = 0;
                foreach ($jobs as $job) :
                    $job = IWJ_Job::get_job($job);
                    $author = $job->get_author();
                    $permalink = $job->permalink();
                    $is_featured = $job->is_featured();
                    $type = $job->get_type();
                    $user = IWJ_User::get_user();
                    ?>
                    <div class="iwj-item">
                        <?php if($author && $show_company_logo_job == '1') : ?>
                            <div class="job-image"><?php echo iwj_get_avatar( $author->get_id(), '', '', '', array('img_size'=> 'inwave-avatar2')); ?></div>
                        <?php endif; ?>
                        <div class="job-info">
                            <h3 class="job-title"><a href="<?php echo $job->get_indeed_url() ? esc_url( $job->get_indeed_url() ) : esc_url($permalink); ?>"><?php echo $job->get_title(); ?></a></h3>
                            <div class="job-type <?php echo $type ? $type->slug : ''; ?>">
                                <?php if($type) {
                                    $color = get_term_meta($type->term_id, IWJ_PREFIX.'color', true);
                                    ?>
                                    <a class="type-name" href="<?php echo get_term_link($type->term_id, 'iwj_type'); ?>" <?php echo $color ? 'data-color="'.$color.'" style="background-color: '.$color.'"' : ''; ?>><?php echo $type->name; ?></a>
                                <?php } ?>
                            </div>
                            <?php if (($locations = $job->get_locations_links()) && ($show_location == '1')) : ?>
                                <div class="address">@<?php echo $locations; ?></div>
                            <?php endif; ?>
                            <?php if ($author && ($show_company == '1')) : ?>
                                <div class="company"><i class="fa fa-suitcase"></i><a href="<?php echo $job->get_indeed_url() ? esc_url( $job->get_indeed_url() ) :$author->permalink(); ?>"><?php echo $job->get_indeed_company_name()?$job->get_indeed_company_name():$author->get_display_name(); ?></a></div>
                            <?php endif; ?>
                            <?php if (($job->get_salary()) && ($show_salary == '1')) : ?>
                                <div class="sallary"><i class="iwj-icon-money"></i><?php echo $job->get_salary(); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="action-btn">
                            <a href="<?php echo esc_url($permalink); ?>"><?php _e('View detail', 'iwjob'); ?></a>
                            <?php if(!is_user_logged_in()){ ?>
                                <button class="save-job" data-toggle="modal" data-target="#iwj-login-popup"><i class="ion-heart"></i></button>
                            <?php }else if(current_user_can('apply_job')) { ?>
                                <a href="#" class="iwj-save-job <?php echo $user->is_saved_job($job->get_id()) ? 'saved' : ''; ?>" data-id="<?php echo $job->get_id(); ?>" data-in-list="true"><i class="ion-heart"></i></a>
                            <?php } ?>
                        </div>
                        <?php if($is_featured) : ?>
                            <div class="iwj-featured"></div>
                        <?php endif; ?>
                    </div>
                    <?php
                    $i ++;
                endforeach; ?>
            </div>
        </div>
        <?php
        break;

    case 'style4':
        ?>
        <div class="iwj-jobs-carousel-v4 iwj-jobs-carousel-match-height <?php echo $atts['class']; echo $atts['style']; ?>" >
            <div class="owl-carousel pagination-dot" data-plugin-options="<?php echo htmlspecialchars(json_encode($data_plugin_options)); ?>">
                <div class="iwj-items">
                    <div class="row">
                        <?php
                        $i = 0;
                        $jobs_per_page = $atts['jobs_per_page'] ? $atts['jobs_per_page'] : 8;
                        $item_class =  'col-item col-md-3 col-sm-6 col-xs-12';
                        if($jobs_per_page == '1'){
                            $item_class =  'col-item col-md-12 col-sm-12 col-xs-12';
                        }elseif($jobs_per_page == '2'){
                            $item_class =  'col-item col-md-6 col-sm-6 col-xs-12';
                        }elseif($jobs_per_page == '3'){
                            $item_class =  'col-item col-md-4 col-sm-6 col-xs-12';
                        }elseif($jobs_per_page == '4'){
                            $item_class =  'col-item col-md-3 col-sm-6 col-xs-12';
                        }elseif($jobs_per_page == '6'){
                            $item_class =  'col-item col-md-4 col-sm-6 col-xs-12';
                        }
                        foreach ($jobs as $job) :
                            $job = IWJ_Job::get_job($job);
                            $author = $job->get_author();
                            $permalink = $job->permalink();
                            $is_featured = $job->is_featured();
                            $type = $job->get_type();
                            $user = IWJ_User::get_user();
                            if($i > 0 && count($jobs) > $i && $i % $jobs_per_page == 0){
                                echo '</div>
                        </div>
                        <div class="iwj-items">
                        <div class="row">';
                            }
                            ?>
                            <div class="iwj-item <?php echo $item_class; ?>">
                                <div class="job-item <?php echo $is_featured ? 'featured-item' : ''; ?>">
                                    <div class="job-info">
                                        <?php if($author && $show_company_logo_job == '1') : ?>
                                            <div class="company-image"><?php echo iwj_get_avatar( $author->get_id(), '', '', '', array('img_size'=> 'inwave-avatar2')); ?></div>
                                        <?php endif; ?>
                                        <h3 class="job-title"><a href="<?php echo $job->get_indeed_url() ? esc_url( $job->get_indeed_url() ) :esc_url($permalink); ?>"><?php echo $job->get_title(); ?></a></h3>
                                        <ul class="job-meta">
                                            <?php if ( $categories = $job->get_categories() && $show_categories_job == '1' ) {
                                                $categories_links = $job->get_categories_links();
                                                ?>
                                                <li class="categories">
                                                    <i class="fa fa-suitcase theme-color"></i>
                                                    <span><?php echo sprintf( __( '%s', '%s', count( $categories ), 'iwjob' ), $categories_links ); ?></span>
                                                </li>
                                            <?php } ?>
                                            <?php if (($locations = $job->get_locations_links()) && ($show_location == '1')) : ?>
                                                <li class="address"><i class="ion-android-pin"></i><?php echo $locations; ?></li>
                                            <?php endif; ?>
                                            <?php if ($show_posted_date_job == '1') { ?>
                                                <li class="time-ago"><i class="fa fa-calendar theme-color"></i><?php printf( _x( '%s ago', '%s = human-readable time difference', 'iwjob' ), human_time_diff( strtotime( $job->get_created() ), current_time( 'timestamp' ) ) ); ?></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                    <div class="job-type <?php echo $type ? $type->slug : ''; ?>">
                                        <div class="job-type-inner">
                                            <?php if($type) {
                                                $color = get_term_meta($type->term_id, IWJ_PREFIX.'color', true);
                                                ?>
                                                <a class="type-name" href="<?php echo get_term_link($type->term_id, 'iwj_type'); ?>" <?php echo $color ? 'style="color: '.$color.'; border-color: '.$color.'; background-color: '.$color.'"' : ''; ?>><?php echo $type->name; ?></a>
                                            <?php } ?>
                                            <?php if(!is_user_logged_in()){ ?>
                                                <button class="save-job iwj-save-job" data-toggle="modal" data-target="#iwj-login-popup"><i class="fa fa-heart"></i></button>
                                            <?php }else if(current_user_can('apply_job')) { ?>
                                                <a href="#" class="iwj-save-job <?php echo $user->is_saved_job($job->get_id()) ? 'saved' : ''; ?>" data-id="<?php echo $job->get_id(); ?>" data-in-list="true"><i class="fa fa-heart"></i></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php if($is_featured) : ?>
                                        <div class="iwj-featured"></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php
                            $i ++;
                        endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;

    case 'style5':
        ?>
        <div class="iwj-jobs-carousel-v5 iwj-jobs-carousel-match-height <?php echo $atts['class']; echo $atts['style']; ?>">
            <div class="owl-carousel pagination-dot" data-plugin-options="<?php echo htmlspecialchars(json_encode($data_plugin_options)); ?>">
                <div class="iwj-items">
                    <div class="row">
                        <?php
                        $i = 0;
                        $jobs_per_page = $atts['jobs_per_page'] ? $atts['jobs_per_page'] : 6;
                        $item_class =  'col-item col-md-4 col-sm-6 col-xs-12';
                        if($jobs_per_page == '1'){
                            $item_class =  'col-item col-md-12 col-sm-12 col-xs-12';
                        }elseif($jobs_per_page == '2'){
                            $item_class =  'col-item col-md-6 col-sm-6 col-xs-12';
                        }elseif($jobs_per_page == '3'){
                            $item_class =  'col-item col-md-4 col-sm-6 col-xs-12';
                        }elseif($jobs_per_page == '4'){
                            $item_class =  'col-item col-md-3 col-sm-6 col-xs-12';
                        }elseif($jobs_per_page == '6'){
                            $item_class =  'col-item col-md-4 col-sm-6 col-xs-12';
                        }
                        foreach ($jobs as $job) :
                            $job = IWJ_Job::get_job($job);
                            $author = $job->get_author();
                            $employer = $job->get_employer();
                            $permalink = $job->permalink();
                            $is_featured = $job->is_featured();
                            $type = $job->get_type();
                            $user = IWJ_User::get_user();
                            $cover_image_url = '';
                            $cover_image = $employer->get_cover_image();
                            if ($cover_image) {
                                $cover_image = wp_get_attachment_image_src($cover_image, 'inwave-370-245');
                                $cover_image_url = $cover_image ? $cover_image[0] : '';
                            }
                            if($i > 0 && count($jobs) > $i && $i % $jobs_per_page == 0){
                                echo '</div>
                        </div>
                        <div class="iwj-items">
                        <div class="row">';
                            }
                            ?>
                            <div class="iwj-item <?php echo $item_class; ?>">
                                <div class="job-item <?php echo $is_featured ? 'featured-item' : ''; ?>">
                                    <div class="job-cover-image" style="background-image: url(<?php echo $cover_image_url; ?>)">
                                        <?php if($is_featured) : ?>
                                            <div class="iwj-featured"></div>
                                        <?php endif; ?>
                                        <div class="job-type <?php echo $type ? $type->slug : ''; ?>">
                                            <?php if($type) {
                                                $color = get_term_meta($type->term_id, IWJ_PREFIX.'color', true);
                                                ?>
                                                <a class="type-name" href="<?php echo get_term_link($type->term_id, 'iwj_type'); ?>" <?php echo $color ? 'style="color: '.$color.'; border-color: '.$color.'; background-color: '.$color.'"' : ''; ?>><?php echo $type->name; ?></a>
                                            <?php } ?>
                                            <?php if(!is_user_logged_in()){ ?>
                                                <button class="save-job iwj-save-job" data-toggle="modal" data-target="#iwj-login-popup"><i class="fa fa-heart"></i></button>
                                            <?php }else if(current_user_can('apply_job')) { ?>
                                                <a href="#" class="iwj-save-job <?php echo $user->is_saved_job($job->get_id()) ? 'saved' : ''; ?>" data-id="<?php echo $job->get_id(); ?>" data-in-list="true"><i class="fa fa-heart"></i></a>
                                            <?php } ?>
                                        </div>
                                        <?php if ($author) { ?>
                                            <div class="company-info">
                                                <?php if($author && $show_company_logo_job == '1') : ?>
                                                    <div class="company-avatar"><?php echo iwj_get_avatar( $author->get_id()); ?></div>
                                                <?php endif; ?>
                                                <div class="company-name-location">
                                                    <?php if ($author && ($show_company == '1')) : ?>
                                                        <h3 class="company-name">
                                                            <?php if ( $job->get_indeed_company_name() ) { ?>
                                                                <a href="<?php echo $job->get_indeed_url(); ?>"><?php echo $job->get_indeed_company_name(); ?></a>
                                                            <?php } elseif($author->is_active_profile()){ ?>
                                                                <a href="<?php echo $author->permalink(); ?>"><?php echo $author->get_display_name(); ?></a>
                                                            <?php }else{
                                                                echo $author->get_display_name();
                                                            } ?>
                                                        </h3>
                                                    <?php endif; ?>
                                                    <?php if ($author->get_locations_links()) { ?>
                                                        <div class="company-locations"><i class="ion-android-pin"></i><?php echo $author->get_locations_links(); ?></div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="job-info">
                                        <h3 class="job-title"><a href="<?php echo $job->get_indeed_url() ? esc_url( $job->get_indeed_url() ) :esc_url($permalink); ?>"><?php echo $job->get_title(); ?></a></h3>
                                        <?php if ( $categories = $job->get_categories() && $show_categories_job == '1' ) {
                                            $categories_links = $job->get_categories_links();
                                            ?>
                                            <div class="categories iwj-job-page">
                                                <i class="fa fa-suitcase theme-color"></i>
                                                <span><?php echo sprintf( __( '%s', '%s', count( $categories ), 'iwjob' ), $categories_links ); ?></span>
                                            </div>
                                        <?php } ?>
                                        <?php if (($locations = $job->get_locations_links()) && ($show_location == '1')) : ?>
                                            <div class="address"><i class="ion-android-pin"></i><?php echo $locations; ?></div>
                                        <?php endif; ?>
                                        <?php if ($show_posted_date_job == '1') { ?>
                                            <div class="time-ago"><i class="fa fa-calendar theme-color"></i><?php printf( _x( '%s ago', '%s = human-readable time difference', 'iwjob' ), human_time_diff( strtotime( $job->get_created() ), current_time( 'timestamp' ) ) ); ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $i ++;
                        endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
}

?>


