<?php
if( ! defined( 'ABSPATH' ) ) exit();
/**
 * Payment method template
 */

// all payment method is enable
$payments = donate_payments_enable();
?>

<?php if( $payments ) : ?>

	<?php do_action( 'donate_before_payments_checkout' ); ?>

	<div class="donate_payments">
		<?php $i = 0; foreach( $payments as $key => $payment ) : ?>

			<label class="payment_method" for="<?php echo esc_attr( $payment->_id ) ?>"><img width="115" height="50" src="<?php echo esc_attr( $payment->_icon ) ?>" /></label>
			<input id="<?php echo esc_attr( $payment->_id ) ?>" type="radio" name="payment_method" value="<?php echo esc_attr( $payment->_id ) ?>"<?php echo $i === 0 ? ' checked' : '' ?>/>

		<?php $i++; endforeach; ?>
	</div>

	<?php do_action( 'donate_after_payments_checkout' ); ?>

<?php endif; ?>
