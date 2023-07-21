<?php

namespace WP_Defender\Component;

use WP_Defender\Component;

/**
 * Use different actions for "What's new" modals.
 *
 * Class Feature_Modal
 * @package WP_Defender\Component
 * @since 2.5.5
 */
class Feature_Modal extends Component {
	/**
	 * Feature data for the last active "What's new" modal.
	*/
	public const FEATURE_SLUG = 'wd_show_feature_global_ip';
	public const FEATURE_VERSION = '3.11.0';

	/**
	 * Get modals that are displayed on the Dashboard page.
	 *
	 * @return array
	 * @since 2.7.0 Use one template for Welcome modal and dynamic data.
	 */
	public function get_dashboard_modals(): array {
		$title = sprintf(
		/* translators: %s: separator */
			__( 'Manage multiple websites IPs effortlessly %s with Global IP Blocker', 'wpdef' ),
			'<br/>'
		);
		$desc = __( 'Say goodbye to the hassle of managing multiple IP addresses for different sites! Our Global IP Blocker feature makes it easier and faster than ever before.', 'wpdef' );
		$wpmudev = wd_di()->get( \WP_Defender\Behavior\WPMUDEV::class );

		return [
			'show_welcome_modal' => $this->display_last_modal( self::FEATURE_SLUG ),
			'welcome_modal' => [
				'title' => $title,
				'desc' => $desc,
				'banner_1x' => defender_asset_url( '/assets/img/modal/welcome-modal.png' ),
				'banner_2x' => defender_asset_url( '/assets/img/modal/welcome-modal@2x.png' ),
				'banner_alt' => __( 'Modal for Global IP lists', 'wpdef' ),
				'button_title' => __( 'Activate', 'wpdef' ),
				'button_title_free' => __( 'Get it now', 'wpdef' ),
				// Additional information.
				'additional_text' => $this->additional_text(),
				'is_disabled_option' => $wpmudev->is_disabled_hub_option(),
			],
		];
	}

	/**
	 * Display modal with the latest changes if:
	 * plugin settings have been reset before -> this is not fresh install,
	 * Whitelabel > Documentation, Tutorials and What’s New Modal > checked Show tab OR Whitelabel is disabled.
	 *
	 * @param string $key
	 *
	 * @return bool
	 * @since 3.11.0 The welcome modal with Global IP has a separate behavior.
	 * Additional cases to display: fresh install only for Pro users, a site is disconnected to the Hub
	 * or the site is connected to the Hub but Global_Ip submodule is deactivated on the site.
	 */
	protected function display_last_modal( $key ): bool {
		$info = defender_white_label_status();
		$wpmudev = wd_di()->get( \WP_Defender\Behavior\WPMUDEV::class );
		$is_hub_option_disabled = $wpmudev->is_disabled_hub_option();
		$is_global_ip_enabled = wd_di()->get( \WP_Defender\Model\Setting\Global_Ip_Lockout::class )->enabled;
		if ( defined( 'WP_DEFENDER_PRO' ) && WP_DEFENDER_PRO ) {
			$allowed_fresh_install = true;
		} else {
			$allowed_fresh_install = (bool) get_site_option( 'wd_nofresh_install' );
		}

		return $allowed_fresh_install && (bool) get_site_option( $key ) && ! $info['hide_doc_link']
			// Additional Global IP cases for v3.11.0.
			&& ( ( ! $is_hub_option_disabled && ! $is_global_ip_enabled ) || $is_hub_option_disabled );
	}

	public function upgrade_site_options() {
		$db_version = get_site_option( 'wd_db_version' );
		$feature_slugs = [
			// Important slugs to display Onboarding, e.g. after the click on Reset settings.
			[
				'slug' => 'wp_defender_shown_activator',
				'vers' => '2.4.0',
			],
			[
				'slug' => 'wp_defender_is_free_activated',
				'vers' => '2.4.0',
			],
			// The latest feature.
			[
				'slug' => self::FEATURE_SLUG,
				'vers' => '3.6.0',
			],
			// The current feature.
			[
				'slug' => self::FEATURE_SLUG,
				'vers' => self::FEATURE_VERSION,
			],
		];
		foreach ( $feature_slugs as $feature ) {
			if ( version_compare( $db_version, $feature['vers'], '==' ) ) {
				// The current feature
				update_site_option( $feature['slug'], true );
			} else {
				// and old one.
				delete_site_option( $feature['slug'] );
			}
		}
	}

	/**
	 * Get additional text.
	 *
	 * @return string
	 */
	private function additional_text(): string {
		$text = '<ul class="list-disc list-inside m-0">';
		$text .= '<li class="sui-no-margin-bottom relative">';
		$text .= '<strong class="text-base text-gray-500 left-10px">';
		$text .= __( 'Global IP Blocker', 'wpdef' );
		$text .= '</strong>';
		$text .= '<span class="sui-description mt-0">';
		$text .= __( 'Simplify the management of IP addresses across multiple websites. It allows website owners to block or unblock IP addresses globally, making it easier to manage access and security for multiple sites from a single platform.', 'wpdef' );
		$text .= '</span>';
		$text .= '</li>';
		$text .= '<li class="sui-no-margin-bottom relative">';
		$text .= '<strong class="text-base text-gray-500 left-10px">';
		$text .= __( 'Auto-sync global IP addresses', 'wpdef' );
		$text .= '</strong>';
		$text .= '<span class="sui-description mt-0">';
		$text .= __( "The auto-sync feature of Global IP Blocker ensures that all changes made to the blocked or unblocked IP addresses are automatically synchronized across all connected websites. This means that you don't have to manually update each site, saving time and ensuring consistency in security measures.", 'wpdef' );
		$text .= '</span>';
		$text .= '</li>';
		$text .= '</ul>';

		return $text;
	}

	/**
	 * Delete welcome modal key.
	 *
	 * @return void
	 */
	public static function delete_modal_key(): void {
		delete_site_option( self::FEATURE_SLUG );
	}
}