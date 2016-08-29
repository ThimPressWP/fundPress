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
     * @param type $hook
     */
    public static function register_scripts( $hook ) {
        /**
         * site.js, site.css
         */
        DN_Assets::register_script( 'donate-frontend-script', TP_DONATE_ASSETS_URI . '/js/frontend/site.js', array(), TP_DONATE_VER, true );
        DN_Assets::register_style( 'donate-frontend-style', TP_DONATE_ASSETS_URI . '/css/frontend/site.css' );
        /**
         * magic popup
         */
        DN_Assets::register_script( 'donate-magnific', TP_DONATE_LIB_URI . '/magnific-popup/jquery.magnific-popup.min.js', array(), TP_DONATE_VER, true );
        DN_Assets::register_style( 'donate-magnific', TP_DONATE_LIB_URI . '/magnific-popup/magnific-popup.css' );
        /**
         * circles library
         */
        DN_Assets::register_script( 'donate-circles', TP_DONATE_LIB_URI . '/circles.min.js' );
    }
    
}

DN_Frontend_Assets::init();