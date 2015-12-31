<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="campaign_countdown">

	<div class="donate_counter">
		<span class="donate_counter_percent" data-percent="<?php echo esc_attr( donate_get_campaign_percent() ) ?>" data-tootip="<?php echo esc_attr( donate_get_campaign_percent() . '%' ) ?>"></span>
	</div>

</div>

<p style="clear:both"></p>