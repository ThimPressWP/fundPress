<?php

class DN_Meta_Box_Event extends DN_Meta_Box
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
		$this->_title = __( 'Event Settings', 'tp-donate' );
		$this->_layout = TP_DONATE_INC . '/metabox/views/donate-countdown.php';
		parent::__construct();
	}

}

new DN_Meta_Box_Event();