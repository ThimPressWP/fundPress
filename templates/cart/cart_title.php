<?php
/**
 * Template for displaying title of cart page.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/cart/cart_title.php
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

<thead>
<tr>
    <th class="donate_action_remove">&nbsp;</th>
    <th class="donate_cart_item_name"><?php _e( 'Item name', 'fundpress' ) ?></th>
    <th class="donate_cart_item_amount"><?php _e( 'Item amount', 'fundpress' ) ?></th>
</tr>
</thead>
