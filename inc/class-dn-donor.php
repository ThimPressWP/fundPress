<?php
/**
 * Fundpress Donor class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Donor' ) ) {
	/**
	 * Class DN_Donor.
	 */
	class DN_Donor extends DN_Post_Base {

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
		public $post_type = 'dn_donor';

		/**
		 * DN_Donor constructor.
		 *
		 * @param $post
		 */
		public function __construct( $post ) {
			$this->meta_prefix = TP_DONATE_META_DONOR;
			parent::__construct( $post );
		}

		/**
		 * Check donor exists.
		 *
		 * @param null $email
		 *
		 * @return bool|WP_Error
		 */
		public function donor_exists( $email = null ) {
			if ( ! $email ) {
				return new WP_Error( 'donate_create_donor', __( 'Could not create new donor with empty email.', 'fundpress' ) );
			}

			global $wpdb;
			$query = $wpdb->prepare( "
				SELECT post.ID as ID FROM {$wpdb->posts} AS post
				INNER JOIN {$wpdb->postmeta} AS meta
				ON meta.post_id = post.ID
				WHERE
				post.post_status = %s
				AND post.post_type = %s
				AND meta.meta_key = %s
				AND meta.meta_value = %s
			", 'publish', $this->post_type, 'thimpress_donor_email', $email );

			$result = $wpdb->get_row( $query, OBJECT );

			if ( ! $result ) {
				return false;
			}

			return $result->ID;
		}

		/**
		 * Create new donor.
		 *
		 * @param null $param
		 *
		 * @return bool|int|WP_Error
		 */
		public function create_donor( $param = null ) {
			if ( ! $param ) {
				return new WP_Error( 'donate_create_donor', __( 'Could not create new donor.', 'fundpress' ) );
			}

			$donor_id = $this->donor_exists( $param['email'] );
			if ( ! $donor_id ) {
				$donor_id = $this->create_post( array(
					'post_title'   => sprintf( '%s %s', $param['first_name'], $param['last_name'] ),
					'post_content' => sprintf( '%s', $param['email'] ),
					'post_excerpt' => sprintf( '%s', $param['email'] )
				) );
			}

			foreach ( $param as $meta_key => $value ) {
				update_post_meta( $donor_id, $this->meta_prefix . $meta_key, $value );
			}

			return $donor_id;
		}

		/**
		 * Update donor.
		 *
		 * @param array $param
		 */
		public function update_donor( $param = array() ) {
			foreach ( $param as $meta_key => $value ) {
				update_post_meta( $this->id, $this->meta_prefix . $meta_key, $value );
			}
		}

		/**
		 * Get all donated of donor.
		 *
		 * @return array
		 */
		public function get_donated() {
			$args        = array(
				'posts_per_page' => - 1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'meta_key'       => 'thimpress_donate_donor_id',
				'meta_value'     => $this->id,
				'post_type'      => 'dn_donate',
				'post_status'    => array( 'donate-pending', 'donate-processing', 'donate-completed' )
			);
			$posts_array = get_posts( $args );
			wp_reset_postdata();

			return $posts_array;
		}

		/**
		 * Get full name.
		 *
		 * @return string
		 */
		public function get_fullname() {
			return sprintf( '%s %s', $this->first_name, $this->last_name );
		}

		/**
		 * Instance.
		 *
		 * @param null $post
		 *
		 * @return DN_Donor
		 */
		static function instance( $post = null ) {
			if ( ! $post ) {
				return new self( $post );
			}

			$id = null;
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