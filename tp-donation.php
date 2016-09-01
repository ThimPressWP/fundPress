<?php
/*
  Plugin Name: ThimPress Donate
  Plugin URI: http://thimpress.com/tp-donate
  Description: Donate
  Author: ThimPress
  Version: 1.0.3
  Author URI: http://thimpress.com
 */

if ( !defined( 'ABSPATH' ) )
    exit();

if ( defined( 'TP_DONATE_PATH' ) )
    return;

define( 'TP_DONATE_FILE', __FILE__ );
define( 'TP_DONATE_PATH', plugin_dir_path( __FILE__ ) );
define( 'TP_DONATE_URI', plugins_url( '', __FILE__ ) );
define( 'TP_DONATE_INC', TP_DONATE_PATH . 'inc' );
define( 'TP_DONATE_INC_URI', TP_DONATE_URI . '/inc' );
define( 'TP_DONATE_ASSETS_URI', TP_DONATE_URI . '/assets' );
define( 'TP_DONATE_LIB_URI', TP_DONATE_INC_URI . '/libraries' );
define( 'TP_DONATE_VER', '1.0.3' );

// define meta post type
define( 'TP_DONATE_META_DONOR', 'thimpress_donor_' );
define( 'TP_DONATE_META_DONATE', 'thimpress_donate_' );
define( 'TP_DONATE_META_CAMPAIGN', 'thimpress_campaign_' );

/**
 * Donate class
 */
class ThimPress_Donate {

    /**
     * file include
     * @var array
     */
    protected $_files = array();

    /**
     * options
     * @var options
     */
    public $options = null;

    /**
     * cart
     * @var null
     */
    public $cart = null;

    /* checkout */
    public $checkout = null;

    /**
     * payments object
     * @var type object
     */
    public $payment_gateways = null;
    // instance
    public static $instance = null;

    public function __construct() {

        $this->includes();

        $GLOBALS['dn_settings'] = $this->options = DN_Settings::instance();

        /**
         * text-domain append plugins_loaded hook
         */
        add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
    }

    // plugins loaded hook
    public function plugins_loaded() {
        /* load text domain */
        $this->load_textdomain();

        /* cart */
        $this->cart = DN_Cart::instance();
        /**
         * payment gateway instance object
         */
        $this->payment_gateways = DN_Payment_Gateways::instance();

        /* checkout */
        $this->checkout = DN_Checkout::instance();
    }

    /**
     * load_textdomain
     * @return null
     */
    public function load_textdomain() {
        // prefix
        $prefix = basename( dirname( plugin_basename( __FILE__ ) ) );
        $locale = get_locale();
        $dir = TP_DONATE_PATH . 'languages';
        $mofile = false;

        $wp_file = WP_LANG_DIR . '/plugins/' . $prefix . '-' . $locale . '.mo';
        $pluginFile = $dir . '/' . $prefix . '-' . $locale . '.mo';

        if ( file_exists( $wp_file ) ) {
            $mofile = $wp_file;
        } else if ( file_exists( $pluginFile ) ) {
            $mofile = $pluginFile;
        }

        if ( $mofile ) {
            // In themes/plugins/mu-plugins directory
            load_textdomain( 'tp-donate', $mofile );
        }
    }

    /**
     * include file
     * @param  array or string
     * @return null
     */
    public function includes() {

        $this->_include( 'inc/class-dn-autoloader.php' );
        $this->_include( 'inc/class-dn-setting.php' );

        $paths = array( 'abstracts', 'settings' );
        $this->autoload( $paths );

        if ( is_admin() ) {
            $this->_include( 'inc/admin/class-dn-admin.php' );
        }

        $this->_include( 'inc/functions.php' );
        /* metaboxes */
        $this->_include( 'inc/widget-functions.php' );
        $this->_include( 'inc/i18n.php' );
        
        $this->_include( 'inc/hooks.php' );
        $this->_include( 'inc/template-hook.php' );
        $this->_include( 'inc/widgets/widget-functions.php' );
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
//        if ( !is_admin() ) {
        $this->_include( 'inc/class-dn-shortcodes.php' );
//        }

        if ( ! is_admin() ) {
            $this->_include( 'inc/class-dn-frontend-assets.php' );
        }

        $this->autoload( array( 'products' ) );
        $this->_include( 'inc/install.php' );

        /* load vendors */
        if ( !defined( 'CMB2_LOADED' ) ) {
            add_filter( 'cmb2_meta_box_url', array( $this, 'cmb2_meta_box_url' ) );
            $this->_include( 'inc/vendors/cmb2/init.php' );
        }
    }

    /**
     * autoload folder
     * @param type $paths
     */
    public function autoload( $paths = array() ) {
        foreach ( $paths as $key => $path ) {
            $real_path = TP_DONATE_INC . '/' . $path;
            $path = substr( $path, 0, -1 );
            foreach ( (array) glob( $real_path . '/class-dn-' . $path . '-*.php' ) as $key => $file ) {
                $this->_include( $file );
            }
        }
    }

    /**
     * _include
     * @param  $file
     * @return null
     */
    public function _include( $file ) {
        if ( !$file )
            return;

        if ( is_array( $file ) ) {
            foreach ( $file as $key => $f ) {
                if ( file_exists( TP_DONATE_PATH . $f ) )
                    require_once TP_DONATE_PATH . $f;
            }
        } else {
            if ( file_exists( TP_DONATE_PATH . $file ) )
                require_once TP_DONATE_PATH . $file;
            elseif ( file_exists( $file ) )
                require_once $file;
        }
    }

    /**
     * load options object class
     * @return object class
     */
    public function options() {
        return DN_Settings::instance();
    }

    /**
     * Filter cmb2 meta box url
     * @param string $url
     * @return string
     */
    public function cmb2_meta_box_url( $url ) {
        $url = TP_DONATE_INC_URI . '/vendors/cmb2/';
        return $url;
    }

    public static function instance() {
        if ( !self::$instance ) {
            return self::$instance = new self();
        }

        return self::$instance;
    }

}

ThimPress_Donate::instance();

if ( !function_exists( 'donate' ) ) {

    function donate() {
        return ThimPress_Donate::instance();
    }

}