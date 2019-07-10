<?php
/**
 * Template for displaying content single campaign.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/content-single-campaign.php
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

<article id="tp_donate-<?php the_ID(); ?>" <?php post_class( 'campaign_single' ); ?>>

	<?php
	/**
	 * donate_before_single_room_summary hook
	 */
	do_action( 'donate_before_single_campaign' );
	?>

    <div class="campaign_entry">

		<?php
		/**
		 * donate_single_campaign_title hook
		 */
		do_action( 'donate_single_campaign_title' );

		/**
		 * donate_single_campaign_thumbnail hook
		 */
		do_action( 'donate_single_campaign_thumbnail' );

		/**
		 * donate_single_campaign_thumbnail hook
		 */
		do_action( 'donate_single_campaign_donate' );

		/**
		 * donate_single_campaign_countdown
		 */
		do_action( 'donate_single_campaign_countdown' );

		/**
		 * donate_single_campaign_content hook
		 */
		do_action( 'donate_single_campaign_content' );
		?>

    </div><!-- .summary -->

	<?php
	/**
	 * donate_after_single_campaign hook
	 */
	do_action( 'donate_after_single_campaign' );
	?>

</article><!-- #campaign-<?php the_ID(); ?> -->