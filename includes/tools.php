<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class IWJ_Admin_Classes_Tools {
	static $fields = array();

	static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_load_scripts' ) );
		add_action( 'iwj_jobs_tools_tab', array( __CLASS__, 'jobs_tools_tab' ), 1 );
		add_action( 'iwj_jobs_tools_content', array( __CLASS__, 'jobs_tools_content' ), 1 );
		add_filter( 'iwj_plugin_jobs_export', array( __CLASS__, 'general_export_field' ), 10, 1 );
	}

	static function admin_load_scripts() {
		if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'iwj-jobs-tools' ) {
			wp_enqueue_style( 'sweet-alert-dev', IWJ_PLUGIN_URL . '/assets/css/sweetalert.css', array() );

			wp_enqueue_script( 'icheck', IWJ_PLUGIN_URL . '/assets/js/icheck.min.js', 'jquery' );
			wp_enqueue_script( 'sweet-alert-dev', IWJ_PLUGIN_URL . '/assets/js/sweetalert-dev.js', array( 'jquery' ) );
			wp_enqueue_script( 'admin-imports-exports', IWJ_PLUGIN_URL . '/assets/js/admin_imports_exports.js', array( 'jquery' ) );
		}
	}

	static function management_page() {
		global $iwj_active_tab;
		$iwj_active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'iwj-export'; ?>
		<h2 class="nav-tab-wrapper">
			<?php do_action( 'iwj_jobs_tools_tab' ); ?>
		</h2>
		<div class="wrap iwj-setting-page iwj-jobs-imports">
			<?php do_action( 'iwj_jobs_tools_content' ); ?>
		</div>
		<?php
	}

	static function jobs_tools_tab() {
		global $iwj_active_tab; ?>
		<a class="nav-tab <?php echo $iwj_active_tab == 'iwj-export' || '' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url( 'edit.php?post_type=iwj_job&page=iwj-jobs-tools&tab=iwj-export' ); ?>"><?php echo esc_html__( 'Export', 'iwjob' ); ?> </a>
		<a class="nav-tab <?php echo $iwj_active_tab == 'iwj-import' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url( 'edit.php?post_type=iwj_job&page=iwj-jobs-tools&tab=iwj-import' ); ?>"><?php echo esc_html__( 'Import', 'iwjob' ); ?> </a>
		<?php
	}

	static function jobs_tools_content() {
		global $iwj_active_tab;
		if ( ( 'iwj-import' || 'iwj-export' || '' ) != $iwj_active_tab ) {
			return;
		}

		if ( $iwj_active_tab == 'iwj-export' || $iwj_active_tab == '' ) {
			self::jobs_export_content_page();
		}

		if ( $iwj_active_tab == 'iwj-import' ) {
			self::jobs_import_content_page();
		}

	}

	static function jobs_export_content_page() { ?>
		<form action="" method="post" class="iwj-form-exports-job">
			<?php
			$plugin_jobs_import = apply_filters( 'iwj_plugin_jobs_export', array() );
			if ( $plugin_jobs_import ) {
				foreach ( $plugin_jobs_import AS $tab => $tab_settings ) {
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
			} ?>
			<?php wp_nonce_field( 'iwj-jobs-import', 'iwj-security' ); ?>
			<p class="submit">
				<button type="submit" class="button button-primary iwj-button-export" name=""><?php echo __( 'Export', 'iwjob' ); ?></button>
			</p>
		</form>
		<div class="iwj-loading-exports hidden">
			<img src="<?php echo IWJ_PLUGIN_URL . '/assets/images/bx_loader.gif'; ?>" alt="<?php echo esc_html__( 'Loading', 'iwjob' ); ?>">
		</div>
		<div id="iwj-download-link" class="hidden">
			<p class="iwj-export-msg"></p>
			<a href=""><?php echo esc_html__( 'Download', 'iwjob' ); ?></a>
		</div>
		<?php
	}

	static function jobs_import_content_page() {
		$parserObj = new SmackCSVParser();
		$step      = isset( $_REQUEST['step'] ) ? sanitize_title( $_REQUEST['step'] ) : '';
		switch ( $step ) {
			case 'import_file':
				self::form_import_file();
				break;
			case 'mapping_config':
				if ( isset( $_REQUEST['eventKey'] ) ? sanitize_key( $_REQUEST['eventKey'] ) : '' ) :
					if ( isset( $_POST ) && ! empty( $_POST ) ) :
						$_POST                 = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
						$parserObj->screenData = array( 'import_file' => $_POST );
						update_option( $_REQUEST['eventKey'], $parserObj->screenData );
					else:
						$parserObj->screenData = get_option( $_REQUEST['eventKey'] );
					endif;
				endif;
				if ( empty( $parserObj->screenData ) ):
					$parserObj->wp_session = __( 'Your mapping configuration may lost. Please configure your mapping again!', 'iwjob' );
				endif;

				self::form_mapping_configuration();
				break;
			case 'confirm':
				self::form_ignite_import();
				break;
			default:
				self::form_import_file();
				break;
		}
	}

	static function general_export_field( $def ) {
		$settings = array(
			'general' => array(
				'name'    => __( 'General', 'iwjob' ),
				'options' => array(
					array(
						'name'    => __( ' ', 'iwjob' ),
						'options' => array(
							array(
								'name'     => __( 'Specific Types', 'iwjob' ),
								'id'       => IWJ_PREFIX . 'specific_job_type',
								'type'     => 'select_advanced',
								'options'  => array(
									'iwj_job'       => esc_html__( 'Classes', 'iwjob' ),
									'iwj_employer'  => esc_html__( 'Students', 'iwjob' ),
									'iwj_candidate' => esc_html__( 'Teachers', 'iwjob' ),
								),
								'desc'     => __( 'Choose types you want to export.', 'iwjob' ),
								'multiple' => true,
								'required' => true
							),
						)
					),
				),
			),
		);

		return array_merge( $def, $settings );
	}

	static function form_import_file() { ?>
		<form class="form-horizontal" method="post" id="form_import_file" action="<?php echo esc_url( admin_url() . 'edit.php?post_type=iwj_job&page=iwj-jobs-tools&tab=iwj-import&step=mapping_config' ); ?>" enctype="multipart/form-data">
			<div id='iwj_importjob_sec' class="col-md-12" style="display: none;">
				<div id="iwj_displayname">
					<div id="iwj_filename_display"></div>
				</div>
				<div class="">
					<div id="iwj_progress-div">
						<div id="iwj_progress-bar">
							<span class="progresslabel"></span>
						</div>
					</div>
					<div id="iwj_targetLayer"></div>
				</div>
				<div class="clearfix"></div>
				<div class="form-group iwj_mt10">
					<span class="import-if-duplicate"><label><?php echo esc_html__( 'If duplicate ID', 'iwjob' ); ?></label></span>
					<span>
						<label>
							<input type="radio" name="import_mode" value="ignore_items" checked="checked"><?php echo esc_html__( ' Ignore', 'iwjob' ); ?>
						</label>
						<label class="pl20">
							<input type="radio" name="import_mode" value="update_existing_items"><?php echo esc_html__( ' Update information', 'iwjob' ); ?>
						</label>
					</span>
				</div>
				<div id="iwj_select_module" class="select_module">
					<span><label class="import-textnew"><?php echo esc_html__( 'Import as post type', 'iwjob' ); ?></label></span>
					<span class="select_box" style="width:200px;height:40px;">
						  <select class="search_dropdown selectpicker" id="search_dropdowns" data-size="5" name="posttype" style="width:200px;height:39px;">
							  <?php
							  $post_types = array(
								  'iwj_job'       => __( 'Job', 'iwjob' ),
								  'iwj_candidate' => __( 'Teacher', 'iwjob' ),
								  'iwj_employer'  => __( 'Student', 'iwjob' ),
							  );
							  foreach ( $post_types as $key_pt => $post_type ) { ?>
								  <option value="<?php echo $key_pt; ?>"><?php echo $post_type; ?></option>
								  <?php
							  } ?>
						  </select>
					</span>
				</div>
				<div class=" iwj_mt20">
					<input type="submit" class="smack-btn smack-btn-primary btn-radius ripple-effect continue-btn" disabled value="<?php echo esc_html__( 'Continue', 'iwjob' ) ?>">
				</div>
			</div>
			<div class="bhoechie-tab-content active" id="iwj_division1">
				<div class="iwj_file_upload">
					<input id="iwj_upload_file" type="file" name="files[]" onchange="iwj_upload_method()" />
					<div class="file-upload-icon">
						<span id="iwj_fileupload" class="import-icon"> <img src="<?php echo IWJ_PLUGIN_URL . '/assets/img/upload-123.png'; ?>" width="60" height="60" /></span>
						<span class="file-upload-text"><?php echo esc_html__( 'Click here to upload from desktop', 'iwjob' ); ?></span>
					</div>
				</div>
			</div>
			<input type="hidden" id="uploaded_name" name="uploaded_name" value="">
			<input type="hidden" id="file_name" name="file_name" value="">
			<input type="hidden" id="file_extension" name="file_extension" value="">
			<input type="hidden" id="import_method" name="import_method" value="desktop">
			<input type="hidden" id="file_version" name="file_version" value="">
			<input type="hidden" id="upload_max" name="upload_max" value="<?php echo ini_get( 'upload_max_filesize' ); ?>">
		</form>
		<?php
	}

	static function form_mapping_configuration() {
		if ( $_POST ) {
			$_POST                  = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
			$records['import_file'] = $_POST;
			self::setPostValues( sanitize_key( $_REQUEST['eventkey'] ), $records );
		}
		$parserObj   = new SmackCSVParser();
		$eventKey    = sanitize_key( $_REQUEST['eventkey'] );
		$get_records = self::getPostValues( $eventKey );
		$import_type = $get_records[ $eventKey ]['import_file']['posttype'];
		/*if ( ! empty( $get_records[ $eventKey ]['mapping_config'] ) && $get_records[ $eventKey ]['mapping_config'] ) {
			$mapping_screendata = self::get_mapping_screendata( $import_type, $get_records[ $eventKey ]['mapping_config'] );
		}*/
		$file = IWJ_IMPORT_DIR . '/' . $eventKey . '/' . $eventKey;
		$parserObj->parseCSV( $file, 0, - 1 );
		$Headers        = $parserObj->get_CSVheaders();
		$Headers        = $Headers[0];
		$server_request = self::serverReq_data();
		$file           = IWJ_IMPORT_URL . '/' . $eventKey . '/' . $eventKey;
		$parserObj->parseCSV( $file, 0, - 1 );
		$total_row_count = $parserObj->total_row_cont - 1;
		$backlink        = esc_url( admin_url() . 'edit.php?post_type=iwj_job&page=iwj-jobs-tools&tab=iwj-import&step=import_file' );
		$actionURL       = esc_url( admin_url() . 'edit.php?post_type=iwj_job&page=iwj-jobs-tools&tab=iwj-import&step=confirm&eventkey=' . $_REQUEST['eventkey'] ); ?>
		<div class="template_body whole_body">
			<div>
				<h3 class="csv-importer-heading"><?php echo esc_html__( 'Mapping Section', 'iwjob' ); ?></h3>
			</div>
			<form id="mapping_section" method="post" action="<?php echo $actionURL; ?>">
				<div id='wp_warning' style='display:none;' class='error'></div>
				<div class="mapping_table">
					<?php
					$integrations = array(
						'Core Fields'  => 'CORE',
						'Extra Fields' => 'EXTRAFIELDS',
						'Term Fields'  => 'TERMFIELDS',
					);
					foreach ( $integrations as $widget_name => $plugin_file ) {
						$widget_slug = strtolower( str_replace( ' ', '_', $widget_name ) );
						$fields      = self::get_widget_fields( $widget_name, $import_type ); ?>
						<div class="panel-group" id='accordion_<?php echo $widget_name; ?>'>
							<div class='panel panel-default' data-target="#<?php echo $widget_slug; ?>" data-parent="#accordion_<?php echo $widget_name; ?>">
								<div id='<?php echo $widget_slug; ?>' class='panel-heading' onclick="toggle_func('<?php echo $widget_slug; ?>');">
									<div class="panel-title">
										<b><?php if ( $widget_name == 'Core Fields' ) {
												echo esc_html__( 'WordPress Fields', 'iwjob' );
											} else {
												echo $widget_name;
											} ?>
										</b>
										<span class='fa fa-minus-square-o' id='<?php echo 'icon' . $widget_slug ?>' style="float:right;"></span>
									</div>
								</div>
								<div id='<?php echo $widget_slug; ?>toggle' style="height:auto;">
									<div class="grouptitlecontent">
										<table class='table table-mapping custom_table' id='<?php echo $widget_slug; ?>_table'>
											<tbody>
											<tr>
												<td class='columnheader mappingtd_style'>
													<label class='groupfield'><?php echo esc_html__( 'WordPress Fields', 'iwjob' ); ?></label>
												</td>
												<td class='columnheader mappingtd_style'>
													<label class='groupfield'><?php echo esc_html__( 'CSV Header', 'iwjob' ); ?></label>
												</td>
											</tr>
											<?php if ( ! empty( $fields ) ) {
												foreach ( $fields as $key => $value ) { ?>
													<tr id="iwj-tr-<?php echo esc_attr( $widget_slug . $key ); ?>">
														<td class='left_align'>
															<?php if ( $widget_name == 'Extra Fields' ) { ?>
																<input type="text" name="iwj_fieldname_<?php echo esc_attr( $widget_slug . $key ); ?>" value="<?php echo $value; ?>">
																<?php
															} else { ?>
																<label class="wpfields"><?php echo ( $value == 'iwj_job' || $value == 'iwj_candidate' || $value == 'iwj_employer' ) ? '[Name: post_type]' : '[Name: ' . $value . ']'; ?></label>
																<input type="hidden" name="iwj_fieldname_<?php echo esc_attr( $widget_slug . $key ); ?>" value="<?php echo ( $value == 'iwj_job' || $value == 'iwj_candidate' || $value == 'iwj_employer' ) ? 'post_type' : $value; ?>">
																<?php
															} ?>
														</td>
														<td class="mappingtd_style">
															<div class="mapping-select-div">
																<select name="iwj_mapping_<?php echo esc_attr( $widget_slug . $key ); ?>" id="iwj_mapping_<?php echo esc_attr( $widget_slug . $key ); ?>" class="selectpicker">
																	<option value=""><?php echo esc_html__( 'Select field', 'iwjob' ); ?></option>
																	<?php
																	foreach ( $Headers as $csvkey => $csvheader ) {
																		$csvheader_entity = preg_replace("/\xEF\xBB\xBF/", "", $csvheader);?>
																		<option value="<?php echo $csvheader_entity; ?>" <?php echo $csvheader_entity == $value ? 'selected' : ''; ?>><?php echo $csvheader_entity; ?></option>
																		<?php
																	} ?>
																</select>
															</div>
														</td>
													</tr>
													<?php
												}
											} ?>
											</tbody>
										</table>
										<input type='hidden' id="<?php echo $widget_slug; ?>_count" name="<?php echo $widget_slug; ?>_count" value="<?php echo count( $fields ); ?>">
									</div>
								</div>
							</div>
						</div>
						<?php
					}

					$filename       = isset( $get_records[ $eventKey ]['import_file']['uploaded_name'] ) ? $get_records[ $eventKey ]['import_file']['uploaded_name'] : '';
					$file_extension = pathinfo( $filename, PATHINFO_EXTENSION );
					$file_extn      = '.' . $file_extension;
					$filename       = explode( $file_extn, $filename ); ?>
				</div>
				<div class="iwj_import_btn_ctn iwj_mt20">
					<div class="pull-left">
						<a class="smack-btn btn-default btn-radius" href="<?php echo esc_url( $backlink ); ?>"><?php echo esc_html__( 'Back', 'iwjob' ) ?></a>
					</div>
					<div class="pull-right">
						<input type="submit" class="smack-btn smack-btn-primary btn-radius" value="<?php echo esc_attr__( 'Continue', 'iwjob' ); ?>" />
					</div>
				</div>
				<div class="iwj_mb20"></div>
			</form>
		</div>
		<?php
	}

	static function form_ignite_import() {
		if ( $_POST ) {
			$_POST                     = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
			$records['mapping_config'] = $_POST;
			$post_values               = self::getPostValues( sanitize_key( $_REQUEST['eventkey'] ) );
			$result                    = array_merge( $post_values[ $_REQUEST['eventkey'] ], $records );
			self::setPostValues( sanitize_key( $_REQUEST['eventkey'] ), $result );
		}
		$get_screen_info = self::getPostValues( sanitize_key( $_REQUEST['eventkey'] ) );
		$eventkey        = sanitize_title( $_REQUEST['eventkey'] );
		$parserObj       = new SmackCSVParser();
		$file            = IWJ_IMPORT_DIR . '/' . $eventkey . '/' . $eventkey;
		$parserObj->parseCSV( $file, 0, - 1 );
		$total_row_count = $parserObj->total_row_cont - 1;
		$import_type     = $get_screen_info[ $eventkey ]['import_file']['posttype'];
		$import_mode     = $get_screen_info[ $eventkey ]['import_file']['import_mode']; ?>
		<div class="template_body whole_body">
			<form class="form-inline" method="post">
				<div class="col-md-12">
					<div class="col-md-12 mt40" style="text-align: center;">
						<input type="button" class="smack-btn smack-btn-danger btn-radius" value="<?php echo esc_attr( 'Verify import and Close', 'iwjob' ); ?>" id="new_import" onclick="iwj_reload_to_new_import()" style="display: none;">
					</div>
				</div>
				<div class="clearfix"></div>
				<div id="iwj_progress-div">
					<div id="iwj_progress-bar">
						<span class="progresslabel"></span>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="event-summary">
					<span class="es-left"> <?php echo esc_html__( 'File Name:', 'iwjob' ); ?><?php echo $get_screen_info[ $eventkey ]['import_file']['uploaded_name']; ?> </span>
					<span class="es-right"> <?php echo esc_html__( 'File Size:', 'iwjob' ); ?><?php echo self::getFileSize( $file ); ?> </span>
				</div>
				<div class="event-summary">
					<span class="es-left"> <?php echo esc_html__( 'Process: Import', 'iwjob' ); ?></span>
					<span class="es-right"> <?php echo esc_html__( 'Total no of records:', 'iwjob' ); ?><?php echo $total_row_count; ?> </span>
				</div>
				<div class="event-summary timer">
					<span class="es-left"> <?php echo esc_html__( 'Time Elapsed:', 'iwjob' ); ?> </span>
					<span class="es-left" style="padding-left: 10px;">
						<span class="hour">00</span>:<span class="minute">00</span>:<span class="second">00</span>
					</span>
					<span class="es-right" id="iwj_import_remaining" style="padding-right:2px;color:red;"> <?php echo esc_html__( 'Remaining Record:', 'iwjob' ); ?> </span>
					<span class="es-right" id="iwj_import_current" style="padding-right:7px;color:green;"> <?php echo esc_html__( 'Current Processing Records:', 'iwjob' ); ?> </span>
				</div>
				<div class="control" style="display: none;">
					<input type="button" id="iwj_import_timer_start" onClick="timer.start(1000)" value="<?php echo esc_attr__( 'Start', 'iwjob' ); ?>" />
					<input type="button" id="iwj_import_timer_stop" onClick="timer.stop()" value="<?php echo esc_attr__( 'Stop', 'iwjob' ); ?>" />
					<input type="button" id="iwj_import_timer_reset" onClick="timer.reset(60)" value="<?php echo esc_attr__( 'Reset', 'iwjob' ); ?>" />
					<input type="button" id="iwj_import_timer_count_up" onClick="timer.mode(1)" value="<?php echo esc_attr__( 'Count up', 'iwjob' ); ?>" />
					<input type="button" id="iwj_import_timer_count_down" onClick="timer.mode(0)" value="<?php echo esc_attr__( 'Countdown', 'iwjob' ); ?>" />
				</div>
				<div id="logsection" class="seoadv_options">
					<div id="innerlog" class="logcontainer"></div>
				</div>
				<input type="hidden" id="eventkey" value="<?php echo sanitize_key( $_REQUEST['eventkey'] ); ?>">
				<input type="hidden" id="import_type" name="import_type" value="<?php echo $import_type; ?>">
				<input type="hidden" id="import_mode" name="import_mode" value="<?php echo $import_mode; ?>">
				<input type="hidden" id="importlimit" name="importlimit" value="1">
				<input type="hidden" id="currentlimit" name="currentlimit" value="1">
				<input type="hidden" id="limit" name="limit" value="1">
				<input type="hidden" id="inserted" value="0">
				<input type="hidden" id="totalcount" name="totalcount" value="<?php echo $total_row_count; ?>">
				<input type="hidden" id="terminate_action" name="terminate_action" value="continue" />
			</form>
		</div>
		<script>
			jQuery(document).ready(function (e) {
				jQuery("#iwj_import_timer_count_up").click();
				jQuery("#iwj_import_timer_start").click();
			});
			iwj_igniteImport();
		</script>
		<?php
	}

	static function coreFields( $type ) {
		$coreFields = array(
			'ID',
			'post_author',
			'post_date',
			'post_date_gmt',
			'post_content',
			'post_title',
			'post_excerpt',
			'post_status',
			'post_name',
			'post_modified',
			'post_modified_gmt',
			'post_content_filtered',
			'post_parent',
			'guid',
			'menu_order',
			$type,
			'post_mime_type',
			'comment_count'
		);
		if ( $type == 'iwj_candidate' || $type == 'iwj_employer' ) {
			$coreFields_ex = array(
				'user_login',
				'user_pass',
				'user_nicename',
				'user_email',
				'user_url',
				'user_registered',
				'user_activation_key',
				'user_status',
				'display_name'
			);
			$coreFields    = array_merge( $coreFields, $coreFields_ex );
		}

		return $coreFields;
	}

	static function extraFields( $type ) {
		$extra_fields = array();
		switch ( $type ) {
			case 'iwj_job':
				$extra_fields = array(
					'_iwj_expiry',
					'_iwj_deadline',
					'_iwj_featured',
					'_iwj_featured_date',
					'_iwj_email_application',
					'_iwj_job_gender',
					'_iwj_job_languages',
					'_iwj_salary_from',
					'_iwj_salary_to',
					'_iwj_salary_postfix',
					'_iwj_currency',
					'_iwj_address',
					'_iwj_map',
					'_iwj_reason',
					'_iwj_views',
					'_iwj_custom_apply_url',
					'_iwj_user_package_id',
					'import_source',
					'import_url',
					'import_company',
					'_iwj_free_job',
					'_iwj_is_new_featured',
					'_iwj_is_new_publish',
				);
				break;
			case 'iwj_candidate':
				$extra_fields = array(
					'_iwj_headline',
					'_iwj_birthday',
					'_iwj_views',
					'_iwj_address',
					'_iwj_gender',
					'_iwj_languages',
					'_iwj_reason',
					'_iwj_public_account',
					'_iwj_phone',
					'_iwj_map',
					'_iwj_facebook',
					'_iwj_twitter',
					'_iwj_google_plus',
					'_iwj_youtube',
					'_iwj_vimeo',
					'_iwj_linkedin',
					'_iwj_experience',
					'_iwj_education',
					'_iwj_skill_showcase',
					'_iwj_gallery',
					'_iwj_video',
					'_iwj_award',
					'_iwj_cv',
					'_iwj_cover_letter',
					'_thumbnail_id',
				);
				break;
			case 'iwj_employer':
				$extra_fields = array(
					'_iwj_headline',
					'_iwj_views',
					'_iwj_map',
					'_iwj_address',
					'_iwj_reason',
					'_iwj_phone',
					'_iwj_founded_date',
					'_iwj_facebook',
					'_iwj_twitter',
					'_iwj_google_plus',
					'_iwj_youtube',
					'_iwj_vimeo',
					'_iwj_linkedin',
					'_iwj_gallery',
					'_iwj_video',
					'_thumbnail_id',
				);
				break;
		}

		return $extra_fields;
	}

	static function termFields( $type ) {
		$term_fields = array();
		switch ( $type ) {
			case 'iwj_job':
			case 'iwj_candidate':
				$term_fields = array(
					'iwj_type',
					'iwj_cat',
					'iwj_skill',
					'iwj_level',
					'iwj_location',
				);
				break;
			case 'iwj_employer':
				$term_fields = array(
					'iwj_size',
					'iwj_cat',
					'iwj_location',
				);
				break;
		}

		return $term_fields;
	}

	static function getFileSize( $file ) {
		$fileSize = filesize( $file );

		if ( $fileSize > 1024 && $fileSize < ( 1024 * 1024 ) ) {
			$fileSize = round( ( $fileSize / 1024 ), 2 ) . ' kb';
		} else if ( $fileSize > ( 1024 * 1024 ) ) {
			$fileSize = round( ( $fileSize / ( 1024 * 1024 ) ), 2 ) . ' mb';
		} else {
			$fileSize = $fileSize . ' bytes';
		}

		return $fileSize;
	}

	static function serverReq_data() {
		$record_arr          = array();
		$split_range         = array();
		$initial_recordcount = 1000;
		$minimum_record      = 0;
		$filesize            = 8000;
		//$tot_records = count($record_arr);
		$tot_records   = 10;
		$executiontime = ini_get( 'max_execution_time' );
		$memorysize    = ini_get( 'memory_limit' );
		if ( $tot_records < $initial_recordcount ) {
			$current_processtime = ( $tot_records / 20 ) * 60;
		} else {
			$current_processtime = ( ( $tot_records / 1000 ) * 5 ) * 60;
		}
		if ( $executiontime < $current_processtime ) {
			$time_in_minute = round( $executiontime / 60, 3 );
			if ( $executiontime > 300 ) {
				$server_req = 1000 * ( $time_in_minute / 5 ); // 1000 record takes 5 minutes i.e 300s
			} else {
				$server_req = 150 * $time_in_minute; // 150 record takes 1 minute.
			}
			$server_req = $server_req / 2;
		} else {
			$server_req = $tot_records / 2;
		}
		if ( $server_req != 0 ) {
			// convert to neareast 10.
			$quotient_val  = (int) $server_req / 10;
			$remainder_val = (int) $server_req % 10;
			if ( $remainder_val != 0 ) {
				$server_req = ( intval( $quotient_val ) + 1 ) * 10;
			}
			// Split the range
			$count = 0;
			for ( $i = $server_req; $count < 10; $i ++ ) {
				if ( $i > 20 ) {
					$split_range[] = $i - 20;
					$i             -= 20;
				} else {
					$split_range[] = $i;
					break;
				}
				$count ++;
			}
		}

		return $split_range;
	}

	static function setPostValues( $eventKey = null, $values = null ) {
		#$uploadpath = SM_UCI_SCREENS_DATA;
		$uploadPath = IWJ_IMPORT_DIR . '/' . $eventKey;
		if ( ! is_dir( $uploadPath ) ) {
			wp_mkdir_p( $uploadPath );
		}
		$filename = $uploadPath . '/screenInfo.txt';

		$myfile = fopen( $filename, "w" ) or die( "Unable to open file!" );
		$post_values[ $eventKey ] = $values;
		$post_values              = serialize( $post_values );
		fwrite( $myfile, $post_values );
		fclose( $myfile );
		//file_put_contents($filename, $post_values,FILE_APPEND);
		$_SESSION[ $eventKey ] = $values;
	}

	static function getPostValues( $eventKey ) {
		$uploadPath       = IWJ_IMPORT_DIR . '/' . $eventKey;
		$screen_info_file = $uploadPath . '/screenInfo.txt';
		$screen_data      = array();
		if ( file_exists( $screen_info_file ) ) {
			$get_screen_data = fopen( $screen_info_file, 'r' );
			$get_screen_data = fread( $get_screen_data, filesize( $screen_info_file ) );
			//@fclose( $get_screen_data );
			$screen_data = unserialize( $get_screen_data );
		}

		return $screen_data;
	}

	static function get_widget_fields( $widget_name, $import_type ) {
		$fields = array();
		if ( $widget_name == 'Core Fields' ) {
			$fields = self::coreFields( $import_type );
		}

		if ( $widget_name == 'Extra Fields' ) {
			$fields = self::extraFields( $import_type );
		}

		if ( $widget_name == 'Term Fields' ) {
			$fields = self::termFields( $import_type );
		}

		return $fields;
	}

}

IWJ_Admin_Classes_Tools::init();