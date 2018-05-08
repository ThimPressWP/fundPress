<?php
/**
 * Fundpress Setting general class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Setting_General' ) ) {
	/**
	 * Class DN_Setting_General.
	 */
	class DN_Setting_General extends DN_Setting_Base {
		/**
		 * @var string
		 */
		public $_id = 'general';

		/**
		 * @var null|string|void
		 */
		public $_title = null;

		/**
		 * @var int
		 */
		public $_position = 10;

		/**
		 * DN_Setting_General constructor.
		 */
		public function __construct() {
			$this->_title = __( 'General', 'fundpress' );
			parent::__construct();
		}

		/**
		 * Setting fields.
		 *
		 * @return array
		 */
		public function load_field() {
			return array(
				array(
					'title'  => __( 'Currency settings', 'fundpress' ),
					'desc'   => __( 'The following options affect how prices are displayed on the frontend.', 'fundpress' ),
					'fields' => array(
						array(
							'type'   => 'input',
							'label'  => __( 'Donation system', 'fundpress' ),
							'desc'   => __( 'Donation system without campaign.', 'fundpress' ),
							'name'   => '',
							'atts'   => array(
								'type'  => 'hidden',
								'id'    => 'donate_system',
								'class' => 'donate_system'
							),
							'filter' => 'donation_system_total_amount'
						),
						array(
							'type'    => 'select',
							'label'   => __( 'Currency aggregator', 'fundpress' ),
							'desc'    => __( 'This controls what the currency prices when change currency setting.', 'fundpress' ),
							'atts'    => array(
								'id'    => 'aggregator',
								'class' => 'aggregator'
							),
							'name'    => 'aggregator',
							'options' => array(
								'google' => 'http://google.com/finance'
							),
							'default' => 'google'
						),
						array(
							'type'    => 'select',
							'label'   => __( 'Currency', 'fundpress' ),
							'desc'    => __( 'This controls what the currency prices.', 'fundpress' ),
							'atts'    => array(
								'id'    => 'currency',
								'class' => 'currency'
							),
							'name'    => 'currency',
							'options' => donate_get_currencies(),
							'default' => 'USD'
						),
						array(
							'type'    => 'select',
							'label'   => __( 'Currency Position', 'fundpress' ),
							'desc'    => __( 'This controls the position of the currency symbol.', 'fundpress' ),
							'atts'    => array(
								'id'    => 'currency_position',
								'class' => 'currency_position'
							),
							'name'    => 'currency_position',
							'options' => array(
								'left'        => __( 'Left', 'fundpress' ) . ' ' . '(£99.99)',
								'right'       => __( 'Right', 'fundpress' ) . ' ' . '(99.99£)',
								'left_space'  => __( 'Left with space', 'fundpress' ) . ' ' . '(£ 99.99)',
								'right_space' => __( 'Right with space', 'fundpress' ) . ' ' . '(99.99 £)',
							),
							'default' => 'left'
						),
						array(
							'type'    => 'input',
							'label'   => __( 'Thousand Separator.', 'fundpress' ),
							'atts'    => array(
								'type'  => 'text',
								'id'    => 'thousand',
								'class' => 'thousand'
							),
							'name'    => 'currency_thousand',
							'default' => ','
						),
						array(
							'type'    => 'input',
							'label'   => __( 'Decimal Separator.', 'fundpress' ),
							'atts'    => array(
								'type'  => 'text',
								'id'    => 'decimal_separator',
								'class' => 'decimal_separator'
							),
							'name'    => 'currency_separator',
							'default' => '.'
						),
						array(
							'type'    => 'input',
							'label'   => __( 'Number of Decimals.', 'fundpress' ),
							'atts'    => array(
								'type'  => 'number',
								'id'    => 'decimals',
								'class' => 'decimals',
								'min'   => 0
							),
							'name'    => 'currency_num_decimal',
							'default' => '2'
						)
					)
				)
			);
		}
	}
}

$GLOBALS['general_settings'] = new DN_Setting_General();