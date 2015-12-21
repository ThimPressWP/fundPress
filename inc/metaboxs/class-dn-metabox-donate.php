<?php

class DN_MetaBox_Base_Donate_Settings extends DN_MetaBox_Base
{
	/**
	 * id of the meta box
	 * @var null
	 */
	public $_id = null;

	/**
	 * title of meta box
	 * @var null
	 */
	public $_title = null;

	/**
	 * array meta key
	 * @var array
	 */
	public $_name = array();

	public function __construct()
	{
		$this->_id = 'donate_setting_section';
		$this->_title = __( 'Donate Settings', 'tp-donate' );
		$this->_layout = TP_DONATE_INC . '/metaboxs/views/donate-settings.php';
		parent::__construct();
	}

	/**
	 * load fields
	 * @return array
	 */
	public function load_field()
	{
		return
			array(
				'goal_raised'	=> array(
						'title'	=> __( 'Goal and Raised', 'tp-donate' ),
						'fields'		=> array(
								array(
										'type'		=> 'input',
										'label'		=> __( 'Goal', 'tp-donate' ),
										'desc'		=> __( 'This controlls how goal?', 'tp-donate' ),
										'atts'		=> array(
												'type'	=> 'text',
												'id'	=> 'goal',
												'class'	=> 'goal'
											),
										'name'		=> 'goal'
									),
								array(
										'type'		=> 'input',
										'label'		=> __( 'Donate redirect.', 'tp-donate' ),
										'desc'		=> __( 'This controlls redirect page on donate submit?', 'tp-donate' ),
										'atts'		=> array(
												'type'	=> 'text',
												'id'	=> 'raised',
												'class'	=> 'raised',
												'readonly'	=> 'readonly'
											),
										'name'		=> 'raised'
									)
							)
					),

				'compensate'	=> array(
					'title'	=> __( 'Compensate', 'tp-donate' ),
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
								)
						)
				)
			);
	}

}

new DN_MetaBox_Base_Donate_Settings();