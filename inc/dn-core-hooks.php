<?php

if ( !defined( 'ABSPATH' ) )
	exit();

add_action( 'donate_update_status_completed', 'donate_update_payment_completed' );
if ( !function_exists( 'donate_update_payment_completed' ) ) {
	/**
	 * Update Payment Completed date
	 *
	 * @param type $donate_id
	 *
	 * @return type null
	 */
	function donate_update_payment_completed( $donate_id ) {
		if ( get_post_meta( $donate_id, TP_DONATE_META_DONATE . 'payment_completed_at', true ) ) return;
		update_post_meta( $donate_id, TP_DONATE_META_DONATE . 'payment_completed_at', date( 'Y-m-d H:i:s' ) );
	}
}

add_action( 'donate_update_status', 'donate_update_campaign_raised', 10, 3 );
if ( !function_exists( 'donate_update_campaign_raised' ) ) {
	/**
	 * Update Total Raised
	 *
	 * @param type $donate_id
	 * @param type $old_status
	 * @param type $status
	 */
	function donate_update_campaign_raised( $donate_id, $old_status, $status ) {
		$donate = DN_Donate::instance( $donate_id );
		$items  = $donate->get_items();
		if ( $donate->type === 'system' || empty( $items ) ) return;

		$donate_currency = $donate->currency;
		foreach ( $items as $item ) {
			$item         = DN_Donate_Item::instance( $item->id );
			$campaign     = DN_Campaign::instance( $item->campaign_id );
			$total_raised = $campaign->get_total_raised();

			$total_item = donate_campaign_convert_amount( $item->total, $donate_currency, $campaign->currency );

			if ( $status === 'completed' && $item->update_campaign_raised ) {
				continue;
			}

			if ( $status === 'completed' ) {
				update_post_meta( $item->campaign_id, TP_DONATE_META_CAMPAIGN . 'total_raised', $total_raised + $total_item );
				update_post_meta( $item->id, 'update_campaign_raised', 1 );
			} else {
				update_post_meta( $item->campaign_id, TP_DONATE_META_CAMPAIGN . 'total_raised', $total_raised - $total_item );
				delete_post_meta( $item->id, 'update_campaign_raised' );
			}
		}
	}
}

// change order status overdue payment
add_action( 'donate_create_booking_donate', 'donate_change_order_donate', 10, 1 );
add_action( 'donate_update_status', 'donate_change_order_donate', 10, 1 );
if ( !function_exists( 'donate_change_order_donate' ) ) {

	// add schedule
	function donate_change_order_donate( $donate_id ) {
		$post_status = get_post_status( $donate_id );

		if ( $post_status === 'donate-pending' ) {

			wp_clear_scheduled_hook( 'donate_cancel_payment_order', array( $donate_id ) );
			$time = DN_Settings::instance()->checkout->get( 'cancel_payment', 12 ) * HOUR_IN_SECONDS;
			wp_schedule_single_event( time() + $time, 'donate_cancel_payment_order', array( $donate_id ) );
		}
	}

}

// register widgets
add_action( 'widgets_init', 'donate_register_widgets' );
if ( !function_exists( 'donate_register_widgets' ) ) {

	function donate_register_widgets() {
		register_widget( 'DN_Widget_Button' );
		register_widget( 'DN_Widget_Donate_system' );
	}

}
