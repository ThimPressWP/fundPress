<?php

/**
 * template hook function
 */
add_filter( 'the_content', 'donate_the_content' );
if( ! function_exists( 'donate_the_content' ) )
{
	function donate_the_content( $content )
	{
		global $post;
		$post_id = $post->ID;
		if( $post_id == DN_Settings::instance()->checkout->get( 'cart_page' ) )
		{
			$content = '[donate_cart]';
		}
		else if( $post_id == DN_Settings::instance()->checkout->get( 'checkout_page' ) )
		{
			$content = '[donate_checkout]';
		}
		else if( in_array( $post->post_type, array( 'dn_donate', 'dn_donor' ) ) )
		{
			wp_redirect( home_url() ); exit();
		}
		return do_shortcode( $content );
	}
}
/*******Archive Template*******/
/**
 * title
 */
add_action( 'donate_loop_campaign_title', 'donate_loop_campaign_title' );
if( ! function_exists( 'donate_loop_campaign_title' ) )
{
	function donate_loop_campaign_title()
	{
		donate_get_template( 'loop/title.php' );
	}
}

/**
 * thumbnai
 */
add_action( 'donate_loop_campaign_thumbnail', 'donate_loop_campaign_thumbnail' );
if( ! function_exists( 'donate_loop_campaign_thumbnail' ) )
{
	function donate_loop_campaign_thumbnail()
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
 * posted
 */
add_action( 'donate_loop_campaign_posted', 'donate_loop_campaign_posted' );
if( ! function_exists( 'donate_loop_campaign_posted' ) )
{
	function donate_loop_campaign_posted()
	{
		donate_get_template( 'loop/posted.php' );
	}
}

/**
 * excerpt loop
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
 * content loop
 */
add_action( 'donate_loop_campaign_content', 'donate_loop_campaign_content' );
if( ! function_exists( 'donate_loop_campaign_content' ) )
{
	function donate_loop_campaign_content()
	{
		donate_get_template( 'loop/content.php' );
	}
}
/*******End Archive Template*******/

/*******Single Template*******/
/**
 * title
 */
add_action( 'donate_single_campaign_title', 'donate_single_campaign_title' );
if( ! function_exists( 'donate_single_campaign_title' ) )
{
	function donate_single_campaign_title()
	{
		donate_get_template( 'single/title.php' );
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
		donate_get_template( 'single/thumbnail.php' );
	}
}

/**
 * countdown
 */
add_action( 'donate_single_campaign_countdown', 'donate_single_campaign_countdown' );
if( ! function_exists( 'donate_single_campaign_countdown' ) )
{
	function donate_single_campaign_countdown()
	{
		if( DN_Settings::instance()->donate->get( 'single_countdown_raised', 'yes' ) === 'yes' )
		{
			donate_get_template( 'single/countdown.php' );
		}
	}
}

/**
 * goal and raised
 */
add_action( 'donate_single_campaign_goal_raised', 'donate_single_campaign_goal_raised' );
if( ! function_exists( 'donate_single_campaign_goal_raised' ) )
{
	function donate_single_campaign_goal_raised()
	{
		if( DN_Settings::instance()->donate->get( 'single_countdown_raised', 'yes' ) === 'yes' )
		{
			donate_get_template( 'single/goal_raised.php' );
		}
	}
}

/**
 * posted
 */
add_action( 'donate_single_campaign_posted', 'donate_single_campaign_posted' );
if( ! function_exists( 'donate_single_campaign_posted' ) )
{
	function donate_single_campaign_posted()
	{
		donate_get_template( 'single/posted.php' );
	}
}

/**
 * content loop
 */
add_action( 'donate_single_campaign_content', 'donate_single_campaign_content' );
if( ! function_exists( 'donate_single_campaign_content' ) )
{
	function donate_single_campaign_content()
	{
		donate_get_template( 'single/content.php' );
	}
}
/*******End Single Template*******/

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
				SELECT SUM( DISTINCT amount.meta_value ) AS raised FROM $wpdb->postmeta AS amount
				RIGHT JOIN $wpdb->posts AS campaign ON amount.post_id = campaign.ID
				LEFT JOIN $wpdb->postmeta AS donate_meta ON donate_meta.post_id = campaign.ID
				RIGHT JOIN $wpdb->posts AS donate ON donate.ID = donate_meta.meta_value
				WHERE campaign.ID = %s
				AND campaign.post_type = %s
				AND campaign.post_status = %s
				AND amount.meta_key = %s
					AND donate.post_type = %s
					AND donate_meta.meta_key = %s
					AND donate.post_status = %s
			", $post_id, 'dn_campaign', 'publish', 'thimpress_campaign_amount', 'dn_donate', 'thimpress_campaign_donate', 'donate-completed' );

		if( $query = $wpdb->get_row( $query, OBJECT ) )
		{
			return round( $query->raised, 2 );
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
		if( ! $goal = $campaign->get_meta( 'goal' ) )
		{
			$goal = 0;
		}
		// convert to current currency settings
		return donate_campaign_convert_amount( $goal, $campaign->get_meta( 'currency' ), donate_get_currency() );
	}

}

if( ! function_exists( 'donate_get_campaign_percent' ) )
{
	function donate_get_campaign_percent( $post = null )
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

		$total = donate_total_campagin( $post_id );
		if( ! $total )
			return 0;
		$goal = donate_goal_campagin( $post_id );

		if( ! $goal )
			return 100;

		return round( ( $total / $goal ) * 100, donate_currency_decimal() );
	}
}
