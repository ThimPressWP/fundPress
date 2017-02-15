<?php
/**
 * Template Posted
 */
if( ! defined( 'ABSPATH' ) ) exit();
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