<?php

if ( !defined( 'ABSPATH' ) || !defined( 'TP_DONATE_INSTALLING' ) ) {
    exit();
}

global $wpdb;
$sql = $wpdb->prepare( "
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
                'post_type' => 'dn_donate_item',
                'post_parent' => $donate_id,
                'post_status' => 'publish'
            ) );
            update_post_meta( $item_id, 'campaign_id', absint( $content->product_id ) );
            update_post_meta( $item_id, 'title', $content->product_data->post_title );
            update_post_meta( $item_id, 'total', $content->amount );
            if ( $donate->post_type === 'completed' ) {
                $count = absint( get_post_meta( $content->product_id, TP_DONATE_META_DONATE . 'donor_count', true ) );
                update_post_meta( $content->product_id, TP_DONATE_META_DONATE . 'donor_count', $count++ );
            }
        }
    } else {
        $donate->update_meta( 'type', 'system' );
    }
}
