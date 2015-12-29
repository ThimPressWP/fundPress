<?php

/**
 * get status label with html
 */
if( ! function_exists( 'donate_get_status_label' ) )
{
	function donate_get_status_label( $post_id )
	{
		$status = array(
				'donate-pending'	=> '<label class="donate-status donate-pending">'.__( 'Pending', 'tp-donate' ).'</span>',
				'donate-processing'	=> '<label class="donate-status donate-processing">'.__( 'Processing', 'tp-donate' ).'</span>',
				'donate-completed'	=> '<label class="donate-status donate-completed">'.__( 'Completed', 'tp-donate' ).'</span>',
			);

		$post_status = get_post_status( $post_id );
		if( array_key_exists( $post_status, $status ) )
			return apply_filters( 'donate_get_status_label', $status[ $post_status ], $post_id );

	}
}