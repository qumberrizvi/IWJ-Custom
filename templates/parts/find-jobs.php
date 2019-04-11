<?php
$find_jobs_style = iwj_option('find_jobs_style');
$style = ((is_tax(iwj_get_job_taxonomies()) || is_page(iwj_get_page_id('jobs'))) ? $find_jobs_style : $style);

$structure = get_option( 'permalink_structure' );
$jobs_page_id = iwj_option('jobs_page_id');

switch ($style) {
case 'style1':
case 'style2':
?>
    <div class="iwj-find-jobs <?php echo $style ?>">
        <form action="<?php echo esc_url(iwj_get_page_permalink('jobs')) ?>" method="get">
            <?php do_action('iwj_before_find_jobs'); ?>
            <div class="fields">
                <?php if ($structure == '') { ?>
                    <input type="hidden" name="page_id" id="page_id" value="<?php echo esc_attr($jobs_page_id); ?>">
                <?php } ?>
                <?php do_action('iwj_before_find_jobs_keyword'); ?>
                <div class="field-item key-word">
                    <div class="field-content">
                        <label><?php echo __("Keyword", 'iwjob') ?>?</label>
                        <div class="input-select">
                            <i class="fa fa-edit" aria-hidden="true"></i>
                            <input placeholder="<?php echo __("Enter job title, position...", 'iwjob') ?>" name="keyword" value="<?php echo (isset($_GET['keyword']) ? esc_attr($_GET['keyword']) : ''); ?>" type="text">
                        </div>
                    </div>
                </div>
                <?php do_action('iwj_before_find_jobs_location'); ?>
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
                                        echo '<option value="'.esc_attr($location->slug).'"  '.selected($location_value, $location->term_id).'>'.str_repeat('- ', ($location->level - 1)).$location->name.'</option>';
                                    }
                                }?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php do_action('iwj_before_find_jobs_category'); ?>
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
                                        echo '<option value="'.esc_attr($category->slug).'" '.selected($category_value, $category->term_id).'>'.str_repeat('- ', ($category->level - 1)).$category->name.'</option>';
                                    }
                                }?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php do_action('iwj_before_find_jobs_submit_btn'); ?>
                <div class="field-item-submit">
                    <button class="theme-bg"><i class="ion-arrow-right-c"></i></button>
                </div>
                <div class="clear"></div>
            </div>
            <?php do_action('iwj_after_find_jobs'); ?>
            <div class="clear"></div>
            <div class="trending-keywords">
                <span class="title"><?php echo __("Trending", 'iwjob') ?>:</span>
                <?php foreach ($key_words as $key_word) {
                    $dashboard = iwj_get_page_permalink('jobs');
                    $class = '';
                    if (isset($_GET['keyword']) && $_GET['keyword'] == $key_word->name) {
                        $class = 'active';
                    }
                    $url = add_query_arg(array('keyword' => urlencode($key_word->name)), $dashboard);
                    echo '<a class="'.$class.'" href="'.esc_url($url).'">'.$key_word->name.'</a>';
                } ?>
            </div>
            <?php do_action('iwj_after_find_jobs_trending_keywords'); ?>
        </form>
    </div>
<?php
break;
} ?>