<?php
/**
 * Fundpress Install class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Install' ) ) {
	/**
	 * Class DN_Install
	 */
	class DN_Install {

		/**
		 * @var array
		 */
		private static $update_db = array(
			'1.0.3' => 'inc/admin/upgrade/upgrade_1.0.3.php',
			'1.4'   => 'inc/admin/upgrade/upgrade_1.4.php'
		);

		/**
		 * @var array
		 */
		public static $options = array();

		/**
		 * Install.
		 */
		public static function install() {
			if ( ! defined( 'TP_DONATE_INSTALLING' ) ) {
				define( 'TP_DONATE_INSTALLING', true );
			}

			/* create tables */
			self::create_pages();

			/* default option */
			self::default_options();

			/* upgrade database */
			self::upgrade_database();

			/* deactivate tp-donate */
			$active_plugins = get_option( 'active_plugins', true );
			if ( ( $key = array_search( 'tp-donate/tp-donation.php', $active_plugins ) ) !== false ) {
				unset( $active_plugins[ $key ] );
			}
			update_option( 'active_plugins', $active_plugins );

			// delete folder tp-donate plugin
			if ( ! function_exists( 'delete_plugins' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				include_once( ABSPATH . 'wp-includes/pluggable.php' );
				include_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
			delete_plugins( array( 'tp-donate/tp-donation.php' ) );

			/* source version */
			update_option( 'thimpress_donate_version', FUNDPRESS_VER );
		}

		/**
		 * Create and add shortcode to default page.
		 *
		 * @since 2.0
		 */
		public static function create_pages() {
			$settings = FP()->settings;

			$pages = array();

			$cart_page_id = $settings->checkout->get( 'cart_page' );
			if ( ! $cart_page_id || ! get_post( $cart_page_id ) ) {
				$pages['donate-cart'] = array(
					'name'        => _x( 'donate-cart', 'donate-cart', 'fundpress' ),
					'title'       => _x( 'Donate Cart', 'Donate Cart', 'fundpress' ),
					'content'     => '[' . apply_filters( 'donate_cart_shortcode_tag', 'donate_cart' ) . ']',
					'option_name' => 'cart_page'
				);
			}

			$checkout_page_id = $settings->checkout->get( 'checkout_page' );
			if ( ! $checkout_page_id || ! get_post( $checkout_page_id ) ) {
				$pages['checkout'] = array(
					'name'        => _x( 'donate-checkout', 'donate-checkout', 'fundpress' ),
					'title'       => _x( 'Donate Checkout', 'Donate Checkout', 'fundpress' ),
					'content'     => '[' . apply_filters( 'donate_checkout_shortcode_tag', 'donate_checkout' ) . ']',
					'option_name' => 'checkout_page'
				);
			}

			if ( ! function_exists( 'donate_create_page' ) ) {
				FP()->_include( 'inc/admin/dn-admin-functions.php' );
			}

			if ( $pages && function_exists( 'donate_create_page' ) ) {
				$options = array();
				foreach ( $pages as $key => $page ) {
					$pageId = donate_create_page( esc_sql( $page['name'] ), 'donate_' . $key . '_page_id', $page['title'], $page['content'] );

					$options['checkout'][ $page['option_name'] ] = $pageId;
				}

				self::$options = array_merge( self::$options, $options );
			}
		}


		/**
		 * Update default options.
		 *
		 * @since 2.0
		 */
		public static function default_options() {

			$default = apply_filters( 'donate_install_default_options', array(
				'general'  => array(
					'aggregator'           => 'google',
					'currency'             => 'GBP',
					'currency_position'    => 'left',
					'currency_thousand'    => ',',
					'currency_separator'   => '.',
					'currency_num_decimal' => 2,
				),
				'checkout' => array(
					'environment'           => 'test',
					'lightbox_checkout'     => 'no',
					'donate_redirect'       => 'checkout',
					'term_condition_enable' => 'yes',
					'paypal_enable'         => 'yes',
					'stripe_enable'         => 'yes'
				),
				'email'    => array( 'enable' => 'yes' ),
				'donate'   => array()
			) );

			self::$options = array_merge( self::$options, $default );

			update_option( 'thimpress_donate', array_merge( self::$options, get_option( 'thimpress_donate', array() ) ) );
		}

		/**
		 * Update database order.
		 *
		 * @return bool
		 */
		public static function upgrade_database() {
			$current_version = get_option( 'thimpress_donate_version', null );
			if ( $current_version && $current_version >= max( array_keys( self::$update_db ) ) ) {
				return false;
			}

			foreach ( self::$update_db as $ver => $file ) {
				if ( version_compare( $current_version, $ver, '<' ) ) {
					FP()->_include( $file );
				}
			}

			return true;
		}
	}
}

// active plugin
register_activation_hook( FUNDPRESS_FILE, array( 'DN_Install', 'install' ) );
