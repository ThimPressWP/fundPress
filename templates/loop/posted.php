<?php
/**
 * Template for displaying campaign posted in archive loop.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/loop/posted.php
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

<div class="donate_campaign_posted">
	<div class="donate_campaign_posted_in">
		<label><?php _e( 'Posted in:', 'fundpress' ); ?></label>
		<?php the_time('F j, Y'); ?>
	</div>

	<div class="donate_campaign_posted_by">
		<label><?php _e( 'Posted by:', 'fundpress' ); ?></label>
		<?php the_author() ?>
	</div>
</div>