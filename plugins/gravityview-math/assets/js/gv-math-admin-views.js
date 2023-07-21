/**
 * Additional functionality for admin Views
 *
 * @package   GravityView Math
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2021, Katz Web Services, Inc.
 *
 * @since 2.0
 *
 * globals jQuery
 */
jQuery(document).ready(function ($) {
    $('body').on('dialogopen', '.gv-fields', function (e) {
        var $parent = $(e.target);
        var $calculation = $parent.find('.gv-setting-container-gv_math_footer_calculation');
        var $calculationType = $parent.find('.gv-setting-container-gv_math_footer_calculation_type');
        var $calculationCustomShortcode = $parent.find('.gv-setting-container-gv_math_footer_calculation_custom_shortcode');
        var settingsInitialized = function () {
            return $parent.hasClass('gv-math-init');
        };
        var initializeSettings = function () {
            $parent.addClass('gv-math-init');
        };

        // Enable footer calculations only for Table and DataTables View types
        if (!$('#gravityview_directory_template').val().match(/_table/)) {
            $calculation.nextAll('[class*="gv-setting-container-gv_math_footer_calculation_"]').addBack().hide();
            return;
        }

        if (settingsInitialized()) {
            return;
        }

        $calculation.nextAll('[class*="gv-setting-container-gv_math_footer_calculation_"]').wrapAll( '<fieldset class="gv-math-container" />');

        $calculation.on('click', function () {
            var calculationEnabled = $(this).find('input[type=checkbox]').is(':checked');
            var animationSpeed = settingsInitialized() ? 'fast' : 0;

            // First parent is the UI dialog. Second is h5.gv-fields
            var $icon = $parent.parent().parent().find('.gv-indicator-icon.icon-footer-calculation' );

            if ( calculationEnabled ) {
                $parent.find( '.gv-math-container' ).fadeIn( animationSpeed );
                $icon.removeClass( 'hide-if-js' );
            } else {
                $parent.find( '.gv-math-container' ).fadeOut( animationSpeed );
                $icon.addClass( 'hide-if-js' );
            }
        }).trigger('click');

        $calculationType.on('change', function () {
            var selectedOption = $(this).find('select').val();

            $calculationCustomShortcode.toggle(selectedOption === 'custom');
        }).trigger('change');

        if (!settingsInitialized()) {
            initializeSettings();
        }
    });
});
