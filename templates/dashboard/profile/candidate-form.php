<?php
$user = IWJ_User::get_user();
$candidate = $user->get_candidate(true);
?>
<div class="iwj-edit-candidate-profile iwj-edit-profile-page">

    <?php if($candidate && $candidate->is_pending()){ ?>
        <div class="iwj-not-active">
            <?php echo iwj_get_alert(__('Your profile is currently awaiting approval.', 'iwjob'), 'info'); ?>
        </div>
    <?php } ?>

    <form method="post" action="" class="iwj-form-2 iwj-candidate-form">

        <?php do_action('iwj_before_candidate_form', $candidate); ?>

        <div class="iwj-block">
            <div class="basic-area iwj-block-inner">
                <?php
                $post_id = $candidate ? $candidate->get_id() : 0;
                ?>

                <?php iwj_field_avatar(IWJ_PREFIX.'avatar', '', false, $post_id, null, '', ''); ?>

                <div class="row">
                    <div class="col-md-6">
                        <?php
                        //Phone Number
                        $value = $candidate ? $candidate->get_title(true) : '';

                        iwj_field_text('your_name', __('Phone Number *', 'iwjob'), true, $post_id, $value, '', '', __('Enter your phone', 'iwjob'));

                        //email
                        iwj_field_email('email', __('Email *', 'iwjob'), true, $post_id, $user->get_email(), '', '', __('Enter your email', 'iwjob'));

                        ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                        //phone
                        iwj_field_text(IWJ_PREFIX.'phone', __('Name *', 'iwjob'), false, $post_id, null, '', '', __('Enter your full name', 'iwjob'));

                        //birthday
                        $display_date_format = iwj_option('display_date_format', 'Y/m/d');
                        iwj_field_date(IWJ_PREFIX.'birthday', __('Birthday *', 'iwjob'), true, $post_id, null, '','', $display_date_format);

                        ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php
                        //headline
                        iwj_field_text(IWJ_PREFIX.'headline', __('Headline *', 'iwjob'), true, $post_id, null, '', '', __('Ex: Maths Teacher', 'iwjob'));
                        ?>
                    </div>

                    <div class="col-md-6">
	                    <?php
	                    //gender
	                    iwj_field_select2(iwj_genders(), IWJ_PREFIX.'gender', __( 'Gender *', 'iwjob' ), true, $post_id, null, '', '', __('Select Gender', 'iwjob'));
	                    ?>
                    </div>
                </div>
				<div class="row">
					<div class="col-md-6">
						<?php
						//gender
						iwj_field_select2( array( '1' => __( 'Yes', 'iwjob' ), '0' => __( 'No', 'iwjob' ) ), IWJ_PREFIX.'public_account', __( 'Show Profile', 'iwjob' ), true, $post_id, null, '1', '', __(' ', 'iwjob'));
						?>
					</div>
				</div>

                <?php
                //Description
                $value = $candidate ? $candidate->get_description() : '';
                iwj_field_textarea('description', __('Description *', 'iwjob'), true, $post_id, $value);
                ?>
            </div>

            <?php do_action('iwj_candidate_form_after_general', $candidate); ?>

            <div class="cv-cover_letter-area iwj-block-inner">
                <h3><?php echo __('Curriculum Vitae & Cover letter', 'iwjob'); ?></h3>
                <div class="row">
                    <div class="col-md-12">
                        <?php iwj_field_file_cv(IWJ_PREFIX.'cv', __( 'Curriculum Vitae', 'iwjob' ), false, $post_id, null, '', '', true); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?php iwj_field_wysiwyg(IWJ_PREFIX.'cover_letter', __( 'Cover Letter', 'iwjob' ), false, $post_id, null); ?>
                    </div>
                </div>
            </div>

            <?php do_action('iwj_candidate_form_after_cv_cover_letter', $candidate); ?>

            <div class="working-preferences-area iwj-block-inner">

                <h3><?php echo __('Working Preferences', 'iwjob'); ?></h3>

                <?php
                $disable_level = iwj_option('disable_level');
                $disable_type = iwj_option('disable_type');
                $disable_language = iwj_option('disable_language');
                $class = "col-md-4";
                if($disable_level && $disable_type){
                    $class = "col-md-12";
                }elseif($disable_level || $disable_type){
                    $class = "col-md-6";
                }
                ?>
                <div class="row">
                    <div class="<?php echo $class; ?>">
                        <?php
                        iwj_field_taxonomy2('iwj_cat','categories', __('Subjects *', 'iwjob'), true, $post_id, null, array(), '', __('Select Subjects', 'iwjob'), true, array(), array('numberDisplayed' => 2));
                        ?>
                    </div>
                    <?php if(!$disable_type){ ?>
                    <div class="<?php echo $class; ?>">
                        <?php
                        iwj_field_taxonomy('iwj_type','types', __('Types *', 'iwjob'), true, $post_id, null, array(), '', __('Select Type', 'iwjob'), true);
                        ?>
                    </div>
                    <?php } ?>
                    <?php if(!$disable_level){ ?>
                    <div class="<?php echo $class; ?>">
                        <?php
                        iwj_field_taxonomy('iwj_level','levels', __('Standards *', 'iwjob'), true, $post_id, null, array(), '', __('Select Standards', 'iwjob'), true);
                        ?>
                    </div>
                    <?php } ?>
                </div>
                <div class="row">
					<?php
					if(!iwj_option('auto_detect_location')) {
					?>
                    <div class="col-md-4">
                        <?php
                        /*iwj_field_taxonomy2('iwj_location', IWJ_PREFIX.'locations', __('Locations *', 'iwjob'), true, $post_id, null, array(), '', __('Select Locations', 'iwjob'), true, array(
                            'hide_empty' => false,
                            'parent' => 0,
                        ), array('numberDisplayed' => 2));*/
                        iwj_field_taxonomy2('iwj_location','locations', __('Locations *', 'iwjob'), true, $post_id, null, array(), '', __('Select Locations', 'iwjob'), true, array(), array('numberDisplayed' => 2));
                        ?>
                    </div>
	                <?php }
	                if(!$disable_language){ ?>
                    <div class="col-md-4">
                        <?php
                        iwj_field_select2(iwj_get_available_languages(), IWJ_PREFIX.'languages', __( 'Languages', 'iwjob' ), false, $post_id, null, '', '', __('Select your languages', 'iwjob'), true);
                        ?>
                    </div>
					<?php } ?>
                    <div class="col-md-4">
                        <?php
                        iwj_field_text(IWJ_PREFIX.'salary_from', __('Average Monthly Charge', 'iwjob'), false, $post_id, null, '','', __('Enter your monthly charge', 'iwjob'));
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?php
                        iwj_field_text(IWJ_PREFIX.'experience_text', __('Experience', 'iwjob'), false, $post_id, null, '','', __('Enter Experience', 'iwjob'));
                        ?>
                    </div>
                    <?php if(!iwj_option('disable_skill')){ ?>
                        <div class="col-md-8">
                            <?php
                            if($candidate){
                                $value = wp_get_object_terms( $candidate->get_id(), 'iwj_skill', array( 'fields' => 'names' ) );
                                if($value){
                                    $value = implode(', ',$value);
                                }else{
                                    $value = '';
                                }
                            }
                            else{
                                $value = '';
                            }

                            iwj_field_tagable(iwj_get_skill_options(), IWJ_PREFIX.'skill', __( 'Skills', 'iwjob' ), true, $post_id, $value);

                            ?>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <?php do_action('iwj_candidate_form_after_preferences', $candidate); ?>

            <div class="educations-area iwj-block-inner">

                <h3><?php echo __('Educations', 'iwjob'); ?></h3>

                <?php
                    //fields
                iwj_field_group(array(
                    array(
                        'name' => __( 'Title', 'iwjob' ),
                        'id'   => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'name' => __( 'School Name', 'iwjob' ),
                        'id'   => 'school_name',
                        'type' => 'text',

                    ),
                    array(
                        'name' => __( 'Date In - Date Out', 'iwjob' ),
                        'id'   => 'date',
                        'type' => 'text',
                    ),
                    array(
                        'name' => __( 'Description', 'iwjob' ),
                        'id'   => 'description',
                        'type' => 'textarea',

                    ),
                ), IWJ_PREFIX.'education', '', $post_id, array(), true, true);
                ?>
            </div>

            <?php do_action('iwj_candidate_form_after_educations', $candidate); ?>

            <div class="experiences-area iwj-block-inner">

                <h3><?php echo __('Experiences', 'iwjob'); ?></h3>

                <?php
                iwj_field_group(array(
                    array(
                        'name' => __( 'Title', 'iwjob' ),
                        'id'   => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'name' => __( 'Company Name', 'iwjob' ),
                        'id'   => 'company',
                        'type' => 'text',
                    ),
                    array(
                        'name' => __( 'Date In - Date Out', 'iwjob' ),
                        'id'   => 'date',
                        'type' => 'text',
                    ),
                    array(
                        'name' => __( 'Description', 'iwjob' ),
                        'id'   => 'description',
                        'type' => 'textarea',
                    ),
                ), IWJ_PREFIX.'experience', '', $post_id, array(), true, true);
                ?>
            </div>

            <?php do_action('iwj_candidate_form_after_experiences', $candidate); ?>

            <?php if(!iwj_option('disable_skill')){ ?>
                <div class="skills-area iwj-block-inner">
                    <h3><?php echo __('Skills', 'iwjob'); ?></h3>
                    <?php
                    iwj_field_group(array(
                        array(
                            'name' => __( 'Title', 'iwjob' ),
                            'id'   => 'title',
                            'type' => 'text',
                        ),
                        array(
                            'name' => __( 'Value (percent)', 'iwjob' ),
                            'id'   => 'value',
                            'type' => 'number',
							'class' => 'iwj_lim_skill_showcase',
                        ),
                    ), IWJ_PREFIX.'skill_showcase', '', $post_id, array(), true, true);
                    ?>
                </div>
            <?php } ?>

            <?php do_action('iwj_candidate_form_after_skills', $candidate); ?>

            <div class="honors-awards-area iwj-block-inner">
                <h3><?php echo __('Honors & Awards', 'iwjob'); ?></h3>
                <?php
                iwj_field_group(array(
                    array(
                        'name' => __( 'Title', 'iwjob' ),
                        'id'   => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'name' => __( 'Year', 'iwjob' ),
                        'id'   => 'year',
                        'type' => 'text',
                    ),
                    array(
                        'name' => __( 'Description', 'iwjob' ),
                        'id'   => 'description',
                        'type' => 'textarea',
                    ),
                ), IWJ_PREFIX.'award', '', $post_id, array(), true, true);
                ?>
            </div>

            <?php do_action('iwj_candidate_form_after_honors_awards', $candidate); ?>

            <div class="gallery-area iwj-block-inner">
                <h3><?php echo __('Aadhaar, Certificate & CV', 'iwjob'); ?></h3>
                <?php
                iwj_field_gallery(IWJ_PREFIX.'gallery', '', false, $post_id);
                ?>
            </div>
            <div class="gallery-area iwj-block-inner">
                <h3><?php echo __('Cover Image', 'iwjob'); ?></h3>
                <?php
                iwj_field_image(IWJ_PREFIX.'cover_image', '', $post_id, null, '', IWJ_FIELD_ASSETS_URL .'img/placeholder-image.png', __('Upload File', 'iwjob'), __( 'Upload cover image file', 'iwjob' ));
                ?>
            </div>
            <div class="video-area iwj-block-inner">
                <h3 class=""><?php echo __('Video URL', 'iwjob'); ?></h3>
                <div>
                    <?php
                    iwj_field_url(IWJ_PREFIX.'video', '', false, $post_id, null, '', '', __('Accept the Youtube or Vimeo url', 'iwjob'));
                    ?>
                </div>
            </div>
            <div class="gallery-area iwj-block-inner">
                <h3><?php echo __('Video Poster', 'iwjob'); ?></h3>
                <?php
                iwj_field_image(IWJ_PREFIX.'video_poster', '', $post_id, null, '', IWJ_FIELD_ASSETS_URL .'img/placeholder-image.png', __('Upload File', 'iwjob'), __( 'Upload image for video poster', 'iwjob' ));
                ?>
            </div>

            <?php do_action('iwj_candidate_form_after_gallery', $candidate); ?>

            <div class="location-area iwj-block-inner">
                <h3 class=""><?php echo __('Location & Map', 'iwjob'); ?></h3>
                <?php
                iwj_field_text(IWJ_PREFIX.'address', __('Address *', 'iwjob'), true, $post_id, null, '', '',__('Enter your address', 'iwjob'));
                iwj_field_map(IWJ_PREFIX.'map', __('Map', 'iwjob'), $post_id, null, null, '', IWJ_PREFIX.'address');
                ?>
            </div>

            <?php do_action('iwj_candidate_form_after_map', $candidate); ?>

            <div class="socials-area iwj-block-inner">
                <h3><?php echo __('Teacher Socials', 'iwjob'); ?></h3>
                <div>
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            iwj_field_text(IWJ_PREFIX.'facebook', __('Facebook', 'iwjob'), false, $post_id);
                            iwj_field_text(IWJ_PREFIX.'google_plus', __('Google Plus', 'iwjob'), false, $post_id);
                            iwj_field_text(IWJ_PREFIX.'vimeo', __('Vimeo', 'iwjob'), false, $post_id);
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?php
                            iwj_field_text(IWJ_PREFIX.'twitter', __('Twitter', 'iwjob'), false, $post_id);
                            iwj_field_text(IWJ_PREFIX.'youtube', __('Youtube', 'iwjob'), false, $post_id);
                            iwj_field_text(IWJ_PREFIX.'linkedin', __('Linkedin', 'iwjob'), false, $post_id);
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php do_action('iwj_candidate_form_after_socials', $candidate); ?>

	        <?php
	        if ( iwj_option( 'show_gdpr_on_profile' ) ) { ?>
				<div class="gdpr_profile-area iwj-block-inner">
					<h3 class=""><?php echo __( 'GDPR Agreement *', 'iwjob' ); ?></h3>
					<div>
						<div class="row">
							<div class="col-md-12">
						        <?php
						        $label_gdpr = iwj_option( 'gdpr_on_profile_label' ) ? iwj_option( 'gdpr_on_profile_label' ) : __( 'I agree to let this website to save my submitted information.', 'inmag' );
						        iwj_field_input( 'checkbox', IWJ_PREFIX . 'gdpr_profile', $label_gdpr, true, $post_id );
						        ?>
							</div>
					        <?php
					        if ( iwj_option( 'gdpr_on_profile_desc' ) ) { ?>
								<div class="col-md-12 ">
									<div class="iwjmb-field iwjmb-textarea-wrapper">
										<div class="iwjmb-input ui-sortable">
											<textarea cols="60" rows="3" id="<?php esc_attr_e( IWJ_PREFIX . 'gdpr_profile_desc' ); ?>" class="iwjmb-textarea  large-text" name="<?php esc_attr_e( IWJ_PREFIX . 'gdpr_profile_desc' ); ?>" readonly="readonly"><?php echo iwj_option( 'gdpr_on_profile_desc' ); ?></textarea>
										</div>
									</div>
								</div>
					        <?php } ?>
						</div>
					</div>
				</div>

		        <?php do_action( 'iwj_candidate_form_after_gdpr', $candidate ); ?>

	        <?php } ?>

            <?php do_action('iwj_after_candidate_form', $candidate); ?>

            <div class="iwj-button-loader-respon-msg clearfix">
                <div class="iwj-button-loader">
                    <button type="submit" class="iwj-btn iwj-btn-primary iwj-candidate-btn"><?php echo __('Update Profile', 'iwjob');?></button>
                </div>
                <div class="iwj-respon-msg iwj-hide">

                </div>
            </div>

        </div>
    </form>

    <?php
    iwj_get_template_part('dashboard/profile/change-password');
    ?>
</div>