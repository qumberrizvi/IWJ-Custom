
(function($) {

    var iwj_filter_job;

    iwj_filter_job = {
        filter_and_count_jobs : function(paged) {
            //hide filter in mobile
            var filter_job = $('.iwj-sidebar-1');
            filter_job.removeClass('open-filter');

            // pre load
            $('.iwj-count').text('...');

            $('body').addClass('iwj-loading');

            // end pre load

            var data_frm = [];

            $('.job-form-filter').each(function () {
                data_frm.push($(this).serializeFormJSON());
            });

            var data_submit = {};
            $.each(data_frm, function (index, value) {
                data_submit = $.extend(data_submit, value);
            });

            data_submit.order = $('.sorting-job').val();

            data_submit.paged = paged;
            data_submit.url = $('#url').val();

            if ($("form#iwjob-search input[name='keyword']").length) {
                data_submit.keyword = $("form#iwjob-search input[name='keyword']").val();
            }

            data_submit.action = 'iwj_filter_and_count_jobs';
            if ($('form[name="is_tax_page_job"]').length) {
                var tax_name = $('form[name="is_tax_page_job"]').find('input#is-tax-page-job').attr('name');
                var tax_val = $('form[name="is_tax_page_job"]').find('input#is-tax-page-job').val();
                data_submit.query_is_tax_page = {};
                data_submit.query_is_tax_page[tax_name] = tax_val;
            }

            data_submit._ajax_nonce = iwj.security;

            if(typeof window.iwj_filter_jobs_data === 'function'){
                data_submit = window.iwj_filter_jobs_data(data_submit);
            }

            $.ajax({
                type: "POST",
                url: iwj.ajax_url,
                dataType:'json',
                data: data_submit,
                beforeSend : function () {
                    if(typeof window.iwj_filter_jobs_before_send === 'function'){
                        data_submit = window.iwj_filter_jobs_before_send(data_submit);
                    }
                },
                success: function(data) {

                    // end filter jobs
                    window.history.pushState('', '', data.url);

                    if($('.iwj-job-feed').length){
                        $('.iwj-job-feed').attr('href', data.feed_url);
                    }

                    if (data.status == '1') {
                        $("#iwajax-load").html(data.html);
                        if($('.iwj-grid').length){
                            $('.iwj-grid .job-item').matchHeight({
                                byRow: true,
                                property: 'height',
                                target: null,
                                remove: false
                            });
                            $('.iwj-grid .job-item').data('setmatchHeight', true);
                        }
                    } else {
                        $("#iwajax-load").html('');
                    }
                    // end filter jobs

                    // begin process count jobs
                    $('.iwj-count').text(0);
                    $('.iwj-count').closest('li').data('order', 0);

                    if (data.count_jobs) {
                        $.each(data.count_jobs, function( index, value ) {
                            $('#iwj-count-'+value.idx).text(value.val);
                            $('#iwj-count-'+value.idx).closest('li').data('order', value.val);
                        });
                    }

                    iwj_filter_common.sort_tax_after_ajax();

                    if(typeof window.iwj_filter_jobs_success === 'function'){
                        data_submit = window.iwj_filter_jobs_success(data, data_submit);
                    }

                    $('body').removeClass('iwj-loading');

                }
            });

        }
    };

    window.iwj_filter_job = iwj_filter_job;

})(jQuery);

jQuery(document).ready(function($) {

    $('body').delegate('#clear-filter-job', 'click', function(e) {
        e.preventDefault();

        if(typeof window.iwj_before_remove_all_filter_callback == 'object'){
            for (var key in window.iwj_before_remove_all_filter_callback) {
                // skip loop if the property is from prototype
                if (!window.iwj_before_remove_all_filter_callback.hasOwnProperty(key)) continue;

                var callback_function = window.iwj_before_remove_all_filter_callback[key];
                if(typeof callback_function == 'function'){
                    callback_function('job');
                }
            }
        }

        $('li.iwj-filter-selected-item').remove();

        $('.iwjob-filter-jobs-cbx').prop('checked', false); // Unchecks it
        $('form[name="iwjob-other-filters"] input').val('');

        iwj_filter_job.filter_and_count_jobs(1);

        iwj_filter_common.display_filter_box();

        if(typeof window.iwj_after_remove_all_filter_callback == 'object'){
            for (var key in window.iwj_after_remove_all_filter_callback) {
                // skip loop if the property is from prototype
                if (!window.iwj_after_remove_all_filter_callback.hasOwnProperty(key)) continue;

                var callback_function = window.iwj_after_remove_all_filter_callback[key];
                if(typeof callback_function == 'function'){
                    callback_function('job');
                }
            }
        }
    });

    $('body').delegate('.iwjob-filter-jobs-cbx', 'change', function(e) {

        var value = $(this).val();
        var getname = $(this).attr("name");

        if(typeof window.iwj_before_filter_callback == 'object'){
            for (var key in window.iwj_before_filter_callback) {
                // skip loop if the property is from prototype
                if (!window.iwj_before_filter_callback.hasOwnProperty(key)) continue;

                var callback_function = window.iwj_before_filter_callback[key];
                if(typeof callback_function == 'function'){
                    callback_function(value, 'job');
                }
            }
        }

        var id_js = '#iwj-filter-selected-item-'+value;
        if ( $(this).prop("checked") ) {
            $(this).closest('li').addClass('checked');

            var id_html = 'iwj-filter-selected-item-'+value;

            if (!$('#iwj-filter-selected').find(id_js).length) {
                if($('#iwj-filter-selected').find('ul').length) {
                    var li = '<li id="'+id_html+'" data-type="job" data-termid="'+value+'" class="iwj-filter-selected-item"><label>'+$(this).data('title')+'</label><a href="#" class="remove"><i class="ion-android-close"></i></a></li>';
                    $('#iwj-filter-selected').find('ul').append(li);
                } else {
                    $('#iwj-filter-selected').append('<ul class="clearfix"></ul>');
                    var li = '<li id="'+id_html+'" data-type="job" data-termid="'+value+'" class="iwj-filter-selected-item"><label>'+$(this).data('title')+'</label><a href="#" class="remove"><i class="ion-android-close"></i></a></li>';
                    $('#iwj-filter-selected').find('ul').append(li);
                }

                //add name category after TITLE
                if(getname === "iwj_cat[]"){
                    var span = '<span id="'+id_html+'" data-type="job" data-termid="'+value+'" class="iwj-filter-selected-item"><label>'+$(this).data('title')+'</label></span>';
                    $('.find-jobs-results').append(span);
                }

            }
        } else {
            $(this).closest('li').removeClass('checked');

            if ($('#iwj-filter-selected').find(id_js).length) {
                    $('#iwj-filter-selected').find(id_js).remove();
            }
            //remove name category after TITLE
            if(getname === "iwj_cat[]") {
                $('.find-jobs-results').find(id_js).remove();
            }
        }

        iwj_filter_job.filter_and_count_jobs(1);
        iwj_filter_common.display_filter_box();

        if(typeof window.iwj_after_filter_callback == 'object'){
            for (var key in window.iwj_after_filter_callback) {
                // skip loop if the property is from prototype
                if (!window.iwj_after_filter_callback.hasOwnProperty(key)) continue;

                var callback_function = window.iwj_after_filter_callback[key];
                if(typeof callback_function == 'function'){
                    callback_function(value, 'job');
                }
            }
        }

    });


    $('body').delegate('.sorting-job', 'change', function() {
        iwj_filter_job.filter_and_count_jobs(1);
    });

});