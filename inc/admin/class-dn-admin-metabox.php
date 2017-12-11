<?php
/**
 * Fundpress Admin meta box class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Admin_Metabox' ) ) {
	/**
	 * Class DN_Admin_Metabox.
	 */
	class DN_Admin_Metabox {

		/**
		 * Init.
		 */
		public static function init() {
			add_action( 'admin_init', array( __CLASS__, 'add_meta_boxes' ) );
			add_action( 'admin_init', array( __CLASS__, 'remove_meta_box' ) );

			add_action( 'save_post', array( __CLASS__, 'save_post' ), 10, 3 );
		}

		/**
		 * Add meta boxes.
		 */
		public static function add_meta_boxes() {
			global $donate_meta_boxes;
			$donate_meta_boxes   = array();
			$donate_meta_boxes[] = new DN_MetaBox_Campaign();
			$donate_meta_boxes[] = new DN_MetaBox_Donate();
			$donate_meta_boxes[] = new DN_MetaBox_Donate_Action();
			$donate_meta_boxes[] = new DN_MetaBox_Donate_Note();
		}

		/**
		 * Remove meta boxes.
		 */
		public static function remove_meta_box() {
			/* remove submit div donate post type */
			remove_meta_box( 'submitdiv', 'dn_donate', 'side' );
		}

		/**
		 * Save post.
		 *
		 * @param $post_id
		 * @param $post
		 * @param $update
		 */
		public static function save_post( $post_id, $post, $update ) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				return;
			}
			if ( ! isset( $_POST ) ) {
				return;
			}

			if ( ! isset( $_POST['thimpress_donate_metabox'] ) || ! wp_verify_nonce( $_POST['thimpress_donate_metabox'], 'thimpress_donate' ) ) {
				return;
			}

			do_action( 'donate_process_update_' . $post->post_type . '_meta', $post_id, $post, $update );
		}
	}
}

DN_Admin_Metabox::init();
