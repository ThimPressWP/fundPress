<?php
/**
 * Admin view: Donate action meta box.
 *
 * @version     2.0
 * @package     View
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
?>

<?php
global $post;
$donation = DN_Donate::instance( $post->ID );
?>

<label for="addition"><?php _e( 'Add note', 'fundpress' ); ?></label>

<textarea name="<?php echo esc_attr( $this->get_field_name( 'addition' ) ) ?>" id="addition" rows="5">
    <?php printf( '%s', $donation->addition ) ?>
</textarea>
