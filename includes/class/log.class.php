<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class IWJ_Log{

	public static function add( $message, $type) {
		global $wpdb;

		$insert = array(
			'timestamp' => current_time('timestamp'),
			'type' => $type,
			'message' => $message,
		);

		$format = array(
			'%d',
			'%s',
			'%s',
		);

		return false !== $wpdb->insert( "{$wpdb->prefix}iwj_logs", $insert, $format );
	}

	/**
	 * Clear all logs from the DB.
	 *
	 * @return bool True if flush was successful.
	 */
	public static function flush() {
		global $wpdb;

		return $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}iwj_logs" );
	}

	/**
	 * Delete selected logs from DB.
	 *
	 * @param int|string|array Log ID or array of Log IDs to be deleted.
	 *
	 * @return bool
	 */
	public static function delete( $log_ids ) {
		global $wpdb;

		if ( ! is_array( $log_ids ) ) {
			$log_ids = array( $log_ids );
		}

		$format = array_fill( 0, count( $log_ids ), '%d' );

		$query_in = '(' . implode( ',', $format ) . ')';

		$query = $wpdb->prepare(
			"DELETE FROM {$wpdb->prefix}iwj_logs WHERE ID IN {$query_in}",
			$log_ids
		);

		return $wpdb->query( $query );
	}
}
