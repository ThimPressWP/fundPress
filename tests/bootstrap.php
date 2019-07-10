<?php
/**
 * Fundpress Unit Tests Bootstrap
 *
 * @since 1.1
 */
class DN_Unit_Tests_Bootstrap {
	/** @var DN_Unit_Tests_Bootstrap instance */
	protected static $instance = null;
	/** @var string directory where wordpress-tests-lib is installed */
	public $wp_tests_dir;
	/** @var string testing directory */
	public $tests_dir;
	/** @var string plugin directory */
	public $plugin_dir;
	/**
	 * Setup the unit testing environment.
	 *
	 * @since 1.1
	 */
	public function __construct() {
		ini_set( 'display_errors','on' );
		error_reporting( E_ALL );
		// Ensure server variable is set for WP email functions.
		if ( ! isset( $_SERVER['SERVER_NAME'] ) ) {
			$_SERVER['SERVER_NAME'] = 'localhost';
		}
	}

	/**
	 * Get the single class instance.
	 *
	 * @since 1.1
	 * @return DN_Unit_Tests_Bootstrap
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
DN_Unit_Tests_Bootstrap::instance();