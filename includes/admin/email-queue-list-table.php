<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class IWJ_Email_Queue_Form_List_Table extends WP_List_Table {

	public static function define_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'from_name' => __( 'From Name', 'iwjob' ),
			'from_address' => __( 'From Address', 'iwjob' ),
			'recipients' => __( 'Recipients', 'iwjob' ),
			'subject' => __( 'Subject', 'iwjob' ),
			'attemp' => __( 'Attempt', 'iwjob' ),
		);

		return $columns;
	}

	function __construct() {
		parent::__construct( array(
			'singular' => 'email',
			'plural' => 'emails',
			'ajax' => false,
		) );
	}

	function prepare_items() {
		$current_screen = get_current_screen();
		$per_page = $this->get_items_per_page( 'iwj_email_queue_forms_per_page' );

		$this->_column_headers = $this->get_column_info();

		global $wpdb;
		$sql = "SELECT * FROM {$wpdb->prefix}iwj_email_queue LIMIT 0,$per_page";
        $this->items = $wpdb->get_results($sql);

        $sql = "SELECT COUNT(1) FROM {$wpdb->prefix}iwj_email_queue";
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

	function column_from_name( $item ) {
		return $item->from_name;
	}

	function column_from_address( $item ) {
		return $item->from_address;
	}

	function column_recipients( $item ) {
		return $item->recipients;
	}

	function column_subject( $item ) {
		return $item->subject;
	}

	function column_attemp( $item ) {
		return $item->attemp;
	}


	/*function column_date( $item ) {
		$post = get_post( $item->id() );

		if ( ! $post ) {
			return;
		}

		$t_time = mysql2date( __( 'Y/m/d g:i:s A', 'contact-form-7' ),
			$post->post_date, true );
		$m_time = $post->post_date;
		$time = mysql2date( 'G', $post->post_date )
			- get_option( 'gmt_offset' ) * 3600;

		$time_diff = time() - $time;

		if ( $time_diff > 0 && $time_diff < 24*60*60 ) {
			$h_time = sprintf(
				__( '%s ago', 'contact-form-7' ), human_time_diff( $time ) );
		} else {
			$h_time = mysql2date( __( 'Y/m/d', 'contact-form-7' ), $m_time );
		}

		return '<abbr title="' . $t_time . '">' . $h_time . '</abbr>';
	}*/
}
