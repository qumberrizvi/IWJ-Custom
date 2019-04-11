<?php
extract($atts);
$filters = IWJ_Job_Listing::get_data_filters();
$query = IWJ_Job_Listing::get_query_jobs($filters);
if ( isset( $_COOKIE['job-archive-view'] ) && $_COOKIE['job-archive-view'] == 'grid' ) {
    $mode_view_class = 'iwj-jobs-grid';
    $list_control_class = '';
    $grid_control_class = 'active';
} else {
    $mode_view_class = 'iwj-jobs-listing';
    $list_control_class = 'active';
    $grid_control_class = '';
}
?>
<div class="container-fluid page-search-map">
    <div class="row">
        <div class="col-md-4 iwj-search-left-side">

            <div class="iw-job-filter container-fluid">

                <div class="content-search">
                    <div class="section-filter" id="section-filter">
                        <input class="form-control" type="text" name="location" id="location" placeholder="<?php echo __('Enter Location', 'iwjob'); ?>">
                        <button class="btn-pinpoint"><i class="ion-pinpoint"></i></button>
                        <input type="hidden" name="current_lat" id="iwj_curent_lat">
                        <input type="hidden" name="current_lng" id="iwj_curent_lng">
                    </div>
                    <div class="slide-range">
                        <div class="row label-distant">
                            <div class="label-distant-left"><?php echo $min_radius.' '.$unit ?></div>
                            <div class="label-distant-right"><?php echo $max_radius . ' ' . $unit ?></div>
                        </div>
                        <div class="range-radius"></div>
                    </div>
                    <input type="hidden" name="search_unit" value="<?php echo $unit; ?>">
                    <div class="section-filter filter-advance">
                        <div class="row">
                            <form class="search-map">
                                <div class="col-md-6 filter-item">
                                    <input class="form-control" type="text" name="keyword" id="keyword" placeholder="<?php echo __('Enter Keywords', 'iwjob'); ?>">
                                </div>
                                <div class="col-md-6 filter-item">
                                    <select class="default-sorting iwj-select-2 form-control"  name="iwj_cat">
                                        <option value="" selected="selected"><?php echo __("Choose category", 'iwjob') ?></option>
                                        <?php
                                        $terms_categories = iwj_get_term_hierarchy('iwj_cat');
                                        foreach ($terms_categories as $cat) {
                                                echo '<option value="' . esc_attr($cat->slug) . '" ' . selected($category_value, $cat->term_id, false) . '>' . str_repeat('- ', ($cat->level - 1)) . $cat->name . '</option>';
                                            } ?>
                                    </select>
                                </div>
                                <?php if(!iwj_option( 'disable_type' )){
                                    ?>
                                    <div class="col-md-6 filter-item">
                                        <select class="default-sorting iwj-select-2 form-control"  name="iwj_type" >
                                            <option value="" selected="selected"><?php echo __("Choose job type", 'iwjob') ?></option>
                                            <?php
                                            $terms_type = get_terms( array(
                                                'taxonomy' => 'iwj_type',
                                                'hide_empty' => false,
                                            ) );
                                            foreach( $terms_type as $type ) {
                                                echo '<option value="'.esc_attr( $type->slug ).'">'.$type->name.'</option>';
                                            } ?>
                                        </select>
                                    </div>
                                <?php } ?>

                                <?php if(!iwj_option( 'disable_skill' )){
                                    ?>
                                    <div class="col-md-6 filter-item">
                                        <select class="default-sorting iwj-select-2 form-control"  name="iwj_skill">
                                            <option value="" selected="selected"><?php echo __("Choose Skill", 'iwjob') ?></option>
                                            <?php
                                            $terms_skill = get_terms( array(
                                                'taxonomy' => 'iwj_skill',
                                                'hide_empty' => false,
                                            ) );
                                            foreach( $terms_skill as $skill ) {
                                                echo '<option value="'.esc_attr( $skill->slug ).'">'.$skill->name.'</option>';
                                            } ?>
                                        </select>
                                    </div>
                                <?php } ?>

                                <?php if(!iwj_option( 'disable_level' )){
                                    ?>
                                    <div class="col-md-6 filter-item">
                                        <select class="default-sorting iwj-select-2 form-control"  name="iwj_level">
                                            <option value="" selected="selected"><?php echo __("Choose Standard", 'iwjob') ?></option>
                                            <?php
                                            $terms_level = get_terms( array(
                                                'taxonomy' => 'iwj_level',
                                                'hide_empty' => false,
                                            ) );
                                            foreach( $terms_level as $level ) {
                                                echo '<option value="'.esc_attr( $level->slug ).'">'.$level->name.'</option>';
                                            } ?>
                                        </select>
                                    </div>
                                <?php } ?>

                                <div class="col-md-6 filter-item">
                                    <select class="default-sorting iwj-select-2 form-control"  name="iwj_salary">
                                        <option value="" selected="selected"><?php echo __("Choose Fees", 'iwjob') ?></option>
                                        <?php
                                        $terms_salary = get_terms( array(
                                            'taxonomy' => 'iwj_salary',
                                            'hide_empty' => false,
                                        ) );
                                        usort($terms_salary, function ( $item1, $item2 ) {
                                            $term_order_1 = get_term_meta($item1->term_id, IWJ_PREFIX . 'salary_order', true);
                                            $term_order_2 = get_term_meta($item2->term_id, IWJ_PREFIX . 'salary_order', true);
                                            if ($term_order_1 == $term_order_2) {
                                                return 0;
                                            }

                                            return $term_order_1 < $term_order_2 ? -1 : 1;
                                        });
                                        foreach( $terms_salary as $salary ) {
                                            echo '<option value="'.esc_attr( $salary->slug ).'">'.$salary->name.'</option>';
                                        } ?>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="clearfix"></div>
                        <div class="bottom-section-filter">
                            <div class="layout-switcher">
                                <ul>
                                    <li class=" <?php echo $list_control_class; ?>">
                                        <a href="#" class="btn iwj-layout layout-list"><i class="ion-navicon"></i></a>
                                    </li>
                                    <li class=" <?php echo $grid_control_class; ?>">
                                        <a href="#" class="btn iwj-layout layout-grid"><i class="ion-ios-grid-view-outline"></i></a>
                                    </li>
                                </ul>
                            </div>
                            <div class="action-search pull-right">
                                <span class="hide-advance show-hide-search"><?php echo __("Hide advanced search", 'iwjob') ?></span>
                                <div class="btn-search btn"><i class="ion-search"></i></div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <div class="section-result">
                    <div class="iwajax-load" id="iwajax-load">

                        <?php
                        $result['status'] = 1;
                        $user_login = 0;
                        $paged = 1;
                        iwj_get_template_part('parts/jobs/jobs', array('query' => $query, 'paged' => $paged, 'type' => 2 ) );
                        if ($query->have_posts()) :
                            $user = IWJ_User::get_user();
                            while ($query->have_posts()) :
                                $query->the_post();
                                $job = IWJ_Job::get_job( get_the_ID() );
                                $latlng = $job->get_map();
                                if($latlng){
                                    $latlng = array_slice( $latlng, 0, 2 );
                                }else{
                                    $latlng = array(0 => '', 1 => '');
                                }
                                $type = $job->get_type();
                                if($type){
                                    $color = get_term_meta( $type->term_id, IWJ_PREFIX.'color', true );
                                    $link_type = get_term_link( $type->term_id, 'iwj_type' );
                                }else{
                                    $color = '';
                                    $link_type = '';
                                }
                                $author = $job->get_author();
                                if ( is_user_logged_in() ){
                                    $savejobclass = $user->is_saved_job( $job->get_id() ) ? 'saved' : '';
                                    $user_login = 1;
                                }else{
                                    $savejobclass = '';

                                }

	                            $lat_rd  = (float) $latlng[0] * iwj_random_number( 0.999999, 1.000001 );
	                            $long_rd = (float) $latlng[1] * iwj_random_number( 0.999999, 1.000001 );

                                $result['data'][] = array(
                                    'location' =>  array(
                                        'lat' => $lat_rd,
                                        'lng' => $long_rd,
                                    ),
                                    'ID'        => $job->get_id(),
                                    'salary'    => $job->get_salary(),
                                    'address'   => $job->get_locations_links(),
                                    'company_name' => $author->get_display_name(),
                                    'company_link' => $author->permalink(),
                                    'color'     => $color,
                                    'link_type' => $link_type,
                                    'type_name' => $type ? $type->name : '',
                                    'title'     => $job->get_title(),
                                    'link'      => esc_url( $job->permalink () ),
                                    'savejobclass'=> $savejobclass,
                                    'user_login' => $user_login,
                                );
                            endwhile;
                            wp_reset_postdata();
                            $data_json = json_encode($result);
                        endif;
                        ?>
                        <div data-array='<?php echo htmlspecialchars($data_json); ?>' class="data-array hidden"></div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8 iwj-search-right-side">
            <div class="iw_search_map_2">
                <div id="iw_search_map" class="iw_search_map"></div>
            </div>
        </div>
    </div>
</div>
