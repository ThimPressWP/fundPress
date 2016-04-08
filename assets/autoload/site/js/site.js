(function($){

	/**
	 * DONATE_Site object
	 * @type Object
	 */
	DONATE_Site = {

		init: function()
		{
			/**
			 * load form action
			 */
			this.load_donate_form();

			/**
			 * submit on lightbox
			 */
			this.donate_submit();

			// load percent
			// this.generate_percent();
		},

		/**
		 * load donate form
		 * @return null
		 */
		load_donate_form: function()
		{
			/*
			 * load form on click
			 */
			$( document ).on( 'click', '.donate_load_form, .donate_button_title', function( event ){
				event.preventDefault();

				var _self = $(this),
					_campaign_id = _self.attr( 'data-campaign-id' ),
					_data = {
						action: 'donate_load_form',
						nonce: thimpress_donate.nonce
					};

				if( typeof _campaign_id !== 'undefined' )
				{
					_data.campaign_id = _campaign_id;
				}

				$.ajax({
					url: thimpress_donate.ajaxurl,
					type: 'POST',
					data: _data,
					beforeSend: function()
					{
						TP_Donate_Global.beforeAjax();
					}
				}).done( function( res ){
					TP_Donate_Global.afterAjax();

					if( typeof res.status !== 'undefined' && res.status === 'success' )
					{
						var _tmpl = wp.template( 'donate-form-template' );

						$('#donate_hidden').addClass('active').html( _tmpl(res) );

						$.magnificPopup.open({
							type: 'inline',
							items: {
								src: '#donate_hidden'
							},
							callbacks: {
								open: function() {
									var timeout = setTimeout(function(){
										$('#donate_hidden input[name="donate_input_amount"]:first').focus();
										$('#donate_hidden input[name="payment_method"]:first').attr('checked', true);
										clearTimeout(timeout);
									}, 100);
							    }
							}
				        });
					}

				});

			});

		},

		donate_submit: function()
		{
			$( document ).on( 'submit', '.donate_form', function( e ){
				e.preventDefault();

				var _form = $(this),
					_layout = _form.find( '.donate_form_layout' );

				// remove old message error
				_form.find( '.donate_form_error_messages' ).remove();
				// // donate on lightbox redirect cart, checkout form.
				// if( DONATE_Site.donate_on_lightbox() === false )
				// {
				// 	_form.submit();
				// }
				// else // donate on lightbox without cart, checkout form.
				// {
					var messages = DONATE_Site.sanitize_form_fields( _form );
					// invalid fields
					if( messages.length > 0 )
					{
						DONATE_Site.generate_messages( _layout, messages );
					}
					else
					{
						if( _form.find( 'input[name="payment_method"]:checked' ).val() === 'stripe' )
						{
							Donate_Stripe_Payment.load_form( _form );
						}
						else
						{
							// process ajax
							var _data = _form.serializeArray( _form );

							$.ajax({
								url: thimpress_donate.ajaxurl,
								type: 'POST',
								data: _data,
								beforeSend: function()
								{
									TP_Donate_Global.beforeAjax();
									// DONATE_Site.beforeAjax( _form );
								}
							}).done( function( res ){
								TP_Donate_Global.afterAjax();
								// DONATE_Site.afterAjax( _form );

								if( typeof res.status === 'undefined' )
									return;

								if( typeof res.form !== 'undefined' && typeof res.args !== 'undefined' && res.form === true )
								{
									// process with authorize.net SIM payment
									var args = res.args;
									if( Object.keys( args ).length !== 0 )
									{
										var html = [];
										html.push( '<form id="donate_form_instead" action="'+res.url+'" method="POST">' )
										$.each( args, function( name, value ){

											html.push( '<input type="hidden" name="'+name+'" value="'+value+'" />' );

										});
										html.push( '<button type="submit" class="donate-redirecting">'+res.submit_text+'</button>' );
										html.push( '</form>' );
										_form.replaceWith( html.join( '' ) );
										$('#donate_form_instead').submit();
									}
								}
								else if( res.status === 'success' && typeof res.url !== 'undefined' )
								{
									window.location.href = res.url;
								}
								else if( res.status === 'failed' && typeof res.message !== 'undefined' )
								{
									if( _layout.length === 1 )
									{
										DONATE_Site.generate_messages( _layout, res.message );
									}
								}
							});
						}
					}


				// }

				return false;

			});

		},

		donate_on_lightbox: function()
		{
			if( typeof thimpress_donate.settings !== 'undefined' &&
	        	typeof thimpress_donate.settings.checkout !== 'undefined' &&
	        	typeof thimpress_donate.settings.checkout.lightbox_checkout !== 'undefined' &&
	        	thimpress_donate.settings.checkout.lightbox_checkout === 'yes' ){

				return true;

			}

			return false;
		},

		sanitize_form_fields: function( _form )
		{
			var messages = [];
			// amount
			var _package = _form.find( 'input[name="donate_input_amount_package"]:checked' ),
				_amount = _form.find('input[name="donate_input_amount"]');

			if( typeof _package.val() === 'undefined' && _amount.val() == '' )
			{
				_amount.addClass( 'donate_input_invalid' );
				messages.push( thimpress_donate.l18n.amount_invalid );
			} else {
				_amount.removeClass( 'donate_input_invalid' );
			}

			// firstname
			var first_name = _form.find( 'input[name="first_name"]' ),
				val = first_name.val();
			if( first_name.length === 1 && ( val === '' || new RegExp('^[a-zA-Z]{3,15}$').test( val ) === false ) )
			{
				first_name.addClass( 'donate_input_invalid' );
				messages.push( thimpress_donate.l18n.first_name_invalid );
			} else {
				first_name.removeClass( 'donate_input_invalid' );
			}

			// lastname
			var last_name = _form.find( 'input[name="last_name"]' ),
				val = last_name.val();
			if( last_name.length === 1 && ( val === '' || new RegExp('^[a-zA-Z]{3,15}$').test( val ) === false ) )
			{
				last_name.addClass( 'donate_input_invalid' );
				messages.push( thimpress_donate.l18n.last_name_invalid );
			} else {
				last_name.removeClass( 'donate_input_invalid' );
			}

			// email
			var email = _form.find( 'input[name="email"]' );
			if( email.length === 1 && ( email.val() === '' || new RegExp('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$').test( email.val() ) === false ) )
			{
				email.addClass( 'donate_input_invalid' );
				messages.push( thimpress_donate.l18n.email_invalid );
			} else {
				email.removeClass( 'donate_input_invalid' );
			}

			// phone
			var phone = _form.find( 'input[name="phone"]' );
			var reges = /^\d{10}$/;
			if( phone.length === 1 && ( phone.val() === '' || reges.test( phone.val() ) === false ) )
			{
				phone.addClass( 'donate_input_invalid' );
				messages.push( thimpress_donate.l18n.phone_number_invalid );
			} else {
				phone.removeClass( 'donate_input_invalid' );
			}

			// payment method
			var payment_method = _form.find( 'input[name="payment_method"]' );
			if( payment_method.length === 1 && payment_method.val() === '' )
			{
				payment_method.addClass( 'donate_input_invalid' );
				messages.push( thimpress_donate.l18n.payment_method_invalid );
			} else {
				payment_method.removeClass( 'donate_input_invalid' );
			}

			// address
			var address = _form.find( '.address' );
			if ( address.val().trim() === '' ) {
				address.addClass( 'donate_input_invalid' );
				messages.push( thimpress_donate.l18n.address_invalid );
			} else {
				address.removeClass( 'donate_input_invalid' );
			}

			return messages;
		},

		generate_messages: function( _layout, messages )
		{
			var html = [];
			if( typeof messages === 'object' )
			{
				for( var i = 0; i < messages.length; i++ )
				{
					html.push( '<p class="donate_message_error">' + messages[i] + '</p>' );
				}
			}
			else if( typeof messages === 'string' )
			{
				html.push( '<p class="donate_message_error">' + messages + '</p>' );
			}

			_layout.prepend( '<div class="donate_form_error_messages">' + html.join( '' ) + '</div>' );
			$('.donate_form_error_messages').addClass( 'active' );
		},

		beforeAjax: function( _form )
		{
			if( typeof _form === 'undefined' )
				return;

			_form.find( '.donate_button' ).addClass( 'donate_button_processing' );
		},

		afterAjax: function( _form )
		{
			if( typeof _form === 'undefined' )
				return;

			_form.find( '.donate_button' ).removeClass( 'donate_button_processing' );
		},

		generate_percent: function()
		{
			var percents = $( '.donate_counter_percent' );
			for( var i = 0; i < percents.length; i++ )
			{
				var percent = $( percents[ i ] ),
					percent_width = percent.attr( 'data-percent' ),
					counter = percent.parent( '.donate_counter:first' ),
					counter_width = counter.outerWidth(),
					tootip = percent.find( '.donate_percent_tooltip' ),
					tootip_width = tootip.outerWidth();

				percent.css({
					width: percent_width + '%'
				});

				if( tootip_width / 2 >= percent.outerWidth() ) {
					tootip.css({
						left: 0
					});
				} else if( ( tootip_width / 2 + percent.outerWidth() ) <= counter_width ) {
					tootip.css({
						left: percent.outerWidth() - tootip_width / 2
					});
				} else if ( ( tootip_width / 2 + percent.outerWidth() ) > counter_width ) {
					tootip.css({
						left: ( counter_width - tootip_width )
					});
				}
			}
		},

	};

	$( document ).ready( function(){

		DONATE_Site.init();

	});

	$( window ).resize( function(){
		DONATE_Site.generate_percent();
	} );

	$( window ).load( function(){
		DONATE_Site.generate_percent();
	} );

})(jQuery);