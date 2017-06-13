<?php

defined( 'ABSPATH' ) || exit();

donate_print_notices();

if ( ! $donate ) {
	return;
}

$donate = DN_Donate::instance( $donate );
?>

<div class="donate-thank-you">
    <h3><?php _e( 'Thanks for your donation.', 'fundpress' ); ?></h3>
    <div class="donate-info">
        <ul>
            <li>
                <h4><?php _e( 'Donate Details', 'fundpress' ); ?></h4>
            </li>
            <li>
                <strong><?php _e( 'Donate ID: ', 'fundpress' ); ?></strong>
				<?php printf( '#%s', $donate->id ); ?>
            </li>
            <li>
                <strong><?php _e( 'Donated Total: ', 'fundpress' ); ?></strong>
				<?php printf( '%s', donate_price( $donate->total, $donate->currency ) ) ?>
            </li>
            <li>
                <strong><?php _e( 'Donate Date: ', 'fundpress' ); ?></strong>
				<?php echo date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $donate->post_date ) ) ?>
            </li>
            <li>
                <strong><?php _e( 'Payment Method: ', 'fundpress' ); ?></strong>
				<?php
				$payment         = $donate->payment_method;
				$payments_enable = donate_payment_gateways();
				if ( array_key_exists( $payment, $payments_enable ) ) {
					$payment_title = $payments_enable[ $payment ]->_title;
				}
				?>
                <span class="donate-status <?php echo esc_attr( $payment ); ?>"><?php printf( '%s', $payment_title ) ?></span>
            </li>
            <li>
                <strong><?php _e( 'Donate Status: ', 'fundpress' ); ?></strong>
				<?php echo donate_get_status_label( $donate->id ) ?>
            </li>
        </ul>
    </div>

    <div class="donor-info">
		<?php $donor = $donate->get_donor(); ?>
        <ul>
            <li>
                <h4><?php _e( 'Donor Information', 'fundpress' ); ?></h4>
            </li>
            <li>
                <strong><?php _e( 'First Name: ', 'fundpress' ); ?></strong>
				<?php printf( '%s', $donor->first_name ) ?>
            </li>
            <li>
                <strong><?php _e( 'Last Name: ', 'fundpress' ); ?></strong>
				<?php printf( '%s', $donor->last_name ) ?>
            </li>
            <li>
                <strong><?php _e( 'Email: ', 'fundpress' ); ?></strong>
				<?php printf( '%s', $donor->email ) ?>
            </li>
            <li>
                <strong><?php _e( 'Phone: ', 'fundpress' ); ?></strong>
				<?php printf( '%s', $donor->phone ) ?>
            </li>
            <li>
                <strong><?php _e( 'Addition Note: ', 'fundpress' ); ?></strong>
				<?php printf( '%s', $donate->addition ) ?>
            </li>
        </ul>
    </div>

</div>