<?php
if (!defined('ABSPATH'))
    exit();
/**
 * template shortcode [donate_system]
 */
?>

<div class="thimpress-donation-system">
    <div class="donation-system">
        <?php echo esc_attr(donate_price(donate_amount_system(), donate_get_currency())); ?>
    </div>
</div>
