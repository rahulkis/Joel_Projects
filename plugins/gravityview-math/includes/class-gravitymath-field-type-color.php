<?php
/**
 * color input type
 */
class GravityView_FieldType_color extends GravityView_FieldType_text {
	function render_input( $override_input = null ) {
		if ( isset( $override_input ) ) {
			echo $override_input;

			return;
		}

		$class = \GV\Utils::get( $this->field, 'class', '' );

		?>
        <input name="<?php echo esc_attr( $this->name ); ?>" id="<?php echo $this->get_field_id(); ?>" type="text" value="<?php echo esc_attr( $this->value ); ?>" class="<?php echo esc_attr( $class ); ?>">

        <script>
	        jQuery(document).ready(function($) {
	        	if( $.fn.wpColorPicker ) {
			        $( '#<?php echo $this->get_field_id(); ?>' ).wpColorPicker();
		        } else {
			        $( '#<?php echo $this->get_field_id(); ?>' ).attr( 'type', 'color' );
                }
	        });
        </script>
        <?php
	}
}
