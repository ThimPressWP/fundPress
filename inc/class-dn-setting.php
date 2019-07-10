<?php
/**
 * Fundpress Settings class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Settings' ) ) {
	/**
	 * Class DN_Settings.
	 */
	class DN_Settings {

		/**
		 * @var null
		 */
		public $_options = null;

		/**
		 * @var null
		 */
		public $_id = null;

		/**
		 * @var null|string
		 */
		public $_prefix = 'thimpress_donate';

		/**
		 * @var null
		 */
		static $_instance = null;

		public function __construct( $id = null ) {

			$this->_id = $id;

			// load options
			$this->options();

			// save, update setting
			add_filter( 'donate_admin_menus', array( $this, 'register_setting_menu' ), 10, 1 );
			add_action( 'admin_init', array( $this, 'register_setting' ) );
		}

		/**
		 * Magic function to get setting.
		 *
		 * @param null $id
		 *
		 * @return null
		 */
		public function __get( $id = null ) {
			$settings = apply_filters( 'donate_settings_field', array() );
			if ( array_key_exists( $id, $settings ) ) {
				return $settings[ $id ];
			}

			return null;
		}

		/**
		 * Register setting page.
		 *
		 * @param $menus
		 *
		 * @return array
		 */
		public function register_setting_menu( $menus ) {
			$menus[] = array(
				'tp_donate',
				__( 'Fundpress Settings', 'fundpress' ),
				__( 'Settings', 'fundpress' ),
				'manage_options',
				'tp_donate_setting',
				array( $this, 'setting_page' )
			);

			return $menus;
		}

		/**
		 * Admin settings page.
		 */
		public function setting_page() {
			FP()->_include( 'inc/admin/views/settings/settings.php' );
		}

		/**
		 * Register setting.
		 */
		public function register_setting() {
			register_setting( $this->_prefix, $this->_prefix );
		}

		/**
		 * Load options.
		 *
		 * @return mixed|null
		 */
		protected function options() {
			if ( $this->_options ) {
				return $this->_options;
			}

			return $this->_options = get_option( $this->_prefix, null );
		}

		/**
		 * Get field name.
		 *
		 * @param null $name
		 *
		 * @return string
		 */
		public function get_field_name( $name = null ) {
			if ( ! $this->_prefix || ! $name ) {
				return '';
			}

			return $this->_prefix . '[' . $name . ']';
		}

		/**
		 * Get field id.
		 *
		 * @param null $name
		 * @param null $default
		 *
		 * @return string
		 */
		public function get_field_id( $name = null, $default = null ) {
			if ( ! $this->_prefix || ! $name ) {
				return '';
			}

			return $this->_prefix . '_' . $name;
		}

		/**
		 * Get option value.
		 *
		 * @param null $name
		 * @param null $default
		 *
		 * @return null
		 */
		public function get( $name = null, $default = null ) {
			if ( ! $this->_options ) {
				$this->_options = $this->options();
			}

			if ( $name && isset( $this->_options[ $name ] ) ) {
				return $this->_options[ $name ];
			}

			return $default;
		}

		/**
		 * Instance.
		 *
		 * @param null $prefix
		 * @param null $id
		 *
		 * @return DN_Settings
		 */
		static function instance( $prefix = null, $id = null ) {
			if ( ! empty( self::$_instance[ $prefix ] ) ) {
				return self::$_instance[ $prefix ];
			}

			return self::$_instance[ $prefix ] = new self( $id );
		}
	}
}