<?php

if ( !defined( 'ABSPATH' ) )
    exit();

class DN_Install {

    private static $update_db = array(
        '1.0.3' => 'inc/admin/upgrade/upgrade_1.0.3.php'
    );
    public static $options = array();

    /* init */

    public static function install() {

        if ( !defined( 'TP_DONATE_INSTALLING' ) ) {
            define( 'TP_DONATE_INSTALLING', true );
        }

        /* create tables */
        self::create_tables();
        self::create_pages();

        /* default option */
        self::default_options();

        /* upgrade database */
        self::upgrade_database();

		/* remove tp-donate */
		$active_plugins = get_option( 'active_plugins', true );
		if ( ( $key = array_search( 'tp-donate/tp-donation.php', $active_plugins ) ) !== false ) {
			unset( $active_plugins[$key] );
		}
		update_option( 'active_plugins', $active_plugins );

		/**
		 * delete folder tp-donate plugin
		 */
		if ( !function_exists( 'delete_plugins' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			include_once( ABSPATH . 'wp-includes/pluggable.php' );
			include_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		delete_plugins( array( 'tp-donate/tp-donation.php' ) );

        /* source version */
        update_option( 'thimpress_donate_version', TP_DONATE_VER );
    }

    /* create tables */

    public static function create_tables() {
        
    }


    /* default options */

    public static function default_options() {
        update_option( 'thimpress_donate', array_merge( self::$options, get_option( 'thimpress_donate', array() ) ) );
    }

    /* default pages */

    public static function create_pages() {
        $options = array(
            'general' => array(
                'aggregator' => 'yahoo',
                'currency' => 'GBP',
                'currency_position' => 'left',
                'currency_thousand' => ',',
                'currency_separator' => '.',
                'currency_num_decimal' => 2,
            ),
            'checkout' => array(
                'environment' => 'test',
                'lightbox_checkout' => 'no',
                'donate_redirect' => 'checkout',
                'term_condition_enable' => 'yes',
                'paypal_enable' => 'yes',
                'stripe_enable' => 'yes'
            ),
            'email' => array(
                'enable' => 'yes'
            ),
            'donate' => array()
        );

        $settings = DN_Settings::instance();

        $pages = array();

        $cart_page_id = $settings->checkout->get( 'cart_page' );
        if ( !$cart_page_id || !get_post( $cart_page_id ) ) {
            $pages['donate-cart'] = array(
                'name' => _x( 'donate-cart', 'donate-cart', 'fundpress' ),
                'title' => _x( 'Donate Cart', 'Donate Cart', 'fundpress' ),
                'content' => '[' . apply_filters( 'donate_cart_shortcode_tag', 'donate_cart' ) . ']',
                'option_name' => 'cart_page'
            );
        }

        $checkout_page_id = $settings->checkout->get( 'checkout_page' );
        if ( !$checkout_page_id || !get_post( $checkout_page_id ) ) {
            $pages['checkout'] = array(
                'name' => _x( 'donate-checkout', 'donate-checkout', 'fundpress' ),
                'title' => _x( 'Donate Checkout', 'Donate Checkout', 'fundpress' ),
                'content' => '[' . apply_filters( 'donate_checkout_shortcode_tag', 'donate_checkout' ) . ']',
                'option_name' => 'checkout_page'
            );
        }

        if ( !function_exists( 'donate_create_page' ) ) {
            ThimPress_Donate::instance()->_include( 'inc/admin/functions.php' );
        }

        if ( $pages && function_exists( 'donate_create_page' ) ) {
            foreach ( $pages as $key => $page ) {
                $pageId = donate_create_page( esc_sql( $page['name'] ), 'donate_' . $key . '_page_id', $page['title'], $page['content'] );

                $options['checkout'][$page['option_name']] = $pageId;
            }

            self::$options = array_merge( self::$options, $options );
        }
    }

    /**
     * upgrade database order
     * @return type
     */
    public static function upgrade_database() {
//        delete_option( 'thimpress_donate_version' );
        $current_verion = get_option( 'thimpress_donate_version', null );
        if ( $current_verion && $current_verion >= max( array_keys( self::$update_db ) ) )
            return;

        foreach ( self::$update_db as $ver => $file ) {
            if ( version_compare( $current_verion, $ver, '<' ) ) {
                ThimPress_Donate::instance()->_include( $file );
            }
        }
    }

    /* uninstall hook action */

    public static function uninstall() {
        
    }

}

// active plugin
register_activation_hook( TP_DONATE_FILE, array( 'DN_Install', 'install' ) );
register_deactivation_hook( TP_DONATE_FILE, array( 'DN_Install', 'uninstall' ) );
