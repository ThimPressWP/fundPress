<?php

/**
 * Class DN_Payment_Stripe
 */
class DN_Payment_Stripe extends DN_Payment_Base{

    /**
     * id of payment
     * @var null
     */
    public $_id = 'stripe';

    /**
     * secret key
     * @var null
     */
    protected $secret_key = null;

    /**
     * publish key
     * @var null
     */
    protected $publish_key = null;

    /**
     * api endpoint
     * @var string
     */
    protected $api_endpoint = 'https://api.stripe.com/v1';

    /**
     * payment title
     * @var null
     */
    public $_title = null;

    function __construct()
    {
        $this->_title = __( 'Stripe', 'tp-donate' );

        $checkout = DN_Settings::instance()->checkout;
        $this->secret_key = $checkout->get( 'stripe_test_secret_key' );
        $this->publish_key = $checkout->get( 'stripe_test_publish_key' );


        // production environment
        if( $checkout->get( 'environment' ) === 'production' )
        {
            $this->secret_key = $checkout->get( 'stripe_live_secret_key' );
            $this->publish_key = $checkout->get( 'stripe_live_publish_key' );
        }
        parent::__construct();

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
        add_action( 'wp_footer', array( $this, 'process_script_js' ) );
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
                                                'id'    => 'stripe_enable',
                                                'class' => 'stripe_enable'
                                            ),
                                        'name'      => 'stripe_enable',
                                        'options'   => array(
                                                'no'                => __( 'No', 'tp-donate' ),
                                                'yes'               => __( 'Yes', 'tp-donate' )
                                            )
                                    ),
                                array(
                                        'type'      => 'input',
                                        'label'     => __( 'Test Secret Key', 'tp-donate' ),
                                        'desc'      => __( 'Test environment', 'tp-donate' ),
                                        'atts'      => array(
                                                'id'    => 'stripe_test_secret_key',
                                                'class' => 'stripe_test_secret_key',
                                                'type'  => 'text'
                                            ),
                                        'name'      => 'stripe_test_secret_key'
                                    ),
                                array(
                                        'type'      => 'input',
                                        'label'     => __( 'Test Publish Key', 'tp-donate' ),
                                        'desc'      => __( 'Test environment', 'tp-donate' ),
                                        'atts'      => array(
                                                'id'    => 'stripe_test_publish_key',
                                                'class' => 'stripe_test_publish_key',
                                                'type'  => 'text'
                                            ),
                                        'name'      => 'stripe_test_publish_key'
                                    ),
                                array(
                                        'type'      => 'input',
                                        'label'     => __( 'Live Secret Key', 'tp-donate' ),
                                        'desc'      => __( 'Production environment', 'tp-donate' ),
                                        'atts'      => array(
                                                'id'    => 'stripe_live_secret_key',
                                                'class' => 'stripe_live_secret_key',
                                                'type'  => 'text'
                                            ),
                                        'name'      => 'stripe_live_secret_key'
                                    ),
                                array(
                                        'type'      => 'input',
                                        'label'     => __( 'Live Publish Key', 'tp-donate' ),
                                        'desc'      => __( 'Production environment', 'tp-donate' ),
                                        'atts'      => array(
                                                'id'    => 'stripe_live_publish_key',
                                                'class' => 'stripe_live_publish_key',
                                                'type'  => 'text'
                                            ),
                                        'name'      => 'stripe_live_publish_key'
                                    ),
                            )
                    )
            );
    }

    // process
    function process()
    {
        if( ! isset( $_POST[ 'id' ] ) )
           return array( 'status' => 'error', 'message' => __( 'Token is invalid', 'tp-donate' ) );

        $token = $_POST[ 'id' ];

        $cart = donate()->cart;

        $donor = DN_Donor::instance( $cart->donor_id );

        $customer_id = $donor->get_meta( 'stripe_id' );

        if( ! $customer_id )
        {
            $params = array(
                    'description'   => sprintf( '%s %s', __( 'Donor for', 'tp-donate' ), $donor->get_meta( 'email' ) ),
                    'source'        => $token
                );
            // create customer
            $response = $this->stripe_request( 'customers', $params );

            if( is_wp_error( $response ) && ! $response->id )
            {
                return array( 'status' => 'error', 'message' => sprintf( __( '%s. Please try again', 'tp-hotel-booking' ), $response->get_error_message() ) );
            }

            $customer_id = $response->id;

            $donor->set_meta( 'stripe_id', $customer_id );
        }

        $params = array(
                'amount'        => $cart->cart_total * 100,
                'currency'      => donate_get_currency(),
                'customer'      => $customer_id,
                'description'   => sprintf(
                    __( '%s - donate %s', 'tp-hotel-booking' ),
                    esc_html( get_bloginfo( 'name' ) ),
                    donate_generate_post_key( $cart->donate_id )
                )
            );
        // create charges
        $response = $this->stripe_request( 'charges', $params );

        if( $response && ! is_wp_error( $response ) && $response->id )
        {
            $donate = DN_Donate::instance( $cart->donate_id );
            $donate->update_status( 'donate-completed' );

            $return = array(
                'status'    => 'success',
                'url'       => donate_checkout_url()
            );
            // remove cart
            DN_Cart::instance()->remove_cart();
        }
        else
        {
            $return = array( 'result' => 'error', 'message' => __( 'Please try again!', 'tp-donate' ) );
        }
        return $return;
    }

    // stripe request
    function stripe_request( $api = 'charges', $params = array() )
    {
        $response = wp_remote_post( $this->api_endpoint . '/' . $api, array(
                'method'        => 'POST',
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode( $this->secret_key . ':' )
                ),
                'body'          => $params,
                'timeout'       => 70,
                'sslverify'     => false,
                'user-agent'    => 'Donate ' . TP_DONATE_VER
        ));

        if( ! is_wp_error( $response ) )
        {
            $body = wp_remote_retrieve_body( $response );
            if( $body )
                $body = json_decode( $body );

            if( ! empty( $body->error ) )
            {
                return new WP_Error( 'stripe_error', $body->error->message );
            }

            if( empty( $body->id ) )
            {
                return new WP_Error( 'stripe_error', __( 'Stripe Process went wrong', 'tp-donate' ) );
            }

            return $body;
        }

        return new WP_Error( 'stripe_error', $response->get_error_message() );
    }

    // enquene script
    function enqueue_script()
    {
        if( ! $this->is_enable )
            return;

        $stripe = apply_filters( 'donate_stripe_payment_object', array(
                    'Secret_Key'    => $this->secret_key,
                    'Publish_Key'   => $this->publish_key,
                    'key_missing'   => __( 'Stripe key is expired. Please contact administrator to do this payment gateway', 'tp-donate' )
            ) );

        wp_register_script( 'donate_payment_stripe', 'https://js.stripe.com/v2/', array(), TP_DONATE_VER, true );
        wp_register_script( 'donate_payment_stripe_checkout', TP_DONATE_LIB_URI . '/stripe/checkout.js' , array(), TP_DONATE_VER, true );
        wp_localize_script( 'donate_payment_stripe', 'Donate_Stripe_Settings', $stripe );

        wp_enqueue_script( 'donate_payment_stripe' );
        wp_enqueue_script( 'donate_payment_stripe_checkout' );
    }

    // process script
    function process_script_js()
    { ?>
    <script type="text/javascript">

        (function($){

            Donate_Stripe_Payment = {

                load_form: function( form )
                {
                    var pl_key = 'pk_test_HHukcwWCsD7qDFWKKpKdJeOT';
                    if ( typeof Donate_Stripe_Settings !== 'undefined' && Donate_Stripe_Settings.Publish_Key )
                    {
                        pl_key = Donate_Stripe_Settings.Publish_Key;

                        var handler = StripeCheckout.configure({
                            key   : pl_key,
                            image : 'https://stripe.com/img/documentation/checkout/marketplace.png',
                            locale: 'auto',
                            token : function (token) {
                                // Use the token to create the charge with a server-side script.
                                // You can access the token ID with `token.id`
                                Donate_Stripe_Payment.stripe_payment_process(form, token);
                            }
                        });

                        var first_name = form.find('input[name="first_name"]').val().trim();
                        var last_name = form.find('input[name="last_name"]').val().trim();
                        var email = form.find('input[name="email"]').val().trim();

                        var amount = form.find('input[name="amount"]').val().trim();

                        // Open Checkout with further options
                        handler.open({
                            name       : first_name + ' ' + last_name,
                            description: email,
                            amount     : amount * 100
                        });
                    }
                    else
                    {
                        alert( Donate_Stripe_Settings.key_missing );
                    }
                },

                stripe_payment_process: function ( form, token )
                {
                    var data = {};
                    var payment_data = form.serializeArray();

                    $.each(payment_data, function (index, obj) {
                        data[obj.name] = obj.value;
                    });

                    $.extend(token, data);

                    $.ajax({
                        url       : thimpress_donate.ajaxurl,
                        data      : token,
                        type      : 'POST',
                        beforeSend: function () {
                            DONATE_Site.beforeAjax( form );
                        }
                    }).done(function (res) {
                        DONATE_Site.beforeAjax( form );

                        if (typeof res.status !== 'undefined' && res.status == 'success') {
                            if ( typeof res.url !== 'undefined' )
                                window.location.href = res.url;
                        }
                        else if (typeof res.message !== 'undefined') {
                            alert(res.message);
                        }
                    }).fail(function () {
                        DONATE_Site.beforeAjax( form );
                    });
                }

            }

        })(jQuery);

    </script>
<?php }

}

new DN_Payment_Stripe();

// add_action( 'init', function(){
//     donate()->cart->remove_cart(); die();
// } );