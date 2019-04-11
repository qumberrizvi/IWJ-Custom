<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class IWJ_Admin_Alerts_Form_List_Table extends WP_List_Table {

    public static function define_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __( 'Title', 'iwjob' ),
            'name' => __( 'Name', 'iwjob' ),
            'email' => __( 'Email', 'iwjob' ),
            'user' => __( 'User', 'iwjob' ),
            'criterias' => __( 'Criteria', 'iwjob' ),
            'salary_from' => __( 'Salary From', 'iwjob' ),
            'alert_frequency' => __( 'Frequency', 'iwjob' ),
            'verify' => __( 'Verify', 'iwjob' ),
        );

        return $columns;
    }

    function __construct() {
        parent::__construct( array(
            'singular' => 'alert',
            'plural' => 'alerts',
            'ajax' => false,
        ) );
    }

    function prepare_items() {
        $current_screen = get_current_screen();
        $per_page = 20;
        $paged = isset($_GET['paged']) ? $_GET['paged'] : 1;
        $from_item = ($paged -1) * $per_page;
        $s = isset($_GET['s']) ? $_GET['s'] : '';
        $search = '';
        if($s){
            if(is_email($s)){
                $user = get_user_by('email', $s);
                if($user){
                    $search = 'user_id = '.$user->ID;
                }else{
                    $search = 'email = "'.$s.'"';
                }
            }else{
                $search = 'position LIKE "%'.$s.'%"';
            }
        }
        $this->_column_headers = $this->get_column_info();

        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}iwj_alerts ".($search ? 'WHERE '.$search : '')." LIMIT $from_item,$per_page";
        $this->items = $wpdb->get_results($sql);

        $sql = "SELECT COUNT(1) FROM {$wpdb->prefix}iwj_alerts  ".($search ? 'WHERE '.$search : '');
        $total_items = $wpdb->get_var($sql);
        $total_pages = ceil( $total_items / $per_page );

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'total_pages' => $total_pages,
            'per_page' => $per_page,
        ) );
    }

    function get_columns() {
        return get_column_headers( get_current_screen() );
    }

    function get_sortable_columns() {
        $columns = array(
            /*'title' => array( 'title', true ),
            'author' => array( 'author', false ),
            'date' => array( 'date', false ),*/
        );

        return $columns;
    }

    function get_bulk_actions() {
        $actions = array(
            'delete' => __( 'Delete', 'iwjob' ),
            'send' => __( 'Send', 'iwjob' ),
        );

        return $actions;
    }

    function column_default( $item, $column_name ) {
        return '';
    }

    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            $item->ID );
    }

    function column_title( $item ) {
        $send_url = add_query_arg(array('action'=> 'send', 'alert'=>$item->ID));
        $delete_url = add_query_arg(array('action'=> 'delete', 'alert'=>$item->ID));
        $verify_url = add_query_arg(array('action'=> 'verify', 'alert'=>$item->ID));
        $html = '<div class="row-actions">
                    '.($item->status === '0' ? '<span class="verify">
                        <a href="'.$verify_url.'">'.__('Verify', 'iwjob').'</a> | 
                    </span>' : '').'
                    <span class="view">
                        <a href="'.$send_url.'">'.__('Send', 'iwjob').'</a> | 
                    </span>
                    <span class="trash">
                        <a href="'.$delete_url.'" class="submitdelete">'.__('Delete', 'iwjob').'</a>
                    </span>
                  </div>';
        return $item->position . $html;
    }

    function column_name( $item ) {
	    $alert = IWJ_Alert::get_alert($item);
        return $alert->get_name();
    }

    function column_email( $item ) {
        $alert = IWJ_Alert::get_alert($item);
        return $alert->get_email();
    }

    function column_user( $item ) {
        if($item->user_id){
            $user = get_userdata($item->user_id);
            if($user){
                echo $user->display_name;
            }else{
                echo __('Unknown', 'iwjob');
            }
        }else{
            echo __('Guest', 'iwjob');
        }
    }

    function column_criterias( $item ) {
        $alert = IWJ_Alert::get_alert($item);
        return $alert->get_relationship_titles('', ', ');
    }

    function column_salary_from( $item ) {
        return $item->salary_from;
    }

    function column_alert_frequency( $item ) {
        return ucfirst($item->frequency);
    }
    function column_verify( $item ) {
        return $item->status === '0' ? __('No', 'iwjob') : __('Yes', 'iwjob');
    }
}
