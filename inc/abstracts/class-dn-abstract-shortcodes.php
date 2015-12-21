<?php

abstract class DN_Shortcode_Base
{

	/**
	 * template file
	 * @var null
	 */
	protected $_template = null;

	/**
	 * shortcode name
	 * @var null
	 */
	protected $_shortcodeName = null;

	function __construct()
	{
		if( ! $this->_shortcodeName || ! $this->_template )
			return;

		add_shortcode( $this->_shortcodeName, array( $this, 'add_shortcode' ) );
		add_action( 'donate_before_wrap_shortcode', array( $this, 'shortcode_start_wrap' ) );
		add_action( 'donate_after_wrap_shortcode', array( $this, 'shortcode_end_wrap' ) );
	}

	// add strat wrap shortcode html
	public function shortcode_start_wrap()
	{
		return '<div class="donate_wrapper '.$this->_shortcodeName.'">';
	}

	protected function add_shortcode( $atts, $content = null )
	{
		do_action( 'donate_before_wrap_shortcode', $this->_shortcodeName );

		donate_get_template( 'shortcodes/' . $this->_template, $this->parses( $atts ) );

		do_action( 'donate_after_wrap_shortcode', $this->_shortcodeName );
	}

	// add end wrap shortcode html
	public function shortcode_end_wrap()
	{
		return '</div>';
	}

	/**
	 * parse atts
	 * @param  array
	 * @return array
	 */
	public function parses( $atts )
	{
		return apply_filters( 'tp_events_shortcode_atts', $atts, $this->_shortcodeName );
	}

}
