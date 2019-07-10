<?php
/**
 * Fundpress Donate class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Donate_Item' ) ) {
	/**
	 * Class DN_Donate_Item.
	 */
	class DN_Donate_Item extends DN_Post_Base {

		/**
		 * @var null
		 */
		public static $_instances = null;

		/**
		 * @var string
		 */
		public $post_type = 'dn_donate_item';

		/**
		 * DN_Donate_Item constructor.
		 *
		 * @param null $post
		 */
		public function __construct( $post = null ) {
			parent::__construct( $post );
		}

		public static function instance( $post ) {
			if ( ! $post ) {
				return new self( $post );
			}

			if ( is_numeric( $post ) ) {
				$post = get_post( $post );
				$id   = $post->ID;
			} else if ( $post instanceof WP_Post ) {
				$id = $post->ID;
			}

			if ( ! empty( self::$_instances[ $id ] ) ) {
				return self::$_instances[ $id ];
			}

			return self::$_instances[ $id ] = new self( $post );
		}

	}
}
