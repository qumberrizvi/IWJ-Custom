/* 
 * @package Inwave Job
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
 * Description of injob-script
 *
 * @developer Hien Tran
 */


function iwj_button_loader(self, action) {
    if (action == 'add') {
        var position = self.position();
        var html = '<div class="rem-button-loader spinner" style="display: -webkit-flex; width: ' + self.outerWidth() + 'px; height: ' + self.outerHeight() + 'px; line-height: ' + self.outerHeight() + 'px; left: ' + position.left + 'px; top: ' + position.top + 'px;">' +
                '<div class="bounce1"></div>' +
                '<div class="bounce2"></div>' +
                '<div class="bounce3"></div>' +
                '</div>';
        self.closest('.iwj-button-loader').append(html);
    } else {
        self.closest('.iwj-button-loader').find('.rem-button-loader').remove();
    }
}

function iwj_recaptcha() {
    jQuery('.g-recaptcha').each(function (index, el) {
        grecaptcha.render(el, {'sitekey': jQuery(el).data('sitekey')});
    });
}

function iwj_payment_stripe_callback($form, $submit_btn, price, currency) {
    var token_triggered = false;
    var handler = StripeCheckout.configure({
        key: stripe_options.publish_key,
        image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
        locale: 'auto',
        closed: function () {
            if (!token_triggered) {
                iwj_button_loader($submit_btn, 'remove');
            }
        },
        token: function (token) {
            token_triggered = true;
            if ($form.find('input[name="stripe_token"]').length > 0) {
                $form.find('input[name="stripe_token"]').val(token.id);
                $form.find('input[name="stripe_email"]').val(token.email);
            } else {
                $form.append('<input type="hidden" name="stripe_token" value="' + token.id + '">');
                $form.append('<input type="hidden" name="stripe_email" value="' + token.email + '">');
            }

            $form.submit();
        }
    });

    function stripe_zerocurrency(price, currency) {
        var stripe_zerocurrency = ["BIF", "CLP", "DJF", "GNF", "JPY", "KMF", "KRW", "MGA", "PYG", "RWF", "VND", "VUV", "XAF", "XOF", "XPF"];
        if (jQuery.inArray(currency, stripe_zerocurrency) === -1) {
            price = price * 100;
        }

        return price;
    }

    var $order_name_input = $form.find('input[name="order_name"]');
    if ($order_name_input.length) {
        var order_name = $order_name_input.val();
    } else {
        var order_name = 'Payment';
    }


    handler.open({
        name: order_name,
        currency: currency,
        amount: (stripe_zerocurrency(price, currency))
    });
}

function iwj_payment_method_select_change(payment_input) {
    if (payment_input.val() == 'stripe') {
        jQuery('.iwj-checkout-form .stripe-recurring').show();
    } else {
        jQuery('.iwj-checkout-form .stripe-recurring').hide();
    }

    if (payment_input.val() == 'paypal') {
        jQuery('.iwj-checkout-form .paypal-recurring').show();
    } else {
        jQuery('.iwj-checkout-form .paypal-recurring').hide();
    }
}

(function ($) {
    'use strict';
    $(document).ready(function ($) {

        $("#tabs-modal").iwTabs("tab");

        $('.iwj-login-form').submit(function (e) {
            e.preventDefault();
            var self = $(this);
            var button = self.find('.iwj-login-btn');
            var data = self.serialize();
            data = 'action=iwj_login&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    self.find('.iwj-respon-msg').slideUp(300, function () {
                        $(this).html('');
                    });
                },
                success: function (result) {
                    if (result) {
                        self.find('.iwj-respon-msg').html(result.message).slideDown(300);
                        if (result.loggedin == true) {
                            window.location.href = result.redirect_url;
                        } else if (typeof grecaptcha !== 'undefined'  && iwj.use_recaptcha.login === 1) {
                            grecaptcha.reset();
                        }
                    }

                    iwj_button_loader(button, 'remove');
                }
            });
        });

        $('.iwj-register-form').submit(function (e) {
            e.preventDefault();
            var self = $(this);
            var button = self.find('.iwj-register-btn');
            var data = self.serialize();
            data = 'action=iwj_register&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    self.find('.iwj-respon-msg').slideUp(300, function () {
                        $(this).html('');
                    });
                },
                success: function (result) {
                    if (result) {
                        self.find('.iwj-respon-msg').html(result.message).slideDown(300);
                        if (result.success) {
                            if (result.redirect_url) {
                                window.location.href = result.redirect_url;
                            }
                        }
                    }

                    iwj_button_loader(button, 'remove');
                }
            });
        });

        $('.iwj-resend-verification').click(function (e) {
            e.preventDefault();
            var self = $(this);
            var original_text = self.html();
            var data = 'action=iwj_resend_verification&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    self.html(self.data('sending-text'));
                },
                success: function (result) {
                    if (result.success) {
                        self.html(original_text);
                        $('.resend-email-message').html(result.message).fadeIn();
                    } else {

                    }
                }
            });
        });

        $('.iwj-change-email-form').submit(function (e) {
            e.preventDefault();
            var self = $(this);
            var button = self.find('.iwj-change-email-btn');
            var data = self.serialize();
            data = 'action=iwj_change_email&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    self.find('.iwj-respon-msg').slideUp(300, function () {
                        $(this).html('');
                    });
                },
                success: function (result) {
                    self.find('.iwj-respon-msg').html(result.message).slideDown(300);
                    iwj_button_loader(button, 'remove');
                }
            });
        });

        $('.iwj-lostpass-form').submit(function (e) {
            e.preventDefault();
            var self = $(this);
            var button = self.find('.iwj-lostpass-btn');
            var data = self.serialize();
            data = 'action=iwj_lostpass&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    self.find('.iwj-respon-msg').slideUp(300, function () {
                        $(this).html('');
                    });
                },
                success: function (result) {
                    if (result) {
                        self.find('.iwj-respon-msg').html(result.message).slideDown(300);
                    }

                    iwj_button_loader(button, 'remove');
                }
            });
        });

        $('.iwj-resetpass-form').submit(function (e) {
            e.preventDefault();
            var self = $(this);
            var button = self.find('.iwj-resetpass-btn');
            var data = self.serialize();
            data = 'action=iwj_resetpass&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    self.find('.iwj-respon-msg').slideUp(300, function () {
                        $(this).html('');
                    });
                },
                success: function (result) {
                    if (result) {
                        self.find('.iwj-respon-msg').html(result.message).slideDown(300);
                    }
                    if (result.success) {
                        setTimeout(function () {
                            window.location.href = result.redirect_url;
                        }, 1500);
                    } else {
                        iwj_button_loader(button, 'remove');
                    }
                }
            });
        });

        if ($('.iwj-magic-line').length) {
            var rtl_support = $('body').hasClass('rtl');
            $('.iwj-magic-line').each(function () {
                var self = $(this);
                self.append("<span class='magic-line'></span>");
                var magic_line = self.find('.magic-line');
                var magic_line_2 = $(".iwj-magic-line.layout2").find('.magic-line');
                var magic_line_4 = $(".iwj-magic-line.layout4").find('.magic-line');
                var current_item = self.find('.active');
                if (current_item.length) {
                    var c_top = current_item.position().top;
                    var c_left = current_item.position().left;
                    self.data('left', c_left);
                    self.data('top', c_top);
                    var width = current_item.outerWidth();
                    var height = current_item.outerHeight();
                    magic_line
                            .width(width)
                            .height(height)
                            .css("left", c_left)
                            .css("top", c_top);

                    current_item.data('top_pos', c_top);
                    current_item.data('left_pos', c_left);
                    current_item.data('width', width);
                    current_item.data('height', height);

                    $(window).on('resize', function () {
                        self.find('.iwj-toggle').data('top_pos', '').data('left_pos', '').data('width', '').data('height', '');
                        current_item = self.find('.active');
                        var c_top = current_item.position().top;
                        var c_left = current_item.position().left;
                        /*self.data('left', c_left);
                         self.data('top', c_top);*/
                        var ts_x = c_left - self.data('left');
                        if (rtl_support) {
                            ts_x = Math.abs(ts_x) * -1;
                        }

                        var ts_y = c_top - self.data('top');
                        var new_width = current_item.outerWidth();
                        var new_height = current_item.outerHeight();
                        magic_line.css({
                            'transform': 'translate(' + ts_x + 'px, ' + ts_y + 'px)',
                            'width': new_width + 'px',
                            'height': new_height + 'px'
                        });
                        magic_line_2.css({
                            'transform': 'translate(' + ts_x + 'px, 0)'
                        });
                        magic_line_4.css({
                            'transform': 'translate(' + ts_x + 'px, 0)'
                        });

                        current_item.data('top_pos', c_top);
                        current_item.data('left_pos', c_left);
                        current_item.data('width', new_width);
                        current_item.data('height', new_height);
                    });
                }

                self.on('click', '.iwj-toggle', function (e) {
                    e.preventDefault();
                    var current_item = self.find('.iwj-toggle.active');
                    current_item.removeClass('active');
                    var el = $(this);
                    var left_pos = el.data('left_pos');
                    var top_pos = el.data('top_pos');
                    var new_width = el.data('width');
                    var new_height = el.data('height');
                    if (!left_pos) {
                        top_pos = el.position().top;
                        left_pos = el.position().left;
                        new_width = el.outerWidth();
                        new_height = el.outerHeight();
                        el.data('left_pos', left_pos);
                        el.data('top_pos', top_pos);
                        el.data('width', new_width);
                        el.data('height', new_height);
                    }

                    el.addClass('active');
                    var ts_x = left_pos - self.data('left');
                    if (rtl_support) {
                        ts_x = Math.abs(ts_x) * -1;
                    }
                    var ts_y = top_pos - self.data('top');

                    magic_line.stop().css({
                        'transform': 'translate(' + ts_x + 'px, ' + ts_y + 'px)',
                        'width': new_width + 'px',
                        'height': new_height + 'px'
                    });
                    magic_line_2.css({
                        'transform': 'translate(' + ts_x + 'px, 0)'
                    });
                    magic_line_4.css({
                        'transform': 'translate(' + ts_x + 'px, 0)'
                    });
                });
            });
        }

        $('#iwj-login-popup').on('shown.bs.modal', function (e) {
            var fallback_action = $(e.relatedTarget).data('fallback');
            if (fallback_action) {
                $('#iwj-login-popup').find('input[name="fallback_action"]').val(fallback_action);
            } else {
                $('#iwj-login-popup').find('input[name="fallback_action"]').val('');
            }
        });

        $('#iwj-register-popup').on('shown.bs.modal', function (e) {
            var magic_line = $('#iwj-register-popup').find('.iwj-magic-line');
            var magic_line_child = magic_line.find('.magic-line');
            var current_item = magic_line.find('.active');
            if (current_item.length) {
                var c_top = current_item.position().top;
                var c_left = current_item.position().left;
                magic_line.data('left', c_left);
                magic_line.data('top', c_top);
                var width = current_item.outerWidth();
                var height = current_item.outerHeight();
                magic_line_child
                        .addClass('faster')
                        .width(width)
                        .height(height)
                        .css("left", c_left)
                        .css("top", c_top);

                current_item.data('top_pos', c_top);
                current_item.data('left_pos', c_left);
                current_item.data('width', width);
                current_item.data('height', height);

                setTimeout(function () {
                    magic_line_child.removeClass('faster');
                }, 200)
            }
        });

        $('.iwj-candidate-toggle').click(function () {
            var form = $(this).closest('form');
            form.find('.company-field').slideUp(200);
            form.find('input[name="role"]').val('candidate');
            form.find('input[name="company"]').prop('required', false);
        });

        $('.iwj-employer-toggle').click(function () {
            var form = $(this).closest('form');
            form.find('.company-field').slideDown(200);
            form.find('input[name="role"]').val('employer');
            form.find('input[name="company"]').prop('required', true);
        });

        $('.iwj-role').change(function () {
            var value = $(this).val();
            var form = $(this).closest('form');
            if (form) {
                if (value == 'candidate') {
                    form.find('.company-field').slideUp(200);
                    form.find('input[name="company"]').prop('required', false);
                } else {
                    form.find('.company-field').slideDown(200);
                    form.find('input[name="company"]').prop('required', true);
                }
            }
        });

        $('.iwj-candidate-btn').click(function () {
            var form = $(this).closest('form');
            form.data('button', $(this));
        });
        $('.iwj-candidate-form').submit(function (e) {
            e.preventDefault();
            if (typeof tinyMCE != 'undefined') {
                tinyMCE.triggerSave();
            }
            var self = $(this);
            var button = self.data('button');
            var respon = self.find('.iwj-respon-msg');
            var data = self.serialize();
            data = 'action=iwj_update_profile&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    respon.slideUp(300, function () {
                        $(this).html('');
                    });
                },
                success: function (result) {
                    iwj_button_loader(button, 'remove');
                    respon.html(result.message).slideDown(300);
                }
            });
        });

        $('.iwj-employer-btn').click(function () {
            var form = $(this).closest('form');
            form.data('button', $(this));
        });
        $('.iwj-employer-form').submit(function (e) {
            e.preventDefault();
            if (typeof tinyMCE != 'undefined') {
                tinyMCE.triggerSave();
            }
            var self = $(this);
            var button = self.data('button');
            var respon = self.find('.iwj-respon-msg');
            var data = self.serialize();
            data = 'action=iwj_update_profile&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    respon.slideUp(300, function () {
                        $(this).html('');
                    });
                },
                success: function (result) {
                    iwj_button_loader(button, 'remove');
                    respon.html(result.message).slideDown(300);
                }
            });
        });

        $('.iwj-user-btn').click(function () {
            var form = $(this).closest('form');
            form.data('button', $(this));
        });
        $('.iwj-user-form').submit(function (e) {
            e.preventDefault();
            var self = $(this);
            var button = self.data('button');
            var respon = self.find('.iwj-respon-msg');
            var data = self.serialize();
            data = 'action=iwj_update_profile&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    respon.slideUp(300, function () {
                        $(this).html('');
                    });
                },
                success: function (result) {
                    iwj_button_loader(button, 'remove');
                    respon.html(result.message).slideDown(300);
                }
            });
        });

        $('.iwj-change-password-form').submit(function (e) {
            e.preventDefault();
            var self = $(this);
            var button = self.find('.iwj-change-password-btn');
            var data = self.serialize();
            data = 'action=iwj_change_password&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    self.find('.iwj-respon-msg').slideUp(300, function () {
                        $(this).html('');
                    });
                },
                success: function (result) {
                    if (result.success == true) {
                        self.get(0).reset();
                    }
                    self.find('.iwj-respon-msg').html(result.message).slideDown(300);
                    iwj_button_loader(button, 'remove');
                }
            });
        });

        $('.iwj-delete-account-btn').click(function (e) {
            e.preventDefault();
            var self = $(this);
            if (confirm(self.data('confirm-delete')) == true) {
                var data = 'action=iwj_delete_account&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang;
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        iwj_button_loader(self, 'add');
                    },
                    success: function (result) {
                        if (result.success == true) {
                            window.location.href = result.redirect;
                        } else {

                        }
                        iwj_button_loader(self, 'remove');
                    }
                });
            }
        });

        $('.iwj-job-submit-form button[type="submit"]').click(function () {
            $('.iwj-job-submit-form input[name="submit_action"]').val($(this).val());
        });

        $('.iwj-job-submit-form').submit(function (e) {
            e.preventDefault();
            if (typeof tinyMCE != 'undefined') {
                tinyMCE.triggerSave();
            }
            var self = $(this);
            var button = self.find('.iwj-submit-job-btn');
            var data = self.serialize();
            data = 'action=iwj_submit_job&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    self.find('.iwj-respon-msg').slideUp(300, function () {
                        $(this).html('');
                    });
                },
                success: function (result) {
                    if (result.id) {
                        self.find('input[name="id"]').val(result.id);
                    }
                    if (result.message) {
                        self.find('.iwj-respon-msg').html(result.message).slideDown(300);
                        iwj_button_loader(button, 'remove');
                    }
                    if (result.redirect) {
                        window.location.href = result.redirect;
                    }
                }
            });
        });

        $('.iwj-job-renew-form').submit(function (e) {
            e.preventDefault();
            var self = $(this);
            if (typeof tinyMCE != 'undefined') {
                tinyMCE.triggerSave();
            }
            var button = self.find('.iwj-renew-job-btn');
            var data = self.serialize();
            data = 'action=iwj_renew_job&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    self.find('.iwj-respon-msg').slideUp(300, function () {
                        $(this).html('');
                    });
                },
                success: function (result) {
                    if (result.message) {
                        self.find('.iwj-respon-msg').html(result.message).slideDown(300);
                    }
                    if (result.sucess) {
                        window.location.href = result.redirect_url;
                    } else {
                        iwj_button_loader(button, 'remove');
                    }
                }
            });
        });

        $('.iwj-edit-job-btn').click(function () {
            var form = $(this).closest('form');
            form.data('button', $(this));
        });

        $('.iwj-job-edit-form').submit(function (e) {
            e.preventDefault();
            var self = $(this);
            if (typeof tinyMCE != 'undefined') {
                tinyMCE.triggerSave();
            }
            var button = self.data('button');
            var respon = self.find('.iwj-respon-msg');
            var data = self.serialize();
            data = 'action=iwj_edit_job&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    respon.slideUp(300, function () {
                        $(this).html('');
                    });
                },
                success: function (result) {
                    if (result.message) {
                        respon.html(result.message).slideDown(300);
                    }
                    if (result.redirect) {
                        if (result.delay) {
                            setTimeout(function () {
                                window.location.href = result.redirect;
                            }, result.delay)
                        } else {
                            window.location.href = result.redirect;
                        }
                    } else {
                        iwj_button_loader(button, 'remove');
                    }
                }
            });
        });
        if (!iwj.woocommerce_checkout) {

            $('form input[name="user_package"],form input[name="package"]').change(function () {
                if ($(this).attr('name') == 'user_package') {
                    $('form input[name="package"]').prop('checked', false);
                } else if ($(this).attr('name') == 'package') {
                    $('form input[name="user_package"]').prop('checked', false);
                }
                var package_id = $('form input[name="package"]:checked').val();
                var user_package_id = $('form input[name="user_package"]:checked').val();

                if (package_id || user_package_id) {
                    if (package_id) {
                        var data = 'action=iwj_get_order_price&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&package_id=' + package_id;
                    } else {
                        var data = 'action=iwj_get_order_price&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&user_package_id=' + user_package_id;
                    }

                    $.ajax({
                        url: iwj.ajax_url,
                        type: 'POST',
                        data: data,
                        dataType: 'json',
                        beforeSend: function () {
                            $('.iwj-payment-btn').prop('disabled', true);
                        },
                        success: function (result) {
                            if (result) {
                                if (parseFloat(result.total_price) > 0) {
                                    $('.iwj-payments').show();
                                    if ($('form input[name="payment_method"]:checked').length > 0) {
                                        $('.iwj-payment-btn').prop('disabled', false);
                                    }
                                } else {
                                    $('.iwj-payments').hide();
                                    $('.iwj-payment-btn').prop('disabled', false);
                                }

                                var form = $('.iwj-payment-btn').closest('form');
                                form.find('input[name="price"]').val(result.total_price);

                                $('.iwj-order-price').html(result.html);
                                $('.iwj-order-payment').slideDown(300);
                            } else {
                                $('.iwj-order-payment').slideUp(300);
                            }
                        }
                    });
                }
            });
        }

        if (!iwj.woocommerce_checkout) {
            $('form input[name="payment_method"]').change(function () {
                $('.iwj-payment-btn').prop('disabled', false);
                if (typeof window.iwj_payment_method_select_change === 'function') {
                    window.iwj_payment_method_select_change($(this));
                }
            });
        }

        $('.iwj-payment-btn').click(function () {
            var self = $(this);
            if (self.is(':disabled')) {
                return false;
            }
            iwj_button_loader(self, 'add');
            var form = self.closest('form');
            var price = form.find('input[name="price"]').val();
            var currency = form.find('input[name="currency"]').val();

            if (!iwj.woocommerce_checkout && parseFloat(price) > 0) {
                var payment_method = form.find('input[name="payment_method"]:checked').val();
                var method_function = window['iwj_payment_' + payment_method + '_callback'];
                if (typeof method_function === "function") {
                    method_function(form, self, price, currency);
                } else {
                    form.submit();
                }
            } else {
                form.submit();
            }

        });


        var owl = $(".iwj-widget-candidates.owl-carousel, .iwj-widget-employers .owl-carousel");
        if (owl.length) {
            owl.owlCarousel({
                direction: $('body').hasClass('rtl') ? 'rtl' : 'ltr',
                autoHeight: true,
                items: 1,
                singleItem: true,
                pagination: false,
                navigation: true,
                navigationText: ['<i class="ion-arrow-left-c"></i>', '<i class="ion-arrow-right-c"></i>']
            });
        }

        $('.iwj-follow').click(function (e) {
            e.preventDefault();
            var self = $(this);
            var id = $(this).data('id');
            if (self.hasClass('followed')) {
                var data = 'action=iwj_unfollow&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&id=' + id;
            } else {
                var data = 'action=iwj_follow&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&id=' + id;
            }
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(self, 'add');
                },
                success: function (result) {
                    if (result) {
                        iwj_button_loader(self, 'remove');
                        if (result.success == true) {
                            if (self.hasClass('followed')) {
                                self.removeClass('followed');
                            } else {
                                self.addClass('followed');
                            }

                            self.html(result.message);
                        }
                    }
                }
            });
        });

        $('.iwj-delete-job').click(function (e) {
            e.preventDefault();
            $('#iwj-confirm-delete-job').find('.iwj-respon-msg').html('');
            $('#iwj-confirm-delete-job .modal-body p').html($(this).data('message'));
            $('#iwj-confirm-delete-job .iwj-agree-delete-job').data('id', $(this).data('id'));
            $('#iwj-confirm-delete-job').modal('show');
        });

        $('.iwj-agree-delete-job').click(function (e) {
            e.preventDefault();
            var self = $(this);
            var id = self.data('id');

            if (id) {
                var data = 'action=iwj_delete_job&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&id=' + id;
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        iwj_button_loader(self, 'add');
                        $('#iwj-confirm-delete-job').find('.iwj-respon-msg').slideUp(300).html('');
                    },
                    success: function (result) {
                        if (result) {
                            if (result.message) {
                                $('#iwj-confirm-delete-job').find('.iwj-respon-msg').html(result.message).slideDown(300);
                            }
                            if (result.success) {
                                $('#job-' + id).remove();
                            }
                            setTimeout(function () {
                                iwj_button_loader(self, 'remove');
                                $('#iwj-confirm-delete-job').modal('hide');
                            }, 2000);
                        }
                    }
                });
            }
        });

        $('.iwj-unfollow').click(function (e) {
            e.preventDefault();
            $('#iwj-confirm-unfollow').find('.iwj-respon-msg').html('');
            $('#iwj-confirm-unfollow .modal-body p').html($(this).data('message'));
            $('#iwj-confirm-unfollow .iwj-agree-unfollow').data('id', $(this).data('id'));
            $('#iwj-confirm-unfollow').modal('show');
        });

        $('.iwj-agree-unfollow').click(function (e) {
            e.preventDefault();
            var self = $(this);
            var id = self.data('id');
            if (id) {
                var data = 'action=iwj_unfollow&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&id=' + id;
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        iwj_button_loader(self, 'add');
                        $('#iwj-confirm-unfollow').find('.iwj-respon-msg').slideUp(300).html('');
                    },
                    success: function (result) {
                        if (result) {
                            if (result.message) {
                                $('#iwj-confirm-unfollow').find('.iwj-respon-msg').html(result.message).slideDown(300);
                            }
                            if (result.success) {
                                $('#follow-' + id).remove();
                            }
                            setTimeout(function () {
                                iwj_button_loader(self, 'remove');
                                $('#iwj-confirm-unfollow').modal('hide');
                            }, 2000);
                        }
                    }
                });
            }
        });

        $(document).on('click', '.iwj-save-job', function (e) {
            e.preventDefault();
            var self = $(this);
            var id = $(this).data('id');
            var in_list = $(this).data('in-list');
            var ori_class = '';
            if (self.hasClass('saved')) {
                var data = 'action=iwj_undo_save_job&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&id=' + id;
            } else {
                var data = 'action=iwj_save_job&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&id=' + id;
            }
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    if (in_list) {
                        ori_class = self.find('i').attr('class');
                        self.find('i').attr('class', 'fa fa-spinner fa-spin');
                    } else {
                        iwj_button_loader(self, 'add');
                    }
                },
                success: function (result) {
                    if (result) {
                        iwj_button_loader(self, 'remove');
                        if (result.success === true) {
                            if (self.hasClass('saved')) {
                                self.removeClass('saved');
                            } else {
                                self.addClass('saved');
                            }

                            if (!in_list) {
                                self.attr('data-original-title', result.message);
                                self.attr('title', result.message);
                            }
                        }
                        if (in_list) {
                            self.find('i').attr('class', ori_class);
                        }
                    }
                }
            });
        });

        $('.iwj-undo-save-job').click(function (e) {
            e.preventDefault();
            $('#iwj-confirm-undo-save-job').find('.iwj-respon-msg').html('');
            $('#iwj-confirm-undo-save-job .modal-body p').html($(this).data('message'));
            $('#iwj-confirm-undo-save-job .iwj-agree-undo-save-job').data('id', $(this).data('id'));
            $('#iwj-confirm-undo-save-job').modal('show');
        });

        $('.iwj-agree-undo-save-job').click(function (e) {
            e.preventDefault();
            var self = $(this);
            var id = self.data('id');
            if (id) {
                var data = 'action=iwj_undo_save_job&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&id=' + id;
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        iwj_button_loader(self, 'add');
                        $('#iwj-confirm-undo-save-job').find('.iwj-respon-msg').slideUp(300).html('');
                    },
                    success: function (result) {
                        if (result) {
                            if (result.message) {
                                $('#iwj-confirm-undo-save-job').find('.iwj-respon-msg').html(result.message).slideDown(300);
                            }
                            if (result.success) {
                                $('#save-job-' + id).remove();
                            }
                            setTimeout(function () {
                                iwj_button_loader(self, 'remove');
                                $('#iwj-confirm-undo-save-job').modal('hide');
                            }, 2000);
                        }
                    }
                });
            }
        });

        $('.iwj-reply-review').click(function (e) {
            e.preventDefault();
            $('#iwj-confirm-reply-review').find('.iwj-respon-msg').html();
            $('#iwj-confirm-reply-review .iwj-agree-reply-review').data('id', $(this).data('id'));
            $('#iwj-confirm-reply-review .iwj-agree-reply-review').data('item_id', $(this).data('item_id'));
            $('#iwj-confirm-reply-review .iwj-agree-reply-review').data('type', 'reply');
            $('#iwj-confirm-reply-review').modal('show');
        });

        $('.iwj-edit-reply-review').click(function (e) {
            e.preventDefault();
            $('#iwj-confirm-reply-review').find('.iwj-respon-msg').html();
            $('#iwj-confirm-reply-review .iwj-agree-reply-review').data('id', $(this).data('id'));
            $('#iwj-confirm-reply-review #iwj_employer_review_content').val($(this).data('message'));
            $('#iwj-confirm-reply-review .iwj-agree-reply-review').data('type', 'edit_reply');
            $('#iwj-confirm-reply-review').modal('show');
        });

        $('.iwj-agree-reply-review').click(function (e) {
            e.preventDefault();
            var self = $(this),
                    id = self.data('id'),
                    type = self.data('type'),
                    content = self.closest('#iwj-confirm-reply-review').find('#iwj_employer_review_content').val();
            if (id) {
                if (type === 'edit_reply') {
                    data = 'action=iwj_edit_reply_review&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&iwj_reply_id=' + id + '&iwj_reply_content=' + content;
                    $.ajax({
                        url: iwj.ajax_url,
                        type: 'POST',
                        data: data,
                        dataType: 'json',
                        beforeSend: function () {
                            iwj_button_loader(self, 'add');
                            $('#iwj-confirm-reply-review').find('.iwj-respon-msg').slideUp(300).html('');
                        },
                        success: function (result) {
                            if (result) {
                                if (result.message) {
                                    $('#iwj-confirm-reply-review').find('.iwj-respon-msg').html(result.message).slideDown(300);
                                }
                                if (result.success) {
                                    self.closest('#iwj-confirm-reply-review').find('#iwj_employer_review_content').val('');
                                    setTimeout(function () {
                                        location.reload();
                                    }, 1900);
                                }
                                setTimeout(function () {
                                    iwj_button_loader(self, 'remove');
                                    $('#iwj-confirm-reply-review').modal('hide');
                                }, 1600);
                            }
                        }
                    });
                } else {
                    var item_id = self.data('item_id'),
                            data = 'action=iwj_reply_review&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&iwj_reply_review_id=' + id + '&iwj_reply_review=' + content + '&item_id=' + item_id;
                    $.ajax({
                        url: iwj.ajax_url,
                        type: 'POST',
                        data: data,
                        dataType: 'json',
                        beforeSend: function () {
                            iwj_button_loader(self, 'add');
                            $('#iwj-confirm-reply-review').find('.iwj-respon-msg').slideUp(300).html('');
                        },
                        success: function (result) {
                            if (result) {
                                if (result.message) {
                                    $('#iwj-confirm-reply-review').find('.iwj-respon-msg').html(result.message).slideDown(300);
                                }
                                if (result.success) {
                                    $('#review-' + id).find('td.iwj-status > span').attr('class', 'approved');
                                    $('#review-' + id).find('td.iwj-status > span').data('original-title', 'Replied');
                                    self.closest('#iwj-confirm-reply-review').find('#iwj_employer_review_content').val('');
                                    setTimeout(function () {
                                        location.reload();
                                    }, 1900);
                                }
                                setTimeout(function () {
                                    iwj_button_loader(self, 'remove');
                                    $('#iwj-confirm-reply-review').modal('hide');
                                }, 1600);
                            }
                        }
                    });
                }
            }
        });

        $('.iwj-delete-view-resume').click(function (e) {
            e.preventDefault();
            $('#iwj-confirm-delete-view-resume').find('.iwj-respon-msg').html('');
            $('#iwj-confirm-delete-view-resume .modal-body p').html($(this).data('message'));
            $('#iwj-confirm-delete-view-resume .iwj-agree-delete-view-resume').data('id', $(this).data('id'));
            $('#iwj-confirm-delete-view-resume').modal('show');
        });

        $('.iwj-agree-delete-view-resume').click(function (e) {
            e.preventDefault();
            var self = $(this);
            var id = self.data('id');
            if (id) {
                var data = 'action=iwj_delete_view_resum&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&id=' + id;
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        iwj_button_loader(self, 'add');
                        $('#iwj-confirm-delete-view-resume').find('.iwj-respon-msg').slideUp(300).html('');
                    },
                    success: function (result) {
                        if (result) {
                            if (result.message) {
                                $('#iwj-confirm-delete-view-resume').find('.iwj-respon-msg').html(result.message).slideDown(300);
                            }
                            if (result.success) {
                                $('#view-resume-' + id).remove();
                            }
                            setTimeout(function () {
                                iwj_button_loader(self, 'remove');
                                $('#iwj-confirm-delete-view-resume').modal('hide');
                            }, 2000);
                        }
                    }
                });
            }
        });

        $('.iwj-delete-save-resume').click(function (e) {
            e.preventDefault();
            $('#iwj-confirm-delete-save-resume').find('.iwj-respon-msg').html('');
            $('#iwj-confirm-delete-save-resume .modal-body p').html($(this).data('message'));
            $('#iwj-confirm-delete-save-resume .iwj-agree-delete-save-resume').data('id', $(this).data('id'));
            $('#iwj-confirm-delete-save-resume').modal('show');
        });

        $('.iwj-agree-delete-save-resume').click(function (e) {
            e.preventDefault();
            var self = $(this);
            var id = self.data('id');
            if (id) {
                var data = 'action=iwj_delete_save_resum&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&id=' + id;
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        iwj_button_loader(self, 'add');
                        $('#iwj-confirm-delete-save-resume').find('.iwj-respon-msg').slideUp(300).html('');
                    },
                    success: function (result) {
                        if (result) {
                            if (result.message) {
                                $('#iwj-confirm-delete-save-resume').find('.iwj-respon-msg').html(result.message).slideDown(300);
                            }
                            if (result.success) {
                                $('#save-resume-' + id).remove();
                            }
                            setTimeout(function () {
                                iwj_button_loader(self, 'remove');
                                $('#iwj-confirm-delete-save-resume').modal('hide');
                            }, 2000);
                        }
                    }
                });
            }
        });

// GDPR required form apply job
        $('a[href="#apply_job_terms_services"]').click(function (e) {
            e.preventDefault();
            $(this).parent().next('[name="terms_and_services_desc"]').show();
        });

        $('input[name="iwj_apply_terms_and_services"]').on('change', function () {
            var btn_apply = $(this).closest('.iwj-application-form').find('.iwj-application-btn');
            if (this.checked) {
                btn_apply.prop("disabled", false);
            } else {
                btn_apply.prop("disabled", true);
            }
        });

// GDPR required form contact candidate
        $('a[href="#candidate_cf_terms_services"]').click(function (e) {
            e.preventDefault();
            $(this).parent().next('[name="terms_and_services_desc"]').show();
        });

        $('input[name="iwj_candidate_cf_terms_and_services"]').on('change', function () {
            var btn_send = $(this).closest('.iwj-contact-form').find('.iwj-contact-btn');
            if (this.checked) {
                btn_send.prop("disabled", false);
            } else {
                btn_send.prop("disabled", true);
            }
        });

// GDPR required form contact employer
        $('a[href="#employer_cf_terms_services"]').click(function (e) {
            e.preventDefault();
            $(this).parent().next('[name="terms_and_services_desc"]').show();
        });

        $('input[name="iwj_employer_cf_terms_and_services"]').on('change', function () {
            var btn_send = $(this).closest('.iwj-contact-form').find('.iwj-contact-btn');
            if (this.checked) {
                btn_send.prop("disabled", false);
            } else {
                btn_send.prop("disabled", true);
            }
        });

        $('.iwj-alert-submit-form').submit(function (e) {
            e.preventDefault();
            var form = $(this);
            var button = form.find('.iwj-submit-alert-btn');
            var is_popup = form.hasClass('iwj-alert-submit-form-popup');
            var data = form.serialize();
            data = 'action=iwj_submit_alert&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data + '&is_popup=' + (is_popup ? 1 : 0);
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    form.find('.iwj-respon-msg').slideUp(300).html('');
                },
                success: function (result) {
                    if (result) {

                        if (is_popup) {
                            if (result.success == true) {
                                form.html(result.message);
                            } else {
                                form.find('.iwj-respon-msg').html(result.message).slideDown(300);
                                iwj_button_loader(button, 'remove');
                            }
                        } else {
                            if (result.message) {
                                form.find('.iwj-respon-msg').html(result.message).slideDown(300);
                            }
                            if (result.success == true) {
                                setTimeout(function () {
                                    window.location = result.redirect_url;
                                }, 2000);
                            } else {
                                iwj_button_loader(button, 'remove');
                            }
                        }
                    }
                }
            });

        });

        $('.iwj-alert-edit-form').submit(function (e) {
            e.preventDefault();
            var form = $(this);
            var button = form.find('.iwj-edit-alert-btn');
            var data = form.serialize();
            data = 'action=iwj_edit_alert&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    form.find('.iwj-respon-msg').slideUp(300).html('');
                },
                success: function (result) {
                    if (result) {
                        if (result.message) {
                            form.find('.iwj-respon-msg').html(result.message).slideDown(300);
                        }
                        if (result.success == true) {
                            setTimeout(function () {
                                window.location = result.redirect_url;
                            }, 2000);
                        } else {
                            iwj_button_loader(button, 'remove');
                        }
                    }
                }
            });

        });

        $('.iwj-delete-alert').click(function (e) {
            e.preventDefault();
            $('#iwj-confirm-delete-alert').find('.iwj-respon-msg').html('');
            $('#iwj-confirm-delete-alert .modal-body p').html($(this).data('message'));
            $('#iwj-confirm-delete-alert .iwj-agree-delete-alert').data('id', $(this).data('id'));
            $('#iwj-confirm-delete-alert').modal('show');
        });

        $('.iwj-agree-delete-alert').click(function (e) {
            e.preventDefault();
            var self = $(this);
            var id = self.data('id');
            if (id) {
                var data = 'action=iwj_delete_alert&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&alert_id=' + id;
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        iwj_button_loader(self, 'add');
                        $('#iwj-confirm-delete-alert').find('.iwj-respon-msg').slideUp(300).html('');
                    },
                    success: function (result) {
                        if (result) {
                            if (result.message) {
                                $('#iwj-confirm-delete-alert').find('.iwj-respon-msg').html(result.message).slideDown(300);
                            }
                            if (result.success) {
                                $('#alert-' + id).remove();
                            }
                            setTimeout(function () {
                                iwj_button_loader(self, 'remove');
                                $('#iwj-confirm-delete-alert').modal('hide');
                            }, 2000);
                        }
                    }
                });
            }
        });

        $('.iwj-c-delete-review').click(function (e) {
            e.preventDefault();
            $('#iwj-confirm-delete-review').find('.iwj-respon-msg').html('');
            $('#iwj-confirm-delete-review .modal-body p').html($(this).data('message'));
            $('#iwj-confirm-delete-review .iwj-agree-delete-review').data('id', $(this).data('id'));
            $('#iwj-confirm-delete-review').modal('show');
        });

        $('.iwj-agree-delete-review').click(function (e) {
            e.preventDefault();
            var self = $(this);
            var id = self.data('id');
            if (id) {
                var data = 'action=iwj_delete_review&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&review_id=' + id;
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        iwj_button_loader(self, 'add');
                        $('#iwj-confirm-delete-review').find('.iwj-respon-msg').slideUp(300).html('');
                    },
                    success: function (result) {
                        if (result) {
                            if (result.message) {
                                $('#iwj-confirm-delete-review').find('.iwj-respon-msg').html(result.message).slideDown(300);
                            }
                            if (result.success) {
                                $('#review-' + id).remove();
                            }
                            setTimeout(function () {
                                iwj_button_loader(self, 'remove');
                                $('#iwj-confirm-delete-review').modal('hide');
                            }, 2000);
                        }
                    }
                });
            }
        });

        $('.iwj-c-edit-review').click(function (e) {
            e.preventDefault();
            $('#iwj-confirm-edit-review').find('.iwj-respon-msg').html('');
            $('#iwj-confirm-edit-review .iwj-agree-edit-review').data('id', $(this).data('id'));
            $('#iwj-confirm-edit-review').find('input[name="user_id_rate"]').val($(this).data('user_id'));
            $('#iwj-confirm-edit-review').find('input[name="rate_item_id"]').val($(this).data('item_id'));
            $('#iwj-confirm-edit-review').find('input[name="iwj_review_title"]').val($(this).data('title'));
            $('#iwj-confirm-edit-review').find('textarea[name="iwj_review_content"]').val($(this).data('content'));
            var type_criteria = $('.re-post-form-submit').data('number_criteria'),
                    rate_star = $(this).data('rate_star');
            if (type_criteria === 'group_vote') {
                $.map($(this).data('vote_for'), function (value, index) {
                    $('input[data-criteria_vote="' + index + '"]').val(value);
                    $('input[data-criteria_vote="' + index + '"]').prev('.filled-stars').css('width', value * 20 + '%');
                });
            } else {
                $('#iwj-confirm-edit-review').find('input[name="iwj_simple_rate"]').val(rate_star);
            }
            for (var t = 1; t <= 5; t++) {
                if (rate_star % 1 === 0) {
                    if (t <= rate_star) {
                        jQuery('.iwj-votes-icon > i:nth-child(' + t + ')').attr('class', 'ion-android-star');
                    } else {
                        jQuery('.iwj-votes-icon > i:nth-child(' + t + ')').attr('class', 'ion-android-star-outline');
                    }
                } else {
                    if (t < Math.ceil(rate_star)) {
                        jQuery('.iwj-votes-icon > i:nth-child(' + t + ')').attr('class', 'ion-android-star');
                    } else if (t === Math.ceil(rate_star)) {
                        jQuery('.iwj-votes-icon > i:nth-child(' + t + ')').attr('class', 'ion-android-star-half');
                    } else {
                        jQuery('.iwj-votes-icon > i:nth-child(' + t + ')').attr('class', 'ion-android-star-outline');
                    }
                }
            }
            $('#iwj-confirm-edit-review').modal('show', {backdrop: 'static', keyboard: false});
        });

        $('.iwj_candidate_edit_review').submit(function (e) {
            e.preventDefault();
            var form = $(this),
                    button = form.find('.iwj-agree-edit-review'),
                    data = form.serialize(),
                    id = button.data('id');
            if (id) {
                data = 'action=iwj_update_review&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data + '&review_id=' + id;
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        iwj_button_loader(button, 'add');
                        $('#iwj-confirm-edit-review').find('.iwj-respon-msg').slideUp(300).html('');
                    },
                    success: function (result) {
                        if (result) {
                            if (result.message) {
                                $('#iwj-confirm-edit-review').find('.iwj-respon-msg').html(result.message).slideDown(300);
                            }
                            setTimeout(function () {
                                iwj_button_loader(button, 'remove');
                            }, 1500);
                            if (result.success) {
                                setTimeout(function () {
                                    form.each(function () {
                                        this.reset();
                                    });
                                    $('#iwj-confirm-edit-review').modal('hide');
                                }, 2000);
                            }
                            if (result.success) {
                                var ahref = window.location.href,
                                        alteredURL = removeParam("review_id", ahref);
                                setTimeout(function () {
                                    window.location.href = alteredURL;
                                }, 2500);
                            }

                        }
                    }
                });
            }
        });

        $('.iwj-delete-reply').click(function (e) {
            e.preventDefault();
            $('#iwj-confirm-delete-reply').find('.iwj-respon-msg').html('');
            $('#iwj-confirm-delete-reply .modal-body p').html($(this).data('message'));
            $('#iwj-confirm-delete-reply .iwj-agree-delete-reply').data('id', $(this).data('id'));
            $('#iwj-confirm-delete-reply .iwj-agree-delete-reply').data('review_id', $(this).data('review_id'));
            $('#iwj-confirm-delete-reply').modal('show');
        });

        $('.iwj-agree-delete-reply').click(function (e) {
            e.preventDefault();
            var self = $(this);
            var id = self.data('id'),
                    review_id = self.data('review_id');
            if (id) {
                var data = 'action=iwj_delete_reply&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&reply_id=' + id;
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        iwj_button_loader(self, 'add');
                        $('#iwj-confirm-delete-reply').find('.iwj-respon-msg').slideUp(300).html('');
                    },
                    success: function (result) {
                        if (result) {
                            if (result.message) {
                                $('#iwj-confirm-delete-reply').find('.iwj-respon-msg').html(result.message).slideDown(300);
                            }
                            if (result.success) {
                                $('#review-' + review_id).find('span[data-toggle="tooltip"]').attr('class', 'pending');
                                setTimeout(function () {
                                    $('#iwj-confirm-delete-reply').modal('hide');
                                    location.reload();
                                }, 2000);
                            }
                            setTimeout(function () {
                                iwj_button_loader(self, 'remove');
                            }, 1500);
                        }
                    }
                });
            }
        });

        $('.iwj-contact-form').submit(function (e) {
            e.preventDefault();
            if (typeof tinyMCE != 'undefined') {
                tinyMCE.triggerSave();
            }
            var form = $(this);
            var button = form.find('.iwj-contact-btn');
            var data = form.serialize();
            data = 'action=iwj_contact&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    form.find('.iwj-respon-msg').slideUp(300).html('');
                },
                success: function (result) {
                    if (result) {
                        iwj_button_loader(button, 'remove');
                        if (result.message) {
                            form.find('.iwj-respon-msg').html(result.message).slideDown(300);
                        }
                        if (result.success == true) {
                            form.get(0).reset();
                        }
                    }
                }
            });
        });

        $('.iwj-view-resume-form').submit(function (e) {
            e.preventDefault();
            var form = $(this);
            var button = form.find('.iwj-view-resume-btn');
            var data = form.serialize();
            data = 'action=iwj_view_resum&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    form.find('.iwj-respon-msg').slideUp(300, function () {
                        $(this).html('');
                    });
                },
                success: function (result) {
                    if (result.success == true) {
                        location.reload();
                    } else if (result.message) {
                        form.find('.iwj-respon-msg').html(result.message).slideDown(300);
                        iwj_button_loader(button, 'remove');
                    }
                }
            });
        });

        $('.iwj-confirm-apply-job-form').submit(function (e) {
            e.preventDefault();
            var form = $(this);
            var button = form.find('.iwj-confirm-apply-job-btn');
            var data = form.serialize();
            data = 'action=iwj_confirm_apply_job&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    form.find('.iwj-respon-msg').slideUp(300, function () {
                        form.html('');
                    });
                },
                success: function (result) {
                    if (result.success == true) {
                        location.reload();
                    } else if (result.message) {
                        form.find('.iwj-respon-msg').html(result.message).slideDown(300);
                        iwj_button_loader(button, 'remove');
                    }
                }
            });
        });

        $('.iwj-save-resume').click(function (e) {
            e.preventDefault();
            var self = $(this);
            var id = $(this).data('id');
            if (self.hasClass('saved')) {
                var data = 'action=iwj_undo_save_resum&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&id=' + id;
            } else {
                var data = 'action=iwj_save_resum&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&id=' + id;
            }
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(self, 'add');
                    self.next('.iwj-respon-msg').fadeOut(function () {
                        $(this).html('');
                    });
                },
                success: function (result) {
                    if (result) {
                        iwj_button_loader(self, 'remove');
                        if (result.success == true) {
                            if (self.hasClass('saved')) {
                                self.removeClass('saved');
                            } else {
                                self.addClass('saved');
                            }

                            self.html(result.message);
                        }
                    }
                }
            });
        });

//application details
        $('.iwj-update-appication-btn, .iwj-update2-appication-btn').click(function () {
            $('.iwj-update-application-form').data('button', $(this));
        });

        $('.iwj-update-application-form').submit(function (e) {
            e.preventDefault();
            var self = $(this);
            var button = self.data('button');
            var send_email = button.hasClass('iwj-update2-appication-btn') ? true : false;
            var data = self.serialize();
            data = 'action=iwj_update_application&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;

            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    self.find('.iwj-respon-msg').slideUp(300, function () {
                        $(this).html('');
                    });
                },
                success: function (result) {
                    if (result) {
                        self.find('.iwj-respon-msg').html(result.message).slideDown(300);
                        if (result.success) {
                            if (send_email) {
                                var email_modal = $('#iwj-application-email-modal');
                                email_modal.find('#application_email').val(result.status).trigger('change');
                                email_modal.modal('show');
                            }
                        }
                    }

                    iwj_button_loader(button, 'remove');
                }
            });
        });

        $('#iwj-application-email-modal').on('show.bs.modal', function (e) {
            var self = $(this);
            var link = $(e.relatedTarget);
            var item_id = link.data('item-id');
            if (item_id) {
                self.find('input[name="application_id"]').val(item_id);
            }
            $('.iwj-respon-msg').empty();
        });

//applications modal
        $('#iwj-application-view-modal').on('click', '.iwj-update-appication-btn, .iwj-update2-appication-btn', function () {
            $('#iwj-application-view-modal').data('button', $(this));
        });

        $('#iwj-application-view-modal').on('submit', '.iwj-update-appication-form', function (e) {
            e.preventDefault();
            var self = $(this);
            var button = $('#iwj-application-view-modal').data('button');
            var send_email = button.hasClass('iwj-update2-appication-btn') ? true : false;
            var data = self.serialize();
            data = 'action=iwj_update_application&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;

            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    self.find('.iwj-respon-msg').slideUp(300, function () {
                        $(this).html('');
                    });
                },
                success: function (result) {
                    if (result) {
                        self.find('.iwj-respon-msg').html(result.message).slideDown(300);
                        if (result.success) {
                            $('tr.application-' + result.application_id + ' .application-status span').html(result.status_icon).attr('data-original-title', result.status_title).attr('class', result.status_class);
                            $('#iwj-application-view-modal').modal('hide');

                            if (send_email) {
                                var email_modal = $('#iwj-application-email-modal');
                                email_modal.find('#application_email').val(result.status).trigger('change');
                                setTimeout(function () {
                                    email_modal.find('input[name="application_id"]').val(result.application_id);
                                    email_modal.modal('show');
                                }, 500);
                            }
                        }
                    }

                    iwj_button_loader(button, 'remove');
                }
            });
        });

        $('#iwj-application-email-modal #application_email').change(function () {
            var value = $(this).val();
            if (value) {
                var email_values = $('#iwj-application-email-modal #application_email_value').val();
                email_values = JSON.parse(email_values);
                tinymce.get('message').setContent(email_values[value].message);
                $('#iwj-application-email-modal').find('[name="subject"]').val(email_values[value].subject);
            } else {
                $('#iwj-application-email-modal').find('[name="subject"]').val('');
                tinymce.get('message').setContent('');
            }
        });

        $('#iwj-application-view-modal').on("show.bs.modal", function (e) {
            var self = $(this);
            var link = $(e.relatedTarget);
            var application_id = link.data('application-id');
            if (application_id) {
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: 'action=iwj_get_application_details&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&application_id=' + application_id,
                    beforeSend: function () {
                        self.find('.modal-body').html($('#iwj-application-view-modal').data('loading'));
                    },
                    success: function (result) {
                        if (result) {
                            self.find('.modal-body').html(result);
                            $('#iwj-application-view-modal').find(".iwj-select-2-wsearch").each(function () {
                                var options = {'minimumResultsForSearch': 'Infinity'};
                                options.dropdownCssClass = 'iwj-select-2-wsearch';
                                $(this).select2(options);
                            });
                        }
                    }
                });
            }
        });

        $('.iwj-application-email-form').submit(function (e) {
            e.preventDefault();
            if (typeof tinyMCE != 'undefined') {
                tinyMCE.triggerSave();
            }
            var self = $(this);
            var button = self.find('.iwj-application-email-btn');
            var data = self.serialize();
            data = 'action=iwj_application_email&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    self.find('.iwj-respon-msg').slideUp(300, function () {
                        $(this).html('');
                    });
                },
                success: function (result) {
                    if (result) {
                        iwj_button_loader(button, 'remove');
                        self.find('.iwj-respon-msg').html(result.message).slideDown(300);
                        if (result.success) {
                            self.get(0).reset();
                        }
                    }

                }
            });
        });

        $('#iwj-submited-application-view-modal').on("show.bs.modal", function (e) {
            var self = $(this);
            var link = $(e.relatedTarget);
            var application_id = link.data('application-id');
            if (application_id) {
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: 'action=iwj_get_submited_application_details&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&application_id=' + application_id,
                    beforeSend: function () {
                        self.find('.modal-body').html($('#iwj-submited-application-view-modal').data('loading'));
                    },
                    success: function (result) {
                        if (result) {
                            self.find('.modal-body').html(result);
                        }
                    }
                });
            }
        });

        $('#iwj-order-view-modal').on("show.bs.modal", function (e) {
            var self = $(this);
            var link = $(e.relatedTarget);
            var order_id = link.data('order-id');
            if (order_id) {
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: 'action=iwj_get_order_details&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&order_id=' + order_id,
                    beforeSend: function () {
                        self.find('.modal-body').html($('#iwj-order-view-modal').data('loading'));
                    },
                    success: function (result) {
                        if (result) {
                            self.find('.modal-body').html(result);
                            $('#iwj-order-view-modal').find(".iwj-select-2").each(function () {
                                var options = $(this).data('options');
                                options = options ? options : {'minimumResultsForSearch': 'Infinity'};
                                $(this).select2(options);
                            });
                        }
                    }
                });
            }
        });

// employer delete application
        $('.iwj-delete-application').click(function (e) {
            e.preventDefault();
            $('#iwj-confirm-delete-application').find('.iwj-respon-msg').html('');
            $('#iwj-confirm-delete-application .modal-body p').html($(this).data('message'));
            $('#iwj-confirm-delete-application .iwj-agree-delete-application').data('id', $(this).data('id'));
            $('#iwj-confirm-delete-application').modal('show');
        });

        $('.iwj-agree-delete-application').click(function (e) {
            e.preventDefault();
            var self = $(this);
            var id = self.data('id');

            if (id) {
                var data = 'action=iwj_delete_application&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&id=' + id;
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        iwj_button_loader(self, 'add');
                        $('#iwj-confirm-delete-application').find('.iwj-respon-msg').slideUp(300).html('');
                    },
                    success: function (result) {
                        if (result) {
                            if (result.message) {
                                $('#iwj-confirm-delete-application').find('.iwj-respon-msg').html(result.message).slideDown(300);
                            }
                            if (result.success) {
                                $('.application-' + id).remove();
                            }
                            setTimeout(function () {
                                iwj_button_loader(self, 'remove');
                                $('#iwj-confirm-delete-application').modal('hide');
                            }, 2000);
                        }
                    }
                });
            }
        });

//trigger alert popup when filter
        if (typeof window.iwj_before_remove_filter_callback != 'object') {
            window.iwj_before_remove_filter_callback = new Array();
        }
        window.iwj_before_remove_filter_callback.push(function (id, type) {
            if (type == 'job') {
                var element = $('#iwj-job-alert-popup option[value="' + id + '"]');
                element.prop('selected', false);
                $('#categories').multiselect('refresh');
                $('#levels').multiselect('refresh');
                $('#types').multiselect('refresh');
                $('#locations').multiselect('refresh');
                $('#skills').multiselect('refresh');
            }
        });

        if (typeof window.iwj_before_remove_all_filter_callback != 'object') {
            window.iwj_before_remove_all_filter_callback = new Array();
        }
        window.iwj_before_remove_all_filter_callback.push(function (type) {
            if (type == 'job') {
                $('#iwj-filter-selected li').each(function () {
                    var id = $(this).data('termid');
                    $('#iwj-job-alert-popup option[value="' + id + '"]').prop('selected', false);
                });

                $('#categories').multiselect('refresh');
                $('#levels').multiselect('refresh');
                $('#types').multiselect('refresh');
                $('#locations').multiselect('refresh');
                $('#skills').multiselect('refresh');
            }
        });

        $('#iwj-job-alert-popup').on("show.bs.modal", function (e) {
            var self = $(this);
            var link = $(e.relatedTarget);

            $('#iwj-filter-selected li').each(function () {
                var term_id = $(this).data('termid');
                $('#iwj-job-alert-popup option[value="' + term_id + '"]').prop('selected', true);
            });

            $('#categories').multiselect('refresh');
            $('#levels').multiselect('refresh');
            $('#types').multiselect('refresh');
            $('#locations').multiselect('refresh');
            $('#skills').multiselect('refresh');
        });
//end trigger alert popup when filter

        var map_data = $("#job-detail-map");
        if (map_data.length) {
            var lat = map_data.data("lat");
            var lng = map_data.data("lng");
            var zoom = map_data.data("zoom");
            var maker_icon = map_data.data("maker");
            var address = map_data.data("address");
            var loc = new google.maps.LatLng(lat, lng);
            var mapOptions = {
                center: loc,
                zoom: zoom ? zoom : 12,
                scaleControl: false,
                scrollwheel: false,
                styles: (iwj.map_styles ? JSON.parse(iwj.map_styles) : [
                    {
                        "featureType": "administrative",
                        "elementType": "labels.text.fill",
                        "stylers": [
                            {
                                "color": "#444444"
                            }
                        ]
                    },
                    {
                        "featureType": "landscape",
                        "elementType": "all",
                        "stylers": [
                            {
                                "color": "#f2f2f2"
                            }
                        ]
                    },
                    {
                        "featureType": "poi",
                        "elementType": "all",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "road",
                        "elementType": "all",
                        "stylers": [
                            {
                                "saturation": -100
                            },
                            {
                                "lightness": 45
                            }
                        ]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "all",
                        "stylers": [
                            {
                                "visibility": "simplified"
                            }
                        ]
                    },
                    {
                        "featureType": "road.arterial",
                        "elementType": "labels.icon",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "transit",
                        "elementType": "all",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "water",
                        "elementType": "all",
                        "stylers": [
                            {
                                "color": "#46bcec"
                            },
                            {
                                "visibility": "on"
                            }
                        ]
                    }
                ]),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            var map = new google.maps.Map($('.job-detail-map').get(0), mapOptions);
            //map.panBy(140, -150);
            var marker_options = {
                position: loc,
                map: map
            };
            if (maker_icon) {
                marker_options.icon = maker_icon;
            }
            var marker = new google.maps.Marker(marker_options);

            var content = '';
            if (address) {
                content += '<div class="map-info">';
                if (address) {
                    content += '<div class="address">' + address + '</div>';
                }
                content += '</div>';
                var infowindow = new google.maps.InfoWindow({
                    content: content
                });

                google.maps.event.addListener(marker, 'click', function () {
                    infowindow.open(map, marker);
                });

                // Move the map a little to the left and down
                map.panBy(0, -100);
                infowindow.open(map, marker);

            }
        }

        $('.iwj-jobs-status, .iwj-jobs-orderby, .iwj-jobs-orderb, .iwj-application-job, .iwj-order-status, .iwj-order-type').change(function () {
            $(this).closest('form').submit();
        });

        $('.iwjmb-file-wrapper input[type="file"]').change(function () {
            var field_text = $(this).prev('input[type="text"]');
            if (field_text) {
                field_text.val($(this).val())
            }
        });

        $('.iwj-grid .job-item, .iwj-grid .iwj-employer-item, .iwj-grid .candidate-item, .iw-job-detail .job-detail-info ul li').matchHeight({
            byRow: true,
            property: 'height',
            target: null,
            remove: false
        });
        $('.iwj-grid .job-item, .iwj-grid .iwj-employer-item, .iw-recent-resumes .iwj-item, .iw-job-detail .job-detail-info ul li').data('setmatchHeight', true);

        if ($('.iwj-jobs-carousel .iwj-item').length) {
            $('.iwj-jobs-carousel .iwj-item').matchHeight({
                byRow: true,
                property: 'height',
                target: null,
                remove: false
            });
        }
        if ($('.iwj-style-match-height .job-item').length) {
            $('.iwj-style-match-height .job-item').matchHeight({
                byRow: true,
                property: 'height',
                target: null,
                remove: false
            });
        }
        if ($('.iw-recent-resumes.shortcode .iwj-item').length) {
            $('.iw-recent-resumes.shortcode .iwj-item').matchHeight({
                byRow: true,
                property: 'height',
                target: null,
                remove: false
            });
        }
        if ($('.iwj-categories .item-category.item-category-match-height').length) {
            $('.iwj-categories .item-category.item-category-match-height').matchHeight({
                byRow: true,
                property: 'height',
                target: null,
                remove: false
            });
        }
        if ($('.iwj-employers-slider.style1 .employer-item').length) {
            $('.iwj-employers-slider.style1 .employer-item').matchHeight({
                byRow: true,
                property: 'height',
                target: null,
                remove: false
            });
        }
        if ($('.iwj-employers-slider.style2 .employer-item').length) {
            $('.iwj-employers-slider.style2 .employer-item').matchHeight({
                byRow: true,
                property: 'height',
                target: null,
                remove: false
            });
        }
        if ($('.iw-recent-resumes-style2 .recent-resume-item').length) {
            $('.iw-recent-resumes-style2 .recent-resume-item').matchHeight({
                byRow: true,
                property: 'height',
                target: null,
                remove: false
            });
        }
        if ($('.iwj-jobs-carousel-match-height .job-item').length) {
            $('.iwj-jobs-carousel-match-height .job-item').matchHeight({
                byRow: true,
                property: 'height',
                target: null,
                remove: false
            });
        }

        $(document).click(function (evt) {
            if ($('.iwj-menu-action.collapse.in').length) {
                if (!$(evt.target).parents('.iwj-menu-action').length) {
                    $('.iwj-menu-action.collapse.in').each(function () {
                        $('[data-target="#' + $(this).attr('id') + '"]').trigger('click');
                    });
                }
            }
        });

        $('.jobs-layout-form .show-filter-mobile').click(function () {
            var filter_job = $('.iwj-sidebar-1');
            if (filter_job.hasClass('open-filter')) {
                filter_job.addClass('close-filter').removeClass('open-filter');
                $('body').removeClass('open-filter');
            } else {
                filter_job.addClass('open-filter').removeClass('close-filter');
                $('body').addClass('open-filter');
            }
            filter_job.on('click', '.widget-title', function () {
                filter_job.addClass('close-filter').removeClass('open-filter');
            });
        });

// Gallery Candidate
        $("a[rel=example_group]").fancybox({
            'transitionIn': 'none',
            'transitionOut': 'none',
            'titlePosition': 'over',
            'titleFormat': function (title, currentArray, currentIndex, currentOpts) {
                return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '</span>';
            }
        });
        /* Gallery Employer */
        if ($('.bxslider').length) {
            $('.bxslider').bxSlider({
                video: true,
                useCSS: false,
                pager: true
            });
        }

        $('body').on('click', '.iwj-show-sub-cat', function (e) {
            e.preventDefault();
            var arrow = jQuery(this);
            var sub_cat = jQuery(this).closest('.item-tax').next('ul.sub-cat');
            var other_cat = jQuery('ul.sub-cat');
            if (arrow.hasClass('open')) {
                arrow.removeClass('open');
            } else {
                arrow.addClass('open');
            }
            sub_cat.toggle(300, function () {
                if (sub_cat.hasClass('open')) {
                    sub_cat.removeClass('open');
                } else {
                    other_cat.removeClass('open');
                    sub_cat.addClass('open');
                }
            });
        });

        $('.iwj-rating-form').submit(function (e) {
            e.preventDefault();
            var form = $(this),
                    button = form.find('.iwj-review-btn'),
                    type_post = form.find('.iwj-review-btn').data('type_post_review'),
                    data = form.serialize();
            if (type_post === 'update_review') {
                var review_id = form.find('.iwj-review-btn').data('review_id');
                if (review_id) {
                    data = 'action=iwj_update_review&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data + '&review_id=' + review_id;
                    $.ajax({
                        url: iwj.ajax_url,
                        type: 'POST',
                        data: data,
                        dataType: 'json',
                        beforeSend: function () {
                            iwj_button_loader(button, 'add');
                            form.find('.iwj-rate-respon-msg').slideUp(300).html('');
                        },
                        success: function (result) {
                            if (result) {
                                iwj_button_loader(button, 'remove');
                                if (result.message) {
                                    form.find('.iwj-rate-respon-msg').html(result.message).slideDown(300);
                                }
                                if (result.success === true) {
                                    form.each(function () {
                                        this.reset();
                                    });
                                    button.data('type_post_review', 'update_review');
                                    setTimeout(function () {
                                        form.closest('.form-review-employer').html(result.message).slideDown(300);
                                    }, 2000);
                                    if (result.auto_approved) {
                                        setTimeout(function () {
                                            location.reload();
                                        }, 2200);
                                    }
                                }
                            }
                        }
                    });
                } else {
                    form.find('.iwj-rate-respon-msg').html('<div class="alert alert-warning">Please wait to your review are approved or adjust your review</div>').slideDown(300);
                }
            } else {
                data = 'action=iwj_review&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        iwj_button_loader(button, 'add');
                        form.find('.iwj-rate-respon-msg').slideUp(300).html('');
                    },
                    success: function (result) {
                        if (result) {
                            iwj_button_loader(button, 'remove');
                            if (result.message) {
                                form.find('.iwj-rate-respon-msg').html(result.message).slideDown(300);
                            }
                            if (result.success === true) {
                                form.each(function () {
                                    this.reset();
                                });
                                button.data('type_post_review', 'update_review');
                                setTimeout(function () {
                                    form.closest('.form-review-employer').html(result.message).slideDown(300);
                                }, 2000);
                            }
                        }
                    }
                });
            }
        });

        $('.iwj-reply-rate-form').submit(function (e) {
            e.preventDefault();
            var form = $(this),
                    button = form.find('.iwj-reply-review-btn'),
                    data = form.serialize(),
                    action = 'action=iwj_reply_review&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: action,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    form.find('.iwj-rate-reply-respon-msg').slideUp(300).html('');
                },
                success: function (result) {
                    if (result) {
                        iwj_button_loader(button, 'remove');
                        if (result.message) {
                            form.find('.iwj-rate-reply-respon-msg').html(result.message).slideDown(300);
                        }
                        if (result.success === true) {
                            form.closest('.employer-review-details').append('<div class="iwj-author-reply"><div class="iwj-reply-author-avatar"><img src="' + result.employer_url + '" align="' + result.employer_name + '" /></div><div class="iwj-reply-author-content"><h4>' + result.employer_name + ' response</h4><p class="iwj-reply-main-content">' + result.reply_content + '</p></div><span class="iwj-reply-review-btn iwj-edit-reply-reviewed pull-right"><i class="ion-edit"></i></span></div>');
                            button.closest('form.iwj-reply-rate-form').remove();
                            setTimeout(function () {
                                location.reload();
                            }, 1700);
                        }
                    }
                }
            });
        });

//popup write review
        var open_vote_1 = false;
        var open_popup_vote1 = function () {
            var out_height = $('.iwj-votes-icon').next('.iwj-box-each-vote').outerHeight();
//			$('.iwj-votes-icon').next('.iwj-box-each-vote').css('top', '-' + out_height / 2 + 'px');
            $('.iwj-votes-icon').next('.iwj-box-each-vote').addClass('iwj-show-popup-rate');
            open_vote_1 = true;
        };
        var close_popup_vote1 = function () {
            $('.iwj-votes-icon').next('.iwj-box-each-vote').removeClass('iwj-show-popup-rate');
            open_vote_1 = false;
        };
        $('.iwj-votes-icon').click(function (e) {
            e.stopPropagation();
            var toggle1 = open_vote_1 ? close_popup_vote1 : open_popup_vote1;
            toggle1();
        });

//popup view review
        $('.iwj-reviewed-box-icon').click(function (e) {
            e.stopPropagation();
            var out_width = $(this).next('.iwj-box-each-vote').outerWidth();
            $(this).next('.iwj-box-each-vote').toggleClass('iwj-show-popup-rate');
        });

        $(document).click(function (event) {
            if (!$(event.target).closest('.iwj-votes-icon').length) {
                close_popup_vote1();
            }
            if (!$(event.target).closest('.iwj-reviewed-box-icon').length) {
                $('.iwj-box-each-vote').removeClass('iwj-show-popup-rate');
            }
        });

        $('.iwj-edit-reviewed').click(function () {
            var review_id = $(this).data('review_id'),
                    num_criteria = $(this).prev('.iwj-box-reviewed').data('num_criteria'),
                    rate_star = $(this).data('rate_star'),
                    data = 'action=iwj_edit_review&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&review_id=' + review_id;
            if (review_id) {
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function (result) {
                        if (result) {
                            $('.form-review-employer').removeClass('iwj-job-reviewed');
                            $('html, body').animate({
                                scrollTop: $(".form-review-employer").offset().top - 35
                            }, 1000);
                            for (var k = 1; k <= 5; k++) {
                                if (rate_star % 1 === 0) {
                                    if (k <= rate_star) {
                                        jQuery('.iwj-votes-icon > i:nth-child(' + k + ')').attr('class', 'ion-android-star');
                                    } else {
                                        jQuery('.iwj-votes-icon > i:nth-child(' + k + ')').attr('class', 'ion-android-star-outline');
                                    }
                                } else {
                                    if (k < Math.ceil(rate_star)) {
                                        jQuery('.iwj-votes-icon > i:nth-child(' + k + ')').attr('class', 'ion-android-star');
                                    } else if (k === Math.ceil(rate_star)) {
                                        jQuery('.iwj-votes-icon > i:nth-child(' + k + ')').attr('class', 'ion-android-star-half');
                                    } else {
                                        jQuery('.iwj-votes-icon > i:nth-child(' + k + ')').attr('class', 'ion-android-star-outline');
                                    }
                                }
                            }
                            if (result.data.title) {
                                $('.iwj-rating-form').find('input[name="iwj_review_title"]').attr('value', result.data.title);
                            }
                            if (result.data.content) {
                                $('.iwj-rating-form').find('textarea[name="iwj_review_content"]').html(result.data.content);
                            }
                            if (result.data.criterias) {
                                var arr_vote_for = $.map(result.data.criterias, function ($el) {
                                    return $el;
                                });
                                for (var i = 0; i < num_criteria; i++) {
                                    var criteria_vote = $('.iwj-rating-form').find('input[name="iwj_rate_num_' + i + '"]').data('criteria_vote');
                                    $('.iwj-rating-form').find('input[data-criteria_vote="' + criteria_vote + '"]').attr('value', arr_vote_for[i]);
                                    $('.iwj-rating-form').find('input[data-criteria_vote="' + criteria_vote + '"]').prev('.filled-stars').css('width', arr_vote_for[i] * 20 + '%');
                                }
                            }
                            $('button[type="submit"].iwj-review-btn').attr('data-review_id', review_id);
                        }
                    }
                });
            }
        });

        $('.iwj-cancel-review-btn').click(function () {
            $(this).closest('.form-review-employer').addClass('iwj-job-reviewed');
        });

        $('.iwj-edit-review-btn').click(function () {
            var form = $(this).closest('form');
            form.data('button', $(this));
        });

        $('.iwj-user-update-review').submit(function (e) {
            e.preventDefault();
            var self = $(this);
            var button = self.data('button');
            var respon = button.parent().prev('.iwj-respon-msg');
            var data = self.serialize();
            data = 'action=iwj_update_review&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&' + data;
            $.ajax({
                url: iwj.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    iwj_button_loader(button, 'add');
                    respon.slideUp(300, function () {
                        $(this).html('');
                    });
                },
                success: function (result) {
                    if (result.message) {
                        respon.html(result.message).slideDown(300);
                    }
                    if (result.success === true) {
                        iwj_button_loader(button, 'remove');
                        setTimeout(function () {
                            window.location.href = result.permalink;
                        }, 2000);
                    }
                }
            });
        });

        $('.iwj-edit-reply-reviewed').click(function (e) {
            e.preventDefault();
            var content = $(this).prev('.iwj-reply-author-content').find('.iwj-reply-main-content').text();
            $(this).prev('.iwj-reply-author-content').find('.iwj-reply-main-content').remove();
            $(this).prev('.iwj-reply-author-content').find('textarea[name="iwj_employer_update_rep"]').slideDown(300);
            $(this).prev('.iwj-reply-author-content').find('.iwj-button-loader').slideDown(400);
            $(this).prev('.iwj-reply-author-content').find('button.iwj-btn-update-reply').removeAttr('disabled');
            $(this).prev('.iwj-reply-author-content').find('button.iwj-btn-update-reply').slideDown(400);
            $(this).prev('.iwj-reply-author-content').find('.iwj-cancel-edit-reply-btn').data('content', content);
        });

        $('.iwj-btn-update-reply').click(function (e) {
            e.preventDefault();
            var id = $(this).data('id'),
                    button = $(this),
                    respon = button.prev('.iwj-respon-msg'),
                    content = button.closest('.iwj-reply-author-content').find('#iwj_employer_update_rep').val();
            if (id) {
                var data = 'action=iwj_edit_reply_review&_ajax_nonce=' + iwj.security + '&lang=' + iwj.lang + '&iwj_reply_id=' + id + '&iwj_reply_content=' + content;
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        iwj_button_loader(button, 'add');
                        respon.slideUp(300, function () {
                            $(this).html('');
                        });
                    },
                    success: function (result) {
                        if (result.message) {
                            respon.html(result.message).slideDown(300);
                        }
                        if (result.success === true) {
                            iwj_button_loader(button, 'remove');
                            setTimeout(function () {
                                respon.slideUp(300);
                                button.closest('.iwj-reply-author-content').find('#iwj_employer_update_rep').slideUp(300);
                                button.closest('.iwj-reply-author-content').find('.iwj-button-loader').slideUp(300);
                                button.closest('.iwj-reply-author-content').find('button.iwj-btn-update-reply').attr('disabled', 'disabled');
                                button.closest('.iwj-reply-author-content').find('#iwj_employer_update_rep').val(result.reply_content);
                                button.closest('.iwj-reply-author-content').find('h4').after('<p class="iwj-reply-main-content">' + result.reply_content + '</p>');
                            }, 1500);
                        }
                    }
                });
            }
        });

        $('.iwj-cancel-edit-reply-btn').click(function () {
            var self = $(this),
                    content = self.data('content');
            self.closest('.iwj-reply-author-content').find('.iwj-respon-msg').slideUp(300);
            self.closest('.iwj-reply-author-content').find('#iwj_employer_update_rep').slideUp(300);
            self.closest('.iwj-reply-author-content').find('.iwj-button-loader').slideUp(300);
            self.closest('.iwj-reply-author-content').find('button.iwj-btn-update-reply').attr('disabled', 'disabled');
            self.closest('.iwj-reply-author-content').find('h4').after('<p class="iwj-reply-main-content">' + content + '</p>');
        });

        var user_id = typeof userSettings === 'object' ? userSettings.uid : 0;
        if (iwj_getCookie('iwj_notification_' + user_id) == 1) {
            $(this).find('.iwj_link_notice').addClass('off-notification');
            $('.notification').find('#notification-count').addClass('hidden');
        }

        $('.notification').on('hover', function (e) {
            e.preventDefault();
            var user_id = $(this).find('.iwj_link_notice').data('user_id');
            if (iwj_getCookie('iwj_notification_' + user_id) !== 1) {
                $(this).find('.iwj_link_notice').addClass('off-notification');
                $(this).find('#notification-count').addClass('hidden');
                iwj_setCookie('iwj_notification_' + user_id, 1, 1);
            }
        });

        if ($('body.iwj-candidate_suggestion-page .iwj-content-inner').hasClass('iwj_empty_cls')) {
            if (!$(this).closest('article').hasClass('iwj_empty_cls')) {
                $('body.iwj-candidate_suggestion-page article.page').addClass('iwj_empty_cls');
            }
        }

        if ($('body.iwj-suggest_job-page .iwj-content-inner').hasClass('iwj_empty_cls')) {
            if (!$(this).closest('article').hasClass('iwj_empty_cls')) {
                $('body.iwj-suggest_job-page article.page').addClass('iwj_empty_cls');
            }
        }

        if (!$('.header-style-default').find('.social-header').length > 0) {
            $(this).find('.notification').addClass('notify-align-left');
        }

// Close menu action
        $('body').on('click', function (e) {
            var item = $('.iwj-menu-action');
            var id = item.data('id');
            var popup = $('#' + id);
            if (!$(this).is(e.target) && !popup.is(e.target) && popup.has(e.target).length == 0) {
                item.removeClass("in");
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

        $('.iwj-button-print-job').on('click', function () {
            var title = $(this).data('title'),
                    author = $(this).data('author'),
                    author_avatar = $(this).data('author_avatar'),
                    divToPrint = document.getElementById('job-detail-content'),
                    $_style = '.job-detail-info{float:left; margin-bottom: 20px;} .job-detail-info ul li{list-style: none; float: left; width: 200px; border-bottom: 1px #f6f7f9 solid; border-right: 1px #f6f7f9 solid; padding: 4px 25px; height: auto !important;} .job-detail-info ul li .left{float: left;} #iwj-print-job >h1,#iwj-print-job >h4{text-align:center;}',
                    newWin = window.open('', '', 'left=50%,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
            var author_url = author_avatar ? '<br><img src="' + author_avatar + '" style="max-width: 135px; margin:15px auto 2px;">' : '';
            newWin.document.open();
            newWin.document.write('<html><head><style>' + $_style + '</style></head><body onload="window.print()"><div id="iwj-print-job"><h1>' + title + '</h1><h4>Company: ' + author + ' ' + author_url + '</h4>' + divToPrint.innerHTML + '</div></body></html>');
            newWin.document.close();
            newWin.focus();
            newWin.print();
            newWin.close();
        });

        $('.iwj-showmore').on('click', function () {
            var button = $(this),
                    offset = button.closest('.iwj-listing').find('.grid-content').length,
                    posts_per_page = button.data('posts_per_page'),
                    max_number_posts = button.data('max_number_posts'),
                    taxonomies = button.data('taxonomies'),
                    include_id = button.data('include_id'),
                    exclude_id = button.data('exclude_id'), style = button.data('style');
            if (taxonomies) {
                taxonomies = JSON.stringify(taxonomies);
            }
            var data = 'action=iwj_loadmore_jobs&_ajax_nonce=' + iwj.security + '&posts_per_page=' + posts_per_page + '&offset=' + offset + '&taxonomies=' + taxonomies + '&exclude_id=' + exclude_id + '&include_id=' + include_id + '&style=' + style;
            if (posts_per_page && offset < max_number_posts) {
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: data,
                    dataType: 'html',
                    beforeSend: function () {
                        iwj_button_loader(button, 'add');
                    },
                    success: function (result) {
                        if (result) {
                            iwj_button_loader(button, 'remove');
                            button.closest('.iwj-listing').find('.iwj-job-items').append(result);
                            if (offset + posts_per_page >= max_number_posts) {
                                button.closest('.w-pag-load-more').remove();
                            }
                            if ($('.iwj-style-match-height .job-item').length) {
                                $('.iwj-style-match-height .job-item').matchHeight({
                                    byRow: true,
                                    property: 'height',
                                    target: null,
                                    remove: false
                                });
                            }
                        }
                    }
                });
            }
        });

        function iwj_get_indeed_jobs(form) {
            var publisher_id = form.data('publisher_id'),
                    max_items = form.data('max_items'),
                    logo_url = form.data('logo_url'),
                    style = form.data('style'), ide_query = form.find('input[name="iwj_ide_query"]').val(),
                    ide_location = form.find('select.iwj_ide_location').val(),
                    ide_type = form.find('select.iwj_ide_type').val();

            if (publisher_id && ide_query) {
                var data = 'action=iwj_indeed_load_data&_ajax_nonce=' + iwj.security + '&query=' + ide_query + '&location=' + ide_location + '&job_type=' + ide_type + '&publisher_id=' + publisher_id + '&max_items=' + max_items + '&logo_url=' + logo_url + '&style=' + style;
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        form.next().css({opacity: 0.3});
                    },
                    success: function (result) {

                        form.next().css({opacity: 1});
                        if (result.success === true) {
                            form.next().find('.iwj-job-items').html(result.data);
                        }
                    },
                    error: function (result) {

                    }
                });
            }
        }

        $('form.iwj-job-indeed-loader').on('change', 'select', function () {
            var form = $(this).closest('form');
            iwj_get_indeed_jobs(form);
        });

        var indeed_search_time, indeed_search_delay = 500;
        $('form.iwj-job-indeed-loader input[name="iwj_ide_query"]').on('keydown blur change', function (e) {
            var form = $(this).closest('form');
            clearTimeout(indeed_search_time);
            indeed_search_time = setTimeout(function () {
                iwj_get_indeed_jobs(form);
            }, indeed_search_delay);
        });

        $('input[name="iwj_ide_query"]').on('change', function () {
            var val = $(this).val();
            $(this).closest('#iwajax-load').find('button.iwj-ide-showmore').attr('data-query', val);
        });

        $('select[name="iwj_ide_location"]').on('change', function () {
            var val = $(this).val();
            $(this).closest('#iwajax-load').find('button.iwj-ide-showmore').attr('data-country', val);
            $(this).closest('#iwajax-load').find('button.iwj-ide-showmore').attr('data-location', '');
        });

        $('select[name="iwj_ide_type"]').on('change', function () {
            var val = $(this).val();
            $(this).closest('#iwajax-load').find('button.iwj-ide-showmore').attr('data-job_type', val);
        });

        $('.iwj-ide-showmore').on('click', function () {
            var button = $(this),
                    offset = button.closest('.iwj-listing').find('.grid-content').length,
                    query = button.data('query'),
                    publisher_id = button.data('publisher_id'),
                    max_items = button.data('max_items'),
                    style = button.data('style'),
                    country = button.data('country'),
                    location = button.data('location'),
                    job_type = button.data('job_type'),
                    logo_url = button.data('logo_url');
            var data = 'action=iwj_loadmore_indeed_jobs&_ajax_nonce=' + iwj.security + '&max_items=' + max_items + '&offset=' + offset + '&query=' + query + '&publisher_id=' + publisher_id + '&logo_url=' + logo_url + '&style=' + style + '&country=' + country + '&location=' + location + '&job_type=' + job_type;
            if (publisher_id) {
                $.ajax({
                    url: iwj.ajax_url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        iwj_button_loader(button, 'add');
                    },
                    success: function (result) {
                        iwj_button_loader(button, 'remove');
                        if (result.success) {
                            button.closest('.iwj-listing').find('.iwj-job-items').append(result.data_opt);
                        } else {
                            button.closest('.w-pag-load-more').remove();
                        }
                    },
                    error: function (result) {
                        iwj_button_loader(button, 'remove');
                    }
                });
            }
        });
    });

    $('body').on('mouseenter', 'a[data-color]', function () {
        var color = $(this).data('color');
        if (color) {
            var ori_background = $(this).css('background-color');
            $(this).data('ori-background-color', ori_background);
            $(this).css({'background-color': color});
        }
    });

    $('body').on('mouseout', 'a[data-color]', function () {
        var color = $(this).data('ori-background-color');
        $(this).css({'background-color': color});
    });

    $(window).load(function () {
        if ($('.iwj-isotope-main').length) {
            var $container = $('.iwj-isotope-main').isotope({
                itemSelector: '.element-item'
                        //	layoutMode:'masonry',
                        //	resizesContainer: true,
                        //	resizable: true,
            });
        }
    });

    $(window).on("load resize", function () {
        /* Position voted Employer */
        $('.iwj-review-content .iwj-review-item').each(function () {
            var item = $(this).find('.iwj-box-reviewed');
            if (item.length > 0) {
                var w_rate = item.outerWidth();
                var offset_left = item.offset().left;
                var w_wrapper = $('body .wrapper').width();
                var ltr = (w_wrapper - (offset_left + w_rate));
                if (ltr < 230 && offset_left > 230) {
                    item.addClass('voted-position-new-l');
                } else {
                    item.removeClass('voted-position-new-l');
                }
                if (offset_left < 230 && ltr > 230) {
                    item.addClass('voted-position-new-r');
                } else {
                    item.removeClass('voted-position-new-r');
                }
            }
        });
    });

})(jQuery);

function removeParam(key, sourceURL) {
    var rtn = sourceURL.split("?")[0],
            param,
            params_arr = [],
            queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
    if (queryString !== "") {
        params_arr = queryString.split("&");
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
                params_arr.splice(i, 1);
            }
        }
        rtn = rtn + "?" + params_arr.join("&");
    }
    return rtn;
}

function iwj_setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

function iwj_getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0)
            return null;
    } else
    {
        begin += 2;
        var end = document.cookie.indexOf(";", begin);
        if (end == -1) {
            end = dc.length;
        }
    }
    return decodeURI(dc.substring(begin + prefix.length, end));
}