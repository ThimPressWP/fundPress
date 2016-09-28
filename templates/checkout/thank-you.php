<?php

defined( 'ABSPATH' ) || exit();

donate_print_notices();
?>

<div class="donate-thank-you">
    <h3><?php _e( 'Thanks for your donation.', 'tp-donate' ); ?></h3>
    <div class="donate-info">
        <ul>
            <li>
                <h4><?php _e( 'Donate Details', 'tp-donate' ); ?></h4>
            </li>
            <li>
                <strong><?php _e( 'Donate ID: ', 'tp-donate' ); ?></strong>
                <?php printf( '#%s', $donate->id ); ?>
            </li>
            <li>
                <strong><?php _e( 'Donated Total: ', 'tp-donate' ); ?></strong>
                <?php printf( '%s', donate_price( $donate->total, $donate->currency ) ) ?>
            </li>
            <li>
                <strong><?php _e( 'Donate Date: ', 'tp-donate' ); ?></strong>
                <?php echo date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $donate->post_date ) ) ?>
            </li>
            <li>
                <strong><?php _e( 'Payment Method: ', 'tp-donate' ); ?></strong>
                <?php
                    $payment = $donate->payment_method;
                    $payments_enable = donate_payment_gateways();
                    if ( array_key_exists( $payment, $payments_enable ) )
                        $payment_title = $payments_enable[$payment]->_title;
                ?>
                <span class="donate-status <?php echo esc_attr( $payment ); ?>"><?php printf( '%s', $payment_title ) ?></span>
            </li>
            <li>
                <strong><?php _e( 'Donate Status: ', 'tp-donate' ); ?></strong>
                <?php echo donate_get_status_label( $donate->id ) ?>
            </li>
        </ul>
    </div>

    <div class="donor-info">
        <?php $donor = $donate->get_donor(); ?>
        <ul>
            <li>
                <h4><?php _e( 'Donor Information', 'tp-donate' ); ?></h4>
            </li>
            <li>
                <strong><?php _e( 'First Name: ', 'tp-donate' ); ?></strong>
                <?php printf( '%s', $donor->first_name ) ?>
            </li>
            <li>
                <strong><?php _e( 'Last Name: ', 'tp-donate' ); ?></strong>
                <?php printf( '%s', $donor->last_name ) ?>
            </li>
            <li>
                <strong><?php _e( 'Email: ', 'tp-donate' ); ?></strong>
                <?php printf( '%s', $donor->email ) ?>
            </li>
            <li>
                <strong><?php _e( 'Phone: ', 'tp-donate' ); ?></strong>
                <?php printf( '%s', $donor->phone ) ?>
            </li>
            <li>
                <strong><?php _e( 'Addition Note: ', 'tp-donate' ); ?></strong>
                <?php printf( '%s', $donate->addition ) ?>
            </li>
        </ul>
    </div>

</div>