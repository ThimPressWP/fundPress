<?php
/**
 * Fundpress Admin menu class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Admin_Menu' ) ) {
	/**
	 * Class DN_Admin_Menu.
	 */
	class DN_Admin_Menu {

		/**
		 * @var array
		 */
		public $_menus = array();

		/**
		 * @var null
		 */
		static $_instance = null;

		/**
		 * DN_Admin_Menu constructor.
		 */
		public function __construct() {
			// admin menu
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}

		/**
		 * Add admin menu callback.
		 */
		public function admin_menu() {
			$donate_menu = apply_filters( 'donation_menu_title', __( 'FundPress', 'fundpress' ) );
			add_menu_page( $donate_menu, $donate_menu, 'manage_options', 'tp_donate', null, 'dashicons-admin-site', 9 );
			/**
			 * menus
			 * @var
			 */
			$menus = apply_filters( 'donate_admin_menus', $this->_menus );
			foreach ( $menus as $menu ) {
				call_user_func_array( 'add_submenu_page', $menu );
			}
		}

		/**
		 * Add menu item.
		 *
		 * @param $params
		 */
		public function add_menu( $params ) {
			$this->_menus[] = $params;
		}

		/**
		 * Instance.
		 *
		 * @return DN_Admin_Menu|null
		 */
		public static function instance() {
			if ( self::$_instance ) {
				return self::$_instance;
			}

			return new self();
		}
	}
}

DN_Admin_Menu::instance();
