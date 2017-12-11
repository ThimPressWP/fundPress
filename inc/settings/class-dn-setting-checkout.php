<?php
/**
 * Fundpress Setting checkout class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Setting_Checkout' ) ) {
	/**
	 * Class DN_Setting_Checkout.
	 */
	class DN_Setting_Checkout extends DN_Setting_Base {

		/**
		 * @var string
		 */
		public $_id = 'checkout';

		/**
		 * @var null|string|void
		 */
		public $_title = null;

		/**
		 * @var bool
		 */
		public $_tab = true;

		/**
		 * @var int
		 */
		public $_position = 30;

		/**
		 * DN_Setting_Checkout constructor.
		 */
		public function __construct() {
			$this->_title = __( 'Checkout', 'fundpress' );
			parent::__construct();
		}

		/**
		 * Setting fields.
		 *
		 * @return array
		 */
		public function load_field() {
			return array(
				'checkout_general' => array(
					'title'  => __( 'General', 'fundpress' ),
					'fields' => array(
						'title'  => __( 'General settings', 'fundpress' ),
						'desc'   => __( 'The following options affect how format are displayed list donate causes on the frontend.', 'fundpress' ),
						'fields' => array(
							array(
								'type'    => 'select',
								'label'   => __( 'Environment', 'fundpress' ),
								'desc'    => __( 'This controls test or production mode', 'fundpress' ),
								'atts'    => array(
									'id'    => 'environment',
									'class' => 'environment'
								),
								'name'    => 'environment',
								'options' => array(
									'test'       => __( 'Test', 'fundpress' ),
									'production' => __( 'Production', 'fundpress' )
								),
								'default' => 'test'
							),
							array(
								'type'    => 'input',
								'label'   => __( 'Cancel Pending Order', 'fundpress' ),
								'desc'    => __( 'This controls how many time cancel Pending Order status.', 'fundpress' ),
								'atts'    => array(
									'id'    => 'cancel_order',
									'class' => 'cancel_order',
									'min'   => 1,
									'type'  => 'number'
								),
								'name'    => 'cancel_order',
								'default' => 12
							),
							array(
								'type'    => 'select',
								'label'   => __( 'Include lightbox', 'fundpress' ),
								'desc'    => __( 'This controls include payment lightbox donate form and not using Cart or Checkout page', 'fundpress' ),
								'atts'    => array(
									'id'    => 'lightbox_checkout',
									'class' => 'lightbox_checkout'
								),
								'name'    => 'lightbox_checkout',
								'options' => array(
									'no'  => __( 'No', 'fundpress' ),
									'yes' => __( 'Yes', 'fundpress' )
								),
								'default' => 'no'
							),
							array(
								'type'    => 'select',
								'label'   => __( 'Donate redirect.', 'fundpress' ),
								'desc'    => __( 'This controls redirect page on donate submit?', 'fundpress' ),
								'atts'    => array(
									'id'    => 'donate_redirect',
									'class' => 'donate_redirect'
								),
								'name'    => 'donate_redirect',
								'options' => array(
									'cart'     => __( 'Cart', 'fundpress' ),
									'checkout' => __( 'Checkout', 'fundpress' )
								),
								'default' => 'checkout'
							),
							array(
								'type'    => 'select',
								'label'   => __( 'Cart page', 'fundpress' ),
								'desc'    => __( 'This controls set Cart page', 'fundpress' ),
								'atts'    => array(
									'id'    => 'cart_page',
									'class' => 'cart_page'
								),
								'name'    => 'cart_page',
								'options' => donate_get_pages_setting(),
								'default' => ''
							),
							array(
								'type'    => 'select',
								'label'   => __( 'Checkout page', 'fundpress' ),
								'desc'    => __( 'This controls set Checkout page', 'fundpress' ),
								'atts'    => array(
									'id'    => 'checkout_page',
									'class' => 'checkout_page'
								),
								'name'    => 'checkout_page',
								'options' => donate_get_pages_setting(),
								'default' => ''
							),
							array(
								'type'    => 'select',
								'label'   => __( 'Terms and Conditions page', 'fundpress' ),
								'desc'    => __( 'This controls set Terms and Conditions page', 'fundpress' ),
								'atts'    => array(
									'id'    => 'term_condition_page',
									'class' => 'term_condition_page'
								),
								'name'    => 'term_condition_page',
								'options' => donate_get_pages_setting(),
								'default' => ''
							)
						)
					),
					array(
						'title'  => __( 'Checkout page setting', 'fundpress' ),
						'desc'   => __( 'The following options affect how format are displayed list donate causes on the checkout page.', 'fundpress' ),
						'fields' => array(
							array(
								'type'    => 'select',
								'label'   => __( 'Show terms & Conditions', 'fundpress' ),
								'desc'    => __( 'This controls display term & condition in checkout page', 'fundpress' ),
								'atts'    => array(
									'id'    => 'term_condition_enable',
									'class' => 'term_condition_enable'
								),
								'name'    => 'term_condition_enable',
								'options' => array(
									'yes' => __( 'Yes', 'fundpress' ),
									'no'  => __( 'No', 'fundpress' )
								),
								'default' => 'yes'
							)
						)
					)
				)
			);
		}
	}

}

$GLOBALS['checkout_settings'] = new DN_Setting_Checkout();
