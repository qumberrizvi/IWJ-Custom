/**
 * Description of image single
 *
 */
jQuery(document).ready(function($){
    'use strict';

    $('.iwj-select-image').each(function () {
        var self = $(this);
        var image_uploader = new plupload.Uploader({
            'runtimes' : 'html5,silverlight,flash,html4',
            'browse_button' : self.attr('id'),
            'container' : self.closest('.iwj-select-image-container').attr('id'),
            'file_data_name' : 'async-upload',
            'multiple_queues' : true,
            'max_file_size' : iwjmbSingleImage.max_file_size,
            'url' : iwjmbSingleImage.url,
            'flash_swf_url' : iwjmbSingleImage.flash_swf_url,
            'silverlight_xap_url' : iwjmbSingleImage.silverlight_xap_url,
            'filters' :  [
                iwjmbSingleImage.filter
            ],
            'multipart' : true,
            'urlstream_upload' : true,
            'multi_selection' : false,
            'multipart_params' : {
                '_ajax_nonce': iwjmbSingleImage.security,
                'action': 'iwj_upload_single_image',
            },

            init : {
                FilesAdded: function(up, files) {
                    var container = $(up.getOption('container'));
                    container.find('img').attr('src', iwjmbSingleImage.loading_image);
                    var multipart_params = up.getOption('multipart_params');
                    multipart_params.remove_file_id = container.find('.iwj-select-image').data('file-uploaded');
                    up.setOption('multipart_params', multipart_params);
                    up.refresh();
                    up.start();
                },
                FileUploaded: function(up, file, response) {
                    var data = response.response;
                    if(data){
                        data = JSON.parse(data);
                        var container = $(up.getOption('container'))
                        container.find('.iwj-select-image').data('file-uploaded', data.ID);
                        container.find('img').attr('src', data.thumbnail_url);
                        container.find('input[type="hidden"]').val(data.ID);
                    }
                },
                Error: function(up, args) {
                    $(up.getOption('container')).find('.upload-error').html(err.message);
                }

            }
        });
        image_uploader.init();
    });
});
