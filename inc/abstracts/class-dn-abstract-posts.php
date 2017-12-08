<?php
/**
 * Fundpress Abstract posts class.
 *
 * @version     2.0
 * @package     Abstract class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Post_Base' ) ) {
	/**
	 * Class DN_Post_Base.
	 */
	abstract class DN_Post_Base {

		/**
		 * @var int|null
		 */
		public $id = null;

		/**
		 * @var int|null|string|WP_Post
		 */
		protected $post = null;

		/**
		 * @var null
		 */
		protected $meta_prefix = null;

		/**
		 * @var null
		 */
		protected $post_type = null;

		/**
		 * DN_Post_Base constructor.
		 *
		 * @param null $post
		 */
		public function __construct( $post = null ) {
			if ( is_numeric( $post ) ) {
				$this->post = get_post( $post );
			}

			if ( $post instanceof WP_Post ) {
				$this->post = $post;
			}

			if ( $this->post ) {
				$this->id = $this->post->ID;
			}
		}

		/**
		 * Get key of post.
		 *
		 * @param $key
		 *
		 * @return mixed
		 */
		public function __get( $key ) {
			if ( ! $this->post ) {
				return false;
			}

			if ( $this->post->{$key} ) {
				return $this->post->{$key};
			}

			if ( metadata_exists( 'post', $this->id, $this->meta_prefix . $key ) ) {
				return $this->get_meta( $key );
			}

			return false;
		}

		/**
		 * Get post meta.
		 *
		 * @param $key
		 * @param bool $unique
		 *
		 * @return mixed
		 */
		public function get_meta( $key, $unique = true ) {
			if ( $meta = get_post_meta( $this->id, $this->meta_prefix . $key, $unique ) ) {
				return $meta;
			}

			return false;
		}

		/**
		 * Get title.
		 *
		 * @return string
		 */
		public function get_title() {
			return get_the_title( $this->id );
		}

		/**
		 * Update post meta.
		 *
		 * @param $key
		 * @param $value
		 */
		public function update_meta( $key, $value ) {
			update_post_meta( $this->id, $this->meta_prefix . $key, $value );
		}

		/**
		 * Set post meta.
		 *
		 * @param $key
		 * @param string $val
		 * @param bool $unique
		 */
		public function set_meta( $key, $val = '', $unique = false ) {
			if ( $key ) {
				update_post_meta( $this->id, $this->meta_prefix . $key, $val, $unique );
			}
		}

		/**
		 * Create post.
		 *
		 * @param array $args
		 *
		 * @return int|WP_Error
		 */
		public function create_post( $args = array() ) {
			$default = array(
				'post_title'   => '',
				'post_content' => '',
				'post_author'  => 1,
				'post_status'  => 'publish',
				'post_type'    => $this->post_type
			);

			$post = wp_parse_args( $args, $default );

			do_action( 'donate_before_insert_post', $this->post_type );

			$id = wp_insert_post( $post, true );

			do_action( 'donate_after_insert_post', $id );

			return $id;
		}

	}
}