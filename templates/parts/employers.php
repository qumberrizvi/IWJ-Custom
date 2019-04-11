<?php
if (isset($_COOKIE['job-archive-view']) && $_COOKIE['job-archive-view'] == 'grid') {
    $list_control_class = '';
    $grid_control_class = 'active';
} else {
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
                <select class="default-sorting sorting-employers iwj-select-2-wsearch" name="orderby">
                    <?php echo iwj_order_list_employers(); ?>
                </select>
            </form>
            <?php if(iwj_option('show_rss_feed_employer')){ ?>
                <div class="iwj-alert-feed">
                    <a href="<?php echo IWJ_Employer_Listing::get_feed_url(); ?>" class="iwj-feed iwj-employer-feed"><i class="fa fa-rss"></i></a>
                </div>
            <?php } ?>
        </div>


        <?php if( iwj_option('show_filter_alpha_employer', '') ) : ?>
        <div class="iwj-employer-alphabet">
            <?php
            $alphabets = array('' => __('All', 'iwjob'), '#' => __('#', 'iwjob'), 'a' => 'a',
                'b' => 'b', 'c' => 'c', 'd' => 'd', 'e' => 'e',
                'f' => 'f', 'g' => 'g', 'h' => 'h', 'i' => 'i',
                'j' => 'j', 'k' => 'k', 'l' => 'l', 'm' => 'm',
                'n' => 'n', 'o' => 'o', 'p' => 'p', 'q' => 'q',
                'r' => 'r', 's' => 's', 't' => 't', 'u' => 'u',
                'v' => 'v', 'w' => 'w', 'x' => 'x', 'y' => 'y',
                'z' => 'z'
               );
            $alphabets = apply_filters('iwj_employer_alphabet', $alphabets);
            ?>

            <?php $filtered_alphabet = isset($_GET['alpha']) ? $_GET['alpha'] : ''; ?>

            <?php foreach ($alphabets as $key => $alphabet) : ?>
                <?php $ext_class = ($key == $filtered_alphabet) ? 'active' : ''; ?>
               <span class="iwj-alpha <?php echo 'iwj-alpha-'.$key; ?> <?php echo $ext_class; ?>"><a href="#" data-filter="<?php echo $key; ?>" class="iwj-alpha-filter"><?php echo $alphabet; ?></a></span>
            <?php endforeach; ?>

            <input type="hidden" name="iwj-alpha-filter" value="<?php echo $filtered_alphabet; ?>" />
        </div>

        <?php endif; ?>
    </div>

    <div id="iwajax-load-employers" class="<?php echo $fixcols;?>">
        <?php
        if (isset($_COOKIE['job-archive-view']) && $_COOKIE['job-archive-view'] == 'grid') {
            iwj_get_template_part('parts/employers/employers-grid', array('query' => $query));
        } else {
            iwj_get_template_part('parts/employers/employers-list', array('query' => $query));
        }
        ?>
    </div>

    <?php
    global $wp;
    $current_url = home_url(add_query_arg(array(),$wp->request));
    ?>

    <input type="hidden" name="url" id="url" value="<?php echo $current_url; ?>">
</div>