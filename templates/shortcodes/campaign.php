<?php
/**
 * Template for displaying campaign slider shortcode.
 *
 * This template can be overridden by copying it to yourtheme/fundpress/shortcodes/campaign.php
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

<?php if ( $id != '' && is_numeric( $id ) ) {
	$query = new WP_Query( array( 'post_type' => 'dn_campaign', 'post__in' => array( $id ) ) );
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post(); ?>

            <div class="donate-campaign">
				<?php if ( $title != '' ) { ?>
                    <div class="title-box"><?php echo esc_attr( $title ); ?></div>
				<?php } ?>

				<?php do_action( 'donate_loop_campaign_title' ); ?>
				<?php do_action( 'donate_loop_campaign_excerpt' ); ?>

                <div class="circle" id="circles-<?php echo esc_attr( get_the_id() ); ?>"></div>
                <script>
                    jQuery(function ($) {
                        $(window).load(function () {
                            setTimeout(function () {
                                var myCircle = Circles.create({
                                    id: 'circles-<?php echo esc_attr( get_the_id() );?>',
                                    radius: 83,
                                    value: <?php echo esc_attr( donate_get_campaign_percent() ) ?>,
                                    maxValue: 100,
                                    width: 10,
                                    text: function (value) {
                                        return '<div class="text-inner">' + value + '<span class="small">%</span><span class="text"><?php esc_html_e( 'Complete', 'thim-charitywp-shortcodes' ); ?></span></div>';
                                    },
                                    colors: ['#FFF', '#f8b864'],
                                    duration: 600,
                                    wrpClass: 'circles-wrp',
                                    textClass: 'circles-text',
                                    valueStrokeClass: 'circles-valueStroke',
                                    maxValueStrokeClass: 'circles-maxValueStroke',
                                    styleWrapper: true,
                                    styleText: true
                                });
                            }, <?php echo esc_html( $stime ); ?>);

                        });
                    });
                </script>

				<?php do_action( 'donate_loop_campaign_goal_raised' ); ?>
				<?php do_action( 'donate_single_campaign_donate' ); ?>
            </div>

			<?php
		}
	}
	wp_reset_postdata();
} ?>