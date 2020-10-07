<?php
/**
 * Admin view: Settings page.
 *
 * @version     2.0
 * @package     View
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
?>

<?php $donate_setting = apply_filters( 'donate_admin_settings', array() );

if ( $donate_setting ) {

	$current_tab = isset( $_GET['tab'] ) && $_GET['tab'] ? DN_Helpper::DN_sanitize_params_submitted( $_GET['tab'] ) : current( array_keys( $donate_setting ) ) ?>

	<form method="POST" name="tp_donate_options" action="options.php">

		<?php settings_fields( $this->settings->_prefix ); ?>

		<div class="wrap tp_donate_setting_wrapper">
			<!--tabs-->
			<h2 class="nav-tab-wrapper">
				<?php foreach ( $donate_setting as $key => $title ) { ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=tp_donate_setting&tab=' . $key ) ); ?>"
					   class="nav-tab<?php echo $current_tab === $key ? ' nav-tab-active' : '' ?>"
					   data-tab="<?php echo esc_attr( $key ) ?>">
						<?php printf( '%s', $title ) ?>
					</a>
				<?php } ?>
			</h2>
			<!--content-->
			<div class="tp_donate_wrapper_content">
				<?php foreach ( $donate_setting as $key => $title ) { ?>
					<div id="<?php echo esc_attr( $key ) ?>">
						<?php do_action( 'donate_admin_setting_' . $key . '_content' ); ?>
					</div>
				<?php } ?>
			</div>
		</div>

		<!--submit button-->
		<?php submit_button(); ?>

	</form>

<?php }
