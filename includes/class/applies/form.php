<?php

class IWJ_Apply_Form extends IWJ_Apply {

    function __construct() {
        parent::__construct();

        add_action('wp_ajax_iwj_submit_application', array($this, 'submit_application'));
        add_action('wp_ajax_nopriv_iwj_submit_application', array($this, 'submit_application'));

        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    }

    function enqueue_scripts() {
        wp_register_style('iwj-apply-form', IWJ_PLUGIN_URL . '/includes/class/applies/assets/form-apply.css');
        wp_register_script('iwj-apply-form', IWJ_PLUGIN_URL . '/includes/class/applies/assets/form-apply.js', array('jquery'), false, true);
    }

    function admin_enqueue_scripts() {
        wp_enqueue_style('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-sortable');
    }

    function get_title() {
        return __('Apply With Form', 'iwjob');
    }

    function get_description() {
        return '';
    }

    function admin_option_fields() {
        ob_start();
        $settings = get_option('iwj_settings', array());
        $enable = isset($settings['apply_form_enable']) ? $settings['apply_form_enable'] : 0;
        $apply_job_multiple = isset($settings['apply_job_multiple']) ? $settings['apply_job_multiple'] : 0;
        $allow_guest_apply_job = isset($settings['allow_guest_apply_job']) ? $settings['allow_guest_apply_job'] : 0;
        $allow_candidate_apply_job = isset($settings['allow_candidate_apply_job']) ? $settings['allow_candidate_apply_job'] : 0;
        ?>
        <tr class="iwjmb-field iwjmb-select-wrapper">
            <th class="iwjmb-label"><label for="apply_form_enable"><?php echo __('Enable', 'iwjob'); ?></label></th>
            <td colspan="1">
                <div class="iwjmb-input ui-sortable">
                    <select size="0" id="apply_form_enable" class="iwjmb-select" name="apply_form_enable">
                        <option value="1" selected="selected" <?php selected($enable, 1); ?>><?php echo __('Yes', 'iwjob'); ?></option>
                        <option value="0" <?php selected($enable, 0); ?>><?php echo __('No', 'iwjob'); ?></option>
                    </select>
                </div>
            </td>
        </tr>
        <!--<tr class="iwjmb-field iwjmb-select-wrapper">
            <th class="iwjmb-label"><label for="apply_job_multiple"><?php /* echo __('Multiple job apply', 'iwjob'); */ ?></label></th>
            <td colspan="1">
                <div class="iwjmb-input ui-sortable">
                    <select size="0" id="apply_job_multiple" class="iwjmb-select" name="apply_job_multiple">
                        <option value="1" selected="selected" <?php /* selected($apply_job_multiple, 1); */ ?>><?php /* echo __('Yes', 'iwjob'); */ ?></option>
                        <option value="0" <?php /* selected($apply_job_multiple, 0); */ ?>><?php /* echo __('No', 'iwjob'); */ ?></option>
                    </select>
                </div>
            </td>
        </tr>-->
        <tr class="iwjmb-field iwjmb-select-wrapper">
            <th class="iwjmb-label"><label for="allow_guest_apply_job"><?php echo __('Allow guest apply', 'iwjob'); ?></label></th>
            <td colspan="1">
                <div class="iwjmb-input ui-sortable">
                    <select id="allow_guest_apply_job" class="iwjmb-select" name="allow_guest_apply_job">
                        <option value="1" selected="selected" <?php selected($allow_guest_apply_job, 1); ?>><?php echo __('Yes', 'iwjob'); ?></option>
                        <option value="0" <?php selected($allow_guest_apply_job, 0); ?>><?php echo __('No', 'iwjob'); ?></option>
                    </select>
                </div>
            </td>
        </tr>
        <tr class="iwjmb-field iwjmb-select-wrapper">
            <th class="iwjmb-label"><label for="allow_candidate_apply_job"><?php echo __('Allow candidate apply', 'iwjob'); ?></label></th>
            <td colspan="1">
                <div class="iwjmb-input ui-sortable">
                    <select id="allow_candidate_apply_job" class="iwjmb-select" name="allow_candidate_apply_job">
                        <option value="0" selected="selected" <?php selected($allow_candidate_apply_job, 0); ?>><?php echo __('Every candidate', 'iwjob'); ?></option>
                        <option value="1" <?php selected($allow_candidate_apply_job, 1); ?>><?php echo __('Teacher has published profile [status: publish]', 'iwjob'); ?></option>
                        <option value="2" <?php selected($allow_candidate_apply_job, 2); ?>><?php echo __('The candidate has submitted profile [status: pending, publish]', 'iwjob'); ?></option>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 0">
                <table id="sortable" class="form-table" style="margin: 0; border: none">
                    <tr class="disabled ui-state-default ui-state-disabled">
                        <th style="width: 15px;"><?php echo __('Order', 'iwjob'); ?></th>
                        <th><?php echo __('Title', 'iwjob'); ?></th>
                        <th><?php echo __('Name', 'iwjob'); ?></th>
                        <th><?php echo __('Type', 'iwjob'); ?></th>
                        <th><?php echo __('Required', 'iwjob'); ?></th>
                        <th><?php echo __('Predefined Value/Settings', 'iwjob'); ?></th>
                        <th><?php echo __('Action', 'iwjob'); ?></th>
                    </tr>
                    <?php
                    $core_fields = IWJ_Application::get_core_field_names();
                    $apply_form = isset($settings['apply_form']) ? $settings['apply_form'] : array();
                    if (!$apply_form) {
                        $apply_form = array(
                            'full_name' => array(
                                'title' => 'Fullname',
                                'name' => 'full_name',
                                'type' => 'text',
                                'required' => '1',
                                'pre_value' => '',
                            ),
                            'email' => array(
                                'title' => 'Email',
                                'name' => 'email',
                                'type' => 'email',
                                'required' => '1',
                                'pre_value' => '',
                            ),
                            'message' => array(
                                'title' => 'Message',
                                'name' => 'message',
                                'type' => 'wysiwyg',
                                'required' => '1',
                                'pre_value' => '',
                            ),
                            'cv' => array(
                                'title' => 'Curriculum Vitae',
                                'name' => 'cv',
                                'type' => 'file',
                                'required' => '1',
                                'pre_value' => 'pdf,zip,doc,docx',
                            ),
                        );
                    }

                    foreach ($apply_form as $field) {
                        $is_core = in_array($field['name'], $core_fields) ? true : false;
                        ?>
                        <tr class="ui-state-default <?php echo $is_core ? 'core-field' : ''; ?>">
                            <td class="sortable-state" style="width: 15px;"><i class="fa fa-arrows"></i></td>
                            <td>
                                <input type="text" name="apply_form[title][]" value="<?php echo $field['title']; ?>" required>
                            </td>
                            <td>
                                <input type="text" name="apply_form[name][]" value="<?php echo $field['name']; ?>" readonly>
                            </td>
                            <td>
                                <select class="apply_field_type" disabled required>
                                    <?php
                                    $types = array(
                                        'text' => __('Text', 'iwjob'),
                                        'email' => __('Email', 'iwjob'),
                                        'textarea' => __('Textarea', 'iwjob'),
                                        'wysiwyg' => __('Editor', 'iwjob'),
                                        'file' => __('File', 'iwjob'),
                                        'select' => __('Select', 'iwjob'),
                                    );
                                    foreach ($types as $type => $title) {
                                        echo '<option value="' . $type . '" ' . selected($field['type'], $type) . '>' . $title . '</option>';
                                    }
                                    ?>
                                </select>
                                <input type="hidden" name="apply_form[type][]" value="<?php echo $field['type']; ?>">
                            </td>
                            <td>
                                <select class="apply_form_required">
                                    <?php
                                    $types = array('1' => __('Yes', 'iwjob'), '0' => __('No', 'iwjob'));
                                    foreach ($types as $type => $title) {
                                        echo '<option value="' . $type . '" ' . selected($field['required'], $type) . '>' . $title . '</option>';
                                    }
                                    ?>
                                </select>
                                <input type="hidden" name="apply_form[required][]" value="<?php echo $field['required']; ?>">
                            </td>
                            <td>
                                <textarea name="apply_form[pre_value][]" class="apply-field-pre-value" ><?php echo $field['pre_value']; ?></textarea>
                            </td>
                            <td>
                                <button class="button apply_form_remove_field" <?php echo $is_core ? 'disabled' : '' ?>><?php echo __('Remove', 'iwjob'); ?></button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr class="disabled ui-state-disabled ui-state-default"><td colspan="7"><a href="#" class="add-apply-form-field button"><?php echo __('Add more field', 'iwjob') ?></a></td></tr>
                </table>
            </td>
        </tr>
        <?php
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    function admin_saved_fields($options) {
        $enable = isset($_POST['apply_form_enable']) ? $_POST['apply_form_enable'] : 0;
        $options['apply_form_enable'] = $enable;

        $new = isset($_POST['apply_form']) ? $_POST['apply_form'] : array();
        $names = $new['name'] ? $new['name'] : array();
        $titles = $new['title'] ? $new['title'] : array();
        $types = $new['type'] ? $new['type'] : array();
        $required = $new['required'] ? $new['required'] : array();
        $pre_values = $new['pre_value'] ? $new['pre_value'] : array();
        $new_value = array();
        foreach ($names as $key => $name) {
            if ($name) {
                $name = sanitize_title($name);
                $new_value[$name] = array(
                    'title' => sanitize_text_field($titles[$key]),
                    'name' => $name,
                    'type' => sanitize_text_field($types[$key]),
                    'required' => sanitize_text_field($required[$key]),
                    'pre_value' => sanitize_text_field($pre_values[$key]),
                );
            }
        }

        $options['apply_form'] = $new_value;

        $options['allow_guest_apply_job'] = isset($_POST['allow_guest_apply_job']) ? $_POST['allow_guest_apply_job'] : 0;
        $options['allow_candidate_apply_job'] = isset($_POST['allow_candidate_apply_job']) ? $_POST['allow_candidate_apply_job'] : 0;
        return $options;
    }

    function apply_button($job) {
        iwj_get_template_part('applies/form/button', array('job' => $job, 'self' => $this));
    }

    static function get_form_fields() {
        $form_fields = iwj_option('apply_form', array());
        if (!$form_fields) {
            $form_fields = array(
                'full_name' => array(
                    'title' => 'Fullname',
                    'name' => 'full_name',
                    'type' => 'text',
                    'required' => '1',
                    'pre_value' => '',
                ),
                'email' => array(
                    'title' => 'Email',
                    'name' => 'email',
                    'type' => 'email',
                    'required' => '1',
                    'pre_value' => '',
                ),
                'message' => array(
                    'title' => 'Message',
                    'name' => 'message',
                    'type' => 'wysiwyg',
                    'required' => '1',
                    'pre_value' => '',
                ),
                'cv' => array(
                    'title' => 'Curriculum Vitae',
                    'name' => 'cv',
                    'type' => 'file',
                    'required' => '1',
                    'pre_value' => 'pdf,zip,doc,docx',
                ),
            );
        }

        $fields = array();
        $user = IWJ_User::get_user();
        foreach ($form_fields as $key => $field) {
            if (!$field['name'] || !$field['type']) {
                continue;
            }
            $_field = array(
                'name' => $field['title'],
                'id' => IWJ_PREFIX . $field['name'],
                'required' => $field['required'] ? 'required' : '',
                'type' => $field['type'],
                'pre_value' => $field['pre_value'],
            );
            if ($field['type'] == 'file') {
                if ($field['name'] == 'cv' && !is_blog_admin()) {
                    $_field['type'] = 'cv';
                } else {
                    $_field['type'] = 'file';
                    $_field['force_delete'] = true;
                    $_field['max_file_uploads'] = 1;
                    $_field['no_for_label'] = is_blog_admin() ? 0 : 1;
                }

                if (isset($field['pre_value']) && $field['pre_value']) {
                    $_field['extensions'] = $field['pre_value'];
                    $file_extensions = explode(',', $field['pre_value']);
                    $all_mime_support = wp_get_mime_types();
                    $mimes = array();
                    $accept = array();
                    foreach ($file_extensions as $file_extension) {
                        $file_extension = trim($file_extension);
                        $accept[] = '.' . $file_extension;
                        switch ($file_extension) {
                            case 'jpg':
                            case 'jpeg':
                            case 'jpe':
                                $file_extension_key = 'jpg|jpeg|jpe';
                                break;
                            case 'asf':
                            case 'asx':
                                $file_extension_key = 'asf|asx';
                                break;
                            case 'mov':
                            case 'qt':
                                $file_extension_key = 'mov|qt';
                                break;
                            case 'mp4':
                            case 'm4v':
                                $file_extension_key = 'mp4|m4v';
                                break;
                            case '3gp':
                            case '3gpp':
                                $file_extension_key = '3gp|3gpp';
                                break;
                            case '3g2':
                            case '3gp2':
                                $file_extension_key = '3g2|3gp2';
                                break;
                            case 'txt':
                            case 'asc':
                            case 'c':
                            case 'cc':
                            case 'h':
                            case 'srt':
                                $file_extension_key = 'txt|asc|c|cc|h|srt';
                                break;
                            case 'htm':
                            case 'html':
                                $file_extension_key = 'htm|html';
                                break;
                            case 'mp3':
                            case 'm4a':
                            case 'm4b':
                                $file_extension_key = 'mp3|m4a|m4b';
                                break;
                            case 'ra':
                            case 'ram':
                                $file_extension_key = 'ra|ram';
                                break;
                            case 'ogg':
                            case 'oga':
                                $file_extension_key = 'ogg|oga';
                                break;
                            case 'mid':
                            case 'midi':
                                $file_extension_key = 'mid|midi';
                                break;
                            case 'gz':
                            case 'gzip':
                                $file_extension_key = 'gz|gzip';
                                break;
                            case 'pot':
                            case 'pps':
                            case 'ppt':
                                $file_extension_key = 'pot|pps|ppt';
                                break;
                            case 'xla':
                            case 'xls':
                            case 'xlt':
                            case 'xlw':
                                $file_extension_key = 'xla|xls|xlt|xlw';
                                break;
                            case 'onetoc':
                            case 'onetoc2':
                            case 'onetmp':
                            case 'onepkg':
                                $file_extension_key = 'onetoc|onetoc2|onetmp|onepkg';
                                break;
                            case 'wp':
                            case 'wpd':
                                $file_extension_key = 'wp|wpd';
                                break;
                            default:
                                $file_extension_key = $file_extension;
                                break;
                        }
                        if (isset($all_mime_support[$file_extension_key])) {
                            $mimes[$file_extension_key] = $all_mime_support[$file_extension_key];
                        }
                    }

                    if ($mimes) {
                        $_field['mimes'] = $mimes;
                    }

                    if ($accept) {
                        $_field['accept'] = implode(",", $accept);
                    }

                    //$_field['desc'] = sprintf(__('Allowed file: %s<br> Maximum upload file size: %s', 'iwjob'), $field['pre_value'], ini_get("upload_max_filesize"));
                }
            } elseif ($field['type'] == 'select') {
                $options = $field['pre_value'];
                $options = explode("|", $options);
                $_field['options'] = array();
                $_field['type'] = 'select_advanced';
                foreach ($options as $option) {
                    $_field['options'][$option] = $option;
                }
            } elseif ($field['type'] == 'wysiwyg') {
                $_field['options'] = array(
                    'quicktags' => false
                );
            }

            if ($user) {
                if ($key == 'full_name') {
                    $_field['std'] = $user->get_display_name();
                } elseif ($key == 'email') {
                    $_field['std'] = $user->get_email();
                } elseif ($key == 'message') {
                    $_field['std'] = $user->get_cover_letter();
                }
            }
            $fields[$field['name']] = $_field;
        }

        return $fields;
    }

    function validate_data() {

        $error_msg = array();
        $job_id = sanitize_text_field($_POST['job_id']);
        if (!$job_id) {
            $error_msg[] = __('Invalid Job', 'iwjob');
            return $error_msg;
        }

        $cookie = 'iwj_apply_' . $job_id;
        if (isset($_COOKIE[$cookie]) && $_COOKIE[$cookie]) {
            $messages[] = __('You have already applied to this job', 'iwjob');
            return $error_msg;
        }

        $form_fields = $this->get_form_fields();
        $max_upload_cv = iwj_option('maximum_file_size_cv') ? iwj_option('maximum_file_size_cv') * 1024 * 1024 : wp_max_upload_size();
        foreach ($form_fields as $field) {
            $field = IWJMB_Field::call('normalize', $field);
            $single = $field['clone'] || !$field['multiple'];
            $new = isset($_POST[$field['id']]) ? $_POST[$field['id']] : ( $single ? '' : array() );
            if ($field['type'] == 'file') {
                $files = IWJMB_File_Field::transform($_FILES[$field['id']]);
                if ($field['required'] && empty($files[0]['tmp_name'])) {
                    $error_msg[] = sprintf(__('Please select %s', 'iwjob'), $field['name']);
                } elseif ($files) {
                    $mimes = $field['mimes'];
                    if (!$mimes) {
                        $mimes = wp_get_mime_types();
                    }

                    foreach ($files as $file) {
                        if (!$file['tmp_name']) {
                            continue;
                        }

                        $test_file_size = $file['size'];
                        // A non-empty file will pass this test.

                        if (!($test_file_size > 0)) {
                            $error_msg[] = __('File is empty. Please upload something more substantial.', 'iwjob');
                            break;
                        }

                        // A properly uploaded file will pass this test. There should be no reason to override this one.
                        $test_uploaded_file = @ is_uploaded_file($file['tmp_name']);
                        if (!$test_uploaded_file) {
                            $error_msg[] = __('Specified file failed upload test.', 'iwjob');
                            break;
                        }

                        // A correct MIME type will pass this test. Override $mimes or use the upload_mimes filter.
                        if ($mimes) {
                            $wp_filetype = wp_check_filetype_and_ext($file['tmp_name'], $file['name'], $mimes);
                            $ext = empty($wp_filetype['ext']) ? '' : $wp_filetype['ext'];
                            $type = empty($wp_filetype['type']) ? '' : $wp_filetype['type'];
                            $proper_filename = empty($wp_filetype['proper_filename']) ? '' : $wp_filetype['proper_filename'];

                            // Check to see if wp_check_filetype_and_ext() determined the filename was incorrect
                            if ($proper_filename) {
                                $file['name'] = $proper_filename;
                            }
                            if ((!$type || !$ext)) {
                                $error_msg[] = __('Sorry, this file type is not permitted for security reasons.', 'iwjob');
                            }
                        }
                    }
                }
            } elseif ($field['type'] == 'cv') {

                if ($field['required'] && $_POST[$field['id']] == 'add_new_cv' && empty($_FILES[$field['id'] . '_new_cv']['tmp_name'])) {
                    $error_msg[] = sprintf(__('Please select %s', 'iwjob'), $field['name']);
                } elseif ($_POST[$field['id']] == 'add_new_cv' && $_FILES[$field['id'] . '_new_cv']) {
                    $file = $_FILES[$field['id'] . '_new_cv'];

                    $mimes = $field['mimes'];
                    if (!$mimes) {
                        $mimes = wp_get_mime_types();
                    }

                    $test_file_size = $file['size'];
                    if ($test_file_size > $max_upload_cv) {
                        $error_msg[] = sprintf(__('File size is too large. Maximum upload is %s', 'iwjob'), iwj_format_bytes($max_upload_cv, 0));
                        break;
                    }

                    // A non-empty file will pass this test.
                    if ($field['required'] != null) { // check required
                        if (!($test_file_size > 0)) {
                            $error_msg[] = __('File is empty. Please upload something more substantial.', 'iwjob');
                            break;
                        }

                        // A properly uploaded file will pass this test. There should be no reason to override this one.
                        $test_uploaded_file = @ is_uploaded_file($file['tmp_name']);
                        if (!$test_uploaded_file) {
                            $error_msg[] = __('Specified file failed upload test.', 'iwjob');
                            break;
                        }

                        // A correct MIME type will pass this test. Override $mimes or use the upload_mimes filter.
                        if ($mimes) {
                            $wp_filetype = wp_check_filetype_and_ext($file['tmp_name'], $file['name'], $mimes);
                            $ext = empty($wp_filetype['ext']) ? '' : $wp_filetype['ext'];
                            $type = empty($wp_filetype['type']) ? '' : $wp_filetype['type'];
                            $proper_filename = empty($wp_filetype['proper_filename']) ? '' : $wp_filetype['proper_filename'];

                            // Check to see if wp_check_filetype_and_ext() determined the filename was incorrect
                            if ($proper_filename) {
                                $file['name'] = $proper_filename;
                            }
                            if ((!$type || !$ext)) {
                                $error_msg[] = __('Sorry, this file type is not permitted for security reasons.', 'iwjob');
                            }
                        }
                    }
                }
            } else {
                if ($field['required']) {
                    if ($single && $new === '' || !$single && empty($new)) {
                        $messages[] = sprintf(__('Please input %s', 'iwjob'), $field['name']);
                    }
                }
            }
        }

        if ($error_msg) {
            return $error_msg;
        }

        return true;
    }

    function submit_application() {
        if (!defined('WP_CACHE') || !WP_CACHE) {
            check_ajax_referer('iwj-security');
        }

        $captcha_respon = IWJ_Controller::check_recaptcha();

        if ($captcha_respon !== true) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(sprintf(__('Wrong captcha %s.', 'iwjob'), $captcha_respon), 'danger')
            ));
            die();
        }

        $user = IWJ_User::get_user();

        $job_id = sanitize_text_field($_POST['job_id']);
        $job = IWJ_Job::get_job($job_id);
        if (!$job) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Invalid Job', 'iwjob'), 'danger')
            ));

            exit;
        }

        if (!$job->can_apply()) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('This Class has been expired', 'iwjob'), 'danger')
            ));

            exit;
        }

        if (!iwj_option('allow_guest_apply_job') && !$user) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('Please login to apply this job', 'iwjob'), 'danger')
            ));

            exit;
        }

        if ($user && !$user->can_apply()) {
            echo json_encode(array(
                'success' => false,
                'message' => iwj_get_alert(__('You do not have permission apply this job', 'iwjob'), 'danger')
            ));

            exit;
        }
        $validate = $this->validate_data();

        if (iwj_option('apply_job_mode')) {
            if ($validate === true) {
                if ($application_id = $this->add_new()) {
                    ob_start();
                    setcookie('iwj_apply_' . $job_id, 1, time() + 60 * 60 * 24 * 360, SITECOOKIEPATH);
                    iwj_get_template_part('applies/form/thankyou', array('job_id' => $job_id, 'application_id' => $application_id));
                    $message = ob_get_contents();
                    ob_end_clean();
                    echo json_encode(array(
                        'success' => true,
                        'message' => $message
                    ));
                } else {
                    echo json_encode(array(
                        'success' => false,
                        'message' => iwj_get_alert(__('An error occurred please reload the page and try again.', 'iwjob'), 'danger')
                    ));
                }
            } else {
                echo json_encode(array(
                    'success' => false,
                    'message' => iwj_get_alert(implode("<br>", $validate), 'danger')
                ));
            }
        } else {
            if (!$user) {
                echo json_encode(array(
                    'success' => false,
                    'message' => iwj_get_alert(__('You must login to apply job', 'iwjob'), 'danger')
                ));

                exit;
            }
            $user_package_ids = $user->get_user_package_ids('apply_job_package');
            if (!$user_package_ids) {
                echo json_encode(array(
                    'success' => false,
                    'message' => iwj_get_alert(__('You must buy Apply Class Package to apply job', 'iwjob'), 'danger')
                ));

                exit;
            }

            $can_view_packages = array();
            foreach ($user_package_ids as $user_package_id) {
                $user_package = IWJ_User_Package::get_user_package($user_package_id);
                if ($user_package->get_remain_apply_job() > 0 && $user_package->get_status() == 'publish') {
                    $can_view_packages[] = $user_package;
                }
            }

            if ($validate === true) {
                if (count($can_view_packages) < 1) {
                    echo json_encode(array(
                        'success' => false,
                        'message' => iwj_get_alert(__('Your Apply Class Package is pending or expired', 'iwjob'), 'danger')
                    ));

                    exit;
                } else {
                    $user_package = $can_view_packages[0];
                    if ($application_id = $this->add_new()) {
                        update_post_meta($user_package->get_id(), IWJ_PREFIX . 'remain_apply_job', $user_package->get_remain_apply_job() - 1);
                        ob_start();
                        setcookie('iwj_apply_' . $job_id, 1, time() + 60 * 60 * 24 * 360, SITECOOKIEPATH);
                        iwj_get_template_part('applies/form/thankyou', array('job_id' => $job_id, 'application_id' => $application_id));
                        $message = ob_get_contents();
                        ob_end_clean();
                        echo json_encode(array(
                            'success' => true,
                            'message' => $message
                        ));
                    } else {
                        echo json_encode(array(
                            'success' => false,
                            'message' => iwj_get_alert(__('An error occurred please reload the page and try again.', 'iwjob'), 'danger')
                        ));
                    }
                }
            } else {
                echo json_encode(array(
                    'success' => false,
                    'message' => iwj_get_alert(implode("<br>", $validate), 'danger')
                ));
            }
        }







        exit;
    }

    function add_new() {

        $job_id = sanitize_text_field($_POST['job_id']);

        $user_id = is_user_logged_in() ? get_current_user_id() : 0;
        $message = wp_kses_post($_POST[IWJ_PREFIX . 'message']);
        $full_name = sanitize_text_field($_POST[IWJ_PREFIX . 'full_name']);
        $post_data = array(
            'post_title' => $full_name,
            'post_content' => $message,
            'post_type' => 'iwj_application',
            'post_status' => 'pending',
            'post_author' => $user_id,
        );

        $post_id = wp_insert_post($post_data);
        if ($post_id) {

            update_post_meta($post_id, IWJ_PREFIX . 'job_id', $job_id);

            $form_fields = $this->get_form_fields();
            foreach ($form_fields as $field) {
                if ($field['id'] == IWJ_PREFIX . 'message')
                    continue;

                $field = IWJMB_Field::call('normalize', $field);

                $single = $field['clone'] || !$field['multiple'];
                $old = IWJMB_Field::call($field, 'raw_post_meta', $post_id);
                $new = isset($_POST[$field['id']]) ? $_POST[$field['id']] : ( $single ? '' : array() );

                // Allow field class change the value
                if ($field['clone']) {
                    $new = IWJMB_Clone::value($new, $old, $post_id, $field);
                } else {
                    $new = IWJMB_Field::call($field, 'value', $new, $old, $post_id);
                    $new = IWJMB_Field::call($field, 'sanitize_value', $new);
                }

                // Call defined method to save meta value, if there's no methods, call common one
                IWJMB_Field::call($field, 'save_post', $new, $old, $post_id);
            }

            clean_post_cache($post_id);

            $application = IWJ_Application::get_application($post_id, true);
            if (class_exists('\W3TC\DbCache_Plugin')) {
                $dbcache = new W3TC\DbCache_Plugin();
                $dbcache->on_post_change();
            }
            IWJ_Email::send_email('new_application', $application);
            IWJ_Email::send_email('new_application_employer', $application);

            return $post_id;
        }

        return false;
    }

}
