<?php
$show_advanced_search = false;
wp_enqueue_script('iwj-search-advanced',IWJ_PLUGIN_URL.'/assets/js/search-advanced.js',array('jquery'),false,true);
wp_enqueue_style( 'search-map-css', IWJ_PLUGIN_URL.'/assets/css/search-map.css', array('iwjmb-select2' ), false);
$structure = get_option( 'permalink_structure' );
$candidates_page_id = iwj_option('candidates_page_id');
?>
<form action="<?php echo iwj_get_page_permalink('candidates'); ?>" class="iw-job-advanced_search iw-candidate-advanced_search">
    <div class="content-search in-page-heading">
        <div class="section-filter filter-advance">
            <?php do_action('iwj_before_advanced_search_candidates'); ?>
            <div class="default-fields">
                <?php if ($structure == '') { ?>
                    <input type="hidden" name="page_id" id="page_id" value="<?php echo esc_attr($candidates_page_id); ?>">
                <?php } ?>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?php do_action('iwj_before_advanced_search_candidates_default_fields'); ?>
                        <div class="filter-item">
                            <select class="default-sorting iwj-select-2 form-control"  name="iwj_cat">
                                <option value="" selected="selected"><?php echo __("Choose category", 'iwjob') ?></option>
                                <?php
                                $category_request = IWJ_Job_Listing::get_request_taxonomies('iwj_cat');
                                $category_value = $category_request ? $category_request[0] : '';
                                $terms_cat = get_terms( array(
                                    'taxonomy' => 'iwj_cat',
                                    'hide_empty' => false,
                                ) );
                                foreach($terms_cat as $cat) {
                                    echo '<option value="'.esc_attr( $cat->slug ).'" '.selected($category_value, $cat->term_id, false).'>'.$cat->name.'</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="filter-item">
                            <select class="default-sorting iwj-select-2 form-control"  name="iwj_location">
                                <option value="" selected="selected"><?php echo __("Choose Location", 'iwjob') ?></option>
                                <?php
                                $location_request = IWJ_Job_Listing::get_request_taxonomies('iwj_location');
                                $location_value = $location_request ? $location_request[0] : '';
                                $terms_location = get_terms( array(
                                    'taxonomy' => 'iwj_location',
                                    'hide_empty' => false,
                                ) );
                                foreach( $terms_location as $location ) {
                                    echo '<option value="'.esc_attr( $location->slug ).'" '.selected($location_value, $location->term_id, false).'>'.$location->name.'</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <?php do_action('iwj_after_advanced_search_candidates_default_fields'); ?>
                </div>
            </div>
            <div class="advanced-fields <?php echo !$show_advanced_search ? 'hide' : ''; ?>">
                <div class="row">
                    <?php do_action('iwj_before_advanced_search_candidates_adv_fields'); ?>
                    <?php if(!iwj_option( 'disable_type' )){ ?>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="filter-item">
                                <select class="default-sorting iwj-select-2 form-control"  name="iwj_type" >
                                    <option value="" selected="selected"><?php echo __("Choose job type", 'iwjob') ?></option>
                                    <?php
                                    $type_request = IWJ_Job_Listing::get_request_taxonomies('iwj_type');
                                    $type_value = $type_request ? $type_request[0] : '';
                                    $terms_type = get_terms( array(
                                        'taxonomy' => 'iwj_type',
                                        'hide_empty' => false,
                                    ) );
                                    foreach( $terms_type as $type ) {
                                        echo '<option value="'.esc_attr( $type->slug ).'" '.selected($type_value, $type->term_id, false).'>'.$type->name.'</option>';
                                    } ?>
                                </select>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if(!iwj_option( 'disable_skill' )){ ?>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="filter-item">
                                <select class="default-sorting iwj-select-2 form-control"  name="iwj_skill" >
                                    <option value="" selected="selected"><?php echo __("Choose skill", 'iwjob') ?></option>
                                    <?php
                                    $skill_request = IWJ_Job_Listing::get_request_taxonomies('iwj_skill');
                                    $skill_value = $skill_request ? $skill_request[0] : '';
                                    $terms_skill = get_terms( array(
                                        'taxonomy' => 'iwj_skill',
                                        'hide_empty' => false,
                                    ) );
                                    foreach( $terms_skill as $skill ) {
                                        echo '<option value="'.esc_attr( $skill->slug ).'" '.selected($type_value, $skill->term_id, false).'>'.$skill->name.'</option>';
                                    } ?>
                                </select>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if(!iwj_option( 'disable_level' )){?>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="filter-item">
                                <select class="default-sorting iwj-select-2 form-control"  name="iwj_level">
                                    <option value="" selected="selected"><?php echo __("Choose Standard", 'iwjob') ?></option>
                                    <?php
                                    $level_request = IWJ_Job_Listing::get_request_taxonomies('iwj_level');
                                    $level_value = $level_request ? $level_request[0] : '';
                                    $terms_level = get_terms( array(
                                        'taxonomy' => 'iwj_level',
                                        'hide_empty' => false,
                                    ) );
                                    foreach( $terms_level as $level ) {
                                        echo '<option value="'.esc_attr( $level->slug ).'" '.selected($level_value, $level->term_id, false).'>'.$level->name.'</option>';
                                    } ?>
                                </select>
                            </div>
                        </div>
                    <?php } ?>
                    <?php do_action('iwj_after_advanced_search_candidates_adv_fields'); ?>
                </div>
            </div>
            <?php do_action('iwj_before_advanced_search_candidates_submit_btn'); ?>
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
