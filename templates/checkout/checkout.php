<?php
/**
 * template checkout
 */
?>

<div class="donate_checkout">

	<form class="donate_form">

		<!-- personal info -->
		<?php donate_get_template( 'checkout/cart.php' ) ?>

		<!-- personal info -->
		<?php donate_get_template( 'checkout/personal.php' ) ?>

		<!--payment method -->
		<?php donate_get_template( 'checkout/term_condition.php' ) ?>

		<!--payment method -->
		<?php donate_get_template( 'checkout/payment_methods.php' ) ?>

		<!--Require-->
		<input type="hidden" name="payment_process" value="1" />

		<!--Require-->
		<input type="hidden" name="action" value="donate_submit" />

		<!--Require-->
		<?php wp_nonce_field( 'thimpress_donate_nonce', 'thimpress_donate_nonce' ); ?>

		<div class="donate_payment_button_process">
			<button type="submit" class="donate_button"><?php _e( 'Donate', 'tp-donate' ); ?></button>
		</div>

	</form>

</div>
