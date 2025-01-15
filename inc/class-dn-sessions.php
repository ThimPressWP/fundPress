<?php
/**
 * Fundpress Sessions class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Sessions' ) ) {
	/**
	 * Class DN_Sessions.
	 */
	class DN_Sessions {

		/**
		 * @var null
		 */
		static $_instance = null;

		/**
		 * @var array|mixed|null
		 */
		public $session = null;

		/**
		 * @var float|int|null
		 */
		private $live_item = null;

		/**
		 * @var bool
		 */
		private $remember = false;

		/**
		 * @var null|string
		 */
		public $prefix = null;

		/**
		 * DN_Sessions constructor.
		 *
		 * @param string $prefix
		 * @param bool $remember
		 */
		public function __construct( $prefix = '', $remember = true ) {
			if ( ! $prefix ) {
				return;
			}
			$this->prefix    = $prefix;
			$this->remember  = $remember;
			$this->live_item = 12 * HOUR_IN_SECONDS;
			// get all
			$this->session = $this->load();
		}

		/**
		 * Load all with prefix.
		 *
		 * @return array|mixed
		 */
		public function load() {
			/**
			 * Only start to prevent request-timeout when
			 * wp try to call a test to a rest-api for site-health feature.
			 */
			try {
				if ( isset( $_SESSION[ $this->prefix ] ) ) {
					return $_SESSION[ $this->prefix ];
				} elseif ( $this->remember && isset( $_COOKIE[ $this->prefix ] ) ) {
					$_SESSION[ $this->prefix ] = json_decode( $_COOKIE[ $this->prefix ] );
					if ( json_last_error() !== JSON_ERROR_NONE ) {
						throw new Exception( 'JSON decode: ' . json_last_error_msg() );
					}

					return $_SESSION[ $this->prefix ];
				}
			} catch ( Throwable $e ) {
			}

			return array();
		}

		/**
		 * Remove session.
		 */
		public function remove() {
			if ( isset( $_SESSION[ $this->prefix ] ) ) {
				unset( $_SESSION[ $this->prefix ] );
			}

			if ( $this->remember && isset( $_COOKIE[ $this->prefix ] ) ) {
				donate_setcookie( $this->prefix, '', time() - $this->live_item );
				unset( $_COOKIE[ $this->prefix ] );
			}
		}

		/**
		 * Set key.
		 *
		 * @param string $name
		 * @param null $value
		 */
		public function set( $name = '', $value = null ) {
			if ( ! $name ) {
				return;
			}

			$name  = sanitize_text_field( $name );
			$value = DN_Helpper::DN_sanitize_params_submitted( $value );

			$time = time();
			if ( ! $value ) {
				unset( $this->session[ $name ] );
				$time = $time - $this->live_item;
			} else {
				$this->session[ $name ] = $value;
				$time                   = $time + $this->live_item;
			}

			// save session
			$_SESSION[ $this->prefix ] = $this->session;
			// save cookie
			donate_setcookie( $this->prefix, json_encode( $this->session ), $time );
		}

		/**
		 * Get value.
		 *
		 * @param null $name
		 * @param null $default
		 *
		 * @return mixed|null
		 */
		public function get( $name = null, $default = null ) {
			if ( ! $name ) {
				return $default;
			}

			if ( isset( $this->session[ $name ] ) ) {
				return $this->session[ $name ];
			}

			return false;
		}

		/**
		 * Instance.
		 *
		 * @param string $prefix
		 *
		 * @return DN_Sessions
		 */
		static function instance( $prefix = '' ) {
			if ( ! empty( self::$_instance[ $prefix ] ) ) {
				return self::$_instance[ $prefix ];
			}

			return self::$_instance[ $prefix ] = new self( $prefix );
		}
	}
}
