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
    
    /**
     * localize
     * @var type array
     */
    public static $_localize_scripts = array();

    public static function init() {
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

    /**
     * register script
     */
    public static function register_script( $handle = '', $src = '', $deps = array(), $ver = false, $in_footer = true ) {
        self::$_scripts[$handle] = array( $handle, self::_get_file_uri( $src ), $deps, $ver, $in_footer );
    }

    /**
     * register style
     */
    public static function register_style( $handle, $src, $deps = array(), $ver = false, $media = 'all' ) {
        self::$_styles[$handle] = array( $handle, self::_get_file_uri( $src ), $deps, $ver, $media );
    }

    /**
     * localize_script scripts
     * @param type $handle
     * @param type $name
     * @param type $data
     */
    public static function localize_script( $handle, $name, $data ) {
        self::$_localize_scripts[ $handle ] = array( $handle, $name, $data );
    }

    /**
     * frontend enqueue scripts
     */
    public static function enqueue_scripts( $hook ) {
        /**
         * Before enqueue scripts
         */
        do_action( 'donate_before_enqueue_scripts', $hook );

        wp_enqueue_script( 'jquery' );
        // wp_dequeue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'wp-util' );

        self::register_script( 'donate-global', TP_DONATE_ASSETS_URI . '/js/donate.js' );
        /**
         * array render object script
         * @var array
         */
        $donate_settings = apply_filters( 'donate_localize_object_settings', array(
            'settings' => DN_Settings::instance()->_options,
            'i18n' => donate_18n_languages(),
            'ajaxurl' => admin_url( 'admin-ajax.php?schema=donate-ajax' ),
            'nonce' => wp_create_nonce( 'thimpress_donate_nonce' ),
            'date_format' => get_option( 'date_format', 'Y-m-d' ),
            'time_format' => get_option( 'time_format', 'H:i:s' )
         ) );

        self::localize_script( 'donate-global', apply_filters( 'thimpress_donate_localize', 'thimpress_donate' ), $donate_settings );

        if ( self::$_scripts ) {
            foreach ( self::$_scripts as $handle => $param ) {
                call_user_func_array( 'wp_register_script', $param );
                if ( array_key_exists( $handle, self::$_localize_scripts ) ) {
                    call_user_func_array( 'wp_localize_script', self::$_localize_scripts[$handle] );
                }
                wp_enqueue_script( $handle );
            }
        }

        self::register_style( 'donate-global', TP_DONATE_ASSETS_URI . '/css/donate.css' );
        if ( self::$_styles ) {
            foreach ( self::$_styles as $handle => $param ) {
                call_user_func_array( 'wp_register_style', $param );
                wp_enqueue_style( $handle );
            }
        }
        
        /**
         * After enqueue scripts
         */
        do_action( 'donate_after_enqueue_scripts', $hook );
    }

    /**
     * Get file uri.
     * if WP_DEBUG is FALSE will load minify file
     */
    public static function _get_file_uri( $uri = '' ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            return $uri;
        }
        $file = self::_get_path_by_uri( $uri );
        $file_name = basename( $file );
        $parse = explode( '.', $file_name );
        /**
         * file's extension '.js' or '.css'
         */
        $file_type = end( $parse );
        if ( in_array( 'min', $parse ) ) {
            return $uri;
        }

        array_pop( $parse );
        $parse[] = 'min';
        $parse[] = $file_type;
        $new_file = implode( '.', $parse );
        
        $new_uri = str_replace( $file_name, $new_file, $uri );
        $new_path = self::_get_path_by_uri( $new_uri );
        if ( file_exists( $new_path ) ){
            return $new_uri;
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
