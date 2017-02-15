<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class DN_Payment_Paypal
 */
class DN_Payment_Paypal extends DN_Payment_Base {

    /**
     * id of payment
     * @var null
     */
    public $id = 'paypal';
    // email
    protected $paypal_email = null;
    // url
    protected $paypal_url = null;
    // payment url
    protected $paypal_payment_url = null;

    /**
     * payment title
     * @var null
     */
    public $_title = null;

    public function __construct() {
        $this->_title = __( 'Paypal', 'fundpress' );

        $this->paypal_url = 'https://www.sandbox.paypal.com/';
        $this->paypal_payment_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        $this->paypal_email = DN_Settings::instance()->checkout->get( 'paypal_sanbox_email' );
        $this->icon = 'icon-paypal';

        // production environment
        if ( DN_Settings::instance()->checkout->get( 'environment' ) === 'production' ) {
            $this->paypal_url = 'https://www.paypal.com/';
            $this->paypal_payment_url = 'https://www.paypal.com/cgi-bin/webscr';
            $this->paypal_email = DN_Settings::instance()->checkout->get( 'paypal_email' );
        }
        // init process
        add_action( 'init', array( $this, 'payment_validation' ), 99 );
        parent::__construct();
    }

    // callback
    public function payment_validation() {
        if ( isset( $_GET['donate-paypal-payment'] ) && $_GET['donate-paypal-payment'] && ! empty( $_GET['donate-id'] ) ) {
            if ( !isset( $_GET['donate-paypal-nonce'] ) || !wp_verify_nonce( $_GET['donate-paypal-nonce'], 'donate-paypal-nonce' ) )
                return;

            if ( $_GET['donate-paypal-payment'] === 'completed' ) {
                $this->completed_process_message();

                DN_Cart::instance()->remove_cart();
            } else if ( $_GET['donate-paypal-payment'] === 'cancel' ) {
                donate_add_notice( 'error', __( 'Donate is cancel.', 'fundpress' ) );
            }
            // redirect
            wp_redirect( donate_get_thankyou_link( $_GET['donate-id'] ) );
            exit();
        }

        // validate payment notify_url, update status
        if ( !empty( $_POST ) && isset( $_POST['txn_type'] ) && $_POST['txn_type'] === 'web_accept' ) {
            if ( !isset( $_POST['payment_status'] ) )
                return;

            if ( empty( $_POST['custom'] ) )
                return;

            // transaction object
            $transaction_subject = stripcslashes( $_POST['custom'] );
            $transaction_subject = json_decode( $transaction_subject );

            if ( !$donate_id = $transaction_subject->donate_id )
                return;

            $donate = DN_Donate::instance( $donate_id );

            // santitize
            $pay_verify = array_merge( array( 'cmd' => '_notify-validate' ), array_map( 'stripcslashes', $_POST ) );

            $paypal_api_url = isset( $_POST['test_ipn'] ) && $_POST['test_ipn'] == 1 ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

            $params = array(
                'body' => $pay_verify,
                'timeout' => 60,
                'httpversion' => '1.1',
                'compress' => false,
                'decompress' => false,
                'user-agent' => 'Donation'
            );
            // $response = wp_remote_post( $paypal_api_url, array( 'body' => $pay_verify ) );
            $response = wp_safe_remote_post( $paypal_api_url, $params );

            if ( !is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
                $body = wp_remote_retrieve_body( $response );

                if ( strtolower( $body ) === 'verified' ) {
                    // payment status
                    $payment_status = strtolower( $_POST['payment_status'] );

                    if ( in_array( $payment_status, array( 'pending', 'completed' ) ) ) {
                        $status = 'donate-completed';
                        $donate->update_status( $status );
                    }
                }
            } else {
                // var_dump($response); die();
            }
        }
    }

    /**
     * fields settings
     * @return array
     */
    public function fields() {
        return array(
            'title' => $this->_title, // tab title
            'fields' => array(
                'fields' => array(
                    array(
                        'type' => 'select',
                        'label' => __( 'Enable', 'fundpress' ),
                        'desc' => __( 'This controlls enable payment method', 'fundpress' ),
                        'atts' => array(
                            'id' => 'paypal_enable',
                            'class' => 'paypal_enable'
                        ),
                        'name' => 'paypal_enable',
                        'options' => array(
                            'no' => __( 'No', 'fundpress' ),
                            'yes' => __( 'Yes', 'fundpress' )
                        )
                    ),
                    array(
                        'type' => 'input',
                        'label' => __( 'Paypal email', 'fundpress' ),
                        'desc' => __( 'Production environment', 'fundpress' ),
                        'atts' => array(
                            'id' => 'paypal_email',
                            'class' => 'paypal_email',
                            'type' => 'text'
                        ),
                        'name' => 'paypal_email'
                    ),
                    array(
                        'type' => 'input',
                        'label' => __( 'Paypal sandbox email', 'fundpress' ),
                        'desc' => __( 'Test environment', 'fundpress' ),
                        'atts' => array(
                            'id' => 'paypal_sanbox_email',
                            'class' => 'paypal_sanbox_email',
                            'type' => 'text'
                        ),
                        'name' => 'paypal_sanbox_email'
                    )
                ),
            )
        );
    }

    /**
     * get_item_name
     * @return string
     */
    public function get_item_name() {
        $description = array();
        if ( $cart_items = donate()->cart->cart_contents ) {
            foreach ( $cart_items as $cart_item_key => $cart_item ) {
                $description[] = sprintf( '%s(%s)', $cart_item->product_data->post_title, donate_price( $cart_item->amount, $cart_item->currency ) );
            }
        } else {
            $description[] = sprintf( '%s %s - %s', __( 'Donate for', 'fundpress' ), get_bloginfo( 'name' ), get_bloginfo( 'description' ) );
        }

        return implode( ',', $description );
    }

    /**
     * checkout url
     * @return url string
     */
    public function checkout_url( $donate = null ) {

        // create nonce
        $nonce = wp_create_nonce( 'donate-paypal-nonce' );

        $email = $donate->get_donor()->email;

        $total = floatval( $donate->total );
        // query post
        $query = array(
            'cmd' => '_xclick',
            'amount' => $total,
            'quantity' => '1',
            'business' => $this->paypal_email, // business email paypal
            'item_name' => $this->get_item_name(),
            'currency_code' => donate_get_currency(),
            'notify_url' => donate_checkout_url(),
            'no_note' => '1',
            'shipping' => '0',
            'email' => $email,
            'rm' => '2',
            'no_shipping' => '1',
            'return' => add_query_arg( array( 'donate-paypal-payment' => 'completed', 'donate-paypal-nonce' => $nonce ), donate_get_thankyou_link( $donate->id ) ),
            'cancel_return' => add_query_arg( array( 'donate-paypal-payment' => 'cancel', 'donate-paypal-nonce' => $nonce ), donate_checkout_url() ),
            'custom' => json_encode( array( 'donate_id' => $donate->id, 'donor_id' => $donate->donor_id ) )
        );

        // allow hook paypal param
        $query = apply_filters( 'donate_payment_paypal_params', $query );

        return $this->paypal_payment_url . '?' . http_build_query( $query );
    }

    public function process( $donate = false, $posted = array() ) {
        if ( !$this->paypal_email ) {
            return array(
                'status' => 'failed',
                'message' => __( 'Email Business PayPal is invalid. Please contact administrator to setup PayPal email.', 'fundpress' )
            );
        }
        return array(
            'status' => 'success',
            'url' => $this->checkout_url( $donate )
        );
    }

}
