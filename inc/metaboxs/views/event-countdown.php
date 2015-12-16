<table>
	<tr>
		<th>
			<label><?php _e( 'Start Event', 'tp_event' ) ?></label>
		</th>
		<td>
			<p id="donate_datetime_start">
			    <input type="text" class="date start" name="<?php echo esc_attr( $this->get_field_name( 'date_start' ) ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'date_start' ) ); ?>"/>
			    <input type="text" class="time start" name="<?php echo esc_attr( $this->get_field_name( 'time_start' ) ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'time_start' ) ); ?>"/>
			</p>
		</td>
	</tr>
	<tr>
		<th>
			<label><?php _e( 'End Event', 'tp_event' ) ?></label>
		</th>
		<td>
			<p id="donate_datetime_end">
			    <input type="text" class="date end" name="<?php echo esc_attr( $this->get_field_name( 'date_end' ) ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'date_end' ) ); ?>"/>
			    <input type="text" class="time end" name="<?php echo esc_attr( $this->get_field_name( 'time_end' ) ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'time_end' ) ); ?>"/>
			</p>
		</td>
	</tr>
</table>
<script>
    (function($){
    	$.noConflict();
    	$(document).ready(function(){
    		// initialize input widgets first
		    $('#donate_datetime_start .time').timepicker({
		        'showDuration': true,
		        'timeFormat': 'g:i A'
		    });

		    $('#donate_datetime_start .date').datepicker({
		        'format': 'mm/dd/yyyy',
		        'autoclose': true
		    });
    		// initialize input widgets first
		    $('#donate_datetime_end .time').timepicker({
		        'showDuration': true,
		        'timeFormat': 'g:i A'
		    });

		    $('#donate_datetime_end .date').datepicker({
		        'format': 'm/d/yyyy',
		        'autoclose': true
		    });
    	});
    })(jQuery);
</script>