<?php
if( ! defined( 'ABSPATH' ) ) exit();
/**
 * template raised
 */
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