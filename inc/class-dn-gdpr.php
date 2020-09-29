<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class DN_GDPR
 */
class DN_GDPR {

	/**
	 * DN_GDPR constructor.
	 */
	public function __construct() {
		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_donate_personal_data_exporter' ) );
		add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_donate_personal_data_eraser' ) );
	}

	/**
	 * @param $exporters
	 *
	 * @return mixed
	 */
	public function register_donate_personal_data_exporter( $exporters ) {
		$exporters['dn-donate'] = array(
			'exporter_friendly_name' => __( 'Fundpress Donate', 'fundpress' ),
			'callback'               => array( $this, 'exporter_personal_data' )
		);

		return $exporters;
	}

	/**
	 * @param $erasers
	 *
	 * @return mixed
	 */
	public function register_donate_personal_data_eraser( $erasers ) {
		$erasers['dn-donate'] = array(
			'eraser_friendly_name' => __( 'Fundpress Donate', 'fundpress' ),
			'callback'             => array( $this, 'eraser_personal_data' )
		);

		return $erasers;
	}

	/**
	 * @param $email_address
	 * @param int $page
	 *
	 * @return array
	 */
	public function exporter_personal_data( $email_address, $page = 1 ) {

		$data_to_export = array();

		$user = get_user_by( 'email', $email_address );
		if ( false === $user ) {
			return array(
				'data' => $data_to_export,
				'done' => true,
			);
		}

		$donates = $this->_query_donate( $user->ID );

		foreach ( $donates as $donate_id ) {
			$donate = DN_Donate::instance( $donate_id );
			$donor  = DN_Donor::instance( $donate->donor_id );

			$customer_details = $donor->get_fullname() . ' - Email: ' . $donor->email . ' - Phone: ' . $donor->phone . ' - Address: ' . $donor->address;

			$data  = __( 'No item', 'fundpress' );
			$items = $donate->get_items();
			foreach ( $items as $item ) {
				$data .= get_the_title( $item->campaign_id ) . ' (' . donate_get_currency_symbol( $donate->currency ) . $item->total . ') ' . "\n";
			}

			$post_data_to_export = array(
				array( 'name' => __( 'ID', 'fundpress' ), 'value' => '#' . $donate_id ),
				array(
					'name'  => __( 'Created Date', 'fundpress' ),
					'value' => get_the_date( get_option( 'date_format' ), $donate_id )
				),
				array(
					'name'  => __( 'Customer Details', 'fundpress' ),
					'value' => $customer_details
				),
				array( 'name' => __( 'Items', 'fundpress' ), 'value' => nl2br( $data ) ),
				array(
					'name'  => __( 'Total', 'fundpress' ),
					'value' => $donate->total
				),
				array(
					'name'  => __( 'Type', 'fundpress' ),
					'value' => $donate->type == 'system' ? __( 'System', 'fundpress' ) : __( 'Campaign', 'fundpress' )
				),
				array(
					'name'  => __( 'Status', 'fundpress' ),
					'value' => get_post_status( $donate_id )
				),
			);

			$data_to_export[] = array(
				'group_id'    => 'dn_donate',
				'group_label' => __( 'Donate', 'fundpress' ),
				'item_id'     => "post-{$donate_id}",
				'data'        => $post_data_to_export,
			);
		}

		return array(
			'data' => $data_to_export,
			'done' => true,
		);
	}

	/**
	 * @param $email_address
	 * @param int $page
	 *
	 * @return array
	 */
	public function eraser_personal_data( $email_address, $page = 1 ) {
		$eraser_data = array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => 1,
		);

		if ( ! $user = get_user_by( 'email', $email_address ) ) {
			return $eraser_data;
		}

		$donors = $this->_query_donor( $email_address );
		if ( $donors ) {
			foreach ( $donors as $donor_id ) {
				$this->_eraser_donor_data( $donor_id );
			}
		}

		$eraser_data['items_removed'] = true;

		return $eraser_data;
	}

	/**
	 * @param $user_id
	 *
	 * @return array|null|object
	 */
	private function _query_donate( $user_id ) {

		if ( ! $user_id ) {
			return array();
		}

		global $wpdb;

		$booking = array();
		$query   = $wpdb->get_results( $wpdb->prepare( "
				SELECT donate.ID FROM {$wpdb->prefix}posts AS donate 
				WHERE 
				donate.post_type = %s AND donate.post_author = %s", 'dn_donate', $user_id ), ARRAY_A );

		if ( $query ) {
			foreach ( $query as $item ) {
				$booking[] = $item['ID'];
			}
		}

		return $booking;
	}

	/**
	 * @param $user_email
	 *
	 * @return array
	 */
	private function _query_donor( $user_email ) {
		if ( ! $user_email ) {
			return array();
		}

		global $wpdb;

		$donor = array();
		$query = $wpdb->get_results( $wpdb->prepare( "
				SELECT donor.ID FROM {$wpdb->prefix}posts AS donor 
				WHERE 
				donor.post_type = %s AND donor.post_content = %s", 'dn_donor', $user_email ), ARRAY_A );

		if ( $query ) {
			foreach ( $query as $item ) {
				$donor[] = $item['ID'];
			}
		}

		return $donor;
	}

	/**
	 * @param $donor_id
	 */
	private function _eraser_donor_data( $donor_id ) {
		$data = array( 'first_name', 'last_name', 'email', 'phone', 'address' );

		$prefix = 'thimpress_donor_';
		foreach ( $data as $_data ) {
			update_post_meta( $donor_id, $prefix . $_data, '' );
		}
	}
}

new DN_GDPR();