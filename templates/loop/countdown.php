<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="campaign_countdown">

	<div class="donate_counter" data-percent="<?php echo esc_attr( donate_get_campaign_percent() ) ?>"></div>

</div>

<p style="clear:both"></p>