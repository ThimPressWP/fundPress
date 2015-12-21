<?php

class DN_Setting_Donate extends DN_Setting_Base
{
	/**
	 * setting id
	 * @var string
	 */
	public $_id = 'donate';

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
		$this->_title = __( 'Donate', 'tp-donate' );
		parent::__construct();
	}

	// render fields
	public function load_field()
	{
		return
			array(
				array(
						'title'	=> __( 'Archive settings', 'tp-donate' ),
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
									),
								array(
										'type'		=> 'select',
										'label'		=> __( 'Raised and Goal', 'tp-donate' ),
										'desc'		=> __( 'Display raised and goal on the frontend', 'tp-donate' ),
										'atts'		=> array(
												'id'	=> 'raised_goal',
												'class'	=> 'raised_goal'
											),
										'name'		=> 'archive_raised_goal',
										'options'	=> array(
												'yes'			=> __( 'Yes', 'tp-donate' ),
												'no'			=> __( 'No', 'tp-donate' )
											)
									),
								array(
										'type'		=> 'select',
										'label'		=> __( 'Countdown Raised', 'tp-donate' ),
										'desc'		=> __( 'Display countdown raised on the frontend', 'tp-donate' ),
										'atts'		=> array(
												'id'	=> 'countdown_raised',
												'class'	=> 'countdown_raised'
											),
										'name'		=> 'archive_countdown_raised',
										'options'	=> array(
												'yes'			=> __( 'Yes', 'tp-donate' ),
												'no'			=> __( 'No', 'tp-donate' )
											)
									)
							)
					),

				array(
						'title'	=> __( 'Single setting', 'tp-donate' ),
						'desc'	=> __( 'The following options affect how format are displayed single page on the frontend.', 'tp-donate' ),
						'fields'		=> array(
								array(
										'type'		=> 'select',
										'label'		=> __( 'Lightbox', 'tp-donate' ),
										'desc'		=> __( 'This controlls using lightbox donate. Yes or No?', 'tp-donate' ),
										'atts'		=> array(
												'id'	=> 'single_lightbox',
												'class'	=> 'lightbox'
											),
										'name'		=> 'lightbox',
										'options'	=> array(
												'yes'			=> __( 'Yes', 'tp-donate' ),
												'no'			=> __( 'No', 'tp-donate' )
											)
									),
								array(
										'type'		=> 'select',
										'label'		=> __( 'Raised and Goal', 'tp-donate' ),
										'desc'		=> __( 'Display raised and goal on the frontend', 'tp-donate' ),
										'atts'		=> array(
												'id'	=> 'raised_goal',
												'class'	=> 'raised_goal'
											),
										'name'		=> 'single_raised_goal',
										'options'	=> array(
												'yes'			=> __( 'Yes', 'tp-donate' ),
												'no'			=> __( 'No', 'tp-donate' )
											)
									),
								array(
										'type'		=> 'select',
										'label'		=> __( 'Countdown Raised', 'tp-donate' ),
										'desc'		=> __( 'Display countdown raised on the frontend', 'tp-donate' ),
										'atts'		=> array(
												'id'	=> 'countdown_raised',
												'class'	=> 'countdown_raised'
											),
										'name'		=> 'single_countdown_raised',
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

$GLOBALS[ 'donate_settings' ] = new DN_Setting_Donate();