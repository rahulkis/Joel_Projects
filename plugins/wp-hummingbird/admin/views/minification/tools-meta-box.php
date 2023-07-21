<?php
/**
 * Tools meta box.
 *
 * @since 1.8
 * @package Hummingbird
 *
 * @var string $css               Above the fold CSS.
 * @var bool   $is_member         Is user a Pro Member.
 * @var bool   $delay_js          Delay JS status.
 * @var string $delay_js_timeout  Delay JS Timeout.
 * @var string $delay_js_excludes Delay JS Exclusion lists.
 */

use Hummingbird\Core\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_site_delay_js_enabled = $delay_js && $is_member;
?>

<div class="sui-box-settings-row">
	<div class="sui-box-settings-col-1">
			<span class="sui-list-label"><strong><?php esc_html_e( 'Delay JavaScript', 'wphb' ); ?></strong>
				<?php if ( ! $is_member ) { ?>
					<span class="sui-tag sui-tag-pro"><?php esc_html_e( 'Pro', 'wphb' ); ?></span>
				<?php } ?>
            </span>
			<span class="sui-description">
				<?php esc_html_e( 'Improve performance by delaying the loading of JavaScript files until user interaction (e.g. scroll, click).', 'wphb' ); ?>
			</span>
	</div>

	<div class="sui-box-settings-col-2">
		<div class="sui-form-field">
			<?php if ( $is_member ) : ?>
				<label for="view_delay_js" class="sui-toggle">
					<input type="checkbox" name="delay_js" id="view_delay_js" aria-labelledby="view_delay_js-label" <?php checked( $is_site_delay_js_enabled ); ?>>
					<span class="sui-toggle-slider" aria-hidden="true"></span>
					<span id="view_delay_js-label" class="sui-toggle-label">
						<?php esc_html_e( 'Enable Delay JavaScript', 'wphb' ); ?>
					</span>
				</label>
			<?php else : ?>
				<label for="non_logged_in_delay_js" class="sui-toggle">
					<input type="checkbox" data-location="eo_settings" data-url="<?php echo esc_url( Utils::get_link( 'plugin', 'hummingbird_delay_js_ao_extra' ) ); ?>" name="non_logged_in_delay_js" id="non_logged_in_delay_js">
					<span class="sui-toggle-slider" aria-hidden="true"></span>
					<span id="non_logged_in_delay_js-label" class="sui-toggle-label">
						<?php esc_html_e( 'Delay JavaScript Execution', 'wphb' ); ?>
					</span>
					<span class="sui-description">
					<?php esc_html_e( 'Upgrade to Pro for instant access and fully optimized JS.', 'wphb' ); ?>
					</span>
				</label>
			<?php endif; ?>
		</div>
		<?php
		$delay_js_exclude_classes = array( 'sui-description', 'sui-toggle-description' );

		if ( ! $is_site_delay_js_enabled ) {
			$delay_js_exclude_classes[] = 'sui-hidden';
		}
		?>
		<span class="<?php echo implode( ' ', $delay_js_exclude_classes ); ?>" style="margin-top: 10px" id="delay_js_file_exclude">

			<?php
			$this->admin_notices->show_inline(
				__( 'Note: Enabling JavaScript Execution will automatically disable the <b>Combine Compression</b> option to ensure scripts are loaded in the correct order.', 'wphb' ),
				'warning'
			);
			?>
			<label class="sui-label" for="delay_js_exclude" style="margin-top: 15px">
				<?php esc_html_e( 'Timeout', 'wphb' ); ?>
			</label>
			<span class="sui-description sui-toggle-description">
				<?php esc_html_e( 'Set a timeout in seconds that the scripts will be loaded if no user interaction has been detected.', 'wphb' ); ?>
			</span>
			<select name="delay_js_timeout" id="delay_js_timeout">
				<?php
				$delay_js_timeout_options = array(
					5  => __( '5 seconds', 'wphb' ),
					10 => __( '10 seconds', 'wphb' ),
					15 => __( '15 seconds', 'wphb' ),
					20 => __( '20 seconds (Recommended minimum)', 'wphb' ),
					25 => __( '25 seconds', 'wphb' ),
					30 => __( '30 seconds', 'wphb' ),
				);

				$selected_time = $delay_js_timeout ? $delay_js_timeout : 20;

				?>
				<?php foreach ( $delay_js_timeout_options as $dts_time => $dvalue ) : ?>
					<option value="<?php echo esc_attr( $dts_time ); ?>" <?php selected( $dts_time, $selected_time ); ?>>
						<?php echo esc_html( ucfirst( $dvalue ) ); ?>
					</option>
				<?php endforeach; ?>
			</select>

			<label class="sui-label" for="delay_js_exclude" style="margin-top: 15px">
				<?php esc_html_e( 'Excluded JavaScript Files ', 'wphb' ); ?>
			</label>
			<textarea class="sui-form-control" id="delay_js_exclude" name="delay_js_exclude" placeholder="/wp-content/themes/some-theme/jsfile.js
jsfile
script id"><?php echo esc_html( $delay_js_excludes ); ?></textarea>
			<?php
			printf( /* translators: %1$s - jsfile, %2$s - jsfile with url, %3$s - script id */
				esc_html__( 'Specify the URLs or keywords that should be excluded from delaying execution (one per line). E.g. %1$s or %2$s or %3$s', 'wphb' ),
				'<b>jsfile</b>',
				'<b>/wp-content/themes/some-theme/jsfile.js</b>',
				'<b>script id</b>'
			);
			?>
		</span>
	</div>
</div>

<div class="sui-box-settings-row">
	<div class="sui-box-settings-col-1">
			<span class="sui-list-label"><strong><?php esc_html_e( 'Critical CSS', 'wphb' ); ?></strong>
				<span class="sui-tag sui-tag-blue sui-tag-sm"><?php esc_html_e( 'Coming Soon', 'wphb' ); ?></span>
			</span>
			<span class="sui-description">
				<?php esc_html_e( 'Drastically reduce your page load time and eliminate render-blocking resources by automatically generating the critical CSS required to load your above-the-fold content.', 'wphb' ); ?>
			</span>
	</div>

	<div class="sui-box-settings-col-2">
		<div class="sui-form-field">
			<label for="critical_css_coming_soon" class="sui-toggle">
				<input type="checkbox" id="critical_css_coming_soon" disabled="disabled">
				<span class="sui-toggle-slider" aria-hidden="true" onclick="wphbMixPanel.trackCriticalCSSUpsell( {'Modal Action': 'na', 'Location': 'eo_settings'} )"></span>
				<span id="critical_css_coming_soon-label" class="sui-toggle-label">
					<?php esc_html_e( 'Generate Critical CSS', 'wphb' ); ?>
				</span>
				<span id="tracking-description" class="sui-description">
					<?php esc_html_e( 'Another speed boost is coming! Even faster pages, better user experience, and improved SEO. You will love it.', 'wphb' ); ?>
				</span>
			</label>
		</div>
	</div>
</div>

<div class="sui-box-settings-row">
	<div class="sui-box-settings-col-1">
		<strong><?php esc_html_e( 'CSS above the fold', 'wphb' ); ?></strong>
		<span class="sui-description">
			<?php
			esc_html_e(
				'Drastically reduce your page load time by moving all of your stylesheets
			to the footer to force them to load after your content.',
				'wphb'
			);
			?>
			<br><br>
			<?php
			esc_html_e(
				'This will result in the content loading quickly, with the styling
			followed shortly after.',
				'wphb'
			);
			?>
		</span>
	</div>
	<div class="sui-box-settings-col-2">
		<ol class="sui-description">
			<li>
				<?php esc_html_e( 'Add critical layout and styling CSS here. We will insert into <style> tags in your <head> section of each page.', 'wphb' ); ?>
			</li>
			<li>
				<?php esc_html_e( 'Next, switch to the manual mode in asset optimization and move all of your CSS files to the footer area.', 'wphb' ); ?>
			</li>
		</ol>

		<span class="sui-description">
			<?php esc_html_e( 'CSS to insert into your <head> area', 'wphb' ); ?>
		</span>
		<textarea class="sui-form-control" name="critical_css" placeholder="<?php esc_attr_e( 'Add CSS here', 'wphb' ); ?>"><?php echo esc_html( $css ); ?></textarea>
	</div>
</div>