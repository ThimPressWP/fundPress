<?php
/**
 * Template for displaying cart page.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/cart/cart.php
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

<?php fundpress_print_notices(); ?>

<form class="donate_cart" action="<?php echo esc_attr( donate_cart_url() ) ?>">

	<?php do_action( 'donate_before_cart_contents' ); ?>

    <table class="donate_cart_form">
        <!-- Cart title -->
		<?php donate_get_template( 'cart/cart_title.php' ); ?>
        <!-- Cart items -->
		<?php donate_get_template( 'cart/cart_items.php' ); ?>
        <!-- Cart total -->
		<?php donate_get_template( 'cart/cart_total.php' ); ?>
    </table>

	<?php donate_get_template( 'cart/cart_footer.php' ); ?>

	<?php do_action( 'donate_after_cart_contents' ); ?>

</form>
