<?php
$post = get_post();
$employer = IWJ_Employer::get_employer($post);
$author = $employer->get_author();
if(is_single() && $post && $post->post_type == 'iwj_employer' ) {
    $data_map_maker = IWJ_PLUGIN_URL.'/assets/images/map-marker-job.png';
    $map_maker = iwj_option('iwj_map_maker');
    if ($map_maker) {
        $data_map_maker = esc_url( wp_get_attachment_url($map_maker[0]) );
    }
    $maps = $employer->get_map();
    $lat = $maps[0];
    $lng = $maps[1];
    $zoom = $maps[2];

    if ($lat && $lng) {
        echo $args['before_widget'];
        if (isset($instance['title'])) {
            $title = (!empty($instance['title'])) ? $instance['title'] : '';
            $title = apply_filters('widget_title', $title, $instance, $widget_id);
            if ($title) {
                echo $args['before_title'] . $title . $args['after_title'];
            }
        }
        ?>
        <div class="iwj-employer-widget-wrap">
            <div class="location iwj-map iwj-single-widget">
                <div id="job-detail-map" class="job-detail-map" data-zoom="<?php echo esc_attr($zoom); ?>" data-lat="<?php echo esc_attr($lat); ?>" data-lng="<?php echo esc_attr($lng); ?>" data-maker="<?php echo esc_attr($data_map_maker); ?>"
                     data-style="style2" style="height: 270px;">
                </div>
            </div>
        </div>
        <?php
        echo $args['after_widget'];
    }
}