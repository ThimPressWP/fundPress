<?php

class DN_Setting
{

	/**
	 * $_options
	 * @var null
	 */
	protected $_options = null;

	/**
	 * prefix option name
	 * @var string
	 */
	protected $_prefix = 'thimpress_donate';

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
		$this->_options = $this->options();

		// save, update setting
		add_filter( 'donate_admnin_menus', array( $this, 'setting_page' ), 10, 1 );
		add_action( 'admin_init', array( $this, 'update_settings' ) );
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

	/**
	 * update settings function
	 * @return options || WP_Error
	 */
	public function update_settings()
	{
		if( empty( $_POST ) || ! isset( $_POST[ $this->_prefix ] ) )
			return;

		update_option( $this->_prefix, $_POST[ $this->_prefix ] );

	}

	/**
	 * options load options
	 * @return array || null
	 */
	protected function options()
	{
		return get_option( $this->_prefix, array() );
	}

	/**
	 * get_name_field
	 * @param  $name of field option
	 * @return string name field
	 */
	public function get_field_name( $name = null, $group = null )
	{
		if( ! $this->_prefix || ! $name )
			return;

		if( $group )
			return $this->_prefix[ $group ][ $name ];

		return $this->_prefix[ $name ];

	}

	/**
	 * get_name_field
	 * @param  $name of field option
	 * @return string name field
	 */
	public function get_field_id( $name = null, $group = null )
	{
		if( ! $this->_prefix || ! $name )
			return;

		if( $group )
			return $this->_prefix . '_' . $group . '_' . $name;

		return $this->_prefix . '_' . $name;

	}

	/**
	 * get option value
	 * @param  $name
	 * @return option value. array, string, boolean
	 */
	public function get( $name = null, $group = null, $default = null )
	{

		if( $group && isset( $this->_options[ $group ], $this->_options[ $group ][ $name ] ) )
			return $this->_options[ $group ][ $name ];

		if( $name && isset( $this->_options[ $group ][ $name ] ) )
			return $this->_options[ $group ][ $name ];

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
			return $GLOBALS[ 'dn_settings' ] = self::$_instance;

		return $GLOBALS[ 'dn_settings' ] = new self( $prefix );

	}

}
