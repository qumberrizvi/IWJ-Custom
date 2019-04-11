<div id="iwj-job-alert-popup" class="modal-popup modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo __('Register for new Class Alert', 'iwjob'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo get_permalink(); ?>" method="post" class="iwj-form-2 iwj-alert-submit-form iwj-alert-submit-form-popup">
                    <?php if(!is_user_logged_in()){ ?>
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            iwj_field_email('email', __('Your Email *', 'iwjob'), true, 0, '', '','', __('Enter your email', 'iwjob'));
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?php
                            iwj_field_text('name', __('Phone Number *', 'iwjob'), true, 0, '', '','', __('Enter your phone', 'iwjob'));
                            ?>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            iwj_field_text('position', __('Title *', 'iwjob'), true, 0, '', '','', __('Enter title', 'iwjob'));
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?php
                            iwj_field_text('salary_from', __('Salary From', 'iwjob'), false, 0, '', '','', __('Enter salary from', 'iwjob'));
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        $disable_type = iwj_option('disable_type');
                        $class = !$disable_type ? 'col-md-6' : 'col-md-12';
                        ?>
                        <div class="<?php echo $class; ?>">
                            <?php
                            iwj_field_taxonomy2('iwj_cat','categories', __('Subjects', 'iwjob'), false, 0, null, null, '', __('Select Subjects', 'iwjob'), true);
                            ?>
                        </div>
                        <?php if(!$disable_type){ ?>
                            <div class="<?php echo $class; ?>">
                                <?php
                                iwj_field_taxonomy2('iwj_type','types', __('Types', 'iwjob'), false, 0, null, null, '', __('Select Types', 'iwjob'), true);
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="row">
                        <?php
                        $disable_level = iwj_option('disable_level');
                        $class = !$disable_level ? 'col-md-6' : 'col-md-12';
                        ?>
                        <?php if(!$disable_level){ ?>
                            <div class="<?php echo $class; ?>">
                                <?php
                                iwj_field_taxonomy2('iwj_level','levels', __('Levels', 'iwjob'), false, 0, null, null, '', __('Select Levels', 'iwjob'), true);
                                ?>
                            </div>
                        <?php } ?>
                        <div class="<?php echo $class; ?>">
                            <?php
                            iwj_field_taxonomy2('iwj_location','locations', __('Locations', 'iwjob'), false, 0, null, null, '', __('Select Locations', 'iwjob'), true,array(
                                'orderby' => 'term_group',
                            ), array('numberDisplayed' => 2), true);
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        $disable_skill = iwj_option('disable_skill');
                        $class = !$disable_skill ? 'col-md-6' : 'col-md-12';
                        ?>
                        <?php if(!$disable_skill){ ?>
                            <div class="<?php echo $class; ?>">
                                <?php
                                iwj_field_taxonomy2('iwj_skill','skills', __('Skills', 'iwjob'), false, 0, null, null, '', __('Select Skills', 'iwjob'), true);
                                ?>
                            </div>
                        <?php } ?>
                        <div class="<?php echo $class; ?>">
                            <?php
                            iwj_field_select2(array(
                                'daily' => __('Daily', 'iwjob'),
                                'weekly' => __('Weekly', 'iwjob'),
                            ), 'frequency', __('Frequency', 'iwjob'), true, 0, null, 'daily', '', '', false, array(
                                'minimumResultsForSearch' => -1
                            ));
                            ?>
                        </div>
                    </div>

                    <?php
                    if(in_array('job_alert', iwj_option('use_recaptcha', array()))) {
                        echo '<div class="g-recaptcha" data-sitekey="'.iwj_option('google_recaptcha_site_key').'"></div>';
                    }
                    ?>

                    <div class="iwj-respon-msg iwj-hide"></div>
                    <div class="iwj-submit-btn">
                        <div class="iwj-button-loader">
                            <button type="submit" class="iwj-btn iwj-btn-icon iwj-btn-primary iwj-submit-alert-btn" value="submit"><?php echo __('<i class="fa fa-envelope-o"></i> Register now', 'iwjob'); ?></button>
                            <button type="button" class="iwj-btn" data-dismiss="modal"><?php echo __('Close', 'iwjob'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
	</div>
</div>


