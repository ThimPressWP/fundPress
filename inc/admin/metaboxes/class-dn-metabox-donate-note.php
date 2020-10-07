<?php
/**
 * Fundpress Donate note meta box class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_MetaBox_Donate_Note' ) ) {
	/**
	 * Class DN_MetaBox_Donate_Note.
	 */
	class DN_MetaBox_Donate_Note extends DN_MetaBox_Base {

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
		 * DN_MetaBox_Donate_Note constructor.
		 */
		public function __construct() {
			$this->_id     = 'donate_note';
			$this->_title  = __( 'Donate Note', 'fundpress' );
			$this->_prefix = TP_DONATE_META_DONATE;
			$this->_layout = FUNDPRESS_INC . '/admin/views/metaboxes/donate-note.php';
			parent::__construct();
			add_action( 'donate_process_update_dn_donate_meta', array( $this, 'update_donate_note' ) );
		}

		/**
		 * Update note.
		 *
		 * @param $post_id
		 */
		public function update_donate_note( $post_id ) {
			if ( ! isset( $_POST['thimpress_donate_addition'] ) ) {
				return;
			}
			update_post_meta( $post_id, 'thimpress_donate_addition', DN_Helpper::DN_sanitize_params_submitted( $_POST['thimpress_donate_addition'] ) );
		}
	}
}
