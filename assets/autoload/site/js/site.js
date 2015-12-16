(function($){

	var counts = $('.donate_counter');
	for( var i = 0; i < counts.length; i++ )
	{
		var time = $(counts[i]).attr( 'data-time' );
		time = new Date(time);

		$(counts[i]).countdown({until: time});
	}

})(jQuery);