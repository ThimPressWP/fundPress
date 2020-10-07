<?php
/**
 * Fundpress ajax class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Ajax' ) ) {
	/**
	 * Class DN_Ajax.
	 */
	class DN_Ajax {

		/**
		 * DN_Ajax constructor.
		 */
		public function __construct() {

			if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
				return;
			}

			$actions = array(
				'donate_load_form'         => true,
				'donate_submit'            => true,
				'donate_remove_compensate' => true,
				'donate_action_status'     => true,
			);

			foreach ( $actions as $action => $nopriv ) {
				if ( ! method_exists( $this, $action ) ) {
					return;
				}

				add_action( 'wp_ajax_' . $action, array( $this, $action ) );
				if ( $nopriv ) {
					if ( $action == 'donate_remove_compensate' ) {
						add_action( 'wp_ajax_nopriv_' . $action, array( $this, 'mustLogin' ) );
					}
					add_action( 'wp_ajax_nopriv_' . $action, array( $this, $action ) );
				}
			}
		}

		/**
		 * Load donate form.
		 */
		public function donate_load_form() {
			if ( ! isset( $_GET['schema'] ) || DN_Helpper::DN_sanitize_params_submitted( $_GET['schema'] ) !== 'donate-ajax' || empty( $_POST ) ) {
				return;
			}

			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( DN_Helpper::DN_sanitize_params_submitted( $_POST['nonce'] ), 'thimpress_donate_nonce' ) ) {
				return;
			}

			// Load form to donate for campaign
			if ( isset( $_POST['campaign_id'] ) && is_numeric( $_POST['campaign_id'] ) ) {
				$campaign = get_post( (int) $_POST['campaign_id'] );

				if ( ! $campaign || $campaign->post_type !== 'dn_campaign' ) {
					wp_send_json( array(
						'status'  => 'failed',
						'message' => __( 'Campaign is not exists in our system.', 'fundpress' )
					) );
				}

				$campaign = DN_Campaign::instance( $campaign );

				$shortcode = '[donate_form';
				$shortcode .= $campaign->id ? ' campaign_id="' . $campaign->id . '"' : '';
				$shortcode .= $campaign->id ? ' title="' . get_the_title( $campaign->id ) . '"' : '';
				// load payments when checkout on light box setting isset yes
				$shortcode .= FP()->settings->checkout->get( 'lightbox_checkout', 'no' ) == 'yes' ? ' payment="1"' : '';
				$shortcode .= ']';
			} else {
				// load form to donate system
				$shortcode = '[donate_form payment="1"]';
			}

			$shortcode = apply_filters( 'donate_load_form_donate_results', $shortcode, $_POST );

			ob_start();
			echo do_shortcode( $shortcode );
			$html = ob_get_clean();
			printf( $html );
			die();
		}

		/**
		 * Submit donate form.
		 */
		public function donate_submit() {
			// validate sanitize input $_POST
			if ( ! isset( $_GET['schema'] ) || DN_Helpper::DN_sanitize_params_submitted( $_GET['schema'] ) !== 'donate-ajax' || empty( $_POST ) ) {
				wp_send_json( array(
					'status'  => 'failed',
					'message' => array( __( 'Could not do action.', 'fundpress' ) )
				) );
			}

			// process checkout
			FP()->checkout->process_checkout();
			die( 0 );
		}

		/**
		 * Remove campaign compensate.
		 */
		public function donate_remove_compensate() {
			if ( ! isset( $_GET['schema'] ) || DN_Helpper::DN_sanitize_params_submitted( $_GET['schema'] ) !== 'donate-ajax' || empty( $_POST ) ) {
				return;
			}

			if ( ! isset( $_POST['compensate_id'] ) || ! isset( $_POST['post_id'] ) ) {
				return;
			}

			$post_id = ! empty( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
			$marker  = get_post_meta( $post_id, TP_DONATE_META_CAMPAIGN . 'marker', true );

			if ( empty( $marker ) ) {
				wp_send_json( array( 'status' => 'success' ) );
				die();
			}

			if ( isset( $marker[ $_POST['compensate_id'] ] ) ) {
				unset( $marker[ $_POST['compensate_id'] ] );
			} else {
				wp_send_json( array( 'status' => 'success' ) );
				die();
			}

			if ( $update = update_post_meta( $post_id, TP_DONATE_META_CAMPAIGN . 'marker', $marker ) ) {
				wp_send_json( array( 'status' => 'success' ) );
				die();
			}

			wp_send_json( array(
				'status'  => 'failed',
				'message' => __( 'Could not delete compensate. Please try again.', 'fundpress' )
			) );
			die();
		}

		/**
		 * must login
		 * @return null
		 */
		public function mustLogin() {
			_e( 'You must login', 'fundpress' );
		}

		/**
		 * Update order donate status.
		 */
		public function donate_action_status() {
			if ( ! isset( $_GET['schema'] ) || DN_Helpper::DN_sanitize_params_submitted( $_GET['schema'] ) !== 'donate-ajax' || empty( $_POST ) ) {
				return;
			}

			if ( ! isset( $_POST['donate_id'] ) || ! isset( $_POST['status'] ) ) {
				return;
			}

			$donate_id = ( isset( $_POST['donate_id'] ) ) ? absint( $_POST['donate_id'] ) : '';
			$status    = ( isset( $_POST['status'] ) ) ? DN_Helpper::DN_sanitize_params_submitted( $_POST['status'] ) : '';

			$donate = DN_Donate::instance( $donate_id );

			if ( $donate ) {
				$donate->update_status( 'donate-' . $status . '' );
				wp_send_json( array( 'status' => 'success', 'action' => $status ) );
				die();
			}

			wp_send_json( array(
				'status'  => 'failed',
				'message' => __( 'Could not change status of Donate. Please try again.', 'fundpress' )
			) );
			die();

		}
	}
}

new DN_Ajax();
