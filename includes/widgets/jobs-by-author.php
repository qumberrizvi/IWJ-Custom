<?php

class IWJ_Widget_Classes_By_Author extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'description'                 => esc_html__( 'Display jobs by author, used only with sidebar job details.', 'iwjob' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'iwj_jobs_by_author', esc_html__( '[IWJ] Classes by Author', 'iwjob' ), $widget_ops );
	}

	public function widget( $args, $instance ) {
		$limit = isset($instance['limit']) ?  strip_tags($instance['limit']) : '5';
		$orderby = isset($instance['orderby']) ?  strip_tags($instance['orderby']) : 'date';
		$order = isset($instance['order']) ?  strip_tags($instance['order']) : 'DESC';

		$data = array(
			'args'      => $args,
			'instance'  => $instance,
			'widget_id' => $this->id_base,
			'parent'    => $this,
			'limit'    => $limit,
			'orderby'    => $orderby,
			'order'    => $order,
		);
		if ( is_single() && get_post_type() == 'iwj_job' ) {
			iwj_get_template_part( 'widgets/jobs-by-author', $data );
		}

	}

	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['limit'] = strip_tags($new_instance['limit']);
		$instance['orderby'] = strip_tags($new_instance['orderby']);
		$instance['order'] = strip_tags($new_instance['order']);

		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'limit' => 5, 'orderby' => 'date', 'order' => 'DESC' ) );
		$limit    = esc_attr( $instance['limit'] );
		$orderby  = esc_attr( $instance['orderby'] );
		$order    = esc_attr( $instance['order'] ); ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Limit:', 'iwjob' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php esc_html_e( 'Order By:', 'iwjob' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>">
				<option value="ID" <?php selected( $orderby, 'ID' ); ?>><?php _e( 'ID', 'iwjob' ); ?></option>
				<option value="date" <?php selected( $orderby, 'date' ); ?>><?php _e( 'Date', 'iwjob' ); ?></option>
				<option value="modified" <?php selected( $orderby, 'modified' ); ?>><?php _e( 'Modified', 'iwjob' ); ?></option>
				<option value="title" <?php selected( $orderby, 'title' ); ?>><?php _e( 'title', 'iwjob' ); ?></option>
				<option value="menu_order" <?php selected( $orderby, 'menu_order' ); ?>><?php _e( 'Ordering', 'iwjob' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php esc_html_e( 'Order:', 'iwjob' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>">
				<option value="DESC" <?php selected( $order, 'DESC' ); ?>><?php _e( 'DESC', 'iwjob' ); ?></option>
				<option value="ASC" <?php selected( $order, 'ASC' ); ?>><?php _e( 'ASC', 'iwjob' ); ?></option>
			</select>
		</p>
		<?php
	}

}

function iwj_widget_jobs_by_author() {
	register_widget( 'IWJ_Widget_Classes_By_Author' );
}

add_action( 'widgets_init', 'iwj_widget_jobs_by_author' );