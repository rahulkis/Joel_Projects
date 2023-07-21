<?php

/**
 * Class GravityView_Featured_Entries
 */
class GravityView_Featured_Entries extends GravityView_Extension {

	protected $_title            = 'Featured Entries';

	protected $_version          = GV_FEATURED_ENTRIES_VERSION;

	protected $_text_domain      = 'gravityview-featured-entries';

	/**
	 * @var int The download ID on gravityview.co
	 * @since 1.1.3
	 */
	protected $_item_id = 31;

	protected $_featured_entries = array();

	protected $_featured_count   = 0;

	protected $_min_gravityview_version = '2.0-dev';

	protected $_path = GV_FEATURED_ENTRIES_FILE;


	/**
	 * Put all plugin hooks here
	 *
	 * @since 1.0.0
	 */
	function add_hooks() {

		add_action( 'wp_enqueue_scripts',                   array( $this, 'enqueue_style' )                 );

		add_action( 'gravityview_datatables_scripts_styles', array( $this, 'enqueue_datatables_style' ) 	);

		add_filter( 'gravityview_default_args',             array( $this, 'featured_setting_arg' )          );

		add_action( 'gravityview_admin_directory_settings', array( $this, 'featured_settings' )             );

		add_filter( 'gravityview_get_entries',              array( $this, 'calculate_view_entries' ), 10, 3 );

		add_filter( 'gravityview/view/entries',             array( $this, 'sort_view_entries' ),      10, 2 );

		add_filter( 'gravityview_entry_class',              array( $this, 'featured_class' ),         10, 3 );

		add_filter( 'gravityview_field_entry_value',        array( $this, 'datatables_featured_class'), 10, 3 );

		// destroy cache when entry is starred or un-starred
		add_action('gform_update_is_starred',				array( $this, 'flush_cache' ), 10, 3 );

		/** @since 1.1 */
		add_action( 'gravityview_recent_entries_widget_form', array( $this, 'recent_entries_widget_setting' ), 10, 2 );

		/** @since 1.1 */
		add_filter( 'gravityview/widget/recent-entries/criteria', array( $this, 'recent_entries_criteria' ), 10, 3 );

	}

	/**
	 * Add a HTML element on featured entry inputs so that the jQuery code can find which entries are featured
	 * @param  string $output         Existing field output
	 * @param  array  $entry          GF Entry array
	 * @param  array  $field_settings GV Field settings array
	 * @return string                 Modified output, if the entry is starred. If not, original output.
	 */
	function datatables_featured_class( $output = '', $entry = array(), $field_settings = array() ) {

		if( !empty( $output ) && $entry['is_starred'] ) {
			$output .= '<span class="featured"></span>';
		}

		return $output;
	}


	/**
	 * Enqueue relevant stylesheets
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function enqueue_style() {

		wp_enqueue_style( 'gravityview-featured-entries', plugin_dir_url( __FILE__ ) . 'assets/css/featured-entries.css', array(), $this->_version );

		wp_register_script( 'gravityview-featured-entries', plugin_dir_url( __FILE__ ) . 'assets/js/featured-entries.min.js', array('gv-datatables'), $this->_version );

	}

	/**
	 * Enqueue DataTables stylesheets, after it is registered by the DataTables extension.
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function enqueue_datatables_style() {

		wp_enqueue_style( 'gv-datatables-featured-entries');

		wp_enqueue_script( 'gravityview-featured-entries' );

	}


	/**
	 * Add settings to the view setting array
	 *
	 * @since  1.0.0
	 *
	 * @param  array  $args Array of other view settings
	 *
	 * @return array        Appended aray of view settings
	 */
	public function featured_setting_arg( $args ) {

		$settings = array(
			'label'              => __( 'Move Featured Entries to Top', 'gravityview-featured-entries' ),
			'type'              => 'checkbox',
			'group'             => 'default',
			'value'             => 0,
			'show_in_shortcode' => true,
		);

		$args['featured_entries_to_top'] = $settings;

		return $args;

	}


	/**
	 * Add tooltip to display in Settings metabox
	 *
	 * @since  1.0.1
	 *
	 * @param  array  $tooltips Existing GV tooltips, with `title` and `value` keys
	 *
	 * @return array           Modified tooltips
	 */
	public function tooltips( $tooltips = array() ) {

		$tooltips['gv_featured_entries_to_top'] = array(
			'title'	=> __( 'Move Featured Entries to Top', 'gravityview-featured-entries' ),
			'value'	=> __( 'Always move Featured (starred) entries to the top of search results. If not enabled, Featured entries will be shown in the default order, but will be highlighted.', 'gravityview-featured-entries' ),
		);

		return $tooltips;
	}


	/**
	 * Render the setting in the metabox
	 *
	 * @since  1.0.0
	 *
	 * @param  array  $current_settings Array of current settings
	 *
	 * @return void
	 */
	public function featured_settings( $current_settings ) {

		GravityView_Render_Settings::render_setting_row( 'featured_entries_to_top', $current_settings );

	}

	/**
	 * If enabled, query featured and adjust main query as needed
	 *
	 * @since  1.0.0
	 *
	 * @param  array  $filters Array of current pre-built filters
	 * @param  array  $args    Array of settings for the current view
	 * @param  int    $form_id Gravity Forms form ID the current view is using
	 *
	 * @return array           Array of filters
	 */
	public function calculate_view_entries( $filters, $args, $form_id ) {
	    // When accessing a single entry or edit entry, don't modify query
		if ( gravityview()->request->is_entry() || gravityview()->request->is_edit_entry( $form_id ) ) {
			return $filters;
		}

		if ( defined( '\GV\Plugin::FEATURE_GFQUERY' ) && gravityview()->plugin->supports( \GV\Plugin::FEATURE_GFQUERY ) ) {
			add_action( 'gravityview/view/query', array( $this, 'gf_query_filter' ), 10, 3 );
			return $filters;
		}

		// If featured entries is enabled...
		if ( !empty( $args['featured_entries_to_top'] ) ) {


			// Get all featured entries
			$all_featured_entries = $this->get_featured_entries( $filters, $args, $form_id, true );

			$this->_featured_count = count( $all_featured_entries );

			do_action( 'gravityview_log_debug', '[featured_entries] Found ' . $this->_featured_count . ' Featured Entries', $all_featured_entries );

			// Now get just the featured entries needed for the current page
			$this->_featured_entries = $this->get_featured_entries( $filters, $args, $form_id );

			do_action( 'gravityview_log_debug', '[featured_entries] Featured entries for current page', $all_featured_entries );

			// Only get entries that aren't starred
			$filters['search_criteria']['field_filters'][] = array(
				'key'      => 'is_starred',
				'value'    => 0,
				'operator' => '='
			);

			// Calculate paging based on the number of featured entries returned
			$paging = $this->calculate_paging( $this->_featured_count, $args );

			if ( ! empty( array_filter( $paging, 'intval' ) ) ) {
				$filters['paging'] = $paging;
			}

			do_action( 'gravityview_log_debug', '[featured_entries] Final sort filter for non-featured entries: ', $filters );
		} else {
			$this->_featured_count = 0;
			$this->_featured_entries = 0;
		}

		return $filters;
	}

	/**
	 * Modify GF query to order results by `is_starred` first
	 *
	 * @since 2.0.4
	 * @since 2.0.7 Removed `$request` parameter
	 *
	 * @param GF_Query $query
	 * @param GV\View  $view
	 *
	 * @return void
	 */
	public function gf_query_filter( &$query, $view ) {
		if ( ! $view->settings->get( 'featured_entries_to_top' ) ) {
			return;
		}

		$original_query = $query->_introspect();

		// Reset order conditions on the GF_Query class by overriding the protected `order` property
		$std_class              = new stdClass;
		$std_class->reset_order = function () use ( $query ) {
			$reset = \Closure::bind( function () {
				$this->order = array();
			}, $query, get_class( $query ) );

			$reset();
		};
		call_user_func( $std_class->reset_order->bindTo( $std_class ) );

		// Order by is_starred first
		$query->order( new GF_Query_Column( 'is_starred', $view->form->ID ), GF_Query::DESC );

		// Re-apply previous order conditions
		foreach ( $original_query['order'] as $order ) {
			$query->order( $order[0], $order[1] );
		}
	}

	/**
	 * Query featured entries
	 *
	 * @since  1.0.2
	 *
	 * @param  array   $parameters Existing search parameters for current view
	 * @param  array   $args    Args array for current view
	 * @param  int     $form_id Gravity Forms form ID the current view is using
	 * @param  boolean $all     Whether all featured entries should be queried or limited to current page
	 *
	 * @return array            Array of form entries; may be empty
	 */
	protected function get_featured_entries( $parameters, $args, $form_id, $all = false ) {
		$parameters = $parameters ?: array();
		$args = $args ?: array();

		/**
		 * Allow override of default behavior, which is to respect search queries.
		 *
		 * @param boolean $always_show_featured_entry If returned true, featured entries will be shown even if the search doesn't match the entry
		 */
		if( apply_filters( 'gravityview_featured_entries_always_show', false ) ) {

			$parameters = array();

		}

		// Only starred entries
		$parameters['search_criteria']['field_filters'][] = array( 'key' => 'is_starred', 'value' => 1, 'operator' => '=' );
		$parameters['search_criteria']['status']          = 'active';

		// Apply the same sorting to featured entries query
		if ( ! empty( $args['sort_field'] ) ) {

			$parameters['sorting'] = array( 'key' => $args['sort_field'], 'direction' => $args['sort_direction'] );

		}

		// Paging & offset
		if ( $all ) {

			$parameters['paging'] = array( 'offset' => 0, 'page_size' => PHP_INT_MAX );

		} else {

			$page_size = $this->get_page_size( $args );

			if ( isset( $args['offset'] ) ) {

				$offset = $args['offset'];

			} else {

				$current_page = $this->get_page_num();
				$offset       = ( $current_page - 1 ) * $page_size;

			}

			$parameters['paging'] = array( 'offset' => $offset, 'page_size' => $page_size );

		}

		$featured = gravityview_get_entries( $form_id, $parameters );

		return $featured;

	}

	/**
	 * Get the # of entries per page
	 *
	 * @since 1.1.1
	 *
	 * @param array $args
	 *
	 * @return mixed
	 */
	private function get_page_size( $args ) {

		$page_size = !empty( $args['page_size'] ) ? $args['page_size'] : apply_filters( 'gravityview_default_page_size', 25 );

		if( !empty( $_POST['draw'] ) ) {
			$page_size = $_POST['length'];
		}

		return $page_size;
	}

	/**
	 * Get the # of entries per page
	 *
	 * @since 1.1.1
	 *
	 * @return int
	 */
	private function get_page_num() {

		// Not DataTables
		if( empty( $_POST['draw'] ) ) {
			$page_num = empty( $_GET['pagenum'] ) ? 1 : intval( $_GET['pagenum'] );
		} else {
			// Start of page count divided by items per page, plus one
			$page_num = floor( $_POST['start'] / $_POST['length'] ) + 1;
		}

		return $page_num;
	}

	/**
	 * Calculate custom paging based on current location and number of featured entries
	 *
	 * @since  1.0.2
	 *
	 * @param  integer $featured_count Total number of featured entries
	 * @param  array   $args           Args array for current view
	 *
	 * @return array                   Array of paging parameters
	 */
	protected function calculate_paging( $featured_count = 0, $args = array() ) {

		$paging = array();

		// Get page size
		$page_size = $this->get_page_size( $args );

		// Calculate some key featured numbers
		$full_pages_of_featured = absint( $featured_count / $page_size );
		$remaining_featured     = $featured_count - ( $page_size * $full_pages_of_featured );

		// Get the current page and set default offset
		$current_page = $this->get_page_num();

		// Calculate page and offset
		if ( ( ( $current_page === $full_pages_of_featured ) && ( 0 === $remaining_featured ) ) || ( $current_page <= $full_pages_of_featured ) ) {

			$paging = array( 'offset' => 0, 'page_size' => 0 );

		} else if ( ( $current_page === $full_pages_of_featured ) && ( 0 < $remaining_featured ) ) {

			$page_size = $page_size - $remaining_featured;

			$paging    = array( 'offset' => 0, 'page_size' => $page_size );

		} else {

			$actual_page = $current_page - $full_pages_of_featured;

			if ( 1 === intval( $actual_page ) ) {

				$page_size = $page_size - $remaining_featured;

				$paging    = array( 'offset' => 0, 'page_size' => $page_size );

			} else {

				$offset = ( ( $actual_page - 1 ) * $page_size ) - $remaining_featured ;

				$paging = array( 'offset' => $offset, 'page_size' => $page_size );

			}

		}

		return $paging;

	}


	/**
	 * Prepend featured entries if they exist & recalculate count (total entries)
	 *
	 * @since  1.0.2
	 *
	 * @param  \GV\Entry_Collection  $entries Associative array containing count, entries & paging
	 * @param  array  $args    Args array for current view
	 *
	 * @return \GV\Entry_Collection A combined array of entries
	 */
	public function sort_view_entries( $entries, $args ) {

		if ( empty ( $this->_featured_count ) ) {
			return $entries;
		}

		$featured_entries_collection = new \GV\Entry_Collection();

		foreach ( $this->_featured_entries as $featured_entry ) {
			$featured_entries_collection->add(  \GV\GF_Entry::from_entry( $featured_entry ) );
		}

		// prepend featured entries to the regular entries result
		$featured_entries_collection->merge( $entries );

		return $featured_entries_collection;
	}


	/**
	 * Maybe add featured class to entry
	 *
	 * @since  1.0.0
	 *
	 * @param  string  $class Current class value
	 * @param  array   $entry Array of entry data
	 * @param  GravityView_View     $view  Current GravityView_View object
	 *
	 * @return string         CSS classes to use for the entry markup
	 */
	public function featured_class( $class, $entry, $view ) {

		/**
		 * Enable or disable featured entries for this entry
		 *
		 * Default: If the entry is starred, add the featured-entry class
		 *
		 * @param GravityView_View $view The current GravityView_View instance
		 * @param array $entry Gravity Forms entry array
		 * @return boolean Whether to enable featured entries for this entry
		 */
		if ( apply_filters( 'gravityview_featured_entries_enable', ! empty( $entry['is_starred'] ), $view, $entry ) ) {

			$class .= ' gv-featured-entry';

		}

		return $class;

	}

	/**
	 * Flush the GravityView cache if the entry 'is_starred' property changes
	 *
	 * @see GFFormsModel::update_lead_property()
	 *
	 * @param  int $entry_id       the entry id
	 * @param  mixed $property_value New value of the Gravity Forms meta
	 * @param  mixed $previous_value Previous value of the Gravity Forms meta
	 *
	 * @return void
	 */
	function flush_cache( $entry_id, $property_value, $previous_value ) {

		if( !class_exists( 'GFAPI' ) ) {
			return;
		}

		$entry = GFAPI::get_entry( $entry_id );

		if( empty( $entry['form_id'] ) ) {
			return;
		}

		/**
		 * Flush the GravityView cache for this form
		 * @see class-cache.php
		 * @since 1.5.1
		 */
		do_action( 'gravityview_clear_form_cache', $entry['form_id'] );

	}

	/**
	 * Modify the search criteria for the Recent Entries widget to add the Featured Entries search filter
	 *
	 * @param array $filters
	 * @param array $instance The settings for the particular instance of the widget.
	 * @param int $form_id The ID of the form for the search
	 *
	 * @since 1.1
	 *
	 * @return array If the widget has `featured` setting enabled, then modified $filters. Otherwise, original.
	 */
	public function recent_entries_criteria( $filters, $instance, $form_id ) {

		// This requires GravityView 1.7+
		$version_check = version_compare( GravityView_Plugin::version, '1.7', '>=' );

		if( $version_check && !empty( $instance['featured'] ) ) {

			// Only get entries that are starred
			$filters['search_criteria']['field_filters'][] = array(
				'key'      => 'is_starred',
				'value'    => 1,
				'operator' => '='
			);
		}

		return $filters;
	}

	/**
	 * Render the setting for the Recent Entries widget
	 *
	 * @param WP_Widget $widget Widget object
	 * @param array $instance Widget settings
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	function recent_entries_widget_setting( WP_Widget $widget , $instance = array() ) {

		// This requires GravityView 1.7+
		if( version_compare( GravityView_Plugin::version, '1.7', '<' ) ) {
			return;
		}

		?>
        <p>
            <label>
                <span><?php _e( 'Only show featured entries:', 'gravityview-featured-entries' ); ?>&nbsp;</span>
                <input name="<?php echo $widget->get_field_name( 'featured' ); ?>" type="hidden" value="0" />
                <input <?php checked( true, !empty( $instance['featured'] ) ); ?> id="<?php echo $widget->get_field_id( 'featured' ); ?>" name="<?php echo $widget->get_field_name( 'featured' ); ?>" type="checkbox" class="checkbox" value="1" />
            </label>
        </p>
		<?php
	}
}

new GravityView_Featured_Entries;
