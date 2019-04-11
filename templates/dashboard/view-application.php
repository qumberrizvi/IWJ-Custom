<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="iwj-application-details iwj-main-block">
    <?php
    if ( $application ) {
        $application_id = $application->get_id();
        $author = $application->get_author();
        ?>
        <div class="application-title">
            <h3><?php echo __('Application Details', 'iwjob'); ?></h3>
            <a class="print-button" href="javascript:window.print()" title="<?php _e( 'Print this page', 'iwjob' ); ?>"><i class="ion-android-print"></i></a>
        </div>
        <form action="" method="post" class="iwj-update-application-form">
            <?php if($author){
            ?>
            <div class="avatar">
                <?php echo get_avatar($author->get_id()); ?>
            </div>
            <?php
            } ?>
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
                <li>
                    <div class="title">
                        <?php echo __('Action', 'iwjob'); ?>
                    </div>
                    <div class="value">
                        <input type="hidden" name="application_id" value="<?php echo $application_id; ?>">
                        <div class="iwj-button-loader">
                            <button class="iwj-btn iwj-btn-primary iwj-update-appication-btn"><i class="ion-checkmark-round"></i><?php echo __('Save', 'iwjob'); ?></button>
                            <?php if(iwj_option('email_application_enable')){ ?>
                                <button class="iwj-btn iwj-btn-primary iwj-update2-appication-btn"><i class="fa fa-envelope"></i><?php echo __('Save & Send Email', 'iwjob'); ?></button>
                            <?php } ?>
                        </div>
                        <div class="iwj-respon-msg iwj-hide"></div>
                    </div>
                </li>
            </ul>
            <div class="clear"></div>
        </form>

        <?php if(iwj_option('email_application_enable')){
            $application_emails = IWJ_Application::get_emails();
            ?>
            <div class="modal fade" id="iwj-application-email-modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form class="iwj-application-email-form" action="<?php the_permalink(); ?>" method="post" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h4 class="modal-title"><?php echo __('Send an email to candidate','iwjob'); ?></h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <select name="email_type" id="application_email">
                                    <option value=""><?php echo __('Select A Letter Template', 'iwjob'); ?></option>
                                    <?php foreach ($application_emails as $email){
                                        echo '<option value="'.$email['id'].'">'.$email['title'].'</option>';
                                    } ?>
                                </select>
                                <textarea id="application_email_value" style="display: none"><?php echo json_encode($application_emails); ?></textarea>
                                <input type="text" name="subject" placeholder="<?php echo __('Enter Email Subject', 'iwjob'); ?>" value="">
                                <?php
                                iwj_field_wysiwyg('message', '', true, '', '', '', __('You can use tags: #employer_name#, #employer_email#, #candidate_name#, #candidate_email#', 'iwjob'),'Enter Email Message', array(
                                    'quicktags' => false,
                                    'editor_height' => 250
                                ));
                                ?>
                            </div>
                            <div class="iwj-respon-msg iwj-hide"></div>
                            <div class="modal-footer">
                                <input type="hidden" name="application_id" value="<?php echo $application->get_id(); ?>">
                                <div class="iwj-button-loader">
                                    <button class="iwj-btn iwj-btn-primary iwj-application-email-btn"><?php echo __('Send', 'iwjob'); ?></button>
                                </div>
                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cancel'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>

    <?php } ?>
</div>
