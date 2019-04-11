<?php
/**
 * Created by PhpStorm.
 * User: VodKa
 * Date: 10/2/2017
 * Time: 11:24 AM
 */
$find_jobs_style = iwj_option('find_jobs_style');
$style = ((is_tax(iwj_get_job_taxonomies()) || is_page(iwj_get_page_id('jobs'))) ? $find_jobs_style : $style);
global $post;
?>
<div class="iwj-find-jobs <?php echo $style ?>">
    <form action="<?php echo get_permalink($post->ID); ?>" method="post" class="iwj-login-form-maps">
        <?php do_action('iwj_before_find_jobs_map'); ?>
        <div class="fields">
            <?php do_action('iwj_before_find_jobs_map_key_word'); ?>
            <div class="field-item key-word">
                <div class="field-content">
                    <label><?php echo __("Keyword", 'iwjob'); ?>?</label>
                    <div class="input-select">
                        <i class="fa fa-edit" aria-hidden="true"></i>
                        <input placeholder="<?php echo __("Enter job title, position...", 'iwjob') ?>" name="keyword" value="<?php echo (isset($_GET['keyword']) ? esc_attr($_GET['keyword']) : ''); ?>" type="text">
                    </div>
                </div>
            </div>
            <?php do_action('iwj_before_find_jobs_map_location'); ?>
            <div class="field-item location">
                <div class="field-content select">
                    <label><?php echo __("Where", 'iwjob') ?>?</label>
                    <i class="ion-android-pin"></i>
                    <div class="input-select">
                        <select class="iwj-find-jobs-select <?php echo $style; ?>" name="iwj_location">
                            <option value="" selected="selected"><?php echo __("All location", 'iwjob') ?></option>
                            <?php if($terms_locations){
                                $location_request = IWJ_Job_Listing::get_request_taxonomies('iwj_location');
                                $location_value = $location_request ? $location_request[0] : '';
                                foreach($terms_locations as $location) {
                                    echo '<option value="'.esc_attr($location->slug).'" '.selected($location_value, $location->term_id).'>'.str_repeat('- ', ($location->level - 1)).$location->name.'</option>';
                                }
                            }?>
                        </select>
                    </div>
                </div>
            </div>
            <?php do_action('iwj_before_find_jobs_map_category'); ?>
            <div class="field-item categories">
                <div class="field-content select">
                    <label><?php echo __("Subjects", 'iwjob') ?>?</label>
                    <i class="fa fa-suitcase"></i>
                    <div class="input-select">
                        <select class="iwj-find-jobs-select <?php echo $style; ?>" name="iwj_cat">
                            <option value="" selected="selected"><?php echo __("All subjects", 'iwjob') ?></option>
                            <?php if($categories){
                                $category_request = IWJ_Job_Listing::get_request_taxonomies('iwj_cat');
                                $category_value = $category_request ? $category_request[0] : '';
                                foreach($categories as $category){
                                    echo '<option value="'.esc_attr($category->slug).'" '.selected($category_value, $category->term_id).'>'.$category->name.'</option>';
                                }
                            }?>
                        </select>
                    </div>
                </div>
            </div>
            <?php do_action('iwj_before_find_jobs_map_submit_btn'); ?>
            <div class="field-item-submit">
                <button type="submit" class="theme-bg iwj-login-btn"><i class="ion-arrow-right-c"></i></button>
            </div>
            <div class="clear"></div>
        </div>
        <?php do_action('iwj_after_find_jobs_map'); ?>
        <div class="clear"></div>
        <div class="trending-keywords">
            <span class="title"><?php echo __("Trending", 'iwjob') ?>:</span>
            <?php foreach ($key_words as $key_word) {
                if(isset($module) && $module == 'map_find_job'){
                    $dashboard = get_permalink($post->ID);
                }else{
                    $dashboard = iwj_get_page_permalink('jobs');
                }
                $class = '';
                if (isset($_GET['keyword']) && $_GET['keyword'] == $key_word->name) {
                    $class = 'active';
                }
                $url = add_query_arg(array('keyword' => urlencode($key_word->name)), $dashboard);
                echo '<a class="'.$class.' keywords-trending" data-keywords="'.$key_word->name.'" href="#">'.$key_word->name.'</a>';
            } ?>
        </div>
        <?php do_action('iwj_after_find_jobs_map_trending_keyword'); ?>
    </form>
</div>