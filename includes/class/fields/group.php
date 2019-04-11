<?php

class IWJMB_Group_Field extends IWJMB_Field {

    /**
     * Get field input
     *
     * @param mixed $meta
     * @param array $field
     * @return string
     */
    public static function input( $meta, $field ) {
        $saved = $meta ? true : false;
        $html = '';
        if($field['fields']){
            ob_start();
            if($field['parent_tag'] != 'div'){
                echo '<table class="form-table">';
            }
            foreach ( $field['fields'] as $sub_field ) {
                $sub_field = IWJMB_Field::call( 'normalize', $sub_field );

                $sub_field['id'] = $field['id'].'_'.$sub_field['id'];
                if($saved){
                    $sub_meta = isset($meta[$sub_field['field_name']]) ? $meta[$sub_field['field_name']] : '';
                }
                else{
                    $sub_meta = $sub_field['std'];
                }

                if ( $sub_field['clone'] || $sub_field['multiple'] ) {
                    if ( empty( $sub_meta ) || ! is_array( $sub_meta ) ) {
                        $sub_meta = $sub_field['clone'] ? array( '' ) : array();
                    }
                }

                $sub_field['field_name'] = $field['field_name'].'['.$sub_field['field_name'].']';

                IWJMB_Field::input($sub_field, $sub_meta );
            }
            if($field['parent_tag'] != 'div'){
                echo '</table>';
            }
            $html = ob_get_clean();
        }

        return $html;
    }

}
