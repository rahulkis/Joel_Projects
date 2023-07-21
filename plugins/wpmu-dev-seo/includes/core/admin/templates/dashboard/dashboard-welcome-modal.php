<?php
/**
 * Dashboard Welcome Modal.
 *
 * @package SmartCrawl
 */

use SmartCrawl\Settings;

$modal_id = 'wds-welcome-modal';

$options = Settings::get_specific_options( 'wds_settings_options' );
?>

<div class="sui-modal sui-modal-md">
	<div
		role="dialog"
		id="<?php echo esc_attr( $modal_id ); ?>"
		class="sui-modal-content <?php echo esc_attr( $modal_id ); ?>-dialog"
		aria-modal="true"
		aria-labelledby="<?php echo esc_attr( $modal_id ); ?>-dialog-title"
		aria-describedby="<?php echo esc_attr( $modal_id ); ?>-dialog-description">

		<div class="sui-box" role="document">
			<div class="sui-box-header sui-flatten sui-content-center sui-spacing-top--40">
				<div class="sui-box-banner" role="banner" aria-hidden="true">
					<img src="<?php echo esc_attr( SMARTCRAWL_PLUGIN_URL ); ?>assets/images/upgrade-welcome-header.svg" alt="<?php esc_html_e( 'Help us improve SmartCrawl', 'wds' ); ?>"/>
				</div>
				<button
					class="sui-button-icon sui-button-float--right" data-modal-close
					id="<?php echo esc_attr( $modal_id ); ?>-close-button"
					type="button"
				>
					<span class="sui-icon-close sui-md" aria-hidden="true"></span>
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog window', 'wds' ); ?></span>
				</button>
				<h3 class="sui-box-title sui-lg" id="<?php echo esc_attr( $modal_id ); ?>-dialog-title">
					<?php esc_html_e( 'Help us improve SmartCrawl', 'wds' ); ?>
				</h3>

				<div class="sui-box-body">
					<p class="sui-description" id="<?php echo esc_attr( $modal_id ); ?>-dialog-description">
						<?php
						esc_html_e(
							'Hey there! We\'re always looking for ways to improve SmartCrawl, and we need your help. Would you be willing to allow us to collect anonymous and non-sensitive usage data? This data will help us understand how you use SmartCrawl, so we can make it even better.',
							'wds'
						);
						?>
						<br/><br/>
						<?php
						echo sprintf(
							/* translators: 1, 2: opening/closing anchor tag */
							esc_html__(
								'Your data will be completely anonymous and will never be used to identify you. We promise to use it only to improve SmartCrawl. Learn more about usage tracking %1$shere%2$s.',
								'wds'
							),
							'<a href="https://wpmudev.com/docs/privacy/our-plugins/#usage-tracking-sc" target="_blank">',
							'</a>'
						);
						?>
					</p>

					<div class="wds-modal-settings">
						<?php
						$this->render_view(
							'toggle-item',
							array(
								'field_name' => 'usage-tracking-enable',
								'item_label' => __( 'Allow usage data collection', 'wds' ),
								'checked'    => isset( $options['usage_tracking'] ) && $options['usage_tracking'],
							)
						);
						?>
					</div>
					<button
						id="<?php echo esc_attr( $modal_id ); ?>-get-started"
						type="button"
						class="sui-button wds-disabled-during-request">
						<span class="sui-loading-text">
							<?php esc_html_e( 'Save', 'wds' ); ?>
						</span>
						<span class="sui-icon-loader sui-loading" aria-hidden="true"></span>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>