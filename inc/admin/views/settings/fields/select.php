<?php
/**
 * Admin view: Select setting field.
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
$multiple = false;
if ( isset( $field['atts'], $field['atts']['multiple'] ) && $field['atts']['multiple'] ) {
	$multiple = true;
}
?>

<select name="<?php echo esc_attr( $this->get_field_name( $field['name'] ) ) . ( $multiple ? '[]' : '' ) ?>"<?php echo $this->render_atts( $field['atts'] ) ?>>

	<?php if ( $field['options'] ) { ?>
		<?php foreach ( $field['options'] as $key => $value ) {
			$val = $this->get( $field['name'] );
			if ( empty( $val ) && isset( $field['default'] ) ) {
				$val = $field['default'];
			}
			?>
			<?php if ( $multiple ) { ?>
                <!--Multi select-->
                <option value="<?php echo esc_attr( $key ) ?>"<?php echo in_array( $key, $val ) ? ' selected="selected"' : '' ?>><?php printf( '%s', $value ) ?></option>
			<?php } else { ?>
                <option value="<?php echo esc_attr( $key ) ?>"<?php echo $val == $key ? ' selected="selected"' : '' ?>><?php printf( '%s', $value ) ?></option>
			<?php } ?>
		<?php } ?>
	<?php } ?>

</select>