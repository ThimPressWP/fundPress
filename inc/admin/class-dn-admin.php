<?php

if ( !defined( 'ABSPATH' ) ) {
    exit();
}

class DN_Admin {

    public function __construct() {
        add_action( 'init', array( $this, 'includes' ) );
    }

    public function includes() {
        ThimPress_Donate::instance()->_include( 'inc/admin/functions.php' );
        ThimPress_Donate::instance()->_include( 'inc/admin/class-dn-admin-menu.php' );
        ThimPress_Donate::instance()->_include( 'inc/admin/class-dn-admin-metabox.php' );
        ThimPress_Donate::instance()->_include( 'inc/admin/class-dn-admin-assets.php' );
    }

}

new DN_Admin();
