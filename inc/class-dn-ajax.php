<?php

if ( !defined( 'ABSPATH' ) ) {
    exit();
}

class DN_Ajax {

    public function __construct() {

        if ( !defined( 'DOING_AJAX' ) || !DOING_AJAX )
            return;

        $actions = array(
            'donate_load_form' => true,
            'donate_submit' => true
        );

        foreach ( $actions as $action => $nopriv ) {

            if ( !method_exists( $this, $action ) )
                return;

            add_action( 'wp_ajax_' . $action, array( $this, $action ) );
            if ( $nopriv ) {
                add_action( 'wp_ajax_nopriv_' . $action, array( $this, $action ) );
            }
        }
    }

    /**
     * ajax load form
     * @return
     */
    public function donate_load_form() {
        if ( !isset( $_GET['schema'] ) || $_GET['schema'] !== 'donate-ajax' || empty( $_POST ) )
            return;

        if ( !isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'thimpress_donate_nonce' ) )
            return;

        /**
         * load form with campaign ID
         */
        $payments = array();
        $payment_enable = donate_payments_enable();
        foreach ( $payment_enable as $key => $payment ) {
            $payments[] = array(
                'id' => $payment->id,
                'title' => $payment->_title,
                'icon' => $payment->icon
            );
        }
        if ( isset( $_POST['campaign_id'] ) && is_numeric( $_POST['campaign_id'] ) ) {
            $campaign = get_post( $_POST['campaign_id'] );

            if ( !$campaign || $campaign->post_type !== 'dn_campaign' ) {
                wp_send_json( array( 'status' => 'failed', 'message' => __( 'Campaign is not exists in our system.', 'tp-donate' ) ) );
            }

            $campaign_id = $campaign->id;
            $campaign = DN_Campaign::instance( $campaign );

            $compensates = array();
            $currency = $campaign->get_currency();

            if ( $eachs = $campaign->get_compensate() ) {
                foreach ( $eachs as $key => $compensate ) {
                    /**
                     * convert campaign amount currency to amount with currency setting
                     * @var
                     */
                    $amount = donate_campaign_convert_amount( $compensate['amount'], $currency );
                    $compensates[$key] = array(
                        'amount' => donate_price( $amount ),
                        'desc' => $compensate['desc']
                    );
                }
            }

            // load payments when checkout on lightbox setting isset yes
            if ( DN_Settings::instance()->checkout->get( 'lightbox_checkout', 'no' ) !== 'yes' ) {
                $payments = array();
            }

            $results = array(
                'status' => 'success',
                'campaign_id' => $campaign->id,
                'campaign_title' => $campaign->get_title(),
                'compensates' => $compensates,
                'currency' => donate_get_currency(),
                'currency_symbol' => donate_get_currency_symbol(),
                'payments' => $payments // list payment allow
            );
        } else { // load form donate now button 
            $results = array(
                'status' => 'success',
                'campaign_title' => apply_filters( 'donate_form_title_without_campaign', sprintf( '%s - %s', get_bloginfo( 'name' ), get_bloginfo( 'description' ) ) ),
                'currency' => donate_get_currency(),
                'currency_symbol' => donate_get_currency_symbol(),
                'allow_payment' => true,
                'donate_system' => true,
                'payments' => $payments // list payment allow
            );
        }

        $results = apply_filters( 'donate_load_form_donate_results', $results );
        wp_send_json( $results );
    }

    /**
     * donoate submit lightbox
     * @return
     */
    public function donate_submit() {
        // validate sanitize input $_POST
        if ( !isset( $_GET['schema'] ) || $_GET['schema'] !== 'donate-ajax' || empty( $_POST ) )
            wp_send_json( array( 'status' => 'failed', 'message' => array( __( 'Could not do action.', 'tp-donate' ) ) ) );

        /* process checkout */
        ThimPress_Donate::instance()->checkout->process_checkout();
        die( 0 );
    }

}

new DN_Ajax();
