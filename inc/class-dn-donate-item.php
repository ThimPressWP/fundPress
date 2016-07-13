<?php

if ( !defined( 'ABSPATH' ) ) {
    exit();
}

class DN_Donate_Item extends DN_Post_Base {

    public static $_instances = null;
    public $post_type = 'dn_donate_item';

    public function __construct( $post = null ) {
        parent::__construct( $post );
    }

    public static function instance( $post ) {
        if ( !$post ) {
            return new self( $post );
        }

        if ( is_numeric( $post ) ) {
            $post = get_post( $post );
            $id = $post->ID;
        } else if ( $post instanceof WP_Post ) {
            $id = $post->ID;
        }

        if ( !empty( self::$_instances[$id] ) ) {
            return self::$_instances[$id];
        }

        return self::$_instances[$id] = new self( $post );
    }


}
