<?php
/**
 * Template Single dn_campaign post type
 */

if( ! defined( 'ABSPATH' ) ) exit();
get_header( ); ?>

	<?php
		/**
		 * donate_before_main_content hook
		 */
		do_action( 'donate_before_main_content' );
	?>

		<div id="donate_main_content">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php donate_get_template_part( 'content', 'single-campaign' ); ?>

			<?php endwhile; // end of the loop. ?>

		</div>

	<?php
		/**
		 * hotel_booking_after_main_content hook
		 *
		 * @hooked donate_after_main_content - 10 (outputs closing divs for the content)
		 */
		do_action( 'donate_after_main_content' );
	?>

	<!--get sidebar-->
	<?php get_sidebar(); ?>

<?php get_footer( );