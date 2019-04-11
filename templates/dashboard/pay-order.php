<?php
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
$order = IWJ_Order::get_order($order_id);
$step = isset($_GET['step']) ? $_GET['step'] : 'form';
$user = IWJ_User::get_user();
?>
<div class="iwj-pay-order-page iwj-main-block">
    <?php if($step != 'done'){ ?>
        <div class="iwj-task-bar job">
            <ul>
                <li class="active"><span class="number">1</span><span class="desc"><?php echo __('Payment', 'iwjob');?></span></li>
                <li class="<?php echo $step == 'done' ? 'active' : 'no-active' ?>"><span class="number">2</span><span class="desc"><?php echo __('Done', 'iwjob');?></span></li>
            </ul>
        </div>
    <?php } ?>
    <div class="iwj-main">
        <?php echo $content; ?>
    </div>
</div>
