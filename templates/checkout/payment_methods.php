<?php
/**
 * Payment method template
 */

// all payment method is enable
$payments = donate_payments_enable();
?>

<?php if( $payments ) : ?>

	<div class="donate_payments">
		<?php foreach( $payments as $key => $payment ) : ?>

			<label class="payment_method" for="<?php echo esc_attr( $payment->_id ) ?>"><img width="115" height="50" src="<?php echo esc_attr( $payment->_icon ) ?>" /></label>
			<input id="<?php echo esc_attr( $payment->_id ) ?>" type="radio" name="payment_method" value="<?php echo esc_attr( $payment->_id ) ?>"/>

		<?php endforeach; ?>
	</div>

<?php endif; ?>
