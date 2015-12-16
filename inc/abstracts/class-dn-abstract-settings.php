<?php

abstract class DN_Setting_Page
{

	/**
	 * $_id tab id
	 * @var null
	 */
	protected $_id = null;

	/**
	 * $_title tab display
	 * @var null
	 */
	protected $_title = null;

	/**
	 * $_fields
	 * @var array
	 */
	protected $_fields = array();

	/**
	 * $_position
	 * @var integer
	 */
	protected $_position = 1;

	public function __construct()
	{
		add_filter( 'donate_admin_settings', array( $this, 'add_tab' ), $this->_position, 1 );
		add_action( 'donate_admin_setting_' . $this->_id . '_content', array( $this, 'layout' ), $this->_position, 1 );
	}

	/**
	 * add_tab setting
	 * @param array
	 */
	public function add_tab( $tabs )
	{
		if( $this->_id && $this->_title )
		{
			$tabs[ $this->_id ] = $this->_title;
			return $tabs;
		}
	}

	/**
	 * generate layout
	 * @return html layout
	 */
	public function layout()
	{
		// before tab content
		do_action( 'donate_admin_setting_before_' . $this->_id, $this->_id );

		donate()->_include( 'inc/admin/views/tab_' . $this->_id . '.php' ); return;
		$this->_fields = apply_filters( 'donate_admin_' . $this->_id  . '_fields', $this->load_field(), $this->_id );

		if( $this->_fields )
		{
			$html = array();
			foreach( $this->_fields as $key => $group )
			{
				if( isset( $group[ 'title' ], $group[ 'desc' ] ) )
				{
					$html[] = '<h3>' . sprintf( '%s', $group[ 'title' ] ) . '</h3>';
					$html[] = '<p>' . sprintf( '%s', $group[ 'desc' ] ) . '</p>';
				}

				if( isset( $group[ 'fields' ] ) )
				{
					$html[] = '<table>';
					foreach( $group[ 'fields' ] as $type => $field )
					{
						$html[] = '<tr>';

						// label
						$html[]	= '<th>' . sprintf( '%s', $field['label'] );

						if( isset( $label[ 'desc' ] ) )
						{
							$html[] = '<small>' . sprintf( '%s', $field['desc'] ) . '</small>';
						}

						$html[]	= '</th>';
						// end label

						// field
						$html[] = '<td>';

						// ob_start();
						donate()->_include( 'inc/admin/views/html/' . $type . '.php' );
						// $html[] = ob_get_clean();

						$html[] = '</td>';
						// end field

						$html[]	= '</tr>';
					}
					$html[] = '</table>';
				}
			}

		}

		// after tab content
		do_action( 'donate_admin_setting_after_' . $this->_id, $this->_id );
	}

	protected function load_field()
	{
		return array();
	}

}
