jQuery(document).ready(function($) {
    function iwj_send_queue_email() {
        var data_submit = {'action': 'iwj_send_queue_email'};
        data_submit._ajax_nonce = iwj.security;
        $.ajax({
            type: "POST",
            url: iwj.ajax_url,
            dataType:'json',
            data: data_submit,
            success: function(data) {
            }
        });
    }

    if(iwj && parseInt(iwj.total_email_queue) > 0){
        setTimeout(iwj_send_queue_email(), 1000);
    }

});