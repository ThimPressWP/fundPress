<?php
/**
 * Fundpress Abstract settings class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Setting_Base' ) ) {
	/**
	 * Class DN_Setting_Base.
	 */
	abstract class DN_Setting_Base extends DN_Settings {

		/**
		 * @var null
		 */
		public $_id = null;

		/**
		 * @var null
		 */
		protected $_title = null;

		/**
		 * @var array
		 */
		protected $_fields = array();

		/**
		 * @var bool
		 */
		public $_tab = false;

		/**
		 * @var null
		 */
		public $_options = null;

		/**
		 * @var int
		 */
		protected $_position = 1;

		/**
		 * DN_Setting_Base constructor.
		 */
		public function __construct() {
			if ( is_admin() ) {
				add_filter( 'donate_admin_settings', array( $this, 'add_tab' ), $this->_position, 1 );
				add_action( 'donate_admin_setting_' . $this->_id . '_content', array(
					$this,
					'layout'
				), $this->_position, 1 );
			}

			$this->options();
			add_filter( 'donate_settings_field', array( $this, 'settings' ) );
		}

		/**
		 * Settings.
		 *
		 * @param $settings
		 *
		 * @return mixed
		 */
		public function settings( $settings ) {
			$settings[ $this->_id ] = $this;

			return $settings;
		}

		/**
		 * Add setting tab.
		 *
		 * @param $tabs
		 *
		 * @return mixed
		 */
		public function add_tab( $tabs ) {
			if ( $this->_id && $this->_title ) {
				$tabs[ $this->_id ] = $this->_title;

				return $tabs;
			}

			return array();
		}

		/**
		 * Generate layout.
		 */
		public function layout() {
			// before tab content
			do_action( 'donate_admin_setting_before_setting_tab', $this->_id );

			$this->_fields = apply_filters( 'donate_admin_setting_fields', $this->load_field(), $this->_id );

			if ( $this->_fields ) {
				$html = array();
				if ( $this->_tab ) {
					$html[] = '<h3>';
					foreach ( $this->_fields as $id => $groups ) {
						$html[] = '<a href="#" id="' . esc_attr( $id ) . '">' . $groups['title'] . '</a>';
					}
					$html[] = '</h3>';
				}

				if ( $this->_tab ) {
					foreach ( $this->_fields as $id => $groups ) {
						$html[] = '<div data-tab-id="' . $id . '">';
						$html[] = $this->generate_fields( $groups );
						$html[] = '</div>';
					}
				} else {
					$html[] = $this->generate_fields( $this->_fields );
				}

				echo implode( '', $html );
			}
			// after tab content
			do_action( 'donate_admin_setting_after_setting_tab' . $this->_id, $this->_id );
		}

		/**
		 * Setting fields.
		 *
		 * @return array
		 */
		protected function load_field() {
			return array();
		}

		/**
		 * Render atts.
		 *
		 * @param array $atts
		 *
		 * @return mixed
		 */
		public function render_atts( $atts = array() ) {
			if ( ! is_array( $atts ) ) {
				return false;
			}

			$html = array();
			foreach ( $atts as $key => $value ) {
				if ( is_array( $value ) ) {
					$value = implode( ' ', $value );
				}
				$html[] = $key . '="' . esc_attr( $value ) . '"';
			}

			return implode( ' ', $html );
		}

		/**
		 * Load options.
		 *
		 * @return mixed|null
		 */
		protected function options() {
			if ( $this->_options ) {
				return $this->_options;
			}

			$options = parent::options();

			if ( ! $options ) {
				$options = get_option( $this->_prefix, null );
			}

			if ( isset( $options[ $this->_id ] ) ) {
				return $this->_options = $options[ $this->_id ];
			}

			return null;
		}

		/**
		 * Get opition value.
		 *
		 * @param null $name
		 * @param null $default
		 *
		 * @return null|string
		 */
		public function get( $name = null, $default = null ) {
			if ( ! $this->_options ) {
				$this->_options = $this->options();
			}

			if ( $name && isset( $this->_options[ $name ] ) ) {
				return trim( $this->_options[ $name ] );
			}

			return $default;
		}

		/**
		 * Get field name.
		 *
		 * @param null $name
		 * @param null $group
		 *
		 * @return string
		 */
		public function get_field_id( $name = null, $group = null ) {
			if ( ! $this->_prefix || ! $name ) {
				return false;
			}

			if ( ! $group ) {
				$group = $this->_id;
			}

			if ( $group ) {
				return $this->_prefix . '_' . $group . '_' . $name;
			}

			return $this->_prefix . '_' . $name;
		}

		/**
		 * Get field name.
		 *
		 * @param null $name
		 * @param null $group
		 *
		 * @return string
		 */
		public function get_field_name( $name = null, $group = null ) {
			if ( ! $this->_prefix || ! $name ) {
				return false;
			}

			if ( ! $group ) {
				$group = $this->_id;
			}

			if ( $group ) {
				return $this->_prefix . '[' . $group . '][' . $name . ']';
			}

			return $this->_prefix . '[' . $name . ']';
		}

		/**
		 * Generate setting fields.
		 *
		 * @param array $groups
		 *
		 * @return string
		 */
		function generate_fields( $groups = array() ) {
			$html = array();
			foreach ( $groups as $key => $group ) {
				if ( isset( $group['title'], $group['desc'] ) ) {
					$html[] = '<h3>' . sprintf( '%s', $group['title'] ) . '</h3>';
					$html[] = '<p>' . sprintf( '%s', $group['desc'] ) . '</p>';
				}

				if ( isset( $group['fields'] ) ) {
					$html[] = '<table>';
					foreach ( $group['fields'] as $type => $field ) {

						$default = array(
							'type'    => '',
							'label'   => '',
							'desc'    => '',
							'atts'    => array(
								'id'    => '',
								'class' => ''
							),
							'name'    => '',
							'group'   => $this->_id ? $this->_id : null,
							'options' => array(),
							'default' => ''
						);
						if ( isset( $field['filter'] ) && $field['filter'] ) {
							ob_start();
							call_user_func_array( $field['filter'], array( $field ) );
							$html[] = ob_get_clean();
						} else if ( isset( $field['name'], $field['type'] ) ) {
							$html[] = '<tr>';

							// label
							$html[] = '<th><label for="' . $this->get_field_id( $field['name'] ) . '">' . sprintf( '%s', $field['label'] ) . '</label>';

							if ( isset( $field['desc'] ) ) {
								$html[] = '<p><small>' . sprintf( '%s', $field['desc'] ) . '</small></p>';
							}

							$html[] = '</th>';
							// end label
							// field
							$html[] = '<td>';

							$field = wp_parse_args( $field, $default );

							ob_start();
							include FUNDPRESS_INC . '/admin/views/settings/fields/' . $field['type'] . '.php';
							$html[] = ob_get_clean();

							$html[] = '</td>';
							// end field

							$html[] = '</tr>';
						}
					}
					$html[] = '</table>';
				}
			}

			return implode( '', $html );
		}
	}
}