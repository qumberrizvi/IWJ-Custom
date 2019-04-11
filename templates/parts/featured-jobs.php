<?php
wp_enqueue_style('owl-carousel');
wp_enqueue_style('owl-theme');
wp_enqueue_style('owl-transitions');
wp_enqueue_script('owl-carousel');

$data_plugin_options = array(
    "navigation"=>false,
    "autoHeight"=>true,
    "pagination"=>true,
    "autoPlay"=>false,
    "paginationNumbers"=>true,
    "singleItem"=>true,
);
?>
<div class="iwj-jobs iwj-featured-jobs <?php echo $class. ' '; echo $style; ?>">
    <?php if ($style == 'style2') { ?>
        <div class="owl-carousel" data-plugin-options="<?php echo htmlspecialchars(json_encode($data_plugin_options)); ?>">
    <?php } ?>
        <div class="iwj-items">
            <div class="row">
                <?php
                $i = 0;
                foreach ($jobs as $job) :
                    $job = IWJ_Job::get_job($job);
                    $author = $job->get_author();
                    $permalink = $job->permalink();
                    $is_featured = $job->is_featured();
                    $type = $job->get_type();
                    $user = IWJ_User::get_user();
                    if($style == 'style2' && $i > 0 && count($jobs) > $i && $i % 10 == 0){
                        echo '</div>
                                            </div>
                                            <div class="iwj-items">
                                            <div class="row">';
                    }
                    ?>
                    <div class="iwj-item col-md-6 col-sm-6 col-xs-12">
                        <div class="job-item">
                            <?php if($author) : ?>
                                <div class="job-image"><?php echo get_avatar( $author->get_id()); ?></div>
                            <?php endif; ?>
                            <div class="job-info">
                                <h3 class="job-title"><a href="<?php echo esc_url($permalink); ?>"><?php echo $job->get_title(); ?></a></h3>
                                <div class="info-company">
                                    <?php if($author){ ?>
                                        <div class="company"><i class="fa fa-suitcase"></i>
                                            <?php if($author->is_active_profile()){ ?>
                                                <a href="<?php echo $author->permalink(); ?>"><?php echo $author->get_display_name(); ?></a>
                                            <?php }else{
                                                echo $author->get_display_name();
                                            } ?>
                                        </div>
                                    <?php } ?>
                                    <?php if ($location_links = $job->get_locations_links()) : ?>
                                        <div class="address"><i class="ion-android-pin"></i><?php echo $location_links; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="job-type <?php echo $type ? $type->slug : ''; ?>">
                                    <?php if($type) {
                                        $color = get_term_meta($type->term_id, IWJ_PREFIX.'color', true);
                                        ?>
                                        <a class="type-name" href="<?php echo get_term_link($type->term_id, 'iwj_type'); ?>" <?php echo $color ? 'data-color="'.$color.'" style="color: '.$color.'"' : ''; ?>><?php echo $type->name; ?></a>
                                    <?php } ?>
                                    <?php if(!is_user_logged_in()){ ?>
                                        <button class="save-job" data-toggle="modal" data-target="#iwj-login-popup"><i class="fa fa-heart"></i></button>
                                    <?php }else if(current_user_can('apply_job')) { ?>
                                        <a href="#" class="iwj-save-job <?php echo $user->is_saved_job($job->get_id()) ? 'saved' : ''; ?>" data-id="<?php echo $job->get_id(); ?>" data-in-list="true"><i class="fa fa-heart"></i></a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $i ++;
                endforeach; ?>
            </div>
        </div>
        <?php if ($style == 'style2') {
            echo '</div>';
        } ?>
    </div>