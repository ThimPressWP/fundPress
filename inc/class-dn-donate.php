<?php

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
	function create_donate( $donor_id = null, $payment_method = null )
	{
		// donor_id
		if( ! $donor_id )
		{
			return array( 'status' => 'failed', 'message' => __( 'Could not created donor', 'tp-donate' ) );
		}

		// create donate with cart contents
		$donate_id = $this->create_post(array(
				'post_title'	=> sprintf( '%s - %s', current_time( 'mysql' ), $donor_id ),
				'post_content'	=> sprintf( '%s - %s', current_time( 'mysql' ), $donor_id ),
				'post_excerpt'	=> sprintf( '%s - %s', current_time( 'mysql' ), $donor_id ),
				'post_status'	=> 'donate-pending'
			));
		// update post with new title
		wp_update_post( array( 'ID' => $donate_id, 'post_title' => donate_generate_post_key( $donate_id ) ) );

		$cart = donate()->cart;
		// get cart contents
		$cart_contents = $cart->cart_contents;
		// cart_contents
		add_post_meta( $donate_id, $this->meta_prefix . 'cart_contents', $cart_contents );
		add_post_meta( $donate_id, $this->meta_prefix . 'total', $cart->cart_total_include_tax );
		// insert post meta
		// foreach ( $cart_contents as $cart_item_id => $cart_content ) {
		// 	// ignoire product_data key
		// 	foreach ( $cart_content as $meta_key => $value ) {
		// 		// ignoire product_data key
		// 		if( $meta_key === 'product_data' )
		// 			continue;

		// 		add_post_meta( $donate_id, $this->meta_prefix . $meta_key, $value );
		// 	}
		// }

		add_post_meta( $donate_id, $this->meta_prefix . 'payment_method', $payment_method );
		add_post_meta( $donate_id, $this->meta_prefix . 'addition', donate()->cart->addtion_note );
		add_post_meta( $donate_id, $this->meta_prefix . 'donor_id', $donor_id );

		// return donate_id
		return $donate_id;

	}

	// static function instead of new class
	static function instance( $post = null )
	{
		if( ! $post )
			return new self( $post );

		if( is_numeric( $post ) && ! self::$_instances[ $post ] )
		{
			$post = get_post( $post );
			$id = $post->ID;
		}
		else if( $post instanceof WP_Post )
		{
			$id = $post->ID;
		}

		if( ! empty( self::$_instances[ $id ] ) )
		{
			return self::$_instances[ $id ];
		}

		return self::$_instances[ $id ] = new self( $post );
	}

}