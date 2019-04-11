<?php
/**
 * Map field class.
 */
class IWJMB_Map_Address_Field extends IWJMB_Text_Field {

    static function init(){
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

    /**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	static function enqueue_scripts() {
		/**
		 * Since June 2016, Google Maps requires a valid API key.
		 *
		 * @link http://googlegeodevelopers.blogspot.com/2016/06/building-for-scale-updates-to-google.html
		 * @link https://developers.google.com/maps/documentation/javascript/get-api-key
		 */

		$google_api_key = iwj_get_map_api_key();
		$google_maps_url = add_query_arg( 'key', $google_api_key, 'https://maps.google.com/maps/api/js' );
		$google_maps_url = add_query_arg( 'libraries', 'places', $google_maps_url);
		$google_maps_url = add_query_arg( 'sensor', 'false', $google_maps_url);
		$google_maps_url = add_query_arg( 'language', get_locale(), $google_maps_url);

		/**
		 * Allows developers load more libraries via a filter.
		 *
		 * @link https://developers.google.com/maps/documentation/javascript/libraries
		 */

		//wp_register_script( 'google-maps', esc_url_raw( $google_maps_url ), array(), '', true );
        wp_register_script( 'iwjmb-map-address', IWJ_FIELD_ASSETS_URL . 'js/map_address.js', array( 'google-maps' ), '', true );

	}

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	static function input( $meta, $field ) {
        wp_enqueue_script( 'google-maps');
        wp_enqueue_script( 'iwjmb-map-address');

		$html = '<div class="iwjmb-map-address-field">';

		$html .= sprintf(
			'<input type="text" name="%s" value="%s">',
            esc_attr( $field['field_name'] ),
            esc_attr( $meta )
		);

        if(iwj_option('auto_detect_location')){
            $html .= '<input type="hidden" name="'.$field['field_name'].'_address_components" value="">';
        }

		$html .= '</div>';

		return $html;
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	static function normalize( $field ) {
		$field = parent::normalize( $field );
		$field = wp_parse_args( $field, array(
			'std'           => '',
		) );

		return $field;
	}

    public static function save_post( $new, $old, $post_id, $field ) {
        parent::save_post($new, $old, $post_id, $field);
        if(iwj_option('auto_detect_location') && isset($_POST[$field['field_name'].'_address_components'])){
            $address_components = json_decode(stripslashes($_POST[$field['field_name'].'_address_components']));
            $allow_address_types = (array)iwj_option('allow_adress_types', array('country', 'administrative_area_level_1', 'administrative_area_level_2'));

            $allow_address = array();
            $short_names = array();
            if($address_components){
                foreach ($address_components as $address_component){
                    foreach ($address_component->types as $type){
                        if(in_array($type, $allow_address_types)){
                            $allow_address[] = $address_component->long_name;
                            $short_names[] = isset($address_component->short_name) ? $address_component->short_name : '';
                            break;
                        }
                    }
                }

                $allow_address = array_reverse($allow_address);
                $location_ids = array();
                $parent_id = 0;
                foreach ($allow_address as $key=> $address){
                    $term = get_term_by('name', $address, 'iwj_location');
                    if($term){
                        $parent_id = $term->term_id;
                        $location_ids[] = $term->term_id;
                    }else{
                        $new_term = wp_insert_term($address, 'iwj_location', array('parent' => $parent_id));
                        $parent_id = $new_term['term_id'];
                        $location_ids[] = $new_term['term_id'];
                        if(isset($short_names[$key])){
                            update_term_meta($new_term['term_id'], IWJ_PREFIX.'short_name', $short_names[$key]);
                        }
                    }
                }
                if($location_ids){
                    wp_set_post_terms($post_id, $location_ids, 'iwj_location');
                }
            }
        }
    }
}

IWJMB_Map_Address_Field::init();