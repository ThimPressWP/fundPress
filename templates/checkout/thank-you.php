<?php

defined( 'ABSPATH' ) || exit();

donate_print_notices();
?>

<div class="donate-thank-you">
    <h3><?php _e( 'Thank you for your donated.', 'tp-donate' ); ?></h3>
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
                <strong><?php _e( 'Addtion Note: ', 'tp-donate' ); ?></strong>
                <?php printf( '%s', $donate->addition ) ?>
            </li>
        </ul>
    </div>

</div>