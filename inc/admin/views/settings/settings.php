<?php
if ( !defined( 'ABSPATH' ) ) {
	exit();
}

$donate_setting = apply_filters( 'donate_admin_settings', array() );
?>

<?php if ( $donate_setting ): ?>

	<?php $current_tab = isset( $_GET['tab'] ) && $_GET['tab'] ? $_GET['tab'] : current( array_keys( $donate_setting ) ) ?>

    <form method="POST" name="tp_donate_options" action="options.php">
		<?php settings_fields( $this->options->_prefix ); ?>
        <div class="wrap tp_donate_setting_wrapper">

            <!--	Tabs	-->
            <h2 class="nav-tab-wrapper">
				<?php foreach ( $donate_setting as $key => $title ): ?>

                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=tp_donate_setting&tab=' . $key ) ); ?>" class="nav-tab<?php echo $current_tab === $key ? ' nav-tab-active' : '' ?>" data-tab="<?php echo esc_attr( $key ) ?>">
						<?php printf( '%s', $title ) ?>
                    </a>

				<?php endforeach; ?>
            </h2>

            <!--	Content 	-->
            <div class="tp_donate_wrapper_content">

				<?php foreach ( $donate_setting as $key => $title ): ?>

                    <div id="<?php echo esc_attr( $key ) ?>">
						<?php do_action( 'donate_admin_setting_' . $key . '_content' ); ?>
                    </div>

				<?php endforeach; ?>

            </div>

        </div>

		<?php submit_button(); ?>

    </form>

<?php endif; ?>
