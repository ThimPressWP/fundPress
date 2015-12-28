<table>
	<thead>
		<tr>
			<th><?php _e( 'ID', 'tp-donate' ) ?></th>
			<th><?php _e( 'First name', 'tp-donate' ) ?></th>
			<th><?php _e( 'Last name', 'tp-donate' ) ?></th>
			<th><?php _e( 'Email', 'tp-donate' ) ?></th>
			<th><?php _e( 'Address', 'tp-donate' ) ?></th>
			<th><?php _e( 'Phone', 'tp-donate' ) ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>
				<?php global $post; ?>
				<?php printf( '%s', donate_generate_post_key( $post->ID ) ) ?>
			</th>
			<td>
				<?php printf( '%s', $this->get_field_value( 'first_name' ) ) ?>
			</td>
			<td>
				<?php printf( '%s', $this->get_field_value( 'last_name' ) ) ?>
			</td>
			<td>
				<a href="mailto:<?php printf( '%s', $this->get_field_value( 'email' ) ) ?>"><?php printf( '%s', $this->get_field_value( 'email' ) ) ?></a>
			</td>
			<td>
				<?php printf( '%s', $this->get_field_value( 'address' ) ) ?>
			</td>
			<td>
				<?php printf( '%s', $this->get_field_value( 'phone' ) ) ?>
			</td>
		</tr>
	</tbody>
</table>

<?php
	$donor = DN_Donor::instance( $post->Id );
	$donated = $donor->get_donated();
?>

<?php if( $donated ): ?>
	<h3><?php _e( 'Donated', 'tp-donate' ) ?></h3>



<?php endif; ?>