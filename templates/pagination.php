<?php
/**
 * Pagination - Show numbered pagination for catalog pages.
 *
 * @author 		ThimPress
 * @package 	Tp-donate/Templates
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wp_query;

if ( $wp_query->max_num_pages <= 1 ) {
	return;
}

?>
<nav class="donate-pagination">
	<?php
		echo paginate_links( apply_filters( 'donate_pagination_args', array(
			'base'         => esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) ),
			'format'       => '',
			'add_args'     => '',
			'current'      => max( 1, get_query_var( 'paged' ) ),
			'total'        => $wp_query->max_num_pages,
			'prev_text'    => __( 'Previous', 'fundpress' ),
			'next_text'    => __( 'Next', 'fundpress' ),
			'type'         => 'list',
			'end_size'     => 3,
			'mid_size'     => 3
		) ) );
	?>
</nav>
