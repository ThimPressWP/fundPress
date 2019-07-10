<?php
/**
 * Fundpress Admin class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Admin' ) ) {
	/**
	 * Class DN_Admin.
	 */
	class DN_Admin {

		/**
		 * DN_Admin constructor.
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'includes' ) );
		}

		/**
		 * Includes files.
		 */
		public function includes() {
			FP()->_include( 'inc/admin/dn-admin-functions.php' );
			FP()->_include( 'inc/admin/class-dn-admin-menu.php' );
			FP()->_include( 'inc/admin/class-dn-admin-metabox.php' );
			FP()->_include( 'inc/admin/class-dn-admin-assets.php' );
		}
	}
}

new DN_Admin();
