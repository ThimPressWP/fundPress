<?php

class DN_Email
{

	function __construct()
	{
		// filter email setting
		add_filter( 'wp_mail_from', array( $this, 'set_email_from' ) );

		// filter email from name
		add_filter( 'wp_mail_from_name', array( $this, 'set_email_name' ) );
	}

	// set email from
	function set_email_from( $email )
	{
		if( $donate_email = DN_Settings::instance()->email->get( 'admin_email' ) )
		{
			return $donate_email;
		}

		return $email;
	}

	// set email name header
	function set_email_name( $name )
	{
		if( $donate_name = DN_Settings::instance()->email->get( 'from_name' ) )
		{
			return sanitize_title( $donate_name );
		}
		return $name;
	}

	// send email donate completed
	function send_email_donate_completed()
	{

	}

}

new DN_Email();