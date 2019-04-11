<?php
$step = isset($_GET['step']) ? $_GET['step'] : 'form';
?>

<div class="iwj-make-featured iwj-main-block">
    <?php if($step != 'done'){ ?>
        <div class="iwj-task-bar job">
            <ul>
                 <li class="active"><span class="number">1</span><span class="desc"><?php echo __('Make Featured', 'iwjob');?></span></li>
                <li class="<?php echo $step == 'done' ? 'active' : '' ?>"><span class="number">2</span><span class="desc"><?php echo __('Done', 'iwjob');?></span></li>
            </ul>
        </div>
    <?php } ?>
    <div class="iwj-main">
        <?php echo $content; ?>
    </div>
</div>