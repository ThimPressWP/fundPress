<?php
/**
 * Template for displaying campaign excerpt in archive loop.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/loop/excerpt.php
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

<div class="campaign_excerpt"><?php the_excerpt(); ?></div>