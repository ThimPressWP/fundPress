<?php

abstract class DN_Post_Base
{

	/**
	 * ID of Post
	 * @var null
	 */
	protected $ID = null;

	/**
	 * post
	 * @var null
	 */
	protected $post = null;

	/**
	 * post type
	 * @var null
	 */
	protected $post_type = null;

	function __construct( $post )
	{
		if( is_numeric( $post ) )
			$this->post = get_post( $post );

		if( $post instanceof WP_Post )
			$this->post = $post;
	}

	/**
	 * get key of post
	 * @param $key
	 * @return *
	 */
	function __get( $key )
	{
		if( ! $this->post )
			return;

		if( ! $this->post->{$key} )
			return;

		return $this->post->{$key};
	}

	/**
	 * create post with post type = $this->post_type
	 * @param  array  $args
	 * @return
	 */
	function create_post( $args = array() )
	{
		$default = array(
			'post_title'	=> '',
			'post_content'	=> '',
			'post_author'	=> 1,
			'post_status'	=> 'publish',
			'post_type'		=> $this->post_type
		);

		$post = wp_parse_args( $args, $default );

		return wp_insert_post( $post, true );
	}

}
