<?php

defined( 'ABSPATH' ) || exit();

class DN_Frontend_Assets {

	/**
	 * Init static class
	 */
	public static function init() {
		add_action( 'donate_before_enqueue_scripts', array( __CLASS__, 'register_scripts' ) );
	}

	/**
	 * Enqueue scripts
	 *
	 * @param type $hook
	 */
	public static function register_scripts( $hook ) {
		/**
		 * site.js, site.css
		 */
		DN_Assets::register_script( 'donate-frontend-script', FUNDPRESS_ASSETS_URI . '/js/frontend/site.js', array(), FUNDPRESS_VER, true );
		DN_Assets::register_style( 'donate-frontend-style', FUNDPRESS_ASSETS_URI . '/css/frontend/site.css' );
		/**
		 * magic popup
		 */
		DN_Assets::register_script( 'donate-magnific', FUNDPRESS_LIB_URI . '/magnific-popup/jquery.magnific-popup.min.js', array(), FUNDPRESS_VER, true );
		DN_Assets::register_style( 'donate-magnific', FUNDPRESS_LIB_URI . '/magnific-popup/magnific-popup.css' );
		/**
		 * circles library
		 */
		DN_Assets::register_script( 'donate-circles', FUNDPRESS_LIB_URI . '/circles.min.js' );
	}

}

DN_Frontend_Assets::init();