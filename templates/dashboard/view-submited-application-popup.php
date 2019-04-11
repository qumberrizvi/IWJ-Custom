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
                do_action('iwj_view_application_before_message', $application, true);
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
                do_action('iwj_view_application_after_message', $application, true);
                ?>
            </ul>
        </form>
    <?php } ?>
</div>
