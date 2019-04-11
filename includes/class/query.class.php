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

class IWJ_Query {

    public $query_vars = array();

    static function init(){
        if (!is_blog_admin()) {
            add_action('pre_get_posts',array( __CLASS__, 'pre_get_posts' ) );
            add_filter('posts_join', array( __CLASS__, 'posts_join' ), 10, 2);
            add_filter('posts_where', array( __CLASS__, 'posts_where' ), 10, 2);
        }
    }

    static function pre_get_posts($query){
        if(
            $query->get('post_type') == 'iwj_job' &&
            $query->is_main_query() &&
            $query->is_preview() &&
            $query->is_singular()
        ){
            add_filter( 'posts_results', array( __CLASS__, 'set_post_to_publish' ), 10, 2 );
        }
        else if(
            !is_blog_admin() &&
            ($query->get('post_type') == 'iwj_job' || is_tax(iwj_get_job_taxonomies())) &&
            $query->is_main_query()
        ){
            $query->set('post_type', 'iwj_job');
            $query->set('posts_per_page', iwj_option('jobs_per_page', get_option('posts_per_page', get_option('posts_per_page', 20))));

            $meta_query = array('relation' => 'AND');
            if(!iwj_option('show_expired_job')){
                $meta_query['_iwj_expired'] = array(
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

            if (isset($_GET['order']) && $_GET['order']) {
                if(iwj_option('prioritize_featured_jobs')){
                    if ($_GET['order'] == 'date') {
                        $query->set('orderby', array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ));
                        $query->set('meta_key', '_iwj_featured');
                    } elseif ($_GET['order'] == 'name') {
                        $query->set('orderby', array( 'meta_value_num' => 'DESC', 'title' => 'ASC' ));
                        $query->set('meta_key', '_iwj_featured');
                    } elseif ($_GET['order'] == 'salary') {
                        $meta_query['_iwj_featured_clause'] = array(
                            'key' => '_iwj_featured',
                            'compare' => 'EXISTS',
                        );
                        $meta_query['_iwj_salary_to_clause'] = array(
                            'key' => '_iwj_salary_to',
                            'compare' => 'EXISTS',
                        );

                        $query->set('orderby', array(
                            '_iwj_featured_clause' => 'DESC',
                            '_iwj_salary_to_clause' => 'DESC',
                        ));

                    }else{
                        $query->set('orderby', array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ));
                        $query->set('meta_key', '_iwj_featured');

                    }
                }else{
                    if ($_GET['order'] == 'date') {
                        $query->set('orderby', array('date' => 'DESC' ));
                    } elseif ($_GET['order'] == 'name') {
                        $query->set('orderby', array( 'title' => 'ASC' ));
                    } elseif ($_GET['order'] == 'salary') {
                        $query->set('orderby', array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ));
                        $query->set('meta_key', '_iwj_salary_to');
                    }else{
                        $query->set('orderby', array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ));
                        $query->set('meta_key', '_iwj_featured');
                    }
                }

            }else{
                $query->set('orderby', array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ));
                $query->set('meta_key', '_iwj_featured');
            }

            if(count($meta_query) > 1){
                $query->set('meta_query', $meta_query);
            }

            IWJ_Job_Listing::add_query_url_params($query);

        }
    }


    public static function set_post_to_publish( $posts ) {
        // Remove the filter again, otherwise it will be applied to other queries too.
        remove_filter( 'posts_results', array( __CLASS__, 'set_post_to_publish' ), 10 );

        if ( empty( $posts ) ) {
            return;
        }

        $post_id = $posts[0]->ID;

        // If the post has gone live, redirect to it's proper permalink.
        self::maybe_redirect_to_published_post( $post_id );

        if ( self::is_public_preview_available( $post_id ) ) {
            // Set post status to publish so that it's visible.
           // $posts[0]->post_status = 'publish';

            // Disable comments and pings for this post.
            add_filter('comments_open', '__return_false');
            add_filter('pings_open', '__return_false');
        }

        return $posts;
    }

    private static function maybe_redirect_to_published_post( $post_id ) {
        if ( ! in_array( get_post_status( $post_id ), self::get_published_statuses() ) ) {
            return false;
        }

        wp_redirect( get_permalink( $post_id ), 301 );
        exit;
    }

    private static function get_published_statuses() {
        $published_statuses = array( 'publish', 'private');

        return apply_filters( 'iwj_published_statuses', $published_statuses );
    }

    private static function is_public_preview_available( $post_id ) {
        if ( empty( $post_id ) ) {
            return false;
        }

        $post = get_post($post_id);
        if(!is_super_admin() && $post->post_author != get_current_user_id()){
            return false;
        }

        return true;
    }

    static function posts_join($join, $query){
        if($query->get('post_type') == 'iwj_job'){
            if(isset($_REQUEST['current_lat'])
                && isset($_REQUEST['current_lng'])
                && isset($_REQUEST['radius'])
                && !empty($_REQUEST['current_lat'])
                && !empty($_REQUEST['current_lng'])
                && $_REQUEST['radius'] > 0
            ){
                global $wpdb;
                $join .= " LEFT JOIN $wpdb->postmeta as latlng_meta ON $wpdb->posts.ID = latlng_meta.post_id ";
            }

        }

        return $join;
    }

    static function posts_where($where, $query){
        if($query->get('post_type') == 'iwj_job'){
            if(isset($_REQUEST['current_lat'])
                && isset($_REQUEST['current_lng'])
                && isset($_REQUEST['radius'])
                && !empty($_REQUEST['current_lat'])
                && !empty($_REQUEST['current_lat'])
                && !empty($_REQUEST['current_lng'])
                && $_REQUEST['radius'] > 0
            ){
                $current_lat = (float)$_REQUEST['current_lat'];
                $current_lng = (float)$_REQUEST['current_lng'];
                if( isset($_REQUEST['search_unit']) && $_REQUEST['search_unit'] == 'Km' ){
                   $radius = (float)$_REQUEST['radius']/1.609344;
                }else{
                    $radius = (float)$_REQUEST['radius'];
                }
                $where .= " AND latlng_meta.meta_key = '".IWJ_PREFIX."map' AND ( ( acos( sin( SUBSTRING_INDEX(latlng_meta.meta_value,',', 1) * 0.0175) * sin( $current_lat * 0.0175) + cos( SUBSTRING_INDEX(latlng_meta.meta_value,',', 1) * 0.0175) * cos( $current_lat * 0.0175 ) * cos( ( $current_lng  * 0.0175 ) - ( SUBSTRING_INDEX(SUBSTRING_INDEX(latlng_meta.meta_value, ',',2),',', -1) * 0.0175 ) ) ) * 3959 ) < $radius ) ";
            }

        }

        return $where;
    }
}

IWJ_Query::init();
