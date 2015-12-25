<?php

class DN_Checkout
{

	function __construct()
	{

	}

	/**
	 * add to cart
	 */
	function add_to_cart()
	{

	}

	/**
	 * process checkout
	 * @return
	 */
	function process_checkout( $params = null )
	{
		// create dornor
		$donor = DN_Donor::instance();
		$donor_id = DN_Donor::instance()->create_donor( $params['dornor'] );
		if( is_wp_error( $donor_id ) )
		{
			return array( 'status' => 'failed', 'message' => $donor_id->get_error_message() );
		}

		$params['donate']['donor_id'] = $donor_id;
		$donate_id = DN_Donate::instance()->create_donate( $params['donate'] );

		if( is_wp_error( $donate_id ) )
		{
			return array( 'status' => 'failed', 'message' => $donate_id->get_error_message() );
		}

		$payments = donate_payments_enable();
		if( ! isset( $params['payment_method'] ) || ! array_key_exists( $params['payment_method'], $payments ) )
			return array( 'status' => 'failed', 'message' => __( 'Invalid payment method. Please try again', 'tp-donate' ) );

		$payment = $payments[ $params['payment_method'] ];

		if( $payment->process() )
		{
			
		}

	}

}
