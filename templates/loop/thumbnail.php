<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php if( has_post_thumbnail() ):  ?>

	<a href="<?php the_permalink(); ?>" data-campaign-id="<?php echo esc_attr( get_the_ID() ) ?>">

		<?php the_post_thumbnail(); ?>

	</a>

<?php endif; ?>