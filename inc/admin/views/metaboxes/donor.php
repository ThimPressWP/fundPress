<?php
/**
 * Admin view: Donor meta box.
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
global $post;
$donor          = DN_Donor::instance( $post->Id );
$donated_amount = $donor->get_donated();
?>

    <table>
        <thead>
        <tr>
            <th><?php _e( 'ID', 'fundpress' ) ?></th>
            <th><?php _e( 'First name', 'fundpress' ) ?></th>
            <th><?php _e( 'Last name', 'fundpress' ) ?></th>
            <th><?php _e( 'Email', 'fundpress' ) ?></th>
            <th><?php _e( 'Address', 'fundpress' ) ?></th>
            <th><?php _e( 'Phone', 'fundpress' ) ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th><?php printf( '%s', donate_generate_post_key( $post->ID ) ) ?></th>
            <td><?php printf( '%s', $this->get_field_value( 'first_name' ) ) ?></td>
            <td><?php printf( '%s', $this->get_field_value( 'last_name' ) ) ?></td>
            <td>
                <a href="mailto:<?php printf( '%s', $this->get_field_value( 'email' ) ) ?>"><?php printf( '%s', $this->get_field_value( 'email' ) ) ?></a>
            </td>
            <td><?php printf( '%s', $this->get_field_value( 'address' ) ) ?></td>
            <td><?php printf( '%s', $this->get_field_value( 'phone' ) ) ?></td>
        </tr>
        </tbody>
    </table>
<?php if ( $donated_amount ) { ?>
    <h3><?php _e( 'Donated', 'fundpress' ) ?></h3>

    <table>
        <thead>
        <tr>
            <th><?php _e( 'Donate ID', 'fundpress' ) ?></th>
            <th><?php _e( 'Amount', 'fundpress' ) ?></th>
        </tr>
        </thead>
        <tbody>
		<?php foreach ( $donated_amount as $key => $donated ) { ?>
			<?php $donation = DN_Donate::instance( $donated->ID ) ?>
            <tr>
                <th>
                    <a href="<?php echo esc_url( get_edit_post_link( $donated->ID ) ); ?>"><?php printf( '%s', donate_generate_post_key( $donated->ID ) ) ?></a>
                </th>
                <td>
					<?php echo donate_price( $donation->get_meta( 'total' ), $donation->get_meta( 'currency' ) ) ?>
                </td>
            </tr>
		<?php } ?>
        </tbody>
    </table>

<?php } ?>