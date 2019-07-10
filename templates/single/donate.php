<?php
/**
 * Template for displaying donate button in single campaign page.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/single/donate.php
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

<!-- Overlay -->
<div class="campaign_thumbnail_overlay">
    <a href="#" class="donate_load_form" data-campaign-id="<?php echo esc_attr( get_the_ID() ) ?>">
		<?php esc_html_e( 'Donate now', 'fundpress' ); ?>
    </a>
</div>