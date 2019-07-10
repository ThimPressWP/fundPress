<?php
/**
 * Template for displaying campaign countdown in archive loop.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/loop/countdown.php
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

<div class="campaign_countdown">
    <div class="donate_counter">
        <div class="donate_counter_percent" data-percent="<?php echo esc_attr( donate_get_campaign_percent() ) ?>"
             data-tootip="<?php echo esc_attr( donate_get_campaign_percent() . '%' ) ?>">
            <span class="donate_percent_tooltip"><?php printf( '%s%s', donate_get_campaign_percent(), '%' ) ?></span>
        </div>
    </div>
    <span class="donate_days_to_go"><?php printf( __( '%s Days To Go', 'fundpress' ), donate_get_campaign_days_to_go() ) ?></span>
</div>