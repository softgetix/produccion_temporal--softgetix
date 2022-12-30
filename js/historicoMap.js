
//FUNCIONES MAPA
var markers = new Array;
var marcadoresACrear = new Array;
var marcadoresAgrupados = new Array;
var marcadoresEnMapa = new Array;
var marcadoresLabel = new Array;
var marcadoresVisibles = new Array;
var latMenor = 90;
var latMayor = -90;
var lonMenor = 180;
var lonMayor = -180;
imgH = 40;

var hexToBin 	= new Array;
hexToBin[0] 	= "0000";
hexToBin[1] 	= "0001";
hexToBin[2] 	= "0010";
hexToBin[3] 	= "0011";
hexToBin[4] 	= "0100";
hexToBin[5] 	= "0101";
hexToBin[6] 	= "0110";
hexToBin[7] 	= "0111";
hexToBin[8] 	= "1000";
hexToBin[9] 	= "1001";
hexToBin["A"] 	= "1010";
hexToBin["B"] 	= "1011";
hexToBin["C"] 	= "1100";
hexToBin["D"] 	= "1101";
hexToBin["E"] 	= "1110";
hexToBin["F"] 	= "1111";
/*
$(document).ready(function(){

});
*/
$(window).resize(function() {
	resizeRef();
});

function resizeRef(){
	$('#mapa-historico').width($(window).width()-216);
	$('#mapa-historico').height(parseInt($(window).height()) - (parseInt($("#navbar").height()) + 102)); //82
	$("#infoPtos").css('width',parseInt($('#mapa-historico').width())-73);
}
	
function Cargar(iUnico){
	var divMapa = document.getElementById('mapa-historico');
	$('#mapa-historico').empty();
	resizeRef();
	
	//if ((divMapa && iMapa == 0) || iUnico != -1){
    if (divMapa){
	    //iMapa = 1;
		CrearMapa(divMapa);
		cargarOpcionesPermisosMapa();
		
		map.events.register('zoomend', this, function (e){
		 	zoomActual = mapGetZoom();
            cargarPuntos(-1,1);
            zoomAnterior = mapGetZoom();
        });
		
		zoomActual = mapGetZoom();
		cargarPuntosInicial(-1);// Pinta el Camino en azul/violeta
	}
}


function crearMarcadores(){
	registros_ordenados = registros;
	
	var iCont = -1;
    markers = new Array;
	
    if (typeof(registros) == 'undefined') {
        return;
    }
	var strCoordenadas = "";
    
	for (i=0;i < registros.length;i++) {
		if(registros_ordenados[i]['lat'] < latMenor){
            latMenor = registros_ordenados[i]['lat'];	
        }
        if(registros_ordenados[i]['lat'] > latMayor){
            latMayor = registros_ordenados[i]['lat'];	
        }
        
		//CHEKEO LA LONGITUD MAYOR Y MENOR
        if(registros_ordenados[i]['lon'] < lonMenor){
            lonMenor = registros_ordenados[i]['lon'];
        }
        if(registros_ordenados[i]['lon'] > lonMayor){
            lonMayor = registros_ordenados[i]['lon'];
        }
        
		var claseColor = "";
        var motor = "";
		var estMotor = "";
       	var bitMotor = arrBitMotor[registros_ordenados[i]['idMovil']].bit;
		var motorEncendido = arrBitMotor[registros_ordenados[i]['idMovil']].motor_encendido;
		var bit1 = hexToBin[registros_ordenados[i]['entrada'].substr(0,1)];
		var bit2 = hexToBin[registros_ordenados[i]['entrada'].substr(1,1)];
		var bits = "";
		if(typeof(bit1) != 'undefined' && typeof(bit2) != 'undefined'){
			bits = bit1.concat(bit2);
		}
		if(!bits){bits = '00000000';}
		
		var estadoMotor = bits.substr(bitMotor,1);
		if (estadoMotor == motorEncendido){ 
			var bAux1 = 1;}
		else{
			var bAux1 = 0;}
			
		if (bAux1 == 1){
            motor = arrLang['encendido'];
			estMotor = 1;}
		else{
            motor = arrLang['apagado'];
			estMotor = 0;}
		
		iCont += 1;	
		
		if (registros_ordenados[i]['hs_desde'] != null){ 
		    var fecha = registros_ordenados[i]['hs_desde'];}
        else{
        	var fecha = registros_ordenados[i]['fecha_txt'];}
		
		if (registros_ordenados[i][37] > 0){
        	fecha = agrupar_horarios(fecha, registros_ordenados[i]['hs_hasta']);
        }
        
		markers[iCont] = {
            lat:registros_ordenados[i]['lat'],
            lng:registros_ordenados[i]['lon'],
            estado:registros_ordenados[i]['movil'],
            motor:motor,
			estadomotor:estMotor,
            numero:registros_ordenados[i]['orden'],
            evento:registros_ordenados[i]['evento'],
            fecha: fecha,
            eventoID:registros_ordenados[i]['idEvento'],
            velocidadgps:registros_ordenados[i]['velocidadGPS'],
           	velocidadOriginal:registros_ordenados[i]['velocidadGPS'],
            icono:registros_ordenados[i]['eventoImg'],
            rumbo:rumbo(registros_ordenados[i]['curso'])
        };
				
        if ( typeof registros_ordenados[i]['ev_programado'] != "undefined" ){
            markers[iCont]["eventoProg"] = registros_ordenados[i]['ev_programado'];
        }
        else{
            markers[iCont]["eventoProg"] = 0;
        }
    }
}


function agrupar_horarios(horA, horB){
    var union = '+a+';
	horA = horA.replace(/^\s+|\s+$/g,"");
	horB = horB.replace(/^\s+|\s+$/g,"");;
	
	if (horA == undefined) {
        var a = "-";
        if (horB == undefined) {
            a = a + "-";
        } else {
            a = a + horB;
        }
        return a;
    }
	
    if (horA.indexOf("a", 0) == -1) {
        // No hay.
        if (horB.indexOf("a", 0) == -1) {
            horA = horA + union  + horB;
        } else {
            var a = horB.indexOf("a", 0);
            var sub = horB.substr(a);
            horA = horA + union + sub;
        }
    } else {
        var a = horA.indexOf("a", 0);
        horA = horA.substr(0, a);
        if (horB.indexOf("a", 0) == -1) {
            horA = horA + union + horB;
        } else {
            var a = horB.indexOf("a", 0);
            var sub = horB.substr(a);
            horA = horA + union + sub;
        }
    }
    return horA;
}

function orderOfCreation(marker, b) {
    return order;
}

function cargarPuntosInicial(iUnico) {
 	var poly = []
    var isIE = ( window.ActiveXObject ) ? true : false;
    var filtrarPuntos = false;
    var latAnt;
    var lngAnt;
	
    try {
        var bounds = mapGetBounds();
    } catch (e) {}
	
	var indiceMarcadores = -1;
    latMenor=  90;
    lonMenor= 180;
    latMayor= -90;
    lonMayor=-180;
	
	var desde = 0;
    var hasta = 0;
	
	if (markers){
        var iCon = 0;
        ignorar = false;
        for (var i = 0; i < markers.length; i++){
            var arr = [];
			arr['lat'] = markers[i].lat;
            arr['lon'] = markers[i].lng;
			
			if (filtrarPuntos == true) {
                // 2 = Motor apagado
                if (markers[i].eventoID == 2) {
                    ignorar = true;
                	poly.push(arr);
				}
				
                // 5 = Motor prendido
                if (markers[i].eventoID == 5 || i == markers.length-1) {
                    ignorar = false;
                }
				
                if (ignorar == false) {
               		poly.push(arr);
                }
            } 
			else {
               poly.push(arr);
            }
		    
            if(arr['lat'] < latMenor){
                latMenor = arr['lat'];	}
            if(arr['lat'] > latMayor){
                latMayor = arr['lat'];}
				
            //CHEKEO LA LONGITUD MAYOR Y MENOR
            if(arr['lon'] < lonMenor){
                lonMenor = arr['lon'];}
            if(arr['lon'] > lonMayor){
                lonMayor = arr['lon'];}
		   
            var imagen = "";
            var muestraLabel = 0;

            if (markers[i].eventoID != 1){
                imagen = 'eventos/'+markers[i].icono;
                muestraLabel = 1;
                esLabel = 0;
            }
            else{
                var icono = "";
                var velocidadAmarilla = (110*registros[0][100])/100;
                if (markers[i].velocidadOriginal > velocidadAmarilla) {
                    icono = "r";	
                    muestraLabel = 1;
                    esLabel = 1;
                }
				else if (registros[i]['velocidadGPS'] >= registros[0][100]) {
                    icono = "a";	
                    muestraLabel = 1;
                    esLabel = 1;
                } 
				else{
                    icono = "v";
                    esLabel = 1;
                }
                imagen = 'flechas/'+markers[i].rumbo+icono+'.png';
                if (iCon % 100 == 0){
                    muestraLabel = 0;}
                
                iCon += 1;
                if (isIE){
                    esLabel = 0;
                }
            }
            esLabel = 0;
		
            if ($('#radPredefinidos:checked').val() == 1 || $('#txtFechaDesde').val() == $('#txtFechaHasta').val()) {
                esLabel = 1;
            }
            esLabel = 1;
		
			var fechaLabel = markers[i].fecha;
			var agruparPuntos = true;
            if (agruparPuntos == false){
				markerV[i] = crearMarcador(arr['lat'], arr['lon'], i, imagen, esLabel, muestraLabel, fechaLabel);
            }
            else{ // Logica de AGRUPACION DE PUNTOS
                // 2 = Motor apagado
                if (markers[i].eventoID == 2 || i == 0) {
                    desde = i;
                    var lat2 = arr['lat'];
                    var lng2 = arr['lon'];
               		//marcadores[i] = crearMarcador(lat, lng, i, imagen, esLabel, muestraLabel,fechaLabel);
                }
				
                // 5 = Motor prendido
                if (desde > 0 && (markers[i].eventoID == 5 || i == markers.length-1)) {
                    hasta = i;
                }
				
                if (desde == 0) {
                    // No se apago el motor, sigue normal.
                    markerV[i] = crearMarcador(arr['lat'], arr['lon'], i, imagen, esLabel, muestraLabel, fechaLabel);
                    marcadoresAgrupados[i] = 0;
                } else {
                    if (hasta == 0) {
                        // desde > 0 y hasta 0, agrupando....
                        marcadoresAgrupados[i] = 1;
                    } else {
                        var fechaLabel = agrupar_horarios(markers[desde].fecha, markers[hasta].fecha);
                        markerV[i] = crearMarcador(lat2, lng2, desde, imagen, esLabel, muestraLabel,fechaLabel, hasta);
                        desde = 0;
                        hasta = 0;
                        marcadoresAgrupados[i] = 0;
                    }
                }
            }
			
            marcadoresEnMapa[i] = 0;
            marcadoresLabel[i]  = esLabel;
            marcadoresVisibles[i] = muestraLabel;
        }
		
		var iTipoBusq = $("#tipoBusqueda").val();
        if ( iTipoBusq == 2 ){
            arrEvents = g_Evento.split(",");
        }

        var polyLine = mapPolyline(poly);
		setMapObj(lineLayer);
        
		var minLngLat = mapLatLng(latMenor, lonMenor);
		var maxLngLat = mapLatLng(latMayor, lonMayor);
		mapFitBounds(minLngLat.lon+','+minLngLat.lat+','+maxLngLat.lon+','+maxLngLat.lat, false);
		//mapFitBounds(maxLngLat.lon+','+maxLngLat.lat+','+minLngLat.lon+','+minLngLat.lat, false); // Habilitar para Zapallo
    }
}

function crearMarcador(lat, lng, i, rumbo, muestraLabel, order1, label, hasta){ 
	
	var marker = "";	
	if ( typeof label == "undefined" ){
        label = "(?)";
    }
	
	var icono = dominio+'imagenes/iconos/'+rumbo;
	//var icono = '../'+rumbo;
	
	order = (order1 == 0) ? 1 : 0;
    
	var arr = [];
	arr['lat'] = lat;
	arr['lng'] = lng;
	arr['icono'] = icono;
	/*
	if(!order1){
		arr['zIndexProcess'] = orderOfCreation;
    }*/
    
	var marker = mapMarker(arr);
	marker.events.register('click',this , function (e){
		cargarInfoPtos(i, -1, hasta, lat, lng, 'click-icon');
	});
   	
	marker.icon.imageDiv.accessKey = icono;
	return marker;
}

function cargarPuntos(iUnico,iTime){
	filtrarPuntos = false;
   
    if (!iTime) {iTime = 0;}
	
    if (markers){
		for (var i = 0; i < markers.length; i++){
        	if (filtrarPuntos == true){
				//console.info("filtrat ptos");
			}
            else{
				marcadoresACrear[i] = i;
            }
		}
					
        if (marcadoresACrear != ""){
		   	mostrarMarcadores(marcadoresACrear);
        }		
	}
}

function mostrarMarcadores(mostrarMarcadores){
	var zoom = mapGetZoom();
    var isIE = (window.ActiveXObject) ? true : false;
    var total = mostrarMarcadores.length;
    var distancia = getEscala(zoom);
    if (isIE && total > 2000) {
        var mostrarCada = Math.round(total / 24);
    }
    else {
        var mostrarCada = Math.round(total / 48);
    }
    if (mostrarCada == 0) mostrarCada = 1;
	
    var fin = mostrarMarcadores.length;
    var latant = null;
    var lngant = null;
    var iMuestra = 0;
    var mostrar = false;
    var km = 0;
    var _km = 0;
    var distanciaAcumulada = 0;
    var x = null;
    var flechasEnMapa = [];
    var lat_ref, long_ref;

    mostrados = 0;
    for (var i = 0; i < fin; i++){ 
        mostrar = false;
        x = mostrarMarcadores[i];
		if(marcadoresAgrupados[i] == 1) {
            continue;}

		if( i == 0 || i == fin-1 ){ // Tomo el primer punto como referencia distancial.
            mostrar = true;
            lat_ref = markers[i].lat;
            long_ref = markers[i].lng;
        }
		
        if(i > 0 && latant != null){
            if(latant == markers[i].lat && lngant == markers[i].lng){
                km = 0;
			}
            else{
                km = dista(latant, markers[i].lat, lngant, markers[i].lng) * 1000;
            }

            distanciaAcumulada = distanciaAcumulada + km;
            if(distanciaAcumulada > distancia){
                mostrar = true;
                distanciaAcumulada = 0;
            }
        }

        // Ahora hay que calcular si alguno de los marcadores 
        // insertados esta cerca del punto nuevo.
        if(mostrar == true){
            mostrados++;
            for(var j = 0; j < i; j++){
                if(marcadoresEnMapa[mostrarMarcadores[j]]){
                    if(markers[i].lat == markers[j].lat && markers[i].lng == markers[j].lng){
                        _km = 0;
                    }
                    else{
                        _km = dista(markers[i].lat, markers[j].lat, markers[i].lng, markers[j].lng);
                    }

                    if((_km * 1000) < distancia * 0.75){
                        mostrar = false;
                        break;
                    }
                }
            }
        }
		
        if(zoom > 17){
            mostrar = true;
        }
		
		if(i == 0 || mostrar == true || i == fin-1){ //los que no son reportes normales, se muestran siempre
        	if (!marcadoresEnMapa[mostrarMarcadores[i]]){//Verifica si ya esta agregado al mapa
               marcadoresEnMapa[mostrarMarcadores[i]] = 1;
			
				if(markerV[mostrarMarcadores[i]].CLASS_NAME == 'OpenLayers.Marker'){
					setMap(markerV[mostrarMarcadores[i]], mostrarMarcadores[i]);
					
				}
				else{
					markerV[mostrarMarcadores[i]].setVisibility(true);
				}   
			}
			
			var lenFecha = String(markers[i].fecha).length;
			if(lenFecha <= 5){ imgW = 58;}
			else if(lenFecha <= 11){ imgW = 74;	}
			else if(lenFecha <= 13){ imgW = 80;	}
			else{ imgW = 136;}
			
			var imgIcono = 'getImage.php?pathmode=rel&file='+markerV[mostrarMarcadores[i]].markers[0].icon.imageDiv.accessKey+'&caption='+markers[i].fecha+'&historico=1';
			setMapIcon(mostrarMarcadores[i],imgIcono);
			markerV[mostrarMarcadores[i]].markers[0].icon.size.w = imgW;
			//markerV[mostrarMarcadores[i]].markers[0].icon.size.h = 40;
			markerV[mostrarMarcadores[i]].markers[0].draw();
		}
        else{
          
		   var forzar = false;
           if(forzar == true){
				markerV[mostrarMarcadores[i]] = crearMarcador(markers[i].lat, markers[i].lng, i, 'eventos/punto.png', true);
                marcadoresEnMapa[mostrarMarcadores[i]] = 1;
            }
			
			//oculta aquellos que estan en la pantalla pero no deben verse por el zoom
          	if(forzar == false){
                if (marcadoresEnMapa[mostrarMarcadores[i]]) {
					markerV[mostrarMarcadores[i]].setVisibility(false);
					marcadoresEnMapa[mostrarMarcadores[i]] = 0;
                }
            }
			
        }
		
        latant = markers[i].lat;
        lngant = markers[i].lng;
	}
    contadorMarcadores = -1;
    marcadoresACrear = new Array;
}


function cargarInfoPtos(i, z, hasta, lat, lng, evento){
	if(typeof(lat) == 'undefined' || typeof(lng) == 'undefined'){
		lat = registros[i]['lat'];
		lng = registros[i]['lon'];
	}
	
	document.getElementById('mapa-historico').style.display = "block";
    document.getElementById('detalle-historico').style.display = "none";
	VerBtnVideo();
		
    var btn = $("#btnDetalleMapa");
    btn.html('&lt;&lt; '+arrLang['detalle']);
	
	if(evento == 'click-nomeclatura'){
		//-- Visualizo en Mapa la referenvia seleccionada --//
		mapSetZoom(16); 
		mapSetCenter(lat, lng);
		z = 0;
		//-- --//
	}
	
	var horario = registros[i]['fecha'];
	var eventos = "";
	if (hasta > i) {
       	horario = agrupar_horarios(horario, registros[hasta]['fecha']);
    }
	
	if (z > 0) {
		mapSetZoom(z);
		mapSetCenter(lat, lng);
    }
	
   	if(markers[i].estadomotor == 1){
		var classEstadoMotor = 'motor-encendido';}
	else{
		var classEstadoMotor = 'motor-apagado';}
			
	//-- Nomenclado geocercas --//
    if (registros[i]['idHe'] > 0) {
		var fechaActualTime = new Date();
   		if(fechaActualTime.getMonth() < 9){
			var mes = fechaActualTime.getMonth()+1;
			mes = "0" + mes;} 
		else {
			var mes = fechaActualTime.getMonth()+1;}
		if(fechaActualTime.getDate() < 10){
			var dia = "0" + fechaActualTime.getDate() + "";}else {
			var dia = fechaActualTime.getDate();}
		
		fechaActualTime = dia+"/"+mes+"/"+fechaActualTime.getFullYear()
		
		//Paso la fecha a timestamp y calculo la diferencia con la 
        var fechaTime = sqlDateToUnixTimestamp(registros[i]['fecha'].substr(0,10));
        var fechaActual = sqlDateToUnixTimestamp(fechaActualTime)
        var diferencia = ((fechaActual - fechaTime)/86400) + 1;
        if (diferencia < 1) diferencia = 1;
        if (isNaN(diferencia)) diferencia = 1;
				
        var idNomenclarGeocerca  = i+","+registros[i]['idHe']+","+diferencia;
     }
	//-- Fin nomenclado geocercas --//		
	
	
   var contenido = '';
   contenido += '<fieldset>';
   contenido += '	<label class="float_l">'+arrLang['movil']+': </label>';
   contenido += '	<span class="float_l"><strong>'+markers[i].estado+'</strong></span>';
   if(celulares == false){	 
	   contenido += '	<span class="float_l movil-motor '+classEstadoMotor+'" title="'+markers[i].motor+'"></span>';
   }
   contenido += '	<span class="float_r">&nbsp;&nbsp;'+horario+'</span>';
   contenido += '</fieldset>';
   contenido += '<fieldset>';
   contenido += '	<label class="float_l" >'+registros[i]['evento_txt']+'</label>';
   contenido += '	<span id="nomenclado_'+i+'"></span>';
   contenido += '</fieldset>';
   contenido += '<fieldset class="float_r">';
   contenido += '	<label>[<a href="javascript:cerrarInfoPtos()">'+arrLang['cerrar']+'</a>]</label>';
   contenido += '</fieldset>';
   	
	$("#infoPtos").html(contenido);
	$("#infoPtos").show();
	
	nomenclarHistoricoMapa(i);
}

var ajaxNomenclarMapa;
function nomenclarHistoricoMapa(nomenclar_i){
	$('span#nomenclado_'+nomenclar_i).html('<img src="imagenes/ajax-loader.gif"/>'); 
	ajaxNomenclarMapa = $.ajax({
		type: "POST",
		url: "ajaxHistorico.php?i="+nomenclar_i,
		data:({
			accion:'nomenclar-historico'
			,i:nomenclar_i
			,idMovil:[0]['idMovil']
			,lat:registros[nomenclar_i]['lat']
			,lng:registros[nomenclar_i]['lon']
			,id_referencia:registros[nomenclar_i]['idHe']
			//,evento:registros[nomenclar_i]['evento_txt']
			,fecha:registros[nomenclar_i]['fechaGenerado'] //fechaOrdenado
		}),
		success: function(data){
			$('span#nomenclado_'+nomenclar_i).html(data); 
		}
	});
}

function cerrarInfoPtos(){
	$("#infoPtos").hide();	
	$("#infoPtos").html("");
}

var seteoTime;
var posicion_video;
function playVideo(valor){
	
	$('#btnPlayVideo').addClass('playvideo');
	$('#btnPlayVideo').html(arrLang['reproduciendo_video']);
	$('#btnPlayVideo').attr('href','javascript:stopVideo()');
	
	//-- Borro el puntero del video del pto anterior --//
	if(posicion_video > 0){
		deleteMap(marker);
	}
	//-- --//
	
	posicion_video = valor;
	
	if(posicion_video == 0){
		clearTimeout(seteoTime);
		mapSetZoom(16);
	}
	
	if(posicion_video == markers.length){
		$('#btnPlayVideo').removeClass('playvideo');	
		$('#btnPlayVideo').html(arrLang['ver_video']);
		$('#btnPlayVideo').attr('href','javascript:playVideo(0)');
		clearTimeout(seteoTime);
	}
	else{
		var arr = [];
		arr['lat'] = markers[posicion_video].lat;
		arr['lng'] = markers[posicion_video].lng;
		if(celulares){
			arr['icono'] = 'imagenes/iconos/markersRastreo/1/verde/1_.png';
		}
		else{
			if(markers[posicion_video].estadomotor == 1){
				arr['icono'] = 'imagenes/iconos/markersRastreo/1/verde/truck.png';
			}
			else{
				arr['icono'] = 'imagenes/iconos/markersRastreo/1/rojo/truck.png';
			}
		}
		//arr['animacion'] = google.maps.Animation.BOUNCE;
		imgW = 31;
		imgH = 46;
		setMap(mapMarker(arr), false);
	}
	
	if(posicion_video < markers.length){
		mapPanTo(markers[posicion_video].lat, markers[posicion_video].lng)
		posicion_video = posicion_video + 1;
		seteoTime = setTimeout(function() {playVideo(posicion_video);},2000); 
	}
}

function stopVideo(){
	$('#btnPlayVideo').removeClass('playvideo');	
	$('#btnPlayVideo').html(arrLang['ver_video']);
	clearTimeout(seteoTime);
	$('#btnPlayVideo').attr('href','javascript:playVideo('+posicion_video+')');
}

function OcultarBtnVideo(){
	$('#btnPlayVideo').hide();
	$('#btnPlayVideo').removeClass('playvideo');	
	$('#btnPlayVideo').html(arrLang['ver_video']);
	$('#btnPlayVideo').attr('href','javascript:playVideo(0)');
	clearTimeout(seteoTime);
	if(posicion_video > 0){
		deleteMap(marker);	
	}	
}

function VerBtnVideo(){
	$('#btnPlayVideo').show();
	$('#btnPlayVideo').attr('href','javascript:playVideo(0)');
	clearTimeout(seteoTime);	
}

function rumbo(curso){
    return calcularRumbo(curso, registros[0]['idioma']);
}