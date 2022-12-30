//-- grafico de tortas --//
/*function drawChartTorta(data, title, divID) {
	var options = {
		title: title
		,colors: ['#5CB85C','#D9534F']
		,legend: 'none'
	};
	
	var chart = new google.visualization.PieChart(document.getElementById(divID));
	chart.draw(data, options);
}*/
						
//-- grafico de barras --//
function drawChartBarras(data, title, divID, $colors = []) {

	if(typeof($colors) != 'object' || (typeof($colors) == 'object' && $colors.length == 0 )){
			$colors = ['#5CB85C','#D9534F'];
	}

	var options = {
		title: title
	  	//, hAxis: {title: 'Year', titleTextStyle: {color: 'red'}}
		,colors: $colors
		,legend: 'none'
	};
				
	var chart = new google.visualization.ColumnChart(document.getElementById(divID));
	chart.draw(data, options);
}

//-- grafico de lineas --//
/*function drawChartLineas(data, title, divID){
	var options = {
		title: title
	  	//, hAxis: {title: 'Year', titleTextStyle: {color: 'red'}}
		//,colors: ['#5CB85C','#D9534F']
		//,curveType: 'function'
		,legend: 'none'
	};
				
	var chart = new google.visualization.LineChart(document.getElementById(divID));
	chart.draw(data, options);
}*/