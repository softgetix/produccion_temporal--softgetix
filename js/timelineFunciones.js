jQuery(function($) {
  
   alinearLogo();
   var lastScroll = document.body.scrollTop;
    $(window).scroll(function(e) {
        var pos = $(window).scrollTop();
		$('.class-fixed').css('top','-'+pos+'px');
		
		var left = 401 - parseInt($(window).scrollLeft());
		$('#enc-datos-fixed').css('left',left+'px');
		
		fijarHeaderInfo(document.getElementById('fijar_info').checked);
    });
});

function posicionMovil(){
	
	if($('#not-find-busqueda').hasClass('none') == true){
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data:({
					accion:'timeline_posicion_movil',
					viajeID:$('#movilID').val()
				}),
			success: function(msg){
				var movil = jQuery.parseJSON(msg);
				if(movil != null){
				for(i=0; i<movil.length; i++){
					
					var actualizarcion_1 = movil[i].ubicacion+'<br>'+movil[i].horaUbicacion;
					var actualizarcion_2 = "";
					var hs_programado = parseInt($('#hs_programado_'+movil[i].id).val());
					
					if(movil[i].vi_finalizado == 1){//en caso q el viaje este FINALIZADO
						actualizarcion_2 = '<strong>Finalizado '+movil[i].horaMarcha+'hs</strong>';
						if(movil[i].horaMarcha > hs_programado){
							actualizarcion_2+= ' <br>Atrasado '.concat(movil[i].horaMarcha - hs_programado)+'hs';}
						else{
							actualizarcion_2+= ' <br>En tiempo';}	
					}
					else{
						actualizarcion_1+= '<br /><strong>En tr&aacute;nsito (hs):</strong> '+movil[i].horaMarcha;
						actualizarcion_2 = movil[i].estadoViaje+'<br />'+movil[i].fechaEstimada;
						actualizarcion_2+= '<br /><strong>Estimado (hs):</strong> '+movil[i].horaEstimada;
					}
					
					//-- Actualizacion 1 --//
					$('#actualizacion1_'+movil[i].id).html(actualizarcion_1);
					
					//-- Actualizacion 2 --//
					$('#actualizacion2_'+movil[i].id).html(actualizarcion_2);
					
					
					if(movil[i].posicion > 0){
						var pos_movil = 0;
						
						/*** Obtener posición del vehículo ***/ 
						for(celda=1; celda< movil[i].posicion; celda++){
							pos_movil = pos_movil + document.getElementById('celdas_hide_'+celda).offsetWidth;
						}
						
						/*** agregar label a etiqueta y centrar ***/
						$('#movil_'+movil[i].id+' span.etiqueta_movil').html(movil[i].label);
						var ancho_etiqueta = $('#movil_'+movil[i].id+' span.etiqueta_movil').width();
						if(ancho_etiqueta > 0 && pos_movil > 2){
							$('#movil_'+movil[i].id+' span.etiqueta_movil').css('width',ancho_etiqueta+'px');
							$('#movil_'+movil[i].id).css('width',ancho_etiqueta+'px');
							$('#movil_'+movil[i].id+' span.etiqueta_movil').css('margin-left','-'+((ancho_etiqueta/2)-16)+'px');
						}
						
						if(movil[i].posicion == 144){
							$('#movil_'+movil[i].id+' span.etiqueta_movil').css('margin-left','-'+(ancho_etiqueta-20)+'px');
						}
						
						/**** posicionar imagen ****/
						$('#movil_'+movil[i].id).css('left',pos_movil-4);
						$('#movil_'+movil[i].id+' img').attr('src',movil[i].imagen);
						
						/*** Colorear celdas ***/
						var hour = new Date();
						var minutos = 0
						if(hour.getMinutes() >= 10 && hour.getMinutes() <= 19){ minutos = 1;}
						else if(hour.getMinutes() >= 20 && hour.getMinutes() <= 29){ minutos = 2;}
						else if(hour.getMinutes() >= 30 && hour.getMinutes() <= 39){ minutos = 3;}
						else if(hour.getMinutes() >= 40 && hour.getMinutes() <= 49){ minutos = 4;}
						else if(hour.getMinutes() >= 50 && hour.getMinutes() <= 59){ minutos = 5;}
						
						var pintar = (hour.getHours() * 6) + parseInt(minutos) + 2;
						
						for(h=1; h< pintar; h++){
							$('#vehiculo_'+movil[i].id+'_'+h).removeClass('trayectoria_inactiva');
							$('#vehiculo_'+movil[i].id+'_'+h).addClass('trayectoria_activa');
						}
					}
				}}
				alinear();
				alinearLogo();
				
				// -- Alinear hora actual al centro de la pantalla --//
				/*
				//if(hour.getMinutes() == 10 || hour.getMinutes() == 20 || hour.getMinutes() == 30 || hour.getMinutes() == 40 || hour.getMinutes() == 50 || hour.getMinutes() == 60){
					//var maxScrollLeft = $(document).width() - $(window).width(); 
					var posicion = $('#vehiculo_'+movil[0].id+'_'+(String)(parseInt(pintar)-1)).position();
					
					/*var centerScroll = parseInt(maxScrollLeft)/2;
					var dif =  centerScroll - parseInt(posicion.left);
					if(dif < 0){
						dif = dif * -1;	
					}
					
					var posicionScroll = Math.round((dif/2) + centerScroll);
					//$(window).scrollLeft(posicionScroll);
					*//*
					var dif = $(window).width()/2;
					var posicionScroll = Math.round(parseInt(posicion.left) - dif);
					$('html, body').animate({scrollLeft: posicionScroll}, 800);
					console.info(posicionScroll+'** pos left '+posicion.left+"**"+dif+"//"+$(window).width());
				//}*/
				//-- --//
			}		
		});
		alinear();
	}
}


function alinear(){
	
	var movilID = jQuery.parseJSON($('#movilID').val());
	var dif = 0;
	var navegador = navigator.userAgent;
	if(navigator.userAgent.indexOf('Firefox') !=-1){
		//dif = 17;
	}
  
  	for(i = 0; i < movilID.length; i++){
  	
		var col1 = $('#viaje_'+movilID[i]).height();
		var col2 = $('tr.display_'+movilID[i]+' td.row_vehiculo_'+movilID[i]).height();
	
		if(col1 < col2){
			$('#viaje_'+movilID[i]).height(col2  + dif);
			$('tr.display_'+movilID[i]+' td.row_vehiculo_'+movilID[i]).height(col2);
		}
		if(col1 > col2){
			$('#viaje_'+movilID[i]).height(col1);
			$('tr.display_'+movilID[i]+' td.row_vehiculo_'+movilID[i]).height(col1  + dif);	
		}
		
		var col3 = $('#patente_'+movilID[i]).height();
		var col4 = $('.row_guia_'+movilID[i]).height();
		
		if(col3 < col4){
			$('#patente_'+movilID[i]).height(col4 + dif);
			$('.row_guia_'+movilID[i]).height(col4);
		}
		if(col3 > col4){
			$('#patente_'+movilID[i]).height(col3);
			$('.row_guia_'+movilID[i]).height(col3 + dif);
		}
  	}
}

function alinearLogo(){
	console.warn('alinear logo')
	/*
	var viajes = jQuery.parseJSON($('#movilID').val());
	
	$('.enc-show').width($('.enc-hide').width());
	if(viajes.length == 0){
		$('.hora-show').width(116);	
	}
	else{
		$('.hora-show').width($('.hora-hide').width());	
	}
	*/	
}

function alinearEncabezado(){
	for(celda=1; celda< 145; celda++){
		$('#celdas_show_'+celda).width(parseInt(document.getElementById('celdas_hide_'+celda).offsetWidth)-5);
	}
}

function fijarInfo(checked){
	if(checked == false){
		$('#table-info').addClass('class-fixed-off'); 
		$('#table-info').removeClass('class-fixed');
		$('#table-info').css('top','0px');
	}
	else{
		$('#table-info').addClass('class-fixed'); 
		$('#table-info').removeClass('class-fixed-off');
		$('#enc-info-fixed').css('left','1px');
		$('#table-info').css('top','-'+$('#table-info').position().top+'px');
	}
	fijarHeaderInfo(checked);
}

function fijarHeaderInfo(checked){
	var left = parseInt($(window).scrollLeft()) - 1;
	
	if(left < 1 ){
		$('#enc-info-fixed').css('left','1px');	
	}
	else if(checked == false && left > 3 && $('#table-info').hasClass('class-fixed-off') == true){
		$('#enc-info-fixed').css('left','-'+left+'px');
	}
}

function serverHour(){
	var arrFecha = $('#fecha_dia div').html().split('-');
	var arrHora = $('#fecha_hora div').html().split(':');
	
	var fecha = new Date(arrFecha[2],arrFecha[1],arrFecha[0],arrHora[0],arrHora[1],parseInt(arrHora[2])+1);
	
	var hora = fecha.getHours();
	var minutos = fecha.getMinutes();
	var segundos = fecha.getSeconds();
	
	if(hora < 10){ hora='0'.concat(parseInt(hora));}
	if(minutos < 10){minutos='0'.concat(parseInt(minutos)); }
	if(segundos < 10){ segundos='0'.concat(parseInt(segundos));}
	
	var fecha_hora = hora+':'+minutos+':'+segundos;
	$('#fecha_hora div').html(fecha_hora);
	
	/*recargar la pagina a media noche*/
	if(fecha_hora == '00:00:00'){
		document.location.reload(true);
	}
}

function serverDate(){
	$.ajax({
		type: "POST",
		url: "ajax.php",
		data:({
			accion:'get-fechaHora-server-format',
			format:'seconds'
		}),
		success: function(msg){ 
			var arrfecha = msg.split(' ');
			$('#fecha_dia div').html(arrfecha[0]);
			$('#fecha_hora div').html(arrfecha[1]);
		}	
	});
}

function getBuscar(valor){
	var viajes = jQuery.parseJSON($('#movilID').val());
		
	if(valor == ""){
		for(i=0; i<viajes.length; i++){
			$('.display_'+viajes[i]).show();	
		}
	}
	else{
			
		for(i=0; i<viajes.length; i++){
			//$('.display_'+viajes[i]).fadeOut();	
			$('.display_'+viajes[i]).hide();	
		}
			
		var resBusqueda = 0;
		$('#not-find-busqueda').addClass('none');
			
		for(i=0; i<viajes.length; i++){
			var busq1 = $('#viaje_'+viajes[i]).html();
			var busq2 = $('#chofer_'+viajes[i]).html();
			var busq3 = $('#cliente_'+viajes[i]).html();
			var busq4 = $('#patente_'+viajes[i]).html();
			var busq5 = $('#actualizacion1_'+viajes[i]).html();
			var busq6 = $('#actualizacion2_'+viajes[i]).html(); 
				
			// Eliminar codigo HTML
			busq1 = busq1.replace(/<[^>]+>/ig,"");
			busq2 = busq2.replace(/<[^>]+>/ig,"");
			busq3 = busq3.replace(/<[^>]+>/ig,"");
			busq4 = busq4.replace(/<[^>]+>/ig,"");
			busq5 = busq5.replace(/<[^>]+>/ig,"");
			busq6 = busq6.replace(/<[^>]+>/ig,"");
			
			// Eliminar texto fijo 
			texto = new Array('Tiempo programado (hs):','En tránsito (hs):','Estimado (hs):');
			for(ini=0; ini<texto.length; ini++){
				busq1 = busq1.replace(texto[ini],"");
				busq2 = busq2.replace(texto[ini],"");
				busq3 = busq3.replace(texto[ini],"");
				busq4 = busq4.replace(texto[ini],"");
				busq5 = busq5.replace(texto[ini],"");
				busq6 = busq6.replace(texto[ini],"");
			}
			 				
			// Convertir todo a Minusculas
			busq1 = busq1.toLowerCase();
			busq2 = busq2.toLowerCase();
			busq3 = busq3.toLowerCase();
			busq4 = busq4.toLowerCase();
			busq5 = busq5.toLowerCase();
			busq6 = busq6.toLowerCase();
			valor = valor.toLowerCase();
				
			// Busqueda
			busq1 = busq1.indexOf(valor);
			busq2 = busq2.indexOf(valor);
			busq3 = busq3.indexOf(valor);
			busq4 = busq4.indexOf(valor);
			busq5 = busq5.indexOf(valor);
			busq6 = busq6.indexOf(valor);
				
			if (busq1!=-1 || busq2!=-1 || busq3!=-1 || busq4!=-1 || busq5!=-1 || busq6!=-1) {
				$('.display_'+viajes[i]).show();
				resBusqueda = 1;
			}
		}
		
		if(resBusqueda == 0){
			$('#not-find-busqueda').removeClass('none');
		}
	}
	
	alinear();
	//alinearLogo();
	alinearEncabezado();
}			