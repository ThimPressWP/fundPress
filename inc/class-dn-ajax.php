<?php

if ( !defined( 'ABSPATH' ) ) {
    exit();
}

class DN_Ajax {

    public function __construct() {

        if ( !defined( 'DOING_AJAX' ) || !DOING_AJAX )
            return;

        $actions = array(
            'donate_load_form' => true,
            'donate_submit' => true
        );

        foreach ( $actions as $action => $nopriv ) {

            if ( !method_exists( $this, $action ) )
                return;

            add_action( 'wp_ajax_' . $action, array( $this, $action ) );
            if ( $nopriv ) {
                add_action( 'wp_ajax_nopriv_' . $action, array( $this, $action ) );
            }
        }
    }

    /**
     * ajax load form
     * @return
     */
    public function donate_load_form() {
        if ( !isset( $_GET['schema'] ) || $_GET['schema'] !== 'donate-ajax' || empty( $_POST ) )
            return;

        if ( !isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'thimpress_donate_nonce' ) )
            return;

        // Load form to donate for campaign
        if ( isset( $_POST['campaign_id'] ) && is_numeric( $_POST['campaign_id'] ) ) {
            
            $campaign = get_post( $_POST['campaign_id'] );

            if ( !$campaign || $campaign->post_type !== 'dn_campaign' ) {
                wp_send_json( array( 'status' => 'failed', 'message' => __( 'Campaign is not exists in our system.', 'tp-donate' ) ) );
            }

            $campaign = DN_Campaign::instance( $campaign );

            $shortcode = '[donate_form';
            $shortcode .= $campaign->id ? ' campaign_id="'.$campaign->id.'"' : '';
            $shortcode .= $campaign->id ? ' title="'.get_the_title($campaign->id).'"' : '';
            // load payments when checkout on lightbox setting isset yes
            $shortcode .= DN_Settings::instance()->checkout->get( 'lightbox_checkout', 'no' ) == 'yes' ? ' payment="1"' : '';
            $shortcode .= ']';
        } else { // Load form to donate for site
            $shortcode = '[donate_form';
            // load payments when checkout on lightbox setting isset yes
            $shortcode .= ' payment="1"';
            $shortcode .= ']';
        }

        $shortcode = apply_filters( 'donate_load_form_donate_results', $shortcode, $_POST );
        
        ob_start();
        echo do_shortcode($shortcode);
        $html = ob_get_clean();
        printf( $html ); die();
    }

    /**
     * donoate submit lightbox
     * @return
     */
    public function donate_submit() {
        // validate sanitize input $_POST
        if ( !isset( $_GET['schema'] ) || $_GET['schema'] !== 'donate-ajax' || empty( $_POST ) )
            wp_send_json( array( 'status' => 'failed', 'message' => array( __( 'Could not do action.', 'tp-donate' ) ) ) );

        /* process checkout */
        ThimPress_Donate::instance()->checkout->process_checkout();
        die( 0 );
    }

}

new DN_Ajax();
