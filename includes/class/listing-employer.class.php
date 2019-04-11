<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class IWJ_Employer_Listing {

    public static function get_data_filters($convert_slug_to_id = true){
        static $data_filters = null;
        if(is_null($data_filters)){
            $data_filters = array('post_type' => 'iwj_employer');

            if ( isset( $_REQUEST['url']) ) {
                $data_filters['url'] = $_REQUEST['url'];
            }

            if (isset($_REQUEST['keyword']) && $_REQUEST['keyword']) {
                $data_filters['keyword'] = $_REQUEST['keyword'];
            }

            if (isset($_REQUEST['alpha']) && $_REQUEST['alpha']) {
                $data_filters['alpha'] = $_REQUEST['alpha'];
            }

            $job_taxs = iwj_get_employer_taxonomies();

            foreach ($job_taxs as $job_tax_key) {
                if ( isset($_REQUEST[$job_tax_key]) && $_REQUEST[$job_tax_key] ) {
                    $term_ids = $convert_slug_to_id  ? (array)iwj_convert_tax_slug_to_id($job_tax_key, $_REQUEST[$job_tax_key]) : (array)$_REQUEST[$job_tax_key];
                    if (isset($data_filters[$job_tax_key])) {
                        $data_filters[$job_tax_key] = array_merge($data_filters[$job_tax_key], $term_ids);
                    } else {
                        $data_filters[$job_tax_key] = $term_ids;
                    }
                }

                if (isset($data_filters[$job_tax_key])) {
                    $data_filters[$job_tax_key] = array_unique($data_filters[$job_tax_key]);
                }
            }

            if (isset($_REQUEST['order']) && $_REQUEST['order']) {
                $data_filters['order'] = $_REQUEST['order'];
            }

            $data_filters['paged'] = isset($_REQUEST['paged']) ? ($_REQUEST['paged'])  : 1;

            if(get_query_var('page') > 1){
                $data_filters['paged'] = get_query_var('page');
            }

            $data_filters = apply_filters('iwj_get_data_filters_candidate', $data_filters);

        }

        return $data_filters;
    }

    public static function count_employers_in_taxonomy( $data_filters ) {

        $result = iwj_count_item_by_taxonomy($data_filters);

        $data = array();

        if ($result) {
            foreach ($result as $k => $v) {
                $data[] = array('idx' => $k, 'name' => $v->name, 'val' => $v->total_post);
            }
        }

        return $data;
    }

    public static function get_query_employers($data_filters) {
        $args = array(
            'post_type' 	 => 'iwj_employer',
            'post_status' => array('publish'),
        );

        if (isset($data_filters['keyword']) && $data_filters['keyword']) {
            $args['s'] = sanitize_text_field( $data_filters['keyword'] );
        }

        $filter_taxonomy = false;
        $args['tax_query'] = array(
            'relation' => 'AND'
        );

        $job_taxs = iwj_get_employer_taxonomies();
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

        if (isset($data_filters['order']) && $data_filters['order']) {
            $order_by = $data_filters['order'];

            if ($order_by == 'date') {
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
            } elseif ($order_by == 'name') {
                $args['orderby'] = 'title';
                $args['order'] = 'ASC';
            } else {
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
            }
        }

        $args['paged'] = isset($data_filters['paged']) ? (int) $data_filters['paged'] : 1;

        if ( !$filter_taxonomy ) {
            unset($args['tax_query']);
        }

        $args['posts_per_page'] = iwj_option('employers_per_page', get_option('posts_per_page', 20));
        if(isset($data_filters['posts_per_page']) && $data_filters['posts_per_page']){
            $args['posts_per_page'] = $data_filters['posts_per_page'];
        }

        $args = apply_filters('iwj_hook_ajax_args_query_listing_employer', $args);

        $query = new WP_Query( $args );

        $query = apply_filters('iwj_hook_ajax_query_listing_employer', $query);

        return $query;
    }

    public static function get_request_url($data_filters) {

        $url = '';
        if ( isset($data_filters['url']) ) {
            $url = $data_filters['url'];
        }

        $add_query_arr = array();

        if (isset($data_filters['feed']) && $data_filters['feed']) {
            $add_query_arr['feed'] = $data_filters['feed'];
        }

        $taxonomy_names = iwj_get_employer_taxonomies();

        foreach($taxonomy_names as $taxonomy_name){
            if (isset($data_filters[$taxonomy_name]) && $data_filters[$taxonomy_name]) {
                $terms = (array)iwj_convert_tax_id_to_slug($taxonomy_name, $data_filters[$taxonomy_name]);
                if($terms){
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
        }

        if (isset($data_filters['paged']) && ($data_filters['paged']) > 1) {
            $add_query_arr['page'] = (int) $data_filters['paged'];
        }

        if (isset($data_filters['alpha']) && $data_filters['alpha']) {
            $add_query_arr['alpha'] = $data_filters['alpha'];
        }

        if (isset($data_filters['order']) && $data_filters['order']) {
            $add_query_arr['order'] = $data_filters['order'];
        }

        if (isset($data_filters['keyword']) && $data_filters['keyword']) {
            $add_query_arr['keyword'] = $data_filters['keyword'];
        }

        $url = add_query_arg( $add_query_arr, $url );

        return $url;
    }

    public static function get_feed_url($filters = null){
        if(is_null($filters)){
            $filters = self::get_data_filters();
        }

        $filters['url'] = home_url('/');
        $filters = array('feed'=>'employer_feed') + $filters;

        return self::get_request_url($filters);
    }
}


