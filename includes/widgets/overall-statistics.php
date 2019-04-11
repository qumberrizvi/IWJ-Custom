<?php
/**
 * Widget API: IWJ_Widget_Overall_Statistics class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */


class IWJ_Widget_Overall_Statistics extends WP_Widget {

    public function __construct() {
        $widget_ops = array(
            'description' => esc_html__( 'Display Overall Statistics.', 'iwjob' ),
            'customize_selective_refresh' => true,
        );
        parent::__construct( 'iwj_overall_statistics', esc_html__( '[IWJ] Overall Statistics', 'iwjob' ), $widget_ops );
    }

    public function widget( $args, $instance ) {

        $data = array(
            'args' => $args,
            'instance' => $instance,
            'widget_id' => $this->id_base,
            'parent' => $this,
        );

        iwj_get_template_part('widgets/overall-statistics', $data);
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['show_employers'] = strip_tags($new_instance['show_employers']);
        $instance['employers'] = strip_tags($new_instance['employers']);
        $instance['show_resums'] = strip_tags($new_instance['show_resums']);
        $instance['resumes'] = strip_tags($new_instance['resumes']);
        $instance['show_jobs'] = strip_tags($new_instance['show_jobs']);
        $instance['jobs'] = strip_tags($new_instance['jobs']);
        return $instance;
    }

    public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array(
            'title' => __('Overall statistics', 'iwjob'),
            'show_employers' => '1',
            'employers' => '',
            'show_resums' => '1',
            'resumes' => '',
            'show_jobs' => '1',
            'jobs' => '',
            )
        );
        $title_id = $this->get_field_id( 'title' );
        $title = strip_tags($instance['title']);
        $show_employers = esc_attr($instance['show_employers']);
        $employers = esc_attr($instance['employers']);
        $show_resums = esc_attr($instance['show_resums']);
        $resumes = esc_attr($instance['resumes']);
        $show_jobs = esc_attr($instance['show_jobs']);
        $jobs = esc_attr($instance['jobs']);
        ?>
        <p><label for="<?php echo esc_attr($title_id); ?>"><?php echo __( 'Title:', 'iwjob' ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr($title_id); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr($title); ?>" />
		</p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('show_employers')); ?>"><?php esc_html_e('Show Students:', 'iwjob'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('show_employers')); ?>" name="<?php echo esc_attr($this->get_field_name('show_employers')); ?>">
                <option value="1" <?php selected($show_employers, '1'); ?>><?php _e('Yes', 'iwjob'); ?></option>
                <option value="0" <?php selected($show_employers, '0'); ?>><?php _e('No', 'iwjob'); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('employers')); ?>"><?php esc_html_e('Students:', 'iwjob'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('employers')); ?>" name="<?php echo esc_attr($this->get_field_name('employers')); ?>" type="text" value="<?php echo $employers; ?>" />
            <span><?php echo __('If empty we will count from the database', 'iwjob'); ?></span>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('show_resums')); ?>"><?php esc_html_e('Show Resumes:', 'iwjob'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('show_resums')); ?>" name="<?php echo esc_attr($this->get_field_name('show_resums')); ?>">
                <option value="1" <?php selected($show_resums, '1'); ?>><?php _e('Yes', 'iwjob'); ?></option>
                <option value="0" <?php selected($show_resums, '0'); ?>><?php _e('No', 'iwjob'); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('resumes')); ?>"><?php esc_html_e('Created resumes:', 'iwjob'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('resumes')); ?>" name="<?php echo esc_attr($this->get_field_name('resumes')); ?>" type="text" value="<?php echo esc_attr($resumes); ?>" />
            <span><?php echo __('If empty we will count from the database', 'iwjob'); ?></span>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('show_jobs')); ?>"><?php esc_html_e('Show Classes:', 'iwjob'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('show_jobs')); ?>" name="<?php echo esc_attr($this->get_field_name('show_jobs')); ?>">
                <option value="1" <?php selected($show_jobs, '1'); ?>><?php _e('Yes', 'iwjob'); ?></option>
                <option value="0" <?php selected($show_jobs, '0'); ?>><?php _e('No', 'iwjob'); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('jobs')); ?>"><?php esc_html_e('Posted jobs:', 'iwjob'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('jobs')); ?>" name="<?php echo esc_attr($this->get_field_name('jobs')); ?>" type="text" value="<?php echo esc_attr($jobs); ?>" />
            <span><?php echo __('If empty we will count from the database', 'iwjob'); ?></span>
        </p>
    <?php
    }
}

function iwj_widget_overall_statistics() {
    register_widget('IWJ_Widget_Overall_Statistics');
}
add_action('widgets_init', 'iwj_widget_overall_statistics');