<?php
/**
 * Fundpress Campaign class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Autoloader' ) ) {
	/**
	 * Class DN_Autoloader.
	 */
	class DN_Autoloader {

		/**
		 * @var string
		 */
		private $include_path = '';

		/**
		 * DN_Autoloader constructor.
		 */
		public function __construct() {
			if ( function_exists( '__autoload' ) ) {
				spl_autoload_register( '__autoload' );
			}

			spl_autoload_register( array( $this, 'autoload' ) );

			$this->include_path = FUNDPRESS_INC;
		}

		/**
		 * Autoload file.
		 *
		 * @param $class
		 */
		public function autoload( $class ) {
			$class = strtolower( $class );

			$file = $this->get_file_name_from_class( $class );
			$path = $this->include_path;
			// gateways
			if ( strpos( $class, 'dn_payment_' ) === 0 ) {
				$payment = substr( str_replace( '_', '-', $class ), strlen( 'dn_payment_' ) );
				$path    = $this->include_path . 'gateways/' . $payment . '/';
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

		/**
		 * Get file name form class.
		 *
		 * @param $class
		 *
		 * @return string
		 */
		private function get_file_name_from_class( $class ) {
			return 'class-' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
		}

		/**
		 * Load file.
		 *
		 * @param $path
		 *
		 * @return bool
		 */
		private function load_file( $path ) {
			if ( $path && is_readable( $path ) ) {
				include_once( $path );

				return true;
			}

			return false;
		}

	}
}

new DN_Autoloader();
