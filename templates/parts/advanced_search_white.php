<?php
$show_advanced_search = false;
$keyword = isset($_REQUEST['keyword']) ? sanitize_text_field($_REQUEST['keyword']) : '';
$structure = get_option('permalink_structure');
$jobs_page_id = iwj_option('jobs_page_id');
?>
<form action="<?php echo iwj_get_page_permalink('jobs'); ?>" class="iw-job-advanced_search white">
    <div class="content-search in-page-heading">
        <div class="section-filter filter-advance">
            <?php do_action('iwj_before_advanced_search_white_jobs'); ?>
            <?php if ($structure == '') { ?>
                <input type="hidden" name="page_id" id="page_id" value="<?php echo esc_attr($jobs_page_id); ?>">
            <?php } ?>
            <div class="default-fields">
                <?php do_action('iwj_before_advanced_search_white_jobs_default_fields'); ?>
                <div class="filter-item-keyword">
                    <input class="form-control keywords" type="text" name="keyword" placeholder="<?php echo __('Enter keywords ( Class title, skills, company, etc... )', 'iwjob'); ?>" value="<?php echo $keyword; ?>">
                </div>
                <div class="default-fields-2">
                    <?php
                    $terms_cat = get_terms(array(
                        'taxonomy' => 'iwj_cat',
                        'hide_empty' => false,
                            ));
                    if ($terms_cat) {
                        $category_request = IWJ_Job_Listing::get_request_taxonomies('iwj_cat');
                        ?>
                        <div class="filter-item check-box">
                            <div class="title-item category"><?php echo __("Choose categories:", 'iwjob') ?></div>
                            <div class="iwjob-list-categories">
                                <?php
                                foreach ($terms_cat as $cat) {
                                    ?>
                                    <div class="category-item iwj-input-checkbox">
                                        <div class="filter-name-item">
                                            <input type="checkbox" name="iwj_cat[]"
                                                   id="iwjob-filter-candidates-cbx-<?php echo $cat->term_id; ?>" class="iwjob-filter-cbx"
                                                   value="<?php echo $cat->slug; ?>" data-title="<?php echo $cat->name; ?>" <?php echo in_array($cat->term_id, $category_request) ? 'checked' : ''; ?>>
                                            <label for="iwjob-filter-candidates-cbx-<?php echo $cat->term_id; ?>">
                                                <?php echo $cat->name; ?>
                                            </label>
                                        </div>
                                        <span id="iwj-count-<?php echo $cat->term_id; ?>" class="iwj-count">
                                            <?php echo $cat->total; ?>
                                        </span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php
                    if (!iwj_option('disable_type')) {
                        $type_request = IWJ_Job_Listing::get_request_taxonomies('iwj_type');
                        $terms_type = get_terms(array(
                            'taxonomy' => 'iwj_type',
                            'hide_empty' => false,
                                ));
                        if ($terms_type) {
                            ?>
                            <div class="filter-item check-box iwjob-types-levels">
                                <div class="title-item type"><?php echo __("Choose job types", 'iwjob') ?></div>
                                <div class="iwjob-list-types">
                                    <?php
                                    foreach ($terms_type as $type) {
                                        $color = get_term_meta($type->term_id, IWJ_PREFIX . 'color', true);
                                        ?>
                                        <div class="type-item iwj-input-checkbox">
                                            <div class="filter-name-item">
                                                <input type="checkbox" name="iwj_type[]"
                                                       id="iwjob-filter-candidates-cbx-<?php echo $type->term_id; ?>" class="iwjob-filter-cbx"
                                                       value="<?php echo $type->slug; ?>" data-title="<?php echo $type->name; ?>"
                                                       <?php echo in_array($type->term_id, $type_request) ? 'checked' : ''; ?> >
                                                <label for="iwjob-filter-candidates-cbx-<?php echo $type->term_id; ?>" <?php echo $color ? 'data-color="' . $color . '" style="color: ' . $color . '"' : ''; ?>>
                                                    <?php echo $type->name; ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php }
                    }
                    ?>
                    <?php
                    if (!iwj_option('disable_level')) {
                        $level_request = IWJ_Job_Listing::get_request_taxonomies('iwj_level');
                        $terms_level = get_terms(array(
                            'taxonomy' => 'iwj_level',
                            'hide_empty' => false,
                                ));
                        ?>
                        <div class="filter-item check-box iwjob-types-levels">
                            <div class="title-item type"><?php echo __("Choose levels", 'iwjob') ?></div>
                            <div class="iwjob-list-levels">
                                <?php foreach ($terms_level as $level) { ?>
                                    <div class="type-item iwj-input-checkbox">
                                        <div class="filter-name-item">
                                            <input type="checkbox" name="iwj_level[]"
                                                   id="iwjob-filter-candidates-cbx-<?php echo $level->term_id; ?>" class="iwjob-filter-cbx"
                                                   value="<?php echo $level->slug; ?>" data-title="<?php echo $level->name; ?>"
                                                   <?php echo in_array($type->term_id, $type_request) ? 'checked' : ''; ?> >
                                            <label for="iwjob-filter-candidates-cbx-<?php echo $level->term_id; ?>">
                                                <?php echo $level->name; ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php do_action('iwj_after_advanced_search_white_jobs_default_fields'); ?>
            </div>
            <div class="advanced-fields <?php echo!$show_advanced_search ? 'hide' : ''; ?>">
                <div class="filter-items-select">
                    <div class="row">
                        <?php do_action('iwj_before_advanced_search_white_jobs_adv_fields'); ?>
                        <?php
                        $terms_location = get_terms(array(
                            'taxonomy' => 'iwj_location',
                            'hide_empty' => false,
                                ));
                        $location_request = IWJ_Job_Listing::get_request_taxonomies('iwj_location');
                        $data_options = array(
                            "enableFiltering" => true,
                            "enableCaseInsensitiveFiltering" => true,
                            "numberDisplayed" => 2,
                            "placeholder" => __("Choose Locations", 'iwjob'),
                            "nonSelectedText" => __("Choose Locations", 'iwjob'),
                        );
                        ?>
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="filter-item select">
                                <select data-options="<?php echo htmlspecialchars(json_encode($data_options)); ?>" multiple="multiple" id="locations" class="iwjmb-taxonomy2" name="iwj_location[]">
                                    <?php
                                    foreach ($terms_location as $location) {
                                        echo '<option value="' . esc_attr($location->slug) . '" ' . (in_array($location->term_id, $location_request) ? 'selected' : '') . '>' . $location->name . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <?php
                        if (!iwj_option('disable_skill')) {
                            $terms_skill = get_terms(array(
                                'taxonomy' => 'iwj_skill',
                                'hide_empty' => false,
                                    ));
                            $skill_request = IWJ_Job_Listing::get_request_taxonomies('iwj_skill');
                            $data_options = array(
                                "enableFiltering" => true,
                                "enableCaseInsensitiveFiltering" => true,
                                "numberDisplayed" => 2,
                                "placeholder" => __("Choose Skills", 'iwjob'),
                                "nonSelectedText" => __("Choose Skills", 'iwjob'),
                            );
                            ?>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="filter-item select">
                                    <select data-options="<?php echo htmlspecialchars(json_encode($data_options)); ?>" multiple="multiple" id="skills" class="iwjmb-taxonomy2" name="iwj_skill[]">
                                        <?php
                                        foreach ($terms_skill as $skill) {
                                            echo '<option value="' . esc_attr($skill->slug) . '" ' . (in_array($skill->term_id, $skill_request) ? 'selected' : '') . '>' . $skill->name . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        <?php
                        $terms_salary = get_terms(array(
                            'taxonomy' => 'iwj_salary',
                            'hide_empty' => false,
                                ));
                        $slary_request = IWJ_Job_Listing::get_request_taxonomies('iwj_salary');
                        $data_options = array(
                            "enableFiltering" => true,
                            "enableCaseInsensitiveFiltering" => true,
                            "numberDisplayed" => 2,
                            "placeholder" => __("Choose Salaries", 'iwjob'),
                            "nonSelectedText" => __("Choose Salaries", 'iwjob'),
                        );
                        usort($terms_salary, function ( $item1, $item2 ) {
                            $term_order_1 = get_term_meta($item1->term_id, IWJ_PREFIX . 'salary_order', true);
                            $term_order_2 = get_term_meta($item2->term_id, IWJ_PREFIX . 'salary_order', true);
                            if ($term_order_1 == $term_order_2) {
                                return 0;
                            }

                            return $term_order_1 < $term_order_2 ? -1 : 1;
                        });
                        if ($terms_salary) {
                            ?>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="filter-item select">
                                    <select data-options="<?php echo htmlspecialchars(json_encode($data_options)); ?>" multiple="multiple" id="Salaries" class="iwjmb-taxonomy2" name="iwj_salary[]">
                                        <?php
                                        foreach ($terms_salary as $salary) {
                                            echo '<option value="' . esc_attr($salary->slug) . '" ' . (in_array($salary->term_id, $slary_request) ? 'selected' : '') . '>' . $salary->name . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
<?php } ?>
            <?php do_action('iwj_after_advanced_search_white_jobs_adv_fields'); ?>
                    </div>
                </div>
            </div>
<?php do_action('iwj_after_advanced_search_white_jobs'); ?>
            <div class="clearfix"></div>
            <div class="bottom-section-filter">
                <div class="action-search pull-right">
                    <span class="hide-advance show-hide-search <?php echo $show_advanced_search ? 'active' : ''; ?>"><?php echo $show_advanced_search ? __("Hide advanced search", 'iwjob') : __("Show advanced search", 'iwjob') ?></span>
                    <button type="submit" class="btn-search btn"><?php echo __("Search now", 'iwjob') ?></button>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</form>
