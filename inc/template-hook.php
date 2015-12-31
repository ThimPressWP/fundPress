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

add_filter( 'the_post', 'donate_get_camgain_amount' );
if( ! function_exists( 'donate_get_camgain_amount' ) )
{
	function donate_get_camgain_amount( $post )
	{
		$post->total = donate_total_campagin( $post->ID );
		return $post;
	}
}

// get campaign total
if( ! function_exists( 'donate_total_campagin' ) )
{
	function donate_total_campagin( $post = null )
	{
		if( ! $post )
		{
			global $post;
			$post_id = $post->ID;
		}

		if( is_numeric( $post ) )
			$post_id = $post;

		if( $post instanceof WP_Post )
		{
			$post_id = $post->ID;
		}

		$campaign = DN_Campaign::instance( $post_id );

		global $wpdb;

		$query = $wpdb->prepare("
				SELECT SUM( amount.meta_value ) AS raised FROM $wpdb->postmeta AS amount
				RIGHT JOIN $wpdb->posts AS campaign ON amount.post_id = campaign.ID
				WHERE campaign.ID = %s
				AND campaign.post_type = %s
				AND campaign.post_status = %s
				AND amount.meta_key = %s
				AND
					(	SELECT DISTINCT donate.post_status FROM $wpdb->posts AS donate
						LEFT JOIN $wpdb->postmeta AS donate_meta ON donate_meta.meta_value = donate.ID
						WHERE
							donate.post_type = %s
							AND donate_meta.post_id = campaign.ID
							AND donate_meta.meta_key = %s
							GROUP BY donate.post_status
					) = %s
			", $post_id, 'dn_campaign', 'publish', 'thimpress_campaign_amount', 'dn_donate', 'thimpress_campaign_donate', 'donate-completed' );
// echo $query;die();
		if( $query = $wpdb->get_row( $query, OBJECT ) )
		{
			return $query->raised;
		}
		return 0;
	}
}

if( ! function_exists( 'donate_goal_campagin' ) )
{
	function donate_goal_campagin( $post = null )
	{
		if( ! $post )
		{
			global $post;
			$post_id = $post->ID;
		}

		if( is_numeric( $post ) )
			$post_id = $post;

		if( $post instanceof WP_Post )
		{
			$post_id = $post->ID;
		}

		$campaign = DN_Campaign::instance( $post_id );
		// convert to current currency settings
		return donate_campaign_convert_amount( $campaign->get_meta( 'goal' ), $campaign->get_meta( 'currency' ), donate_get_currency() );
	}

}
