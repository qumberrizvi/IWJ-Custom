/**
 * Description of cv-field
 *
 */
jQuery(document).ready(function($){
    'use strict';

    $('.iwj-select-cv').each(function () {
        var self = $(this);
        var cv_uploader = new plupload.Uploader({
            'runtimes' : 'html5,silverlight,flash,html4',
            'browse_button' : self.attr('id'),
            'container' : self.closest('.iwj-select-cv-container').attr('id'),
            'file_data_name' : 'async-upload',
            'multiple_queues' : true,
            'max_file_size' : iwjmbCV.max_file_size,
            'url' : iwjmbCV.url,
            'flash_swf_url' : iwjmbCV.flash_swf_url,
            'silverlight_xap_url' : iwjmbCV.silverlight_xap_url,
            'filters' :  [
                iwjmbCV.filter,
            ],
            'multipart' : true,
            'urlstream_upload' : true,
            'multi_selection' : false,
            'multipart_params' : {
                '_ajax_nonce': iwjmbCV.security,
                'action': 'iwj_upload_cv',
            },

            init : {
                FilesAdded: function(up, files) {
                    var container = $(up.getOption('container'));
                    container.find('input[type="text"]').val('Uploading...');
                    var multipart_params = up.getOption('multipart_params');
                    multipart_params.remove_file_id = container.find('.iwj-select-cv').data('file-uploaded');
                    up.setOption('multipart_params', multipart_params);
                    up.refresh();
                    up.start();
                },
                FileUploaded: function(up, file, response) {
                    var data = response.response;
                    if(data){
                        data = JSON.parse(data);
                        var container = $(up.getOption('container'));
                        container.find('.iwj-select-cv').data('file-uploaded', data.ID);
                        container.find('input[type="text"]').val(data.file_name);
                        container.find('input[type="hidden"]').val(data.ID);
                    }
                },
                Error: function(up, args) {
                	var err = 'Max file size is '+iwjmbCV.max_file_size,
						err_container = $(up.getOption('container')).find('.upload-error'),
						err2 = args.message+err;
						err_container.html(err2);
					err_container.slideDown(400);
                }

            }
        });
        cv_uploader.init();
    });

    $('.iwj-remove-cv').click(function (e) {
        e.preventDefault();
        var parent = $(this).closest('.iwj-select-cv-wrap');
        parent.find('input').val('');
		parent.find('.upload-error').hide();
    });

    $('.iwj-cv-area input[type="file"]').change(function () {
        var parent = $(this).closest('.add-new-cv');
        parent.find('.select_cv_named').html($(this).val().replace(/C:\\fakepath\\/i, ''));
    });

    $('.iwj-cv-area input[type="radio"]').change(function () {
        var value = $(this).val();
        if(value == 'add_new_cv'){
            var parent = $(this).closest('.add-new-cv');
            parent.find('input[type="file"]').trigger('click');
        }
    });

    $('.iwj-cv-area .select_cv').click(function () {
        if($('.iwj-cv-area input[type="radio"]:checked').val() == 'add_new_cv'){
            $('.iwj-cv-area input[type="file"]').trigger('click');
        }
    });
});
