<div class="iwj-thankyou-page">
    <div class="thankyou-icon"><img src="<?php echo IWJ_PLUGIN_URL; ?>/assets/img/thankyou.jpg" alt=""></div>
    <div class="thankyou-panel">
        <h3><?php echo __('Thank you for your verification','iwjob');?></h3>
        <div class="success-txt">
            <p><?php echo __('Your account has been verified.','iwjob');?></p>
        </div>
        <ul>
            <li>
                <a class="iwj-btn iwj-btn-primary" href="<?php echo home_url('/'); ?>"><i class="ion-home"></i> <?php echo __('Home','iwjob'); ?></a>
            </li>
            <li>
                <a class="iwj-btn iwj-btn-secondary" href="<?php echo iwj_get_page_permalink('dashboard'); ?>"><i class="ion-home"></i> <?php echo __('Dashboard','iwjob'); ?></a>
            </li>
        </ul>
    </div>
</div>
