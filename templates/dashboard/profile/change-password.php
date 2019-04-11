<div class="iwj-change-password iwj-block">
    <form action="" method="post" class="iwj-form-2 iwj-change-password-form">
    <?php
    echo '<h3 class="">'.__('Change Password', 'iwjob').'</h3>';
    ?>
    <div class="row">
        <div class="col-md-6">
            <?php
            iwj_field_password('current_password', __('Current Password', 'iwjob'), true, 0, '');
            ?>
        </div>
        <div class="col-md-6">
            <?php
            iwj_field_password('new_password', __('New Password', 'iwjob'), true, 0, '');
            ?>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="iwj-respon-msg iwj-hide"></div>
    <div class="iwj-button-loader">
        <button type="submit" class="iwj-btn iwj-btn-primary iwj-change-password-btn"><?php echo __('Change Password', 'iwjob');?></button>
        <?php if(!current_user_can('administrator')){ ?>
        <button type="button" class="iwj-btn iwj-btn-iwj-btn-danger iwj-delete-account-btn" data-confirm-delete="<?php echo __("Are you sure you want to delete your account?", 'iwjob'); ?>"><?php echo __('Delete My Account', 'iwjob');?></button>
        <?php } ?>
    </div>
    </form>
</div>