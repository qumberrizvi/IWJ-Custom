<?php

class IWJ_Admin_Indeed_Imports {
	static $fields = array();

	static function init() {
		add_filter( 'iwj_plugin_indeed_imports', array( __CLASS__, 'general_import_field' ), 10, 1 );
	}

	static function general_import_field( $def ) {
		$args = array(
			'pad_counts'         => 1,
			'show_counts'        => 1,
			'hierarchical'       => 1,
			'hide_empty'         => 1,
			'show_uncategorized' => 1,
			'orderby'            => 'name',
			'menu_order'         => false
		);

		$terms_types = get_terms( 'iwj_type', $args );
		$types       = array();
		if ( is_wp_error( $terms_types ) ) {
		} else {
			if ( empty( $terms_types ) ) {
			} else {
				foreach ( $terms_types as $terms_type ) {
					$types[ $terms_type->term_id ] = $terms_type->name;
				}
			}
		}

		$terms_categories = get_terms( 'iwj_cat', $args );
		$categories       = array();
		if ( is_wp_error( $terms_categories ) ) {
		} else {
			if ( empty( $terms_categories ) ) {
			} else {
				foreach ( $terms_categories as $terms_category ) {
					$categories[ $terms_category->term_id ] = $terms_category->name;
				}
			}
		}
		$ide_publisher_id = get_option( 'iwj_ide_publisher_id', '' );

		$settings = array(
			'general' => array(
				'name'    => __( 'General', 'iwjob' ),
				'options' => array(
					array(
						'name'    => __( 'Indeed Class Import', 'iwjob' ),
						'options' => array(
							array(
								'name'     => __( 'Publisher Id', 'iwjob' ),
								'id'       => IWJ_PREFIX . 'publisher_id',
								'type'     => 'text',
								'std'      => $ide_publisher_id,
								'desc'     => __( 'Your Publisher ID from indeed. Don\'t you have such a key? <a href="https://ads.indeed.com/jobroll/signup" target="_blank">Request one here</a>', 'iwjob' ),
								'required' => true
							),
							array(
								'name'     => __( 'Keyword', 'iwjob' ),
								'id'       => IWJ_PREFIX . 'keyword',
								'type'     => 'text',
								'desc'     => __( 'Example: Design, WordPress, ...', 'iwjob' ),
								'required' => true
							),
							array(
								'name'     => __( 'Country', 'iwjob' ),
								'id'       => IWJ_PREFIX . 'country',
								'type'     => 'select_advanced',
								'options'  => iwj_countries_list(),
							),
							array(
								'name'     => __( 'Location', 'iwjob' ),
								'id'       => IWJ_PREFIX . 'location',
								'type'     => 'text',
								'desc'     => __( 'Use a postal code or a "city, state/province/region" combination.', 'iwjob' ),
							),
							array(
								'name'     => __( 'Job Type', 'iwjob' ),
								'id'       => IWJ_PREFIX . 'job_type',
								'type'     => 'select_advanced',
								'options'  => array(
									'contract'   => __( 'Contract', 'iwjob' ),
									'fulltime'   => __( 'Full-Time', 'iwjob' ),
									'parttime'   => __( 'Part-Time', 'iwjob' ),
									'internship' => __( 'Internship', 'iwjob' ),
									'temporary'  => __( 'Temporary', 'iwjob' ),
								),
								'desc'     => __( 'Choose one job type in the list that is allowed by Indeed.', 'iwjob' ),
							),
							array(
								'name' => __( 'Import From Number', 'iwjob' ),
								'id'   => IWJ_PREFIX . 'import_from_number',
								'type' => 'number',
								'std'  => '1',
							),
							array(
								'name'  => __( 'Max Item Import', 'iwjob' ),
								'id'    => IWJ_PREFIX . 'max_item_import',
								'type'  => 'number',
								'std'   => '10',
								'desc'  => __( 'Maximum value is 25 so we recommend that you should set the parameter for Max Item Import up to 10', 'iwjob' ),
								'class' => 'iwj_lim_ide_import'
							),
							array(
								'name' => __( 'Channel', 'iwjob' ),
								'id'   => IWJ_PREFIX . 'channel',
								'type' => 'text',
								'desc' => __( 'Channel Name: Group API requests to a specific channel.', 'iwjob' ),
							),
							array(
								'name'    => __( 'New Post Status', 'iwjob' ),
								'id'      => IWJ_PREFIX . 'new_post_status',
								'type'    => 'select_advanced',
								'options' => array(
									'publish' => __( 'Publish', 'iwjob' ),
									'pending' => __( 'Pending', 'iwjob' ),
									'draft'   => __( 'Draft', 'iwjob' ),
								),
								'std'     => 'publish',
							),
							array(
								'name'    => __( 'Import to Category', 'iwjob' ),
								'id'      => IWJ_PREFIX . 'category_name',
								'type'    => 'select_advanced',
								'options' => $categories,
							),
							array(
								'name'        => __( 'Author', 'iwjob' ),
								'id'          => IWJ_PREFIX . 'user_id',
								'type'        => 'user_ajax',
								'placeholder' => __( 'Select an Student', 'iwjob' ),
								'required'    => true
							),
						)
					),
				),
			),
		);

		return array_merge( $def, $settings );
	}

	static function management_page() {
		if ( isset( $_POST['iwj-security'] ) && wp_verify_nonce( $_POST['iwj-security'], 'iwj-indeed-import-job' ) ) {
			self::indeed_import_job();
		} ?>
		<div class="wrap iwj-setting-page iwj-indeed-job-imports">

			<?php
			$msgs = get_option( IWJ_PREFIX . 'ide_imports_messages' );
			if ( $msgs ) { ?>
				<div class="notice notice-success is-dismissible">
					<p>
						<?php if ( $msgs == 'empty' ) {
							echo __( 'Not found job imported.', 'iwjob' );
						}
						if ( $msgs == 'success' ) {
							echo __( 'Import successfully.', 'iwjob' );
						} ?>
					</p>
				</div>
				<?php
				delete_option( IWJ_PREFIX . 'ide_imports_messages' );
			}
			?>
			<form action="" method="post">
				<?php
				$plugin_indeed_imports = apply_filters( 'iwj_plugin_indeed_imports', array() );
				if ( $plugin_indeed_imports ) {
					foreach ( $plugin_indeed_imports AS $tab => $tab_settings ) {
						echo '<div id="iwj-tab-' . $tab . '"><table class="form-table">';
						foreach ( $tab_settings['options'] as $group ) {
							if ( $group['name'] ) {
								echo '<tr class="iwj-heading"><th colspan="2">';
								echo '<span>' . $group['name'] . '</span>';
								if ( isset( $group['desc'] ) && $group['desc'] ) {
									echo '<p>' . $group['desc'] . '</p>';
								}
								echo '</th></tr>';
							}

							if ( isset( $group['options'] ) && $group['options'] ) {

								foreach ( $group['options'] as $field ) {
									$field = IWJMB_Field::call( 'normalize', $field );
									$meta  = $field['std'];
									if ( $field['clone'] || $field['multiple'] ) {
										if ( empty( $meta ) || ! is_array( $meta ) ) {
											/**
											 * Note: if field is clonable, $meta must be an array with values
											 * so that the foreach loop in self::show() runs properly
											 *
											 * @see self::show()
											 */
											$meta = $field['clone'] ? array( '' ) : array();
										}
									}
									IWJMB_Field::input( $field, $meta );
								}

							}

						}
						echo '</table></div>';
					}
				}
				?>
				<?php wp_nonce_field( 'iwj-indeed-import-job', 'iwj-security' ); ?>
				<p class="submit">
					<button type="submit" class="button button-primary" name=""><?php echo __( 'Import', 'iwjob' ); ?></button>
				</p>
			</form>
		</div>
		<?php
	}

	static function indeed_import_job() {
		global $wpdb;
		$publisher_id    = sanitize_text_field( $_POST[ IWJ_PREFIX . 'publisher_id' ] );
		$feed_keyword    = sanitize_text_field( $_POST[ IWJ_PREFIX . 'keyword' ] );
		$feed_country    = sanitize_text_field( $_POST[ IWJ_PREFIX . 'country' ] );
		$feed_location   = sanitize_text_field( $_POST[ IWJ_PREFIX . 'location' ] );
		$feed_job_type   = sanitize_text_field( $_POST[ IWJ_PREFIX . 'job_type' ]);
		$from_number     = sanitize_text_field( $_POST[ IWJ_PREFIX . 'import_from_number' ] );
		$max_item_import = sanitize_text_field( $_POST[ IWJ_PREFIX . 'max_item_import' ] );
		$new_post_status = sanitize_text_field( $_POST[ IWJ_PREFIX . 'new_post_status' ] );
		$job_category    = sanitize_text_field( $_POST[ IWJ_PREFIX . 'category_name' ] );
		$user_id         = sanitize_text_field( $_POST[ IWJ_PREFIX . 'user_id' ] );
		$import_count    = 0;
		$channel         = sanitize_text_field( $_POST[ IWJ_PREFIX . 'channel' ] );

		$max_item_import = $max_item_import > 25 ? 25 : $max_item_import;
		update_option( 'iwj_ide_publisher_id', $publisher_id );

		$api_url       = 'http://api.indeed.com/ads/apisearch?publisher=' . $publisher_id . '&q=' . urlencode( $feed_keyword ) . '&l=' . urlencode( $feed_location ) . '&sort=relevance&radius=&st=&jt=' . urlencode( $feed_job_type ) . '&start=' . urlencode( $from_number ) . '&limit=' . urlencode( $max_item_import ) . '&fromage=%20&filter=&latlong=&co=' . urlencode( $feed_country ) . '&&userip=' . urlencode( iwj_indeed_job_importer_user_ip_address() ) . '&v=2&useragent=' . urlencode( isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : "Mozilla/5.0 (compatible; Konqueror/4.0; Microsoft Windows) KHTML/4.0.80 (like Gecko)" ) . ( ( $channel != '' ) ? '&chnl=' . urlencode( $channel ) : '' );
		$content       = iwj_indeed_job_importer_readFeeds( $api_url );
		$total_records = count( $content );

		if ( $total_records > 0 && is_array( $content ) ) {
			$arr_ids_imported = array();
			for ( $i = 0; $i < $total_records && $import_count < $max_item_import; $i ++ ) {
				$error            = false;
				$item_id          = wp_filter_nohtml_kses( $content[ $i ]['id'] );
				$item_title       = wp_filter_nohtml_kses( $content[ $i ]['title'] );
				$item_url         = wp_filter_nohtml_kses( $content[ $i ]['url'] );
				$item_description = stripslashes( $content[ $i ]['description'] );

				$item_f_location_full = wp_filter_nohtml_kses( $content[ $i ]['formatted_loc_full'] );
				$item_company         = wp_filter_nohtml_kses( $content[ $i ]['company'] );

				if ( strlen( $item_title ) <= 0 ) {
					$error = true;
				} elseif ( $result1 = $wpdb->get_var( $wpdb->prepare( "SELECT post_title FROM $wpdb->posts  INNER JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) WHERE meta_key = '_iwj_indeed_jobkey' AND meta_value='%s'", $item_id ) ) ) {
					$error = true;
				}
				if ( ! $error ) {
					$sql_data_array = array(
						'post_title'   => $item_title,
						'post_content' => $item_description,
						'post_type'    => 'iwj_job',
						'post_author'  => $user_id,
						'post_status'  => $new_post_status,
					);

					// add new job
					$import_id = wp_insert_post( $sql_data_array );

					if ( $import_id ) {
						update_post_meta( $import_id, IWJ_PREFIX . 'featured', '0' );
						wp_set_post_terms( $import_id, $job_category, 'iwj_cat' );

						if ( $feed_job_type && ! iwj_option( 'disable_type' ) ) {
							$ide_term = get_term_by( 'slug', $feed_job_type, 'iwj_type' );
							if ( ! $ide_term ) {
								$new_term    = wp_insert_term( $feed_job_type, 'iwj_type' );
								$ide_term_id = $new_term['term_id'];
							} else {
								$ide_term_id = $ide_term->term_id;
							}
							wp_set_post_terms( $import_id, $ide_term_id, 'iwj_type' );
						}

						if ( $feed_country ) {
							$name_country      = iwj_get_country_titles( $feed_country );
							$country_term_name = get_term_by( 'name', $name_country, 'iwj_location' );
							if ( ! $country_term_name ) {
								$new_term_ct = wp_insert_term( $name_country, 'iwj_location' );
								$ct_term_id  = $new_term_ct['term_id'];
							} else {
								$ct_term_id = $country_term_name->term_id;
							}
							wp_set_post_terms( $import_id, $ct_term_id, 'iwj_location' );
						}

						update_post_meta( $import_id, IWJ_PREFIX . 'indeed_jobkey', $item_id );
						update_post_meta( $import_id, IWJ_PREFIX . 'import_source', 'indeed' );
						update_post_meta( $import_id, IWJ_PREFIX . 'address', $item_f_location_full );
						update_post_meta( $import_id, IWJ_PREFIX . 'import_url', $item_url );
						update_post_meta( $import_id, IWJ_PREFIX . 'import_company', $item_company );

						update_post_meta( $import_id, IWJ_PREFIX . 'expiry', '' );


						$import_count       = $import_count + 1;
						$arr_ids_imported[] = $import_id;
					}
					do_action( 'iwj_add_new_job', $import_id );
				}
			}
			if ( count( $arr_ids_imported ) > 0 ) {
				update_option( IWJ_PREFIX . 'ide_imports_messages', 'success' );
			} else {
				update_option( IWJ_PREFIX . 'ide_imports_messages', 'empty' );
			}
		} else {
			update_option( IWJ_PREFIX . 'ide_imports_messages', 'empty' );
		}
	}

}

IWJ_Admin_Indeed_Imports::init();