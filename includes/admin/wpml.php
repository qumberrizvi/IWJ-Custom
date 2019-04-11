<?php
Class IWJ_Admin_WPML{
    static $fields = array();

    static public function init(){
        if(iwj_option('translate_cat')){
            add_action( 'iwj_cat_add_form_fields', array(__CLASS__, 'add_form_fields'), 10, 1 );
            add_action( 'iwj_cat_edit_form_fields', array(__CLASS__, 'edit_form_fields'), 10, 2 );

            add_action( 'edited_iwj_cat', array(__CLASS__, 'save_custom_meta'), 10, 2 );
            add_action( 'create_iwj_cat', array(__CLASS__, 'save_custom_meta'), 10, 2 );
        }

        if(iwj_option('translate_type')){
            add_action( 'iwj_type_add_form_fields', array(__CLASS__, 'add_form_fields'), 10, 1 );
            add_action( 'iwj_type_edit_form_fields', array(__CLASS__, 'edit_form_fields'), 10, 2 );

            add_action( 'edited_iwj_type', array(__CLASS__, 'save_custom_meta'), 10, 2 );
            add_action( 'create_iwj_type', array(__CLASS__, 'save_custom_meta'), 10, 2 );
        }

        if(iwj_option('translate_level')){
            add_action( 'iwj_level_add_form_fields', array(__CLASS__, 'add_form_fields'), 10, 1 );
            add_action( 'iwj_level_edit_form_fields', array(__CLASS__, 'edit_form_fields'), 10, 2 );

            add_action( 'edited_iwj_level', array(__CLASS__, 'save_custom_meta'), 10, 2 );
            add_action( 'create_iwj_level', array(__CLASS__, 'save_custom_meta'), 10, 2 );
        }

        if(iwj_option('translate_salary')){
            add_action( 'iwj_salary_add_form_fields', array(__CLASS__, 'add_form_fields'), 10, 1 );
            add_action( 'iwj_salary_edit_form_fields', array(__CLASS__, 'edit_form_fields'), 10, 2 );

            add_action( 'edited_iwj_salary', array(__CLASS__, 'save_custom_meta'), 10, 2 );
            add_action( 'create_iwj_salary', array(__CLASS__, 'save_custom_meta'), 10, 2 );
        }

        if(iwj_option('translate_location')){
            add_action( 'iwj_location_add_form_fields', array(__CLASS__, 'add_form_fields'), 10, 1 );
            add_action( 'iwj_location_edit_form_fields', array(__CLASS__, 'edit_form_fields'), 10, 2 );

            add_action( 'edited_iwj_location', array(__CLASS__, 'save_custom_meta'), 10, 2 );
            add_action( 'create_iwj_location', array(__CLASS__, 'save_custom_meta'), 10, 2 );
        }

        if(iwj_option('translate_package')){
            add_action('admin_menu', array( __CLASS__ , 'register_metabox'));
            add_action('save_post', array( __CLASS__ , 'save_post'), 99);

            add_filter( 'woocommerce_product_data_tabs', array( __CLASS__ , 'add_iwj_translate_tab') , 99 , 1 );
            add_action( 'woocommerce_product_data_panels', array( __CLASS__ , 'add_my_custom_product_data_fields') );
            add_action( 'woocommerce_process_product_meta', array( __CLASS__ , 'woocommerce_process_product_meta_fields_save') );
        }

        add_filter( 'iwj_plugin_settings', array(__CLASS__, 'multiple_language_settings'), 99, 1 );
    }

    static public function init_fields(){
        if(!self::$fields){
            $langugages = iwj_get_wpml_languages();
            global $sitepress;
            $default_language = $sitepress->get_default_language();
            if($langugages){
                foreach($langugages as $langugage){
                    if($langugage['code'] == $default_language){
                        continue;
                    }
                    self::$fields[] = array(
                        'name' => sprintf(__( 'Name for %s', 'iwjob' ), $langugage['english_name']),
                        'id'   => IWJ_PREFIX.'title_'.$langugage['code'],
                        'lang_code'   => $langugage['code'],
                        'type' => 'text',
                    );
                }
            }
        }
    }

    static function add_form_fields($taxonomy){
        if(defined('ICL_LANGUAGE_CODE')){
        $langugages = iwj_get_wpml_languages();
        global $sitepress;
        $default_language = $sitepress->get_default_language();
        wp_enqueue_script("jquery-ui-core");
        wp_enqueue_script("jquery-ui-tabs");
        ?>
        <div>
            <div><?php echo __('Translate Name', 'iwjob'); ?></div>
            <div id="iwj-translate-term-tabs">
                <ul>
                    <?php
                    foreach($langugages as $langugage){
                        if($langugage['code'] == $default_language){
                            continue;
                        }
                        echo '<li><a href="#iwj-translate-term-name-'.$langugage['code'].'"><img src="'.$langugage['country_flag_url'].'">'.$langugage['english_name'].'</a></li>';
                    }
                    ?>
                </ul>
                <?php
                foreach($langugages as $langugage){
                    if($langugage['code'] == $default_language){
                        continue;
                    }

                    echo '<div id="iwj-translate-term-name-'.$langugage['code'].'">';
                    $field = array(
                        'name' => '',
                        'parent_tag' => 'div',
                        'id'   => IWJ_PREFIX.'title_'.$langugage['code'],
                        'placeholder'   => sprintf(__('Name for %s', 'iwjob'), $langugage['english_name']),
                        'type' => 'text',
                    );
                    $field = IWJMB_Field::call( 'normalize', $field );
                    $meta = '';
                    IWJMB_Field::input($field, $meta );
                    echo '</div>';
                }
                ?>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $('#iwj-translate-term-tabs').tabs();
                })
            </script>
        </div>
    <?php }}

    static function edit_form_fields($term, $taxonomy){
        if(defined('ICL_LANGUAGE_CODE')){
            $langugages = iwj_get_wpml_languages();
            global $sitepress;
            $default_language = $sitepress->get_default_language();
            wp_enqueue_script("jquery-ui-core");
            wp_enqueue_script("jquery-ui-tabs");
            ?>
            <tr>
            <th><label><?php echo __('Translate Name', 'iwjob'); ?></label></th>
            <td>
                <div id="iwj-translate-term-tabs">
                <ul>
                    <?php
                    foreach($langugages as $langugage){
                        if($langugage['code'] == $default_language){
                            continue;
                        }
                        echo '<li><a href="#iwj-translate-term-name-'.$langugage['code'].'"><img src="'.$langugage['country_flag_url'].'">'.$langugage['english_name'].'</a></li>';
                    }
                    ?>
                </ul>
                <?php
                foreach($langugages as $langugage){
                    if($langugage['code'] == $default_language){
                        continue;
                    }

                    echo '<div id="iwj-translate-term-name-'.$langugage['code'].'">';
                    $field = array(
                        'name' => '',
                        'parent_tag' => 'div',
                        'id'   => IWJ_PREFIX.'title_'.$langugage['code'],
                        'placeholder'   => sprintf(__('Name for %s', 'iwjob'), $langugage['english_name']),
                        'type' => 'text',
                    );
                    $field = IWJMB_Field::call( 'normalize', $field );
                    $meta = iwj_get_term_translate($term->term_id, 'title', $langugage['code']);
                    if($meta){
                        $meta = $meta->translate_string;
                    }
                    IWJMB_Field::input($field, $meta );
                    echo '</div>';
                }
                ?>
                </div>
                <script type="text/javascript">
                    jQuery(document).ready(function ($) {
                        $('#iwj-translate-term-tabs').tabs();
                    })
                </script>
            </td></tr>
    <?php }}

    static function save_custom_meta($term_id, $taxonomy){
        if(defined('ICL_LANGUAGE_CODE')){
            self::init_fields();
            foreach (self::$fields as $field){
                $field = IWJMB_Field::call( 'normalize', $field );

                $single = $field['clone'] || ! $field['multiple'];
                $old    = '';
                $new    = isset( $_POST[ $field['id'] ] ) ? $_POST[ $field['id'] ] : ( $single ? '' : array() );
                // Allow field class change the value
                if ( $field['clone'] ) {
                    $new = IWJMB_Clone::value( $new, $old, $term_id, $field );
                } else {
                    $new = IWJMB_Field::call( $field, 'value', $new, $old, $term_id );
                    $new = IWJMB_Field::call( $field, 'sanitize_value', $new);
                }
                // Call defined method to save meta value, if there's no methods, call common one
                iwj_update_term_translate($term_id, $field['lang_code'], 'title', $new);
            }
        }
    }

    static function register_metabox(){
        if(defined('ICL_LANGUAGE_CODE')) {
            add_meta_box('iwj-translate-package-box', __('Translation', 'iwjob'), array(__CLASS__, 'translate_metabox_html'), 'iwj_package', 'normal', 'high');
            add_meta_box('iwj-translate-resume-box', __('Translation', 'iwjob'), array(__CLASS__, 'translate_metabox_html'), 'iwj_resum_package', 'normal', 'high');
            add_meta_box('iwj-translate-apply-job-box', __('Translation', 'iwjob'), array(__CLASS__, 'translate_metabox_html'), 'iwj_apply_package', 'normal', 'high');
        }
    }

    static function translate_metabox_html(){
        global $post;
        $post_id = $post->ID;
        wp_enqueue_script("jquery-ui-core");
        wp_enqueue_script("jquery-ui-tabs");
        //wp_enqueue_script("jquery-ui-accordion");
        //wp_enqueue_style("jquery-ui-accordion");
        ?>
        <div class="iwj-translate-metabox wp-clearfix">
            <table class="form-table">
                <tr>
                    <th class="iwjmb-label"><label for="user_id"><?php echo __('Translate Title', 'iwjob'); ?></label></th>
                    <td colspan="1">
                        <?php
                        $langugages = iwj_get_wpml_languages();
                        global $sitepress;
                        $default_language = $sitepress->get_default_language();
                        ?>
                        <div id="iwj-translate-title-tabs">
                            <ul>
                                <?php
                                foreach($langugages as $langugage){
                                    if($langugage['code'] == $default_language){
                                        continue;
                                    }
                                    echo '<li><a href="#iwj-translate-title-'.$langugage['code'].'"><img src="'.$langugage['country_flag_url'].'">'.$langugage['english_name'].'</a></li>';
                                }
                                ?>
                            </ul>
                            <?php
                            foreach($langugages as $langugage){
                                if($langugage['code'] == $default_language){
                                    continue;
                                }

                                echo '<div id="iwj-translate-title-'.$langugage['code'].'">';
                                $field = array(
                                    'name' => '',
                                    'parent_tag' => 'div',
                                    'id'   => IWJ_PREFIX.'title_'.$langugage['code'],
                                    'placeholder'   => sprintf(__('Title for %s', 'iwjob'), $langugage['english_name']),
                                    'type' => 'text',
                                );
                                $field = IWJMB_Field::call( 'normalize', $field );
                                $meta = iwj_get_post_translate($post_id, 'title', $langugage['code']);
                                if($meta){
                                    $meta = $meta->translate_string;
                                }
                                IWJMB_Field::input($field, $meta );
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="iwjmb-label"><label for="user_id"><?php echo __('Translate Sub Title', 'iwjob'); ?></label></th>
                    <td colspan="1">
                        <div id="iwj-translate-subtitle-tabs">
                            <ul>
                                <?php
                                foreach($langugages as $langugage){
                                    if($langugage['code'] == $default_language){
                                        continue;
                                    }
                                    echo '<li><a href="#iwj-translate-subtitle-'.$langugage['code'].'"><img src="'.$langugage['country_flag_url'].'">'.$langugage['english_name'].'</a></li>';
                                }
                                ?>
                            </ul>
                            <?php
                            foreach($langugages as $langugage){
                                if($langugage['code'] == $default_language){
                                    continue;
                                }

                                echo '<div id="iwj-translate-subtitle-'.$langugage['code'].'">';
                                $field = array(
                                    'name' => '',
                                    'parent_tag' => 'div',
                                    'id'   => IWJ_PREFIX.'subtitle_'.$langugage['code'],
                                    'placeholder'   => sprintf(__('Sub Title for %s', 'iwjob'), $langugage['english_name']),
                                    'type' => 'text',
                                );
                                $field = IWJMB_Field::call( 'normalize', $field );
                                $meta = iwj_get_post_translate($post_id, 'subtitle', $langugage['code']);
                                if($meta){
                                    $meta = $meta->translate_string;
                                }
                                IWJMB_Field::input($field, $meta );
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </td>
                </tr>
            </table>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $('#iwj-translate-title-tabs').tabs();
                    <?php if($post->post_type == 'iwj_job'){ ?>
                    $('#iwj-translate-content-tabs').tabs();
                    <?php }else{ ?>
                    $('#iwj-translate-subtitle-tabs').tabs();
                <?php } ?>
                })
            </script>
        </div>
        <?php
    }

    static function save_post($post_id){
        if(isset($_POST) && $_POST && is_blog_admin() && !defined( 'DOING_AJAX' )) {
            if (get_post_type($post_id) == 'iwj_job' || get_post_type($post_id) == 'iwj_package' || get_post_type($post_id) == 'iwj_resum_package' || get_post_type($post_id) == 'iwj_apply_package' ) {

                $langugages = iwj_get_wpml_languages();
                global $sitepress;
                $default_language = $sitepress->get_default_language();
                foreach($langugages as $langugage){
                    if($langugage['code'] == $default_language){
                        continue;
                    }

                    //update title;
                    $field = array(
                        'id'   => IWJ_PREFIX.'title_'.$langugage['code'],
                        'type' => 'text',
                    );

                    $field = IWJMB_Field::call( 'normalize', $field );

                    $single = $field['clone'] || ! $field['multiple'];
                    $old    = '';
                    $new    = isset( $_POST[ $field['id'] ] ) ? $_POST[ $field['id'] ] : ( $single ? '' : array() );
                    // Allow field class change the value
                    if ( $field['clone'] ) {
                        $new = IWJMB_Clone::value( $new, $old, $post_id, $field );
                    } else {
                        $new = IWJMB_Field::call( $field, 'value', $new, $old, $post_id );
                        $new = IWJMB_Field::call( $field, 'sanitize_value', $new);
                    }

                    iwj_update_post_translate($post_id, $langugage['code'], 'title', $new);

                    if(get_post_type($post_id) == 'iwj_job'){
                        //update content;
                        $field = array(
                            'id'   => IWJ_PREFIX.'content_'.$langugage['code'],
                            'type' => 'wysiwyg',
                        );

                        $field = IWJMB_Field::call( 'normalize', $field );

                        $single = $field['clone'] || ! $field['multiple'];
                        $old    = '';
                        $new    = isset( $_POST[ $field['id'] ] ) ? $_POST[ $field['id'] ] : ( $single ? '' : array() );
                        // Allow field class change the value
                        if ( $field['clone'] ) {
                            $new = IWJMB_Clone::value( $new, $old, $post_id, $field );
                        } else {
                            $new = IWJMB_Field::call( $field, 'value', $new, $old, $post_id );
                            $new = IWJMB_Field::call( $field, 'sanitize_value', $new);
                        }

                        iwj_update_post_translate($post_id, $langugage['code'], 'content', $new);
                    }else{
                        //update content;
                        $field = array(
                            'id'   => IWJ_PREFIX.'subtitle_'.$langugage['code'],
                            'type' => 'text',
                        );

                        $field = IWJMB_Field::call( 'normalize', $field );

                        $single = $field['clone'] || ! $field['multiple'];
                        $old    = '';
                        $new    = isset( $_POST[ $field['id'] ] ) ? $_POST[ $field['id'] ] : ( $single ? '' : array() );
                        // Allow field class change the value
                        if ( $field['clone'] ) {
                            $new = IWJMB_Clone::value( $new, $old, $post_id, $field );
                        } else {
                            $new = IWJMB_Field::call( $field, 'value', $new, $old, $post_id );
                            $new = IWJMB_Field::call( $field, 'sanitize_value', $new);
                        }

                        iwj_update_post_translate($post_id, $langugage['code'], 'subtitle', $new);
                    }
                }
            }
        }
    }


    static function multiple_language_settings($def){
        if(!defined('ICL_LANGUAGE_CODE')){
            return $def;
        }

        $settings = array(
        'multiple_languages' => array(
            'name'    => __( 'Multiple Languages', 'iwjob' ),
            'options' => array(
                array(
                    'name' => __( 'Settings', 'iwjob' ),
                    'options' => array(
                        array(
                            'id'      => 'show_languages_flag',
                            'name'    => esc_html__( 'Show language flags', 'iwjob' ),
                            'desc'    => esc_html__( 'Show language flags on top bar menu', 'iwjob' ),
                            'type'    => 'select',
                            'std'     => '1',
                            'options' => array(
                                '1' => __( 'Yes', 'iwjob' ),
                                ''  => __( 'No', 'iwjob' ),
                            ),
                        ),
                        array(
                            'id'      => 'translate_package',
                            'name'    => esc_html__( 'Allow translate package', 'iwjob' ),
                            'type'    => 'select',
                            'std'     => '',
                            'options' => array(
                                ''  => __( 'No', 'iwjob' ),
                                '1' => __( 'Yes', 'iwjob' ),
                            ),
                        ),array(
                            'id'      => 'translate_cat',
                            'name'    => esc_html__( 'Allow translate category', 'iwjob' ),
                            'type'    => 'select',
                            'std'     => '',
                            'options' => array(
                                ''  => __( 'No', 'iwjob' ),
                                '1' => __( 'Yes', 'iwjob' ),
                            ),
                        ),
                        array(
                            'id'      => 'translate_type',
                            'name'    => esc_html__( 'Allow translate type', 'iwjob' ),
                            'type'    => 'select',
                            'std'     => '',
                            'options' => array(
                                ''  => __( 'No', 'iwjob' ),
                                '1' => __( 'Yes', 'iwjob' ),
                            ),
                        ),
                        array(
                            'id'      => 'translate_level',
                            'name'    => esc_html__( 'Allow translate level', 'iwjob' ),
                            'type'    => 'select',
                            'std'     => '',
                            'options' => array(
                                ''  => __( 'No', 'iwjob' ),
                                '1' => __( 'Yes', 'iwjob' ),
                            ),
                        ),
                        array(
                            'id'      => 'translate_salary',
                            'name'    => esc_html__( 'Allow translate salary', 'iwjob' ),
                            'type'    => 'select',
                            'std'     => '',
                            'options' => array(
                                ''  => __( 'No', 'iwjob' ),
                                '1' => __( 'Yes', 'iwjob' ),
                            ),
                        ),
                        array(
                            'id'      => 'translate_location',
                            'name'    => esc_html__( 'Allow translate location', 'iwjob' ),
                            'type'    => 'select',
                            'std'     => '',
                            'options' => array(
                                ''  => __( 'No', 'iwjob' ),
                                '1' => __( 'Yes', 'iwjob' ),
                            ),
                        ),
                    )
                ),
            ),
        ),
        );

        return array_merge( $def, $settings );
    }

    static function add_iwj_translate_tab( $product_data_tabs ) {
        if(defined('ICL_LANGUAGE_CODE')){
            global $woocommerce, $post;
            if($post){
                $product_type = get_post_meta($post->ID, IWJ_PREFIX.'product_type');
                if($product_type){
                    $product_data_tabs['iwj-translate-tab'] = array(
                        'label' => __( 'Translate', 'iwjob' ),
                        'target' => 'iwj_translate_product_data',
                    );
                }
            }
        }

        return $product_data_tabs;
    }

    static function add_my_custom_product_data_fields() {
        if(defined('ICL_LANGUAGE_CODE')){
            global $woocommerce, $post;
            if($post){
                $product_type = get_post_meta($post->ID, IWJ_PREFIX.'product_type');
                if($product_type){
                    $langugages = iwj_get_wpml_languages();
                    global $sitepress;
                    $default_language = $sitepress->get_default_language();
                    wp_enqueue_script("jquery-ui-core");
                    wp_enqueue_script("jquery-ui-tabs");
                    ?>
                    <!-- id below must match target registered in above add_my_custom_product_data_tab function -->
                    <div id="iwj_translate_product_data" class="panel woocommerce_options_panel">
                        <div id="iwj-translate-term-tabs">
                            <ul>
                                <?php
                                foreach($langugages as $langugage){
                                    if($langugage['code'] == $default_language){
                                        continue;
                                    }
                                    echo '<li><a href="#iwj-translate-term-name-'.$langugage['code'].'"><img src="'.$langugage['country_flag_url'].'">'.$langugage['english_name'].'</a></li>';
                                }
                                ?>
                            </ul>
                            <?php
                            foreach($langugages as $langugage){
                                if($langugage['code'] == $default_language){
                                    continue;
                                }

                                echo '<div id="iwj-translate-term-name-'.$langugage['code'].'">';
                                $field = array(
                                    'name' => '',
                                    'parent_tag' => 'div',
                                    'id'   => IWJ_PREFIX.'title_'.$langugage['code'],
                                    'placeholder'   => sprintf(__('Title for %s', 'iwjob'), $langugage['english_name']),
                                    'type' => 'text',
                                );
                                $field = IWJMB_Field::call( 'normalize', $field );
                                $meta = iwj_get_post_translate($post->ID, 'title', $langugage['code']);
                                if($meta){
                                    $meta = $meta->translate_string;
                                }
                                IWJMB_Field::input($field, $meta );
                                echo '</div>';
                            }
                            ?>
                        </div>
                        <script type="text/javascript">
                            jQuery(document).ready(function ($) {
                                $('#iwj-translate-term-tabs').tabs();
                            })
                        </script>
                    </div>
                    <?php
                }
            }
        }
    }

    static function woocommerce_process_product_meta_fields_save( $post_id ){
        if(defined('ICL_LANGUAGE_CODE')){
            self::init_fields();
            foreach (self::$fields as $field){
                $field = IWJMB_Field::call( 'normalize', $field );

                $single = $field['clone'] || ! $field['multiple'];
                $old    = '';
                $new    = isset( $_POST[ $field['id'] ] ) ? $_POST[ $field['id'] ] : ( $single ? '' : array() );
                // Allow field class change the value
                if ( $field['clone'] ) {
                    $new = IWJMB_Clone::value( $new, $old, $post_id, $field );
                } else {
                    $new = IWJMB_Field::call( $field, 'value', $new, $old, $post_id );
                    $new = IWJMB_Field::call( $field, 'sanitize_value', $new);
                }
                // Call defined method to save meta value, if there's no methods, call common one
                iwj_update_post_translate($post_id, $field['lang_code'], 'title', $new);
            }
        }
    }
}

IWJ_Admin_WPML::init();