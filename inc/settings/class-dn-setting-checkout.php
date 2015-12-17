<?php

class DN_Admin_Setting_Checkout extends DN_Setting_Page
{
	/**
	 * setting id
	 * @var string
	 */
	public $_id = 'checkout';

	/**
	 * _title
	 * @var null
	 */
	public $_title = null;

	/**
	 * $_position
	 * @var integer
	 */
	public $_position = 30;

	public function __construct()
	{
		$this->_title = __( 'Checkout', 'tp-donate' );
		parent::__construct();
	}

}

new DN_Admin_Setting_Checkout();
