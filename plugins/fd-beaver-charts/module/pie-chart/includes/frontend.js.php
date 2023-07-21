(function($) {
var flickCanvas = document.getElementById("pie-chart-<?php echo $id; ?>");       
var flickData = {
    labels: [<?php 
		        foreach( $settings->pie_chart as $chart_item )
			    { ?>" <?php echo $chart_item->pie_tag_text;  ?>",
			    <?php } ?>
			],
    datasets: [
        {
             backgroundColor: [<?php 
		 foreach( $settings->pie_chart as $chart_item )
			{
			?>"#<?php echo $chart_item->pie_bar_color;  ?>", <?php } ?>],
          data: [<?php 
		 foreach( $settings->pie_chart as $chart_item )
			{
			?>"<?php echo $chart_item->pie_value;  ?>", <?php } ?>]
        }]
};
var pieChart = new Chart(flickCanvas, {
    type: 'pie',
    data: flickData,
    options: {  
        legend: {
        position: '<?php echo $settings->tag_position; ?>',
		},
	    responsive: true,		
		maintainAspectRatio: false,
		cutoutPercentage : <?php echo $settings->pie_chart_width; ?>,
    }
});
})(jQuery);