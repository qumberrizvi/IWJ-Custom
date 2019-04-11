<?php
$user = IWJ_User::get_user();
?>
<div class="iwj-edit-job">
    <div class="iwj-main">
        <form action="" method="post" class="iwj-form-2 iwj-job-edit-form iwj-block">
            <?php do_action('iwj_edit_job_form_before', $job); ?>
            <div class="basic iwj-block-inner">
                <?php
                //fields
                $job_update = $job->get_update();
                if($job_update){
                    $edit_job = $job_update;
                }else{
                    $edit_job = $job;
                }

                $post_id = $edit_job->get_id();

                ?>
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        iwj_field_text('title', __('Title *', 'iwjob'), true, $post_id, $edit_job->get_title(true), '', '', __('Enter Title', 'iwjob'));
                        ?>
                    </div>
                </div>

                <?php do_action('iwj_job_form_after_title', $post_id); ?>

                <?php
                iwj_field_wysiwyg('description', __('Description *', 'iwjob'), true, $post_id, $edit_job->get_description(true), '', '','', array(
                    'quicktags' => false,
                    'editor_height' => 200
                ));
                ?>

                <?php
                $disable_type = iwj_option('disable_type');
                $disable_level = iwj_option('disable_level');
                $class = "col-md-4";
                if($disable_type && $disable_level){
                    $class = "col-md-12";
                }elseif($disable_type || $disable_level){
                    $class = "col-md-6";
                }
                ?>
                <div class="row">
                    <div class="<?php echo $class; ?>">
                        <?php


                        $allow_post_job_multi_cats = iwj_option( 'allow_post_job_multi_cats', '' );
                        $max_selected_cats = iwj_option( 'maximum_number_categories_selected', '' );
                        if ( iwj_option('submit_job_mode') == '3' || ($allow_post_job_multi_cats && $max_selected_cats > 1) ) {
                            if(iwj_option('submit_job_mode') == '3'){
                                $plan = $user->get_plan_for_submition();
                                if($plan){
                                    $max_selected_cats = $plan->get_max_categories();
                                }
                            }
	                        $text_desc_limit = $max_selected_cats ? sprintf(__( 'Maximum categories selected: <b>%d</b>', 'iwjob' ),$max_selected_cats) : '';
	                        iwj_field_taxonomy2( 'iwj_cat', 'job_category', __( 'Job Category *', 'iwjob' ), true, $post_id, null, null, $text_desc_limit, __( 'Select A Category', 'iwjob' ), true, null, array( 'maxSelectItems'  => $max_selected_cats,
	                                                                                                                                                                                                              'numberDisplayed' => 1
	                        ), true );
                        } else {
	                        $user_package = $job->get_user_package();
	                        $max_categories = $user_package ? $user_package->get_max_categories() : 1;
	                        $multiple = $max_categories == 1 ? false : true;
	                        $placeholder = _n('Select A Category', 'Select Subjects *', $max_categories, 'iwjob');
	                        if(!$multiple){
		                        iwj_field_taxonomy('iwj_cat','job_category', __('Job Category', 'iwjob'), true, $post_id, null, null, '', $placeholder, $multiple, array(), array(
			                        'maximumSelectionLength' => $max_categories == 1 ? false : $max_categories
		                        ));
	                        }else{
		                        $text_desc_limit = $max_categories ? sprintf(__( 'Maximum categories selected: <b>%d</b>', 'iwjob' ),$max_selected_cats) : '';
		                        iwj_field_taxonomy2('iwj_cat','job_category', __('Job Category *', 'iwjob'), true, $post_id, null, null, $text_desc_limit, $placeholder, $multiple, array(), array(
			                        'maxSelectItems' => $max_categories,
			                        'numberDisplayed' => 1
		                        ), true);
	                        }
                        } ?>
                    </div>
                    <?php if(!$disable_type){ ?>
                        <div class="<?php echo $class; ?>">
                            <?php
                            iwj_field_taxonomy('iwj_type','job_type', __('Job Type *', 'iwjob'), true, $post_id, null, null, '', __('Select A Type', 'iwjob'));
                            ?>
                        </div>
                    <?php } ?>
                    <?php if(!$disable_level){ ?>
                        <div class="<?php echo $class; ?>">
                            <?php
                            iwj_field_taxonomy('iwj_level','job_level', __('Job Level *', 'iwjob'), true, $post_id, null, null, '', __('Select A Level', 'iwjob'));
                            ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?php
                        iwj_field_text(IWJ_PREFIX.'salary_from', __('Salary From', 'iwjob'), false, $post_id, null, '', '', __('Enter Salary From', 'iwjob'));
                        ?>
                    </div>
                    <div class="col-md-4">
                        <?php
                        iwj_field_text(IWJ_PREFIX.'salary_to', __('Salary To', 'iwjob'), false, $post_id, null, '', '', __('Enter Salary To', 'iwjob'));

                        ?>
                    </div>
					<div class="col-md-4">
		                <?php
                        iwj_field_text( IWJ_PREFIX . 'salary_postfix', __( 'Salary Postfix Text', 'iwjob' ), false, $post_id, null, '', '', __( 'Ex: month', 'iwjob' ) );
		                ?>
					</div>
                </div>
				<div class="row">
					<div class="col-md-4">
						<?php
						iwj_field_select2(iwj_get_job_currencies(), IWJ_PREFIX.'currency', __( 'Currency', 'iwjob' ), false, $post_id, null, iwj_get_currency(), '', __('Select Currency', 'iwjob'), false, array(
							'minimumResultsForSearch' => -1
						));
						?>
					</div>
					<?php if ( ! iwj_option( 'disable_language' ) ) { ?>
					<div class="col-md-4">
						<?php
						//languages
						iwj_field_select2(iwj_get_available_languages(), IWJ_PREFIX.'job_languages', __( 'Languages', 'iwjob' ), false, $post_id, null, '', '', __('Select languages', 'iwjob'),true);
						?>
					</div>
					<?php } ?>
					<?php if ( ! iwj_option( 'disable_gender' ) ) { ?>
						<div class="col-md-4">
							<?php
							//gender
							iwj_field_select2( iwj_genders(), IWJ_PREFIX . 'job_gender', __( 'Gender', 'iwjob' ), false, $post_id, $edit_job->get_genders(), '', '', __( 'Select Gender', 'iwjob' ), true );
							?>
						</div>
					<?php } ?>
				</div>

                <?php do_action('iwj_job_form_after_general', $post_id); ?>

                <h3><?php echo __('Application Settings', 'iwjob'); ?></h3>
                <div class="row">
                    <?php
                    $class = "col-md-4";
                    if(!iwj_option('custom_apply_url')){
                        $class = "col-md-6";
                    }
                    ?>
                    <div class="<?php echo $class; ?>">
                        <?php
                        $display_date_format = iwj_option('display_date_format', 'Y/m/d');
                        iwj_field_date(IWJ_PREFIX.'deadline', __('Deadline Submission', 'iwjob'), false, $post_id, null, '', __('Enter Deadline Submission', 'iwjob'), $display_date_format);
                        ?>
                    </div>
                    <div class="<?php echo $class; ?>">
                        <?php
                        iwj_field_text( IWJ_PREFIX . 'email_application', __( 'Email for applications', 'iwjob' ), false, $post_id, null, '', __( 'Enter multiple email addresses separated by comma.', 'iwjob' ), $user->get_email());
                        ?>
                    </div>
                    <?php if(iwj_option('custom_apply_url')){ ?>
                        <div class="<?php echo $class; ?>">
                            <?php
                            iwj_field_text( IWJ_PREFIX . 'custom_apply_url', __( 'Apply URL', 'iwjob' ), false, $post_id, null, '', __( 'Enter Custom Apply URL', 'iwjob' ), 'http://yoursite.com' );
                            ?>
                        </div>
                    <?php } ?>
                </div>

                <?php do_action('iwj_job_form_after_application', $post_id); ?>

                <?php if(!iwj_option('disable_skill')){ ?>
                    <h3><?php echo __('Requirements Skills', 'iwjob'); ?></h3>
                    <div class="row">
                        <div class="col-md-12">
                            <?php
                            $value = '';
                            if($job){
                                $value = wp_get_object_terms( $job->get_id(), 'iwj_skill', array( 'fields' => 'names' ) );
                                if($value){
                                    $value = implode(', ',$value);
                                }
                            }
                            iwj_field_tagable(iwj_get_skill_options(), IWJ_PREFIX.'skill', '', true, $post_id, $value, '', '', __('Enter Requirements Skills', 'iwjob'));
                            ?>
                        </div>
                    </div>
                <?php } ?>

                <?php do_action('iwj_new_job_form_after_skill_requirements', $post_id);  ?>

                <h3><?php echo __('Location & Map', 'iwjob'); ?></h3>
                <?php
                if(!iwj_option('auto_detect_location')) {
                    iwj_field_select_tree('iwj_location', IWJ_PREFIX . 'location', __('Location', 'iwjob'), true, $post_id, null, null, '', __('Location', 'iwjob'));
                }

                iwj_field_text(IWJ_PREFIX.'address', __('Address *', 'iwjob'), true, $post_id);

                iwj_field_map(IWJ_PREFIX.'map', __('Map', 'iwjob'), $post_id, null, null, '', IWJ_PREFIX.'address');
                ?>

                <?php do_action('iwj_job_form_after_map', $post_id);  ?>

            </div>

            <?php do_action('iwj_edit_job_form_after', $post_id); ?>

            <div class="iwj-button-loader-respon-msg btn-right clearfix">
                <div class="iwj-button-loader text-right">
                    <button type="submit" class="iwj-btn iwj-btn-primary iwj-edit-job-btn"><?php echo __('Update Infomation', 'iwjob'); ?></button>
                </div>
                <div class="iwj-respon-msg iwj-hide"></div>
            </div>

            <input type="hidden" name="id" value="<?php echo $job->get_id(); ?>">
        </form>
    </div>
</div>