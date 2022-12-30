var marker =  '';
var markerPoint = '';
var arrPoint = [];
var arrLiners = [];

$(document).ready(function(e) {
	var divMapa = document.getElementById('mapa');
	CrearMapa(divMapa);
	mapSetCenter(-34.644207,-58.416967);
	mapSetZoom(14);	
});

$(function() {	
	$(".date").live("focusin", function() { 
       $(this).datepicker({
            /*onSelect: function(objDatepicker){
				var fecha = $(this).val().replace('/','-');
                var fecha = fecha.replace('/','-');
				$(this).val(fecha);
            }*/
        });
    });
	
	$( "#imei" ).autocomplete({
    	source: function( request, response ) {
        $.ajax({
        	type: "POST",
			url: "controladores/abmLogGatewayControlador.php",
			dataType: "json",
			data:({
				accion:'autocomplete-imei',
				buscar:request.term
			}),
			success: function(data){
            	response($.map(data, function( item ) {
                	return {
                    	label: item.movil,
                        value: item.imei
                    }
                }));
			}
    	});
		},
    	minLength: 2,
        select: function( event, ui ) {
        	$(this).end().val(ui.item.value);
        },
        open: function() {
        	$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
        },
        close: function() {
        	$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
        }
	});
}); 


function getLogGateway(){
	var imei = $('#imei').val();
	var fecha = $('#fecha').val();
	var equipo = $('#equipo').val();
	
	if(imei == ''){
		alert('Ingrese el imei');
	}
	else if(fecha == ''){
		alert('Ingrese la fecha deseada');	
	}
	else{
		deleteMap(marker);
		deleteMap(arrLiners[0]);
		deleteMap(arrLiners[1]);
		deleteMap(arrLiners[2]);
		
		$('#ResultadoLog').html('<img src="imagenes/ajax-loader.gif" >');
		$('#rueda-carga').html('<img src="imagenes/ajax-loader.gif" >');
		
		 getDatosMovil(imei);
		
						
		$.ajax({
			async:false,
			cache:false,
			type: "POST",
			url: "controladores/abmLogGatewayControlador.php",
			data:({
				accion:'getInfoGateway',
				imei:imei,
				fecha:fecha,
				equipo:equipo
			}),
			success: function(msg){
				
				var arr = jQuery.parseJSON(msg);
				$('#ResultadoLog').html(arr.resp);
				var centerMap = 0;
				
				//-- Ptos del LOG --//
				var hasta = 0;
				var cantReg = 0;
				if(arr.ptosLog != null){
					cantReg = arr.cantRegLog;
					hasta = arr.ptosLog.length;
					var polyPoints = [];
					for(i=0; i<hasta; i++){
						if(typeof(arr.ptosLog[i]) != 'undefined'){
							/*
							var lng = arr.ptosLog[i]['gps_longitude'];
							var lat = arr.ptosLog[i]['gps_latitude'];
							polyPoints.push(mapLatLng(lat,lng));
							*/
							var arrAux = [];
							arrAux['lat'] = arr.ptosLog[i]['gps_latitude'];
							arrAux['lon'] = arr.ptosLog[i]['gps_longitude'];
							polyPoints.push(arrAux);
							
							if(!centerMap && arrAux['lat'] != 0 && arrAux['lon'] != 0){
								verMapa(arrAux['lat'], arrAux['lon']);
								centerMap = 1;	
							}
						}
					}
					//marker[0] = mapPolyline(polyPoints , , 0, 3);
					//setMap(marker[0]);
					//console.warn(polyPoints);
					arrLiners[0] = mapPolyline(polyPoints, '#FF0000');
					setMapObj(lineLayer);
				}
				if(cantReg == '-1'){cantReg = 0;}
				$('#cant-gps').html('('+cantReg+')');
				//-- --//
				
				//-- Ptos del CELLTAWER --//
				var hasta = 0;
				if(arr.ptosCell != null){
					hasta = arr.ptosCell.length;
					var polyPoints = [];
					for(i=0; i<hasta; i++){
						/*var lng = arr.ptosCell[i]['lng'];
						var lat = arr.ptosCell[i]['lat'];
						polyPoints.push(mapLatLng(lat,lng));*/
						 
						var arrAux = [];
						arrAux['lat'] = arr.ptosCell[i]['lat'];
						arrAux['lon'] = arr.ptosCell[i]['lng'];
						polyPoints.push(arrAux);
							
					}
					//marker[1] = mapPolyline(polyPoints , '#FF00FF', 0.3, 3);
					//setMap(marker[1]);
					
					arrLiners[1] = mapPolyline(polyPoints, '#FF00FF');
					setMapObj(lineLayer);
					
				}
				$('#cant-antenas').html('('+hasta+')');
				//-- --//
				
				//-- Ptos del HISTORY --//
				var hasta = 0;
				if(arr.ptosHistory != null){
					hasta = arr.ptosHistory.length;
					var polyPoints = [];
					for(i=0; i<hasta; i++){
						/*var lng = arr.ptosHistory[i]['hy_longitud'];
						var lat = arr.ptosHistory[i]['hy_latitud'];
						polyPoints.push(mapLatLng(lat,lng));*/
						 
						var arrAux = [];
						arrAux['lat'] = arr.ptosHistory[i]['hy_latitud'];
						arrAux['lon'] = arr.ptosHistory[i]['hy_longitud'];
						polyPoints.push(arrAux);
						
						if(!centerMap){
							verMapa(arrAux['lat'], arrAux['lon']);
							centerMap = 1;	
						}
					}
					//marker[2] = mapPolyline(polyPoints , '#0000FF', 0.6, 3);
					//setMap(marker[2]);
					arrLiners[2] = mapPolyline(polyPoints, '#0000FF');
					setMapObj(lineLayer);
				}
				if(typeof(hasta) == 'undefined'){hasta = 0;}
				$('#cant-historico').html('('+hasta+')');
				//-- --//
			
				$('#rueda-carga').html('');
				
			},	
			beforeSend:function(){},
			error:function(objXMLHttpRequest){}	
		});
	}
}

function getDatosMovil(imei){
	$.ajax({
			async:false,
			cache:false,
			type: "POST",
			url: "controladores/abmLogGatewayControlador.php",
			data:({
				accion:'getInfoMovil',
				imei:imei
			}),
			success: function(msg){
				var arr = jQuery.parseJSON(msg);
				$('#datos-movil').html('<strong>Sist. Op:</strong> '+arr.modelo);
			},	
			beforeSend:function(){},
			error:function(objXMLHttpRequest){}	
		});	
}

function verMapa(lat, lng){
	/*
	deleteMap(markerPoint);
	arrPoint['myLatLng'] = mapLatLng(lat,lng);	
	markerPoint = mapMarker(arrPoint);
	markerPoint.setMap(map);
	mapSetCenter(lat, lng);
	mapSetZoom(16);
	*/
	
	deleteMap(marker);
	var arr = [];
	arr['lat'] = lat;
	arr['lng'] = lng;
	arr['icono'] = 'imagenes/iconos/markersRastreo/1/verde/1_.png'
	imgW = 31;
	imgH = 46;
	setMap(mapMarker(arr), false);
	mapSetCenter(lat, lng);
	mapSetZoom(16);
	
	
}