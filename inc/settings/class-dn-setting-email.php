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
        $this->_title = __( 'Email', 'tp-donate' );
        parent::__construct();
    }

    // render fields
    public function load_field() {
        return
                array(
                    array(
                        'title' => __( 'Email Settings', 'tp-donate' ),
                        'desc' => __( 'The following options affect how prices are displayed on the frontend.', 'tp-donate' ),
                        'fields' => array(
                            array(
                                'type' => 'select',
                                'label' => __( 'Enable', 'tp-donate' ),
                                'desc' => __( 'This controlls what the currency prices', 'tp-donate' ),
                                'atts' => array(
                                    'id' => 'enable',
                                    'class' => 'enable'
                                ),
                                'name' => 'enable',
                                'options' => array(
                                    'yes' => __( 'Yes', 'tp-donate' ),
                                    'no' => __( 'No', 'tp-donate' )
                                ),
                                'default' => 'yes'
                            ),
                            array(
                                'type' => 'input',
                                'label' => __( 'From name', 'tp-donate' ),
                                'desc' => __( 'This set email from name', 'tp-donate' ),
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
                                'label' => __( 'Email from', 'tp-donate' ),
                                'desc' => __( 'This set email send', 'tp-donate' ),
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
                                'label' => __( 'Email Content', 'tp-donate' ),
                                'desc' => __( 'Use [donor_email], [donor_first_name], [donor_last_name], [donor_phone], [donor_address] tags to generate email template', 'tp-donate' ),
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
