<?php

/**
 * register all post type
 */
class DN_Post_Type
{

	public function __construct()
	{
		/**
		 * register post type
		 */
		// add_action( 'init', array( $this, 'register_post_type_event' ) );
		add_action( 'init', array( $this, 'register_post_type_cause' ) );
		add_action( 'init', array( $this, 'register_post_type_donate' ) );

		/**
		 * register taxonomy
		 */
		add_action( 'init', array( $this, 'register_taxonomy_causes' ) );
	}

	// register post type event hook callback
	public function register_post_type_event()
	{
		$labels = array(
			'name'               => _x( 'Events', 'Events', 'tp-donate' ),
			'singular_name'      => _x( 'Event', 'Event', 'tp-donate' ),
			'menu_name'          => _x( 'Events', 'admin menu', 'tp-donate' ),
			'name_admin_bar'     => _x( 'Event', 'add new on admin bar', 'tp-donate' ),
			'add_new'            => _x( 'Add Event', 'event', 'tp-donate' ),
			'add_new_item'       => __( 'Add New Event', 'tp-donate' ),
			'new_item'           => __( 'New Event', 'tp-donate' ),
			'edit_item'          => __( 'Edit Event', 'tp-donate' ),
			'view_item'          => __( 'View Event', 'tp-donate' ),
			'all_items'          => __( 'Events', 'tp-donate' ),
			'search_items'       => __( 'Search Events', 'tp-donate' ),
			'parent_item_colon'  => __( 'Parent Events:', 'tp-donate' ),
			'not_found'          => __( 'No events found.', 'tp-donate' ),
			'not_found_in_trash' => __( 'No events found in Trash.', 'tp-donate' )
		);

		$args = array(
			'labels'             => $labels,
            'description'        => __( 'Event post type.', 'tp-donate' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'event' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 7,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
		);

		register_post_type( 'dn_event', $args );
	}

	// register post type cause hook callback
	public function register_post_type_cause()
	{
		$labels = array(
			'name'               => _x( 'Causes', 'Causes', 'tp-donate' ),
			'singular_name'      => _x( 'Cause', 'Cause', 'tp-donate' ),
			'menu_name'          => _x( 'Causes', 'admin menu', 'tp-donate' ),
			'name_admin_bar'     => _x( 'Cause', 'add new on admin bar', 'tp-donate' ),
			'add_new'            => _x( 'Add Cause', 'donate', 'tp-donate' ),
			'add_new_item'       => __( 'Add New Cause', 'tp-donate' ),
			'new_item'           => __( 'New Cause', 'tp-donate' ),
			'edit_item'          => __( 'Edit Cause', 'tp-donate' ),
			'view_item'          => __( 'View Cause', 'tp-donate' ),
			'all_items'          => __( 'Causes', 'tp-donate' ),
			'search_items'       => __( 'Search Causes', 'tp-donate' ),
			'parent_item_colon'  => __( 'Parent Causes:', 'tp-donate' ),
			'not_found'          => __( 'No causes found.', 'tp-donate' ),
			'not_found_in_trash' => __( 'No causes found in Trash.', 'tp-donate' )
		);

		$args = array(
			'labels'             => $labels,
            'description'        => __( 'Causes', 'tp-donate' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'causes' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 8,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
		);

		register_post_type( 'dn_cause', $args );
	}

	// register post type donate
	public function register_post_type_donate()
	{
		$labels = array(
			'name'               => _x( 'Donates', 'Donates', 'tp-donate' ),
			'singular_name'      => _x( 'Donate', 'Donate', 'tp-donate' ),
			'menu_name'          => _x( 'Donates', 'Donates', 'tp-donate' ),
			'name_admin_bar'     => _x( 'Donate', 'Donate', 'tp-donate' ),
			'add_new'            => _x( 'Add Donate', 'donate', 'tp-donate' ),
			'add_new_item'       => __( 'Add New Donate', 'tp-donate' ),
			'new_item'           => __( 'New Donate', 'tp-donate' ),
			'edit_item'          => __( 'Edit Donate', 'tp-donate' ),
			'view_item'          => __( 'View Donate', 'tp-donate' ),
			'all_items'          => __( 'Donates', 'tp-donate' ),
			'search_items'       => __( 'Search Donates', 'tp-donate' ),
			'parent_item_colon'  => __( 'Parent Donates:', 'tp-donate' ),
			'not_found'          => __( 'No donates found.', 'tp-donate' ),
			'not_found_in_trash' => __( 'No donates found in Trash.', 'tp-donate' )
		);

		$args = array(
			'labels'             => $labels,
            'description'        => __( 'Donates', 'tp-donate' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'tp_donate',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'donate' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
			'capabilities' => array(
				'create_posts'       => false,
			),
		);

		register_post_type( 'dn_donate', $args );
	}

	// register taxonomy
	public function register_taxonomy_causes()
	{
		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => _x( 'Cause Categories', 'tp-donate' ),
			'singular_name'     => _x( 'Cause', 'tp-donate' ),
			'search_items'      => __( 'Search Causes', 'tp-donate' ),
			'all_items'         => __( 'All Causes', 'tp-donate' ),
			'parent_item'       => __( 'Parent Cause', 'tp-donate' ),
			'parent_item_colon' => __( 'Parent Cause:', 'tp-donate' ),
			'edit_item'         => __( 'Edit Cause', 'tp-donate' ),
			'update_item'       => __( 'Update Cause', 'tp-donate' ),
			'add_new_item'      => __( 'Add New Cause', 'tp-donate' ),
			'new_item_name'     => __( 'New Cause Name', 'tp-donate' ),
			'menu_name'         => __( 'Categories', 'tp-donate' )
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'cause_cat' ),
		);

		register_taxonomy( 'dn_causes_cat', array( 'dn_cause' ), $args );

		// Add new taxonomy, make it hierarchical (like tags)
		$labels = array(
			'name'              => _x( 'Cause Tags', 'tp-donate', 'tp-donate' ),
			'singular_name'     => _x( 'Cause', 'tp-donate' ),
			'search_items'      => __( 'Search Causes Tag', 'tp-donate' ),
			'all_items'         => __( 'All Causes', 'tp-donate' ),
			'parent_item'       => __( 'Parent Cause Tag', 'tp-donate' ),
			'parent_item_colon' => __( 'Parent Cause Tag:', 'tp-donate' ),
			'edit_item'         => __( 'Edit Cause Tag', 'tp-donate' ),
			'update_item'       => __( 'Update Cause Tag', 'tp-donate' ),
			'add_new_item'      => __( 'Add New Cause Tag', 'tp-donate' ),
			'new_item_name'     => __( 'New Cause Tag Name', 'tp-donate' ),
			'menu_name'         => __( 'Tags', 'tp-donate' )
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'cause_tag' ),
		);

		register_taxonomy( 'dn_causes_tag', array( 'dn_cause' ), $args );
	}

}

new DN_Post_Type();