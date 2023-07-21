<?php
/**
 * Utility class for Redirection.
 *
 * @package wpmu-dev-seo
 */

namespace SmartCrawl\Redirects;

use SmartCrawl\Settings;
use SmartCrawl\Singleton;
use SmartCrawl\String_Utils;

/**
 * Utility class for Redirection.
 */
class Utils {

	use Singleton;

	const DEFAULT_TYPE = 302;

	/**
	 * Default redirection type.
	 *
	 * @return int
	 */
	public function get_default_type() {
		$default_type = Settings::get_setting( 'redirections-code' );

		return empty( $default_type )
			? self::DEFAULT_TYPE
			: $default_type;
	}

	/**
	 * Create redirect item.
	 *
	 * @param string $source Source.
	 * @param string $destination Destination.
	 * @param string $type Type.
	 * @param string $title Label to identity long or similar URLs.
	 * @param array  $options Options.
	 *
	 * @return Item
	 */
	public function create_redirect_item( $source, $destination, $type = null, $title = '', $options = array() ) {
		$redirect_item = ( new Item() )
			->set_destination( $this->prepare_destination( $destination ) )
			->set_type( $this->prepare_type( $type ) )
			->set_title( \smartcrawl_clean( $title ) )
			->set_options( $this->prepare_options( $options ) );

		if ( $redirect_item->is_regex() ) {
			$redirect_item
				->set_source( $source )
				->set_path( 'regex' );
		} else {
			$source_normalized = $this->prepare_source( $source );
			$path_normalized   = $this->source_to_path( $source_normalized );
			$redirect_item
				->set_source( $source_normalized )
				->set_path( $path_normalized );
		}

		return $redirect_item;
	}

	/**
	 * Prepare source.
	 *
	 * @param string $source Source.
	 *
	 * @return string
	 */
	private function prepare_source( $source ) {
		return $this->prepare_url( $source );
	}

	/**
	 * Prepare type.
	 *
	 * @param string $type Type.
	 *
	 * @return int
	 */
	private function prepare_type( $type ) {
		$default_type = $this->get_default_type();
		$type         = empty( $type )
			? $default_type
			: $type;

		return intval( $type );
	}

	/**
	 * Prepare options.
	 *
	 * @param array $options Options.
	 *
	 * @return array
	 */
	private function prepare_options( $options ) {
		return empty( $options ) || ! is_array( $options )
			? array()
			: \smartcrawl_clean( $options );
	}

	/**
	 * Remove scheme from url.
	 *
	 * @param string $url Url.
	 *
	 * @return string
	 */
	private function remove_scheme( $url ) {
		return str_replace( array( 'http://', 'https://' ), '', $url );
	}

	/**
	 * Generate path from source.
	 *
	 * @param string $source Source.
	 *
	 * @return string
	 */
	public function source_to_path( $source ) {
		$path = $this->remove_scheme( $source );

		/**
		 * Below code snippet is used to avoid conflict with WPML.
		 * WPML uses ```add_filter( 'home_url', [ $this, 'home_url_filter' ], - 10, 4 );```
		 * And that filter makes home_url() to be changed based on current language.
		 * So to get unfiltered home_url(), we need to remove the filter and add it back later.
		 */
		$hook_name       = 'home_url';
		$callback_groups = false;

		global $wp_filter;

		if ( isset( $wp_filter[ $hook_name ] ) ) {
			$callback_groups = $wp_filter[ $hook_name ]->callbacks;
			$wp_filter[ $hook_name ]->remove_all_filters();

			if ( ! $wp_filter[ $hook_name ]->has_filters() ) {
				unset( $wp_filter[ $hook_name ] );
			}
		}

		$home_url = $this->remove_scheme( home_url( '/' ) );

		if ( $callback_groups ) {
			$wp_filter[ $hook_name ]            = new \WP_Hook(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$wp_filter[ $hook_name ]->callbacks = $callback_groups;
		}

		if ( strpos( $path, $home_url ) === 0 ) {
			$path = str_replace( $home_url, '/', $path );
		}

		$path = wp_parse_url( $path, PHP_URL_PATH );

		return $this->normalize_path( $path );
	}

	/**
	 * Normalize path.
	 *
	 * @param string $path Path.
	 *
	 * @return string
	 */
	public function normalize_path( $path ) {
		// No slash at the end.
		$path = untrailingslashit( $path );

		// Normalize case.
		$path = String_Utils::lowercase( $path );

		// Encode characters.
		$path = $this->encode_path( $path );

		// Always start with a slash.
		return $this->enforce_starting_slash( $path );
	}

	/**
	 * Encode path.
	 *
	 * @param string $path Path.
	 *
	 * @return string
	 */
	private function encode_path( $path ) {
		$decode = array(
			'/',
			':',
			'[',
			']',
			'@',
			'~',
			',',
			'(',
			')',
			';',
		);

		// URL encode everything - this converts any i10n to the proper encoding.
		$path = rawurlencode( $path );

		// We also converted things we don't want encoding, such as a /. Change these back.
		foreach ( $decode as $char ) {
			$path = str_replace( rawurlencode( $char ), $char, $path );
		}

		return $path;
	}

	/**
	 * Prepare destination from destination url.
	 *
	 * @param string $destination Destination url.
	 *
	 * @return string
	 */
	private function prepare_destination( $destination ) {
		return $this->prepare_url( $destination );
	}

	/**
	 * Add starting slash to string.
	 *
	 * @param string $string String.
	 *
	 * @return string
	 */
	private function enforce_starting_slash( $string ) {
		return '/' . ltrim( $string, '/' );
	}

	/**
	 * Make sure url to be absolute url or starting with slash.
	 *
	 * @param string $url Url.
	 *
	 * @return string
	 */
	private function prepare_url( $url ) {
		// Trim.
		$url = trim( $url );
		// Remove new lines.
		$url = preg_replace( "/[\r\n\t].*?$/s", '', $url );
		// Remove control codes.
		$url = preg_replace( '/[^\PC\s]/u', '', $url );
		// Decode.
		$url = rawurldecode( $url );

		return $this->is_url_absolute( $url )
			? $url
			: $this->enforce_starting_slash( $url );
	}

	/**
	 * Check if it's absolute url.
	 *
	 * @param string $url Url.
	 *
	 * @return bool
	 */
	private function is_url_absolute( $url ) {
		return strpos( $url, 'http://' ) === 0 || strpos( $url, 'https://' ) === 0;
	}
}