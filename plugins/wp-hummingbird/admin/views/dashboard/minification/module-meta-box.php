<?php
/**
 * Asset optimization meta box on dashboard page.
 *
 * @package Hummingbird
 *
 * @var float  $compressed_size          Overall compressed files size in Kb.
 * @var float  $compressed_size_scripts  Amount of space saved by compressing JavaScript.
 * @var float  $compressed_size_styles   Amount of space saved by compressing CSS.
 * @var int    $enqueued_files           Number of enqueued files.
 * @var float  $original_size            Overall original file size in Kb.
 * @var float  $percentage               Percentage saved.
 * @var bool   $is_member                Is user a Pro Member.
 * @var bool   $delay_js                 Delay JS status.
 * @var string $delayupsell              Upsell url for delay JS.
 * @var string $ao_page_url              AO extra optimization page URL.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<p class="sui-margin-bottom"><?php esc_html_e( 'Compress, combine and position your assets to dramatically improve your page load speed.', 'wphb' ); ?></p>

<ul class="sui-list sui-margin-bottom">
	<li>
		<span class="sui-list-label"><?php esc_html_e( 'Enqueued files', 'wphb' ); ?></span>
		<span class="sui-list-detail"><?php echo absint( $enqueued_files ); ?></span>
	</li>
	<li>
		<span class="sui-list-label"><?php esc_html_e( 'Total file size reductions', 'wphb' ); ?></span>
		<span class="sui-list-detail">
			<div class="wphb-pills-group">
				<span class="wphb-pills with-arrow right grey"><?php echo esc_html( $original_size ); ?>KB</span>
				<span class="wphb-pills"><?php echo esc_html( $compressed_size ); ?>KB</span>
			</div>
		</span>
	</li>
	<li>
		<span class="sui-list-label"><?php esc_html_e( 'Total % reductions', 'wphb' ); ?></span>
		<span class="sui-list-detail"><?php echo esc_html( $percentage ); ?>%</span>
	</li>
	</li>
	<li>
		<span class="sui-list-label">
			<span class="wphb-filename-extension wphb-filename-extension-js"><?php esc_html_e( 'JS', 'wphb' ); ?></span>
			<span class="wphb-filename-extension-label"><?php esc_html_e( 'JavaScript', 'wphb' ); ?></span>
		</span>
		<span class="sui-list-detail"><?php echo esc_html( $compressed_size_scripts ); ?>KB</span>
	</li>
	</li>
	<li>
		<span class="sui-list-label">
			<span class="wphb-filename-extension wphb-filename-extension-css"><?php esc_html_e( 'CSS', 'wphb' ); ?></span>
			<span class="wphb-filename-extension-label"><?php esc_html_e( 'CSS', 'wphb' ); ?></span>
		</span>
		<span class="sui-list-detail"><?php echo esc_html( $compressed_size_styles ); ?>KB</span>
	</li>
</ul>
<div class="dashboard-delay-highlight-sui-box">
	<div class="dashboard-delay-highlight">
		<h4 class="sui-no-margin-bottom">
			<?php esc_html_e( 'Extra Optimization', 'wphb' ); ?>
			<span class="sui-tag sui-tag-beta"><?php esc_html_e( 'New', 'wphb' ); ?></span>
		</h4>
		<div class="sui-no-margin-bottom">
			<p>
				<?php esc_html_e( 'Enhanced features for peak site performance optimization.', 'wphb' ); ?>
			</p>
		</div>
		<ul class="sui-list sui-no-margin-bottom" style="margin-top: 20px;">
			<li>
				<span class="sui-list-label">
					<span><?php esc_html_e( 'Delay JavaScript Execution', 'wphb' ); ?></span>
					<?php if ( ! $is_member ) { ?>
						<span class="sui-tag sui-tag-pro"><?php esc_html_e( 'Pro', 'wphb' ); ?></span>
					<?php } ?>
				</span>
				<span class="sui-list-detail">
					<?php if ( $is_member ) : ?>
						<label for="view_delay_dashboard" class="sui-toggle">
							<input type="checkbox" name="delay_js" id="view_delay_dashboard" aria-labelledby="view_delay_dashboard-label" <?php checked( $delay_js ); ?>>
							<span class="sui-toggle-slider" aria-hidden="true"></span>
						</label>
					<?php else : ?>
						<label for="non_logged_in_delay_js" class="sui-toggle">
							<input type="checkbox" data-location="dash_widget" data-url="<?php echo esc_url( $delayupsell ); ?>" name="non_logged_in_delay_js" id="non_logged_in_delay_js">
							<span class="sui-toggle-slider" aria-hidden="true"></span>
						</label>
					<?php endif; ?>
				</span>
			</li>
			<li>
				<span class="sui-list-label">
					<span><?php esc_html_e( 'Generate Critical CSS', 'wphb' ); ?></span>
					<span class="sui-tag sui-tag-blue sui-tag-sm"><?php esc_html_e( 'Coming Soon', 'wphb' ); ?></span>
				</span>
				<span class="sui-list-detail">
					<label for="critical_css_coming_soon" class="sui-toggle" onclick="wphbMixPanel.trackCriticalCSSUpsell( {'Modal Action': 'na', 'Location': 'dash_widget'} )">
						<input type="checkbox" id="critical_css_coming_soon" disabled="disabled">
						<span class="sui-toggle-slider" aria-hidden="true"></span>
					</label>
				</span>
			</li>
		</ul>
	</div>
</div>