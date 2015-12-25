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
	public $meta_prefix = 'thimpress_donate_';

	static $_instances = null;

	/**
	 * post type
	 * @var null
	 */
	public $post_type = 'dn_donate';

	public function __construct( $post )
	{
		parent::__construct( $post );
	}

	// create new donate
	function create_donate( $params = null )
	{
		if( ! $params || ! $params[ 'campaign_id' ] || ! $params[ 'amount' ] )
		{
			return new WP_Error( 'donate_create_donate', __( 'Can not create new donate.', 'tp-donate' ) );
		}

		$donate_id = $this->create_post(array(
				'post_title'	=> sprintf( '%s %s', __( 'Donate for', 'tp-donate' ), $params[ 'campaign_id' ] ),
				'post_content'	=> sprintf( '%s %s', __( 'Donate for', 'tp-donate' ), $params[ 'campaign_id' ] ),
				'post_excerpt'	=> sprintf( '%s %s', __( 'Donate for', 'tp-donate' ), $params[ 'campaign_id' ] ),
				'post_status'	=> 'donate-pending'
			));

		wp_update_post( array( 'ID' => $donate_id, 'post_title' => donate_generate_post_key( $donate_id ) ) );
		foreach ( $params as $meta_key => $value ) {
			add_post_meta( $donate_id, $this->meta_prefix . $meta_key, $value );
		}

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