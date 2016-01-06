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

		/**
		 * load form with campaign ID
		 */
		$payments = array();
		$payment_enable = donate_payments_enable();
		if( isset( $_POST[ 'campaign_id' ] ) && is_numeric( $_POST[ 'campaign_id' ] ) )
		{
			$campaign = get_post( $_POST[ 'campaign_id' ] );

			if( ! $campaign || $campaign->post_type !== 'dn_campaign' )
			{
				wp_send_json( array( 'status' => 'failed', 'message' => __( 'Campaign is not exists in our system.', 'tp-donate' ) ) );
			}

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

			// load payments when checkout on lightbox setting isset yes
			if( DN_Settings::instance()->checkout->get( 'lightbox_checkout', 'no' ) === 'yes' )
			{
				foreach( $payment_enable as $key => $payment )
				{
					$payments[] = array(
							'id'		=> $payment->_id,
							'title'		=> $payment->_title,
							'icon'		=> $payment->_icon
						);
				}
			}

			$results = array(
				'status'				=> 'success',
				'campaign_id'			=> $campaign->ID,
				'campaign_title'		=> get_the_title( $campaign->ID ),
				'compensates'			=> $compensates,
				'currency'				=> donate_get_currency(),
				'currency_symbol'		=> donate_get_currency_symbol(),
				'payments'				=> $payments // list payment allow
			);
		}
		else // load form donate now button
		{
			foreach( $payment_enable as $key => $payment )
			{
				$payments[] = array(
						'id'		=> $payment->_id,
						'title'		=> $payment->_title,
						'icon'		=> $payment->_icon
					);
			}

			$results = array(
				'status'				=> 'success',
				'campaign_title'		=> apply_filters( 'donate_form_title_without_campaign', sprintf( '%s - %s', get_bloginfo( 'name' ), get_bloginfo( 'description' ) ) ),
				'currency'				=> donate_get_currency(),
				'currency_symbol'		=> donate_get_currency_symbol(),
				'allow_payment'			=> true,
				'donate_system'			=> true,
				'payments'				=> $payments // list payment allow
			);
		}

		$results = apply_filters( 'donate_load_form_donate_results', $results );
		wp_send_json( $results );
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
		// update cart.
		// errors array
		$errors = array();
		// if isset campaign_id when click donate

		$amount = 0;
		if( isset( $_POST[ 'donate_input_amount' ] ) )
		{
			$amount = sanitize_text_field( $_POST[ 'donate_input_amount' ] );
		}

		// donate within campaign
		if( isset( $_POST[ 'campaign_id' ] ) && is_numeric( $_POST[ 'campaign_id' ] ) )
		{
			// get campaign
			$campaign = get_post( $_POST[ 'campaign_id' ] );

			if( ! $campaign || $campaign->post_type !== 'dn_campaign' )
				$errors[] = __( 'Campaign is invalid.', 'tp-donate' );

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
			 * donate 0 amount
			 * @var
			 */
			if( $amount == 0 )
			{
				$errors[] = sprintf( '%s %s', __( 'Can not donate amount zero point.', 'tp-donate' ), donate_price( 0 ) );
			}
			// add to cart param
			$cart_params = apply_filters( 'donate_add_to_cart_item_params', array(

					'product_id'		=> $campaign->ID,
					'currency'			=> donate_get_currency()

				) );

			// failed if errors is not empty
			if( ! empty( $errors ) )
			{
				wp_send_json( array( 'status' => 'failed', 'message' => $errors ) );
			}
			else
			{
				// add to cart
				$cart_item_id = donate()->cart->add_to_cart( $campaign->ID, $cart_params, 1, $amount );

				if( ! $cart_item_id || is_wp_error( $cart_item_id ) )
				{
					// failed
					wp_send_json( array( 'status' => 'failed', 'message' => __( 'Something went wrong, could not add to cart item. Please try again.', 'tp-donate' ) ) );
				}
			}
		}
		// end update cart

		// process checkout
		if( isset( $_POST[ 'payment_process' ] ) && $_POST[ 'payment_process' ] )
		{
			$donate_system = false;
			if( isset( $_POST[ 'donate_system' ] ) && $_POST[ 'donate_system' ] == 1 )
			{
				$donate_system = true;
			}

			/**
			 * donate 0 amount
			 * @var
			 */
			if( ( $donate_system === false && DN_Cart::instance()->cart_total == 0 ) || ( $donate_system === true  && $amount <= 0 ) )
			{
				$errors[] = sprintf( '%s %s', __( 'Can not donate amount zero point.', 'tp-donate' ), donate_price( 0 ) );
			}

			// terms and conditions
			$term_enable = DN_Settings::instance()->checkout->get( 'term_condition', 'yes' );
			$term_condition_page = DN_Settings::instance()->checkout->get( 'term_condition_page' );
			if( $term_enable === 'yes' && $term_condition_page && get_post( $term_condition_page ) )
			{
				if( ! isset( $_POST[ 'term_condition' ] ) || ! $_POST[ 'term_condition' ] )
					$errors[] = __( 'Terms and Contidions is require field.', 'tp-donate' );
			}

			// address is field is require
			if( ! isset( $_POST[ 'address' ] ) || ! $_POST[ 'address' ] )
				$errors[] = __( 'Please fill in the Address require field.', 'tp-donate' );

			// payments method
			$payments = donate_payments_enable();

			// payment method is invalid
			if( ! isset( $_POST['payment_method'] ) || ! $_POST['payment_method'] || ! array_key_exists( $_POST['payment_method'], $payments ) )
				$errors[] = __( 'Invalid payment method. Please try again.', 'tp-donate' );

			// failed if errors is not empty
			if( ! empty( $errors ) )
			{
				$results = array( 'status' => 'failed', 'message' => $errors );
			}
			else
			{
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
				$addition_note	= isset( $_POST['addition_note'] ) ? sanitize_text_field( $_POST['addition_note'] ) : '';

				$checkout = new DN_Checkout();

				// send json
				if( $donate_system === false )
				{
					// donate for campaign
					$results = $checkout->process_checkout( $params, $payment_method, $addition_note );
				}
				else
				{
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

	}

}

new DN_Ajax();
