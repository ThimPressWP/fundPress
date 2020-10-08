<?php
/**
 * Fundpress Custom post types class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Post_Type' ) ) {
	/**
	 * Class DN_Post_Type
	 */
	class DN_Post_Type {

		/**
		 * DN_Post_Type constructor.
		 */
		public function __construct() {

			// register campaign, donate, donor
			add_action( 'init', array( $this, 'register_post_types' ) );
			// register taxonomy
			add_action( 'init', array( $this, 'register_taxonomies' ) );
			// post post status
			add_action( 'init', array( $this, 'register_status' ) );

			// custom post type admin column
			add_filter( 'manage_dn_campaign_posts_columns', array( $this, 'campaign_columns' ) );
			add_action( 'manage_dn_campaign_posts_custom_column', array( $this, 'campaign_column_content' ), 10, 2 );
			add_filter( 'manage_edit-dn_campaign_sortable_columns', array( $this, 'campaign_sortable_columns' ) );

			add_filter( 'manage_dn_donate_posts_columns', array( $this, 'donate_columns' ) );
			add_action( 'manage_dn_donate_posts_custom_column', array( $this, 'donate_column_content' ), 10, 2 );
			add_filter( 'manage_edit-dn_donate_sortable_columns', array( $this, 'donate_sortable_columns' ) );

			add_filter( 'manage_dn_donor_posts_columns', array( $this, 'donor_columns' ) );
			add_action( 'manage_dn_donor_posts_custom_column', array( $this, 'donor_column_content' ), 10, 2 );
			add_filter( 'manage_edit-dn_donor_sortable_columns', array( $this, 'donor_sortable_columns' ) );

			/* sortable order donate column */
			add_filter( 'request', array( $this, 'request_query' ) );

			/* cmb2 */
			add_action( 'cmb2_init', array( $this, 'donor_meta_info' ) );
		}

		/**
		 * Register Fundpress post types.
		 *
		 * @since 2.0
		 */
		public function register_post_types() {
			// register campaign
			$args = array(
				'labels'             => array(
					'name'               => _x( 'Campaigns', 'post type general name', 'fundpress' ),
					'singular_name'      => _x( 'Campaign', 'post type singular name', 'fundpress' ),
					'menu_name'          => _x( 'Campaigns', 'admin menu', 'fundpress' ),
					'name_admin_bar'     => _x( 'Campaign', 'add new on admin bar', 'fundpress' ),
					'add_new'            => _x( 'Add Campaign', 'add new on admin bar', 'fundpress' ),
					'add_new_item'       => __( 'Add New Campaign', 'fundpress' ),
					'new_item'           => __( 'New Campaign', 'fundpress' ),
					'edit_item'          => __( 'Edit Campaign', 'fundpress' ),
					'view_item'          => __( 'View Campaign', 'fundpress' ),
					'all_items'          => __( 'Campaigns', 'fundpress' ),
					'search_items'       => __( 'Search Campaigns', 'fundpress' ),
					'parent_item_colon'  => __( 'Parent Campaigns:', 'fundpress' ),
					'not_found'          => __( 'No campaign found.', 'fundpress' ),
					'not_found_in_trash' => __( 'No campaign found in Trash.', 'fundpress' )
				),
				'description'        => __( 'Campaigns', 'fundpress' ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'campaigns' ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => 9,
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
			);

			$args = apply_filters( 'donate_register_post_type_campaign', $args );
			register_post_type( 'dn_campaign', $args );

			// register donate
			$args = array(
				'labels'             => array(
					'name'               => _x( 'Donates', 'post type general name', 'fundpress' ),
					'singular_name'      => _x( 'Donate', 'post type singular name', 'fundpress' ),
					'menu_name'          => _x( 'Donates', 'add new on admin bar', 'fundpress' ),
					'name_admin_bar'     => _x( 'Donate', 'admin menu', 'fundpress' ),
					'add_new'            => _x( 'Add Donate', 'dn_donate', 'fundpress' ),
					'add_new_item'       => __( 'Add New Donate', 'fundpress' ),
					'new_item'           => __( 'New Donate', 'fundpress' ),
					'edit_item'          => __( 'Edit Donate', 'fundpress' ),
					'view_item'          => __( 'View Donate', 'fundpress' ),
					'all_items'          => __( 'Donates', 'fundpress' ),
					'search_items'       => __( 'Search Donates', 'fundpress' ),
					'parent_item_colon'  => __( 'Parent Donates:', 'fundpress' ),
					'not_found'          => __( 'No donates found.', 'fundpress' ),
					'not_found_in_trash' => __( 'No donates found in Trash.', 'fundpress' )
				),
				'description'        => __( 'Donates', 'fundpress' ),
				'public'             => false,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => 'tp_donate',
				'query_var'          => true,
				'rewrite'            => array( 'slug' => _x( 'donates', 'URL slug', 'fundpress' ) ),
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => false,
				'capabilities'       => array(),
				'map_meta_cap'       => true
			);

			$args = apply_filters( 'donate_register_post_type_donate', $args );
			register_post_type( 'dn_donate', $args );

			// register donate item
			$args = array(
				'labels'             => array(
					'name' => _x( 'Donate Item', 'post type general name', 'fundpress' ),
				),
				'description'        => __( 'Donate Item', 'fundpress' ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => false,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => _x( 'donate-item', 'URL slug', 'fundpress' ) ),
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'author' ),
				'capabilities'       => array(
					'create_posts' => false,
				),
				'map_meta_cap'       => true
			);

			$args = apply_filters( 'donate_register_post_type_donate_item', $args );
			register_post_type( 'dn_donate_item', $args );

			// register donor
			$args = array(
				'labels'             => array(
					'name'               => _x( 'Donors', 'post type general name', 'fundpress' ),
					'singular_name'      => _x( 'Donor', 'post type singular name', 'fundpress' ),
					'menu_name'          => _x( 'Donors', 'admin menu', 'fundpress' ),
					'name_admin_bar'     => _x( 'Donor', 'add new on admin bar', 'fundpress' ),
					'add_new'            => _x( 'Add Donor', 'dn_donor', 'fundpress' ),
					'add_new_item'       => __( 'Add New Donor', 'fundpress' ),
					'new_item'           => __( 'New Donor', 'fundpress' ),
					'edit_item'          => __( 'Edit Donor', 'fundpress' ),
					'view_item'          => __( 'View Donor', 'fundpress' ),
					'all_items'          => __( 'Donors', 'fundpress' ),
					'search_items'       => __( 'Search Donors', 'fundpress' ),
					'parent_item_colon'  => __( 'Parent Donors:', 'fundpress' ),
					'not_found'          => __( 'No donors found.', 'fundpress' ),
					'not_found_in_trash' => __( 'No donors found in Trash.', 'fundpress' )
				),
				'description'        => __( 'Donors', 'fundpress' ),
				'public'             => false,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => 'tp_donate',
				'query_var'          => true,
				'rewrite'            => array( 'slug' => _x( 'donors', 'URL slug', 'fundpress' ) ),
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => false,
				'capabilities'       => array(
					'create_posts' => false
				),
				'map_meta_cap'       => true
			);

			$args = apply_filters( 'donate_register_post_type_donor', $args );
			register_post_type( 'dn_donor', $args );
		}

		/**
		 * Register Fundpress categories.
		 *
		 * @since 2.0
		 */
		public function register_taxonomies() {
			// campaign category

			$args = array(
				'hierarchical'      => true,
				'labels'            => array(
					'name'              => _x( 'Categories', 'taxonomy general name', 'fundpress' ),
					'singular_name'     => _x( 'Category', 'taxonomy singular name', 'fundpress' ),
					'search_items'      => __( 'Search Campaigns', 'fundpress' ),
					'all_items'         => __( 'All Campaigns', 'fundpress' ),
					'parent_item'       => __( 'Parent Category', 'fundpress' ),
					'parent_item_colon' => __( 'Parent Category:', 'fundpress' ),
					'edit_item'         => __( 'Edit Category', 'fundpress' ),
					'update_item'       => __( 'Update Category', 'fundpress' ),
					'add_new_item'      => __( 'Add New Category', 'fundpress' ),
					'new_item_name'     => __( 'New Category', 'fundpress' ),
					'menu_name'         => __( 'Categories', 'fundpress' )
				),
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => _x( 'campaign-cat', 'URL slug', 'fundpress' ) ),
			);

			$args = apply_filters( 'donate_register_tax_capaign_cat', $args );
			register_taxonomy( 'dn_campaign_cat', array( 'dn_campaign' ), $args );

			// campaign tag
			$args = array(
				'hierarchical'      => false,
				'labels'            => array(
					'name'              => _x( 'Tags', 'taxonomy general name', 'fundpress' ),
					'singular_name'     => _x( 'Tag', 'taxonomy singular name', 'fundpress' ),
					'search_items'      => __( 'Search Tag', 'fundpress' ),
					'all_items'         => __( 'All Tags', 'fundpress' ),
					'parent_item'       => __( 'Parent Tag', 'fundpress' ),
					'parent_item_colon' => __( 'Parent Tag:', 'fundpress' ),
					'edit_item'         => __( 'Edit Tag', 'fundpress' ),
					'update_item'       => __( 'Update Tag', 'fundpress' ),
					'add_new_item'      => __( 'Add New Tag', 'fundpress' ),
					'new_item_name'     => __( 'New Tag', 'fundpress' ),
					'menu_name'         => __( 'Tags', 'fundpress' )
				),
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => _x( 'campaign-tag', 'URL slug', 'fundpress' ) ),
			);

			$args = apply_filters( 'donate_register_tax_capaign_tag', $args );
			register_taxonomy( 'dn_campaign_tag', array( 'dn_campaign' ), $args );
		}

		/**
		 * Register Fundpress post status.
		 */
		public function register_status() {
			global $donate_statuses;

			// cancelled payment
			$donate_statuses['donate-cancelled'] = apply_filters( 'donate_register_post_status_cancel', array(
				'label'                     => _x( 'Cancelled', 'Donate Status', 'fundpress' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>' ),
			) );

			// pending payment
			$donate_statuses['donate-pending'] = apply_filters( 'donate_register_post_status_pending', array(
				'label'                     => _x( 'Pending', 'Donate Status', 'fundpress' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>' ),
			) );

			// processing payment
			$donate_statuses['donate-processing'] = apply_filters( 'donate_register_post_status_processing', array(
				'label'                     => _x( 'Processing', 'Donate Status', 'fundpress' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Processing <span class="count">(%s)</span>', 'Processing <span class="count">(%s)</span>' ),
			) );

			// completed payment
			$donate_statuses['donate-completed'] = apply_filters( 'donate_register_post_status_completed', array(
				'label'                     => _x( 'Completed', 'Donate Status', 'fundpress' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>' ),
			) );

			// refunded payment
			$donate_statuses['donate-refunded'] = apply_filters( 'donate_register_post_status_refunded', array(
				'label'                     => _x( 'Refunded', 'Donate Status', 'fundpress' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Refunded <span class="count">(%s)</span>', 'Refunded <span class="count">(%s)</span>' ),
			) );

			// allow filter donate statuses
			$donate_statuses = apply_filters( 'donate_payment_status', $donate_statuses );

			// register donate statuses
			foreach ( $donate_statuses as $status => $args ) {
				register_post_status( $status, $args );
			}
		}

		/**
		 * Campaign post type admin columns.
		 *
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function campaign_columns( $columns ) {
			unset( $columns['date'], $columns['comments'], $columns['author'] );
			$columns['start']  = apply_filters( 'donate_add_column_campaign_start_column', __( 'Start', 'fundpress' ) );
			$columns['end']    = apply_filters( 'donate_add_column_campaign_end_column', __( 'End', 'fundpress' ) );
			$columns['funded'] = apply_filters( 'donate_add_column_campaign_publish_column', __( 'Founded', 'fundpress' ) );
			$columns['date']   = apply_filters( 'donate_add_column_campaign_publish_column', __( 'Created Date', 'fundpress' ) );

			return $columns;
		}

		/**
		 * Campaign post type admin column content.
		 *
		 * @param $column
		 * @param $post_id
		 */
		public function campaign_column_content( $column, $post_id ) {
			$campaign = DN_Campaign::instance( $post_id );
			$html     = '';
			switch ( $column ) {
				case 'start':
					$html = $campaign->start ? sprintf( '%s', date_i18n( get_option( 'date_format', 'Y-m-d' ), strtotime( $campaign->start ) ) ) : '—';
					break;
				case 'end':
					$html = $campaign->end ? sprintf( '%s', date_i18n( get_option( 'date_format', 'Y-m-d' ), strtotime( $campaign->end ) ) ) : '—';
					break;
				case 'funded':
					$html = sprintf( '%s', donate_get_campaign_percent() . '%' );
					break;
				case 'donors':
					$html = donate_get_donors( $post_id );
					break;
				default:
					break;
			}
			echo sprintf( '%s', $html );
		}

		/**
		 * Add sortable column link order campaign.
		 *
		 * @param $columns
		 *
		 * @return array
		 */
		public function campaign_sortable_columns( $columns ) {
			$custom = array(
				'start' => 'start',
				'end'   => 'end'
			);

			return wp_parse_args( $custom, $columns );
		}

		/**
		 * Donate post type admin columns.
		 *
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function donate_columns( $columns ) {
			unset( $columns['title'], $columns['author'], $columns['date'] );
			$columns['donate_title']          = apply_filters( 'donate_add_column_donate_title', __( 'Donate', 'fundpress' ) );
			$columns['donate_user']           = apply_filters( 'donate_add_column_donate_user', __( 'User', 'fundpress' ) );
			$columns['donate_type']           = apply_filters( 'donate_add_column_donate_type', __( 'Donate For', 'fundpress' ) );
			$columns['donate_date']           = apply_filters( 'donate_add_column_donate_date', __( 'Date', 'fundpress' ) );
			$columns['donate_total']          = apply_filters( 'donate_add_column_donate_total', __( 'Total', 'fundpress' ) );
			$columns['donate_payment_method'] = apply_filters( 'donate_add_column_donate_payment_method', __( 'Method', 'fundpress' ) );
			$columns['donate_status']         = apply_filters( 'donate_add_column_donate_status', __( 'Status', 'fundpress' ) );

			return $columns;
		}

		/**
		 * Donate post type admin column content.
		 *
		 * @param $column
		 * @param $post_id
		 */
		public function donate_column_content( $column, $post_id ) {
			$donate = DN_Donate::instance( $post_id );
			switch ( $column ) {
				case 'donate_title':
					$title = '<a href="' . get_edit_post_link( $post_id ) . '">';
					$title .= '<strong>#' . $post_id . '</strong>';
					$title .= '</a>';
					printf( __( '%s <small>by</small> %s', 'fundpress' ), $title, '<a href="' . get_edit_post_link( $donate->donor_id ) . '"><strong>' . donate_get_donor_fullname( $post_id ) . '</strong></a>' );
					break;
				case 'donate_date':
					printf( '%s', date_i18n( get_option( 'date_format' ), strtotime( get_post_field( 'post_date', $post_id ) ) ) );
					break;
				case 'donate_total':
					$total = $donate->total ? $donate->total : 0;
					printf( '%s', donate_price( $total ) );
					break;
				case 'donate_user':
					if ( $donate->user_id ) {
						$user = get_userdata( $donate->user_id );
						printf( '<a href="%s">%s</a>', get_edit_user_link( $donate->user_id ), $user->user_login );
					} else {
						_e( 'Guest', 'fundpress' );
					}
					break;
				case 'donate_type':
					if ( $donate->type === 'system' ) {
						_e( 'System', 'fundpress' );
					} else {
						_e( 'Campaign', 'fundpress' );
					}
					break;
				case 'donate_payment_method':
					$payment         = $donate->payment_method ? $donate->payment_method : '';
					$payments_enable = donate_payment_gateways();
					if ( array_key_exists( $payment, $payments_enable ) ) {
						echo $payments_enable[ $payment ]->_title;
					} else {
						echo '—';
					}
					break;
				case 'donate_status':
					echo donate_get_status_label( $post_id );
					break;
				default:
					break;
			}
		}

		/**
		 * Add sortable column link order donate.
		 *
		 * @param $columns
		 *
		 * @return array
		 */
		public function donate_sortable_columns( $columns ) {
			$custom = array(
				'donate_title' => 'ID',
				'donate_total' => 'donate_total',
				'donate_date'  => 'date'
			);
			unset( $columns['comments'] );

			return wp_parse_args( $custom, $columns );
		}

		/**
		 * Donor post type admin columns.
		 *
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function donor_columns( $columns ) {
			unset( $columns['title'], $columns['author'], $columns['date'] );

			$columns['donor_fullname'] = __( 'Full Name', 'fundpress' );
			$columns['donor_email']    = __( 'Email', 'fundpress' );
			$columns['donor_address']  = __( 'Address', 'fundpress' );
			$columns['donor_phone']    = __( 'Phone', 'fundpress' );
			$columns['date']           = __( 'Date', 'fundpress' );

			return $columns;
		}

		/**
		 * Donor post type admin column content.
		 *
		 * @param $column
		 * @param $post_id
		 */
		public function donor_column_content( $column, $post_id ) {
			$donor = DN_Donor::instance( $post_id );
			switch ( $column ) {
				case 'donor_email':
					printf( '<a href="mailto:%1$s">%1$s</a>', $donor->email );
					break;
				case 'donor_fullname':
					printf( '<a href="%s">%s</a>', get_edit_post_link( $donor->id ), $donor->get_fullname() );
					break;
				case 'donor_address':
					printf( '%s', $donor->address );
					break;
				case 'donor_phone':
					printf( '%s', $donor->phone );
					break;
				default:
					break;
			}
		}

		/**
		 * Add sortable column link order donor.
		 *
		 * @param $columns
		 *
		 * @return array
		 */
		public function donor_sortable_columns( $columns ) {
			$custom = array(
				'donor_fullname' => 'full_name',
				'donor_email'    => 'email',
				'donor_phone'    => 'phone'
			);
			unset( $columns['comments'] );

			return wp_parse_args( $custom, $columns );
		}

		/**
		 * Sortable order donate column.
		 *
		 * @param $vars
		 *
		 * @return array
		 */
		public function request_query( $vars ) {

			if ( ! is_admin() || ! isset( $_GET['post_type'] ) || ! in_array( DN_Helpper::DN_sanitize_params_submitted( $_GET['post_type'] ), array(
					'dn_donate',
					'dn_donor'
				) ) ) {
				return $vars;
			}
			$post_type = DN_Helpper::DN_sanitize_params_submitted( $_GET['post_type'] );
			if ( ! isset( $_GET['orderby'] ) || ! isset( $_GET['order'] ) ) {
				return $vars;
			}

			$orderBy = DN_Helpper::DN_sanitize_params_submitted( $_GET['orderby'] );

			if ( $post_type === 'dn_donate' ) {
				if ( $orderBy === 'donate_total' ) {
					$vars = array_merge( $vars, array(
						'meta_key' => TP_DONATE_META_DONATE . 'total',
						'orderby'  => 'meta_value'
					) );
				}
			}

			if ( $post_type === 'dn_donor' ) {
				if ( $orderBy === 'full_name' ) {
					$vars = array_merge( $vars, array(
						'meta_key' => TP_DONATE_META_DONOR . 'first_name',
						'orderby'  => 'meta_value',
						'order'    => DN_Helpper::DN_sanitize_params_submitted( $_GET['order'] )
					) );
				}
				if ( $orderBy === 'email' ) {
					$vars = array_merge( $vars, array(
						'meta_key' => TP_DONATE_META_DONOR . 'email',
						'orderby'  => 'meta_value',
						'order'    => DN_Helpper::DN_sanitize_params_submitted( $_GET['order'] )
					) );
				}
				if ( $orderBy === 'phone' ) {
					$vars = array_merge( $vars, array(
						'meta_key' => TP_DONATE_META_DONOR . 'phone',
						'orderby'  => 'meta_value',
						'order'    => DN_Helpper::DN_sanitize_params_submitted( $_GET['order'] )
					) );
				}
			}

			if ( $post_type === 'dn_campaign' ) {
				if ( $orderBy === 'start' ) {
					$vars = array_merge( $vars, array(
						'meta_key' => TP_DONATE_META_DONATE . 'start',
						'orderby'  => 'meta_value',
						'order'    => DN_Helpper::DN_sanitize_params_submitted( $_GET['order'] )
					) );
				}
				if ( $orderBy === 'end' ) {
					$vars = array_merge( $vars, array(
						'meta_key' => TP_DONATE_META_DONATE . 'end',
						'orderby'  => 'meta_value',
						'order'    => DN_Helpper::DN_sanitize_params_submitted( $_GET['order'] )
					) );
				}
			}

			return $vars;
		}

		/**
		 * Donor information meta box.
		 *
		 * @since 2.0
		 */
		public function donor_meta_info() {
			$prefix = TP_DONATE_META_DONOR;

			$cmb = new_cmb2_box( array(
				'id'           => 'donor_info',
				'title'        => __( 'Donor Info', 'fundpress' ),
				'object_types' => array( 'dn_donor' ), // post type
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true
			) );

			$cmb->add_field( array(
				'name' => sprintf( __( 'Donor ID #%s', 'fundpress' ), $cmb->object_id ),
				'type' => 'title',
				'id'   => 'wiki_test_title'
			) );

			$cmb->add_field( array(
				'name' => __( 'First Name', 'fundpress' ),
				'desc' => __( 'Enter First Name (required)', 'fundpress' ),
				'id'   => $prefix . 'first_name',
				'type' => 'text'
			) );

			$cmb->add_field( array(
				'name' => __( 'Last Name', 'fundpress' ),
				'desc' => __( 'Enter Last Name (required)', 'fundpress' ),
				'id'   => $prefix . 'last_name',
				'type' => 'text'
			) );

			$cmb->add_field( array(
				'name' => __( 'Email', 'fundpress' ),
				'desc' => __( 'Enter Email (required)', 'fundpress' ),
				'id'   => $prefix . 'email',
				'type' => 'text_email'
			) );

			$cmb->add_field( array(
				'name' => __( 'Address', 'fundpress' ),
				'desc' => __( 'Enter Address (required)', 'fundpress' ),
				'id'   => $prefix . 'address',
				'type' => 'textarea_small'
			) );

			$cmb->add_field( array(
				'name' => __( 'Phone Num.', 'fundpress' ),
				'desc' => __( 'Enter Phone Number (required)', 'fundpress' ),
				'id'   => $prefix . 'phone',
				'type' => 'text'
			) );
		}

	}
}

new DN_Post_Type();
