
<?php
if (isset($_COOKIE['job-archive-view']) && $_COOKIE['job-archive-view'] == 'grid') {
    $mode_view_class = 'iwj-grid';
} else {
    $mode_view_class = 'iwj-listing';
}
if(isset($type)){
    $type = $type;
} else {
    $type = 1;
}
?>

<div class="iwj-jobs <?php echo $mode_view_class; ?> ">
    <div class="iwj-job-items-margin">
        <?php
        if ($query->have_posts()) :
            while ($query->have_posts()) :
                $query->the_post();
                iwj_get_template_part('parts/jobs/job');
            endwhile;
            wp_reset_postdata();
        else :
            echo '<div class="iwj-alert-box">'.__('No class found', 'iwjob').'</div>';
        endif;
        ?>
    </div>
</div>
<?php if( $query->max_num_pages > 1 ): ?>
    <?php
    if (!isset($paged)) :
        $paged = (get_query_var('page')) ? get_query_var('page') : 1;
    endif;
    ?>
    <div class="clearfix"></div>
    <div class="w-pagination">
        <a href="#" class="job-alert-btn" data-toggle="modal" data-target="#iwj-job-alert-popup"><i class="fa fa-envelope-o"></i><?php echo __('Class Alert', 'iwjob'); ?></a>
        <?php iwj_ajax_pagination(  $query->max_num_pages, $paged, 2, $type ); ?>
    </div>
<?php endif; ?>
