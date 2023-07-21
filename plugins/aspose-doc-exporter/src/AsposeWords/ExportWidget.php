<?php

namespace AsposeWords;

class ExportWidget extends \WP_Widget {

    public static function register() {
        add_action("widgets_init", function() {
            register_widget(new ExportWidget());
        });
    }

    public function __construct() {
        parent::__construct("Aspose", "Aspose.Words");
    }

    public function widget($args, $instance) {
        if (!is_singular() || is_admin()) {
            return;
        }

        $engine = new ExportEngine(get_the_ID());
        $title = ! empty( $instance['title'] ) ? $instance['title'] : 'Aspose.Words';
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        echo $args['before_widget'];

        if ( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        include_once dirname(ASPOSE_WORDS_PLUGIN_FILE) . "/templates/download-exported-file-widget.php";

        echo $args['after_widget'];
    }

    public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title    = $instance['title'];
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}

    public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$new_instance      = wp_parse_args( (array) $new_instance, array( 'title' => '' ) );
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		return $instance;
	}
}
