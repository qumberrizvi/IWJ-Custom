<?php
$user = IWJ_User::get_user();
?>

<?php if(!$user && !iwj_option('allow_guest_apply_job')){ ?>
    <a href="#" class="apply-job" data-toggle="modal" data-target="#iwj-login-popup" data-fallback="show_apply_form"><i class="ion-android-checkbox-outline"></i><?php echo __('Apply for Class', 'iwjob'); ?></a>
<?php }else {
    $cookie = 'iwj_apply_' . $job->get_id();
    if (isset($_COOKIE[$cookie]) && $_COOKIE[$cookie]) { ?>
        <a href="javascript:void(0)" class="apply-job applied"><i class="ion-android-checkbox-outline"></i><?php echo __('Applied this job', 'iwjob'); ?></a>
    <?php } else { ?>
        <a href="#" class="apply-job" data-toggle="modal" data-target="#iwj-modal-apply-<?php echo get_the_ID(); ?>">
            <i class="ion-android-checkbox-outline"></i><?php echo __('Apply for Class', 'iwjob'); ?>
        </a>
        <?php
        wp_enqueue_style('iwj-apply-form');
        wp_enqueue_script('iwj-apply-form');
    }
}
?>
