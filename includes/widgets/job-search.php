<?php


class IWJ_Widget_Simple_Search extends WP_Widget {

    public function __construct() {
        $widget_ops = array(
            'description' => esc_html__( 'Search jobs, employers, candidates.', 'iwjob'),
            'customize_selective_refresh' => true,
        );
        parent::__construct( 'iwj_simple_search', esc_html__( '[IWJ] Simple Class Search', 'iwjob'), $widget_ops );
    }

    public function widget( $args, $instance ) {

            /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
            $title = (!empty($instance['title'])) ? $instance['title'] : __('Search Class', 'iwjob');
            $title = apply_filters('widget_title', $title, $instance, $this->id_base);

            $type = isset($instance['type']) ? $instance['type'] : 'job';

            echo $args['before_widget'];
            if ($title) {
                echo $args['before_title'] . $title . $args['after_title'];
            }
            $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
            ?>
            <div class="job-search sidebar-job-1">
                <form name="iwjob-search" id="iwjob-search">
                    <div class="input-group">
                        <input name="keyword" type="text" class="form-control" placeholder="<?php echo __('Search', 'iwj'); ?>" value="<?php echo esc_html($keyword); ?>">
                        <input name="type" type="hidden" value="<?php echo esc_html($type); ?>" />
                        <div class="input-group-btn">
                            <button class="btn btn-default btn-iwjob-search" type="button">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <?php
            echo $args['after_widget'];

    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['type'] = sanitize_text_field( $new_instance['type'] );
        return $instance;
    }

    public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => __('Search Class', 'iwjob') ) );
        $title_id = $this->get_field_id( 'title' );
        $title = strip_tags($instance['title']);
        echo '<p><label for="' . $title_id .'">' . __( 'Title:' ) . '</label>
			<input type="text" class="widefat" id="' . $title_id .'" name="' . $this->get_field_name( 'title' ) .'" value="' . $title .'" />
		</p>';

        $type = isset($instance['type']) ? strip_tags($instance['type']) : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php echo __( 'Search type:', 'iwjob'); ?></label>
            <select name="<?php echo $this->get_field_name( 'type' ); ?>">
                <option <?php echo ($type == 'job') ? 'selected' : ''; ?> value="job"><?php echo __('Job', 'iwjob'); ?></option>
                <option <?php echo ($type == 'candidate') ? 'selected' : ''; ?> value="candidate"><?php echo __('Teacher', 'iwjob'); ?></option>
                <option <?php echo ($type == 'employer') ? 'selected' : ''; ?> value="employer"><?php echo __('Student', 'iwjob'); ?></option>
            </select>
        </p>
        <?php
    }

}

function iwj_widget_simple_search() {
    register_widget('IWJ_Widget_Simple_Search');
}
add_action('widgets_init', 'iwj_widget_simple_search');