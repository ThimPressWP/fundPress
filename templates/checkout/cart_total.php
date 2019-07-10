<?php
/**
 * Template for displaying cart total in checkout page.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/checkout/cart_total.php
 *
 * @version     2.0
 * @package     Template
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
?>

<tfoot>
<tr>
    <th class="donate_cart_foot_total"><?php _e( 'Total', 'fundpress' ) ?></th>
    <td class="donate_cart_foot_total_amount"><?php printf( '%s', donate_price( FP()->cart->cart_total ) ) ?></td>
</tr>
</tfoot>
