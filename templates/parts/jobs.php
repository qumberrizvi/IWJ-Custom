<?php
if (isset($_COOKIE['job-archive-view']) && $_COOKIE['job-archive-view'] == 'grid') {
    $mode_view_class = 'iwj-grid';
    $list_control_class = '';
    $grid_control_class = 'active';
} else {
    $mode_view_class = 'iwj-listing';
    $list_control_class = 'active';
    $grid_control_class = '';
}

?>
<div class="iwj-content-inner">
    <div class="iwj-filter-form">
        <div class="jobs-layout-form">
            <form>
                <div class="show-filter-mobile"><?php echo __('Show Filter', 'iwjob'); ?></div>
                <div class="layout-switcher">
                    <ul>
                        <li class="<?php echo $list_control_class; ?>">
                            <a href="#" class="iwj-layout layout-list"><i class="ion-navicon"></i></a>
                        </li>
                        <li class="<?php echo $grid_control_class; ?>">
                            <a href="#" class="iwj-layout layout-grid"><i class="ion-grid"></i></a>
                        </li>
                    </ul>
                </div>

                <select class="default-sorting sorting-job iwj-select-2" name="orderby">
                    <?php echo iwj_order_list_jobs(); ?>
                </select>
            </form>
            <div class="iwj-alert-feed job">
                <a href="#" class="job-alert-btn" data-toggle="modal" data-target="#iwj-job-alert-popup"><i class="fa fa-envelope-o"></i><?php echo __('Class Alert', 'iwjob'); ?></a>
                <?php if(iwj_option('show_rss_feed_job')){ ?>
                    <a href="<?php echo IWJ_Job_Listing::get_feed_url(); ?>" class="iwj-feed iwj-job-feed"><i class="fa fa-rss"></i></a>
                <?php } ?>
            </div>
        </div>
    </div>

    <div id="iwajax-load" class="iwj-jobs-style <?php echo $atts['style_jobs_page']; echo ' column-'.$atts['number_column_grid'] ?>">
        <div class="iwj-jobs <?php echo $mode_view_class; ?>">
            <div class="iwj-job-items iwj-job-items-margin">
                <?php
                if ($query->have_posts()) :
                    while ($query->have_posts()) :
                        $query->the_post();
                        iwj_get_template_part('parts/jobs/job', array('number_column_grid' => $atts['number_column_grid'], 'style_jobs_page' => $atts['style_jobs_page']));
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<div class="iwj-alert-box">'.__('No class found', 'iwjob').'</div>';
                endif;
                ?>
                <div class="clearfix"></div>
            </div>

            <?php if( $query->max_num_pages > 1 ): ?>
                <?php $paged = (get_query_var('page')) ? get_query_var('page') : 1; ?>

                <div class="w-pagination">
                    <a href="#" class="job-alert-btn" data-toggle="modal" data-target="#iwj-job-alert-popup"><i class="fa fa-envelope-o"></i><?php echo __('Class Alert', 'iwjob'); ?></a>
                    <?php iwj_ajax_pagination(  $query->max_num_pages, $paged ); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
    global $wp;
    $current_url = home_url(add_query_arg(array(),$wp->request));
    ?>
    <input type="hidden" name="url" id="url" value="<?php echo $current_url; ?>">
</div>





