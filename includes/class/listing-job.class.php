<?php
/**
 * Contains the query functions for inTravel which alter the front-end post queries and loops
 *
 * @class 		indirectory_query
 * @package		inTravel/Classes
 * @category	Class
 * @author 		inwaveThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class IWJ_Job_Listing {

    public static function get_data_filters($convert_slug_to_id = true){
        static $data_filters = null;
        if(is_null($data_filters)){
            $data_filters = array();
            if ( isset( $_REQUEST['url']) ) {
                $data_filters['url'] = $_REQUEST['url'];
            }

            if (isset($_REQUEST['keyword']) && $_REQUEST['keyword']) {
                $data_filters['keyword'] = $_REQUEST['keyword'];
            }
            if (isset($_REQUEST['current_lat']) && $_REQUEST['current_lat']) {
                $data_filters['current_lat'] = $_REQUEST['current_lat'];
            }
            if (isset($_REQUEST['current_lng']) && $_REQUEST['current_lng']) {
                $data_filters['current_lng'] = $_REQUEST['current_lng'];
            }
            if (isset($_REQUEST['radius']) && $_REQUEST['radius']) {
                $data_filters['radius'] = $_REQUEST['radius'];
            }
            if (isset($_REQUEST['address']) && $_REQUEST['address']) {
                $data_filters['address'] = $_REQUEST['address'];
            }
            if (isset($_REQUEST['search_unit']) && $_REQUEST['search_unit']) {
                $data_filters['search_unit'] = $_REQUEST['search_unit'];
            }

            $job_taxs = iwj_get_job_taxonomies();
            $current_term = get_queried_object();

            foreach ($job_taxs as $job_tax_key) {
                if ( isset($_REQUEST[$job_tax_key]) && $_REQUEST[$job_tax_key] ) {
                    $term_ids = $convert_slug_to_id  ? (array)iwj_convert_tax_slug_to_id($job_tax_key, $_REQUEST[$job_tax_key]) : (array)$_REQUEST[$job_tax_key];

                    if (isset($data_filters[$job_tax_key])) {
                        $data_filters[$job_tax_key] = array_merge($data_filters[$job_tax_key], $term_ids);
                    } else {
                        $data_filters[$job_tax_key] = $term_ids;
                    }
                }

                if($current_term && isset($current_term->taxonomy) && $current_term->taxonomy== $job_tax_key){
                    if (isset($data_filters[$job_tax_key])) {
                        $data_filters[$job_tax_key][] = $current_term->term_id;
                    } else {
                        $data_filters[$job_tax_key] = (array)$current_term->term_id;
                    }
                }

                if (isset($data_filters[$job_tax_key])) {
                    $data_filters[$job_tax_key] = array_unique($data_filters[$job_tax_key]);
                }
            }

            //map old version
            /*$job_taxs = array();
            $job_taxs['iwj_location'] = 'iwj_locations';
            $job_taxs['iwj_level'] = 'iwj_levels';
            $job_taxs['iwj_salary'] = 'iwj_salaries';
            $job_taxs['iwj_type'] = 'iwj_types';
            $job_taxs['iwj_skill'] = 'iwj_skills';
            $job_taxs['iwj_cat'] = 'iwj_cats';

            foreach ($job_taxs as $job_tax_key => $job_tax_post_name) {

                if ( isset($_REQUEST[$job_tax_post_name]) && $_REQUEST[$job_tax_post_name] ) {
                    $job_tax_values = explode(',', $_REQUEST[$job_tax_post_name]);
                    $job_tax_values = iwj_convert_tax_slug_to_id($job_tax_key, $job_tax_values);
                    $data_filters[$job_tax_key] = array_unique($job_tax_values);
                }
            }*/

            if (isset($_REQUEST['order']) && $_REQUEST['order']) {
                $data_filters['order'] = $_REQUEST['order'];
            }

            $data_filters['paged'] = isset($_REQUEST['paged']) ? ($_REQUEST['paged'])  : 1;

            if(get_query_var('page') > 1){
                $data_filters['paged'] = get_query_var('page');
            }

            $data_filters = apply_filters('iwj_get_data_filters_job', $data_filters);

        }

        return $data_filters;
    }

    public static function count_jobs_in_taxonomy($data_filters) {

        $result = iwj_count_item_by_taxonomy($data_filters);

        $data = array();

        if ($result) {
            foreach ($result as $k => $v) {
                $data[] = array('idx' => $k, 'name' => $v->name, 'val' => $v->total_post);
            }
        }
        return $data;
    }

    /**
     * Apply archive page / taxonomy page
     *
     * @param $query
     */
    public static function add_query_url_params($query){

        $paged = get_query_var('page');

        $query->set('paged', $paged);

        $tax_query = array('relation' => 'AND');
        $filter_taxonomy = false;
        $job_taxs = iwj_get_job_taxonomies();
        if($job_taxs){
            foreach ($job_taxs as $job_tax_key) {
                if ( isset($_GET[$job_tax_key]) && $_GET[$job_tax_key] ) {
                    $job_tax_values = $_GET[$job_tax_key];
                    $filter_taxs = array(
                        'taxonomy' => $job_tax_key,
                        'field'    => 'slug',
                        'terms'    => $job_tax_values,
                    );

                    if ( $filter_taxs ) {
                        array_push($tax_query, $filter_taxs);
                    }
                }
            }
        }

        if ($filter_taxonomy) {
            $query->set( 'tax_query', $tax_query );
        }

        if (isset($_GET['keyword']) && $_GET['keyword']) {
            $s = sanitize_text_field( $_GET['keyword'] );
            $query->set( 's', $s );
        }
    }

    /**
     * Apply ajax
     *
     * @return WP_Query
     */
    public static function get_query_jobs($data_filters) {

        $args = array(
            'post_type' 	 => 'iwj_job',
            'post_status' => array('publish'),
        );

        if (isset($data_filters['keyword']) && $data_filters['keyword']) {
            $args['s'] = sanitize_text_field( $data_filters['keyword'] );
        }

        $filter_taxonomy = false;
        $args['tax_query'] = array(
            'relation' => 'AND'
        );

        $job_taxs = iwj_get_job_taxonomies();
        foreach ($job_taxs as $tax) {
            if ( isset($data_filters[$tax]) && is_array($data_filters[$tax]) && $data_filters[$tax] ) {
                $filter_taxonomy = true;
                $args['tax_query'][] = array(
                    'taxonomy' => $tax,
                    'field'    => 'term_id',
                    'terms'    => $data_filters[$tax]
                );
            }
        }

        $args['meta_query'] = array('relation' => 'AND');
        if(!iwj_option('show_expired_job')){
            $args['meta_query']['_iwj_expired'] = array(
                'relation' => 'OR',
                array(
                    'key'     => IWJ_PREFIX.'expiry',
                    'value' => '',
                    'compare' => '=',
                ),
                array(
                    'key'     => IWJ_PREFIX.'expiry',
                    'value' => current_time('timestamp'),
                    'compare' => '>',
                    'type' => 'NUMERIC'
                )
            );
        }

        if (isset($data_filters['order']) && $data_filters['order']) {
            if(iwj_option('prioritize_featured_jobs')){
                if ($data_filters['order'] == 'date') {
                    $args['orderby']  = array( 'meta_value_num' => 'DESC', 'date' => 'DESC' );
                    $args['meta_key'] = '_iwj_featured_date';
                } elseif ($data_filters['order'] == 'name') {
                    $args['orderby']  = array( 'meta_value_num' => 'DESC', 'title' => 'ASC' );
                    $args['meta_key'] = '_iwj_featured_date';
                } elseif ($data_filters['order'] == 'salary') {
                    $args['meta_query']['_iwj_featured_clause'] = array(
                        'key' => '_iwj_featured_date',
                        'compare' => 'EXISTS',
                    );
                    $args['meta_query']['_iwj_salary_to_clause'] = array(
                        'key' => '_iwj_salary_to',
                        'compare' => 'EXISTS',
                        'type'      => 'numeric'
                    );
                    $args['meta_query']['_iwj_salary_from_clause'] = array(
                        'key' => '_iwj_salary_from',
                        'compare' => 'EXISTS',
                        'type'      => 'numeric'
                    );

                    $args['orderby'] = array(
                        '_iwj_featured_clause' => 'DESC',
                        '_iwj_salary_to_clause' => 'DESC',
                        '_iwj_salary_from_clause' => 'DESC',
                    );

                }else{
                    $args['orderby']  = array( 'meta_value_num' => 'DESC', 'date' => 'DESC' );
                    $args['meta_key'] = '_iwj_featured_date';
                }
            }else{
                if ($data_filters['order'] == 'date') {
                    $args['orderby']  = array('date' => 'DESC' );
                } elseif ($data_filters['order'] == 'name') {
                    $args['orderby']  = array( 'title' => 'ASC' );
                } elseif ($data_filters['order'] == 'salary') {
                    $args['meta_query']['_iwj_salary_to_clause'] = array(
                        'key' => '_iwj_salary_to',
                        'compare' => 'EXISTS',
                        'type'      => 'numeric'
                    );
                    $args['meta_query']['_iwj_salary_from_clause'] = array(
                        'key' => '_iwj_salary_from',
                        'compare' => 'EXISTS',
                        'type'      => 'numeric'
                    );

                    $args['orderby'] = array(
                        '_iwj_salary_to_clause' => 'DESC',
                        '_iwj_salary_from_clause' => 'DESC',
                        'date' => 'DESC'
                    );
                }else{
                    $args['orderby']  = array( 'meta_value_num' => 'DESC', 'date' => 'DESC' );
                    $args['meta_key'] = '_iwj_featured_date';
                }
            }
        }else{
            $args['orderby']  = array( 'meta_value_num' => 'DESC', 'date' => 'DESC' );
            $args['meta_key'] = '_iwj_featured_date';
        }

        if (isset($data_filters['paged'])) {
            $args['paged'] = (int) $data_filters['paged'];
        } else {
            $args['paged'] = 1;
        }

        if ( !$filter_taxonomy ) {
            unset($args['tax_query']);
        }

        $args['posts_per_page'] = iwj_option('jobs_per_page', get_option('posts_per_page', 20));
        if(isset($data_filters['posts_per_page']) && $data_filters['posts_per_page']){
            $args['posts_per_page'] = $data_filters['posts_per_page'];
        }

        $args = apply_filters('iwj_hook_args_query_listing_job', $args);
        
        $query = new WP_Query( $args );

        $query = apply_filters('iwj_hook_query_listing_job', $query);

        if (isset($args['s']) && strlen ($args['s']) > 2 && $query->have_posts() ) {

            $term = term_exists($args['s'], 'iwj_keyword');

            if ($term) {
                $term_id = $term['term_id'];
                $searched = (int)get_term_meta($term_id, IWJ_PREFIX.'searched', true);
                $searched += 1;
                update_term_meta($term_id,IWJ_PREFIX.'searched', $searched);
            }else{
                wp_insert_term(
                    $args['s'],   // the term
                    'iwj_keyword', // the taxonomy
                    array(
                        'parent' => 0
                    )
                );
                $term = term_exists($args['s'], 'iwj_keyword');
                $term_id = $term['term_id'];
                update_term_meta($term_id,IWJ_PREFIX.'searched', 1);
            }

        }

        return $query;
    }

    public static function get_request_taxonomies($tax){
        $current_term = get_queried_object();
        static $taxonomies = array();
        if(!isset($taxonomies[$tax])){

            $taxonomies[$tax] = array();

            if ( isset($_REQUEST[$tax]) && $_REQUEST[$tax] ) {
                $taxonomies[$tax] = (array)iwj_convert_tax_slug_to_id($tax, $_REQUEST[$tax]);
            }

            if($current_term && isset($current_term->taxonomy) && $current_term->taxonomy == $tax){
                $taxonomies[$tax][] = $current_term->term_id;
            }

            if ($taxonomies[$tax]) {
                $taxonomies[$tax] = array_unique($taxonomies[$tax]);
            }
        }

        return $taxonomies[$tax];
    }

    public static function get_request_url( $data_filters ) {

        $url = '';
        if ( isset($data_filters['url']) ) {
            $url = $data_filters['url'];
        }

        $add_query_arr = array();

        if (isset($data_filters['feed']) && $data_filters['feed']) {
            $add_query_arr['feed'] = $data_filters['feed'];
        }

        if (isset($data_filters['keyword']) && $data_filters['keyword']) {
            $add_query_arr['keyword'] = $data_filters['keyword'];
        }

        if (isset($data_filters['address']) && $data_filters['address']) {
            $add_query_arr['address'] = $data_filters['address'];
        }

        if (isset($data_filters['current_lat']) && $data_filters['current_lat']) {
            $add_query_arr['current_lat'] = $data_filters['current_lat'];
        }

        if (isset($data_filters['current_lng']) && $data_filters['current_lng']) {
            $add_query_arr['current_lng'] = $data_filters['current_lng'];
        }

        if (isset($data_filters['radius']) && $data_filters['radius']) {
            $add_query_arr['radius'] = $data_filters['radius'];
        }

        if (isset($data_filters['search_unit']) && $data_filters['search_unit']) {
            $add_query_arr['search_unit'] = $data_filters['search_unit'];
        }

        $taxonomy_names = iwj_get_job_taxonomies();

        foreach($taxonomy_names as $taxonomy_name){
            if (isset($data_filters[$taxonomy_name]) && $data_filters[$taxonomy_name]) {
                $terms = (array)iwj_convert_tax_id_to_slug($taxonomy_name, $data_filters[$taxonomy_name]);
                if($terms){
                    if(count($terms) > 1){
                        foreach($terms as $term){
                            $add_query_arr[$taxonomy_name.'%5B%5D='.$term] = '';
                        }
                    }else{
                        $add_query_arr[$taxonomy_name.'='.$terms[0]] = '';
                    }
                }
            }
        }

        if (isset($data_filters['paged']) && ($data_filters['paged'] > 1)) {
            $add_query_arr['page'] = (int) $data_filters['paged'];
        }

        if (isset($data_filters['order']) && $data_filters['order']) {
            $add_query_arr['order'] = $data_filters['order'];
        }

        $add_query_arr = apply_filters('iwj_job_url_request_args', $add_query_arr, $data_filters);

        $url = add_query_arg( $add_query_arr, $url );

        return $url;
    }

    public static function get_feed_url($filters = null){
        if(is_null($filters)){
            $filters = self::get_data_filters();
        }

        $filters['url'] = home_url('/');
        $filters = array('feed'=>'job_feed') + $filters;

        return self::get_request_url($filters);
    }
}

