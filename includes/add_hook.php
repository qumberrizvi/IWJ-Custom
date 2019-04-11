<?php

add_filter( 'posts_where', 'iwj_employer_posts_where', 10, 2 );
function iwj_employer_posts_where( $where, $wp_query )
{
    global $wpdb;

    if ( isset($_REQUEST['alpha']) && $_REQUEST['alpha'] && (strlen($_REQUEST['alpha']) == 1) ) {
        if (isset($wp_query->query) && isset($wp_query->query['post_type'])) {
            if ($wp_query->query['post_type'] == 'iwj_employer') {

                if ($_REQUEST['alpha'] == '#') {
                    $where .= ' AND Upper(substr(' . $wpdb->posts . '.post_title ,1,1))  NOT in ("A","B","C","D","E","F","G","H", "I", "J","K","L","M","N", "O", "P", "Q", "R","S", "T", "U", "V", "W", "X","Y", "Z")';
                } else {
                    $where .= ' AND (' . $wpdb->posts . '.post_title LIKE \'' . esc_sql( $wpdb->esc_like( $_REQUEST['alpha'] ) ) . '%\'';
                    $where .= ' OR ' . $wpdb->posts . '.post_title LIKE \'' . esc_sql( $wpdb->esc_like( strtoupper($_REQUEST['alpha']) ) ) . '%\')';
                }
            }
        }
    }

    return $where;
}