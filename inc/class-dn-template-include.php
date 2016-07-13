<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

class DN_Template {

    /**
     * Path to the includes directory
     * @var string
     */
    private $include_path = '';

    /**
     * The Constructor
     */
    public function __construct() {
        // template include
        add_filter( 'template_include', array( $this, 'template_loader' ) );
    }

    /**
     * filter template
     * @param  [type] $template [description]
     * @return [type]           [description]
     */
    public function template_loader( $template ) {
        $post_type = get_post_type();

        $file = '';
        $find = array();
        if ( $post_type !== 'dn_campaign' )
            return $template;

        if ( is_post_type_archive( 'dn_campaign' ) ) {
            $file = 'archive-campaign.php';
            $find[] = $file;
            $find[] = donate_template_path() . '/' . $file;
        } else if ( is_single() ) {
            $file = 'single-campaign.php';
            $find[] = $file;
            $find[] = donate_template_path() . '/' . $file;
        } else if ( is_tax( 'dn_campaign_cat' ) || is_tax( 'dn_campaign_tag' ) ) {
            $term = get_queried_object();

            $taxonomy = str_replace( 'dn_', '', $term->taxonomy );

            $file = 'taxonomy-' . $taxonomy . '.php';

            $find[] = 'taxonomy-' . $taxonomy . '-' . $term->slug . '.php';
            $find[] = donate_template_path() . '/' . 'taxonomy-' . $taxonomy . '-' . $term->slug . '.php';
            $find[] = $file;
            $find[] = donate_template_path() . '/' . $file;
        }

        if ( $file ) {
            $find[] = donate_template_path() . $file;
            $hb_template = untrailingslashit( TP_DONATE_PATH ) . '/templates/' . $file;
            $template = locate_template( array_unique( $find ) );

            if ( !$template && file_exists( $hb_template ) ) {
                $template = $hb_template;
            }
        }

        return $template;
    }

}

new DN_Template();
