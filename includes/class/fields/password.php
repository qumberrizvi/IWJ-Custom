<?php
/**
 * Password field class.
 */
class IWJMB_Password_Field extends IWJMB_Text_Field {

	/**
	 * Store secured password in the database.
	 *
	 * @param mixed $new
	 * @param mixed $old
	 * @param int   $post_id
	 * @param array $field
	 * @return string
	 */
	static function value( $new, $old, $post_id, $field ) {
		$new = $new != $old ? wp_hash_password( $new ) : $new;
		return $new;
	}
}
