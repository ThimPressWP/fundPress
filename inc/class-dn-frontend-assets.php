<?php
/**
 * Fundpress Frontend assets class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Frontend_Assets' ) ) {
	/**
	 * Class DN_Frontend_Assets
	 */
	class DN_Frontend_Assets {

		/**
		 * Init.
		 */
		public static function init() {
			add_action( 'donate_before_enqueue_scripts', array( __CLASS__, 'register_scripts' ) );
		}

		/**
		 * Enqueue scripts.
		 *
		 * @param $hook
		 */
		public static function register_scripts( $hook ) {

			$checkout        = FP()->settings->checkout;
			$localize_script = array(
				'stripe_publish_key' => $checkout->get( 'stripe_test_publish_key' ),
				'error_verify'       => esc_html__( 'Unable to process this payment, please try again or use alternative method.', 'fundpress' ),
				'button_verify'      => esc_html__( 'Updating', 'fundpress' ),
			);

			if ( $checkout->get( 'environment' ) === 'production' ) {
				$localize_script['stripe_publish_key'] = $checkout->get( 'stripe_live_publish_key' );
			}

			wp_enqueue_script( 'stripe', 'https://js.stripe.com/v3/', '', '3.0', true );
			// site assets
			wp_enqueue_script( 'donate-frontend-script', FUNDPRESS_ASSETS_URI . '/js/frontend/site.js', array(), FUNDPRESS_VER, true );
			DN_Assets::register_style( 'donate-frontend-style', FUNDPRESS_ASSETS_URI . '/css/frontend/site.css' );

			// magic popup
			DN_Assets::register_script( 'donate-magnific', FUNDPRESS_LIB_URI . '/magnific-popup/jquery.magnific-popup.min.js', array(), FUNDPRESS_VER, true );
			DN_Assets::register_style( 'donate-magnific', FUNDPRESS_LIB_URI . '/magnific-popup/magnific-popup.css' );

			// circles library
			DN_Assets::register_script( 'donate-circles', FUNDPRESS_LIB_URI . '/circles.min.js' );

			wp_localize_script( 'donate-frontend-script', 'dn_localize', $localize_script );
		}
	}
}

DN_Frontend_Assets::init();
