<?php

echo $args['before_widget'];

if (isset($instance['title'])) {
    $title = (!empty($instance['title'])) ? $instance['title'] : '';
    $title = apply_filters('widget_title', $title, $instance, $widget_id);

    if ($title) {
        echo $args['before_title'] . $title . $args['after_title'];
    }
    //print_r($instance['heri'])
}
?>

<?php do_action('iwj_before_widget_job_filter', $args, $instance, $parent); ?>

<?php if (isset($list_taxonomies) && is_array($list_taxonomies) && $list_taxonomies) : ?>

    <div id="iwj-filter-selected" class="iwj-filter-selected" style="display: none;">
        <h3 class="widget-title"><span><?php echo __('Your selected', 'iwjob'); ?></span></h3>
        <?php if (!empty($filters_data)) : ?>
            <ul class="clearfix">
                <?php if(isset($filters_data['keyword']) && $filters_data['keyword']){ ?>
                <li data-search_type="keyword" data-type="job" class="iwj-filter-selected-item"><label><i class="fa fa-search"></i> <?php echo $filters_data['keyword'] ?></label>
                    <a href="#" class="remove"><i class="ion-android-close"></i></a>
                </li>
                <?php } ?>
                <?php if((isset($filters_data['current_lat']) && $filters_data['current_lat'] || isset($filters_data['current_lng']) && $filters_data['current_lng'])
                        && isset($filters_data['radius']) && $filters_data['radius']){
                ?>
                <li data-search_type="radius" data-type="job" class="iwj-filter-selected-item">
                    <label><i class="ion-pinpoint"></i> <?php echo ($filters_data['address'] ? wp_trim_words($filters_data['address'], 5) : '') ?></label>
                    <a href="#" class="remove"><i class="ion-android-close"></i></a>
                </li>
                <?php } ?>
                <?php foreach ($filters_data['taxonomies'] as $filters_data_item) :
                    ?>
                    <li id="iwj-filter-selected-item-<?php echo $filters_data_item->term_id; ?>" data-termid="<?php echo $filters_data_item->term_id; ?>" data-type="job" class="iwj-filter-selected-item">
                        <label><?php echo $filters_data_item->name ?></label>
                        <a href="#" class="remove"><i class="ion-android-close"></i></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div id="iwj-clear-filter-btn" class="facet open-filters" style="display: none;">
        <button class="btn btn-primary" id="clear-filter-job">
            <?php echo __('Clear Filter', 'iwjob'); ?></button>
    </div>

    <?php foreach($list_taxonomies as $item_taxonomy) : ?>

        <?php if (!empty($list_taxonomies_data[$item_taxonomy])) :
            $iwj_limit = isset($instance[$item_taxonomy.'_limit']) ? (int) $instance[$item_taxonomy.'_limit'] : 5;
            $iwj_limit_show_more = isset($instance[$item_taxonomy.'_limit_show_more']) ? (int) $instance[$item_taxonomy.'_limit_show_more'] : 20;
            $iwj_hide_empty_item = isset($instance[$item_taxonomy.'_hide_empty_item']) ? (int) $instance[$item_taxonomy.'_hide_empty_item'] : 0;
            $iwj_hierarchy_cat = isset($instance[$item_taxonomy.'_hierarchy_cat']) ? (int) $instance[$item_taxonomy.'_hierarchy_cat'] : 0;
            $taxonomy_label = iwj_get_taxonomy_title($item_taxonomy);
			$is_real_tree = $iwj_hierarchy_cat ? true : false;
			$is_tree = in_array( $item_taxonomy, iwj_taxonomy_widget_tree_show( 'job', $is_real_tree ) );
            ?>
            <aside class="widget sidebar-jobs-item sidebar-<?php echo $item_taxonomy; ?>">
                <h3 class="widget-title"><span><?php echo $taxonomy_label; ?></span></h3>
                <div class="sidebar-job-1">
                    <form name="<?php echo $item_taxonomy; ?>" class="job-form-filter form-<?php echo $item_taxonomy; ?>">
                        <input type="hidden" name="limit" value="<?php echo $iwj_limit; ?>" />
                        <input type="hidden" name="limit_show_more" value="<?php echo $iwj_limit_show_more; ?>" />
                        <ul class="iwjob-list-<?php echo $item_taxonomy.($is_tree ? ' iwj-tax-tree' : ''); ?>">
                            <?php
                            if($is_tree){
                                iwj_walk_tax_tree( $list_taxonomies_data[$item_taxonomy], 0, $filters_data['taxonomy_ids'], $iwj_limit, 'jobs', $item_taxonomy );
                            }else{
                                $i = 1;
                                foreach ($list_taxonomies_data[$item_taxonomy] as $term) :
									if($term->total_post == 0 && $iwj_hide_empty_item) continue;
                                    $style = ($i > $iwj_limit) ? 'style="display:none"' : '';
                                    ?>
                                    <li <?php echo $style; ?> class="theme-color-hover iwj-input-checkbox <?php echo $item_taxonomy; ?>" data-order="<?php echo $term->total_post; ?>">
                                        <div class="filter-name-item">
                                            <input type="checkbox" <?php echo iwj_filter_tax_checked($term->term_id, $filters_data['taxonomy_ids']); ?> name="<?php echo $item_taxonomy; ?>[]"
                                                   id="iwjob-filter-jobs-cbx-<?php echo $term->term_id; ?>" class="iwjob-filter-jobs-cbx"
                                                   value="<?php echo $term->term_id; ?>" data-title="<?php echo $term->name; ?>">
                                            <label for="iwjob-filter-jobs-cbx-<?php echo $term->term_id; ?>"><?php echo $term->name; ?></label>
                                        </div>
                                        <span id="iwj-count-<?php echo $term->term_id; ?>" class="iwj-count"><?php echo $term->total_post; ?></span>
                                    </li>
                                    <?php $i++; ?>
                                <?php endforeach; ?>

                                <?php if (count($list_taxonomies_data[$item_taxonomy]) > $iwj_limit) : ?>
                                    <li class="show-more"><a class="theme-color" href="#"><?php echo __('Show more', 'iwjob'); ?></a></li>
                                    <li class="show-less" style="display: none"><a class="theme-color" href="#"><?php echo __('Show less', 'iwjob'); ?></a></li>
                                <?php endif; ?>

                            <?php } ?>

                        </ul>
                    </form>
                </div>
            </aside>

            <?php do_action('iwj_widget_job_filter_after_'.$item_taxonomy, $args, $instance, $parent); ?>

        <?php endif; ?>

    <?php endforeach; ?>

        <form name="iwjob-other-filters" class="job-form-filter form-other-filters">
            <?php
            $keyword = isset($filters_data['keyword']) ? $filters_data['keyword'] : '';
            $address = isset($filters_data['address']) ? $filters_data['address'] : '';
            $current_lat = isset($filters_data['current_lat']) ? $filters_data['current_lat'] : '';
            $current_lng = isset($filters_data['current_lng']) ? $filters_data['current_lng'] : '';
            $search_unit = isset($filters_data['search_unit']) ? $filters_data['search_unit'] : '';
            echo '<input type="hidden" name="keyword" value="'.$keyword.'">';
            echo '<input type="hidden" name="address" value="'.$address.'">';
            echo '<input type="hidden" name="current_lat" value="'.$current_lat.'">';
            echo '<input type="hidden" name="current_lng" value="'.$current_lng.'">';
            echo '<input type="hidden" name="search_unit" value="'.$search_unit.'">';
            ?>
        </form>
<?php endif; ?>

<?php do_action('iwj_after_widget_job_filter', $args, $instance, $parent); ?>

<input type="hidden" value="job" name="wgit_job_filter" id="wgit-job-filter" />
<?php echo $args['after_widget']; ?>



