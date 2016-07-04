<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
?>
<div class="donate_form_success_messages active">
	<?php foreach( $messages as $message ) : ?>
		<p class="donate-line-message success">
			<?php printf( '%s', $message ) ?>
		</p>
	<?php endforeach; ?>
</div>
