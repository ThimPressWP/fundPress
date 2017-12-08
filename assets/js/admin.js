(function ($) {

    // object js
    DONATE_Admin = {
        init: function () {
            this.admin_setting_tab();
            this.donate_meta_box.init();
            this.donate_lightbox();
            this.action_status();
            // this.status_tooltip();
            // select2 js
            $('.tp_donate_wrapper_content select').select2({
                width: 'resolve',
                dropdownAutoWidth: true
            });
        },
        // tab setting function
        admin_setting_tab: function () {
            // admin setting
            $('.tp_donate_wrapper_content > div:not(:first)').hide();
            $(document).on('click', '.tp_donate_setting_wrapper .nav-tab-wrapper a', function (e) {
                e.preventDefault();

                var a_tabs = $('.tp_donate_setting_wrapper .nav-tab-wrapper a');
                a_tabs.removeClass('nav-tab-active');
                var _self = $(this),
                    _tab_id = _self.attr('data-tab');

                _self.addClass('nav-tab-active');
                $('.tp_donate_wrapper_content > div').hide();
                $('.tp_donate_wrapper_content #' + _tab_id).fadeIn();

                return false;
            });

            // donate metabox
            $('.donate_metabox_setting_section:not(:first)').hide();
            $(document).on('click', '.donate_metabox_setting a', function (e) {
                e.preventDefault();

                var a_tabs = $('.donate_metabox_setting a');
                a_tabs.removeClass('nav-tab-active');
                var _self = $(this),
                    _tab_id = _self.attr('id');

                _self.addClass('nav-tab-active');
                $('.donate_metabox_setting_section').hide();
                $('.donate_metabox_setting_section[data-id^="' + _tab_id + '"]').fadeIn();

                return false;
            });

            $('#checkout > div:not(:first)').hide();
            $(document).on('click', '.tp_donate_wrapper_content h3 a', function (e) {
                e.preventDefault();

                $('.tp_donate_wrapper_content h3 a').removeClass('active');
                var _self = $(this),
                    _data_id = _self.attr('id');

                _self.addClass('active');
                $('#checkout > div').hide();

                $('#checkout > div[data-tab-id^="' + _data_id + '"]').show();

            });
        },
        donate_meta_box: {
            init: function () {
                $(document).on('click', '.donate_metabox_setting_section .add_compensate', this.add_compensate);
                $(document).on('click', '.donate_metabox_setting_container .donate_metabox .remove', this.remove_compensate);
                this.datepicker();

                /* change donate type */
                $(document).on('change', '#thimpress_donate_type', this.donate_type_on_change);

                /* edit donate item */
                $(document).on('click', '.donate_add_campaign', this.add_campain);
                /* remove donate item */
                $(document).on('click', '.donate_items .remove', this.remove_campaign);

                /* on change total campaign item */
                $(document).on('change', '.donate_item_total', function (e) {
                    e.preventDefault();
                    DONATE_Admin.donate_meta_box.calculator();
                    return false;
                });
            },
            add_compensate: function (e) {
                e.preventDefault();

                var _self = $(this),
                    _section = $('.donate_metabox:last'),
                    _id = 0,
                    _parent = _self.parents('.donate_metabox_setting_section:first'),
                    _template = wp.template('compensate-layout');

                if (_section.length === 1) {
                    _id = _section.attr('data-compensate-id');
                    _id = parseInt(_id) + 1;
                }

                _self.before(_template({id: _id}));
            },
            remove_compensate: function (e) {
                e.preventDefault();
                var _self = $(this),
                    _record = _self.parents('.form-group:first'),
                    _post_id = $('body').find('#post_ID').val();

                $.ajax({
                    url: thimpress_donate.ajaxurl,
                    type: 'POST',
                    data: {
                        compensate_id: _self.attr('data-compensate-id'),
                        action: 'donate_remove_compensate',
                        post_id: _post_id
                    },
                    beforeSend: function () {

                    }
                }).done(function (res) {

                    if (typeof res.status === 'undefined') {
                        return;
                    }

                    if (res.status === 'success') {
                        _record.remove();
                    } else if (res.status === 'failed' && typeof res.message !== 'undefined') {
                        alert(res.message);
                    }

                }).fail(function () {

                });
            },
            datepicker: function () {
                var _start = $('input[name="thimpress_campaign_start"]'),
                    _end = $('input[name="thimpress_campaign_end"]');

                _start.datepicker({
                    dateFormat: thimpress_donate.i18n.date_time_format,
                    maxDate: '+365D',
                    numberOfMonths: 1,
                    onSelect: function (date) {
                        _end.datepicker('option', 'minDate', date);
                    }
                });
                _end.datepicker({
                    dateFormat: thimpress_donate.i18n.date_time_format,
                    maxDate: '+365D',
                    numberOfMonths: 1,
                    onSelect: function (date) {
                        _start.datepicker('option', 'maxDate', date);
                    }
                });
            },
            donate_type_on_change: function (e) {
                e.preventDefault();
                var _self = $(this),
                    _type = _self.val(),
                    _section = $('#section_' + _type);

                $('.donate_section_type').toggleClass('hide-if-js');
                DONATE_Admin.donate_meta_box.calculator();
            },
            add_campain: function (e) {
                e.preventDefault();
                var template = wp.template('donate-template-campaign-item')({
                    unique_id: Math.random().toString(36).substring(7)
                });
                $('.donate_items tbody').append(template);
                return false;
            },
            remove_campaign: function (e) {
                e.preventDefault();
                var _self = $(this),
                    _tr = _self.parents('tr:first');
                _tr.remove();

                DONATE_Admin.donate_meta_box.calculator();
                return false;
            },
            calculator: function () {
                var items = $('.donate_item_total'),
                    total = 0,
                    foot = $('.donate_items tfoot'),
                    currency = foot.attr('data-currency');
                for (var i = 0; i < items.length; i++) {
                    var item = $(items[i]),
                        item_total = parseFloat(item.val());
                    total += item_total;
                }

                foot.find('.amount ins').html(total);
            }
        },
        donate_lightbox: function () {
            var donate_lightbox = $('#lightbox_checkout'),
                donate_redirect = $('#donate_redirect'),
                tr_donate_redirect = donate_redirect.parents('tr:first');

            if (donate_lightbox.val() === 'no')
                return;

            tr_donate_redirect.hide();

            donate_lightbox.on('change', function (e) {
                e.preventDefault();

                if ($(this).val() === 'yes') {
                    tr_donate_redirect.hide();
                } else {
                    tr_donate_redirect.show();
                }
            });
        },
        action_status: function (e) {
            $(document).on('click', '#action-status a', function (e) {
                e.preventDefault();
                var _self = $(this),
                    _donate_id = _self.parents('.action-status').attr('data-id'),
                    _action = _self.attr('data-action'),
                    _status = _self.closest('.type-dn_donate').find('label.donate-status');

                $.ajax({
                    url: thimpress_donate.ajaxurl,
                    type: 'POST',
                    data: {
                        donate_id: _donate_id,
                        action: 'donate_action_status',
                        status: _action
                    },
                    beforeSend: function () {

                    }
                }).done(function (res) {
                    if (typeof res.status === 'undefined') {
                        return;
                    }

                    if (res.status === 'success') {
                        _status.text(thimpress_donate.i18n['status_' + res.action + '']);
                        _status.addClass('donate-' + res.action + '');
                        _self.hide();
                    } else if (res.status === 'failed' && typeof res.message !== 'undefined') {
                        alert(res.message);
                    }
                })


            })
        },
        // status_tooltip: function(){
        //     $(document).on('hover', '#action-status a', function (e) {
        //         var _tooltips = $('[tooltip]').tooltip({
        //             position: {
        //                 my: "left top",
        //                 at: "right+5 top-5",
        //                 collision: "none"
        //             }
        //         });
        //         _tooltips.tooltip('open')
        //     })
        // }
    };

    // ready
    $(document).ready(function () {
        // call DONATE_Admin initialize
        DONATE_Admin.init();
    });

})(jQuery);