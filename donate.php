<?php
/*
	Plugin Name: ThimPress Donate
	Plugin URI: http://thimpress.com/tp-donate
	Description: Donate
	Author: ThimPress
	Version: 1.0
	Author URI: http://thimpress.com
*/

if( ! defined( 'ABSPATH' ) ) exit();

if( defined( 'TP_DONATE_PATH' ) ) return;

define( 'TP_DONATE_PATH', plugin_dir_path( __FILE__ ) );
define( 'TP_DONATE_URI', plugins_url( '', __FILE__ ) );
define( 'TP_DONATE_INC', TP_DONATE_PATH . 'inc' );
define( 'TP_DONATE_INC_URI', TP_DONATE_URI . '/inc' );
define( 'TP_DONATE_ASSETS_URI', TP_DONATE_URI . '/assets' );
define( 'TP_DONATE_LIB_URI', TP_DONATE_INC_URI . '/libraries' );
define( 'TP_DONATE_VER', 1.0 );

/**
 * Donate class
 */
class ThimPress_Donate
{

	/**
	 * file include
	 * @var array
	 */
	protected $_files = array();

	/**
	 * assets enqueue
	 * @var array
	 */
	protected $_assets = array(
			'admin'	=> array( 'css' => array(), 'js' => array() ),
			'site'	=> array( 'css' => array(), 'js' => array() )
		);

	/**
	 * options
	 * @var options
	 */
	public $options = null;

	function __construct()
	{
		$this->includes();

		$this->options = DN_Setting::instance();

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueues' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueues' ) );
		// active plugin
		register_activation_hook( __FILE__, array( $this, 'install' ) );
		register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );
	}

	/**
	 * install plugin options, define v.v.
	 * @return null
	 */
	public function install()
	{
		$this->_include( 'install.php' );
	}

	/**
	 * uninstall plugin
	 * @return null
	 */
	public function uninstall()
	{
		$this->_include( 'uninstall.php' );
	}

	/**
	 * autoload function
	 * @return null
	 */
	public function autoload()
	{

		$path = TP_DONATE_PATH . 'assets/autoload';
		$local = array( 'admin', 'site' );

		// assets file
		foreach ($local as $key => $lo) {
			$csss = $path . '/' . $lo . '/css';
			if( file_exists( $csss ) )
			{
				foreach ( (array)glob( $csss . '/*.css' ) as $key => $f ) {
					$this->_assets[ $lo ]['css'][] = TP_DONATE_ASSETS_URI . '/autoload/' . $lo . '/css/' . basename( $f );
				}
			}

			$jss = $path . '/' . $lo . '/js';
			if( file_exists( $jss ) )
			{
				foreach ( (array)glob( $jss . '/*.js' ) as $key => $f ) {
					$this->_assets[ $lo ]['js'][] = TP_DONATE_ASSETS_URI . '/autoload/' . $lo . '/js/' . basename( $f );
				}
			}
		}
	}

	/**
	 * include file
	 * @param  array or string
	 * @return null
	 */
	public function includes()
	{
		$this->autoload();

		$this->_include( 'inc/functions.php' );
		$this->_include( 'inc/class-dn-setting.php' );

		$paths = array( 'abstracts', 'settings', 'shortcodes', 'widgets', 'metaboxs' );

		foreach ($paths as $key => $path) {
			$real_path = TP_DONATE_INC . '/' . $path;
			$path = substr( $path, 0, -1 );
			foreach ( (array)glob( $real_path . '/class-dn-'. $path .'-*.php' ) as $key => $file) {
				$this->_include( $file );
			}
		}

		if( is_admin() )
		{
			$this->_include( 'inc/admin/functions.php' );

			foreach ( (array)glob( TP_DONATE_INC . '/admin/class-dn-admin-*.php' ) as $key => $file) {
				$this->_include( $file );
			}
		}

		$this->_include( 'inc/class-dn-custom-post-type.php' );
		$this->_include( 'inc/class-dn-template-include.php' );

	}

	public function _include( $file )
	{
		if( ! $file ) return;

		if( is_array( $file ) )
		{
			foreach ($file as $key => $f) {
				if( file_exists( TP_DONATE_PATH . $f ) )
					require_once TP_DONATE_PATH . $f;
			}
		}
		else
		{
			if( file_exists( TP_DONATE_PATH . $file ) )
				require_once TP_DONATE_PATH . $file;
			elseif ( file_exists($file) )
				require_once $file;
		}
	}

	/**
	 * enqueue script, style
	 * @return null
	 */
	public function enqueues()
	{
		wp_enqueue_script( 'jquery' );
		wp_dequeue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-core' );
		if( is_admin() )
		{
			wp_enqueue_script( 'tp-donate-bootstrap-datepicker-js', TP_DONATE_LIB_URI . '/datetimepicker/js/bootstrap-datepicker.js', array(), TP_DONATE_VER, true );
			wp_enqueue_script( 'tp-donate-bootstrap-timepicker-js', TP_DONATE_LIB_URI . '/datetimepicker/js/jquery.timepicker.js', array(), TP_DONATE_VER, true );

			wp_enqueue_style( 'tp-donate-datepicker', TP_DONATE_LIB_URI . '/datetimepicker/css/bootstrap-datetimepicker.css', array(), TP_DONATE_VER );
			wp_enqueue_style( 'tp-donate-timepicker', TP_DONATE_LIB_URI . '/datetimepicker/css/jquery.timepicker.css', array(), TP_DONATE_VER );

			foreach ( $this->_assets[ 'admin' ] as $key => $files ) {
				if( $key === 'css' )
				{
					foreach ($files as $k => $f) {
						wp_enqueue_style( 'tp-donate-'.$key.'-'.$k, $f , array(), TP_DONATE_VER );
					}
				} else if( $key === 'js' ) {
					foreach ($files as $k => $f)
					{
						wp_enqueue_script( 'tp-donate-'.$key.'-'.$k, $f, array(), TP_DONATE_VER, true );
					}
				}
			}
		}
		else
		{
			wp_enqueue_script( 'tp-donate-countdown-plugin-js', TP_DONATE_LIB_URI . '/countdown/js/jquery.plugin.min.js', array(), TP_DONATE_VER, true );
			wp_enqueue_script( 'tp-donate-countdown-js', TP_DONATE_LIB_URI . '/countdown/js/jquery.countdown.min.js', array(), TP_DONATE_VER, true );
			wp_enqueue_style( 'tp-donate-countdown-css', TP_DONATE_LIB_URI . '/countdown/css/jquery.countdown.css', array(), TP_DONATE_VER );
			foreach ( $this->_assets[ 'site' ] as $key => $files ) {
				if( $key === 'css' )
				{
					foreach ($files as $k => $f) {
						wp_enqueue_style( 'tp-donate-'.$key.'-'.$k, $f , array(), TP_DONATE_VER );
					}
				} else if( $key === 'js' ) {
					foreach ($files as $k => $f)
					{
						wp_enqueue_script( 'tp-donate-'.$key.'-'.$k, $f, array(), TP_DONATE_VER, true );
					}
				}
			}
		}

	}

	/**
	 * load options object class
	 * @return object class
	 */
	public function options()
	{
		return DN_Setting::instance();
	}

}

new ThimPress_Donate();