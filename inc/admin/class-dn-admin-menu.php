<?php

class DN_Admin_Menu
{

	/**
	 * menus
	 * @var array
	 */
	public $_menus = array();

	/**
	 * instead new class
	 * @var null
	 */
	static $_instance = null;

	public function __construct()
	{
		// admin menu
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	// add admin menu callback
	function admin_menu()
	{
		add_menu_page( __( 'TP Donate', 'tp-donate' ), __( 'TP Donate', 'tp-donate' ), 'manage_options', 'tp_donate', null, 'dashicons-admin-site', 9 );
		/**
		 * menus
		 * @var
		 */
		$menus = apply_filters( 'donate_admnin_menus', $this->_menus );
		foreach( $menus as $menu )
		{
			call_user_func_array( 'add_submenu_page', $menu);
		}
	}

	/**
	 * add menu item
	 * @param $params
	 */
	function add_menu( $params )
	{
		$this->_menus[] = $params;
	}

	/**
	 * instance
	 * @return object class
	 */
	static function instance()
	{
		if( self::$_instance )
			return self::$_instance;

		return new self();
	}


}

DN_Admin_Menu::instance();