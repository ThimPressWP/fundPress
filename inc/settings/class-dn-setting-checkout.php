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
        $this->_title = __( 'Checkout', 'fundpress' );
        parent::__construct();
    }

    // render fields
    public function load_field() {
        return
                array(
                    'checkout_general' => array(
                        'title' => __( 'General', 'fundpress' ),
                        'fields' => array(
                            'title' => __( 'General settings', 'fundpress' ),
                            'desc' => __( 'The following options affect how format are displayed list donate causes on the frontend.', 'fundpress' ),
                            'fields' => array(
                                array(
                                    'type' => 'select',
                                    'label' => __( 'Environment', 'fundpress' ),
                                    'desc' => __( 'This controlls test or production mode', 'fundpress' ),
                                    'atts' => array(
                                        'id' => 'environment',
                                        'class' => 'environment'
                                    ),
                                    'name' => 'environment',
                                    'options' => array(
                                        'test' => __( 'Test', 'fundpress' ),
                                        'production' => __( 'Production', 'fundpress' )
                                    ),
                                    'default' => 'test'
                                ),
                                array(
                                    'type' => 'input',
                                    'label' => __( 'Cancel Pending Order', 'fundpress' ),
                                    'desc' => __( 'This controlls how many time cancel Pending Order status.', 'fundpress' ),
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
                                    'label' => __( 'Include lightbox', 'fundpress' ),
                                    'desc' => __( 'This controlls include payment lightbox donate form and not using Cart or Checkout page', 'fundpress' ),
                                    'atts' => array(
                                        'id' => 'lightbox_checkout',
                                        'class' => 'lightbox_checkout'
                                    ),
                                    'name' => 'lightbox_checkout',
                                    'options' => array(
                                        'no' => __( 'No', 'fundpress' ),
                                        'yes' => __( 'Yes', 'fundpress' )
                                    ),
                                    'default' => 'no'
                                ),
                                array(
                                    'type' => 'select',
                                    'label' => __( 'Donate redirect.', 'fundpress' ),
                                    'desc' => __( 'This controlls redirect page on donate submit?', 'fundpress' ),
                                    'atts' => array(
                                        'id' => 'donate_redirect',
                                        'class' => 'donate_redirect'
                                    ),
                                    'name' => 'donate_redirect',
                                    'options' => array(
                                        'cart' => __( 'Cart', 'fundpress' ),
                                        'checkout' => __( 'Checkout', 'fundpress' )
                                    ),
                                    'default' => 'checkout'
                                ),
                                array(
                                    'type' => 'select',
                                    'label' => __( 'Cart page', 'fundpress' ),
                                    'desc' => __( 'This controlls set Cart page', 'fundpress' ),
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
                                    'label' => __( 'Checkout page', 'fundpress' ),
                                    'desc' => __( 'This controlls set Checkout page', 'fundpress' ),
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
                                    'label' => __( 'Terms and Conditions page', 'fundpress' ),
                                    'desc' => __( 'This controlls set Terms and Conditions page', 'fundpress' ),
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
                            'title' => __( 'Checkout page setting', 'fundpress' ),
                            'desc' => __( 'The following options affect how format are displayed list donate causes on the checkout page.', 'fundpress' ),
                            'fields' => array(
                                array(
                                    'type' => 'select',
                                    'label' => __( 'Show terms & Conditions', 'fundpress' ),
                                    'desc' => __( 'This controlls display term & condition in checkout page', 'fundpress' ),
                                    'atts' => array(
                                        'id' => 'term_condition_enable',
                                        'class' => 'term_condition_enable'
                                    ),
                                    'name' => 'term_condition_enable',
                                    'options' => array(
                                        'yes' => __( 'Yes', 'fundpress' ),
                                        'no' => __( 'No', 'fundpress' )
                                    ),
                                    'default' => 'yes'
                                ),
                            // array(
                            // 		'type'		=> 'select',
                            // 		'label'		=> __( 'Name on donors list?', 'fundpress' ),
                            // 		'desc'		=> __( 'This controlls hide name on donors box', 'fundpress' ),
                            // 		'atts'		=> array(
                            // 				'id'	=> 'term_condition',
                            // 				'class'	=> 'term_condition'
                            // 			),
                            // 		'name'		=> 'term_condition',
                            // 		'options'	=> array(
                            // 				'yes'			=> __( 'Yes', 'fundpress' ),
                            // 				'no'			=> __( 'No', 'fundpress' )
                            // 			)
                            // 	)
                            )
                        )
                    )
        );
    }

}

$GLOBALS['checkout_settings'] = new DN_Setting_Checkout();
