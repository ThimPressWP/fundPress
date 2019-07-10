<?php
/**
 * Admin view: Donate detail meta box.
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
global $post;
$donation = DN_Donate::instance( $post->ID );
$currency = $donation->currency ? $donation->currency : donate_get_currency();
$donor_id = $donation->donor_id;

$type = $this->get_field_value( 'type', 'system' );
?>

<div class="cmb2-wrap">
    <div class="cmb-field-list">

        <!-- donate type -->
        <div class="cmb-row">
            <div class="cmb-th">
                <label for="<?php echo esc_attr( $this->get_field_name( 'type' ) ) ?>"><?php _e( 'Donate Type', 'fundpress' ); ?></label>
            </div>
            <div class="cmb-td">
                <select name="<?php echo esc_attr( $this->get_field_name( 'type' ) ) ?>"
                        id="<?php echo esc_attr( $this->get_field_name( 'type' ) ) ?>">
                    <option value="system"<?php selected( $donation->type, 'system' ); ?>><?php _e( 'System', 'fundpress' ); ?></option>
                    <option value="campaign"<?php selected( $donation->type, 'campaign' ); ?>><?php _e( 'Campaign', 'fundpress' ); ?></option>
                </select>
                <p class="cmb2-metabox-description"><?php _e( 'Select donate type, donate for <i>Campaign</i> or <i>System</i>', 'fundpress' ); ?></p>
            </div>
        </div>
        <!-- end donate type -->

        <!-- donor -->
        <div class="cmb-row">
            <div class="cmb-th">
                <label for="<?php echo esc_attr( $this->get_field_name( 'donor_id' ) ) ?>"><?php _e( 'Donor', 'fundpress' ); ?></label>
            </div>
            <div class="cmb-td">
                <select name="<?php echo esc_attr( $this->get_field_name( 'donor_id' ) ) ?>"
                        id="<?php echo esc_attr( $this->get_field_name( 'donor_id' ) ) ?>">
					<?php foreach ( donate_get_donors() as $id ) : ?>
						<?php $donor = DN_Donor::instance( $id ); ?>
                        <option value="<?php echo esc_attr( $id ); ?>"<?php selected( $donor_id, $id ); ?>>
							<?php printf( '%s(%s)', $donor->get_fullname(), $donor->email ) ?>
                        </option>
					<?php endforeach; ?>
                </select>
                <p class="cmb2-metabox-description"><?php _e( 'Select donor', 'fundpress' ); ?></p>
            </div>
        </div>
        <!-- end donor -->

        <!-- donate for campaign -->
        <div class="donate_section_type<?php echo $type !== 'campaign' ? ' hide-if-js' : '' ?>" id="section_campaign">
            <!-- hide-if-js -->
            <table class="donate_items" cellpadding="0" cellspacing="0">
                <thead>
                <tr>
                    <th class="campaign"><?php _e( 'Campaign', 'fundpress' ); ?></th>
                    <th class="amount"><?php _e( 'Amount', 'fundpress' ); ?></th>
                    <th class="action"><?php _e( 'Action', 'fundpress' ); ?></th>
                </tr>
                </thead>
                <tbody>
				<?php if ( $items = $donation->get_items() ) : ?>
					<?php foreach ( $items as $k => $item ) : ?>
                        <tr class="item" data-id="<?php echo esc_attr( $item->id ); ?>">
                            <td class="campaign">
								<?php if ( $campaigns = donate_get_campaigns() ) : ?>
                                    <select name="donate_item[<?php echo esc_attr( $k ); ?>][campaign_id]">
                                        <optgroup label="<?php _e( 'Select Campaign', 'fundpress' ); ?>">
											<?php foreach ( $campaigns as $campaign_id ) : ?>
                                                <option value="<?php echo esc_attr( $campaign_id ); ?>"<?php selected( $item->campaign_id, $campaign_id ); ?>><?php printf( '%s', get_the_title( $campaign_id ) ) ?></option>
											<?php endforeach; ?>
                                        </optgroup>
                                    </select>
								<?php endif; ?>
                            </td>
                            <td class="amount">
                                <input type="number" step="any"
                                       name="donate_item[<?php echo esc_attr( $k ); ?>][amount]"
                                       value="<?php echo esc_attr( $item->total ); ?>" class="donate_item_total"/>
                            </td>
                            <td class="action">
                                <input type="hidden" name="donate_item[<?php echo esc_attr( $k ); ?>][item_id]"
                                       value="<?php echo esc_attr( $item->id ); ?>"/>
                                <a href="#" data-item-id="<?php echo esc_attr( $item->id ); ?>" class="remove"><i
                                            class="icon-cross"></i></a>
                            </td>
                        </tr>
					<?php endforeach; ?>
				<?php endif; ?>
                </tbody>
                <tfoot data-currency="<?php echo esc_attr( donate_get_currency_symbol( $donation->currency ) ); ?>">
                <tr class="item">
                    <td class="campaign" colspan="3" style="text-align: center;">
                        <a href="#" class="button donate_add_campaign"><?php _e( 'Add Campaign', 'fundpress' ); ?></a>
                    </td>
                </tr>
                <tr>
                    <td class="total"><?php printf( __( 'Total(%s)', 'fundpress' ), donate_get_currency_symbol( $donation->currency ) ); ?></td>
                    <td class="amount">
                        <ins><?php printf( '%s', $donation->total ) ?></ins>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
        <!-- end donate for campaign -->

        <!-- donate for system -->
        <div class="donate_section_type<?php echo $type !== 'system' ? ' hide-if-js' : '' ?>" id="section_system">
            <div class="cmb-row">
                <div class="cmb-th">
                    <label for="<?php echo esc_attr( $this->get_field_name( 'total' ) ) ?>"><?php _e( 'Total', 'fundpress' ); ?></label>
                </div>
                <div class="cmb-td">
                    <input type="number" name="<?php echo esc_attr( $this->get_field_name( 'total' ) ) ?>" step="any"
                           min="0" value="<?php echo esc_attr( $donation->total ); ?>"/>
                    <p class="cmb2-metabox-description"><?php printf( __( 'Donate Total(%s)', 'fundpress' ), donate_get_currency_symbol( $donation->currency ) ); ?></p>
                </div>
            </div>
        </div>
        <!-- end donate for system -->
    </div>
</div>
<script type="text/html" id="tmpl-donate-template-campaign-item">
    <tr class="item">
        <td class="campaign">
			<?php if ( $campaigns = donate_get_campaigns() ) : ?>
                <select name="donate_item[{{ data.unique_id }}][campaign_id]">
                    <optgroup label="<?php _e( 'Select Campaign', 'fundpress' ); ?>">
						<?php foreach ( $campaigns as $campaign_id ) : ?>
                            <option value="<?php echo esc_attr( $campaign_id ); ?>"><?php printf( '%s', get_the_title( $campaign_id ) ) ?></option>
						<?php endforeach; ?>
                    </optgroup>
                </select>
			<?php endif; ?>
        </td>
        <td class="amount">
            <input type="number" step="any" name="donate_item[{{ data.unique_id }}][amount]" value=""
                   class="donate_item_total"/>
        </td>
        <td class="action">
            <input type="hidden" name="donate_item[{{ data.unique_id }}][item_id]" value=""/>
            <a href="#" data-item-id="" class="remove"><i class="icon-cross"></i></a>
        </td>
    </tr>
</script>
