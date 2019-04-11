<div class="modal fade" id="iwj-modal-facebook-apply-<?php echo get_the_ID(); ?>" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form class="iwj-application-facebook-form iwj-popup-form" action="<?php the_permalink(); ?>" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo __('Application With Facebook','iwjob'); ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <?php $user_data = $_SESSION['iwj_facebook_profile']; ?>
                    <div class="user-profile">
                        <div class="user-profile-header clearfix">
                            <?php if($user_data['picture']['url']){ ?>
                                <img class="user-avatar" src="<?php echo $user_data['picture']['url']; ?>" alt="">
                            <?php } ?>
                            <h4><?php echo $user_data['name']; ?></h4>
                            <span class="headline"><?php //echo $user_data->headline; ?></span>
                        </div>
                        <div class="user-profile-body">
                           <?php if (!empty($user_data['work']) && is_array($user_data['work'])) { ?>
                            <div class="user-profile-div">
                                <h4><?php echo __('Experience', 'iwjob'); ?></h4>
                                <div class="user-experiences">
                                    <?php
                                    foreach ($user_data['work'] as $jobs) {
                                        echo '<div class="experience-item">';
                                        if(isset($jobs[ 'position' ][ 'name' ])){
                                            $title =  $jobs[ 'position' ][ 'name' ] . ' at ' . $jobs[ 'employer' ][ 'name' ];
                                        }else{
                                            $title =  $jobs[ 'employer' ][ 'name' ];
                                        }
                                        $start_date = ( $jobs['start_date'] == '0000-00' ) ? '' : date(" F, Y", strtotime( $jobs[ 'start_date' ] ) );
                                        $end_date = ( !isset( $jobs['end_date'] ) || $jobs['end_date'] == '0000-00' ) ? "Today" : date("F Y",  strtotime( $jobs[ 'end_date' ] ));
                                        $working_period = "$start_date - $end_date";

                                        echo '<h5 class="experience-title">'.$title.'</h5>';
                                        echo '<div class="experience-period">'.$working_period.'</div>';
                                        if (isset($jobs['description'])) {
                                            echo '<p class="experience-sumary">'.$jobs['description'].'</p>';
                                        }
                                        echo '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php } ?>

                            <?php if (!empty($user_data[ 'education' ]) && is_array($user_data[ 'education' ])) { ?>
                                <div class="user-profile-div">
                                    <h4><?php echo __('Education', 'iwjob'); ?></h4>
                                    <div class="user-educations">
                                        <?php
                                        foreach ($user_data[ 'education' ] as $education) {
                                            echo '<div class="education-item">';
                                            $title = $education[ 'school' ][ 'name' ];
                                            $type = ( isset($education['type']) ) ? $education['type'] : '';
                                            $concentration = '';
                                            if( isset( $education['concentration'] ) && is_array( $education['concentration'] ) ) {
                                                $concentration .= ' - ';
                                                foreach ( $education['concentration'] as $concentrations ) {
                                                    $concentration .= $concentrations['name'] .',';
                                                }
                                            }
                                            // $start_date = $education->startDate->year;
                                            // $end_date = (empty($education->endDate)) ? "Today" : $education->endDate->year;
                                            $year = ( isset($education['year']['name']) ) ? __('Class of ', 'iwjob') . $education['year']['name'] : '';
                                            $school_data = "$type $concentration $year";
                                            //$school_data = "{$type}, {$education->fieldOfStudy} ($start_date - $end_date)";

                                            echo '<h5 class="education-title">'.$title.'</h5>';
                                            echo '<div class="education-school_data">'.$school_data.'</div>';
                                            // if (isset($education->notes)) {
                                            //     echo '<p class="education-sumary">'.$position->notes.'</p>';
                                            // }
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if (!empty($user_data['languages']) && is_array($user_data['languages'])) { ?>
                                <div class="user-profile-div">
                                    <h4><?php echo __('Languages', 'iwjob'); ?></h4>
                                    <div class="user-languages">
                                        <?php
                                        foreach ($user_data['languages'] as $language) {
                                            echo '<span class="language-item">';
                                            echo $language['name'];
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
                        if(iwj_option('apply_facebook_cover_letter_field_type') == 'editor'){
                            iwj_field_wysiwyg('message_apply_fb', __('Message', 'iwjob'), false, 0, '','', '','', array(
                                'quicktags' => false,
                                'editor_height' => 200
                            ));
                        }else{
                            iwj_field_textarea('message_apply_fb', __('Message', 'iwjob'), false, 0, '');
                        }
                        ?>
                    </div>
                    <div class="iwj-respon-msg iwj-hide"></div>
                    <input type="hidden" name="job_id" value="<?php echo get_the_ID(); ?>">
                    <input type="hidden" name="action" value="iwj_submit_application_facebook">
                    <div class="iwj-btn-action">
                        <button type="button" class="iwj-btn" data-dismiss="modal"><?php echo __('Close', 'iwjob'); ?></button>
                        <div class="iwj-button-loader">
                            <button type="submit" class="iwj-btn iwj-btn-primary iwj-facebook-application-btn"><?php echo __('Apply Now', 'iwjob'); ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>