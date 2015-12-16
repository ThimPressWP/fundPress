<?php

class DN_Admin_Setting_Email extends DN_Setting_Page
{
	/**
	 * setting id
	 * @var string
	 */
	public $_id = 'email';

	/**
	 * _title
	 * @var null
	 */
	public $_title = null;

	/**
	 * $_position
	 * @var integer
	 */
	public $_position = 20;

	public function __construct()
	{
		$this->_title = __( 'Email', 'tp-donate' );
		parent::__construct();
	}

}

new DN_Admin_Setting_Email();