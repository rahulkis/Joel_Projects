<?php
/**
 * Table/DataTables View footer calculation functionality
 *
 * @package GravityMath
 */

class GravityMath_Table_Footer_Calculation {
	/**
	 * @var string Unique reference name for UI assets
	 */
	const UI_ASSETS_HANDLE = 'gv_math_admin_views';

	/**
	 * @since 2.0.2
	 *
	 * @var int Default number of decimal places
	 */
	const DECIMAL_PLACES = 2;

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_filter( 'gravityview_noconflict_scripts', [ $this, 'register_no_conflicts' ] );
		add_filter( 'gravityview_noconflict_styles', [ $this, 'register_no_conflicts' ] );
		add_filter( 'gravityview_template_field_options', [ $this, 'modify_view_field_settings' ], 10, 6 );
		add_filter( 'gravityview/template/footer', [ $this, 'modify_table_footer' ] );
		add_filter( 'gravityview/datatables/output', [ $this, 'modify_datatables_footer' ], 10, 3 );
		add_filter( 'gravityview/template/after', [ $this, 'modify_datatables_options' ], 15, 3 );
		add_filter( 'gravityview/view/settings/defaults', [ $this, 'modify_view_settings' ] );
		add_filter( 'gravityview/admin/indicator_icons', [ $this, 'modify_indicator_icons' ], 10, 2 );
		add_filter( 'gravityview/metaboxes/multiple_entries/after', [ $this, 'render_view_settings' ] );
		add_filter( 'gravityview/math/table_footer_calculation/calculation_result', [ $this, 'modify_calculation_result' ], 10, 4 );
		add_filter( 'gravityview/math/aggregate_data/modify_field_value', [ $this, 'modify_field_value_for_aggregate_calculation' ], 10, 5 );
		add_filter( 'gform_input_masks', [ $this, 'add_gf_input_mask' ] );
	}

	/**
	 * Adds duration dropdowns from available masks
	 *
	 * @since 2.0
	 *
	 * @param array $masks
	 *
	 * @return array
	 */
	public function add_gf_input_mask( $masks = array() ) {

		$masks[ esc_html_x( 'Duration (MM:SS)', 'Duration with hours and minutes', 'gk-gravitymath' ) ]              = '99:99';
		$masks[ esc_html_x( 'Duration (HH:MM:SS)', 'Duration with hours, minutes, and seconds', 'gk-gravitymath' ) ] = '99:99:99';

		return $masks;
	}

	/**
	 * Enqueue scripts on admin Views screen
	 *
	 * @since 2.0
	 *
	 * @see   /assets/js/gv-math-admin.js
	 *
	 * @param string $hook
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ) {
		global $post;

		if ( ( function_exists( 'gravityview' ) && ! gravityview()->request->is_admin( $hook ) ) || empty( $post->ID ) ) {
			return;
		}

		wp_enqueue_script(
			self::UI_ASSETS_HANDLE,
			plugins_url( 'assets/js/gv-math-admin-views.js', GRAVITYMATH_FILE ),
			[ 'jquery', 'wp-color-picker' ],
			GRAVITYMATH_VERSION
		);

		wp_enqueue_style(
			self::UI_ASSETS_HANDLE,
			plugins_url( 'assets/css/gv-math-admin-views.css', GRAVITYMATH_FILE ),
			[ 'wp-color-picker' ],
			GRAVITYMATH_VERSION
		);

	}

	/**
	 * Allowlist UI assets
	 *
	 * @since 2.0
	 *
	 * @param array $registered Registered scripts/styles
	 *
	 * @return array
	 *
	 */
	public function register_no_conflicts( $registered ) {

		$registered[] = self::UI_ASSETS_HANDLE;
		$registered[] = 'wp-color-picker';

		return $registered;
	}

	/**
	 * Add Math settings to an array of available View settings
	 *
	 * @param $settings
	 *
	 * @return array
	 */
	public function modify_view_settings( $settings ) {

		$settings = array_merge( $settings, [
			'gv_math_footer_row_background_color' => [
				'label' => __( 'Calculation Row Background', 'gk-gravitymath' ),
				'type'  => 'color',
				'value' => '#f0f0f0',
			],
		] );

		return $settings;
	}

	/**
	 * Render Math settings inside Multiple Entries metabox
	 *
	 * @param $settings
	 *
	 * @return void
	 */
	function render_view_settings( $settings ) {
		require_once GRAVITYMATH_DIR . 'includes/class-gravitymath-field-type-color.php';

		GravityView_Render_Settings::render_setting_row( 'gv_math_footer_row_background_color', $settings );
	}

	/**
	 * Add footer calculation options to View field settings
	 *
	 * @since 2.0
	 *
	 * @param array  $field_options Array of field options
	 * @param string $template_id   Table slug
	 * @param float  $field_id      GF Field ID
	 * @param string $context       Context (e.g., single or directory)
	 * @param string $field_type    Input type (e.g., textarea, list, select, etc.)
	 * @param int    $form_id       Form ID
	 *
	 * @return array
	 */
	public function modify_view_field_settings( $field_options, $template_id, $field_id, $context, $field_type, $form_id ) {
		$field = gravityview_get_field( $form_id, $field_id );
		$field = $field ? (array) $field : [];

		// min, max, count, count-empty and count-nonempty may have derivatives (e.g., count-nonempty-checked) where the only difference is the translation; calculation remains the same
		$available_calculation_types = [
			'min'                      => esc_html_x( 'Lowest', 'Lowest value', 'gk-gravitymath' ),
			'max'                      => esc_html_x( 'Highest', 'Highest value', 'gk-gravitymath' ),
			'avg'                      => esc_html_x( 'Average', 'Average value', 'gk-gravitymath' ),
			'max-slowest'              => esc_html_x( 'Slowest', 'Fastest duration', 'gk-gravitymath' ),
			'min-fastest'              => esc_html_x( 'Fastest', 'Slowest duration', 'gk-gravitymath' ),
			'count'                    => esc_html_x( 'Count (All)', 'Count of all values', 'gk-gravitymath' ),
			'count-nonempty'           => esc_html_x( 'Count (With Values)', 'Count of all empty values', 'gk-gravitymath' ),
			'count-empty'              => esc_html_x( 'Count (Without Values)', 'Count of all non-empty values', 'gk-gravitymath' ),
			'sum'                      => esc_html_x( 'Sum', 'Sum of all values', 'gk-gravitymath' ),
			'count-nonempty-checked'   => esc_html_x( 'Count (Checked)', 'Count checked options', 'gk-gravitymath' ),
			'count-empty-unchecked'    => esc_html_x( 'Count (Unchecked)', 'Count unchecked options', 'gk-gravitymath' ),
			'count-nonempty-selected'  => esc_html_x( 'Count (Selected)', 'Count selected options', 'gk-gravitymath' ),
			'count-empty-unselected'   => esc_html_x( 'Count (Unselected)', 'Count unselected options', 'gk-gravitymath' ),
			'count-nonempty-consented' => esc_html_x( 'Count (Consented)', 'Count number of consents', 'gk-gravitymath' ),
			'count-empty-unconsented'  => esc_html_x( 'Count (Unconsented)', 'Count number of unconsented options', 'gk-gravitymath' ),
			'quiz-passed'              => esc_html_x( 'Passed', 'Number of people who passed the quiz', 'gk-gravitymath' ),
			'quiz-failed'              => esc_html_x( 'Failed', 'Number of people who failed the quiz', 'gk-gravitymath' ),
			'quiz-passed-percent'      => esc_html_x( '% Passed', '% of people who passed the quiz', 'gk-gravitymath' ),
			'quiz-failed-percent'      => esc_html_x( '% Failed', '% of people who failed the quiz', 'gk-gravitymath' ),
			'quiz-avg-score'           => esc_html__( 'Average Score', 'gk-gravitymath' ),
		];

		$default_calculation_types = [ 'min', 'max', 'sum', 'avg' ];

		$supported_fields_schema = [
			// standard fields
			'number'       => $default_calculation_types,
			// quiz/survey
			'quiz_is_pass' => [ 'avg', 'quiz-passed', 'quiz-failed', 'quiz-passed-percent', 'quiz-failed-percent' ],
			'quiz_score'   => [ 'avg', 'sum' ],
			'survey'       => [ 'min', 'max', 'avg', 'count' ],
			// product fields
			'product'      => $default_calculation_types,
			'quantity'     => $default_calculation_types,
			'shipping'     => $default_calculation_types,
			'total'        => $default_calculation_types,
			// extra fields
			'consent'      => [ 'count-nonempty-consented', 'count-empty-unconsented' ],
			'checkbox'     => [ 'count-nonempty-checked', 'count-empty-unchecked' ],
			'radio'        => [ 'count-nonempty-checked', 'count-empty-unchecked' ],
			'select'       => [ 'count-nonempty-selected', 'count-empty-unselected' ],
			'multiselect'  => [ 'count-nonempty-selected', 'count-empty-unselected' ],
			'duration'     => [ 'max-slowest', 'min-fastest', 'avg', 'sum' ],
			'custom'       => [], // Only "Custom"
		];

		/**
		 * @filter `gk/gravitymath/table-footer-calculation/supported-fields-schema` Modify the list of fields that support table footer calculations.
		 *
		 * @since  2.3.3
		 *
		 * @param array $supported_fields_schema     Supported fields.
		 * @param array $available_calculation_types Supported calculations.
		 */
		$supported_fields_schema = apply_filters( 'gk/gravitymath/table-footer-calculation/supported-fields-schema', $supported_fields_schema, $available_calculation_types );

		// We only support Likert surveys with scoring enabled
		if ( 'survey' === $field_type && empty( $field['gsurveyLikertEnableScoring'] ) ) {
			return $field_options;
		}

		// We only support single checkbox inputs
		if ( 'checkbox' === $field_type && strpos( $field_id, '.' ) === false ) {
			return $field_options;
		}

		if ( $this->is_duration_field( $field ) ) {
			$field_type = 'duration';
		}

		if ( 'directory' !== $context || ! in_array( $field_type, array_keys( $supported_fields_schema ) ) ) {
			return $field_options;
		}

		$field_calculation_types = array_intersect_key(
			$available_calculation_types,
			array_fill_keys( $supported_fields_schema[ $field_type ], '' )
		);

		$math_field_options = array_merge(
			[
				'gv_math_footer_calculation'       => [
					'type'  => 'checkbox',
					'label' => esc_html__( 'Add field calculations to the table footer?', 'gk-gravitymath' ),
					'value' => false,
				],
				'gv_math_footer_calculation_label' => [
					'type'        => 'text',
					'label'       => esc_html__( 'Label', 'gk-gravitymath' ),
					'desc'        => esc_html__( 'Wrap output in custom text. Use {result} to insert calculation result.', 'gk-gravitymath' ),
					'value'       => '{result}',
					'placeholder' => esc_attr_x( 'Sum: {result}', 'Placeholder value for label of the footer calculation row', 'gk-gravitymath' ),
					'merge_tags'  => true,
				],
			],
			( 'duration' !== $field_type ) ? [] : [
				'gv_math_footer_calculation_duration_field_format' => [
					'type'    => 'select',
					'label'   => esc_html__( 'Format Duration', 'gk-gravitymath' ),
					'options' => [
						'hh:mm:ss'       => esc_html__( '00:00:00', 'gk-gravitymath' ),
						'human_readable' => esc_html__( '0 hours, 0 minutes, 0 seconds', 'gk-gravitymath' ),
					],
					'value'   => 'human_readable',
				],
			],
			[
				'gv_math_footer_calculation_decimals' => [
					'type'  => 'number',
					'label' => esc_html__( 'Decimals', 'gk-gravitymath' ),
					'desc'  => esc_html__( 'Precision of the number of decimal places.', 'gk-gravitymath' ),
					'value' => self::DECIMAL_PLACES,
				],
			],
			[
				'gv_math_footer_calculation_source'           => [
					'type'    => 'radio',
					'label'   => esc_html__( 'Calculation Source:', 'gk-gravitymath' ),
					'desc'    => esc_html__( 'Choose between all View entries, visible View entries or all form entries.', 'gk-gravitymath' ),
					'choices' => [
						'view'    => esc_html_x( 'View (All)', 'Use all View entries as a calculation source', 'gk-gravitymath' ),
						'visible' => esc_html_x( 'View (Visible)', 'Use visible View entries as a calculation source', 'gk-gravitymath' ),
						'form'    => esc_html_x( 'Form (All)', 'Use all form entries as a calculation source', 'gk-gravitymath' ),
					],
					'value'   => 'view',
				],
				'gv_math_footer_calculation_type'             => [
					'type'    => 'select',
					'label'   => esc_html__( 'Calculation Type:', 'gk-gravitymath' ),
					'options' => array_merge(
						$field_calculation_types,
						[ 'custom' => esc_html__( 'Custom', 'gk-gravitymath' ) ]
					),
					'value'   => 'sum',
				],
				'gv_math_footer_calculation_custom_shortcode' => [
					'type'       => 'text',
					'label'      => esc_html__( 'Custom Calculation', 'gk-gravitymath' ),
                                    // translators: %1$s: opening <a> tag, %2$s: closing </a> tag
					'desc'       => sprintf( __( 'Enter custom [url]Math shortcode[/url].', 'gk-gravitymath' ), '<a href="https://docs.gravityview.co/article/295-math-shortcode" data-beacon-article-sidebar="55c14201e4b01fdb81eb078d">', '</a>' ) . ' ' .
                                    // translators: %1$s: opening <a> tag, %2$s: closing </a> tag
                                    sprintf( __( 'Click the arrow icon next to the content area to add %1$sMerge Tags%2$s.', 'gk-gravitymath' ), '<a href="https://docs.gravityview.co/article/76-merge-tags" data-beacon-article-inline="54c67bbbe4b051242988551d">', '</a>' ),
					'value'      => '',
					'merge_tags' => 'force',
					'class'      => 'widefat code',
				],
			] );

		$priority = 2000;
		foreach ( $math_field_options as &$option ) {
			$option['priority'] = $priority;
			$priority           = $priority + 10;
		}

		return array_merge( $field_options, $math_field_options );
	}

	/**
	 * Add calculations to the Table View footer
	 *
	 * @since 2.0
	 *
	 * @param \GV\Template_Context $context
	 *
	 * @return void
	 */
	public function modify_table_footer( GV\Template_Context $context ) {
		// Do not add calculations in the single entry layout.
		if ( gravityview()->request->is_entry() ) {
			return;
		}

		$result = $this->get_footer_calculation_output( $context->template->view );

		echo $result;
	}


	/**
	 * Add `footerCallback` to the DataTables options to modify the footer output
	 *
	 * @return void
	 */
	public function modify_datatables_options() {
		?>
        <script type="text/javascript">
			if ( window.gvDTglobals ) {
				for ( var index in gvDTglobals ) {
					gvDTglobals[ index ].footerCallback = function () {
						var api = this.api();
						var $footer = jQuery( api.table().footer() );
						var mathFooter = api.ajax.json()[ 'gvMathFooter' ];

						if ( !mathFooter ) {
							return;
						}

						if ( $footer.find( '.gv-math-footer-calculation' ).length ) {
							$footer.find( '.gv-math-footer-calculation' ).replaceWith( mathFooter );
						} else {
							$footer.append( mathFooter );
						}

						jQuery( api.table().footer() ).html( $footer.html() );
					}
				}
			}
        </script>
		<?php
	}

	/**
	 * Add calculations to the DataTables View footer
	 *
	 * @since 2.0
	 *
	 * @param array                $datatables_settings
	 * @param \GV\View             $view
	 * @param \GV\Template_Context $context
	 * @param \GV\Entry_Collection $entries
	 *
	 * @return void
	 */
	public function modify_datatables_footer( $datatables_settings, \GV\View $view, GV\Entry_Collection $entries ) {
		$visible_entries = function () use ( $entries ) {
			return $entries->all();
		};

		add_filter( 'gravityview/math/entries/visible', $visible_entries );

		$output = $this->get_footer_calculation_output( $view );

		if ( '' !== $output ) {
			$datatables_settings['gvMathFooter'] = $output;
		}

		return $datatables_settings;
	}

	/**
	 * Get footer markup with calculation results
	 *
	 * @param \GV\View $view
	 *
	 * @return string
	 */
	public function get_footer_calculation_output( \GV\View $view ) {
		$column_configuration = $view->fields->by_visible( $view )->as_configuration();

		$columns = rgar( $column_configuration, 'directory_table-columns', [] );

		if ( ! preg_match( '/"gv_math_footer_calculation":"1"/', json_encode( $columns ) ) ) {
			return '';
		}

		$footer_style = '';
		if ( ! empty( $view->settings->get( 'gv_math_footer_row_background_color' ) ) ) {
			$footer_style = sprintf( 'background-color: %s;', $view->settings->get( 'gv_math_footer_row_background_color' ) );
		}

		$footer = '<tr class="gv-math-footer-calculation">';

		foreach ( $columns as $column ) {
			$field = gravityview_get_field( $view->form, $column['id'] );
			$field = $field ? (array) $field : [ 'type' => $column['id'] ];

			if ( empty( $column['gv_math_footer_calculation'] ) ) {
				$footer .= '<th></th>';

				continue;
			}

			$scope            = rgar( $column, 'gv_math_footer_calculation_source', 'visible' );
			$field_id         = $column['id'];
			$view_id          = $view->ID;
			$form_id          = $view->form->ID;
			$calculation_type = rgar( $column, 'gv_math_footer_calculation_type', 'sum' );

			$math_shortcode = ( 'custom' === $calculation_type ) ?
				$column['gv_math_footer_calculation_custom_shortcode'] :
				$this->construct_math_shortcode( $scope, $field_id, $view_id, $form_id, $calculation_type, $field, $column );

			$modify_calculation_output = function ( $calculation_result ) use ( $field, $column, $math_shortcode ) {
				/**
				 * @filter `gravityview/math/table_footer_calculation/calculation_result` Modify calculation result
				 *
				 * @since  2.0
				 * @since  2.0.2 Added $math_shortcode parameter
				 *
				 * @param string $calculation_result Calculation result
				 * @param array  $field              Field data
				 * @param array  $column             Column data
				 * @param string $math_shortcode     Math shortcode
				 */
				$calculation_result = apply_filters( 'gravityview/math/table_footer_calculation/calculation_result', $calculation_result, $field, $column, $math_shortcode );

				return $calculation_result;
			};

			// Calculation result must be modified before debug and other markup is added to it
			add_filter( 'gravityview/math/shortcode/output', $modify_calculation_output, 1 );
			$calculation_result = do_shortcode( $math_shortcode );
			remove_filter( 'gravityview/math/shortcode/output', $modify_calculation_output, 1 );

			$label = rgar( $column, 'gv_math_footer_calculation_label', '{result}' );
			$label = str_replace( '{result}', $calculation_result, $label );

			$footer .= sprintf( '<th style="%s">%s</th>', $footer_style, $label );
		}

		$footer .= '</tr>';

		return $footer;
	}

	/**
	 * Modify Math shortcode before processing it
	 *
	 * @since    2.0
	 *
	 * @param string $scope            Scope
	 * @param string $field_id         Field ID
	 * @param int    $view_id          View ID
	 * @param int    $form_id          Form ID
	 * @param string $calculation_type Calculation type ( e.g., 'count', 'sum', 'avg' )
	 * @param array  $field            Field data
	 * @param array  $column           Column data
	 *
	 * @return string Shortcode
	 */
	public function construct_math_shortcode( $scope, $field_id, $view_id, $form_id, $calculation_type, $field, $column ) {
		$formula = '';
		$filter  = '';

		if ( 'consent' === $field['type'] ) {
			$field_id = $field['inputs'][0]['id'];
		}

		if ( preg_match( '/^(max|min)/', $calculation_type ) ) {
			$calculation_type = ( strpos( $calculation_type, 'max' ) !== false ) ? 'max' : 'min';
		}

		if ( preg_match( '/count-(empty|nonempty)/', $calculation_type ) ) {
			$op               = ( strpos( $calculation_type, 'nonempty' ) !== false ) ? 'isnot' : 'is';
			$filter           = "filter_${field_id}=&op_${field_id}={$op}";
			$calculation_type = 'count';
		}

		if ( preg_match( '/quiz-(passed|failed)/', $calculation_type ) ) {
			$value  = ( strpos( $calculation_type, 'passed' ) !== false ) ? 1 : 0;
			$filter = "filter_${field_id}=${value}&op_${field_id}=is";

			if ( strpos( $calculation_type, 'percent' ) !== false ) {
				$formula = sprintf( '{:%s:count} / {entry_count} * 100', $field_id );
			}

			$calculation_type = 'count';
		}

		if ( 'view' === $scope ) {
			$scope = 'scope="view" id="' . $view_id . '"';
		} elseif ( 'form' === $scope ) {
			$scope = 'scope="form" id="' . $form_id . '"';
		} else {
			$scope = 'scope="visible"';
		}

		$decimals = rgar( $column, 'gv_math_footer_calculation_decimals' );
		$decimals = '' === $decimals ? self::DECIMAL_PLACES : $decimals;

		$filter = ( '' !== $filter ) ?
			'filter="' . $filter . '"' :
			$filter;

		$formula = ( '' !== $formula ) ?
			$formula :
			sprintf( '{:%s:%s}', $field_id, $calculation_type );

		$shortcode = sprintf(
			'[gvmath %s %s decimals="' . $decimals . '"]%s[/gvmath]',
			$scope,
			$filter,
			$formula
		);

		$filter_data = compact( 'scope', 'field_id', 'view_id', 'form_id', 'calculation_type', 'field', 'column', 'formula' );

		/**
		 * @filter `gravityview/math/table_footer_calculation/math_shortcode` Modify Math shortcode used for calculation
		 *
		 * @since  2.0
		 *
		 * @param string $shortcode        Shortcode
		 * @param array  $filter_data      {
		 *
		 * @type string  $scope            Scope.
		 * @type string  $field_id         Field ID.
		 * @type int     $view_id          View ID.
		 * @type int     $form_id          Form ID.
		 * @type string  $calculation_type Calculation type ( e.g., 'count', 'sum', 'avg' ).
		 * @type array   $field            Field data.
		 * @type array   $column           Column data.
		 * }
		 */
		$shortcode = apply_filters( 'gravityview/math/table_footer_calculation/math_shortcode', $shortcode, $filter_data );

		return $shortcode;
	}

	/**
	 * Modify field value before used for calculation
	 *
	 * @since 2.0
	 *
	 * @param string            $transformed_value Transformed field value
	 * @param string            $orginal_value     Original field value
	 * @param int|double|string $field_id          Field ID
	 * @param array             $entry             Entry data
	 * @param string|null       $calculation_type  Calculation type (e.g., sum, avg, etc.)
	 *
	 * @return string
	 */
	public function modify_field_value_for_aggregate_calculation( $transformed_value, $original_value, $field_id, $entry, $calculation_type ) {
		if ( ! isset( $entry['form_id'] ) ) {
			return $transformed_value;
		}

		$field = gravityview_get_field( $entry['form_id'], $field_id );
		$field = $field ? (array) $field : [];

		if ( 'survey' === rgar( $field, 'type' ) && rgar( $field, 'gsurveyLikertEnableScoring' ) ) {
			foreach ( $field['choices'] as $choice ) {
				if ( $original_value === $choice['value'] ) {
					return $choice['score'];
				}
			}
		}

		if ( $this->is_duration_field( $field ) && preg_match( '/(\d+):(\d+):?(\d+)?/', $original_value, $matches ) ) {
			if ( isset( $matches[3] ) ) {
				$hours   = (int) $matches[1] * 3600;
				$minutes = (int) $matches[2] * 60;
				$seconds = (int) $matches[3];
			} else {
				$hours   = 0;
				$minutes = (int) $matches[1] * 60;
				$seconds = (int) $matches[2];
			}

			return $hours + $minutes + $seconds;
		}

		return $transformed_value;
	}

	/**
	 * Modify calculation result
	 *
	 * @since  2.0
	 * @since  2.0.2 Added $math_shortcode parameter
	 *
	 * @param string $calculation_type Calculation type (e.g., 'count', 'sum', 'avg')
	 * @param array  $field            Field data
	 * @param array  $column           Column data
	 * @param string $math_shortcode   Math shortcode
	 *
	 * @return string $calculation_type
	 */
	public function modify_calculation_result( $calculation_result, $field, $column, $math_shortcode ) {
		$calculation_result = (float) GravityMath_Shortcode::strip_thousands_sep( $calculation_result );

		if ( $this->is_duration_field( $field ) ) {
			$hours   = floor( $calculation_result / 3600 );
			$minutes = floor( $calculation_result / 60 ) % 60;
			$seconds = $calculation_result % 60;

			/** @see https://www.w3.org/TR/2014/REC-html5-20141028/infrastructure.html#valid-duration-string */
			$duration_string = sprintf( 'P%dH%dM%dS', $hours, $minutes, $seconds );
			$human_readable  = human_readable_duration( sprintf( '%d:%d:%d', $hours, $minutes, $seconds ) );

			// Pass lots of data with just one variable
			$filter_data = compact( 'hours', 'minutes', 'seconds', 'field', 'column', 'human_readable', 'duration_string' );

			/**
			 * @filter `gravityview/math/table_footer_calculation/format_duration_field_calculation_result` Format calculation result of a duration field
			 *
			 * @since  2.0
			 *
			 * @param float    $calculation_result Total duration in seconds
			 * @param array    $filter_data        {
			 *
			 * @type int       $hours              Hours.
			 * @type int       $minutes            Minutes.
			 * @type int       $seconds            Seconds.
			 * @type \GF_Field $field              Gravity Forms field used for calculation.
			 * @type array     $column             GravityView field settings for the column.
			 * @type string    $duration_string    An ISO8601-valid duration string to be used with `<time>` HTML tag.
			 * @type string    $human_readable     The duration, in a human-readable format via {@see human_readable_duration}
			 * }
			 */
			$formatted_calculation_result = apply_filters( 'gravityview/math/table_footer_calculation/format_duration_field_calculation_result', $calculation_result, $filter_data );

			if ( $formatted_calculation_result !== $calculation_result ) {
				return $formatted_calculation_result;
			}

			if ( 'hh:mm:ss' === \GV\Utils::get( $column, 'gv_math_footer_calculation_duration_field_format' ) ) {
				$value = sprintf( '%s:%s:%s', str_pad( $hours, 2, '0', STR_PAD_LEFT ), str_pad( $minutes, 2, '0', STR_PAD_LEFT ), str_pad( $seconds, 2, '0', STR_PAD_LEFT ) );
			} else {
				$value = $human_readable;
			}

			return sprintf( '<time datetime="%s" title="%s">%s</time>', $duration_string, esc_attr( $human_readable ), esc_html( $value ) );
		}

		if ( in_array( $field['type'], [ 'total', 'product', 'shipping' ] ) || ( isset( $field['inputType'] ) && 'price' === $field['inputType'] ) ) {
			$process_as_currency = true;

			if ( 'product' === $field['type'] && strpos( $column['id'], '.3' ) !== false ) {
				// .3 = quantity.
				$process_as_currency = false;
			}

			if ( $process_as_currency ) {
				return GFCommon::format_number(
					$calculation_result,
					'currency',
					GFCommon::get_currency()
				);
			}
		}

		// `shortcode_parse_atts()` fails to return proper results
		if ( preg_match( '/decimals="(\d+)"/', $math_shortcode, $match ) ) {
			$decimals = $match[1];
		} else {
			$decimals = ( isset( $column['gv_math_footer_calculation_decimals'] ) ) ? $column['gv_math_footer_calculation_decimals'] : '';
		}

		return GravityMath_Shortcode::format_number( $calculation_result, $decimals );
	}

	/**
	 * @filter `gravityview/admin/indicator_icons` Modify the icon output to add additional indicator icons
	 *
	 * @since  2.0
	 *
	 * @param array $icons Array of icons to be shown, with `visible`, `title`, `css_class` keys.
	 * @param array $item_settings
	 *
	 * @return array $icons + $added_icons
	 */
	public function modify_indicator_icons( $icons, $item_settings ) {
		$added_icons = [
			'footer_calc' => [
				'visible'   => ! empty( $item_settings['gv_math_footer_calculation'] ),
				'title'     => esc_html__( 'This field has calculations being displayed in the table footer.', 'gk-gravitymath' ),
				'css_class' => 'dashicons dashicons-calculator icon-footer-calculation gv-indicator-icon'
			]
		];

		return $icons + $added_icons;
	}

	/**
	 * Check if field should be treated as a duration field. Checks field type, input mask, and CSS class.
	 *
	 * @since 2.0
	 *
	 * @param array $field
	 *
	 * @return bool
	 */
	public function is_duration_field( $field = [] ) {

		if ( 'duration' === \GV\Utils::get( $field, 'type' ) ) {
			return true;
		}

		if ( in_array( \GV\Utils::get( $field, 'inputMaskValue' ), array( '99:99', '99:99:99' ), true ) ) {
			return true;
		}

		return strpos( \GV\Utils::get( $field, 'cssClass', '' ), 'gv-math-duration' ) !== false;
	}
}

new GravityMath_Table_Footer_Calculation();
