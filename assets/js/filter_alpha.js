

jQuery(document).ready(function($) {

    $('a.iwj-alpha-filter').on('click', function(e){
        e.preventDefault();

        var filter = $(this).data('filter');

        $('.iwj-alpha').removeClass('active');
        $(this).closest('.iwj-alpha').addClass('active');

        if ($('input[name="iwj-alpha-filter"]').length) {
            $('input[name="iwj-alpha-filter"]').val(filter);
        }

        iwj_filter_employer.filter_and_count_employers(1);
    });
});


