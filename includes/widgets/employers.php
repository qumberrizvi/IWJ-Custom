<?php
/**
 * Widget API: IWJ_Top_Company class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */


class IWJ_Widget_Employers extends WP_Widget {

    public function __construct() {
        $widget_ops = array(
            'description' => esc_html__( 'Display Students Lists.', 'iwjob' ),
            'customize_selective_refresh' => true,
        );
        parent::__construct( 'iwj_employers', esc_html__( '[IWJ] Students', 'iwjob' ), $widget_ops );
    }

    public function widget( $args, $instance ) {

        $limit = isset($instance['limit']) ?  strip_tags($instance['limit']) : 12;
        $orderby = isset($instance['orderby']) ?  strip_tags($instance['orderby']) : 'date';
        $order = isset($instance['order']) ?  strip_tags($instance['order']) : 'DESC';
        $show_featured_employers = isset($instance['show_featured_employers']) ?  strip_tags($instance['show_featured_employers']) : '';

        if($orderby != 'total_jobs'){
            $args_company = array(
                'posts_per_page' => ($limit ? $limit : -1),
                'post_type' => 'iwj_employer',
                'post_status' => 'publish',
                'orderby' => $orderby,
                'order' => $order,
            );
	        if ( $show_featured_employers ) {
		        $args_company['meta_query'] = array(
			        'relation' => 'AND',
			        array(
				        'key'   => '_iwj_featured',
				        'value' => '1',
			        ),
		        );
	        }
            $employers = get_posts($args_company);
        }else{
            global $wpdb;
            $sql = "SELECT p.*, COUNT(p.ID) AS total_jobs
                    FROM {$wpdb->prefix}posts AS p
                    LEFT JOIN {$wpdb->prefix}posts AS p1 ON p.post_author = p1.post_author
                    ".(!iwj_option('show_expired_job' || $show_featured_employers) ? " JOIN {$wpdb->postmeta} AS pm ON pm.post_id = p.ID" : "")."
                    WHERE p.post_type = 'iwj_employer' AND p.post_status = 'publish' AND p1.post_type = 'iwj_job' AND p1.post_status = 'publish'
                    ".(!iwj_option('show_expired_job') ? " AND pm.meta_key = '".IWJ_PREFIX."expiry' AND (pm.meta_value = '' OR CAST(pm.meta_value AS UNSIGNED) > ".current_time('timestamp').")" : "")."
                    ".($show_featured_employers ? " AND (pm.meta_key = '".IWJ_PREFIX."featured' AND pm.meta_value ='1')":"")."
                    GROUP BY p1.post_author
                    ORDER BY total_jobs {$order}
                    LIMIT 0, {$limit}
                    ";
            $employers = $wpdb->get_results($sql);
        }

        $data = array(
            'args' => $args,
            'instance' => $instance,
            'widget_id' => $this->id_base,
            'parent' => $this,
            'employers' => $employers
        );

        iwj_get_template_part('widgets/employers', $data);
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['ids'] = strip_tags( $new_instance['ids'] );
        $instance['limit'] = strip_tags($new_instance['limit']);
        $instance['orderby'] = strip_tags($new_instance['orderby']);
        $instance['order'] = strip_tags($new_instance['order']);
        $instance['show_featured_employers'] = strip_tags($new_instance['show_featured_employers']);
        return $instance;
    }

    public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => __('Lastest Students', 'iwjob'), 'ids' => '', 'limit' => '12', 'orderby' => 'date', 'order' => 'DESC' ) );
        $title_id = $this->get_field_id( 'title' );
        $title = strip_tags($instance['title']);
        $limit = esc_attr($instance['limit']);
        $orderby = esc_attr($instance['orderby']);
        $order = esc_attr($instance['order']);
        $show_featured_employers = esc_attr($instance['show_featured_employers']);
        ?>
        <p><label for="<?php echo esc_attr($title_id); ?>"><?php echo __( 'Title:', 'iwjob'); ?></label>
            <input type="text" class="widefat" id="<?php echo esc_attr($title_id); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr($title); ?>" />
        </p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_featured_employers')); ?>"><?php esc_html_e('Show Students:', 'iwjob'); ?></label>
			<select class="widefat" id="<?php echo esc_attr($this->get_field_id('show_featured_employers')); ?>" name="<?php echo esc_attr($this->get_field_name('show_featured_employers')); ?>">
				<option value="" <?php selected($show_featured_employers, 'any'); ?>><?php _e('Any', 'iwjob'); ?></option>
				<option value="featured" <?php selected($show_featured_employers, 'featured'); ?>><?php _e('Featured', 'iwjob'); ?></option>
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
                <option value="total_jobs" <?php selected($orderby, 'total_jobs'); ?>><?php _e('Total Classes', 'iwjob'); ?></option>
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

function iwj_widget_employers() {
    register_widget('IWJ_Widget_Employers');
}
add_action('widgets_init', 'iwj_widget_employers');