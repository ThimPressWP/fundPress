<?php
/**
 * Fundpress Checkout class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Checkout' ) ) {
	/**
	 * Class DN_Checkout
	 */
	class DN_Checkout {

		/**
		 * @var null
		 */
		private $posted = null;

		/**
		 * @var null
		 */
		public static $instance = null;

		/**
		 * Process checkout.
		 */
		public function process_checkout() {
			try {
				if ( ! isset( $_POST['thimpress_donate_nonce'] ) || ! wp_verify_nonce( DN_Helpper::DN_sanitize_params_submitted( $_POST['thimpress_donate_nonce'] ), 'thimpress_donate_nonce' ) ) {
					throw new Exception( __( 'We were unable to process your order, please try again.', 'fundpress' ) );
				}

				$amount = 0;
				if ( isset( $_POST['donate_input_amount'] ) ) {
					$amount = DN_Helpper::DN_sanitize_params_submitted( $_POST['donate_input_amount'] );
				}

				/* set global posted */
				$this->posted = $_POST;

				// donate within campaign
				if ( isset( $this->posted['campaign_id'] ) && is_numeric( $this->posted['campaign_id'] ) ) {
					// get campaign
					$campaign = get_post( DN_Helpper::DN_sanitize_params_submitted( $this->posted['campaign_id'] ) );

					if ( ! $campaign || $campaign->post_type !== 'dn_campaign' ) {
						donate_add_notice( 'error', __( 'Campaign is invalid.', 'fundpress' ) );
					}
					$campaign = DN_Campaign::instance( $campaign );

					if ( ! $amount && isset( $this->posted['donate_input_amount_package'] ) ) {

						$compensate_id = sanitize_text_field( $this->posted['donate_input_amount_package'] );
						// Campaign
						$compensates = $campaign->get_compensate();

						if ( isset( $compensates[ $compensate_id ], $compensates[ $compensate_id ]['amount'] ) ) {
							$amount = donate_campaign_convert_amount( $compensates[ $compensate_id ]['amount'], $campaign->get_currency() );
						}
					}

					if ( $amount == 0 ) {
						throw new Exception( __( 'Please enter donation amount.', 'fundpress' ) );
					}
					if ( $campaign->type == 'fixed' && $amount > donate_goal_campagin( $campaign->id ) ) {
						throw new Exception( __( 'Custom donate amount overcome campaign goal.', 'fundpress' ) );
					}

					// add to cart param
					$cart_params = apply_filters( 'donate_add_to_cart_item_params', array(
						'currency' => donate_get_currency()
					) );

					if ( ! donate_campaign_is_allow_donate( $campaign->id ) ) {
						throw new Exception( __( 'This Campaign currently is not allow donate.', 'fundpress' ) );
					}

					// add to cart
					$cart_item_id = FP()->cart->add_to_cart( $campaign->id, $cart_params, 1, $amount );
					if ( ! $cart_item_id || is_wp_error( $cart_item_id ) ) {
						// failed
						throw new Exception( __( 'Something went wrong, could not add to cart item. Please try again.', 'fundpress' ) );
					}
				}

				// process checkout
				$donate_system = false;
				if ( isset( $this->posted['payment_process'] ) && $this->posted['payment_process'] ) {
					if ( isset( $this->posted['donate_system'] ) && DN_Helpper::DN_sanitize_params_submitted( $this->posted['donate_system'] ) == 1 ) {
						$donate_system = true;
					}

					/* donate total */
					if ( ( $donate_system === false && FP()->cart->cart_total == 0 ) || ( $donate_system === true && $amount <= 0 ) ) {
						donate_add_notice( 'error', sprintf( '%s', __( 'Please enter donation amount.', 'fundpress' ) ) );
					}

					/* VALIDATE POST FIELDS */
					if ( ! isset( $this->posted['first_name'] ) || trim( DN_Helpper::DN_sanitize_params_submitted( $this->posted['first_name'] ) ) === '' ) {
						donate_add_notice( 'error', __( '<strong>First Name</strong> is a required field.', 'fundpress' ) );
					}

					if ( ! isset( $this->posted['last_name'] ) || trim( DN_Helpper::DN_sanitize_params_submitted( $this->posted['last_name'] ) ) === '' ) {
						donate_add_notice( 'error', __( '<strong>Last Name</strong> is a required field.', 'fundpress' ) );
					}

					if ( ! isset( $this->posted['email'] ) || trim( DN_Helpper::DN_sanitize_params_submitted( $this->posted['email'] ) ) === '' || ! is_email( $this->posted['email'] ) ) {
						donate_add_notice( 'error', __( '<strong>Email</strong> is invalid.', 'fundpress' ) );
					}

					if ( ! isset( $this->posted['phone'] ) || trim( DN_Helpper::DN_sanitize_params_submitted( $this->posted['phone'] ) ) === '' ) {
						donate_add_notice( 'error', __( '<strong>Phone Number</strong> is a required field.', 'fundpress' ) );
					}
					// terms and conditions
					$term_enable         = FP()->settings->checkout->get( 'term_condition', 'yes' );
					$term_condition_page = FP()->settings->checkout->get( 'term_condition_page' );
					if ( $term_enable === 'yes' && $term_condition_page && get_post( $term_condition_page ) ) {
						if ( ! isset( $this->posted['term_condition'] ) || ! $this->posted['term_condition'] ) {
							donate_add_notice( 'error', __( '<strong>Terms and Contidions</strong> is a required field.', 'fundpress' ) );
						}
					}

					// address is field is require
					if ( ! isset( $this->posted['address'] ) || ! $this->posted['address'] ) {
						donate_add_notice( 'error', __( '<strong>Address</strong> is a required field.', 'fundpress' ) );
					}

					// payments method
					$payments = fundpress_payments_enable();
					// payment method
					$payment_method = isset( $this->posted['payment_method'] ) ? sanitize_text_field( $this->posted['payment_method'] ) : false;

					// payment method is invalid
					if ( ! $payment_method || ! array_key_exists( DN_Helpper::DN_sanitize_params_submitted( $this->posted['payment_method'] ), $payments ) ) {
						// return error with message if payment method is not enable or not exists in system.
						throw new Exception( __( '<strong>Payment method</strong> is invalid. Please try again.', 'fundpress' ) );
					}
					/* END VALIDATE POST FIELDS */

					// failed if errors is not empty
					if ( ! donate_has_notice( 'error' ) ) {
						$params = array(
							'first_name' => isset( $this->posted['first_name'] ) ? sanitize_text_field( $this->posted['first_name'] ) : __( 'No First Name', 'fundpress' ),
							'last_name'  => isset( $this->posted['last_name'] ) ? sanitize_text_field( $this->posted['last_name'] ) : __( 'No Last Name', 'fundpress' ),
							'email'      => isset( $this->posted['email'] ) ? sanitize_text_field( $this->posted['email'] ) : false,
							'phone'      => isset( $this->posted['phone'] ) ? sanitize_text_field( $this->posted['phone'] ) : '',
							'address'    => isset( $this->posted['address'] ) ? sanitize_text_field( $this->posted['address'] ) : ''
						);

						// alow hook to submit param donor
						$params = apply_filters( 'donate_ajax_submit_donor', $params );
						// addtional note
						$addition_note = isset( $this->posted['addition'] ) ? sanitize_text_field( $this->posted['addition'] ) : '';

						/* create donor */
						$cart = FP()->cart; // cart
						// get donate_id from cart
						$donor_id = $cart->donor_id;
						if ( ! $donor_id ) {
							// create donor
							$donor_id = DN_Donor::instance()->create_donor( $params );
						} else {
							$donor = DN_Donor::instance( $donor_id )->update_donor( $params );
						}

						// is return wp error
						if ( is_wp_error( $donor_id ) ) {
							throw new Exception( $donor_id->get_error_message() );
						}
						/* end create donor */

						// set cart information
						$param = array(
							'addtion_note' => $addition_note,
							'donor_id'     => $donor_id
						);
						// hook cart information
						$param = apply_filters( 'donate_cart_information_data', $param );
						// set cart info
						$cart->set_cart_information( $param );

						$donate_id = $cart->donate_id;
						if ( $donate_id && get_post( $donate_id ) ) {
							$donate    = DN_Donate::instance( $donate_id );
							$donate_id = $donate->update_information( $donor_id, $payment_method );
							/* remove all old donate items */
							$donate->remove_donate_items();
						} else {
							$donate_id = DN_Donate::instance()->create_donate( $donor_id, $payment_method );
						}

						/* donate */
						$donate = DN_Donate::instance( $donate_id );

						// update post meta
						if ( $donate_system && $amount ) {
							$donate->update_meta( 'total', $amount );
							$donate->update_meta( 'type', 'sytem' );
							// $donate->update_meta( 'amount_system', $amount );
						} else if ( $cart_contents = $cart->cart_contents ) {
							$donate->update_meta( 'type', 'campaign' );
							foreach ( $cart_contents as $cart_content ) {
								$donate->add_donate_item( $cart_content->campaign_id, get_the_title( $cart_content->campaign_id ), $cart_content->total );
							}
						}

						// is wp error when create donate
						if ( is_wp_error( $donate_id ) ) {
							throw new Exception( $donate_id->get_error_message() );
						}

						// set cart information
						$param = array(
							'addtion_note' => $addition_note,
							'donate_id'    => $donate_id,
							'donor_id'     => $donor_id
						);
						// hook cart information
						$param = apply_filters( 'donate_cart_information_data', $param );
						// set cart info
						$cart->set_cart_information( $param );

						// payment method selected
						$payment = $payments[ $payment_method ];

						$results = $payment->process( $donate, $this->posted );
						if ( isset( $results['status'] ) && $results['status'] === 'success' ) {
							if ( fundpress_is_ajax_request() ) {
								wp_send_json( $results );
							} else if ( isset( $results['url'] ) ) {
								wp_redirect( $results['url'] );
								exit();
							}
						} else if ( isset( $results['message'] ) ) {
							throw new Exception( $results['message'] );
						}
					}
				} else {
					wp_send_json( array( 'status' => 'success', 'url' => donate_redirect_url() ) );
				}
			}
			catch ( Exception $e ) {
				donate_add_notice( 'error', $e->getMessage() );
			}

			if ( fundpress_is_ajax_request() ) {
				ob_start();
				fundpress_print_notices();
				$message = ob_get_clean();
				$results = array( 'status' => 'failed', 'message' => $message );
				wp_send_json( $results );
			}
		}

		/**
		 * Instance.
		 *
		 * @return DN_Checkout|null
		 */
		public static function instance() {
			if ( ! self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

	}
}
