<?php

abstract class DN_Payment_Base
{
	/**
	 * id of payment
	 * @var null
	 */
	protected $_id = null;

	/**
	 * payment title
	 * @var null
	 */
	protected $_title = null;

	function __construct()
	{
		/**
		 * filter payments
		 */
		add_filter( 'donate_payment_gateways', array( $this, 'payment_gateways' ) );
	}

	/**
	 * payment process
	 * @return null
	 */
	protected function process(){}

	/**
	 * refun action
	 * @return null
	 */
	protected function refun(){}

	/**
	 * payment send email
	 * @return null
	 */
	public function send_email(){}

	/**
	 * donate_payment_gateways filter callback
	 * @param  $payment_gateways array
	 * @return $payment_gateways array
	 */
	public function payment_gateways( $payment_gateways )
	{
		if( $this->_id && $this->_title )
		{
			$payment_gateways[ $this->_id ] = $this->_title;
		}
		return $payment_gateways;
	}

}
