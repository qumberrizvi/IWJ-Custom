<?php
if ( isset( $_COOKIE['job-archive-view'] ) && $_COOKIE['job-archive-view'] == 'grid' ) {
	$list_control_class = '';
	$grid_control_class = 'active';
} else {
	$list_control_class = 'active';
	$grid_control_class = '';
} ?>
<div class="iwj-content-inner">

	<div class="iwj-filter-form">
		<div class="jobs-layout-form">
			<form>
				<div class="show-filter-mobile"><?php echo __( 'Show Filter', 'iwjob' ); ?></div>
				<div class="layout-switcher">
					<ul>
						<li class="<?php echo $list_control_class; ?>">
							<a href="#" class="iwj-layout layout-list"><i class="ion-navicon"></i></a>
						</li>
						<li class="<?php echo $grid_control_class; ?>">
							<a href="#" class="iwj-layout layout-grid"><i class="ion-grid"></i></a>
						</li>
					</ul>
				</div>

				<select class="default-sorting sorting-candidates iwj-select-2-wsearch" name="orderby">
					<?php echo iwj_order_list_candidates(); ?>
				</select>

			</form>
            <?php if(iwj_option('show_rss_feed_candidate')){ ?>
                <div class="iwj-alert-feed">
                    <a href="<?php echo IWJ_Candidate_Listing::get_feed_url(); ?>" class="iwj-feed iwj-candidate-feed"><i class="fa fa-rss"></i></a>
                </div>
            <?php } ?>
		</div>
	</div>

	<div id="iwajax-load-candidates" class="<?php echo $fixcols;?>">
		<?php
		if ( isset( $_COOKIE['job-archive-view'] ) && $_COOKIE['job-archive-view'] == 'grid' ) {
			iwj_get_template_part( 'parts/candidates/candidates-grid', array( 'query' => $query ) );
		} else {
			iwj_get_template_part( 'parts/candidates/candidates-list', array( 'query' => $query ) );
		}
		?>

	</div>

	<?php
	global $wp;
	$current_url = home_url( add_query_arg( array(), $wp->request ) );
	?>

	<input type="hidden" name="url" id="url" value="<?php echo $current_url; ?>">
</div>
