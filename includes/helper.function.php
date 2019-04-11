<?php

/**
 * Created by PhpStorm.
 * User: ADMIN
 * Date: 20/03/2017
 * Time: 8:40 SA
 */
function iwj_atts($pairs, $atts) {
    $atts = (array) $atts;
    $out = array();
    foreach ($pairs as $name => $default) {
        if (array_key_exists($name, $atts))
            $out[$name] = $atts[$name];
        else
            $out[$name] = $default;
    }

    return $out;
}

/**
 * Get option value
 *
 * @param string $key
 * @param string $default
 * @return mixed|string
 */
function iwj_option($key = '', $default = '', $translate = true) {
    $options = get_option('iwj_settings');

    if ($key) {
        if (!isset($options[$key]) || $options[$key] === '') {
            return $default;
        } else {
            return $options[$key];
        }
    } else {
        return $options;
    }
}

function iwj_genders() {
    $gender = array(
        'Male' => esc_html__('Male', 'iwjob'),
        'Female' => esc_html__('Female', 'iwjob'),
        'Other' => esc_html__('Other', 'iwjob'),
    );
    return apply_filters('iwj_gender_values', $gender);
}

function iwj_gender_titles($gender = '') {
    if (!$gender) {
        return array_values(iwj_genders());
    }

    if (is_array($gender)) {
        $titles = array();
        $gender_arr = iwj_genders();
        foreach ($gender as $_gender) {
            if (isset($gender_arr[$_gender])) {
                $titles[$_gender] = $gender_arr[$_gender];
            } else {
                $titles[$_gender] = $_gender;
            }
        }

        return $titles;
    } else {
        $gender_arr = iwj_genders();
        if (isset($gender_arr[$gender])) {
            return $gender_arr[$gender];
        } else {
            return $gender;
        }
    }
}

function iwj_languages() {
    $languages = array(
        'Afrikanns' => esc_html__('Afrikanns', 'iwjob'),
        'Albanian' => esc_html__('Albanian', 'iwjob'),
        'Arabic' => esc_html__('Arabic', 'iwjob'),
        'Armenian' => esc_html__('Armenian', 'iwjob'),
        'Basque' => esc_html__('Basque', 'iwjob'),
        'Bengali' => esc_html__('Bengali', 'iwjob'),
        'Bulgarian' => esc_html__('Bulgarian', 'iwjob'),
        'Catalan' => esc_html__('Catalan', 'iwjob'),
        'Cambodian' => esc_html__('Cambodian', 'iwjob'),
        'Chinese (Mandarin)' => esc_html__('Chinese (Mandarin)', 'iwjob'),
        'Croation' => esc_html__('Croation', 'iwjob'),
        'Czech' => esc_html__('Czech', 'iwjob'),
        'Danish' => esc_html__('Danish', 'iwjob'),
        'Dutch' => esc_html__('Dutch', 'iwjob'),
        'English' => esc_html__('English', 'iwjob'),
        'Estonian' => esc_html__('Estonian', 'iwjob'),
        'Fiji' => esc_html__('Fiji', 'iwjob'),
        'Finnish' => esc_html__('Finnish', 'iwjob'),
        'French' => esc_html__('French', 'iwjob'),
        'Georgian' => esc_html__('Georgian', 'iwjob'),
        'German' => esc_html__('German', 'iwjob'),
        'Greek' => esc_html__('Greek', 'iwjob'),
        'Gujarati' => esc_html__('Gujarati', 'iwjob'),
        'Hebrew' => esc_html__('Hebrew', 'iwjob'),
        'Hindi' => esc_html__('Hindi', 'iwjob'),
        'Hungarian' => esc_html__('Hungarian', 'iwjob'),
        'Icelandic' => esc_html__('Icelandic', 'iwjob'),
        'Indonesian' => esc_html__('Indonesian', 'iwjob'),
        'Irish' => esc_html__('Irish', 'iwjob'),
        'Italian' => esc_html__('Italian', 'iwjob'),
        'Japanese' => esc_html__('Japanese', 'iwjob'),
        'Javanese' => esc_html__('Javanese', 'iwjob'),
        'Korean' => esc_html__('Korean', 'iwjob'),
        'Latin' => esc_html__('Latin', 'iwjob'),
        'Latvian' => esc_html__('Latvian', 'iwjob'),
        'Lithuanian' => esc_html__('Lithuanian', 'iwjob'),
        'Macedonian' => esc_html__('Macedonian', 'iwjob'),
        'Malay' => esc_html__('Malay', 'iwjob'),
        'Malayalam' => esc_html__('Malayalam', 'iwjob'),
        'Maltese' => esc_html__('Maltese', 'iwjob'),
        'Maori' => esc_html__('Maori', 'iwjob'),
        'Marathi' => esc_html__('Marathi', 'iwjob'),
        'Mongolian' => esc_html__('Mongolian', 'iwjob'),
        'Nepali' => esc_html__('Nepali', 'iwjob'),
        'Norwegian' => esc_html__('Norwegian', 'iwjob'),
        'Persian' => esc_html__('Persian', 'iwjob'),
        'Polish' => esc_html__('Polish', 'iwjob'),
        'Portuguese' => esc_html__('Portuguese', 'iwjob'),
        'Punjabi' => esc_html__('Punjabi', 'iwjob'),
        'Quechua' => esc_html__('Quechua', 'iwjob'),
        'Romanian' => esc_html__('Romanian', 'iwjob'),
        'Russian' => esc_html__('Russian', 'iwjob'),
        'Samoan' => esc_html__('Samoan', 'iwjob'),
        'Serbian' => esc_html__('Serbian', 'iwjob'),
        'Slovak' => esc_html__('Slovak', 'iwjob'),
        'Slovenian' => esc_html__('Slovenian', 'iwjob'),
        'Spanish' => esc_html__('Spanish', 'iwjob'),
        'Swahili' => esc_html__('Swahili', 'iwjob'),
        'Swedish ' => esc_html__('Swedish ', 'iwjob'),
        'Tamil' => esc_html__('Tamil', 'iwjob'),
        'Tatar' => esc_html__('Tatar', 'iwjob'),
        'Telugu' => esc_html__('Telugu', 'iwjob'),
        'Thai' => esc_html__('Thai', 'iwjob'),
        'Tibetan' => esc_html__('Tibetan', 'iwjob'),
        'Tonga' => esc_html__('Tonga', 'iwjob'),
        'Turkish' => esc_html__('Turkish', 'iwjob'),
        'Ukranian' => esc_html__('Ukranian', 'iwjob'),
        'Urdu' => esc_html__('Urdu', 'iwjob'),
        'Uzbek' => esc_html__('Uzbek', 'iwjob'),
        'Vietnamese' => esc_html__('Vietnamese', 'iwjob'),
        'Welsh' => esc_html__('Welsh', 'iwjob'),
        'Xhosa' => esc_html__('Xhosa', 'iwjob'),
    );
    return apply_filters('iwj_languages', $languages);
}

function iwj_get_language_titles($language = '') {
    if (!$language) {
        return array_values(iwj_languages());
    }

    if (is_array($language)) {
        $titles = array();
        $language_arr = iwj_languages();
        foreach ($language as $_language) {
            if (isset($language_arr[$_language])) {
                $titles[$_language] = $language_arr[$_language];
            } else {
                $titles[$_language] = $_language;
            }
        }

        return $titles;
    } else {
        $language_arr = iwj_languages();
        if (isset($language_arr[$language])) {
            return $language_arr[$language];
        } else {
            return $language;
        }
    }
}

function iwj_get_available_languages() {
    $languages = iwj_option('allow_languages');
    if ($languages) {
        $chosen_languages = array();
        foreach ($languages as $language) {
            $chosen_languages[$language] = iwj_get_language_titles($language);
        }
        return $chosen_languages;
    }

    return iwj_languages();
}

function iwj_countries_list() {
    $Countries = array(
        'US' => esc_html__('United States', 'iwjob'),
        'CA' => esc_html__('Canada', 'iwjob'),
        'AU' => esc_html__('Australia', 'iwjob'),
        'FR' => esc_html__('France', 'iwjob'),
        'DE' => esc_html__('Germany', 'iwjob'),
        'IS' => esc_html__('Iceland', 'iwjob'),
        'IE' => esc_html__('Ireland', 'iwjob'),
        'IT' => esc_html__('Italy', 'iwjob'),
        'ES' => esc_html__('Spain', 'iwjob'),
        'SE' => esc_html__('Sweden', 'iwjob'),
        'AT' => esc_html__('Austria', 'iwjob'),
        'BE' => esc_html__('Belgium', 'iwjob'),
        'FI' => esc_html__('Finland', 'iwjob'),
        'CZ' => esc_html__('Czech Republic', 'iwjob'),
        'DK' => esc_html__('Denmark', 'iwjob'),
        'NO' => esc_html__('Norway', 'iwjob'),
        'GB' => esc_html__('United Kingdom', 'iwjob'),
        'CH' => esc_html__('Switzerland', 'iwjob'),
        'NZ' => esc_html__('New Zealand', 'iwjob'),
        'RU' => esc_html__('Russian Federation', 'iwjob'),
        'PT' => esc_html__('Portugal', 'iwjob'),
        'NL' => esc_html__('Netherlands', 'iwjob'),
        'IM' => esc_html__('Isle of Man', 'iwjob'),
        'AF' => esc_html__('Afghanistan', 'iwjob'),
        'AX' => esc_html__('Aland Islands ', 'iwjob'),
        'AL' => esc_html__('Albania', 'iwjob'),
        'DZ' => esc_html__('Algeria', 'iwjob'),
        'AS' => esc_html__('American Samoa', 'iwjob'),
        'AD' => esc_html__('Andorra', 'iwjob'),
        'AO' => esc_html__('Angola', 'iwjob'),
        'AI' => esc_html__('Anguilla', 'iwjob'),
        'AQ' => esc_html__('Antarctica', 'iwjob'),
        'AG' => esc_html__('Antigua and Barbuda', 'iwjob'),
        'AR' => esc_html__('Argentina', 'iwjob'),
        'AM' => esc_html__('Armenia', 'iwjob'),
        'AW' => esc_html__('Aruba', 'iwjob'),
        'AZ' => esc_html__('Azerbaijan', 'iwjob'),
        'BS' => esc_html__('Bahamas', 'iwjob'),
        'BH' => esc_html__('Bahrain', 'iwjob'),
        'BD' => esc_html__('Bangladesh', 'iwjob'),
        'BB' => esc_html__('Barbados', 'iwjob'),
        'BY' => esc_html__('Belarus', 'iwjob'),
        'BZ' => esc_html__('Belize', 'iwjob'),
        'BJ' => esc_html__('Benin', 'iwjob'),
        'BM' => esc_html__('Bermuda', 'iwjob'),
        'BT' => esc_html__('Bhutan', 'iwjob'),
        'BO' => esc_html__('Bolivia, Plurinational State of', 'iwjob'),
        'BQ' => esc_html__('Bonaire, Sint Eustatius and Saba', 'iwjob'),
        'BA' => esc_html__('Bosnia and Herzegovina', 'iwjob'),
        'BW' => esc_html__('Botswana', 'iwjob'),
        'BV' => esc_html__('Bouvet Island', 'iwjob'),
        'BR' => esc_html__('Brazil', 'iwjob'),
        'IO' => esc_html__('British Indian Ocean Territory', 'iwjob'),
        'BN' => esc_html__('Brunei Darussalam', 'iwjob'),
        'BG' => esc_html__('Bulgaria', 'iwjob'),
        'BF' => esc_html__('Burkina Faso', 'iwjob'),
        'BI' => esc_html__('Burundi', 'iwjob'),
        'KH' => esc_html__('Cambodia', 'iwjob'),
        'CM' => esc_html__('Cameroon', 'iwjob'),
        'CV' => esc_html__('Cape Verde', 'iwjob'),
        'KY' => esc_html__('Cayman Islands', 'iwjob'),
        'CF' => esc_html__('Central African Republic', 'iwjob'),
        'TD' => esc_html__('Chad', 'iwjob'),
        'CL' => esc_html__('Chile', 'iwjob'),
        'CN' => esc_html__('China', 'iwjob'),
        'CX' => esc_html__('Christmas Island', 'iwjob'),
        'CC' => esc_html__('Cocos (Keeling) Islands', 'iwjob'),
        'CO' => esc_html__('Colombia', 'iwjob'),
        'KM' => esc_html__('Comoros', 'iwjob'),
        'CG' => esc_html__('Congo', 'iwjob'),
        'CD' => esc_html__('Congo, the Democratic Republic of the', 'iwjob'),
        'CK' => esc_html__('Cook Islands', 'iwjob'),
        'CR' => esc_html__('Costa Rica', 'iwjob'),
        'CI' => esc_html__('Cote d\'Ivoire', 'iwjob'),
        'HR' => esc_html__('Croatia', 'iwjob'),
        'CU' => esc_html__('Cuba', 'iwjob'),
        'CW' => esc_html__('Curaçao', 'iwjob'),
        'CY' => esc_html__('Cyprus', 'iwjob'),
        'DJ' => esc_html__('Djibouti', 'iwjob'),
        'DM' => esc_html__('Dominica', 'iwjob'),
        'DO' => esc_html__('Dominican Republic', 'iwjob'),
        'EC' => esc_html__('Ecuador', 'iwjob'),
        'EG' => esc_html__('Egypt', 'iwjob'),
        'SV' => esc_html__('El Salvador', 'iwjob'),
        'GQ' => esc_html__('Equatorial Guinea', 'iwjob'),
        'ER' => esc_html__('Eritrea', 'iwjob'),
        'EE' => esc_html__('Estonia', 'iwjob'),
        'ET' => esc_html__('Ethiopia', 'iwjob'),
        'FK' => esc_html__('Falkland Islands (Malvinas)', 'iwjob'),
        'FO' => esc_html__('Faroe Islands', 'iwjob'),
        'FJ' => esc_html__('Fiji', 'iwjob'),
        'GF' => esc_html__('French Guiana', 'iwjob'),
        'PF' => esc_html__('French Polynesia', 'iwjob'),
        'TF' => esc_html__('French Southern Territories', 'iwjob'),
        'GA' => esc_html__('Gabon', 'iwjob'),
        'GM' => esc_html__('Gambia', 'iwjob'),
        'GE' => esc_html__('Georgia', 'iwjob'),
        'GH' => esc_html__('Ghana', 'iwjob'),
        'GI' => esc_html__('Gibraltar', 'iwjob'),
        'GR' => esc_html__('Greece', 'iwjob'),
        'GL' => esc_html__('Greenland', 'iwjob'),
        'GD' => esc_html__('Grenada', 'iwjob'),
        'GP' => esc_html__('Guadeloupe', 'iwjob'),
        'GU' => esc_html__('Guam', 'iwjob'),
        'GT' => esc_html__('Guatemala', 'iwjob'),
        'GG' => esc_html__('Guernsey', 'iwjob'),
        'GN' => esc_html__('Guinea', 'iwjob'),
        'GW' => esc_html__('Guinea-Bissau', 'iwjob'),
        'GY' => esc_html__('Guyana', 'iwjob'),
        'HT' => esc_html__('Haiti', 'iwjob'),
        'HM' => esc_html__('Heard Island and McDonald Islands', 'iwjob'),
        'VA' => esc_html__('Holy See (Vatican City State)', 'iwjob'),
        'HN' => esc_html__('Honduras', 'iwjob'),
        'HK' => esc_html__('Hong Kong', 'iwjob'),
        'HU' => esc_html__('Hungary', 'iwjob'),
        'IN' => esc_html__('India', 'iwjob'),
        'ID' => esc_html__('Indonesia', 'iwjob'),
        'IR' => esc_html__('Iran, Islamic Republic of', 'iwjob'),
        'IQ' => esc_html__('Iraq', 'iwjob'),
        'IL' => esc_html__('Israel', 'iwjob'),
        'JM' => esc_html__('Jamaica', 'iwjob'),
        'JP' => esc_html__('Japan', 'iwjob'),
        'JE' => esc_html__('Jersey', 'iwjob'),
        'JO' => esc_html__('Jordan', 'iwjob'),
        'KZ' => esc_html__('Kazakhstan', 'iwjob'),
        'KE' => esc_html__('Kenya', 'iwjob'),
        'KI' => esc_html__('Kiribati', 'iwjob'),
        'KP' => esc_html__('Korea, Democratic People\'s Republic of', 'iwjob'),
        'KR' => esc_html__('Korea, Republic of', 'iwjob'),
        'KV' => esc_html__('kosovo', 'iwjob'),
        'KW' => esc_html__('Kuwait', 'iwjob'),
        'KG' => esc_html__('Kyrgyzstan', 'iwjob'),
        'LA' => esc_html__('Lao People\'s Democratic Republic', 'iwjob'),
        'LV' => esc_html__('Latvia', 'iwjob'),
        'LB' => esc_html__('Lebanon', 'iwjob'),
        'LS' => esc_html__('Lesotho', 'iwjob'),
        'LR' => esc_html__('Liberia', 'iwjob'),
        'LY' => esc_html__('Libyan Arab Jamahiriya', 'iwjob'),
        'LI' => esc_html__('Liechtenstein', 'iwjob'),
        'LT' => esc_html__('Lithuania', 'iwjob'),
        'LU' => esc_html__('Luxembourg', 'iwjob'),
        'MO' => esc_html__('Macao', 'iwjob'),
        'MK' => esc_html__('Macedonia', 'iwjob'),
        'MG' => esc_html__('Madagascar', 'iwjob'),
        'MW' => esc_html__('Malawi', 'iwjob'),
        'MY' => esc_html__('Malaysia', 'iwjob'),
        'MV' => esc_html__('Maldives', 'iwjob'),
        'ML' => esc_html__('Mali', 'iwjob'),
        'MT' => esc_html__('Malta', 'iwjob'),
        'MH' => esc_html__('Marshall Islands', 'iwjob'),
        'MQ' => esc_html__('Martinique', 'iwjob'),
        'MR' => esc_html__('Mauritania', 'iwjob'),
        'MU' => esc_html__('Mauritius', 'iwjob'),
        'YT' => esc_html__('Mayotte', 'iwjob'),
        'MX' => esc_html__('Mexico', 'iwjob'),
        'FM' => esc_html__('Micronesia, Federated States of', 'iwjob'),
        'MD' => esc_html__('Moldova, Republic of', 'iwjob'),
        'MC' => esc_html__('Monaco', 'iwjob'),
        'MN' => esc_html__('Mongolia', 'iwjob'),
        'ME' => esc_html__('Montenegro', 'iwjob'),
        'MS' => esc_html__('Montserrat', 'iwjob'),
        'MA' => esc_html__('Morocco', 'iwjob'),
        'MZ' => esc_html__('Mozambique', 'iwjob'),
        'MM' => esc_html__('Myanmar', 'iwjob'),
        'NA' => esc_html__('Namibia', 'iwjob'),
        'NR' => esc_html__('Nauru', 'iwjob'),
        'NP' => esc_html__('Nepal', 'iwjob'),
        'NC' => esc_html__('New Caledonia', 'iwjob'),
        'NI' => esc_html__('Nicaragua', 'iwjob'),
        'NE' => esc_html__('Niger', 'iwjob'),
        'NG' => esc_html__('Nigeria', 'iwjob'),
        'NU' => esc_html__('Niue', 'iwjob'),
        'NF' => esc_html__('Norfolk Island', 'iwjob'),
        'MP' => esc_html__('Northern Mariana Islands', 'iwjob'),
        'OM' => esc_html__('Oman', 'iwjob'),
        'PK' => esc_html__('Pakistan', 'iwjob'),
        'PW' => esc_html__('Palau', 'iwjob'),
        'PS' => esc_html__('Palestinian Territory, Occupied', 'iwjob'),
        'PA' => esc_html__('Panama', 'iwjob'),
        'PG' => esc_html__('Papua New Guinea', 'iwjob'),
        'PY' => esc_html__('Paraguay', 'iwjob'),
        'PE' => esc_html__('Peru', 'iwjob'),
        'PH' => esc_html__('Philippines', 'iwjob'),
        'PN' => esc_html__('Pitcairn', 'iwjob'),
        'PL' => esc_html__('Poland', 'iwjob'),
        'PR' => esc_html__('Puerto Rico', 'iwjob'),
        'QA' => esc_html__('Qatar', 'iwjob'),
        'RE' => esc_html__('Reunion', 'iwjob'),
        'RO' => esc_html__('Romania', 'iwjob'),
        'RW' => esc_html__('Rwanda', 'iwjob'),
        'BL' => esc_html__('Saint Barthélemy', 'iwjob'),
        'SH' => esc_html__('Saint Helena', 'iwjob'),
        'KN' => esc_html__('Saint Kitts and Nevis', 'iwjob'),
        'LC' => esc_html__('Saint Lucia', 'iwjob'),
        'MF' => esc_html__('Saint Martin (French part)', 'iwjob'),
        'PM' => esc_html__('Saint Pierre and Miquelon', 'iwjob'),
        'VC' => esc_html__('Saint Vincent and the Grenadines', 'iwjob'),
        'WS' => esc_html__('Samoa', 'iwjob'),
        'SM' => esc_html__('San Marino', 'iwjob'),
        'ST' => esc_html__('Sao Tome and Principe', 'iwjob'),
        'SA' => esc_html__('Saudi Arabia', 'iwjob'),
        'SN' => esc_html__('Senegal', 'iwjob'),
        'RS' => esc_html__('Serbia', 'iwjob'),
        'SC' => esc_html__('Seychelles', 'iwjob'),
        'SL' => esc_html__('Sierra Leone', 'iwjob'),
        'SG' => esc_html__('Singapore', 'iwjob'),
        'SX' => esc_html__('Sint Maarten (Dutch part)', 'iwjob'),
        'SK' => esc_html__('Slovakia', 'iwjob'),
        'SI' => esc_html__('Slovenia', 'iwjob'),
        'SB' => esc_html__('Solomon Islands', 'iwjob'),
        'SO' => esc_html__('Somalia', 'iwjob'),
        'ZA' => esc_html__('South Africa', 'iwjob'),
        'GS' => esc_html__('South Georgia and the South Sandwich Islands', 'iwjob'),
        'LK' => esc_html__('Sri Lanka', 'iwjob'),
        'SD' => esc_html__('Sudan', 'iwjob'),
        'SR' => esc_html__('Suriname', 'iwjob'),
        'SJ' => esc_html__('Svalbard and Jan Mayen', 'iwjob'),
        'SZ' => esc_html__('Swaziland', 'iwjob'),
        'SY' => esc_html__('Syrian Arab Republic', 'iwjob'),
        'TW' => esc_html__('Taiwan, Province of China', 'iwjob'),
        'TJ' => esc_html__('Tajikistan', 'iwjob'),
        'TZ' => esc_html__('Tanzania, United Republic of', 'iwjob'),
        'TH' => esc_html__('Thailand', 'iwjob'),
        'TL' => esc_html__('Timor-Leste', 'iwjob'),
        'TG' => esc_html__('Togo', 'iwjob'),
        'TK' => esc_html__('Tokelau', 'iwjob'),
        'TO' => esc_html__('Tonga', 'iwjob'),
        'TT' => esc_html__('Trinidad and Tobago', 'iwjob'),
        'TN' => esc_html__('Tunisia', 'iwjob'),
        'TR' => esc_html__('Turkey', 'iwjob'),
        'TM' => esc_html__('Turkmenistan', 'iwjob'),
        'TC' => esc_html__('Turks and Caicos Islands', 'iwjob'),
        'TV' => esc_html__('Tuvalu', 'iwjob'),
        'UG' => esc_html__('Uganda', 'iwjob'),
        'UA' => esc_html__('Ukraine', 'iwjob'),
        'AE' => esc_html__('United Arab Emirates', 'iwjob'),
        'UM' => esc_html__('United States Minor Outlying Islands', 'iwjob'),
        'UY' => esc_html__('Uruguay', 'iwjob'),
        'UZ' => esc_html__('Uzbekistan', 'iwjob'),
        'VU' => esc_html__('Vanuatu', 'iwjob'),
        'VE' => esc_html__('Venezuela, Bolivarian Republic of', 'iwjob'),
        'VN' => esc_html__('Viet Nam', 'iwjob'),
        'VG' => esc_html__('Virgin Islands, British', 'iwjob'),
        'VI' => esc_html__('Virgin Islands, U.S.', 'iwjob'),
        'WF' => esc_html__('Wallis and Futuna', 'iwjob'),
        'EH' => esc_html__('Western Sahara', 'iwjob'),
        'YE' => esc_html__('Yemen', 'iwjob'),
        'ZM' => esc_html__('Zambia', 'iwjob'),
        'ZW' => esc_html__('Zimbabwe', 'iwjob')
    );
    return $Countries;
}

function iwj_get_country_titles($country = '') {
    if (!$country) {
        return array_values(iwj_countries_list());
    }

    if (is_array($country)) {
        $titles = array();
        $countries_arr = iwj_countries_list();
        foreach ($country as $_country) {
            if (isset($countries_arr[$_country])) {
                $titles[$_country] = $countries_arr[$_country];
            } else {
                $titles[$_country] = $_country;
            }
        }

        return $titles;
    } else {
        $countries_arr = iwj_countries_list();
        if (isset($countries_arr[$country])) {
            return $countries_arr[$country];
        } else {
            return $country;
        }
    }
}

function iwj_get_country_keys($country = '') {
    if (!$country) {
        return array_keys(iwj_countries_list());
    }

    if (is_array($country)) {
        $keys = array();
        $countries_arr = iwj_countries_list();
        foreach ($country as $_key => $_country) {
            if (isset($countries_arr[$_key])) {
                $keys[$_key] = $countries_arr[$_key];
            } else {
                $keys[$_key] = $_key;
            }
        }

        return $keys;
    } else {
        $countries_arr = iwj_countries_list();
        if (isset($countries_arr[$country])) {
            return $countries_arr[$country];
        } else {
            return $country;
        }
    }
}

function iwj_get_template_part($slug, $_args = array()) {
    $template = '';

    // Look in yourtheme/slug-name.php and yourtheme/indirectory/slug-name.php
    if ($slug) {
        $template = locate_template(array("{$slug}.php", IWJ()->template_path() . "{$slug}.php"));
    }
    // Get default slug-name.php
    if (!$template && file_exists(IWJ()->plugin_path() . "/templates/{$slug}.php")) {
        $template = IWJ()->plugin_path() . "/templates/{$slug}.php";
    }

    // Allow 3rd party plugins to filter template file from their plugin.
    $template = apply_filters('iwj_get_template_part', $template, $slug, $_args);
    if ($template) {
        iwj_load_template($template, $_args, false);
    }
}

function iwj_load_template($_template_file, $_args = array(), $require_once = true) {
    if ($_args) {
        extract($_args, EXTR_SKIP);
    }

    if ($require_once) {
        require_once( $_template_file );
    } else {
        require( $_template_file );
    }
}

function iwj_get_page_id($page) {

    $page = iwj_option($page . '_page_id');
    $page = $page ? absint($page) : 0;

    if ($page && function_exists('wpml_object_id_filter')) {
        $page = wpml_object_id_filter($page, 'page', true);
    }

    return $page;
}

function iwj_get_page_permalink($page) {
    $page_id = iwj_get_page_id($page);
    $permalink = $page_id ? get_permalink($page_id) : home_url('/');
    return $permalink;
}

function iwj_get_alert($message, $type, $dismissable = false) {
    ob_start();
    iwj_get_template_part('parts/alert', array(
        'type' => $type,
        'dismissable' => $dismissable,
        'message' => $message
    ));
    $content = ob_get_clean();

    return $content;
}

/**
 * Get full list of currency codes.
 *
 * @return array
 */
function iwj_get_currencies() {
    $currencies = IWJ_Currency::getAllCurrencies();
    $currency_options = array();
    foreach ($currencies as $key => $currency) {
        $currency_options[$key] = $currency['title'];
    }

    return $currency_options;
}

function iwj_get_job_currencies() {
    $_allow_currencies = iwj_option('allow_currencies', array());
    $all_currencies = iwj_get_currencies();
    if (!$_allow_currencies) {
        return $all_currencies;
    } else {
        $allow_currencies = array();
        foreach ($_allow_currencies AS $allow_currency) {
            $allow_currencies[$allow_currency] = $all_currencies[$allow_currency];
        }
        return $allow_currencies;
    }
}

function iwj_get_desc_job() {
    $default_job_content = '<h4>Overview</h4>
            <blockquote>Lorem ipsum dolor sit amet consectetur adipiscing, elit vehicula semper velit vestibulum felis purus, gravida rhoncus vulputate aliquet cras. Conubia libero morbi tristique rutrum elementum dapibus per cras volutpat, semper consequat nisl aenean urna ultricies tincidunt etiam senectus. Rhoncus blandit neque vivamus nullam sodales maecenas felis faucibus, lectus suspendisse vitae donec hendrerit montes ultrices fames, penatibus est pulvinar sagittis proin phareultrices fringilla.</blockquote>

            <hr />

            <h4>What You Will Do</h4>
            <ol>
                <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>
                <li>Auctor class pellentesque augue dignissim venenatis, turpis vestibulum lacinia dignissim venenatis.</li>
                <li>Mus arcu euismod ad hac dui, vivamus platea netus.</li>
                <li>Neque per nisl posuere sagittis, id platea dui.</li>
                <li>A enim magnis dapibus, nullam odio porta, nisl class.</li>
                <li>Turpis leo pellentesque per nam, nostra fringilla id.</li>
            </ol>   

            <hr />

            <h4>What we can offer you</h4>
            <ul>
                <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>
                <li>Mus arcu euismod ad hac dui, vivamus platea netus.</li>
                <li>Neque per nisl posuere sagittis, id platea dui.</li>
                <li>A enim magnis dapibus, nullam odio porta, nisl class.</li>
                <li>Turpis leo pellentesque per nam, nostra fringilla id.</li>
            </ul>';
    $job_content = iwj_option('default_job_content');

    if (!$job_content) {
        $job_content = $default_job_content;
    }

    return $job_content;
}

function iwj_get_currency() {
    return iwj_option('currency', 'USD');
}

function iwj_price($amount, $currency = '') {
    if (!$currency) {
        $currency = iwj_get_currency();
    }
    return new IWJ_Price($amount, $currency);
}

function iwj_get_system_currency() {
    return iwj_option('system_currency', 'USD');
}

function iwj_system_price($amount, $currency = '', $free_is_0 = false) {
    if (!$currency) {
        $currency = iwj_get_system_currency();
    }

    if ($free_is_0 && (float) $amount == 0) {
        return __('free', 'iwjob');
    }
    return new IWJ_Price($amount, $currency);
}

function iwj_get_tax_price($price) {
    $tax_price = false;
    if (iwj_option('tax_used')) {
        $tax_value = iwj_option('tax_value');
        if ($tax_value < 0) {
            $tax_value = 0;
        }
        $tax_price = $price / 100 * $tax_value;
    }

    return $tax_price;
}

function iw_get_featured_job_expirty() {
    $expiry = iwj_option('featured_job_expiry');
    $unit = iwj_option('featured_job_expiry_unit');
    $seconds = '';
    switch ($unit) {
        case 'day':
            $seconds = $expiry * 60 * 60 * 24 + current_time('timestamp');
            break;
        case 'week':
            $seconds = $expiry * 60 * 60 * 24 * 7 + current_time('timestamp');
            break;
        case 'month':
            $seconds = strtotime('+' . $expiry . ' month', current_time('timestamp'));
            break;
        case 'year':
            $seconds = strtotime('+' . $expiry . ' year', current_time('timestamp'));
            break;
    }

    return $seconds;
}

function iw_get_job_expirty() {
    $expiry = iwj_option('job_expiry');
    $unit = iwj_option('job_expiry_unit');
    $seconds = 0;
    switch ($unit) {
        case 'day':
            $seconds = $expiry * 60 * 60 * 24 + current_time('timestamp');
            break;
        case 'week':
            $seconds = $expiry * 60 * 60 * 24 * 7 + current_time('timestamp');
            break;
        case 'month':
            $seconds = strtotime('+' . $expiry . ' month', current_time('timestamp'));
            break;
        case 'year':
            $seconds = strtotime('+' . $expiry . ' year', current_time('timestamp'));
            break;
    }

    return $seconds;
}

if (!function_exists('iwj_resize_image')) {

    function iwj_resize_image($url, $width, $height = null, $crop = null, $single = true) {
        //validate inputs
        if (!$url OR ! $width)
            return false;

        //define upload path & dir
        $upload_info = wp_upload_dir();
        $upload_dir = $upload_info['basedir'];
        $upload_url = $upload_info['baseurl'];
        //check if $img_url is local
        if (strpos($url, $upload_url) === false) {
            //define path of image
            $rel_path = str_replace(content_url(), '', $url);
            $img_path = WP_CONTENT_DIR . $rel_path;
        } else {
            $rel_path = str_replace($upload_url, '', $url);
            $img_path = $upload_dir . $rel_path;
        }

        //check if img path exists, and is an image indeed
        if (!file_exists($img_path) OR ! @getimagesize($img_path))
            return $url;

        //get image info
        $info = pathinfo($img_path);
        $ext = $info['extension'];
        list($orig_w, $orig_h) = @getimagesize($img_path);

        //get image size after cropping
        $dims = image_resize_dimensions($orig_w, $orig_h, $width, $height, $crop);
        $dst_w = $dims[4];
        $dst_h = $dims[5];

        //use this to check if cropped image already exists, so we can return that instead
        $suffix = "{$dst_w}x{$dst_h}";
        $dst_rel_url = str_replace('.' . $ext, '', $url);
        $destfilename = "{$img_path}-{$suffix}.{$ext}";
        if (!$dst_h) {
            //can't resize, so return original url
            $img_url = $url;
            $dst_w = $orig_w;
            $dst_h = $orig_h;
        } //else check if cache exists
        elseif (file_exists($destfilename) && @getimagesize($destfilename)) {
            $img_url = "{$dst_rel_url}-{$suffix}.{$ext}";
        } //else, we resize the image and return the new resized image url
        else {
            // Note: This pre-3.5 fallback check will edited out in subsequent version
            if (function_exists('wp_get_image_editor')) {

                $editor = wp_get_image_editor($img_path);

                if (is_wp_error($editor) || is_wp_error($editor->resize($width, $height, $crop)))
                    return false;

                $resized_file = $editor->save();

                if (!is_wp_error($resized_file)) {
                    $resized_rel_path = str_replace($upload_dir, '', $resized_file['path']);
                    $img_url = "{$dst_rel_url}-{$suffix}.{$ext}";
                } else {
                    return false;
                }
            }
        }

        //return the output
        if ($single) {
            //str return
            $image = $img_url;
        } else {
            //array return
            $image = array(
                0 => $img_url,
                1 => $dst_w,
                2 => $dst_h
            );
        }

        return $image;
    }

}

// determine the topmost parent of a term
function iwj_get_term_hierarchy($taxonomy, $parent = 0, $level = 0, $args1 = array()) {
    $terms = array();
    $default_args = array(
        'hide_empty' => false,
        'parent' => $parent
    );

    $args = wp_parse_args($args1, $default_args);

    $childterms = get_terms($taxonomy, $args);
    if ($childterms) {
        $level++;
        foreach ($childterms as $childterm) {
            $childterm->level = $level;
            $terms[] = $childterm;
            $newterms = iwj_get_term_hierarchy($taxonomy, $childterm->term_id, $level, $args1);
            if ($newterms) {
                $terms = array_merge($terms, $newterms);
            }
        }
    }

    return $terms;
}

function iwj_count_job_with_term($term_id, $taxonomy) {
    global $wpdb;

    $st = $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts p
		INNER JOIN $wpdb->term_relationships tr
		ON (p.ID = tr.object_id)
		INNER JOIN $wpdb->term_taxonomy tt
		ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
		WHERE
		p.post_type = 'iwj_job'
		AND p.post_status = 'publish'
		AND tt.taxonomy = %s
		AND tt.term_id = %d;", $taxonomy, $term_id);

    return $wpdb->get_var($st);
}

function iwj_get_skill_options() {
    $terms = get_terms(array(
        'taxonomy' => 'iwj_skill',
        'hide_empty' => false
    ));

    if ($terms) {
        $options = array();
        foreach ($terms as $term) {
            $options[] = $term->name;
        }

        return $options;
    }

    return array();
}

/**
 * Callback which can flatten post meta (gets the first value if it's an array).
 *
 * @param  array $value
 * @return mixed
 */
function iwj_flatten_meta_callback($value) {
    return is_array($value) ? current($value) : $value;
}

if (!function_exists('iwj_rgb_from_hex')) {

    /**
     * Hex darker/lighter/contrast functions for colors.
     *
     * @param mixed $color
     * @return string
     */
    function iwj_rgb_from_hex($color) {
        $color = str_replace('#', '', $color);
        // Convert shorthand colors to full format, e.g. "FFF" -> "FFFFFF"
        $color = preg_replace('~^(.)(.)(.)$~', '$1$1$2$2$3$3', $color);

        $rgb = array();
        $rgb['R'] = hexdec($color{0} . $color{1});
        $rgb['G'] = hexdec($color{2} . $color{3});
        $rgb['B'] = hexdec($color{4} . $color{5});

        return $rgb;
    }

}

if (!function_exists('iwj_hex_darker')) {

    /**
     * Hex darker/lighter/contrast functions for colors.
     *
     * @param mixed $color
     * @param int $factor (default: 30)
     * @return string
     */
    function iwj_hex_darker($color, $factor = 30) {
        $base = iwj_rgb_from_hex($color);
        $color = '#';

        foreach ($base as $k => $v) {
            $amount = $v / 100;
            $amount = round($amount * $factor);
            $new_decimal = $v - $amount;

            $new_hex_component = dechex($new_decimal);
            if (strlen($new_hex_component) < 2) {
                $new_hex_component = "0" . $new_hex_component;
            }
            $color .= $new_hex_component;
        }

        return $color;
    }

}

if (!function_exists('iwj_hex_lighter')) {

    /**
     * Hex darker/lighter/contrast functions for colors.
     *
     * @param mixed $color
     * @param int $factor (default: 30)
     * @return string
     */
    function iwj_hex_lighter($color, $factor = 30) {
        $base = iwj_rgb_from_hex($color);
        $color = '#';

        foreach ($base as $k => $v) {
            $amount = 255 - $v;
            $amount = $amount / 100;
            $amount = round($amount * $factor);
            $new_decimal = $v + $amount;

            $new_hex_component = dechex($new_decimal);
            if (strlen($new_hex_component) < 2) {
                $new_hex_component = "0" . $new_hex_component;
            }
            $color .= $new_hex_component;
        }

        return $color;
    }

}

if (!function_exists('iwj_light_or_dark')) {

    /**
     * Detect if we should use a light or dark color on a background color.
     *
     * @param mixed $color
     * @param string $dark (default: '#000000')
     * @param string $light (default: '#FFFFFF')
     * @return string
     */
    function iwj_light_or_dark($color, $dark = '#000000', $light = '#FFFFFF') {

        $hex = str_replace('#', '', $color);

        $c_r = hexdec(substr($hex, 0, 2));
        $c_g = hexdec(substr($hex, 2, 2));
        $c_b = hexdec(substr($hex, 4, 2));

        $brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

        return $brightness > 155 ? $dark : $light;
    }

}

if (!function_exists('iwj_format_hex')) {

    /**
     * Format string as hex.
     *
     * @param string $hex
     * @return string
     */
    function iwj_format_hex($hex) {

        $hex = trim(str_replace('#', '', $hex));

        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return $hex ? '#' . $hex : null;
    }

}

function iwj_order_list_jobs() {

    $order_list = array();
    $order_list[] = array('val' => '', 'name' => __('Default', 'iwjob'));
    $sorting_default = iwj_option('sorting_jobs_default');
    if ($sorting_default != 'date') {
        $order_list[] = array('val' => 'date', 'name' => __('New job', 'iwjob'));
    }
    if ($sorting_default && $sorting_default != 'featured') {
        $order_list[] = array('val' => 'featured', 'name' => __('Featured job', 'iwjob'));
    }
    if ($sorting_default != 'salary') {
        $order_list[] = array('val' => 'salary', 'name' => __('Salary', 'iwjob'));
    }
    if ($sorting_default != 'name') {
        $order_list[] = array('val' => 'name', 'name' => __('Job title', 'iwjob'));
    }

    $html = '';
    $current_order = '';

    if (isset($_GET['order'])) {
        $current_order = $_GET['order'];
    }

    foreach ($order_list as $order_item) {
        $selected = ($current_order == $order_item['val']) ? 'selected' : '';
        $html .= ' <option value="' . $order_item['val'] . '"  ' . $selected . ' >' . $order_item['name'] . '</option>';
    }

    return $html;
}

function iwj_order_list_candidates() {

    $order_list = array();
    $order_list[] = array('val' => '', 'name' => __('Default', 'iwjob'));
    $order_list[] = array('val' => 'name', 'name' => __('Name', 'iwjob'));
    $order_list[] = array('val' => 'date', 'name' => __('New candidate', 'iwjob'));

    $html = '';
    $current_order = '';

    if (isset($_GET['order'])) {
        $current_order = $_GET['order'];
    }

    foreach ($order_list as $order_item) {
        $selected = ($current_order == $order_item['val']) ? 'selected' : '';
        $html .= ' <option value="' . $order_item['val'] . '"  ' . $selected . ' >' . $order_item['name'] . '</option>';
    }


    return $html;
}

function iwj_order_list_employers() {

    $order_list = array();
    $order_list[] = array('val' => '', 'name' => __('Default', 'iwjob'));
    $order_list[] = array('val' => 'name', 'name' => __('Name', 'iwjob'));
    $order_list[] = array('val' => 'date', 'name' => __('New employer', 'iwjob'));

    $html = '';
    $current_order = '';

    if (isset($_GET['order'])) {
        $current_order = $_GET['order'];
    }

    foreach ($order_list as $order_item) {
        $selected = ($current_order == $order_item['val']) ? 'selected' : '';
        $html .= ' <option value="' . $order_item['val'] . '"  ' . $selected . ' >' . $order_item['name'] . '</option>';
    }


    return $html;
}

function iwj_clean($var) {
    if (is_array($var)) {
        return array_map('iwj_clean', $var);
    } else {
        return is_scalar($var) ? sanitize_text_field($var) : $var;
    }
}

function iwj_get_terms_by_type($types) {
    $types = (array) $types;
    global $wpdb;

    $query = "SELECT t.term_id, t.name, t.slug, tx.taxonomy, tx.parent";
    $query .= " FROM " . $wpdb->terms . " AS t";
    $query .= " JOIN " . $wpdb->term_taxonomy . " AS tx ON tx.term_id = t.term_id";
    if (is_array($types) && count($types)) {
        //$where = " AND tx.taxonomy IN ('iwj_cat', 'iwj_location', 'iwj_type', 'iwj_salary', 'iwj_skill', 'iwj_level')";
        $where = " tx.taxonomy IN ('" . implode("','", $types) . "')";
        $query .= " WHERE " . $where;
    }
    $query .= " GROUP BY t.term_id";
    $query .= " ORDER BY tx.taxonomy";
    $result = $wpdb->get_results($query, OBJECT_K);

    return $result;
}

function get_terms_has_jobs($terms = array()) {
    global $wpdb;

    $query = "SELECT t.term_id, t.name, t.slug, tx.taxonomy, tx.parent, COUNT(DISTINCT(p.ID)) AS total_post";
    $query .= " FROM " . $wpdb->terms . " AS t";
    $query .= " JOIN " . $wpdb->term_taxonomy . " AS tx ON tx.term_id = t.term_id";
    $query .= " JOIN " . $wpdb->term_relationships . " AS tr ON tx.term_taxonomy_id = tr.term_taxonomy_id";
    $query .= " JOIN " . $wpdb->posts . " AS p ON p.ID = tr.object_id";
    $where = " p.post_type = 'iwj_job' AND p.post_status = 'publish'";
    if (!iwj_option('show_expired_job')) {
        $query .= " JOIN " . $wpdb->postmeta . " AS pm ON p.ID = pm.post_id";
        $where .= " AND pm.meta_key = '" . IWJ_PREFIX . "expiry' AND (pm.meta_value = '' OR (pm.meta_value != '' AND CAST(pm.meta_value AS SIGNED) > " . current_time('timestamp') . "))";
    }
    if ($terms) {
        $where .= " AND tx.taxonomy IN ('iwj_cat', 'iwj_location', 'iwj_type', 'iwj_salary', 'iwj_skill', 'iwj_level')";
    }
    $query .= " WHERE " . $where;
    $query .= " GROUP BY t.term_id";
    $query .= " ORDER BY tx.taxonomy, total_post DESC";

    $result = $wpdb->get_results($query);

    return $result;
}

function get_terms_has_candidates() {
    global $wpdb;

    $query = "SELECT t.term_id, t.name, t.slug, tx.parent, tx.taxonomy, COUNT(DISTINCT(p.ID)) AS total_post";
    $query .= " FROM " . $wpdb->terms . " AS t";
    $query .= " LEFT JOIN " . $wpdb->term_taxonomy . " AS tx ON tx.term_id = t.term_id";
    $query .= " LEFT JOIN " . $wpdb->term_relationships . " AS tr ON tx.term_taxonomy_id = tr.term_taxonomy_id";
    $query .= " LEFT JOIN " . $wpdb->posts . " AS p ON p.ID = tr.object_id AND p.post_type = 'iwj_candidate' AND p.post_status = 'publish'";

    $query .= " WHERE tx.taxonomy IN ('iwj_cat', 'iwj_location', 'iwj_skill')";
    $query .= " GROUP BY t.term_id";
    $query .= " ORDER BY tx.taxonomy, total_post DESC";

    $result = $wpdb->get_results($query, OBJECT_K);

    return $result;
}

function get_terms_has_employers() {
    global $wpdb;

    $query = "SELECT t.term_id, t.name, t.slug, tx.taxonomy, COUNT(DISTINCT(p.ID)) AS total_post";
    $query .= " FROM " . $wpdb->terms . " AS t";
    $query .= " LEFT JOIN " . $wpdb->term_taxonomy . " AS tx ON tx.term_id = t.term_id";
    $query .= " LEFT JOIN " . $wpdb->term_relationships . " AS tr ON tx.term_taxonomy_id = tr.term_taxonomy_id";
    $query .= " LEFT JOIN " . $wpdb->posts . " AS p ON p.ID = tr.object_id AND p.post_type = 'iwj_employer' AND p.post_status = 'publish'";

    $query .= " WHERE tx.taxonomy IN ('iwj_cat', 'iwj_location', 'iwj_type', 'iwj_salary', 'iwj_skill', 'iwj_level')";
    $query .= " GROUP BY t.term_id";
    $query .= " ORDER BY tx.taxonomy, total_post DESC";

    $result = $wpdb->get_results($query);

    return $result;
}

function iwj_count_item_by_taxonomy($filters = array()) {

    global $wpdb;
    $filters_def = array(
        'post_type' => 'iwj_job',
        'iwj_cat' => array(),
        'iwj_type' => array(),
        'iwj_skill' => array(),
        'iwj_salary' => array(),
        'iwj_location' => array(),
        'iwj_level' => array(),
        'keyword' => '',
        'radius' => '',
        'current_lat' => '',
        'current_lng' => '',
        'search_unit' => 'Km',
    );

    $filters = array_merge($filters_def, $filters);

    extract($filters);

    $sql_select = "SELECT ";
    $sql_select .= " t.term_id";
    $sql_select .= " ,t.name";
    $sql_select .= " ,t.slug";
    $sql_select .= " ,tx.taxonomy";
    $sql_select .= " ,tx.parent";
    $sql_select .= " ,COUNT(DISTINCT (p.ID)) AS total_post";

    $sql_select .= " FROM " . $wpdb->posts . " AS p";

    $sql_join = " JOIN " . $wpdb->term_relationships . " as tr ON p.ID = tr.object_id";
    $sql_join .= " JOIN " . $wpdb->terms . " as t ON t.term_id = tr.term_taxonomy_id";
    $sql_join .= " JOIN " . $wpdb->term_taxonomy . " AS tx ON t.term_id = tx.term_id";

    $sql_left_join = " ";

    // where
    $sql_where = " WHERE";

    $sql_where .= " p.post_type = '" . $post_type . "'";

    $sql_where .= " AND p.post_status='publish'";

    if ($post_type == 'iwj_job') {
        if (!iwj_option('show_expired_job')) {
            $sql_join .= " JOIN " . $wpdb->postmeta . " as pm ON p.ID = pm.post_id";
            $sql_where .= " AND pm.meta_key='" . IWJ_PREFIX . "expiry' AND (pm.meta_value = '' OR CAST(pm.meta_value AS UNSIGNED) > " . current_time('timestamp') . ")";
        }

        if (($filters['current_lat'] || $filters['current_lng']) && $filters['radius']) {
            $sql_join .= " JOIN " . $wpdb->postmeta . " as latlng_meta ON p.ID = latlng_meta.post_id";

            $current_lat = (float) $filters['current_lat'];
            $current_lng = (float) $filters['current_lng'];
            if ($filters['radius'] == 'Km') {
                $radius = (float) $filters['radius'] / 1.609344;
            } else {
                $radius = (float) $filters['radius'];
            }
            $sql_where .= " AND latlng_meta.meta_key = '" . IWJ_PREFIX . "map' AND ( ( acos( sin( SUBSTRING_INDEX(latlng_meta.meta_value,',', 1) * 0.0175) * sin( $current_lat * 0.0175) + cos( SUBSTRING_INDEX(latlng_meta.meta_value,',', 1) * 0.0175) * cos( $current_lat * 0.0175 ) * cos( ( $current_lng  * 0.0175 ) - ( SUBSTRING_INDEX(SUBSTRING_INDEX(latlng_meta.meta_value, ',',2),',', -1) * 0.0175 ) ) ) * 3959 ) < $radius ) ";
        }
    }

    if ($post_type == 'iwj_candidate') {
        $sql_join .= " JOIN " . $wpdb->postmeta . " as pm ON p.ID = pm.post_id";
        $sql_where .= " AND pm.meta_key='" . IWJ_PREFIX . "public_account' AND CAST(pm.meta_value AS CHAR) = '1'";
    }

    if (isset($keyword) && $keyword) {
        $sql_where .= " AND ( p.post_title LIKE '%" . $keyword . "%'" . " OR p.post_excerpt LIKE '%" . $keyword . "%' OR p.post_content LIKE '%" . $keyword . "%' )";
    }

    if (isset($alpha) && $alpha) {

        if ($alpha == '#') {
            $sql_where .= ' AND p.post_title LIKE \'' . esc_sql($wpdb->esc_like('[0-9]')) . '%\'';
        } else {
            $sql_where .= ' AND (p.post_title LIKE \'' . esc_sql($wpdb->esc_like($alpha)) . '%\'';
            $sql_where .= ' OR p.post_title LIKE \'' . esc_sql($wpdb->esc_like(strtoupper($alpha))) . '%\')';
        }
    }

    $job_taxs = iwj_get_job_taxonomies();
    if ($job_taxs) {
        foreach ($job_taxs as $tax) {
            if (isset($$tax) && count($$tax)) {
                $term_ids = implode(',', $$tax);
                $sql_left_join .= " LEFT JOIN " . $wpdb->term_relationships . " as {$tax} ON p.ID = {$tax}.object_id AND {$tax}.term_taxonomy_id IN (" . $term_ids . ")";
                $sql_where .= " AND ({$tax}.term_taxonomy_id IN (" . $term_ids . ") OR tx.taxonomy = '{$tax}')";
            }
        }
    }

    // group by
    $sql_group = " GROUP BY tr.term_taxonomy_id";
    $sql_order = " ORDER BY tx.taxonomy";

    $sql = $sql_select . " " . $sql_join . " " . $sql_left_join . " " . $sql_where . " " . $sql_group . " " . $sql_order;

    //$query = $wpdb->prepare( $raw_query );

    $result = $wpdb->get_results($sql, OBJECT_K);

    return $result;
}

function iwj_add_new_intervals($schedules) {
    // add weekly and monthly intervals
    $schedules['weekly'] = array(
        'interval' => 604800,
        'display' => __('Once Weekly', 'iwjob')
    );

    /* $schedules['monthly'] = array(
      'interval' => 2635200,
      'display' => __('Once a month')
      ); */

    return $schedules;
}

function iwj_alert_job_daily() {
    global $wpdb;
    //ORDER BY ar.term_id ASC
    $sql = "SELECT a.*, GROUP_CONCAT(ar.term_id SEPARATOR ',') AS term_ids FROM {$wpdb->prefix}iwj_alerts as a JOIN {$wpdb->prefix}iwj_alert_relationships as ar ON (a.ID = ar.alert_id) WHERE a.frequency = %s AND a.status = 1 GROUP BY a.ID";
    $alerts = $wpdb->get_results($wpdb->prepare($sql, 'daily'));
    iwj_send_alerts_job($alerts);
}

function iwj_alert_job_weekly() {
    global $wpdb;
    //ORDER BY ar.term_id ASC
    $sql = "SELECT a.*, GROUP_CONCAT(ar.term_id SEPARATOR ',') AS term_ids FROM {$wpdb->prefix}iwj_alerts as a JOIN {$wpdb->prefix}iwj_alert_relationships as ar ON (a.ID = ar.alert_id) WHERE a.frequency = %s AND a.status = 1 GROUP BY a.ID";
    $alerts = $wpdb->get_results($wpdb->prepare($sql, 'weekly'));
    iwj_send_alerts_job($alerts);
}

function iwj_send_alerts_job($alerts, $method = "") {
    if ($alerts) {
        global $wpdb;
        foreach ($alerts as $alert) {
            $key = md5($alert->frequency . ',' . $alert->term_ids . ',' . (int) $alert->salary_from);
            if (!isset(IWJ_Class::$jobs_alert[$key])) {
                if ((int) $alert->salary_from > 0) {
                    $sql = "SELECT p.* FROM {$wpdb->posts} AS p JOIN {$wpdb->postmeta} AS m ON (p.ID = m.post_id)  
                                JOIN {$wpdb->postmeta} AS m1 ON (p.ID = m1.post_id) 
                                JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id) 
                        WHERE p.post_type = %s AND p.post_status = %s AND m.meta_key = %s AND (m.meta_value = '' OR CAST(m.meta_value AS UNSIGNED) > %d)
                          AND m1.meta_key = %s AND CAST(m1.meta_value AS UNSIGNED) >= %d AND DATE_ADD(p.post_date, INTERVAL %d DAY) > %s AND tr.term_taxonomy_id IN({$alert->term_ids}) GROUP BY p.ID";
                    $day = $alert->frequency == 'daily' ? 1 : 7;
                    $jobs = $wpdb->get_results($wpdb->prepare($sql, 'iwj_job', 'publish', IWJ_PREFIX . 'expiry', current_time('timestamp'), IWJ_PREFIX . 'salary_from', (int) $alert->salary_from, $day, current_time('mysql')));
                } else {
                    $sql = "SELECT p.* FROM {$wpdb->posts} AS p JOIN {$wpdb->postmeta} AS m ON (p.ID = m.post_id) JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id) 
                        WHERE p.post_type = %s AND p.post_status = %s AND m.meta_key = %s AND (m.meta_value = '' OR CAST(m.meta_value AS UNSIGNED) > %d)  AND DATE_ADD(p.post_date, INTERVAL %d DAY) > %s AND tr.term_taxonomy_id IN({$alert->term_ids}) GROUP BY p.ID";
                    $day = $alert->frequency == 'daily' ? 1 : 7;
                    $jobs = $wpdb->get_results($wpdb->prepare($sql, 'iwj_job', 'publish', IWJ_PREFIX . 'expiry', current_time('timestamp'), $day, current_time('mysql')));
                }

                IWJ_Class::$jobs_alert[$key] = $jobs;
            }

            $jobs = IWJ_Class::$jobs_alert[$key];
            if ($jobs) {
                foreach ($jobs as $key => $job) {
                    $jobs[$key] = IWJ_Job::get_job($job);
                }

                $alert = IWJ_Alert::get_alert($alert);
                $user = IWJ_User::get_user($alert->user_id);
                IWJ_Email::send_email('alert_job', array('jobs' => $jobs, 'user' => $user, 'alert' => $alert), $method);
            }
        }
    }
}

function iwj_check_featured_job() {
    global $wpdb;
    //ORDER BY ar.term_id ASC
    $sql = "SELECT p.ID FROM {$wpdb->posts} AS p 
            JOIN {$wpdb->postmeta} AS pm ON pm.post_id = p.ID 
            JOIN {$wpdb->postmeta} AS pm1 ON pm1.post_id = p.ID 
            WHERE pm.meta_key = %s AND pm1.meta_key = %s AND pm.meta_value = %s AND pm1.meta_value != '' AND CAST(pm1.meta_value AS UNSIGNED) < %d";
    $jobs = $wpdb->get_results($wpdb->prepare($sql, IWJ_PREFIX . 'featured', IWJ_PREFIX . 'featured_expiry', '1', current_time('timestamp')));
    $job_ids = array();
    if ($jobs) {
        foreach ($jobs AS $job) {
            $job_ids[] = $job->ID;
        }
    }

    if ($job_ids) {
        $sql = "UPDATE {$wpdb->postmeta} SET meta_value = %s WHERE post_id IN (" . implode(",", $job_ids) . ") AND meta_key = %s";
        $wpdb->query($wpdb->prepare($sql, '0', IWJ_PREFIX . 'featured'));

        $sql = "UPDATE {$wpdb->postmeta} SET meta_value = %s WHERE post_id IN (" . implode(",", $job_ids) . ") AND meta_key = %s";
        $wpdb->query($wpdb->prepare($sql, '', IWJ_PREFIX . 'featured_date'));
    }
}

function iwj_delete_draft_job() {
    global $wpdb;
    //ORDER BY ar.term_id ASC
    $held_duration = iwj_option('delete_draft_job_hours', '24');
    $sql = "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s AND (post_date + INTERVAL %d HOUR) < %s";
    $jobs = $wpdb->get_results($wpdb->prepare($sql, 'iwj_job', 'draft', $held_duration, current_time('mysql')));
    if ($jobs) {
        foreach ($jobs AS $job) {
            wp_delete_post($job->ID);
        }
    }

    if ($held_duration >= 1) {
        wp_schedule_single_event(time() + ( absint($held_duration) * 60 * 60), 'iwj_delete_draft_job');
    }
}

function iwj_delete_pending_order() {
    global $wpdb;
    //ORDER BY ar.term_id ASC
    $held_duration = iwj_option('delete_pending_order_hours');
    $sql = "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s AND (post_date + INTERVAL %d HOUR) < %s";
    $orders = $wpdb->get_results($wpdb->prepare($sql, 'iwj_order', 'iwj-pending-payment', $held_duration, current_time('mysql')));
    if ($orders) {
        foreach ($orders AS $order) {
            wp_delete_post($order->ID);
        }
    }

    if ($held_duration >= 1) {
        wp_schedule_single_event(time() + ( absint($held_duration) * 60 * 60), 'iwj_delete_pending_order');
    }
}

function iwj_parse_name($name) {
    if ($name) {
        $name_arr = explode(' ', $name);
        $return[] = $name_arr[0];
        unset($name_arr[0]);

        if ($name_arr) {
            $return[] = implode(' ', $name_arr);
        }

        return $return;
    }

    return array(0 => '', 1 => '');
}

function iwj_clone_post($post_id, $new_status = 'pending') {
    $new_post_id = 0;

    global $wpdb;

    /*
     * and all the original post data then
     */
    $post = get_post($post_id);

    /*
     * if post data exists, create the post duplicate
     */
    if (isset($post) && $post != null) {

        /*
         * new post data array
         */
        $args = array(
            'comment_status' => $post->comment_status,
            'ping_status' => $post->ping_status,
            'post_author' => $post->post_author,
            'post_content' => $post->post_content,
            'post_excerpt' => $post->post_excerpt,
            'post_name' => $post->post_name,
            'post_parent' => $post_id,
            'post_password' => $post->post_password,
            'post_status' => $new_status,
            'post_title' => $post->post_title,
            'post_type' => $post->post_type,
            'to_ping' => $post->to_ping,
            'menu_order' => $post->menu_order
        );

        /*
         * insert the post by wp_insert_post() function
         */
        $new_post_id = wp_insert_post($args);

        /*
         * get all current post terms ad set them to the new post draft
         */
        $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
        foreach ($taxonomies as $taxonomy) {
            $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
            wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
        }

        /*
         * duplicate all post meta just in two SQL queries
         */
        $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
        if (count($post_meta_infos) != 0) {
            $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
            foreach ($post_meta_infos as $meta_info) {
                $meta_key = $meta_info->meta_key;
                $meta_value = addslashes($meta_info->meta_value);
                $sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
            }
            $sql_query .= implode(" UNION ALL ", $sql_query_sel);
            $wpdb->query($sql_query);
        }
    }

    return $new_post_id;
}

function iwj_move_post($from_post_id, $to_post_id) {
    $new_post_id = 0;

    global $wpdb;

    /*
     * and all the original post data then
     */
    $from_post = get_post($from_post_id);
    $to_post = get_post($to_post_id);

    /*
     * if post data exists, create the post duplicate
     */
    if ($from_post && $to_post) {
        $query = "UPDATE {$wpdb->posts} SET post_title = %s, post_content = %s, post_excerpt = %s WHERE ID = %d";
        $wpdb->query($wpdb->prepare($query, $from_post->post_title, $from_post->post_content, $from_post->post_excerpt, $to_post->ID));

        /*
         * get all current post terms ad set them to the new post draft
         */
        $taxonomies = get_object_taxonomies($from_post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
        foreach ($taxonomies as $taxonomy) {
            $post_terms = wp_get_object_terms($from_post_id, $taxonomy, array('fields' => 'slugs'));
            wp_set_object_terms($to_post_id, $post_terms, $taxonomy, false);
        }

        /*
         * duplicate all post meta just in two SQL queries
         */
        $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$from_post_id");
        if (count($post_meta_infos) != 0) {
            foreach ($post_meta_infos as $meta_info) {
                $meta_key = $meta_info->meta_key;
                $meta_value = $meta_info->meta_value;
                update_post_meta($to_post_id, $meta_key, $meta_value);
            }
        }
    }

    return $new_post_id;
}

function iwj_allowed_tags() {

    $allowed_atts = array(
        'align' => array(),
        'class' => array(),
        'type' => array(),
        'id' => array(),
        'dir' => array(),
        'lang' => array(),
        'style' => array(),
        'xml:lang' => array(),
        'src' => array(),
        'alt' => array(),
        'href' => array(),
        'rel' => array(),
        'rev' => array(),
        'target' => array(),
        'novalidate' => array(),
        'type' => array(),
        'value' => array(),
        'name' => array(),
        'tabindex' => array(),
        'action' => array(),
        'method' => array(),
        'for' => array(),
        'width' => array(),
        'height' => array(),
        'data' => array(),
        'title' => array(),
    );
    $allowedposttags['form'] = $allowed_atts;
    $allowedposttags['label'] = $allowed_atts;
    $allowedposttags['input'] = $allowed_atts;
    $allowedposttags['textarea'] = $allowed_atts;
    $allowedposttags['iframe'] = $allowed_atts;
    $allowedposttags['script'] = $allowed_atts;
    $allowedposttags['style'] = $allowed_atts;
    $allowedposttags['strong'] = $allowed_atts;
    $allowedposttags['small'] = $allowed_atts;
    $allowedposttags['table'] = $allowed_atts;
    $allowedposttags['span'] = $allowed_atts;
    $allowedposttags['abbr'] = $allowed_atts;
    $allowedposttags['code'] = $allowed_atts;
    $allowedposttags['pre'] = $allowed_atts;
    $allowedposttags['div'] = $allowed_atts;
    $allowedposttags['img'] = $allowed_atts;
    $allowedposttags['h1'] = $allowed_atts;
    $allowedposttags['h2'] = $allowed_atts;
    $allowedposttags['h3'] = $allowed_atts;
    $allowedposttags['h4'] = $allowed_atts;
    $allowedposttags['h5'] = $allowed_atts;
    $allowedposttags['h6'] = $allowed_atts;
    $allowedposttags['ol'] = $allowed_atts;
    $allowedposttags['ul'] = $allowed_atts;
    $allowedposttags['li'] = $allowed_atts;
    $allowedposttags['em'] = $allowed_atts;
    $allowedposttags['hr'] = $allowed_atts;
    $allowedposttags['br'] = $allowed_atts;
    $allowedposttags['tr'] = $allowed_atts;
    $allowedposttags['td'] = $allowed_atts;
    $allowedposttags['p'] = $allowed_atts;
    $allowedposttags['a'] = $allowed_atts;
    $allowedposttags['b'] = $allowed_atts;
    $allowedposttags['i'] = $allowed_atts;

    $allowed_tags = array(
        'a' => array(
            'class' => array(),
            'href' => array(),
            'rel' => array(),
            'title' => array(),
        ),
        'abbr' => array(
            'title' => array(),
        ),
        'b' => array(),
        'blockquote' => array(
            'cite' => array(),
        ),
        'cite' => array(
            'title' => array(),
        ),
        'code' => array(),
        'del' => array(
            'datetime' => array(),
            'title' => array(),
        ),
        'dd' => array(),
        'div' => array(
            'class' => array(),
            'title' => array(),
            'style' => array(),
        ),
        'dl' => array(),
        'dt' => array(),
        'em' => array(),
        'h1' => array(),
        'h2' => array(),
        'h3' => array(),
        'h4' => array(),
        'h5' => array(),
        'h6' => array(),
        'i' => array(),
        'img' => array(
            'alt' => array(),
            'class' => array(),
            'height' => array(),
            'src' => array(),
            'width' => array(),
        ),
        'li' => array(
            'class' => array(),
        ),
        'ol' => array(
            'class' => array(),
        ),
        'p' => array(
            'class' => array(),
        ),
        'q' => array(
            'cite' => array(),
            'title' => array(),
        ),
        'span' => array(
            'class' => array(),
            'title' => array(),
            'style' => array(),
        ),
        'strike' => array(),
        'strong' => array(),
        'ul' => array(
            'class' => array(),
        ),
    );

    return $allowed_tags;
}

function iwj_field_input($type = 'text', $id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '', $placeholder = '') {
    $field = array(
        'name' => $title,
        'id' => $id,
        'type' => $type,
        'required' => $required ? 'required' : '',
        'std' => $default_value,
        'desc' => $description,
        'placeholder' => $placeholder,
    );
    $field = IWJMB_Field::call('normalize', $field);
    if ($value === null) {
        $value = IWJMB_Field::call($field, 'post_meta', $post_id, ($post_id ? true : false));
    }

    IWJMB_Field::input($field, $value);
}

function iwj_field_text($id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '', $placeholder = '') {
    iwj_field_input('text', $id, $title, $required, $post_id, $value, $default_value, $description, $placeholder);
}

function iwj_field_url($id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '', $placeholder = '') {
    iwj_field_input('url', $id, $title, $required, $post_id, $value, $default_value, $description, $placeholder);
}

function iwj_field_email($id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '', $placeholder = '') {
    iwj_field_input('email', $id, $title, $required, $post_id, $value, $default_value, $description, $placeholder);
}

function iwj_field_password($id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '', $placeholder = '') {
    iwj_field_input('password', $id, $title, $required, $post_id, $value, $default_value, $description, $placeholder);
}

function iwj_field_textarea($id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '', $placeholder = '') {
    $field = array(
        'name' => $title,
        'id' => $id,
        'type' => 'textarea',
        'required' => $required,
        'std' => $default_value,
        'desc' => $description,
        'placeholder' => $placeholder,
    );
    $field = IWJMB_Field::call('normalize', $field);
    if ($value === null) {
        $value = IWJMB_Field::call($field, 'post_meta', $post_id, ($post_id ? true : false));
    }

    IWJMB_Field::input($field, $value);
}

function iwj_field_map($id, $title, $post_id = null, $value = null, $default_value = '', $description = '', $address_field = '') {
    if (!$default_value && iwj_option('map_latitude') && iwj_option('map_logtitude')) {
        $default_value = iwj_option('map_latitude') . ',' . iwj_option('map_logtitude') . ',' . iwj_option('map_zoom', 14);
    }
    $field = array(
        'name' => $title,
        'id' => $id,
        'type' => 'map',
        'std' => $default_value,
        'desc' => $description,
        'address_field' => $address_field,
    );
    $field = IWJMB_Field::call('normalize', $field);
    if ($value === null) {
        $value = IWJMB_Field::call($field, 'post_meta', $post_id, ($post_id ? true : false));
    }

    IWJMB_Field::input($field, $value);
}

function iwj_field_map_address($id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '', $placeholder = '') {
    $field = array(
        'name' => $title,
        'id' => $id,
        'type' => 'map_address',
        'required' => $required ? 'required' : '',
        'std' => $default_value,
        'desc' => $description,
        'placeholder' => $placeholder,
    );
    $field = IWJMB_Field::call('normalize', $field);
    if ($value === null) {
        $value = IWJMB_Field::call($field, 'post_meta', $post_id, ($post_id ? true : false));
    }

    IWJMB_Field::input($field, $value);
}

function iwj_field_date($id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '', $placeholder = '', $format = '') {
    $field = array(
        'name' => $title,
        'id' => $id,
        'type' => 'date',
        'required' => $required,
        'std' => $default_value,
        'desc' => $description,
        'placeholder' => $placeholder,
    );
    if ($format) {
        $field['format'] = $format;
    }
    $field = IWJMB_Field::call('normalize', $field);
    if ($value === null) {
        $value = IWJMB_Field::call($field, 'post_meta', $post_id, ($post_id ? true : false));
    }

    IWJMB_Field::input($field, $value);
}

function iwj_field_wysiwyg($id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '', $placeholder = '', $options = array(
    'quicktags' => false,
    'editor_height' => 200
)) {
    $field = array(
        'name' => $title,
        'id' => $id,
        'type' => 'wysiwyg',
        'required' => $required,
        'std' => $default_value,
        'desc' => $description,
        'placeholder' => $placeholder,
        'options' => $options,
    );

    $field = IWJMB_Field::call('normalize', $field);
    if ($value === null) {
        $value = IWJMB_Field::call($field, 'post_meta', $post_id, ($post_id ? true : false));
    }

    IWJMB_Field::input($field, $value);
}

function iwj_field_tagable($options, $id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '', $placeholder = '') {
    $field = array(
        'name' => $title,
        'id' => $id,
        'type' => 'tagable',
        'placeholder' => $placeholder,
        'required' => $required,
        'std' => $default_value,
        'desc' => $description,
        'options' => $options
    );
    $field = IWJMB_Field::call('normalize', $field);
    if ($value === null) {
        $value = IWJMB_Field::call($field, 'post_meta', $post_id, ($post_id ? true : false));
    }

    IWJMB_Field::input($field, $value);
}

function iwj_field_taxonomy($taxonomy, $id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '', $placeholder = '', $multiple = false, $query_args = array(), $js_options = array()) {
    $field = array(
        'name' => $title,
        'id' => $id,
        'type' => 'taxonomy',
        'options' => array(
            'taxonomy' => $taxonomy
        ),
        'query_args' => $query_args,
        'placeholder' => $placeholder,
        'multiple' => $multiple,
        'required' => $required,
        'std' => $default_value,
        'desc' => $description,
        'js_options' => $js_options
    );
    $field = IWJMB_Field::call('normalize', $field);
    if ($value === null) {
        $value = IWJMB_Field::call($field, 'post_meta', $post_id, ($post_id ? true : false));
    }

    IWJMB_Field::input($field, $value);
}

function iwj_field_taxonomy2($taxonomy, $id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '', $placeholder = '', $multiple = false, $query_args = array(), $js_options = array(), $hierarchy = false) {
    $field = array(
        'name' => $title,
        'id' => $id,
        'type' => 'taxonomy2',
        'options' => array(
            'taxonomy' => $taxonomy
        ),
        'query_args' => $query_args,
        'placeholder' => $placeholder,
        'multiple' => $multiple,
        'required' => $required,
        'std' => $default_value,
        'desc' => $description,
        'js_options' => $js_options,
        'hierarchy' => $hierarchy,
    );
    $field = IWJMB_Field::call('normalize', $field);
    if ($value === null) {
        $value = IWJMB_Field::call($field, 'post_meta', $post_id, ($post_id ? true : false));
    }

    IWJMB_Field::input($field, $value);
}

function iwj_field_select_tree($taxonomy, $id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '', $placeholder = '', $multiple = false, $query_args = array()) {
    $field = array(
        'name' => $title,
        'id' => $id,
        'type' => 'taxonomy',
        'options' => array(
            'type' => 'select_tree',
            'taxonomy' => $taxonomy
        ),
        'query_args' => $query_args,
        'placeholder' => $placeholder,
        'multiple' => $multiple,
        'required' => $required,
        'std' => $default_value,
        'desc' => $description,
        'attributes' => array(
            'class' => 'iwjmb-select_advanced'
        ),
    );
    $field = IWJMB_Field::call('normalize', $field);
    if ($value === null) {
        $value = IWJMB_Field::call($field, 'post_meta', $post_id, ($post_id ? true : false));
    }

    IWJMB_Field::input($field, $value);
}

function iwj_field_gallery($id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '', $max_file_uploads = 5) {
    $field = array(
        'name' => $title,
        'id' => $id,
        'type' => 'image_upload',
        'required' => $required,
        'std' => $default_value,
        'desc' => $description,
        'max_file_uploads' => $max_file_uploads
    );
    $field = IWJMB_Field::call('normalize', $field);
    if ($value === null) {
        $value = IWJMB_Field::call($field, 'post_meta', $post_id, ($post_id ? true : false));
    }

    IWJMB_Field::input($field, $value);
}

function iwj_field_image($id, $title, $post_id = null, $value = null, $default_value = '', $default_image_url = '', $button_text = '', $description = '') {
    $field = array(
        'name' => $title,
        'id' => $id,
        'type' => 'image_single',
        'std' => $default_value,
        'default_image_url' => $default_image_url,
        'button_text' => $button_text,
        'button_desc' => $description,
    );
    $field = IWJMB_Field::call('normalize', $field);
    if ($value === null) {
        $value = IWJMB_Field::call($field, 'post_meta', $post_id, ($post_id ? true : false));
    }

    IWJMB_Field::input($field, $value);
}

function iwj_field_select($options, $id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '', $placeholder = '', $multiple = false) {
    $field = array(
        'name' => $title,
        'id' => $id,
        'type' => 'select',
        'options' => $options,
        'desc' => $description,
        'placeholder' => $placeholder,
        'multiple' => $multiple,
        'required' => $required,
        'std' => $default_value
    );
    $field = IWJMB_Field::call('normalize', $field);
    if ($value === null) {
        $value = IWJMB_Field::call($field, 'post_meta', $post_id, ($post_id ? true : false));
    }

    IWJMB_Field::input($field, $value);
}

function iwj_field_radio($options, $id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '') {
    $field = array(
        'name' => $title,
        'id' => $id,
        'type' => 'radio',
        'options' => $options,
        'desc' => $description,
        'required' => $required,
        'std' => $default_value
    );
    $field = IWJMB_Field::call('normalize', $field);
    if ($value === null) {
        $value = IWJMB_Field::call($field, 'post_meta', $post_id, ($post_id ? true : false));
    }

    IWJMB_Field::input($field, $value);
}

function iwj_field_select2($options, $id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '', $placeholder = '', $multiple = false, $js_options = array()) {
    $field = array(
        'name' => $title,
        'id' => $id,
        'type' => 'select_advanced',
        'options' => $options,
        'desc' => $description,
        'placeholder' => $placeholder,
        'multiple' => $multiple,
        'required' => $required,
        'std' => $default_value,
        'js_options' => $js_options
    );
    $field = IWJMB_Field::call('normalize', $field);
    if ($value === null) {
        $value = IWJMB_Field::call($field, 'post_meta', $post_id, ($post_id ? true : false));
    }
    IWJMB_Field::input($field, $value);
}

function iwj_field_group($fields, $id, $title, $post_id = null, $default_value = array(), $clone = false, $sort_clone = false) {
    $field = array(
        'name' => $title,
        'id' => $id,
        'type' => 'group',
        'clone' => $clone,
        'sort_clone' => $sort_clone,
        'fields' => $fields,
    );

    $field = IWJMB_Field::call('normalize', $field);
    if ($post_id) {
        $value = IWJMB_Field::call($field, 'post_meta', $post_id, true);
    } else {
        $value = $default_value;
    }

    IWJMB_Field::input($field, $value);
}

function iwj_field_file_upload($id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '', $args = array()) {
    wp_enqueue_media();
    $field = array(
        'name' => $title,
        'id' => $id,
        'type' => 'file_input',
        'required' => $required,
        'std' => $default_value,
        'desc' => $description,
    );

    if (isset($args['mime_type'])) {
        $field['mime_type'] = $args['mime_type'];
    }

    if (isset($args['max_file_uploads'])) {
        $field['max_file_uploads'] = $args['max_file_uploads'];
    }

    if (isset($args['force_delete'])) {
        $field['force_delete'] = $args['force_delete'];
    }

    $field = IWJMB_Field::call('normalize', $field);
    if ($value === null) {
        $value = IWJMB_Field::call($field, 'post_meta', $post_id, ($post_id ? true : false));
    }

    IWJMB_Field::input($field, $value);
}

function iwj_field_file_cv($id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '', $is_profile = false) {
    $field = array(
        'name' => $title,
        'id' => $id,
        'type' => 'cv',
        'required' => $required,
        'std' => $default_value,
        'desc' => $description,
        'is_profile' => $is_profile,
    );

    $field = IWJMB_Field::call('normalize', $field);
    if ($value === null) {
        $value = IWJMB_Field::call($field, 'post_meta', $post_id, ($post_id ? true : false));
    }

    IWJMB_Field::input($field, $value);
}

function iwj_field_avatar($id, $title, $required = false, $post_id = null, $value = null, $default_value = '', $description = '') {
    $field = array(
        'name' => $title,
        'id' => $id,
        'type' => 'avatar',
        'required' => $required,
        'std' => $default_value,
        'desc' => $description,
    );

    $field = IWJMB_Field::call('normalize', $field);
    if ($value === null) {
        $value = IWJMB_Field::call($field, 'post_meta', $post_id, ($post_id ? true : false));
    }

    IWJMB_Field::input($field, $value);
}

function iwj_get_status_icon($status) {
    switch ($status) {
        case 'pending':
        case 'iwj-pending-payment':
            return '<i class="ion-ios-clock-outline"></i>';
        case 'iwj-hold':
            return '<i class="ion-ios-clock-outline"></i>';
        case 'publish':
        case 'approved':
        case 'iwj-completed':
            return '<i class="ion-checkmark-circled"></i>';
        case 'iwj-expired':
            return '<i class="ion-ios-minus-outline"></i>';
        case 'reject':
        case 'iwj-rejected':
        case 'iwj-cancelled':
            return '<i class="ion-android-cancel"></i>';
        case 'draft':
            return '<i class="ion-ios-compose-outline"></i>';
    }

    return '';
}

function iwj_get_map_api_key() {
    $api_key = iwj_option('google_api_key');
    if (!$api_key && class_exists('Inwave_Helper')) {
        $api_key = Inwave_Helper::getThemeOption('google_api');
    }

    return $api_key;
}

function iwj_get_cats($cat_ids = array(), $exclude_ids = array(), $hide_empty = false, $limit = '', $order_by = 'count', $oder = 'DESC') {
    static $cats = array();
    $key = '';
    if ($cat_ids) {
        $key .= implode(',', $cat_ids);
    }
    if ($exclude_ids) {
        $key .= '-' . implode(',', $exclude_ids);
    }
    $key .= '-' . (int) $hide_empty . '-' . $limit . '-' . $order_by . '-' . $oder;
    $key = md5($key);

    if (!isset($cats[$key])) {
        global $wpdb;

        $sql1 = "SELECT COUNT(p.ID) FROM {$wpdb->posts} AS p
        JOIN {$wpdb->term_relationships} AS tr ON tr.object_id = p.ID
        JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id";

        if (!iwj_option('show_expired_job')) {
            $sql1 .= " LEFT JOIN {$wpdb->postmeta} AS pm ON pm.post_id = p.ID";
        }

        $sql1 .= " WHERE
        t.term_id = tt.term_id 
        AND p.post_type = 'iwj_job' 
        AND p.post_status = 'publish'
        AND tt.taxonomy = 'iwj_cat'";

        if (!iwj_option('show_expired_job')) {
            $sql1 .= " AND pm.meta_key = '" . IWJ_PREFIX . "expiry' AND (pm.meta_value = '' OR CAST(pm.meta_value AS UNSIGNED) > " . current_time('timestamp') . ")";
        }

        $sql1 .= "GROUP BY tt.term_id";

        $where_sql = array('tm.taxonomy = "iwj_cat"');

        if ($hide_empty) {
            $where_sql[] = " ($sql1) >= 1";
        }
        if ($cat_ids) {
            $where_sql[] = " t.term_id IN (" . implode(',', $cat_ids) . ")";
        }

        if ($exclude_ids) {
            $where_sql[] = " t.term_id NOT IN (" . implode(',', $exclude_ids) . ")";
        }

        $where = $where_sql ? "WHERE " . implode(" AND ", $where_sql) : "";

        $limit_sql = '';
        if ($limit) {
            $limit_sql = "LIMIT 0,{$limit}";
        }

        $oder_by_sql = 'total';
        if ($order_by == 'name') {
            $oder_by_sql = 't.name';
        } else if ($order_by == 'term_id') {
            $oder_by_sql = 't.term_id';
        } else if ($order_by == 'custom' && $cat_ids) {
            $oder_by_sql = 'FIELD(t.term_id, ' . implode(',', $cat_ids) . ')';
            $oder = '';
        }

        $sql = "SELECT t.*, ({$sql1}) AS total FROM {$wpdb->terms} AS t JOIN {$wpdb->term_taxonomy} AS tm ON (t.term_id = tm.term_taxonomy_id) $where ORDER BY {$oder_by_sql} {$oder} $limit_sql";

        $_cats = $wpdb->get_results($sql);
        $cats[$key] = array();
        if ($_cats) {
            foreach ($_cats as $cat) {
                $cats[$key][$cat->term_id] = $cat;
            }
        }
    }

    return apply_filters('iwj_get_cats', $cats[$key]);
}

function iwj_get_cats_parent($cat_ids = array(), $exclude_ids = array(), $hide_empty = false, $limit = '', $order_by = 'count', $oder = 'DESC') {
    static $cats = array();
    $key = '';
    if ($cat_ids) {
        $key .= implode(',', $cat_ids);
    }
    if ($exclude_ids) {
        $key .= '-' . implode(',', $exclude_ids);
    }
    $key .= '-' . (int) $hide_empty . '-' . $limit . '-' . $order_by . '-' . $oder;
    $key = md5($key);

    if (!isset($cats[$key])) {
        global $wpdb;

        $sql1 = "SELECT COUNT(p.ID) FROM {$wpdb->posts} AS p
        JOIN {$wpdb->term_relationships} AS tr ON tr.object_id = p.ID
        JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id";

        if (!iwj_option('show_expired_job')) {
            $sql1 .= " LEFT JOIN {$wpdb->postmeta} AS pm ON pm.post_id = p.ID";
        }

        $sql1 .= " WHERE
        t.term_id = tt.term_id
        AND p.post_type = 'iwj_job'
        AND p.post_status = 'publish'
        AND tt.parent = '0'
        AND tt.taxonomy = 'iwj_cat' ";

        if (!iwj_option('show_expired_job')) {
            $sql1 .= " AND pm.meta_key = '" . IWJ_PREFIX . "expiry' AND (pm.meta_value = '' OR CAST(pm.meta_value AS UNSIGNED) > " . current_time('timestamp') . ")";
        }

        $sql1 .= "GROUP BY tt.term_id";



        $where_sql = array('tm.taxonomy = "iwj_cat"');

        if ($hide_empty) {
            $where_sql[] = " ($sql1) >= 1";
        }
        if ($cat_ids) {
            $where_sql[] = " t.term_id IN (" . implode(',', $cat_ids) . ")";
        }

        if ($exclude_ids) {
            $where_sql[] = " t.term_id NOT IN (" . implode(',', $exclude_ids) . ")";
        }

        $where = $where_sql ? "WHERE " . implode(" AND ", $where_sql) : "";

        $limit_sql = '';
        if ($limit) {
            $limit_sql = "LIMIT 0,{$limit}";
        }

        $oder_by_sql = 'total';
        if ($order_by == 'name') {
            $oder_by_sql = 't.name';
        } else if ($order_by == 'term_id') {
            $oder_by_sql = 't.term_id';
        } else if ($order_by == 'custom' && $cat_ids) {
            $oder_by_sql = 'FIELD(t.term_id, ' . implode(',', $cat_ids) . ')';
            $oder = '';
        }

        $sql = "SELECT t.*, ({$sql1}) AS total FROM {$wpdb->terms} AS t JOIN {$wpdb->term_taxonomy} AS tm ON (t.term_id = tm.term_taxonomy_id) $where AND tm.parent = '0' ORDER BY {$oder_by_sql} {$oder} $limit_sql";
        $_cats = $wpdb->get_results($sql);
        $cats[$key] = array();
        if ($_cats) {
            foreach ($_cats as $cat) {
                $cats[$key][$cat->term_id] = $cat;
            }
        }
    }

    return apply_filters('iwj_get_cats_parent', $cats[$key]);
}

function iwj_get_employers($employer_ids = array(), $exclude_ids = array(), $show_featured_employers = '', $hide_empty = false, $limit = '', $order_by = 'count', $oder = 'DESC') {
    static $employers = array();
    $key = '';
    if ($employer_ids) {
        $key .= implode(',', $employer_ids);
    }
    if ($exclude_ids) {
        $key .= '-' . implode(',', $exclude_ids);
    }
    $key .= '-' . (int) $hide_empty . '-' . $limit . '-' . $order_by . '-' . $oder;
    $key = md5($key);

    if (!isset($employers[$key])) {
        global $wpdb;
        $emp_where = "p.post_type='iwj_employer' and p.post_status='publish'";
        $job_where = "post_type = 'iwj_job' and post_status='publish' and post_author=p.post_author";
        $job_join = '';
        $emp_join = '';
        if (!iwj_option('show_expired_job')) {
            $job_join .= " LEFT JOIN {$wpdb->postmeta} AS pm ON pm.post_id = ID";
            $job_where .= " AND (pm.meta_key = '" . IWJ_PREFIX . "expiry' AND (pm.meta_value = '' OR CAST(pm.meta_value AS UNSIGNED) > " . current_time('timestamp') . "))";
		}
		if ($show_featured_employers) {
			$emp_join .= " LEFT JOIN {$wpdb->postmeta} AS epm ON epm.post_id = p.ID";
			$emp_where .= " AND (epm.meta_key = '" . IWJ_PREFIX . "featured' AND epm.meta_value = '1')";
		}

        if ($employer_ids) {
            $emp_where .= " AND p.ID IN (" . implode(',', $employer_ids) . ")";
        }

        if ($exclude_ids) {
            $emp_where .= " AND p.ID NOT IN (" . implode(',', $exclude_ids) . ")";
        }

        $having = '';
        if ($hide_empty) {
            $having = " HAVING total_jobs > 0";
        }

        $limit_sql = '';
        if ($limit) {
            $limit_sql = "LIMIT 0,{$limit}";
        }

        $oder_by_sql = 'total_jobs';
        if ($order_by == 'name') {
            $oder_by_sql = 'p.post_title';
        } else if ($order_by == 'date') {
            $oder_by_sql = 'p.post_date';
        } else if ($order_by == 'custom' && $employer_ids) {
            $oder_by_sql = 'FIELD(p.ID, ' . implode(',', $employer_ids) . ')';
            $oder = '';
        }

        $sql = "select (select count(ID) from {$wpdb->posts} {$job_join} WHERE {$job_where}) as total_jobs, p.* from {$wpdb->posts} as p {$emp_join} WHERE {$emp_where} GROUP BY p.post_author {$having} ORDER BY {$oder_by_sql} {$oder} {$limit_sql}";
		
        $_employers = $wpdb->get_results($sql);
        $employers[$key] = array();
        if ($_employers) {
            foreach ($_employers as $employer) {
                $employers[$key][$employer->ID] = $employer;
            }
        }
    }

    return $employers[$key];
}

function iwj_get_candidates($candidates_ids = array(), $exclude_ids = array(), $limit = '', $order_by = 'date', $oder = 'DESC') {
    static $candidates = array();
    $key = '';
    if ($candidates_ids) {
        $key .= implode(',', $candidates_ids);
    }
    if ($exclude_ids) {
        $key .= '-' . implode(',', $exclude_ids);
    }
    $key .= '-' . $limit . '-' . $order_by . '-' . $oder;
    $key = md5($key);

    if (!isset($candidates[$key])) {
        global $wpdb;
        $join_sql = '';
        $join_sql .= " JOIN {$wpdb->postmeta} AS pm ON pm.post_id = p.ID ";
        $where_sql = "p.post_type = 'iwj_candidate' AND p.post_status = 'publish'";
        if ($candidates_ids) {
            $where_sql .= " AND p.ID IN (" . implode(',', $candidates_ids) . ")";
        }
        if ($exclude_ids) {
            $where_sql .= " AND p.ID NOT IN (" . implode(',', $exclude_ids) . ")";
        }

        $where_sql .= " AND pm.meta_key = '" . IWJ_PREFIX . "public_account' AND pm.meta_value = '1' ";

        $limit_sql = '';
        if ($limit) {
            $limit_sql = "LIMIT 0,{$limit}";
        }

        $oder_by_sql = 'p.post_date';
        if ($order_by == 'name') {
            $oder_by_sql = 'p.post_title';
        } else if ($order_by == 'date') {
            $oder_by_sql = 'p.post_date';
        } else if ($order_by == 'custom' && $candidates_ids) {
            $oder_by_sql = 'FIELD(p.ID, ' . implode(',', $candidates_ids) . ')';
            $oder = '';
        }

        $sql = "SELECT p.*  FROM {$wpdb->posts} AS p {$join_sql} WHERE {$where_sql} ORDER BY {$oder_by_sql} {$oder} $limit_sql";

        $_candidates = $wpdb->get_results($sql);
        $candidates[$key] = array();
        if ($_candidates) {
            foreach ($_candidates as $candidate) {
                $candidates[$key][$candidate->ID] = $candidate;
            }
        }
    }

    return $candidates[$key];
}

function iwj_get_jobs($filter = 'any', $include_ids = '', $exclude_ids = '', $limit = '', $order_by = 'date', $order = 'DESC', $taxonomies = array(), $load_more = false) {
    $jobs = array();

    $key = $filter . '-' . (is_array($include_ids) ? implode(',', $include_ids) : '') . '-' .
            (is_array($exclude_ids) ? implode(',', $exclude_ids) : '') . '-' . $limit . '-' . $order_by . '-' . $order;

    if ($taxonomies && is_array($taxonomies)) {
        foreach ($taxonomies as $taxonomy => $taxonomy_ids) {
            if (is_array($taxonomy_ids)) {
                $key .= '-' . $taxonomy . implode(',', $taxonomy_ids);
            }
        }
    }

    $key = md5($key);

    if (!isset($jobs[$key])) {
        $args = array(
            'posts_per_page' => ($limit ? $limit : -1),
            'post_type' => 'iwj_job',
            'post_status' => 'publish',
            'include' => $include_ids,
            'exclude' => $exclude_ids,
            'suppress_filters' => false,
        );

        $args['meta_query'] = array('relation' => 'AND');

        if ($order_by == 'date' || $order_by == 'rand' || $order_by == 'title' || $order_by == 'modified') {
            $args['orderby'] = $order_by;
            $args['order'] = $order;
        } elseif ($order_by == 'salary') {
            $args['meta_query']['_iwj_salary_to_clause'] = array(
                'key' => '_iwj_salary_to',
                'compare' => 'EXISTS',
                'type' => 'numeric'
            );
            $args['meta_query']['_iwj_salary_from_clause'] = array(
                'key' => '_iwj_salary_from',
                'compare' => 'EXISTS',
                'type' => 'numeric'
            );

            $args['orderby'] = array(
                '_iwj_salary_to_clause' => 'DESC',
                '_iwj_salary_from_clause' => 'DESC',
                'date' => 'DESC'
            );
        } elseif ($order_by == 'featured') {
            $args['orderby'] = array('meta_value_num' => 'DESC', 'date' => 'DESC');
            $args['meta_key'] = '_iwj_featured_date';
        }

        if (!iwj_option('show_expired_job')) {
            $args['meta_query'][] = array(
                'relation' => 'OR',
                array(
                    'key' => IWJ_PREFIX . 'expiry',
                    'value' => '',
                    'compare' => '=',
                ),
                array(
                    'key' => IWJ_PREFIX . 'expiry',
                    'value' => current_time('timestamp'),
                    'compare' => '>',
                    'type' => 'NUMERIC'
                ),
            );
        }

        if ($filter == 'featured') {

            $args['meta_query'][] = array(
                'key' => IWJ_PREFIX . 'featured',
                'value' => '1',
                'compare' => '='
            );
        } elseif ($filter == 'recommend') {
            $job_suggestion_conditions = (array) iwj_option('job_suggestion_conditions');
            if ($job_suggestion_conditions && is_user_logged_in() && class_exists('IWJ_User')) {
                $user = IWJ_User::get_user();
                if ($user->is_candidate()) {
                    $candidate = $user->get_candidate();
                    if ($candidate) {
                        $gender = $languages = '';
                        if (in_array('gender', $job_suggestion_conditions)) {
                            $gender = $candidate->get_gender();
                        }

                        if (in_array('language', $job_suggestion_conditions)) {
                            $languages = $candidate->get_languages();
                        }
                        if ($gender || $languages) {
                            if ($gender) {
                                $args['meta_query'][] = array(
                                    'relation' => 'OR',
                                    array(
                                        'key' => IWJ_PREFIX . 'job_gender',
                                        'compare' => 'NOT EXISTS'
                                    ),
                                    array(
                                        'key' => IWJ_PREFIX . 'job_gender',
                                        'value' => array($gender),
                                        'compare' => 'IN'
                                    )
                                );
                            }
                            if ($languages) {
                                $args['meta_query'][] = array(
                                    'relation' => 'OR',
                                    array(
                                        'key' => IWJ_PREFIX . 'job_languages',
                                        'compare' => 'NOT EXISTS'
                                    ),
                                    array(
                                        'key' => IWJ_PREFIX . 'job_languages',
                                        'value' => $languages,
                                        'compare' => 'IN'
                                    ),
                                );
                            }
                        }


                        $categorie_ids = $type_ids = $skill_ids = $level_ids = $location_ids = array();

                        if (in_array('category', $job_suggestion_conditions)) {
                            $categories = $candidate->get_categories();
                            if ($categories) {
                                foreach ($categories as $category) {
                                    $categorie_ids[] = $category->term_id;
                                }
                            }
                        }

                        if (in_array('type', $job_suggestion_conditions)) {
                            $types = $candidate->get_types();
                            if ($types) {
                                foreach ($types as $type) {
                                    $type_ids[] = $type->term_id;
                                }
                            }
                        }

                        if (in_array('skill', $job_suggestion_conditions)) {
                            $skills = $candidate->get_skills();
                            if ($skills) {
                                foreach ($skills as $skill) {
                                    $skill_ids[] = $skill->term_id;
                                }
                            }
                        }

                        if (in_array('level', $job_suggestion_conditions)) {
                            $levels = $candidate->get_levels();
                            if ($levels) {
                                foreach ($levels as $level) {
                                    $level_ids[] = $level->term_id;
                                }
                            }
                        }

                        if (in_array('location', $job_suggestion_conditions)) {
                            $locations = $candidate->get_locations();
                            if ($locations) {
                                foreach ($locations as $location) {
                                    $location_ids[] = $location->term_id;
                                }
                            }
                        }

                        if ($categorie_ids || $type_ids || $skill_ids || $level_ids) {
                            $args['tax_query'] = array(
                                'relation' => 'AND'
                            );
                            if ($categorie_ids) {
                                $args['tax_query'][] = array(
                                    'taxonomy' => 'iwj_cat',
                                    'field' => 'term_id',
                                    'terms' => $categorie_ids,
                                );
                            }
                            if ($type_ids) {
                                $args['tax_query'][] = array(
                                    'taxonomy' => 'iwj_type',
                                    'field' => 'term_id',
                                    'terms' => $type_ids,
                                );
                            }
                            if ($level_ids) {
                                $args['tax_query'][] = array(
                                    'taxonomy' => 'iwj_level',
                                    'field' => 'term_id',
                                    'terms' => $level_ids,
                                );
                            }
                            if ($skill_ids) {
                                $args['tax_query'][] = array(
                                    'taxonomy' => 'iwj_skill',
                                    'field' => 'term_id',
                                    'terms' => $skill_ids,
                                );
                            }
                            if ($location_ids) {
                                $args['tax_query'][] = array(
                                    'taxonomy' => 'iwj_location',
                                    'field' => 'term_id',
                                    'terms' => $location_ids,
                                );
                            }
                        }
                    }
                }
            }
        }

        if ($taxonomies && is_array($taxonomies)) {
            $args['tax_query'] = array();
            foreach ($taxonomies as $taxonomy => $taxonomy_ids) {
                if (is_array($taxonomy_ids)) {
                    $args['tax_query'][] = array(
                        'taxonomy' => $taxonomy,
                        'field' => 'term_id',
                        'terms' => $taxonomy_ids, //the taxonomy terms I'd like to dynamically query
                    );
                }
            }
            if (!$args['tax_query']) {
                unset($args['tax_query']);
            }
        }

        if ($load_more) {
            $jobs[$key] = new WP_Query($args);
        } else {
            $jobs[$key] = get_posts($args);
        }
    }

    return $jobs[$key];
}

function iwj_get_jobs_indeed($ide_publisher_id = '', $ide_query = '', $ide_location = '', $ide_job_type = '', $ide_country = '', $ide_from_item = '', $ide_max_item = '') {
    $api_url = 'http://api.indeed.com/ads/apisearch?publisher=' . $ide_publisher_id . '&q=' . urlencode($ide_query) . '&l=' . urlencode($ide_location) . '&sort=relevance&radius=&st=&jt=' . urlencode($ide_job_type) . '&start=' . urlencode($ide_from_item) . '&limit=' . urlencode($ide_max_item) . '&fromage=%20&filter=&latlong=&co=' . urlencode($ide_country) . '&&userip=' . urlencode(iwj_indeed_job_importer_user_ip_address()) . '&v=2&useragent=' . urlencode(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "Mozilla/5.0 (compatible; Konqueror/4.0; Microsoft Windows) KHTML/4.0.80 (like Gecko)");
    $content = iwj_indeed_job_importer_readFeeds($api_url);
    return $content;
}

function iwj_get_candidates_suggestion($filter = 'any', $include_ids = '', $exclude_ids = '', $limit = '', $order_by = 'date', $order = 'DESC') {
    $args = array(
        'posts_per_page' => ( $limit ? $limit : - 1 ),
        'post_type' => 'iwj_candidate',
        'post_status' => 'publish',
        'include' => $include_ids,
        'exclude' => $exclude_ids,
        'orderby' => $order_by,
        'order' => $order,
    );
    $args['meta_query'] = array('relation' => 'AND');

    if ($filter == 'recommend') {
        $candidate_suggestion_conditions = (array) iwj_option('candidate_suggestion_conditions');
        if ($candidate_suggestion_conditions && is_user_logged_in() && class_exists('IWJ_User')) {
            $user = IWJ_User::get_user();
            if ($user->is_employer()) {
                $employer = $user->get_employer();
                $employer_jobs = $user->get_job_ids();
                if ($employer) {
                    $categorie_ids = $type_ids = $skill_ids = $level_ids = array();

                    if (in_array('profile_category', $candidate_suggestion_conditions)) {
                        $categories = $employer->get_categories();
                        if ($categories) {
                            foreach ($categories as $category) {
                                $categorie_ids[] = $category->term_id;
                            }
                        }
                    }

                    if ($employer_jobs) {
                        foreach ($employer_jobs as $employer_job) {
                            $job = IWJ_Job::get_job($employer_job);

                            if (in_array('job_category', $candidate_suggestion_conditions)) {
                                $categories_job = $job->get_category();
                                if ($categories_job) {
                                    $categorie_ids[] = $categories_job->term_id;
                                }
                            }

                            if (in_array('job_type', $candidate_suggestion_conditions)) {
                                $types = $job->get_type();
                                if ($types) {
                                    $type_ids[] = $types->term_id;
                                }
                            }

                            if (in_array('job_gender', $candidate_suggestion_conditions)) {
                                $genders = $job->get_genders();
                            } else {
                                $genders = array();
                            }

                            if (in_array('job_language', $candidate_suggestion_conditions)) {
                                $languages = $job->get_languages();
                            } else {
                                $languages = array();
                            }

                            if (($genders && count($genders) ) || ($languages && count($languages))) {
                                $args['meta_query'] = array(
                                    'relation' => 'AND',
                                );
                                if ($genders && count($genders)) {
                                    $args['meta_query'][] = array(
                                        'relation' => 'OR',
                                        array(
                                            'key' => IWJ_PREFIX . 'gender',
                                            'compare' => 'NOT EXISTS'
                                        ),
                                        array(
                                            'key' => IWJ_PREFIX . 'gender',
                                            'value' => $genders,
                                            'compare' => 'IN'
                                        )
                                    );
                                }
                                if ($languages && count($languages)) {
                                    $args['meta_query'][] = array(
                                        'relation' => 'OR',
                                        array(
                                            'key' => IWJ_PREFIX . 'languages',
                                            'compare' => 'NOT EXISTS'
                                        ),
                                        array(
                                            'key' => IWJ_PREFIX . 'languages',
                                            'value' => $languages,
                                            'compare' => 'IN'
                                        )
                                    );
                                }
                            }

                            if (in_array('job_skill', $candidate_suggestion_conditions)) {
                                $skills = $job->get_skills();
                                if ($skills) {
                                    $skill_ids[] = $skills->term_id;
                                }
                            }

                            if (in_array('job_level', $candidate_suggestion_conditions)) {
                                $levels = $job->get_levels();
                                if ($levels) {
                                    $level_ids[] = $levels->term_id;
                                }
                            }
                        }
                    }

                    if ($categorie_ids || $type_ids || $skill_ids || $level_ids) {
                        $args['tax_query'] = array(
                            'relation' => 'OR'
                        );
                        if ($categorie_ids) {
                            $args['tax_query'][] = array(
                                'taxonomy' => 'iwj_cat',
                                'field' => 'term_id',
                                'terms' => $categorie_ids,
                            );
                        }
                        if ($type_ids) {
                            $args['tax_query'][] = array(
                                'taxonomy' => 'iwj_type',
                                'field' => 'term_id',
                                'terms' => $type_ids,
                            );
                        }
                        if ($skill_ids) {
                            $args['tax_query'][] = array(
                                'taxonomy' => 'iwj_skill',
                                'field' => 'term_id',
                                'terms' => $skill_ids,
                            );
                        }
                        if ($level_ids) {
                            $args['tax_query'][] = array(
                                'taxonomy' => 'iwj_level',
                                'field' => 'term_id',
                                'terms' => $level_ids,
                            );
                        }
                    }
                }
            }
        }
    }

    if ($filter == 'featured') {
        $args['meta_query'][] = array(
            array(
                'key' => IWJ_PREFIX . 'featured',
                'value' => 1,
                'compare' => '='
            )
        );
    }

    $args['meta_query'][] = array(
        'relation' => 'OR',
        array(
            'key' => IWJ_PREFIX . 'public_account',
            'compare' => 'NOT EXISTS' // doesn't work
        ),
        array(
            'key' => IWJ_PREFIX . 'public_account',
            'value' => 1,
            'compare' => '='
        )
    );

    $candidates = get_posts($args);

    return $candidates;
}

function iwj_get_avatar($id_or_email, $size = '', $default = '', $alt = '', $args = null) {

    $args = (array) $args;

    if (!isset($args['img_size']) || !$args['img_size']) {
        $args['img_size'] = 'inwave-avatar';

        if (!$size) {
            $size = 60;
        }
    }

    return get_avatar($id_or_email, $size, $default = '', $alt = '', $args);
}

function iwj_get_avatar_url($id_or_email, $args = null) {
    if (is_numeric($id_or_email))
        $user_id = (int) $id_or_email;
    elseif (is_string($id_or_email) && ( $user = get_user_by('email', $id_or_email) ))
        $user_id = $user->ID;
    elseif (is_object($id_or_email) && !empty($id_or_email->user_id))
        $user_id = (int) $id_or_email->user_id;

    if (empty($user_id))
        return get_avatar_url($id_or_email, $args);

    // fetch local avatar from meta and make sure it's properly ste
    $avatars = get_user_meta($user_id, IWJ_PREFIX . 'avatar', true);

    if (empty($avatars))
        return get_avatar_url($id_or_email, $args);

    if (is_numeric($avatars)) {
        $avatars = wp_get_attachment_image_src($avatars, 'full');
        if (empty($avatars[0]))
            return get_avatar_url($id_or_email, $args);

        return $avatars[0];
    }else {
        return $avatars;
    }
}

function iwj_count_jobs() {
    $count_jobs = get_transient('iwj_count_jobs');
    if ($count_jobs === false) {
        global $wpdb;
        $expired_job = iwj_option('show_expired_job');
        if (!$expired_job) {
            $sql = "SELECT COUNT(1) FROM {$wpdb->posts} AS p JOIN {$wpdb->postmeta} AS pm ON pm.post_id = p.ID
                        WHERE pm.meta_key = '" . IWJ_PREFIX . "expiry' AND (pm.meta_value = '' OR (pm.meta_value != '' AND CAST(pm.meta_value AS UNSIGNED) > " . current_time('timestamp') . ")) AND p.post_type = %s AND p.post_status IN ('publish')";
            $sql = $wpdb->prepare($sql, 'iwj_job');
            $total_jobs = $wpdb->get_var($sql);
            set_transient('iwj_count_jobs', (int) $total_jobs, DAY_IN_SECONDS);
        } else {
            $sql = "SELECT COUNT(1) FROM {$wpdb->posts} WHERE post_type = %s AND post_status IN ('publish')";
            $sql = $wpdb->prepare($sql, 'iwj_job');
            $total_jobs = $wpdb->get_var($sql);
            set_transient('iwj_count_jobs', (int) $total_jobs, MONTH_IN_SECONDS);
        }
    }

    return (int) get_transient('iwj_count_jobs');
}

function iwj_count_employers() {
    $count_employers = get_transient('iwj_count_employers');
    if ($count_employers === false) {
        global $wpdb;
        $sql = "SELECT COUNT(1) FROM {$wpdb->posts} WHERE post_type = %s AND post_status IN ('publish')";
        $sql = $wpdb->prepare($sql, 'iwj_employer');
        $total_jobs = $wpdb->get_var($sql);

        set_transient('iwj_count_employers', (int) $total_jobs, MONTH_IN_SECONDS);
    }

    return (int) get_transient('iwj_count_employers');
}

function iwj_count_candidates() {
    $count_candidates = get_transient('iwj_count_candidates');
    if ($count_candidates === false) {
        global $wpdb;
        $sql = "SELECT COUNT(1) FROM {$wpdb->posts} WHERE post_type = %s AND post_status IN ('publish')";
        $sql = $wpdb->prepare($sql, 'iwj_candidate');
        $total_jobs = $wpdb->get_var($sql);

        set_transient('iwj_count_candidates', (int) $total_jobs, MONTH_IN_SECONDS);
    }

    return (int) get_transient('iwj_count_candidates');
}

function iwj_woocommerce_checkout() {
    if (class_exists('Woocommerce') && iwj_option('woocommerce_checkout')) {
        return true;
    }

    return false;
}

function iwj_indeed_job_importer_readFeeds($url) {
    $indeed_content = array();
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, ( isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "Mozilla/5.0 (compatible; Konqueror/4.0; Microsoft Windows) KHTML/4.0.80 (like Gecko)"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $data = curl_exec($ch);
        curl_close($ch);
        if ($data) {
            $parsed_xml = simplexml_load_string($data);
            $i = 0;
            if ($parsed_xml->results->result) {
                foreach ($parsed_xml->results->result as $current) {
                    $jobkey = wp_filter_nohtml_kses($current->jobkey);
                    $jobtitle = wp_filter_nohtml_kses($current->jobtitle);
                    $company = wp_filter_nohtml_kses($current->company);
                    $city = wp_filter_nohtml_kses($current->city);
                    $state = wp_filter_nohtml_kses($current->state);
                    $country = wp_filter_nohtml_kses($current->country);
                    $description = wp_filter_nohtml_kses($current->snippet);
                    $url = wp_filter_nohtml_kses($current->url);
                    $formatted_loc = wp_filter_nohtml_kses($current->formattedLocation);
                    $formatted_loc_full = wp_filter_nohtml_kses($current->formattedLocationFull);
                    $relativetime = wp_filter_nohtml_kses($current->formattedRelativeTime);
                    $onmousedown = wp_filter_nohtml_kses($current->onmousedown);

                    $indeed_content[$i] = array(
                        'id' => $jobkey,
                        'title' => $jobtitle,
                        'company' => $company,
                        'city' => $city,
                        'state' => $state,
                        'country' => $country,
                        'description' => $description,
                        'formatted_loc' => $formatted_loc,
                        'formatted_loc_full' => $formatted_loc_full,
                        'url' => $url,
                        'relative_time' => $relativetime,
                        'onmousedown' => $onmousedown,
                    );
                    $i ++;
                }
            }
        }
    }

    return $indeed_content;
}

function iwj_indeed_job_importer_user_ip_address() {
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } else {
            $ip = getenv('REMOTE_ADDR');
        }
    }

    return $ip;
}

function iwj_set_view_post($post_id = '', $user_ID = '') {

    $post = get_post($post_id);

    if (!$user_ID) {
        $user_ID = get_current_user_id();
    }

    $allow_post_types = apply_filters('iwj_view_post_types', array(
        'iwj_job',
        'iwj_candidate',
        'iwj_employer'
    ));

    $should_count = false;

    if (in_array($post->post_type, $allow_post_types) && $post->post_status == 'publish') {
        $id = intval($post->ID);
        if (!$post_views = get_post_meta($post->ID, IWJ_PREFIX . 'views', true)) {
            $post_views = 0;
        }
        $view_type = 0;
        switch ($view_type) {
            case 0:
                $should_count = true;
                if (intval($user_ID) > 0 && $user_ID == $post->post_author) {
                    $should_count = false;
                }
                if ($should_count && isset($_COOKIE['iwj_view_post_' . $id]) && $_COOKIE['iwj_view_post_' . $id]) {
                    $should_count = false;
                }
                break;
            case 1:
                if (empty($_COOKIE[USER_COOKIE]) && intval($user_ID) === 0) {
                    $should_count = true;
                }
                break;
            case 2:
                if (intval($user_ID) > 0) {
                    $should_count = true;
                }
                break;
        }
        $bots = array
            (
            'Google Bot' => 'google'
            ,
            'MSN' => 'msnbot'
            ,
            'Alex' => 'ia_archiver'
            ,
            'Lycos' => 'lycos'
            ,
            'Ask Jeeves' => 'jeeves'
            ,
            'Altavista' => 'scooter'
            ,
            'AllTheWeb' => 'fast-webcrawler'
            ,
            'Inktomi' => 'slurp@inktomi'
            ,
            'Turnitin.com' => 'turnitinbot'
            ,
            'Technorati' => 'technorati'
            ,
            'Yahoo' => 'yahoo'
            ,
            'Findexa' => 'findexa'
            ,
            'NextLinks' => 'findlinks'
            ,
            'Gais' => 'gaisbo'
            ,
            'WiseNut' => 'zyborg'
            ,
            'WhoisSource' => 'surveybot'
            ,
            'Bloglines' => 'bloglines'
            ,
            'BlogSearch' => 'blogsearch'
            ,
            'PubSub' => 'pubsub'
            ,
            'Syndic8' => 'syndic8'
            ,
            'RadioUserland' => 'userland'
            ,
            'Gigabot' => 'gigabot'
            ,
            'Become.com' => 'become.com'
            ,
            'Baidu' => 'baiduspider'
            ,
            'so.com' => '360spider'
            ,
            'Sogou' => 'spider'
            ,
            'soso.com' => 'sosospider'
            ,
            'Yandex' => 'yandex'
        );
        $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        foreach ($bots as $name => $lookfor) {
            if (!empty($useragent) && ( stristr($useragent, $lookfor) !== false )) {
                $should_count = false;
                break;
            }
        }

        if ($should_count) {
            update_post_meta($id, IWJ_PREFIX . 'views', ( $post_views + 1));
        }
    }

    do_action('iwj_view_post', $post_id, $user_ID, $should_count);

    return $should_count;
}

/* $show_on_position
  1 on dashboard
  2 on mobile
  3 on topbar menu */

function iwj_get_dashboard_menus($show_on_position = 1) {
    $dashboard_url = iwj_get_page_permalink('dashboard');
    $user = IWJ_User::get_user();
    $menus = array(
        'overview' => array(
            'url' => add_query_arg(array('iwj_tab' => 'overview'), $dashboard_url),
            'title' => '<i class="ion-ios-contact"></i>' . __('Dashboard', 'iwjob')
        )
    );

    if ($user && $user->is_employer()) {
        if ($show_on_position == 1 || $show_on_position == 2) {
            $menus['new-job'] = array(
                'url' => add_query_arg(array('iwj_tab' => 'new-job'), $dashboard_url),
                'title' => '<i class="ion-ios-compose"></i>' . __('Add New Class', 'iwjob')
            );
        }
        $menus['jobs'] = array(
            'url' => add_query_arg(array('iwj_tab' => 'jobs'), $dashboard_url),
            'title' => '<i class="ion-briefcase"></i>' . __('All Classes', 'iwjob')
        );
        $menus['applications'] = array(
            'url' => add_query_arg(array('iwj_tab' => 'applications'), $dashboard_url),
            'title' => '<i class="ion-ios-people"></i>' . __('Applications', 'iwjob')
        );
       /*if (!current_user_can('administrator')) {
            if (iwj_option('submit_job_mode') == '1' || iwj_option('submit_job_mode') == '2') {
                $menus['packages'] = array(
                    'url' => add_query_arg(array('iwj_tab' => 'packages'), $dashboard_url),
                    'title' => '<i class="fa fa-hdd-o"></i>' . __('Packages', 'iwjob')
                );
            } elseif (iwj_option('submit_job_mode') == '3') {
                $menus['current-plan'] = array(
                    'url' => add_query_arg(array('iwj_tab' => 'current-plan'), $dashboard_url),
                    'title' => '<i class="fa fa-hdd-o"></i>' . __('Your Plan', 'iwjob')
                );
            }
        }*/

      /*  if (!iwj_option('view_free_resum') && !current_user_can('privilege_view_resum')) {
            $menus['resume-packages'] = array(
                'url' => add_query_arg(array('iwj_tab' => 'resume-packages'), $dashboard_url),
                'title' => '<i class="fa fa-trophy"></i>' . __('Resume Packages', 'iwjob')
            );
        }*/
        if (iwj_option('view_free_resum') || current_user_can('privilege_view_resum')) {
            $menus['save-resumes'] = array(
                'url' => add_query_arg(array('iwj_tab' => 'save-resumes'), $dashboard_url),
                'title' => '<i class="ion-ios-bookmarks"></i>' . __('Saved Profiles', 'iwjob')
            );
        } else {
            $menus['view-resumes'] = array(
                'url' => add_query_arg(array('iwj_tab' => 'view-resumes'), $dashboard_url),
                'title' => '<i class="fa fa-flag-checkered"></i>' . __('Viewed Profiles', 'iwjob')
            );
        }

        if ($show_on_position == 1 || $show_on_position == 2) {
            $show_order = false;
            if (!current_user_can('administrator') && (!iwj_woocommerce_checkout() && $user->count_orders())) {
                $show_order = true;
                $menus['orders'] = array(
                    'url' => add_query_arg(array('iwj_tab' => 'orders'), $dashboard_url),
                    'title' => '<i class="ion-android-cart"></i>' . __('Orders', 'iwjob')
                );
            }

            if ((class_exists('Woocommerce') && iwj_option('include_my_account_woocommerce')) || iwj_woocommerce_checkout()) {
                $customer_orders = wc_get_orders(apply_filters('woocommerce_my_account_my_orders_query', array('customer' => get_current_user_id(), 'page' => 1, 'paginate' => true)));
                $has_orders = 0 < $customer_orders->total;
                $has_downloads = false;
                if (iwj_option('include_my_account_woocommerce')) {
                    $downloads = WC()->customer->get_downloadable_products();
                    $has_downloads = (bool) $downloads;
                }
                if ($has_orders) {
                    $menus['w-orders'] = array(
                        'url' => add_query_arg(array('iwj_tab' => 'w-orders'), $dashboard_url),
                        'title' => '<i class="ion-android-cart"></i>' . ($show_order ? __('Shop Orders', 'iwjob') : __('Orders', 'iwjob'))
                    );
                }
                if ($has_downloads) {
                    $menus['downloads'] = array(
                        'url' => add_query_arg(array('iwj_tab' => 'downloads'), $dashboard_url),
                        'title' => '<i class="fa fa-cloud-download"></i>' . __('Downloads', 'iwjob')
                    );
                }
            }
        }

        $disable_review = iwj_option('disable_review');
        if (!$disable_review) {
            $menus['reviews'] = array(
                'url' => add_query_arg(array('iwj_tab' => 'reviews'), $dashboard_url),
                'title' => '<i class="ion-stats-bars"></i>' . __('Reviews', 'iwjob')
            );
        }
    }

    if ($user && $user->is_candidate()) {

        $menus['submited-applications'] = array(
            'url' => add_query_arg(array('iwj_tab' => 'submited-applications'), $dashboard_url),
            'title' => '<i class="fa fa-newspaper-o"></i>' . __('Applied Classes', 'iwjob')
        );
        /*$menus['follows'] = array(
            'url' => add_query_arg(array('iwj_tab' => 'follows'), $dashboard_url),
            'title' => '<i class="fa fa-briefcase"></i>' . __('Follow Companies', 'iwjob')
        );*/
        if (!iwj_option('apply_job_mode')) {
            $menus['apply-job-package'] = array(
                'url' => add_query_arg(array('iwj_tab' => 'apply-job-package'), $dashboard_url),
                'title' => '<i class="fa fa-paper-plane"></i>' . __('Apply Classes Packages', 'iwjob')
            );
        }
        $menus['save-jobs'] = array(
            'url' => add_query_arg(array('iwj_tab' => 'save-jobs'), $dashboard_url),
            'title' => '<i class="fa fa-heart"></i>' . __('Saved Classes', 'iwjob')
        );
        $menus['alerts'] = array(
            'url' => add_query_arg(array('iwj_tab' => 'alerts'), $dashboard_url),
            'title' => '<i class="fa fa-envelope-o"></i>' . __('Alerts', 'iwjob')
        );
        if ($show_on_position == 1 || $show_on_position == 2) {
            $show_order_c = false;
            if (!current_user_can('administrator') && (!iwj_woocommerce_checkout() && $user->count_orders())) {
                $show_order_c = true;
                $menus['orders'] = array(
                    'url' => add_query_arg(array('iwj_tab' => 'orders'), $dashboard_url),
                    'title' => '<i class="fa fa-cart-plus"></i>' . __('Orders', 'iwjob')
                );
            }
            if ((class_exists('Woocommerce') && iwj_option('include_my_account_woocommerce')) || iwj_woocommerce_checkout()) {
                $customer_orders = wc_get_orders(apply_filters('woocommerce_my_account_my_orders_query', array('customer' => get_current_user_id(), 'page' => 1, 'paginate' => true)));
                $has_orders = 0 < $customer_orders->total;
                $has_downloads = false;
                if (iwj_option('include_my_account_woocommerce')) {
                    $downloads = WC()->customer->get_downloadable_products();
                    $has_downloads = (bool) $downloads;
                }
                if ($has_orders) {
                    $menus['w-orders'] = array(
                        'url' => add_query_arg(array('iwj_tab' => 'w-orders'), $dashboard_url),
                        'title' => '<i class="fa fa-cart-plus"></i>' . ($show_order_c ? __('Shop Orders', 'iwjob') : __('Orders', 'iwjob'))
                    );
                }
                if ($has_downloads) {
                    $menus['downloads'] = array(
                        'url' => add_query_arg(array('iwj_tab' => 'downloads'), $dashboard_url),
                        'title' => '<i class="fa fa-cloud-download"></i>' . __('Downloads', 'iwjob')
                    );
                }
            }
        }
        $menus['my-reviews'] = array(
            'url' => add_query_arg(array('iwj_tab' => 'my-reviews'), $dashboard_url),
            'title' => '<i class="fa fa-star"></i>' . __('My Reviews', 'iwjob')
        );
    }

    $menus['logout'] = array(
        'url' => wp_logout_url(home_url('/')),
        'title' => '<i class="fa fa-power-off"></i>' . __('Logout', 'iwjob')
    );

    return apply_filters('iwj_get_dashboard_menus', $menus, $show_on_position);
}

function iwj_get_username($user_name, $count = 0) {
    if (is_email($user_name)) {
        $_user_name = explode('@', $user_name);
        if (count($_user_name) > 1) {
            $user_name = $_user_name[0];
        }
    }

    $username = sanitize_user($user_name, true);
    if ($count > 0) {
        $username .= $count;
    }

    $user = get_user_by('login', $username);
    if ($user) {
        $count++;
        $username = iwj_get_username($user_name, $count);
    }

    return apply_filters('iwj_get_username', $username, $username, $count);
}

function iwj_stripslashes($array) {
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = iwj_stripslashes($value);
        } else {
            $array[$key] = stripslashes($value);
        }
    }

    return $array;
}

if (!function_exists('iwj_folderToZip')) {

    function iwj_folderToZip($folder, &$zipFile, $exclusiveLength) {
        $handle = opendir($folder);
        while (false !== $f = readdir($handle)) {
            if ($f != '.' && $f != '..') {
                $filePath = "$folder/$f";
                // Remove prefix from file path before add to zip.
                $localPath = substr($filePath, $exclusiveLength);
                if (is_file($filePath)) {
                    $zipFile->addFile($filePath, $localPath);
                } elseif (is_dir($filePath)) {
                    $zipFile->addEmptyDir($localPath);
                    $this->iwj_folderToZip($filePath, $zipFile, $exclusiveLength);
                }
            }
        }
        closedir($handle);
    }

}

if (!function_exists('iwj_zipDir')) {

    function iwj_zipDir($sourcePath, $outZipPath) {
        $pathInfo = pathInfo($sourcePath);
        $parentPath = $pathInfo['dirname'];
        $dirName = $pathInfo['basename'];

        $z = new ZipArchive();
        $z->open($outZipPath, ZIPARCHIVE::CREATE);
        $z->addEmptyDir($dirName);
        iwj_folderToZip($sourcePath, $z, strlen("$parentPath/"));
        $z->close();
    }

}

if (!function_exists('iwj_get_image_id_by_url')) {

    function iwj_get_image_id_by_url($url) {
        global $wpdb;
        $img_id = 0;
        preg_match('|' . get_site_url() . '|i', $url, $matches);
        if (isset($matches) and 0 < count($matches)) {
            $url = preg_replace('/([^?]+).*/', '\1', $url);
            $guid = preg_replace('/(.+)-\d+x\d+\.(\w+)/', '\1.\2', $url);
            $img_id = $wpdb->get_var($wpdb->prepare("SELECT `ID` FROM $wpdb->posts WHERE `guid` = '%s'", $guid));

            if ($img_id) {
                $img_id = intval($img_id);
            }
        }

        return $img_id;
    }

}

if (!function_exists('iwj_random_number')) {

    function iwj_random_number($Min, $Max, $round = 0) {
        //validate input
        if ($Min > $Max) {
            $min = $Max;
            $max = $Min;
        } else {
            $min = $Min;
            $max = $Max;
        }
        $randomfloat = $min + mt_rand() / mt_getrandmax() * ( $max - $min );
        if ($round > 0) {
            $randomfloat = round($randomfloat, $round);
        }

        return $randomfloat;
    }

}

if (!function_exists('iwj_format_bytes')) {

    function iwj_format_bytes($bytes, $precision = 2) {
        $units = array('KB', 'MB', 'GB', 'TB');
        while (count($units) > 0 && $bytes > 1024) {
            $precision = array_shift($units);
            $bytes /= 1024;
        }
        return ($bytes | 0) . $precision;
    }

}

function user_last_login($user_login, $user) {
    update_user_meta($user->ID, '_last_login', time());
}

add_action('wp_login', 'user_last_login', 10, 2);
