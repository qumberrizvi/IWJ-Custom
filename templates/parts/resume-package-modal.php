<div class="modal fade" id="iwj-modal-view-<?php echo $candidate->get_id(); ?>" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo __('View Profile Information','iwjob'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <?php
                $user = IWJ_User::get_user();
                $user_package_ids = $user->get_user_package_ids('resum_package');
                $new_resum_package_url = add_query_arg(array('iwj_tab'=>'new-resume-package'), iwj_get_page_permalink('dashboard'));
                if(!$user_package_ids){
                    echo iwj_get_alert(sprintf(__('To view Information you need buy a Resume Package <a href="%s">Buy Now</a>', 'iwjob'), $new_resum_package_url), 'info');
                }
                else{
                    $can_view_packages = array();
                    $expired_packages = array();
                    $pending_payment_packages = array();
                    foreach ($user_package_ids as $user_package_id){
                        $user_package = IWJ_User_Package::get_user_package($user_package_id);
                        if($user_package->can_view_resum()){
                            $can_view_packages[] = $user_package;
                        }elseif($user_package->get_status() == 'iwj-expired'){
                            $expired_packages[] = $user_package;
                        }elseif($user_package->get_status() == 'iwj-pending-payment'){
                            $pending_payment_packages[] = $user_package;
                        }
                    }

                    if(count($can_view_packages) >= 1){ ?>
                        <form action="<?php get_the_permalink() ; ?>" method="post" class="iwj-view-resume-form">
                            <?php
                            if(count($can_view_packages) == 1){
                                $user_package = $can_view_packages[0];
                                echo '<ul>';
                                echo '<li>'.sprintf(__('<span class="package-title">Your Package:</span> %s', 'iwjob'), $user_package->get_package_title()).'</li>';
                                echo '<li>'.sprintf(__('<span class="package-title">Remaining Resume:</span> %d', 'iwjob'), $user_package->get_remain_resum()).'</li>';
                                echo '</ul>';
                                ?>
                                <input type="hidden" name="user_package" value="<?php echo $user_package->get_id();?>">
                                <?php
                            }else{
                                echo '<div class="form-title">'.__('Your Packages.', 'iwjob').'</div>';
                                echo '<div class="your-packages">';
                                $i = 0;
                                foreach ($can_view_packages as $user_package){
                                    $id = 'input-' . rand(10000, 99999);
                                    $i++;
                                    ?>
                                    <div class="your-package-item">
                                        <input <?php echo $i== 1 ? 'checked' : ''; ?> id="<?php echo esc_attr($id); ?>" class="custom-input-radio" type="radio" name="user_package" value="<?php echo $user_package->get_id();?>"><label for="<?php echo esc_attr($id); ?>"></label>
                                        <?php
                                        echo sprintf(__('<span class="package-title">Package:</span> %s', 'iwjob'), $user_package->get_title());
                                        echo'<span> - </span>';
                                        echo sprintf(__('<span class="package-title">Remaining Resume:</span> %d', 'iwjob'), $user_package->get_remain_resum());
                                        ?>
                                    </div>
                                    <?php
                                }
                                echo '</div>';
                            }
                            ?>
                            <input type="hidden" name="resum_id" value="<?php echo get_the_ID();?>">
                            <div class="iwj-respon-msg iwj-hide"></div>
                            <div class="iwj-button-loader">
                                <button type="submit" class="iwj-btn iwj-btn-icon iwj-btn-primary iwj-view-resume-btn"><?php echo __('<i class="ion-android-send"></i> Continue', 'iwjob'); ?></button>
                            </div>
                        </form>
                        <?php
                    }elseif($pending_payment_packages){
                        echo iwj_get_alert(sprintf(__('Your package is pending payment. <a href="%s">Buy Other Package</a>', 'iwjob'), $new_resum_package_url), 'info');
                    }elseif($expired_packages){
                        echo iwj_get_alert(sprintf(__('Your package expired. <a href="%s">Buy New Package</a>', 'iwjob'), $new_resum_package_url), 'info');
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>