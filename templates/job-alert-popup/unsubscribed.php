<div id="iwj-job-alert-unsubscribed-popup" class="modal-popup modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo __('Unsubscribed receiving new jobs', 'iwjob'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="iwj-alert-submit-form-popup">
                    <div class="iwj-thankyou-page">
                        <div class="thankyou-icon"><img src="<?php echo IWJ_PLUGIN_URL; ?>/assets/img/thankyou.jpg" alt=""></div>
                        <div class="thankyou-panel">
                            <h3><?php echo __('Unsubscribed successfully','iwjob');?></h3>
                            <div class="success-txt">
                                <p><?php echo __('You have canceled the job of new registration. As you wish you can re-register it at any time','iwjob');?></p>
                            </div>
                            <ul>
                                <li>
                                    <a class="iwj-btn iwj-btn-primary" href="<?php echo home_url('/'); ?>"><i class="ion-home"></i> <?php echo __('Home','iwjob'); ?></a>
                                </li>
                                <li>
                                    <button type="button" class="iwj-btn" data-dismiss="modal"><?php echo __('Close', 'iwjob'); ?></button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


