
jQuery(document).ready(function($) {
    $('body').delegate('li.iwj-filter-selected-item', 'click', function(e) {
        e.preventDefault();
        $(this).remove();

        var term_id = $(this).data('termid');
        var type = $(this).data('type');
        var search_type = $(this).data('search_type');

        if(typeof window.iwj_before_remove_filter_callback == 'object'){
            for (var key in window.iwj_before_remove_filter_callback) {
                // skip loop if the property is from prototype
                if (!window.iwj_before_remove_filter_callback.hasOwnProperty(key)) continue;

                var callback_function = window.iwj_before_remove_filter_callback[key];
                if(typeof callback_function == 'function'){
                    callback_function(term_id, type);
                }
            }
        }

        if(search_type){
            if(search_type == 'keyword'){
                $('form[name="iwjob-other-filters"] input[name="keyword"]').val('');
            }else if(search_type == 'radius'){
                $('form[name="iwjob-other-filters"] input[name="current_lat"]').val('');
                $('form[name="iwjob-other-filters"] input[name="current_lng"]').val('');
                $('form[name="iwjob-other-filters"] input[name="current_address"]').val('');
                $('form[name="iwjob-other-filters"] input[name="current_radius"]').val('');
            }
            iwj_filter_job.filter_and_count_jobs(1);
        }else{
            if (type == 'candidate') {
                $('#iwjob-filter-candidates-cbx-'+term_id).trigger('click');
            } else if (type == 'employer') {
                $('#iwjob-filter-employers-cbx-'+term_id).trigger('click');
            } else {
                $('#iwjob-filter-jobs-cbx-'+term_id).trigger('click');
            }
        }

        iwj_filter_common.display_filter_box();

        if(typeof window.iwj_after_remove_filter_callback == 'object'){
            for (var key in window.iwj_after_remove_filter_callback) {
                // skip loop if the property is from prototype
                if (!window.iwj_after_remove_filter_callback.hasOwnProperty(key)) continue;

                var callback_function = window.iwj_after_remove_filter_callback[key];
                if(typeof callback_function == 'function'){
                    callback_function(term_id, type);
                }
            }
        }
    });
});



