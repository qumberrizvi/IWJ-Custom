jQuery(function ($) {
    'use strict';
    $(document).ready(function () {
        $('.iwjmb-datetime, .iwjmb-date, .iwjmb-time').each(function () {
            var options = $(this).data('options');
            if (options.lang) {
                $.datetimepicker.setLocale(options.lang);
            } else {
                $.datetimepicker.setLocale(iwjmbDateTime.locale_short);
            }
            $(this).datetimepicker(options);
        });
    });
});
