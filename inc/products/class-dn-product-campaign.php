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

if ( ! class_exists( 'DN_Product_Campaign' ) ) {
	/**
	 * Class DN_Product_Campaign.
	 */
	class DN_Product_Campaign extends DN_Product_Base {

		/**
		 * @var int
		 */
		public $tax = 0;

		/**
		 * DN_Product_Campaign constructor.
		 */
		public function __construct() {

		}

		/**
		 * Get amount exclude tax.
		 *
		 * @return int
		 */
		public function amount_exclude_tax() {
			return 1;
		}

		/**
		 * Get amount include tax.
		 *
		 * @return int
		 */
		public function amount_include_tax() {
			return 1;
		}
	}
}