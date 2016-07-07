<?php
if( ! defined( 'ABSPATH' ) ) exit();

$cart_contents = $this->get_field_value( 'cart_contents' );
global $post;
$donation = DN_Donate::instance( $post->ID );
$currency = $donation->currency ? $donation->currency : donate_get_currency();

?>

<style type="text/css">
	#post-body-content{ display: none; }
</style>

<table class="donate_items" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th class="campaign" colspan="2"><?php _e( 'Campaign', 'tp-donate' ); ?></th>
			<th class="amount"><?php _e( 'Amount', 'tp-donate' ); ?></th>
			<th class="action">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $donation->get_items() as $item ) : ?>
			<tr>
				<td class="thumb">
					<a href="<?php echo get_edit_post_link( $item->campaign_id ) ?>">
						<img src="<?php get_the_post_thumbnail_url( $item->campaign_id ) ?>" width="40" height="40" />
					</a>
				</td>
				<td class="campaign">
					<a href="<?php echo get_edit_post_link( $item->campaign_id ) ?>">
						<?php printf( '%s', $item->title ) ?>
					</a>
				</td>
				<td class="amount">
					<ins><?php echo donate_price( $item->total, $donation->currency ) ?></ins>
				</td>
				<td class="action">
					x
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2" class="total"><?php _e( 'Total', 'tp-donate' ); ?></td>
			<td colspan="1" class="amount"><ins><?php echo donate_price( $donation->total, $donation->currency ) ?></ins></td>
		</tr>
		<tr>
			<td colspan="2" class="donor"><?php _e( 'Donor', 'tp-donate' ); ?></td>
			<td colspan="1" class="amount">
				<?php $donor_id = $donation->donor_id; ?>
				<a href="<?php echo get_edit_post_link( $donor_id ) ?>">
					<?php printf( '%s', donate_get_donor_fullname( $donation->id ) ) ?>
				</a>
			</td>
		</tr>
	</tfoot>
</table>

<div class="donate_addition">
	<label for="addition"><?php _e( 'Donor Notes', 'tp-donate' ); ?></label>
	<textarea class="addition" name="<?php echo esc_attr( $donation->meta_prefix ) ?>addition" id="addition" rows="5"><?php printf( '%s', $donation->addition ) ?></textarea>
</div>
