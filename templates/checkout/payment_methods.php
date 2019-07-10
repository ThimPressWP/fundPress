<?php
/**
 * Template for displaying payment methods in checkout page.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/checkout/payment_methods.php
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

<?php
// all payment method is enable
$payments = fundpress_payments_enable();
?>

<?php if ( $payments ) { ?>

	<?php do_action( 'donate_before_payments_checkout' ); ?>

    <div class="donate_payments">

        <ul>
			<?php $default = ''; ?>
			<?php $i = 0; ?>
			<?php foreach ( $payments as $key => $payment ) { ?>
				<?php $default = ( ! $default ) ? $payment->id : $default; ?>
                <li<?php echo $i === 0 ? ' class="active"' : '' ?>>
                    <a href="#payment-method-<?php echo esc_attr( $payment->id ); ?>"
                       data-payment-id="<?php echo esc_attr( $payment->id ); ?>">
                        <i class="<?php echo esc_attr( $payment->icon ); ?>"></i>
                        <span><?php echo esc_html( $payment->get_title() ); ?></span>
                    </a>
                </li>
				<?php $i ++; ?>
			<?php } ?>
            <input type="hidden" name="payment_method" value="<?php echo esc_attr( $default ); ?>"/>
        </ul>

		<?php $i = 0; ?>
		<?php foreach ( $payments as $key => $payment ) { ?>
			<?php if ( $form = $payment->checkout_form() ) { ?>
                <div class="payment-method<?php echo $i === 0 ? ' active' : '' ?>"
                     id="payment-method-<?php echo esc_attr( $payment->id ); ?>">
					<?php printf( '%s', $form ); ?>
                </div>
			<?php } ?>
			<?php $i ++; ?>
		<?php } ?>

    </div>

	<?php do_action( 'donate_after_payments_checkout' ); ?>

<?php } ?>
