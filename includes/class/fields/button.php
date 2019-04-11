<?php
/**
 * Button field class.
 */
class IWJMB_Button_Field extends IWJMB_Field {

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @return string
	 */
	static function input( $meta, $field ) {
		$attributes = self::get_attributes( $field );
		return sprintf( '<a href="#" %s>%s</a>', self::render_attributes( $attributes ), $field['std'] );
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	static function normalize( $field ) {
		$field        = parent::normalize( $field );
		$field['std'] = $field['std'] ? $field['std'] : __( 'Click me', 'iwjob' );
		return $field;
	}

	/**
	 * Get the attributes for a field
	 *
	 * @param array $field
	 * @param mixed $value
	 * @return array
	 */
	static function get_attributes( $field, $value = null ) {
		$attributes = parent::get_attributes( $field, $value );
		$attributes['class'] .= ' button hide-if-no-js';

		return $attributes;
	}
}
