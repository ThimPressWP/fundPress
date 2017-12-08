<?php
/**
 * Template for displaying cart title in checkout page.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/checkout/cart_title.php
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
		<th class="donate_cart_item_name">
			<?php _e( 'Item name', 'fundpress' ) ?>
		</th>
		<th class="donate_cart_item_amount">
			<?php _e( 'Item amount', 'fundpress' ) ?>
		</th>
	</tr>
</thead>
