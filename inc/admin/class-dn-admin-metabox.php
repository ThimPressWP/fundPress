<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class DN_Admin_Metabox {

	public function __construct() {
		/* remove metabox */
		add_action( 'admin_init', array( __CLASS__, 'remove_meta_box' ) );
		add_action( 'admin_init', array( __CLASS__, 'add_meta_boxes' ) );

	}

	/* add metaboxes */
	public static function add_meta_boxes() {
		new DN_MetaBox_Campaign();
		new DN_MetaBox_Donate();
		new DN_MetaBox_Donate_Action();
	}

	/* remove metaboxes */
	public static function remove_meta_box() {
		/* remove submit div donate post type */
		remove_meta_box( 'submitdiv', 'dn_donate', 'side' );
	}
}

new DN_Admin_Metabox();
