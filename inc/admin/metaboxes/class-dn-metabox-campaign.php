<?php

if ( !defined( 'ABSPATH' ) )
    exit();

class DN_MetaBox_Campaign extends DN_MetaBox_Base {

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

    public function __construct() {
        $this->_id = 'donate_setting_section';
        $this->_title = __( 'Campaign Settings', 'tp-donate' );
        $this->_prefix = TP_DONATE_META_CAMPAIGN;
        add_action( 'donate_metabox_setting_section', array( $this, 'donate_metabox_setting' ), 10, 1 );
        add_action( 'admin_footer', array( $this, 'admin_footer' ) );

        add_action( 'wp_ajax_donate_remove_compensate', array( $this, 'donate_remove_compensate' ) );
        add_action( 'wp_ajax_nopriv_donate_remove_compensate', array( $this, 'mustLogin' ) );
        parent::__construct();

        /* update meta */
        add_action( 'donate_process_update_dn_campaign_meta', array( $this, 'update_meta_campaign' ), 10, 3 );
        add_action( 'donate_schedule_campaign_status', array( $this, 'update_campaign_status' ), 10, 3 );
    }

    /**
     * load fields
     * @return array
     */
    public function load_field() {
        return array(
            'general' => array(
                'title' => __( 'General', 'tp-donate' ),
            ),
            'compensate' => array(
                'title' => __( 'Compensate', 'tp-donate' ),
            )
        );
    }

    /**
     * metabox section
     * @param $id
     * @return
     */
    public function donate_metabox_setting( $id ) {
        $html = array();

        global $post;
        $currency = donate_get_currency();
        if ( $post && get_post_meta( $post->ID, $this->get_field_name( 'currency' ), true ) ) {
            $currency = get_post_meta( $post->ID, $this->get_field_name( 'currency' ), true );
        }

        $html[] = '<input type="hidden" name="' . esc_attr( $this->get_field_name( 'currency' ) ) . '" value="' . esc_attr( $currency ) . '"/>';
        if ( $id === 'general' ) {
            $start = $this->get_field_value( 'start' );
            if ( $start ) {
                $start = date_i18n( get_option( 'date_format', 'Y-m-d' ), strtotime( $start ) );
            }
            $end = $this->get_field_value( 'end' );
            if ( $end ) {
                $end = date_i18n( get_option( 'date_format', 'Y-m-d' ), strtotime( $end ) );
            }
            $html[] = '<div class="form-group">';
            $html[] = '<p>';
            $html[] = '<label for="' . esc_attr( $this->get_field_name( 'goal' ) ) . '">' . sprintf( '%s(%s)', __( 'Goal', 'tp-donate' ), donate_get_currency_symbol( $currency ) ) . '</label>';
            $html[] = '<input type="number" class="goal regular-text" name="' . $this->get_field_name( 'goal' ) . '" id="' . $this->get_field_name( 'goal' ) . '" value="' . $this->get_field_value( 'goal', 0 ) . '" min="0"/></th>';
            $html[] = '</p>';
            $html[] = '<p>';
            $html[] = '<label for="' . esc_attr( $this->get_field_name( 'raised' ) ) . '">' . sprintf( '%s(%s)', __( 'Raised', 'tp-donate' ), donate_get_currency_symbol( $currency ) ) . '</label>';
            $html[] = '<input type="number" class="raised regular-text" name="' . $this->get_field_name( 'raised' ) . '" id="' . $this->get_field_name( 'raised' ) . '" value="' . donate_campaign_convert_amount( donate_total_campaign(), donate_get_currency(), $currency ) . '"/></th>';
            $html[] = '</p>';
            $html[] = '</div>';
            $html[] = '<div class="form-group">';
            $html[] = '<p>';
            $html[] = '<label for="' . esc_attr( $this->get_field_name( 'start' ) ) . '">' . __( 'Start Date', 'tp-donate' ) . '</label>';
            $html[] = '<input type="text" class="start regular-text" name="' . $this->get_field_name( 'start' ) . '" id="' . $this->get_field_name( 'start' ) . '" value="' . $start . '" /></th>';
            $html[] = '</p>';
            $html[] = '<p>';
            $html[] = '<label for="' . esc_attr( $this->get_field_name( 'end' ) ) . '">' . __( 'End Date', 'tp-donate' ) . '</label>';
            $html[] = '<input type="text" class="end regular-text" name="' . $this->get_field_name( 'end' ) . '" id="' . $this->get_field_name( 'end' ) . '" value="' . $end . '" /></th>';
            $html[] = '</p>';
            $html[] = '</div>';
        } else if ( $id === 'compensate' ) {

            if ( ( $markers = $this->get_field_value( 'marker' ) ) && !empty( $markers ) ) {
                foreach ( $markers as $marker_id => $meta_val ) {
                    $html[] = '<div class="form-group donate_metabox" data-compensate-id="' . esc_attr( $marker_id ) . '">';
                    $html[] = '<div class="section">';
                    $html[] = '<p>';
                    $html[] = '<label>' . sprintf( '%s(%s)', __( 'Marker', 'tp-donate' ), donate_get_currency_symbol( $currency ) ) . '</label>';
                    $html[] = '<input type="number" step="any" name="' . $this->get_field_name( 'marker' ) . '[' . $marker_id . '][amount]" value="' . esc_attr( $meta_val['amount'] ) . '"/>';
                    $html[] = '</p>';
                    $html[] = '<p>';
                    $html[] = '<label>' . __( 'Description', 'tp-donate' ) . '</label>';
                    $html[] = '<textarea name="' . $this->get_field_name( 'marker' ) . '[' . $marker_id . '][desc]">' . esc_textarea( $meta_val['desc'] ) . '</textarea>';
                    $html[] = '</p>';
                    $html[] = '<p>';
                    $html[] = '<a href="#" class="remove" data-compensate-id="{{ data.id }}">' . __( 'Remove', 'tp-donate' ) . '</a>';
                    $html[] = '</p>';
                    $html[] = '</div>';
                    $html[] = '</div>';
                }
            }
            $html[] = '<div class="form-group">';
            $html[] = '<a href="#" class="button add_compensate">' . __( 'Add Compensate', 'tp-donate' ) . '</a>';
            $html[] = '</div>';
        }

        echo implode( '', $html );
    }

    public function admin_footer() {
        global $post;
        if ( $post && $post->post_type !== 'dn_campaign' )
            return;

        $html = '<script type="text/html" id="tmpl-compensate-layout">
			<div class="form-group donate_metabox" data-compensate-id="{{ data.id }}">
				<div class="section">
					<tr>
						<p>
							<label>' . sprintf( '%s(%s)', __( 'Marker', 'tp-donate' ), donate_get_currency_symbol() ) . '</label>
							<input type="number" step="any" name="' . $this->get_field_name( 'marker' ) . '[{{ data.id }}][amount]" value="{{ data.amount }}" />
						</p>
						<p>
							<label>' . __( 'Description', 'tp-donate' ) . '</label>
							<textarea name="' . $this->get_field_name( 'marker' ) . '[{{ data.id }}][desc]">{{ data.desc }}</textarea>
						</p>
						<p>
							<a href="#" class="remove" data-compensate-id="{{ data.id }}">' . __( 'Remove', 'tp-donate' ) . '</a>
						</p>
					</tr>
				</div>
			</div>
		</script>';

        echo $html;
    }

    /**
     * ajax create compensate
     * @return
     */
    public function donate_remove_compensate() {
        if ( !isset( $_GET['schema'] ) || $_GET['schema'] !== 'donate-ajax' || empty( $_POST ) ) {
            return;
        }

        if ( !isset( $_POST['compensate_id'] ) || !isset( $_POST['post_id'] ) )
            return;

        $marker = $this->get_field_value( 'marker' );

        if ( empty( $marker ) ) {
            wp_send_json( array( 'status' => 'success' ) );
            die();
        }

        if ( isset( $marker[$_POST['compensate_id']] ) ) {
            unset( $marker[$_POST['compensate_id']] );
        } else {
            wp_send_json( array( 'status' => 'success' ) );
            die();
        }

        if ( $update = update_post_meta( $_POST['post_id'], $this->get_field_name( 'marker' ), $marker ) ) {
            wp_send_json( array( 'status' => 'success' ) );
            die();
        }

        wp_send_json( array( 'status' => 'failed', 'message' => __( 'Could not delete compensate. Please try again.', 'tp-donate' ) ) );
        die();
    }

    /* update meta campaign */

    public function update_meta_campaign( $post_id, $post, $update ) {
        if ( !isset( $_POST ) || empty( $_POST ) ) {
            return;
        }
        foreach ( $_POST as $name => $value ) {
            if ( strpos( $name, $this->_prefix ) !== 0 ) {
                continue;
            }
            if ( in_array( $name, array( 'thimpress_campaign_start', 'thimpress_campaign_end' ) ) ) {
                if ( !$value )
                    continue;
                update_post_meta( $post_id, $name, date( 'Y-m-d H:i:s', strtotime( $value ) ) );
                if ( $value ) {
                    wp_schedule_single_event( strtotime( $value ), 'donate_schedule_campaign_status', array( $post_id, $name, $value ) );
                }
            } else {
                update_post_meta( $post_id, $name, $value );
            }
        }
    }

    public function update_campaign_status( $post_id, $name, $value ) {
        
    }

    /**
     * must login
     * @return null
     */
    public function mustLogin() {
        _e( 'You must login', 'tp-donate' );
    }

}
