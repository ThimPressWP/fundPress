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
			// site assets
			DN_Assets::register_script( 'donate-frontend-script', FUNDPRESS_ASSETS_URI . '/js/site.js', array(), FUNDPRESS_VER, true );
			DN_Assets::register_style( 'donate-frontend-style', FUNDPRESS_ASSETS_URI . '/css/site.css' );

			// magic popup
			DN_Assets::register_script( 'donate-magnific', FUNDPRESS_LIB_URI . '/magnific-popup/jquery.magnific-popup.min.js', array(), FUNDPRESS_VER, true );
			DN_Assets::register_style( 'donate-magnific', FUNDPRESS_LIB_URI . '/magnific-popup/magnific-popup.css' );

			// circles library
			DN_Assets::register_script( 'donate-circles', FUNDPRESS_LIB_URI . '/circles.min.js' );
		}
	}
}

DN_Frontend_Assets::init();