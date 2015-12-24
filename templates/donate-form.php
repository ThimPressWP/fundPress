<div id="donate_hidden"></div>
<script type="text/html" id="tmpl-donate-form-template">
	<form action="<?php echo esc_attr( donate_redirect_url() ) ?>" method="POST" class="donate_form">
		<h2><?php _e( 'DONATION AMOUNT', 'tp-donate' ) ?></h2>
		<div class="donate_form_layout">

	        <# if ( typeof data.compensates !== 'undefined' && Object.keys(data.compensates).length > 0 ) { #>

	            <div class="donate_compensates">
	                <ul>
	                    <# jQuery.each( data.compensates, function(key, val){ #>
	                            <li>
	                            	<input type="radio" name="donate_input_price" value="{{ key }}" id="{{ key }}"/>
	                            	<label class="donate_amount_group" for="{{ key }}">
	                            		<?php _e( 'Donate', 'tp-donate' ) ?>
	                            		<span class="donate_amount">{{{ val.amount }}}</span>
	                            	</label>
	                            	<p>{{{ val.desc }}}</p>
	                            </li>
	                     <# }); #>
	                </ul>
	            </div>

	        <# } #>

	        <# if( typeof thimpress_donate.settings !== 'undefined' &&
	        	typeof thimpress_donate.settings.checkout !== 'undefined' &&
	        	typeof thimpress_donate.settings.checkout.lightbox_checkout !== 'undefined' &&
	        	thimpress_donate.settings.checkout.lightbox_checkout === 'yes' ) { #>

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

	        	<# if( typeof data.payments !== 'undefined' ){ #>

	        		<# for( var i = 0; i < data.payments.length; i++ ) { #>

	        			<# var payment = data.payments[i] #>

	        			<label for="payment_method_{{ payment.id }}"><img width="115" height="50" src="{{ payment.icon }}" /></label>
	        			<input id="payment_method_{{ payment.id }}" type="radio" name="payment_method" value="{{ payment.id }}"/>

	        		<# } #>

	        	<# } #>

	        <# } #>

	        <div class="donate_form_footer">

	            <h4><?php _e( 'Enter custom donate amount: ', 'tp-donate' ); ?></h4>

	            <span class="currency">{{{ data.currency }}}</span>

	            <input type="number" name="donate_input_amount" step="any" class="donate_form_input" min="0"/>

	            <button type="submit" class="donate_submit button"><?php _e( 'Donate', 'tp-donate' ) ?></button>

	        </div>

	    </div>
	</form>

</script>