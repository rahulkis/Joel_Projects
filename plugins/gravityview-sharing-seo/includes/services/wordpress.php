<?php
/**
 * Add support for sharing content using WordPress social icons
 *
 * @since      2.0.3
 * @package    GravityView_Sharing
 * @subpackage services
 */

class GravityView_Sharing_WordPress extends GravityView_Sharing_Service {

	const SERVICE_ID = 'GravityView_Sharing_WordPress';

	var $_service_name = 'WordPress';

	public function __construct() {

		parent::__construct();

		add_filter( 'gravityview_social/sharing_field_options', array( $this, 'configure_sharing_field_settings' ) );
	}

	/**
	 * Activate only when WordPress Social Icons are available
	 */
	public function is_plugin_active() {

		return function_exists( 'block_core_social_link_services' );
	}

	/**
	 * Configure frontend hooks
	 */
	public function frontend_view_hooks() {

		// If the current page has Views
		if ( gravityview_get_current_views() ) {

			add_action( 'wp_enqueue_scripts', array( $this, 'add_frontend_styles' ), 1100 );
		}

		parent::frontend_view_hooks();
	}

	/**
	 * Configure backend (admin) hooks
	 */
	public function admin_view_hooks() {

		// admin - add scripts - run at 1100 to make sure GravityView_Admin_Views::add_scripts_and_styles() runs first at 999
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts_and_styles' ), 1100 );
		add_filter( 'gravityview_noconflict_scripts', array( $this, 'register_no_conflict' ) );
		add_filter( 'gravityview_noconflict_styles', array( $this, 'register_no_conflict' ) );
	}

	/**
	 * Configure sharing field settings appearing in the modal window
	 *
	 * @options array Field settings
	 *
	 * @return string
	 */
	public function configure_sharing_field_settings( $options ) {

		$labels = array(
			'available_services'      => esc_html__( 'Available Services:', 'gravityview-sharing-seo' ),
			'available_services_hint' => esc_html__( 'Drag and drop individual services between available and enabled services boxed areas', 'gravityview-sharing-seo' ),
			'enabled_services'        => esc_html__( 'Enabled Services:', 'gravityview-sharing-seo' ),
			'text_to_share'           => esc_html__( 'Text To Share:', 'gravityview-sharing-seo' ),
		);

		$services = '';
		foreach ( $this->get_wordpress_social_services() as $id => $service ) {
			$services .= <<<HTML
<div class="wp-social-link-container">
	<span class="wp-social-link wp-block-social-link wp-social-link-{$id}" data-service="{$id}">
		<a class="wp-block-social-link-anchor">{$service['icon']}</a>
	</span>
	<div>{$service['name']}</div>
</div>
HTML;
		}

		$wp_service_template = <<<HTML
	<span class="gv-label">{$labels['available_services']}</span>
	<span class="howto">{$labels['available_services_hint']}</span>
	<div class="gv-social-wordpress-services-selection available">
		{$services}
	</div>
	<span class="gv-label">{$labels['enabled_services']}</span>
	<div class="gv-social-wordpress-services-selection enabled wp-block-social-links is-layout-flex"></div>
HTML;

		$options['wp_service'] = array(
			'type' => 'html',
			'desc' => $wp_service_template,
		);

		$options['wp_service_text_to_share'] = array(
			'type'  => 'text',
			'value' => '',
			'label' => $labels['text_to_share'],
			'class' => 'widefat',
		);

		$options['wp_service_enabled_services'] = array(
			'type'  => 'hidden',
			'value' => '',
		);

		return $options;
	}

	/**
	 * @param \GV\Template_Context $context The context.
	 * @return string|null
	 */
	public function output( $context = null ) {

		if ( ! $this->is_plugin_active() || ! $context instanceof \GV\Template_Context ) {
			return null;
		}

		$field            = $context->field->as_configuration();
		$sharing_service  = rgar( $field, 'sharing_service', '' );
		$text_to_share    = rgar( $field, 'wp_service_text_to_share', $context->view->settings->get( 'single_title' ) );
		$text_to_share    = GravityView_API::replace_variables( $text_to_share, $context->view->form->form, $context->entry->as_entry() );
		$new_window       = (int) rgar( $field, 'new_window', 0 );
		$enabled_services = json_decode( rgar( $field, 'wp_service_enabled_services', '[]' ), true );

		if ( self::SERVICE_ID !== $sharing_service || empty( $enabled_services ) ) {
			return null;
		}

		$available_services = $this->get_wordpress_social_services();

		$entry_permalink = gravityview_social_get_permalink( $context->entry->get_permalink() );

		$services = '';
		foreach ( $enabled_services as $service ) {
			if ( empty( $available_services[ $service ]['url'] ) ) {
				continue;
			}

			$url = str_replace(
				array( '{url}', '{text}' ),
				array( rawurlencode( $entry_permalink ), rawurlencode( $text_to_share ) ),
				$available_services[ $service ]['url']
			);

			$label      = sprintf( esc_html_x( 'Click to share on %s', '%s is replaced with social network name', 'gravityview-sharing-seo' ), $available_services[ $service ]['name'] );
			$new_window = $new_window ? 'target="_blank" rel="noopener noreferrer"' : '';

			// TODO: offload markup to "wp:social-links" block once https://github.com/WordPress/gutenberg/issues/20707 is resolved
			$services .= <<<HTML
<li class="wp-social-link wp-block-social-link wp-social-link-{$service}" data-service="{$service}">
	<a href="{$url}" class="wp-block-social-link-anchor" title="{$label}" {$new_window}>{$available_services[$service]['icon']}<span class="wp-block-social-link-label screen-reader-text">{$available_services[$service]['name']}</span></a>
</li>
HTML;
		}

		return <<<HTML
<ul class="is-layout-flex wp-block-social-links">
	{$services}
</div>
HTML;
	}

	/**
	 * Enqueue frontend styles.
	 *
	 * @return void
	 */

	public function add_frontend_styles() {
		wp_enqueue_style( 'wp-block-library' );

		if ( current_theme_supports( 'wp-block-styles' ) ) {
			wp_enqueue_style( 'wp-block-library-theme' );
		}
	}

	/**
	 * Enqueue admin scripts and styles
	 *
	 * @return void
	 */
	public function add_admin_scripts_and_styles() {

		if( ! gravityview()->request->is_admin( '', 'single' ) ) {
			return;
		}

		// Only run on GV screens.
		if ( ! wp_script_is( 'gravityview_views_scripts', 'registered' ) ) {
			return;
		}

		wp_enqueue_script( 'gravityview_social_wordpress', plugins_url( 'assets/js/gravityview-social-wordpress.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'gravityview_views_scripts' ), \GV\Plugin::$version );
		wp_enqueue_style( 'gravityview_social_wordpress', plugins_url( 'assets/css/gravityview-social-wordpress.css', __FILE__ ), array(), \GV\Plugin::$version );
		wp_enqueue_style( 'wp-block-library' );

		if ( current_theme_supports( 'wp-block-styles' ) ) {
			wp_enqueue_style( 'wp-block-library-theme' );
		}
		wp_localize_script( 'gravityview_social_wordpress', 'gvSocialWordpress', array(
			'service_id' => self::SERVICE_ID,
		) );
	}

	/**
	 * List of available social services
	 *
	 * @return array
	 */
	public function get_wordpress_social_services() {

		$services = block_core_social_link_services();

		$services_share_urls = array(
			'twitter'   => '//twitter.com/intent/tweet?url={url}&text={text}',
			'pinterest' => '//www.pinterest.com/pin/create/button/?url={url}&description={text}',
			'linkedin'  => '//www.linkedin.com/sharing/share-offsite/?url={url}',
			'facebook'  => '//www.facebook.com/sharer.php?u={url}&t={text}',
			'reddit'    => '//reddit.com/submit?url={url}&title={text}',
		);

		foreach ( $services as $id => &$service ) {
			if ( empty( $services_share_urls[ $id ] ) ) {
				unset( $services[ $id ] );
				continue;
			}

			$service['url'] = $services_share_urls[ $id ];
		}

		/**
		 * Customize available WP social services
		 *
		 * @param array $services WP social services
		 *
		 * @return array
		 */
		return apply_filters( 'gravityview_social/wordpress_social_services', $services );
	}

	/**
	 * Add admin script to the no-conflict scripts whitelist
	 *
	 * @param array $allowed Scripts allowed in no-conflict mode
	 *
	 * @return array Scripts allowed in no-conflict mode, plus the search widget script
	 */
	public function register_no_conflict( $allowed ) {

		$allowed[] = 'gravityview_social_wordpress';
		$allowed[] = 'wp-block-library';
		$allowed[] = 'wp-block-library-theme';

		return $allowed;
	}

}

new GravityView_Sharing_WordPress;
