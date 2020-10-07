<?php
/**
 * Fundpress Authorize.Net payment gateway class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Payment_Authorize_Net' ) ) {
	/**
	 * Class DN_Payment_Authorize_Net.
	 */
	class DN_Payment_Authorize_Net extends DN_Payment_Base {

		/**
		 * @var string
		 */
		public $id = 'authorize';

		/**
		 * @var null
		 */
		protected $_api_login_id = null;

		/**
		 * @var null
		 */
		protected $_transaction_key = null;

		/**
		 * api endpoint
		 *
		 * @var string
		 */
		protected $api_endpoint = 'https://test.authorize.net/gateway/transact.dll';

		/**
		 * @var array|null
		 */
		public $_messages = null;

		/**
		 * @var null|string|void
		 */
		public $_title = null;

		/**
		 * DN_Payment_Authorize_Net constructor.
		 */
		public function __construct() {
			$this->_title = __( 'AuthorizeNet', 'fundpress' );

			$checkout               = FP()->settings->checkout;
			$this->_api_login_id    = $checkout->get( 'authorize_api_login_id' );
			$this->_transaction_key = $checkout->get( 'authorize_transaction_key' );
			$this->icon             = 'icon-text-color';

			// production environment
			if ( $checkout->get( 'environment' ) === 'production' ) {
				$this->api_endpoint = 'https://secure.authorize.net/gateway/transact.dll';
			}
			parent::__construct();

			$this->_messages = array(
				1 => __( 'This transaction has been approved.', 'fundpress' ),
				2 => __( 'This transaction has been declined.', 'fundpress' ),
				3 => __( 'There has been an error processing this transaction.', 'fundpress' ),
				4 => __( ' This transaction is being held for review.', 'fundpress' )
			);

			add_action( 'init', array( $this, 'verify' ) );
		}

		/**
		 * Verify payment.
		 */
		public function verify() {
			ob_start();
			if ( ! isset( $_POST ) ) {
				return;
			}

			if ( ! isset( $_POST['x_response_code'] ) ) {
				return;
			}

			if ( isset( $_POST['x_response_reason_text'] ) ) {
				donate_add_notice( 'error', DN_Helpper::DN_sanitize_params_submitted( $_POST['x_response_reason_text'] ) );
			}

			$code = 0;
			if ( isset( $_POST['x_response_code'] ) && array_key_exists( (int) $_POST['x_response_code'], $this->_messages ) ) {
				$code = (int) $_POST['x_response_code'];
			}

			$amout = 0;
			if ( isset( $_POST['x_amount'] ) ) {
				$amout = (float) $_POST['x_amount'];
			}

			if ( ! isset( $_POST['x_invoice_num'] ) ) {
				return;
			}

			$id     = (int) $_POST['x_invoice_num'];
			$donate = DN_Donate::instance( $id );

			if ( $code === 1 ) {
				if ( (float) $donate->total === (float) $amout ) {
					$status = 'donate-completed';
				} else {
					$status = 'donate-processing';
				}
			} else {
				$status = 'donate-pending';
			}

			$donate->update_status( $status );
			FP()->cart->remove_cart();
			ob_end_clean();

			// redirect
			wp_redirect( donate_get_thankyou_link( $donate->id ) );
			exit();
		}

		/**
		 * Payment setting fields.
		 *
		 * @return array
		 */
		public function fields() {
			return array(
				'title'  => $this->_title,
				'fields' => array(
					'fields' => array(
						array(
							'type'    => 'select',
							'label'   => __( 'Enable', 'fundpress' ),
							'desc'    => __( 'This controls enable payment method', 'fundpress' ),
							'atts'    => array(
								'id'    => 'authorize_enable',
								'class' => 'authorize_enable'
							),
							'name'    => 'authorize_enable',
							'options' => array(
								'no'  => __( 'No', 'fundpress' ),
								'yes' => __( 'Yes', 'fundpress' )
							)
						),
						array(
							'type'  => 'input',
							'label' => __( 'Api Login ID', 'fundpress' ),
							'desc'  => __( 'Api login id', 'fundpress' ),
							'atts'  => array(
								'id'    => 'authorize_api_login_id',
								'class' => 'authorize_api_login_id',
								'type'  => 'text'
							),
							'name'  => 'authorize_api_login_id'
						),
						array(
							'type'  => 'input',
							'label' => __( 'Transaction Key', 'fundpress' ),
							'desc'  => __( 'Transaction key', 'fundpress' ),
							'atts'  => array(
								'id'    => 'authorize_transaction_key',
								'class' => 'authorize_transaction_key',
								'type'  => 'text'
							),
							'name'  => 'authorize_transaction_key'
						)
					)
				)
			);
		}

		/**
		 * Checkout args.
		 *
		 * @param null  $donate
		 * @param array $posted
		 *
		 * @return array|mixed
		 */
		public function checkout_args( $donate = null, $posted = array() ) {
			if ( ! $this->_transaction_key ) {
				return array( 'status' => 'failed', 'message' => __( 'Transaction Key is invalid.', 'fundpress' ) );
			}

			$donor = DN_Donor::instance( $donate->donor_id );

			$total = $donate->total;

			$time = time();
			if ( function_exists( 'hash_hmac' ) ) {
				$fingerprint = hash_hmac(
					"md5", $this->_api_login_id . "^" . $donate->id . "^" . $time . "^" . $total . "^" . donate_get_currency(), $this->_transaction_key
				);
			} else {
				$fingerprint = bin2hex( mhash( MHASH_MD5, $this->_api_login_id . "^" . $donate->id . "^" . $time . "^" . $total . "^" . donate_get_currency(), $this->_transaction_key ) );
			}

			$nonce = wp_create_nonce( 'donate-authorize-net-nonce' );

			// 4007000000027
			$authorize_args = array(
				'x_login'               => $this->_api_login_id,
				'x_amount'              => $total,
				'x_currency_code'       => donate_get_currency(),
				'x_invoice_num'         => $donate->id,
				'x_relay_response'      => 'FALSE',
				'x_relay_url'           => donate_checkout_url(),
				'x_fp_sequence'         => $donate->id,
				'x_fp_hash'             => $fingerprint,
				'x_show_form'           => 'PAYMENT_FORM',
				'x_version'             => '3.1',
				'x_fp_timestamp'        => $time,
				'x_first_name'          => $donor->first_name,
				'x_last_name'           => $donor->last_name,
				'x_address'             => $donor->address,
				'x_phone'               => $donor->phone,
				'x_email'               => $donor->email,
				'x_type'                => 'AUTH_CAPTURE',
				'x_cancel_url'          => donate_checkout_url(),
				'x_email_customer'      => 'TRUE',
				'x_cancel_url_text'     => __( 'Cancel', 'fundpress' ),
				'x_receipt_link_method' => 'POST',
				'x_receipt_link_text'   => __( 'Click here to return our homepage.', 'fundpress' ),
				'x_receipt_link_URL'    => add_query_arg( array(
					'donate-authorize-net-status' => 'completed',
					'donate-authorize-net-nonce'  => $nonce
				), donate_checkout_url() ),
			);

			if ( FP()->settings->checkout->get( 'environment' ) === 'production' ) {
				$authorize_args['x_test_request'] = 'FALSE';
			} else {
				$authorize_args['x_test_request'] = 'TRUE';
			}

			$authorize_args = apply_filters( 'donate_payment_authorize_net_args', $authorize_args );

			return $authorize_args;
		}

		/**
		 * Checkout process.
		 *
		 * @param null  $donate
		 * @param array $posted
		 *
		 * @return array|null
		 */
		public function process( $donate = null, $posted = array() ) {
			return array(
				'status'      => 'success',
				'form'        => true,
				'submit_text' => __( 'Redirect to Authorize.Net', 'fundpress' ),
				'url'         => $this->api_endpoint,
				'args'        => $this->checkout_args( $donate, $posted )
			);
		}
	}
}