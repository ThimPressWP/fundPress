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

						$('#donate_hidden').html( _tmpl(res) );

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