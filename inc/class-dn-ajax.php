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

		$compensate = $campaign->get_compensate();

		wp_send_json( array(

				'status'		=> 'success',
				'compensates'	=> $compensate,
				'currency'		=> donate_get_currency_symbol( $campaign->get_currency() )

			));
	}

}

new DN_Ajax();
