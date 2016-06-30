<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
global $post;
$donor = DN_Donor::instance( $post->Id );
$donateds = $donor->get_donated();
?>
<style type="text/css">
	#post-body-content{
		display: none;
	}
</style>
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
<?php if( $donateds ): ?>
	<h3><?php _e( 'Donated', 'tp-donate' ) ?></h3>

	<table>
		<thead>
			<tr>
				<th><?php _e( 'Donate ID', 'tp-donate' ) ?></th>
				<th><?php _e( 'Amount', 'tp-donate' ) ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $donateds as $key => $donated ): ?>
				<?php $donation = DN_Donate::instance( $donated->ID ) ?>
				<tr>
					<th>
						<a href="<?php echo esc_url( get_edit_post_link( $donated->ID ) ); ?>"><?php printf( '%s', donate_generate_post_key( $donated->ID ) ) ?></a>
					</th>
					<td>
						<?php echo donate_price( $donation->get_meta( 'total' ), $donation->get_meta( 'currency' ) ) ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

<?php endif; ?>