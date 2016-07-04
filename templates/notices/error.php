<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>
<div class="donate_form_error_messages active">
	<?php foreach( $messages as $message ) : ?>
		<p class="donate-line-message donate-payment-message errors">
			<?php printf( '%s', $message ) ?>
		</p>
	<?php endforeach; ?>
</div>