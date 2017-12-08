<?php
/**
 * Template for displaying cart in checkout page.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/checkout/cart.php
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

<?php do_action( 'donate_after_checkout_cart' ); ?>

<div class="donate_cart">
	<table class="donate_cart_form">
		<!-- Cart head -->
		<?php donate_get_template( 'checkout/cart_title.php' ); ?>
		<!-- Cart items -->
		<?php donate_get_template( 'checkout/cart_items.php' ); ?>
		<!-- Cart total -->
		<?php donate_get_template( 'checkout/cart_total.php' ); ?>
	</table>
</div>

<input type="hidden" name="amount" value="<?php echo esc_attr( FP()->cart->cart_total ) ?>">

<?php do_action( 'donate_after_checkout_cart' ); ?>

<?php do_action( 'donate_after_cart_contents' ); ?>
