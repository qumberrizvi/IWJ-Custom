<?php

/**
 * The Meta Box Clone class.
 *
 * @package Meta Box
 */
class IWJMB_Clone {

	/**
	 * Get clone field input
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	public static function input( $meta, $field ) {
		$field_html = '';

		/**
		 * Note: $meta must contain value so that the foreach loop runs!
		 *
		 * @see meta()
		 */
		foreach ( $meta as $index => $sub_meta ) {
			$sub_field               = $field;
			$sub_field['field_name'] = $field['field_name'] . "[{$index}]";
			if ( $index > 0 ) {
				if ( isset( $sub_field['address_field'] ) ) {
					$sub_field['address_field'] = $field['address_field'] . "_{$index}";
				}
				$sub_field['id'] = $field['id'] . "_{$index}";
			}
			if ( $field['multiple'] ) {
				$sub_field['field_name'] .= '[]';
			}

			// Wrap field HTML in a div with class="iwjmb-clone" if needed
			$class     = "iwjmb-clone iwjmb-{$field['type']}-clone";
			$sort_icon = '';
			if ( $field['sort_clone'] ) {
				$class .= ' iwjmb-sort-clone';
				$sort_icon = "<a href='javascript:;' class='iwjmb-clone-icon'></a>";
			}
			$input_html = "<div class='$class'>" . $sort_icon;

			// Call separated methods for displaying each type of field
			$input_html .= IWJMB_Field::call( $sub_field, 'input', $sub_meta );

			// Remove clone button
			$input_html .= self::remove_clone_button( $sub_field );
			$input_html .= '</div>';

			$field_html .= $input_html;
		}

		return $field_html;
	}

	/**
	 * Set value of meta before saving into database
	 *
	 * @param mixed $new
	 * @param mixed $old
	 * @param int   $post_id
	 * @param array $field
	 *
	 * @return mixed
	 */
	public static function value( $new, $old, $post_id, $field ) {
		foreach ( $new as $key => $value ) {
			$old_value = isset( $old[ $key ] ) ? $old[ $key ] : null;
			$value     = IWJMB_Field::call( $field, 'value', $value, $old_value, $post_id );
			$new[ $key ] = IWJMB_Field::call( $field, 'sanitize_value', $value );
		}

		return $new;
	}

	/**
	 * Add clone button
	 *
	 * @param array $field Field parameter
	 * @return string $html
	 */
	public static function add_clone_button( $field ) {
		if ( ! $field['clone'] ) {
			return '';
		}
		$text = IWJMB_Field::filter( 'add_clone_button_text', __( '+ Add more', 'iwjob' ), $field );
		return '<a href="#" class="iwjmb-button button-primary add-clone">' . esc_html( $text ) . '</a>';
	}

	/**
	 * Remove clone button
	 *
	 * @param array $field Field parameter
	 * @return string $html
	 */
	public static function remove_clone_button( $field ) {
		$text = IWJMB_Field::filter( 'remove_clone_button_text', '<i class="dashicons dashicons-minus"></i>', $field );
		return '<a href="#" class="iwjmb-button remove-clone">' . $text . '</a>';
	}
}
