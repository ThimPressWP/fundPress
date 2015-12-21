(function($){

	// object js
	DONATE_Admin = {

		init: function()
		{
			this.admin_setting_tab();

			// select2 js
			$('.tp_donate_wrapper_content select').select2({
				width: 'resolve',
				dropdownAutoWidth : true
			});
		},

		// tab setting function
		admin_setting_tab: function()
		{
			$('.tp_donate_wrapper_content > div:not(:first)').hide();
			var a_tabs = $('.tp_donate_setting_wrapper .nav-tab-wrapper a');
			$( document ).on( 'click', '.tp_donate_setting_wrapper .nav-tab-wrapper a', function( e ){
				e.preventDefault();

				a_tabs.removeClass('nav-tab-active');
				var _self = $(this),
					_tab_id = _self.attr( 'data-tab' );

				_self.addClass( 'nav-tab-active' );
				$('.tp_donate_wrapper_content > div').hide();
				$( '.tp_donate_wrapper_content #'+ _tab_id ).fadeIn();


				return false;
			});
		},

	};

	// ready
	$(document).ready( function() {
		// call DONATE_Admin initialize
		DONATE_Admin.init();
	});

})(jQuery);