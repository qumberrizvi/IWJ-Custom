<?php
$column = $atts['number_column'];
?>
<div class="iwj-content-inner">
	<div id="iwajax-load">
		<div class="iwj-jobs <?php echo $column == 2 ? 'iwj-style-match-height iwj-listing' : 'iwj-listing'; ?> iwj-jobs-listing-term iwj-jobs-listing-term1">
			<div class="iwj-job-items <?php echo $atts['style']; ?>">
				<?php
				if($query->have_posts()){
					while ($query->have_posts()) :
						$query->the_post();
						iwj_get_template_part('parts/jobs/job', array('style' => $atts['style']));
					endwhile;
					wp_reset_postdata();
				}else{
					echo '<div class="iwj-alert-box">' . __( 'No class found', 'iwjob' ) . '</div>';
				} ?>
				<div class="clearfix"></div>
			</div>
			<?php if( $atts['show_load_more'] && $query->max_num_pages > 1 ): ?>
				<div class="w-pag-load-more iwj-button-loader">
					<button class="iwj-btn iwj-btn-primary iwj-showmore" data-style="<?php echo $atts['style']; ?>" data-max_number_posts="<?php echo esc_attr($query->found_posts); ?>" data-taxonomies="<?php echo htmlspecialchars( $taxonomies, ENT_QUOTES, 'UTF-8' ); ?>" data-posts_per_page="<?php echo $atts['limit']; ?>" data-include_id="<?php echo esc_attr($atts['include_ids']); ?>" data-exclude_id="<?php echo esc_attr($atts['exclude_ids']); ?>"><?php echo __( 'Show More', 'iwjob' ); ?></button>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>