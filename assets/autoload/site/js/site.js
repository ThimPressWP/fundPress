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
			this.generate_percent();

			// donate system anywhere
			this.donate_system_form();
		},

		donate_system_form: function()
		{

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
			$( document ).on( 'click', '.donate_load_form', function(event){
				event.preventDefault();

				var _self = $(this),
					_campaign_id = _self.attr( 'data-campaign-id' );

				$.ajax({
					url: thimpress_donate.ajaxurl,
					type: 'POST',
					data: {
						action: 'donate_load_form',
						nonce: thimpress_donate.nonce,
						campaign_id: _campaign_id
					},
					beforeSend: function()
					{
						TP_Donate_Global.beforeAjax();
						// DONATE_Site.beforeAjax();
					}
				}).done( function( res ){
					TP_Donate_Global.afterAjax();
					// DONATE_Site.afterAjax();

					if( typeof res.status !== 'undefined' && res.status === 'success' )
					{
						var _tmpl = wp.template( 'donate-form-template' );

						$('#donate_hidden').addClass('active').html( _tmpl(res) );

						$.magnificPopup.open({
							type: 'inline',
							items: {
								src: '#donate_hidden'
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

								if( res.status === 'success' && typeof res.url !== 'undefined' )
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
				messages.push( thimpress_donate.l18n.amount_invalid );
			}

			// firstname
			var first_name = _form.find( 'input[name="first_name"]' ),
				val = first_name.val();
			if( first_name.length === 1 && ( val === '' || new RegExp('^[a-zA-Z]{3,15}$').test( val ) === false ) )
			{
				messages.push( thimpress_donate.l18n.first_name_invalid );
			}

			// lastname
			var last_name = _form.find( 'input[name="last_name"]' ),
				val = last_name.val();
			if( last_name.length === 1 && ( val === '' || new RegExp('^[a-zA-Z]{3,15}$').test( val ) === false ) )
			{
				messages.push( thimpress_donate.l18n.last_name_invalid );
			}

			// email
			var email = _form.find( 'input[name="email"]' );
			if( email.length === 1 && ( email.val() === '' || new RegExp('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$').test( email.val() ) === false ) )
			{
				messages.push( thimpress_donate.l18n.email_invalid );
			}

			// phone
			var phone = _form.find( 'input[name="phone"]' );
			var reges = /^\d{10}$/;
			if( phone.length === 1 && ( phone.val() === '' || reges.test( phone.val() ) === false ) )
			{
				messages.push( thimpress_donate.l18n.phone_number_invalid );
			}

			// payment method
			var payment_method = _form.find( 'input[name="payment_method"]' );
			if( payment_method.length === 1 && payment_method.val() === '' )
			{
				messages.push( thimpress_donate.l18n.payment_method_invalid );
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

		afterAjax: function( _form)
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
				var percent = $( percents[ i ] );
				percent.css({
					width: percent.attr( 'data-percent' ) + '%'
				})
			}
		},

	};

	$( document ).ready( function(){

		DONATE_Site.init();

	});

})(jQuery);