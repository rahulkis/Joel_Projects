<?php

/**
 * A class that handles loading chart modules and custom
 * fields if the builder is installed and activated.
 */
class Beaver_Chart_Modules_Init {

    /**
     * Initializes the class once all plugins have loaded.
     */
    static public function init() {
        add_action('plugins_loaded', __CLASS__ . '::beaver_chart_setup_hooks');
    }

    /**
     * Setup hooks if the builder is installed and activated.
     */
    static public function beaver_chart_setup_hooks() {
        if (!class_exists('FLBuilder')) {
            return;
        }
        // Load chart modules.
        add_action('init', __CLASS__ . '::beaver_chart_module_load');
    }

    /**
     * Loads chart modules.
     */
    static public function beaver_chart_module_load() {
        require_once BEAVER_CHART_MODULE_DIR . 'module/pie-chart/pie-chart.php';
        require_once BEAVER_CHART_MODULE_DIR . 'module/bar-chart/bar-chart.php';
    }
}

Beaver_Chart_Modules_Init::init();