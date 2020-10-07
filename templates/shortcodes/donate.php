<?php
/**
 * Template for displaying donate button shortcode.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/shortcodes/donate.php
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

<div class="thimpress_donate_button">

    <div class="donate_button_title">
		<?php _e( 'Donate now', 'fundpress' ) ?>
    </div>

    <span class="donate_items_count"><?php printf( '%s', FP()->cart->cart_items_count ) ?></span>
    <div class="donate_items_content">
		<?php if ( FP()->cart->cart_items_count > 0 ) { ?>
            <ul>
				<?php foreach ( FP()->cart->cart_contents as $cart_key => $cart_content ) { ?>

                    <li>
                        <a class="<?php echo esc_url( get_permalink( $cart_content->campaign_id ) ) ?>">
							<?php echo get_the_post_thumbnail( $cart_content->campaign_id, 'thumbnail' ); ?>
							<?php printf( '%s', $cart_content->product_data->post_title ) ?>
                        </a>
                    </li>

				<?php } ?>
            </ul>
            <div class="donate_items_footer">
                <a href="<?php echo esc_url( donate_cart_url() ) ?>" class="donate_button donate_button_view_cart">
					<?php _e( 'View Cart', 'fundpress' ) ?>
                </a>
                <a href="<?php echo esc_url( donate_checkout_url() ) ?>"
                   class="donate_button donate_button_view_checkout">
					<?php _e( 'Checkout', 'fundpress' ) ?>
                </a>
            </div>
		<?php } ?>
    </div>

</div>
