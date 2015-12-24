<?php

/**
 * Class DN_Payment_Palpal
 */
class DN_Payment_Palpal extends DN_Payment_Base{

    /**
     * id of payment
     * @var null
     */
    public $_id = 'paypal';

    /**
     * payment title
     * @var null
     */
    public $_title = null;

    function __construct()
    {
        $this->_title = __( 'Paypal', 'tp-donate' );
        parent::__construct();
    }

    /**
     * fields settings
     * @return array
     */
    public function fields()
    {
        return  array(
                    'title'     => $this->_title, // tab title
                    'fields'    => array(
                            'fields'        => array(
                                array(
                                        'type'      => 'select',
                                        'label'     => __( 'Enable', 'tp-donate' ),
                                        'desc'      => __( 'This controlls enable payment method', 'tp-donate' ),
                                        'atts'      => array(
                                                'id'    => 'paypal_enable',
                                                'class' => 'paypal_enable'
                                            ),
                                        'name'      => 'paypal_enable',
                                        'options'   => array(
                                                'no'                => __( 'No', 'tp-donate' ),
                                                'yes'               => __( 'Yes', 'tp-donate' )
                                            )
                                    ),
                                array(
                                        'type'      => 'input',
                                        'label'     => __( 'Paypal email', 'tp-donate' ),
                                        'atts'      => array(
                                                'id'    => 'paypal_email',
                                                'class' => 'paypal_email',
                                                'type'  => 'text'
                                            ),
                                        'name'      => 'paypal_email'
                                    ),
                                array(
                                        'type'      => 'input',
                                        'label'     => __( 'Paypal sandbox email', 'tp-donate' ),
                                        'atts'      => array(
                                                'id'    => 'paypal_sanbox_email',
                                                'class' => 'paypal_sanbox_email',
                                                'type'  => 'text'
                                            ),
                                        'name'      => 'paypal_sanbox_email'
                                    )
                            ),

                )
            );
    }

}

new DN_Payment_Palpal();