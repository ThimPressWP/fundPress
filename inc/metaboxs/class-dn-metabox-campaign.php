<?php

class DN_MetaBox_Campaign_Settings extends DN_MetaBox_Base
{
	/**
	 * id of the meta box
	 * @var null
	 */
	public $_id = null;

	/**
	 * title of meta box
	 * @var null
	 */
	public $_title = null;

	/**
	 * array meta key
	 * @var array
	 */
	public $_name = array();

	public function __construct()
	{
		$this->_id = 'donate_setting_section';
		$this->_title = __( 'Donate Settings', 'tp-donate' );
		$this->_layout = TP_DONATE_INC . '/metaboxs/views/donate-settings.php';
		add_action( 'donate_metabox_setting_section', array( $this, 'donate_metabox_setting' ), 10, 1 );
		add_action( 'admin_footer', array( $this, 'admin_footer' ) );

		add_action( 'wp_ajax_donate_remove_compensate', array( $this, 'donate_remove_compensate' ) );
		add_action( 'wp_ajax_nopriv_donate_remove_compensate', array( $this, 'mustLogin' ) );
		parent::__construct();
	}

	/**
	 * load fields
	 * @return array
	 */
	public function load_field()
	{
		return
			array(
				'goal_raised'	=> array(
						'title'	=> __( 'Goal and Raised', 'tp-donate' ),
					),

				'compensate'	=> array(
					'title'	=> __( 'Compensate', 'tp-donate' ),
				)
			);
	}

	/**
	 * metabox section
	 * @param $id
	 * @return
	 */
	public function donate_metabox_setting( $id )
	{
		$html = array();

		global $post;
		$currency = donate_get_currency();
		if( $post && get_post_meta( $post->ID, $this->get_field_name( 'currency' ), true ) )
		{
			$currency = get_post_meta( $post->ID, $this->get_field_name( 'currency' ), true );
		}

		$html[] = '<input type="hidden" name="' . $this->get_field_name( 'currency' ) . '" value="'.esc_attr( $currency ).'"/>';
		if( $id === 'goal_raised' )
		{
			$html[] = '<div class="tool_box">';

			$html[] = '<table>';
			$html[] = '<tr>';
			$html[] = '<th><label>'.sprintf( '%s(%s)', __( 'Goal', 'tp-donate' ), donate_get_currency_symbol( $currency ) ).'</label>';
			$html[] = '<input type="number" class="goal" name="'.$this->get_field_name( 'goal' ).'" value="'.$this->get_field_value( 'goal', 0 ).'" min="0"/></th>';
			$html[] = '<td><label>'.sprintf( '%s(%s)', __( 'Description', 'tp-donate' ), donate_get_currency_symbol( $currency ) ).'</label>';
			$html[] = '<input type="number" class="raised" name="'.$this->get_field_name( 'raised' ).'" value="'.$this->get_field_value( 'raised', 0 ).'" min="0" readonly/></td>';
			$html[] = '</tr>';
			$html[] = '</table>';

			$html[] = '</div>';
		}
		else if( $id === 'compensate' )
		{
			$html[] = '<div class="tool_box">';
			$html[] = '<label>'.__( 'Set compensate', 'tp-donate' );
			$html[] = '<a href="#" class="button add_compensate">'.__( 'Add', 'tp-donate' ).'</a>';
			$html[] = '</label>';
			$html[] = '</div>';

			if( $markers = $this->get_field_value( 'marker' ) )
			{
				if( ! empty( $markers ) )
				{
					foreach( $markers as $marker_id => $meta_val )
					{
						$html[] = '<div class="donate_metabox" data-compensate-id="'.$marker_id.'">';
						$html[] = '<table>';
						$html[] = '<tr>';
						$html[] = '<th><label>'.sprintf( '%s(%s)', __( 'Marker', 'tp-donate' ), donate_get_currency_symbol( $currency ) ).'</label>';
						$html[] = '<input type="number" step="any" name="'.$this->get_field_name( 'marker' ).'['.$marker_id.'][amount]" value="'.esc_attr( $meta_val['amount'] ).'"/></th>';
						$html[] = '<td><label>'.__( 'Description', 'tp-donate' ).'</label>';
						$html[] = '<textarea name="'.$this->get_field_name( 'marker' ).'['.$marker_id.'][desc]">'.esc_textarea( $meta_val['desc'] ).'</textarea></td>';
						$html[] = '<td><a href="#" class="remove" data-compensate-id="'.$marker_id.'">'.__( 'Remove', 'tp-donate' ).'</a></td>';
						$html[] = '</tr>';
						$html[] = '</table>';
						$html[] = '</div>';
					}
				}
			}

			$html[] = '</div>';
		}

		echo implode( '', $html );
	}

	public function admin_footer()
	{
		global $post;
		if( $post && $post->post_type !== 'dn_campaign' )
			return;

		$html = '<script type="text/html" id="tmpl-compensate-layout">
			<div class="donate_metabox" data-compensate-id="{{ data.id }}">
				<table>
					<tr>
						<th>
							<label>'.sprintf( '%s(%s)', __( 'Marker', 'tp-donate' ), donate_get_currency_symbol() ).'</label>
							<input type="number" step="any" name="'.$this->get_field_name( 'marker' ).'[{{ data.id }}][amount]" value="{{ data.amount }}" />
						</th>
						<td>
							<label>'.__( 'Description', 'tp-donate' ).'</label>
							<textarea name="'.$this->get_field_name( 'marker' ).'[{{ data.id }}][desc]">{{ data.desc }}</textarea>
						</td>
						<td>
							<a href="#" class="remove" data-compensate-id="{{ data.id }}">'.__( 'Remove', 'tp-donate' ).'</a>
						</td>
					</tr>
				</table>
			</div>
		</script>';

		echo $html;
	}

	/**
	 * ajax create compensate
	 * @return
	 */
	function donate_remove_compensate()
	{
		if( ! isset( $_GET[ 'schema' ] ) || $_GET[ 'schema' ] !== 'donate-ajax' || empty( $_POST ) )
			return;

		if( ! isset( $_POST[ 'compensate_id' ] ) || ! isset( $_POST[ 'post_id' ] ) ) return;

		$marker = $this->get_field_value( 'marker', $_POST[ 'post_id' ] );

		if( empty( $marker ) )
		{
			wp_send_json( array( 'status' => 'success' ) ); die();
		}

		if( isset( $marker[ $_POST[ 'compensate_id' ] ] ) )
		{
			unset( $marker[ $_POST[ 'compensate_id' ] ] );
		}
		else
		{
			wp_send_json( array( 'status' => 'success' ) ); die();
		}

		if ( $update = update_post_meta( $_POST[ 'post_id' ], $this->get_field_name( 'marker' ), $marker ) )
		{
			wp_send_json( array( 'status' => 'success' ) ); die();
		}

		wp_send_json( array( 'status' => 'failed', 'message' => __( 'Could not delete compensate. Plesae try again' ) ) ); die();
	}

	/**
	 * must login
	 * @return null
	 */
	function mustLogin()
	{
		_e( 'You must login', 'tp-donate' );
	}

}

new DN_MetaBox_Campaign_Settings();