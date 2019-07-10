<?php
/**
 * Fundpress Setting donate class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Setting_Donate' ) ) {
	/**
	 * Class DN_Setting_Donate.
	 */
	class DN_Setting_Donate extends DN_Setting_Base {

		/**
		 * @var string
		 */
		public $_id = 'donate';

		/**
		 * @var null|string|void
		 */
		public $_title = null;

		/**
		 * @var int
		 */
		public $_position = 30;

		/**
		 * DN_Setting_Donate constructor.
		 */
		public function __construct() {
			$this->_title = __( 'Donate', 'fundpress' );
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
					'title'  => __( 'Archive settings', 'fundpress' ),
					'desc'   => __( 'The following options affect how format are displayed list donate causes on the frontend.', 'fundpress' ),
					'fields' => array(
						array(
							'type'    => 'input',
							'label'   => __( 'Archive columns', 'fundpress' ),
							'desc'    => __( 'This controls how many column archive page.', 'fundpress' ),
							'atts'    => array(
								'id'    => 'columns',
								'class' => 'columns',
								'min'   => 1,
								'max'   => 4,
								'type'  => 'number'
							),
							'name'    => 'archive_column',
							'default' => 4
						),
						array(
							'type'    => 'select',
							'label'   => __( 'Raised and Goal', 'fundpress' ),
							'desc'    => __( 'Display raised and goal on the frontend', 'fundpress' ),
							'atts'    => array(
								'id'    => 'raised_goal',
								'class' => 'raised_goal'
							),
							'name'    => 'archive_raised_goal',
							'options' => array(
								'yes' => __( 'Yes', 'fundpress' ),
								'no'  => __( 'No', 'fundpress' )
							),
							'default' => 'yes'
						),
						array(
							'type'    => 'select',
							'label'   => __( 'Countdown Raised', 'fundpress' ),
							'desc'    => __( 'Display countdown raised on the frontend', 'fundpress' ),
							'atts'    => array(
								'id'    => 'countdown_raised',
								'class' => 'countdown_raised'
							),
							'name'    => 'archive_countdown_raised',
							'options' => array(
								'yes' => __( 'Yes', 'fundpress' ),
								'no'  => __( 'No', 'fundpress' )
							),
							'default' => 'yes'
						)
					)
				),
				array(
					'title'  => __( 'Single setting', 'fundpress' ),
					'desc'   => __( 'The following options affect how format are displayed single page on the frontend.', 'fundpress' ),
					'fields' => array(
						array(
							'type'    => 'select',
							'label'   => __( 'Raised and Goal', 'fundpress' ),
							'desc'    => __( 'Display raised and goal on the frontend', 'fundpress' ),
							'atts'    => array(
								'id'    => 'raised_goal',
								'class' => 'raised_goal'
							),
							'name'    => 'single_raised_goal',
							'options' => array(
								'yes' => __( 'Yes', 'fundpress' ),
								'no'  => __( 'No', 'fundpress' )
							),
							'default' => 'yes'
						),
						array(
							'type'    => 'select',
							'label'   => __( 'Countdown Raised', 'fundpress' ),
							'desc'    => __( 'Display countdown raised on the frontend', 'fundpress' ),
							'atts'    => array(
								'id'    => 'countdown_raised',
								'class' => 'countdown_raised'
							),
							'name'    => 'single_countdown_raised',
							'options' => array(
								'yes' => __( 'Yes', 'fundpress' ),
								'no'  => __( 'No', 'fundpress' )
							),
							'default' => 'yes'
						)
					)
				)
			);
		}
	}
}

$GLOBALS['donate_settings'] = new DN_Setting_Donate();
