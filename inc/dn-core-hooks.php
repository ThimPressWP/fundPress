<?php
/**
 * Fundpress core hooks functions.
 *
 * @version     2.0
 * @package     Function
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

add_filter( 'the_content', 'fundpress_setup_page' );

if ( ! function_exists( 'fundpress_setup_page' ) ) {
	/**
	 * Setup shortcode for default page.
	 *
	 * @param $content
	 *
	 * @return string
	 */
	function fundpress_setup_page( $content ) {
		global $post;
		$post_id = $post->ID;
		if ( $post_id == FP()->settings->checkout->get( 'cart_page' ) ) {
			$content = '[donate_cart]';
		} else if ( $post_id == FP()->settings->checkout->get( 'checkout_page' ) ) {
			$content = '[donate_checkout]';
		} else if ( in_array( $post->post_type, array( 'dn_donate', 'dn_donor' ) ) ) {
			wp_redirect( home_url() );
			exit();
		}

		return do_shortcode( $content );
	}
}

add_action( 'donate_cancel_payment_order', 'fundpress_cancel_payment_order' );

if ( ! function_exists( 'fundpress_cancel_payment_order' ) ) {
	/**
	 * Update order status when cancel order.
	 *
	 * @param $donate_id
	 */
	function fundpress_cancel_payment_order( $donate_id ) {
		$post_status = get_post_status( $donate_id );
		if ( $post_status === 'donate-pending' ) {
			wp_update_post( array( 'ID' => $donate_id, 'post_status' => 'donate-cancelled' ) );
		}
	}
}

add_action( 'donate_update_status_completed', 'fundpress_update_payment_completed' );

if ( ! function_exists( 'fundpress_update_payment_completed' ) ) {
	/**
	 * Update payment completed date.
	 *
	 * @param $donate_id
	 */
	function fundpress_update_payment_completed( $donate_id ) {
		if ( get_post_meta( $donate_id, TP_DONATE_META_DONATE . 'payment_completed_at', true ) ) {
			return;
		}
		update_post_meta( $donate_id, TP_DONATE_META_DONATE . 'payment_completed_at', date( 'Y-m-d H:i:s' ) );
	}
}

add_action( 'init', 'fundpress_empty_cart_thankyou_page' );

if ( ! function_exists( 'fundpress_empty_cart_thankyou_page' ) ) {
	/**
	 * Remove cart in when thank you page.
	 */
	function fundpress_empty_cart_thankyou_page() {
		if ( donate_is_thankyou_page() ) {
			FP()->cart->remove_cart();
		}
	}
}

add_action( 'wp_footer', 'fundpress_blank_lightbox' );

if ( ! function_exists( 'fundpress_blank_lightbox' ) ) {
	/**
	 * Insert blank light box in footer.
	 */
	function fundpress_blank_lightbox() { ?>
        <div id="donate_hidden" class="mfp-hide"></div>
        <div class="donate_ajax_overflow">
            <div class="donate_ajax_loading">
                <span class="donate-1"></span>
                <span class="donate-2"></span>
                <span class="donate-3"></span>
                <span class="donate-4"></span>
                <span class="donate-5"></span>
                <span class="donate-6"></span>
            </div>
        </div>
		<?php
	}
}

add_action( 'donate_update_status', 'fundpress_update_campaign_raised', 10, 3 );

if ( ! function_exists( 'fundpress_update_campaign_raised' ) ) {
	/**
	 * Update campaign total raised.
	 *
	 * @param $donate_id
	 * @param $old_status
	 * @param $status
	 */
	function fundpress_update_campaign_raised( $donate_id, $old_status, $status ) {
		$donate = DN_Donate::instance( $donate_id );
		$items  = $donate->get_items();

		if ( $donate->type === 'system' || empty( $items ) ) {
			return;
		}

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

add_action( 'donate_create_booking_donate', 'fundpress_change_order_donate', 10, 1 );
add_action( 'donate_update_status', 'fundpress_change_order_donate', 10, 1 );

if ( ! function_exists( 'fundpress_change_order_donate' ) ) {
	/**
	 * Schedule cancel order when payment expired.
	 *
	 * @param $donate_id
	 */
	function fundpress_change_order_donate( $donate_id ) {
		$post_status = get_post_status( $donate_id );

		if ( $post_status === 'donate-pending' ) {
			wp_clear_scheduled_hook( 'donate_cancel_payment_order', array( $donate_id ) );
			$time = FP()->settings->checkout->get( 'cancel_payment', 12 ) * HOUR_IN_SECONDS;
			wp_schedule_single_event( time() + $time, 'donate_cancel_payment_order', array( $donate_id ) );
		}
	}
}

add_action( 'widgets_init', 'fundpress_register_widgets' );

if ( ! function_exists( 'fundpress_register_widgets' ) ) {
	/**
	 * Register widgets.
	 */
	function fundpress_register_widgets() {
		register_widget( 'DN_Widget_Button' );
		register_widget( 'DN_Widget_Donate_system' );
	}

}
