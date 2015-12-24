<?php

/**
 * Class DN_Payment_Palpal
 */
class DN_Payment_Palpal extends DN_Payment_Base{
    /**
     * @var null
     */
    protected $paypal_live_url              = null;

    /**
     * @var null
     */
    protected $paypal_sandbox_url           = null;

    /**
     * @var null
     */
    protected $paypal_payment_live_url      = null;

    /**
     * @var null
     */
    protected $paypal_payment_sandbox_url   = null;

    /**
     * @var null
     */
    protected $paypal_nvp_api_live_url      = null;

    /**
     * @var null
     */
    protected $paypal_vnp_api_sandbox_url   = null;

    /**
     * @var array
     */
    protected $_settings = array();

    /**
     * Construction
     */
    function __construct(){

    }

    /**
     * Init hooks
     */
    function init(){

    }

}