<?php
/**
 * Template for displaying campaign title in archive loop.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/loop/title.php
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

<div class="campaign_title">
    <h3>
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </h3>
</div>
