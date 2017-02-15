<?php
if( ! defined( 'ABSPATH' ) ) exit();
/**
 * cart template footer
 */
?>

<tfoot>
	<tr>
		<th class="donate_cart_foot_total" colspan="2"><?php _e( 'Total', 'fundpress' ) ?></th>
		<td class="donate_cart_foot_total_amount"><?php printf( '%s', donate_price( donate()->cart->cart_total ) ) ?></td>
	</tr>
</tfoot>
