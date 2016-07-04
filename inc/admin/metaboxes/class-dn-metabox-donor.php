<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class DN_MetaBox_Donor extends DN_MetaBox_Base
{
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
	 * screen post, page, tp_donate
	 * @var array
	 */
	public $_screen = array( 'dn_donor' );

	/**
	 * array meta key
	 * @var array
	 */
	public $_name = array();

	public function __construct()
	{
		$this->_id = 'donate_donor_info_section';
		$this->_title = __( 'Donor Information', 'tp-donate' );
		$this->_prefix = TP_DONATE_META_DONOR;
		$this->_layout = TP_DONATE_INC . '/admin/metaboxes/views/donor.php';
		parent::__construct();
	}

}