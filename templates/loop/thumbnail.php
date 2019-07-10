<?php
/**
 * Template for displaying campaign thumbnail in archive loop.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/loop/thumbnail.php
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

<?php global $post; ?>

<?php if ( has_post_thumbnail() ) { ?>
    <div class="campaign_thumbnail_figure">
        <!-- Thumbnail -->
        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>

        <!-- Overlay -->
        <div class="campaign_thumbnail_overlay">
            <a href="#" class="donate_load_form"
               data-campaign-id="<?php echo esc_attr( get_the_ID() ) ?>"><?php esc_html_e( 'Donate now', 'fundpress' ); ?></a>
        </div>
    </div>
<?php } ?>