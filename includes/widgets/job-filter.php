<?php

class IWJ_Widget_Job_Filter extends WP_Widget {

    public function __construct() {
        $widget_ops = array(
            'description' => esc_html__( 'Filter Classes Widget.', 'iwjob'),
            'customize_selective_refresh' => true,
        );

        parent::__construct( 'iwj_job_filter', esc_html__( '[IWJ] Filter Classes', 'iwjob'), $widget_ops );
    }

    public function widget( $args, $instance ) {

        //get taxonomies support
        $list_taxonomies = array();
        $job_taxonomies = iwj_get_job_taxonomies();
        foreach ($job_taxonomies as $job_tax) {
            if (isset($instance[$job_tax]) && is_numeric($instance[$job_tax])) {
                $index = (int) $instance[$job_tax];
                if (isset($list_taxonomies[$index])) {
                    $new_index = max(array_keys($list_taxonomies)) + 1;
                    $list_taxonomies[$new_index] = $job_tax;
                } else {
                    $list_taxonomies[$index] = $job_tax;
                }
            }
        }

        ksort($list_taxonomies);

        //get term objects
        $taxonomies = get_terms( array('taxonomy' => $list_taxonomies, 'hide_empty' => false));

        //current term
        $current_term = get_queried_object();

        //get data filters
        $filters = IWJ_Job_Listing::get_data_filters();

        $filters_data = $filters;
        //get terms filtered objects
        $filters_data['taxonomies'] = array();
        $filters_data['taxonomy_ids'] = array();
        if ($filters) {
            $taxonomy_types = array();
            $term_ids = array();
            foreach ($job_taxonomies as $job_tax) {
               if(isset($filters[$job_tax]) && $filters[$job_tax]){
                   $term_ids = array_merge($term_ids, $filters[$job_tax]);
               }
            }
            if($term_ids){
                $filters_data['taxonomy_ids'] = $term_ids;
                $filters_data['taxonomies'] = get_terms( array('taxonomy' => $taxonomy_types, 'include' => $term_ids));
            }
        }

        //count jobs for terms
        $terms_has_job = iwj_count_item_by_taxonomy($filters);
        if ($taxonomies) {
            foreach ($taxonomies as $taxonomy) {
                if ( isset($terms_has_job[$taxonomy->term_id]) ) {
                    $taxonomy->total_post = $terms_has_job[$taxonomy->term_id]->total_post;
                } else {
                    $taxonomy->total_post = 0;
                }
            }
        }
        $taxonomies_data = $taxonomies;

        //sort terms by count
        usort($taxonomies_data, function ($item1, $item2) {
            if ($item1->total_post == $item2->total_post) return 0;
            return $item1->total_post < $item2->total_post ? 1 : -1;
        });

        //hide taxonomy if is taxonomy page
        if (is_array($instance) && !empty($instance)) {
            foreach ($instance as $instance_key =>  $instance_item) {
                if (is_tax(iwj_get_job_taxonomies()) && is_object($current_term) && isset($current_term->taxonomy) && ($current_term->taxonomy == $instance_key)) {
                    unset($instance[$instance_key]);
                }
            }
        }

        $list_taxonomies_data = array();

        foreach ($list_taxonomies as $list_taxonomy){

            $terms_request = isset($filters_data[$list_taxonomy]) ? $filters_data[$list_taxonomy] : array();
            $terms_ud = array();
            $terms_salary = array();
            foreach($taxonomies_data as $term){
                if($term->taxonomy == $list_taxonomy){
                	if($list_taxonomy == 'iwj_salary'){
		                $terms_salary[] = $term;
					}else{
		                $terms_ud[] = $term;
					}
                }
            }

            /*iwj_salary sort by order key*/
	        usort( $terms_salary, function ( $item1, $item2 ) {
		        $term_order_1 = get_term_meta( $item1->term_id, IWJ_PREFIX . 'salary_order', true );
		        $term_order_2 = get_term_meta( $item2->term_id, IWJ_PREFIX . 'salary_order', true );
		        if ( $term_order_1 == $term_order_2 ) {
			        return 0;
		        }

		        return $term_order_1 < $term_order_2 ? -1 : 1;
	        } );
	        $terms = array_merge( $terms_salary, $terms_ud );

            $terms_sorted = $terms_list = array();
            foreach($terms as $term){
                if (in_array($term->term_id, $terms_request)) {
                    $terms_sorted[$term->term_id] = $term;
                } else {
                    $terms_list[$term->term_id] = $term;
                }
            }

            $list_taxonomies_data[$list_taxonomy] = array_merge($terms_sorted, $terms_list);
        }

        $data = array(
                'args' => $args,
                'instance' => $instance,
                'widget_id' => $this->id_base,
                'parent' => $this,
                'list_taxonomies' => $list_taxonomies,
                'list_taxonomies_data' => $list_taxonomies_data,
                'filters_data' => $filters_data
        );

        iwj_get_template_part('widgets/job-filter', $data);
    }


    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $job_taxs = iwj_get_job_taxonomies();
        if($job_taxs){
            foreach($job_taxs as $tax){
                $instance[$tax] = sanitize_text_field( $new_instance[$tax] );
                $instance[$tax.'_limit'] = sanitize_text_field( $new_instance[$tax.'_limit'] );
                $instance[$tax.'_limit_show_more'] = sanitize_text_field( $new_instance[$tax.'_limit_show_more'] );
                $instance[$tax.'_hide_empty_item'] = sanitize_text_field( $new_instance[$tax.'_hide_empty_item'] );
                $instance[$tax.'_hierarchy_cat'] = sanitize_text_field( $new_instance[$tax.'_hierarchy_cat'] );
            }
        }

        $instance = apply_filters('iwj_widget_job_filter_update', $instance, $new_instance, $old_instance, $this);

        return $instance;
    }

    public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => __('Job Filter', 'iwjob') ) );
        $title_id = $this->get_field_id( 'title' );
        $title = strip_tags($instance['title']);
        echo '<p><label for="' . $title_id .'">' . __( 'Title:', 'iwjob') . '</label>
			<input type="text" class="widefat" id="' . $title_id .'" name="' . $this->get_field_name( 'title' ) .'" value="' . $title .'" />
		</p>';
        ?>

        <?php
        do_action('iwj_before_widget_from_job_filter', $this, $instance);
        ?>

        <?php
        $job_taxs = iwj_get_job_taxonomies();
        foreach ($job_taxs as $job_tax_item) {
            $value = isset($instance[$job_tax_item]) ? strip_tags($instance[$job_tax_item]) : '1';
            $field_id = $this->get_field_id( $job_tax_item );
            ?>
            <p><label for="<?php echo $field_id; ?>"><strong><?php echo iwj_get_taxonomy_title($job_tax_item); ?></strong></label>
                <select class="widefat" id="<?php echo $field_id; ?>" name="<?php echo $this->get_field_name( $job_tax_item ); ?>">
                    <option <?php echo ($value == 'hide') ? 'selected' : ''; ?> value="hide"><?php echo __('Hide', 'iwjob'); ?></option>
                    <?php
                    for ($i = 1 ; $i<= count($job_taxs); $i++){
                        echo '<option value="'.$i.'" '.selected((int)$value, $i, false).'>'.$i.'</option>';
                    }
                    ?>
                </select>
            </p>

            <?php
            $key_limit = $job_tax_item . '_limit';
            $field_id_limit = $this->get_field_id( $key_limit );
            $value = isset($instance[$key_limit]) ? $instance[$key_limit] : 5;
            ?>
            <p>
                <label for="<?php echo $field_id_limit; ?>"><?php echo __('Items display', 'iwjob'); ?></label>
                <input type="text" id="<?php echo $field_id_limit; ?>" name="<?php echo $this->get_field_name( $key_limit ); ?>" value="<?php echo $value; ?>" style="width:100%" />
            </p>

            <?php
            $key_limit_show_more = $job_tax_item . '_limit_show_more';
            $field_id_limit = $this->get_field_id( $key_limit_show_more );
            $value = isset($instance[$key_limit_show_more]) ? $instance[$key_limit_show_more] : 20;
            ?>
            <p>
                <label for="<?php echo $field_id_limit; ?>"><?php echo __('Limit', 'iwjob'); ?></label>
                <input type="text" id="<?php echo $field_id_limit; ?>" name="<?php echo $this->get_field_name( $key_limit_show_more ); ?>" value="<?php echo $value; ?>" style="width:100%" />
            </p>

	        <?php
	        $key_hide_empty_item = $job_tax_item . '_hide_empty_item';
	        $field_id_hide_empty = $this->get_field_id( $key_hide_empty_item );
	        $value = isset($instance[$key_hide_empty_item]) ? $instance[$key_hide_empty_item] : 0;
	        ?>
			<p>
				<label for="<?php echo $field_id_hide_empty; ?>"><?php echo __('Hide items 0 post', 'iwjob'); ?></label>
				<select class="widefat" id="<?php echo $field_id_hide_empty; ?>" name="<?php echo $this->get_field_name( $key_hide_empty_item ); ?>">
					<option <?php echo selected( "0", $value, false ); ?> value="0"><?php echo __('No', 'iwjob'); ?></option>
					<option <?php echo selected( "1", $value, false ); ?> value="1"><?php echo __('Yes', 'iwjob'); ?></option>
				</select>
			</p>
			<?php
				if($job_tax_item == 'iwj_cat'){
					$key_hierarchy_cat = $job_tax_item . '_hierarchy_cat';
					$field_id_hierarchy_cat = $this->get_field_id( $key_hierarchy_cat );
					$value = isset($instance[$key_hierarchy_cat]) ? $instance[$key_hierarchy_cat] : 0;
					?>
					<p>
						<label for="<?php echo $field_id_hierarchy_cat; ?>"><?php echo __('Hierarchy', 'iwjob'); ?></label>
						<select class="widefat" id="<?php echo $field_id_hierarchy_cat; ?>" name="<?php echo $this->get_field_name( $key_hierarchy_cat ); ?>">
							<option <?php echo selected( "0", $value, false ); ?> value="0"><?php echo __('No', 'iwjob'); ?></option>
							<option <?php echo selected( "1", $value, false ); ?> value="1"><?php echo __('Yes', 'iwjob'); ?></option>
						</select>
					</p>
					<?php
				}
			?>
            <hr>
            <?php
        }

        do_action('iwj_after_widget_form_job_filter', $this, $instance);
    }
}

function iwj_widget_job_filter() {
    register_widget('IWJ_Widget_Job_Filter');
}
add_action('widgets_init', 'iwj_widget_job_filter');