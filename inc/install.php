<?php
if( ! defined( 'ABSPATH' ) ) exit();

/**
 * install script
 * @var array
 */

$options = array(

        'general'       => array(
                'aggregator'                =>  'yahoo',
                'currency'                  =>  'GBP',
                'currency_position'         =>  'left',
                'currency_thousand'         =>  ',',
                'currency_separator'        =>  '.',
                'currency_num_decimal'      =>  2,
            ),

        'checkout'      => array(
                'environment'               => 'test',
                'lightbox_checkout'         => 'no',
                'donate_redirect'           => 'checkout',
                'term_condition_enable'     => 'yes',
                'paypal_enable'             => 'yes',
                'stripe_enable'             => 'yes'
            ),

        'email'         => array(
                'enable'        => 'yes'
            ),

        'donate'        => array()

    );

$settings = DN_Settings::instance();

$pages = array();

$cart_page_id = $settings->checkout->get( 'cart_page' );
if( ! $cart_page_id || ! get_post( $cart_page_id ) )
{
    $pages['donate-cart'] = array(
        'name'          => _x( 'donate-cart', 'donate-cart', 'tp-donate' ),
        'title'         => _x( 'Donate Cart', 'Donate Cart', 'tp-donate' ),
        'content'       => '[' . apply_filters( 'donate_cart_shortcode_tag', 'donate_cart' ) . ']',
        'option_name'   => 'cart_page'
    );
}

$checkout_page_id = $settings->checkout->get( 'checkout_page' );
if( ! $checkout_page_id || ! get_post( $checkout_page_id ) )
{
    $pages['checkout'] = array(
        'name'          => _x( 'donate-checkout', 'donate-checkout', 'tp-donate' ),
        'title'         => _x( 'Donate Checkout', 'Donate Checkout', 'tp-donate' ),
        'content'       => '[' . apply_filters( 'donate_checkout_shortcode_tag', 'donate_checkout' ) . ']',
        'option_name'   => 'checkout_page'
    );
}

if( $pages && function_exists( 'donate_create_page' ) )
{
    foreach ( $pages as $key => $page ) {
        $pageId = donate_create_page( esc_sql( $page['name'] ), 'donate_' . $key . '_page_id', $page['title'], $page['content'] );

        $options['checkout'][ $page['option_name'] ] = $pageId;
    }

    update_option( 'thimpress_donate', array_merge( $options, get_option( 'thimpress_donate', array() ) ) );
}