<?php
/**
 * Template for displaying system notice success.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/notices/success.php
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

<div class="donate_form_success_messages active">
	<?php foreach ( $messages as $message ) { ?>
        <p class="donate-line-message success">
			<?php printf( '%s', $message ) ?>
        </p>
	<?php } ?>
</div>
