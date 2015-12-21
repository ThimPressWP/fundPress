<?php

class TP_Shortcode_Event_Countdown extends DN_Shortcode_Base
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
		$this->_shortcodeName = 'donate_countdown';
		$this->_template = 'donate-countdown.php';
		parent::__construct();
	}

}

new TP_Shortcode_Event_Countdown();