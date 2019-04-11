<?php

/**
 * Abstract input field class which is used for all <input> fields.
 */
class IWJMB_User_Ajax_Field extends IWJMB_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_ajax_iwj_user_ajax_field', array( __CLASS__, 'get_users' ) );
    }

    static function get_users(){
        check_ajax_referer('iwj-security');

        $q = sanitize_text_field($_REQUEST['q']);
        $role = sanitize_text_field($_REQUEST['role']);
        $args = array(
            'role'         => $role,
            'search'       => '*'.esc_attr( $q ).'*',
            'fields'       => array('ID', 'display_name'),
        );
        $users = get_users( $args );
        $options = array();

        if($users){
            foreach ($users as $user){
                $options[] = array('id' => $user->ID, 'text' => $user->display_name);
            }
        }

        echo json_encode($options);
        exit;
    }

    /**
     * Enqueue scripts and styles
     */
    public static function enqueue_scripts() {
        wp_register_style( 'iwjmb-select2', IWJ_FIELD_ASSETS_URL . 'css/select2.css', array(), '4.0.1' );
        wp_register_script( 'iwjmb-select2', IWJ_FIELD_ASSETS_URL . 'js/select2.min.js', array( 'jquery' ), '', true );

        wp_register_script( 'iwjmb-user-ajax', IWJ_FIELD_ASSETS_URL . 'js/user-ajax.js', array(), '', true );
        wp_localize_script('iwjmb-user-ajax', 'rmb_user_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce( "iwj-security" ),
            'action' => 'iwj_user_ajax_field',
        ));
    }

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @return string
	 */
	public static function input( $meta, $field ) {
        wp_enqueue_style('iwjmb-select2');
        wp_enqueue_style( 'iwjmb-select-advanced');

        wp_enqueue_script('iwjmb-select2');
        wp_enqueue_script('iwjmb-user-ajax');

		$attributes = self::call( 'get_attributes', $field, $meta );
		$html = sprintf( '<select %s>', self::render_attributes( $attributes ));
		if($meta){
            $user = get_user_by('ID', $meta);
            $html .= '<option value="'.$meta.'">'.$user->display_name.'</option>';
        }
		$html .= '</select>';

		return $html;
	}

    public static function normalize( $field ) {
        $field = wp_parse_args( $field, array(
            'js_options'  => array(),
            'placeholder' => __( 'Select an item', 'iwjob' ),
            'role' => '',
        ));

        $field = parent::normalize( $field );

        $field['js_options'] = wp_parse_args( $field['js_options'], array(
            'placeholder' => $field['placeholder'],
            'minimumInputLength' => 3,
        ) );

        return $field;
    }

	/**
	 * Get the attributes for a field
	 *
	 * @param array $field
	 * @param mixed $value
	 * @return array
	 */
	public static function get_attributes( $field, $value = null ) {
		$attributes = parent::get_attributes( $field, $value );
		$attributes = wp_parse_args( $attributes, array(
            'data-options' => wp_json_encode( $field['js_options'] ),
			'data-role' => isset($field['role']) ? $field['role'] : '',
		) );

		return $attributes;
	}
}

IWJMB_User_Ajax_Field::init();
