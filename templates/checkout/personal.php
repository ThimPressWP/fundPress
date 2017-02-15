<?php
if( ! defined( 'ABSPATH' ) ) exit();
/**
 * Template
 * personal.php
 */
?>

<?php do_action( 'donate_before_donor_info' ); ?>

<div class="donate_form_layout donate_donor_info">

	<h3><?php _e( 'Personal Info', 'fundpress' ) ?></h3>

	<?php do_action( 'donate_before_donor_info_field' ); ?>

	<div class="donate_field">
		<input name="first_name" id="first_name" class="first_name required" placeholder="<?php _e( '* First Name', 'fundpress' ) ?>" />
	</div>

	<div class="donate_field">
		<input name="last_name" id="last_name" class="last_name required" placeholder="<?php _e( '* Last Name', 'fundpress' ) ?>" />
	</div>

	<div class="donate_field">
		<input name="email" id="email" class="email required" placeholder="<?php _e( '* Email', 'fundpress' ) ?>" />
	</div>

	<div class="donate_field">
		<input name="phone" id="phone" class="phone required" placeholder="<?php _e( '* Phone', 'fundpress' ) ?>" />
	</div>

	<div class="donate_field">
		<textarea name="address" id="address" class="address required" placeholder="<?php _e( '* Address', 'fundpress' ) ?>"></textarea>
	</div>

	<div class="donate_field">
		<textarea name="addition" id="addition" class="addition" placeholder="<?php _e( 'Additional note', 'fundpress' ) ?>"></textarea>
	</div>

	<?php do_action( 'donate_after_donor_info_field' ); ?>

</div>

<?php do_action( 'donate_after_donor_info' ); ?>