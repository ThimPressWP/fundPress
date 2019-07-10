<?php
/**
 * Fundpress Abstract payment gateway class.
 *
 * @version     2.0
 * @package     Abstract class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Payment_Base' ) ) {
	/**
	 * Class DN_Payment_Base.
	 */
	abstract class DN_Payment_Base {

		/**
		 * @var null
		 */
		protected $id = null;

		/**
		 * @var null
		 */
		protected $_title = null;

		/**
		 * @var bool
		 */
		public $is_enable = true;

		/**
		 * @var null
		 */
		public $icon = null;

		/**
		 * DN_Payment_Base constructor.
		 */
		public function __construct() {
			// generate fields settings
			add_filter( 'donate_admin_setting_fields', array( $this, 'generate_fields' ), 10, 2 );
			// check payment enable
			$this->is_enable();
		}

		/**
		 * Get title.
		 *
		 * @return null
		 */
		public function get_title() {
			return $this->_title;
		}

		/**
		 * Checkout process.
		 *
		 * @param bool $donate
		 * @param null $posted
		 *
		 * @return bool
		 */
		protected function process( $donate = false, $posted = null ) {
			return false;
		}

		/**
		 * Refund action.
		 */
		protected function refund() {
			return false;
		}

		/**
		 * Payment send mail.
		 *
		 * @return bool
		 */
		public function send_email() {
			return false;
		}

		/**
		 * Generate settings fields.
		 *
		 * @param $groups
		 * @param $id
		 *
		 * @return mixed
		 */
		public function generate_fields( $groups, $id ) {
			if ( $id === 'checkout' && $this->id ) {

				$groups[ $id . '_' . $this->id ] = apply_filters( 'donate_admin_setting_fields_checkout', $this->fields(), $this->id );
			}

			return $groups;
		}

		/**
		 * Admin setting fields.
		 *
		 * @return array
		 */
		public function fields() {
			return array();
		}

		/**
		 * Check payment enable.
		 *
		 * @return bool
		 */
		public function is_enable() {
			if ( FP()->settings->checkout->get( $this->id . '_enable', 'yes' ) === 'yes' ) {
				return $this->is_enable = true;
			}

			return $this->is_enable = false;
		}

		/**
		 * Checkout form.
		 *
		 * @return null
		 */
		public function checkout_form() {
			return null;
		}

		/**
		 * Add notice message completed when payment completed.
		 */
		public function completed_process_message() {
			if ( ! donate_has_notice( 'success' ) ) {
				donate_add_notice( 'success', __( 'Payment completed. We will send you email when payment method verify.', 'fundpress' ) );
			}
		}

	}
}