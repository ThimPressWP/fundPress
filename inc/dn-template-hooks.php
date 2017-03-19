<?php

if ( !defined( 'ABSPATH' ) )
    exit();

/**
 * template hook function
 */
add_filter( 'the_content', 'donate_the_content' );
if ( !function_exists( 'donate_the_content' ) ) {

    function donate_the_content( $content ) {
        global $post;
        $post_id = $post->ID;
        if ( $post_id == DN_Settings::instance()->checkout->get( 'cart_page' ) ) {
            $content = '[donate_cart]';
        } else if ( $post_id == DN_Settings::instance()->checkout->get( 'checkout_page' ) ) {
            $content = '[donate_checkout]';
        } else if ( in_array( $post->post_type, array( 'dn_donate', 'dn_donor' ) ) ) {
            wp_redirect( home_url() );
            exit();
        }
        return do_shortcode( $content );
    }

}
/* * *****Archive Template****** */
/**
 * title
 */
add_action( 'donate_loop_campaign_title', 'donate_loop_campaign_title' );
if ( !function_exists( 'donate_loop_campaign_title' ) ) {

    function donate_loop_campaign_title() {
        donate_get_template( 'loop/title.php' );
    }

}

/**
 * thumbnai
 */
add_action( 'donate_loop_campaign_thumbnail', 'donate_loop_campaign_thumbnail' );
if ( !function_exists( 'donate_loop_campaign_thumbnail' ) ) {

    function donate_loop_campaign_thumbnail() {
        donate_get_template( 'loop/thumbnail.php' );
    }

}

/**
 * countdown
 */
add_action( 'donate_loop_campaign_countdown', 'donate_loop_campaign_countdown' );
if ( !function_exists( 'donate_loop_campaign_countdown' ) ) {

    function donate_loop_campaign_countdown() {
        if ( DN_Settings::instance()->donate->get( 'archive_countdown_raised', 'yes' ) === 'yes' ) {
            donate_get_template( 'loop/countdown.php' );
        }
    }

}

/**
 * goal and raised
 */
add_action( 'donate_loop_campaign_goal_raised', 'donate_loop_campaign_goal_raised' );
if ( !function_exists( 'donate_loop_campaign_goal_raised' ) ) {

    function donate_loop_campaign_goal_raised() {
        if ( DN_Settings::instance()->donate->get( 'archive_raised_goal', 'yes' ) === 'yes' ) {
            donate_get_template( 'loop/goal_raised.php' );
        }
    }

}

/**
 * posted
 */
add_action( 'donate_loop_campaign_posted', 'donate_loop_campaign_posted' );
if ( !function_exists( 'donate_loop_campaign_posted' ) ) {

    function donate_loop_campaign_posted() {
        donate_get_template( 'loop/posted.php' );
    }

}

/**
 * excerpt loop
 */
add_action( 'donate_loop_campaign_excerpt', 'donate_loop_campaign_excerpt' );
if ( !function_exists( 'donate_loop_campaign_excerpt' ) ) {

    function donate_loop_campaign_excerpt() {
        donate_get_template( 'loop/excerpt.php' );
    }

}

/**
 * content loop
 */
add_action( 'donate_loop_campaign_content', 'donate_loop_campaign_content' );
if ( !function_exists( 'donate_loop_campaign_content' ) ) {

    function donate_loop_campaign_content() {
        donate_get_template( 'loop/content.php' );
    }

}
/* * *****End Archive Template****** */

/* * *****Single Template****** */
/**
 * title
 */
add_action( 'donate_single_campaign_title', 'donate_single_campaign_title' );
if ( !function_exists( 'donate_single_campaign_title' ) ) {

    function donate_single_campaign_title() {
        donate_get_template( 'single/title.php' );
    }

}

/**
 * thumbnai
 */
add_action( 'donate_single_campaign_thumbnail', 'donate_single_campaign_thumbnail' );
if ( !function_exists( 'donate_single_campaign_thumbnail' ) ) {

    function donate_single_campaign_thumbnail() {
        donate_get_template( 'single/thumbnail.php' );
    }

}

/**
 * donate
 */
add_action( 'donate_single_campaign_donate', 'donate_single_campaign_donate' );
if ( !function_exists( 'donate_single_campaign_donate' ) ) {

    function donate_single_campaign_donate() {
        donate_get_template( 'single/donate.php' );
    }

}

/**
 * countdown
 */
add_action( 'donate_single_campaign_countdown', 'donate_single_campaign_countdown' );
if ( !function_exists( 'donate_single_campaign_countdown' ) ) {

    function donate_single_campaign_countdown() {
        if ( DN_Settings::instance()->donate->get( 'single_countdown_raised', 'yes' ) === 'yes' ) {
            donate_get_template( 'single/countdown.php' );
        }
    }

}

/**
 * goal and raised
 */
add_action( 'donate_single_campaign_goal_raised', 'donate_single_campaign_goal_raised' );
if ( !function_exists( 'donate_single_campaign_goal_raised' ) ) {

    function donate_single_campaign_goal_raised() {
        if ( DN_Settings::instance()->donate->get( 'single_countdown_raised', 'yes' ) === 'yes' ) {
            donate_get_template( 'single/goal_raised.php' );
        }
    }

}

/**
 * posted
 */
add_action( 'donate_single_campaign_posted', 'donate_single_campaign_posted' );
if ( !function_exists( 'donate_single_campaign_posted' ) ) {

    function donate_single_campaign_posted() {
        donate_get_template( 'single/posted.php' );
    }

}

/**
 * content loop
 */
add_action( 'donate_single_campaign_content', 'donate_single_campaign_content' );
if ( !function_exists( 'donate_single_campaign_content' ) ) {

    function donate_single_campaign_content() {
        donate_get_template( 'single/content.php' );
    }

}
/* * *****End Single Template****** */

add_filter( 'the_post', 'donate_get_camgain_amount' );
if ( !function_exists( 'donate_get_camgain_amount' ) ) {

    function donate_get_camgain_amount( $post ) {
        $post->total = donate_total_campaign( $post->ID );
        return $post;
    }

}

add_action( 'campaign_after_archive_loop', 'donate_campaign_pagination_archive' );
if ( !function_exists( 'donate_campaign_pagination_archive' ) ) {

    function donate_campaign_pagination_archive() {
        donate_get_template( 'pagination.php' );
    }

}

// cancel payment order
add_action( 'donate_cancel_payment_order', 'donate_cancel_payment_order' );
if ( !function_exists( 'donate_cancel_payment_order' ) ) {

    function donate_cancel_payment_order( $donate_id ) {
        $post_status = get_post_status( $donate_id );
        if ( $post_status === 'donate-pending' ) {
            wp_update_post( array(
                'ID' => $donate_id,
                'post_status' => 'donate-cancelled'
            ) );
        }
    }

}

add_action( 'init', 'donate_empty_cart_thankyou_page' );
function donate_empty_cart_thankyou_page(){
    if ( donate_is_thankyou_page() ) {
        DN_Cart::instance()->remove_cart();
    }
}

add_action( 'wp_footer', 'donate_footer_insert_blank_lightbox_div' );
function donate_footer_insert_blank_lightbox_div() {
    ?>
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