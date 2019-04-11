<?php

function iwj_get_wpml_languages() {
    global $wpdb;
    $lang_data = array();
    $languages = $wpdb->get_results("SELECT code, english_name, tag
            FROM {$wpdb->prefix}icl_languages WHERE active = 1");

    foreach ($languages as $l) {
        $lang_data[$l->code] = array(
            'code' => $l->code,
            'english_name' => $l->english_name,
            'tag' => $l->tag,
            'country_flag_url' => plugins_url() . '/sitepress-multilingual-cms/res/flags/' . $l->code . '.png',
        );
    }

    return $lang_data;
}

function iwj_get_actual_wpml_url($url) {
    global $sitepress;
    $lang = $sitepress->get_current_language();
    $actual_uri = preg_replace('#' . $lang . '$#', '', $url);

    return $actual_uri;
}

function iwj_get_wpml_url($current, $code = null) {
    global $sitepress;

    if ($sitepress) {
        if ($code === null) {
            $code = ICL_LANGUAGE_CODE;
        }
        $default_language = $sitepress->get_default_language();
        if (WPML_LANGUAGE_NEGOTIATION_TYPE_DIRECTORY === (int) $sitepress->get_setting('language_negotiation_type')) {
            $lang = $sitepress->get_current_language();
            $home_url = get_home_url();
            $home_direct = str_replace($lang,'', rtrim($home_url,'/'));
            if($code == $default_language){
                    $code ='';
            }
            if($code && $lang != $default_language){
                    $code = $code .'/';
            }
            if($lang == $default_language){
                    $code = '/'.$code;
            }
            
            $home_replace = str_replace($home_url,$home_direct.$code, $current);
            return $home_replace;
        } elseif (WPML_LANGUAGE_NEGOTIATION_TYPE_PARAMETER === (int) $sitepress->get_setting('language_negotiation_type')) {
            return add_query_arg('lang', $code, $current);
        } elseif (WPML_LANGUAGE_NEGOTIATION_TYPE_DOMAIN === (int) $sitepress->get_setting('language_negotiation_type')) {
            global $sitepress;
            $lang = $sitepress->get_current_language();
            if ($code == $default_language) {
                return is_ssl() ? str_replace('https://' . $lang . '.', 'https://', $current) : str_replace('http://' . $lang . '.', 'http://', $current);
            } else {
                return is_ssl() ? str_replace('https://' . $lang, 'https://' . $code, $current) : str_replace('http://' . $lang, 'http://' . $code, $current);
            }
        }
    }


    return $current;
}

function iwj_force_wpml_url($current, $code = null) {
    global $sitepress;

    if ($sitepress) {
        if ($code === null) {
            $code = ICL_LANGUAGE_CODE;
        }
        $default_language = $sitepress->get_default_language();
        if (WPML_LANGUAGE_NEGOTIATION_TYPE_DIRECTORY === (int) $sitepress->get_setting('language_negotiation_type')) {
            $home_url = get_home_url('/');
            $home_actual_url = iwj_get_actual_wpml_url($home_url);
            if ($default_language == $code) {
                return str_replace($home_url, $home_actual_url, $current);
            } else {

                $home_actual_url = rtrim($home_actual_url, "/");
                $home_replace = $home_actual_url . '/' . $code;
                return str_replace($home_actual_url, $home_replace, $current);
            }
        } elseif (WPML_LANGUAGE_NEGOTIATION_TYPE_PARAMETER === (int) $sitepress->get_setting('language_negotiation_type')) {
            return add_query_arg('lang', $code, $current);
        } elseif (WPML_LANGUAGE_NEGOTIATION_TYPE_DOMAIN === (int) $sitepress->get_setting('language_negotiation_type')) {
            global $sitepress;
            $lang = $sitepress->get_current_language();
            if ($code == $default_language) {
                return is_ssl() ? str_replace('https://' . $lang . '.', 'https://', $current) : str_replace('http://' . $lang . '.', 'http://', $current);
            } else {
                return is_ssl() ? str_replace('https://' . $lang, 'https://' . $code, $current) : str_replace('http://' . $lang, 'http://' . $code, $current);
            }
        }
    }

    return $current;
}

function iwj_switch_language($language = null) {
    if ($language === null) {
        $language = isset($_POST['lang']) ? $_POST['lang'] : '';
    }

    if ($language) {
        global $sitepress;
        if ($sitepress) {
            $sitepress->switch_lang($language, false);
        }
    }
}

function iwj_insert_term_translate($term_id, $lang_code, $key, $translate) {
    global $wpdb;
    if ($wpdb->insert(
                    $wpdb->prefix . 'iwj_term_translates', array(
                'term_id' => $term_id,
                'lang_code' => $lang_code,
                'translate_key' => $key,
                'translate_string' => $translate
                    ), array(
                '%d',
                '%s',
                '%s',
                '%s'
                    )
            )) {
        return $wpdb->insert_id;
    }

    return null;
}

function iwj_update_term_translate($term_id, $lang_code, $key, $translate) {
    global $wpdb;
    if ($term_translate = iwj_get_term_translate($term_id, $key, $lang_code)) {
        $wpdb->update(
                $wpdb->prefix . 'iwj_term_translates', array(
            'translate_string' => $translate
                ), array(
            'term_id' => $term_id,
            'lang_code' => $lang_code,
            'translate_key' => $key,
                ), array(
            '%s'
                ), array(
            '%d',
            '%s',
            '%s',
                )
        );

        return $term_translate->ID;
    } else {
        return iwj_insert_term_translate($term_id, $lang_code, $key, $translate);
    }
}

function iwj_get_term_translate($term_id, $key, $lang_code) {
    static $term_translates = array();
    $return = array();
    $lang_codes = (array) $lang_code;
    global $wpdb;
    foreach ($lang_codes as $_lang_code) {
        $_key = $term_id . '_' . $key . $_lang_code;
        if (!isset($term_translates[$_key])) {
            $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}iwj_term_translates WHERE term_id = %d AND translate_key = %s AND lang_code = %s", $term_id, $key, $lang_code));
            if ($results) {
                $term_translates[$_key] = $results[0];
            } else {
                $term_translates[$_key] = null;
            }
        }

        $return[$_lang_code] = $term_translates[$_key];
    }

    if (is_array($lang_code)) {
        return $return;
    } else {
        return array_shift($return);
    }
}

function iwj_insert_post_translate($post_id, $lang_code, $key, $translate) {
    global $wpdb;
    if ($wpdb->insert(
                    $wpdb->prefix . 'iwj_post_translates', array(
                'post_id' => $post_id,
                'lang_code' => $lang_code,
                'translate_key' => $key,
                'translate_string' => $translate
                    ), array(
                '%d',
                '%s',
                '%s',
                '%s'
                    )
            )) {
        return $wpdb->insert_id;
    }

    return null;
}

function iwj_update_post_translate($post_id, $lang_code, $key, $translate) {
    global $wpdb;
    if ($post_translate = iwj_get_post_translate($post_id, $key, $lang_code)) {
        $wpdb->update(
                $wpdb->prefix . 'iwj_post_translates', array(
            'translate_string' => $translate
                ), array(
            'post_id' => $post_id,
            'lang_code' => $lang_code,
            'translate_key' => $key,
                ), array(
            '%s'
                ), array(
            '%d',
            '%s',
            '%s',
                )
        );

        return $post_translate->ID;
    } else {
        return iwj_insert_post_translate($post_id, $lang_code, $key, $translate);
    }
}

function iwj_get_post_translate($post_id, $key, $lang_code) {
    static $term_translates = array();
    $return = array();
    $lang_codes = (array) $lang_code;
    global $wpdb;
    foreach ($lang_codes as $_lang_code) {
        $_key = $post_id . '_' . $key . $_lang_code;
        if (!isset($term_translates[$_key])) {
            $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}iwj_post_translates WHERE post_id = %d AND translate_key = %s AND lang_code = %s", $post_id, $key, $lang_code));
            if ($results) {
                $term_translates[$_key] = $results[0];
            } else {
                $term_translates[$_key] = null;
            }
        }

        $return[$_lang_code] = $term_translates[$_key];
    }

    if (is_array($lang_code)) {
        return $return;
    } else {
        return array_shift($return);
    }
}

function iwj_get_languages_flag_html() {
    $html = '';
    if (defined('ICL_LANGUAGE_CODE')) {
        $languages = wpml_get_active_languages_filter('');
        $count_languages = count($languages);
        $html .= '<div class="iwj-switch-language">';
        $html .= '<div class="content-wrap">';
        $html .= '<div class="language-item-current language-item">';
        if (iwj_option('show_languages_flag')) {
            $html .= '<img src="' . esc_url(plugins_url() . '/sitepress-multilingual-cms/res/flags/' . ICL_LANGUAGE_CODE . '.png') . '">';
        }
        $html .= '<span>' . ICL_LANGUAGE_NAME . '</span></div>';
        if ($languages && ($count_languages > 1)) {
            $html .= '<div class="language-items">';
            $html .= '<ul>';
            foreach ($languages as $language) {
                $url = $language['url'];
                if ($language['code'] != ICL_LANGUAGE_CODE) {
                    $html .= '<li class="language-item"><a href="' . esc_url($url) . '" data-lang="' . $language['code'] . '"><img src="' . esc_url($language['country_flag_url']) . '"><span>' . $language['translated_name'] . '</span></a></li>';
                }
            }
            $html .= '</ul>';
            $html .= '</div>';
        } else {
            // get current url with query string.
            $current_url = iwj_get_current_url();
            $languages = iwj_get_wpml_languages();
            $count_languages = count($languages);
            if ($languages && ($count_languages > 1)) {
                $html .= '<div class="language-items">';
                $html .= '<ul>';
                foreach ($languages as $language) {
                    $url = iwj_get_wpml_url($current_url, $language['code']);
                    $icon = $language['country_flag_url'];
                    if ($language['code'] != ICL_LANGUAGE_CODE) {
                        $html .= '<li class="language-item"><a href="' . $url . '" data-lang="' . $language['code'] . '">';
                        if (iwj_option('show_languages_flag')) {
                            $html .= '<img src="' . $icon . '">';
                        }
                        $html .= '<span>' . wpml_translated_language_name_filter('', $language['code']) . '</span></a></li>';
                    }
                }
                $html .= '</ul>';
                $html .= '</div>';
            }
        }
        $html .= '</div>';
        $html .= '</div>';
    }

    return $html;
}

function iwj_get_current_url() {
    $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    return $actual_link;
    /* global $wp;
      return add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request.'/' ) ); */
}
