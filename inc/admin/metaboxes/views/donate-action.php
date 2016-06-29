<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

global $post;
?>
<style type="text/css">
	#donate_action .inside{
		padding: 0;
	}
	#donate_action #major-publishing-actions{
		overflow: hidden;
	}
</style>
<div class="submitbox" id="submitpost">
	<div id="major-publishing-actions">
		<?php if ( current_user_can( "delete_post", $post->ID ) ) : ?>
			<div id="delete-action">
				<a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post->ID ); ?>"><?php _e( 'Move to Trash', 'tp-donate' ); ?></a>
			</div>
		<?php endif; ?>

		<div id="publishing-action">
			<span class="spinner"></span>
			<button name="save" type="submit" class="button button-primary" id="publish">
				<?php printf( '%s', $post->post_status !== 'auto-draft' ? __( 'Update', 'tp-donate' ) : __( 'Save', 'tp-donate' ) ) ?>
			</button>
		</div>
	</div>
</div>