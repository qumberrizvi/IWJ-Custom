<?php
/**
 * Divider field class.
 */
class IWJMB_Divider_Field extends IWJMB_Field {

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	static function enqueue_scripts() {
		wp_enqueue_style( 'iwjmb-divider', IWJMB_CSS_URL . 'divider.css', array(), IWJMB_VER );
	}

	/**
	 * Show begin HTML markup for fields
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	static function begin_html( $meta, $field ) {
		$attributes = empty( $field['id'] ) ? '' : " id='{$field['id']}'";
		return "<hr$attributes>";
	}

	/**
	 * Show end HTML markup for fields
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	static function end_html( $meta, $field ) {
		return '';
	}
}
