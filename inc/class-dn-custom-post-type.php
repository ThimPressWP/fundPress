<?php
if( ! defined( 'ABSPATH' ) ) exit();

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
		add_action( 'init', array( $this, 'register_post_type_campaign' ) ); // campaign
		add_action( 'init', array( $this, 'register_post_type_donate' ) ); // donate
		add_action( 'init', array( $this, 'register_post_type_donor' ) ); // donor

		// custom post type admin column
		add_filter( 'manage_dn_donate_posts_columns', array( $this, 'add_columns' ) );
		add_action( 'manage_dn_donate_posts_custom_column', array( $this, 'columns' ), 10, 2 );

		add_filter( 'manage_dn_campaign_posts_columns', array( $this, 'campaign_columns' ) );
		add_action( 'manage_dn_campaign_posts_custom_column', array( $this, 'campaign_column_content' ), 10, 2 );

		/**
		 * register taxonomy
		 */
		add_action( 'init', array( $this, 'register_taxonomy_causes' ) );

		/**
		 * post status
		 */
		add_action( 'init', array( $this, 'register_post_status' ) );
	}

	/**
	 * add_columns to donate post type admin
	 * @param array
	 */
	public function add_columns( $columns )
	{
		$columns[ 'donate_payment_method' ] = apply_filters( 'donate_add_column_donate_payment_method', sprintf( '%s', __( 'Payment Method', 'tp-donate' ) ) );
		$columns[ 'donate_status' ] = apply_filters( 'donate_add_column_donate_status', sprintf( '%s', __( 'Status', 'tp-donate' ) ) );
		return $columns;
	}

	// add columns
	public function columns( $column, $post_id )
	{
		switch ( $column ) {
			case 'donate_payment_method':
					$donate = DN_Donate::instance( $post_id );
					$payment = $donate->get_meta( 'payment_method' );
					$payments_enable = donate_payment_gateways();
					if( array_key_exists( $payment, $payments_enable ) )
						echo $payments_enable[ $payment ]->_title;
				break;
			case 'donate_status':
					echo donate_get_status_label( $post_id );
				break;
		}
	}

	public function campaign_columns( $columns ) {
		unset( $columns['date'], $columns['comments'], $columns['author'] );
		$columns[ 'start' ]	= apply_filters( 'donate_add_column_campaign_start_column', __( 'Start', 'tp-donate' ) );
		$columns[ 'end' ]	= apply_filters( 'donate_add_column_campaign_end_column', __( 'End', 'tp-donate' ) );
		$columns[ 'funded' ] = apply_filters( 'donate_add_column_campaign_publish_column', __( 'Founded', 'tp-donate' ) );
		$columns[ 'donors' ] = apply_filters( 'donate_add_column_campaign_backer_column', __( 'Donors', 'tp-donate' ) );
		$columns[ 'date' ] = apply_filters( 'donate_add_column_campaign_publish_column', __( 'Created At', 'tp-donate' ) );
		return $columns;
	}

	public function campaign_column_content( $column, $post_id ) {
		$campaign = DN_Campaign::instance( $post_id );
		switch ( $column ) {
			case 'start':
				$campaign->start ? printf( '%s', date_i18n( get_option( 'date_format', 'Y-m-d' ), strtotime( $campaign->start ) ) ) : '';
				break;
			case 'end':
				$campaign->end ? printf( '%s', date_i18n( get_option( 'date_format', 'Y-m-d' ), strtotime( $campaign->end ) ) ) : '';
				break;
			case 'funded':
				$campaign->end ? printf( '%s', donate_get_campaign_percent() . '%' ) : '';
				break;
			case 'donors':
				$campaign->end ? printf( '%s', date_i18n( get_option( 'date_format', 'Y-m-d' ), strtotime( $campaign->end ) ) ) : '';
				break;

			default:
				# code...
				break;
		}
	}

	// register post type cause hook callback
	public function register_post_type_campaign()
	{
		$labels = array(
			'name'               => _x( 'Campaigns', 'post type general name', 'tp-donate' ),
			'singular_name'      => _x( 'Campaign', 'post type singular name', 'tp-donate' ),
			'menu_name'          => _x( 'Campaigns', 'admin menu', 'tp-donate' ),
			'name_admin_bar'     => _x( 'Campaign', 'add new on admin bar', 'tp-donate' ),
			'add_new'            => _x( 'Add Campaign', 'add new on admin bar', 'tp-donate' ),
			'add_new_item'       => __( 'Add New Campaign', 'tp-donate' ),
			'new_item'           => __( 'New Campaign', 'tp-donate' ),
			'edit_item'          => __( 'Edit Campaign', 'tp-donate' ),
			'view_item'          => __( 'View Campaign', 'tp-donate' ),
			'all_items'          => __( 'Campaigns', 'tp-donate' ),
			'search_items'       => __( 'Search Campaigns', 'tp-donate' ),
			'parent_item_colon'  => __( 'Parent Campaigns:', 'tp-donate' ),
			'not_found'          => __( 'No campaign found.', 'tp-donate' ),
			'not_found_in_trash' => __( 'No campaign found in Trash.', 'tp-donate' )
		);

		$args = array(
			'labels'             => $labels,
            'description'        => __( 'Campaigns', 'tp-donate' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'campaigns' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 8,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
		);

		$args = apply_filters( 'donate_register_post_type_campaign', $args );
		register_post_type( 'dn_campaign', $args );
	}

	// register post type donate
	public function register_post_type_donate()
	{
		$labels = array(
			'name'               => _x( 'Donates', 'post type general name', 'tp-donate' ),
			'singular_name'      => _x( 'Donate', 'post type singular name', 'tp-donate' ),
			'menu_name'          => _x( 'Donates', 'add new on admin bar', 'tp-donate' ),
			'name_admin_bar'     => _x( 'Donate', 'admin menu', 'tp-donate' ),
			'add_new'            => _x( 'Add Donate', 'dn_donate', 'tp-donate' ),
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
			'rewrite'            => array( 'slug' => _x( 'donates', 'URL slug', 'tp-donate' ) ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'author' ),
			'capabilities' => array(
				'create_posts'       => false,
			),
			'map_meta_cap' => true
		);

		$args = apply_filters( 'donate_register_post_type_donate', $args );
		register_post_type( 'dn_donate', $args );
	}

	// register post type donor
	public function register_post_type_donor()
	{
		$labels = array(
			'name'               => _x( 'Donors', 'post type general name', 'tp-donate' ),
			'singular_name'      => _x( 'Donor', 'post type singular name', 'tp-donate' ),
			'menu_name'          => _x( 'Donors', 'admin menu', 'tp-donate' ),
			'name_admin_bar'     => _x( 'Donor', 'add new on admin bar', 'tp-donate' ),
			'add_new'            => _x( 'Add Donor', 'dn_donor', 'tp-donate' ),
			'add_new_item'       => __( 'Add New Donor', 'tp-donate' ),
			'new_item'           => __( 'New Donor', 'tp-donate' ),
			'edit_item'          => __( 'Edit Donor', 'tp-donate' ),
			'view_item'          => __( 'View Donor', 'tp-donate' ),
			'all_items'          => __( 'Donors', 'tp-donate' ),
			'search_items'       => __( 'Search Donors', 'tp-donate' ),
			'parent_item_colon'  => __( 'Parent Donors:', 'tp-donate' ),
			'not_found'          => __( 'No donors found.', 'tp-donate' ),
			'not_found_in_trash' => __( 'No donors found in Trash.', 'tp-donate' )
		);

		$args = array(
			'labels'             => $labels,
            'description'        => __( 'Donors', 'tp-donate' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'tp_donate',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => _x( 'donors', 'URL slug', 'tp-donate' ) ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'author' ),
			'capabilities' => array(
				'create_posts'  => false
			),
			'map_meta_cap' => true
		);

		$args = apply_filters( 'donate_register_post_type_donor', $args );
		register_post_type( 'dn_donor', $args );
	}

	// register taxonomy
	public function register_taxonomy_causes()
	{
		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => _x( 'Categories', 'taxonomy general name', 'tp-donate' ),
			'singular_name'     => _x( 'Category', 'taxonomy singular name', 'tp-donate' ),
			'search_items'      => __( 'Search Campaigns', 'tp-donate' ),
			'all_items'         => __( 'All Campaigns', 'tp-donate' ),
			'parent_item'       => __( 'Parent Category', 'tp-donate' ),
			'parent_item_colon' => __( 'Parent Category:', 'tp-donate' ),
			'edit_item'         => __( 'Edit Category', 'tp-donate' ),
			'update_item'       => __( 'Update Category', 'tp-donate' ),
			'add_new_item'      => __( 'Add New Category', 'tp-donate' ),
			'new_item_name'     => __( 'New Category', 'tp-donate' ),
			'menu_name'         => __( 'Categories', 'tp-donate' )
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => _x( 'campaign-cat', 'URL slug', 'tp-donate' ) ),
		);

		$args = apply_filters( 'donate_register_tax_capaign_cat', $args );
		register_taxonomy( 'dn_campaign_cat', array( 'dn_campaign' ), $args );

		// Add new taxonomy, make it hierarchical (like tags)
		$labels = array(
			'name'              => _x( 'Tags', 'taxonomy general name', 'tp-donate' ),
			'singular_name'     => _x( 'Tag', 'taxonomy singular name', 'tp-donate' ),
			'search_items'      => __( 'Search Tag', 'tp-donate' ),
			'all_items'         => __( 'All Tags', 'tp-donate' ),
			'parent_item'       => __( 'Parent Tag', 'tp-donate' ),
			'parent_item_colon' => __( 'Parent Tag:', 'tp-donate' ),
			'edit_item'         => __( 'Edit Tag', 'tp-donate' ),
			'update_item'       => __( 'Update Tag', 'tp-donate' ),
			'add_new_item'      => __( 'Add New Tag', 'tp-donate' ),
			'new_item_name'     => __( 'New Tag', 'tp-donate' ),
			'menu_name'         => __( 'Tags', 'tp-donate' )
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => _x( 'campaign-tag', 'URL slug', 'tp-donate' ) ),
		);

		$args = apply_filters( 'donate_register_tax_capaign_tag', $args );
		register_taxonomy( 'dn_campaign_tag', array( 'dn_campaign' ), $args );
	}

	public function register_post_status()
	{
		/**
		 * cancelled payment
		 */
		$args = apply_filters( 'donate_register_post_status_cancel', array(
			'label'                     => _x( 'Cancelled', 'Donate Status', 'tp-donate' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>' ),
		) );
		register_post_status( 'donate-cancelled', $args );
		/**
		 * pending payment
		 */
		$args = apply_filters( 'donate_register_post_status_pending', array(
			'label'                     => _x( 'Pending', 'Donate Status', 'tp-donate' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>' ),
		) );
		register_post_status( 'donate-pending', $args );

		/**
		 * processing payment
		 */
		$args = apply_filters( 'donate_register_post_status_processing', array(
			'label'                     => _x( 'Processing', 'Donate Status', 'tp-donate' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Processing <span class="count">(%s)</span>', 'Processing <span class="count">(%s)</span>' ),
		) );
		register_post_status( 'donate-processing', $args );

		/**
		 * completed payment
		 */
		$args = apply_filters( 'donate_register_post_status_completed', array(
			'label'                     => _x( 'Completed', 'Donate Status', 'tp-donate' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>' ),
		) );
		register_post_status( 'donate-completed', $args );
	}

}

new DN_Post_Type();