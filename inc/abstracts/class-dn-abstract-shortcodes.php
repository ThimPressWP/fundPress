<?php
/**
 * Fundpress Abstract shortcodes class.
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
	 * Class DN_Shortcode_Base.
	 */
	abstract class DN_Shortcode_Base {

		/**
		 * @var null
		 */
		protected $_template = null;

		/**
		 * @var null
		 */
		protected $_shortcodeName = null;

		/**
		 * DN_Shortcode_Base constructor.
		 */
		public function __construct() {
			if ( ! $this->_shortcodeName || ! $this->_template ) {
				return;
			}

			add_shortcode( $this->_shortcodeName, array( $this, 'add_shortcode' ) );
			add_action( 'donate_before_wrap_shortcode', array( $this, 'shortcode_start_wrap' ) );
			add_action( 'donate_after_wrap_shortcode', array( $this, 'shortcode_end_wrap' ) );
		}

		/**
		 * Add start wrap shortcode html.
		 *
		 * @return string
		 */
		public function shortcode_start_wrap() {
			return '<div class="donate_wrapper ' . $this->_shortcodeName . '">';
		}

		/**
		 * Add end wrap shortcode html.
		 *
		 * @return string
		 */
		public function shortcode_end_wrap() {
			return '</div>';
		}

		/**
		 * Parse atts.
		 *
		 * @param $atts
		 *
		 * @return mixed
		 */
		public function parses( $atts ) {
			return apply_filters( 'donate_shortcode_atts', $atts, $this->_shortcodeName );
		}

		/**
		 * Shortcode callback.
		 *
		 * @param $atts
		 * @param null $content
		 *
		 * @return string
		 */
		public function add_shortcode( $atts, $content = null ) {
			ob_start();
			do_action( 'donate_before_wrap_shortcode', $this->_shortcodeName );

			donate_get_template( 'shortcodes/' . $this->_template, $this->parses( $atts ) );

			do_action( 'donate_after_wrap_shortcode', $this->_shortcodeName );

			return ob_get_clean();
		}

	}
}
