
(function($) {

    var iwj_filter_candidate;

    iwj_filter_candidate = {
        filter_and_count_candidates : function(paged, callback) {

            //hide filter in mobile
            var filter_job = $('.iwj-sidebar-1');
            filter_job.removeClass('open-filter');

            $('.iwj-count').text('...');

            $('body').addClass('iwj-loading');

            var data_frm = [];

            $('.candidate-form-filter').each(function () {
                data_frm.push($(this).serializeFormJSON());
            });

            var data_submit = {};
            $.each(data_frm, function (index, value) {
                data_submit = $.extend(data_submit, value);
            });

            data_submit.order = $('.sorting-candidates').val();

            data_submit.paged = paged;
            data_submit.url = $('#url').val();

            data_submit.keyword = $("form#iwjob-search input[name='keyword']").val();

            data_submit.action = 'iwj_filter_and_count_candidates';

            data_submit.mode = iwjCookie.getCookie('job-archive-view');

            data_submit._ajax_nonce = iwj.security;

            $.ajax({
                type: "POST",
                url: iwj.ajax_url,
                dataType:'json',
                data: data_submit,
                success: function(data) {

                    window.history.pushState('', '', data.url);
                    if($('.iwj-candidate-feed').length){
                        $('.iwj-candidate-feed').attr('href', data.feed_url);
                    }

                    if (data.status == '1') {
                        $("#iwajax-load-candidates").html(data.html);
                    } else {
                        $("#iwajax-load-candidates").html('');
                    }

                    $.fn.matchHeight._apply($('.iwj-candidates.iwj-grid .candidate-item'), {
                        byRow: true,
                        property: 'height',
                        target: null,
                        remove: false
                    });

                    // end success
                    // begin process count jobs
                    $('.iwj-count').text(0);
                    $('.iwj-count').closest('li').data('order', 0);
                    if (data.count_candidates) {
                        $.each(data.count_candidates, function( index, value ) {
                            $('#iwj-count-'+value.idx).text(value.val);
                            $('#iwj-count-'+value.idx).closest('li').data('order', value.val);
                        });
                    }

                    iwj_filter_common.sort_tax_after_ajax();
                    // end process count jobs

                    if(typeof window.callback === 'function'){
                        window.callback(data);
                    }

                    $('body').removeClass('iwj-loading');
                }
            });
        }
    };

    window.iwj_filter_candidate = iwj_filter_candidate;

})(jQuery);

jQuery(document).ready(function($) {


    $('body').delegate('#clear-filter-candidate', 'click', function(e) {
        e.preventDefault();

        if(typeof window.iwj_before_remove_all_filter_callback == 'object'){
            for (var key in window.iwj_before_remove_all_filter_callback) {
                // skip loop if the property is from prototype
                if (!window.iwj_before_remove_all_filter_callback.hasOwnProperty(key)) continue;

                var callback_function = window.iwj_before_remove_all_filter_callback[key];
                if(typeof callback_function == 'function'){
                    callback_function('candidate');
                }
            }
        }

        $('li.iwj-filter-selected-item').remove();

        $('.iwjob-filter-candidates-cbx').prop('checked', false);

        iwj_filter_candidate.filter_and_count_candidates(1);

        iwj_filter_common.display_filter_box();

        if(typeof window.iwj_after_remove_all_filter_callback == 'object'){
            for (var key in window.iwj_after_remove_all_filter_callback) {
                // skip loop if the property is from prototype
                if (!window.iwj_after_remove_all_filter_callback.hasOwnProperty(key)) continue;

                var callback_function = window.iwj_after_remove_all_filter_callback[key];
                if(typeof callback_function == 'function'){
                    callback_function('candidate');
                }
            }
        }
    });


    $('body').delegate('.iwjob-filter-candidates-cbx', 'change', function(e) {

        var value = $(this).val();

        if(typeof window.iwj_before_filter_callback == 'object'){
            for (var key in window.iwj_before_filter_callback) {
                // skip loop if the property is from prototype
                if (!window.iwj_before_filter_callback.hasOwnProperty(key)) continue;

                var callback_function = window.iwj_before_filter_callback[key];
                if(typeof callback_function == 'function'){
                    callback_function(value, 'canidate');
                }
            }
        }

        var id_js = '#iwj-filter-selected-item-'+value;
        if ( $(this).prop("checked") ) {
            $(this).closest('li').addClass('checked');

            var id_html = 'iwj-filter-selected-item-'+value;

            if (!$('#iwj-filter-selected').find(id_js).length) {
                if($('#iwj-filter-selected').find('ul').length) {
                    var li = '<li id="'+id_html+'" data-type="candidate" data-termid="'+value+'" class="iwj-filter-selected-item"><label>'+$(this).data('title')+'</label><a href="#" class="remove"><i class="ion-android-close"></i></a></li>';
                    $('#iwj-filter-selected').find('ul').append(li);
                } else {
                    $('#iwj-filter-selected').append('<ul class="clearfix"></ul>');
                    var li = '<li id="'+id_html+'" data-type="candidate" data-termid="'+value+'" class="iwj-filter-selected-item"><label>'+$(this).data('title')+'</label><a href="#" class="remove"><i class="ion-android-close"></i></a></li>';
                    $('#iwj-filter-selected').find('ul').append(li);
                }
            }
        } else {
            $(this).closest('li').removeClass('checked');

            if ($('#iwj-filter-selected').find(id_js).length) {
                $('#iwj-filter-selected').find(id_js).remove();
            }
        }
        iwj_filter_candidate.filter_and_count_candidates(1);

        iwj_filter_common.display_filter_box();

        if(typeof window.iwj_after_filter_callback == 'object'){
            for (var key in window.iwj_after_filter_callback) {
                // skip loop if the property is from prototype
                if (!window.iwj_after_filter_callback.hasOwnProperty(key)) continue;

                var callback_function = window.iwj_after_filter_callback[key];
                if(typeof callback_function == 'function'){
                    callback_function(value, 'candidate');
                }
            }
        }
    });


    $('body').delegate('.sorting-candidates', 'change', function() {
        iwj_filter_candidate.filter_and_count_candidates(1);
    });


});


