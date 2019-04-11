<?php
/**
 * Checkbox list field class.
 */
class IWJMB_Checkbox_List_Field extends IWJMB_Input_List_Field {

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	static function normalize( $field ) {
		$field['multiple'] = true;
		$field = parent::normalize( $field );

		return $field;
	}
}
