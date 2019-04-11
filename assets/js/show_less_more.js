jQuery(document).ready(function($) {

    $('body').delegate('.show-more a', 'click', function(e) {
        e.preventDefault();
        var ulSelector = $(this).closest('ul');
        $('li.show-more', ulSelector).hide();
        var limit = 0;
        var cfg_limit_show_more_selector = $(this).closest('form').find('input[name="limit_show_more"]');
        if (cfg_limit_show_more_selector.length) {
            limit = parseInt(cfg_limit_show_more_selector.val());
        }
        if(limit > 0){
            $('> li.iwj-input-checkbox:lt('+limit+')', ulSelector).show();
        }else{
            $('> li.iwj-input-checkbox', ulSelector).show();
        }
        // show all li tag
        // show li show less
        $('li.show-less', ulSelector).show();
    });

    $('body').delegate('.show-less a', 'click', function(e) {
        e.preventDefault();
        var ul_selector = $(this).closest('ul');
        $('li.show-less', ul_selector).hide();

        var limit = 0;
        var input_limit_selector = ul_selector.closest('form').find('input[name="limit"]');
        if (input_limit_selector.length) {
            var limit = parseInt(input_limit_selector.val());
        }
        if(limit > 0){
            var li_show = '> li.iwj-input-checkbox:lt('+limit+')';
        }else{
            var li_show = '> li.iwj-input-checkbox';
        }
        $('> li.iwj-input-checkbox', ul_selector).hide();
        $(li_show, ul_selector).show();
        $('li.show-more', ul_selector).show();
    });
});


