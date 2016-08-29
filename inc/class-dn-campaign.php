<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

class DN_Campaign extends DN_Post_Base {

    /**
     * ID of Post
     * @var null
     */
    public $id = null;

    /**
     * post
     * @var null
     */
    public $post = null;

    /**
     * meta prefix of post type
     * @var null
     */
    public $meta_prefix = null;
    static $_instances = null;

    /**
     * post type
     * @var null
     */
    public $post_type = 'dn_campaign';

    public function __construct( $post ) {
        $this->meta_prefix = TP_DONATE_META_CAMPAIGN;
        parent::__construct( $post );
    }

    /**
     * compensate
     * @return array
     */
    public function get_compensate() {
        return get_post_meta( $this->id, $this->meta_prefix . 'marker', true );
    }

    /**
     * currency
     * @return array
     */
    public function get_currency() {
        if ( ! ( $currency = get_post_meta( $this->id, $this->meta_prefix . 'currency', true ) ) ) {
            $currency = donate_get_currency();
        }
        return $currency;
    }

    /**
     * Get Campaign total raised
     */
    public function get_total_raised() {
        return floatval( get_post_meta( $this->id, $this->meta_prefix . 'total_raised', true ) );
    }

    // static function instead of new class
    static function instance( $post = null ) {

        if ( is_numeric( $post ) ) {
            $post = get_post( $post );
            $id = $post->ID;
        } else if ( $post instanceof WP_Post ) {
            $id = $post->ID;
        }

        if ( !isset( $id ) && $post )
            $id = $post->ID;

        if ( !empty( self::$_instances[$id] ) ) {
            return self::$_instances[$id];
        }

        return self::$_instances[$id] = new self( $post );
    }

}
