<?php 
$step = isset($_GET['step']) ? $_GET['step'] : 'form';
?>
<div class="iwj-new-package iwj-main-block">
    <?php if($step != 'done'){ ?>
	<div class="task-bar">
		<ul>
			<li class="active"><span class="number">1</span><span class="desc"><?php echo __('Package & Payment', 'iwjob');?></span></li>
			<li class="<?php echo $step == 'done' ? 'active' : '' ?>"><span class="number">2</span><span class="desc"><?php echo __('Done', 'iwjob');?></span></li>
		</ul>
	</div>
    <?php } ?>
	<div class="main">
		<?php echo $content; ?>
	</div>
</div>