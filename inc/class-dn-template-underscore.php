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
			<script type="text/html" id="tmpl-donate-form-template">
				<form action="<?php echo esc_attr( donate_redirect_url() ) ?>" method="POST" class="donate_form">
					<h2><?php _e( 'DONATION AMOUNT', 'tp-donate' ) ?></h2>
					<div class="donate_form_layout">

				        <# if ( typeof data.compensates !== 'undefined' && data.compensates.length > 0 ) { #>
				            <div class="donate_compensates">
				                <ul>
				                    <#  for ( var i = 0; i < data.compensates.length; i++ ) { #>
				                            <# var pack = data.compensates[i] #>
				                            <li>
				                            	<input type="radio" name="donate_input_price" value="{{ i }}"/>
				                            	<label>
				                            		<?php _e( 'Donate', 'tp-donate' ) ?>
				                            		<span class="donate_amount">{{{ data.amount }}}</span>
				                            	</label>
				                            	<p>{{{ data.desc }}}</p>
				                            </li>
				                     <# } #>
				                </ul>
				            </div>
				        <# } #>

				        <div class="donate_form_input">

				            <h3><?php _e( 'Enter custom donate amount: ', 'tp-donate' ); ?></h3>

				            <span>{{{ data.currency }}}</span>

				            <input type="number" name="donate_input_amount" step="any" class="donate_form_input"/>

				        </div>

				    </div>
				</form>

			</script>
		<?php
	}

}

new DN_Template_Underscore();
