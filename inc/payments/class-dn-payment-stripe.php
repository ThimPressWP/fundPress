<?php

/**
 * Class DN_Payment_Stripe
 */
class DN_Payment_Stripe extends DN_Payment_Base{

    /**
     * @var array
     */
    protected $_settings = array();

    public $slug = 'stripe';

    protected $_api_endpoint = '';

    /**
     * protected strip secret
     */
    protected $_stripe_secret = null;

    /**
     * protected strip secret
     */
    protected $_stripe_publish = null;

    protected $_stripe = null;

    function __construct(){

    }

    function init(){

    }
}