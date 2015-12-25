<?php

class DN_Campaign extends DN_Post_Base
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
	public $meta_prefix = 'donate_';

	static $_instances = null;

	/**
	 * post type
	 * @var null
	 */
	public $post_type = 'dn_campaign';

	public function __construct( $post )
	{
		parent::__construct( $post );
	}

	/**
	 * compensate
	 * @return array
	 */
	public function get_compensate()
	{
		return get_post_meta( $this->ID, 'donate_marker', true );
	}

	/**
	 * currency
	 * @return array
	 */
	public function get_currency()
	{
		$currency = donate_get_currency();
		if( get_post_meta( $this->ID, 'donate_currency', true ) )
		{
			$currency = get_post_meta( $this->ID, 'donate_currency', true );
		}
		return $currency;
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

		if( ! isset( $id ) )
			$id = $post->ID;

		if( ! empty( self::$_instances[ $id ] ) )
		{
			return self::$_instances[ $id ];
		}

		return self::$_instances[ $id ] = new self( $post );
	}

}