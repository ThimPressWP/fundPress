<?php

class DN_Setting_Checkout extends DN_Setting_Page
{
	/**
	 * setting id
	 * @var string
	 */
	public $_id = 'checkout';

	/**
	 * _title
	 * @var null
	 */
	public $_title = null;

	/**
	 * $_position
	 * @var integer
	 */
	public $_position = 30;

	public function __construct()
	{
		$this->_title = __( 'Checkout', 'tp-donate' );
		parent::__construct();
	}

	// render fields
	public function load_field()
	{
		return
			array(
				array(
						'title'	=> __( 'Archive setting', 'tp-donate' ),
						'desc'	=> __( 'The following options affect how format are displayed list donate causes on the frontend.', 'tp-donate' ),
						'fields'		=> array(
								array(
										'type'		=> 'select',
										'label'		=> __( 'Lightbox', 'tp-donate' ),
										'desc'		=> __( 'This controlls using lightbox donate. Yes or No?', 'tp-donate' ),
										'atts'		=> array(
												'id'	=> 'lightbox',
												'class'	=> 'lightbox'
											),
										'name'		=> 'archive_lightbox',
										'options'	=> array(
												'yes'			=> __( 'Yes', 'tp-donate' ),
												'no'			=> __( 'No', 'tp-donate' )
											)
									)
							)
					)
			);
	}

}

new DN_Setting_Checkout();
