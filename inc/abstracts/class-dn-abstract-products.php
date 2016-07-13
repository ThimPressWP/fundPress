<?php

if ( !defined( 'ABSPATH' ) )
    exit();

abstract class DN_Product_Base {

    // tax
    protected $tax = 0;

    // constructor
    function __construct() {
        
    }

    // get amount exclude tax
    protected function amount_exclude_tax() {
        
    }

    // get amount include tax
    protected function amount_include_tax() {
        
    }

}
