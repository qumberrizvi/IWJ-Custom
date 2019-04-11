<?php

$array_data = array();
if ($query->have_posts()) :
    $i = 0;
    $user = IWJ_User::get_user();
    while ($query->have_posts()) :
        $query->the_post();
        $candidate = IWJ_Candidate::get_candidate(get_the_ID());
        $avatar = iwj_get_avatar_url($candidate->get_author_id(), '');
        $maps = $candidate->get_map();
        $author = $candidate->get_author();
        $user_candidate = IWJ_User::get_user($candidate->get_author_id());
        $last_login = get_user_meta($user_candidate->get_id(), '_last_login');
        $diff = '';
        $last_login1 = '';
        $last_login2 = '';
        $text_activities = __('Latest Activities:', 'iwjob');
        $text_ago = __('ago', 'iwjob');
        if ($last_login) {
            $to = time();
            $diff = (int) abs($to - $last_login[0]);
            if ($diff <= MONTH_IN_SECONDS) {
                $last_login1 = human_time_diff($last_login[0]);
            } else {
                $last_login2 = date("j F, Y", $last_login[0]);
            }
        }
        $date_registered = date("j F, Y", strtotime($user_candidate->user->user_registered));
        $lat = $maps[0] * iwj_random_number(0.999999, 1.000001);
        $lng = $maps[1] * iwj_random_number(0.999999, 1.000001);
        $array_data[$i]['lat'] = $lat;
        $array_data[$i]['lng'] = $lng;
        $array_data[$i]['id'] = $candidate->get_id();
        $array_data[$i]['link'] = $candidate->permalink();
        $array_data[$i]['title'] = $candidate->get_title();
        $array_data[$i]['date_registered'] = $date_registered;
        $array_data[$i]['text_activities'] = $text_activities;
        $array_data[$i]['text_ago'] = $text_ago;
        if ($avatar) {
            $array_data[$i]['image'] = $avatar;
        } else {
            $array_data[$i]['image'] = '';
        }
        if ($last_login) {
            $array_data[$i]['last_login_1'] = $last_login1;
            $array_data[$i]['last_login_2'] = $last_login2;
        } else {
            $array_data[$i]['last_login_1'] = '';
            $array_data[$i]['last_login_2'] = '';
        }
        if ($candidate->get_headline()) {
            $array_data[$i]['headline'] = $candidate->get_headline();
        } else {
            $array_data[$i]['headline'] = '';
        }
        $i ++;
    endwhile;
    wp_reset_postdata();
endif;
wp_localize_script('iw-candidate-map', 'iwj_candidate_map', array(
    'map_styles' => Inwave_Helper::getThemeOption('map_styles') ? stripslashes(Inwave_Helper::getThemeOption('map_styles')) : '',
    'close_icon' => IWJ_PLUGIN_URL . '/assets/img/close.png',
    'marker_icon' => $atts['icon_url'],
    'latitude' => $atts['latitude'],
    'auto_center' => $atts['auto_center'],
    'longitude' => $atts['longitude'],
    'zoom' => $atts['zoom'],
    'path_image_google' => $atts['path_image_google'],
    'js_array_map' => $array_data,
));

if ($atts['height'] != '') {
    if (is_numeric($atts['height'])) {
        $height = 'style="height:' . esc_attr($atts['height']) . 'px"';
    } else {
        $height = 'style="height:' . esc_attr($atts['height']) . '"';
    }
}

$output = '';

$output .= '<div class="iwj-candidate-with-map" >';
$output .= '<div class="map-view map-frame" ' . $height . '></div>';
$output .= '</div>';
echo $output;
