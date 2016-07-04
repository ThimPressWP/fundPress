<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class DN_Checkout {

	/* instance */
	public static $instance = null;

	/**
	 * process checkout
	 * @return
	 */
	public function process_checkout( $donor_info = null, $payment_method = 'paypal', $addition = null, $amount = false )
	{
		try {

			if( ! isset( $_POST[ 'thimpress_donate_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'thimpress_donate_nonce' ], 'thimpress_donate_nonce' ) ) {
				throw new Exception( __( 'We were unable to process your order, please try again.', 'tp-donate' ) );
			}

			/************** NEW SCRIPT **************/
			$amount = 0;
			if( isset( $_POST[ 'donate_input_amount' ] ) ) {
				$amount = sanitize_text_field( $_POST[ 'donate_input_amount' ] );
			}

			// donate within campaign
			if( isset( $_POST[ 'campaign_id' ] ) && is_numeric( $_POST[ 'campaign_id' ] ) ) {
				// get campaign
				$campaign = get_post( $_POST[ 'campaign_id' ] );

				if( ! $campaign || $campaign->post_type !== 'dn_campaign' ) {
					donate_add_notice( 'error', __( 'Campaign is invalid.', 'tp-donate' ) );
				}

				if( ! $amount && isset( $_POST[ 'donate_input_amount_package' ] ) ) {
					$compensate_id = sanitize_text_field( $_POST[ 'donate_input_amount_package' ] );
					// Campaign
					$campaign = DN_Campaign::instance( $campaign );
					$compensates = $campaign->get_compensate();

					if( isset( $compensates[ $compensate_id ], $compensates[ $compensate_id ]['amount'] ) ) {
						$amount = donate_campaign_convert_amount( $compensates[ $compensate_id ]['amount'], $campaign->get_currency() );
					}

				}

				/**
				 * donate 0 amount
				 * @var
				 */
				if( $amount == 0 ) {
					donate_add_notice( 'error', sprintf( '%s %s', __( 'Can not donate amount zero point.', 'tp-donate' ), donate_price( 0 ) ) );
				}
				// add to cart param
				$cart_params = apply_filters( 'donate_add_to_cart_item_params', array(

						'product_id'		=> $campaign->ID,
						'currency'			=> donate_get_currency()

					) );

				// failed if errors is not empty
				if( ! empty( $errors ) ) {
					wp_send_json( array( 'status' => 'failed', 'message' => $errors ) );
				} else {
					// add to cart
					$cart_item_id = donate()->cart->add_to_cart( $campaign->ID, $cart_params, 1, $amount );

					if( ! $cart_item_id || is_wp_error( $cart_item_id ) ) {
						// failed
						wp_send_json( array( 'status' => 'failed', 'message' => __( 'Something went wrong, could not add to cart item. Please try again.', 'tp-donate' ) ) );
					}
				}
			}
			// end update cart

			// process checkout
			if( isset( $_POST[ 'payment_process' ] ) && $_POST[ 'payment_process' ] ) {
				$donate_system = false;
				if( isset( $_POST[ 'donate_system' ] ) && $_POST[ 'donate_system' ] == 1 ) {
					$donate_system = true;
				}

				/**
				 * donate 0 amount
				 * @var
				 */
				if( ( $donate_system === false && DN_Cart::instance()->cart_total == 0 ) || ( $donate_system === true  && $amount <= 0 ) ) {
					donate_add_notice( 'error', sprintf( '%s %s', __( 'Can not donate amount zero point.', 'tp-donate' ), donate_price( 0 ) ) );
				}

				// terms and conditions
				$term_enable = DN_Settings::instance()->checkout->get( 'term_condition', 'yes' );
				$term_condition_page = DN_Settings::instance()->checkout->get( 'term_condition_page' );
				if( $term_enable === 'yes' && $term_condition_page && get_post( $term_condition_page ) ) {
					if( ! isset( $_POST[ 'term_condition' ] ) || ! $_POST[ 'term_condition' ] ) {
						donate_add_notice( 'error', __( 'Terms and Contidions is require field.', 'tp-donate' ) );
					}
				}

				// address is field is require
				if( ! isset( $_POST[ 'address' ] ) || ! $_POST[ 'address' ] ) {
					donate_add_notice( 'error', __( 'Please fill in the Address require field.', 'tp-donate' ) );
				}

				// payments method
				$payments = donate_payments_enable();

				// payment method is invalid
				if( ! isset( $_POST['payment_method'] ) || ! $_POST['payment_method'] || ! array_key_exists( $_POST['payment_method'], $payments ) ) {
					donate_add_notice( 'error', __( 'Invalid payment method. Please try again.', 'tp-donate' ) );
				}

				// failed if errors is not empty
				if( ! empty( $errors ) ) {
					$results = array( 'status' => 'failed', 'message' => $errors );
				} else {
					// payment method
					$payment_method = sanitize_text_field( $_POST['payment_method'] );

					$params = array(
							'first_name'		=> isset( $_POST['first_name'] ) 	? sanitize_text_field( $_POST['first_name'] ) 	: __( 'No First Name', 'tp-donate' ),
							'last_name'			=> isset( $_POST['last_name'] ) 	? sanitize_text_field( $_POST['last_name'] ) 	: __( 'No Last Name', 'tp-donate' ),
							'email'				=> isset( $_POST['email'] ) 		? sanitize_text_field( $_POST['email'] ) 		: false,
							'phone'				=> isset( $_POST['phone'] ) 		? sanitize_text_field( $_POST['phone'] ) 		: '',
							'address'			=> isset( $_POST['address'] ) 		? sanitize_text_field( $_POST['address'] ) 		: ''
						);

					// alow hook to submit param donor
					$params = apply_filters( 'donate_ajax_submit_donor', $params );
					// addtional note
					$addition_note	= isset( $_POST['addition'] ) ? sanitize_text_field( $_POST['addition'] ) : '';

					$checkout = new DN_Checkout();

					// send json
					if( $donate_system === false ) {
						// donate for campaign
						$results = $checkout->process_checkout( $params, $payment_method, $addition_note );
					} else {
						// donate for system
						$results = $checkout->process_checkout( $params, $payment_method, $addition_note, $amount );
					}
				}
				// send results
				wp_send_json( $results );

			}
			// end process checkout

			// failed
			wp_send_json( array( 'status' => 'success', 'url' => donate_redirect_url() ) );
			/************** END NEW SCRIPT **************/

			/*ENDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD*/
			// cart
			$cart = donate()->cart;
			// get donate_id from cart
			$donor_id = $cart->donor_id;
			if( ! $donor_id ) {
				// create donor
				$donor_id = DN_Donor::instance()->create_donor( $donor_info );
			} else {
				$donor = DN_Donor::instance( $donor_id )->update_donor( $donor_info );
			}

			// is return wp error
			if( is_wp_error( $donor_id ) ) {
				throw new Exception( $donor_id->get_error_message() );
			}

			// set cart information
			$param = array(
							'addtion_note'	=> $addition,
							'donor_id'		=> $donor_id
						);
			// hook cart information
			$param = apply_filters( 'donate_cart_information_data', $param );

			// set cart info
			$cart->set_cart_information( $param );

			$donate_id = $cart->donate_id;
			if ( $donate_id ) {
				$donate = DN_Donate::instance( $donate_id );
				$donate_id = $donate->update_information( $donor_id, $payment_method, $amount );
				/* remove all old donate items */
				$donate->remove_donate_items();
			} else {
				$donate_id = DN_Donate::instance()->create_donate( $donor_id, $payment_method, $amount );
			}

			/* donate */
			$donate = DN_Donate::instance( $donate_id );
			$donate->update_meta( 'total', $amount );
			// update post meta
			if( $amount ) {
				$donate->update_meta( 'amount_system', $amount );
			} else if ( $cart_contents = $cart->cart_contents ){
				foreach ( $cart_contents as $cart_content ) {
					$donate->add_donate_item( $cart_content->product_id, get_the_title( $cart_content->product_id ), $cart_content->amount_exclude_tax );
				}
			}

			// is wp error when create donate
			if( is_wp_error( $donate_id ) ) {
				throw new Exception( $donate_id->get_error_message() );
			}

			// payments method is enable
			$payments = donate_payments_enable();

			if( ! array_key_exists( $payment_method, $payments ) ) {
				// return error with message if payment method is not enable or not exists in system.
				throw new Exception( __( 'Invalid payment method. Please try again.', 'tp-donate' ) );
			}

			// set cart information
			$param = array(
							'addtion_note'	=> $addition,
							'donate_id'		=> $donate_id,
							'donor_id'		=> $donor_id
						);
			// hook cart information
			$param = apply_filters( 'donate_cart_information_data', $param );

			// set cart info
			$cart->set_cart_information( $param );

			// payment method selected
			$payment = $payments[ $payment_method ];

			return $payment->process( $amount );
		} catch( Exception $e ) {
			return array( 'status' => 'failed', 'message' => $e->getMessage() );
		}

	}

	/* instance */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
