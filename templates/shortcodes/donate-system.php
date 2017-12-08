<?php
/**
 * Template for displaying donate system amount shortcode.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/shortcodes/donate-system.php
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

<div class="thimpress-donation-system">
    <div class="donation-system">
		<?php echo esc_attr( donate_price( donate_amount_system(), donate_get_currency() ) ); ?>
    </div>
</div>
