<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $post;

?>

<?php if( has_post_thumbnail() ):  ?>

	<div class="campaign_thumbnail_figure">

		<!-- Thumbnail -->
		<a href="<?php the_permalink(); ?>">

			<?php the_post_thumbnail(); ?>

		</a>

		<!-- Overlay -->
		<div class="campaign_thumbnail_overlay">

			<a href="#" class="donate_load_form" data-campaign-id="<?php echo esc_attr( get_the_ID() ) ?>"><?php esc_html_e( 'Donate now', 'fundpress' ); ?></a>

		</div>

	</div>

<?php endif; ?>