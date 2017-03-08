<?php

if ( !defined( 'ABSPATH' ) ) {
    exit();
}

class DN_MetaBox_Donate_Action extends DN_MetaBox_Base {

    /**
     * id of the meta box
     * @var null
     */
    public $_id = null;

    /**
     * title of meta box
     * @var null
     */
    public $_title = null;

    /**
     * meta key prefix
     * @var string
     */
    public $_prefix = null;

    /**
     * screen post, page
     * @var array
     */
    public $_screen = array( 'dn_donate' );

    /**
     * array meta key
     * @var array
     */
    public $_name = array();
    public $_context = 'side';

    public function __construct() {
        $this->_id = 'donate_action';
        $this->_title = __( 'Donate Actions', 'fundpress' );
        $this->_prefix = TP_DONATE_META_DONATE;
        $this->_layout = TP_DONATE_INC . '/admin/views/metaboxes/donate-action.php';
        parent::__construct();
        add_action( 'donate_process_update_dn_donate_meta', array( $this, 'update_status' ), 10, 1 );
    }

    /**
     * update function
     * @param  $post_id
     * @param  $post
     * @param  $update
     * @return null
     */
    public function update_status( $post_id ) {
        if ( isset( $_POST['thimpress_donate_user_id'] ) ) {
            update_post_meta( $post_id, 'thimpress_donate_user_id', esc_attr(absint( $_POST['thimpress_donate_user_id'] ) ));
        }
        remove_action( 'donate_process_update_dn_donate_meta', array( $this, 'update_status' ), 10, 3 );
        $status = isset( $_POST['donate_payment_status'] ) ? sanitize_text_field( $_POST['donate_payment_status'] ) : '';
        $donate = DN_Donate::instance( $post_id );
        $donate->update_status( $status );
        add_action( 'donate_process_update_dn_donate_meta', array( $this, 'update_status' ), 10, 3 );
    }

}
