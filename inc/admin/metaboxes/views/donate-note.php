<?php
if ( !defined( 'ABSPATH' ) ) {
    exit();
}
global $post;
$donation = DN_Donate::instance( $post->ID );
?>

<label for="addition"><?php _e( 'Add note', 'fundpress' ); ?></label>
<textarea name="<?php echo esc_attr( $this->get_field_name( 'addition' ) ) ?>" id="addition" rows="5"><?php printf( '%s', $donation->addition ) ?></textarea>
