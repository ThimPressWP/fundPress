<?php
/**
 * Fundpress Campaign class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Campaign' ) ) {
	/**
	 * Class DN_Campaign
	 */
	class DN_Campaign extends DN_Post_Base {

		/**
		 * @var null
		 */
		public $id = null;

		/**
		 * @var null
		 */
		public $post = null;

		/**
		 * @var null|string
		 */
		public $meta_prefix = null;

		/**
		 * @var null
		 */
		static $_instances = null;

		/**
		 * @var string
		 */
		public $post_type = 'dn_campaign';

		/**
		 * DN_Campaign constructor.
		 *
		 * @param $post
		 */
		public function __construct( $post ) {
			$this->meta_prefix = TP_DONATE_META_CAMPAIGN;
			parent::__construct( $post );
		}

		/**
		 * Get compensate.
		 *
		 * @return mixed
		 */
		public function get_compensate() {
			return get_post_meta( $this->id, $this->meta_prefix . 'marker', true );
		}

		/**
		 * Get campaign currency.
		 *
		 * @return mixed
		 */
		public function get_currency() {
			if ( ! ( $currency = get_post_meta( $this->id, $this->meta_prefix . 'currency', true ) ) ) {
				$currency = donate_get_currency();
			}

			return $currency;
		}

		/**
		 * Get campaign total raised.
		 *
		 * @return float
		 */
		public function get_total_raised() {
			return floatval( get_post_meta( $this->id, $this->meta_prefix . 'total_raised', true ) );
		}

		/**
		 * Instance.
		 *
		 * @param null $post
		 *
		 * @return DN_Campaign
		 */
		static function instance( $post = null ) {

			if ( is_numeric( $post ) ) {
				$post = get_post( $post );
				$id   = $post->ID;
			} else if ( $post instanceof WP_Post ) {
				$id = $post->ID;
			}

			if ( ! isset( $id ) && $post ) {
				$id = $post->ID;
			}

			if ( ! empty( self::$_instances[ $id ] ) ) {
				return self::$_instances[ $id ];
			}

			return self::$_instances[ $id ] = new self( $post );
		}

	}
}