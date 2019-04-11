<?php

/**
 * Heading field class.
 */
class IWJMB_Heading_Field extends IWJMB_Field {

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	static function enqueue_scripts() {
		wp_enqueue_style( 'iwjmb-heading', IWJMB_CSS_URL . 'heading.css', array(), IWJMB_VER );
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
		return sprintf( '<h4%s>%s</h4>', $attributes, $field['name'] );
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
		return self::element_description( $field );
	}
}
