<?php
/**
 * Widget API: IWJ_Widget_Classes class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */


class IWJ_Widget_Classes extends WP_Widget {

    public function __construct() {
        $widget_ops = array(
            'description' => esc_html__( 'Display Classes List.', 'iwjob' ),
            'customize_selective_refresh' => true,
        );
        parent::__construct( 'iwj_jobs', esc_html__( '[IWJ] Classes', 'iwjob' ), $widget_ops );
    }

    public function widget( $args, $instance ) {

        $limit = isset($instance['limit']) ?  strip_tags($instance['limit']) : '5';
        $orderby = isset($instance['orderby']) ?  strip_tags($instance['orderby']) : 'date';
        $order = isset($instance['order']) ?  strip_tags($instance['order']) : 'DESC';
        $args_job = array(
            'posts_per_page' => $limit,
            'post_type' => 'iwj_job',
            'offset' => 0,
            'category' => 0,
            'orderby' => $orderby,
            'order' => $order,
            'post_status' => 'publish',
        );


        if(!iwj_option('show_expired_job')){
            $args_job['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key'     => IWJ_PREFIX.'expiry',
                    'value' => '',
                    'compare' => '=',
                ),
                array(
                    'key'     => IWJ_PREFIX.'expiry',
                    'value' => current_time('timestamp'),
                    'compare' => '>',
                    'type' => 'NUMERIC'
                ));
        }
        
        $jobs = get_posts($args_job);

        $data = array(
            'args' => $args,
            'instance' => $instance,
            'widget_id' => $this->id_base,
            'parent' => $this,
            'jobs' => $jobs,
        );

        iwj_get_template_part('widgets/jobs', $data);
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['limit'] = strip_tags($new_instance['limit']);
        $instance['orderby'] = strip_tags($new_instance['orderby']);
        $instance['order'] = strip_tags($new_instance['order']);
        return $instance;
    }
    public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => __('Latest Classes', 'iwjob'), 'limit'=>5 , 'orderby' => 'date', 'order' => 'DESC') );
        $title = strip_tags($instance['title']);
        $limit = esc_attr($instance['limit']);
        $orderby = esc_attr($instance['orderby']);
        $order = esc_attr($instance['order']);
        ?>
        <p><label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php echo __( 'Title:', 'iwjob'); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
            <label for="<?php echo esc_attr($this->get_field_id('limit')); ?>"><?php esc_html_e('Limit:', 'iwjob'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('limit')); ?>" name="<?php echo esc_attr($this->get_field_name('limit')); ?>" type="text" value="<?php echo esc_attr($limit); ?>" />
        </p>
		<p>
            <label for="<?php echo esc_attr($this->get_field_id('orderby')); ?>"><?php esc_html_e('Order By:', 'iwjob'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('orderby')); ?>" name="<?php echo esc_attr($this->get_field_name('orderby')); ?>">
                <option value="ID" <?php selected($orderby, 'ID'); ?>><?php _e('ID', 'iwjob'); ?></option>
                <option value="date" <?php selected($orderby, 'date'); ?>><?php _e('Date', 'iwjob'); ?></option>
                <option value="modified" <?php selected($orderby, 'modified'); ?>><?php _e('modified', 'iwjob'); ?></option>
                <option value="title" <?php selected($orderby, 'title'); ?>><?php _e('title', 'iwjob'); ?></option>
                <option value="menu_order" <?php selected($orderby, 'menu_order'); ?>><?php _e('Ordering', 'iwjob'); ?></option>
            </select>
        </p>
		<p>
            <label for="<?php echo esc_attr($this->get_field_id('order')); ?>"><?php esc_html_e('Order:', 'iwjob'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('order')); ?>" name="<?php echo esc_attr($this->get_field_name('order')); ?>">
                <option value="DESC" <?php selected($order, 'DESC'); ?>><?php _e('DESC', 'iwjob'); ?></option>
                <option value="ASC" <?php selected($order, 'ASC'); ?>><?php _e('ASC', 'iwjob'); ?></option>
            </select>
        </p>
		<?php
    }

}

function iwj_widget_jobs() {
    register_widget('IWJ_Widget_Classes');
}
add_action('widgets_init', 'iwj_widget_jobs');