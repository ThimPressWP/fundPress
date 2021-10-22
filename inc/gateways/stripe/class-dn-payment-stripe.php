<?php
/**
 * Fundpress Stripe payment gateway class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

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

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
			add_action( 'wp_footer', array( $this, 'process_script_js' ) );
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
		 * @var array $posted
		 */
		public $posted = [];

		/**
		 * @var array $form_data
		 */
		public $form_data = [];

		/**
		 * Stripe request.
		 *
		 * @param array $data
		 * @param string $api
		 * @param string $method
		 * @return array|mixed|object|string|WP_Error
		 */
		public function stripe_request( array $data, string $api = 'charges', string $method = 'POST' ) {
			$response = wp_safe_remote_post(
				$this->api_endpoint . '/' . $api,
				array(
					'method'     => $method,
					'headers'    => array(
						'Authorization' => 'Basic ' . base64_encode( $this->secret_key . ':' ),
					),
					'body'       => $data,
					'timeout'    => 70,
					'sslverify'  => false,
					'user-agent' => 'Donate ' . FUNDPRESS_VER,
				)
			);

			if ( ! is_wp_error( $response ) ) {
				$body = wp_remote_retrieve_body( $response );
				if ( $body ) {
					$body = json_decode( $body );
				}

				if ( ! empty( $body->error ) ) {
					return new WP_Error( 'stripe_error', $body->error->message );
				}

				if ( empty( $body->id ) ) {
					return new WP_Error( 'stripe_error', __( 'Stripe process went wrong', 'fundpress' ) );
				}

				return $body;
			}

			return new WP_Error( 'stripe_error', $response->get_error_message() );
		}

		public function source_send_stripe() {
			$params = array(
				'type'  => 'card',
				'owner' => array(
					'name'  => $this->form_data['customer_name'],
					'email' => $this->form_data['customer_email'],
				),
				'card'  => array(
					'number'    => $this->form_data['card_number'],
					'exp_month' => $this->form_data['expiry_month'],
					'exp_year'  => $this->form_data['expiry_year'],
					'cvc'       => $this->form_data['card_code'],
				),
				'key'   => $this->publish_key,
			);

			return $this->stripe_request( $params, 'sources' );
		}

		/**
		 * Get form data.
		 *
		 * @return WP_Error|boolean
		 */
		public function check_valid_fields() {
			try {
				if ( empty( $this->posted ) ) {
					throw new Exception( 'Request invalid', 'fundpress' );
				}

				if ( ! isset( $this->posted['stripe'], $this->posted['thimpress_donate_nonce'], $this->posted['payment_method'], $this->posted['amount'] ) ) {
					throw new Exception( 'Request invalid', 'fundpress' );
				}

				if ( ! wp_verify_nonce( $this->posted['thimpress_donate_nonce'], 'thimpress_donate_nonce' ) ) {
					throw new Exception( 'Request invalid', 'fundpress' );
				}

				if ( ! isset( $this->posted['first_name'], $this->posted['last_name'], $this->posted['email'], $this->posted['phone'] ) ) {
					throw new Exception( 'Please fill all fields required', 'fundpress' );
				}

				$zero_decimal_currencies = array( 'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF' );
				$currency                = donate_get_currency();
				$total                   = in_array( $currency, $zero_decimal_currencies ) ? (float) $this->posted['amount'] : (float) $this->posted['amount'] * 100;

				$string_expiry_info = explode( '/', $this->posted['stripe']['cc-exp'] ?? '0/0' );

				$this->form_data = array(
					'amount'               => $total,
					'currency'             => donate_get_currency(),
					'source'               => '',
					'description'          => '',
					'customer_id'          => '',
					'customer_name'        => $this->posted['last_name'] . ' ' . $this->posted['first_name'],
					'customer_email'       => $this->posted['email'],
					'site_url'             => esc_url( get_site_url() ),
					'payment_method_types' => array(
						'card',
					),
					'card_number'          => $this->posted['stripe']['cc-number'] ?? 0,
					'expiry_month'         => $string_expiry_info[0],
					'expiry_year'          => $string_expiry_info[1],
					'card_code'            => $this->posted['stripe']['cc-cvc'] ?? 0,
					'payment_intents_id'   => '',
				);
			} catch ( Throwable $e ) {
				return new WP_Error( 2, $e->getMessage() );
			}

			return true;
		}

		/**
		 * Payment checkout form.
		 *
		 * @return string
		 */
		public function checkout_form() {
			ob_start();
			require FUNDPRESS_TEMP . '/payments/stripe-checkout-form.php';

			return ob_get_clean();
		}

		/**
		 * Enqueue script.
		 */
		public function enqueue_script() {
			if ( ! $this->is_enable ) {
				return;
			}

			/*$stripe = apply_filters( 'donate_stripe_payment_object', array(
				'Secret_Key'  => $this->secret_key,
				'Publish_Key' => $this->publish_key,
				'key_missing' => __( 'Stripe key is expired. Please contact administrator to do this payment gateway', 'fundpress' )
			) );*/

			wp_register_script( 'donate_payment_stripe', FUNDPRESS_INC_URI . '/gateways/stripe/jquery.payment.min.js', array(), FUNDPRESS_VER, true );
			//wp_localize_script( 'donate_payment_stripe', 'Donate_Stripe_Settings', $stripe );

			wp_enqueue_script( 'donate_payment_stripe' );
		}

		/**
		 * Process checkout script.
		 */
		public function process_script_js() {
			?>
			<script type="text/javascript">
				(function ($) {
					if (typeof $.fn.payment !== 'undefined') {

						window.Donate_Stripe_Payment = {
							init: function () {
								$('.stripe-cc-number').payment('formatCardNumber');
								$('.stripe-cc-exp').payment('formatCardExpiry');
								$('.stripe-cc-cvc').payment('formatCardCVC');
								TP_Donate_Global.addFilter('donate_before_submit_form', this.before_submit_checkout);
							},
							before_submit_checkout: function (data) {
								var is_stripe = false;
								for (var i = 0; i < data.length; i++) {
									if (data[i].name === 'payment_method' && data[i].value === 'stripe') {
										is_stripe = true;
									}
								}
								if (is_stripe && !Donate_Stripe_Payment.validator_credit_card()) {
									return false;
								}

								return data;
							},
							// validate create card format
							validator_credit_card: function () {
								var card_num = $('.stripe-cc-number'),
									card_expiry = $('.stripe-cc-exp'),
									card_cvc = $('.stripe-cc-cvc'),
									card_type = $.payment.cardType(card_num.val());

								var validated = true;
								// validate card number
								if (!$.payment.validateCardNumber(card_num.val())) {
									validated = false;
									card_num.addClass('error').removeClass('validated');
								} else {
									card_num.addClass('validated').removeClass('error');
								}
								// validate card expired
								if (!card_expiry.val() || !$.payment.cardExpiryVal(card_expiry.val())) {
									validated = false;
									card_expiry.addClass('error').removeClass('validated');
								} else {
									card_expiry.addClass('validated').removeClass('error');
								}
								// validate card cvc
								if (!card_cvc.val() || !$.payment.validateCardCVC(card_cvc.val(), card_type)) {
									validated = false;
									card_cvc.addClass('error').removeClass('validated');
								} else {
									card_cvc.addClass('validated').removeClass('error');
								}
								return validated;
							}
						};
						$(document).ready(function () {
							Donate_Stripe_Payment.init();
						});
					}
				})(jQuery);
			</script>
			<?php
		}

		/**
		 * Checkout process.
		 *
		 * @param bool $donate
		 * @param array $posted
		 *
		 * @return array
		 */
		/*public function process_bk( $donate = false, $posted = array() ) {
			if ( ! $this->secret_key || ! $this->publish_key ) {
				return array(
					'status'  => 'failed',
					'message' => __( 'Secret key and Publish key is invalid. Please contact administrator to setup Stripe payment.', 'fundpress' )
				);
			}

			if ( empty( $posted['stripe'] ) ) {
				return array(
					'status'  => 'failed',
					'message' => __( 'Credit Card information error.', 'fundpress' )
				);
			}

			$card_number = isset( $posted['stripe']['cc-number'] ) ? sanitize_text_field( $posted['stripe']['cc-number'] ) : '';
			list( $card_exp_month, $card_exp_year ) = array_map( 'trim', explode( '/', isset( $posted['stripe']['cc-exp'] ) ? $posted['stripe']['cc-exp'] : '' ) );
			$card_cvc = isset( $posted['stripe']['cc-cvc'] ) ? sanitize_text_field( $posted['stripe']['cc-cvc'] ) : '';

			$tokens = $this->stripe_request( 'tokens', array(
				'card' => array(
					'number'    => $card_number,
					'exp_month' => $card_exp_month,
					'exp_year'  => $card_exp_year,
					'cvc'       => $card_cvc,
				)
			) );
			if ( is_wp_error( $tokens ) || ! $tokens->id ) {
				return array(
					'status'  => 'failed',
					'message' => sprintf( __( 'Please try again', 'fundpress' ) )
				);
			}

			$token = $tokens->id;

			$donor = DN_Donor::instance( $donate->donor_id );

			$customer_id = $donor->stripe_id;

			if ( ! $customer_id ) {
				$params = array(
					'description' => sprintf( '%s %s', __( 'Donor for', 'fundpress' ), $donor->email ),
					'sources_id'      => $token
				);
				// create customer
				$response = $this->stripe_request( 'customers', $params );

				if ( is_wp_error( $response ) && ! $response->id ) {
					return array(
						'status'  => 'failed',
						'message' => sprintf( '%s. ' . __( 'Please try again', 'fundpress' ), $response->get_error_message() )
					);
				}

				$customer_id = $response->id;

				$donor->set_meta( 'stripe_id', $customer_id );
			}

			$total = $donate->total;

			$params = array(
				'amount'      => round( $total * 100 ),
				'currency'    => donate_get_currency(),
				'customer'    => $customer_id,
				'description' => sprintf(
					__( '%s - donate %s', 'fundpress' ), esc_html( get_bloginfo( 'name' ) ), donate_generate_post_key( $donate->id )
				)
			);
			// create charges
			$response = $this->stripe_request( 'charges', $params );
			if ( $response && ! is_wp_error( $response ) && $response->id ) {
				$donate->update_status( 'donate-completed' );

				// notice message completed
				$this->completed_process_message();

				$return = array(
					'status' => 'success',
					'url'    => donate_get_thankyou_link( $donate->id )
				);
				// remove cart
				FP()->cart->remove_cart();
			} else {
				$return = array(
					'result'  => 'failed',
					'message' => __( 'Connect Stripe has error. Please try again!', 'fundpress' )
				);
			}

			return $return;
		}*/

		public function process( $donate = false, $posted = array() ): array {
			$response = [
				'status'  => 'failed',
				'message' => '',
			];

			try {
				if ( ! $this->secret_key || ! $this->publish_key ) {

					$response['message'] = __( 'Secret key and Publish key is invalid. Please contact administrator to setup Stripe payment.', 'fundpress' );
				}

				if ( empty( $posted['stripe'] ) ) {
					return array(
						'status'  => 'failed',
						'message' => __( 'Credit Card information error.', 'fundpress' ),
					);
				}

				$this->posted = $posted;

				$check_valid_fields = $this->check_valid_fields();
				if ( $check_valid_fields instanceof WP_Error ) {
					throw new Exception( $check_valid_fields->get_error_message() );
				}

				// Create sources allow you to accept a variety of payment methods.
				// Read more on https://stripe.com/docs/api/sources
				$sources = $this->source_send_stripe();
				if ( $sources instanceof WP_Error ) {
					throw new Exception( $sources->get_error_message() );
				}

				// Set sources id
				$this->form_data['sources_id'] = $sources->id;

				// Creat customer
				$customer = $this->customer_payment_stripe();
				if ( $customer instanceof WP_Error ) {
					throw new Exception( $customer->get_error_message() );
				}

				$this->form_data['customer_id'] = $customer->id;

				//
				$payment_intents = $this->send_payment_intents();
				if ( $payment_intents instanceof WP_Error ) {
					throw new Exception( $payment_intents->get_error_message() );
				}

				$this->form_data['payment_intents_id'] = $payment_intents->id;
				$confirm_payment_intents               = $this->confirm_payment_stripe();
				if ( $confirm_payment_intents instanceof WP_Error ) {
					throw new Exception( $confirm_payment_intents->get_error_message() );
				}

				if ( 'requires_action' === $confirm_payment_intents->status ) {
					update_post_meta( $donate->id, '_dn_stripe_intent_id', $confirm_payment_intents->id );

					$redirect = sprintf( '%s#confirm-pi-%s', 'http://lp.local/lp-checkout', $confirm_payment_intents->client_secret );

					$response['status']   = 'success';
					$response['redirect'] = $redirect;
				} elseif ( 'succeeded' === $confirm_payment_intents->status ) {
					/*learn_press_delete_order_item_meta( $this->order->id, '_lp_stripe_intent_id', $intent->id );

					$this->order_complete();

					return array(
						'result'   => 'success',
						'redirect' => $this->get_return_url( $this->order ),
					);*/
				}
			} catch ( Exception $e ) {
				$response['message'] = $e->getMessage();
			}

			return $response;
		}

		public function customer_payment_stripe() {
			$params = array(
				'email'       => $this->form_data['customer_email'],
				'name'        => $this->form_data['customer_name'],
				'description' => $this->form_data['customer_name'],
			);

			return $this->stripe_request( $params, 'customers' );
		}

		public function send_payment_intents() {
			$params = array(
				'source'               => $this->form_data['sources_id'],
				'amount'               => $this->form_data['amount'],
				'currency'             => $this->form_data['currency'],
				'description'          => $this->form_data['description'],
				'capture_method'       => 'automatic',
				'payment_method_types' => $this->form_data['payment_method_types'],
				'customer'             => $this->form_data['customer_id'],
				'metadata'             => array(
					'customer_name'  => $this->form_data['customer_name'],
					'customer_email' => $this->form_data['customer_email'],
					'site_url'       => $this->form_data['site_url'],
				),
			);

			return $this->stripe_request( $params, 'payment_intents' );
		}

		public function confirm_payment_stripe() {
			$params = array(
				'source' => $this->form_data['sources_id'],
			);

			return $this->stripe_request( $params, 'payment_intents/' . $this->form_data['payment_intents_id'] . '/confirm' );
		}
	}
}
