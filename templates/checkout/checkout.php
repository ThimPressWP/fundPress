<?php
/**
 * Template for displaying checkout page.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/checkout/checkout.php
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

<?php fundpress_print_notices(); ?>

<div class="donate_checkout">

    <form class="donate_form">

		<?php do_action( 'donate_before_form_checkout' ); ?>

        <!-- cart -->
		<?php donate_get_template( 'checkout/cart.php' ) ?>

        <!-- personal info -->
		<?php donate_get_template( 'checkout/personal.php' ) ?>

        <!-- payment term condition -->
		<?php donate_get_template( 'checkout/term_condition.php' ) ?>

        <!-- payment method -->
		<?php donate_get_template( 'checkout/payment_methods.php' ) ?>

        <!-- hidden -->
        <input type="hidden" name="payment_process" value="1"/>

        <!-- button -->
        <input type="hidden" name="action" value="donate_submit"/>

        <!-- nonce -->
		<?php wp_nonce_field( 'thimpress_donate_nonce', 'thimpress_donate_nonce' ); ?>

		<?php do_action( 'donate_after_form_checkout' ); ?>

        <div class="donate_payment_button_process">
            <button type="submit" class="donate_button"><?php _e( 'Donate', 'fundpress' ); ?></button>
        </div>

    </form>

</div>
