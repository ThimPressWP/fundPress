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
	function process_checkout( $donor_info = null, $payment_method = 'paypal' )
	{

		// create donor
		$donor = DN_Donor::instance();
		$donor_id = DN_Donor::instance()->create_donor( $donor_info );

		// is return wp error
		if( is_wp_error( $donor_id ) )
		{
			return array( 'status' => 'failed', 'message' => $donor_id->get_error_message() );
		}

		$donate_id = DN_Donate::instance()->create_donate( $donor_id, $payment_method );
var_dump($donate_id); die();
		if( is_wp_error( $donate_id ) )
		{
			return array( 'status' => 'failed', 'message' => $donate_id->get_error_message() );
		}

		// payments method is enable
		$payments = donate_payments_enable();

		if( ! array_key_exists( $payment_method , $payments ) )
			return array( 'status' => 'failed', 'message' => __( 'Invalid payment method. Please try again', 'tp-donate' ) );

		$payment = $payments[ $payment_method  ];

		if( $payment->process() )
		{
			
		}

	}

}
