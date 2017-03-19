<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class DN_MetaBox_Donate extends DN_MetaBox_Base {

	/**
	 * id of the meta box
	 * @var null
	 */
	public $_id = null;

	/**
	 * title of meta box
	 * @var null
	 */
	public $_title = null;

	/**
	 * meta key prefix
	 * @var string
	 */
	public $_prefix = null;

	/**
	 * screen post, page, tp_donate
	 * @var array
	 */
	public $_screen = array( 'dn_donate' );

	/**
	 * array meta key
	 * @var array
	 */
	public $_name = array();

	public function __construct() {
		$this->_id     = 'donate_donate_info_section';
		$this->_title  = __( 'Donate Details', 'fundpress' );
		$this->_prefix = TP_DONATE_META_DONATE;
		$this->_layout = TP_DONATE_INC . '/admin/views/metaboxes/donate.php';
		parent::__construct();
		add_action( 'donate_process_update_dn_donate_meta', array( $this, 'update_donate' ), 20, 1 );
	}

	public function update_donate( $post_id ) {
		if ( !isset( $_POST['thimpress_donate_type'] ) ) {
			return;
		}

		$donate      = DN_Donate::instance( $post_id );
		$donate_type = sanitize_text_field( $_POST['thimpress_donate_type'] );

		/* donate type */
		update_post_meta( $post_id, 'thimpress_donate_type', $donate_type );
		/* donor */
		update_post_meta( $post_id, 'thimpress_donate_donor_id', isset( $_POST['thimpress_donate_donor_id'] ) ? esc_attr( absint( $_POST['thimpress_donate_donor_id'] ) ) : 0 );

		$total = 0;
		if ( $donate_type === 'system' || ( !isset( $_POST['donate_item'] ) || empty( $_POST['donate_item'] ) ) ) {
			$donate->remove_donate_items();
			$total = isset( $_POST['thimpress_donate_total'] ) ? esc_attr( floatval( $_POST['thimpress_donate_total'] ) ) : 0;
		} else if ( $donate_type === 'campaign' ) {
			foreach ( $_POST['donate_item'] as $item ) {
				if ( !isset( $item['campaign_id'] ) || !isset( $item['amount'] ) ) {
					continue;
				}
				if ( isset( $item['item_id'] ) && $item['item_id'] ) {
					$item_id     = sanitize_text_field( $item['item_id'] );
					$campaign_id = sanitize_text_field( $item['campaign_id'] );
					$amount      = sanitize_text_field( $item['amount'] );
					update_post_meta( $item_id, 'campaign_id', absint( $campaign_id ) );
					update_post_meta( $item_id, 'title', get_the_title( $campaign_id ) );
					update_post_meta( $item_id, 'total', floatval( $amount ) );
				} else {
					$donate->add_donate_item( $item['campaign_id'], get_the_title( $item['campaign_id'] ), floatval( $item['amount'] ) );
				}
				$total += floatval( $item['amount'] );
			}
		}

		/* total */
		update_post_meta( $post_id, 'thimpress_donate_total', $total );
	}

}
