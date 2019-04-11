(function($) {

    var iwj_filter_common;

    var ul_taxs = [];

    iwj_filter_common = {
        add_class_has_item : function() {
            if(!ul_taxs.length){
                ul_taxs = $('ul[class^="iwjob-list-"]');
            }
            if(ul_taxs.length){
                ul_taxs.each(function () {

                    var ulSelector = $(this);
                    var liSelector = ulSelector.find("li.iwj-input-checkbox");

                    liSelector.each(function(index, value){
                        if ($(this).data('order') > 0) {
                            $(this).removeClass('empty');
                            $(this).addClass('has-item');
                        } else{
                            $(this).removeClass('has-item');
                            $(this).addClass('empty');
                        }
                    });
                });
            }
        },
        check_filter_job_checked : function() {
            $('input.iwjob-filter-jobs-cbx').each(function() {
                if ( $(this).prop("checked") ) {
                    $(this).closest('li').addClass('checked');
                } else {
                    $(this).closest('li').removeClass('checked');
                }
            });
            $('input.iwjob-filter-candidates-cbx').each(function() {
                if ( $(this).prop("checked") ) {
                    $(this).closest('li').addClass('checked');
                } else {
                    $(this).closest('li').removeClass('checked');
                }
            });
            $('input.iwjob-filter-employers-cbx').each(function() {
                if ( $(this).prop("checked") ) {
                    $(this).closest('li').addClass('checked');
                } else {
                    $(this).closest('li').removeClass('checked');
                }
            });
        },
        sort_tax_after_ajax : function() {
            iwj_filter_common.check_filter_job_checked();

            if(!ul_taxs.length){
                ul_taxs = $('ul[class^="iwjob-list-"]');
            }
            if(ul_taxs.length){
                ul_taxs.each(function () {

                    var ulSelector = $(this);
                    var liSelector = ulSelector.find(" > li.iwj-input-checkbox");
                    var total_visiable = ulSelector.find(" > li.iwj-input-checkbox:visible").length;

                    liSelector.sort(function(a, b){
                            if ($(a).hasClass('checked') && $(b).hasClass('checked')) {
                                var res = $(b).data("order")-$(a).data("order");
                                return res;
                            } else if ($(a).hasClass('checked')) {
                                return -1;
                            } else if ($(b).hasClass('checked')) {
                                return 1;
                            } else {
                                var res = $(b).data("order")-$(a).data("order");
                                return res;
                            }
                        }
                    );

                    var show_more = ulSelector.find('li.show-more');
                    var show_less = ulSelector.find('li.show-less');

                    $( ulSelector ).html(liSelector);

                    // back append show less and show more to ul tag
                    $( ulSelector ).append( show_more );
                    $( ulSelector ).append( show_less );

                    ulSelector.find(" > li.iwj-input-checkbox").hide();
                    ulSelector.find(" > li.iwj-input-checkbox:lt("+total_visiable+")").show();
                });
            }
        },
        display_filter_box : function() {
            if($('li.iwj-filter-selected-item').length) {
                $('#iwj-filter-selected').show();
                $('#iwj-clear-filter-btn').show();
            } else {
                $('#iwj-filter-selected').hide();
                $('#iwj-clear-filter-btn').hide();
            }
        }
    };

    window.iwj_filter_common = iwj_filter_common;

})(jQuery);

jQuery(document).ready(function($) {

    function iwj_init_filter() {
        iwj_filter_common.add_class_has_item();
        iwj_filter_common.check_filter_job_checked();
    }

    iwj_init_filter();

    iwj_filter_common.display_filter_box();

});