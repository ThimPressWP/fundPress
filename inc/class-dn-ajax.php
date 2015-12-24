<?php

class DN_Ajax
{

	function __construct()
	{

		if( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
			return;

		$actions = array(
				'donate_load_form'	=> true
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
				$compensates[ $key ] = array(
						'amount'		=> donate_price( $compensate['amount'], $currency ),
						'desc'			=> $compensate['desc']
					);
			}
		}

		$payments = array();

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
				'compensates'	=> $compensates,
				'currency'		=> donate_get_currency_symbol( $currency ),
				'payments'		=> $payments

			));
	}

}

new DN_Ajax();
