<?php
/**
 * Template for displaying single campaign page.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/single-campaign.php
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

<?php get_header(); ?>

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
 * donate_after_main_content hook
 */
do_action( 'donate_after_main_content' );
?>

<?php get_sidebar(); ?>

<?php get_footer();