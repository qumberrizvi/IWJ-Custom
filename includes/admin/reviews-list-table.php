<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class IWJ_Reviews_Form_List_Table extends WP_List_Table {
	public static function define_columns() {
		$columns = array(
			'cb'          => '<input type="checkbox" />',
            'title'       => __( 'Title', 'iwjob' ),
            'author_name' => __( 'Author Name', 'iwjob' ),
			'rating'      => __( 'Rating', 'iwjob' ),
			'item_name'   => __( 'Student', 'iwjob' ),
			'status'      => __( 'Status', 'iwjob' ),
			'content'     => __( 'Content', 'iwjob' ),
			'time'        => __( 'Date', 'iwjob' ),
		);

		return $columns;
	}

	function __construct() {
		parent::__construct( array(
			'singular' => 'review',
			'plural'   => 'reviews',
			'ajax'     => false,
		) );

	}

    /**
     * @param string $which
     */
    protected function extra_tablenav( $which ) {
        parent::extra_tablenav($which);
        return;
        $status = isset($_GET['review_status']) ? $_GET['review_status'] : '';
        ?>
        <div class="alignleft actions" >
            <select name="review_status" onchange='this.form.submit()'>
                <option value="" <?php selected($status, '')?>><?php echo __('All Status', 'iwjob'); ?></option>
                <option value="pending" <?php selected($status, 'pending')?>><?php echo __('Pending', 'iwjob'); ?></option>
                <option value="rejected" <?php selected($status, 'rejected')?>><?php echo __('Rejected', 'iwjob'); ?></option>
                <option value="approved" <?php selected($status, 'approved')?>><?php echo __('Approved', 'iwjob'); ?></option>
            </select>
        </div>
        <?php
    }

	function prepare_items() {
        $per_page = 20;
        $paged = isset($_GET['paged']) ? $_GET['paged'] : 1;
        $from_item = ($paged -1) * $per_page;

		$this->_column_headers = $this->get_column_info();

		$status = isset($_GET['review_status']) ? sanitize_text_field($_GET['review_status']) : '';
		$s = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

		global $wpdb;
		$sql         = "SELECT * FROM {$wpdb->prefix}iwj_reviews";
        $where = array();
        if($status || $s ){
		    if($status){
		        $where[] = 'status="'.$status.'"';
            }
		    if($s){
		        $where[] = 'title like "%'.$s.'%"';
            }

            $sql .= " WHERE ".implode(" AND ", $where);
        }

        $sql .= " LIMIT $from_item,$per_page";

		$this->items = $wpdb->get_results( $sql );

		$sql         = "SELECT COUNT(1) FROM {$wpdb->prefix}iwj_reviews";
		if($where){
            $sql .= " WHERE ".implode(" AND ", $where);
        }

		$total_items = $wpdb->get_var( $sql );
		$total_pages = ceil( $total_items / $per_page );

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'total_pages' => $total_pages,
			'per_page'    => $per_page,
		) );
	}

	function get_columns() {
		return get_column_headers( get_current_screen() );
	}

	function get_bulk_actions() {
		$actions = array(
			'delete'   => __( 'Delete', 'iwjob' ),
			'pending'  => __( 'Pending', 'iwjob' ),
			'approved' => __( 'Approve', 'iwjob' ),
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

	function column_author_name( $item ) {
		$author = IWJ_User::get_user( $item->user_id );
        if($author){
            return $author->get_display_name();
        }else{
            return __('Unknown', 'iwjob');
        }
	}

	function column_rating( $item ) {
		return $item->rating;
	}

	function column_item_name( $item ) {
		$employer = IWJ_Employer::get_employer( $item->item_id );
        if($employer){
            return $employer->get_display_name();
        }
        else{
            return __('Unknown', 'iwjob');
        }
	}

	function column_status( $item ) {
		return '<span class="job-status ' . $item->status . '">' . $item->status . '</span>';
	}

	function column_title( $item ) {
        $review = IWJ_Reviews::get_review( $item->ID );

        if ( $review->get_status() == 'approved' ) {
            $actions = array(
                'edit'   => sprintf( '<a href="?post_type=iwj_job&page=%s&action=%s&review=%s">Edit</a>', $_REQUEST['page'], 'edit', $item->ID ),
                'delete' => sprintf( '<a href="?post_type=iwj_job&page=%s&action=%s&review=%s" onclick="return confirm(\'Are you sure you want to delete this item?\');">Delete</a>', $_REQUEST['page'], 'delete', $item->ID ),
            );
        }
        if ( $review->get_status() == 'pending' ) {
            $actions = array(
                'approved' => sprintf( '<a href="?post_type=iwj_job&page=%s&action=%s&review=%s">Approve</a>', $_REQUEST['page'], 'approved', $item->ID ),
                'edit'     => sprintf( '<a href="?post_type=iwj_job&page=%s&action=%s&review=%s">Edit</a>', $_REQUEST['page'], 'edit', $item->ID ),
                'delete'   => sprintf( '<a href="?post_type=iwj_job&page=%s&action=%s&review=%s" onclick="return confirm(\'Are you sure you want to delete this item?\');">Delete</a>', $_REQUEST['page'], 'delete', $item->ID ),
            );
        }
        if ( $review->get_status() == 'reject' ) {
            $actions = array(
                'approved' => sprintf( '<a href="?post_type=iwj_job&page=%s&action=%s&review=%s">Approve</a>', $_REQUEST['page'], 'approved', $item->ID ),
                'delete' => sprintf( '<a href="?post_type=iwj_job&page=%s&action=%s&review=%s" onclick="return confirm(\'Are you sure you want to delete this item?\');">Delete</a>', $_REQUEST['page'], 'delete', $item->ID ),
            );
        }

        $read = $item->read ? '' : 'no-read';
        return sprintf( '%1$s %2$s', '<a class="'.$read.'" href="?post_type=iwj_job&page=iwj-reviews&action=edit&review='.$item->ID.'">'.$item->title.'</a>', $this->row_actions( $actions ) );
	}

	function column_content( $item ) {
		return $item->content;
	}

	function column_time( $item ) {
		$data_time = date_i18n( get_option( 'date_format' ), $item->time );

		return $data_time;
	}

	function column_criterias( $item ) {
		return unserialize( $item->criterias );
	}

}