<?php
/**
 * messages template
 */
if( ! defined( 'ABSPATH' ) ) exit();
?>

<div class="donation-messages">

	<?php foreach( $messages as $status => $message ) : ?>
		<?php if ( ! empty( $message ) ) : ?>
			<?php donate_get_template( 'notices/' . $status . '.php', array( 'messages' => $message ) ); ?>
		<?php endif; ?>
	<?php endforeach; ?>

</div>