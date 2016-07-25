<?php
if ( !defined( 'ABSPATH' ) )
    exit();
/**
 * Payment method template
 */
// all payment method is enable
$payments = donate_payments_enable();
?>

<?php if ( $payments ) : ?>

    <?php do_action( 'donate_before_payments_checkout' ); ?>

    <div class="donate_payments">
        <ul>
            <?php
            $default = '';
            $i = 0;
            foreach ( $payments as $key => $payment ) :
                $default = (!$default ) ? $payment->id : $default;
                ?>
                <li<?php echo $i === 0 ? ' class="active"' : '' ?>>
                    <a href="#payment-method-<?php echo esc_attr( $payment->id ); ?>" data-payment-id="<?php echo esc_attr( $payment->id ); ?>">
                        <i class="<?php echo esc_attr( $payment->icon ); ?>"></i>
                        <span><?php echo esc_html( $payment->get_title() ); ?></span>
                    </a>
                </li>

                <?php
                $i++;
            endforeach;
            ?>
            <input type="hidden" name="payment_method" value="<?php echo esc_attr( $default ); ?>" />
        </ul>
        <?php $i = 0;
        foreach ( $payments as $key => $payment ) :
            ?>
                <?php if ( $form = $payment->checkout_form() ) : ?>
                <div class="payment-method<?php echo $i === 0 ? ' active' : '' ?>" id="payment-method-<?php echo esc_attr( $payment->id ); ?>">
                <?php printf( '%s', $form ); ?>
                </div>
            <?php endif; ?>
            <?php $i++;
        endforeach;
        ?>
    </div>

    <?php do_action( 'donate_after_payments_checkout' ); ?>

<?php endif; ?>
