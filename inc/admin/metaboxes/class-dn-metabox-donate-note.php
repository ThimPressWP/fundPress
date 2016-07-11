<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class DN_MetaBox_Donate_Note extends DN_MetaBox_Base {

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

	public function __construct()
	{
		$this->_id = 'donate_note';
		$this->_title = __( 'Donate Note', 'tp-donate' );
		$this->_prefix = TP_DONATE_META_DONATE;
		$this->_layout = TP_DONATE_INC . '/admin/metaboxes/views/donate-note.php';
		parent::__construct();
	}

}
