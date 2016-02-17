<?php

/**
 * Class DN_Payment_Authorize_Net
 *
 */
class DN_Payment_Authorize_Net extends DN_Payment_Base{

    /**
     * id of payment
     * @var null
     */
    public $_id = 'authorize';

    /**
     * secret key
     * @var null
     */
    protected $_api_login_id = null;

    /**
     * publish key
     * @var null
     */
    protected $_transaction_key = null;

    /**
     * api endpoint
     * @var string
     */
    protected $api_endpoint = 'https://test.authorize.net/gateway/transact.dll';

    public $_messages = null;

    /**
     * payment title
     * @var null
     */
    public $_title = null;

    function __construct()
    {
        $this->_title = __( 'AuthorizeNet', 'tp-donate' );

        $checkout = DN_Settings::instance()->checkout;
        $this->_api_login_id = $checkout->get( 'authorize_api_login_id' );
        $this->_transaction_key = $checkout->get( 'authorize_transaction_key' );

        // production environment
        if( $checkout->get( 'environment' ) === 'production' )
        {
            $this->api_endpoint = 'https://secure.authorize.net/gateway/transact.dll';
        }
        parent::__construct();

        $this->_messages = array(
                1 => __( 'This transaction has been approved.', 'tp-hotel-booking' ),
                2 => __( 'This transaction has been declined.', 'tp-hotel-booking' ),
                3 => __( 'There has been an error processing this transaction.', 'tp-hotel-booking' ),
                4 => __( ' This transaction is being held for review.', 'tp-hotel-booking' )
            );

        add_action( 'init', array( $this, 'verify' ) );
    }

    function verify()
    {
        ob_start();
        if( ! isset($_POST) )
            return;

        if( ! isset( $_POST['x_response_code'] ) )
            return;

        if( isset( $_POST['x_response_reason_text'] ) )
            donate_add_notice( $_POST['x_response_reason_text'] );

        $code = 0;
        if( isset( $_POST['x_response_code'] ) && array_key_exists( (int)$_POST['x_response_code'], $this->_messages) )
            $code = (int)$_POST['x_response_code'];

        $amout = 0;
        if( isset($_POST['x_amount']) )
            $amout = (float)$_POST['x_amount'];

        if( !isset( $_POST['x_invoice_num'] ) )
            return;

        $id = (int)$_POST['x_invoice_num'];
        $donation = DN_Donate::instance( $id );

        if( $code === 1 )
        {
            if( (float)$donation->get_meta( 'total' ) === (float)$amout )
                $status = 'donate-completed';
            else
                $status = 'donate-processing';
        }
        else
        {
            $status = 'donate-pending';
        }

        $donation->update_status( $status );
        if( in_array( $status, array( 'donate-completed', 'donate-pending' ) ) )
        {
            donate()->cart->remove_cart();
        }
        ob_end_clean();

        // redirect
        wp_redirect( donate_checkout_url() ); exit();
    }

    /**
     * fields settings
     * @return array
     */
    public function fields()
    {
        return array(
                    'title'     => $this->_title, // tab title
                    'fields'    => array(
                        'fields'        => array(
                                array(
                                        'type'      => 'select',
                                        'label'     => __( 'Enable', 'tp-donate' ),
                                        'desc'      => __( 'This controlls enable payment method', 'tp-donate' ),
                                        'atts'      => array(
                                                'id'    => 'authorize_enable',
                                                'class' => 'authorize_enable'
                                            ),
                                        'name'      => 'authorize_enable',
                                        'options'   => array(
                                                'no'                => __( 'No', 'tp-donate' ),
                                                'yes'               => __( 'Yes', 'tp-donate' )
                                            )
                                    ),
                                array(
                                        'type'      => 'input',
                                        'label'     => __( 'Api Login ID', 'tp-donate' ),
                                        'desc'      => __( 'Api login id', 'tp-donate' ),
                                        'atts'      => array(
                                                'id'    => 'authorize_api_login_id',
                                                'class' => 'authorize_api_login_id',
                                                'type'  => 'text'
                                            ),
                                        'name'      => 'authorize_api_login_id'
                                    ),
                                array(
                                        'type'      => 'input',
                                        'label'     => __( 'Transaction Key', 'tp-donate' ),
                                        'desc'      => __( 'Transaction key', 'tp-donate' ),
                                        'atts'      => array(
                                                'id'    => 'authorize_transaction_key',
                                                'class' => 'authorize_transaction_key',
                                                'type'  => 'text'
                                            ),
                                        'name'      => 'authorize_transaction_key'
                                    )
                            )
                    )
            );
    }

    function checkout_args( $amount = false )
    {
        if( ! $this->_transaction_key )
            return array( 'status' => 'failed', 'message' => __( 'Transaction Key is invalid.', 'tp-donate' ) );

        $cart = donate()->cart;
        $donation = DN_Donate::instance( $cart->donate_id );
        $donor = DN_Donor::instance( $cart->donor_id );

        $total = $cart->cart_total;
        if( $amount )
        {
            $total = (float)$amount;
        }

        $time = time();
        if ( function_exists( 'hash_hmac' ) )
        {
            $fingerprint = hash_hmac(
                    "md5",
                    $this->_api_login_id . "^" . $cart->donate_id . "^" . $time . "^" . $total . "^" . donate_get_currency(),
                    $this->_transaction_key
                );
        }
        else
        {
            $fingerprint = bin2hex(mhash(MHASH_MD5, $this->_api_login_id . "^" . $cart->donate_id . "^" . $time . "^" . $total . "^" . donate_get_currency(), $this->_transaction_key));
        }

        $nonce = wp_create_nonce( 'donate-authorize-net-nonce' );

        // 4007000000027
        $authorize_args = array(
            'x_login'                  => $this->_api_login_id,
            'x_amount'                 => $total,
            'x_currency_code'          => donate_get_currency(),
            'x_invoice_num'            => $cart->donate_id,
            'x_relay_response'         => 'FALSE',
            'x_relay_url'              => donate_checkout_url(),
            'x_fp_sequence'            => $cart->donate_id,
            'x_fp_hash'                => $fingerprint,
            'x_show_form'              => 'PAYMENT_FORM',
            'x_version'                => '3.1',
            'x_fp_timestamp'           => $time,
            'x_first_name'             => $donor->get_meta( 'first_name' ),
            'x_last_name'              => $donor->get_meta( 'last_name' ),
            'x_address'                => $donor->get_meta( 'address' ),
            // 'x_country'                => isset( $customer['_hb_country'] ) ? $customer['_hb_country'][0] : '',
            // 'x_state'                  => isset( $customer['_hb_state'] ) ? $customer['_hb_state'][0] : '',
            // 'x_city'                   => isset( $customer['_hb_city'] ) ? $customer['_hb_city'][0] : '',
            // 'x_zip'                    => isset( $customer['_hb_postal_code'] ) ? $customer['_hb_postal_code'][0] : '',
            'x_phone'                  => $donor->get_meta( 'phone' ),
            'x_email'                  => $donor->get_meta( 'email' ),
            'x_type'                   => 'AUTH_CAPTURE',
            'x_cancel_url'             => donate_checkout_url(),
            'x_email_customer'         => 'TRUE',
            'x_cancel_url_text'        => __( 'Cancel', 'tp-donate' ),
            'x_receipt_link_method'    => 'POST',
            'x_receipt_link_text'      => __( 'Click here to return our homepage.', 'tp-donate' ),
            'x_receipt_link_URL'       => add_query_arg( array( 'donate-authorize-net-status' => 'completed', 'donate-authorize-net-nonce' => $nonce ), donate_checkout_url() ),
        );

        if( DN_Settings::instance()->checkout->get( 'environment' ) === 'production' )
            $authorize_args['x_test_request'] = 'FALSE';
        else
            $authorize_args['x_test_request'] = 'TRUE';

        $authorize_args = apply_filters( 'donate_payment_authorize_net_args', $authorize_args );

        return $authorize_args;
    }

    // process
    function process( $amount = false )
    {
        return array(
                'status'    => 'success',
                'form'      => true,
                'submit_text'   => __( 'Redirect to Authorize.Net', 'tp-donate' ),
                'url'       => $this->api_endpoint,
                'args'      => $this->checkout_args( $amount )
            );
    }

}

new DN_Payment_Authorize_Net();