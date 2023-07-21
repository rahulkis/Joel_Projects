<?php

/**
 * Integrate with the WordPress SEO plugin
 *
 * @since 1.0
 */
class GravityView_Social_Meta {

	/**
	 * @var GravityView_Social_Meta
	 */
	static $_instance;

	/**
	 * @var string Minimum WordPress SEO version
	 */
	protected $_min_wpseo_version = '14.1';

	private function __construct() {

		if ( defined( 'WPSEO_VERSION' ) && ! version_compare( WPSEO_VERSION, $this->_min_wpseo_version, ">=" ) ) {
			return;
		}

		$this->add_hooks();
	}

	/**
	 * @return GravityView_Social_Meta
	 */
	static function get_instance() {

		if ( empty( self::$_instance ) ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/**
	 * Add Frontend and Admin hooks
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function add_hooks() {

		add_action( 'admin_enqueue_scripts', array( $this, 'override_gv_tab_style_and_behavior' ) );
		add_action( 'admin_init', array( $this, 'admin_view_hooks' ) );
		add_action( 'wp', array( $this, 'frontend_view_hooks' ) );
	}

	/**
	 * Add admin hooks
	 *
	 * @return void
	 */
	public function admin_view_hooks() {

		add_filter( 'wpseo_metabox_entries_social', array( $this, 'modify_metabox_fields' ), 10, 2 );
		add_filter( 'wpseo_metabox_entries_general', array( $this, 'modify_metabox_fields' ), 10, 2 );
		add_action( 'yoast_free_additional_metabox_sections', array( $this, 'add_gv_tab' ) );
		add_action( 'wpseo_save_metaboxes', array( $this, 'save_metaboxes' ) );
	}

	/**
	 * Set custom style/JS for the GravityView tab
	 *
	 * @return void
	 */
	public function override_gv_tab_style_and_behavior() {

		$style = <<<CSS
#wpseo-meta-tab-gravityview-sharing-seo .gv-icon-astronaut-head {
	font-size: 20px; 
	height: 20px; 
	width: 20px; 
	line-height: 20px; 
	margin-right: 8px;"
}

#wpseo-meta-section-gravityview-sharing-seo .upload { 
	width: 50% !important; 
} 

@media screen and (max-width: 782px) {
	#wpseo-meta-section-gravityview-sharing-seo .upload { 
		width: 90% !important; 
	} 
} 
CSS;

		// Clear upload field only of the target element, not all upload fields
		$script = <<<JS
jQuery( document ).ready( function( $ ) {
	$( window ).on( 'load', function() {

		$( '.wpseo_image_remove_button' ).off().on( 'click', function( e ) {
			e.preventDefault();

			$( this ).prevAll( '.upload' ).val( '' );
		} );
	} );
} );
JS;

		wp_add_inline_style( 'yoast-seo-metabox-css', $style );
		wp_add_inline_script( 'yoast-seo-admin-script', $script );
		wp_add_inline_script( 'yoast-seo-admin-global-script', $script ); // Yoast 14.7+
	}

	/**
	 * Add a GravityView tab with field settings to the Yoast metabox
	 *
	 * @since 2.0
	 *
	 * @param array $tabs_data Metabox tabs data
	 *
	 * @return array $tabs_data Modified metabox abs data
	 */
	public function add_gv_tab( $tabs_data ) {

		global $post;

		if ( empty( $post ) || 'gravityview' !== $post->post_type ) {
			return $tabs_data;
		}

		if ( ! class_exists( 'WPSEO_Metabox' ) || ! class_exists( 'WPSEO_Meta' ) ) {
			return $tabs_data;
		}

		$metabox = new WPSEO_Metabox;

		$meta_fields = $this->get_single_entry_meta_fields();

		$content = '';

		foreach ( $meta_fields as $key => $meta_field ) {

			$content .= $metabox->do_meta_box( $meta_field, $key );
		}

		$content = '<div class="wpseotab">' . $content . '</div>';

		$tabs_data[] = array(
			'name'         => 'gravityview-sharing-seo',
			'link_content' => '<span class="gv-icon-astronaut-head"></span>' . esc_html__( 'Single Entry Settings', 'gravityview-sharing-seo' ),
			'content'      => $content,
		);

		return $tabs_data;
	}

	/**
	 * Saves our meta fields when saving Yoast meta
	 *
	 * @since 2.0
	 *
	 * @param array $metaboxes Existing metaboxes to render
	 *
	 * @return array
	 */
	public function save_metaboxes( $metaboxes = array() ) {

		return array_merge( $this->get_single_entry_meta_fields(), $metaboxes );
	}

	/**
	 * Return an array of modified WPSEO_Meta fields with the keys updated to use with Single Entry contexts
	 *
	 * @return array
	 */
	private function get_single_entry_meta_fields() {

		$choice_meta_fields = array_merge(
			WPSEO_Meta::$meta_fields['social'],
			array(
				'opengraph-title'       =>
					array(
						'type'  => 'text',
						'title' => esc_html__( 'Facebook Title', 'gravityview-sharing-seo' ),
					),
				'opengraph-description' =>
					array(
						'type'  => 'textarea',
						'title' => esc_html__( 'Facebook Description', 'gravityview-sharing-seo' ),
					),
				'opengraph-image'       =>
					array(
						'type'  => 'upload',
						'title' => esc_html__( 'Facebook Image', 'gravityview-sharing-seo' ),
					),
				'twitter-title'         =>
					array(
						'type'  => 'text',
						'title' => esc_html__( 'Twitter Title', 'gravityview-sharing-seo' ),
					),
				'twitter-description'   =>
					array(
						'type'  => 'textarea',
						'title' => esc_html__( 'Twitter Description', 'gravityview-sharing-seo' ),
					),
				'twitter-image'         =>
					array(
						'type'  => 'upload',
						'title' => esc_html__( 'Twitter Image', 'gravityview-sharing-seo' ),
					),
			)
		);

		$choice_meta_fields = array_merge(
			array(
				'title'    => array(
					'type'  => 'text',
					'title' => esc_html__( 'SEO Title', 'gravityview-sharing-seo' ),
				),
				'metadesc' => array(
					'type'  => 'textarea',
					'title' => esc_html__( 'Meta Description', 'gravityview-sharing-seo' ),
				),
			),
			$choice_meta_fields
		);

		$gv_fields = array();

		foreach ( $choice_meta_fields as $key => $meta_field ) {

			if ( ! in_array( $key, $this->get_meta_field_keys_to_copy() ) ) {
				continue;
			}

			$gv_fields[ $key . '-gv-entry' ] = $meta_field;

			$class = sprintf( '%s %s %s',
				'merge-tag-support mt-position-right mt-hide_all_fields ',
				$gv_fields[ $key . '-gv-entry' ]['type'], // This allows us to target specific fields (e.g., align upload input elements on one line)
				! empty( $gv_fields[ $key . '-gv-entry' ]['class'] ) ? $gv_fields[ $key . '-gv-entry' ]['class'] : ''
			);

			$gv_fields[ $key . '-gv-entry' ]['title'] = sprintf( esc_html__( '%s (Single Entry)', 'gravityview-sharing-seo' ), $meta_field['title'] );
			$gv_fields[ $key . '-gv-entry' ]['class'] = $class;
		}

		return $gv_fields;
	}

	private function get_meta_field_keys_to_copy() {

		return array(
			'title',
			'metadesc',
			'opengraph-title',
			'opengraph-description',
			'opengraph-image',
			'twitter-title',
			'twitter-description',
			'twitter-image',
		);
	}

	/**
	 * Copy each setting in the WordPress SEO metabox so there's also a setting for Single Entry context
	 *
	 * Adds merge tags to Single Entry settings
	 *
	 * @param array  $field_defs Field settings
	 * @param string $post_type  Post type - inaccurate; we check it ourselves
	 *
	 * @return array Modified $field_defs, if `gravityview` post type
	 */
	public function modify_metabox_fields( $field_defs, $post_type = '' ) {

		global $post;

		if ( empty( $post ) || 'gravityview' !== $post->post_type ) {
			return $field_defs;
		}

		$help_notice = esc_html__( 'To configure GravityView Single Entry settings, click on the Add-ons tab icon (it looks like a plug).', 'gravityview-sharing-seo' );
		foreach ( $field_defs as $key => $fielddef ) {
			if ( in_array( $key, $this->get_meta_field_keys_to_copy() ) ) {
				$field_defs[ $key ]['title']       = sprintf( esc_html__( '%s (Multiple Entries)', 'gravityview-sharing-seo' ), $field_defs[ $key ]['title'] );
				$field_defs[ $key ]['help']        = ! empty( $field_defs[ $key ]['help'] ) ? sprintf( '%s %s', $field_defs[ $key ]['help'], $help_notice ) : $help_notice;
				$field_defs[ $key ]['help-button'] = '';
			}
		}

		return $field_defs;
	}

	/**
	 * Add filters for the WordPress SEO metadata
	 */
	public function frontend_view_hooks() {

		$this->add_wordpress_seo_hooks();

		$this->add_jetpack_hooks();

	}

	private function add_jetpack_hooks() {

		add_filter( 'jetpack_open_graph_base_tags', array( $this, 'jetpack_opengraph_filter' ), 10, 2 );
	}

	/**
	 * Filter the OpenGraph data generated by Jetpack
	 *
	 * @param array $tags
	 * @param array $image_sizes
	 *
	 * @return array
	 */
	public function jetpack_opengraph_filter( $tags = array(), $image_sizes = array() ) {

		$return = $tags;

		$return['og:title'] = gravityview_social_get_title( $tags['og:title'] );
		$return['og:url']   = gravityview_social_get_permalink( $tags['og:url'] );

		return $return;
	}

	/**
	 * Add hooks for the WordPress SEO plugin social meta
	 */
	private function add_wordpress_seo_hooks() {

		add_filter( 'wpseo_canonical', 'gravityview_social_get_permalink' );
		add_filter( 'wpseo_opengraph_url', 'gravityview_social_get_permalink' );
		add_filter( 'wpseo_add_opengraph_images', array( $this, 'maybe_fix_yoast_single_entry_og_image' ) );

		$filters = array(
			'wpseo_metadesc',
			'wpseo_opengraph_desc',
			'wpseo_opengraph_image',
			'wpseo_opengraph_title',
			'wpseo_opengraph_type',
			'wpseo_title',
			'wpseo_twitter_description',
			'wpseo_twitter_image',
			'wpseo_twitter_title',
		);

		$callback = gravityview()->request->is_entry() ? 'single_entry_filter_meta_value' : 'multiple_entries_filter_meta_value';

		foreach ( $filters as $filter ) {
			add_filter( $filter, array( $this, $callback ) );
		}
	}

	/**
	 * Fix for Yoast 14.7 not firing `wpseo_opengraph_image` filter unless the OG  (https://github.com/Yoast/wordpress-seo/issues/15721)
	 *
	 * @since 3.0
	 *
	 * @return void
	 *
	 * @todo  Remove once the issue is addressed by Yoast
	 */
	public function maybe_fix_yoast_single_entry_og_image( $image_class_instance ) {

		if ( ! gravityview()->request->is_entry() ) {
			return $image_class_instance;
		}

		$post      = get_post();
		$post_meta = get_post_meta( $post->ID );

		$gv_og_image = rgar( $post_meta, '_yoast_wpseo_opengraph-image-gv-entry' ); // Single entry OG image
		$og_image    = rgar( $post_meta, '_yoast_wpseo_opengraph-image' ); // Multiple entries OG image

		if ( empty( $og_image ) && ! empty( $gv_og_image ) ) {

			$image_class_instance->add_image( $gv_og_image[0] );
		}

		return $image_class_instance;
	}

	/**
	 * Replace merge tags in the WordPress SEO settings
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public function process_merge_tags( $value ) {

		// No merge tags; don't waste the time.
		if ( strpos( $value, '{' ) === false ) {
			return $value;
		}

		$single_entry = GravityView_frontend::is_single_entry();

		// No entry
		if ( ! $single_entry ) {
			return $value;
		}

		$entry = GFAPI::get_entry( $single_entry );

		// Getting the entry didn't work!
		if ( is_wp_error( $entry ) ) {
			return $value;
		}

		$form = GFAPI::get_form( $entry['form_id'] );

		$return = GravityView_API::replace_variables( $value, $form, $entry );

		return $return;
	}

	/**
	 * Get the key for the data stored in WordPress SEO based on the current filter
	 * The filters correspond to the filters added in {@see frontend_view_hooks() }
	 *
	 * @see WPSEO_Meta::get_value
	 *
	 * @param string $filter Current filter or action
	 *
	 * @return string
	 */
	private function get_wpseo_meta_filter_key( $filter = '' ) {

		$key = '';
		switch ( $filter ) {

			case 'wpseo_title':
				$key = 'title';
				break;
			case 'wpseo_metadesc':
				$key = 'metadesc';
				break;
			case 'wpseo_twitter_title':
				$key = 'twitter-title';
				break;
			case 'wpseo_twitter_description':
				$key = 'twitter-description';
				break;
			case 'wpseo_twitter_image':
				$key = 'twitter-image';
				break;
			case 'wpseo_opengraph_title':
				$key = 'opengraph-title';
				break;
			case 'wpseo_opengraph_desc':
				$key = 'opengraph-description';
				break;
			case 'wpseo_opengraph_image':
				$key = 'opengraph-image';
				break;
		}

		return $key;
	}

	/**
	 * Process shortcodes so that [gvlogic] can run for context detection
	 *
	 * @since 2.0
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public function multiple_entries_filter_meta_value( $value ) {

		return do_shortcode( $value );
	}

	/**
	 * @since 2.0 Added do_shortcode()
	 *
	 * @param string $value Existing value for the
	 *
	 * @return string
	 */
	public function single_entry_filter_meta_value( $value ) {

		$return = $value;

		$filter = current_action();

		/** @var \GV\View $view */
		$views = gravityview()->views->get();

		if ( $views instanceof \GV\View ) {
			$view = $views;
		} elseif ( $views instanceof \GV\View_Collection ) {

			$entry = gravityview()->request->is_entry();

			// Return the first View that the entry is visible in.
			foreach ( $views as $_view ) {
				if ( \GVCommon::check_entry_display( $entry->as_entry(), $_view ) ) {
					$view = $_view;
					break;
				}
			}
		}

		$view_id = $view ? $view->ID : null;

		// Replace titles with default title format
		if ( preg_match( '/.+_title$/', $filter ) ) {
			$return = gravityview_social_get_title( $return, $view_id, $view );
			$return = $this->process_merge_tags( $return );
		}

		$key = $this->get_wpseo_meta_filter_key( $filter );

		if ( ! empty( $key ) ) {
			$meta_desc = trim( WPSEO_Meta::get_value( $key . '-gv-entry', $view_id ) );

			if ( ! empty( $meta_desc ) ) {
				$return = $this->process_merge_tags( $meta_desc );
			} else {
				$return = $this->process_merge_tags( $return );
			}
		}

		return do_shortcode( $return );
	}
}

GravityView_Social_Meta::get_instance();
