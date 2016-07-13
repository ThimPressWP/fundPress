<?php

defined( 'ABSPATH' ) or die( "Cannot access pages directly." );

if ( !session_id() ) {
    session_start();
}

class DN_Sessions {

    // instance
    static $_instance = null;
    // $session
    public $session = null;
    // live time of cookie
    private $live_item = null;
    // remember
    private $remember = false;

    /**
     * prefix
     * @var null
     */
    public $prefix = null;

    public function __construct( $prefix = '', $remember = true ) {
        if ( !$prefix )
            return;

        $this->prefix = $prefix;
        $this->remember = $remember;

        $this->live_item = 12 * HOUR_IN_SECONDS;

        // get all
        $this->session = $this->load();
    }

    /**
     * load all with prefix
     * @return
     */
    public function load() {
        if ( isset( $_SESSION[$this->prefix] ) ) {
            return $_SESSION[$this->prefix];
        } else if ( $this->remember && isset( $_COOKIE[$this->prefix] ) ) {
            return $_SESSION[$this->prefix] = maybe_unserialize( $_COOKIE[$this->prefix] );
        }

        return array();
    }

    // remove session
    public function remove() {
        if ( isset( $_SESSION[$this->prefix] ) ) {
            unset( $_SESSION[$this->prefix] );
        }

        if ( $this->remember && isset( $_COOKIE[$this->prefix] ) ) {
            donate_setcookie( $this->prefix, '', time() - $this->live_item );
            unset( $_COOKIE[$this->prefix] );
        }
    }

    /**
     * set key
     * @param $key
     * @param $value
     */
    public function set( $name = '', $value = null ) {
        if ( !$name )
            return;

        $time = time();
        if ( !$value ) {
            unset( $this->session[$name] );
            $time = $time - $this->live_item;
        } else {
            $this->session[$name] = $value;
            $time = $time + $this->live_item;
        }

        // save session
        $_SESSION[$this->prefix] = $this->session;

        // save cookie
        donate_setcookie( $this->prefix, maybe_serialize( $this->session ), $time );
    }

    /**
     * get value
     * @param  $key
     * @return anythings
     */
    public function get( $name = null, $default = null ) {
        if ( !$name )
            return $default;

        if ( isset( $this->session[$name] ) )
            return $this->session[$name];
    }

    static function instance( $prefix = '' ) {
        if ( !empty( self::$_instance[$prefix] ) )
            return self::$_instance[$prefix];

        return self::$_instance[$prefix] = new self( $prefix );
    }

}
