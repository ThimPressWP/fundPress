<?php

defined( 'ABSPATH' ) || exit();

class DN_Admin_Assets {

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
        wp_enqueue_script( 'jquery-ui-datepicker' );
        DN_Assets::register_script( 'donate-admin-script', TP_DONATE_ASSETS_URI . '/js/admin/admin.js', array(), TP_DONATE_VER, true );
        DN_Assets::register_style( 'donate-admin-style', TP_DONATE_ASSETS_URI . '/css/admin/admin.css' );

        DN_Assets::register_script( 'donate-admin-select2-script', TP_DONATE_ASSETS_URI . '/js/admin/select2.min.js', array(), TP_DONATE_VER, true );
        DN_Assets::register_style( 'donate-admin-select2--style', TP_DONATE_ASSETS_URI . '/css/admin/select2.min.css' );
    }

}

DN_Admin_Assets::init();
