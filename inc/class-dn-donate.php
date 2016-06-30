<?php
if( ! defined( 'ABSPATH' ) ) exit();

class DN_Donate extends DN_Post_Base
{

	/**
	 * ID of Post
	 * @var null
	 */
	public $ID = null;

	/**
	 * post
	 * @var null
	 */
	public $post = null;

	/**
	 * meta prefix of post type
	 * @var null
	 */
	public $meta_prefix = null; //'thimpress_donate_';

	public $donate_system = 0;

	public $donor = null;

	static $_instances = null;

	/**
	 * post type
	 * @var null
	 */
	public $post_type = 'dn_donate';

	public function __construct( $post )
	{
		$this->meta_prefix = TP_DONATE_META_DONATE;
		parent::__construct( $post );
	}

	// create new donate
	function create_donate( $donor_id = null, $payment_method = null, $donate_system = false )
	{
		try {
			// donor_id
			if( ! $donor_id ) {
				return new WP_Error( 'donor_error', __( 'Could not created donor.', 'tp-donate' ) );
			}

			// create donate with cart contents
			$donate_id = $this->create_post( array(
					'post_title'	=> sprintf( '%s - %s', current_time( 'mysql' ), $donor_id ),
					'post_content'	=> sprintf( '%s - %s', current_time( 'mysql' ), $donor_id ),
					'post_excerpt'	=> sprintf( '%s - %s', current_time( 'mysql' ), $donor_id ),
					'post_status'	=> 'donate-pending'
				) );
			// update post with new title
			wp_update_post( array( 'ID' => $donate_id, 'post_title' => donate_generate_post_key( $donate_id ) ) );

			// cart
			$cart = donate()->cart;

			// create donate for symtem without campaign
			if( $donate_system && is_numeric( $donate_system ) )
			{
				$this->donate_system = $donate_system;
				// create donate without campaign
				update_post_meta( $donate_id, $this->meta_prefix . 'amount_system', $donate_system );
				update_post_meta( $donate_id, $this->meta_prefix . 'total', $donate_system );
			}
			// get cart contents
			else if( $cart_contents = $cart->cart_contents )
			{
				// create donate with cart_contents
				update_post_meta( $donate_id, $this->meta_prefix . 'cart_contents', $cart_contents ); // cart contents meta donate
				update_post_meta( $donate_id, $this->meta_prefix . 'total', $cart->cart_total_include_tax );

				// insert post meta
				foreach ( $cart_contents as $cart_item_id => $cart_content ) {
					// ignoire product_data key
					$campaign = DN_Campaign::instance( $cart_content->product_id );
					// convert campaign currency format
					// $campaign->set_meta( 'amount', donate_campaign_convert_amount( $cart_content->amount, $cart_content->currency, $campaign->get_meta( 'currency' ) ) );

					// ralationship campagin id and donate
					$campaign->set_meta( 'donate', $donate_id );
					// update_post_meta( $cart_content->product_id , $this->meta_prefix . 'donate', $donate_id  );
				}
			}

			update_post_meta( $donate_id, $this->meta_prefix . 'addition', $cart->addtion_note );
			update_post_meta( $donate_id, $this->meta_prefix . 'currency', donate_get_currency() );
			update_post_meta( $donate_id, $this->meta_prefix . 'payment_method', $payment_method );
			update_post_meta( $donate_id, $this->meta_prefix . 'donor_id', $donor_id );
			update_post_meta( $donate_id, $this->meta_prefix . 'user_id', get_current_user_id() );

			// allow hook
			do_action( 'donate_create_booking_donate', $donate_id );

			return apply_filters( 'donate_create_booking_donate_result', $donate_id );

		} catch ( Exception $e ) {
			return new WP_Error( 'create-donate-error', $e->getMessage() );
		}
	}

	function update_information( $donor_id = null, $payment_method = null, $donate_system = false ) {
		// cart
		$cart = donate()->cart;

		// remove donate with cart_contents
		delete_post_meta( $this->ID, $this->meta_prefix . 'cart_contents' ); // cart contents meta donate
		delete_post_meta( $this->ID, $this->meta_prefix . 'total' );
		delete_post_meta( $this->ID, $this->meta_prefix . 'addition' );
		delete_post_meta( $this->ID, $this->meta_prefix . 'currency' );
		delete_post_meta( $this->ID, $this->meta_prefix . 'payment_method' );
		delete_post_meta( $this->ID, $this->meta_prefix . 'donor_id' );

		// create donate for symtem without campaign
		if( $donate_system && is_numeric( $donate_system ) )
		{
			$this->donate_system = $donate_system;
			// create donate without campaign
			update_post_meta( $this->ID, $this->meta_prefix . 'amount_system', $donate_system );
			update_post_meta( $this->ID, $this->meta_prefix . 'total', $donate_system );
		}
		// get cart contents
		else if( $cart_contents = $cart->cart_contents )
		{
			// create donate with cart_contents
			update_post_meta( $this->ID, $this->meta_prefix . 'cart_contents', $cart_contents ); // cart contents meta donate
			update_post_meta( $this->ID, $this->meta_prefix . 'total', $cart->cart_total_include_tax );

			// insert post meta
			foreach ( $cart_contents as $cart_item_id => $cart_content ) {
				// ignoire product_data key
				$campaign = DN_Campaign::instance( $cart_content->product_id );

				// ralationship campagin id and donate
				$campaign->set_meta( 'donate', $this->ID );
			}
		}

		update_post_meta( $this->ID, $this->meta_prefix . 'addition', $cart->addtion_note );
		update_post_meta( $this->ID, $this->meta_prefix . 'currency', donate_get_currency() );
		update_post_meta( $this->ID, $this->meta_prefix . 'payment_method', $payment_method );
		update_post_meta( $this->ID, $this->meta_prefix . 'donor_id', $donor_id );
		return $this->ID;
	}

	// update status
	function update_status( $status = 'donate-processing' )
	{

		if( ! $this->ID )
			return;

		$old_status = get_post_status( $this->ID );

		wp_update_post( array( 'ID' => $this->ID, 'post_status' => $status ) );

		do_action( 'donate_update_status_' . $old_status . '_' . $status, $this->ID );
		do_action( 'donate_update_status', $this->ID, $old_status, $status );

		$this->send_email( $status );

	}

	// send email if status is completed
	function send_email( $status )
	{
		if( $status === 'donate-completed' && $donor = $this->get_donor() ) {
			DN_Email::instance()->send_email_donate_completed( $donor );
		}
	}

	// get donor by donate id
	function get_donor()
	{
		if( $this->donor ) {
			return $this->donor;
		}

		$donor_id = $this->get_meta( 'donor_id' );
		if( ! $donor_id ) return;

		return $this->donor = DN_Donor::instance( $donor_id );
	}

	// static function instead of new class
	static function instance( $post = null )
	{
		if( ! $post ) {
			return new self( $post );
		}

		if( is_numeric( $post ) ) {
			$post = get_post( $post );
			$id = $post->ID;
		} else if( $post instanceof WP_Post ) {
			$id = $post->ID;
		}

		if( ! empty( self::$_instances[ $id ] ) ) {
			return self::$_instances[ $id ];
		}

		return self::$_instances[ $id ] = new self( $post );
	}

}