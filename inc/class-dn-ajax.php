<?php

class DN_Ajax
{

	function __construct()
	{

		if( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
			return;

		$actions = array(
				'donate_load_form'	=> true,
				'donate_submit'		=> true
			);

		foreach ( $actions as $action => $nopriv ) {

			if( ! method_exists( $this, $action ) )
				return;

			add_action( 'wp_ajax_' . $action, array( $this, $action ) );
			if( $nopriv )
			{
				add_action( 'wp_ajax_noprive_' . $action, array( $this, $action ) );
			}

		}

	}

	/**
	 * ajax load form
	 * @return
	 */
	function donate_load_form()
	{

		if( ! isset( $_GET[ 'schema' ] ) || $_GET[ 'schema' ] !== 'donate-ajax' || empty( $_POST ) )
			return;

		if( ! isset( $_POST[ 'nonce' ] ) || ! wp_verify_nonce( $_POST[ 'nonce' ], 'thimpress_donate_nonce' ) )
			return;

		if( ! isset( $_POST[ 'campaign_id' ] ) || ! is_numeric( $_POST[ 'campaign_id' ] ) )
			return;

		$campaign = get_post( $_POST[ 'campaign_id' ] );

		if( ! $campaign || $campaign->post_type !== 'dn_campaign' )
			return;

		$campaign_id = $campaign->ID;
		$campaign = DN_Campaign::instance( $campaign );

		$compensates = array();
		$currency = $campaign->get_currency();

		if( $eachs = $campaign->get_compensate() )
		{
			foreach ( $eachs as $key => $compensate ) {
				/**
				 * convert campaign amount currency to amount with currency setting
				 * @var
				 */
				$amount = donate_campaign_convert_amount( $compensate['amount'], $currency );
				$compensates[ $key ] = array(
						'amount'		=> donate_price( $amount ),
						'desc'			=> $compensate['desc']
					);
			}
		}

		$payments = array();

		// load payments when checkout on lightbox setting isset yes
		if( DN_Settings::instance()->checkout->get( 'lightbox_checkout', 'no' ) === 'yes' )
		{
			$payment_enable = donate_payments_enable();
			foreach( $payment_enable as $key => $payment )
			{
				$payments[] = array(
						'id'		=> $payment->_id,
						'title'		=> $payment->_title,
						'icon'		=> $payment->_icon
					);
			}
		}

		wp_send_json( array(

				'status'		=> 'success',
				'campaign_id'	=> $campaign->ID,
				'compensates'	=> $compensates,
				'currency'		=> donate_get_currency_symbol(),
				'payments'		=> $payments // list payment allow

			));
	}

	/**
	 * donoate submit lightbox
	 * @return
	 */
	function donate_submit()
	{
		// validate sanitize input $_POST
		if( ! isset( $_GET[ 'schema' ] ) || $_GET[ 'schema' ] !== 'donate-ajax' || empty( $_POST ) )
			wp_send_json( array( 'status' => 'failed', 'message' => array( __( 'Could not do action.', 'tp-donate' ) ) ) );

		if( ! isset( $_POST[ 'thimpress_donate_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'thimpress_donate_nonce' ], 'thimpress_donate_nonce' ) )
			wp_send_json( array( 'status' => 'failed', 'message' => array( __( 'Could not do action.', 'tp-donate' ) ) ) );

		/************** NEW SCRIPT **************/
		// update cart
		if( isset( $_POST[ 'campaign_id' ] ) && is_numeric( $_POST[ 'campaign_id' ] ) )
		{
			// get campaign
			$campaign = get_post( $_POST[ 'campaign_id' ] );

			if( ! $campaign || $campaign->post_type !== 'dn_campaign' )
				return;

			$amount = 0;
			if( isset( $_POST[ 'donate_input_amount' ] ) )
				$amount = sanitize_text_field( $_POST[ 'donate_input_amount' ] );

			if( ! $amount && isset( $_POST[ 'donate_input_amount_package' ] ) )
			{
				$compensate_id = sanitize_text_field( $_POST[ 'donate_input_amount_package' ] );
				// Campaign
				$campaign = DN_Campaign::instance( $campaign );
				$compensates = $campaign->get_compensate();

				if( isset( $compensates[ $compensate_id ], $compensates[ $compensate_id ]['amount'] ) )
				{
					$amount = $compensates[ $compensate_id ]['amount'];
				}

			}

			/**
			 * donate 0 currency
			 * @var
			 */
			if( $amount == 0 )
			{
				wp_send_json( array( 'status' => 'failed', 'message' => sprintf( '%s %s', __( 'Can not donate amount zero point', 'tp-donate' ), donate_price( 0 ) ) ) ); die();
			}
			// add to cart param
			$cart_params = apply_filters( 'donate_add_to_cart_item_params', array(

					'product_id'		=> $campaign->ID,
					'currency'			=> donate_get_currency()

				) );

			$cart_item_id = donate()->cart->add_to_cart( $campaign->ID, $cart_params, 1, $amount );

			if( ! $cart_item_id || is_wp_error( $cart_item_id ) )
			{
				wp_send_json( array( 'status' => 'failed', 'message' => __( 'Something went wrong, could not add to cart item. Please try again', 'tp-donate' ) ) ); die();
			}
		}

		// process checkout
		if( isset( $_POST[ 'payment_process' ] ) && $_POST[ 'payment_process' ] )
		{
			// terms and conditions
			$term_enable = DN_Settings::instance()->checkout->get( 'term_condition', 'yes' );
			if( $term_enable === 'yes' )
			{
				if( ! isset( $_POST[ 'term_condition' ] ) || ! $_POST[ 'term_condition' ] )
					wp_send_json( array( 'status' => 'failed', 'message' => array( __( 'Terms and Contidions invalid.', 'tp-donate' ) ) ) );
			}

			// payments method
			$payments = donate_payments_enable();

			// payment method is invalid
			if( ! isset( $_POST['payment_method'] ) || ! $_POST['payment_method'] || ! array_key_exists( $_POST['payment_method'], $payments ) )
				wp_send_json( array( 'status' => 'failed', 'message' => __( 'Invalid payment method. Please try again.', 'tp-donate' ) ) );

			// payment method
			$payment_method = sanitize_text_field( $_POST['payment_method'] );

			$params = array(
					'first_name'		=> isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : __( 'No First Name', 'tp-donate' ),
					'last_name'			=> isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : __( 'No Last Name', 'tp-donate' ),
					'email'				=> isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : false,
					'phone'				=> isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '',
					'address'			=> isset( $_POST['address'] ) ? sanitize_text_field( $_POST['address'] ) : ''
				);
			// addtion note
			$addition_note	= isset( $_POST['addition_note'] ) ? sanitize_text_field( $_POST['addition_note'] ) : '';

			// alow hook to submit param donor
			$params = apply_filters( 'donate_ajax_submit_donor', $params );

			$checkout = new DN_Checkout();
			// send json
			wp_send_json( $checkout->process_checkout( $params, $payment_method, $addition_note ) ); die();
		}

		// failed
		wp_send_json( array( 'status' => 'success', 'url' => donate_redirect_url() ) ); die();
		/************** END NEW SCRIPT **************/

	}

}

new DN_Ajax();
