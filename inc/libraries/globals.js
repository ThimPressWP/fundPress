;(function($){

	// global
	TP_Donate_Global = {

		beforeAjax: function()
		{
			$( '.donate_ajax_overflow' ).addClass( 'active' );
		},

		afterAjax: function()
		{
			$( '.donate_ajax_overflow' ).removeClass( 'active' );
		},

	}

})(jQuery);