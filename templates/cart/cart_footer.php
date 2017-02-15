<?php
if( ! defined( 'ABSPATH' ) ) exit();
/**
 * template cart footer
 */
?>
<div class="donate_cart_footer_button">
	<!-- <button class="donate_button update"><?php //_e( 'Update', 'fundpress' ) ?></button> -->
	<a class="donate_button checkout_url" href="<?php echo esc_attr( donate_checkout_url() ) ?>"><?php _e( 'Donate Payment', 'fundpress' ) ?></a>
</div>