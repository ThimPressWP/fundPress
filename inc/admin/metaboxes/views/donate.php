<?php
if( ! defined( 'ABSPATH' ) ) exit();

global $post;
$donation = DN_Donate::instance( $post->ID );
$currency = $donation->currency ? $donation->currency : donate_get_currency();
$donor_id = $donation->donor_id;
?>

<style type="text/css">
	#post-body-content{ display: none; }
</style>

<div class="cmb2-wrap">
	<div class="cmb-field-list">

		<!-- donate type -->
		<div class="cmb-row">
			<div class="cmb-th">
				<label for="<?php echo esc_attr( $this->get_field_name( 'type' ) ) ?>"><?php _e( 'Donate Type', 'tp-donate' ); ?></label>
			</div>
			<div class="cmb-td">
				<select name="<?php echo esc_attr( $this->get_field_name( 'type' ) ) ?>" id="<?php echo esc_attr( $this->get_field_name( 'type' ) ) ?>">
					<option value="system"<?php selected( $donation->type, 'system' ); ?>><?php _e( 'System', 'tp-donate' ); ?></option>
					<option value="campaign"<?php selected( $donation->type, 'campaign' ); ?>><?php _e( 'Campaign', 'tp-donate' ); ?></option>
				</select>
				<p class="cmb2-metabox-description"><?php _e( 'Select donate type, donate for <i>Campaign</i> or <i>System</i>', 'tp-donate' ); ?></p>
			</div>
		</div>
		<!-- end donate type -->

		<!-- donor -->
		<div class="cmb-row">
			<div class="cmb-th">
				<label for="<?php echo esc_attr( $this->get_field_name( 'donor_id' ) ) ?>"><?php _e( 'Donor', 'tp-donate' ); ?></label>
			</div>
			<div class="cmb-td">
				<select name="<?php echo esc_attr( $this->get_field_name( 'donor_id' ) ) ?>" id="<?php echo esc_attr( $this->get_field_name( 'donor_id' ) ) ?>">
					<?php foreach ( donate_get_donors() as $id ) : ?>
						<?php $donor = DN_Donor::instance( $id ); ?>
						<option value="<?php echo esc_attr( $id ); ?>"<?php selected( $donor_id, $id ); ?>>
							<?php printf( '%s(%s)', $donor->get_fullname(), $donor->email ) ?>
						</option>
					<?php endforeach; ?>
				</select>
				<p class="cmb2-metabox-description"><?php _e( 'Select donor.', 'tp-donate' ); ?></p>
			</div>
		</div>
		<!-- end donor -->

		<div class="donate_section_type<?php echo $this->get_field_value( 'type' ) !== 'campaign' ? '' : ' hide-if-js' ?>" id="section_campaign">
			<!-- hide-if-js -->
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
								<a href="#" data-item-id="<?php echo esc_attr( $item->id ); ?>" class="remove"><i class="icon-cross"></i></a>
								<a href="#" data-item-id="<?php echo esc_attr( $item->id ); ?>" class="edit"><i class="icon-pencil"></i></a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="2" class="total"><?php _e( 'Total', 'tp-donate' ); ?></td>
						<td colspan="1" class="amount"><ins><?php echo donate_price( $donation->total, $donation->currency ) ?></ins></td>
					</tr>
				</tfoot>
			</table>
		</div>
		<div class="donate_section_type<?php echo $this->get_field_value( 'type' ) !== 'sytem' ? '' : ' hide-if-js' ?>" id="section_system">
			<div class="cmb-row">
				<div class="cmb-th">
					<label for="<?php echo esc_attr( $this->get_field_name( 'total' ) ) ?>"><?php _e( 'Total', 'tp-donate' ); ?></label>
				</div>
				<div class="cmb-td">
					<input type="number" name="<?php echo esc_attr( $this->get_field_name( 'total' ) ) ?>" step="any" min="0" value="<?php echo esc_attr( $donation->total ); ?>"/>
					<p class="cmb2-metabox-description"><?php _e( 'Donate Total', 'tp-donate' ); ?></p>
				</div>
			</div>
		</div>
	</div>
</div>
