<?php

defined( 'ABSPATH' ) || exit();

/**
 * DN_Assets class
 */
class DN_Assets {

    /**
     * styles
     * @var type array
     */
    public static $_styles = array();

    /**
     * scripts
     * @var type array
     */
    public static $_scripts = array();

    public static function init() {
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

    /**
     * register script
     */
    public static function register_script( $handle = '', $src = '', $deps = array(), $ver = false, $in_footer = false ) {
        wp_register_script( $handle, $src, $deps, $ver, $in_footer );
        self::$_scripts[$handle] = $src;
    }

    /**
     * register style
     */
    public static function register_style( $handle, $src, $deps = array(), $ver = false, $media = 'all' ) {
        wp_register_style( $handle, $src, $deps, $ver, $media );
        self::$_styles[$handle] = $src;
    }

    /**
     * admin enqueue scripts
     */
    public static function admin_enqueue_scripts() {
        
    }

    /**
     * frontend enqueue scripts
     */
    public static function enqueue_scripts() {
        
    }

    /**
     * Get file uri.
     * if WP_DEBUG is FALSE will load minify file
     */
    public static function _get_file_uri( $uri = '' ) {
        if ( defined( 'WP_DEBUG' ) || WP_DEBUG ) {
            return $uri;
        }

        return $uri;
    }

    /**
     * get file path by uri
     * @param type $uri
     */
    public static function _get_path_by_uri( $uri = '' ) {
        $base_url = trailingslashit( TP_DONATE_URI );
        $path = trailingslashit( TP_DONATE_PATH );

        /**
         * file path
         */
        return str_replace( $base_url, $path, $uri );
    }

}

/**
 * init
 */
DN_Assets::init();
