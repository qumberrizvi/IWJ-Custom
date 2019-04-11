<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="iwj-application-details application-popup">
    <?php
    if ( $application ) {
        $application_id = $application->get_id();
        $author = $application->get_author();
        if($author){
            ?>
            <div class="avatar">
               <?php echo get_avatar($author->get_id()); ?>
            </div>
            <?php
        }
        ?>
        <form action="" method="post" class="iwj-update-appication-form">
            <div class="application-job-date">
                <?php
                $job = $application->get_job();
                if($job){
                    echo '<a class="application-job" href="'.$job->permalink().'" title="'.$job->get_title().'"><i class="fa fa-suitcase"></i>'.wp_trim_words($job->get_title(), 7).'</a>';
                }
                echo '<span class="application-date"><i class="ion-clock"></i>'.$application->get_created(get_option('date_format')).'</span>';
                ?>
            </div>
            <ul class="application-details">
                <li class="application-full-name">
                    <div class="title">
                        <?php echo __('Full name', 'iwjob'); ?>
                    </div>
                    <div class="value">
                        <?php
                        echo $application->get_full_name();
                        ?>
                    </div>
                </li>
                <li class="application-email">
                    <div class="title">
                        <?php echo __('Email', 'iwjob'); ?>
                    </div>
                    <div class="value">
                        <?php
                        echo $application->get_email();
                        ?>
                    </div>
                </li>
                <li class="application-cv">
                    <div class="title">
                        <?php echo __('Teacher CV', 'iwjob'); ?>
                    </div>
                    <div class="value">
                        <?php
                        $cv = $application->get_cv();
                        if($cv){
                            echo '<a href="'.$cv['url'].'">'.__('Download full CV', 'iwjob').'</a>';
                        }
                        ?>
                    </div>
                </li>
                <?php
                do_action('iwj_view_application_before_message', $application, false);
                ?>
                <li class="application-message">
                    <div class="title">
                        <?php echo __('Teacher Message', 'iwjob'); ?>
                    </div>
                    <div class="value">
                        <?php
                        echo $application->get_message();
                        ?>
                    </div>
                </li>
                <?php
                $source = $application->get_source();
                if($source == 'form'){
                    $form_fields = IWJ_Apply_Form::get_form_fields();
                    $core_field_names = $application->get_core_field_names();
                    foreach($form_fields as $field_key => $form_field){
                        if(!in_array($field_key, $core_field_names)){
                            ?>
                            <li>
                                <div class="title">
                                    <?php echo $form_field['name']; ?>
                                </div>
                                <div class="value">
                                    <?php echo $application->get_field_value($field_key); ?>
                                </div>
                            </li>
                            <?php
                        }
                    }
                }
                ?>
                <?php
                do_action('iwj_view_application_after_message', $application, false);
                ?>
                <li class="application-note">
                    <div class="title">
                        <?php echo __('Student Note', 'iwjob'); ?>
                    </div>
                    <div class="value">
                        <textarea name="private_note"><?php echo $application->get_private_note(); ?></textarea>
                    </div>
                </li>
                <li class="application-status">
                    <div class="title">
                        <?php echo __('Application Status', 'iwjob'); ?>
                    </div>
                    <div class="value">
                        <select class="iwj-select-2-wsearch" name="application_status">
                            <option value="pending" <?php selected($application->get_status(), 'pending'); ?>><?php echo __('Pending', 'iwjob'); ?></option>
                            <option value="publish" <?php selected($application->get_status(), 'publish'); ?>><?php echo __('Accept', 'iwjob'); ?></option>
                            <option value="iwj-rejected" <?php selected($application->get_status(), 'iwj-rejected'); ?>><?php echo __('Reject', 'iwjob'); ?></option>
                        </select>
                    </div>
                </li>
            </ul>

            <div class="application-button">
                <input type="hidden" name="application_id" value="<?php echo $application_id; ?>">
                <div class="iwj-button-loader">
                    <button class="iwj-btn iwj-btn-primary iwj-update-appication-btn"><i class="ion-checkmark-round"></i><?php echo __('Save', 'iwjob'); ?></button>
                    <?php if(iwj_option('email_application_enable')){ ?>
                        <button class="iwj-btn iwj-btn-primary iwj-update2-appication-btn"><i class="fa fa-envelope"></i><?php echo __('Save & Send Email', 'iwjob'); ?></button>
                    <?php } ?>
                </div>
                <div class="iwj-respon-msg iwj-hide"></div>
            </div>
        </form>
    <?php } ?>
</div>
