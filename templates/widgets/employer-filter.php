<?php
echo $args['before_widget'];

if (isset($instance['title'])) {
    $title = (!empty($instance['title'])) ? $instance['title'] : '';
    $title = apply_filters('widget_title', $title, $instance, $widget_id);

    if ($title) {
        echo $args['before_title'] . $title . $args['after_title'];
    }
}
?>

<?php do_action('iwj_before_widget_employer_filter', $args, $instance, $parent); ?>

<?php if (isset($list_taxonomies) && is_array($list_taxonomies) && $list_taxonomies) : ?>

    <div id="iwj-filter-selected" class="iwj-filter-selected" style="display: none;">
        <h3 class="widget-title"><span><?php echo __('Your selected', 'iwjob'); ?></span></h3>
        <?php if (!empty($filters_data)) : ?>
            <ul>
                <?php if(isset($filters_data['keyword']) && $filters_data['keyword']){ ?>
                    <li data-search_type="keyword" data-type="job" class="iwj-filter-selected-item"><label><i class="fa fa-search"></i> <?php echo $filters_data['keyword'] ?></label>
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
        <button class="btn btn-primary iwj-clear-filter" id="clear-filter-employer">
            <?php echo __('Clear Filter', 'iwjob'); ?></button>
    </div>

    <?php foreach ($list_taxonomies as $item_taxonomy) : ?>

        <?php if (!empty($list_taxonomies_data[$item_taxonomy])) :
            $iwj_limit = isset($instance[$item_taxonomy.'_limit']) ? (int) $instance[$item_taxonomy.'_limit'] : 5;
            $iwj_limit_show_more = isset($instance[$item_taxonomy.'_limit_show_more']) ? (int) $instance[$item_taxonomy.'_limit_show_more'] : 20;
            $taxonomy_label = iwj_get_taxonomy_title($item_taxonomy);
            $is_tree = in_array($item_taxonomy, iwj_taxonomy_widget_tree_show('employer', true));
            ?>
            <aside class="widget sidebar-jobs-item">
                <h3 class="widget-title"><span><?php echo $taxonomy_label; ?></span></h3>
                <div class="sidebar-job-1">
                    <form name="<?php echo $item_taxonomy; ?>" class="employer-form-filter form-<?php echo $item_taxonomy; ?>">
                        <input type="hidden" name="limit" value="<?php echo $iwj_limit; ?>" />
                        <input type="hidden" name="limit_show_more" value="<?php echo $iwj_limit_show_more; ?>" />
                        <ul class="iwjob-list-<?php echo $item_taxonomy.($is_tree ? ' iwj-tax-tree' : ''); ?>">
                            <?php
                            if($is_tree){
                                iwj_walk_tax_tree( $list_taxonomies_data[$item_taxonomy], 0, $filters_data['taxonomy_ids'], $iwj_limit, 'employers', $item_taxonomy );
                            }else{
                                $i = 1;
                                foreach ($list_taxonomies_data[$item_taxonomy] as $term) :
                                    $style = ($i > $iwj_limit) ? 'style="display:none"' : '';
                                    ?>
                                    <li <?php echo $style; ?> class="theme-color-hover iwj-input-checkbox <?php echo $item_taxonomy; ?>" data-order="<?php echo $term->total_post; ?>">
                                        <div class="filter-name-item">
                                            <input type="checkbox" <?php echo iwj_filter_tax_checked($term->term_id, $filters_data['taxonomy_ids']); ?> name="<?php echo $item_taxonomy; ?>[]"
                                                   id="iwjob-filter-employers-cbx-<?php echo $term->term_id; ?>" class="iwjob-filter-employers-cbx"
                                                   value="<?php echo $term->term_id; ?>" data-title="<?php echo $term->name; ?>">
                                            <label for="iwjob-filter-employers-cbx-<?php echo $term->term_id; ?>"><?php echo $term->name; ?></label>
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

            <?php do_action('iwj_widget_employer_filter_after_'.$item_taxonomy, $args, $instance, $parent); ?>

        <?php endif; ?>

    <?php endforeach; ?>

    <form name="iwjob-other-filters" class="employer-form-filter form-other-filters">
        <?php
        $keyword = isset($filters_data['keyword']) ? $filters_data['keyword'] : '';
        echo '<input type="hidden" name="keyword" value="'.$keyword.'">';
        ?>
    </form>

<?php endif; ?>

<?php do_action('iwj_after_widget_employer_filter', $args, $instance, $parent); ?>

<input type="hidden" value="employer" name="wgit_job_filter" id="wgit-job-filter" />
<?php echo $args['after_widget']; ?>
