<?php

class DN_Setting_Email extends DN_Setting_Page
{
	/**
	 * setting id
	 * @var string
	 */
	public $_id = 'email';

	/**
	 * _title
	 * @var null
	 */
	public $_title = null;

	/**
	 * $_position
	 * @var integer
	 */
	public $_position = 20;

	public function __construct()
	{
		$this->_title = __( 'Email', 'tp-donate' );
		parent::__construct();
	}

	// render fields
	public function load_field()
	{
		return
			array(
				array(
						'title'	=> __( 'Email Donate', 'tp-donate' ),
						'desc'	=> __( 'The following options affect how prices are displayed on the frontend.', 'tp-donate' ),
						'fields'		=> array(
								array(
										'type'		=> 'select',
										'label'		=> __( 'Enable', 'tp-donate' ),
										'desc'		=> __( 'This controlls what the currency prices', 'tp-donate' ),
										'atts'		=> array(
												'id'	=> 'enable',
												'class'	=> 'enable'
											),
										'name'		=> 'enable',
										'options'	=> array(
												'yes'			=> __( 'Yes', 'tp-donate' ),
												'no'			=> __( 'No', 'tp-donate' )
											),
										'default'	=> array()
									),
								array(
										'type'		=> 'input',
										'label'		=> __( 'From name', 'tp-donate' ),
										'desc'		=> __( 'This set email from name', 'tp-donate' ),
										'atts'		=> array(
												'id'			=> 'from_name',
												'class'			=> 'from_name',
												'placeholder'	=> get_option( 'blogname' )
											),
										'name'		=> 'from_name',
										'default'	=> ''
									),
								array(
										'type'		=> 'input',
										'label'		=> __( 'Email from', 'tp-donate' ),
										'desc'		=> __( 'This set email send', 'tp-donate' ),
										'atts'		=> array(
												'id'			=> 'admin_email',
												'class'			=> 'admin_email',
												'placeholder'	=> get_option( 'admin_email' )
											),
										'name'		=> 'admin_email',
										'default'	=> ''
									)
							)
					)
			);
	}

}

new DN_Setting_Email();