<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class IWJ_Reviews {
	static $cache = array();
	public $review;

	function __construct( $review ) {
		$this->review = $review;
	}

	static public function add_review( $criterias, $user_id, $rating, $item_id, $title, $content, $status = 'pending' ) {
		global $wpdb;
		$insert = array(
			'user_id'   => $user_id,
			'rating'    => $rating,
			'item_id'   => $item_id,
			'title'     => $title,
			'content'   => $content,
			'time'      => current_time( 'timestamp' ),
			'status'    => $status,
			'criterias' => $criterias,
			'read'      => 0,
		);
		$format = array(
			'%d',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
		);

		if ( $wpdb->insert( "{$wpdb->prefix}iwj_reviews", $insert, $format ) ) {
			return $wpdb->insert_id;
		}

		return false;
	}

	static public function send_review( $args ) {
		if ( $args && count( $args ) ) {
			$review_id = self::add_review( $args['criterias'], $args['user_id'], $args['rating'], $args['item_id'], $args['title'], $args['content'] );
			if ( $review_id ) {
				IWJ_Email::send_email( 'new_review', self::get_review( $review_id ) );
			}
		}
	}

	static public function get_employer_reviews( $item_id, $status = '', $args = array() ) {
		global $wpdb;
		$default_args = array( 'posts_per_page' => iwj_option( 'dashboard_items_per_page', get_option( 'posts_per_page', 5 ) ) );
		$args         = wp_parse_args( $args, $default_args );
		$query        = "SELECT * FROM {$wpdb->prefix}iwj_reviews WHERE item_id = '" . $item_id . "'";
		if ( $status ) {
			$query .= " AND status = '" . $status . "' ";
		}

		$total_query    = "SELECT COUNT(1) FROM ({$query}) AS combined_table";
		$total          = $wpdb->get_var( $total_query );
		$items_per_page = $args['posts_per_page'];
		$page           = isset( $_GET['rpage'] ) ? abs( (int) $_GET['rpage'] ) : 1;
		$offset         = ( $page * $items_per_page ) - $items_per_page;
		$result         = $wpdb->get_results( $query . " ORDER BY ID DESC LIMIT ${offset}, ${items_per_page}" );
		$total_page     = ceil( $total / $items_per_page );
		if ( $total ) {
			return array(
				'result'       => $result,
				'total_page'   => $total_page,
				'current_page' => $page,
			);
		} else {
			return null;
		}
	}

	static public function delete_review( $review_ids ) {
		global $wpdb;
		if ( ! is_array( $review_ids ) ) {
			$review_ids = array( $review_ids );
		}

		$format = array_fill( 0, count( $review_ids ), '%d' );

		$query_in = '(' . implode( ',', $format ) . ')';

		$query = $wpdb->prepare(
			"DELETE FROM {$wpdb->prefix}iwj_reviews WHERE ID IN {$query_in}",
			$review_ids
		);

		$wpdb->query( $query );

		$query = $wpdb->prepare(
			"DELETE FROM {$wpdb->prefix}iwj_reviews_reply WHERE review_id IN {$query_in}",
			$review_ids
		);

		$wpdb->query( $query );

		return true;
	}

	static public function update_review_status( $ids = array(), $status ) {
		global $wpdb;

		$sql = "UPDATE {$wpdb->prefix}iwj_reviews SET status = %s, `read` = 1 WHERE ID IN(" . implode( ",", $ids ) . ")";

		return $wpdb->query( $wpdb->prepare( $sql, $status ) );
	}

	static public function set_read( $ids ) {
		global $wpdb;
		$ids = (array) $ids;
		$sql = "UPDATE {$wpdb->prefix}iwj_reviews SET `read` = 1 WHERE ID IN(" . implode( ",", $ids ) . ")";

		return $wpdb->query( $sql );
	}

	static public function get_number_stars( $num ) {
		$html = '';
		for ( $i = 1; $i <= 5; $i ++ ) {
			if ( (int) ( $num ) != $num ) {
				if ( $i < ceil( $num ) ) {
					$cls = 'ion-android-star';
				} elseif ( $i == ceil( $num ) ) {
					$cls = 'ion-android-star-half';
				} else {
					$cls = 'ion-android-star-outline';
				}
			} else {
				if ( $i <= $num ) {
					$cls = 'ion-android-star';
				} else {
					$cls = 'ion-android-star-outline';
				}
			}

			$html .= '<i class="ion ' . $cls . '"></i>';
		}

		return $html;
	}

	static public function get_total_criterias( $review_id ) {
		global $wpdb;
		$sql     = "SELECT criterias FROM {$wpdb->prefix}iwj_reviews WHERE ID = %d";
		$results = $wpdb->get_results( $wpdb->prepare( $sql, $review_id ) );
		if ( count( $results ) ) {
			return $results[0]->criterias;
		} else {
			return null;
		}
	}

	static public function get_average_rate( $item_id ) {
		global $wpdb;
		$sql           = "SELECT rating FROM {$wpdb->prefix}iwj_reviews WHERE item_id = %d AND status = 'approved'";
		$results       = $wpdb->get_results( $wpdb->prepare( $sql, $item_id ) );
		$average_total = array();
		if ( $results && count( $results ) ) {
			$totals = count( $results );
			$sum    = 0;
			foreach ( $results as $result ) {
				$sum += $result->rating;
			}
			$average       = round( $sum / $totals, 1, PHP_ROUND_HALF_UP );
			$average_total = array(
				'totals'  => $totals,
				'average' => $average
			);
		}

		return $average_total;
	}

	static function get_review( $review = '', $force = false ) {
		$review_id = 0;
		if ( $review ) {
			if ( is_numeric( $review ) ) {
				$review_id = $review;
			} elseif ( is_object( $review ) ) {
				$review_id = $review->ID;
			}
		}

		if ( $review_id ) {
			if ( $force || ! is_object( $review ) ) {
				global $wpdb;
				$sql    = "SELECT * FROM {$wpdb->prefix}iwj_reviews WHERE ID = %d";
				$review = $wpdb->get_row( $wpdb->prepare( $sql, $review_id ) );
			}

			if ( $force || ! isset( self::$cache[ $review_id ] ) ) {
				if ( $review ) {
					self::$cache[ $review_id ] = new IWJ_Reviews( $review );
				} else {
					self::$cache[ $review_id ] = null;
				}
			}

			return self::$cache[ $review_id ];
		}

		return null;
	}

	static function get_review_by_id( $id ) {
		global $wpdb;
		$query   = "SELECT * FROM {$wpdb->prefix}iwj_reviews WHERE ID = %d";
		$results = $wpdb->get_results( $wpdb->prepare( $query, $id ) );
		if ( $results ) {
			return $results[0];
		} else {
			return null;
		}
	}

	public function get_id() {
		return $this->review->ID;
	}

	public function get_user_id() {
		return $this->review->user_id;
	}

	public function get_user_name() {
		$author = IWJ_User::get_user( $this->get_user_id() );
        if($author){
            return $author->get_display_name();
        }
		return null;
	}

	public function get_user_post() {
		if ( $this->get_user_id() ) {
			return IWJ_User::get_user( $this->get_user_id() );
		}

		return null;
	}

	public function get_rate_star() {
		return $this->review->rating;
	}

	public function get_item_id() {
		return $this->review->item_id;
	}

	public function get_employer() {
		if ( $this->get_item_id() ) {
			return IWJ_Employer::get_employer( $this->get_item_id() );
		}

		return null;
	}

	public function get_employer_name() {
		$employer = IWJ_Employer::get_employer( $this->get_item_id() );
		if($employer){
            return $employer->get_display_name();
        }

        return null;
	}

	public function get_author_item_id() {
		$employer = IWJ_Employer::get_employer( $this->get_item_id() );
        if($employer){
            return $employer->get_author_id();
        }

        return null;

    }

	public function get_title() {
		return $this->review->title;
	}

	public function get_content() {
		return $this->review->content;
	}

	public function get_criterias() {
		return unserialize( $this->review->criterias );
	}

	public function get_status() {
		return $this->review->status;
	}

	public function get_read() {
		return $this->review->read;
	}

	public function get_reason() {
		return get_option( 'iwj_rev_reject_reason_' . $this->get_id(), '' );
	}

	public function user_link_candidate_edit_review() {
		return get_home_url() . '/dashboard/?iwj_tab=edit-review&review-id=' . $this->get_id();
	}

	public function review_link() {
		$employer = IWJ_Employer::get_employer( $this->get_item_id() );
		if($employer){
            if ( $employer->permalink() && $this->get_status() == 'approved' ) {
                return $employer->permalink() . '#vote-employer-id-' . $this->get_id();
            }

            return $employer->permalink();
        }

        return null;
	}

	public function can_delete() {
		if ( $this->get_user_id() != get_current_user_id() ) {
			return false;
		}

		return true;
	}

	public function permalink() {
		return get_home_url() . '/dashboard/?iwj_tab=my-reviews';
	}

	public function admin_link() {
		return get_admin_url() . 'edit.php?post_type=iwj_job&page=iwj-reviews&action=edit&review=' . $this->get_id();
	}

	static public function update_review( $args ) {
		global $wpdb;

		$update = array(
			'user_id'   => $args['user_id'],
			'rating'    => $args['rating'],
			'item_id'   => $args['item_id'],
			'title'     => $args['title'],
			'content'   => $args['content'],
			'time'      => current_time( 'timestamp' ),
			'status'    => $args['status'],
			'criterias' => $args['criterias'],
			'read'      => $args['read'],
		);
		$format = array(
			'%d',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
		);

		if ( $args['status'] == 'reject' ) {
			IWJ_Email::send_email( 'rejected_review', self::get_review( $args['ID'], true ) );
			delete_option( 'iwj_rev_reject_reason_' . $args['ID'] );
		}
		if ( $args['status'] == 'approved' ) {
			if (isset($args['update']) && $args['update']){
				IWJ_Email::send_email( 'approved_review', self::get_review( $args['ID'], true ) );
			}else{
				IWJ_Email::send_email( 'approved_review', self::get_review( $args['ID'], true ) );
				IWJ_Email::send_email( 'candidate_approved_review', self::get_review( $args['ID'] ) );
			}
		}
		if ( $args['status'] == 'pending' ) {
			IWJ_Email::send_email( 'new_review', self::get_review( $args['ID'], true ) );
		}

		return false !== $wpdb->update( "{$wpdb->prefix}iwj_reviews", $update, array( 'ID' => $args['ID'] ), $format );
	}

	static public function check_reply_review( $id ) {
		global $wpdb;
		$query   = "SELECT rp.* FROM {$wpdb->prefix}iwj_reviews_reply AS rp JOIN {$wpdb->prefix}iwj_reviews AS re ON rp.review_id = re.ID WHERE re.ID = %d";
		$results = $wpdb->get_results( $wpdb->prepare( $query, $id ) );
		if ( $results ) {
			return $results[0];
		} else {
			return null;
		}
	}

	static public function reply_review( $args ) {
		global $wpdb;
		$insert = array(
			'review_id'     => $args['review_id'],
			'user_id'       => $args['user_id'],
			'reply_content' => $args['reply_content'],
			'time'          => current_time( 'timestamp' ),
		);
		$format = array(
			'%d',
			'%d',
			'%s',
			'%s',
		);

		return false !== $wpdb->insert( "{$wpdb->prefix}iwj_reviews_reply", $insert, $format );
	}

	static public function update_reply_review( $args ) {
		global $wpdb;
		$sql = "UPDATE {$wpdb->prefix}iwj_reviews_reply SET `reply_content` = %s WHERE ID = %d";

		return $wpdb->query( $wpdb->prepare( $sql, $args['reply_content'], $args['ID'] ) );
	}

	static public function delete_reply( $id ) {
		global $wpdb;
		$query = $wpdb->prepare(
			"DELETE FROM {$wpdb->prefix}iwj_reviews_reply WHERE ID = %d",
			$id
		);

		$wpdb->query( $query );

		return true;
	}

}
