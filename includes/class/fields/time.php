<?php
/**
 * Time field class.
 */
class IWJMB_Time_Field extends IWJMB_Datetime_Field {

    public static function normalize($field)
    {
        $field = wp_parse_args( $field, array(
            'format'  => 'H:i:s',
            'js_options' => array(),
        ) );

        // Deprecate 'format', but keep it for backward compatible
        // Use 'js_options' instead
        $field['js_options'] = wp_parse_args( $field['js_options'], array(
            'format'      => empty( $field['format'] ) ? 'H:i:s' : $field['format'],
        ) );

        $field['js_options']['timepicker'] = true;
        $field['js_options']['datepicker'] = false;

        $field = parent::normalize( $field );

        return $field;
    }
}
