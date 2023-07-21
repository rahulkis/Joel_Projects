<?php
/**
 * JS delay modal for non PRO member.
 *
 * @since 3.5.0
 * @package Hummingbird
 */

use Hummingbird\Core\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="sui-modal sui-modal-md">
	<div
			role="dialog"
			id="delay-js-non-pro-member-modal"
			class="sui-modal-content"
			aria-modal="true"
			aria-labelledby="delay-js-non-pro-member-modal-title"
			data-modal-size="lg"
	>
		<div class="sui-box">
			<div class="sui-box-header sui-flatten sui-content-center sui-spacing-top--60">
				<?php if ( ! apply_filters( 'wpmudev_branding_hide_branding', false ) ) : ?>
					<figure class="sui-box-banner" aria-hidden="true">
						<img src="<?php echo esc_url( WPHB_DIR_URL . 'admin/assets/image/upgrade-js-summary-bg.png' ); ?>" alt=""
							srcset="<?php echo esc_url( WPHB_DIR_URL . 'admin/assets/image/upgrade-js-summary-bg.png' ); ?> 1x, <?php echo esc_url( WPHB_DIR_URL . 'admin/assets/image/upgrade-js-summary-bg@2x.png' ); ?> 2x">
					</figure>
				<?php endif; ?>

				<button class="sui-button-icon sui-button-float--right" id="delay-js-non-pro-member-dismiss-button" data-action="closed" data-location="dash_widget" onclick="WPHB_Admin.minification.hbTrackDelayMPEvent( this )">
					<span class="sui-icon-close sui-md" aria-hidden="true"></span>
					<span class="sui-screen-reader-text"><?php esc_attr_e( 'Close this modal', 'wphb' ); ?></span>
				</button>

				<h3 id="delay-js-non-pro-member-modal-title" class="sui-box-title sui-lg" style="white-space: inherit">
					<?php esc_html_e( 'Activate Delay JavaScript Execution', 'wphb' ); ?>
				</h3>
			</div>

			<div class="sui-box-body sui-content-center sui-spacing-top--10">
				<p class="sui-description" style="text-align: center">
					<?php esc_html_e( 'Ready for faster page loading, improved web vitals, and perfect page speed scores? Upgrade today to unlock Delay JS Execution and a host of other powerful and free WPMU DEV tools.', 'wphb' ); ?>
				</p>
				<p style="margin-bottom: 10px; margin-top: 25px;">
					<a
						style="background: #286ef1;"
						id="delay-js-non-pro-member-try-pro"
						data-action="cta_clicked"
						data-location="dash_widget"
						target="_blank"
						href="<?php echo esc_url( Utils::get_link( 'plugin', 'hummingbird_delay_js_ao_summary' ) ); ?>"
						class="sui-button margin-top-10"
						onclick="WPHB_Admin.minification.hbTrackDelayMPEvent( this )"
					>
						<?php esc_html_e( 'UPGRADE NOW', 'wphb' ); ?>
					</a>
				</p>
				<p class="sui-description">
					<?php esc_html_e( 'Already a member?', 'wphb' ); ?>
					<a
						style="color: #286ef1;"
						class="wphb-already-member-connect-site"
						id="delay-js-non-pro-member-connect"
						href="<?php echo esc_url( Utils::get_link( 'connect-url', 'hummingbird_delay_js_existing' ) ); ?>"
						data-action="<?php echo Utils::is_dash_plugin_active_and_disconnected() ? esc_attr( 'connect_dash' ) : esc_attr( 'connect_site' ); ?>"
						data-location="dash_widget"
						onclick="WPHB_Admin.minification.hbTrackDelayMPEvent( this )"
					>
						<?php esc_html_e( 'Connect site', 'wphb' ); ?>
					</a>
				</p>
			</div>
		</div>
	</div>
</div>