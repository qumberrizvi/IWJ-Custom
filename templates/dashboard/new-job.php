<?php
$step = isset($_GET['step']) ? $_GET['step'] : 'form';
$user = IWJ_User::get_user();
$step_number = 2;
?>
<div class="iwj-new-job iwj-main-block">
        <?php if($step != 'done'){ ?>
        <div class="iwj-task-bar job">
            <ul>
                <li class="active"><span class="number">1</span><span class="desc"><?php echo __('Job Infomation', 'iwjob');?></span></li>
                <?php if(iwj_option('submit_job_mode') == '1'){
                    $step_number++;
                ?>
                <li class="<?php echo $step == 'select-package' || $step == 'done' ? 'active' : 'no-active' ?>"><span class="number">2</span><span class="desc"><?php echo __('Package & Payment', 'iwjob');?></span></li>
                <?php }elseif(iwj_option('submit_job_mode') == '2'){
                    $step_number++;
                ?>
                <li class="<?php echo $step == 'payment' || $step == 'payment' ? 'active' : 'no-active' ?>"><span class="number">2</span><span class="desc"><?php echo __('Payment', 'iwjob');?></span></li>
                <?php } ?>
                <li class="<?php echo $step == 'done' ? 'active' : 'no-active' ?>"><span class="number"><?php echo $step_number; ?></span><span class="desc"><?php echo __('Done', 'iwjob');?></span></li>
            </ul>
        </div>
        <?php } ?>
        <div class="iwj-main">
            <?php echo $content; ?>
        </div>
</div>
