<?php
if( ! defined( 'ABSPATH' ) ) exit();
/**
 * Template Shortcode [donate_cart]
 */

// display message
echo donate_notice_display();

if( donate()->cart->is_empty() )
{
	// empty cart
	donate_get_template( 'cart/empty.php' );
}
else
{
	// cart is not empty
	donate_get_template( 'checkout/checkout.php' );
}
