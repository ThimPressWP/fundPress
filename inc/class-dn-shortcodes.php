<?php

defined( 'ABSPATH' ) || exit();

class DN_Shortcodes {

	public static function init() {
		add_action( 'donate_before_wrap_shortcode', array( __CLASS__, 'shortcode_start_wrap' ) );
		add_action( 'donate_after_wrap_shortcode', array( __CLASS__, 'shortcode_end_wrap' ) );

		$shortcodes = array(
			'tp_donate',
			'donate_campaign',
			'donate_checkout',
			'donate_cart',
			'donate_form',
			'donate_system'
		);
		foreach ( $shortcodes as $shortcode ) {
			add_shortcode( $shortcode, array( __CLASS__, $shortcode ) );
		}
	}

	/**
	 * [tp_donate]
	 *
	 * @param type $atts
	 * @param type $contents
	 *
	 * @return type
	 */
	public static function tp_donate( $atts, $contents = null ) {
		return DN_Shortcodes::render( 'donate_checkout', 'shortcodes/donate.php', $atts, $contents );
	}

	/**
	 * [donate_checkout]
	 *
	 * @param type $atts
	 * @param type $contents
	 */
	public static function donate_checkout( $atts, $contents = null ) {
		if ( donate_is_thankyou_page() ) {
			$temp   = 'checkout/thank-you.php';
			$donate = ( isset( $_GET['donate-id'] ) ? $_GET['donate-id'] : 0 );

			$atts = shortcode_atts( array(
				'donate' => $donate
			), $atts );

		} else if ( donate()->cart->is_empty() ) {
			$temp = 'cart/empty.php';
		} else {
			$temp = 'checkout/checkout.php';
		}

		return DN_Shortcodes::render( 'donate_checkout', $temp, $atts, $contents );
	}

	/**
	 * [donate_cart]
	 *
	 * @param type $atts
	 * @param type $contents
	 */
	public static function donate_cart( $atts, $contents = null ) {
		if ( donate()->cart->is_empty() ) {
			$tmp = 'cart/empty.php';
		} else {
			$tmp = 'cart/cart.php';
		}

		return DN_Shortcodes::render( 'donate_cart', $tmp, $atts, $contents );
	}

	/*
	 * [donate_system]
	 * @param type $atts
	 * @param type $contents
	 */
	public static function donate_system( $atts, $contents = null ) {
		return DN_Shortcodes::render( 'donate_system', 'shortcodes/donate-system.php', $atts, $contents );
	}

	/**
	 * [donate_form]
	 *
	 * @param type $atts
	 * @param type $contents
	 *
	 * @return type
	 */
	public static function donate_form( $atts, $contents = null ) {
		$atts = shortcode_atts( array(
			'campaign_id' => '',
			'title'       => '',
			'payments'    => DN_Settings::instance()->checkout->get( 'lightbox_checkout', 'yes' ) === 'yes',
			'compensates' => false
		), $atts );

		if ( $atts['campaign_id'] && ! $atts['title'] ) {
			$atts['title'] = get_the_title( $atts['campaign_id'] );
		} else {
			if ( ! $atts['title'] ) {
				$atts['title'] = apply_filters( 'donate_form_title_without_campaign', sprintf( '%s - %s', get_bloginfo( 'name' ), get_bloginfo( 'description' ) ) );
			}
		}
		if ( $atts['campaign_id'] ) {
			$campaign = DN_Campaign::instance( $atts['campaign_id'] );

			$compensates = array();
			$currency    = $campaign->get_currency();

			if ( $eachs = $campaign->get_compensate() ) {
				foreach ( $eachs as $key => $compensate ) {
					/**
					 * convert campaign amount currency to amount with currency setting
					 * @var
					 */
					$amount              = donate_campaign_convert_amount( $compensate['amount'], $currency );
					$compensates[ $key ] = array(
						'amount' => donate_price( $amount ),
						'desc'   => $compensate['desc']
					);
				}
			}
			$atts['compensates'] = $compensates;
		}

		return DN_Shortcodes::render( 'donate_form', 'shortcodes/donate-form.php', $atts, $contents );
	}

	/**
	 * [donate_campaign]
	 *
	 * @param type $atts
	 * @param type $contents
	 *
	 * @return type
	 */
	public static function donate_campaign( $atts, $contents = null ) {
		$atts = shortcode_atts( array(
			'id'    => '',
			'title' => '',
			'style' => '',
			'stime' => '100',
		), $atts );

		return DN_Shortcodes::render( 'donate_campaign', 'shortcodes/campaign.php', $atts, $contents );
	}

	public static function shortcode_start_wrap( $name ) {
		return '<div class="donate_wrapper ' . esc_attr( $name ) . '">';
	}

	public static function shortcode_end_wrap( $name ) {
		return '</div>';
	}

	public static function render( $name = '', $template = '', $atts = array(), $content = null ) {
		ob_start();
		do_action( 'donate_before_wrap_shortcode', $name );
		donate_get_template( $template, $atts );
		do_action( 'donate_after_wrap_shortcode', $name );

		return ob_get_clean();
	}

}

DN_Shortcodes::init();
