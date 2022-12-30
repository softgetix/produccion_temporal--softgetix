var actionCalendar = 'get-calendario-60-dias';

$(document).ready(function(){
	
	if($('#solapa').val() == 'historico' || $('#solapa').val() == 'km_recorridos' || $('#solapa').val() == 'viajes'){
		<!-- BUSQUEDA simple -->
		getCalendario('#busqueda-simple', $('#mes-desde').val(), $('#anio-desde').val(),$('#fecha').val());
		<!-- -->	
	}
	else{
		<!-- BUSQUEDA avanzada -->
		getCalendario('#busqueda-avanzada .calendar-desde', $('#mes-desde').val(), $('#anio-desde').val(),$('#fecha_desde').val());
		getCalendario('#busqueda-avanzada .calendar-hasta', $('#mes-hasta').val(), $('#anio-hasta').val(),$('#fecha_hasta').val());	
		<!-- -->
	}
});



function clickDate(id){
	var ide = String(id).split('#');
	var ide_1 = ide[0];
	//var fecha = ide[1];
	ide_1 = String(ide_1).replace('.','');
	ide_1 = String(ide_1).replace(' ','-');
	
	var ide_2 = String(ide[1]).split('-');
	
	var id = ide_2[0]+ide_2[1]+ide_2[2]; 
	
	
	var fecha = ((ide_2[0] < 10)?'0':'')+ide_2[0];
	fecha+= '-'+((ide_2[1] < 10)?'0':'')+ide_2[1];
	fecha+= '-'+ide_2[2];
	
	if($('#'+ide_1+'-'+id).hasClass('activa') == true){
		//$('#'+ide_1+'-'+id).removeClass('activa');
	}
	else{
		for(i=1; i<=31; i++){
			$('#'+ide_1+'-'+i+ide_2[1]+ide_2[2]).removeClass('activa');	
		}	
		$('#'+ide_1+'-'+id).addClass('activa');	
	}
	
	if(ide_1 == 'busqueda-simple'){
		$('#fecha').val(fecha);
		if(typeof($('#idMovil').val()) != 'undefined'){
			if($('#idMovil').val() != ''){
				filtrar($('#idMovil').val(), fecha);
			}
		}
		$('strong#msg_fecha_desde').html(fecha);
	}
	else if(ide_1 == 'busqueda-avanzada-calendar-desde'){
		$('#fecha_desde').val(fecha);
		
		$.ajax({
			async:false,
			cache:false,
			type: "POST",
			url: 'ajax.php'
			,data:({accion:"get-formato-fecha",fecha:fecha,formato:'date'})
			,success:function(c){
				$('strong#msg_fecha_desde').html(c);
			}
		});
	}
	else if(ide_1 == 'busqueda-avanzada-calendar-hasta'){
		$('#fecha_hasta').val(fecha);
		
		$.ajax({
			async:false,
			cache:false,
			type: "POST",
			url: 'ajax.php'
			,data:({accion:"get-formato-fecha",fecha:fecha,formato:'date'})
			,success:function(c){
				$('strong#msg_fecha_hasta').html(c);
			}
		});
	}
}



//-- Chechs Moviles --//
function checkGroup(ide){
	if(ide == 'all-movil' || ide == 'all-event'){
		
		var idpadre = $('#group-'+ide).parent().parent().parent().parent().attr('id');
		if($('#group-'+ide).is(':checked')){
			$('#'+idpadre+' input:checkbox').attr('checked',true);
		}
		else{
			$('#'+idpadre+' input:checkbox').attr('checked',false);
		}
	}
	else{
		if($('#group-'+ide).is(':checked')){
			$('.check-grupo-'+ide).attr('checked',true);
		}
		else{
			$('.check-grupo-'+ide).attr('checked',false);
		}
	}
}

function deployGroup(ide){
	if($('#ul-group-'+ide).is(':visible')){	
		$('#ul-group-'+ide).hide();
		$('#deploy-'+ide).css('background-image','url(imagenes/mas.png)');
	}
	else{
		$('#ul-group-'+ide).show();
		$('#deploy-'+ide).css('background-image','url(imagenes/menos.png)');
	}
}

function generarInforme(solapa){
	closeMessage();
	var $submit = false;
	if(solapa == 'export_historico_xls' || solapa == 'export_historico_kml'){
		if($('#idMovil').val() != ''){
			$submit = true;	
		}
		else{
			viewMessage(false, arrLang['seleccione_movil']);
		}
	}
	else if(solapa == 'export_historico_avanzado_xls'){
		if($('#listado-moviles input:radio').is(':checked')){
			$submit = true;	
		}
		else{
			viewMessage(false, arrLang['seleccione_movil']);
		}
	}
	else if($('#listado-moviles input:checkbox').is(':checked')){
		if(solapa == 'export_alertas_xls'){
			if($('#listado-eventos input:checkbox').is(':checked')){
				$submit = true;	
			}
			else{
				viewMessage(false, arrLang['seleccione_evento']);
			}
		}
		else{
			$submit = true;
		}
	}
	else{
		viewMessage(false, arrLang['seleccione_movil']);
	}
	
	if($submit == true){
		//viewReload(arrLang['procesando_datos']);
		enviar(solapa);	
	}
}