<?php

class DN_Cart
{

	/**
	 * current cart items
	 * @var null
	 */
	public $cart_contents = null;

	/** @var null **/
	public $sessions = null;

	public $cart_include_totals = 0;
	public $cart_exclude_totals = 0;
	public $cart_items_count = 0;

	/**
	 * instance insteadof new class();
	 * @var null
	 */
	static $_instance = null;

	function __construct()
	{
		// load cart items
		$this->cart_contents = $this->get_cart();
		$this->sessions = DN_Sessions::instance( 'thimpress_donate_cart', true );
	}

	/**
	 * get list cart item
	 * @return array
	 */
	function get_cart()
	{
		$cart_items = array();

		if( $this->sessions && $this->sessions->session )
		{
			foreach ( $this->sessions->session as $cart_item_id => $cart_param ) {
				$param = new stdClass();

				foreach ( $cart_param as $key => $value ) {
					$param->{ $key } = $value;
				}

				if( $param->product_id )
				{
					$param->product_data = get_post( $param->product_id );

					$post_type = $param->product_data->post_type;
					$product_class = 'DN_Product_' . ucfirst( str_replace( 'dn_', '', $post_type) );
					if( ! class_exists( $product_class ) )
					 	$product_class = 'DN_Product_Base';

					$param->product_class = apply_filters( 'donate_product_type_class', $product_class, $post_type );
					$product = new $param->product_class;
					$param->amount_include_tax = $product->amount_include_tax();
					$param->amount_exclude_tax = $product->amount_exclude_tax();
					$param->tax = $param->amount_include_tax - $param->amount_exclude_tax;
				}

				$cart_items[ $cart_item_id ] = $param;
			}
		}

		return apply_filters( 'donate_load_cart_from_session', $cart_items );
	}

	/**
	 * add to cart
	 * @param integer  $post_id
	 * @param array   $param
	 * @param integer $qty
	 */
	function add_to_cart( $post_id, $params = array(), $qty = 1, $asc = false )
	{

		$cart_item_id = $this->generate_cart_id( $params );
		if( in_array( $cart_item_id, $this->cart_contents ) )
		{
			if( $asc === false )
			{
				$this->remove_cart_item( $cart_item_id );
			}
			else
			{
				$params[ 'quantity' ] = $this->cart_contents[ 'quantity' ] + $qty;
			}
		}

		// allow hook before set sessions
		do_action( 'donate_before_add_to_cart_item' );

		// set cart session
		$this->sessions->set( $cart_item_id, $params );

		// allow hook after set sessions
		do_action( 'donate_after_add_to_cart_item' );

		// refresh cart data
		$this->refresh();
		echo '<pre>'; print_r( $this->cart_contents ); die();
	}

	// refresh all
	function refresh()
	{
		// refresh cart_contents
		$this->cart_contents = $this->get_cart();

		// refresh cart_totals
		$this->cart_include_totals = $this->cart_include_totals();

		// refresh cart_totals_exclude_tax
		$this->cart_totals_exclude_tax = $this->cart_total_exclude_tax();

		// refresh cart_items_count
		$this->cart_items_count = count( $this->cart_contents );
	}

	// cart totals
	function cart_include_totals()
	{
		$total = 0;
		foreach ( $this->cart_contents as $cart_item_key => $cart_item ) {
			$total = $total + $cart_item->amount_include_tax;
		}
		return apply_filters( 'donate_cart_include_totals', $total );
	}

	// cart exclude tax
	function cart_total_exclude_tax()
	{
		$total = 0;
		foreach ( $this->cart_contents as $cart_item_key => $cart_item ) {
			$total = $total + $cart_item->amount_exclude_tax;
		}

		return apply_filters( 'donate_cart_exclude_totals', $total );
	}

	// cart tax
	function cart_taxs()
	{
		$total = 0;
		foreach ( $this->cart_contents as $cart_item_key => $cart_item ) {
			$total = $total + $cart_item->tax;
		}
		return apply_filters( 'donate_cart_tax_total', $total );
	}

	/**
	 * get cart item
	 */
	function get_cart_item( $item_key = null )
	{
		if( $item_key && isset( $this->cart_contents[ $item_key ] ) )
			return $this->cart_contents[ $item_key ];
	}

	/**
	 * get cart item
	 */
	function remove_cart_item( $item_key = null )
	{
		do_action( 'donate_remove_cart_item', $item_key );
		return $this->sessions->set( $item_key, null );
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

		return apply_filters( 'donat_generate_cart_item_id', md5( implode( '', $html ) ) );
	}

	//instance
	static function instance()
	{
		if( ! empty( self::$_instance ) )
			return self::$_instance;

		return self::$_instance = new self();
	}

}
