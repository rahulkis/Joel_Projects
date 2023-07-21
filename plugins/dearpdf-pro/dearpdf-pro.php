<?php

// @formatter:off
/**
 * Plugin Name: DearPDF Pro
 * Description: PDF Viewer and PDF Flipbook for WordPress
 * Version: 1.2.61
 *
 * Text Domain: dearpdf
 * Author: DearHive
 * Author URI: http://dearhive.com/
 *
 */
// @formatter:on
// Do not allow direct file access
if ( !defined( 'ABSPATH' ) ) {
    exit( 'Direct script access denied.' );
}

if ( !function_exists( 'dearpdf_fs' ) ) {
    // Create a helper function for easy SDK access.
    function dearpdf_fs()
    {
        global  $dearpdf_fs ;
        
        if ( !isset( $dearpdf_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            /** @noinspection PhpUndefinedFunctionInspection */
            $dearpdf_fs = fs_dynamic_init( array(
                'id'               => '6860',
                'slug'             => 'dearpdf',
                'premium_slug'     => 'dearpdf-pro',
                'type'             => 'plugin',
                'public_key'       => 'pk_9d37994010c171f5593142bfdbcb7',
                'is_premium'       => true,
                'is_premium_only'  => true,
                'has_addons'       => false,
                'has_paid_plans'   => true,
                'is_org_compliant' => false,
                'menu'             => array(
                'slug'    => 'edit.php?post_type=dearpdf',
                'contact' => false,
                'support' => false,
            ),
                'is_live'          => true,
            ) );
        }
        
        return $dearpdf_fs;
    }
    
    // Init Freemius.
    dearpdf_fs();
    // Signal that SDK was initiated.
    do_action( 'dearpdf_fs_loaded' );
}

require_once dirname( __FILE__ ) . '/dearpdf.php';
class DearPDF_Pro extends DearPDF
{
    public function init_settings()
    {
        parent::init_settings();
        $this->post_shortcode_tabs = array(
            'embed'    => array(
            'title'   => __( 'Embed', 'dearpdf' ),
            'content' => '[dearpdf id="{id}" ][/dearpdf]',
        ),
            'lightbox' => array(
            'title'   => __( 'LightBox (Popup)', 'dearpdf' ),
            'content' => 'Button:<br><code>[dearpdf id="{id}" type="button"][/dearpdf]</code><hr>
              Thumb:<br><code>[dearpdf id="{id}" type="thumb"][/dearpdf]</code>',
        ),
        );
        $this->defaults['mobileViewerType'] = array(
            'std'     => 'auto',
            'choices' => array(
            'auto'     => 'Same as Viewer Type',
            'reader'   => 'Vertical Reader',
            'flipbook' => 'Flipbook',
        ),
            'title'   => 'Mobile Viewer Type',
            'desc'    => 'Choose the fallback Mobile Viewer Type. "Same as Viewer Type" will follow individual post setting "Viewer Type".',
        );
        $this->defaults['pdfThumb'] = array(
            'std'            => "",
            'title'          => 'PDF Thumbnail Image',
            'desc'           => '',
            'placeholder'    => 'Select an image',
            'type'           => 'upload',
            'button-tooltip' => 'Select PDF Thumb Image',
            'button-text'    => 'Select Thumb',
        );
        $this->defaults['has3DCover'] = array(
            'std'       => 'true',
            'choices'   => array(
            'global' => 'Default Setting',
            'true'   => 'True',
            'false'  => 'False',
        ),
            'title'     => 'Enable 3D Cover',
            'desc'      => 'Enable 3D Thick cover. Happens only when there are more than 10 pages.',
            'condition' => 'dearpdf_viewerType:is(flipbook),dearpdf_is3D:is(true)',
        );
        $this->defaults['color3DCover'] = array(
            'std'         => "#777",
            'title'       => '3D Book Cover Color',
            'desc'        => 'Color in hexadecimal format eg:<code>#FFF</code> or <code>#666666</code>',
            'placeholder' => 'Example: #777',
            'type'        => 'text',
            'condition'   => 'dearpdf_viewerType:is(flipbook),dearpdf_is3D:is(true)',
        );
        $this->defaults['controlsPosition'] = array(
            'std'     => 'bottom',
            'choices' => array(
            'global' => __( 'Default Setting', 'dearpdf' ),
            'bottom' => __( 'Bottom', 'dearpdf' ),
            'top'    => __( 'Top', 'dearpdf' ),
            'hidden' => __( 'Hidden', 'dearpdf' ),
        ),
            'class'   => '',
            'title'   => 'Controls Position',
            'desc'    => 'Choose where you want to display the controls bar or not display at all.',
        );
        $this->defaults['rangeChunkSize'] = array(
            'std'     => '524288',
            'choices' => array(
            'global'  => __( 'Default Setting', 'dearpdf' ),
            '65536'   => '64KB',
            '131072'  => '128KB',
            '262144'  => '256KB',
            '524288'  => '512KB',
            '1048576' => '1024KB',
        ),
            'class'   => '',
            'title'   => 'PDF Partial Loading Chunk Size',
            'desc'    => 'Choose the size chunk size to be loaded on demand',
        );
        $this->defaults['maxTextureSize'] = array(
            'std'     => '3200',
            'choices' => array(
            'global' => __( 'Default Setting', 'dearpdf' ),
            '2048'   => '2048 px',
            '3200'   => '3200 px',
            '4096'   => '4096 px (caution)',
        ),
            'class'   => '',
            'title'   => 'PDF Page Maximum Render Size',
            'desc'    => 'Choose the maximum page size rendering to support for zoom',
        );
        $this->defaults['pageMode'] = array(
            'std'       => 'auto',
            'choices'   => array(
            'global' => 'Default Setting',
            'auto'   => 'Auto Page Mode',
            'single' => 'Single Page Mode',
            'double' => 'Double Page Mode',
        ),
            'title'     => 'Page Mode',
            'desc'      => 'Choose whether you want single mode or double page mode. Recommended Auto',
            'condition' => 'dearpdf_viewerType:is(flipbook)',
        );
        $this->defaults['singlePageMode'] = array(
            'std'       => 'auto',
            'choices'   => array(
            'global'  => 'Default Setting',
            'auto'    => 'Auto Mode',
            'zoom'    => 'Side by Side Single',
            'booklet' => 'Booklet Mode',
        ),
            'title'     => 'Single Page Mode(Flipbook)',
            'desc'      => 'Choose how the single page will behave. If set to Auto, then in mobiles single page mode will be in Booklet mode.',
            'condition' => 'dearpdf_viewerType:is(flipbook)',
        );
        $this->defaults['pdfVersion'] = array(
            'std'     => 'default',
            'choices' => array(
            'default' => 'Default Version (2.5.207)',
            'latest'  => 'Latest (2.13.216)',
            'beta'    => 'Stable Candidate (2.12.313)',
        ),
            'title'   => 'PDF.js Version',
            'desc'    => 'Choose which version of PDF.js rendering Engine to use.',
        );
        $this->defaults['autoPDFLinktoViewer'] = array(
            'std'     => 'false',
            'choices' => array(
            'true'  => 'True',
            'false' => 'False',
        ),
            'title'   => 'Auto convert PDF links on page to viewers',
            'desc'    => 'If the link is set to open in new tab or download, conversion will not happen.',
        );
        $this->defaults['thumbLayout'] = array(
            'std'     => 'book-title-hover',
            'choices' => array(
            'book-title-hover'  => 'Book Cover with Title on Hover',
            'book-title-fixed'  => 'Book Cover with Title Fixed',
            'book-title-bottom' => 'Book Cover with Title Bottom (Beta)',
            'cover-title'       => 'Flat Cover with Title (Beta)',
            'custom'            => 'Custom Development (Beta)',
        ),
            'title'   => 'Thumb Popup Display',
            'desc'    => 'Select the layout of thumb popup display. Note: Beta can change in future!',
        );
        $this->defaults['attachmentLightbox'] = array(
            'std'     => 'true',
            'choices' => array(
            'true'  => __( 'True', 'dearpdf' ),
            'false' => __( 'False', 'dearpdf' ),
        ),
            'title'   => 'Attachment PDF page auto Lightbox',
            'desc'    => 'When opening attachment page for PDF, display lightbox instead of embedded flipbook.',
        );
        $this->defaults['duration'] = array(
            'std'         => '800',
            'placeholder' => 'Example: 800',
            'type'        => 'number',
            'title'       => 'Flip Duration',
            'desc'        => 'Duration for flip animation.',
            'condition'   => 'dearpdf_viewerType:is(flipbook)',
        );
        $this->defaults['autoOpenOutline'] = array(
            'std'     => 'false',
            'choices' => array(
            'true'  => __( 'True', 'dearpdf' ),
            'false' => __( 'False', 'dearpdf' ),
        ),
            'title'   => 'Auto Open Outline',
            'desc'    => 'Auto open Outline/Table of content sidebar when pdf viewer loads.',
        );
        $this->defaults['autoOpenThumbnail'] = array(
            'std'     => 'false',
            'choices' => array(
            'true'  => __( 'True', 'dearpdf' ),
            'false' => __( 'False', 'dearpdf' ),
        ),
            'title'   => 'Auto Open Thumbnail',
            'desc'    => 'Auto open thumbnail sidebar when pdf viewer loads.',
        );
        $this->defaults['paddingLeft'] = array(
            'std'         => '15',
            'placeholder' => 'Example: 15',
            'type'        => 'number',
            'title'       => 'Padding Left',
            'desc'        => 'Padding on left side of Flipbook.',
            'condition'   => 'dearpdf_viewerType:is(flipbook)',
        );
        $this->defaults['paddingRight'] = array(
            'std'         => '15',
            'placeholder' => 'Example: 15',
            'type'        => 'number',
            'title'       => 'Padding Right',
            'desc'        => 'Padding on right side of Flipbook.',
            'condition'   => 'dearpdf_viewerType:is(flipbook)',
        );
        $this->defaults['paddingTop'] = array(
            'std'         => '20',
            'placeholder' => 'Example: 20',
            'type'        => 'number',
            'title'       => 'Padding Top',
            'desc'        => 'Padding on top side of Flipbook.',
            'condition'   => 'dearpdf_viewerType:is(flipbook)',
        );
        $this->defaults['paddingBottom'] = array(
            'std'         => '20',
            'placeholder' => 'Example: 20',
            'type'        => 'number',
            'title'       => 'Padding Bottom',
            'desc'        => 'Padding on bottom side of Flipbook.',
            'condition'   => 'dearpdf_viewerType:is(flipbook)',
        );
        $this->defaults['moreControls'] = array(
            'std'         => "download,pageMode,startPage,endPage,sound",
            'title'       => 'More Controls - CASE SENSITIVE',
            'desc'        => 'Names of Controls in more Control Bar<br><code>altPrev, pageNumber, altNext, outline, thumbnail, zoomIn, zoomOut, fullScreen,share, more, download, pageMode, startPage, endPage, sound</code>',
            'placeholder' => '',
            'type'        => 'textarea',
        );
        $this->defaults['hideControls'] = array(
            'std'         => "",
            'title'       => 'Hide Controls - CASE SENSITIVE',
            'desc'        => 'Names of Controls to be hidden.. ',
            'placeholder' => '',
            'type'        => 'textarea',
        );
        $this->defaults['sideMenuOverlay'] = array(
            'std'     => 'true',
            'choices' => array(
            'true'  => __( 'True', 'dearpdf' ),
            'false' => __( 'False', 'dearpdf' ),
        ),
            'title'   => 'SideMenu Overlay',
            'desc'    => 'Thumbs and Outline overlay over the viewer(True) or push the viewer and stay separately(False)',
        );
        //    add_filter( 'dearpdf_single_content', array( $this, 'override_tempate' ));
    }
    
    public function init_global()
    {
        parent::init_global();
        
        if ( is_admin() && get_option( 'dearpdf_activated' ) == 'dearpdf' ) {
            delete_option( 'dearpdf_activated' );
            flush_rewrite_rules();
        }
    
    }
    
    public function filter_post_args( $args )
    {
        $args['publicly_queryable'] = true;
        return $args;
    }
    
    public function filter_hook_data( $data )
    {
        $data = array(
            'text'       => array(
            'blank' => "",
        ),
            'viewerType' => $this->get_global_config( 'viewerType' ),
            'is3D'       => $this->get_global_config( 'is3D' ) == "true",
            'pageScale'  => "auto",
        );
        $data['height'] = $this->get_global_config( 'height' );
        $data['mobileViewerType'] = $this->get_global_config( 'mobileViewerType' );
        $data['backgroundColor'] = $this->get_global_config( 'backgroundColor' );
        $data['backgroundImage'] = $this->get_global_config( 'backgroundImage' );
        $data['showDownloadControl'] = $this->get_global_config( 'showDownloadControl' ) == "true";
        $data['sideMenuOverlay'] = $this->get_global_config( 'sideMenuOverlay' ) == "true";
        $data['readDirection'] = $this->get_global_config( 'readDirection' );
        $data['disableRange'] = $this->get_global_config( 'disableRange' ) == "true";
        $data['has3DCover'] = $this->get_global_config( 'has3DCover' ) == "true";
        $data['enableSound'] = $this->get_global_config( 'enableSound' ) == "true";
        $data['color3DCover'] = $this->get_global_config( 'color3DCover' );
        $data['controlsPosition'] = $this->get_global_config( 'controlsPosition' );
        $data['rangeChunkSize'] = $this->get_global_config( 'rangeChunkSize' );
        $data['maxTextureSize'] = $this->get_global_config( 'maxTextureSize' );
        $data['pageMode'] = $this->get_global_config( 'pageMode' );
        $data['singlePageMode'] = $this->get_global_config( 'singlePageMode' );
        $data['pdfVersion'] = $this->get_global_config( 'pdfVersion' );
        $data['autoPDFLinktoViewer'] = $this->get_global_config( 'autoPDFLinktoViewer' ) == "true";
        //    $data['thumbLayout'] = $this->get_global_config( 'thumbLayout' );
        $data['attachmentLightbox'] = $this->get_global_config( 'attachmentLightbox' );
        $data['duration'] = $this->get_global_config( 'duration' );
        $data['paddingLeft'] = $this->get_global_config( 'paddingLeft' );
        $data['paddingRight'] = $this->get_global_config( 'paddingRight' );
        $data['paddingTop'] = $this->get_global_config( 'paddingTop' );
        $data['paddingBottom'] = $this->get_global_config( 'paddingBottom' );
        $data['moreControls'] = $this->get_global_config( 'moreControls' );
        $data['hideControls'] = $this->get_global_config( 'hideControls' );
        //also update in utils.sanitizeOptions
        return $data;
    }
    
    public function filter_save_post( $raw_data, $post_id )
    {
        $thumbURL = $this->process_thumb( $raw_data, $post_id );
        $sanitized_data = $raw_data;
        $sanitized_data['pdfThumb'] = esc_url_raw( $thumbURL );
        return $sanitized_data;
    }
    
    public function init_admin()
    {
        parent::init_admin();
        // TODO: Change the autogenerated stub
        //Create a copy of defaults and remove global value since it is not displayed in Global settings.
        $this->settings_fields = array_merge( array(), $this->defaults );
        foreach ( $this->settings_fields as $key => $value ) {
            if ( isset( $value['choices'] ) && is_array( $value['choices'] ) && isset( $value['choices']['global'] ) ) {
                unset( $this->settings_fields[$key]['choices']['global'] );
            }
        }
        add_filter(
            'attachment_fields_to_edit',
            array( $this, 'attachment_field_content_filter' ),
            11,
            2
        );
    }
    
    public function attachment_field_content_filter( $form_fields, $post )
    {
        
        if ( $post->post_mime_type == "application/pdf" ) {
            $link = get_attachment_link( $post );
            $form_fields['dearpdf_title'] = array(
                'label' => 'DearPDF',
                'input' => 'html',
                'html'  => 'Copy the following link and shortcodes',
            );
            $form_fields['dearpdf_links'] = array(
                'label' => 'PDF Links',
                'input' => 'html',
                'html'  => '<a target="_blank" href="' . $link . '"">Default PDF Viewer</a></br>
                     <a target="_blank" href="' . $link . '?viewertype=reader">Vertical Reader</a></br>
                     <a target="_blank" href="' . $link . '?viewertype=flipbook&is3d=true">3D Flipbook</a></br>
                     <a target="_blank" href="' . $link . '?viewertype=flipbook&is3d=false">2D Flipbook</a></br></br>',
            );
            $form_fields['dearpdf_shortcode'] = array(
                'label' => 'PDF Shortcodes',
                'input' => 'html',
                'html'  => '<code>[dearpdf id="' . $post->ID . '"]</code></br><a target="_blank" href="https://dearpdf.com/docs/shortcode-options/">More Shortcode Options</a>',
            );
        }
        
        return $form_fields;
    }
    
    public function init_front()
    {
        parent::init_front();
        //include the shortcode parser
        add_shortcode( 'dpcss', array( $this, 'shortcode_dearpdfcss' ) );
        self::init_templates();
    }
    
    public function shortcode_dearpdfcss( $raw_attr, $content = '' )
    {
        $post_id = trim( $content );
        $post = get_post( $post_id );
        if ( $post == null ) {
            return "";
        }
        $post_data = $this->get_post_data( $post );
        $post_data['slug'] = $post->post_name;
        if ( isset( $post_data['pdfThumb'] ) ) {
            unset( $post_data['pdfThumb'] );
        }
        $post_data["id"] = $post->ID;
        return "dpcss dpcss_e_" . base64_encode( json_encode( $post_data ) );
    }
    
    //region Templates
    public function init_templates()
    {
        add_filter( 'single_template', array( $this, 'single_template' ) );
        add_filter( 'taxonomy_template', array( $this, 'category_template' ) );
        add_action(
            "dearpdf_single_content",
            array( $this, "single_template_content" ),
            10,
            1
        );
        add_action(
            "dearpdf_category_content",
            array( $this, "category_template_content" ),
            10,
            1
        );
        add_filter(
            "dearpdf_category_post_content",
            array( $this, "category_template_post_content" ),
            10,
            2
        );
        do_action( "after_dearpdf_init_templates" );
    }
    
    public function single_template( $single )
    {
        global  $post ;
        
        if ( $post->post_type === "dearpdf" ) {
            $template = plugin_dir_path( __FILE__ ) . '/assets/templates/single.php';
            if ( file_exists( $template ) ) {
                $single = $template;
            }
        }
        
        return $single;
    }
    
    public function category_template( $category )
    {
        
        if ( is_tax( 'dearpdf_category' ) ) {
            $template = plugin_dir_path( __FILE__ ) . '/assets/templates/category.php';
            if ( file_exists( $template ) ) {
                $category = $template;
            }
        }
        
        return $category;
    }
    
    public function single_template_content()
    {
        global  $post ;
        echo  '<div class="dearpdf-single-content">' ;
        echo  do_shortcode( '[dearpdf id="' . $post->ID . '"][/dearpdf]' ) ;
        echo  '</div>' ;
    }
    
    public function category_template_content()
    {
        $current_term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
        echo  do_shortcode( '[dearpdf posts="' . $current_term->slug . '"][/dearpdf]' ) ;
    }
    
    public function category_template_post_content( $post_content, $post )
    {
        ob_start();
        ?>

    <a class="dearpdf-post" href="<?php 
        echo  get_the_permalink( $post->ID ) ;
        ?>">
      <?php 
        echo  do_shortcode( '[dearpdf id="' . $post->ID . '" type="thumb"][/dearpdf]' ) ;
        ?>
    </a>
    
    <?php 
        return ob_get_clean();
    }
    
    //endregion
    public function action_admin_menu()
    {
        parent::action_admin_menu();
        $this->settings_menu_hook = add_submenu_page(
            'edit.php?post_type=dearpdf',
            __( 'DearPDF Global Settings', 'dearpdf' ),
            __( 'Global Settings', 'dearpdf' ),
            'manage_options',
            $this->plugin_slug . '-settings',
            array( $this, 'global_page_settings' )
        );
        if ( $this->settings_menu_hook ) {
            add_action( 'load-' . $this->settings_menu_hook, array( $this, 'action_settings_update' ) );
        }
    }
    
    /**
     * Creates the UI for Source tab
     *
     * @param object $post The current post object.
     *
     * @since 1.0.0
     *
     */
    public function tab_post_source( $post )
    {
        $this->create_normal_setting( 'source', $post );
        $this->create_normal_setting( 'pdfThumb', $post );
        //PRO Feature
    }
    
    /**
     * Creates the UI for General tab
     *
     * @param object $post The current post object.
     *
     * @since 1.0.0
     *
     */
    public function tab_post_general( $post )
    {
        $this->create_global_setting( 'viewerType', $post, 'global' );
        $this->create_global_setting( 'height', $post, '' );
        $this->create_global_setting( 'backgroundColor', $post, '' );
        $this->create_global_setting( 'backgroundImage', $post, '' );
        $this->create_global_setting( 'showDownloadControl', $post, 'global' );
        $this->create_global_setting( 'controlsPosition', $post, 'global' );
        //PRO Feature
        $this->create_normal_setting( 'autoOpenOutline', $post );
        //PRO Feature
        $this->create_normal_setting( 'autoOpenThumbnail', $post );
        //PRO Feature
    }
    
    /**
     * Creates the UI for flipbook tab
     *
     * @param object $post The current post object.
     *
     * @since 1.0.0
     *
     */
    public function tab_post_flipbook( $post )
    {
        $this->notice_flipbook_tab();
        $this->create_global_setting( 'is3D', $post, 'global' );
        $this->create_global_setting( 'has3DCover', $post, 'global' );
        //PRO Feature
        $this->create_global_setting( 'color3DCover', $post, '' );
        //PRO Feature
        $this->create_global_setting( 'enableSound', $post, 'global' );
        $this->create_global_setting( 'duration', $post, '' );
        $this->create_global_setting( 'readDirection', $post, 'global' );
        $this->create_global_setting( 'pageMode', $post, 'global' );
        //PRO Feature
        $this->create_global_setting( 'singlePageMode', $post, 'global' );
        //PRO Feature
    }
    
    /**
     * Creates the UI for Advanced tab
     *
     * @param object $post The current post object.
     *
     * @since 1.0.0
     *
     */
    public function tab_post_advanced( $post )
    {
        $this->create_global_setting( 'disableRange', $post, 'global' );
        $this->create_global_setting( 'rangeChunkSize', $post, 'global' );
        //PRO Feature
        $this->create_global_setting( 'maxTextureSize', $post, 'global' );
        //PRO Feature
    }
    
    /**
     * Callback to create the settings page
     *
     * @since 1.2
     */
    public function global_page_settings()
    {
        $tabs = array(
            'general'  => __( 'General', 'dearpdf' ),
            'flipbook' => __( 'Flipbook', 'dearpdf' ),
            'advanced' => __( 'Advanced', 'dearpdf' ),
        );
        //create tabs and content
        ?>

    <h2><?php 
        echo  esc_html( get_admin_page_title() ) ;
        ?></h2>
    <form id="dearpdf-settings" method="post" class="dearpdf-settings postbox">
      
      <?php 
        wp_nonce_field( 'dearpdf_settings_nonce', 'dearpdf_settings_nonce' );
        submit_button(
            __( 'Update Settings', 'dearpdf' ),
            'primary',
            'dearpdf_settings_submit',
            false
        );
        ?>

      <div class="dearpdf-tabs">
        <ul class="dearpdf-tabs-list">
          <?php 
        //create tabs
        $active_set = false;
        foreach ( $tabs as $id => $title ) {
            ?>
            <li class="dearpdf-update-hash dearpdf-tab <?php 
            echo  ( $active_set == false ? 'dearpdf-active' : '' ) ;
            ?>">
              <a href="#dearpdf-tab-content-<?php 
            echo  $id ;
            ?>"><?php 
            echo  $title ;
            ?></a></li>
            <?php 
            $active_set = true;
        }
        ?>
        </ul>
        <?php 
        $active_set = false;
        foreach ( $tabs as $id => $title ) {
            ?>
          <div id="dearpdf-tab-content-<?php 
            echo  $id ;
            ?>"
                  class="dearpdf-tab-content <?php 
            echo  ( $active_set == false ? "dearpdf-active" : "" ) ;
            ?>">
            
            <?php 
            $active_set = true;
            //create content for tab
            $function = "tab_settings_" . $id;
            call_user_func( array( $this, $function ) );
            ?>
          </div>
        <?php 
        }
        ?>
      </div>
    </form>
    <?php 
    }
    
    /**
     * Creates the UI for Layout tab
     *
     * @since 1.0.0
     *
     */
    public function tab_settings_general()
    {
        $this->create_setting( 'viewerType' );
        $this->create_setting( 'mobileViewerType' );
        $this->create_setting( 'backgroundColor' );
        $this->create_setting( 'backgroundImage' );
        $this->create_setting( 'height' );
        $this->create_setting( 'showDownloadControl' );
        $this->create_setting( 'sideMenuOverlay' );
        $this->create_setting( 'controlsPosition' );
        $this->create_setting( 'moreControls' );
        $this->create_setting( 'hideControls' );
        //    $this->create_setting( 'thumbLayout' );
    }
    
    /**
     * Creates the UI for flipbook tab
     *
     * @since 1.0.0
     *
     */
    public function tab_settings_flipbook()
    {
        $this->notice_flipbook_tab();
        $this->create_setting( 'is3D' );
        $this->create_setting( 'has3DCover' );
        $this->create_setting( 'color3DCover' );
        $this->create_setting( 'enableSound' );
        $this->create_setting( 'duration' );
        $this->create_setting( 'readDirection' );
        $this->create_setting( 'pageMode' );
        $this->create_setting( 'singlePageMode' );
        $this->create_setting( 'paddingLeft' );
        $this->create_setting( 'paddingRight' );
        $this->create_setting( 'paddingTop' );
        $this->create_setting( 'paddingBottom' );
    }
    
    /**
     * Creates the UI for advance tab
     *
     * @since 1.0.0
     *
     */
    public function tab_settings_advanced()
    {
        $this->create_setting( 'disableRange' );
        $this->create_setting( 'rangeChunkSize' );
        //PRO Feature
        $this->create_setting( 'maxTextureSize' );
        //PRO Feature
        $this->create_setting( 'pdfVersion' );
        //PRO Feature
        $this->create_setting( 'autoPDFLinktoViewer' );
        //PRO Feature
        //    $this->create_setting( 'thumbLayout' );//PRO Feature
        $this->create_setting( 'attachmentLightbox' );
        //PRO Feature
    }
    
    /**
     * Update settings
     *
     * @return null Invalid nonce / no need to save
     * @since 1.2.0.1
     *
     */
    public function action_settings_update()
    {
        // Check form was submitted
        if ( !isset( $_POST['dearpdf_settings_submit'] ) ) {
            return;
        }
        // Check nonce is valid
        if ( !wp_verify_nonce( $_POST['dearpdf_settings_nonce'], 'dearpdf_settings_nonce' ) ) {
            return;
        }
        $data = $_POST['_dearpdf'];
        //todo parse and validate data
        
        if ( is_multisite() ) {
            // Update options
            update_blog_option( null, '_dearpdf_settings', $data );
        } else {
            // Update options
            update_option( '_dearpdf_settings', $data );
        }
        
        // Show confirmation
        add_action( 'admin_notices', array( $this, 'action_settings_updated_notice' ) );
    }
    
    /**
     * display a saved notice
     *
     * @since 1.2.0.1
     */
    public function action_settings_updated_notice()
    {
        ?>
    <div class="updated">
      <p><?php 
        _e( 'Settings updated.', 'dearpdf' );
        ?></p>
    </div>
    <?php 
    }
    
    public function shortcode_dearpdf_wrapper( $attr, $content = '' )
    {
        
        if ( isset( $attr['posts'] ) && trim( $attr['posts'] ) !== '' ) {
            $atts_default = array(
                'posts' => '',
            );
            $atts = shortcode_atts( $atts_default, $attr, 'dearpdf' );
            $limit = ( isset( $attr['limit'] ) ? (int) $attr['limit'] : -1 );
            $ids = array();
            $books = explode( ',', $atts['posts'] );
            foreach ( (array) $books as $query ) {
                $query = trim( $query );
                
                if ( is_numeric( $query ) ) {
                    array_push( $ids, $query );
                } else {
                    $postslist = array();
                    
                    if ( $query == 'all' || $query == '*' ) {
                        $postslist = get_posts( array(
                            'post_type'      => 'dearpdf',
                            'posts_per_page' => -1,
                            'numberposts'    => $limit,
                            'nopaging'       => true,
                            'exclude'        => $ids,
                        ) );
                    } else {
                        
                        if ( $query == "pdfs" ) {
                            $postslist = get_posts( array(
                                'post_type'      => 'attachment',
                                'post_mime_type' => 'application/pdf',
                                'numberposts'    => $limit,
                                'nopaging'       => true,
                                'exclude'        => $ids,
                            ) );
                        } else {
                            $postslist = get_posts( array(
                                'tax_query'      => array( array(
                                'taxonomy' => 'dearpdf_category',
                                'field'    => 'slug',
                                'terms'    => $query,
                            ) ),
                                'post_type'      => 'dearpdf',
                                'posts_per_page' => -1,
                                'numberposts'    => $limit,
                                'nopaging'       => true,
                                'exclude'        => $ids,
                            ) );
                        }
                    
                    }
                    
                    foreach ( $postslist as $post ) {
                        array_push( $ids, $post->ID );
                    }
                }
            
            }
            $html = '<div class="dearpdf-posts">';
            $limitMax = ( $limit == '-1' ? 999 : (int) $limit );
            $limitMax = min( count( $ids ), $limitMax );
            $limit = 0;
            foreach ( $ids as $id ) {
                if ( $limit >= $limitMax ) {
                    break;
                }
                $attr['id'] = $id;
                
                if ( isset( $attr['type'] ) ) {
                    if ( $attr['type'] == "" ) {
                        $attr['type'] = "thumb";
                    }
                } else {
                    $attr['type'] = "thumb";
                }
                
                $attr['last_item'] = $limitMax == $limit + 1;
                $html .= $this->shortcode_dearpdf( $attr, $content, true );
                $limit++;
            }
            return $html . '</div>';
        } else {
            return $this->shortcode_dearpdf( $attr, $content );
        }
    
    }
    
    public function render_shortcode_html( $args )
    {
        $popup = $args['popup'];
        $raw_attr = $args['raw_attr'];
        $post_data = $args['post_data'];
        if ( isset( $raw_attr["thumb-layout"] ) ) {
            $post_data['thumbLayout'] = $raw_attr['thumb-layout'];
        }
        if ( isset( $raw_attr["viewertype"] ) ) {
            $post_data["thumbLayout"] = $raw_attr["thumblayout"];
        }
        
        if ( $popup == "" || $popup == "embed" ) {
            $post_data['lightbox'] = "none";
        } else {
            $post_data['lightbox'] = $popup;
        }
        
        if ( isset( $raw_attr["viewer-type"] ) ) {
            $post_data["viewerType"] = $raw_attr["viewer-type"];
        }
        if ( isset( $raw_attr["viewertype"] ) ) {
            $post_data["viewerType"] = $raw_attr["viewertype"];
        }
        if ( isset( $raw_attr["is3d"] ) ) {
            $post_data["is3D"] = $raw_attr["is3d"] == "true";
        }
        if ( isset( $raw_attr["height"] ) ) {
            $post_data["height"] = $raw_attr["height"];
        }
        if ( isset( $raw_attr["apfl"] ) ) {
            $post_data["apfl"] = $raw_attr["apfl"];
        }
        $class = 'class="dp-element dpcss dpcss_e_' . base64_encode( json_encode( $post_data ) ) . " " . $args['class'] . ' "';
        $title = $args['title'];
        $args['post_data'] = $post_data;
        $args['class'] = $class;
        //default
        
        if ( $popup == "" || $popup == "embed" ) {
            $html = '<div ' . $class . '></div>';
        } else {
            
            if ( $popup == 'link' ) {
                $html = '<a ' . $class . ' href="#">' . $title . '</a>';
            } else {
                $html = '<div ' . $class . '>' . $title . '</div>';
            }
        
        }
        
        //    add_filter( "dearpdf_popup_shortcode_html", "dearpdf_filter", 10, 2 );
        $html = apply_filters( "dearpdf_shortcode_html", $html, $args );
        
        if ( !isset( $raw_attr['last_item'] ) || isset( $raw_attr['last_item'] ) && $raw_attr['last_item'] == true ) {
            $code = 'if(window.DEARPDF && window.DEARPDF.parseElements){window.DEARPDF.parseElements();}';
            $html .= '<script class="dp-shortcode-script" type="application/javascript">' . $code . '</script>';
        }
        
        return $html;
    }
    
    public function is_attachment_lightbox( $post )
    {
        $is_lightbox = $this->get_global_config( 'attachmentLightbox' ) == 'true';
        if ( $is_lightbox ) {
            return $content = do_shortcode( '[dearpdf apfl="true" type="link" id="' . $post->ID . '"][/dearpdf]' );
        }
        return parent::is_attachment_lightbox( $post );
    }
    
    public function action_add_meta_boxes()
    {
        parent::action_add_meta_boxes();
        remove_meta_box( 'dearpdf_post_meta_box_support_us', 'dearpdf', 'normal' );
    }
    
    public function wordpress_rating_box()
    {
    }
    
    public function register_post()
    {
        parent::register_post();
        register_taxonomy( 'dearpdf_category', 'dearpdf', array(
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'rewrite'           => array(
            'slug'         => 'pdf-category',
            'hierarchical' => true,
        ),
        ) );
    }
    
    public function process_thumb( $raw_data, $post_id )
    {
        $thumbURL = $raw_data['pdfThumb'];
        $up_dir = wp_upload_dir();
        $dir = $up_dir['basedir'] . '/dearpdf-thumbs';
        $filename = $dir . '/' . $post_id . '.jpeg';
        $autoThumbURL = $up_dir['baseurl'] . '/dearpdf-thumbs/' . $post_id . '.jpeg';
        //save base64 to file
        
        if ( !empty($thumbURL) ) {
            
            if ( substr( $thumbURL, 0, 22 ) === "data:image/jpeg;base64" ) {
                $img = str_replace( 'data:image/jpeg;base64,', '', $thumbURL );
                $thumbURL = "";
                $img = str_replace( ' ', '+', $img );
                $decoded = base64_decode( $img );
                if ( !file_exists( $dir ) ) {
                    mkdir( $dir, 0777, true );
                }
                file_put_contents( $filename, $decoded );
                $thumbURL = $autoThumbURL;
            } else {
                if ( file_exists( $filename ) && $thumbURL != $autoThumbURL ) {
                    unlink( $filename );
                }
            }
            
            //        set_transient("my_save_post_errors_{$post_id}", "ThumbURL: " . $thumbURL . "\nAutoUrl: " . $autoThumbURL, 10);
        }
        
        return $thumbURL;
    }
    
    /**
     * Loads all script and style sheets for frontend into scope.
     *
     * @since 1.0.0
     */
    public function action_init_front_scripts()
    {
        parent::action_init_front_scripts();
        //register scripts
        wp_deregister_script( $this->plugin_slug . '-script' );
        wp_register_script(
            $this->plugin_slug . '-script',
            plugins_url( 'assets/js/dearpdf-pro.min.js', __FILE__ ),
            array( "jquery" ),
            $this->version,
            true
        );
    }
    
    public function create_meta_boxes_support_us( $post )
    {
        //Exists to hide default setup
    }
    
    /**
     * Helper method for retrieving config values.
     *
     * @param string $key The config key to retrieve.
     *
     * @return string Key value on success, empty string on failure.
     * @since 1.2.6
     *
     */
    public function get_global_config( $key )
    {
        $values = ( is_multisite() ? get_blog_option( null, '_dearpdf_settings', true ) : get_option( '_dearpdf_settings', true ) );
        $value = ( isset( $values[$key] ) ? $values[$key] : '' );
        $value = $this->handle_depreciated_values( $key, $value );
        $default = $this->get_default( $key );
        /* set standard value */
        if ( $default !== null ) {
            $value = $this->filter_std_value( $value, $default );
        }
        return $value;
    }
    
    public function handle_depreciated_values( $key, $value )
    {
        switch ( $key ) {
            case "pdfVersion":
                if ( $value == "2.10.377-legacy-dist" ) {
                    $value = "beta";
                }
                break;
            default:
                break;
        }
        return $value;
    }

}
//Load the DearPDF Plugin Class
$dearpdf = DearPDF_Pro::get_instance( DearPDF_Pro::class );
function dearpdf_pro_plugin_activated()
{
    add_option( 'dearpdf_activated', 'dearpdf' );
    deactivate_plugins( 'dearpdf-lite/dearpdf-lite.php' );
}

function dearpdf_pro_plugin_deactivated()
{
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'dearpdf_pro_plugin_activated' );
register_deactivation_hook( __FILE__, 'dearpdf_pro_plugin_deactivated' );
/*Avoid PHP closing tag to prevent "Headers already sent"*/