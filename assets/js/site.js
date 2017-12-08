(function ($) {

	/**
	 * DONATE_Site object
	 * @type Object
	 */
	DONATE_Site = {
		/**
		 *
		 * @returns document
		 */
		$doc                  : null,
		init                  : function () {
			/**
			 * document
			 */
			this.$doc = $(document);

			/* validate checkout form */
			this.validate_checkout_form();

			/**
			 * percent count campaign donated
			 */
			this.generate_percent();

			/**
			 * payment gateways tabs
			 */
			this.$doc.on('click', '.donate_payments li a', this.payment_gateway_tab);
			/**
			 * load form action
			 */
			this.$doc.on('click', '.donate_load_form, .donate_button_title', this.load_donate_form);
			/**
			 * submit on lightbox
			 */
			this.$doc.on('submit', '.donate_form', this.donate_submit);

			/**
			 * add hook
			 */
			this.hooks.init();
		},
		/**
		 * hooks
		 */
		hooks                 : {
			init    : function () {
				TP_Donate_Global.addAction('donate_submit_submited_form_completed', this.submited);
			},
			submited: function (res) {
				if (res.status === 'success' && typeof res.url !== 'undefined') {
					window.location.href = res.url;
				} else if (res.status === 'failed' && typeof res.message !== 'undefined') {
					console.log(res.message);
					DONATE_Site.generate_messages(res.message);
					console.log(DONATE_Site.generate_messages(res.message));
					$('body, html').animate({
						scrollTop: $('.donation-messages').offset().top
					});
				}
			}
		},
		/**
		 * payment gateways change toggle
		 * @param {type} e
		 * @returns {Boolean}
		 */
		payment_gateway_tab   : function (e) {
			e.preventDefault();
			var _this = $(this),
				_li_target = _this.parents('li:first'),
				_payment = _this.attr('data-payment-id'),
				_input_payment = $('input[name="payment_method"]'),
				_target = _this.attr('href'),
				_gateways = $('.payment-method');
			if (_li_target.hasClass('active')) {
				return false;
			}

			_input_payment.val(_payment);
			_this.parents('.donate_payments:first').find('li').removeClass('active');
			_li_target.addClass('active');
			_gateways.slideUp(400, function () {
				$(_target).slideDown();
			});
			return false;
		},
		/**
		 * load donate form
		 * @return null
		 */
		load_donate_form      : function (event) {
			event.preventDefault();

			var _self = $(this),
				_campaign_id = _self.attr('data-campaign-id'),
				_data = {
					action: 'donate_load_form',
					nonce : thimpress_donate.nonce
				};

			if (typeof _campaign_id !== 'undefined') {
				_data.campaign_id = _campaign_id;
			}

			$.ajax({
				url       : thimpress_donate.ajaxurl,
				type      : 'POST',
				data      : _data,
				dataType  : 'html',
				beforeSend: function () {
					TP_Donate_Global.beforeAjax();
				}
			}).done(function (html) {
				TP_Donate_Global.afterAjax();
				$('#donate_hidden').html(html);
				$.magnificPopup.open({
					type     : 'inline',
					items    : {
						src: '#donate_hidden'
					},
					callbacks: {
						open: function () {
							var timeout = setTimeout(function () {
								$('#donate_hidden input[name="donate_input_amount"]:first').focus();
								$('#donate_hidden input[name="payment_method"]:first').attr('checked', true);
								TP_Donate_Global.applyFilters('donate_loaded_donate_form', _data);
								clearTimeout(timeout);
							}, 100);
						}
					}
				});

			});

		},
		/* validate checkout fields */
		validate_checkout_form: function () {
			var form = $('.donate_form'),
				fields = form.find('input, textarea');

			for (var i = 0; i < fields.length; i++) {
				var field = $(fields[i]);
				field.blur(function () {
					var input = $(this);
					if (input.hasClass('required')) {
						if (input.val() === '' || ( input.hasClass('email') && new RegExp('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$').test(input.val()) === false )) {
							input.removeClass('validated').addClass('donate_input_invalid').addClass('error');
						} else {
							input.removeClass('donate_input_invalid').removeClass('error').addClass('validated');
						}
					}
				});
			}
		},
		/**
		 * submit form action
		 * @param {type} e
		 * @returns {Boolean}
		 */
		donate_submit         : function (e) {
			e.preventDefault();

			var _form = $(this),
				_layout = _form.find('.donate_form_layout'),
				_message = _form.find('.donation-messages');

			// remove old message error
			_form.find('.donate_form_error_messages').remove();
			// invalid fields process ajax
			var _data = _form.serializeArray(_form);

			if (!TP_Donate_Global.applyFilters('donate_before_submit_form', _data)) {
				return;
			}

			$.ajax({
				url       : thimpress_donate.ajaxurl,
				type      : 'POST',
				data      : _data,
				beforeSend: function () {
					TP_Donate_Global.processing();
				}
			}).done(function (res) {
				TP_Donate_Global.complete();

				res = TP_Donate_Global.applyFilters('donate_submit_submited_form_results', res);

				if (typeof res.status === 'undefined') {
					return;
				}

				if (res) {
					TP_Donate_Global.doAction('donate_submit_submited_form_completed', res);
				}

				if (typeof res.form !== 'undefined' && typeof res.args !== 'undefined' && res.form === true) {
					// process with authorize.net SIM payment
					var args = res.args;
					if (Object.keys(args).length !== 0) {
						var html = [];
						html.push('<form id="donate_form_instead" action="' + res.url + '" method="POST">')
						$.each(args, function (name, value) {
							html.push('<input type="hidden" name="' + name + '" value="' + value + '" />');
						});
						html.push('<button type="submit" class="donate-redirecting">' + res.submit_text + '</button>');
						html.push('</form>');
						_form.replaceWith(html.join(''));
						$('#donate_form_instead').submit();
					}
				}
			});

			return false;
		},
		donate_on_lightbox    : function () {
			if (typeof thimpress_donate.settings !== 'undefined' &&
				typeof thimpress_donate.settings.checkout !== 'undefined' &&
				typeof thimpress_donate.settings.checkout.lightbox_checkout !== 'undefined' &&
				thimpress_donate.settings.checkout.lightbox_checkout === 'yes') {
				return true;
			}

			return false;
		},
		generate_messages     : function (messages) {
			var form = $('.donate_form');
			if (form.find('.donation-messages').length === 1) {
				$('.donation-messages').replaceWith(messages);
			} else {
				form.prepend(messages);
			}
		},
		beforeAjax            : function (_form) {
			if (typeof _form === 'undefined')
				return;

			_form.find('.donate_button').addClass('donate_button_processing');
		},
		afterAjax             : function (_form) {
			if (typeof _form === 'undefined')
				return;

			_form.find('.donate_button').removeClass('donate_button_processing');
		},
		generate_percent      : function () {
			var percents = $('.donate_counter_percent');
			for (var i = 0; i < percents.length; i++) {
				var percent = $(percents[i]),
					percent_width = percent.attr('data-percent'),
					counter = percent.parent('.donate_counter:first'),
					counter_width = counter.outerWidth(),
					tootip = percent.find('.donate_percent_tooltip'),
					tootip_width = tootip.outerWidth();

				percent.css({
					width: percent_width + '%'
				});

				if (tootip_width / 2 >= percent.outerWidth()) {
					tootip.css({
						left: 0
					});
				} else if (( tootip_width / 2 + percent.outerWidth() ) <= counter_width) {
					tootip.css({
						left: percent.outerWidth() - tootip_width / 2
					});
				} else if (( tootip_width / 2 + percent.outerWidth() ) > counter_width) {
					tootip.css({
						left: ( counter_width - tootip_width )
					});
				}
			}
		}
	};

	$(document).ready(function () {
		DONATE_Site.init();
	});

	$(window).resize(function () {
		DONATE_Site.generate_percent();
	});

})(jQuery);
