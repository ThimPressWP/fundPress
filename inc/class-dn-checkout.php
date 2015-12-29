<?php

class DN_Checkout
{

	function __construct()
	{

	}

	/**
	 * process checkout
	 * @return
	 */
	function process_checkout( $donor_info = null, $payment_method = 'paypal', $addition = null )
	{

		$cart = donate()->cart;
		// get donate_id from cart
		if( ! $donor_id = $cart->donor_id )
		{
			// create donor
			$donor_id = DN_Donor::instance()->create_donor( $donor_info );
		}

		// is return wp error
		if( is_wp_error( $donor_id ) )
		{
			return array( 'status' => 'failed', 'message' => $donor_id->get_error_message() );
		}

		// get donate_id from cart
		if( ! $donate_id = $cart->donate_id )
		{
			$donate_id = DN_Donate::instance()->create_donate( $donor_id, $payment_method );
		}

		// is wp error when create donate
		if( is_wp_error( $donate_id ) )
		{
			return array( 'status' => 'failed', 'message' => $donate_id->get_error_message() );
		}

		// payments method is enable
		$payments = donate_payments_enable();

		if( ! array_key_exists( $payment_method , $payments ) )
			return array( 'status' => 'failed', 'message' => __( 'Invalid payment method. Please try again', 'tp-donate' ) );

		// set cart information
		$param = array(
						'addtion_note'	=> $addition,
						'donate_id'		=> $donate_id,
						'donor_id'		=> $donor_id
					);
		$param = apply_filters( 'donate_cart_information_data', $param );
		// set cart info
		$cart->set_cart_infomation( $param );

		// payment method selected
		$payment = $payments[ $payment_method  ];

		return $payment->process();

	}

}
