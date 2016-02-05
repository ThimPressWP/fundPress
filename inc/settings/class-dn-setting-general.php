<?php

class DN_Setting_General extends DN_Setting_Base
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
						'title'	=> __( 'Currency settings', 'tp-donate' ),
						'desc'	=> __( 'The following options affect how prices are displayed on the frontend.', 'tp-donate' ),
						'fields'		=> array(
								array(
										'type'		=> 'input',
										'label'		=> __( 'Donation system', 'tp-donate' ),
										'desc'		=> __( 'Donation system without campaign.', 'tp-donate' ),
										'name'		=> '',
										'atts'		=> array(
												'type'	=> 'hidden',
												'id'	=> 'donate_system',
												'class'	=> 'donate_system'
											),
										'filter'	=> 'donation_sytem_total_amount'
									),
								array(
										'type'		=> 'select',
										'label'		=> __( 'Currency aggregator', 'tp-donate' ),
										'desc'		=> __( 'This controlls what the currency prices when change currency setting.', 'tp-donate' ),
										'atts'		=> array(
												'id'	=> 'aggregator',
												'class'	=> 'aggregator'
											),
										'name'		=> 'aggregator',
										'options'	=> array(
												'google'		=> 'http://google.com/finance',
												'yahoo'			=> 'http://finance.yahoo.com'
											),
										'default'	=> array()
									),
								array(
										'type'		=> 'select',
										'label'		=> __( 'Currency', 'tp-donate' ),
										'desc'		=> __( 'This controlls what the currency prices.', 'tp-donate' ),
										'atts'		=> array(
												'id'	=> 'currency',
												'class'	=> 'currency'
											),
										'name'		=> 'currency',
										'options'	=> donate_get_currencies(),
										'default'	=> array()
									),
								array(
										'type'		=> 'select',
										'label'		=> __( 'Currency Position', 'tp-donate' ),
										'desc'		=> __( 'This controlls the position of the currency symbol.', 'tp-donate' ),
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
											),
										'default'	=> array()
									),
								array(
										'type'		=> 'input',
										'label'		=> __( 'Thousand Separator.', 'tp-donate' ),
										'atts'		=> array(
												'type'	=> 'text',
												'id'	=> 'thousand',
												'class'	=> 'thousand'
											),
										'name'		=> 'currency_thousand',
										'default'	=> ','
									),
								array(
										'type'		=> 'input',
										'label'		=> __( 'Decimal Separator.', 'tp-donate' ),
										'atts'		=> array(
												'type'	=> 'text',
												'id'	=> 'decimal_separator',
												'class'	=> 'decimal_separator'
											),
										'name'		=> 'currency_separator',
										'default'	=> '.'
									),
								array(
										'type'		=> 'input',
										'label'		=> __( 'Number of Decimals.', 'tp-donate' ),
										'atts'		=> array(
												'type'	=> 'number',
												'id'	=> 'decimals',
												'class'	=> 'decimals',
												'min'	=> 0
											),
										'name'		=> 'currency_num_decimal',
										'default'	=> '2'
									)
							)
					)
			);
	}

}

$GLOBALS[ 'general_settings' ] = new DN_Setting_General();

if( ! function_exists( 'donation_sytem_total_amount' ) )
{
	function donation_sytem_total_amount( $field )
	{
		?>
			<tr>
				<th>
					<?php if( isset( $field['label'] ) ) : ?>
						<label for="<?php echo esc_attr( $field[ 'name' ] ) ?>"><?php printf( '%s', $field['label'] ) ?></label>
						<?php if( isset( $field[ 'desc' ] ) ) : ?>
							<p><small><?php printf( '%s', $field['desc'] ) ?></small></p>
						<?php endif; ?>
					<?php endif; ?>
				</th>
				<td>
					<input type="text" value="<?php echo esc_attr( donate_amount_system() ); ?>" readonly="readonly" />
					<?php printf( '%s', donate_get_currency_symbol() ) ?>
				</td>
			</tr>

		<?php
	}
}