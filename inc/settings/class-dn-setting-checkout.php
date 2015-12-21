<?php

class DN_Setting_Checkout extends DN_Setting_Base
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
						'title'	=> __( 'General settings', 'tp-donate' ),
						'desc'	=> __( 'The following options affect how format are displayed list donate causes on the frontend.', 'tp-donate' ),
						'fields'		=> array(
								array(
										'type'		=> 'select',
										'label'		=> __( 'Donate redirect.', 'tp-donate' ),
										'desc'		=> __( 'This controlls redirect page on donate submit?', 'tp-donate' ),
										'atts'		=> array(
												'id'	=> 'donate_redirect',
												'class'	=> 'donate_redirect'
											),
										'name'		=> 'donate_redirect',
										'options'	=> array(
												'cart'				=> __( 'Cart', 'tp-donate' ),
												'checkout'			=> __( 'Checkout', 'tp-donate' )
											)
									),
								array(
										'type'		=> 'select',
										'label'		=> __( 'Cart page', 'tp-donate' ),
										'desc'		=> __( 'This controlls set Cart page', 'tp-donate' ),
										'atts'		=> array(
												'id'	=> 'cart_page',
												'class'	=> 'cart_page'
											),
										'name'		=> 'cart_page',
										'options'	=> donate_get_pages_setting()
									),
								array(
										'type'		=> 'select',
										'label'		=> __( 'Checkout page', 'tp-donate' ),
										'desc'		=> __( 'This controlls set Checkout page', 'tp-donate' ),
										'atts'		=> array(
												'id'	=> 'checkout_page',
												'class'	=> 'checkout_page'
											),
										'name'		=> 'checkout_page',
										'options'	=> donate_get_pages_setting()
									)
							)
					),
					array(
						'title'	=> __( 'Checkout page setting', 'tp-donate' ),
						'desc'	=> __( 'The following options affect how format are displayed list donate causes on the checkout page.', 'tp-donate' ),
						'fields'		=> array(
								array(
										'type'		=> 'select',
										'label'		=> __( 'Show terms & Conditions', 'tp-donate' ),
										'desc'		=> __( 'This controlls display term & condition in checkout page', 'tp-donate' ),
										'atts'		=> array(
												'id'	=> 'term_condition',
												'class'	=> 'term_condition'
											),
										'name'		=> 'term_condition',
										'options'	=> array(
												'yes'			=> __( 'Yes', 'tp-donate' ),
												'no'			=> __( 'No', 'tp-donate' )
											)
									),
								array(
										'type'		=> 'select',
										'label'		=> __( 'Name on donors list?', 'tp-donate' ),
										'desc'		=> __( 'This controlls hide name on donors box', 'tp-donate' ),
										'atts'		=> array(
												'id'	=> 'term_condition',
												'class'	=> 'term_condition'
											),
										'name'		=> 'term_condition',
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

$GLOBALS[ 'checkout_settings' ] = new DN_Setting_Checkout();