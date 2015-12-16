<?php

class DN_Admin_Setting_General extends DN_Setting_Page
{
	/**
	 * setting id
	 * @var string
	 */
	public $_id = 'general';

	/**
	 * _title
	 * @var null
	 */
	public $_title = null;

	/**
	 * $_position
	 * @var integer
	 */
	public $_position = 10;

	public function __construct()
	{
		$this->_title = __( 'General', 'tp-donate' );
		parent::__construct();
	}

	// render fields
	public function load_field()
	{
		return
			array(
				array(
						'title'	=> __( '1' ),
					),
				array(
						'title'	=> __( 'Currency', 'tp-donate' ),
						'desc'	=> __( 'The following options affect how prices are displayed on the frontend.', 'tp-donate' ),
						'fields'		=> array(
								'select'	=> array(
										'label'		=> __( 'Currency', 'tp-donate' ),
										'atts'		=> array(
												'id'	=> 'currency',
												'class'	=> 'currency'
											),
										'name'		=> 'currency',
										'options'	=> donate_get_currencies()
									),
								'select'	=> array(
										'label'		=> __( 'Currency Position', 'tp-donate' ),
										'atts'		=> array(
												'id'	=> 'currency_position',
												'class'	=> 'currency_position'
											),
										'name'		=> 'currency_position',
										'options'	=> array(
												'left'			=> __( 'Left', 'tp-donate' ) . ' ' . '(£99.99)',
												'right'			=> __( 'Right', 'tp-donate' ) . ' ' . '(99.99£)',
												'left_space'	=> __( 'Left with space', 'tp-donate' ) . ' ' . '(£ 99.99)',
												'right_space'	=> __( 'Right with space', 'tp-donate' ) . ' ' . '(99.99 £)',
											)
									),
								'input'		=> array(
										'label'		=> __( 'Thousand Separator', 'tp-donate' ),
										'atts'		=> array(
												'id'	=> 'thousand',
												'class'	=> 'thousand'
											),
										'name'		=> 'currency_thousand',
									),
								'input'		=> array(
										'label'		=> __( 'Decimal Separator', 'tp-donate' ),
										'atts'		=> array(
												'id'	=> 'decimal',
												'class'	=> 'decimal'
											),
										'name'		=> 'currency_decimal',
									),
								'input'		=> array(
										'label'		=> __( 'Number of Decimals', 'tp-donate' ),
										'type'		=> 'number',
										'atts'		=> array(
												'id'	=> 'decimals',
												'class'	=> 'decimals'
											),
										'name'		=> 'currency_num_decimal',
									),
							)
					)
			);
	}

}

new DN_Admin_Setting_General();