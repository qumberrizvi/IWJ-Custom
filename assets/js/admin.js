/*
 * @package Inwave Event
 * @version 1.0.0
 * @created Jun 2, 2016
 * @author Inwavethemes
 * @email inwavethemes@gmail.com
 * @website http://inwavethemes.com
 * @support Ticket https://inwave.ticksy.com/
 * @copyright Copyright (c) 2015 Inwavethemes. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */

/**
 * Description of iwevent-script
 *
 * @developer Hien Tran
 */
(function ($) {
    'use strict';

    $(document).ready(function ($) {


        function iwj_send_all_email(data_send) {

            $.ajax({
                type: "POST",
                url: iwjadmin.ajax_url,
                dataType: 'json',
                data: data_send,
                success: function (data) {

                    $('#progress-bar-send-mail').css('width', data.percent + '%');
                    $('#progress-bar-send-mail').text(data.percent + '%');


                    if (data.next == 1) {
                        var data_send = {'step': data.step + 1, 'total': data.total, 'current': data.current, action: 'iwj_send_all_email'};

                        iwj_send_all_email(data_send);
                    } else {
                        alert('done');
                    }
                }
            });

        }


        $('#send-all-email').on('click', function (e) {
            e.preventDefault();

            $(this).hide();

            $('.send-all-email-control').find('.progress').show();

            $.ajax({
                type: "POST",
                url: iwjadmin.ajax_url,
                dataType: 'json',
                data: {'step': 1, action: 'iwj_send_all_email'},
                success: function (data) {

                    $('#progress-bar-send-mail').css('width', data.percent + '%');
                    $('#progress-bar-send-mail').text(data.percent + '%');

                    if (data.next == 1) {
                        var data_send = {'step': data.step + 1, 'total': data.total, 'current': data.current, action: 'iwj_send_all_email'};
                        iwj_send_all_email(data_send);
                    } else {
                        alert('done');
                    }

                }
            });
        });


        if ($('#iwj-setting-tabs').length) {
            $('#iwj-setting-tabs').tabs({
                activate: function (event, ui) {
                    localStorage.selectedTab = ui.newTab.index() + 1;
                },
                active: localStorage.selectedTab ? localStorage.selectedTab - 1 : 0
            }
            );
        }
        if ($('.iwj-setting-accordion').length) {
            $('.iwj-setting-accordion').accordion({
                heightStyle: "content"
            });
        }
        if ($('.iwj-setting-apply-form').length) {
            $('.iwj-setting-apply-form table').sortable({
                items: 'tr:not(".disabled")'
            });
        }

        $('.add-apply-form-field').click(function (e) {
            e.preventDefault();
            var tr = $(this).closest('tr');
            var new_tr = tr.prev('tr').clone();
            new_tr.removeClass('core-field');
            new_tr.find('input').val('');
            new_tr.find('textarea').val('');
            new_tr.find('select').prop('selectedIndex', -1);
            new_tr.find('input[readonly]').prop('readOnly', false);
            new_tr.find('select[disabled]').prop('disabled', false);
            new_tr.find('button[disabled]').prop('disabled', false);
            tr.before(new_tr);
        });

        $('.add-review-option-field').click(function (e) {
            e.preventDefault();
            var tr = $(this).closest('tr');
            var new_tr = tr.prev('tr').clone();
            new_tr.find('input').val('');
            new_tr.find('button[disabled]').prop('disabled', false);
            tr.before(new_tr);
        });

        $('.iwj-setting-page').on('click', '.review_option_remove_field', function (e) {
            e.preventDefault();
            var parent = $(this).closest('tr');
            parent.remove();
        });

        $('.iwj-setting-page').on('change', '.apply_field_type, .apply_form_required', function (e) {
            var value = $(this).val();
            $(this).next('input[type="hidden"]').val(value);
        });

        $('.iwj-setting-page').on('click', '.apply_form_remove_field', function (e) {
            e.preventDefault();
            var parent = $(this).closest('tr');
            if (!parent.hasClass('core-field')) {
                parent.remove();
            }
        });

        $('#iwj-candidate-status').change(function () {
            var value = $(this).val();
            if (value == 'iwj-incomplete') {
                $('#iwj-candidate-reason').show();
            } else {
                $('#iwj-candidate-reason').hide();
            }
        });

        $('#iwj-employer-status').change(function () {
            var value = $(this).val();
            if (value == 'iwj-incomplete') {
                $('#iwj-employer-reason').show();
            } else {
                $('#iwj-employer-reason').hide();
            }
        });

        $('#iwj-job-status').change(function () {
            var value = $(this).val();
            if (value == 'iwj-rejected') {
                $('#iwj-job-reason').show();
            } else {
                $('#iwj-job-reason').hide();
            }
        });

        if ($('#iwj-order-from-date').length) {
            $('#iwj-order-from-date').datetimepicker({
                timepicker: false,
                format: 'Y/m/d',
                onSelectDate: function (ct, $i) {
                    $('#iwj-order-to-date').datetimepicker('setOptions', {minDate: $i.val()});
                }
            });
            $('#iwj-order-to-date').datetimepicker({
                timepicker: false,
                format: 'Y/m/d',
            });
        }

        $('.iwj-reset-settings').click(function (e) {
            e.preventDefault();
            var self = $(this);
            if (self.is(':disabled')) {
                return false;
            }
            if (confirm("Are you sure want to reset settings to default") == true) {
                var original_text = self.html();
                self.attr("disabled", true);
                self.html('Reseting...');
                var data = {
                    action: 'iwj_reset_settings',
                    _ajax_nonce: iwjadmin.security
                };
                $.post(iwjadmin.ajax_url, data, function (response) {
                    self.attr("disabled", false);
                    self.html(original_text);
                });
            }
        });

        $('input[type="hidden"].iwj_num_rate').rating();

        $('select[name="iwj_admin_rev_status"]').on('change', function () {
            var self_val = $(this).val();
            if (self_val === 'reject') {
                $(this).closest('table.iwj-edit-review-form').find('.iwj_reason_reject_rev').removeClass('hidden');
            } else {
                if (!$(this).closest('table.iwj-edit-review-form').find('.iwj_reason_reject_rev').hasClass('hidden')) {
                    $(this).closest('table.iwj-edit-review-form').find('.iwj_reason_reject_rev').addClass('hidden');
                }
            }
        });

        $('.iwj_lim_skill_showcase input[type="number"]').attr({"max": 100, "min": 0});
        $('.iwj_lim_skill_showcase input[type="number"]').change(function () {
            var max = parseInt($(this).attr('max')),
                    min = parseInt($(this).attr('min'));
            if ($(this).val() > max) {
                $(this).val(max);
            } else if ($(this).val() < min) {
                $(this).val(min);
            }
        });

        $('.iwj_lim_ide_import input[type="number"]').attr({"max": 25, "min": 1});
        $('.iwj_lim_ide_import input[type="number"]').change(function () {
            var max = parseInt($(this).attr('max')),
                    min = parseInt($(this).attr('min'));
            if ($(this).val() > max) {
                $(this).val(max);
            } else if ($(this).val() < min) {
                $(this).val(min);
            }
        });

        $("#sortable").sortable({
            placeholder: "ui-state-highlight",
            items: "tr:not(.ui-state-disabled)"
        });
        $("#sortable").disableSelection();

    });

    /**
     * Order Notes Panel
     */
    var iwj_meta_boxes_order_notes = {
        init: function () {
            $('#iwj-order-notes')
                    .on('click', 'button.iwj-add-note', this.add_order_note)
                    .on('click', 'a.iwj-delete-note', this.delete_order_note);

        },

        add_order_note: function () {
            if (!$('textarea#iwj-add-order-note').val()) {
                return;
            }

            $('#iwj-order-notes').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });

            var data = {
                action: 'iwj_add_order_note',
                post_id: $('input#iwj-order-note-id').val(),
                note: $('textarea#iwj-add-order-note').val(),
                note_type: $('select#iwj-order-note-type').val(),
                _ajax_nonce: iwjadmin.security
            };

            $.post(iwjadmin.ajax_url, data, function (response) {
                $('ul.iwj-order-notes').prepend(response);
                $('#iwj-order-notes').unblock();
                $('#iwj-add-order-note').val('');
            });

            return false;
        },

        delete_order_note: function () {
            if (window.confirm(iwjadmin.i18n_delete_note)) {
                var note = $(this).closest('li.iwj-note');

                $(note).block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });

                var data = {
                    action: 'iwj_delete_order_note',
                    note_id: $(note).attr('rel'),
                    _ajax_nonce: iwjadmin.security
                };

                $.post(iwjadmin.ajax_url, data, function () {
                    $(note).remove();
                });
            }

            return false;
        }
    };

    iwj_meta_boxes_order_notes.init();

    $('.iwj-send-cusomer-invoice').click(function () {
        var self = $(this);
        var order_id = self.data('order-id');
        var sending_text = self.data('sending-text');
        var ori_text = self.html();

        var data = 'action=iwj_send_customer_invoice&_ajax_nonce=' + iwjadmin.security + '&order_id=' + order_id;
        $.ajax({
            url: iwjadmin.ajax_url,
            type: 'POST',
            data: data,
            dataType: 'json',
            beforeSend: function () {
                self.html(sending_text);
            },
            success: function (result) {
                self.html(ori_text);
                if (result && !result.success) {
                    alert(result.message);
                }
            }
        });
    });

    $('.iwj-create-all-job-products').click(function (e) {
        e.preventDefault();
        var self = $(this);
        var old_text = self.html();
        var data = 'action=iwj_create_all_job_products&_ajax_nonce=' + iwjadmin.security;
        $.ajax({
            url: iwjadmin.ajax_url,
            type: 'POST',
            data: data,
            dataType: 'json',
            beforeSend: function () {
                self.html('Creating...');
            },
            success: function (result) {
                self.html(old_text);
                if (result.success == true) {
                    $('.iwj-all-job-products').html(result.html);
                } else {
                    alert(result.message)
                }
            }
        });
    });

    $('.iwj-toogle-job-products').click(function (e) {
        e.preventDefault();
        var self = $(this);
        var parent = self.parent();
        var all_products_div = parent.next('.iwj-all-job-products');
        if(all_products_div.hasClass('hide')){
            all_products_div.removeClass('hide');
            self.html('Hide All Products');
        }else{
            all_products_div.addClass('hide');
            self.html('Show All Products');
        }
    })

})(jQuery);