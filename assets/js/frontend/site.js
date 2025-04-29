;(function ($) {

    try {
        var stripe = Stripe( dn_localize.stripe_publish_key );
    } catch( error ) {
        console.log( error );
        return;
    }
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
                console.log(TP_Donate_Global);
			},
			submited: function (res) {
                console.log(res);
				if (res.status === 'success' && typeof res.redirect !== 'undefined') {
					window.location.href = res.redirect;
				} else if (res.status === 'failed' && typeof res.message !== 'undefined') {
                    var desiredHeight = $(window).height() - 150;
					console.log(res.message);
					DONATE_Site.generate_messages(res.message);
					console.log(DONATE_Site.generate_messages(res.message));
					$('body, html').animate({
						scrollTop: $('.donation-messages').offset().top - desiredHeight
					},1000);
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
				_message = _form.find('.donation-messages'),
                _button = _form.find('.donate_button');
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
                    TP_Donate_Global.beforeAjax();
				}
			}).done(function (res) {
                TP_Donate_Global.afterAjax();

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

	var dn_stripe = {
		init: function() {

			window.addEventListener( 'hashchange', dn_stripe.onHashChange );
		},

		notice: function( $message ) {
			$( 'div.donate_checkout' ).find( '.donate_form_messages' ).remove();
			$( 'div.donate_checkout' ).prepend( '<div class="donate_form_messages error">' + $message + '</div>' );
		},

		onHashChange: function() {
			
            var partials = window.location.hash.match( /^#?confirm-(pi|si)-([^:]+):(.+)$/ );

            if ( ! partials || 4 > partials.length ) {
                return;
            }

            var type               = partials[1];
            var intentClientSecret = partials[2];
            var redirectURL        = decodeURIComponent(partials[3]);
            window.location.hash = '';
            dn_stripe.openIntentModal( intentClientSecret, redirectURL, false, 'si' === type );
		},

		openIntentModal: function( intentClientSecret, redirectURL, alwaysRedirect, isSetupIntent ) {
			var buttonCheckout = $( '.donate_payment_button_process .donate_button' );

			stripe[ isSetupIntent ? 'confirmCardSetup' : 'confirmCardPayment' ]( intentClientSecret )
				.then( function( response ) {
                    if ( response.error ) {                        
                        dn_stripe.notice( response.error.message );
                        throw response.error;
                    }
                    var intent = response[ isSetupIntent ? 'setupIntent' : 'paymentIntent' ];

                    if ( 'requires_capture' !== intent.status && 'succeeded' !== intent.status ) {
                        dn_stripe.notice( dn_localize.error_verify );
                        return;
                    }

                    $.get( redirectURL, function( data ) {
                        if ( data.status !== 'success' ) {
                            if ( data.message ) {
                                dn_stripe.notice( data.message );
                            } else {
                                dn_stripe.notice( 'Hotel Booking Stripe Js error.' );
                            }
                        }

                        if ( data.redirect ) {
                            window.location = data.redirect;
                        }

                        //buttonCheckout.html( lpCheckoutSettings.i18n_place_order );
                        //buttonCheckout.prop( 'disabled', false );
                    } );
				} )
				.catch( function( error ) {
                    $( document.body ).trigger( 'stripeError', { error: error } );
                    
                    dn_stripe.notice( dn_localize.error_verify );

                    //Report back to the server.
                    buttonCheckout.html( dn_localize.button_verify );

                    $.get( redirectURL, function( data ) {
                        if ( data.status !== 'success' ) {
                            if ( data.message ) {
                                dn_stripe.notice( data.message );
                            } else {
                                dn_stripe.notice( 'FundPress Stripe Js error.' );
                            }
                        }

                        if ( data.redirect ) {
                            window.location = data.redirect;
                        }

                        //buttonCheckout.html( lpCheckoutSettings.i18n_place_order );
                        //buttonCheckout.prop( 'disabled', false );
                    } );
				} );
		},

	};

	$(document).ready(function () {
		DONATE_Site.init();
		dn_stripe.init();
	});

	$(window).resize(function () {
		DONATE_Site.generate_percent();
	});

})(jQuery);
