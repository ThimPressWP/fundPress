<?php

if ( !defined( 'ABSPATH' ) )
    exit();

class DN_Settings {

    /**
     * $_options
     * @var null
     */
    public $_options = null;
    public $_id = null;

    /**
     * prefix option name
     * @var string
     */
    public $_prefix = 'thimpress_donate';

    /**
     * _instance
     * @var null
     */
    static $_instance = null;

    public function __construct( $prefix = null, $id = null ) {
        if ( $prefix ) {
            $this->_prefix = $prefix;
        }

        $this->_id = $id;

        // load options
        $this->options();

        // save, update setting
        add_filter( 'donate_admnin_menus', array( $this, 'setting_page' ), 10, 1 );
        add_action( 'admin_init', array( $this, 'register_setting' ) );
    }

    public function __get( $id = null ) {
        $settings = apply_filters( 'donate_settings_field', array() );
        if ( array_key_exists( $id, $settings ) ) {
            return $settings[$id];
        }
        return null;
    }

    /**
     * generate setting page
     * @param  $menus array
     * @return array $menus
     */
    public function setting_page( $menus ) {
        $menus[] = array( 'tp_donate', __( 'TP Donate Settings', 'fundpress' ), __( 'Settings', 'fundpress' ), 'manage_options', 'tp_donate_setting', array( $this, 'register_options_page' ) );
        return $menus;
    }

    /**
     * register option page
     * @return
     */
    public function register_options_page() {
        donate()->_include( 'inc/admin/views/settings.php' );
    }

    public function register_setting() {
        register_setting( $this->_prefix, $this->_prefix );
    }

    /**
     * options load options
     * @return array || null
     */
    protected function options() {
        if ( $this->_options )
            return $this->_options;

        return $this->_options = get_option( $this->_prefix, null );
    }

    /**
     * get_name_field
     * @param  $name of field option
     * @return string name field
     */
    public function get_field_name( $name = null ) {
        if ( !$this->_prefix || !$name )
            return;

        return $this->_prefix . '[' . $name . ']';
    }

    /**
     * get_name_field
     * @param  $name of field option
     * @return string name field
     */
    public function get_field_id( $name = null, $default = null ) {
        if ( !$this->_prefix || !$name ) {
            return;
        }

        return $this->_prefix . '_' . $name;
    }

    /**
     * get option value
     * @param  $name
     * @return option value. array, string, boolean
     */
    public function get( $name = null, $default = null ) {
        if ( !$this->_options ) {
            $this->_options = $this->options();
        }

        if ( $name && isset( $this->_options[$name] ) )
            return $this->_options[$name];

        return $default;
    }

    /**
     * instance
     * @param  $prefix
     * @return object class
     */
    static function instance( $prefix = null, $id = null ) {

        if ( !empty( self::$_instance[$prefix] ) )
            return self::$_instance[$prefix];

        return self::$_instance[$prefix] = new self( $prefix, $id );
    }

}
