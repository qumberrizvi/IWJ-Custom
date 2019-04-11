<?php
/**
 * Date field class.
 */
class IWJMB_Date_Field extends IWJMB_Datetime_Field
{

    public static function normalize($field)
    {
    	$display_date_format = iwj_option('display_date_format', 'Y/m/d');
        $field = wp_parse_args( $field, array(
            'format'  => $display_date_format,
            'js_options' => array(),
        ) );

        // Deprecate 'format', but keep it for backward compatible
        // Use 'js_options' instead
        $field['js_options'] = apply_filters('iwj_date_options', $field['js_options']);
        
        $field['js_options'] = wp_parse_args( $field['js_options'], array(
            'format'      => empty( $field['format'] ) ? $display_date_format : $field['format'],
        ) );

        $field['js_options']['timepicker'] = false;
        $field['js_options']['datepicker'] = true;

        $field = parent::normalize( $field );

        return $field;
    }
}