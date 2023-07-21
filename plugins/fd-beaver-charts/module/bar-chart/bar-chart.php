<?php
/**
 * @class BarChartModule
 */
class BarChartModule extends FLBuilderModule {  
    /** 
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */  
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __( 'Bar Chart', BCM_DOMAIN ),
            'description'   => __( 'Bar charts used to display and compare the number, frequency or other measure (e.g. mean) for different discrete categories of data.', BCM_DOMAIN ),            
            'category'      => __( 'BeaverCharts', BCM_DOMAIN ),		
            'dir'           => BEAVER_CHART_MODULE_DIR . 'module/bar-chart',
            'url'           => BEAVER_CHART_MODULE_URL . 'module/bar-chart',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
            'partial_refresh' => true,
        ));  
    }
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('BarChartModule', array(
    'bar_chart'     => array( // Tab
        'title'     => __( 'General', BCM_DOMAIN ), // Tab title
        'sections'  => array( // Tab Sections		
            'barchart-xaxis-value' => array(
                'title'     => __( 'Bar Chart', BCM_DOMAIN ),
                'fields'    => array(
                    'xaxis_data_value' => array(
                        'type'          => 'textarea',
                        'label'         => __( 'X - Axis Data', BCM_DOMAIN ),
                        'placeholder'   => __( '"Jan","Feb","Mar","Apr"',BCM_DOMAIN ),
                        'default'       => __( '"Jan","Feb","Mar","Apr"', BCM_DOMAIN ),
                        'help'          => __( 'Add Multiple X - Axis data using comma(,) saperator and text between (")', BCM_DOMAIN ),
                        'rows'          => '3'
                    ),
                    'fl_bar_chart' => array(
                        'type'          => 'form',
                        'label'         => __( 'Y - Axis Data Value', BCM_DOMAIN ),
                        'form'          => 'barchart_data_form',                        
                        'multiple'      => true
                    ),					
                )
            ),
        )
    ),
    'barchart_control'  => array( 
        'title'     => __( 'Control Setting', BCM_DOMAIN ), 
        'sections'  => array( 
            'general'       => array( 
                'title'     => __( 'Add Border', BCM_DOMAIN ), 
                'fields'    => array( 
                    'bar_border_width' => array(
                        'type'          => 'unit',
                        'label'         => __( 'Border Width', BCM_DOMAIN ),
                        'description'   => 'px',
                        'default'       => '',
                    ),
                    'bar_border_color' => array(
                        'type'          => 'color',
                        'label'         => __( 'Border Color', BCM_DOMAIN ),
                        'default'       => '000000',
                        'show_reset'    => true,
                    ),						
                )
            ),
            'label_style' => array( 
                'title'     => __( 'Tag Setting', BCM_DOMAIN ), 
                'fields'    => array(
                    'bar_chart_tag'  => array(
                        'type'          => 'select',
                        'label'         => __( 'Display Tag', BCM_DOMAIN ),
                        'default'       => 'yes',
                        'options'       => array(
                            'yes'           => __( 'Yes', BCM_DOMAIN ),
                            'no'            => __( 'No', BCM_DOMAIN ),
                        ),
                        'toggle' => array(
                            'yes' => array(
                                'fields' => array('bar_tag_position', 'bar_tag_fontsize', 'bar_tag_fontcolor'),                            
                            ),
                        )
                    ),
                    'bar_tag_position' => array(
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
                    'bar_tag_fontsize' => array(
                        'type'          => 'unit',
                        'label'         => __( 'Font Size', BCM_DOMAIN ),
                        'description'   => 'px',
                        'default'       => '18',

                    ),
                    'bar_tag_fontcolor' => array(
                        'type'          => 'color',
                        'label'         => __( 'Tag Color', BCM_DOMAIN ),
                        'default'       => '666666',
                        'show_reset'    => true,
                    ),
                )
            ),
            'bar-yaxis-control'       => array( 
                'title'     => __( 'Y - Axis Data Control', BCM_DOMAIN ), 
                'fields'    => array( 
                    'yaxis_label_display' => array(
                        'type'      => 'select',
                        'label'     => __( 'Y - Axis Label Display', BCM_DOMAIN ),
                        'default'   => 'true',						
                        'options'   => array(
                            'true'      => __( 'Show', BCM_DOMAIN ),
                            'false'     => __( 'Don`t Show', BCM_DOMAIN )							
                        ),
                        'toggle' => array(
                            'true' => array(
                                'fields' => array('yaxis_label', 'yaxis_label_fontsize', 'yaxis_label_fontcolor'),                         
                            ),
                        )
                    ),
                    'yaxis_label' => array(
                        'type'      => 'text',
                        'label'     => __( 'Y - Axis Label Text', BCM_DOMAIN ),
                        'default'   => __( 'Y - Axis Label', BCM_DOMAIN ),							
                    ),
                    'yaxis_label_fontsize' => array(
                        'type'      => 'unit',
                        'label'     => __( 'Label Font Size', BCM_DOMAIN ),
                        'default'   => __( '15', BCM_DOMAIN ),							
                    ),
                    'yaxis_label_fontcolor' => array( 
                        'type'      => 'color',
                        'label'     => __( 'Y - Axis Label Font Color', BCM_DOMAIN ),
                        'default'   => '1E90FF',
                        'show_reset'=> true
                    ),					
                    'bar_yaxis_callback' => array(
                        'type'      => 'text',
                        'label'     => __( 'Y - Axis Callback Value', BCM_DOMAIN ),
                        'default'   => '',	
                        'size'      => '5'
                    ),
                    'yaxis_callback_position' => array(
                        'type'      => 'select',
                        'label'     => __( 'Y - Axis callback Setting', BCM_DOMAIN ),
                        'default'   => 'suffix',						
                        'options'   => array(
                            'suffix'    => __( 'Suffix', BCM_DOMAIN ),
                            'prefix'    => __( 'Prefix', BCM_DOMAIN )							
                        ),
                    ),
                    'yaxis_line_position' => array(
                        'type'      => 'select',
                        'label'     => __( 'Y - Axis Line Position', BCM_DOMAIN ),
                        'default'   => 'left',
                        'options'   => array(
                            'left'      => __( 'Left', BCM_DOMAIN ),
                            'right'     => __( 'Right', BCM_DOMAIN )							
                        ),
                    ),
                    'yaxis_beginAtZero' => array(
                        'type'      => 'select',
                        'label'     => __( 'Y - Axis Value begin at Zero', BCM_DOMAIN ),
                        'default'   => 'true',
                        'options'   => array(
                            'true'      => __( 'Yes', BCM_DOMAIN ),
                            'false'     => __( 'No', BCM_DOMAIN )							
                        ),
                    ),
                    'yaxis_gridline' => array(
                        'type'      => 'select',
                        'label'     => __( 'Y - Axis Grid Line', BCM_DOMAIN ),
                        'default'   => 'true',						
                        'options'   => array(
                            'true'      => __( 'Show', BCM_DOMAIN ),
                            'false'     => __( 'Don`t Show', BCM_DOMAIN )							
                        ),
                    ),
                    'yaxis_reversevalue' => array(
                        'type'      => 'select',
                        'label'     => __( 'Y - Axis Value Reverse', BCM_DOMAIN ),
                        'default'   => 'false',
                        'options'   => array(
                            'true'      => __( 'Yes', BCM_DOMAIN ),
                            'false'     => __( 'No', BCM_DOMAIN )							
                        ),
                    ),
		)
            ),
            'bar-xaxis-control' => array( 
                'title'     => __( 'X - Axis Data Control', BCM_DOMAIN ), 
                'fields'    => array(
                    'xaxis_label_display' => array(
                        'type'      => 'select',
                        'label'     => __( 'X - Axis Label Display', BCM_DOMAIN ),
                        'default'   => 'true',						
                        'options'   => array(
                            'true'      => __( 'Show', BCM_DOMAIN ),
                            'false'     => __( 'Don`t Show', BCM_DOMAIN )							
                        ),
                        'toggle' => array(
                            'true'  => array(
                                'fields' => array('xaxis_label', 'xaxis_label_fontsize', 'xaxis_label_fontcolor'),                         
                            ),
                        )
                    ),
                    'xaxis_label' => array(
                        'type'      => 'text',
                        'label'     => __( 'X - Axis Label Text', BCM_DOMAIN ),
                        'default'   => __( 'X - Axis Label', BCM_DOMAIN ),							
                    ),
                    'xaxis_label_fontsize' => array(
                        'type'      => 'unit',
                        'label'     => __( 'Label Font Size', BCM_DOMAIN ),
                        'default'   => __( '15', BCM_DOMAIN ),							
                    ),
                    'xaxis_label_fontcolor' => array( 
                        'type'      => 'color',
                        'label'     => __( 'X - Axis Label Font Color', BCM_DOMAIN ),
                        'default'   => '1E90FF',
                        'show_reset'=> true
                    ),					
                    'bar_xaxis_callback' => array(
                        'type'      => 'text',
                        'label'     => __( 'X - Axis Callback', BCM_DOMAIN ),
                        'default'   => '',
                        'size'      => '5'
                    ),
                    'xaxis_callback_position'   => array(
                        'type'      => 'select',
                        'label'     => __( 'X - Axis Callback Setting', BCM_DOMAIN ),
                        'default'   => 'suffix',
                        'options'   => array(
                            'suffix'    => __( 'Suffix', BCM_DOMAIN ),
                            'prefix'    => __( 'Prefix', BCM_DOMAIN )							
                        ),
                    ),
                    'xaxis_line_position' => array(
                        'type'      => 'select',
                        'label'     => __( 'X - Axis Line Position', BCM_DOMAIN ),
                        'default'   => 'bottom',
                        'options'   => array(
                            'bottom'    => __( 'Bottom', BCM_DOMAIN ),
                            'top'       => __( 'Top', BCM_DOMAIN )							
                        ),
                    ),					
                    'xaxis_gridline' => array(
                        'type'      => 'select',
                        'label'     => __( 'X - Axis Grid Line', BCM_DOMAIN ),
                        'default'   => 'true',						
                        'options'   => array(
                            'true'      => __( 'Show', BCM_DOMAIN ),
                            'false'     => __( 'Don`t Show', BCM_DOMAIN )							
                        ),
                    ),					
                )
            ),
            'tooltip_section' => array( 
                'title'     => __( 'Tooltip Style', BCM_DOMAIN ), 
                'fields'    => array( 
                    'tooltip_style' => array(
                        'type'      => 'select',
                        'label'     => __( 'Tooltip Mode Style', BCM_DOMAIN ),
                        'default'   => 'index',
                        'options'   => array(
                            'index'     => __( 'Index', BCM_DOMAIN ),
                            'point'     => __( 'Point', BCM_DOMAIN ),
                            'nearest'   => __( 'Nearest', BCM_DOMAIN ),
                            'dataset'   => __( 'Dataset', BCM_DOMAIN ),
                            'x'         => __( 'X', BCM_DOMAIN ),
                            'y'         => __( 'Y', BCM_DOMAIN ),
                        ),
                    ),
                    'tooltip_bg_color' => array(
                        'type'          => 'color',
                        'label'         => __( 'Background Color', BCM_DOMAIN ),
                        'default'       => 'rgba(0,0,0,0.8)',
                        'show_reset'    => true,
                        'show_alpha'    => true
                    ),
                    'tooltip_title_font_size' => array(
                        'type'          => 'unit',
                        'label'         => __( 'Title Font Size', BCM_DOMAIN ),
                        'description'   => 'px',
                        'default'       => '15',
                    ),
                    'tooltip_title_color' => array(
                        'type'          => 'color',
                        'label'         => __( 'Title Color', BCM_DOMAIN ),
                        'default'       => 'ffffff',
                        'show_reset'    => true,
                    ),
                    'tooltip_title_bspace' => array(
                        'type'          => 'unit',
                        'label'         => __( 'Title Bottom Space', BCM_DOMAIN ),
                        'description'   => 'px',
                        'default'       => '10',
                    ),
                    'tooltip_body_text_font_size' => array(
                        'type'          => 'unit',
                        'label'         => __( 'Body Text Font Size', BCM_DOMAIN ),
                        'description'   => 'px',
                        'default'       => '16',
                    ),
                    'tooltip_body_text_color' => array(
                        'type'          => 'color',
                        'label'         => __( 'Body Text Color', BCM_DOMAIN ),
                        'default'       => 'ffffff',
                        'show_reset'    => true,
                    ),
                )
            ),
        )
    ),
));
/**
 * Register a settings form to use in the "barchart_data_form" field type above.
 */
FLBuilder::register_settings_form('barchart_data_form', array(
    'title' => __( 'Add Chart Data', BCM_DOMAIN ),
    'tabs'  => array(
        'barchartvalue' => array( 
            'title'     => __( 'Layout', BCM_DOMAIN ), 
            'sections'  => array( 
                'bar-yaxis-value' => array(
                    'title'     => __( 'BarChart', BCM_DOMAIN ),
                    'fields'    => array(
                        'bar_tag_text'  => array(
                            'type'          => 'text',
                            'label'         => __( 'Tag Name', BCM_DOMAIN ),
                            'default'       => __( 'Title', BCM_DOMAIN )							
                        ),
                        'yaxis_data_value' => array(
                            'type'          => 'textarea',
                            'label'         => __( 'Y - Axis Data', BCM_DOMAIN ),
                            'default'       => __( '27,43,54,72', BCM_DOMAIN ),
                            'placeholder'   => __( '27,43,54,72,....', BCM_DOMAIN ),
                            'rows'          => '3',
                            'help'          => __( 'Add Multiple Y - Axis data using comma(,) saperator', BCM_DOMAIN ),
                        ),
                        'bar_color' => array( 
                            'type'          => 'color',
                            'label'         => __( 'Bar Color', BCM_DOMAIN ),
                            'default'       => '298cd8',
                            'show_reset'    => true
                        ),
                    )
                ),               
            )
        ),
    )
));