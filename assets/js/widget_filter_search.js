
jQuery(document).ready(function($) {

    $("form#iwjob-search input[name='keyword']").on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            var type = $(this).closest('form').find('input[name="type"]').val();

            if (type == 'candidate') {
                iwj_filter_candidate.filter_and_count_candidates(1);
            } else if (type == 'employer') {
                iwj_filter_employer.filter_and_count_employers(1);
            } else {
                iwj_filter_job.filter_and_count_jobs(1);
            }

            return false;
        }
    });

    $('.btn-iwjob-search').on('click', function(e){
        e.preventDefault;
        var type = $(this).closest('form').find('input[name="type"]').val();

        if (type == 'candidate') {
            iwj_filter_candidate.filter_and_count_candidates(1);
        } else if (type == 'employer') {
            iwj_filter_employer.filter_and_count_employers(1);
        } else {
            iwj_filter_job.filter_and_count_jobs(1);
        }
    });

});

