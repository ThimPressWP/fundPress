<?php
/**
 * The Template for displaying all archive products.
 *
 * Override this template by copying it to yourtheme/tp-donate/templates/content-campaign.php
 *
 * @author 		ThimPress
 * @package 	tp-donate/templates
 * @version     1.0
 */
if( ! defined( 'ABSPATH' ) ) exit();
?>

<li class="campaign_entry">

	<div class="campaign_item">

		<div class="campaign_thumbnail">

			<?php

				/**
				 * donate_loop_campaign_thumbnail hook
				 * <!-- Thumbnail Campaign -->
				 */
				do_action( 'donate_loop_campaign_thumbnail' );
			?>

		</div>

		<div class="campaign_details">

			<?php
				/**
				 * donate_loop_campaign_title hook
				 * <!-- Title Campaign -->
				 */
				do_action( 'donate_loop_campaign_title' );

				/**
				 * donate_loop_campaign_countdown
				 * <!-- Description Campaign -->
				 */
				do_action( 'donate_loop_campaign_excerpt' );

				/**
				 * donate_loop_campaign_countdown
				 * <!-- Countdown Campaign -->
				 */
				do_action( 'donate_loop_campaign_countdown' );

				/**
				 * donate_loop_campaign_goal_raised hook
				 * <!-- Goal and Raised Campaign -->
				 */
				do_action( 'donate_loop_campaign_goal_raised' );

				/**
				 * donate_loop_campaign_posted hook
				 * <!-- Posted Campaign -->
				 */
				do_action( 'donate_loop_campaign_posted' );
			?>

		</div>

	</div>

</li>
