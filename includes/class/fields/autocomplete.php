<?php

/**
 * Autocomplete field class.
 */
class IWJMB_Autocomplete_Field extends IWJMB_Multiple_Values_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

    /**
	 * Enqueue scripts and styles.
	 */
	static function enqueue_scripts() {
		wp_register_style( 'iwjmb-autocomplete', IWJ_FIELD_ASSETS_URL . 'css/autocomplete.css', array( 'wp-admin' ));
		wp_register_script( 'iwjmb-autocomplete', IWJ_FIELD_ASSETS_URL . 'js/autocomplete.js', array( 'jquery-ui-autocomplete' ), '', true );

		self::localize_script( 'iwjmb-autocomplete', 'IWJMB_Autocomplete', array( 'delete' => __( 'Delete', 'iwjob' ) ) );

	}

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @return string
	 */
	static function input( $meta, $field ) {
		if ( ! is_array( $meta ) ) {
			$meta = array( $meta );
		}

        wp_enqueue_style( 'iwjmb-autocomplete');
        wp_enqueue_script( 'iwjmb-autocomplete');

		$field   = apply_filters( 'iwjmb_autocomplete_field', $field, $meta );
		$options = $field['options'];

		if ( ! is_string( $field['options'] ) ) {
			$options = array();
			foreach ( (array) $field['options'] as $value => $label ) {
				$options[] = array(
					'value' => $value,
					'label' => $label,
				);
			}

			$options = wp_json_encode( $options );
		}

		// Input field that triggers autocomplete.
		// This field doesn't store field values, so it doesn't have "name" attribute.
		// The value(s) of the field is store in hidden input(s). See below.
		$html = sprintf(
			'<input type="text" class="iwjmb-autocomplete-search" size="%s">
			<input type="hidden" name="%s" class="iwjmb-autocomplete" data-options="%s" disabled>',
			$field['size'],
			$field['field_name'],
			''//esc_attr( $options )
		);

		$html .= '<div class="iwjmb-autocomplete-results">';

		// Each value is displayed with label and 'Delete' option
		// The hidden input has to have ".iwjmb-*" class to make clone work
		$tpl = '
			<div class="iwjmb-autocomplete-result">
				<div class="label">%s</div>
				<div class="actions">%s</div>
				<input type="hidden" class="iwjmb-autocomplete-value" name="%s" value="%s">
			</div>
		';

		if ( is_array( $field['options'] ) ) {
			foreach ( $field['options'] as $value => $label ) {
				if ( in_array( $value, $meta ) ) {
					$html .= sprintf(
						$tpl,
						$label,
						__( 'Delete', 'iwjob' ),
						$field['field_name'],
						$value
					);
				}
			}
		} else {
			foreach ( $meta as $value ) {
				if ( empty( $value ) ) {
					continue;
				}
				$label = apply_filters( 'iwjmb_autocomplete_result_label', $value, $field );
				$html .= sprintf(
					$tpl,
					$label,
					__( 'Delete', 'iwjob' ),
					$field['field_name'],
					$value
				);
			}
		}

		$html .= '</div>'; // .iwjmb-autocomplete-results

		return $html;
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	static function normalize( $field ) {
		$field = parent::normalize( $field );
		$field = wp_parse_args( $field, array(
			'size' => 30,
		) );
		return $field;
	}
}

IWJMB_Autocomplete_Field::init();