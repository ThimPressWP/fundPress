<?php

if ( !defined( 'ABSPATH' ) ) {
    exit();
}

class DN_Admin_Metabox {

    public static function init() {
        /* remove metabox */
        add_action( 'admin_init', array( __CLASS__, 'remove_meta_box' ) );
        add_action( 'admin_init', array( __CLASS__, 'add_meta_boxes' ) );

        add_action( 'save_post', array( __CLASS__, 'save_post' ), 10, 3 );
    }

    /* add metaboxes */

    public static function add_meta_boxes() {
        global $donate_meta_boxes;
        $donate_meta_boxes = array();
        $donate_meta_boxes[] = new DN_MetaBox_Campaign();
        $donate_meta_boxes[] = new DN_MetaBox_Donate();
        $donate_meta_boxes[] = new DN_MetaBox_Donate_Action();
        $donate_meta_boxes[] = new DN_MetaBox_Donate_Note();
    }

    /* remove metaboxes */

    public static function remove_meta_box() {
        /* remove submit div donate post type */
        remove_meta_box( 'submitdiv', 'dn_donate', 'side' );
    }

    public static function save_post( $post_id, $post, $update ) {
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            return;
        }
        if ( !isset( $_POST ) )
            return;

        if ( !isset( $_POST['thimpress_donate_metabox'] ) || !wp_verify_nonce( $_POST['thimpress_donate_metabox'], 'thimpress_donate' ) )
            return;

        do_action( 'donate_process_update_' . $post->post_type . '_meta', $post_id, $post, $update );
    }

}

DN_Admin_Metabox::init();
