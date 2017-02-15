<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class DN_Payment_Stripe
 */
class DN_Payment_Stripe extends DN_Payment_Base {

    /**
     * id of payment
     * @var null
     */
    public $id = 'stripe';

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

    public function __construct() {
        $this->_title = __( 'Stripe', 'fundpress' );

        $checkout = DN_Settings::instance()->checkout;
        $this->secret_key = $checkout->get( 'stripe_test_secret_key' );
        $this->publish_key = $checkout->get( 'stripe_test_publish_key' );
        $this->icon = 'icon-credit-card';
        // production environment
        if ( $checkout->get( 'environment' ) === 'production' ) {
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
                            'id' => 'stripe_enable',
                            'class' => 'stripe_enable'
                        ),
                        'name' => 'stripe_enable',
                        'options' => array(
                            'no' => __( 'No', 'fundpress' ),
                            'yes' => __( 'Yes', 'fundpress' )
                        )
                    ),
                    array(
                        'type' => 'input',
                        'label' => __( 'Test Secret Key', 'fundpress' ),
                        'desc' => __( 'Test environment', 'fundpress' ),
                        'atts' => array(
                            'id' => 'stripe_test_secret_key',
                            'class' => 'stripe_test_secret_key',
                            'type' => 'text'
                        ),
                        'name' => 'stripe_test_secret_key'
                    ),
                    array(
                        'type' => 'input',
                        'label' => __( 'Test Publish Key', 'fundpress' ),
                        'desc' => __( 'Test environment', 'fundpress' ),
                        'atts' => array(
                            'id' => 'stripe_test_publish_key',
                            'class' => 'stripe_test_publish_key',
                            'type' => 'text'
                        ),
                        'name' => 'stripe_test_publish_key'
                    ),
                    array(
                        'type' => 'input',
                        'label' => __( 'Live Secret Key', 'fundpress' ),
                        'desc' => __( 'Production environment', 'fundpress' ),
                        'atts' => array(
                            'id' => 'stripe_live_secret_key',
                            'class' => 'stripe_live_secret_key',
                            'type' => 'text'
                        ),
                        'name' => 'stripe_live_secret_key'
                    ),
                    array(
                        'type' => 'input',
                        'label' => __( 'Live Publish Key', 'fundpress' ),
                        'desc' => __( 'Production environment', 'fundpress' ),
                        'atts' => array(
                            'id' => 'stripe_live_publish_key',
                            'class' => 'stripe_live_publish_key',
                            'type' => 'text'
                        ),
                        'name' => 'stripe_live_publish_key'
                    ),
                )
            )
        );
    }

    // process
    public function process( $donate = false, $posted = array() ) {
        if ( !$this->secret_key || !$this->publish_key ) {
            return array(
                'status' => 'failed',
                'message' => __( 'Secret key and Publish key is invalid. Please contact administrator to setup Stripe payment.', 'fundpress' )
            );
        }

        if ( empty( $posted['stripe'] ) ) {
            return array(
                'status' => 'failed',
                'message' => __( 'Credit Card information error.', 'fundpress' )
            );
        }

        $card_number = isset( $posted['stripe']['cc-number'] ) ? sanitize_text_field( $posted['stripe']['cc-number'] ) : '';
        list( $card_exp_month, $card_exp_year ) = array_map( 'trim', explode( '/', isset( $posted['stripe']['cc-exp'] ) ? $posted['stripe']['cc-exp'] : array()  ) );
        $card_cvc = isset( $posted['stripe']['cc-cvc'] ) ? sanitize_text_field( $posted['stripe']['cc-cvc'] ) : '';

        $tokens = $this->stripe_request( 'tokens', array(
            'card' => array(
                'number' => $card_number,
                'exp_month' => $card_exp_month,
                'exp_year' => $card_exp_year,
                'cvc' => $card_cvc,
            )
                ) );
        if ( is_wp_error( $tokens ) || !$tokens->id ) {
            return array( 'status' => 'failed', 'message' => sprintf( '%s. ' . __( 'Please try again', 'fundpress' ), $response->get_error_message() ) );
        }

        $token = $tokens->id;

        $donor = DN_Donor::instance( $donate->donor_id );

        $customer_id = $donor->stripe_id;

        if ( !$customer_id ) {
            $params = array(
                'description' => sprintf( '%s %s', __( 'Donor for', 'fundpress' ), $donor->email ),
                'source' => $token
            );
            // create customer
            $response = $this->stripe_request( 'customers', $params );

            if ( is_wp_error( $response ) && !$response->id ) {
                return array( 'status' => 'failed', 'message' => sprintf( '%s. ' . __( 'Please try again', 'fundpress' ), $response->get_error_message() ) );
            }

            $customer_id = $response->id;

            $donor->set_meta( 'stripe_id', $customer_id );
        }

        $total = $donate->total;

        $params = array(
            'amount' => round( $total * 100 ),
            'currency' => donate_get_currency(),
            'customer' => $customer_id,
            'description' => sprintf(
                    __( '%s - donate %s', 'fundpress' ), esc_html( get_bloginfo( 'name' ) ), donate_generate_post_key( $donate->id )
            )
        );
        // create charges
        $response = $this->stripe_request( 'charges', $params );
        if ( $response && !is_wp_error( $response ) && $response->id ) {
            $donate->update_status( 'donate-completed' );

            // notice message completed
            $this->completed_process_message();

            $return = array(
                'status' => 'success',
                'url' => donate_get_thankyou_link( $donate->id )
            );
            // remove cart
            DN_Cart::instance()->remove_cart();
        } else {
            $return = array( 'result' => 'failed', 'message' => __( 'Connect Stripe has error. Please try again!', 'fundpress' ) );
        }
        return $return;
    }

    // stripe request
    public function stripe_request( $api = 'charges', $params = array() ) {
        $response = wp_safe_remote_post( $this->api_endpoint . '/' . $api, array(
            'method' => 'POST',
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( $this->secret_key . ':' )
            ),
            'body' => $params,
            'timeout' => 70,
            'sslverify' => false,
            'user-agent' => 'Donate ' . TP_DONATE_VER
                ) );

        if ( !is_wp_error( $response ) ) {
            $body = wp_remote_retrieve_body( $response );
            if ( $body )
                $body = json_decode( $body );

            if ( !empty( $body->error ) ) {
                return new WP_Error( 'stripe_error', $body->error->message );
            }

            if ( empty( $body->id ) ) {
                return new WP_Error( 'stripe_error', __( 'Stripe process went wrong', 'fundpress' ) );
            }

            return $body;
        }

        return new WP_Error( 'stripe_error', $response->get_error_message() );
    }

    /**
     * payment checkout form
     */
    public function checkout_form() {
        ob_start();
        require TP_DONATE_INC . '/gateways/stripe/checkout-form.php';
        return ob_get_clean();
    }

    // enquene script
    public function enqueue_script() {
        if ( !$this->is_enable )
            return;

        $stripe = apply_filters( 'donate_stripe_payment_object', array(
            'Secret_Key' => $this->secret_key,
            'Publish_Key' => $this->publish_key,
            'key_missing' => __( 'Stripe key is expired. Please contact administrator to do this payment gateway', 'fundpress' )
                ) );

        wp_register_script( 'donate_payment_stripe', TP_DONATE_INC_URI . '/gateways/stripe/jquery.payment.min.js', array(), TP_DONATE_VER, true );
        wp_localize_script( 'donate_payment_stripe', 'Donate_Stripe_Settings', $stripe );

        wp_enqueue_script( 'donate_payment_stripe' );
    }

    // process script
    public function process_script_js() {
        ?>
        <script type="text/javascript">
            ( function ( $ ) {
                if ( typeof $.fn.payment !== 'undefined' ) {

                    window.Donate_Stripe_Payment = {
                        init: function () {
                            $( '.stripe-cc-number' ).payment( 'formatCardNumber' );
                            $( '.stripe-cc-exp' ).payment( 'formatCardExpiry' );
                            $( '.stripe-cc-cvc' ).payment( 'formatCardCVC' );
                            TP_Donate_Global.addFilter( 'donate_before_submit_form', this.before_submit_checkout );
                        },
                        before_submit_checkout: function ( data ) {
                            var is_stripe = false;
                            for ( var i = 0; i < data.length; i++ ) {
                                if ( data[i].name === 'payment_method' && data[i].value === 'stripe' ) {
                                    is_stripe = true;
                                }
                            }
                            if ( is_stripe && !Donate_Stripe_Payment.validator_credit_card() ) {
                                return false;
                            }

                            return data;
                        },
                        /**
                         * validate create card format
                         * @returns boolean
                         */
                        validator_credit_card: function () {
                            var card_num = $( '.stripe-cc-number' ),
                                card_expiry = $( '.stripe-cc-exp' ),
                                card_cvc = $( '.stripe-cc-cvc' ),
                                card_type = $.payment.cardType( card_num.val() );
                            var validated = true;
                            /*
                             * validate card number
                             */
                            if ( !$.payment.validateCardNumber( card_num.val() ) ) {
                                validated = false;
                                card_num.addClass( 'error' ).removeClass( 'validated' );
                            } else {
                                card_num.addClass( 'validated' ).removeClass( 'error' );
                            }
                            /**
                             * vaildate card expired
                             */
                            if ( !card_expiry.val() || !$.payment.cardExpiryVal( card_expiry.val() ) ) {
                                validated = false;
                                card_expiry.addClass( 'error' ).removeClass( 'validated' );
                            } else {
                                card_expiry.addClass( 'validated' ).removeClass( 'error' );
                            }
                            /**
                             * validate card cvc
                             */
                            if ( !card_cvc.val() || !$.payment.validateCardCVC( card_cvc.val(), card_type ) ) {
                                validated = false;
                                card_cvc.addClass( 'error' ).removeClass( 'validated' );
                            } else {
                                card_cvc.addClass( 'validated' ).removeClass( 'error' );
                            }
                            return validated;
                        }
                    };
                    $( document ).ready( function () {
                        Donate_Stripe_Payment.init();
                    } );
                }
            } )( jQuery );
        </script>
        <?php

    }

}
