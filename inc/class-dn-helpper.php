<?php

/**
 * Fundpress assets class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */
defined( 'ABSPATH' ) || exit();
if ( ! class_exists( 'DN_Helpper' ) ) {
    class DN_Helpper{
        private static $_instance = null;
        public function __construct() {
            if ( self::$_instance ) {
		return;
            }
            self::$_instance = $this;

        }
        public static function instance() {
            if (!self::$_instance) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }
        public static function DN_sanitize_params_submitted($value, $type_content = 'text') {
            $value = wp_unslash($value);

            if (is_string($value)) {
                switch ($type_content) {
                    case 'html':
                        $value = wp_kses_post($value);
                        break;
                    case 'textarea' :
                        $value = sanitize_textarea_field($value);
                        break;
                    default:
                        $value = sanitize_text_field(wp_unslash($value));
                }
            } elseif (is_array($value)) {
                foreach ($value as $k => $v) {
                    $value[$k] = DN_Helpper::DN_sanitize_params_submitted($v, $type_content);
                }
            }

            return $value;
        }
        
    }
    
}
function DN() {
    return DN_Helpper::instance();
}
$GLOBALS['DN_Helpper'] = DN();