<?php
/**
 * Widget API: IWJ_Recent_Resumes class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */


class IWJ_Widget_Candidates extends WP_Widget {

    public function __construct() {
        $widget_ops = array(
            'description' => esc_html__( 'Display Teachers list.', 'iwjob' ),
            'customize_selective_refresh' => true,
        );
        parent::__construct( 'iwj_candidates', esc_html__( '[IWJ] Teachers', 'iwjob' ), $widget_ops );
    }

    public function widget( $args, $instance ) {

        $limit = isset($instance['limit']) ?  strip_tags($instance['limit']) : 12;
        $orderby = isset($instance['orderby']) ? esc_attr($instance['orderby']) : 'date';
        $order = isset($instance['order']) ? esc_attr($instance['order']) : 'DESC';
	    $show_featured_candidates = isset($instance['show_featured_candidates']) ?  strip_tags($instance['show_featured_candidates']) : '';

        $args_candidate = array(
            'posts_per_page' => $limit,
            'post_type' => 'iwj_candidate',
            'orderby' => $orderby,
            'order' => $order,
        );
	    $args_company['meta_query'] = array(
		    'relation' => 'AND',
		);

	    if ( $show_featured_candidates ) {
		    $args_candidate['meta_query'][] =
			    array(
				    'key'   => '_iwj_featured',
				    'value' => '1',
					'compare' => '='
			    );
	    }

	    $args_candidate['meta_query'][] = array(
		    'relation' => 'OR',
		    array(
			    'key' => IWJ_PREFIX . 'public_account',
			    'compare' => 'NOT EXISTS' // doesn't work
		    ),
		    array(
			    'key'     => IWJ_PREFIX . 'public_account',
			    'value'   => 1,
			    'compare' => '='
		    )
	    );

        $candidates = get_posts($args_candidate);

        $data = array(
            'args' => $args,
            'instance' => $instance,
            'widget_id' => $this->id_base,
            'parent' => $this,
            'candidates' => $candidates,
        );

        iwj_get_template_part('widgets/candidates', $data);
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['limit'] = strip_tags($new_instance['limit']);
        $instance['orderby'] = strip_tags($new_instance['orderby']);
        $instance['order'] = strip_tags($new_instance['order']);
	    $instance['show_featured_candidates'] = strip_tags($new_instance['show_featured_candidates']);
        return $instance;
    }

    public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => __('Recent Resumes', 'iwjob'), 'limit' => '5', 'orderby' => 'date', 'order' => 'DESC' ) );
        $title = strip_tags($instance['title']);
        $limit = esc_attr($instance['limit']);
        $orderby = esc_attr($instance['orderby']);
        $order = esc_attr($instance['order']);
	    $show_featured_candidates = esc_attr($instance['show_featured_candidates']);
        ?>
        <p><label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php echo __( 'Title:', 'iwjob'); ?></label>
            <input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr($title); ?>" />
        </p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_featured_candidates')); ?>"><?php esc_html_e('Show Teachers:', 'iwjob'); ?></label>
			<select class="widefat" id="<?php echo esc_attr($this->get_field_id('show_featured_candidates')); ?>" name="<?php echo esc_attr($this->get_field_name('show_featured_candidates')); ?>">
				<option value="" <?php selected($show_featured_candidates, 'any'); ?>><?php _e('Any', 'iwjob'); ?></option>
				<option value="featured" <?php selected($show_featured_candidates, 'featured'); ?>><?php _e('Featured', 'iwjob'); ?></option>
			</select>
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

function iwj_widget_candidates() {
    register_widget('IWJ_Widget_Candidates');
}
add_action('widgets_init', 'iwj_widget_candidates');