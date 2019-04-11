<?php if ($resumes_slider) {
    wp_enqueue_style('owl-carousel');
    wp_enqueue_style('owl-theme');
    wp_enqueue_style('owl-transitions');
    wp_enqueue_script('owl-carousel');

    $data_plugin_options = array(
        "navigation"=>true,
        "autoHeight"=>true,
        "pagination"=>false,
        "autoPlay"=>($auto_play ? true : false),
        "paginationNumbers"=>false,
        "singleItem"=>true,
        "navigationText" => array('<i class="ion-android-arrow-back"></i>', '<i class="ion-android-arrow-forward"></i>')
    );
?>
    <div class="iw-resumes-slider <?php echo $class; ?>">
        <div class="heading-block">
            <?php if ($title_block) { ?>
                <h3 class="title-block"><?php echo esc_html(count($resumes_slider).' '); echo esc_html($title_block); ?></h3>
            <?php } ?>
            <?php if ($description_block) { ?>
                <div class="desc-block"><?php echo esc_html($description_block); ?></div>
            <?php } ?>
        </div>
        <div class="owl-carousel" data-plugin-options="<?php echo htmlspecialchars(json_encode($data_plugin_options)); ?>">
            <div class="resume-items">
                <div class="row">
                    <?php
                    $item_per_slider = $candidates_per_slider ? $candidates_per_slider : 6;
                    $item_class =  'col-item col-md-2 col-sm-3 col-xs-6';
                    if($item_per_slider == '1'){
                        $item_class =  'col-item col-md-12 col-sm-12 col-xs-12';
                    }elseif($item_per_slider == '2'){
                        $item_class =  'col-item col-md-6 col-sm-6 col-xs-6';
                    }elseif($item_per_slider == '3'){
                        $item_class =  'col-item col-md-4 col-sm-3 col-xs-6';
                    }elseif($item_per_slider == '4'){
                        $item_class =  'col-item col-md-3 col-sm-3 col-xs-6';
                    }elseif($item_per_slider == '6'){
                        $item_class =  'col-item col-md-2 col-sm-3 col-xs-6';
                    }

                    $i = 0;
                    $number_item = 12 % $item_per_slider;
                    foreach ($resumes_slider as $resume_slider) :
                        $resume_slider = IWJ_Candidate::get_candidate($resume_slider);
                        $user = IWJ_User::get_user();

                        $image = iwj_get_avatar( $resume_slider->get_author_id(), '', '', $resume_slider->get_title(), array('img_size'=>'inwave-avatar2') );
                        if($i > 0 && count($resumes_slider) > $i && $i % $item_per_slider == 0){
                            echo '</div>
                            </div>
                            <div class="resume-items">
                            <div class="row">';
                        }
                        ?>
                        <div class="<?php echo esc_attr($item_class); ?>">
                            <div class="resumes-avatar"><a href="<?php echo esc_url($resume_slider->permalink()); ?>"><?php echo ($image); ?></a></div>
                        </div>
                        <?php
                        $i ++;
                    endforeach; ?>
                </div>
            </div>
        </div>
        <?php if ($button_text && $button_link) { ?>
            <h6 class="link-browse-all">
                <a class="theme-color-hover" href="<?php echo esc_url($button_link); ?>"><?php echo esc_html($button_text); ?><i class="fa fa-arrow-circle-right"></i></a>
            </h6>
        <?php } ?>
    </div>
<?php } ?>