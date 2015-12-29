<?php

/**
 * Class DN_Payment_Palpal
 */
class DN_Payment_Palpal extends DN_Payment_Base{

    /**
     * id of payment
     * @var null
     */
    public $_id = 'paypal';

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

    function __construct()
    {
        $this->_title = __( 'Paypal', 'tp-donate' );

        $this->paypal_url = 'https://www.sandbox.paypal.com/';
        $this->paypal_payment_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        $this->paypal_email = DN_Settings::instance()->checkout->get( 'paypal_sanbox_email' );

        // production environment
        if( DN_Settings::instance()->checkout->environment === 'production' )
        {
            $this->paypal_url = 'https://www.paypal.com/';
            $this->paypal_payment_url = 'https://www.paypal.com/cgi-bin/webscr';
            $this->paypal_email = DN_Settings::instance()->checkout->get( 'paypal_email' );
        }

        // init process
        add_action( 'init', array( $this, 'payment_validation'), 99 );
        parent::__construct();
    }

    // callback
    function payment_validation()
    {
        if( isset( $_GET[ 'donate-paypal-nonce' ] ) && isset( $_GET[ 'donate-paypal-payment' ] ) )
        {
            if( $_GET[ 'donate-paypal-payment' ] === 'completed' )
            {
                donate_add_notice( 'success', __( 'Payment completed. We will send you email when payment method validate.', 'tp-donate' ) );
                donate()->cart->remove_cart();
            }
            else if( $_GET[ 'donate-paypal-payment' ] === 'cancel' )
            {
                donate_add_notice( 'errors', __( 'Donate is cancel.', 'tp-donate' ) );
            }
            // redirect
            $url = add_query_arg( array(  'donate-paypal-nonce' => $_GET[ 'donate-paypal-nonce' ]  ), donate_checkout_url() );
            wp_redirect( $url ); exit();
        }

    }

    /**
     * fields settings
     * @return array
     */
    public function fields()
    {
        return  array(
                    'title'     => $this->_title, // tab title
                    'fields'    => array(
                            'fields'        => array(
                                array(
                                        'type'      => 'select',
                                        'label'     => __( 'Enable', 'tp-donate' ),
                                        'desc'      => __( 'This controlls enable payment method', 'tp-donate' ),
                                        'atts'      => array(
                                                'id'    => 'paypal_enable',
                                                'class' => 'paypal_enable'
                                            ),
                                        'name'      => 'paypal_enable',
                                        'options'   => array(
                                                'no'                => __( 'No', 'tp-donate' ),
                                                'yes'               => __( 'Yes', 'tp-donate' )
                                            )
                                    ),
                                array(
                                        'type'      => 'input',
                                        'label'     => __( 'Paypal email', 'tp-donate' ),
                                        'desc'      => __( 'Production environment', 'tp-donate' ),
                                        'atts'      => array(
                                                'id'    => 'paypal_email',
                                                'class' => 'paypal_email',
                                                'type'  => 'text'
                                            ),
                                        'name'      => 'paypal_email'
                                    ),
                                array(
                                        'type'      => 'input',
                                        'label'     => __( 'Paypal sandbox email', 'tp-donate' ),
                                        'desc'      => __( 'Test environment', 'tp-donate' ),
                                        'atts'      => array(
                                                'id'    => 'paypal_sanbox_email',
                                                'class' => 'paypal_sanbox_email',
                                                'type'  => 'text'
                                            ),
                                        'name'      => 'paypal_sanbox_email'
                                    )
                            ),

                )
            );
    }

    function get_item_name()
    {
        $cart_items = donate()->cart->cart_contents;
        $description = array();
        foreach ( $cart_items as $cart_item_key => $cart_item ) {
            $description[] = sprintf( '%s(%s)', $cart_item->product_data->post_title, donate_price( $cart_item->amount, $cart_item->currency ) );
        }

        return implode( ',', $description );
    }

    /**
     * checkout url
     * @return url string
     */
    function checkout_url()
    {
        // cart
        $cart = donate()->cart;

        // create nonce
        $nonce = wp_create_nonce( 'donate-paypal-nonce' );

        $email = DN_Donor::instance( $cart->donor_id )->get_meta( 'email' );

        $query = array(
            'cmd'           => '_xclick',
            'amount'        => $cart->cart_total,
            'quantity'      => '1',
            'business'      => $this->paypal_email, // business email paypal
            'item_name'     => $this->get_item_name(),
            'currency_code' => donate_get_currency(),
            'notify_url'    => donate_checkout_url(),
            'no_note'       => '1',
            'shipping'      => '0',
            'email'         => $email,
            'rm'            => '2',
            'no_shipping'   => '1',
            'return'        => add_query_arg( array( 'donate-paypal-payment' => 'completed', 'donate-paypal-nonce' => $nonce ), donate_checkout_url() ),
            'cancel_return' => add_query_arg( array( 'donate-paypal-payment' => 'cancel', 'donate-paypal-nonce' => $nonce ), donate_checkout_url() ),
            // 'custom'        => json_encode( $cart->cart_contents )
        );

        // allow hook paypal param
        $query = apply_filters( 'donate_payment_paypal_params', $query );

        return $this->paypal_payment_url . '?' . http_build_query( $query );
    }

    public function process()
    {
        return array(
                'status'    => 'success',
                'url'       => $this->checkout_url()
            );
    }

}

new DN_Payment_Palpal();