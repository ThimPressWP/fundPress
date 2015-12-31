<?php

class DN_Shortcode_Donate extends DN_Shortcode_Base
{
	/**
	 * template file
	 * @var null
	 */
	public $_template = null;

	/**
	 * shortcode name
	 * @var null
	 */
	public $_shortcodeName = null;

	public function __construct()
	{
		$this->_shortcodeName = 'tp_donate';
		$this->_template = 'donate.php';
		parent::__construct();
	}

}

new DN_Shortcode_Donate();
