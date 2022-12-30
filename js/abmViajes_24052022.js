
function OnOff(ide, $ajax, $function,$auxide){

	$id = '#a-'+ide;
	if($auxide != ''){
		$id = $id + '-'+$auxide;
	}
	if($($id).hasClass('iconOn')){
		var $addClass = 'iconOff';
		var $removeClass = 'iconOn';
		var $value = 0;
	}
	else{
		var $addClass = 'iconOn';
		var $removeClass = 'iconOff';
		var $value = 1;
	}
	
	if($ajax == true){
		$.ajax({
			type: "POST",
			url: 'ajaxViajes.php',
			data:({
				accion:ide,
				id_viaje:$('#id_viaje').val(),
				value:$value
			}),
			success: function(msg){
				if(msg == true || msg == 'true'){
					$($id).addClass($addClass);
					$($id).removeClass($removeClass);
					$('#'+ide).val($value);
					if($function){
						eval($function);		
					}
				}
			}	
		});		
	}
	else{
		$($id).addClass($addClass);
		$($id).removeClass($removeClass);
		$('#'+ide).val($value);
	}
}

function viewDestinos(ide){
	if($('#link_'+ide).hasClass('off')){
		
		$('.row_viajes').hide();
		$('.icon.colapsar').addClass('off');
		$('.icon.colapsar').removeClass('on')
		
		$('.destinos_'+ide).show();
		$('#link_'+ide).addClass('on');
		$('#link_'+ide).removeClass('off');
	}
	else{
		$('.destinos_'+ide).hide();
		$('#link_'+ide).addClass('off');
		$('#link_'+ide).removeClass('on');
	}
}

function viewOptions($class, ide, $retroactivo){
	if($('#link_'+ide).hasClass('off')){
		$('.'+$class+'_'+ide).show();
		$('#link_'+ide).addClass('on');
		$('#link_'+ide).removeClass('off');
	}
	else{
		//--Ini. ocultar info hijos si el padre lo requiere
		if($retroactivo != ''){
			$('.'+$retroactivo).hide();
			$('.'+$class+'_'+ide).children().children().removeClass('on').addClass('off')
		}
		//--Fin.

		$('.'+$class+'_'+ide).hide();
		$('#link_'+ide).addClass('off');
		$('#link_'+ide).removeClass('on');
	}
}

var daysSearch = 31;
function getBuscar(e, valor){ 
	if(valor != ''){
		$('.date').attr('disabled','disabled');
		$('.date').val('');
		
		if($('#buscador_viaje').nextAll('#deleteBusqueda').length < 1){
			$('#buscador_viaje').after('<a href="javascript:emptyBuscar();" id="deleteBusqueda"><span class="sprite eliminar_black"></span></a>');
		}
	}
	else{
            $('.date').removeAttr('disabled');
            $('.date').datepicker('setDate',new Date());
            $('.fhasta').datepicker('change', {minDate:new Date(),maxDate:new Date(new Date().getTime() +daysSearch*24*60*60*1000)});
        }
	
	if(e.keyCode == 13){
		enviar('index');
	}
}

function emptyBuscar(){ 
	$('#buscador_viaje').val('');
	$('.date').removeAttr('disabled');
        $('.date').datepicker('setDate',new Date());
        $('.fhasta').datepicker('change', {minDate:new Date(),maxDate:new Date(new Date().getTime() +daysSearch*24*60*60*1000)});
        $('#deleteBusqueda').remove();
}

$(document).ready(function() {
	$('#buscador_viaje').trigger('keyup');
});


/**** graficos ****/
//-- grafico de tortas --//

function drawChartTorta(data, title, divID) {
	var options = {
		title: title
		,colors: ['#5CB85C','#D9534F']
		,legend: 'none'
	};
	
	var chart = new google.visualization.PieChart(document.getElementById(divID));
	chart.draw(data, options);
}
						
//-- grafico de barras --//
function drawChartBarras(data, title, divID) {
	var options = {
		title: title
	  	//, hAxis: {title: 'Year', titleTextStyle: {color: 'red'}}
		,colors: ['#5CB85C','#D9534F']
		,legend: 'none'
	};
				
	var chart = new google.visualization.ColumnChart(document.getElementById(divID));
	chart.draw(data, options);
	
	/*
	 google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Year', 'Sales', 'Expenses', 'Profit'],
          ['2014', 1000, 400, 200],
          ['2015', 1170, 460, 250],
          ['2016', 660, 1120, 300],
          ['2017', 1030, 540, 350]
        ]);

        var options = {
          chart: {
            title: 'Company Performance',
            subtitle: 'Sales, Expenses, and Profit: 2014-2017',
          },
          bars: 'vertical',
          vAxis: {format: 'decimal'},
          height: 400,
          colors: ['#1b9e77', '#d95f02', '#7570b3']
        };

	*/
}

//-- grafico de lineas --//
function drawChartLineas(data, title, divID){
	var options = {
		title: title
	  	//, hAxis: {title: 'Year', titleTextStyle: {color: 'red'}}
		//,colors: ['#5CB85C','#D9534F']
		//,curveType: 'function'
		,legend: 'none'
	};
				
	var chart = new google.visualization.LineChart(document.getElementById(divID));
	chart.draw(data, options);
}

/******/

function getVehiculosRecomendado(ide, id_transportista, id_usuario, id_conductor){
	var txtSelectMovil = $('#'+ide+' option:eq(0)').html();
	$('#'+ide).html('<option value="">'+txtSelectMovil+'</option>');
	
	$.ajax({
		type: "POST",
		url: 'ajaxViajes.php',
		data:({
			  	accion:'get-vehiculos-recomendados',
				id_transportista:id_transportista,
				id_usuario:id_usuario,
				id_conductor:id_conductor
			}),
		success: function(msg){
			var vehiculos = jQuery.parseJSON(msg);
			if(vehiculos){
				$('#'+ide).append('<option value="" disabled="disabled">------ '+arrLang['movil_recomendado']+' ------</option>');	
				for(i=0; i<vehiculos.length; i++){
					$('#'+ide).append('<option selected="selected" value="'+vehiculos[i]['id']+'">'+vehiculos[i]['dato']+'</option>');	
				}
				$('#'+ide).append('<option value="" disabled="disabled">---------- '+arrLang['otros_moviles']+' ---------</option>');	
			}
			getComboVehiculos(ide, id_transportista, id_usuario);
		}	
	});	
}

function getComboVehiculos(ide, transportista, id_usuario){
	$.ajax({
		type: "POST",
		url: 'ajaxViajes.php',
		data:({
			  	accion:'get-vehiculos',
				transportista:transportista,
				id_usuario:id_usuario
			}),
		success: function(msg){
			if(msg!="false"){
				var vehiculos = jQuery.parseJSON(msg);
				if(vehiculos){ 
					for(i=0; i<vehiculos.length; i++){ 
						var selected = "";
						if($('#id_vehiculo').val() == vehiculos[i]['id']){ 
							selected = 'selected="selected"';
						} 
						$('#'+ide).append('<option value="'+vehiculos[i]['id']+'" '+selected+'>'+vehiculos[i]['dato']+'</option>');
					}
				}
			}
			else{
				$('#'+ide).append('<option value="" disabled="disabled">'+arrLang['avanzada']+'</option>');
			}			
		}	
		
	});
}

function saveCotizacion($valor, $observacion){
	$('#motivo_error, motivo_ok').hide();
	$.ajax({
		dataType: "json",
		type: "POST",
		url: 'ajaxViajes.php',
		data:({
			accion:'save-cotizacion',
			id_viaje:$('#id_viaje').val(),
			valor:$valor,
			observacion:$observacion
		}),
		success: function(response){
			if(response.isvalid == false){
				$('#motivo_error').html(response.msg);
				$('#motivo_error').show();
			}
			else{
				$('#motivo_ok').html('Los datos se procesaron correctamente.');
				$('#motivo_ok').show();
			}
		}	
	});		
}

function asignarCotizacion($idtransportista, $name){
	$resp = confirm('Está seguro de asignar el viaje a: '+$name+'?');
	if($resp){
		$.ajax({
			dataType: "json",
			type: "POST",
			url: 'ajaxViajes.php',
			data:({
				accion:'asignar-cotizacion',
				id_viaje:$('#id_viaje').val(),
				idtransportista:$idtransportista
			}),
			success: function(response){
				if(response.isvalid == true){
					top.location = self.location+'&idViaje='+$('#id_viaje').val();
				}
				else{
					alert('Algo salío mal: El viaje no pudo ser asignado');
				}
			}	
		});	
	}	
}
