<?php
/**
 * messages template
 */
if( ! defined( 'ABSPATH' ) ) exit();
?>

<div class="donate_form_error_messages active">
	<?php foreach( $messages as $status => $message ) : ?>
			<?php foreach( $message as $msg ) : ?>
				<p class="donate-payment-message <?php echo esc_attr( $status ) ?>">
					<?php printf( '%s', $msg ) ?>
				</p>
			<?php endforeach; ?>
	<?php endforeach; ?>
</div>
