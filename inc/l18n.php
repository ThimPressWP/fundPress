<?php

if( ! function_exists( 'donate_18n_languages' ) )
{
	function donate_18n_languages()
	{
		$l18n = array(

				'amount_invalid'			=> __( 'Please enter amount', 'tp-donate' ),
				'email_invalid'				=> __( 'Please enter valid email', 'tp-donate' ),
				'first_name_invalid'		=> __( 'First name invalid', 'tp-donate' ),
				'last_name_invalid'			=> __( 'Last name invalid', 'tp-donate' ),
				'phone_number_invalid'		=> __( 'Phone number invalid', 'tp-donate' ),
				'payment_method_invalid'	=> __( 'Plesae select payment method', 'tp-donate' ),

			);
		return apply_filters( 'donate_l18n', $l18n );
	}
}