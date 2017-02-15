<?php
if( ! defined( 'ABSPATH' ) ) exit();

?>
<!-- Overlay -->
<div class="campaign_thumbnail_overlay">

	<a href="#" class="donate_load_form" data-campaign-id="<?php echo esc_attr( get_the_ID() ) ?>"><?php esc_html_e( 'Donate now', 'fundpress' ); ?></a>

</div>