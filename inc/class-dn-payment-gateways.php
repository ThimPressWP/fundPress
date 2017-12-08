<?php
/**
 * Fundpress Payment gateways class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Payment_Gateways' ) ) {
	/**
	 * Class DN_Payment_Gateways.
	 */
	class DN_Payment_Gateways {

		/**
		 * @var null
		 */
		public static $instance = null;

		/**
		 * @var array
		 */
		public $payment_gateways = array();

		/**
		 * DN_Payment_Gateways constructor.
		 */
		public function __construct() {
			$this->load_payment_gateways();
		}

		/**
		 * Load payment gateways.
		 *
		 * @return array
		 */
		public function load_payment_gateways() {
			$payment_gateways = array(
				'DN_Payment_Paypal',
				'DN_Payment_Stripe',
				'DN_Payment_Authorize_Net'
			);

			$payment_gateways = apply_filters( 'donate_payment_gateways', $payment_gateways );

			foreach ( $payment_gateways as $payment ) {
				$payment = class_exists( $payment ) ? new $payment : null;
				if ( $payment ) {
					$this->payment_gateways[ $payment->id ] = $payment;
				}
			}

			// return all payment activated
			return $this->payment_gateways;
		}

		/**
		 * Get payment available already to process checkout.
		 *
		 * @return mixed
		 */
		public function get_payment_available() {
			$payment_gateways_available = array();
			foreach ( $this->payment_gateways as $id => $payment ) {
				if ( $payment->is_enable ) {
					$payment_gateways_available[ $id ] = $payment;
				}
			}

			return apply_filters( 'donate_payment_gateways_enable', $payment_gateways_available );
		}

		/**
		 * @return DN_Payment_Gateways|null
		 */
		public static function instance() {
			if ( ! self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
}