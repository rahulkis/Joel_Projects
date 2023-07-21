<?php
/**
 * Upgrade highlight modal.
 *
 * @since 2.6.0
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
			id="upgrade-summary-modal"
			class="sui-modal-content"
			aria-modal="true"
			aria-labelledby="upgrade-summary-modal-title"
	>
		<div class="sui-box">
			<div class="sui-box-header sui-flatten sui-content-center sui-spacing-top--60">
				<?php if ( ! apply_filters( 'wpmudev_branding_hide_branding', false ) ) : ?>
					<figure class="sui-box-banner" aria-hidden="true">
						<img src="<?php echo esc_url( WPHB_DIR_URL . 'admin/assets/image/upgrade-js-summary-bg.png' ); ?>" alt=""
							srcset="<?php echo esc_url( WPHB_DIR_URL . 'admin/assets/image/upgrade-js-summary-bg.png' ); ?> 1x, <?php echo esc_url( WPHB_DIR_URL . 'admin/assets/image/upgrade-js-summary-bg@2x.png' ); ?> 2x">
					</figure>
				<?php endif; ?>

				<button class="sui-button-icon sui-button-float--right" onclick="window.WPHB_Admin.dashboard.hideUpgradeSummary()">
					<span class="sui-icon-close sui-md" aria-hidden="true"></span>
					<span class="sui-screen-reader-text"><?php esc_attr_e( 'Close this modal', 'wphb' ); ?></span>
				</button>

				<h3 id="upgrade-summary-modal-title" class="sui-box-title sui-lg" style="white-space: inherit">
					<?php esc_html_e( 'New: Delay JavaScript Execution', 'wphb' ); ?>
				</h3>
			</div>

			<div class="sui-box-body sui-spacing-top--20 sui-spacing-bottom--20">
				<div class="wphb-upgrade-feature">
					<p class="wphb-upgrade-item-desc" style="text-align: center">
						<?php esc_html_e( 'This feature delays the loading of all JavaScript files and inline scripts until there is user interaction on the page (e.g. mouse movement, keyboard action or screen tap) and improves the following PageSpeed recommendations:', 'wphb' ); ?>
					</p>
				</div>
				<div class="wphb-upgrade-feature">
					<ul class="sui-list">
						<li><span class="sui-icon-check" aria-hidden="true"></span><span class="sui-list-label"><?php esc_html_e( 'Remove unused JavaScript', 'wphb' ); ?></span></li>
						<li><span class="sui-icon-check" aria-hidden="true"></span><span class="sui-list-label"><?php esc_html_e( 'Total blocking time', 'wphb' ); ?></li>
						<li><span class="sui-icon-check" aria-hidden="true"></span><span class="sui-list-label"><?php esc_html_e( 'Minimize main thread work', 'wphb' ); ?></span></li>
						<li><span class="sui-icon-check" aria-hidden="true"></span><span class="sui-list-label"><?php esc_html_e( 'Time to interactive', 'wphb' ); ?></li></span></li>
						<li><span class="sui-icon-check" aria-hidden="true"></span><span class="sui-list-label"><?php esc_html_e( 'Reduce JS execution time', 'wphb' ); ?></li></span></li>
						<li><span class="sui-icon-check" aria-hidden="true"></span><span class="sui-list-label"><?php esc_html_e( 'First contentful paint', 'wphb' ); ?></span></li>
					</ul>

					<?php
					if ( is_multisite() ) {
						$hb_button      = esc_html__( 'Got it', 'wphb' );
						$hb_button_link = '#';
						printf( /* translators: %1$s - p tag, %2$s - strong, %3$s - closing strong, %4$s - closing p tag */
							esc_html__( '%1$sTo enable this feature, go to %2$sAsset Optimization > Extra Optimization%3$s.%4$s', 'wphb' ),
							'<p class="wphb-upgrade-item-desc" style="text-align: center;margin-top: 10px">',
							'<strong>',
							'</strong>',
							'</p>'
						);
					} else {
						$hb_button      = esc_html__( 'Check It Out Now', 'wphb' );
						$hb_button_link = Utils::get_admin_menu_url( 'minification' ) . '&view=tools';
					}
					?>
				</div>
			</div>

			<div class="sui-box-footer sui-flatten sui-content-center">
			<a href="<?php echo esc_url( $hb_button_link ); ?>" class="sui-button sui-button-blue" onclick="window.WPHB_Admin.dashboard.hideUpgradeSummary()">
					<?php echo esc_html( $hb_button ); ?>
			</a>
			</div>
		</div>
	</div>
</div>