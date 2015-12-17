<?php

class DN_Setting
{

	/**
	 * $_options
	 * @var null
	 */
	public $_options = null;

	/**
	 * prefix option name
	 * @var string
	 */
	public $_prefix = 'thimpress_donate';

	/**
	 * _instance
	 * @var null
	 */
	static $_instance = null;

	function __construct( $prefix = null )
	{
		if( $prefix )
			$this->_prefix = $prefix;

		// load options
		if( ! $this->_options )
			$this->_options = $this->options();

		// save, update setting
		add_filter( 'donate_admnin_menus', array( $this, 'setting_page' ), 10, 1 );
		add_action( 'admin_init', array( $this, 'register_setting' ) );
	}

	/**
	 * generate setting page
	 * @param  $menus array
	 * @return array $menus
	 */
	public function setting_page( $menus )
	{
		$menus[] = array( 'tp_donate', __( 'TP Donate Settings', 'tp-donate' ), __( 'Settings', 'tp-donate' ), 'manage_options', 'tp_donate_setting', array( $this, 'register_options_page' ) );
		return $menus;
	}

	/**
	 * register option page
	 * @return
	 */
	function register_options_page()
	{
		donate()->_include( 'inc/admin/views/settings.php' );
	}

	function register_setting()
	{
		register_setting( $this->_prefix, $this->_prefix );
	}

	/**
	 * options load options
	 * @return array || null
	 */
	protected function options()
	{
		return get_option( $this->_prefix, null );
	}

	/**
	 * get_name_field
	 * @param  $name of field option
	 * @return string name field
	 */
	public function get_field_name( $name = null )
	{
		if( ! $this->_prefix || ! $name )
			return;

		return $this->_prefix . '[' . $name . ']' ;

	}

	/**
	 * get_name_field
	 * @param  $name of field option
	 * @return string name field
	 */
	public function get_field_id( $name = null, $default = null )
	{
		if( ! $this->_prefix || ! $name )
			return;

		return $this->_prefix . '_' . $name;

	}

	/**
	 * get option value
	 * @param  $name
	 * @return option value. array, string, boolean
	 */
	public function get( $name = null, $default = null )
	{
		if( ! $this->_options )
			$this->_options = $this->options();

		if( $name && isset( $this->_options[ $name ] ) )
			return $this->_options[ $name ];

		return $default;

	}

	/**
	 * instance
	 * @param  $prefix
	 * @return object class
	 */
	static function instance( $prefix = null )
	{

		if( self::$_instance && $prefix === $this->_prefix )
			return self::$_instance;

		return new self( $prefix );

	}

}

$GLOBALS[ 'dn_settings' ] = DN_Setting::instance();