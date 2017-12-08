<?php
/**
 * Template for displaying footer of cart page.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/cart/cart_footer.php
 *
 * @version     2.0
 * @package     Template
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
?>

<div class="donate_cart_footer_button">
	<a class="donate_button checkout_url" href="<?php echo esc_attr( donate_checkout_url() ) ?>"><?php _e( 'Donate Payment', 'fundpress' ) ?></a>
</div>