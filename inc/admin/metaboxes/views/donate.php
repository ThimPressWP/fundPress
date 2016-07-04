<?php
if( ! defined( 'ABSPATH' ) ) exit();

$cart_contents = $this->get_field_value( 'cart_contents' );
global $post;
$donation = DN_Donate::instance( $post->ID );
$currency = $donation->currency ? $donation->currency : donate_get_currency();
var_dump( $cart_contents );
?>

<style type="text/css">
	#post-body-content{ display: none; }
</style>
<?php if( $cart_contents ): ?>
	<table>
		<thead>
			<tr>
				<th><?php _e( 'Campaign ID', 'tp-donate' ); ?></th>
				<th><?php _e( 'Campaign Title', 'tp-donate' ); ?></th>
				<th><?php _e( 'Compensate', 'tp-donate' ); ?></th>
				<th><?php _e( 'Donation Amount', 'tp-donate' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $cart_contents as $cart_item_key => $cart_content ) : ?>
				<tr>
					<td>
						<a href="<?php echo get_edit_post_link( $cart_content->product_id  ) ?>"><?php printf( '%s', donate_generate_post_key( $cart_content->product_id ) ) ?></a>
					</td>
					<td>
						<?php printf( '%s', get_the_title( $cart_content->product_id ) ) ?>
					</td>
					<td>
						<?php echo donate_find_compensate_by_amount( $cart_content->product_id, $cart_content->amount ); ?>
					</td>
					<td>
						<?php printf( '%s', donate_price( $cart_content->amount, $cart_content->currency ) ) ?>
					</td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td><?php _e( 'Addition Note', 'tp-donate' ); ?></td>
				<td colspan="3">
					<?php printf( '%s', $donation->addition ) ?>
				</td>
			</tr>
			<tr>
				<td colspan="3"><?php _e( 'Total Amount', 'tp-donate' ); ?></td>
				<td>
					<?php printf( '%s', donate_price( $donation->total, $currency ) ) ?>
				</td>
			</tr>
			<tr>
				<td colspan="3"><?php _e( 'Donor', 'tp-donate' ); ?></td>
				<td>
					<?php $donor_id = $this->get_field_value( 'donor_id' ); ?>
					<a href="<?php echo get_edit_post_link( $donor_id ) ?>">
						<?php printf( '%s', donate_get_donor_fullname( $donation->ID ) ) ?>
					</a>
				</td>
			</tr>
		</tbody>
	</table>

<?php else : ?>

	<table>
		<tbody>
			<tr>
				<th>
					<?php _e( 'Donate For System', 'tp-donate' ); ?>
				</th>
				<td>
					<?php echo donate_price( $donation->get_meta( 'total' ), $currency ) ?>
				</td>
			</tr>
			<tr>
				<td><?php _e( 'Donor', 'tp-donate' ); ?></td>
				<td>
					<?php $donor_id = $this->get_field_value( 'donor_id' ); ?>
					<a href="<?php echo get_edit_post_link( $donor_id ) ?>">
						<?php printf( '%s', donate_get_donor_fullname( $donation->ID ) ) ?>
					</a>
				</td>
			</tr>
			<tr>
				<td><?php _e( 'Addition Note', 'tp-donate' ); ?></td>
				<td>
					<?php printf( '%s', $donation->get_meta( 'addition' ) ) ?>
				</td>
			</tr>
		</tbody>
	</table>

<?php endif; ?>
