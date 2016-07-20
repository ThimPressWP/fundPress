<?php

if ( !defined( 'ABSPATH' ) ) {
    exit();
}

class DN_Autoloader {

    /**
     * Path to the inc directory
     * @var string
     */
    private $include_path = '';

    public function __construct() {
        if ( function_exists( '__autoload' ) ) {
            spl_autoload_register( '__autoload' );
        }

        spl_autoload_register( array( $this, 'autoload' ) );

        $this->include_path = untrailingslashit( TP_DONATE_PATH ) . '/inc/';
    }

    private function get_file_name_from_class( $class ) {
        if ( $class === 'DN_Metabox_Campaign' ) {
            'class-' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
        }
        return 'class-' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
    }

    private function load_file( $path ) {
        if ( $path && is_readable( $path ) ) {
            include_once( $path );
            return true;
        }
        return false;
    }

    public function autoload( $class ) {
        $class = strtolower( $class );

        $file = $this->get_file_name_from_class( $class );
        $path = $this->include_path;
        // gateways
        if ( strpos( $class, 'dn_payment_' ) === 0 ) {
            $payment = substr( str_replace( '_', '-', $class ), strlen( 'dn_payment_' ) );
            $path = $this->include_path . 'gateways/' . $payment . '/';
        }

        // widgets
        if ( stripos( $class, 'dn_widget_' ) === 0 ) {
            $path = $this->include_path . 'widgets/';
        }

        // metaboxes
        if ( strpos( $class, 'dn_metabox_' ) === 0 ) {
            $path = $this->include_path . 'admin/metaboxes/';
        }

        $this->load_file( $path . $file );
    }

}

new DN_Autoloader();
