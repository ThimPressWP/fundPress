<?php
/**
 * Template for displaying system messages.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/messages.php
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

<div class="donation-messages">
	<?php foreach ( $messages as $status => $message ) { ?>
		<?php if ( ! empty( $message ) ) { ?>
			<?php donate_get_template( 'notices/' . $status . '.php', array( 'messages' => $message ) ); ?>
		<?php } ?>
	<?php } ?>
</div>