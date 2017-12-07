<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class DN_Admin {

	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
	}

	public function includes() {
		FP()->_include( 'inc/admin/functions.php' );
		FP()->_include( 'inc/admin/class-dn-admin-menu.php' );
		FP()->_include( 'inc/admin/class-dn-admin-metabox.php' );
		FP()->_include( 'inc/admin/class-dn-admin-assets.php' );
	}

}

new DN_Admin();
