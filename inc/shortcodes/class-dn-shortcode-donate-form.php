<?php

class DN_Shortcode_Donate_Form extends DN_Shortcode_Base
{
	/**
	 * template file
	 * @var null
	 */
	public $_template = null;

	/**
	 * shortcode name
	 * @var null
	 */
	public $_shortcodeName = null;

	public function __construct()
	{
		$this->_shortcodeName = 'donate_form';
		$this->_template = 'donate-form.php';
		parent::__construct();
		add_filter( 'donate_shortcode_atts', array( $this, 'shortcode_atts' ), 10, 2 );
	}

	public function shortcode_atts( $atts, $shortcode ) {
		if ( $shortcode !== 'donate_form' ) {
			return $atts;
		}

		$atts = wp_parse_args( $atts, array(
				'campaign_id' 	=> '',
				'title' 		=> '',
				'payments'		=> true,
				'compensates'	=> false
			) );

		if ( $atts['campaign_id'] && ! $atts[ 'title' ] ) {
			$atts[ 'title' ]	= get_the_title( $atts['campaign_id'] );
			$campaign = DN_Campaign::instance( $atts['campaign_id'] );

			$compensates = array();
			$currency = $campaign->get_currency();

			if( $eachs = $campaign->get_compensate() )
			{
				foreach ( $eachs as $key => $compensate ) {
					/**
					 * convert campaign amount currency to amount with currency setting
					 * @var
					 */
					$amount = donate_campaign_convert_amount( $compensate['amount'], $currency );
					$compensates[ $key ] = array(
							'amount'		=> donate_price( $amount ),
							'desc'			=> $compensate['desc']
						);
				}
			}
			$atts['compensates'] = $compensates;
		} else {
			if ( ! $atts[ 'title' ] ) {
				$atts[ 'title' ]	= apply_filters( 'donate_form_title_without_campaign', sprintf( '%s - %s', get_bloginfo( 'name' ), get_bloginfo( 'description' ) ) );
			}
		}

		return $atts;
	}

}

new DN_Shortcode_Donate_Form();
