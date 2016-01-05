<?php
/**
 * Template Posted
 */
?>

<div class="donate_campaign_posted">

	<div class="donate_campaign_posted_in">
		<label><?php _e( 'Posted in:', 'tp-donate' ); ?></label>
		<?php the_time('F j, Y'); ?>
	</div>

	<div class="donate_campaign_posted_by">
		<label><?php _e( 'Posted by:', 'tp-donate' ); ?></label>
		<?php the_author() ?>
	</div>


</div>