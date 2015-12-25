<?php

class DN_Cart
{

	/**
	 * current cart items
	 * @var null
	 */
	public $cart_contents = null;

	/**
	 * instance insteadof new class();
	 * @var null
	 */
	static $_instance = null;

	function __construct()
	{

	}

	/**
	 * get list cart item
	 * @return array
	 */
	function get_cart()
	{
		return $this->cart_contents = apply_filters( 'donate_load_cart_from_cookie', DN_Cookies::instance( 'thimpress_donate_cart' )->_cookie );
	}

	/**
	 * add to cart
	 * @param integer  $post_id
	 * @param array   $param
	 * @param integer $qty
	 */
	function add_to_cart( $post_id, $params = array(), $qty = 1 )
	{

	}

	/**
	 * get cart item
	 */
	function get_cart_item( $item_key = null )
	{

	}

	/**
	 * generate cart item key
	 * @return string
	 */
	function generate_cart_id( $params = array() )
	{

		$html = array();
		foreach ( $params as $key => $value ) {
			if( is_array( $value ) )
			{
				$html[] = $key . donate_array_to_string( $value );
			}
			else
			{
				$html[] = $key . $value;
			}
		}

		return md5( implode( '', $html ) );
	}

	//instance
	static function instance()
	{
		if( ! empty( self::$_instance ) )
			return self::$_instance;

		return self::$_instance = new self();
	}

}
