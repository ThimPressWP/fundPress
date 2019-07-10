<?php
/**
 * Admin view: Input setting field.
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

<input name="<?php echo esc_attr( $this->get_field_name( $field['name'] ) ) ?>" value="<?php echo $this->get( $field['name'], $field['default'] ) ?>"<?php echo $this->render_atts( $field['atts'] ) ?>/>
