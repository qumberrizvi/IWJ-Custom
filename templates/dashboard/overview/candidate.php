<?php
$user = IWJ_User::get_user();
if($user){
$name = $user->get_display_name();
$headline = $user->get_headline();
$description = $user->get_description();
$candidate = $user->get_candidate();
$address = $user->get_address();
$phone = $user->get_phone();
$website = $user->get_website();
$email = $user->get_email();
$permalink = $user->permalink();
$dashboard_url = iwj_get_page_permalink('dashboard');
wp_enqueue_script('google-maps');
wp_enqueue_script('infobox');
wp_enqueue_style('jquery-fancybox');
wp_enqueue_script('jquery-fancybox');

?>
<div class="info-top-wrap">
    <div class="sidebar-info">
        <div class="avatar"><?php echo get_avatar($user->get_id(), 150); ?></div>
        <a class="iwj-edit-profile" href="<?php echo add_query_arg(array('iwj_tab' => 'profile'), $dashboard_url); ?>"><i class="fa fa-edit"></i><?php echo __('Edit My Profile', 'iwjob');?></a>
    </div>
    <div class="main-info candidate-info iw-job-detail-sidebar">
        <div class="info-top">
            <h3 class="iwj-title"><a href="<?php echo esc_url($permalink); ?>"><?php echo $name; ?></a></h3>
            <div class="headline"><?php echo $headline ? $headline : __('Headline', 'iwjob'); ?></div>
        </div>
        <div class="iwj-sidebar-bottom info-bottom">
            <div class="description"><?php echo $description ? esc_attr(wp_trim_words($description, 20)) : __('Click edit profile to update your description.', 'iwjob'); ?></div>
            <ul>
                <li class="location">
                    <div class="left">
                        <i class="ion-ios-location"></i>
                        <span class="title-meta theme-color"><?php _e('Locations:', 'iwjob'); ?></span>
                    </div>
                    <div class="content"><?php echo $address ? $address :  __('Click edit profile to update your address.', 'iwjob');?></div>
                </li>
                <li class="phone">
                    <div class="left">
                        <i class="ion-android-phone-portrait"></i>
                        <span class="title-meta theme-color"><?php _e('Phone:', 'iwjob'); ?></span>
                    </div>
                    <div class="content"><?php echo $phone ? $phone : __('Click edit profile to update your phone.', 'iwjob'); ?></div>
                </li>
                <li class="email">
                    <div class="left theme-color">
                        <i class="iwj-icon-email"></i>
                        <span class="title-meta"><?php _e('Email:', 'iwjob'); ?></span>
                    </div>
                    <div class="content"><a href="<?php echo $email ? 'mailto:'.$email : '#'; ?>"><?php echo $email ? $email : __('Click edit profile to update your email.', 'iwjob'); ?></a></div>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="iwj-candicate-detail iw-profile-content">
    <div class="candicate-main-content">
        <div class="resume-detail-info">
            <?php
            if($candidate && $experience = $candidate->get_experience()){
                ?>
                <div class="work-experience">
                    <div class="title"><?php echo __('Work experience', 'iwjob'); ?></div>
                    <ul class="time-line">
                        <?php
                        foreach ($experience as $item){
                            ?>
                            <li>
                                <div class="position-company top"><span class="position"><?php echo $item['title']; ?></span><?php echo $item['company'] ?></div>
                                <div class="date"><?php echo $item['date']; ?></div>
                                <div class="desc"><?php echo apply_filters('the_content',$item['description']); ?></div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>

            <?php if($candidate && $education = $candidate->get_education()){?>
                <div class="education">
                    <div class="title"><?php echo __('Education', 'iwjob'); ?></div>
                    <ul class="time-line">
                        <?php
                        foreach ($education as $item){
                            ?>
                            <li>
                                <div class="speciality-school top"><span class="speciality"><?php echo $item['title']; ?></span><?php echo $item['school_name']; ?></div>
                                <div class="date"><?php echo $item['date']; ?></div>
                                <div class="desc"><?php echo apply_filters('the_content',$item['description']); ?></div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>

            <?php if($candidate && $skill = $candidate->get_skill_showcase()){ ?>
                <div class="skills">
                    <div class="title"><?php echo __('Skills', 'iwjob'); ?></div>
                    <ul class="theme-color">
                        <?php
                        foreach ($skill as $item){
                            ?>
                            <li>
                                <div class="title-skill"><?php echo $item['title']; ?></div>
                                <div class="scoring">
                                    <span class="line theme-bg" style="width: <?php echo $item['value']; ?>%;"></span>
                                    <span class="percent"><?php echo $item['value'].'%'; ?></span>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>

            <?php if($candidate && $gallery = $candidate->get_gallery()){ ?>
                <div class="iwj-gallery">
                    <div class="title"><?php echo __('Portfolio', 'iwjob'); ?></div>
                    <div class="image-list">
                        <?php
                        foreach ($gallery as $image) {
                            $image_url = wp_get_attachment_url( $image );
                            if($image_url){
                                echo '
                                            <a rel="iwj-gallery" href="'.$image_url.'">
                                                <img class="srch-photo" src="'.$image_url.'" alt="">
                                            </a>';
                            }
                        } ?>
                    </div>
                </div>
            <?php } ?>

            <?php if($candidate && $award = $candidate->get_award()){ ?>
                <div class="honors-awards">
                    <div class="title"><?php echo __('Honors & awards', 'iwjob'); ?></div>
                    <ul class="time-line">
                        <?php
                        foreach ($award as $item){
                            ?>
                            <li>
                                <div class="title"><?php echo $item['title']; ?></div>
                                <div class="date"><?php echo $item['year']; ?></div>
                                <div class="desc"><?php echo apply_filters('the_content',$item['description']); ?></div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>

            <?php
            if($candidate && $maps = $candidate->get_map()){
                $data_map_maker = IWJ_PLUGIN_URL.'/assets/images/map-marker-job.png';
                $map_maker = iwj_option('iwj_map_maker');
                if ($map_maker) {
                    $data_map_maker = esc_url( wp_get_attachment_url($map_maker[0]) );
                }
                $lat = $maps[0];
                $lng = $maps[1];
                $zoom = $maps[2];
                ?>
                <?php if ($lat && $lng) : ?>
                    <div class="location iwj-map">
                        <div class="title"><?php _e('location', 'iwjob'); ?></div>
                        <div id="job-detail-map" class="job-detail-map" data-lat="<?php echo esc_attr($lat); ?>" data-lng="<?php echo esc_attr($lng); ?>" data-maker="<?php echo esc_attr($data_map_maker); ?>"
                             data-address="<?php echo esc_attr($candidate->get_address()); ?>" data-style="style1" style="height: 332px;">
                        </div>
                    </div>
                <?php endif; ?>
            <?php } ?>
        </div>

    </div>
</div>
<?php }
