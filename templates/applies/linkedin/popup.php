<div class="modal fade" id="iwj-modal-linkedin-apply-<?php echo get_the_ID(); ?>" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form class="iwj-application-linkedin-form iwj-popup-form" action="<?php the_permalink(); ?>" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo __('Applied with LinkedIn','iwjob'); ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <?php $user_data = $_SESSION['iwj_linkedin_profile']; ?>
                    <div class="user-profile">
                        <div class="user-profile-header clearfix">
                            <?php if($user_data->pictureUrl){ ?>
                                <img class="user-avatar" src="<?php echo $user_data->pictureUrl; ?>" alt="">
                            <?php } ?>
                            <h4><?php echo $user_data->firstName. ' '. $user_data->lastName; ?></h4>
                            <span class="headline"><?php echo $user_data->headline; ?></span>
                        </div>
                        <div class="user-profile-body">
                           <?php if (!empty($user_data->positions->values) && is_array($user_data->positions->values)) { ?>
                            <div class="user-profile-div">
                                <h4><?php echo __('Experience', 'iwjob'); ?></h4>
                                <div class="user-experiences">
                                    <?php
                                    foreach ($user_data->positions->values as $position) {
                                        echo '<div class="experience-item">';
                                        $title = $position->title . ' at ' . $position->company->name;
                                        $start_date = date("F Y", mktime(0, 0, 0, $position->startDate->month, 0, $position->startDate->year));
                                        $end_date = ($position->isCurrent) ? "Today" : date("F Y", mktime(0, 0, 0, $position->endDate->month, 0, $position->endDate->year));
                                        $working_period = "$start_date - $end_date";

                                        echo '<h5 class="experience-title">'.$title.'</h5>';
                                        echo '<div class="experience-period">'.$working_period.'</div>';
                                        if (isset($position->summary)) {
                                            echo '<p class="experience-sumary">'.$position->summary.'</p>';
                                        }
                                        echo '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php } ?>

                            <?php if (!empty($user_data->educations->values) && is_array($user_data->educations->values)) { ?>
                                <div class="user-profile-div">
                                    <h4><?php echo __('Education', 'iwjob'); ?></h4>
                                    <div class="user-educations">
                                        <?php
                                        foreach ($user_data->educations->values as $education) {
                                            echo '<div class="education-item">';
                                            $title = $education->schoolName;
                                            $start_date = $education->startDate->year;
                                            $end_date = (empty($education->endDate)) ? "Today" : $education->endDate->year;
                                            $school_data = "{$education->degree}, {$education->fieldOfStudy} ($start_date - $end_date)";

                                            echo '<h5 class="education-title">'.$title.'</h5>';
                                            echo '<div class="education-school_data">'.$school_data.'</div>';
                                            if (isset($education->notes)) {
                                                echo '<p class="education-sumary">'.$position->notes.'</p>';
                                            }
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if (!empty($user_data->skills->values) && is_array($user_data->skills->values)) { ?>
                            <div class="user-profile-div">
                                <h4><?php echo __('Skills', 'iwjob'); ?></h4>
                                <div class="user-skills">
                                    <?php
                                    foreach ($user_data->skills->values as $skill) {
                                        echo '<span class="skill-item">';
                                        echo $skill->skill->name.'';
                                        echo '</span>';
                                    }
                                    ?>
                                </div>
                             </div>
                            <?php } ?>

                            <?php if (!empty($user_data->languages->values) && is_array($user_data->languages->values)) { ?>
                                <div class="user-profile-div">
                                    <h4><?php echo __('Languages', 'iwjob'); ?></h4>
                                    <div class="user-languages">
                                        <?php
                                        foreach ($user_data->languages->values as $language) {
                                            echo '<span class="language-item">';
                                            echo $language->language->name;
                                            echo '</span>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="cover-letter">
                        <?php
                        if(iwj_option('apply_linkedin_cover_letter_field_type') == 'editor'){
                            iwj_field_wysiwyg('message', __('Message', 'iwjob'), false, 0, '','', '','', array(
                                'quicktags' => false,
                                'editor_height' => 200
                            ));
                        }else{
                            iwj_field_textarea('message', __('Message', 'iwjob'), false, 0, '');
                        }
                        ?>
                    </div>
                    <div class="iwj-respon-msg iwj-hide"></div>
                    <input type="hidden" name="job_id" value="<?php echo get_the_ID(); ?>">
                    <input type="hidden" name="action" value="iwj_submit_application_linkedin">
                    <div class="iwj-btn-action">
                        <button type="button" class="iwj-btn" data-dismiss="modal"><?php echo __('Close', 'iwjob'); ?></button>
                        <div class="iwj-button-loader">
                            <button type="submit" class="iwj-btn iwj-btn-primary iwj-linkedin-application-btn"><?php echo __('Apply Now', 'iwjob'); ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>