<?php

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
 * donate payments
 */
if ( ! function_exists( 'donate_payment_gateways' ) )
{
	function donate_payment_gateways()
	{
		return apply_filters( 'donate_payment_gateways', array() );
	}
}
/**
 * donate payment is enable
 */
if ( ! function_exists( 'donate_payments_enable' ) )
{
	function donate_payments_enable()
	{
		return apply_filters( 'donate_payment_gateways_enable', array() );
	}
}

if( ! function_exists( 'donate_get_currencies' ) )
{
	/**
	 * donate_get_currencies
	 * @return array currencies
	 */
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
	/**
	 * donate_get_currency
	 * @return donate_get_currency
	 */
	function donate_get_currency()
	{
		return DN_Settings::instance()->general->get( 'currency', 'USD' );
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

	function donate_price( $price, $currency = null )
	{
		if( ! is_numeric( $price ) ) return;

		$price = number_format( $price, donate_currency_decimal(), donate_currency_thousand(), donate_currency_separator() );

		$position = donate_currency_position();
		$symbol = donate_get_currency_symbol( $currency );
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
		return apply_filters( 'donate_currency_position', DN_Settings::instance()->general->get( 'currency_position', 'left' ) );
	}

}

/**
 * currency thousand format
 */
if( ! function_exists( 'donate_currency_thousand' ) )
{

	function donate_currency_thousand()
	{
		return apply_filters( 'donate_currency_thousand', DN_Settings::instance()->general->get( 'currency_thousand', ',' ) );
	}

}

/**
 * currency separator format
 */
if( ! function_exists( 'donate_currency_separator' ) )
{

	function donate_currency_separator()
	{
		return apply_filters( 'donate_currency_separator', DN_Settings::instance()->general->get( 'currency_separator', '.' ) );
	}

}

/**
 * currency separator format
 */
if( ! function_exists( 'donate_currency_decimal' ) )
{

	function donate_currency_decimal()
	{
		return apply_filters( 'donate_currency_decimal', DN_Settings::instance()->general->get( 'currency_num_decimal', 2 ) );
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

/**
 * donate redirect
 */
if( ! function_exists( 'donate_redirect_url' ) )
{

	function donate_redirect_url()
	{
		$rediect = DN_Settings::instance()->checkout->get( 'donate_redirect', 'checkout' );

		if( $rediect === 'checkout' )
		{
			return donate_checkout_url();
		}
		else if( $rediect === 'cart' )
		{
			return donate_cart_url();
		}
	}

}

// checkout url
if( ! function_exists( 'donate_checkout_url' ) )
{
	function donate_checkout_url()
	{
		return get_permalink( DN_Settings::instance()->checkout->get( 'checkout_page', 1 ) );
	}
}

// cart url
if( ! function_exists( 'donate_cart_url' ) )
{
	function donate_cart_url()
	{
		return get_permalink( DN_Settings::instance()->checkout->get( 'cart_page', 1 ) );
	}
}
// term & conditions url
if( ! function_exists( 'donate_term_condition_url' ) )
{
	function donate_term_condition_url()
	{
		$page_id = DN_Settings::instance()->checkout->get( 'term_condition_page', 1 );

		if( ! $page_id ) return;

		return get_permalink( $page_id );
	}
}

/**
 * convert amount campaigns
 */
if( ! function_exists( 'donate_campaign_convert_amount' ) )
{

	/**
	 * donate_campaign_convert_amount
	 * @param  integer $amount   amount of campaign
	 * @param  string  $currency currency  of campaign
	 * @return integer $amount
	 */
	function donate_campaign_convert_amount( $amount = 1, $from = '', $to = '' )
	{

		// currency setting
		if( ! $to )
		{
			$to = donate_get_currency();
		}

		if( ! $from || $from === $to )
			return $amount;

		$name = 'donate_rate_' . $from . '_' . $to;

		if( false === ( $rate = get_transient( $name ) ) )
		{
			$type = DN_Settings::instance()->general->get( 'aggregator', 'yahoo' );

			switch ( $type ) {
				case 'yahoo':
	                $yql_query = 'select * from yahoo.finance.xchange where pair in ("' . $from . $to. '")';

	                $url = 'http://query.yahooapis.com/v1/public/yql?q='. urlencode($yql_query);
	                $url .= "&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys";

	                if ( function_exists('curl_init') ) {
	                    $res = donate_curl_get($url);
	                } else {
	                    $res = file_get_contents($url);
	                }

	                //***
	                $results = json_decode($res, true);
	                $rate = (float) $results['query']['results']['rate']['Rate'];

					break;

				case 'google':
					# code...
					$amount = urlencode(1);
	                $from_Currency = urlencode( $from );
	                $to_Currency = urlencode( $to );
	                $url = "http://www.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency";

	                if ( function_exists('curl_init') ) {
	                    $html = donate_curl_get($url);
	                } else {
	                    $html = file_get_contents($url);
	                }

	                preg_match_all('/<span class=bld>(.*?)<\/span>/s', $html, $matches);

	                if ( isset($matches[1][0]) ) {
	                    $rate = floatval($matches[1][0]);
	                } else {
	                    $rate = sprintf( __("no data for %s", 'tp-hotel-booking'), $to );
	                }
					break;

				default:
					$rate = 1;
					break;
			}

			set_transient( $name, $rate, 12 * HOUR_IN_SECONDS );
		}

		return round( $amount * $rate, donate_currency_decimal() );

	}

	/**
	 * get rate of currency
	 * @param  string $from
	 * @param  string $to
	 * @return rate
	 */
	function donate_curl_get( $url )
	{
		$ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
	}

}

if( ! function_exists( 'donate_find_compensate_by_amount' ) )
{
	/**
	 * fint compensate by amount donate
	 * @param $campaign
	 * @param  integer $amount
	 * @return string
	 */
	function donate_find_compensate_by_amount( $campaign = null, $amount = 0 )
	{
		if( $amount === 0 )
			return '';

		$campaign = DN_Campaign::instance( $campaign );
		$compensates = $campaign->get_compensate();

		if( ! $compensates )
			return '';

		$desc = '';
		$prev = 0;
		foreach ( $compensates as $key => $compensate ) {
			if( $compensate[ 'amount' ] && $amount >= $compensate[ 'amount' ] && $compensate[ 'amount' ] > $prev )
			{
				$desc = $compensate['desc'];
				$prev = $compensate[ 'amount' ];
			}
		}

		return $desc;

	}

}

/**
 * generate post key
 */
if( ! function_exists( 'donate_generate_post_key' ) )
{
	function donate_generate_post_key( $post_id )
	{
		return '#'.$post_id;
	}
}

/**
 * convert array to string
 */
if( ! function_exists( 'donate_array_to_string' ) )
{
	function donate_array_to_string( $param )
	{
		$html = array();
		foreach ( $param as $key => $value ) {
			if( is_array( $value ) )
			{
				$html[] = donate_array_to_string( $value );
			}
			else
			{
				$html[] = $key . $value;
			}
		}
		return implode( '', $html );
	}
}

if( ! function_exists( 'donate_setcookie' ) )
{
	// setcookie
	function donate_setcookie( $name, $value, $expire = 0, $secure = false ) {
		if ( ! headers_sent() ) {
			setcookie( $name, $value, $expire, COOKIEPATH, COOKIE_DOMAIN, $secure );
		} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			headers_sent( $file, $line );
			trigger_error( "{$name} cookie cannot be set - headers already sent by {$file} on line {$line}", E_USER_NOTICE );
		}
	}
}

if( ! function_exists( 'donate_add_notice' ) )
{
	function donate_add_notice( $notice_key = null, $message = null )
	{
		if( ! $notice_key || ! $message )
			return;

		if( ! isset( $_SESSION[ 'donate_messages' ] ) )
		{
			$_SESSION[ 'donate_messages' ] = array();
			$_SESSION[ 'donate_messages' ][ 'errors' ] = array();
			$_SESSION[ 'donate_messages' ][ 'success' ] = array();
		}

		if( $notice_key === 'errors' )
		{
			$_SESSION[ 'donate_messages' ][ 'errors' ][] = sprintf( '%s', $message );
		}
		else
		{
			$_SESSION[ 'donate_messages' ][ 'success' ][] = sprintf( '%s', $message );
		}

	}
}

if( ! function_exists( 'donate_has_notice' ) )
{
	function donate_has_notice( $name = null )
	{
		if( empty( $_SESSION[ 'donate_messages' ] ) )
			return false;

		if( isset( $_SESSION[ 'donate_messages' ][ $name ] ) )
			return true;
	}
}

/**
 * show message
 */
if( ! function_exists( 'donate_notice_display' ) )
{
	function donate_notice_display()
	{
		if( empty( $_SESSION[ 'donate_messages' ] ) )
			return;

		if( isset( $_SESSION[ 'donate_messages' ] ) )
		{
			donate_get_template( 'messages.php', array( 'messages' => $_SESSION[ 'donate_messages' ] ) );
			unset( $_SESSION[ 'donate_messages' ] );
		}
	}

}

/**
 * get status
 */
if( ! function_exists( 'donate_get_status' ) )
{
	function donate_get_status( $post_id )
	{
		$status = array(
				'donate-pending'	=> __( 'Pending', 'tp-donate' ),
				'donate-processing'	=> __( 'Processing', 'tp-donate' ),
				'donate-completed'	=> __( 'Completed', 'tp-donate' )
			);

		return apply_filters( 'donate_get_status', $status );
	}
}

if( ! function_exists( 'donate_amount_system' ) )
{
	/**
	 * donate_amount_system
	 * @return total donate amount for system without campaign
	 */
	function donate_amount_system()
	{
		global $wpdb;

		$query = $wpdb->prepare("
				SELECT donate_system.meta_value AS amount, donate_cyrrency.meta_value AS currency
				FROM $wpdb->postmeta AS donate_system
				INNER JOIN $wpdb->posts AS donation ON donation.ID = donate_system.post_id
				INNER JOIN $wpdb->postmeta AS donate_cyrrency ON donate_cyrrency.post_id = donation.ID
				WHERE
					donation.post_type = %s
					AND donation.post_status = %s
					AND donate_system.meta_key = %s
					AND donate_cyrrency.meta_key = %s
				HAVING amount > 0
			", 'dn_donate', 'donate-completed', TP_DONATE_META_DONATE . 'amount_system', TP_DONATE_META_DONATE . 'currency' );

		if( $results = $wpdb->get_results( $query ) )
		{
			$total = 0;
			foreach ( $results as $key => $donate ) {

				if( ! $donate->amount )
					continue;

				$currency = $donate->currency;
				if( ! $currency )
					$currency = donate_get_currency();

				$total = $total + donate_campaign_convert_amount( $donate->amount, $currency );
			}
			return $total;
		}
	}
}