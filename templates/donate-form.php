<div id="donate_hidden"></div>
<script type="text/html" id="tmpl-donate-form-template">
	<form action="<?php echo esc_attr( donate_redirect_url() ) ?>" method="POST" class="donate_form" id="donate_form">
		<h2><?php _e( 'Donation amount', 'tp-donate' ) ?></h2>
		<p class="description">{{ data.campaign_title }}<p>
		<div class="donate_form_layout">

			<# var payment = false; #>

			<!--Allow payment-->
			<# if( ( typeof thimpress_donate.settings !== 'undefined' &&
	        	typeof thimpress_donate.settings.checkout !== 'undefined' &&
	        	typeof thimpress_donate.settings.checkout.lightbox_checkout !== 'undefined' &&
	        	thimpress_donate.settings.checkout.lightbox_checkout === 'yes' )
				|| ( typeof data.allow_payment !== 'undefined' && data.allow_payment === true ) ) {

				payment = true;

			} #>

			<!--Donate For System-->
			<# if( typeof data.donate_system !== 'undefined' && data.donate_system === true ) { #>

				<input type="hidden" name="donate_system" value="1" />

			<# } #>
			<!--End Donate For System-->

	        <!--Campaign ID-->
	        <# if( typeof data.campaign_id !== 'undefined' ){ #>

	        	<input type="hidden" name="campaign_id" value="{{{ data.campaign_id }}}" />

	        <# } #>
	        <!--End Campaign ID-->

	        <!--Hidden field-->
	        <?php wp_nonce_field( 'thimpress_donate_nonce', 'thimpress_donate_nonce' ); ?>
	        <input type="hidden" name="action" value="donate_submit" />
	        <!--End Hidden field-->

			<!--If payment is true, display input donate amount-->
	        <# if ( payment ) { #>

	        	<!--Compensates of campaign ID-->
	            <div class="donate_compensates">
	                <ul>
	                	<# if( typeof data.compensates !== 'undefined' && Object.keys(data.compensates).length > 0 ) { #>

		                    <# jQuery.each( data.compensates, function(key, val) { #>
		                            <li>
		                            	<input type="radio" name="donate_input_amount_package" value="{{ key }}" id="{{ key }}"/>
		                            	<label class="donate_amount_group" for="{{ key }}">
		                            		<?php _e( 'Donate', 'tp-donate' ) ?>
		                            		<span class="donate_amount">{{{ val.amount }}}</span>
		                            	</label>
		                            	<p>{{{ val.desc }}}</p>
		                            </li>
		                    <# }); #>

	                	<# } #>

	                    <li>
				            <h4><?php _e( 'Enter custom donate amount: ', 'tp-donate' ); ?></h4>

				            <span class="currency">{{{ data.currency }}}</span>

				            <input type="number" name="donate_input_amount" step="any" class="donate_form_input payment" min="0"/>
                    	</li>

	                </ul>
	            </div>
	            <!--End Compensates of campaign ID-->

	            <!--Donor Info-->
	            <div class="donate_donor_info">

	        		<h3><?php _e( 'Personal Info', 'tp-donate' ) ?></h3>

	        		<div class="donate_field">
	        			<input name="first_name" id="first_name" class="first_name" placeholder="<?php _e( '* First Name', 'tp-donate' ) ?>" />
	        		</div>

	        		<div class="donate_field">
	        			<input name="last_name" id="last_name" class="last_name" placeholder="<?php _e( '* Last Name', 'tp-donate' ) ?>" />
	        		</div>

	        		<div class="donate_field">
	        			<input name="email" id="email" class="email" placeholder="<?php _e( '* Email', 'tp-donate' ) ?>" />
	        		</div>

	        		<div class="donate_field">
	        			<input name="phone" id="phone" class="phone" placeholder="<?php _e( '* Phone', 'tp-donate' ) ?>" />
	        		</div>

	        		<div class="donate_field">
	        			<textarea name="address" id="address" class="address" placeholder="<?php _e( '* Address', 'tp-donate' ) ?>"></textarea>
	        		</div>

	        		<div class="donate_field">
	        			<textarea name="addition" id="addition" class="addition" placeholder="<?php _e( 'Additional note', 'tp-donate' ) ?>"></textarea>
	        		</div>

	        	</div>
	        	<!--End Donor Info-->

	        	<!--Terms and Conditional-->
	        	<?php $term_condition_page_id = DN_Settings::instance()->checkout->get( 'term_condition_page' ); ?>
				<?php $enable = DN_Settings::instance()->checkout->get( 'term_condition', 'yes' ); ?>

				<?php if( $enable === 'yes' && $term_condition_page_id ) : ?>

					<div class="donate_term_condition">
						<input type="checkbox" name="term_condition" value="1" id="term_condition"/>
						<label for="term_condition">
							<?php _e( 'Terms & Conditions', 'tp-donate' ); ?>
						</label>
					</div>

				<?php endif; ?>
				<!--End Terms and Conditional-->

				<!--Payments enable-->
	        	<# if( typeof data.payments !== 'undefined' && data.payments.length > 0 ){ #>

	        		<div class="donate_payment_method">

		        		<# for( var i = 0; i < data.payments.length; i++ ) { #>

		        			<# var payment = data.payments[i] #>

		        			<label class="payment_method" for="payment_method_{{ payment.id }}"><img width="115" height="50" src="{{ payment.icon }}" /></label>
		        			<input id="payment_method_{{ payment.id }}" type="radio" name="payment_method" value="{{ payment.id }}"/>

		        		<# } #>

	        		</div>

	        	<# } #>
	        	<!--End Payments enable-->

	        	<!--Require to process if allow payment in lightbox setting-->
	        	<input type="hidden" name="payment_process" value="1" />
	        	<!--End Require to process if allow payment in lightbox setting-->

            	<div class="donate_form_footer center">

            		<button type="submit" class="donate_submit button payment" form="donate_form"><?php _e( 'Donate', 'tp-donate' ) ?></button>

            	</div>

            <# } else { #>

            	<div class="donate_form_footer">

		            <h4><?php _e( 'Enter custom donate amount: ', 'tp-donate' ); ?></h4>

		            <span class="currency">{{{ data.currency }}}</span>

		            <input type="number" name="donate_input_amount" step="any" class="donate_form_input" min="0"/>
	            	<button type="submit" class="donate_submit button" form="donate_form"><?php _e( 'Donate', 'tp-donate' ) ?></button>

	            </div>

	        <# } #>

	    </div>
	</form>

</script>