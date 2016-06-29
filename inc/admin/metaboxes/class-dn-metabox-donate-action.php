<?php
if ( ! defined( 'ABSPATH' ) ) {
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
	 * screen post, page, tp_event
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
		$this->_id = 'donate_action';
		$this->_title = __( 'Donate Actions', 'tp-donate' );
		$this->_prefix = TP_DONATE_META_DONATE;
		$this->_layout = TP_DONATE_INC . '/admin/metaboxes/views/donate-action.php';
		parent::__construct();

	}
}
