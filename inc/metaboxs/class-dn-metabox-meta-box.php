<?php

class DN_Meta_Box_Donate_Settings extends DN_Meta_Box
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
	 * array meta key
	 * @var array
	 */
	public $_name = array();

	public function __construct()
	{
		$this->_id = 'donate_setting_section';
		$this->_title = __( 'Donate Settings', 'tp-donate' );
		$this->_layout = TP_DONATE_INC . '/metaboxs/views/donate-settings.php';
		parent::__construct();
	}

}

new DN_Meta_Box_Donate_Settings();