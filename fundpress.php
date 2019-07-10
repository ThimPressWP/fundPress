<?php
/*
  Plugin Name: FundPress
  Plugin URI: http://thimpress.com/fundpress
  Description: Easily build your own crowdfunding platform like Kickstarter with this free WordPress donation plugin.
  Author: ThimPress
  Version: 2.0.1
  Author URI: http://thimpress.com
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

! defined( 'FUNDPRESS_FILE' ) || exit();

define( 'FUNDPRESS_FILE', __FILE__ );
define( 'FUNDPRESS_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

define( 'FUNDPRESS_URI', plugins_url( '', __FILE__ ) );

define( 'FUNDPRESS_INC', FUNDPRESS_PATH . 'inc/' );
define( 'FUNDPRESS_TEMP', FUNDPRESS_PATH . 'templates/' );

define( 'FUNDPRESS_INC_URI', FUNDPRESS_URI . '/inc' );
define( 'FUNDPRESS_ASSETS_URI', FUNDPRESS_URI . '/assets' );
define( 'FUNDPRESS_LIB_URI', FUNDPRESS_INC_URI . '/libraries' );
define( 'FUNDPRESS_VER', '2.0.1' );

// define meta post type
define( 'TP_DONATE_META_DONOR', 'thimpress_donor_' );
define( 'TP_DONATE_META_DONATE', 'thimpress_donate_' );
define( 'TP_DONATE_META_CAMPAIGN', 'thimpress_campaign_' );

if ( ! class_exists( 'FundPress' ) ) {
	/**
	 * Class FundPress.
	 */
	class FundPress {

		/**
		 * @var array
		 */
		protected $_files = array();

		/**
		 * @var null
		 */
		public $options = null;

		/**
		 * @var null
		 */
		public $cart = null;

		/**
		 * @var null
		 */
		public $checkout = null;

		/**
		 * @var null
		 */
		public $payment_gateways = null;

		/**
		 * @var null
		 */
		public $settings = null;

		/**
		 * @var null
		 */
		public static $instance = null;

		/**
		 * FundPress constructor.
		 */
		public function __construct() {
			$this->includes();
			$this->init_hooks();
		}

		/**
		 * Includes needed files.
		 */
		public function includes() {

			$this->_include( 'inc/class-dn-autoloader.php' );
			$this->_include( 'inc/class-dn-setting.php' );

			// autoload abstracts and settings classes
			$paths = array( 'abstracts', 'settings' );
			$this->_autoload( $paths );

			// settings (autoload settings load before plugin loaded)
			$this->settings = DN_Settings::instance();

			if ( is_admin() ) {
				$this->_include( 'inc/admin/class-dn-admin.php' );
			}

			$this->_include( 'inc/dn-core-functions.php' );
			$this->_include( 'inc/dn-core-hooks.php' );
			$this->_include( 'inc/dn-template-hooks.php' );
			$this->_include( 'inc/class-dn-custom-post-type.php' );

			// sessions libraries
			$this->_include( 'inc/class-dn-sessions.php' );

			$this->_include( 'inc/class-dn-campaign.php' );
			$this->_include( 'inc/class-dn-cart.php' );
			$this->_include( 'inc/class-dn-checkout.php' );
			$this->_include( 'inc/class-dn-donate.php' );
			$this->_include( 'inc/class-dn-donor.php' );
			$this->_include( 'inc/class-dn-email.php' );
			$this->_include( 'inc/class-dn-payment-gateways.php' );

			$this->_include( 'inc/class-dn-template-include.php' );
			$this->_include( 'inc/class-dn-ajax.php' );
			$this->_include( 'inc/class-dn-assets.php' );
			$this->_include( 'inc/class-dn-shortcodes.php' );

			if ( ! is_admin() ) {
				$this->_include( 'inc/class-dn-frontend-assets.php' );
			}

			$this->_autoload( array( 'products' ) );
			$this->_include( 'inc/class-dn-install.php' );

			// load vendors
			if ( ! defined( 'CMB2_LOADED' ) ) {
				// filter cmb2 metabox vendor
				add_filter( 'cmb2_meta_box_url', array( $this, 'cmb2_meta_box_url' ) );
				$this->_include( 'inc/vendors/cmb2/init.php' );
			}
		}

		/**
		 * Init hooks.
		 */
		public function init_hooks() {
			add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		}

		/**
		 * Plugins loaded hook.
		 */
		public function plugins_loaded() {
			// load text domain
			$this->load_text_domain();

			/**
			 * Only create cart in frontend to prevent request-timeout when
			 * wp try to call a test to a rest-api for site-health feature.
			 */
			if ( ! is_admin() ) {
				// cart
				$this->cart = DN_Cart::instance();
				// checkout
				$this->checkout = DN_Checkout::instance();
			}

			// payment gateways
			$this->payment_gateways = DN_Payment_Gateways::instance();
		}

		/**
		 * Load text domain.
		 */
		public function load_text_domain() {
			// prefix
			$prefix = basename( dirname( plugin_basename( __FILE__ ) ) );
			$locale = get_locale();
			$dir    = FUNDPRESS_PATH . 'languages';
			$mofile = false;

			$wp_file    = WP_LANG_DIR . '/plugins/' . $prefix . '-' . $locale . '.mo';
			$pluginFile = $dir . '/' . $prefix . '-' . $locale . '.mo';

			if ( file_exists( $wp_file ) ) {
				$mofile = $wp_file;
			} else if ( file_exists( $pluginFile ) ) {
				$mofile = $pluginFile;
			}

			if ( $mofile ) {
				// In themes/plugins/mu-plugins directory
				load_textdomain( 'fundpress', $mofile );
			}
		}

		/**
		 * Auto load files.
		 *
		 * @param array $paths
		 */
		private function _autoload( $paths = array() ) {
			foreach ( $paths as $key => $path ) {
				$real_path = FUNDPRESS_INC . '/' . $path;
				$path      = substr( $path, 0, - 1 );
				foreach ( (array) glob( $real_path . '/class-dn-' . $path . '-*.php' ) as $file ) {
					$this->_include( $file );
				}
			}
		}

		/**
		 * Include file.
		 *
		 * @param $file
		 */
		public function _include( $file ) {
			if ( ! $file ) {
				return;
			}

			if ( is_array( $file ) ) {
				foreach ( $file as $key => $f ) {
					if ( file_exists( FUNDPRESS_PATH . $f ) ) {
						require_once FUNDPRESS_PATH . $f;
					}
				}
			} else {
				if ( file_exists( FUNDPRESS_PATH . $file ) ) {
					require_once FUNDPRESS_PATH . $file;
				} elseif ( file_exists( $file ) ) {
					require_once $file;
				}
			}
		}

		/**
		 * Filter cmb2 meta box url.
		 *
		 * @param string $url
		 *
		 * @return string
		 */
		public function cmb2_meta_box_url( $url ) {
			$url = FUNDPRESS_INC_URI . '/vendors/cmb2/';

			return $url;
		}

		/**
		 * Instance.
		 *
		 * @return FundPress|null
		 */
		public static function instance() {
			if ( ! self::$instance ) {
				return self::$instance = new self();
			}

			return self::$instance;
		}
	}
}

/**
 * Short way to load main instance of plugin.
 *
 * @since 2.0
 *
 * @return FundPress|null
 */
function FP() {
	return FundPress::instance();
}

/**
 * Done! entry point of the plugin
 * Create new instance of LearnPress and put it to global
 */
$GLOBALS['FundPress'] = FP();