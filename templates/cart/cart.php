<?php
if( ! defined( 'ABSPATH' ) ) exit();
/**
 * Cart is not empty
 */
donate_print_notices();
?>
<form class="donate_cart" action="<?php echo esc_attr( donate_cart_url() ) ?>">

	<?php do_action( 'donate_before_cart_contents' ); ?>

	<table class="donate_cart_form">

		<!-- Cart head -->
		<?php donate_get_template( 'cart/cart_title.php' ); ?>

		<!-- Cart head -->
		<?php donate_get_template( 'cart/cart_items.php' ); ?>

		<!-- Cart total -->
		<?php donate_get_template( 'cart/cart_total.php' ); ?>

	</table>

	<?php donate_get_template( 'cart/cart_footer.php' ); ?>

	<?php do_action( 'donate_after_cart_contents' ); ?>

</form>
