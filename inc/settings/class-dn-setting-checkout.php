<?php

if ( !defined( 'ABSPATH' ) ) {
    exit();
}

class DN_Setting_Checkout extends DN_Setting_Base {

    /**
     * setting id
     * @var string
     */
    public $_id = 'checkout';

    /**
     * _title
     * @var null
     */
    public $_title = null;

    /**
     * tab in tab setting
     * @var boolean
     */
    public $_tab = true;

    /**
     * $_position
     * @var integer
     */
    public $_position = 30;

    public function __construct() {
        $this->_title = __( 'Checkout', 'tp-donate' );
        parent::__construct();
    }

    // render fields
    public function load_field() {
        return
                array(
                    'checkout_general' => array(
                        'title' => __( 'General', 'tp-donate' ),
                        'fields' => array(
                            'title' => __( 'General settings', 'tp-donate' ),
                            'desc' => __( 'The following options affect how format are displayed list donate causes on the frontend.', 'tp-donate' ),
                            'fields' => array(
                                array(
                                    'type' => 'select',
                                    'label' => __( 'Environment', 'tp-donate' ),
                                    'desc' => __( 'This controlls test or production mode', 'tp-donate' ),
                                    'atts' => array(
                                        'id' => 'environment',
                                        'class' => 'environment'
                                    ),
                                    'name' => 'environment',
                                    'options' => array(
                                        'test' => __( 'Test', 'tp-donate' ),
                                        'production' => __( 'Production', 'tp-donate' )
                                    ),
                                    'default' => 'test'
                                ),
                                array(
                                    'type' => 'input',
                                    'label' => __( 'Cancel Pending Order', 'tp-donate' ),
                                    'desc' => __( 'This controlls how many time cancel Pending Order status.', 'tp-donate' ),
                                    'atts' => array(
                                        'id' => 'cancel_order',
                                        'class' => 'cancel_order',
                                        'min' => 1,
                                        'type' => 'number'
                                    ),
                                    'name' => 'cancel_order',
                                    'default' => 12
                                ),
                                array(
                                    'type' => 'select',
                                    'label' => __( 'Include lightbox', 'tp-donate' ),
                                    'desc' => __( 'This controlls include payment lightbox donate form and not using Cart or Checkout page', 'tp-donate' ),
                                    'atts' => array(
                                        'id' => 'lightbox_checkout',
                                        'class' => 'lightbox_checkout'
                                    ),
                                    'name' => 'lightbox_checkout',
                                    'options' => array(
                                        'no' => __( 'No', 'tp-donate' ),
                                        'yes' => __( 'Yes', 'tp-donate' )
                                    ),
                                    'default' => 'no'
                                ),
                                array(
                                    'type' => 'select',
                                    'label' => __( 'Donate redirect.', 'tp-donate' ),
                                    'desc' => __( 'This controlls redirect page on donate submit?', 'tp-donate' ),
                                    'atts' => array(
                                        'id' => 'donate_redirect',
                                        'class' => 'donate_redirect'
                                    ),
                                    'name' => 'donate_redirect',
                                    'options' => array(
                                        'cart' => __( 'Cart', 'tp-donate' ),
                                        'checkout' => __( 'Checkout', 'tp-donate' )
                                    ),
                                    'default' => 'checkout'
                                ),
                                array(
                                    'type' => 'select',
                                    'label' => __( 'Cart page', 'tp-donate' ),
                                    'desc' => __( 'This controlls set Cart page', 'tp-donate' ),
                                    'atts' => array(
                                        'id' => 'cart_page',
                                        'class' => 'cart_page'
                                    ),
                                    'name' => 'cart_page',
                                    'options' => donate_get_pages_setting(),
                                    'default' => ''
                                ),
                                array(
                                    'type' => 'select',
                                    'label' => __( 'Checkout page', 'tp-donate' ),
                                    'desc' => __( 'This controlls set Checkout page', 'tp-donate' ),
                                    'atts' => array(
                                        'id' => 'checkout_page',
                                        'class' => 'checkout_page'
                                    ),
                                    'name' => 'checkout_page',
                                    'options' => donate_get_pages_setting(),
                                    'default' => ''
                                ),
                                array(
                                    'type' => 'select',
                                    'label' => __( 'Terms and Conditions page', 'tp-donate' ),
                                    'desc' => __( 'This controlls set Terms and Conditions page', 'tp-donate' ),
                                    'atts' => array(
                                        'id' => 'term_condition_page',
                                        'class' => 'term_condition_page'
                                    ),
                                    'name' => 'term_condition_page',
                                    'options' => donate_get_pages_setting(),
                                    'default' => ''
                                )
                            )
                        ),
                        array(
                            'title' => __( 'Checkout page setting', 'tp-donate' ),
                            'desc' => __( 'The following options affect how format are displayed list donate causes on the checkout page.', 'tp-donate' ),
                            'fields' => array(
                                array(
                                    'type' => 'select',
                                    'label' => __( 'Show terms & Conditions', 'tp-donate' ),
                                    'desc' => __( 'This controlls display term & condition in checkout page', 'tp-donate' ),
                                    'atts' => array(
                                        'id' => 'term_condition_enable',
                                        'class' => 'term_condition_enable'
                                    ),
                                    'name' => 'term_condition_enable',
                                    'options' => array(
                                        'yes' => __( 'Yes', 'tp-donate' ),
                                        'no' => __( 'No', 'tp-donate' )
                                    ),
                                    'default' => 'yes'
                                ),
                            // array(
                            // 		'type'		=> 'select',
                            // 		'label'		=> __( 'Name on donors list?', 'tp-donate' ),
                            // 		'desc'		=> __( 'This controlls hide name on donors box', 'tp-donate' ),
                            // 		'atts'		=> array(
                            // 				'id'	=> 'term_condition',
                            // 				'class'	=> 'term_condition'
                            // 			),
                            // 		'name'		=> 'term_condition',
                            // 		'options'	=> array(
                            // 				'yes'			=> __( 'Yes', 'tp-donate' ),
                            // 				'no'			=> __( 'No', 'tp-donate' )
                            // 			)
                            // 	)
                            )
                        )
                    )
        );
    }

}

$GLOBALS['checkout_settings'] = new DN_Setting_Checkout();
