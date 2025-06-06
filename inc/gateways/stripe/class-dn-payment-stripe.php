<?php
/**
 * Fundpress Stripe payment gateway class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Payment_Stripe' ) ) {
	/**
	 * Class DN_Payment_Stripe.
	 */
	class DN_Payment_Stripe extends DN_Payment_Base {

		/**
		 * @var string
		 */
		public $id = 'stripe';

		/**
		 * @var null
		 */
		protected $secret_key = null;

		/**
		 * @var null
		 */
		protected $publish_key = null;

		/**
		 * api endpoint
		 *
		 * @var string
		 */
		protected $api_endpoint = 'https://api.stripe.com/v1';

		/**
		 * @var null|string|void
		 */
		public $_title = null;

		/**
		 * DN_Payment_Stripe constructor.
		 */
		public function __construct() {
			$this->_title = __( 'Stripe', 'fundpress' );

			$checkout          = FP()->settings->checkout;
			$this->secret_key  = $checkout->get( 'stripe_test_secret_key' );
			$this->publish_key = $checkout->get( 'stripe_test_publish_key' );
			$this->icon        = 'icon-credit-card';

			// production environment
			if ( $checkout->get( 'environment' ) === 'production' ) {
				$this->secret_key  = $checkout->get( 'stripe_live_secret_key' );
				$this->publish_key = $checkout->get( 'stripe_live_publish_key' );
			}

			parent::__construct();
			if ( ! is_admin() ) {
				add_action( 'init', array( $this, 'verify_payment' ), 999 );
			}
		}

		/**
		 * Payment setting fields.
		 *
		 * @return array
		 */
		public function fields() {
			return array(
				'title'  => $this->_title, // tab title
				'fields' => array(
					'fields' => array(
						array(
							'type'    => 'select',
							'label'   => __( 'Enable', 'fundpress' ),
							'desc'    => __( 'This controls enable payment method', 'fundpress' ),
							'atts'    => array(
								'id'    => 'stripe_enable',
								'class' => 'stripe_enable',
							),
							'name'    => 'stripe_enable',
							'options' => array(
								'no'  => __( 'No', 'fundpress' ),
								'yes' => __( 'Yes', 'fundpress' ),
							),
						),
						array(
							'type'  => 'input',
							'label' => __( 'Test Secret Key', 'fundpress' ),
							'desc'  => __( 'Test environment', 'fundpress' ),
							'atts'  => array(
								'id'    => 'stripe_test_secret_key',
								'class' => 'stripe_test_secret_key',
								'type'  => 'text',
							),
							'name'  => 'stripe_test_secret_key',
						),
						array(
							'type'  => 'input',
							'label' => __( 'Test Publish Key', 'fundpress' ),
							'desc'  => __( 'Test environment', 'fundpress' ),
							'atts'  => array(
								'id'    => 'stripe_test_publish_key',
								'class' => 'stripe_test_publish_key',
								'type'  => 'text',
							),
							'name'  => 'stripe_test_publish_key',
						),
						array(
							'type'  => 'input',
							'label' => __( 'Live Secret Key', 'fundpress' ),
							'desc'  => __( 'Production environment', 'fundpress' ),
							'atts'  => array(
								'id'    => 'stripe_live_secret_key',
								'class' => 'stripe_live_secret_key',
								'type'  => 'text',
							),
							'name'  => 'stripe_live_secret_key',
						),
						array(
							'type'  => 'input',
							'label' => __( 'Live Publish Key', 'fundpress' ),
							'desc'  => __( 'Production environment', 'fundpress' ),
							'atts'  => array(
								'id'    => 'stripe_live_publish_key',
								'class' => 'stripe_live_publish_key',
								'type'  => 'text',
							),
							'name'  => 'stripe_live_publish_key',
						),
					),
				),
			);
		}

		/**
		 * Payment checkout form.
		 *
		 * @return string
		 */
		public function checkout_form() {
			return '';
			// ob_start();
			// require FUNDPRESS_TEMP . '/payments/stripe-checkout-form.php';

			// return ob_get_clean();
		}

		public function process( $donate = false, $posted = array() ): array {

			$response = [
				'status'  => 'failed',
				'message' => '',
			];

			try {

				if ( ! $this->secret_key || ! $this->publish_key ) {

					$response['message'] = __( 'Secret key and Publish key is invalid. Please contact administrator to setup Stripe payment.', 'fundpress' );
				}
				$amount = sanitize_text_field( $posted['amount'] );

				if ( ! $amount ) {
					return array(
						'status'  => 'failed',
						'message' => __( 'Amount is required.', 'fundpress' ),
					);
				}
				$donate_id = $donate->id;
				$redirect  = $this->get_stripe_redirect_url( $donate_id, $amount );
				$response  = array(
					'status'   => 'success',
					'redirect' =>  $redirect,
				);
			} catch ( Exception $e ) {
				$response['message'] = $e->getMessage();
			}

			return $response;
		}
		public function get_stripe_redirect_url( $donate_id, $amount ) {
			$stripe                  = new StripeClient( $this->secret_key );
			$success_url             = donate_get_thankyou_link( $donate_id );
			$cancel_url              = donate_checkout_url();
			$stripe_checkout_session = $stripe->checkout->sessions->create(
				array(
					'line_items'  => [
						[
							'price_data' => array(
								'currency'     => strtolower( donate_get_currency() ),
								'product_data' => array(
									'name' => sprintf( __( 'Donate %s', 'fundpress' ), $donate_id ),
								),
								'unit_amount'  => $this->calculate_amount( $amount ),
							),
							'quantity'   => 1,
						],
					],
					'mode'        => 'payment',
					'success_url' => add_query_arg( 'fp_stripe_session_id', '{CHECKOUT_SESSION_ID}', $success_url ),
					'cancel_url'  => $cancel_url,
					'metadata'    => array( 'fp_donate_id' => $donate_id ),
				)
			);

			return $stripe_checkout_session->url;
		}
		public function verify_payment() {
			if ( ! isset( $_REQUEST['fp_stripe_session_id'] ) ) {
				return;
			}
			if ( ! isset( $_REQUEST['donate-id'] ) ) {
				return;
			}
			$checkout_session_id = sanitize_text_field( $_REQUEST['fp_stripe_session_id'] );
			$stripe   = new StripeClient( $this->secret_key );
			$retrieve = $stripe->checkout->sessions->retrieve( $checkout_session_id );
			if ( $retrieve->payment_status === 'paid' ) {
				$donate_id = $retrieve->metadata->fp_donate_id ?? 0;
				$donate = DN_Donate::instance( $donate_id );
				if ( ! $donate ) {
					return;
				}
				if ( $donate->has_status( 'completed' ) ) {
					return;
				}
				$donate->update_status( 'donate-completed' );
				$donate->update_meta( 'transaction_id', $retrieve->payment_intent );
				FP()->cart->remove_cart();
			}
		}
		public function calculate_amount( $amount ) {
			$supported_currencies = $this->supported_currencies();
			$currency             = donate_get_currency();
			if ( in_array( $currency, $supported_currencies['zero-decimal'] ) ) {
				$amount = (int) $amount;
			} elseif ( in_array( $currency, $supported_currencies['three-decimal'] ) ) {
				$amount = round( $amount, 2 ) * 1000;
			} elseif ( in_array( $currency, $supported_currencies['special-case'] ) ) {
				$amount = (int) $amount * 100;
			} else {
				$amount = $amount * 100;
			}

			return $amount;
		}
		public function supported_currencies() {
			return array(
				'zero-decimal'  => array( 'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF' ),
				'three-decimal' => array( 'TND', 'OMR', 'KWD', 'JOD', 'BHD' ),
				'special-case'  => array( 'ISK', 'HUF', 'TWD', 'UGX' ),
			);
		}
	}
}
