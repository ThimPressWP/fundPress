<?php

class DN_Cookies
{

	// instance
	static $_instance = null;

	// $_cookie
	public $_cookie = null;

	/**
	 * prefix
	 * @var null
	 */
	protected $prefix = null;

	function __construct( $prefix = '' )
	{
		if( ! $prefix )
			return;

		$this->prefix = $prefix;

		// get all
		if( isset( $_COOKIE[ $this->prefix ] ) )
		{
			$this->_cookie = $this->load();
		}

	}

	/**
	 * load all with prefix
	 * @return
	 */
	function load()
	{
		if( isset( $_COOKIE[ $this->prefix ] ) ){
			return json_decode( $_COOKIE[ $this->prefix ] );
		}
	}

	/**
	 * set key
	 * @param $key
	 * @param $value
	 */
	function set( $key = '', $value = null )
	{

	}

	/**
	 * get value
	 * @param  $key
	 * @return anythings
	 */
	function get( $key = null, $default = null )
	{
		if( ! $key )
			return $default;
	}

	static function instance( $prefix = '' )
	{
		if( ! empty( self::$_instance[ $prefix ] ) )
			return self::$_instance[ $prefix ];

		return self::$_instance[ $prefix ] = new self( $prefix );
	}

}