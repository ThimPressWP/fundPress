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
     * payment title
     * @var null
     */
    public $_title = null;

    function __construct()
    {
        $this->_title = __( 'Stripe', 'tp-donate' );
        parent::__construct();
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

}

new DN_Payment_Stripe();