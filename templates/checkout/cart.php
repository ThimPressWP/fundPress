<?php
/**
 * template cart of checkout page
 */
?>
<?php do_action( 'donate_after_checkout_cart' ); ?>

<div class="donate_cart">
	<table class="donate_cart_form">

		<!-- Cart head -->
		<?php donate_get_template( 'checkout/cart_title.php' ); ?>

		<!-- Cart head -->
		<?php donate_get_template( 'checkout/cart_items.php' ); ?>

		<!-- Cart total -->
		<?php donate_get_template( 'checkout/cart_total.php' ); ?>

	</table>
</div>

<?php do_action( 'donate_after_checkout_cart' ); ?>

<?php do_action( 'donate_after_cart_contents' ); ?>
