<?php
/**
 * Template for displaying campaign goal and raised in archive loop.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/loop/goal_raised.php
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

<div class="donate_campaign_goal_raised">
	<div class="campaign_raised campaign_goal_raised" data-raised="<?php echo esc_attr( donate_total_campaign() ) ?>">
		<label><?php _e( 'Raised', 'fundpress' ) ?></label>
		<?php printf( '%s', donate_price( donate_total_campaign() ) ) ?>
	</div>
	<div class="campaign_goal campaign_goal_raised" data-goal="<?php echo esc_attr( donate_goal_campagin() ) ?>">
		<label><?php _e( 'Goal', 'fundpress' ) ?></label>
		<?php printf( '%s', donate_price( donate_goal_campagin() ) ) ?>
	</div>
</div>