<?php

if ( !defined( 'ABSPATH' ) )
    exit();

add_action( 'donate_update_status_completed', 'donate_update_payment_completed' );
if ( !function_exists( 'donate_update_payment_completed' ) ) {
    function donate_update_payment_completed( $donate_id ) {
        if ( get_post_meta( $donate_id, TP_DONATE_META_DONATE . 'payment_completed_at', true ) ) return;
        update_post_meta( $donate_id, TP_DONATE_META_DONATE . 'payment_completed_at', date( 'Y-m-d H:i:s' ) );
    }
}
