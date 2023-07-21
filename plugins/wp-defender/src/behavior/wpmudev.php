<?php

namespace WP_Defender\Behavior;

use Calotes\Component\Behavior;
use WP_Defender\Traits\IO;
use WP_Defender\Traits\Formats;
use WP_Defender\Traits\Defender_Dashboard_Client;
use WP_Defender\Traits\Defender_Hub_Client;
use WP_Defender\Behavior\WPMUDEV_Const_Interface;

/**
 * This class contains everything relate to WPMUDEV.
 * Class WPMUDEV
 * @package WP_Defender\Behavior
 * @since 2.2
 */
class WPMUDEV extends Behavior implements WPMUDEV_Const_Interface {
	use IO, Formats, Defender_Dashboard_Client, Defender_Hub_Client;

	/**
	 * Get membership status.
	 *
	 * @return bool
	 */
	public function is_pro() {
		return $this->get_apikey() !== false;
	}

	/**
	 * Get WPMUDEV API KEY.
	 *
	 * @return bool|string
	 */
	public function get_apikey() {
		if ( ! class_exists( '\WPMUDEV_Dashboard' ) ) {
			return false;
		}

		\WPMUDEV_Dashboard::instance();
		if (
			method_exists( \WPMUDEV_Dashboard::$upgrader, 'user_can_install' )
			&& \WPMUDEV_Dashboard::$upgrader->user_can_install( 1081723, true )
		) {
			return \WPMUDEV_Dashboard::$api->get_key();
		} else {
			return false;
		}
	}

	/**
	 * @since 2.5.5 Use Whitelabel filters instead of calling the whitelabel functions directly.
	 * @return bool
	 */
	public function is_whitelabel_enabled() {
		if ( $this->get_apikey() ) {
			// Use backward compatibility.
			if ( \WPMUDEV_Dashboard::$version > '4.11.1' ) {
				$settings = apply_filters( 'wpmudev_branding', [] );

				return ! empty( $settings );
			} else {
				$site = \WPMUDEV_Dashboard::$site;
				$settings = $site->get_whitelabel_settings();

				return $settings['enabled'];
			}
		}

		return false;
	}

	/**
	 * @return array
	 */
	public function get_remote_access() {
		// Use backward compatibility.
		if ( \WPMUDEV_Dashboard::$version > '4.11.9' ) {
			return \WPMUDEV_Dashboard::$site->get( 'remote_access' );
		} else {
			return \WPMUDEV_Dashboard::$site->get_option( 'remote_access' );
		}
	}

	/**
	 * Show support links if:
	 * plugin version isn't Free,
	 * Whitelabel is disabled.
	 *
	 * @return bool
	 * @since 2.5.5
	 */
	public function show_support_links() {
		if ( $this->get_apikey() ) {
			// Use backward compatibility.
			if ( \WPMUDEV_Dashboard::$version > '4.11.1' ) {
				$settings = apply_filters( 'wpmudev_branding', [] );

				return empty( $settings );
			} else {
				$site = \WPMUDEV_Dashboard::$site;
				$settings = $site->get_whitelabel_settings();

				return ! $settings['enabled'];
			}
		}

		return false;
	}
}