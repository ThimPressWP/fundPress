<?php
/**
 * Admin process: Update version 1.4
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
$sql     = $wpdb->prepare( "SELECT post.ID FROM $wpdb->posts AS post WHERE post.post_type = %s", 'dn_campaign' );
$donates = $wpdb->get_col( $sql );

$currency = donate_get_currency();

foreach ( $donates as $donate_id ) {
	update_post_meta( $donate_id, 'thimpress_campaign_currency', $currency );
}
