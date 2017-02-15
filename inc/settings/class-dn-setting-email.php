<?php

if ( !defined( 'ABSPATH' ) ) {
    exit();
}

class DN_Setting_Email extends DN_Setting_Base {

    /**
     * setting id
     * @var string
     */
    public $_id = 'email';

    /**
     * _title
     * @var null
     */
    public $_title = null;

    /**
     * $_position
     * @var integer
     */
    public $_position = 20;

    public function __construct() {
        $this->_title = __( 'Email', 'fundpress' );
        parent::__construct();
    }

    // render fields
    public function load_field() {
        return
                array(
                    array(
                        'title' => __( 'Email Settings', 'fundpress' ),
                        'desc' => __( 'The following options affect how prices are displayed on the frontend.', 'fundpress' ),
                        'fields' => array(
                            array(
                                'type' => 'select',
                                'label' => __( 'Enable', 'fundpress' ),
                                'desc' => __( 'This controlls what the currency prices', 'fundpress' ),
                                'atts' => array(
                                    'id' => 'enable',
                                    'class' => 'enable'
                                ),
                                'name' => 'enable',
                                'options' => array(
                                    'yes' => __( 'Yes', 'fundpress' ),
                                    'no' => __( 'No', 'fundpress' )
                                ),
                                'default' => 'yes'
                            ),
                            array(
                                'type' => 'input',
                                'label' => __( 'From name', 'fundpress' ),
                                'desc' => __( 'This set email from name', 'fundpress' ),
                                'atts' => array(
                                    'id' => 'from_name',
                                    'class' => 'from_name',
                                    'placeholder' => get_option( 'blogname' ),
                                    'type' => 'text'
                                ),
                                'name' => 'from_name',
                                'default' => get_option( 'blogname' )
                            ),
                            array(
                                'type' => 'input',
                                'label' => __( 'Email from', 'fundpress' ),
                                'desc' => __( 'This set email send', 'fundpress' ),
                                'atts' => array(
                                    'id' => 'admin_email',
                                    'class' => 'admin_email',
                                    'placeholder' => get_option( 'admin_email' ),
                                    'type' => 'text'
                                ),
                                'name' => 'admin_email',
                                'default' => get_option( 'admin_email' )
                            ),
                            array(
                                'type' => 'editor',
                                'label' => __( 'Email Content', 'fundpress' ),
                                'desc' => __( 'Use [donor_email], [donor_first_name], [donor_last_name], [donor_phone], [donor_address] tags to generate email template', 'fundpress' ),
                                'atts' => array(
                                    'id' => 'email_template',
                                    'class' => 'email_template',
                                    'placeholder' => get_option( 'admin_email' ),
                                    'type' => 'text',
                                    'cols' => 50,
                                    'rows' => 20
                                ),
                                'name' => 'email_template',
                                'default' => ''
                            )
                        )
                    )
        );
    }

}

$GLOBALS['email_settings'] = new DN_Setting_Email();
