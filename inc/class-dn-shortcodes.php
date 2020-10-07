<?php
/**
 * Fundpress shortcodes class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Shortcodes' ) ) {
	/**
	 * Class DN_Shortcodes.
	 */
	class DN_Shortcodes {

		/**
		 * Init.
		 */
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
		 * Shortcode start wrap.
		 *
		 * @param $name
		 *
		 * @return string
		 */
		public static function shortcode_start_wrap( $name ) {
			return '<div class="donate_wrapper ' . esc_attr( $name ) . '">';
		}

		/**
		 * Shortcode end wrap.
		 *
		 * @param $name
		 *
		 * @return string
		 */
		public static function shortcode_end_wrap( $name ) {
			return '</div>';
		}

		/**
		 * Shortcode render.
		 *
		 * @param string $name
		 * @param string $template
		 * @param array  $atts
		 * @param null   $content
		 *
		 * @return string
		 */
		public static function render( $name = '', $template = '', $atts = array(), $content = null ) {
			ob_start();
			do_action( 'donate_before_wrap_shortcode', $name );
			donate_get_template( $template, $atts );
			do_action( 'donate_after_wrap_shortcode', $name );

			return ob_get_clean();
		}

		/**
		 * Donate button.
		 *
		 * @param      $atts
		 * @param null $contents
		 *
		 * @return string
		 */
		public static function tp_donate( $atts, $contents = null ) {
			return DN_Shortcodes::render( 'donate_checkout', 'shortcodes/donate.php', $atts, $contents );
		}

		/**
		 * Cart page.
		 *
		 * @param      $atts
		 * @param null $contents
		 *
		 * @return string
		 */
		public static function donate_cart( $atts, $contents = null ) {
			if ( FP()->cart->is_empty() ) {
				$tmp = 'cart/empty.php';
			} else {
				$tmp = 'cart/cart.php';
			}

			return DN_Shortcodes::render( 'donate_cart', $tmp, $atts, $contents );
		}

		/**
		 * Checkout page.
		 *
		 * @param      $atts
		 * @param null $contents
		 *
		 * @return string
		 */
		public static function donate_checkout( $atts, $contents = null ) {
			if ( donate_is_thankyou_page() ) {
				$temp   = 'checkout/thank-you.php';
				$donate = ( isset( $_GET['donate-id'] ) ? DN_Helpper::DN_sanitize_params_submitted( $_GET['donate-id'] ) : 0 );
				$atts   = shortcode_atts( array( 'donate' => $donate ), $atts );
			} else if ( FP()->cart->is_empty() ) {
				$temp = 'cart/empty.php';
			} else {
				$temp = 'checkout/checkout.php';
			}

			return DN_Shortcodes::render( 'donate_checkout', $temp, $atts, $contents );
		}

		/**
		 * Total donate system.
		 *
		 * @param      $atts
		 * @param null $contents
		 *
		 * @return string
		 */
		public static function donate_system( $atts, $contents = null ) {
			return DN_Shortcodes::render( 'donate_system', 'shortcodes/donate-system.php', $atts, $contents );
		}

		/**
		 * Donate form.
		 *
		 * @param      $atts
		 * @param null $contents
		 *
		 * @return string
		 */
		public static function donate_form( $atts, $contents = null ) {
			$atts = shortcode_atts( array(
				'campaign_id' => '',
				'title'       => '',
				'payments'    => FP()->settings->checkout->get( 'lightbox_checkout', 'yes' ) === 'yes',
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
						// convert campaign amount currency to amount with currency setting
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
		 * Campaign slider.
		 *
		 * @param      $atts
		 * @param null $contents
		 *
		 * @return string
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
	}
}

DN_Shortcodes::init();
