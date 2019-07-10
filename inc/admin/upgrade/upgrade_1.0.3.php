<?php
/**
 * Admin process: Update version 1.0.3
 *
 * @version     2.0
 * @package     View
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

// plugin install
defined( 'TP_DONATE_INSTALLING' ) || exit();

global $wpdb;
$sql     = $wpdb->prepare( "
		SELECT post.ID FROM $wpdb->posts AS post WHERE post.post_type = %s
	", 'dn_donate' );
$donates = $wpdb->get_col( $sql );

foreach ( $donates as $donate_id ) {
	$donate = DN_Donate::instance( $donate_id );
	if ( $donate->cart_contents ) {
		$donate->update_meta( 'type', 'campaign' );
		$donate->remove_donate_items();
		foreach ( $donate->cart_contents as $content ) {
			$item_id = wp_insert_post( array(
				'post_type'   => 'dn_donate_item',
				'post_parent' => $donate_id,
				'post_status' => 'publish'
			) );
			update_post_meta( $item_id, 'campaign_id', absint( $content->product_id ) );
			update_post_meta( $item_id, 'title', $content->product_data->post_title );
			update_post_meta( $item_id, 'total', $content->amount );
			$donate_item = DN_Donate_Item::instance( $item_id );
			if ( ! $donate_item->update_campaign_raised && $donate->has_status( 'completed' ) ) {
				$campaign = DN_Campaign::instance( $donate_item->campaign_id );
				$total    = $campaign->get_total_raised();
				update_post_meta( $donate_item->campaign_id, TP_DONATE_META_CAMPAIGN . 'total_raised', $total + $content->amount );
				update_post_meta( $item_id, 'update_campaign_raised', 1 );
			}
		}
	} else {
		$donate->update_meta( 'type', 'system' );
	}
}
