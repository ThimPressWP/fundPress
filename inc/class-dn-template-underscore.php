<?php

class DN_Template_Underscore
{

	function __construct()
	{

		/**
		 * load form
		 */
		add_action( 'wp_footer', array( $this, 'campaign_form' ) );

	}

	/**
	 * form
	 * @return js template
	 */
	function campaign_form()
	{
		?>
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
		<?php
	}

}

new DN_Template_Underscore();
