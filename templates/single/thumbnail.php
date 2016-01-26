<?php
/**
 * Thumbnail Single Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


the_post_thumbnail();

?>

<!-- Overlay -->
<div class="campaign_thumbnail_overlay">

	<a href="#" class="donate_load_form" data-campaign-id="<?php echo esc_attr( get_the_ID() ) ?>"><?php esc_html_e( 'Donate now', 'tp-donate' ); ?></a>

</div>