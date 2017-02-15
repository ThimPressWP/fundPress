<?php
if ( !defined( 'ABSPATH' ) )
	exit();
/**
 * template shortcode [donate_form]
 */
?>

<div class="thimpress_donate_form">
    <form action="<?php echo esc_attr( donate_redirect_url() ) ?>" method="POST" class="donate_form" id="donate_form">
        <h2><?php _e( 'Donation amount', 'fundpress' ) ?></h2>
        <p class="description"><?php echo esc_html( $title ) ?><p>
        <div>
            <!--Donate For System OR Campaign -->
			<?php if ( $campaign_id ) : ?>

                <input type="hidden" name="campaign_id" value="<?php echo esc_attr( $campaign_id ) ?>" />

			<?php else: ?>

                <input type="hidden" name="donate_system" value="1" />

			<?php endif; ?>

            <!--End Donate For System OR Campaign -->

            <!--Hidden field-->
			<?php wp_nonce_field( 'thimpress_donate_nonce', 'thimpress_donate_nonce' ); ?>
            <input type="hidden" name="action" value="donate_submit" />
            <input type="hidden" name="currency" value="<?php echo esc_attr( donate_get_currency() ) ?>" />
            <!--End Hidden field-->

            <!--If payment is true, display input donate amount-->
			<?php if ( $payments || !$campaign_id ) : ?>

                <div class="donate_compensates">
                    <ul>
						<?php if ( $compensates ): ?>

							<?php foreach ( $compensates as $key => $compen ) : ?>
                                <li>
                                    <input type="radio" name="donate_input_amount_package" value="<?php echo esc_attr( $key ) ?>" id="<?php echo esc_attr( $key ) ?>" />
                                    <label class="donate_amount_group" for="<?php echo esc_attr( $key ) ?>">
										<?php _e( 'Donate', 'fundpress' ) ?>
                                        <span class="donate_amount"><?php printf( '%s', $compen['amount'] ) ?></span>
                                    </label>
                                    <span><?php printf( '%s', $compen['desc'] ) ?></span>
                                </li>
							<?php endforeach; ?>

						<?php endif; ?>

                        <li>
                            <h4><?php _e( 'Enter custom donate amount: ', 'fundpress' ); ?></h4>

                            <span class="currency"><?php echo esc_html( donate_get_currency_symbol() ); ?></span>

                            <input type="number" name="donate_input_amount" step="any" class="donate_form_input payment" min="0" />
                        </li>
                    </ul>

                </div>
                <!--End Compensates of campaign ID-->

                <!--Donor Info-->
                <div class="donate_donor_info">

                    <h3><?php _e( 'Personal Info', 'fundpress' ) ?></h3>

                    <div class="donate_field">
                        <input name="first_name" id="first_name" class="first_name" placeholder="<?php _e( '* First Name', 'fundpress' ) ?>" />
                    </div>

                    <div class="donate_field">
                        <input name="last_name" id="last_name" class="last_name" placeholder="<?php _e( '* Last Name', 'fundpress' ) ?>" />
                    </div>

                    <div class="donate_field">
                        <input name="email" id="email" class="email" placeholder="<?php _e( '* Email', 'fundpress' ) ?>" />
                    </div>

                    <div class="donate_field">
                        <input name="phone" id="phone" class="phone" placeholder="<?php _e( '* Phone', 'fundpress' ) ?>" />
                    </div>

                    <div class="donate_field">
                        <textarea name="address" id="address" class="address" placeholder="<?php _e( '* Address', 'fundpress' ) ?>"></textarea>
                    </div>

                    <div class="donate_field">
                        <textarea name="addition" id="addition" class="addition" placeholder="<?php _e( 'Additional note', 'fundpress' ) ?>"></textarea>
                    </div>

                </div>
                <!--End Donor Info-->

                <!--Terms and Conditional-->
				<?php $term_condition_page_id = DN_Settings::instance()->checkout->get( 'term_condition_page' ); ?>
				<?php $enable = DN_Settings::instance()->checkout->get( 'term_condition', 'yes' ); ?>
				<?php if ( $enable === 'yes' && $term_condition_page_id ) : ?>

                    <div class="donate_term_condition">
                        <input type="checkbox" name="term_condition" value="1" id="term_condition" />
                        <label for="term_condition">
							<?php _e( 'Terms & Conditions', 'fundpress' ); ?>
                        </label>
                    </div>

				<?php endif; ?>
                <!--End Terms and Conditional-->

                <!--Payments enable-->
				<?php
//				if ( $payments ) : $i = 0;
					donate_get_template( 'checkout/payment_methods.php' );
//				endif;
				?>
                <!--End Payments enable-->

                <!--Require to process if allow payment in lightbox setting-->
                <input type="hidden" name="payment_process" value="1" />
                <!--End Require to process if allow payment in lightbox setting-->

                <div class="donate_form_footer center">

                    <button type="submit" class="donate_submit button payment" form="donate_form"><?php _e( 'Donate', 'fundpress' ) ?></button>

                </div>

			<?php else: ?>

                <!--Compensates of campaign ID-->
                <div class="donate_compensates">
                    <ul>
						<?php if ( $compensates ) : ?>

							<?php foreach ( $compensates as $key => $compen ) : ?>
                                <li>
                                    <input type="radio" name="donate_input_amount_package" value="<?php echo esc_attr( $key ) ?>" id="<?php echo esc_attr( $key ) ?>" />
                                    <label class="donate_amount_group" for="<?php echo esc_attr( $key ) ?>">
										<?php _e( 'Donate', 'fundpress' ) ?>
                                        <span class="donate_amount"><?php printf( '%s', $compen['amount'] ) ?></span>
                                    </label>
                                    <span><?php printf( '%s', $compen['desc'] ) ?></span>
                                </li>
							<?php endforeach; ?>

						<?php endif; ?>
                    </ul>
                </div>
                <!--End Compensates of campaign ID-->

                <div class="donate_form_footer">

                    <h4><?php _e( 'Enter custom donate amount: ', 'fundpress' ); ?></h4>

                    <span class="currency"><?php echo esc_html( donate_get_currency_symbol() ); ?></span>

                    <input type="number" name="donate_input_amount" step="any" class="donate_form_input" min="0" />
                    <button type="submit" class="donate_submit button" form="donate_form"><?php _e( 'Donate', 'fundpress' ) ?></button>

                </div>

			<?php endif; ?>


        </div>
    </form>
</div>



