<?php

if ( !defined( 'ABSPATH' ) )
    exit();

class DN_Template_Underscore {

    function __construct() {

        /**
         * load form
         */
        add_action( 'wp_footer', array( $this, 'campaign_form' ) );
    }

    /**
     * form
     * @return js template
     */
    function campaign_form() {
        donate_get_template( 'donate-form.php' );
    }

}

new DN_Template_Underscore();
