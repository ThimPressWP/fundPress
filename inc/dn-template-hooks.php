<?php
/**
 * Fundpress template hooks functions.
 *
 * @version     2.0
 * @package     Function
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

/* ***** Start Archive Campaign Template ***** */

add_action( 'donate_loop_campaign_title', 'fundpress_loop_campaign_title' );

if ( !function_exists( 'fundpress_loop_campaign_title' ) ) {
	/**
	 * Campaign title.
	 */
    function fundpress_loop_campaign_title() {
        donate_get_template( 'loop/title.php' );
    }
}

add_action( 'donate_loop_campaign_thumbnail', 'fundpress_loop_campaign_thumbnail' );

if ( !function_exists( 'fundpress_loop_campaign_thumbnail' ) ) {
	/**
	 * Campaign thumbnail.
	 */
    function fundpress_loop_campaign_thumbnail() {
        donate_get_template( 'loop/thumbnail.php' );
    }
}

add_action( 'donate_loop_campaign_countdown', 'fundpress_loop_campaign_countdown' );

if ( !function_exists( 'fundpress_loop_campaign_countdown' ) ) {
	/**
	 * Campaign countdown.
	 */
    function fundpress_loop_campaign_countdown() {
        if ( FP()->settings->donate->get( 'archive_countdown_raised', 'yes' ) === 'yes' ) {
            donate_get_template( 'loop/countdown.php' );
        }
    }
}

add_action( 'donate_loop_campaign_goal_raised', 'fundpress_loop_campaign_goal_raised' );

if ( !function_exists( 'fundpress_loop_campaign_goal_raised' ) ) {
	/**
	 * Campaign goal and raised.
	 */
    function fundpress_loop_campaign_goal_raised() {
        if ( FP()->settings->donate->get( 'archive_raised_goal', 'yes' ) === 'yes' ) {
            donate_get_template( 'loop/goal_raised.php' );
        }
    }

}

add_action( 'donate_loop_campaign_posted', 'fundpress_loop_campaign_posted' );

if ( !function_exists( 'fundpress_loop_campaign_posted' ) ) {
	/**
	 * Campaign posted.
	 */
    function fundpress_loop_campaign_posted() {
        donate_get_template( 'loop/posted.php' );
    }
}

add_action( 'donate_loop_campaign_excerpt', 'fundpress_loop_campaign_excerpt' );

if ( !function_exists( 'fundpress_loop_campaign_excerpt' ) ) {
	/**
	 * Campaign except.
	 */
    function fundpress_loop_campaign_excerpt() {
        donate_get_template( 'loop/excerpt.php' );
    }
}

add_action( 'donate_loop_campaign_content', 'fundpress_loop_campaign_content' );

if ( !function_exists( 'fundpress_loop_campaign_content' ) ) {
	/**
	 * Campaign content.
	 */
    function fundpress_loop_campaign_content() {
        donate_get_template( 'loop/content.php' );
    }
}

/* ***** End Archive Campaign Template ***** */

/* ***** Start Single Campaign Template ***** */

add_action( 'donate_single_campaign_title', 'fundpress_single_campaign_title' );

if ( !function_exists( 'fundpress_single_campaign_title' ) ) {
	/**
	 * Campaign title.
	 */
    function fundpress_single_campaign_title() {
        donate_get_template( 'single/title.php' );
    }
}

add_action( 'donate_single_campaign_thumbnail', 'fundpress_single_campaign_thumbnail' );

if ( !function_exists( 'fundpress_single_campaign_thumbnail' ) ) {
	/**
	 * Campaign thumbnail.
	 */
    function fundpress_single_campaign_thumbnail() {
        donate_get_template( 'single/thumbnail.php' );
    }
}

add_action( 'donate_single_campaign_donate', 'fundpress_single_campaign_donate' );

if ( !function_exists( 'fundpress_single_campaign_donate' ) ) {
	/**
	 * Campaign donate button.
	 */
    function fundpress_single_campaign_donate() {
        donate_get_template( 'single/donate.php' );
    }
}

add_action( 'donate_single_campaign_countdown', 'fundpress_single_campaign_countdown' );

if ( !function_exists( 'fundpress_single_campaign_countdown' ) ) {
	/**
	 * Campaign countdown.
	 */
    function fundpress_single_campaign_countdown() {
        if ( FP()->settings->donate->get( 'single_countdown_raised', 'yes' ) === 'yes' ) {
            donate_get_template( 'single/countdown.php' );
        }
    }
}

add_action( 'donate_single_campaign_goal_raised', 'fundpress_single_campaign_goal_raised' );

if ( !function_exists( 'fundpress_single_campaign_goal_raised' ) ) {
	/**
	 * Campaign goal and raised.
	 */
    function fundpress_single_campaign_goal_raised() {
        if ( FP()->settings->donate->get( 'single_countdown_raised', 'yes' ) === 'yes' ) {
            donate_get_template( 'single/goal_raised.php' );
        }
    }
}

add_action( 'donate_single_campaign_posted', 'fundpress_single_campaign_posted' );

if ( !function_exists( 'fundpress_single_campaign_posted' ) ) {
	/**
	 * Campaign posted.
	 */
    function fundpress_single_campaign_posted() {
        donate_get_template( 'single/posted.php' );
    }
}

add_action( 'donate_single_campaign_content', 'fundpress_single_campaign_content' );

if ( !function_exists( 'fundpress_single_campaign_content' ) ) {
	/**
	 * Campaign content.
	 */
    function fundpress_single_campaign_content() {
        donate_get_template( 'single/content.php' );
    }
}
/* ***** End Single Campaign Template ***** */

add_filter( 'the_post', 'fundpress_get_campaign_amount' );

if ( !function_exists( 'fundpress_get_campaign_amount' ) ) {
	/**
     * Set campaign amount.
     *
	 * @param $post
	 *
	 * @return mixed
	 */
    function fundpress_get_campaign_amount( $post ) {
        $post->total = donate_total_campaign( $post->ID );
        return $post;
    }
}

add_action( 'campaign_after_archive_loop', 'fundpress_campaign_pagination_archive' );

if ( !function_exists( 'fundpress_campaign_pagination_archive' ) ) {
	/**
	 * Archive campaign pagination.
	 */
    function fundpress_campaign_pagination_archive() {
        donate_get_template( 'pagination.php' );
    }
}