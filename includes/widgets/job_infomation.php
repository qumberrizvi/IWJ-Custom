<?php
/**
 * Widget API: IWJ_Widget_Job_Infomation class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */


class IWJ_Widget_Job_Infomation extends WP_Widget {

    public function __construct() {
        $widget_ops = array(
            'description' => esc_html__( 'Display Class Information, used only with sidebar job details.', 'iwjob' ),
            'customize_selective_refresh' => true,
        );
        parent::__construct( 'iwj_job_infomation', esc_html__( '[IWJ] Class Information', 'iwjob' ), $widget_ops );
    }

    public function widget( $args, $instance ) {

        $data = array(
            'args' => $args,
            'instance' => $instance,
            'widget_id' => $this->id_base,
            'parent' => $this,
        );

        iwj_get_template_part('widgets/job_infomation', $data);
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['style'] = sanitize_text_field( $new_instance['style'] );
        return $instance;
    }
    public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '') );
        $title = strip_tags($instance['title']);
        $style = strip_tags($instance['style']);
        ?>
        <p><label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php echo __( 'Title:', 'iwjob'); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr($title); ?>" />
		</p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('style')); ?>"><?php esc_html_e('Style:', 'iwjob'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('style')); ?>" name="<?php echo esc_attr($this->get_field_name('style')); ?>">
                <option value="style1" <?php selected($style, 'style1'); ?>><?php _e('Style 1', 'iwjob'); ?></option>
                <option value="style2" <?php selected($style, 'style2'); ?>><?php _e('Style 2', 'iwjob'); ?></option>
            </select>
        </p>
		<?php
    }

}

function iwj_widget_job_infomation() {
    register_widget('IWJ_Widget_Job_Infomation');
}
add_action('widgets_init', 'iwj_widget_job_infomation');