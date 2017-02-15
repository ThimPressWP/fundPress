<?php

if ( !defined( 'ABSPATH' ) )
    exit();

abstract class DN_Payment_Base {

    /**
     * id of payment
     * @var null
     */
//    protected $_id = null;

    /**
     * payment title
     * @var null
     */
    protected $_title = null;
    // is enable
    public $is_enable = true;

    /**
     * icon url
     * @var null
     */
    public $icon = null;

    function __construct() {
        add_action( 'init', array( $this, 'init' ) );
//        $this->icon = TP_DONATE_INC_URI . '/gateways/' . $this->id . '/' . $this->id . '.png';
        $this->is_enable();
    }

    public function init() {

        if ( is_admin() ) {
            /**
             * generate fields settings
             */
            add_filter( 'donate_admin_setting_fields', array( $this, 'generate_fields' ), 10, 2 );
        }
    }
    
    public function get_title() {
        return $this->_title;
    }

    /**
     * payment process
     * @return null
     */
    protected function process( $donate = false, $posted = null ) {
        
    }

    /**
     * refun action
     * @return null
     */
    protected function refun() {
        
    }

    /**
     * payment send email
     * @return null
     */
    public function send_email() {
        
    }

    /**
     * fields setting
     * @param  [type] $groups [description]
     * @param  [type] $id     [description]
     * @return [type]         [description]
     */
    public function generate_fields( $groups, $id ) {
        if ( $id === 'checkout' && $this->id ) {

            $groups[$id . '_' . $this->id] = apply_filters( 'donate_admin_setting_fields_checkout', $this->fields(), $this->id );
        }

        return $groups;
    }

    /**
     * admin setting fields
     * @return array
     */
    public function fields() {
        return array();
    }

    /**
     * enable
     * @return boolean
     */
    public function is_enable() {
        if ( DN_Settings::instance()->checkout->get( $this->id . '_enable', 'yes' ) === 'yes' ) {
            return $this->is_enable = true;
        }
        return $this->is_enable = false;
    }
    
    /**
     * 
     * @return type html or null
     */
    public function checkout_form() {
        return null;
    }

    /**
     * add notice message completed when payment completed
     * @return null
     */
    public function completed_process_message() {
        if ( !donate_has_notice( 'success' ) ) {
            donate_add_notice( 'success', __( 'Payment completed. We will send you email when payment method verify.', 'fundpress' ) );
        }
    }

}
