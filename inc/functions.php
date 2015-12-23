<?php
if( ! function_exists( 'donate' ) )
{
	function donate()
	{
		return new ThimPress_Donate();
	}
}

if( ! function_exists( 'donate_get_template' ) )
{
	function donate_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' )
	{
		if ( $args && is_array( $args ) ) {
	        extract( $args );
	    }

	    $located = donate_locate_template( $template_name, $template_path, $default_path );

	    if ( !file_exists( $located ) ) {
	        _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
	        return;
	    }
	    // Allow 3rd party plugin filter template file from their plugin
	    $located = apply_filters( 'donate_get_template', $located, $template_name, $args, $template_path, $default_path );

	    do_action( 'donate_before_template_part', $template_name, $template_path, $located, $args );

	    include( $located );

	    do_action( 'donate_after_template_part', $template_name, $template_path, $located, $args );
	}
}

if( ! function_exists( 'donate_template_path' ) )
{
	function donate_template_path(){
	    return apply_filters( 'donate_template_path', 'tp-donate' );
	}
}

if( ! function_exists( 'donate_get_template_part' ) )
{
	function donate_get_template_part( $slug, $name = '' )
	{
		$template = '';

	    // Look in yourtheme/slug-name.php and yourtheme/courses-manage/slug-name.php
	    if ( $name ) {
	        $template = locate_template( array( "{$slug}-{$name}.php", donate_template_path() . "/{$slug}-{$name}.php" ) );
	    }

	    // Get default slug-name.php
	    if ( !$template && $name && file_exists( TP_DONATE_PATH . "/templates/{$slug}-{$name}.php" ) ) {
	        $template = TP_DONATE_PATH . "/templates/{$slug}-{$name}.php";
	    }

	    // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/courses-manage/slug.php
	    if ( !$template ) {
	        $template = locate_template( array( "{$slug}.php", donate_template_path() . "{$slug}.php" ) );
	    }

	    // Allow 3rd party plugin filter template file from their plugin
	    if ( $template ) {
	        $template = apply_filters( 'donate_get_template_part', $template, $slug, $name );
	    }
	    if ( $template && file_exists( $template ) ) {
	        load_template( $template, false );
	    }

	    return $template;
	}
}

if( ! function_exists( 'donate_locate_template' ) )
{
	function donate_locate_template( $template_name, $template_path = '', $default_path = '' )
	{

	    if ( !$template_path ) {
	        $template_path = donate_template_path();
	    }

	    if ( !$default_path ) {
	        $default_path = TP_DONATE_PATH . '/templates/';
	    }

	    $template = null;
	    // Look within passed path within the theme - this is priority
	    $template = locate_template(
            array(
                trailingslashit($template_path) . $template_name,
                $template_name
            )
        );
	    // Get default template
	    if ( !$template ) {
	        $template = $default_path . $template_name;
	    }

	    // Return what we found
	    return apply_filters( 'donate_locate_template', $template, $template_name, $template_path );
	}
}

if ( ! function_exists( 'is_event_taxonomy' ) ) {

    /**
     * Returns true when viewing a room taxonomy archive.
     * @return bool
     */
    function is_event_taxonomy() {
        return is_tax( get_object_taxonomies( 'tp_event' ) );
    }
}

/**
 * template hook function
 */
add_filter( 'the_content', 'donate_the_content' );
if( ! function_exists( 'donate_the_content' ) )
{
	function donate_the_content( $content )
	{
		return do_shortcode( $content );
	}
}

add_filter( 'the_post', 'donate_add_property_countdown' );
if( ! function_exists( 'donate_add_property_countdown' ) )
{
	/**
	 * add property inside the loop
	 * @param  [type] $post [description]
	 * @return [type]       [description]
	 */
	function donate_add_property_countdown( $post )
	{
		if( $post->post_type !== 'tp_event' )
			return $post;

		$date_start = get_post_meta( $post->ID, 'donate_date_start', true );
		$time_start = get_post_meta( $post->ID, 'donate_time_start', true );
		if( $date_start && $time_start )
		{
			$start = $date_start . ' ' . $time_start;
			$post->event_start = date( 'Y-m-d H:i:s', strtotime($start) );
		}
		else
		{
			$post->event_start = null;
		}

		$date_end = get_post_meta( $post->ID, 'donate_date_end', true );
		$time_end = get_post_meta( $post->ID, 'donate_time_end', true );
		if( $date_end && $time_end )
		{
			$end = $date_end . ' ' . $time_end;
			$post->event_end = date( 'Y-m-d H:i:s', strtotime($end) );
		}
		else
		{
			$post->event_end = null;
		}

		return $post;
	}

	/**
	 * get event start datetime
	 * @param  string $format [description]
	 * @return [type]         [description]
	 */
	function donate_start( $format = 'Y-m-d H:i:s' )
	{
		$post = get_post();
		if( ! $post->event_start )
			return null;

		return date( $format, strtotime( $post->event_start ) );
	}

	/**
	 * get event end datetime same as function
	 * @param  string $format [description]
	 * @return [type]         [description]
	 */
	function donate_end( $format = 'Y-m-d H:i:s' )
	{
		$post = get_post();
		if( ! $post->event_end )
			return null;

		return date( $format, strtotime( $post->event_end ) );
	}

}

add_action( 'donate_before_main_content', 'donate_before_main_content' );
if( ! function_exists( 'donate_before_main_content' ) )
{
	function donate_before_main_content()
	{

	}
}

add_action( 'donate_after_main_content', 'donate_after_main_content' );
if( ! function_exists( 'donate_after_main_content' ) )
{
	function donate_after_main_content()
	{

	}
}

add_action( 'donate_before_single_event', 'donate_before_single_event' );
if( ! function_exists( 'donate_before_single_event' ) )
{
	function donate_before_single_event()
	{

	}
}

add_action( 'donate_after_single_event', 'donate_after_single_event' );
if( ! function_exists( 'donate_after_single_event' ) )
{
	function donate_after_single_event()
	{

	}
}

/*template hook*/
add_action( 'donate_single_event_title', 'donate_single_event_title' );
if( ! function_exists( 'donate_single_event_title' ) )
{
	function donate_single_event_title()
	{
		donate_get_template('loop/title.php');
	}
}

add_action( 'donate_single_event_thumbnail', 'donate_single_event_thumbnail' );
if( ! function_exists( 'donate_single_event_thumbnail' ) )
{
	function donate_single_event_thumbnail()
	{
		donate_get_template('loop/thumbnail.php');
	}
}

add_action( 'donate_loop_event_countdown', 'donate_loop_event_countdown' );
if( ! function_exists( 'donate_loop_event_countdown' ) )
{
	function donate_loop_event_countdown()
	{
		donate_get_template('loop/countdown.php');
	}
}

add_action( 'donate_single_event_content', 'donate_single_event_content' );
if( ! function_exists( 'donate_single_event_content' ) )
{
	function donate_single_event_content()
	{
		donate_get_template('loop/content.php');
	}
}

if( ! function_exists( 'donate_get_currencies' ) )
{
	function donate_get_currencies()
	{
		$currencies = array(
				'AED' => 'United Arab Emirates Dirham (د.إ)',
				'AUD' => 'Australian Dollars ($)',
				'BDT' => 'Bangladeshi Taka (৳&nbsp;)',
				'BRL' => 'Brazilian Real (R$)',
				'BGN' => 'Bulgarian Lev (лв.)',
				'CAD' => 'Canadian Dollars ($)',
				'CLP' => 'Chilean Peso ($)',
				'CNY' => 'Chinese Yuan (¥)',
				'COP' => 'Colombian Peso ($)',
				'CZK' => 'Czech Koruna (Kč)',
				'DKK' => 'Danish Krone (kr.)',
				'DOP' => 'Dominican Peso (RD$)',
				'EUR' => 'Euros (€)',
				'HKD' => 'Hong Kong Dollar ($)',
				'HRK' => 'Croatia kuna (Kn)',
				'HUF' => 'Hungarian Forint (Ft)',
				'ISK' => 'Icelandic krona (Kr.)',
				'IDR' => 'Indonesia Rupiah (Rp)',
				'INR' => 'Indian Rupee (Rs.)',
				'NPR' => 'Nepali Rupee (Rs.)',
				'ILS' => 'Israeli Shekel (₪)',
				'JPY' => 'Japanese Yen (¥)',
				'KIP' => 'Lao Kip (₭)',
				'KRW' => 'South Korean Won (₩)',
				'MYR' => 'Malaysian Ringgits (RM)',
				'MXN' => 'Mexican Peso ($)',
				'NGN' => 'Nigerian Naira (₦)',
				'NOK' => 'Norwegian Krone (kr)',
				'NZD' => 'New Zealand Dollar ($)',
				'PYG' => 'Paraguayan Guaraní (₲)',
				'PHP' => 'Philippine Pesos (₱)',
				'PLN' => 'Polish Zloty (zł)',
				'GBP' => 'Pounds Sterling (£)',
				'RON' => 'Romanian Leu (lei)',
				'RUB' => 'Russian Ruble (руб.)',
				'SGD' => 'Singapore Dollar ($)',
				'ZAR' => 'South African rand (R)',
				'SEK' => 'Swedish Krona (kr)',
				'CHF' => 'Swiss Franc (CHF)',
				'TWD' => 'Taiwan New Dollars (NT$)',
				'THB' => 'Thai Baht (฿)',
				'TRY' => 'Turkish Lira (₺)',
				'USD' => 'US Dollars ($)',
				'VND' => 'Vietnamese Dong (₫)',
				'EGP' => 'Egyptian Pound (EGP)'
			);
		return apply_filters( 'donate_currencies', $currencies );
	}
}

if( ! function_exists( 'donate_get_currency' ) )
{

	function donate_get_currency()
	{
		return DN_Setting::instance()->general->get( 'currency', 'USD' );
	}
}

/**
 * Get Currency symbol.
 * @param string $currency (default: '')
 * @return string
 */
if( ! function_exists( 'donate_get_currency_symbol' ) )
{

	function donate_get_currency_symbol( $currency = '' ) {
		if ( ! $currency ) {
			$currency = donate_get_currency();
		}

		switch ( $currency ) {
			case 'AED' :
				$currency_symbol = 'د.إ';
				break;
			case 'AUD' :
			case 'ARS' :
			case 'CAD' :
			case 'CLP' :
			case 'COP' :
			case 'HKD' :
			case 'MXN' :
			case 'NZD' :
			case 'SGD' :
			case 'USD' :
				$currency_symbol = '&#36;';
				break;
			case 'BDT':
				$currency_symbol = '&#2547;&nbsp;';
				break;
			case 'BGN' :
				$currency_symbol = '&#1083;&#1074;.';
				break;
			case 'BRL' :
				$currency_symbol = '&#82;&#36;';
				break;
			case 'CHF' :
				$currency_symbol = '&#67;&#72;&#70;';
				break;
			case 'CNY' :
			case 'JPY' :
			case 'RMB' :
				$currency_symbol = '&yen;';
				break;
			case 'CZK' :
				$currency_symbol = '&#75;&#269;';
				break;
			case 'DKK' :
				$currency_symbol = 'DKK';
				break;
			case 'DOP' :
				$currency_symbol = 'RD&#36;';
				break;
			case 'EGP' :
				$currency_symbol = 'EGP';
				break;
			case 'EUR' :
				$currency_symbol = '&euro;';
				break;
			case 'GBP' :
				$currency_symbol = '&pound;';
				break;
			case 'HRK' :
				$currency_symbol = 'Kn';
				break;
			case 'HUF' :
				$currency_symbol = '&#70;&#116;';
				break;
			case 'IDR' :
				$currency_symbol = 'Rp';
				break;
			case 'ILS' :
				$currency_symbol = '&#8362;';
				break;
			case 'INR' :
				$currency_symbol = 'Rs.';
				break;
			case 'ISK' :
				$currency_symbol = 'Kr.';
				break;
			case 'KIP' :
				$currency_symbol = '&#8365;';
				break;
			case 'KRW' :
				$currency_symbol = '&#8361;';
				break;
			case 'MYR' :
				$currency_symbol = '&#82;&#77;';
				break;
			case 'NGN' :
				$currency_symbol = '&#8358;';
				break;
			case 'NOK' :
				$currency_symbol = '&#107;&#114;';
				break;
			case 'NPR' :
				$currency_symbol = 'Rs.';
				break;
			case 'PHP' :
				$currency_symbol = '&#8369;';
				break;
			case 'PLN' :
				$currency_symbol = '&#122;&#322;';
				break;
			case 'PYG' :
				$currency_symbol = '&#8370;';
				break;
			case 'RON' :
				$currency_symbol = 'lei';
				break;
			case 'RUB' :
				$currency_symbol = '&#1088;&#1091;&#1073;.';
				break;
			case 'SEK' :
				$currency_symbol = '&#107;&#114;';
				break;
			case 'THB' :
				$currency_symbol = '&#3647;';
				break;
			case 'TRY' :
				$currency_symbol = '&#8378;';
				break;
			case 'TWD' :
				$currency_symbol = '&#78;&#84;&#36;';
				break;
			case 'UAH' :
				$currency_symbol = '&#8372;';
				break;
			case 'VND' :
				$currency_symbol = '&#8363;';
				break;
			case 'ZAR' :
				$currency_symbol = '&#82;';
				break;
			default :
				$currency_symbol = '';
				break;
		}

		return apply_filters( 'donate_currency_symbol', $currency_symbol, $currency );
	}
}

/**
 * format price
 */
if( ! function_exists( 'donate_price' ) )
{

	function donate_price( $price )
	{
		if( ! is_numeric( $price ) ) return;

		$price = number_format( $price, donate_currency_decimal(), donate_currency_thousand(), donate_currency_separator() );

		$position = donate_currency_position();
		$symbol = donate_get_currency_symbol();
		switch ( $position ) {
			case 'left':
				$price = $symbol . $price;
				break;

			case 'right':
				$price = $price . $symbol;
				break;

			case 'left_space':
				$price = $symbol . ' ' . $price;
				break;

			case 'right_space':
				$price = $price . ' ' . $symbol;
				break;

			default:
				$price = $symbol . $price;
				break;
		}
		return apply_filters( 'donate_price', $price );
	}

}

/**
 * currency position format
 */
if( ! function_exists( 'donate_currency_position' ) )
{

	function donate_currency_position()
	{
		return apply_filters( 'donate_currency_position', DN_Setting::instance()->general->get( 'currency_position', 'left' ) );
	}

}

/**
 * currency thousand format
 */
if( ! function_exists( 'donate_currency_thousand' ) )
{

	function donate_currency_thousand( $price )
	{
		return apply_filters( 'donate_currency_thousand', DN_Setting::instance()->general->get( 'currency_thousand', ',' ) );
	}

}

/**
 * currency separator format
 */
if( ! function_exists( 'donate_currency_separator' ) )
{

	function donate_currency_separator( $price )
	{
		return apply_filters( 'donate_currency_separator', DN_Setting::instance()->general->get( 'currency_separator', '.' ) );
	}

}

/**
 * currency separator format
 */
if( ! function_exists( 'donate_currency_decimal' ) )
{

	function donate_currency_decimal( $price )
	{
		return apply_filters( 'donate_currency_decimal', DN_Setting::instance()->general->get( 'currency_num_decimal', 2 ) );
	}

}

/**
 * get list pages
 */
if( ! function_exists( 'donate_get_pages_setting' ) )
{
	function donate_get_pages_setting()
	{
		$pages = array();

		$pages[] = __( '--- Select page ---', 'tp-donate' );
		$list = get_all_page_ids();
		foreach ( $list as $key => $id ) {
			$pages[ $id ] = get_the_title( $id );
		}
		return apply_filters( 'donate_all_page', $pages );
	}
}
