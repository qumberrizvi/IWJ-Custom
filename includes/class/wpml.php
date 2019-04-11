<?php
Class IWJ_WPML{
    static function init(){
        add_filter( 'get_terms', array(__CLASS__, 'get_terms'), 10, 4 );
        add_filter( 'get_term', array(__CLASS__, 'get_term'), 10, 2 );
        if(!is_blog_admin()){
            add_filter( 'the_title', array(__CLASS__, 'the_title'), 10, 2);
        }
        add_filter( 'iwj_package_sub_title', array(__CLASS__, 'package_sub_title'), 10, 2);
        add_filter( 'iwj_get_cats', array(__CLASS__, 'get_cats'));
    }

    static function get_terms($terms, $taxonomy, $query_vars, $term_query){
        global $sitepress;
        if($sitepress){
            $lang = $sitepress->get_current_language();
            foreach($terms as $key=>$term){
                if(is_object($term) && strpos($term->taxonomy, "iwj_") === 0){
                    $type = str_replace('iwj_', '', $term->taxonomy);
                    if(iwj_option('translate_'.$type)){
                        $translate = iwj_get_term_translate($term->term_id, 'title', $lang);
                        if($translate){
                            $terms[$key]->name = $translate->translate_string;
                        }
                    }
                }elseif(is_array($term) && strpos($term['taxonomy'], "iwj_") === 0){
                    $type = str_replace('iwj_', '', $term['taxonomy']);
                    if(iwj_option('translate_'.$type)) {
                        $translate = iwj_get_term_translate($term['term_id'], 'title', $lang);
                        if ($translate) {
                            $terms[$key]['name'] = $translate->translate_string;
                        }
                    }
                }
            }
        }

        return $terms;
    }

    static function get_term($term, $taxonomy){
        global $sitepress;
        if($sitepress){
            $lang = $sitepress->get_current_language();
            if(strpos($taxonomy, "iwj_") === 0 && iwj_option('translate_'.str_replace('iwj_', '', $taxonomy))){
                $translate = iwj_get_term_translate($term->term_id, 'title', $lang);
                if($translate){
                    $term->name = $translate->translate_string;
                }
            }
        }

        return $term;
    }

    static function the_title($title, $id){
        global $sitepress;
        //var_dump(get_post_type($id));
        if($sitepress && (get_post_type($id) == 'iwj_package' || get_post_type($id) == 'iwj_resum_package' || get_post_type($id) == 'iwj_apply_package' || get_post_type($id) == 'product') && iwj_option('translate_package')){
            $lang = $sitepress->get_current_language();
            $translate = iwj_get_post_translate($id, 'title', $lang);
            if($translate){
                $title = $translate->translate_string;
            }
        }

        return $title;
    }

    static function package_sub_title($title, $id){
        global $sitepress;
        if($sitepress && iwj_option('translate_package')){
            $lang = $sitepress->get_current_language();
            $translate = iwj_get_post_translate($id, 'subtitle', $lang);
            if($translate){
                $title = $translate->translate_string;
            }
        }

        return $title;
    }

    static function get_cats($categories){
        global $sitepress;
        if($sitepress && iwj_option('translate_cat')){
            foreach($categories as $key=>$category){
                $lang = $sitepress->get_current_language();
                $translate = iwj_get_term_translate($category->term_id, 'title', $lang);
                if($translate){
                    $categories[$key]->name = $translate->translate_string;
                }
            }
        }

        return $categories;
    }

}

IWJ_WPML::init();