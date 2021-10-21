<?php
/**
 * Admin view: Multi radio setting field.
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

<?php if ( ! empty( $field['options'] ) ) { ?>
	<?php foreach ( $field['options'] as $k => $option ) { ?>
		<?php unset( $option['id'] ); ?>
        <p>
            <input type="radio" name="<?php echo esc_attr( $this->get_field_name( $field['name'] ) ) ?>"
                   value="<?php printf( '%s', $option['value'] ) ?>"<?php echo $this->render_atts( $field['atts'] ) ?>
                   id="<?php echo esc_attr( $this->get_field_id( $field['name'] ) . $option['value'] ); ?>"
				<?php echo $option['value'] === $this->get( $field['name'] ) ? ' checked="checked"' : '' ?>
            />
            <label for="<?php echo esc_attr( $this->get_field_id( $field['name'] ) ) . $option['value']; ?>"><?php printf( '%s', $option['label'] ) ?></label>
        </p>
	<?php } ?>
<?php } ?>