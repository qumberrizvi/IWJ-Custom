<?php
extract($atts);
$address = isset($_GET['address']) ? sanitize_text_field($_GET['address']) : '';
$current_lat = isset($_GET['current_lat']) ? sanitize_text_field($_GET['current_lat']) : '';
$current_lng = isset($_GET['current_lng']) ? sanitize_text_field($_GET['current_lng']) : '';
$radius = isset($_GET['radius']) ? sanitize_text_field($_GET['radius']) : $default_radius;
$keyword = isset($_GET['keyword']) ? sanitize_text_field($_GET['keyword']) : '';
$show_advanced_search = false;
$structure = get_option('permalink_structure');
$jobs_page_id = iwj_option('jobs_page_id');
?>
<form action="<?php echo iwj_get_page_permalink('jobs'); ?>" class="iw-job-advanced_search default">
    <div class="content-search in-page-heading">
        <?php do_action('iwj_before_advanced_search_radius_jobs'); ?>
        <?php if ($structure == '') { ?>
            <input type="hidden" name="page_id" id="page_id" value="<?php echo esc_attr($jobs_page_id); ?>">
        <?php } ?>
        <div class="section-filter">
            <input class="form-control" type="text" name="address" placeholder="<?php echo __('Enter Location', 'iwjob'); ?>" value="<?php echo $address; ?>">
            <button type="button" class="btn-pinpoint"><i class="ion-pinpoint"></i></button>
            <input type="hidden" name="current_lat" value="<?php echo $current_lat; ?>">
            <input type="hidden" name="current_lng" value="<?php echo $current_lng; ?>">
        </div>
        <div class="slide-range">
            <div class="row label-distant">
                <div class="label-distant-left"><?php echo $min_radius . ' ' . $unit ?></div>
                <div class="label-distant-right"><?php echo $max_radius . ' ' . $unit ?></div>
            </div>
            <div class="range-radius"></div>
            <input type="hidden" name="radius" value="<?php echo $radius; ?>">
        </div>
        <?php do_action('iwj_advanced_search_radius_jobs_after_radius'); ?>
        <input type="hidden" name="search_unit" value="<?php echo $unit; ?>">
        <div class="section-filter filter-advance">
            <div class="advanced-fields <?php echo!$show_advanced_search ? 'hide' : ''; ?>">
                <div class="row row-eq-height row-flex-wrap">
                    <?php do_action('iwj_before_advanced_search_radius_jobs_adv_fields'); ?>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="filter-item">
                            <input class="form-control keywords" type="text" name="keyword" placeholder="<?php echo __('Enter Keywords', 'iwjob'); ?>" value="<?php echo $keyword; ?>">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="filter-item">
                            <select class="default-sorting iwj-select-2 form-control"  name="iwj_cat">
                                <option value="" selected="selected"><?php echo __("Choose category", 'iwjob') ?></option>
                                <?php
                                if ($terms_categories) {
                                    $category_request = IWJ_Job_Listing::get_request_taxonomies('iwj_cat');
                                    $category_value = $category_request ? $category_request[0] : '';
                                    /* $terms_cat = get_terms( array(
                                      'taxonomy' => 'iwj_cat',
                                      'hide_empty' => false,
                                      ) ); */
                                    foreach ($terms_categories as $cat) {
                                        echo '<option value="' . esc_attr($cat->slug) . '" ' . selected($category_value, $cat->term_id, false) . '>' . str_repeat('- ', ($cat->level - 1)) . $cat->name . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?php if (!iwj_option('disable_type')) {
                            ?>
                            <div class="filter-item">
                                <select class="default-sorting iwj-select-2 form-control"  name="iwj_type" >
                                    <option value="" selected="selected"><?php echo __("Choose job type", 'iwjob') ?></option>
                                    <?php
                                    $type_request = IWJ_Job_Listing::get_request_taxonomies('iwj_type');
                                    $type_value = $type_request ? $type_request[0] : '';
                                    $terms_type = get_terms(array(
                                        'taxonomy' => 'iwj_type',
                                        'hide_empty' => false,
                                    ));
                                    foreach ($terms_type as $type) {
                                        echo '<option value="' . esc_attr($type->slug) . '" ' . selected($type_value, $type->term_id, false) . '>' . $type->name . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?php if (!iwj_option('disable_skill')) {
                            ?>
                            <div class="filter-item">
                                <select class="default-sorting iwj-select-2 form-control"  name="iwj_skill">
                                    <option value="" selected="selected"><?php echo __("Choose Skill", 'iwjob') ?></option>
                                    <?php
                                    $skill_request = IWJ_Job_Listing::get_request_taxonomies('iwj_skill');
                                    $skill_value = $skill_request ? $skill_request[0] : '';
                                    $terms_skill = get_terms(array(
                                        'taxonomy' => 'iwj_skill',
                                        'hide_empty' => false,
                                    ));
                                    foreach ($terms_skill as $skill) {
                                        echo '<option value="' . esc_attr($skill->slug) . '" ' . selected($skill_value, $skill->term_id, false) . '>' . $skill->name . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?php if (!iwj_option('disable_level')) {
                            ?>
                            <div class="filter-item">
                                <select class="default-sorting iwj-select-2 form-control"  name="iwj_level">
                                    <option value="" selected="selected"><?php echo __("Choose Standard", 'iwjob') ?></option>
                                    <?php
                                    $level_request = IWJ_Job_Listing::get_request_taxonomies('iwj_level');
                                    $level_value = $level_request ? $level_request[0] : '';
                                    $terms_level = get_terms(array(
                                        'taxonomy' => 'iwj_level',
                                        'hide_empty' => false,
                                    ));
                                    foreach ($terms_level as $level) {
                                        echo '<option value="' . esc_attr($level->slug) . '" ' . selected($level_value, $level->term_id, false) . '>' . $level->name . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="filter-item">
                            <select class="default-sorting iwj-select-2 form-control"  name="iwj_salary">
                                <option value="" selected="selected"><?php echo __("Choose Fees", 'iwjob') ?></option>
                                <?php
                                $salary_request = IWJ_Job_Listing::get_request_taxonomies('iwj_salary');
                                $salary_value = $salary_request ? $salary_request[0] : '';
                                $terms_salary = get_terms(array(
                                    'taxonomy' => 'iwj_salary',
                                    'hide_empty' => false,
                                ));
                                usort($terms_salary, function ( $item1, $item2 ) {
                                    $term_order_1 = get_term_meta($item1->term_id, IWJ_PREFIX . 'salary_order', true);
                                    $term_order_2 = get_term_meta($item2->term_id, IWJ_PREFIX . 'salary_order', true);
                                    if ($term_order_1 == $term_order_2) {
                                        return 0;
                                    }

                                    return $term_order_1 < $term_order_2 ? -1 : 1;
                                });
                                foreach ($terms_salary as $salary) {
                                    echo '<option value="' . esc_attr($salary->slug) . '" ' . selected($salary_value, $salary->term_id, false) . '>' . $salary->name . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <?php do_action('iwj_after_advanced_search_radius_jobs_adv_fields'); ?>
                </div>
            </div>
            <?php do_action('iwj_after_advanced_search_radius_jobs'); ?>
            <div class="clearfix"></div>
            <div class="bottom-section-filter">
                <div class="action-search pull-right">
                    <span class="hide-advance show-hide-search <?php echo $show_advanced_search ? 'active' : ''; ?>"><?php echo $show_advanced_search ? __("Hide advanced search", 'iwjob') : __("Show advanced search", 'iwjob') ?></span>
                    <button type="submit" class="btn-search btn"><i class="ion-search"></i></button>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</form>
