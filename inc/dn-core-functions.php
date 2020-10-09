<?php
/**
 * Fundpress core functions.
 *
 * @version     2.0
 * @package     Function
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! function_exists( 'donate_get_template' ) ) {

	function donate_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		if ( $args && is_array( $args ) ) {
			extract( $args );
		}

		$located = donate_locate_template( $template_name, $template_path, $default_path );

		if ( ! file_exists( $located ) ) {
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

if ( ! function_exists( 'donate_template_path' ) ) {

	function donate_template_path() {
		return apply_filters( 'donate_template_path', 'fundpress' );
	}

}

if ( ! function_exists( 'donate_get_template_part' ) ) {

	function donate_get_template_part( $slug, $name = '' ) {
		$template = '';

		// Look in yourtheme/slug-name.php and yourtheme/courses-manage/slug-name.php
		if ( $name ) {
			$template = locate_template( array(
				"{$slug}-{$name}.php",
				donate_template_path() . "/{$slug}-{$name}.php"
			) );
		}

		// Get default slug-name.php
		if ( ! $template && $name && file_exists( FUNDPRESS_TEMP . "{$slug}-{$name}.php" ) ) {
			$template = FUNDPRESS_TEMP . "{$slug}-{$name}.php";
		}

		// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/courses-manage/slug.php
		if ( ! $template ) {
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

if ( ! function_exists( 'donate_locate_template' ) ) {

	function donate_locate_template( $template_name, $template_path = '', $default_path = '' ) {

		if ( ! $template_path ) {
			$template_path = donate_template_path();
		}

		if ( ! $default_path ) {
			$default_path = FUNDPRESS_TEMP;
		}

		$template = null;
		// Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name
			)
		);
		// Get default template
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		// Return what we found
		return apply_filters( 'donate_locate_template', $template, $template_name, $template_path );
	}

}

if ( ! function_exists( 'donate_payment_gateways' ) ) {
	/**
	 * Get donate payments.
	 *
	 * @return mixed
	 */
	function donate_payment_gateways() {
		return FP()->payment_gateways->load_payment_gateways();
	}

}

if ( ! function_exists( 'fundpress_payments_enable' ) ) {
	/**
	 * Get donate payment enable.
	 */
	function fundpress_payments_enable() {
		return FP()->payment_gateways->get_payment_available();
	}
}

if ( ! function_exists( 'donate_get_currencies' ) ) {

	/**
	 * donate_get_currencies
	 * @return array currencies
	 */
	function donate_get_currencies() {
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

if ( ! function_exists( 'donate_get_currency' ) ) {

	/**
	 * donate_get_currency
	 * @return donate_get_currency
	 */
	function donate_get_currency() {
		return FP()->settings->general->get( 'currency', 'USD' );
	}

}

/**
 * Get Currency symbol.
 *
 * @param string $currency (default: '')
 *
 * @return string
 */
if ( ! function_exists( 'donate_get_currency_symbol' ) ) {

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
if ( ! function_exists( 'donate_price' ) ) {

	function donate_price( $price, $currency = null ) {
		if ( ! is_numeric( $price ) ) {
			return;
		}

		$price = number_format( $price, donate_currency_decimal(), donate_currency_separator(), donate_currency_thousand() );

		$position = donate_currency_position();
		$symbol   = donate_get_currency_symbol( $currency );
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
if ( ! function_exists( 'donate_currency_position' ) ) {

	function donate_currency_position() {
		return apply_filters( 'donate_currency_position', FP()->settings->general->get( 'currency_position', 'left' ) );
	}

}

/**
 * currency thousand format
 */
if ( ! function_exists( 'donate_currency_thousand' ) ) {

	function donate_currency_thousand() {
		return apply_filters( 'donate_currency_thousand', FP()->settings->general->get( 'currency_thousand', ',' ) );
	}

}

/**
 * currency separator format
 */
if ( ! function_exists( 'donate_currency_separator' ) ) {

	function donate_currency_separator() {
		return apply_filters( 'donate_currency_separator', FP()->settings->general->get( 'currency_separator', '.' ) );
	}

}

/**
 * currency separator format
 */
if ( ! function_exists( 'donate_currency_decimal' ) ) {

	function donate_currency_decimal() {
		return apply_filters( 'donate_currency_decimal', FP()->settings->general->get( 'currency_num_decimal', 2 ) );
	}

}

/**
 * get list pages
 */
if ( ! function_exists( 'donate_get_pages_setting' ) ) {

	function donate_get_pages_setting() {
		$pages = array();

		$pages[] = __( '--- Select page ---', 'fundpress' );
		$list    = get_all_page_ids();
		foreach ( $list as $key => $id ) {
			$pages[ $id ] = get_the_title( $id );
		}

		return apply_filters( 'donate_all_page', $pages );
	}

}

/**
 * donate redirect
 */
if ( ! function_exists( 'donate_redirect_url' ) ) {

	function donate_redirect_url() {
		$rediect = FP()->settings->checkout->get( 'donate_redirect', 'checkout' );

		if ( $rediect === 'checkout' ) {
			return donate_checkout_url();
		} else if ( $rediect === 'cart' ) {
			return donate_cart_url();
		}
	}

}

// checkout url
if ( ! function_exists( 'donate_checkout_url' ) ) {

	function donate_checkout_url() {
		return get_permalink( FP()->settings->checkout->get( 'checkout_page', 1 ) );
	}

}

// cart url
if ( ! function_exists( 'donate_cart_url' ) ) {

	function donate_cart_url() {
		return get_permalink( FP()->settings->checkout->get( 'cart_page', 1 ) );
	}

}
// term & conditions url
if ( ! function_exists( 'donate_term_condition_url' ) ) {

	function donate_term_condition_url() {
		$page_id = FP()->settings->checkout->get( 'term_condition_page', 1 );

		if ( ! $page_id ) {
			return;
		}

		return get_permalink( $page_id );
	}

}

/**
 * convert amount campaigns
 */
if ( ! function_exists( 'donate_campaign_convert_amount' ) ) {

	/**
	 * donate_campaign_convert_amount
	 *
	 * @param  integer $amount amount of campaign
	 * @param  string $currency currency  of campaign
	 *
	 * @return integer $amount
	 */
	function donate_campaign_convert_amount( $amount = 1, $from = '', $to = '' ) {

		// currency setting
		if ( ! $to ) {
			$to = donate_get_currency();
		}

		if ( ! $from || $from === $to ) {
			return $amount;
		}

		$name = 'donate_rate_' . $from . '_' . $to;

		if ( false === ( $rate = get_transient( $name ) ) ) {
			// disable convert currency by yahoo api
			// $type = FP()->settings->general->get('aggregator', 'yahoo');

			$type = 'google';

			switch ( $type ) {
				case 'yahoo':
					$yql_query = 'select * from yahoo.finance.xchange where pair in ("' . $from . $to . '")';

					$url = 'http://query.yahooapis.com/v1/public/yql?q=' . urlencode( $yql_query );
					$url .= "&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys";

					if ( function_exists( 'curl_init' ) ) {
						$res = donate_curl_get( $url );
					} else {
						$res = file_get_contents( $url );
					}

					//***
					$results = json_decode( $res, true );
					$rate    = (float) $results['query']['results']['rate']['Rate'];
					break;

				case 'google':
					# code...
					$a             = urlencode( 1 );
					$from_Currency = urlencode( $from );
					$to_Currency   = urlencode( $to );
					$url           = "http://www.google.com/finance/converter?a=$a&from=$from_Currency&to=$to_Currency";

					if ( function_exists( 'curl_init' ) ) {
						$html = donate_curl_get( $url );
					} else {
						$html = file_get_contents( $url );
					}

					preg_match_all( '/<span class=bld>(.*?)<\/span>/s', $html, $matches );
					if ( isset( $matches[1], $matches[1][0] ) ) {
						$rate = floatval( $matches[1][0] );
					} else {
						$rate = sprintf( __( "no data for %s", 'fundpress' ), $to );
					}
					break;

				default:
					$rate = 1;
					break;
			}

			set_transient( $name, $rate, 12 * HOUR_IN_SECONDS );
		}

		if ( $rate == 0 ) {
			delete_transient( $name );

			$rate = 1;
		}

		return round( $amount * $rate, donate_currency_decimal() );
	}

	/**
	 * get rate of currency
	 *
	 * @param  string $from
	 * @param  string $to
	 *
	 * @return rate
	 */
	function donate_curl_get( $url ) {
            $response = wp_remote_get($url );
            $data    = wp_remote_retrieve_body( $response );
            return $data;
	}

}

if ( ! function_exists( 'donate_find_compensate_by_amount' ) ) {

	/**
	 * fint compensate by amount donate
	 *
	 * @param $campaign
	 * @param  integer $amount
	 *
	 * @return string
	 */
	function donate_find_compensate_by_amount( $campaign = null, $amount = 0 ) {
		if ( $amount === 0 ) {
			return '';
		}

		$campaign    = DN_Campaign::instance( $campaign );
		$compensates = $campaign->get_compensate();

		if ( ! $compensates ) {
			return '';
		}

		$desc = '';
		$prev = 0;
		foreach ( $compensates as $key => $compensate ) {
			if ( $compensate['amount'] && $amount >= $compensate['amount'] && $compensate['amount'] > $prev ) {
				$desc = $compensate['desc'];
				$prev = $compensate['amount'];
			}
		}

		return $desc;
	}

}

/**
 * generate post key
 */
if ( ! function_exists( 'donate_generate_post_key' ) ) {

	function donate_generate_post_key( $post_id ) {
		return '#' . $post_id;
	}

}

/**
 * convert array to string
 */
if ( ! function_exists( 'donate_array_to_string' ) ) {

	function donate_array_to_string( $param ) {
		$html = array();
		foreach ( $param as $key => $value ) {
			if ( is_array( $value ) ) {
				$html[] = donate_array_to_string( $value );
			} else {
				$html[] = $key . $value;
			}
		}

		return implode( '', $html );
	}

}

if ( ! function_exists( 'donate_setcookie' ) ) {

	// setcookie
	function donate_setcookie( $name, $value, $expire = 0, $secure = false ) {
		if ( ! headers_sent() ) {
			setcookie( $name, $value, $expire, defined( 'COOKIEPATH' ) ? COOKIEPATH : '/', defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : site_url(), $secure );
		} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			headers_sent( $file, $line );
			trigger_error( "{$name} cookie cannot be set - headers already sent by {$file} on line {$line}", E_USER_NOTICE );
		}
	}

}

if ( ! function_exists( 'donate_add_notice' ) ) {

	function donate_add_notice( $notice_key = null, $message = null ) {
		if ( ! $notice_key || ! $message ) {
			return;
		}

		if ( ! isset( $_SESSION['donate_messages'] ) ) {
			$_SESSION['donate_messages']            = array();
			$_SESSION['donate_messages']['error']   = array();
			$_SESSION['donate_messages']['success'] = array();
		}

		if ( $notice_key === 'error' ) {
			$_SESSION['donate_messages']['error'][] = sprintf( '%s', $message );
		} else {
			$_SESSION['donate_messages']['success'][] = sprintf( '%s', $message );
		}
	}

}

if ( ! function_exists( 'donate_has_notice' ) ) {

	function donate_has_notice( $name = null ) {
		if ( empty( $_SESSION['donate_messages'] ) ) {
			return false;
		}

		if ( isset( $_SESSION['donate_messages'][ $name ] ) ) {
			return true;
		}
	}

}

if ( ! function_exists( 'fundpress_print_notices' ) ) {
	/**
	 * Print notice.
	 */
	function fundpress_print_notices() {
		if ( empty( $_SESSION['donate_messages'] ) ) {
			return;
		}

		if ( isset( $_SESSION['donate_messages'] ) ) {
			ob_start();
			donate_get_template( 'messages.php', array( 'messages' => $_SESSION['donate_messages'] ) );
			echo ob_get_clean();
			unset( $_SESSION['donate_messages'] );
		}
	}

}

/**
 * get status
 */
if ( ! function_exists( 'donate_get_status' ) ) {

	function donate_get_status( $post_id ) {
		$status = array(
			'donate-pending'    => __( 'Pending', 'fundpress' ),
			'donate-processing' => __( 'Processing', 'fundpress' ),
			'donate-completed'  => __( 'Completed', 'fundpress' )
		);

		return apply_filters( 'donate_get_status', $status );
	}

}

if ( ! function_exists( 'donate_amount_system' ) ) {

	/**
	 * donate_amount_system
	 * @return total donate amount for system without campaign
	 */
	function donate_amount_system() {
		global $wpdb;

		$query = $wpdb->prepare( "
				SELECT donate_system.meta_value AS amount, donate_currency.meta_value AS currency
				FROM $wpdb->postmeta AS donate_system
				INNER JOIN $wpdb->posts AS donation ON donation.ID = donate_system.post_id
				INNER JOIN $wpdb->postmeta AS donate_currency ON donate_currency.post_id = donation.ID
				INNER JOIN $wpdb->postmeta AS donate_type ON donate_type.post_id = donation.ID
				WHERE
					donation.post_type = %s
					AND donation.post_status = %s
					AND donate_system.meta_key = %s
					AND donate_currency.meta_key = %s
					AND donate_type.meta_key = %s
					AND donate_type.meta_value = %s
				HAVING amount > 0
			", 'dn_donate', 'donate-completed', TP_DONATE_META_DONATE . 'total', TP_DONATE_META_DONATE . 'currency', TP_DONATE_META_DONATE . 'type', 'system' );

		$total = 0;
		if ( $results = $wpdb->get_results( $query ) ) {
			foreach ( $results as $key => $donate ) {

				if ( ! $donate->amount ) {
					continue;
				}

				$currency = $donate->currency;
				if ( ! $currency ) {
					$currency = donate_get_currency();
				}

				$total = $total + donate_campaign_convert_amount( $donate->amount, $currency );
			}
		}

		return $total;
	}

}

if ( ! function_exists( 'donate_get_donors' ) ) {

	function donate_get_donors() {
		global $wpdb;
		$sql = $wpdb->prepare( "
				SELECT DISTINCT ID FROM $wpdb->posts WHERE post_type = %s AND post_status = %s
			", 'dn_donor', 'publish' );

		return $wpdb->get_col( $sql );
	}

}

if ( ! function_exists( 'donate_get_campaigns' ) ) {

	function donate_get_campaigns() {
		global $wpdb;
		$sql = $wpdb->prepare( "
				SELECT ID FROM $wpdb->posts WHERE post_type = %s AND post_status = %s
			", 'dn_campaign', 'publish' );

		return $wpdb->get_col( $sql );
	}

}

if ( ! function_exists( 'donate_get_donor_fullname' ) ) {

	function donate_get_donor_fullname( $donate_id = null ) {
		if ( ! $donate_id ) {
			return;
		}
		$donate = DN_Donate::instance( $donate_id );
		if ( ! $donate_id ) {
			return;
		}

		return ( $donor = $donate->get_donor() ) ? $donor->get_fullname() : '';
	}

}

if ( ! function_exists( 'donate_get_donor_email' ) ) {

	function donate_get_donor_email( $donate_id = null ) {
		if ( ! $donate_id ) {
			return;
		}
		$donate = DN_Donate::instance( $donate_id );

		return $donate->get_donor()->email;
	}

}

// date time format
function donate_date_time_format_js() {
	// set detault datetime format datepicker
	$dateFormat = get_option( 'date_format' );

	switch ( $dateFormat ) {
		case 'Y-m-d':
			$return = 'yy-mm-dd';
			break;

		case 'Y/m/d':
			$return = 'yy/mm/dd';
			break;

		case 'd/m/Y':
			$return = 'dd/mm/yy';
			break;

		case 'd-m-Y':
			$return = 'dd-mm-yy';
			break;

		case 'm/d/Y':
			$return = 'mm/dd/yy';
			break;

		case 'm-d-Y':
			$return = 'mm-dd-yy';
			break;

		case 'F j, Y':
			$return = 'MM dd, yy';
			break;

		default:
			$return = 'mm/dd/yy';
			break;
	}

	return $return;
}

/* count campaign day */

function donate_get_campaign_days_to_go( $campaign_id = null ) {
	if ( ! $campaign_id ) {
		global $post;
		$campaign_id = $post->ID;
	}
	if ( ! $campaign_id ) {
		return false;
	}

	$campaign = DN_Campaign::instance( $campaign_id );

	$current_time = current_time( 'timestamp' );
	$start        = $end = '';
	if ( $campaign->start ) {
		$start = strtotime( $campaign->start );
	}
	if ( $campaign->end ) {
		$end = strtotime( $campaign->end );
	}

	if ( $current_time >= $end ) {
		return 0;
	}

	return ceil( ( $end - $current_time ) / DAY_IN_SECONDS );
}

if ( ! function_exists( 'donate_get_donors' ) ) {
	/* get total donor donated */

	function donate_get_donors( $donate_id = null ) {
		if ( ! $donate_id ) {
			return 0;
		}
		global $wpdb;
		$sql = $wpdb->prepare( "
				SELECT IFNULL( COUNT( donor.meta_value ), 0 ) FROM $wpdb->postmeta AS donor
					lEFT JOIN $wpdb->posts AS donate ON donor.post_id = donate.ID AND donor.meta_key = %s
				WHERE donate.post_type = %s
					AND donate.ID = %d
				GROUP BY donor.meta_value
			", 'thimpress_donate_donor_id', 'dn_donate', $donate_id );

		return apply_filters( 'donate_get_donors_count', absint( $wpdb->get_var( $sql ) ), $donate_id );
	}

}

if ( ! function_exists( 'fundpress_is_ajax_request' ) ) {
	/**
	 * Check ajax request.
	 *
	 * @return bool
	 */
	function fundpress_is_ajax_request() {
		return defined( 'DOING_AJAX' ) && DOING_AJAX === true;
	}
}

if ( ! function_exists( 'donate_get_donate_items' ) ) {

	function donate_get_donate_items( $donate_id ) {
		return DN_Donate::instance( $donate_id )->get_items();
	}

}

if ( ! function_exists( 'donate_get_thankyou_link' ) ) {

	function donate_get_thankyou_link( $donate_id = null ) {
		return add_query_arg( array(
			'thank-you' => 1,
			'donate-id' => $donate_id
		), donate_checkout_url() );
	}

}

if ( ! function_exists( 'donate_is_thankyou_page' ) ) {

	function donate_is_thankyou_page() {
		global $post;
		$checkout_page_id = FP()->settings->checkout->get( 'checkout_page', 1 );
		if ( isset( $post->ID ) && $post->ID == $checkout_page_id && ! empty( $_GET['thank-you'] ) && ! empty( $_GET['donate-id'] ) ) {
			return true;
		}
	}

}

/**
 * get status label with html
 */
if ( ! function_exists( 'donate_get_status_label' ) ) {

	function donate_get_status_label( $post_id ) {
		global $donate_statuses;
		$statuses = array();
		foreach ( $donate_statuses as $status => $args ) {
			$statuses[ $status ] = '<label class="donate-status ' . $status . '">' . $args['label'] . '</span>';
		}

		$post_status = get_post_status( $post_id );
		if ( array_key_exists( $post_status, $statuses ) ) {
			return apply_filters( 'donate_get_status_label', $statuses[ $post_status ], $post_id );
		}
	}

}

if ( ! function_exists( 'donate_campaign_is_coming' ) ) {

	/**
	 * Is coming campaign
	 * @global type $post
	 *
	 * @param type $post_id
	 */
	function donate_campaign_is_coming( $post_id = null ) {
		if ( ! $post_id ) {
			global $post;
			$post_id = $post->ID;
		}
		$campaign = DN_Campaign::instance( $post_id );
		$start    = strtotime( $campaign->start );
		if ( ! $start ) {
			return false;
		}

		return time() < $start;
	}

}

if ( ! function_exists( 'donate_campaign_is_happening' ) ) {

	/**
	 * Is happening campaign
	 * @global type $post
	 *
	 * @param type $post_id
	 */
	function donate_campaign_is_happening( $post_id = null ) {
		if ( ! $post_id ) {
			global $post;
			$post_id = $post->ID;
		}
		$campaign = DN_Campaign::instance( $post_id );
		$start    = strtotime( $campaign->start );
		$end      = strtotime( $campaign->end );
		$time     = time();
		if ( ! $start && $end ) {
			return $time < $end;
		}

		if ( $start && ! $end ) {
			return $time >= $start;
		}

		if ( $start && $end ) {
			return $time >= $start && $time < $end;
		}

		return false;
	}

}

if ( ! function_exists( 'donate_campaign_is_expired' ) ) {

	/**
	 * Is expired campaign
	 * @global type $post
	 *
	 * @param type $post_id
	 */
	function donate_campaign_is_expired( $post_id = null ) {
		if ( ! $post_id ) {
			global $post;
			$post_id = $post->ID;
		}
		$campaign = DN_Campaign::instance( $post_id );
		$end      = strtotime( $campaign->end );
		$time     = time();
		if ( $end ) {
			return $time > $end;
		}

		return false;
	}

}

if ( ! function_exists( 'donate_campaign_count_donor' ) ) {

	/**
	 * Get donor donated for this campaign
	 * @global type $post
	 * @global type $wpdb
	 *
	 * @param type $campaign_id
	 *
	 * @return type integer
	 */
	function donate_campaign_count_donor( $campaign_id = null ) {
		if ( ! $campaign_id ) {
			global $post;
			$campaign_id = $post->ID;
		}
		global $wpdb;
		$sql = $wpdb->prepare( "SELECT COUNT( DISTINCT item_meta.meta_value ) FROM $wpdb->postmeta AS item_meta"
		                       . " INNER JOIN $wpdb->posts AS item ON item.ID = item_meta.post_id"
		                       . " INNER JOIN $wpdb->posts AS donate ON donate.ID = item.post_parent"
		                       . " WHERE donate.post_status = %s"
		                       . " AND donate.post_type = %s"
		                       . " AND item.post_status = %s"
		                       . " AND item.post_type = %s"
		                       . " AND item_meta.meta_key = %s"
		                       . " AND item_meta.meta_value = %s", 'donate-completed', 'dn_donate', 'publish', 'dn_donate_item', 'campaign_id', $campaign_id );

		return abs( $wpdb->get_var( $sql ) );
	}

}

if ( ! function_exists( 'donate_get_campaign_percent' ) ) {

	function donate_get_campaign_percent( $post = null ) {
		if ( ! $post ) {
			global $post;
			$post_id = $post->ID;
		}

		if ( is_numeric( $post ) ) {
			$post_id = $post;
		}

		if ( $post instanceof WP_Post ) {
			$post_id = $post->ID;
		}

		$total = donate_total_campaign( $post_id );
		$goal  = donate_goal_campagin( $post_id );

		if ( ! $goal ) {
			return 100;
		}

		return round( ( $total / $goal ) * 100, donate_currency_decimal() );
	}

}
// get campaign total
if ( ! function_exists( 'donate_total_campaign' ) ) {

	function donate_total_campaign( $post = null ) {
		if ( ! $post ) {
			global $post;
			$post_id = $post->ID;
		}

		if ( is_numeric( $post ) ) {
			$post_id = $post;
		}

		if ( $post instanceof WP_Post ) {
			$post_id = $post->ID;
		}
		$campaign = DN_Campaign::instance( $post_id );

		return $campaign->get_total_raised();
	}

}

// get campaign total by donated
if ( ! function_exists( 'donate_total_campaign_donated' ) ) {

	function donate_total_campaign_donated( $post = null ) {
		if ( ! $post ) {
			global $post;
			$post_id = $post->ID;
		}

		if ( is_numeric( $post ) ) {
			$post_id = $post;
		}

		if ( $post instanceof WP_Post ) {
			$post_id = $post->ID;
		}

		global $wpdb;

		$sql = $wpdb->prepare( "SELECT total.meta_value AS raised, c.meta_value AS currency FROM $wpdb->postmeta AS total"
		                       . " INNER JOIN $wpdb->posts AS item ON item.ID = total.post_id"
		                       . " INNER JOIN $wpdb->postmeta AS item_meta ON item.ID = item_meta.post_id"
		                       . " INNER JOIN $wpdb->posts AS donate ON donate.ID = item.post_parent"
		                       . " INNER JOIN $wpdb->postmeta AS c ON c.post_id = donate.ID"
		                       . " WHERE donate.post_status = %s"
		                       . " AND donate.post_type = %s"
		                       . " AND item.post_status = %s"
		                       . " AND item.post_type = %s"
		                       . " AND item_meta.meta_key = %s"
		                       . " AND item_meta.meta_value = %s"
		                       . " AND total.meta_key = %s"
		                       . " AND c.meta_key = %s", 'donate-completed', 'dn_donate', 'publish', 'dn_donate_item', 'campaign_id', $post_id, 'total', TP_DONATE_META_DONATE . 'currency' );

		$total = 0;
		if ( $results = $wpdb->get_results( $sql ) ) {
			foreach ( $results as $k => $donate ) {
				$total += donate_campaign_convert_amount( $donate->raised, $donate->currency );
			}
		}

		return $total;
	}

}

// get goal campaign
if ( ! function_exists( 'donate_goal_campagin' ) ) {

	function donate_goal_campagin( $post = null ) {
		if ( ! $post ) {
			global $post;
			$post_id = $post->ID;
		}

		if ( is_numeric( $post ) ) {
			$post_id = $post;
		}

		if ( $post instanceof WP_Post ) {
			$post_id = $post->ID;
		}

		$campaign = DN_Campaign::instance( $post_id );

		// convert to current currency settings
		return donate_campaign_convert_amount( floatval( $campaign->goal ), $campaign->currency, donate_get_currency() );
	}

}

// check campaign allow donate
if ( ! function_exists( 'donate_campaign_is_allow_donate' ) ) {

	function donate_campaign_is_allow_donate( $campaign_id = null ) {
		if ( ! $campaign_id ) {
			global $post;
			$campaign_id = $post->ID;
		}
		$campaign = DN_Campaign::instance( $campaign_id );
		if ( $campaign->type === 'flexible' ) {
			return true;
		}
		if ( ! $campaign->start && ! $campaign->end ) {
			return true;
		}
		$time  = time();
		$start = strtotime( $campaign->start );
		$end   = strtotime( $campaign->end );
		if ( $time >= $start && $time <= $end && donate_get_campaign_percent() < 100 ) {
			return true;
		}
	}

}

// change donate status
if ( ! function_exists( 'donate_action_status' ) ) {
	function donate_action_status( $post_id ) {
		$donate = DN_Donate::instance( $post_id );
		$action = '<div id="action-status" data-id="' . esc_attr( $post_id ) . '" >';
		if ( $donate->has_status( 'pending' ) ) {
			$action .= '<a href="#" class="button" data-action="processing" title="' . esc_html__( 'Processing', 'fundpress' ) . '"><i class="icon-spinner6"></i></a>';
		}
		if ( $donate->has_status( 'pending' ) || $donate->has_status( 'processing' ) ) {
			$action .= '<a href="#" class="button" data-action="completed" title="' . esc_html__( 'Complete', 'fundpress' ) . '"><i class="icon-checkmark"></i></a>';
		}
		$action .= '</div>';
		$action .= '<a href="' . get_edit_post_link( $post_id ) . '" class="button edit-donate" title="' . esc_html__( 'View', 'fundpress' ) . '"><i class="icon-eye view"></i></a>';

		return apply_filters( 'donate_action_status', $action, $post_id );

	}
}

// i18n
if ( ! function_exists( 'donate_18n_languages' ) ) {

	function donate_18n_languages() {
		$i18n = array(
			'amount_invalid'         => __( 'Please enter donate amount.', 'fundpress' ),
			'email_invalid'          => __( 'Please enter valid email. Eg: example@example.com', 'fundpress' ),
			'first_name_invalid'     => __( 'First name invalid, min length 3 and max length 15 character.', 'fundpress' ),
			'last_name_invalid'      => __( 'Last name invalid, min length 3 and max length 15 character.', 'fundpress' ),
			'phone_number_invalid'   => __( 'Phone number invalid. Eg: 01365987521.', 'fundpress' ),
			'payment_method_invalid' => __( 'Please select payment method.', 'fundpress' ),
			'address_invalid'        => __( 'Please enter your address.', 'fundpress' ),
			'processing'             => __( 'Processing...', 'fundpress' ),
			'complete'               => __( 'Donate', 'fundpress' ),
			'status_processing'      => __( 'Processing', 'fundpress' ),
			'status_completed'       => __( 'Completed', 'fundpress' ),
			'date_time_format'       => donate_date_time_format_js()
		);

		return apply_filters( 'donate_i18n', $i18n );
	}
}

if ( ! function_exists( 'donation_system_total_amount' ) ) {
	function donation_system_total_amount( $field ) {
		?>
        <tr>
            <th>
				<?php if ( isset( $field['label'] ) ) : ?>
                    <label for="<?php echo esc_attr( $field['name'] ) ?>"><?php printf( '%s', $field['label'] ) ?></label>
					<?php if ( isset( $field['desc'] ) ) : ?>
                        <p>
                            <small><?php printf( '%s', $field['desc'] ) ?></small>
                        </p>
					<?php endif; ?>
				<?php endif; ?>
            </th>
            <td>
                <input type="text"
                       value="<?php echo esc_attr( donate_price( donate_amount_system(), donate_get_currency() ) ); ?>"
                       readonly="readonly"/>
            </td>
        </tr>

		<?php
	}
}
