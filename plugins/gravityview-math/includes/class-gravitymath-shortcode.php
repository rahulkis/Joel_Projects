<?php

/**
 * @property string original_shortcode
 */
class GravityMath_Shortcode {
	/**
	 * @since 1.0
	 *
	 * @var int The max number of decimal digits to display. Overridden by `gravityview/math/precision` filter
	 */
	private $precision_level = 16;

	/**
	 * @var mixed $default_value when {merge_tag} value returns empty or 0. If 'skip' then skip the current formula
	 */
	private $default_value = 'skip';

	/**
	 * @since 1.0
	 *
	 * @var array {
	 * @type string $formula       If defined, use this for the equation instead of content inside the shortcode
	 * @type string $format        Format for output. Options: `raw` (no number formatting)
	 * @type int    $decimals      Number of decimals to use. Default: '' (empty string). If not set, shows calculated # of decimals
	 * @type bool   $debug         Whether or not to display debug content. {@see GravityMath_Shortcode::report_errors}
	 * @type string $default_value The default value to display if error or warning
	 * @type bool   $notices       Whether to show or hide accuracy messages
	 *        }
	 */
	private static $default_atts = array(
		'formula'       => '', // If defined, use this for the equation instead of content inside the shortcode
		'format'        => '', // raw
		'decimals'      => '',       // If not set, use original decimals
		'debug'         => false,
		'default_value' => 'skip',
		'notices'       => false,
	);

	/**
	 * @since 1.0
	 *
	 * @var GravityMath_Engine
	 */
	private $calculator;


	/**
	 * @since 1.1
	 *
	 * @var GravityMath_Report
	 */
	public $reporter;

	/**
	 * @since 1.0
	 *
	 * @var GravityMath_Shortcode
	 */
	public static $instance = null;

	/**
	 * @since 1.0
	 *
	 * @param GravityMath_Engine $calculator
	 * @param GravityMath_Report $reporter
	 *
	 * @return GravityMath_Shortcode
	 */
	public static function get_instance( $calculator, $reporter ) {

		if ( ! self::$instance ) {
			self::$instance = new self( $calculator, $reporter );
		}

		return self::$instance;
	}

	/**
	 * @param GravityMath_Engine $calculator
	 * @param GravityMath_Report $reporter
	 */
	private function __construct( $calculator, $reporter ) {

		$this->calculator = $calculator;
		$this->reporter   = $reporter;

		/**
		 * @filter `gravityview/math/precision`
		 * @since  1.0
		 *
		 * @param int $float_precision The number of decimal places to use as a maximum precision level
		 */
		$this->precision_level = (int) apply_filters( 'gravityview/math/precision', $this->precision_level );

		$this->add_hooks();
	}

	/**
	 * Register the shortcode
	 *
	 * @since 1.0
	 */
	public function add_hooks() {
		add_shortcode( 'gravitymath', array( $this, 'do_shortcode' ), 11 );
		add_shortcode( 'gravitymath2', array( $this, 'do_shortcode' ), 11 );
		add_shortcode( 'gravitymath3', array( $this, 'do_shortcode' ), 11 );
		add_shortcode( 'gvmath', array( $this, 'do_shortcode' ), 11 );
		add_shortcode( 'gv_math', array( $this, 'do_shortcode' ), 11 );
	}

	/**
	 * WordPress converts minus to "–" (ndash)
	 *
	 * @since 1.0
	 *
	 * @param string $original_formula
	 *
	 * @return string Formula with tags and whitespaces stripped, with thousands separators taken out, and dashes converted to minus
	 */
	public static function sanitize_formula( $original_formula ) {
		$sanitized_formula = wp_strip_all_tags( $original_formula ); // Fix BR or P for shortcodes with newlines

		// Remove mdash
		$sanitized_formula = str_replace( array( '&#8211;' ), array( '-' ), $sanitized_formula );

		// replace multiple white spaces with single space
		$sanitized_formula = preg_replace( '/\s+/', ' ', $sanitized_formula );

		// Strip currencies
		if ( class_exists( 'RGCurrency' ) ) {

			$currencies = RGCurrency::get_currencies();

			foreach ( $currencies as $currency ) {

				$strip_currency = array_merge(
					array( $currency['symbol_right'], $currency['symbol_left'] ),
					array( html_entity_decode( $currency['symbol_right'] ), html_entity_decode( $currency['symbol_left'] ) )
				);

				if ( ! empty( $currency['symbol_old'] ) ) {
					$strip_currency[] = $currency['symbol_old'];
				}

				$strip_currency = array_map( 'preg_quote', array_filter( $strip_currency ) );

				/**
				 * Make sure that anything we want to replace is standalone and not part of a constant.
				 */
				$sanitized_formula = preg_replace( sprintf( '/(?=^|[^A-Z])(%s)(?=[^A-Z]|$)/', implode( '|', $strip_currency ) ), '', $sanitized_formula );
			}
		}

		$sanitized_formula = self::strip_thousands_sep( $sanitized_formula );

		/**
		 * @filter `gravityview/math/sanitize` Modify the sanitization
		 *
		 * @since  1.0
		 * @since  2.0.2 Added $original_formula parameter
		 *
		 * @param string $sanitized_formula Sanitized formula
		 * @param string $original_formula  Original formula
		 */
		$sanitized_formula = apply_filters( 'gravityview/math/sanitize', $sanitized_formula, $original_formula );

		return $sanitized_formula;
	}

	/**
	 *
	 * @param $formula
	 *
	 * @return string|string[]
	 * @internal
	 *
	 */
	static public function strip_thousands_sep( $original_number ) {
		global $wp_locale;

		/**
		 * @filter `gravityview/math/thousands_sep` Change the thousands separator used to clean GF merge tag values
		 *
		 * @since  1.0
		 *
		 * @param string $thousands_sep The thousands separator for numbers. Normally either a comma or dot
		 */
		$thousands_sep = apply_filters( 'gravityview/math/thousands_sep', $wp_locale->number_format['thousands_sep'] );

		// Number is an array if passed via callback
		$number = is_array( $original_number ) ? $original_number[1] : $original_number;

		// Number can be a formula (e.g., "min(-1, 1)") with commas separating values; temporarily swap it with a custom character and replace it back with a comma after all other operations are done
		$number = preg_replace( '/(\s+)?,\s+/', '##', $number );

		$data = preg_split( '/\s+/', $number );
		foreach ( $data as &$datum ) {
			$datum = str_replace( $thousands_sep, '', $datum );
			$datum = str_replace( ',', '.', $datum );
		}

		$number = implode( ' ', $data );
		$number = str_replace( '##', ', ', $number );

		return $number;
	}

	/**
	 * If a default value is set and not '' or false, then use it. Otherwise, use the form's default value
	 *
	 * @since 1.0
	 *
	 * @see   GravityMathAddOn::get_default_value() Modifies the default value for each form
	 *
	 * @param array $atts Attributes array passed to the shortcode
	 *
	 * @return mixed The default value to use if the equation does not have a valid output
	 */
	private function get_default_value( $atts ) {

		/**
		 * @filter `gravityview/math/shortcode/default_value` Modify the default value before being processed
		 *
		 * @param  [in,out] mixed $this->default_value The default value to modify
		 */
		$default_value = apply_filters( 'gravityview/math/shortcode/default_value', $this->default_value, $atts );

		if ( ! isset( $atts['default_value'] ) ) {
			return $default_value;
		}

		if ( '' === $atts['default_value'] || 'false' === $atts['default_value'] ) {
			return $default_value;
		}

		if ( ! GravityMath_Shortcode::is_valid_default_value( $atts['default_value'] ) ) {
			return $default_value;
		}

		return $atts['default_value'];
	}

	/**
	 * Process the shortcode
	 *
	 * @since 1.0
	 *
	 * @see   GravityMath_Shortcode::$default_atts
	 *
	 * @param array  $atts Attributes for the shortcode.
	 * @param string $content
	 * @param string $shortcode
	 *
	 * @return mixed|string|void
	 */
	public function do_shortcode( $atts = array(), $passed_content = '', $shortcode = 'gravitymath' ) {
		static $nesting_level;

		$nesting_level = isset( $nesting_level ) ? $nesting_level + 1 : 1;

		// Allow for nesting.
		$content = apply_shortcodes( $passed_content );

		$nesting_level = $nesting_level - 1;

		$this->default_value = $this->get_default_value( $atts );

		// Don't use shortcode_atts() because we don't want to filter any other defined attributes
		$atts = wp_parse_args( $atts, self::$default_atts );

		// If content is empty, use the `formula` parameter instead
		$formula = ( '' === $content ) ? $atts['formula'] : $content;

		/**
		 * @filter `gravityview/math/shortcode/before` Modify the formula before being processed
		 *
		 * @since  1.0
		 *
		 * @param string $formula The math formula to modify
		 * @param array $atts Shortcode parameters
		 * @param string $content Content passed to the shortcode
		 * @param string $shortcode Shortcode used (default: `gv_math`)
		 * @param GravityMath_Shortcode $this is Current object
		 * @param int $nesting_level Current nesting level
		 */
		$formula = apply_filters( 'gravityview/math/shortcode/before', $formula, $atts, $content, $shortcode, $this, $nesting_level );

		// Strip whitespace, <br>s, etc.
		$formula = self::sanitize_formula( $formula );

		//if the flag is true then there is an error or warning to explain this
		if ( true === $this->reporter->get_skip_flag() ) {
			$result = '';
		} //if the formula is empty and the skipFlag is not true, log empty formula error
		elseif ( '' === $formula ) {

			$data = array(
				'atts'    => $atts,
				'content' => $content,
				'code'    => 'empty_formula'
			);

			do_action( 'gravityview_math_log_error', esc_html__( 'The [gv_math] formula was empty', 'gk-gravitymath' ), $data );

			unset( $data );

			$result = '';

		} else {
			$result = $this->process_formula( $formula, $atts, $shortcode );
		}

		/**
		 * @filter `gravityview/math/shortcode/before` Modify the output of the shortcode
		 *
		 * @since  1.0
		 *
		 * @param  string $result Shortcode output.
		 * @param  array $atts Shortcode parameters.
		 * @param  string $content Content passed to the shortcode.
		 * @param  string $shortcode Shortcode used (default: `gv_math`).
		 * @param  GravityMath_Shortcode $this Current object.
		 * @param  string $nesting_level Level of shortcode nesting.
		 */
		$output = apply_filters( 'gravityview/math/shortcode/output', $result, $atts, $content, $shortcode, $this, $nesting_level );

		return $output;
	}

	/**
	 * Run the formula and format the result
	 *
	 * @since 1.0
	 *
	 * @param string $formula   The math formula to run
	 * @param array  $atts
	 * @param string $shortcode Shortcode used. Default: `gv_math`
	 *
	 * @return mixed|string|void
	 * @uses  GravityMath_Engine::result
	 *
	 */
	private function process_formula( $formula = '', $atts = array(), $shortcode = 'gravitymath' ) {

		$result = '';

		try {

			$result = $this->calculator->result( $formula );

		} catch ( Exception $e ) {

			$data = array(
				'atts'               => $atts,
				'trace'              => $e,
				'calculated_formula' => $formula,
				'code'               => 'calc_error'
			);

			do_action( 'gravityview_math_log_error', esc_html__( 'Error', 'gk-gravitymath' ), $data );

			unset( $data );
		}

		// If the $atts['format'] isn't set as raw, then format the number
		if ( 'raw' !== $atts['format'] && is_numeric( $result ) ) {

			$result = $this->format_number( $result, $atts['decimals'] );
		}

		return $result;
	}

	/**
	 * Intelligently format a number
	 *
	 * If you don't define the number of decimal places, then it will use the existing number of decimal places. This is done
	 * in a way that respects the localization of the site.
	 *
	 * If you do define decimals, it uses number_format_i18n()
	 *
	 * @since 1.0
	 *
	 * @see   number_format_i18n()
	 *
	 * @param int|float|string|double $number   A number to format
	 * @param int|string              $decimals Optional. Precision of the number of decimal places. Default '' (use existing number of decimals)
	 *
	 * @return string Converted number in string format.
	 */
	static public function format_number( $number, $decimals = '' ) {
		global $wp_locale;

		$instance = self::$instance;

		if ( '' === $decimals ) {

			$decimal_point = isset( $wp_locale ) ? $wp_locale->number_format['decimal_point'] : '.';

			/**
			 * Calculate the position of the decimal point in the number
			 *
			 * @see http://stackoverflow.com/a/2430144/480856
			 */
			$decimals = strlen( substr( strrchr( $number, $decimal_point ), 1 ) );
		}

		// Force a max precision of decimal places
		$decimal_places = ( $instance->precision_level && $decimals > $instance->precision_level ) ? $instance->precision_level : $decimals;

		$number = number_format_i18n( $number, $decimal_places );

		return $number;
	}

	/**
	 * Validates shortcode supplied default value
	 *
	 * @since 1.0
	 *
	 * @see   GravityMath_Shortcode::do_shortcode
	 *
	 * @param mixed $value Default value setting
	 *
	 * @return bool True: valid default value; False: invalid
	 */
	static public function is_valid_default_value( $value ) {
		return is_numeric( $value ) || $value === "skip";
	}
}
