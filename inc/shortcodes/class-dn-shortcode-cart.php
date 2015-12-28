<?php

class DN_Shortcode_Cart extends DN_Shortcode_Base
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
		$this->_shortcodeName = 'donate_cart';
		$this->_template = 'cart.php';
		parent::__construct();
	}

}

new DN_Shortcode_Cart();
