<?php
/**
 * The Template for displaying all posts
 * @package injob
 */

get_header();

$jobs_sidebar = iwj_option('jobs_sidebar');
if (isset($_COOKIE['job-archive-view']) && $_COOKIE['job-archive-view'] == 'grid') {
    $mode_view_class = 'iwj-jobs-grid';
    $list_control_class = '';
    $grid_control_class = 'active';
} else {
    $mode_view_class = 'iwj-jobs-listing';
    $list_control_class = 'active';
    $grid_control_class = '';
}
$jobs_taxonomy_version = iwj_option('jobs_taxonomy_version');
$number_column_grid_taxonomy = iwj_option('number_column_grid_taxonomy');
?>
<div class="contents-main" id="contents-main">
<div class="iwj-jobs page">
    <div class="container">
        <div class="row">
            <?php if ($jobs_sidebar && ($jobs_sidebar == 'both' || $jobs_sidebar == 'left')) { ?>
                <div class="col-md-3 iwj-sidebar-1">
                    <div class="widget-area sidebar-jobs-1">
                        <?php dynamic_sidebar('sidebar-jobs-1'); ?>
                    </div>
                </div>
            <?php } ?>

            <?php
            $content_class = ($jobs_sidebar && $jobs_sidebar == 'both') ? 'iwj-content col-md-6 col-xs-12' : ($jobs_sidebar ? 'col-md-8 col-xs-12' : 'col-md-12 col-xs-12');
            ?>

            <div class="jobs-content-wrap <?php echo $content_class; ?>">
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

                    <div id="iwajax-load" class="iwj-jobs-style <?php echo esc_attr($jobs_taxonomy_version); echo ' column-'.esc_attr($number_column_grid_taxonomy); ?>">
                        <?php
                        iwj_get_template_part('parts/jobs/jobs', array('query' => $wp_query));
                        ?>
                    </div>

                    <?php
                    global $wp;
                    $current_url = home_url(add_query_arg(array(),$wp->request));
                    ?>

                    <input type="hidden" name="url" id="url" value="<?php echo $current_url; ?>">

                    <?php
                    // check is taxonomy page
                    global $wp_query;
                    $is_tax_page =  is_tax(iwj_get_job_taxonomies()); ?>
                    <?php if ($is_tax_page) : ?>
                        <?php if (isset($wp_query->query) && is_array($wp_query->query) && count($wp_query->query)) : ?>
                            <?php foreach ($wp_query->query as $k => $v) : ?>
                                <?php if ( in_array($k, array('iwj_cat', 'iwj_skill', 'iwj_level', 'iwj_salary', 'iwj_type', 'iwj_location')) ) : ?>
                                    <form name="is_tax_page_job">
                                        <input type="hidden" id="is-tax-page-job" name="<?php echo $k; ?>" value="<?php echo $v; ?>">
                                    </form>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <form name="iwjob-filter-url">
                        <?php
                        $accept_params = array(
                            'keyword' => 'keyword',
                            'iwj_cat' => 'iwj_cats',
                            'iwj_skill' => 'iwj_skills',
                            'iwj_level' => 'iwj_levels',
                            'iwj_salary' => 'iwj_salaries',
                            'iwj_type' => 'iwj_types',
                            'iwj_location' => 'iwj_locations' );
                        foreach ($accept_params as $tax_key => $accept_param_item) : ?>
                            <?php if ( isset($_GET[$accept_param_item]) && $_GET[$accept_param_item] ) :
                                $values = explode(',',$_GET[$accept_param_item]);
                                foreach ($values as $item) :
                                    ?>
                                    <input type="hidden" name="<?php echo $tax_key; ?>_url[]" id="iwjob-tax-url-<?php echo $item; ?>" value="<?php echo $item; ?>" />
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </form>
                </div>
            </div>

            <?php if($jobs_sidebar && ($jobs_sidebar == 'both' || $jobs_sidebar == 'right')){ ?>
                <div class="col-md-3 iwj-sidebar-2">
                    <div class="widget-area sidebar-jobs-2">
                    <?php
                    dynamic_sidebar('sidebar-jobs-2');
                    ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
</div>

<?php get_footer(); ?>


