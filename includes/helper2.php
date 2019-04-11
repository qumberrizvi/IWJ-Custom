<?php

if ( !function_exists('iwj_ajax_pagination')) {

    function iwj_ajax_pagination($pages = '', $paged = 1,  $range = 2, $type = 1 ) {

        $prev = $paged - 1;
        $next = $paged + 1;
        $showitems = ( $range * 2 )+1;
        $range = 2; // change it to show more links

        if( $pages == '' ){
            global $wp_query;

            $pages = $wp_query->max_num_pages;
            if( !$pages ){
                $pages = 1;
            }
        }

        if( 1 != $pages ){
            if($type == 1) { 
                echo '<div class="iwjob-ajax-pagination pagination-main">';
            } else { 
                echo '<div class="iwjob-ajax-map-pagination pagination-main">';
            }
            
            echo '<ul class="pagination pagination-job page-nav">';
            echo ( $paged > 2 && $paged > $range+1 && $showitems < $pages ) ? '<li class="page-numbers"><a aria-label="First" href=""><span aria-hidden="true"><i class="fa fa-angle-double-left"></i></span></a></li>' : '';
            echo ( $paged > 1 ) ? '<li class="page-numbers" data-paged="'.$prev.'"><a aria-label="'.__('Previous','iwjob').'" href="'.get_pagenum_link($prev).'"><span aria-hidden="true"><i class="fa fa-angle-left"></i></span></a></li>' : '<li class="disabled page-numbers"><span aria-label="Previous"><span aria-hidden="true"><i class="fa fa-angle-left"></i></span></span></li>';
            for ( $i = 1; $i <= $pages; $i++ ) {
                if ( 1 != $pages &&( !( $i >= $paged+$range+1 || $i <= $paged-$range-1 ) || $pages <= $showitems ) )
                {
                    if ( $paged == $i ){
                        echo '<li class="active page-numbers" data-paged="'.$i.'"><a href="'.get_pagenum_link($i).'">'.$i.' <span class="sr-only"></span></a></li>';
                    } else {
                        echo '<li class="page-numbers" data-paged="'.$i.'"><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
                    }
                }
            }
            echo ( $paged < $pages ) ? '<li class="page-numbers" data-paged="'.$next.'"><a aria-label="'.__('Next','iwjob').'" href="'.get_pagenum_link($next).'"><span aria-hidden="true"><i class="fa fa-angle-right"></i></span></a></li>' : '';
            echo ( $paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages ) ? '<li class="page-numbers" data-paged="'.$pages.'"><a aria-label="Last" href="'.get_pagenum_link( $pages ).'"><span aria-hidden="true"><i class="fa fa-angle-double-right"></i></span></a></li>' : '';
            echo '</ul>';
            echo '<input type="hidden" name="page_number" value="'.$paged.'" />';
            echo '</div>';
        }
    }

}

function iwj_widget_get_data_taxonomy_location($taxonomies_data, $locations_request) {

    $first_request = !empty($locations_request) ? $locations_request[0] : '';
    $first_request = $first_request ? get_term_by('id', $first_request, 'iwj_location') : '';
    $parent = $first_request ? $first_request->parent : '0';

    $locations = array();
    if ($taxonomies_data) {
        foreach ($taxonomies_data as $term) {
            // if ($term->taxonomy == 'iwj_location' && $term->parent == $parent) {
            if ($term->taxonomy == 'iwj_location' ) {
                $locations[$term->term_id] = $term;
            }
        }
    }

    if(!empty($locations_request)){
        foreach ($locations_request as $location_term_id){
            if(!isset($locations[$location_term_id])){
                $location = get_term_by('id', $location_term_id, 'iwj_location');
                $location->total_post = 0;
                $location->post_parent = $parent;
                $locations[$location_term_id] = $location;
            }
        }
    }

    return $locations;
}

function iwj_get_request_url_location($current_term = false) {

    if ( isset($_GET['iwj_locations']) && $_GET['iwj_locations']) {
        $locations_request = explode(',', $_GET['iwj_locations']);
    }else {
        $locations_request = array();
    }

    if ($current_term) {
        if($current_term->taxonomy == 'iwj_location'){
            $locations_request[] = $current_term->term_id;
        }
    }

    return $locations_request;
}



function iwj_widget_get_data_taxonomy_level($taxonomies_data) {

    $levels = array();
    if ($taxonomies_data) {
        foreach ($taxonomies_data as $term) {
            if ($term->taxonomy == 'iwj_level') {
                $levels[$term->term_id] = $term;
            }
        }
    }

    return $levels;
}


function iwj_get_request_url_level($current_term = false) {

    if ( isset($_GET['iwj_levels']) && $_GET['iwj_levels']) {
        $level_request = explode(',', $_GET['iwj_levels']);
    }else {
        $level_request = array();
    }

    if ($current_term) {
        if($current_term && $current_term->taxonomy == 'iwj_level'){
            $level_request[] = $current_term->term_id;
        }
    }

    return $level_request;
}



function iwj_widget_get_data_taxonomy_salary($taxonomies_data) {

    $iwj_salaries = array();
    if ($taxonomies_data) {
        foreach ($taxonomies_data as $term) {
            if ($term->taxonomy == 'iwj_salary') {
                $iwj_salaries[$term->term_id] = $term;
            }
        }
    }

    return $iwj_salaries;
}


function iwj_get_request_url_salary($current_term = false) {

    if (isset($_GET['iwj_salaries']) && $_GET['iwj_salaries']) {
        $salary_request = explode(',', $_GET['iwj_salaries']);
    } else {
        $salary_request = array();
    }

    if ($current_term) {
        if($current_term->taxonomy == 'iwj_salary'){
            $salary_request[] = $current_term->term_id;
        }
    }

    return $salary_request;
}

function iwj_widget_get_data_taxonomy_skill($taxonomies_data) {

    $skills = array();
    if ($taxonomies_data) {
        foreach ($taxonomies_data as $term) {
            if ($term->taxonomy == 'iwj_skill') {
                $skills[$term->term_id] = $term;
            }
        }
    }

    return $skills;
}


function iwj_get_request_url_skill($current_term = false) {

    if (isset($_GET['iwj_skills']) && $_GET['iwj_skills']) {
        $skills_request = explode(',', $_GET['iwj_skills']);
    } else {
        $skills_request = array();
    }

    if ($current_term) {
        if($current_term->taxonomy == 'iwj_skill'){
            $skills_request[] = $current_term->term_id;
        }
    }

    return $skills_request;
}

function iwj_widget_get_data_taxonomy_type($taxonomies_data) {

    $types = array();
    if ($taxonomies_data) {
        foreach ($taxonomies_data as $term) {
            if ($term->taxonomy == 'iwj_type') {
                $types[$term->term_id] = $term;
            }
        }
    }

    return $types;
}

function iwj_get_request_url_type($current_term = false) {

    if (isset($_GET['iwj_types']) && $_GET['iwj_types']) {
        $types_request = explode(',', $_GET['iwj_types']);
    } else {
        $types_request = array();
    }

    if ($current_term) {
        if($current_term->taxonomy == 'iwj_type'){
            $types_request[] = $current_term->term_id;
        }
    }

    return $types_request;
}

function iwj_widget_get_data_taxonomy_category($taxonomies_data) {

    $categories = array();
    if ($taxonomies_data) {
        foreach ($taxonomies_data as $term) {
            if ($term->taxonomy == 'iwj_cat') {
                $categories[$term->term_id] = $term;
            }
        }
    }

    return $categories;

}

function iwj_get_request_url_category($current_term = false) {

    if (isset($_GET['iwj_cats']) && $_GET['iwj_cats']) {
        $cats_request = explode(',', $_GET['iwj_cats']);
    } else {
        $cats_request = array();
    }

    if ($current_term) {
        if($current_term->taxonomy == 'iwj_cat'){
            $cats_request[] = $current_term->term_id;
        }
    }

    return $cats_request;
}

function iwj_filter_tax_checked($term_id, $request_params){
    return (in_array($term_id, $request_params)) ? 'checked' : '';
}

/**
 * Function render HTML taxonomy location on filter job
 *
 * @param array $locations data
 * @param int   $parent
 * @param array $terms_request
 * @param int   $limit
 *
 * @return void
 */
function iwj_walk_tax_tree($terms, $parent = 0, $terms_request, $limit, $filter_name, $taxonomy) {
    $i = 0;
    foreach ( $terms as $term ) {
        if( isset($term->parent) && $term->parent == $parent ) {
            $style = '';
            if($parent == 0){
                $i++;
                $style = ( $i > $limit ) ? 'style="display:none"' : '';
            }
            $checked = ( iwj_filter_tax_checked( $term->term_id, $terms_request ) != '' ) ? 'open' : '';
            $child_terms = get_term_children( $term->term_id, $taxonomy );
        ?>

        <li <?php echo ( $parent == 0 )? $style : '' ?>  class="iwj-input-checkbox <?php echo $taxonomy;?> tax-tree" data-order="<?php echo $term->total_post; ?>">
            <a href="javascript:void(0)" class="item-tax">
                <div class="filter-name-item">
                    <input type="checkbox" <?php echo iwj_filter_tax_checked( $term->term_id, $terms_request ); ?> name="<?php echo $taxonomy;?>[]"
                           id="iwjob-filter-<?php echo $filter_name ?>-cbx-<?php echo $term->term_id; ?>" class="iwjob-filter-<?php echo $filter_name ?>-cbx"
                           value="<?php echo $term->term_id; ?>" data-title="<?php echo $term->name; ?>">
                    <label for="iwjob-filter-<?php echo $filter_name ?>-cbx-<?php echo $term->term_id; ?>">
                        <?php echo $term->name; ?></label>
                        <?php if( count($child_terms) > 0 ) {
                            echo '<span class="iwj-show-sub-cat '. $checked .'"> <i class="fa fa-angle-down"></i></span>';
                        } ?>
                </div>
                <span id="iwj-count-<?php echo $term->term_id; ?>" class="iwj-count">
                    <?php echo $term->total_post; ?>
                </span>
            </a>
            <?php

            if ( count( $child_terms ) > 0 ) :
                $checked = false;
                foreach ($terms_request as $term_request){
                    foreach ($child_terms as $term_id){
                        if($term_request == $term_id){
                            $checked = true;
                            break;
                        }
                    }

                    if($checked == true){
                        break;
                    }
                }
                ?>
                <ul class="sub-cat <?php echo $checked ? 'open' : ''; ?>">
                <?php iwj_walk_tax_tree( $terms, $term->term_id, $terms_request, $limit, $filter_name , $taxonomy); ?>
                </ul>
            <?php endif ?>
        </li>
        <?php
       }
    }
    if ( $i > $limit) : ?>
        <li class="show-more">
            <a class="theme-color" href="#">
                <?php echo __('Show more', 'iwjob'); ?>
            </a>
        </li>
        <li class="show-less" style="display: none">
            <a class="theme-color" href="#">
                <?php echo __('Show less', 'iwjob'); ?>
            </a>
        </li>
    <?php endif;
}

function iwj_get_job_taxonomies(){
    $job_taxonomies = array('iwj_cat', 'iwj_type', 'iwj_level', 'iwj_skill', 'iwj_salary', 'iwj_location', );

    if(iwj_option('disable_skill')){
        unset($job_taxonomies[3]);
    }
    if(iwj_option('disable_level')){
        unset($job_taxonomies[2]);
    }
    if(iwj_option('disable_type')){
        unset($job_taxonomies[1]);
    }

    return apply_filters('iwj_get_job_taxonomies', $job_taxonomies);
}

function iwj_get_candidate_taxonomies(){
    $job_taxonomies = array('iwj_cat', 'iwj_type', 'iwj_level', 'iwj_skill', 'iwj_location', );

    if(iwj_option('disable_skill')){
        unset($job_taxonomies[3]);
    }
    if(iwj_option('disable_level')){
        unset($job_taxonomies[2]);
    }
    if(iwj_option('disable_type')){
        unset($job_taxonomies[1]);
    }

    return apply_filters('iwj_get_candidate_taxonomies', $job_taxonomies);
}

function iwj_get_employer_taxonomies(){
    $job_taxonomies = array('iwj_cat', 'iwj_size', 'iwj_location', );

    return apply_filters('iwj_get_employer_taxonomies', $job_taxonomies);
}

function iwj_get_taxonomy_title($tax){

    $job_taxs = array(
        'iwj_cat' => __('Subjects', 'iwjob'),
        'iwj_skill' => __('Skills', 'iwjob'),
        'iwj_level' => __('Levels', 'iwjob'),
        'iwj_salary' => __('Salaries', 'iwjob'),
        'iwj_location' => __('Locations', 'iwjob'),
        'iwj_type' => __('Types', 'iwjob'),
        'iwj_size' => __('Sizes', 'iwjob')
    );

    if(isset($job_taxs[$tax])){
        $name =  $job_taxs[$tax];
    }else{
        $taxonomy = get_taxonomy($tax);
        $name = $taxonomy ? __(ucfirst($taxonomy->label), 'iwjob') : $tax;
    }

    return apply_filters('iwj_get_taxonomy_title', $name, $tax);
}

function iwj_taxonomy_widget_tree_show($type = 'job', $is_real_tree = false){

	$taxonomies = array( 'iwj_location' );
	if ( ($type == 'job' || $type == 'candidate' || $type == 'employer') && $is_real_tree ) {
		$taxonomies[] = 'iwj_cat';
	}

    return (array)apply_filters('iwj_taxonomy_widget_tree_show', $taxonomies, $type);
}


function iwj_get_terms_value($terms, $field = 'name'){
    $values = array();
    if($terms){
        foreach ($terms as $term){
            $values[] = $term->$field;
        }
    }

    return $values;
}

function iwj_convert_tax_id_to_slug($tax, $ids){
    $ids = (array)$ids;
    $slugs = array();
    foreach($ids as $id){
        if(is_numeric ($id)){
            $taxonomy = get_term($id, $tax);
            if($taxonomy && !is_wp_error($taxonomy)){
                $slugs[] = $taxonomy->slug;
            }
        }else{
            $slugs[] = $id;
        }
    }

    return $slugs;
}

function iwj_convert_tax_slug_to_id($tax, $slugs){
    $slugs = (array)$slugs;
    $ids = array();

    foreach($slugs as $slug){
        $taxonomy = get_term_by('slug',$slug, $tax);
        if($taxonomy && !is_wp_error($taxonomy)){
            $ids[] = $taxonomy->term_id;
        }elseif(is_numeric ($slug)){
            $ids[] = $slug;
        }
    }

    return $ids;
}