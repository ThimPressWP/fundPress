<?php

if ( !defined( 'ABSPATH' ) ) {
    exit();
}

class DN_Product_Campaign extends DN_Product_Base {

    // tax
    public $tax = 0;

    // constructor
    function __construct() {
        
    }

    // get amount exclude tax
    public function amount_exclude_tax() {
        return 1;
    }

    // get amount include tax
    public function amount_include_tax() {
        return 1;
    }

}
