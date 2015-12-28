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
						DONATE_Site.beforeAjax();
					}
				}).done( function( res ){

					DONATE_Site.afterAjax();

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

				var _form = $(this);

				// remove old message error
				_form.find( '.donate_message_error' ).remove();
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
						var html = [];
						for( var i = 0; i < messages.length; i++ )
						{
							html.push( '<p class="donate_message_error">' + messages[i] + '</p>' );
						}

						$( '.donate_form_footer' ).prepend( html.join( '' ) );
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
								DONATE_Site.beforeAjax();
							}
						}).done( function( res ){
							DONATE_Site.afterAjax();

							if( typeof res.status === 'undefined' )
								return;

							if( res.status === 'success' && typeof res.url !== 'undefined' )
							{
								window.location.href = res.url;
							}
							else if( res.status === 'failed' && typeof res.messages !== 'undefined' )
							{
								$( '.donate_form_footer .donate_message_error' ).remove();
								$( '.donate_form_footer .donate_message_error' ).append( '<p class="donate_message_error">' + res.messages + '</p>' );
							}
						});
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
			if( _form.find( 'input[name="donate_input_amount_package"]' ).val() === '' && _form.find('input[name="donate_input_amount"]').val() === '' )
			{
				messages.push( thimpress_donate.l18n.amount_invalid );
			}

			// firstname
			var first_name = _form.find( 'input[name="first_name"]' ).val();
			if( first_name === '' || _form.find('.first_name').val() === '' )
			{
				messages.push( thimpress_donate.l18n.first_name_invalid );
			}

			// lastname
			var last_name = _form.find( 'input[name="last_name"]' ).val();
			if( last_name === '' )
			{
				messages.push( thimpress_donate.l18n.last_name_invalid );
			}

			// email
			var email = _form.find( 'input[name="email"]' );
			if( email.length != 0 && ( email.val() === '' || new RegExp('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$').test( email.val() ) === false ) )
			{
				messages.push( thimpress_donate.l18n.email_invalid );
			}

			// phone
			var phone = _form.find( 'input[name="phone"]' );
			var reges = /^\d{10}$/;
			if( phone.length != 0 && ( phone.val() === '' || reges.test( phone.val() ) === false ) )
			{
				messages.push( thimpress_donate.l18n.phone_number_invalid );
			}

			// payment method
			var payment_method = _form.find( 'input[name="payment_method"]' );
			if( payment_method.length != 0 && payment_method.val() === '' )
			{
				messages.push( thimpress_donate.l18n.payment_method_invalid );
			}

			return messages;
		},

		beforeAjax: function()
		{

		},

		afterAjax: function()
		{

		},

	};

	$( document ).ready( function(){

		DONATE_Site.init();

	});

})(jQuery);