<?php

/**
 * template hook function
 */
add_filter( 'the_content', 'donate_the_content' );
if( ! function_exists( 'donate_the_content' ) )
{
	function donate_the_content( $content )
	{
		return do_shortcode( $content );
	}
}

/**
 * title
 */
add_action( 'donate_single_campaign_title', 'donate_single_campaign_title' );
if( ! function_exists( 'donate_single_campaign_title' ) )
{
	function donate_single_campaign_title()
	{
		donate_get_template( 'loop/title.php' );
	}
}

/**
 * thumbnai
 */
add_action( 'donate_single_campaign_thumbnail', 'donate_single_campaign_thumbnail' );
if( ! function_exists( 'donate_single_campaign_thumbnail' ) )
{
	function donate_single_campaign_thumbnail()
	{
		donate_get_template( 'loop/thumbnail.php' );
	}
}

/**
 * countdown
 */
add_action( 'donate_loop_campaign_countdown', 'donate_loop_campaign_countdown' );
if( ! function_exists( 'donate_loop_campaign_countdown' ) )
{
	function donate_loop_campaign_countdown()
	{
		if( DN_Settings::instance()->donate->get( 'archive_countdown_raised', 'yes' ) === 'yes' )
		{
			donate_get_template( 'loop/countdown.php' );
		}
	}
}

/**
 * goal and raised
 */
add_action( 'donate_loop_campaign_goal_raised', 'donate_loop_campaign_goal_raised' );
if( ! function_exists( 'donate_loop_campaign_goal_raised' ) )
{
	function donate_loop_campaign_goal_raised()
	{
		if( DN_Settings::instance()->donate->get( 'archive_raised_goal', 'yes' ) === 'yes' )
		{
			donate_get_template( 'loop/goal_raised.php' );
		}
	}
}

/**
 * content
 */
add_action( 'donate_loop_campaign_excerpt', 'donate_loop_campaign_excerpt' );
if( ! function_exists( 'donate_loop_campaign_excerpt' ) )
{
	function donate_loop_campaign_excerpt()
	{
		donate_get_template( 'loop/excerpt.php' );
	}
}

/**
 * content
 */
add_action( 'donate_single_campaign_content', 'donate_single_campaign_content' );
if( ! function_exists( 'donate_single_campaign_content' ) )
{
	function donate_single_campaign_content()
	{
		donate_get_template( 'loop/content.php' );
	}
}