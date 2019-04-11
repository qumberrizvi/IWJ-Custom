<form action="" method="post" class="iwj-form-2 iwj-job-submit-form">
    <?php

    $post_id = $job ? $job->get_id() : '';
    $user = IWJ_User::get_user();
    do_action('iwj_job_form_before', $post_id);

    $title = $description = '';
    if($job){
        $title = $job->get_title(true);
        $description = $job->get_description(true);
    }else{
        $description = iwj_get_desc_job();
    }
    ?>

    <div class="row">
        <div class="col-md-12">
            <?php
            iwj_field_text('title', __('Title *', 'iwjob'), true, $post_id, $title, '', '', __('Enter Title', 'iwjob'));
            ?>
        </div>
    </div>

    <?php do_action('iwj_new_job_form_after_title', $job); ?>

    <?php
    iwj_field_wysiwyg('description', __('Description *', 'iwjob'), true, $post_id, $description, '', '','', array(
            'quicktags' => false,
            'editor_height' => 250
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
	        if(iwj_option('submit_job_mode') == '3'){
	            $plan = $user->get_plan_for_submition();
	            if($plan){
                    $max_selected_cats = $plan->get_max_categories();
                }else{
                    $max_selected_cats = iwj_option( 'maximum_number_categories_selected', '' );
                }
            }else{
                $max_selected_cats = iwj_option( 'maximum_number_categories_selected', '' );
            }

	        if ( $allow_post_job_multi_cats && $max_selected_cats > 1 ) {
		        $text_desc_limit = $max_selected_cats ? sprintf(__( 'Maximum categories selected: <b>%d</b>', 'iwjob' ),$max_selected_cats) : '';
		        iwj_field_taxonomy2( 'iwj_cat', 'job_category', __( 'Job Category *', 'iwjob' ), true, $post_id, null, null, $text_desc_limit, __( 'Select A Category', 'iwjob' ), true, null, array( 'maxSelectItems'  => $max_selected_cats,
		                                                                                                                                                                                              'numberDisplayed' => 1
		        ), true );
	        } else {
		        iwj_field_taxonomy( 'iwj_cat', 'job_category', __( 'Job Category *', 'iwjob' ), true, $post_id, null, null, '', __( 'Select A Category', 'iwjob' ) );
	        } ?>
        </div>
        <?php if(!$disable_type){ ?>
            <div class="<?php echo $class; ?>">
                <?php
                iwj_field_taxonomy('iwj_type','job_type', __('Job Type *', 'iwjob'), true, $post_id, null, null, '', __('Select A Type', 'iwjob'), false);
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
			iwj_field_select2(iwj_get_job_currencies(), IWJ_PREFIX.'currency', __( 'Currency', 'iwjob' ), true, $post_id, null, iwj_get_currency(), '', __('Select Currency', 'iwjob'));
			?>
		</div>
		<?php if ( ! iwj_option( 'disable_language' ) ) { ?>
		<div class="col-md-4">
			<?php
			//languages
			iwj_field_select2(iwj_get_available_languages(), IWJ_PREFIX.'job_languages', __( 'Languages', 'iwjob' ), false, $post_id, null, '', '', __('Select Languages', 'iwjob'), true);
			?>
		</div>
		<?php } ?>
		<?php if ( ! iwj_option( 'disable_gender' ) ) { ?>
			<div class="col-md-4">
				<?php
				//gender
				iwj_field_select2( iwj_genders(), IWJ_PREFIX . 'job_gender', __( 'Gender', 'iwjob' ), false, $post_id, null, '', '', __( 'Select Gender', 'iwjob' ), true );
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
            $user = IWJ_User::get_user();
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
    <h3 ><?php echo __('Skills Requirements', 'iwjob'); ?></h3>
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
            iwj_field_tagable(iwj_get_skill_options(), IWJ_PREFIX.'skill', '', true, $post_id, $value, '', '', __('Enter Skills Requirements', 'iwjob'));
            ?>
        </div>
    </div>
    <?php } ?>

    <?php do_action('iwj_job_form_after_skill_requirements', $post_id);  ?>

    <?php
    echo '<h3 class="">'.__('Location & Map', 'iwjob').'</h3>';
    if(!iwj_option('auto_detect_location')){
        iwj_field_select_tree('iwj_location', IWJ_PREFIX.'location', __( 'Location', 'iwjob' ), true, $post_id );
    }

    iwj_field_text(IWJ_PREFIX.'address', __('Address *', 'iwjob'), true, $post_id);

    iwj_field_map(IWJ_PREFIX.'map', __('Map', 'iwjob'), $post_id, null, null, '', IWJ_PREFIX.'address');
    ?>

    <?php do_action('iwj_new_job_form_after_map', $post_id);  ?>

    <?php do_action('iwj_new_job_form_after', $post_id); ?>

    <input type="hidden" name="id" value="<?php echo $post_id; ?>">
    <input type="hidden" name="submit_action" value="submit">
    <?php if(iwj_option('submit_job_mode') == '3'){ ?>
    <input type="hidden" name="plan_id" value="<?php echo $user->get_plan_id_for_submition(); ?>">
    <?php } ?>
    <div class="iwj-respon-msg iwj-hide"></div>
    <div class="iwj-submit-btn text-right">
        <div class="iwj-button-loader">
            <button type="submit" class="iwj-btn iwj-btn-primary iwj-btn-icon iwj-submit-job-btn" value="submit"><?php echo __('<i class="ion-android-send"></i>  Save & Preview', 'iwjob'); ?></button>
        </div>
    </div>
</form>