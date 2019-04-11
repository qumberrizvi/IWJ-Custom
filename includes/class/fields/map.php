<?php
/**
 * Map field class.
 */
class IWJMB_Map_Field extends IWJMB_Field {

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
        wp_register_style( 'iwjmb-map', IWJ_FIELD_ASSETS_URL . 'css/map.css' );
        wp_register_script( 'iwjmb-map', IWJ_FIELD_ASSETS_URL . 'js/map.js', array( 'jquery-ui-autocomplete', 'google-maps' ), '', true );

        wp_localize_script('iwjmb-map', 'iwjmap', array(
            'map_styles' => stripslashes(iwj_option('map_styles')),
        ));
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
        wp_enqueue_style( 'iwjmb-map');
        wp_enqueue_script( 'iwjmb-map');

        $map_maker_url = '';
        $map_maker = iwj_option('iwj_map_maker');
        if ($map_maker) {
            $map_maker_url = wp_get_attachment_url($map_maker[0]);
        }
		$html = '<div class="iwjmb-map-field" data-marker="'.$map_maker_url.'">';

		$html .= sprintf(
			'<div class="iwjmb-map-canvas" data-default-loc="%s"></div>
			<input type="hidden" name="%s" class="iwjmb-map-coordinate" value="%s">',
			esc_attr( $field['std'] ),
			esc_attr( $field['field_name'] ),
			esc_attr( $meta )
		);

		if(iwj_option('auto_detect_location')){
            $html .= '<input type="hidden" name="'.$field['field_name'].'_address_components" class="iwjmb-map-address_components" value="">';
        }

		if ( $address = $field['address_field'] ) {
			$html .= sprintf(
				'<button class="button iwjmb-map-goto-address-button" value="%s">%s</button>',
				is_array( $address ) ? implode( ',', $address ) : $address,
				__( 'Find Address', 'iwjob' )
			);
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
			'address_field' => '',

			// Default API key, required by Google Maps since June 2016.
			// Users should overwrite this key with their own key.
			'api_key'       => 'AIzaSyC1mUh87SGFyf133tpZQJa-s96p0tgnraQ',
		) );

		return $field;
	}

	/**
	 * Get the field value
	 * The difference between this function and 'meta' function is 'meta' function always returns the escaped value
	 * of the field saved in the database, while this function returns more meaningful value of the field
	 *
	 * @param  array    $field   Field parameters
	 * @param  array    $args    Not used for this field
	 * @param  int|null $post_id Post ID. null for current post. Optional.
	 *
	 * @return mixed Array(latitude, longitude, zoom)
	 */
	static function get_value( $field, $args = array(), $post_id = null ) {
		$value = parent::get_value( $field, $args, $post_id );
		list( $latitude, $longitude, $zoom ) = explode( ',', $value . ',,' );
		return compact( 'latitude', 'longitude', 'zoom' );
	}

    /**
     * Save post meta value
     *
     * @param $new
     * @param $old
     * @param $post_id
     * @param $field
     */
    public static function save_post( $new, $old, $post_id, $field ) {
        parent::save_post($new, $old, $post_id, $field);

        if(iwj_option('auto_detect_location') && isset($_POST[$field['field_name'].'_address_components'])){

            $address_components = json_decode(stripslashes($_POST[$field['field_name'].'_address_components']));
            $allow_address_types = (array)iwj_option('allow_adress_types', array('country', 'administrative_area_level_1', 'administrative_area_level_2'));

            $allow_address = array();

            if($address_components){
                foreach ($address_components as $address_component){
                    foreach ($address_component->types as $type){
                        if(in_array($type, $allow_address_types)){
                            $allow_address[] = $address_component->long_name;
                            break;
                        }
                    }
                }

                $allow_address = array_reverse($allow_address);
                $location_ids = array();
                $parent_id = 0;
                foreach ($allow_address as $address){
                    $term = get_term_by('name', $address, 'iwj_location');
                    if($term){
                        $parent_id = $term->term_id;
                        $location_ids[] = $term->term_id;
                    }else{
                        $new_term = wp_insert_term($address, 'iwj_location', array('parent' => $parent_id));
                        $parent_id = $new_term['term_id'];
                        $location_ids[] = $new_term['term_id'];
                    }
                }

                if($location_ids){
                    wp_set_post_terms($post_id, $location_ids, 'iwj_location');
                }
            }
        }
    }

	/**
	 * Output the field value
	 * Display Google maps
	 *
	 * @param  array    $field   Field parameters
	 * @param  array    $args    Additional arguments. Not used for these fields.
	 * @param  int|null $post_id Post ID. null for current post. Optional.
	 *
	 * @return mixed Field value
	 */
	static function the_value( $field, $args = array(), $post_id = null ) {
		$value = self::get_value( $field, $args, $post_id );
		if ( ! $value['latitude'] || ! $value['longitude'] ) {
			return '';
		}
		if ( ! $value['zoom'] ) {
			$value['zoom'] = 14;
		}

		$args = wp_parse_args( $args, array(
			'latitude'     => $value['latitude'],
			'longitude'    => $value['longitude'],
			'width'        => '100%',
			'height'       => '480px',
			'marker'       => true, // Display marker?
			'marker_title' => '', // Marker title, when hover
			'info_window'  => '', // Content of info window (when click on marker). HTML allowed
			'js_options'   => array(),

			// Default API key, required by Google Maps since June 2016.
			// Users should overwrite this key with their own key.
			'api_key'       => 'AIzaSyC1mUh87SGFyf133tpZQJa-s96p0tgnraQ',
		) );

		/*
		 * Enqueue scripts
		 * API key is get from $field (if found by IWJMB_Helper::find_field()) or $args as a fallback
		 * Note: We still can enqueue script which outputs in the footer
		 */
		$api_key = isset( $field['api_key'] ) ? $field['api_key'] : $args['api_key'];
		$google_maps_url = add_query_arg( 'key', $api_key, 'https://maps.google.com/maps/api/js' );

		/*
		 * Allows developers load more libraries via a filter.
		 * @link https://developers.google.com/maps/documentation/javascript/libraries
		 */
		$google_maps_url = apply_filters( 'iwjmb_google_maps_url', $google_maps_url );
		//wp_register_script( 'google-maps', esc_url_raw( $google_maps_url ), array(), '', true );
		wp_enqueue_script( 'iwjmb-map-frontend', IWJMB_JS_URL . 'map-frontend.js', array( 'google-maps' ), '', true );

		/*
		 * Google Maps options
		 * Option name is the same as specified in Google Maps documentation
		 * This array will be convert to Javascript Object and pass as map options
		 * @link https://developers.google.com/maps/documentation/javascript/reference
		 */
		$args['js_options'] = wp_parse_args( $args['js_options'], array(
			// Default to 'zoom' level set in admin, but can be overwritten
			'zoom'      => $value['zoom'],

			// Map type, see https://developers.google.com/maps/documentation/javascript/reference#MapTypeId
			'mapTypeId' => 'ROADMAP',
		) );

		$output = sprintf(
			'<div class="iwjmb-map-canvas" data-map_options="%s" style="width:%s;height:%s"></div>',
			esc_attr( wp_json_encode( $args ) ),
			esc_attr( $args['width'] ),
			esc_attr( $args['height'] )
		);
		return $output;
	}
}

IWJMB_Map_Field::init();