<?php
//// params default
// array(
// 	'type'		=> 'select',
// 	'label'		=> __( 'Currency Position', 'tp-donate' ),
// 	'desc'		=> __( 'This controlls the position of the currency symbol', 'tp-donate' ),
// 	'atts'		=> array(
// 			'id'	=> 'currency_position',
// 			'class'	=> 'currency_position'
// 		),
// 	'name'		=> 'currency_position',
// 	'options'	=> array(
// 			'left'			=> __( 'Left', 'tp-donate' ) . ' ' . '(£99.99)',
// 			'right'			=> __( 'Right', 'tp-donate' ) . ' ' . '(99.99£)',
// 			'left_space'	=> __( 'Left with space', 'tp-donate' ) . ' ' . '(£ 99.99)',
// 			'right_space'	=> __( 'Right with space', 'tp-donate' ) . ' ' . '(99.99 £)',
// 		),
// 	'default'	=> array()
// ),

?>

<?php
	$multiple = false;
	if( isset( $field['atts'], $field['atts']['multiple'] ) && $field['atts']['multiple'] )
	{
		$multiple = true;
	}
?>

<select name="<?php echo esc_attr( $this->get_field_name( $field['name'] ) ) . ( $multiple ? '[]' : '' ) ?>"<?php echo $this->render_atts( $field['atts'] ) ?>>

	<?php if( $field['options'] ): ?>

		<?php foreach ( $field['options'] as $key => $value ): ?>

			<?php if( $multiple ): ?>
				<!--Multi select-->
				<option value="<?php echo esc_attr( $key ) ?>"<?php echo in_array( $key, $this->get( $field['name'] ) ) ? ' selected="selected"' : ''  ?>><?php printf( '%s', $value ) ?></option>
			<?php else: ?>
				<option value="<?php echo esc_attr( $key ) ?>"<?php echo $this->get( $field['name'] ) == $key ? ' selected="selected"' : ''  ?>><?php printf( '%s', $value ) ?></option>
			<?php endif; ?>

		<?php endforeach; ?>

	<?php endif; ?>

</select>