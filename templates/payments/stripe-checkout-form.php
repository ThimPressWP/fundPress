<?php
/**
 * Template for displaying Stripe checkout form.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/payments/stripe-checkout-form.php
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

<div id="donate-stripe-form">

	<p class="description"><?php _e( 'Pay securely using your credit card.', 'fundpress' ); ?></p>

	<div class="donate_field">
		<label for="cc-number" class="label-field"><?php _e( 'Card Number', 'fundpress' ) ?></label>
		<input name="stripe[cc-number]" id="cc-number" type="tel" class="required stripe-cc-number" autocomplete="cc-number" placeholder="•••• •••• •••• ••••" />
	</div>

	<div class="donate_field">
		<label for="cc-exp" class="label-field"><?php _e( 'Expires (MM / YY)', 'fundpress' ) ?></label>
		<input name="stripe[cc-exp]" id="cc-exp" type="text" class="required stripe-cc-exp" autocomplete="cc-exp" placeholder="•• / ••" />
	</div>

	<div class="donate_field">
		<label for="cc-cvc" class="label-field"><?php _e( 'Card Code (CVC)', 'fundpress' ) ?></label>
		<input name="stripe[cc-cvc]" id="cc-cvc" type="tel" class="required stripe-cc-cvc" autocomplete="off" placeholder="•••" />
	</div>

</div>
