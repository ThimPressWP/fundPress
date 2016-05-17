<?php

class DN_Shortcode_Campagin extends DN_Shortcode_Base
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
		$this->_shortcodeName = 'donate_campaign';
		$this->_template = 'campaign.php';
		parent::__construct();

		add_filter( 'donate_shortcode_atts', array( $this, 'shortcode_atts' ), 10, 2 );
	}

	public function shortcode_atts( $atts, $shortcode ) {
		if ( $shortcode !== 'donate_campaign' ) {
			return $atts;
		}

		$atts = wp_parse_args( $atts, array(
				'id' 			=> '',
				'title' 		=> '',
				'style' 		=> '',
				'stime'			=> '100',
			) );

		return $atts;
	}

}

new DN_Shortcode_Campagin();
