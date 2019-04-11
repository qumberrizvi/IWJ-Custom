<?php
/**
 * Widget API: IWJ_Widget_Employer class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */


class IWJ_Widget_Employer_Map extends WP_Widget {

    public function __construct() {
        $widget_ops = array(
            'description' => esc_html__( 'Display Student Map.', 'iwjob' ),
            'customize_selective_refresh' => true,
        );
        parent::__construct( 'iwj_employer_map', esc_html__( '[IWJ] Student Map', 'iwjob' ), $widget_ops );
    }

    public function widget( $args, $instance ) {

        $data = array(
            'args' => $args,
            'instance' => $instance,
            'widget_id' => $this->id_base,
            'parent' => $this,
        );

        iwj_get_template_part('widgets/employer_map', $data);
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        return $instance;
    }
    public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '') );
        $title = strip_tags($instance['title']);
        ?>
        <p><label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php echo __( 'Title:', 'iwjob'); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr($title); ?>" />
		</p>
		<?php
    }

}

function iwj_widget_employer_map() {
    register_widget('IWJ_Widget_Employer_Map');
}
add_action('widgets_init', 'iwj_widget_employer_map');