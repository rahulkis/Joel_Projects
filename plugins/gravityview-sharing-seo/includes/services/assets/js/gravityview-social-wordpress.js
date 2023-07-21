/**
 * WordPress service for GravityView's Social Sharing extension
 *
 * @package   GravityView Social Sharing
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2020, Katz Web Services, Inc.
 *
 * @since 2.0.3
 */

jQuery( document ).ready( function( $ ) {

	var service;
	service = {
		id: window.gvSocialWordpress.service_id,

		init: function() {
			$( 'body' ).on( 'change', '.gv-setting-container-sharing_service select', function() {

				$( this ).parents( '.ui-widget-content' ).find( '.gv-setting-container-wp_service' ).toggle( $( this ).val() === service.id );

			} );

			$( 'body' ).on( 'dialogopen', '[data-fieldid="sharing"]', function( e ) {

				var $wpServiceContainer = $( e.target ).find( '.gv-setting-container-wp_service' );

				if ( $wpServiceContainer.hasClass( 'initialized' ) ) {
					return;
				}

				var $serviceSelector = $( e.target ).find( '.gv-setting-container-sharing_service select' );
				var $enabledServicesInput = $( '.gv-setting-container-wp_service_enabled_services input' );

				$wpServiceContainer.toggle( $serviceSelector.val() === service.id );

				try {
					var enabledServices = JSON.parse( $enabledServicesInput.val() );
				} catch ( e ) {
					var enabledServices = [];
				}

				// If any services have been enabled, move them to the enabled services box
				if ( enabledServices.length ) {
					var $availableServiceSelector = $wpServiceContainer.find( '.gv-social-wordpress-services-selection.available' );
					var $enabledServiceSelector = $wpServiceContainer.find( '.gv-social-wordpress-services-selection.enabled' );

					$.each( enabledServices, function( i, service ) {
						$availableServiceSelector.find( '.wp-social-link-' + service ).parent().detach().appendTo( $enabledServiceSelector );
					} );
				}

				// Enable drag and dropping
				$wpServiceContainer.find( '.gv-social-wordpress-services-selection' ).sortable( {
					connectWith: '.gv-social-wordpress-services-selection',
					placeholder: 'gv-social-wordpress-services-selection-highlight',
					stop: function() {
						// When items are dropped, update the hidden input field with a list of enabled services
						enabledServices = [];

						$wpServiceContainer.find( '.gv-social-wordpress-services-selection.enabled .wp-social-link' ).each( function() {
							enabledServices.push( $( this ).attr( 'data-service' ) );
						} );

						$enabledServicesInput.val( JSON.stringify( enabledServices ) );
					},
				} ).disableSelection();

				$wpServiceContainer.addClass( 'initialized' );
			} );
		},
	};

	service.init();
} );
