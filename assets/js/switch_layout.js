

jQuery(document).ready(function($) {

    function init_mode_view() {
        var displayMode = iwjCookie.getCookie('job-archive-view');

        if (displayMode == 'grid') {
            change_mode_view('grid');
        }
    }
    window.iwj_change_list_view_callback = function(data) {
        $('#iwajax-load-employers > div').removeClass('iwj-grid').addClass('iwj-listing');
        $('#iwajax-load-candidates > div').removeClass('iwj-grid').addClass('iwj-listing');
    };
    window.iwj_change_grid_view_callback = function(data) {
        $('#iwajax-load-employers > div').removeClass('iwj-listing').addClass('iwj-grid');
        $('#iwajax-load-employers > div').removeClass('iwj-listing').addClass('iwj-grid');
    };
    function change_mode_view(change_mode) {

        if (change_mode == 'list') {
            $('a.layout-grid').closest('li').removeClass('active');
            $('a.layout-list').closest('li').addClass('active');

            iwjCookie.setCookie('job-archive-view', 'list', 1);

            var displayMode = iwjCookie.getCookie('job-archive-view');

            if ( $('#iwajax-load').length) {
                $('#iwajax-load > div').removeClass('iwj-grid').addClass('iwj-listing');
            }
            if ( $('#iwajax-load-candidates').length) {
                if ($('.ajax-candidate-pagination').length) {
                    var current_page = $('.ajax-candidate-pagination').find('input[name="page_number"]').val();
                } else {
                    var current_page = 1;
                }

                iwj_filter_candidate.filter_and_count_candidates(current_page, iwj_change_list_view_callback);
            }

            if ( $('#iwajax-load-employers').length) {
                if ($('.ajax-employer-pagination').length) {
                    var current_page = $('.ajax-employer-pagination').find('input[name="page_number"]').val();
                } else {
                    var current_page = 1;
                }

                iwj_filter_employer.filter_and_count_employers(current_page, iwj_change_list_view_callback);
            }
        } else {
            $('a.layout-list').closest('li').removeClass('active');
            $('a.layout-grid').closest('li').addClass('active');

            iwjCookie.setCookie('job-archive-view', 'grid', 1);
            var displayMode = iwjCookie.getCookie('job-archive-view');

            if ( $('#iwajax-load').length ) {
                $('#iwajax-load > div').removeClass('iwj-listing').addClass('iwj-grid');
                if(!$('.iwj-grid .job-item').data('setmatchHeight')){
                    $('.iwj-grid .job-item').matchHeight({
                        byRow: true,
                        property: 'height',
                        target: null,
                        remove: false
                    });
                    $('.iwj-grid .job-item').data('setmatchHeight', true);
                }
            }

            if ( $('#iwajax-load-candidates').length) {
                if ($('.ajax-candidate-pagination').length) {
                    var current_page = $('.ajax-candidate-pagination').find('input[name="page_number"]').val();
                } else {
                    var current_page = 1;
                }

                iwj_filter_candidate.filter_and_count_candidates(current_page, iwj_change_grid_view_callback);
            }

            if ( $('#iwajax-load-employers').length) {
                if ($('.ajax-employer-pagination').length) {
                    var current_page = $('.ajax-employer-pagination').find('input[name="page_number"]').val();
                } else {
                    var current_page = 1;
                }

                iwj_filter_employer.filter_and_count_employers(current_page, iwj_change_grid_view_callback);
            }
        }

    }

    setTimeout(function () {
        $('body').delegate('a.layout-list', 'click', function(e) {
            e.preventDefault();
            change_mode_view('list');
        });
    }, 200);

    setTimeout(function () {
        $('body').delegate('a.layout-grid', 'click', function(e) {
            e.preventDefault();
            change_mode_view('grid');
        });
    }, 200);

});


