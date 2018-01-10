<?php
/**
 * Fundpress Email class.
 *
 * @version     2.0
 * @package     Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'DN_Email' ) ) {
	/**
	 * Class DN_Email
	 */
	class DN_Email {

		/**
		 * @var null
		 */
		static $instance = null;

		/**
		 * Send email to donor when donate completed.
		 *
		 * @param null $donor
		 */
		public function send_email_donate_completed( $donor = null ) {
			if ( $this->is_enable() !== true ) {
				return;
			}

			// email template
			$email_template = FP()->settings->email->get( 'email_template' ) ? FP()->settings->email->get( 'email_template' ) : '';
			$email          = $donor->get_meta( 'email' );
			if ( $email ) {
				$subject = __( 'Donate completed', 'fundpress' );

				$replace = array(
					'/\[(.*?)donor_first_name(.*?)\]/i',
					'/\[(.*?)donor_last_name(.*?)\]/i',
					'/\[(.*?)donor_phone(.*?)\]/i',
					'/\[(.*?)donor_email(.*?)\]/i',
					'/\[(.*?)donor_address(.*?)\]/i'
				);

				$replace_with = array(
					$donor->get_meta( 'first_name' ),
					$donor->get_meta( 'last_name' ),
					$donor->get_meta( 'phone' ),
					$donor->get_meta( 'email' ),
					$donor->get_meta( 'address' )
				);

				ob_start();
				echo preg_replace( $replace, $replace_with, $email_template );
				$body = ob_get_clean();

				// filter email setting
				add_filter( 'wp_mail_from', array( $this, 'set_email_from' ) );
				// filter email from name
				add_filter( 'wp_mail_from_name', array( $this, 'set_email_name' ) );
				// filter content type
				add_filter( 'wp_mail_content_type', array( $this, 'email_content_type' ) );
				// filter charset
				add_filter( 'wp_mail_charset', array( $this, 'email_charset' ) );

				wp_mail( $email, $subject, $body );

				// filter email setting
				remove_filter( 'wp_mail_from', array( $this, 'set_email_from' ) );
				// filter email from name
				remove_filter( 'wp_mail_from_name', array( $this, 'set_email_name' ) );
				// filter content type
				remove_filter( 'wp_mail_content_type', array( $this, 'email_content_type' ) );
				// filter charset
				remove_filter( 'wp_mail_charset', array( $this, 'email_charset' ) );
			}
		}

		/**
		 * Check send mail option enable.
		 *
		 * @return bool
		 */
		public function is_enable() {
			if ( FP()->settings->email->get( 'enable', 'yes' ) === 'yes' ) {
				return true;
			}

			return false;
		}

		/**
		 * Set email from, default admin mail.
		 *
		 * @param $email
		 *
		 * @return mixed
		 */
		public function set_email_from( $email ) {
			if ( $donate_email = FP()->settings->email->get( 'admin_email' ) ) {
				return $donate_email;
			}

			return $email;
		}

		/**
		 * Set email name header.
		 *
		 * @param $name
		 *
		 * @return string
		 */
		public function set_email_name( $name ) {
			if ( $donate_name = FP()->settings->email->get( 'from_name' ) ) {
				return sanitize_title( $donate_name );
			}

			return $name;
		}

		/**
		 * Content mail type.
		 *
		 * @param $type
		 *
		 * @return string
		 */
		public function email_content_type( $type ) {
			return 'text/html';
		}

		/**
		 * Mail charset.
		 *
		 * @param $chartset
		 *
		 * @return string
		 */
		public function email_charset( $chartset ) {
			return 'UTF-8';
		}

		/**
		 * Instance.
		 *
		 * @return DN_Email|null
		 */
		public static function instance() {
			if ( ! self::$instance ) {
				return self::$instance = new self();
			}

			return self::$instance;
		}

	}
}

DN_Email::instance();
