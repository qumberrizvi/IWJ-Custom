(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as anonymous module.
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node / CommonJS
        factory(require('jquery'));
    } else {
        // Browser globals.
        factory(jQuery);
    }
})(function ($) {

    'use strict';

    var console = window.console || { log: function () {} };

    function CropAvatar($element) {
        this.$container = $element;

        this.$avatarView = this.$container.find('.avatar-view');
        this.$avatar = this.$avatarView.find('img');
        this.$avatarModal = this.$container.find('#avatar-modal');
        this.$loading = this.$container.find('.loading');

        this.$avatarForm = this.$avatarModal.find('.avatar-form');
        this.$avatarUpload = this.$avatarForm.find('.avatar-upload');
        this.$avatarSrc = this.$avatarForm.find('.avatar-src');
        this.$avatarData = this.$avatarForm.find('.avatar-data');
        this.$avatarCanvasData = this.$avatarForm.find('.avatar-canvas-data');
        this.$avatarZoom = this.$avatarForm.find('.avatar-zoom');
        this.$avatarInput = this.$avatarForm.find('.avatar-input');
        this.$avatarSave = this.$avatarForm.find('.avatar-save');
        this.$avatarBtns = this.$avatarForm.find('.avatar-btns');

        this.$avatarWrapper = this.$avatarModal.find('.avatar-wrapper');
        this.$avatarPreview = this.$avatarModal.find('.avatar-preview');

        this.init();
    }

    CropAvatar.prototype = {
        constructor: CropAvatar,

        support: {
            fileList: !!$('<input type="file">').prop('files'),
            blobURLs: !!window.URL && URL.createObjectURL,
            formData: !!window.FormData
        },

        init: function () {
            this.support.datauri = this.support.fileList && this.support.blobURLs;

            if (!this.support.formData) {
                this.initIframe();
            }

            //this.initTooltip();
            this.initModal();
            this.addListener();

        },

        addListener: function () {
            this.$avatarView.on('click', $.proxy(this.click, this));
            this.$avatarInput.on('change', $.proxy(this.change, this));
            this.$avatarForm.on('submit', $.proxy(this.submit, this));
            this.$avatarBtns.on('click', $.proxy(this.rotate, this));
            this.$avatarSave.on('click', $.proxy(this.ajaxUpload, this));
            var _this = this;
            this.$avatarModal.on('shown.bs.modal', function () {
                if(!_this.modal_init){
                    var avatar_src = _this.$avatarSrc.val();
                    if(avatar_src){
                        _this.url = avatar_src;
                        _this.startCropper();
                    }else{
                        _this.modal_init = true;
                    }
                }else if(_this._url){
                    _this.url = _this._url;
                    _this.startCropper();
                    _this._url = '';
                }

            });

            this.$container.find('.change-image-btn button').click(function () {
                _this.$avatarInput.trigger('click');
                //_this.$avatarModal.modal('show');
            })
        },

        initTooltip: function () {
            this.$avatarView.tooltip({
                placement: 'top'
            });
        },

        initModal: function () {
            this.$avatarModal.modal({
                show: false
            });
        },

        initPreview: function () {
            var url = this.$avatar.attr('src');

            this.$avatarPreview.html('<img src="' + url + '">');
        },

        initIframe: function () {
            var target = 'upload-iframe-' + (new Date()).getTime();
            var $iframe = $('<iframe>').attr({
                name: target,
                src: ''
            });
            var _this = this;

            // Ready ifrmae
            $iframe.one('load', function () {

                // respond response
                $iframe.on('load', function () {
                    var data;

                    try {
                        data = $(this).contents().find('body').text();
                    } catch (e) {
                        console.log(e.message);
                    }

                    if (data) {
                        try {
                            data = $.parseJSON(data);
                        } catch (e) {
                            console.log(e.message);
                        }

                        _this.submitDone(data);
                    } else {
                        _this.submitFail('Image upload failed!');
                    }

                    _this.submitEnd();

                });
            });

            this.$iframe = $iframe;
            this.$avatarForm.attr('target', target).after($iframe.hide());
        },

        click: function () {
            this.$avatarModal.modal('show');
            if(!this.modal_init){
                this.initPreview();
            }
        },

        change: function () {
            var files;
            var file;
            if (this.support.datauri) {
                files = this.$avatarInput.prop('files');

                if (files.length > 0) {
                    file = files[0];

                    if (this.isImageFile(file)) {
                        this.modal_init = true;
                        if (this.url) {
                            URL.revokeObjectURL(this.url); // Revoke the old one
                        }

                        if(this.$avatarModal.hasClass('in')){
                            this.url = URL.createObjectURL(file);
                            this.startCropper();
                        }else{
                            this._url = URL.createObjectURL(file);
                            this.modal_init = true;
                            this.$avatarModal.modal('show');
                        }
                    }

                }
            } else {
                file = this.$avatarInput.val();

                if (this.isImageFile(file)) {
                    this.syncUpload();
                }
            }
        },

        submit: function () {
            if (!this.$avatarSrc.val() && !this.$avatarInput.val()) {
                return false;
            }

            if (this.support.formData) {
                this.ajaxUpload();
                return false;
            }
        },

        rotate: function (e) {
            var data;

            if (this.active) {
                data = $(e.target).data();

                if (data.method) {
                    this.$img.cropper(data.method, data.option);
                }
            }
        },

        isImageFile: function (file) {
            if (file.type) {
                return /^image\/\w+$/.test(file.type);
            } else {
                return /\.(jpg|jpeg|png|gif)$/.test(file);
            }
        },

        startCropper: function () {
            var _this = this;
            var wrap_width, wrap_height;
            if (this.active) {
                this.$img.cropper('replace', this.url);
            } else {
                this.$img = $('<img src="' + this.url + '">');
                this.$avatarWrapper.empty().html(this.$img);
                this.$img.cropper({
                    aspectRatio: 0,
                    viewMode: 0,
                    dragMode: 'move',
                    restore: false,
                    guides: true,
                    highlight: true,
                    cropBoxMovable: false,
                    cropBoxResizable: false,
                    minCanvasWidth : parseInt(iwjmbAvatar.image_width) - 20,
                    minCanvasHeight : parseInt(iwjmbAvatar.image_height) - 20,
                    wheelZoomRatio: 0.1,
                    ready: function () {
                        wrap_width = $('.cropper-container').width();
                        wrap_height = $('.cropper-container').height();
                        var crop_width = parseInt(iwjmbAvatar.image_width);
                        var crop_height = parseInt(iwjmbAvatar.image_height);

                        _this.$img.cropper('setCropBoxData', {"left": (wrap_width - crop_width) / 2,"top": (wrap_height - crop_height)/ 2,"width":crop_width,"height":crop_height});
                        if(!_this.modal_init){

                            var data = _this.$avatarCanvasData.val();
                            if(data){
                                _this.$img.cropper('setCanvasData', JSON.parse(data));
                            }
                            _this.modal_init = true;
                        }
                    },
                    preview: this.$avatarPreview.selector,
                    crop: function (e) {
                        var json = [
                            '{"x":' + e.x,
                            '"y":' + e.y,
                            '"height":' + e.height,
                            '"width":' + e.width,
                            '"rotate":' + e.rotate + '}'
                        ].join();
                        _this.$avatarData.val(json);
                        _this.$avatarCanvasData.val(JSON.stringify(_this.$img.cropper('getCanvasData')));
                    },
                    zoom : function (e) {
                        _this.$avatarZoom.val(e.ratio);
                        _this.$avatarCanvasData.val(JSON.stringify(_this.$img.cropper('getCanvasData')));
                    }
                });

                this.active = true;
            }

            //this.$avatarModal.one('hidden.bs.modal', function () {
                //_this.$avatarPreview.empty();
                //_this.stopCropper();
            //});
        },

        stopCropper: function () {
            if (this.active) {
                this.$img.cropper('destroy');
                this.$img.remove();
                this.active = false;
            }
        },

        ajaxUpload: function () {

            var data = new FormData();
            data.append(this.$avatarInput.attr('name'), this.$avatarInput[0].files[0]);
            data.append(this.$avatarSrc.attr('name'), this.$avatarSrc.val());
            data.append(this.$avatarData.attr('name'), this.$avatarData.val());
            data.append(this.$avatarCanvasData.attr('name'), this.$avatarCanvasData.val());
            data.append(this.$avatarZoom.attr('name'), this.$avatarZoom.val());
            data.append('action', 'iwj_upload_avatar');
            data.append('_ajax_nonce', iwj.security);

            var _this = this;

            $.ajax(iwj.ajax_url, {
                type: 'post',
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,

                beforeSend: function () {
                    _this.submitStart();
                },

                success: function (data) {
                    _this.submitDone(data);
                },

                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    _this.submitFail(textStatus || errorThrown);
                },

                complete: function () {
                    _this.submitEnd();
                }
            });
        },

       /* syncUpload: function () {
            this.$avatarSave.click();
        },*/

        submitStart: function () {
            this.$loading.fadeIn();
            this.$avatarSave.prop('disabled', true);
        },

        submitDone: function (data) {
            if ($.isPlainObject(data) && data.state === 200) {
                if (data.avatar_url) {
                    /*this.url = data.result;

                    if (this.support.datauri || this.uploaded) {
                        this.uploaded = false;
                        this.cropDone();
                    } else {
                        this.uploaded = true;
                        this.$avatarSrc.val(this.url);
                        this.startCropper();
                    }

                    this.$avatarInput.val('');*/

                    this.$avatarSrc.val(data.avatar_src);
                    this.$avatar.attr('src', data.avatar_url);

                    this.$avatarModal.modal('hide');

                } else if (data.message) {
                    console.log(data.message);
                }
            } else {
                this.alert('Failed to response');
            }

            this.$avatarSave.prop('disabled', false);
        },

        submitFail: function (msg) {
            this.alert(msg);
        },

        submitEnd: function () {
            this.$loading.fadeOut();
        },

        cropDone: function () {
            this.$avatar.attr('src', this.url);
            this.$avatarModal.modal('hide');
        },

        alert: function (msg) {
            var $alert = [
                '<div class="alert alert-danger avatar-alert alert-dismissable">',
                '<button type="button" class="close" data-dismiss="alert">&times;</button>',
                msg,
                '</div>'
            ].join('');

            this.$avatarUpload.after($alert);
        }
    };

    $(function () {
        $('.iwj-avatar-container').each(function () {
            var crop_avatar = new CropAvatar($(this));
            $(this).data('crop_avatar', crop_avatar);
        })

    });

});
