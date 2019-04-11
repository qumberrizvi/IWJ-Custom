<?php if(isset($package) && $package){
    $price = $package->get_price();
    $tax_price = iwj_get_tax_price($price);

    if($tax_price !== false) {
        $total_price = $price + $tax_price;
        $tax_value = iwj_option('tax_value');
    }else{
        $total_price = $price;
    }
    ?>

    <div class="package-price"><span class="title"><?php echo sprintf(__('Package %s', 'iwjob'), $package->get_title()); ?> :</span><span><?php echo iwj_system_price($package->get_price()); ?></span></div>
    <?php if($tax_price !== false) { ?>
        <div class="package-price"><span class="title"><?php echo __('Tax - ', 'iwjob'). $tax_value. '% :'; ?></span><span><?php echo iwj_system_price($tax_price); ?></span></div>
    <?php } ?>
    <div class="total-price"><span class="title"><?php echo __('Total price : ', 'iwjob'); ?></span><span><?php echo iwj_system_price($total_price); ?></span></div>
<?php } ?>
<?php if(isset($user_package) && $user_package){ ?>
    <div class="package-price"><span class="title"><?php echo $user_package->get_package_title(); ?>:</span><span><?php echo __('Actived', 'iwjob'); ?></span></div>
    <div class="total-price"><span class="title"><?php echo __('Total price : ', 'iwjob'); ?></span><span><?php echo __('Paid', 'iwjob'); ?></span></div>
<?php } ?>