<?php
/**
 * Fundpress Donate action meta box class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_MetaBox_Donate_Action' ) ) {
	/**
	 * Class DN_MetaBox_Donate_Action.
	 */
	class DN_MetaBox_Donate_Action extends DN_MetaBox_Base {

		/**
		 * @var null|string
		 */
		public $_id = null;

		/**
		 * @var null|string|void
		 */
		public $_title = null;

		/**
		 * @var null|string
		 */
		public $_prefix = null;

		/**
		 * @var array
		 */
		public $_screen = array( 'dn_donate' );

		/**
		 * @var array
		 */
		public $_name = array();

		/**
		 * @var string
		 */
		public $_context = 'side';

		/**
		 * DN_MetaBox_Donate_Action constructor.
		 */
		public function __construct() {
			$this->_id     = 'donate_action';
			$this->_title  = __( 'Donate Actions', 'fundpress' );
			$this->_prefix = TP_DONATE_META_DONATE;
			$this->_layout = FUNDPRESS_INC . '/admin/views/metaboxes/donate-action.php';
			parent::__construct();
			add_action( 'donate_process_update_dn_donate_meta', array( $this, 'update_status' ), 10, 1 );
		}

		/**
		 * Update donate status.
		 *
		 * @param $post_id
		 */
		public function update_status( $post_id ) {
			if ( isset( $_POST['thimpress_donate_user_id'] ) ) {                           
				update_post_meta( $post_id, 'thimpress_donate_user_id', absint( $_POST['thimpress_donate_user_id'] ) );
			}
			remove_action( 'donate_process_update_dn_donate_meta', array( $this, 'update_status' ), 10 );
			$status = isset( $_POST['donate_payment_status'] ) ? DN_Helpper::DN_sanitize_params_submitted( $_POST['donate_payment_status'] ) : '';
			$donate = DN_Donate::instance( $post_id );
			$donate->update_status( $status );
			add_action( 'donate_process_update_dn_donate_meta', array( $this, 'update_status' ), 10, 3 );
		}
	}
}
