<table>
	<tr>
		<th>
			<label><?php _e( 'Goal:', 'dn_donate' ) ?></label>
		</th>
		<td>
			<p id="donate_coal">
			    <input type="number" class="goal" name="<?php echo esc_attr( $this->get_field_name( 'goal' ) ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'goal', 0 ) ); ?>" min="0"/>
			</p>
		</td>
	</tr>
	<tr>
		<th>
			<label><?php _e( 'Raised:', 'dn_donate' ) ?></label>
		</th>
		<td>
			<p id="donate_raised">
			    <input type="number" class="raised" name="<?php //echo esc_attr( $this->get_field_name( 'raised' ) ); ?>" value="<?php //echo esc_attr( $this->get_field_value( 'raised', 0 ) ); ?>" min="0" readonly/>
			</p>
		</td>
	</tr>
</table>