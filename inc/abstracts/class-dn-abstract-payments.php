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

	// is enable
	public $is_enable = true;

	/**
	 * icon url
	 * @var null
	 */
	public $_icon = null;

	function __construct()
	{
		add_action( 'init', array( $this, 'init' ) );
		$this->_icon = TP_DONATE_INC_URI . '/payments/' . $this->_id . '.png';
		add_action( 'donate_payment_gateways_select', array( $this, 'donate_gateways' ) );
		$this->is_enable();
	}

	public function init()
	{
		/**
		 * filter payments enable
		 */
		add_filter( 'donate_payment_gateways_enable', array( $this, 'payment_gateways_enable' ) );
		/**
		 * filter payments enable
		 */
		add_filter( 'donate_payment_gateways', array( $this, 'payment_gateways' ) );

		if( is_admin() )
		{
			/**
			 * generate fields settings
			 */
			add_filter( 'donate_admin_setting_fields', array( $this, 'generate_fields' ), 10, 2 );
		}

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
	 * payment_gateways
	 * @param  $payment_gateways
	 * @return $payment_gateways
	 */
	public function payment_gateways( $payment_gateways )
	{
		if( $this->_id && $this->_title )
		{
			$payment_gateways[ $this->_id ] = $this;
		}
		return $payment_gateways;
	}

	/**
	 * donate_payment_gateways_enable filter callback
	 * @param  $payment_gateways array
	 * @return $payment_gateways array
	 */
	public function payment_gateways_enable( $payment_gateways )
	{
		if( $this->is_enable )
		{
			if( $this->_id && $this->_title )
			{
				$payment_gateways[ $this->_id ] = $this;
			}
		}
		return $payment_gateways;
	}

	/**
	 * fields setting
	 * @param  [type] $groups [description]
	 * @param  [type] $id     [description]
	 * @return [type]         [description]
	 */
	public function generate_fields( $groups, $id )
	{
		if( $id === 'checkout' && $this->_id )
		{

			$groups[ $id . '_' . $this->_id ] = apply_filters( 'donate_admin_setting_fields_checkout', $this->fields(), $this->_id );

		}

		return $groups;
	}

	/**
	 * admin setting fields
	 * @return array
	 */
	public function fields()
	{
		return array();
	}

	/**
	 * enable
	 * @return boolean
	 */
	public function is_enable()
	{
		if( DN_Settings::instance()->checkout->get( $this->_id . '_enable', 'yes' ) === 'yes' )
		{
			return $this->is_enable = true;
		}
		return $this->is_enable = false;
	}

	/**
	 * donate_gateways fontend display
	 * @return html
	 */
	public function donate_gateways()
	{
		$html = array();

		$html[] = '<label for="payment_method_'.esc_attr( $this->_id ).'"><img width="115" height="50" src="'. esc_attr( $this->_icon ) .'" /></label>';
		$html[] = '<input id="payment_method_'.esc_attr( $this->_id ).'" type="radio" name="payment_method" value="'.esc_attr( $this->_id ).'"/>';

		echo implode( '' , $html );
	}

}
