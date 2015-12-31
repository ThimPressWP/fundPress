<div id="donate_hidden"></div>
<script type="text/html" id="tmpl-donate-form-template">
	<form action="<?php echo esc_attr( donate_redirect_url() ) ?>" method="POST" class="donate_form" id="donate_form">
		<h2><?php _e( 'DONATION AMOUNT', 'tp-donate' ) ?></h2>
		<div class="donate_form_layout">

			<# var payment = false; #>

			<# if( typeof thimpress_donate.settings !== 'undefined' &&
	        	typeof thimpress_donate.settings.checkout !== 'undefined' &&
	        	typeof thimpress_donate.settings.checkout.lightbox_checkout !== 'undefined' &&
	        	thimpress_donate.settings.checkout.lightbox_checkout === 'yes' ) {

				payment = true;

			} #>

	        <# if ( typeof data.compensates !== 'undefined' ) { #>

	            <div class="donate_compensates">
	                <ul>
	                	<# if( Object.keys(data.compensates).length > 0 ) { #>

		                    <# jQuery.each( data.compensates, function(key, val){ #>
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

	                    <# if( payment ) { #>

	                    	<li>
					            <h4><?php _e( 'Enter custom donate amount: ', 'tp-donate' ); ?></h4>

					            <span class="currency">{{{ data.currency }}}</span>

					            <input type="number" name="donate_input_amount" step="any" class="donate_form_input payment" min="0"/>
	                    	</li>

	                    <# } #>

	                </ul>
	            </div>

	        <# } #>

	        <# if( payment ) { #>

	        	<div class="donate_dornor_info">

	        		<h3><?php _e( 'Personal Info', 'tp-donate' ) ?></h3>

	        		<div class="donate_field">
	        			<input name="first_name" id="first_name" class="first_name" placeholder="First Name" />
	        		</div>

	        		<div class="donate_field">
	        			<input name="last_name" id="last_name" class="last_name" placeholder="Last Name" />
	        		</div>

	        		<div class="donate_field">
	        			<input name="email" id="email" class="email" placeholder="<?php _e( 'Email', 'tp-donate' ) ?>" />
	        		</div>

	        		<div class="donate_field">
	        			<input name="phone" id="phone" class="phone" placeholder="<?php _e( 'Phone', 'tp-donate' ) ?>" />
	        		</div>

	        		<div class="donate_field">
	        			<textarea name="address" id="address" class="address" placeholder="<?php _e( 'Address', 'tp-donate' ) ?>"></textarea>
	        		</div>

	        		<div class="donate_field">
	        			<textarea name="addition" id="addition" class="addition" placeholder="<?php _e( 'Addition note', 'tp-donate' ) ?>"></textarea>
	        		</div>

	        	</div>

	        	<# if( typeof data.payments !== 'undefined' && data.payments.length > 0 ){ #>

	        		<div class="donate_payment_method">

		        		<# for( var i = 0; i < data.payments.length; i++ ) { #>

		        			<# var payment = data.payments[i] #>

		        			<label class="payment_method" for="payment_method_{{ payment.id }}"><img width="115" height="50" src="{{ payment.icon }}" /></label>
		        			<input id="payment_method_{{ payment.id }}" type="radio" name="payment_method" value="{{ payment.id }}"/>

		        		<# } #>

	        		</div>

	        	<# } #>

	        <# } #>

	        <?php wp_nonce_field( 'thimpress_donate_nonce', 'thimpress_donate_nonce' ); ?>
	        <input type="hidden" name="campaign_id" value="{{{ data.campaign_id }}}" />
	        <input type="hidden" name="action" value="donate_submit" />

	        <# if( payment === false ) { #>
        		<div class="donate_form_footer">

		            <h4><?php _e( 'Enter custom donate amount: ', 'tp-donate' ); ?></h4>

		            <span class="currency">{{{ data.currency }}}</span>

		            <input type="number" name="donate_input_amount" step="any" class="donate_form_input" min="0"/>
	            	<button type="submit" class="donate_submit button" form="donate_form"><?php _e( 'Donate', 'tp-donate' ) ?></button>

	            </div>

            <# } else { #>
	        	<input type="hidden" name="payment_process" value="1" />
	        	<?php $term_condition_page_id = DN_Settings::instance()->checkout->get( 'term_condition_page' ); ?>
				<?php $enable = DN_Settings::instance()->checkout->get( 'term_condition', 'yes' ); ?>

				<?php if( $enable === 'yes' && $term_condition_page_id ) : ?>

					<input type="checkbox" name="term_condition" value="1" id="term_condition"/>
					<label for="term_condition">
						<?php _e( 'Terms & Conditions', 'tp-donate' ); ?>
						<!-- <a href="<?php //echo esc_attr( donate_term_condition_url() ) ?>"><?php //_e( 'Terms & Conditions', 'tp-donate' ); ?></a> -->
					</label>

				<?php endif; ?>
            	<div class="donate_form_footer center">

            		<button type="submit" class="donate_submit button payment" form="donate_form"><?php _e( 'Donate', 'tp-donate' ) ?></button>

            	</div>

            <# } #>

	    </div>
	</form>

</script>