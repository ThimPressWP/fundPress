<?php
/**
 * Template for displaying thumbnail in single campaign page.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/single/thumbnail.php
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

<?php the_post_thumbnail(); ?>