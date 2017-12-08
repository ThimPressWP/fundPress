<?php
/**
 * Template for displaying content archive campaign.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/content-campaign.php
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

<li class="campaign_entry">

    <div class="campaign_item">

        <div class="campaign_thumbnail">

			<?php
			/**
			 * donate_loop_campaign_thumbnail hook
			 */
			do_action( 'donate_loop_campaign_thumbnail' );
			?>

        </div>

        <div class="campaign_details">

			<?php
			/**
			 * donate_loop_campaign_title hook
			 */
			do_action( 'donate_loop_campaign_title' );

			/**
			 * donate_loop_campaign_countdown
			 */
			do_action( 'donate_loop_campaign_excerpt' );

			/**
			 * donate_loop_campaign_countdown
			 */
			do_action( 'donate_loop_campaign_countdown' );

			/**
			 * donate_loop_campaign_goal_raised hook
			 */
			do_action( 'donate_loop_campaign_goal_raised' );

			/**
			 * donate_loop_campaign_posted hook
			 */
			do_action( 'donate_loop_campaign_posted' );
			?>

        </div>

    </div>

</li>
