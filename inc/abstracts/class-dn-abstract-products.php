<?php
/**
 * Fundpress Product campaign class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Product_Base' ) ) {
	/**
	 * Class DN_Product_Base.
	 */
	abstract class DN_Product_Base {

		/**
		 * @var int
		 */
		protected $tax = 0;

		/**
		 * DN_Product_Base constructor.
		 */
		public function __construct() {

		}

		/**
		 * Get amount exclude tax.
		 */
		protected function amount_exclude_tax() {

		}

		/**
		 * Get amount include tax.
		 */
		protected function amount_include_tax() {

		}
	}
}
