<?php
/**
 * Admin view: WP Editor setting field.
 *
 * @version     2.0
 * @package     View
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
?>

<?php
wp_editor( $this->get( $field['name'] ), $this->get_field_id( $field['name'] ), array( 'textarea_name' => $this->get_field_name( $field['name'] ) ) );
?>