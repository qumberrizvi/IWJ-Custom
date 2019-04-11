jQuery(document).ready(function ($) {
    $('.iw-job-advanced_search .hide-advance').click(function(){
        var that = $(this);
        var parent = that.closest('.iw-job-advanced_search');
        parent.find('.advanced-fields').toggle(400);
        if(that.hasClass('active')){
            that.html(iwj_search_advanced.show_advance_text);
        }else{
            that.html(iwj_search_advanced.hide_advance_text)
        }
        that.toggleClass('active');
    });
});