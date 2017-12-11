<?php
/**
 * Fundpress Abstract meta box class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_MetaBox_Base' ) ) {
	/**
	 * Class DN_MetaBox_Base.
	 */
	abstract class DN_MetaBox_Base {

		/**
		 * @var null
		 */
		protected $_id = null;

		/**
		 * @var string
		 */
		public $_prefix = 'donate_';

		/**
		 * @var null
		 */
		protected $_title = null;

		/**
		 * @var array
		 */
		protected $_name = array();

		/**
		 * @var null
		 */
		protected $_layout = null;

		/**
		 * @var array
		 */
		protected $_fields = array();

		/**
		 * @var array
		 */
		public $_screen = array( 'dn_campaign' );

		/**
		 * @var string
		 */
		public $_context = 'normal';

		/**
		 * @var string
		 */
		public $_priority = 'high';

		/**
		 * DN_MetaBox_Base constructor.
		 */
		public function __construct() {
			if ( ! $this->_id ) {
				return;
			}

			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
			add_action( 'delete_post', array( $this, 'delete' ) );
		}

		/**
		 * Add meta box.
		 */
		public function add_meta_box() {
			foreach ( $this->_screen as $post_type ) {
				add_meta_box(
					$this->_id, $this->_title, array( $this, 'render' ), $post_type, $this->_context, $this->_priority
				);
			}
		}

		/**
		 * Build meta box layout.
		 */
		public function render() {
			wp_nonce_field( 'thimpress_donate', 'thimpress_donate_metabox' );

			do_action( 'donate_metabox_before_render', $this->_id );

			// require_once $this->_layout;
			$this->_fields = apply_filters( 'donate_metabox_fields', $this->load_field(), $this->_id );

			if ( ! empty( $this->_fields ) ) {
				$html = array();

				$html[] = '<ul class="donate_metabox_setting" id="' . esc_attr( $this->_id ) . '">';
				$i      = 0;
				foreach ( $this->_fields as $id => $group ) {
					if ( isset( $group['title'] ) ) {
						$html[] = '<li><a href="#" id="' . esc_attr( $id ) . '"' . ( $i === 0 ? ' class="nav-tab-active"' : '' ) . '>' . sprintf( '%s', $group['title'] ) . '</a>';

						if ( isset( $group['desc'] ) ) {
							$html[] = '<p>' . sprintf( '%s', $group['desc'] ) . '</p>';
						}

						$html[] = '</li>';
					}
					$i ++;
				}
				$html[] = '</ul>';

				$html[] = '<div class="donate_metabox_setting_container">';
				foreach ( $this->_fields as $id => $group ) {
					$html[] = '<div class="donate_metabox_setting_section" data-id="' . esc_attr( $id ) . '">';
					if ( ! empty( $group['fields'] ) ) {
						$html[] = '<table>';
						foreach ( $group['fields'] as $type => $field ) {

							if ( isset( $field['name'], $field['type'] ) ) {
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
									'default' => null
								);

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
					} else {
						ob_start();
						do_action( 'donate_metabox_setting_section', $id );
						$html[] = ob_get_clean();
					}
					$html[] = '</div>';
				}
				$html[] = '</div>';
				echo implode( '', $html );
			} else if ( $this->_layout && file_exists( $this->_layout ) ) {
				$this->_layout = apply_filters( 'donate_metabox_layout', $this->_layout, $this->_id );
				require_once $this->_layout;
			}

			do_action( 'donate_metabox_after_render', $this->_id );
		}

		/**
		 * Setting fields.
		 *
		 * @return array
		 */
		public function load_field() {
			return array();
		}

		/**
		 * Get field name.
		 *
		 * @param string $name
		 *
		 * @return string
		 */
		public function get_field_name( $name = '' ) {
			return $this->_prefix . $name;
		}

		/**
		 * Get field value.
		 *
		 * @param string $name
		 * @param bool $default
		 *
		 * @return bool|mixed
		 */
		public function get_field_value( $name = '', $default = false ) {
			global $post;
			$post_id = $post->ID;
			$value   = get_post_meta( $post_id, $this->_prefix . $name, true );
			if ( ! $value && $default ) {
				$value = $default;
			}

			return $value;
		}

		/**
		 * Get field id.
		 *
		 * @param $name
		 *
		 * @return string
		 */
		public function get_field_id( $name ) {
			return '';
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
		 * Get attribute.
		 *
		 * @param string $name
		 *
		 * @return bool|mixed
		 */
		public function get( $name = '' ) {
			return $this->get_field_value( $name );
		}

		/**
		 * Delete post meta.
		 *
		 * @param $post_id
		 */
		public function delete( $post_id ) {

		}

	}
}