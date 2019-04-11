<div class="iwj-done">
    <?php
    $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
    if($order_id){
        iwj_get_template_part('parts/thankyou-page');
    }else{
        $job_id = isset($_GET['job-id']) ? $_GET['job-id'] : '';
        $job = IWJ_Job::get_job($job_id);
        $admin_email_receiver = iwj_option('admin_email_receiver', get_option('admin_email',''));
        if($job){
            ?>
            <div class="iwj-thankyou-page">
                <div class="thankyou-icon"><img src="<?php echo IWJ_PLUGIN_URL; ?>/assets/img/thankyou.jpg" alt=""></div>
                <div class="thankyou-panel">
                    <h3><?php echo __('Thank you for submitting','iwjob');?></h3>
                    <div class="success-txt">
                        <?php if($job->has_status('publish')){ ?>
                            <span class="job-publish"><i class="ion-android-checkmark-circle"></i></span>
                            <p><?php echo sprintf(__('Thank you for submitting, your job has been published. If you need help please contact us via email <a href="mailto:%s">%s</a>', 'iwjob'), $admin_email_receiver, $admin_email_receiver); ?></p>
                        <?php }elseif($job->has_status('pending')){ ?>
                            <span class="job-pending"><i class="ion-ios-clock-outline"></i></span>
							<p><?php echo sprintf(__('Thank you for submitting, this job is being reviewed. If you need help please contact us via email <a href="mailto:%s">%s</a>', 'iwjob'), $admin_email_receiver, $admin_email_receiver); ?></p>
                        <?php }elseif($job->has_status('pending-payment')){ ?>
                            <span class="job-pending"><i class="ion-ios-clock-outline"></i></span>
							<p><?php echo sprintf(__('Thank you for submitting, this job is being payment. If you need help please contact us via email <a href="mailto:%s">%s</a>', 'iwjob'), $admin_email_receiver, $admin_email_receiver); ?></p>
                        <?php } ?>
                    </div>
                    <ul>
                        <li>
                            <a class="iwj-btn-shadow iwj-btn-primary" href="<?php echo iwj_get_page_permalink('dashboard'); ?>"><i class="ion-home"></i> <?php echo __('Dashboard','iwjob'); ?></a>
                        </li>
                        <li>
                            <a class="iwj-btn-shadow iwj-btn-secondary" href="<?php echo $job->permalink(); ?>"><i class="ion-ios-eye"></i> <?php echo __('View Job','iwjob'); ?></a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php
        }
    }
    ?>
</div>