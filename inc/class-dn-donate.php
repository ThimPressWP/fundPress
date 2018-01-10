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

if ( ! class_exists( 'DN_Donate' ) ) {
	/**
	 * Class DN_Donate.
	 */
	class DN_Donate extends DN_Post_Base {

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
		 * @var int
		 */
		public $donate_system = 0;

		/**
		 * @var null
		 */
		public $donor = null;

		/**
		 * @var null
		 */
		static $_instances = null;

		/**
		 * post type
		 * @var null
		 */
		public $post_type = 'dn_donate';

		/**
		 * DN_Donate constructor.
		 *
		 * @param $post
		 */
		public function __construct( $post ) {
			$this->meta_prefix = TP_DONATE_META_DONATE;
			parent::__construct( $post );
		}

		/**
		 * Create new donate.
		 *
		 * @param null $donor_id
		 * @param null $payment_method
		 *
		 * @return mixed|WP_Error
		 */
		public function create_donate( $donor_id = null, $payment_method = null ) {
			// donor_id
			if ( ! $donor_id ) {
				return new WP_Error( 'donor_error', __( 'Could not created donor.', 'fundpress' ) );
			}

			// create donate with cart contents
			$donate_id = $this->create_post( array(
				'post_title'   => sprintf( '%s - %s', current_time( 'mysql' ), $donor_id ),
				'post_content' => sprintf( '%s - %s', current_time( 'mysql' ), $donor_id ),
				'post_excerpt' => sprintf( '%s - %s', current_time( 'mysql' ), $donor_id ),
				'post_status'  => 'donate-pending'
			) );
			// update post with new title
			wp_update_post( array( 'ID' => $donate_id, 'post_title' => donate_generate_post_key( $donate_id ) ) );

			// cart
			$cart = FP()->cart;

			update_post_meta( $donate_id, $this->meta_prefix . 'total', $cart->cart_total );
			update_post_meta( $donate_id, $this->meta_prefix . 'addition', $cart->addtion_note );
			update_post_meta( $donate_id, $this->meta_prefix . 'currency', donate_get_currency() );
			update_post_meta( $donate_id, $this->meta_prefix . 'payment_method', $payment_method );
			update_post_meta( $donate_id, $this->meta_prefix . 'donor_id', $donor_id );
			update_post_meta( $donate_id, $this->meta_prefix . 'user_id', get_current_user_id() );

			// allow hook
			do_action( 'donate_create_booking_donate', $donate_id );

			return apply_filters( 'donate_create_booking_donate_result', $donate_id );
		}

		/**
		 * Update donor information.
		 *
		 * @param null $donor_id
		 * @param null $payment_method
		 *
		 * @return null
		 */
		public function update_information( $donor_id = null, $payment_method = null ) {
			// cart
			$cart = FP()->cart;

			// remove donate with cart_contents
			delete_post_meta( $this->id, $this->meta_prefix . 'total' );
			delete_post_meta( $this->id, $this->meta_prefix . 'addition' );
			delete_post_meta( $this->id, $this->meta_prefix . 'currency' );
			delete_post_meta( $this->id, $this->meta_prefix . 'payment_method' );
			delete_post_meta( $this->id, $this->meta_prefix . 'donor_id' );
			delete_post_meta( $this->id, $this->meta_prefix . 'user_id' );

			/* update new information */
			update_post_meta( $this->id, $this->meta_prefix . 'total', $cart->cart_total );
			update_post_meta( $this->id, $this->meta_prefix . 'addition', $cart->addtion_note );
			update_post_meta( $this->id, $this->meta_prefix . 'currency', donate_get_currency() );
			update_post_meta( $this->id, $this->meta_prefix . 'payment_method', $payment_method );
			update_post_meta( $this->id, $this->meta_prefix . 'donor_id', $donor_id );
			update_post_meta( $this->id, $this->meta_prefix . 'user_id', get_current_user_id() );

			return $this->id;
		}

		/**
		 * Remove all donate items.
		 */
		public function remove_donate_items() {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "DELETE FROM itemmeta USING {$wpdb->prefix}postmeta itemmeta INNER JOIN {$wpdb->prefix}posts items WHERE itemmeta.post_id = items.ID AND items.ID = %d AND items.post_type = %s", $this->id, 'dn_donate_item' ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}posts WHERE post_parent = %d AND post_type = %s", $this->id, 'dn_donate_item' ) );
		}

		/**
		 * Add donate item.
		 *
		 * @param null $campaign_id
		 * @param string $title
		 * @param int $total
		 *
		 * @return int|WP_Error
		 */
		public function add_donate_item( $campaign_id = null, $title = '', $total = 0 ) {
			if ( ! $this->id ) {
				return false;
			}

			$item_id = wp_insert_post( array(
				'post_type'   => 'dn_donate_item',
				'post_parent' => $this->id,
				'post_status' => 'publish'
			) );

			update_post_meta( $item_id, 'campaign_id', absint( $campaign_id ) );
			update_post_meta( $item_id, 'title', $title );
			update_post_meta( $item_id, 'total', floatval( $total ) );

			return $item_id;
		}

		/**
		 * Update donate status.
		 *
		 * @param string $status
		 */
		public function update_status( $status = 'donate-processing' ) {

			if ( ! $this->id ) {
				return;
			}

			if ( strpos( $status, 'donate-' ) !== 0 ) {
				$status = 'donate-' . $status;
			}

			wp_update_post( array( 'ID' => $this->id, 'post_status' => $status ) );

			$old_status = get_post_status( $this->id );
			$old_status = substr( $old_status, strlen( 'donate-' ) );
			$status     = substr( $status, strlen( 'donate-' ) );
			do_action( 'donate_update_status_' . $old_status . '_' . $status, $this->id );
			do_action( 'donate_update_status', $this->id, $old_status, $status );
			do_action( 'donate_update_status_' . $status, $this->id );

			$this->send_email( $status );
		}

		/**
		 * Send email if status is completed.
		 *
		 * @param $status
		 */
		public function send_email( $status ) {
			if ( $status === 'completed' && $donor = $this->get_donor() ) {
				DN_Email::instance()->send_email_donate_completed( $donor );
			}
		}

		/**
		 * Get donor by donate id.
		 *
		 * @return DN_Donor|null|bool
		 */
		public function get_donor() {
			if ( $this->donor ) {
				return $this->donor;
			}

			$donor_id = $this->donor_id;
			if ( ! $donor_id ) {
				return false;
			}

			return $this->donor = DN_Donor::instance( $donor_id );
		}

		/**
		 * Check donate status.
		 *
		 * @param string $status
		 *
		 * @return bool
		 */
		public function has_status( $status = 'completed' ) {
			return $this->post->post_status === 'donate-' . $status;
		}

		/**
		 * Get donate items.
		 *
		 * @return mixed
		 */
		public function get_items() {
			if ( ! $this->id ) {
				return array();
			}
			$results = get_children( array(
				'post_type'   => 'dn_donate_item',
				'order'       => 'ASC',
				'numberposts' => - 1,
				'post_status' => 'publish',
				'post_parent' => $this->id
			) );

			$items = array();
			foreach ( $results as $post ) {
				$items[] = DN_Donate_Item::instance( $post->ID );
			}

			return apply_filters( 'donate_get_donate_items', $items, $this->id );
		}

		/**
		 * Instance.
		 *
		 * @param null $post
		 *
		 * @return DN_Donate
		 */
		public static function instance( $post = null ) {
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