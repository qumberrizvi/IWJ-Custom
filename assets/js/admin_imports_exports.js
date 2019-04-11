var timer;

(function ($) {
	'use strict';

	$(document).ready(function ($) {

		$('.iwj-form-exports-job').submit(function (e) {
			e.preventDefault();
			var self = $(this),
				respon = self.closest('.iwj-jobs-imports').find('.iwj-loading-exports'),
				job_type = self.find('#_iwj_specific_job_type').val();
			var data = 'action=iwj_jobs_export&_ajax_nonce=' + iwjadmin.security + '&job_type=' + job_type;
			$.ajax({
				url       : iwjadmin.ajax_url,
				type      : 'POST',
				data      : data,
				dataType  : 'json',
				beforeSend: function () {
					respon.removeClass('hidden');
					self.closest('.iwj-jobs-imports').find('#iwj-download-link').addClass('hidden');
					self.closest('.iwj-jobs-imports').find('#iwj-download-link a').attr('href', '');
				},
				success   : function (result) {
					respon.addClass('hidden');
					if (result.success) {
						self.closest('.iwj-jobs-imports').find('#iwj-download-link').removeClass('hidden');
						self.closest('.iwj-jobs-imports').find('#iwj-download-link p.iwj-export-msg').html(result.message);
						self.closest('.iwj-jobs-imports').find('#iwj-download-link a').attr('href', result.path_down);
					}
				},
				error     : function (result) {

				}
			});
		});

		$("#iwj_fileupload").on('click', function (e) {
			e.preventDefault();
			$("#iwj_upload_file:hidden").trigger('click');
		});

		$("#image-handling-btn").click(function () {
			if ($(this).is(":checked")) {
				$("#image-handling-btn-opt").slideDown();
			} else {
				$("#image-handling-btn-opt").slideUp();
			}
		});

		timer = new _timer
		(
			function (time) {
				if (time == 0) {
					timer.stop();
					swal('Warning!', 'Time Out.', 'warning')
				}
			}
		);
		timer.reset(0);
		timer.mode(0);
	});
})(jQuery);

function _timer(callback) {
	var time = 0;     //  The default time of the timer
	var mode = 1;     //    Mode: count up or count down
	var status = 0;    //    Status: timer is running or stoped
	var timer_id;    //    This is used by setInterval function

	// this will start the timer ex. start the timer with 1 second interval timer.start(1000)
	this.start = function (interval) {
		interval = (typeof(interval) !== 'undefined') ? interval : 1000;

		if (status == 0) {
			status = 1;
			timer_id = setInterval(function () {
				switch (mode) {
					default:
						if (time) {
							time--;
							generateTime();
							if (typeof(callback) === 'function') callback(time);
						}
						break;

					case 1:
						if (time < 86400) {
							time++;
							generateTime();
							if (typeof(callback) === 'function') callback(time);
						}
						break;
				}
			}, interval);
		}
	};

	//  Same as the name, this will stop or pause the timer ex. timer.stop()
	this.stop = function () {
		if (status == 1) {
			status = 0;
			clearInterval(timer_id);
		}
	};

	// Reset the timer to zero or reset it to your own custom time ex. reset to zero second timer.reset(0)
	this.reset = function (sec) {
		sec = (typeof(sec) !== 'undefined') ? sec : 0;
		time = sec;
		generateTime(time);
	};

	// Change the mode of the timer, count-up (1) or countdown (0)
	this.mode = function (tmode) {
		mode = tmode;
	};

	// This methode return the current value of the timer
	this.getTime = function () {
		return time;
	};

	// This methode return the current mode of the timer count-up (1) or countdown (0)
	this.getMode = function () {
		return mode;
	};

	// This method return the status of the timer running (1) or stoped (1)
	this.getStatus = function () {
		return status;
	};

	// This methode will render the time variable to hour:minute:second format
	function generateTime() {
		var second = time % 60;
		var minute = Math.floor(time / 60) % 60;
		var hour = Math.floor(time / 3600) % 60;

		second = (second < 10) ? '0' + second : second;
		minute = (minute < 10) ? '0' + minute : minute;
		hour = (hour < 10) ? '0' + hour : hour;

		jQuery('div.event-summary span.second').html(second);
		jQuery('div.event-summary span.minute').html(minute);
		jQuery('div.event-summary span.hour').html(hour);
	}
}

function iwj_igniteImport() {

	// When closing browser window alert for stay on page or leave page
	window.onbeforeunload = function () {
		return "Do you want to leave?";
	};
	jQuery(window).unload(function () {
		var currentURL = location.protocol + '//' + location.host + location.pathname + '?post_type=iwj_job&page=iwj-jobs-tools';
		window.location = currentURL;
	});
	var eventkey = document.getElementById('eventkey').value;
	var import_type = document.getElementById('import_type').value;
	var import_mode = document.getElementById('import_mode').value;
	var totalcount = document.getElementById('totalcount').value;
	var currentlimit = document.getElementById('currentlimit').value;
	jQuery('#iwj_import_current').html('Current Processing Record: ' + currentlimit);
	var importlimit = document.getElementById('importlimit').value;
	var remaining = parseInt(totalcount) - parseInt(currentlimit);
	jQuery('#iwj_import_remaining').html('Remaining Record: ' + remaining);
	var inserted = document.getElementById('inserted').value;
	var limit = document.getElementById('limit').value;
	var total = parseInt(totalcount) + 1;
	var startLimit = currentlimit;
	var endLimit = parseInt(importlimit) + parseInt(currentlimit),
		percent = 0;
	var postData = new Array();
	postData = {
		'event_key'  : eventkey,
		'import_type': import_type,
		'import_mode': import_mode,
		'startLimit' : startLimit,
		'endLimit'   : endLimit,
		'Limit'      : limit,
		'totalcount' : totalcount,
		'inserted'   : inserted
	};
	jQuery.ajax({
		type    : 'POST',
		url     : ajaxurl,
		dataType: 'json',
		data    : {
			'action'  : 'iwj_parse_data_to_import',
			'postData': postData,
		},
		success : function (response) {
			currentlimit = parseInt(currentlimit) + parseInt(importlimit);
			document.getElementById('currentlimit').value = currentlimit;
			document.getElementById('inserted').value = response.inserted;

			percent = Math.ceil(parseInt(response.inserted) / totalcount * 100);
			jQuery("#iwj_progress-div #iwj_progress-bar").css("width", +percent + "%");
			jQuery("#iwj_progress-bar span.progresslabel").html(percent + "%");

			if (currentlimit == total) {
				var msg = 'Import Successfully Completed';
				document.getElementById('new_import').style.display = '';
				jQuery('#innerlog').prepend(jQuery("<p>" + msg + "</p>"));
				jQuery("#iwj_import_timer_stop").click();
				return false;
			} else {
				setTimeout(function () {
					iwj_igniteImport()
				}, 0);
			}
		},
		error   : function (errorThrown) {
			console.log(errorThrown);
		}
	});
}

function iwj_upload_method() {
	var formData = new FormData();
	var filesArray = jQuery('#iwj_upload_file').prop('files')[0];
	formData.append('files', filesArray);
	formData.append('action', 'iwj_upload_csv_actions');
	document.getElementById('iwj_division1').style.display = "none";
	document.getElementById('iwj_importjob_sec').style.display = "";
	jQuery("#iwj_progress-bar").width('0%');
	jQuery.ajax({
		type       : 'post',
		url        : ajaxurl,
		data       : formData,
		contentType: false,
		cache      : false,
		processData: false,
		target     : '#iwj_targetLayer',
		xhr        : function () {
			//upload Progress
			var xhr = jQuery.ajaxSettings.xhr();
			if (xhr.upload) {
				xhr.upload.addEventListener('progress', function (event) {
					var percent = 0;
					var position = event.loaded || event.position;
					var total = event.total;
					if (event.lengthComputable) {
						percent = Math.ceil(position / total * 100);
					}
					jQuery("#iwj_progress-div" + " #iwj_progress-bar").css("width", +percent + "%");
					jQuery("#iwj_progress-bar span.progresslabel").html(percent + "%");
				}, true);
			}
			return xhr;
		},
		success    : function (uploaded_file_info) {
			uploaded_file_info = JSON.parse(uploaded_file_info);
			jQuery.each(uploaded_file_info, function (objkey, objval) {
				jQuery.each(objval, function (o_key, file) {
					document.getElementById('file_name').value = file.name;
					document.getElementById('uploaded_name').value = file.uploadedname;
					var file_extn = file.name.split(".");
					var check_file = file_extn[file_extn.length - 1];
					document.getElementById('file_extension').value = check_file;
					var get_current_action = jQuery('#form_import_file').attr('action');
					if (check_file != "csv") {
						warning = 'Un Supported File Format';
						swal({
								title             : warning,
								text              : "You will not be able to upload this file!",
								type              : "warning",
								showCancelButton  : true,
								confirmButtonColor: "#DD6B55",
								confirmButtonText : "Upload file again?",
								closeOnConfirm    : false
							},
							function () {
								jQuery('#iwj_importjob_sec').css('display', 'none');
								jQuery('#iwj_division1').css('display', '');
								swal("Deleted!", "Your uploaded file has been deleted.", "success");
							});
						document.getElementById('iwj_upload_file').value = "";
						return false;
					}

					var version = file_extn[0].split("-"),
						current_version = version[version.length - 1];
					document.getElementById('file_version').value = current_version;
					if (file.size > 1024 && file.size < (1024 * 1024)) {
						var fileSize = (file.size / 1024).toFixed(2) + ' kb';
					}
					else if (file.size > (1024 * 1024)) {
						var fileSize = (file.size / (1024 * 1024)).toFixed(2) + ' mb';
					}
					else {
						var fileSize = (file.size) + ' byte';
					}
					var max_filesize = document.getElementById('upload_max').value,
						max_size = 'Please increase the upload_max_filesize in php.ini \n (Or) \n Upload the csv file below ' + max_filesize + '.';
					if (fileSize == '0 byte') {
						warning = 'Un Supported File Format';
						swal({
								title             : 'Sorry your filesize is exceeded.',
								text              : max_size,
								type              : "warning",
								showCancelButton  : true,
								confirmButtonColor: "#DD6B55",
								confirmButtonText : "Upload file again?",
								closeOnConfirm    : false
							},
							function () {
								jQuery('#iwj_importjob_sec').css('display', 'none');
								jQuery('#iwj_division1').css('display', '');
								swal("Deleted!", "Your uploaded file has been deleted.", "success");
							});
						document.getElementById('iwj_upload_file').value = "";
						return false;
					}
					jQuery("#iwj_filename_display").empty();
					jQuery('<label/>').text((file.uploadedname) + ' - ' + fileSize).appendTo('#iwj_filename_display');

					if (check_file != 'zip') {
						jQuery.ajax({
							type   : 'POST',
							url    : ajaxurl,
							data   : {
								'action'      : 'iwj_set_post_types',
								'filekey'     : file.eventkey,
								'uploadedname': file.uploadedname
							},
							success: function (priority_result) {
								if (priority_result != '') {
									priority_result = JSON.parse(priority_result);

									var splitaction = get_current_action.split("&"),
										action = splitaction[0] + '&' + splitaction[1] + '&' + splitaction[2] + '&step=mapping_config&eventkey=' + file.eventkey;
									jQuery('.selectpicker').val(priority_result['type']);
									var checkvalue = jQuery('.selectpicker').val();
									if (checkvalue == null) {
										jQuery('.selectpicker').val('iwj_job');
									}
								} else {
									var splitaction = get_current_action.split("&"),
										action = splitaction[0] + '&' + splitaction[1] + '&' + splitaction[2] + '&step=mapping_config&eventkey=' + file.eventkey;
								}

								jQuery('#form_import_file').attr('action', action);
								jQuery('.continue-btn').attr('disabled', false);
							}
						});
					}
				})
			})
		},
		error      : function (errorThrown) {
			console.log(errorThrown);
		}
	});
}

function toggle_func(id) {
	jQuery('#' + id + 'toggle').slideToggle('slow');
	jQuery('#icon' + id).toggleClass("fa fa-chevron-down").toggleClass("fa fa-minus-square-o");
	jQuery('#' + id).toggleClass("text-primary");
}

function iwj_reload_to_new_import() {
	var currentURL = location.protocol + '//' + location.host + location.pathname + '?post_type=iwj_job&page=iwj-jobs-tools';
	window.location = currentURL;
}