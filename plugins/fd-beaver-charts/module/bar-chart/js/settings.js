(function ($) {    FLBuilder._registerModuleHelper('bar-chart', {        rules: {            xaxis_data_value: {                required: true,            },            yaxis_label_fontsize: {                required: true,            },            xaxis_label_fontsize: {                required: true,            }        },    });    FLBuilder._registerModuleHelper('barchart_data_form', {        rules: {            yaxis_data_value: {                required: true,            },        },    });})(jQuery);