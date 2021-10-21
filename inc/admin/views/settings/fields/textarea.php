<?php
/**
 * Admin view: Textarea setting field.
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

<textarea name="<?php echo esc_attr( $this->get_field_name( $field['name'] ) ) ?>"<?php echo $this->render_atts( $field['atts'] ) ?>>
    <?php echo esc_textarea( trim( $this->get( $field['name'] ) ) ) ?>
</textarea>