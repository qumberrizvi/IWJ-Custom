
(function($) {

    var iwj_filter_employer;

    iwj_filter_employer = {
        filter_and_count_employers : function(paged, callback) {

            //hide filter in mobile
            var filter_job = $('.iwj-sidebar-1');
            filter_job.removeClass('open-filter');

            $('.iwj-count').text('...');

            $('body').addClass('iwj-loading');

            var data_frm = [];

            $('.employer-form-filter').each(function () {
                data_frm.push($(this).serializeFormJSON());
            });

            var data_submit = {};
            $.each(data_frm, function (index, value) {
                data_submit = $.extend(data_submit, value);
            });

            data_submit.order = $('.sorting-employers').val();

            data_submit.paged = paged;
            data_submit.url = $('#url').val();

            data_submit.keyword = $("form#iwjob-search input[name='keyword']").val();

            data_submit.action = 'iwj_filter_and_count_employers';

            data_submit.mode = iwjCookie.getCookie('job-archive-view');

            if ($('input[name="iwj-alpha-filter"]').length) {
                data_submit.alpha = $('input[name="iwj-alpha-filter"]').val();
            } else {
                data_submit.alpha = '';
            }

            data_submit._ajax_nonce = iwj.security;

            $.ajax({
                type: "POST",
                url: iwj.ajax_url,
                dataType:'json',
                data: data_submit,
                success: function(data) {

                    window.history.pushState('', '', data.url);

                    if($('.iwj-employer-feed').length){
                        $('.iwj-employer-feed').attr('href', data.feed_url);
                    }

                    if (data.status == '1') {
                        $("#iwajax-load-employers").html(data.html);
                    } else {
                        $("#iwajax-load-employers").html('');
                    }

                    $.fn.matchHeight._apply($('.iwj-grid .iwj-employer-item'), {
                        byRow: true,
                        property: 'height',
                        target: null,
                        remove: false
                    });

                    // end success

                    // begin process count jobs
                    $('.iwj-count').text(0);
                    $('.iwj-count').closest('li').data('order', 0);
                    if (data.count_employers) {
                        $.each(data.count_employers, function( index, value ) {
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

    window.iwj_filter_employer = iwj_filter_employer;

})(jQuery);

jQuery(document).ready(function($) {


    $('body').delegate('#clear-filter-employer', 'click', function(e) {
        e.preventDefault();

        if(typeof window.iwj_before_remove_all_filter_callback == 'object'){
            for (var key in window.iwj_before_remove_all_filter_callback) {
                // skip loop if the property is from prototype
                if (!window.iwj_before_remove_all_filter_callback.hasOwnProperty(key)) continue;

                var callback_function = window.iwj_before_remove_all_filter_callback[key];
                if(typeof callback_function == 'function'){
                    callback_function('employer');
                }
            }
        }

        $('li.iwj-filter-selected-item').remove();
        $('.iwj-alpha').removeClass('active');
        $('.iwj-alpha-').addClass('active');
        if ( $('input[name="iwj-alpha-filter"]').length) {
            $('input[name="iwj-alpha-filter"]').val('');
        }

        $('.iwjob-filter-employers-cbx').prop('checked', false); // Unchecks it
        iwj_filter_employer.filter_and_count_employers(1);

        iwj_filter_common.display_filter_box();

        if(typeof window.iwj_after_remove_all_filter_callback == 'object'){
            for (var key in window.iwj_after_remove_all_filter_callback) {
                // skip loop if the property is from prototype
                if (!window.iwj_after_remove_all_filter_callback.hasOwnProperty(key)) continue;

                var callback_function = window.iwj_after_remove_all_filter_callback[key];
                if(typeof callback_function == 'function'){
                    callback_function('employer');
                }
            }
        }
    });


    $('body').delegate('.iwjob-filter-employers-cbx', 'change', function(e) {

        var value = $(this).val();

        if(typeof window.iwj_before_filter_callback == 'object'){
            for (var key in window.iwj_before_filter_callback) {
                // skip loop if the property is from prototype
                if (!window.iwj_before_filter_callback.hasOwnProperty(key)) continue;

                var callback_function = window.iwj_before_filter_callback[key];
                if(typeof callback_function == 'function'){
                    callback_function(value, 'employer');
                }
            }
        }

        var id_js = '#iwj-filter-selected-item-'+value;
        if ( $(this).prop("checked") ) {
            $(this).closest('li').addClass('checked');

            var id_html = 'iwj-filter-selected-item-'+value;

            if (!$('#iwj-filter-selected').find(id_js).length) {
                if($('#iwj-filter-selected').find('ul').length) {
                    var li = '<li id="'+id_html+'" data-type="employer" data-termid="'+value+'" class="iwj-filter-selected-item"><label>'+$(this).data('title')+'</label><a href="#" class="remove"><i class="ion-android-close"></i></a></li>';
                    $('#iwj-filter-selected').find('ul').append(li);
                } else {
                    $('#iwj-filter-selected').append('<ul class="clearfix"></ul>');
                    var li = '<li id="'+id_html+'" data-type="employer" data-termid="'+value+'" class="iwj-filter-selected-item"><label>'+$(this).data('title')+'</label><a href="#" class="remove"><i class="ion-android-close"></i></a></li>';
                    $('#iwj-filter-selected').find('ul').append(li);
                }
            }
        } else {
            $(this).closest('li').removeClass('checked');

            if ($('#iwj-filter-selected').find(id_js).length) {
                $('#iwj-filter-selected').find(id_js).remove();
            }
        }
        iwj_filter_employer.filter_and_count_employers(1);

        iwj_filter_common.display_filter_box();

        if(typeof window.iwj_after_filter_callback == 'object'){
            for (var key in window.iwj_after_filter_callback) {
                // skip loop if the property is from prototype
                if (!window.iwj_after_filter_callback.hasOwnProperty(key)) continue;

                var callback_function = window.iwj_after_filter_callback[key];
                if(typeof callback_function == 'function'){
                    callback_function(value, 'employer');
                }
            }
        }
    });

    $('body').delegate('.sorting-employers', 'change', function() {
        iwj_filter_employer.filter_and_count_employers(1);
    });


});

