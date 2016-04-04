<?php

if( ! function_exists( 'donate_18n_languages' ) )
{
	function donate_18n_languages()
	{
		$l18n = array(

				'amount_invalid'			=> __( 'Please enter donate amount.', 'tp-donate' ),
				'email_invalid'				=> __( 'Please enter valid email. Eg: example@example.com', 'tp-donate' ),
				'first_name_invalid'		=> __( 'First name invalid, min length 3 and max length 15 character.', 'tp-donate' ),
				'last_name_invalid'			=> __( 'Last name invalid, min length 3 and max length 15 character.', 'tp-donate' ),
				'phone_number_invalid'		=> __( 'Phone number invalid. Eg: 01365987521.', 'tp-donate' ),
				'payment_method_invalid'	=> __( 'Please select payment method.', 'tp-donate' ),
				'address_invalid'			=> __( 'Please enter your address.', 'tp-donate' )
			);
		return apply_filters( 'donate_l18n', $l18n );
	}
}