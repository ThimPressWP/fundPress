<?php
/**
 * Template for displaying archive campaign.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/archive-campaign.php
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
 * campaign_before_main_content hook
 */
do_action( 'campaign_before_main_content' );
?>

<?php
/**
 * campaign_archive_description hook
 *
 * @hooked campaign_taxonomy_archive_description - 10
 * @hooked campaign_campaign_archive_description - 10
 */
do_action( 'campaign_archive_description' );
?>

<?php if ( have_posts() ) : ?>

	<?php
	/**
	 * campaign_before_shop_loop hook
	 *
	 * @hooked campaign_result_count - 20
	 * @hooked campaign_catalog_ordering - 30
	 */
	do_action( 'campaign_before_archive_loop' );
	?>

    <div id="donate_main_content">
        <ul class="campaign_archive campaign_column_<?php echo esc_attr( FP()->settings->donate->get( 'archive_column', '4' ) ) ?>">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php donate_get_template_part( 'content', 'campaign' ); ?>
			<?php endwhile; // end of the loop. ?>
        </ul>
    </div>

	<?php
	/**
	 * campaign_after_shop_loop hook
	 *
	 * @hooked campaign_pagination - 10
	 */
	do_action( 'campaign_after_archive_loop' );
	?>

<?php endif; ?>

<?php
/**
 * campaign_after_main_content hook
 *
 * @hooked campaign_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'campaign_after_main_content' );
?>

<?php
/**
 * campaign_sidebar hook
 *
 * @hooked campaign_get_sidebar - 10
 */
do_action( 'campaign_sidebar' );
?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>