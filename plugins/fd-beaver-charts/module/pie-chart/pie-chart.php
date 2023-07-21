<?php

/**
 * @class PieChartModule
 */
class PieChartModule extends FLBuilderModule {

    /** 
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */  
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __( 'Pie and Doughnut Chart', BCM_DOMAIN ),
            'description'   => __( 'A pie chart and doughnut chart is a circular statistical graphic which is divided into slices to illustrate numerical proportion', BCM_DOMAIN ),
            'category'      => __( 'BeaverCharts', BCM_DOMAIN ),
            'dir'           => BEAVER_CHART_MODULE_DIR . 'module/pie-chart',
            'url'           => BEAVER_CHART_MODULE_URL . 'module/pie-chart',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
        ));  
    }

}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('PieChartModule', array(
    'fl_chart' => array( 
        'title'     => __( 'General', BCM_DOMAIN ), 
        'sections'  => array( 
            'pieform_layout' => array(
                'title'     => __( 'Pie Chart', BCM_DOMAIN ),
                'fields'    => array(
                    'pie_chart'   => array(
                        'type'      => 'form',
                        'label'     => __( 'Pie and Doughnut Chart Data', BCM_DOMAIN ),
                        'form'      => 'piechart_item_form',                        
                        'multiple'  => true
                    ),					
                )
            ),
        )
    ),
    'style_Control' => array( 
        'title'     => __( 'Control Setting', BCM_DOMAIN ), 
        'sections'  => array( 
            'pie_chartSettings' => array( 
                'title'     => __( 'Chart Settings', BCM_DOMAIN ), 
                'fields'    => array( 
                    'tag_position' => array(
                        'type'      => 'select',
                        'label'     => __( 'Tag Position', BCM_DOMAIN ),
                        'default'   => 'top',						
                        'options'   => array(
                            'top'       => __( 'Top', BCM_DOMAIN ),
                            'bottom'    => __( 'Bottom', BCM_DOMAIN ),
                            'left'      => __( 'Left', BCM_DOMAIN ),
                            'right'     => __( 'Right', BCM_DOMAIN )
                        ),
                    ),
                    'chart_size' => array(
                        'type'          => 'text',
                        'label'         => __( 'Chart Height', BCM_DOMAIN ),
                        'size'          => '5',
                        'placeholder'   => '500',
			'default'       => '500',
                        'help'          => __( 'Height of Chart', BCM_DOMAIN ),
                        'description'   => 'px'
                    ),
                    'pie_chart_width' => array(
                        'type'          => 'unit',
                        'label'         => __( 'Width', BCM_DOMAIN ),
                        'size'          => '5',
                        'placeholder'   => '50',
                        'default'       => '0',
                        'help'          => __('If you want show doughnut chart then increase width value <br><b>Note:</b> You can not set width value more then 99', BCM_DOMAIN ),
                        'description'   => '%'
                    ),					
                )
            ),
        )
    ),
));


/**
 * Register a settings form to use in the "piechart_item_form" field type above.
 */
FLBuilder::register_settings_form('piechart_item_form', array(
    'title' => __( 'Add Chart Value', BCM_DOMAIN ),
    'tabs'  => array(
        'general' => array( 
            'title'     => __( 'Layout', BCM_DOMAIN ), 
            'sections'   => array(
                'pie_data_value' => array(
                    'title'     => __( 'Chart Settings', BCM_DOMAIN ),
                    'fields'    => array(
                        'pie_tag_text' => array(
                            'type'          => 'text',
                            'label'         => __( 'Tag Name', BCM_DOMAIN ),
                            'default'       => __( 'Title', BCM_DOMAIN ),
                            'size'          => '20'
                        ),
                        'pie_value' => array(
                            'type'          => 'text',
                            'label'         => __( 'Value', BCM_DOMAIN ),
                            'placeholder'   => '30',
                            'default'       => __( '50', BCM_DOMAIN ),
                            'size'          => '5'
                        ),                        
			    'pie_bar_color' => array( 
                            'type'          => 'color',
                            'label'         => __( 'Color', BCM_DOMAIN ),
                            'default'       => '298cd8',
                            'show_reset'    => true
                        ),
                    )
                ),                
            )
        ),
    )
));