<?php
if ( !defined( 'ABSPATH' ) )
    exit();
/**
 * template shortcode [tp_donate]
 */
?>

<div class="thimpress_donate_button">
    <div class="donate_button_title">
        <?php _e( 'Donate now', 'fundpress' ) ?>
    </div>
    <span class="donate_items_count"><?php printf( '%s', donate()->cart->cart_items_count ) ?></span>
    <div class="donate_items_content">
        <?php if ( donate()->cart->cart_items_count > 0 ) : ?>
            <ul>
                <?php foreach ( donate()->cart->cart_contents as $cart_key => $cart_content ) : ?>

                    <li>
                        <a class="<?php echo esc_attr( get_permalink( $cart_content->campaign_id ) ) ?>">
                            <?php echo get_the_post_thumbnail( $cart_content->campaign_id, 'thumbnail' ); ?>
                            <?php printf( '%s', $cart_content->product_data->post_title ) ?>
                        </a>
                    </li>

                <?php endforeach; ?>
            </ul>
            <div class="donate_items_footer">
                <a href="<?php echo esc_attr( donate_cart_url() ) ?>" class="donate_button donate_button_view_cart"><?php _e( 'View Cart', 'fundpress' ) ?></a>
                <a href="<?php echo esc_attr( donate_checkout_url() ) ?>" class="donate_button donate_button_view_checkout"><?php _e( 'Checkout', 'fundpress' ) ?></a>
            </div>
        <?php endif; ?>
    </div>
</div>
