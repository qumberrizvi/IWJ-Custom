<div class="alert alert-<?php echo $type; ?>">
    <?php if($dismissable) {?>
    <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
    <?php } ?>
    <?php echo $message; ?>
</div>