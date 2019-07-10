<?php
/**
 * Fundpress Template loader class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Template' ) ) {
	/**
	 * Class DN_Template.
	 */
	class DN_Template {

		/**
		 * DN_Template constructor.
		 */
		public function __construct() {
			// template include
			add_filter( 'template_include', array( $this, 'template_loader' ) );
		}

		/**
		 * Filter template.
		 *
		 * @param $template
		 *
		 * @return string
		 */
		public function template_loader( $template ) {
			$post_type = get_post_type();

			$file = '';
			$find = array();
			if ( $post_type !== 'dn_campaign' ) {
				return $template;
			}

			if ( is_post_type_archive( 'dn_campaign' ) ) {
				$file   = 'archive-campaign.php';
				$find[] = $file;
				$find[] = donate_template_path() . '/' . $file;
			} else if ( is_single() ) {
				$file   = 'single-campaign.php';
				$find[] = $file;
				$find[] = donate_template_path() . '/' . $file;
			} else if ( is_tax( 'dn_campaign_cat' ) || is_tax( 'dn_campaign_tag' ) ) {
				$term = get_queried_object();
				$taxonomy = str_replace( 'dn_', '', $term->taxonomy );
				$file = 'taxonomy-' . $taxonomy . '.php';
				$find[] = 'taxonomy-' . $taxonomy . '-' . $term->slug . '.php';
				$find[] = donate_template_path() . '/' . 'taxonomy-' . $taxonomy . '-' . $term->slug . '.php';
				$find[] = $file;
				$find[] = donate_template_path() . '/' . $file;
			}

			if ( $file ) {
				$find[]      = donate_template_path() . $file;
				$hb_template = FUNDPRESS_TEMP . $file;
				$template    = locate_template( array_unique( $find ) );

				if ( ! $template && file_exists( $hb_template ) ) {
					$template = $hb_template;
				}
			}

			return $template;
		}
	}
}

new DN_Template();
