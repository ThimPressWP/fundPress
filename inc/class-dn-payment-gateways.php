<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class-dn-payment-gateways
 *
 * @author ducnvtt
 */
class DN_Payment_Gateways {
    /*
     * instance cache object
     */

    public static $instance = null;

    /**
     * payment gateways
     * @var type array
     */
    public $payment_gateways = array();

    public function __construct() {
        /**
         * load payment gateways
         */
        $this->load_payment_gateways();
    }

    public function load_payment_gateways() {
        $payment_gateways = array(
            'DN_Payment_Paypal',
            'DN_Payment_Stripe',
            'DN_Payment_Authorize_Net'
        );

        $payment_gateways = apply_filters( 'donate_payment_gateways', $payment_gateways );

        foreach ( $payment_gateways as $payment ) {
            $payment = class_exists( $payment ) ? new $payment : null;
            if ( $payment ) {
                $this->payment_gateways[$payment->id] = $payment;
            }
        }
        /**
         * return all payment activated
         */
        return $this->payment_gateways;
    }

    /**
     * get payment available already to process checkout
     */
    public function get_payment_available() {
        $payment_gateways_available = array();
        foreach ( $this->payment_gateways as $id => $payment ) {
            if ( $payment->is_enable ) {
                $payment_gateways_available[$id] = $payment;
            }
        }
        return apply_filters( 'donate_payment_gateways_enable', $payment_gateways_available );
    }

    /**
     * instance object
     * @return type object
     */
    public static function instance() {
        if ( !self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}
