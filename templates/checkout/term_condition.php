<?php
/**
 * Template for displaying term condition in checkout page.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/checkout/term_condition.php
 *
 * @version     2.0
 * @package     Template
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
?>

<?php $term_condition_page_id = FP()->settings->checkout->get( 'term_condition_page' ); ?>
<?php $enable = FP()->settings->checkout->get( 'term_condition', 'yes' ); ?>

<?php if ( $enable === 'yes' && $term_condition_page_id ) { ?>
    <div class="donate_term_condition">
        <input type="checkbox" name="term_condition" value="1" id="term_condition"/>
        <label for="term_condition">
			<?php _e( 'Terms & Conditions', 'fundpress' ); ?>
        </label>
    </div>
<?php } ?>
