<?php
/**
 * Widget API: IWJ_Widget_Candidate_Contact_Form class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */


class IWJ_Widget_Candidate_Contact_Form extends WP_Widget {

    public function __construct() {
        $widget_ops = array(
            'description' => esc_html__( 'Display Teacher Contact Form, used only with sidebar employer details.', 'iwjob' ),
            'customize_selective_refresh' => true,
        );
        parent::__construct( 'iwj_candidate_contact_form', esc_html__( '[IWJ] Teacher Contact Form', 'iwjob' ), $widget_ops );
    }

    public function widget( $args, $instance ) {

        $data = array(
            'args' => $args,
            'instance' => $instance,
            'widget_id' => $this->id_base,
            'parent' => $this,
        );

        iwj_get_template_part('widgets/candidate_contact_form', $data);
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

function iwj_widget_candidate_contact_form() {
    register_widget('IWJ_Widget_Candidate_Contact_Form');
}
add_action('widgets_init', 'iwj_widget_candidate_contact_form');