
jQuery(document).ready(function($) {

    $('body').delegate('.iwjob-ajax-pagination li a', 'click', function(e) {
        e.preventDefault();

        $('.iwjob-ajax-pagination li').removeClass('active');
        $(this).closest('li').addClass('active');

        var paged = $(this).closest('li').data('paged');

        var pag = $(this).closest('.w-pagination');

        pag.find('input[name="page_number"]').val(paged);

        if ( pag.hasClass('ajax-candidate-pagination')) {
            iwj_filter_candidate.filter_and_count_candidates(paged);
        } else if ( pag.hasClass('ajax-employer-pagination')) {
            iwj_filter_employer.filter_and_count_employers(paged);
        } else {
            iwj_filter_job.filter_and_count_jobs(paged);
        }

    });

});

