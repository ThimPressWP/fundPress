<?php

class DN_Checkout
{

	function __construct()
	{

	}

	/**
	 * create dornor
	 * @return integer $dornor_id
	 */
	function create_dornor()
	{

	}

	/**
	 * create donate
	 * @return integer $donate_id
	 */
	function create_donate()
	{

	}

	/**
	 * add to cart
	 */
	function add_to_cart()
	{

	}

	/**
	 * process checkout
	 * @return
	 */
	function process_checkout( $params = null )
	{
		var_dump($params); die();
		// create dornor
		$this->create_dornor( $params['dornor'] );
	}


}
