<?php
$keyword = isset($_GET['keyword']) ? sanitize_text_field($_GET['keyword']) : '';
$show_advanced_search = false;

$advanced_search_jobs_style = iwj_option('advanced_search_jobs_style');
$style = ((is_tax(iwj_get_job_taxonomies()) || is_page(iwj_get_page_id('jobs'))) ? $advanced_search_jobs_style : $style);

$structure = get_option('permalink_structure');
$jobs_page_id = iwj_option('jobs_page_id');

switch ($style) {
    case 'style1':
        ?>
        <form action="<?php echo iwj_get_page_permalink('jobs'); ?>" class="iw-job-advanced_search">
            <div class="content-search in-page-heading">
                <div class="section-filter filter-advance">
                        <?php do_action('iwj_before_advanced_search_jobs'); ?>
                    <div class="default-fields">
                        <?php if ($structure == '') { ?>
                            <input type="hidden" name="page_id" id="page_id" value="<?php echo esc_attr($jobs_page_id); ?>">
                            <?php } ?>
                        <div class="row">
        <?php do_action('iwj_before_advanced_search_jobs_default_fields'); ?>
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
                                        $terms_categories = ( isset($terms_categories) && $terms_categories ) ? $terms_categories : iwj_get_term_hierarchy('iwj_cat');
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
        <?php do_action('iwj_after_advanced_search_jobs_default_fields'); ?>
                        </div>
                    </div>
                    <div class="advanced-fields <?php echo!$show_advanced_search ? 'hide' : ''; ?>">
                        <div class="row">
        <?php do_action('iwj_before_advanced_search_jobs_adv_fields'); ?>
        <?php if (!iwj_option('disable_type')) { ?>
                                <div class="col-md-6 col-sm-6 col-xs-12">
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
                                </div>
        <?php } ?>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="filter-item">
                                    <select class="default-sorting iwj-select-2 form-control"  name="iwj_location">
                                        <option value="" selected="selected"><?php echo __("Choose Location", 'iwjob') ?></option>
                                        <?php
                                        $terms_locations = ( isset($terms_locations) && $terms_locations ) ? $terms_locations : iwj_get_term_hierarchy('iwj_location');
                                        if ($terms_locations) {
                                            $location_request = IWJ_Job_Listing::get_request_taxonomies('iwj_location');
                                            $location_value = $location_request ? $location_request[0] : '';
                                            /* $terms_location = get_terms( array(
                                              'taxonomy' => 'iwj_location',
                                              'hide_empty' => false,
                                              ) ); */

                                            foreach ($terms_locations as $location) {
                                                echo '<option value="' . esc_attr($location->slug) . '" ' . selected($location_value, $location->term_id, false) . '>' . str_repeat('- ', ($location->level - 1)) . $location->name . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                                        <?php if (!iwj_option('disable_level')) { ?>
                                <div class="col-md-6 col-sm-6 col-xs-12">
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
                                </div>
                                        <?php } ?>

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
        <?php do_action('iwj_after_advanced_search_jobs_adv_fields'); ?>
                        </div>
                    </div>
        <?php do_action('iwj_before_advanced_search_jobs_submit_btn'); ?>
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
                    <?php
                    break;
                case 'style2':
                    ?>
        <div class="iwj-find-jobs style2-3 style3">
            <form action="<?php echo esc_url(iwj_get_page_permalink('jobs')) ?>" method="get" class="iw-job-advanced_search">
        <?php do_action('iwj_before_find_jobs'); ?>
                <div class="fields">
        <?php if ($structure == '') { ?>
                        <input type="hidden" name="page_id" id="page_id" value="<?php echo esc_attr($jobs_page_id); ?>">
                    <?php } ?>
        <?php do_action('iwj_before_find_jobs_keyword'); ?>
                    <div class="field-item key-word">
                        <div class="input-select">
                            <input placeholder="<?php echo __("Enter keywords", 'iwjob') ?>" name="keyword" value="<?php echo (isset($_GET['keyword']) ? esc_attr($_GET['keyword']) : ''); ?>" type="text">
                        </div>
                    </div>
                                <?php do_action('iwj_before_find_jobs_location'); ?>
                    <div class="field-item location">
                        <div class="input-select">
                            <select class="iwj-find-jobs-select style3" name="iwj_location">
                                <option value="" selected="selected"><?php echo __("Location", 'iwjob') ?></option>
                                <?php
                                $terms_locations = ( isset($terms_locations) && $terms_locations ) ? $terms_locations : iwj_get_term_hierarchy('iwj_location');
                                if ($terms_locations) {
                                    $location_request = IWJ_Job_Listing::get_request_taxonomies('iwj_location');
                                    $location_value = $location_request ? $location_request[0] : '';
                                    /* $terms_locations = get_terms( array(
                                      'taxonomy' => 'iwj_location',
                                      'hide_empty' => false,
                                      ) ); */

                                    foreach ($terms_locations as $location) {
                                        $option_name = ($location->level - 1) > 0 ? str_repeat('- ', ($location->level - 1)) . $location->name : $location->name;
                                        echo '<option value="' . esc_attr($location->slug) . '"  ' . selected($location_value, $location->term_id) . '>' . $option_name . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                                <?php do_action('iwj_before_find_jobs_category'); ?>
                    <div class="field-item categories">
                        <div class="input-select">
                            <select class="iwj-find-jobs-select style3" name="iwj_cat">
                                <option value="" selected="selected"><?php echo __("Subjects", 'iwjob') ?></option>
                                <?php
                                $terms_categories = ( isset($terms_categories) && $terms_categories ) ? $terms_categories : iwj_get_term_hierarchy('iwj_cat');
                                if ($terms_categories) {
                                    $category_request = IWJ_Job_Listing::get_request_taxonomies('iwj_cat');
                                    $category_value = $category_request ? $category_request[0] : '';
                                    /* $terms_cat = get_terms( array(
                                      'taxonomy' => 'iwj_cat',
                                      'hide_empty' => false,
                                      ) ); */
                                    foreach ($terms_categories as $category) {
                                        echo '<option value="' . esc_attr($category->slug) . '" ' . selected($category_value, $category->term_id) . '>' . str_repeat('- ', ($category->level - 1)) . $category->name . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
        <?php do_action('iwj_before_find_jobs_submit_btn'); ?>
                    <div class="field-item submit">
                        <button><i class="ion-search"></i><h6><?php echo __("Search", 'iwjob') ?></h6></button>
                        <span class="iw-search-add-advanced"><i class="ion-android-settings"></i></span>
                    </div>

                </div>
                                    <?php do_action('iwj_after_find_jobs'); ?>
                <div class="trending-advance">
                    <div class="section-filter filter-advance <?php echo!$show_advanced_search ? 'hide' : ''; ?>">
                        <form class="find-jobs-advance">
                            <div class="title-form"><?php echo __("Filter by", 'iwjob') ?></div>
                                    <?php if (!iwj_option('disable_type')) {
                                        ?>
                                <div class="filter-item">
                                    <select class="default-sorting iwj-select-2-advance form-control"  name="iwj_type" >
                                        <option value="" selected="selected"><?php echo __("Choose job type", 'iwjob') ?></option>
                                <?php
                                $terms_type = get_terms(array(
                                    'taxonomy' => 'iwj_type',
                                    'hide_empty' => false,
                                        ));
                                foreach ($terms_type as $type) {
                                    echo '<option value="' . esc_attr($type->slug) . '">' . $type->name . '</option>';
                                }
                                ?>
                                    </select>
                                </div>
                                    <?php } ?>

                                    <?php if (!iwj_option('disable_skill')) {
                                        ?>
                                <div class="filter-item">
                                    <select class="default-sorting iwj-select-2-advance form-control"  name="iwj_skill">
                                        <option value="" selected="selected"><?php echo __("Choose Skill", 'iwjob') ?></option>
                                <?php
                                $terms_skill = get_terms(array(
                                    'taxonomy' => 'iwj_skill',
                                    'hide_empty' => false,
                                        ));
                                foreach ($terms_skill as $skill) {
                                    echo '<option value="' . esc_attr($skill->slug) . '">' . $skill->name . '</option>';
                                }
                                ?>
                                    </select>
                                </div>
                                    <?php } ?>

                                    <?php if (!iwj_option('disable_level')) {
                                        ?>
                                <div class="filter-item">
                                    <select class="default-sorting iwj-select-2-advance form-control"  name="iwj_level">
                                        <option value="" selected="selected"><?php echo __("Choose Standard", 'iwjob') ?></option>
            <?php
            $terms_level = get_terms(array(
                'taxonomy' => 'iwj_level',
                'hide_empty' => false,
                    ));
            foreach ($terms_level as $level) {
                echo '<option value="' . esc_attr($level->slug) . '">' . $level->name . '</option>';
            }
            ?>
                                    </select>
                                </div>
                                    <?php } ?>

                            <div class="filter-item">
                                <select class="default-sorting iwj-select-2-advance form-control"  name="iwj_salary">
                                    <option value="" selected="selected"><?php echo __("Choose Fees", 'iwjob') ?></option>
        <?php
        $terms_salary = get_terms(array(
            'taxonomy' => 'iwj_salary',
            'hide_empty' => false,
                ));
        foreach ($terms_salary as $salary) {
            echo '<option value="' . esc_attr($salary->slug) . '">' . $salary->name . '</option>';
        }
        ?>
                                </select>
                            </div>
                        </form>
                    <?php do_action('iwj_after_find_jobs_advance'); ?>
                    </div>
                </div>
            </form>
        </div>
        <?php
        break;
    case 'style3':
        ?>
        <div class="iwj-find-jobs style3 style3-3">
            <form action="<?php echo esc_url(iwj_get_page_permalink('jobs')) ?>" method="get" class="iw-job-advanced_search style3" style="background: rgba(0, 0, 0, <?php echo esc_attr($bg_opacity); ?>);">
        <?php do_action('iwj_before_find_jobs'); ?>
                <div class="fields">
                                <?php if ($structure == '') { ?>
                        <input type="hidden" name="page_id" id="page_id" value="<?php echo esc_attr($jobs_page_id); ?>">
                                <?php } ?>
                                <?php do_action('iwj_before_find_jobs_keyword'); ?>
                    <div class="field-item key-word">
                        <div class="input-select">
                            <input placeholder="<?php echo __("Enter keywords", 'iwjob') ?>" name="keyword" value="<?php echo (isset($_GET['keyword']) ? esc_attr($_GET['keyword']) : ''); ?>" type="text">
                        </div>
                    </div>
                                <?php do_action('iwj_before_find_jobs_location'); ?>
                    <div class="field-item location">
                        <div class="input-select">
                            <select class="iwj-find-jobs-select <?php echo $style; ?>" name="iwj_location">
                                <option value="" selected="selected"><?php echo __("Location", 'iwjob') ?></option>
        <?php
        $terms_locations = ( isset($terms_locations) && $terms_locations ) ? $terms_locations : iwj_get_term_hierarchy('iwj_location');
        if ($terms_locations) {
            $location_request = IWJ_Job_Listing::get_request_taxonomies('iwj_location');
            $location_value = $location_request ? $location_request[0] : '';
            /* $terms_locations  = get_terms( array(
              'taxonomy'   => 'iwj_location',
              'hide_empty' => false,
              ) ); */
            foreach ($terms_locations as $location) {
                $option_name = ( $location->level - 1 ) > 0 ? str_repeat('- ', ( $location->level - 1)) . $location->name : $location->name;
                echo '<option value="' . esc_attr($location->slug) . '"  ' . selected($location_value, $location->term_id) . '>' . $option_name . '</option>';
            }
        }
        ?>
                            </select>
                        </div>
                    </div>
                                <?php do_action('iwj_before_find_jobs_category'); ?>
                    <div class="field-item categories">
                        <div class="input-select">
                            <select class="iwj-find-jobs-select <?php echo $style; ?>" name="iwj_cat">
                                <option value="" selected="selected"><?php echo __("Subjects", 'iwjob') ?></option>
                    <?php
                    $terms_categories = ( isset($terms_categories) && $terms_categories ) ? $terms_categories : iwj_get_term_hierarchy('iwj_cat');
                    if ($terms_categories) {
                        $category_request = IWJ_Job_Listing::get_request_taxonomies('iwj_cat');
                        $category_value = $category_request ? $category_request[0] : '';
                        /* $terms_cat = get_terms( array(
                          'taxonomy' => 'iwj_cat',
                          'hide_empty' => false,
                          ) ); */
                        foreach ($terms_categories as $category) {
                            echo '<option value="' . esc_attr($category->slug) . '" ' . selected($category_value, $category->term_id) . '>' . str_repeat('- ', ($category->level - 1)) . $category->name . '</option>';
                        }
                    }
                    ?>
                            </select>
                        </div>
                    </div>
                                    <?php do_action('iwj_before_find_jobs_submit_btn'); ?>
                    <div class="field-item submit">
                        <button><i class="ion-search"></i><h6><?php echo __("Start Learning", 'iwjob') ?></h6></button>
                    </div>
                </div>
                                    <?php do_action('iwj_after_find_jobs'); ?>
                <div class="trending-advance">
                    <div class="section-filter filter-advance <?php echo!$show_advanced_search ? 'hide' : ''; ?>">
                        <form class="find-jobs-advance">
                            <div class="title-form"><?php echo __("Filter by", 'iwjob') ?></div>
                            <?php if (!iwj_option('disable_type')) {
                                ?>
                                <div class="filter-item">
                                    <select class="default-sorting iwj-select-2-advance form-control"  name="iwj_type" >
                                        <option value="" selected="selected"><?php echo __("Choose job type", 'iwjob') ?></option>
                                        <?php
                                        $terms_type = get_terms(array(
                                            'taxonomy' => 'iwj_type',
                                            'hide_empty' => false,
                                                ));
                                        foreach ($terms_type as $type) {
                                            echo '<option value="' . esc_attr($type->slug) . '">' . $type->name . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            <?php } ?>

                            <?php if (!iwj_option('disable_skill')) {
                                ?>
                                <div class="filter-item">
                                    <select class="default-sorting iwj-select-2-advance form-control"  name="iwj_skill">
                                        <option value="" selected="selected"><?php echo __("Choose Skill", 'iwjob') ?></option>
                                        <?php
                                        $terms_skill = get_terms(array(
                                            'taxonomy' => 'iwj_skill',
                                            'hide_empty' => false,
                                                ));
                                        foreach ($terms_skill as $skill) {
                                            echo '<option value="' . esc_attr($skill->slug) . '">' . $skill->name . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
        <?php } ?>

        <?php if (!iwj_option('disable_level')) {
            ?>
                                <div class="filter-item">
                                    <select class="default-sorting iwj-select-2-advance form-control"  name="iwj_level">
                                        <option value="" selected="selected"><?php echo __("Choose Standard", 'iwjob') ?></option>
                                        <?php
                                        $terms_level = get_terms(array(
                                            'taxonomy' => 'iwj_level',
                                            'hide_empty' => false,
                                                ));
                                        foreach ($terms_level as $level) {
                                            echo '<option value="' . esc_attr($level->slug) . '">' . $level->name . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
        <?php } ?>

                            <div class="filter-item">
                                <select class="default-sorting iwj-select-2-advance form-control"  name="iwj_salary">
                                    <option value="" selected="selected"><?php echo __("Choose Fees", 'iwjob') ?></option>
                            <?php
                            $terms_salary = get_terms(array(
                                'taxonomy' => 'iwj_salary',
                                'hide_empty' => false,
                                    ));
                            foreach ($terms_salary as $salary) {
                                echo '<option value="' . esc_attr($salary->slug) . '">' . $salary->name . '</option>';
                            }
                            ?>
                                </select>
                            </div>
                        </form>
        <?php do_action('iwj_after_find_jobs_advance'); ?>
                    </div>
                    <div class="trending-keywords">
                        <div class="keywords">
                            <span class="title"><?php echo __("Trending", 'iwjob') ?>:</span>
        <?php
        foreach ($key_words as $key_word) {
            $dashboard = iwj_get_page_permalink('jobs');
            $class = '';
            if (isset($_GET['keyword']) && $_GET['keyword'] == $key_word->name) {
                $class = 'active';
            }
            $url = add_query_arg(array('keyword' => urlencode($key_word->name)), $dashboard);
            echo '<a class="' . $class . '" href="' . esc_url($url) . '">' . $key_word->name . '</a>';
        }
        ?>
                        </div>
                        <div class="iw-search-add-advanced"><i class="ion-android-settings"></i> <?php echo __("Advance search", 'iwjob') ?></div>
                    </div>
        <?php do_action('iwj_after_find_jobs_trending_keywords'); ?>
                </div>
            </form>
        </div>
        <?php
        break;
}
?>