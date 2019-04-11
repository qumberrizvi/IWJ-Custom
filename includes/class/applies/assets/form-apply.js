jQuery(document).ready(function ($) {
    $('.iwj-application-form').submit(function (e) {
        e.preventDefault();
        var form = $(this);
        if(typeof tinyMCE != 'undefined'){
            tinyMCE.triggerSave();
        }
        var formData = new FormData(form[0]);
        formData.append('_ajax_nonce', iwj.security);
        var button = form.find('.iwj-application-btn');
        $.ajax({
            url: iwj.ajax_url,
            type: 'POST',
            data: formData,
            dataType : 'json',
            cache: false,
            contentType: false,
            processData: false,
            beforeSend : function () {
                iwj_button_loader(button, 'add');
                form.find('.iwj-respon-msg').slideUp(300).html('');
            },
            success: function(result){
                if(result){
                    iwj_button_loader(button, 'remove');
                    if(result.success == true){
                        form.find('.modal-body').html(result.message);
                    }else{
                        if(typeof grecaptcha !== 'undefined' && iwj.use_recaptcha.apply_job === 1){
                            grecaptcha.reset();
                        }
                        if(result.message){
                            form.find('.iwj-respon-msg').html(result.message).slideDown(300);
                        }
                    }
                }
            }
        });
    });
});