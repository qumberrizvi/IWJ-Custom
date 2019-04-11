
<?php if (isset($categories) && is_array($categories) && $categories) : ?>
    <?php
    $cats_request = iwj_get_request_url_category();
    $i = 1;
    ?>
    <?php foreach($categories as $category) : ?>
        <?php
        $style = ($i > $iwj_cat_limit) ? 'style="display:none"' : '';
        ?>
        <li <?php echo $style; ?> class="theme-color-hover iwj-input-checkbox iwj_cat" data-order="<?php echo $category->total_post; ?>">
            <div class="filter-name-item">
                <input type="checkbox" <?php echo iwj_filter_tax_checked($category->term_id, $cats_request); ?> name="iwj_cat[]"
                       id="iwjob-filter-employers-cbx-<?php echo $category->term_id; ?>" class="iwjob-filter-employers-cbx"
                       value="<?php echo $category->term_id; ?>" data-title="<?php echo $category->name; ?>">
                <label for="iwjob-filter-employers-cbx-<?php echo $category->term_id; ?>">
                    <?php echo $category->name; ?></label>
            </div>
            <span id="iwj-count-<?php echo $category->term_id; ?>" class="iwj-count"> <?php echo $category->total_post; ?></span>
        </li>
        <?php $i++; ?>
    <?php endforeach; ?>

    <?php if (count($categories) > $iwj_cat_limit) : ?>
        <li class="show-more"><a class="theme-color" href="#"><?php echo __('Show more', 'iwjob'); ?></a></li>
        <li class="show-less" style="display: none"><a class="theme-color" href="#"><?php echo __('Show less', 'iwjob'); ?></a></li>
    <?php endif; ?>
<?php endif; ?>
