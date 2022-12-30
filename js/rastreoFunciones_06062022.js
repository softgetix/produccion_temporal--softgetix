// -- refresh debug --
var timeLastReq = null;
var timeCurrReq = null;
var arrReqTimes = {};
var arrUpdIds   = [];
// -- refresh debug --
var mostrandoDetalles = false;
var mgr = null;
var registros;

// Para conservar el zoom luego de que se hizo doble click en un movil.
var individualZoomLevel = 0;

var map = null,
    mytime = 0,
    timeArray = 0,
    timePreferencias = 0,
    timeGuardar = 0,
    campo = "fecha",
    orden = 1,
    flagEnMedicion=0,
    ultimoCentrado=0,
    trDato = [],
    m = -1,
    arrIconosMoviles = [],
    idUltimoResaltado = 0,
    textUltimoResaltado = '',
    esPrimera = 1,
    flagGuardarPreferencias = false, //SE ACTIVA CUANDO SE HACE ALGUNA MODIFICACION EN GRUPOS O MOVILES SELECCIONADOS O EN SEGUIR.
    mostrarAlertaMedir = true,
    strGrupos = null;

g_iGroupsChecked = 0,
g_bMulticheckInProcess = false;
markerV3data = [];


$(document).ready(function() {
	//-- Botonera de Pincke, Ver Referencias--//
	var $btnDlgReferencias = $("#btnDlgReferencias");
		$btnDlgReferencias.bind("click", function(ev){
			if ( g_dlgReferencias.dialog("isOpen")){
				g_dlgReferencias.dialog("close");
			}
			else{
				g_dlgReferencias.dialog("open");
			}
        });
			
	var $btnPtosReferencias = $("#btnPtosReferencias");
	$btnPtosReferencias.attr('title',(g_bConReferencias == true)?arrLang['ocultar_referencias']:arrLang['mostrar_referencias']);
    $btnPtosReferencias.bind("click", function(ev){
    	$('#divScrollUltimosReportes').html('<img src="imagenes/ajax-loader.gif" border="0" style="margin:10px;" />');
		g_bConReferencias = (g_bConReferencias == true)?false:true;
		g_bIsDataUpdate = false;
		actualizarArray();
		$(this).attr('title',(g_bConReferencias == true)?arrLang['ocultar_referencias']:arrLang['mostrar_referencias']);
		$('#btnPtosReferencias img').attr('src',(g_bConReferencias == true)?'imagenes/raster/black/map_pin_stroke_10x16.png':'imagenes/raster/black/map_pin_stroke_10x16_off.png');
    });
	//-- --//
	
	//-- inicio. Autocomplete MOVILES/GEOZONAS --//
	var ajaxAutocomplete;
	$( "#txtBuscar" ).autocomplete({
    	source: function( request, response ) {
			if(typeof(ajaxAutocomplete) != 'undefined'){
				ajaxAutocomplete.abort();
				$(this).removeClass('ui-autocomplete-loading');
			}
			ajaxAutocomplete = $.ajax({
				type: "POST",
				url: "ajax.php",
				dataType: "json",
				data:({
					accion:'get-buscador-rastreo',
					buscar:request.term
				}),
				success: function(data){
					response( $.map( data.resultados, function(item) {
						return {
							label: item.valor,
							value: item.valor
						}
					}));
					
					if (!data.resultados.length) {
						buscar_response = false;
					}
					else {
						buscar_response = true;
					}   
					$( "#txtBuscar" ).removeClass('ui-autocomplete-loading');    
				}
			});
			
		},
    	minLength: 2,
        select: function( event, ui ) {
        	$(this).end().val(ui.item.value);
			
			if (buscarHabilitado){
				var filtro, idMovil;
				newTracer.unfilterSIR();
				var sFilterText = $.trim(ui.item.value);
				idMovil = newTracer.filtrarMovil(sFilterText);
				if(typeof(idMovil) != 'undefined'){
					newTracer.seguirMovilFiltrado(idMovil);
					g_bSearchIsActive = true;
				}
				else{
					$('#divScrollUltimosReportes').hide();
					$('#divMSG').show();
				}
			}
			
        },
        open: function() {
        	$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
        },
        close: function() {
        	$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
        }
	});
	//-- fin. Autocomplete MOVILES/GEOZONAS --//
		
    onload_rastreo();
    ///newTracer.setupAutocompleteMoviles();
});


var time1 = new Date().getTime();
var time2 = 0;
var continua = true;
function esDblClick() {
    if (time2 == 0) {
        time1 = new Date().getTime();
        time2 = new Date().getTime();
        continua = true;
    } else {
        time2 = new Date().getTime();
        continua = (time2 - time1 < 1000) ? true : false;
        time1 = new Date().getTime();
        time2 = 0;
    }
}

function Cargar() {
    var divMapa = document.getElementById('mapa');
	if (divMapa) {
		CrearMapa(divMapa);	
		cargarOpcionesPermisosMapa();

		map.events.register('mousedown', this, function (e) {
			registerMapBounds();
            g_bUserDragging = true;
        },true); 
		
		map.events.register('mouseup', this, function (e) {
			g_bUserDragging = false;
            if (userPannedMap()){
                mostrarMarcadoresAgrupados();
            }
        },true); 
		
		map.events.register("mousemove", map,function (e) {
			var px = map.getLayerPxFromViewPortPx(e.xy) ;
			var latLng = map.getLonLatFromPixel(px);
			if(latLng != null){
				latLng.transform(new OpenLayers.Projection("EPSG:900913"),new OpenLayers.Projection("EPSG:4326"));
				$lat = $("#hoverLat");
				$lng = $("#hoverLng");
				$lat.html(latLng.lat.toFixed(LATLNG_MAX_LENGTH));
				$lng.html(latLng.lon.toFixed(LATLNG_MAX_LENGTH));
			}
			$g_oOverlayLatLng.css({ 
                "left": (px.x + 7) + "px",
                "top": (px.y + 7) + "px"
            });
			
			if (g_bShowLatLng){
                $g_oOverlayLatLng.show();
                //map.setOptions({ draggableCursor: "crosshair" });
            }
            else{
                $g_oOverlayLatLng.hide();
                //map.setOptions({ draggableCursor: "url(http://maps.google.com/mapfiles/openhand.cur), move" });
            }
        });
				
        map.events.register('mouseover', this, function (e){
            g_bHoveringMap = true;
        });
        
		map.events.register('mouseout', this, function (e){
            g_bHoveringMap = false;
           	$g_oOverlayLatLng.hide();
        });

		map.events.register('zoomend', this, function (e){
			individualZoomLevel = mapGetZoom();
            idUltimoResaltado = 0;
            if(individualZoomLevel < 2 ){
                mapSetZoom(2);
            }
			g_bZoomPending = true;
			
			if (g_bZoomPending && !g_bUserDragging){
                g_bZoomPending = false;
                var iZoom = mapGetZoom();
				mostrarMarcadoresAgrupados();
            }
        });
        
		idMovil = obtenerValorRadSeleccionado();
        // Solo una vez.
        actualizarArray();
        //arrayIntervalHandler = setInterval(actualizarArray, TRACE_REFRESH_INTERVAL );
		
		/*if(permisos['alertas'] == true){//-- Tiene Permiso Alertas --//
			actualizarAlertas();
			alertasIntervalHandler = setInterval( actualizarAlertas, ALERTAS_REFRESH_INTERVAL );
		}*/
	}
    else{
        setTimeout("Cargar()", 1000);
    }
}

setTimeout(Cargar, 1000);

var g_oMapBounds = {};

function getMapBounds() {
	var oMapBounds = mapGetBounds();
	
	if(typeof(map) != 'undefined' && typeof(oMapBounds) != 'undefined'){
        return oMapBounds;
    }
    else{
        return {};
    }
}

function registerMapBounds() {
    g_oMapBounds = getMapBounds();
}

function userPannedMap() {
    oMapBounds = getMapBounds();
    
    var bUserPannedMap = (oMapBounds.lat1 != g_oMapBounds.lat1);
    bUserPannedMap = bUserPannedMap || (oMapBounds.lat2 != g_oMapBounds.lat2);
    bUserPannedMap = bUserPannedMap || (oMapBounds.lng1 != g_oMapBounds.lng1);
    bUserPannedMap = bUserPannedMap || (oMapBounds.lng2 != g_oMapBounds.lng2);
    
    return bUserPannedMap;
}

function agregarRef(lat,lng,idMovil){
   mostrarPopup('boot.php?c=abmReferencias&action=popup&lt='+lat+'&lg='+lng+'&idMovil='+idMovil);
}

var idMovilSeleccionado=0;
var idUltimoMovilSeleccionado=0;

function pintarFila(idMovil){
    if(document.getElementById(idMovilSeleccionado)){
        if(idMovilSeleccionado > 0){
            document.getElementById(idMovilSeleccionado).className = "movilListaReporte";
            document.getElementById("fecha_" + idMovilSeleccionado).className = "movilListaReporte";
        }
    }
    if (document.getElementById(idMovil)) {
		document.getElementById(idMovil).className = "movilListaReporteSeleccionado";
		document.getElementById("fecha_" + idMovil).className = "movilListaReporteSeleccionado";
		idMovilSeleccionado = idMovil;
		idUltimoMovilSeleccionado = idMovilSeleccionado;
		resaltarIcono(idMovil,null,null);
	}
}

function despintarFila(idMovil){
    if(idMovil > 0){
        document.getElementById(idMovil).className = "movilListaReporte";
        document.getElementById("fecha_" + idMovil).className = "movilListaReporte";
    }
}

function centrarMovil(idMovil,latitud,longitud){
    pintarFila(idMovil);
    document.getElementById("chk_" + idMovil).checked = true;
    if (lastInfoWindow) {
        try {
            markerV[lastInfoWindow].close();
        } catch (e) {}
    }
}

function obtenerMovilesListados(){
    var $reportes = $("#divScrollUltimosReportes");
    var $checkboxes = $("input[type='checkbox']", $reportes);
    var arrMoviles = [], strMoviles="";
    
    $.each( $checkboxes, function( ind, checkbox ){
        arrMoviles.push( checkbox.value );
    } );

    strMoviles = arrMoviles.join(",");
    return strMoviles;
}

var flagRad=0;
var flagChekeados=0;

function seleccionarChecks(){
    if (flagRad == 1){
        flagRad=0;
    }
    else{
        deseleccionarRad();
    }
    var divUltimosReportes = document.getElementById("divScrollUltimosReportes"),
    listaInputs = divUltimosReportes.getElementsByTagName("input");
    for(i=0;i < listaInputs.length;i++){
        input = listaInputs[i];
        if(input.type=="checkbox"){
            if(flagChekeados==0){
                input.checked=true;
            }else{
                input.checked=false;
            }
        }
    }
    
    if(flagChekeados == 0){
        flagChekeados=1;
    }
    else{
        flagChekeados=0;
    }
}

function deshabilitarCheck(){
    var $reportes = $("#divScrollUltimosReportes");
    var $checkboxes = $("input[type='checkbox']", $reportes);
    $.each( $checkboxes, function(ind, checkbox){
        checkbox.disabled = true;
    });
}

function habilitarCheck(){
    var $reportes = $("#divScrollUltimosReportes");
    var $checkboxes = $("input[type='checkbox']", $reportes);
    $.each( $checkboxes, function(ind, checkbox){
        checkbox.disabled = false;
    });
}

function deseleccionarRad(){
    var divUltimosReportes = document.getElementById("divScrollUltimosReportes");
    var listaInputs = divUltimosReportes.getElementsByTagName("input");
    for(i=0;i < listaInputs.length;i++){
        input = listaInputs[i];
        if(input.type=="radio"){
            input.checked=false;
        }
    }
}

function deshabilitarRad(){
    return;
    var divUltimosReportes = document.getElementById("divScrollUltimosReportes");
    var listaInputs = divUltimosReportes.getElementsByTagName("input");
    for(i=0;i < listaInputs.length;i++){
        input = listaInputs[i];
        if(input.type=="radio"){
            input.disabled=true;
        }
    }
}

function habilitarRad(){
    return;
    var divUltimosReportes = document.getElementById("divScrollUltimosReportes");
    var listaInputs = divUltimosReportes.getElementsByTagName("input");
    for(i=0;i < listaInputs.length;i++){
        input = listaInputs[i];
        if(input.type=="radio"){
            input.disabled=false;
        }
    }
}

function obtenerValorRadSeleccionado(){
    var $reportes = $("#divScrollUltimosReportes");
    var $radiobuttons = $("input[type='radio']", $reportes);
    
    for(i = 0; i < $radiobuttons.length; i++){
        radiobutton = $radiobuttons[i];
        if(radiobutton.checked==true){
            return radiobutton.value;
        }
    }
    return 0;
}

function obtenerMovilesCheckeados(){
    var $reportes = $("#divScrollUltimosReportes");
    var $checkboxes = $("input[type='checkbox']", $reportes);
    
    arrCheckeados = [];
    $.each( $checkboxes, function( ind, checkbox ){
        if ( checkbox.checked ){
            arrCheckeados.push(checkbox.value);
        }
    });
    
    strMoviles = $.trim( arrCheckeados.join(",") );

    // Si no hay moviles seleccionados y se viene un update...
    if ( strMoviles == "" && g_bIsDataUpdate ){
        strMoviles = "none";
    }
    return strMoviles;
}


function verMovilesSeleccionados(){
    flagGuardarPreferencias = true;
    deshabilitarCheck();
    strMoviles = obtenerMovilesCheckeados();
	mostrarMarcadoresAgrupados(strMoviles);
	centrarMovilesSeleccionados(strMoviles);
}

var g_arrLatestMovIDs = []; // Aca se va a conservar la lista de moviles mostrados entre zoom y zoom
var g_arrMarkersForMap = []; // Los marcadores que finalmente mostraremos en el mapa
var g_arrMarkersForMap_keys = [];
var g_arr_bMutexActive = {'grouping': false};
var g_arrZoomCache = [];

function mostrarMarcadoresAgrupados(strMoviles){
	var msg = typeof strMoviles;
    msg += (typeof strMoviles == 'string' 
        ? ":" + strMoviles 
        : typeof strMoviles == 'object'
            ? ( strMoviles == null ? ":null" : "{...}" )
            : ":other"
    );
    
	var iZoom = mapGetZoom();
	
	if(!g_arr_bMutexActive['grouping']){    
    
	    g_arr_bMutexActive['grouping'] = true; // Evito que se ejecute esta funcion de manera simultanea (concurrente)
		g_arrMarkersForMap = [];
        g_arrMarkersForMap_keys = [];
        
        var bError = false;
		var g_bDebug = false;
		if(typeof strMoviles == "string"){
            switch($.trim(strMoviles)){
                case '':
                    bError = true;
                    break;
                case 'all':
				    for (mo_id in markerV3data){
                        g_arrLatestMovIDs = [];
                        g_arrLatestMovIDs.push(mo_id);
                    }
                    break;
                case 'none':
				    g_arrLatestMovIDs = [];
                    limpiarMoviles();
                    g_arr_bMutexActive['grouping'] = false;
                    return;
                    break;
                default: // Cadena de moviles
				    g_arrLatestMovIDs = strMoviles.split(',');
                    break;
            }
        }
        else if(typeof strMoviles == "undefined"){
            g_bDebug = true;
        }
        else{
            bError = true;
        }

        if(bError){
            g_arr_bMutexActive['grouping'] = false;
            return;
        }

	    var oViewportBounds = getMapBounds();
		var arr_MovsInView = [];
          
        for(var i = 0; i < g_arrLatestMovIDs.length; i++){
            iCurrentID = Number(g_arrLatestMovIDs[i]);
            var oLatLng = { 
                "lat": markerV3data[iCurrentID].lat, 
                "lng": markerV3data[iCurrentID].lng
            };
            
            bConditions = [
                oLatLng.lat >= oViewportBounds.lat1,
                oLatLng.lat <= oViewportBounds.lat2,
                oLatLng.lng >= oViewportBounds.lng1,
                oLatLng.lng <= oViewportBounds.lng2
            ];
            
			bInViewPort = true;
			for (var idx in bConditions){
                bInViewPort = bInViewPort && bConditions[idx];
            }
            
            if (bInViewPort) {
				arr_MovsInView.push(iCurrentID);
            }
        }
      
		//###
        // Logica de Agrupamiento por Geocercania
        //###
		var arrTipoPto = [];
		var arrIDPolygonosYRectas = [];
		if(arr_MovsInView.length > 0){

            var iCnt = 0, iCurrentID = null;
            var iDistance = getEscala(iZoom);
            
			limpiarMoviles();
			
            for(i = 0; i < arr_MovsInView.length; i++){
				
				iCurrentID = arr_MovsInView[i];

					if(iZoom >= g_iZoomSpreadThreshold){ // Si hay mucho zoom se muestran todos los puntos
						g_arrMarkersForMap.push( cloneObject(markerV3data[iCurrentID]));
					}
					else{
						if(iCnt == 0){ // Si es el 1er elemento de la lista va directamente al mapa
							g_arrMarkersForMap.push(cloneObject(markerV3data[iCurrentID]) );
						}
						else{
							var bIsFarFromAll = true, bIsFarFromThis = true;
							//Chequeo que este lo suficientemente distante de los demas puntos del mapa a mostrar.
							//En cuanto encuentro un punto cerca, lo agrupo a este y DEJO de buscar porque no tiene sentido.
							for(var j = 0; ( j < g_arrMarkersForMap.length) && bIsFarFromAll; j++ ){
								bIsFarFromThis = true;
								var iKm = dista(
									markerV3data[iCurrentID].lat, g_arrMarkersForMap[j].lat,
									markerV3data[iCurrentID].lng, g_arrMarkersForMap[j].lng
								);
	
								if((iKm * 1000) < (iDistance * 0.75)){
									
									//-- Si el circulo/poligono/recta dibujado en el mapa paso a formar parte de un grupo lo elimino --//
									if((iCurrentID == referenciaSelect['id']) || (g_arrMarkersForMap[j].ID == referenciaSelect['id'])){
										newTracer.deleteReferenciaSelect();	
									}
									//-- --//
									
									//##-- Se define los tipos de cada grupo --##//
									if(typeof(arrTipoPto[j]) == 'undefined'){
										arrTipoPto[j] = markerV3data[iCurrentID].tipoPto;	
									}
									if(arrTipoPto[j].indexOf(markerV3data[iCurrentID].tipoPto) == -1){
										arrTipoPto[j]+= ','+markerV3data[iCurrentID].tipoPto;	
									} 
									if(arrTipoPto[j].indexOf(g_arrMarkersForMap[j].tipoPto) == -1){
										arrTipoPto[j]+= ','+g_arrMarkersForMap[j].tipoPto;	
									}
									//##-- --##//  
									
									bIsFarFromThis = false;
									g_arrMarkersForMap[j].grouped++;
									
									// Calculo de latitud minima
									if (typeof g_arrMarkersForMap[j].minLat == 'undefined' ) {
										g_arrMarkersForMap[j].minLat = Math.min(
											markerV3data[iCurrentID].lat,
											g_arrMarkersForMap[j].lat
										);
									} else {
										g_arrMarkersForMap[j].minLat = Math.min(
											markerV3data[iCurrentID].lat,
											g_arrMarkersForMap[j].minLat
										);
									}
									
									// Calculo de latitud maxima
									if ( typeof g_arrMarkersForMap[j].maxLat == 'undefined' ) {
										g_arrMarkersForMap[j].maxLat = Math.max(
											markerV3data[iCurrentID].lat,
											g_arrMarkersForMap[j].lat
										);
									} else {
										g_arrMarkersForMap[j].maxLat = Math.max(
											markerV3data[iCurrentID].lat,
											g_arrMarkersForMap[j].maxLat
										);
									}
									
									// Calculo de longitud minima
									if ( typeof g_arrMarkersForMap[j].minLng == 'undefined' ) {
										g_arrMarkersForMap[j].minLng = Math.min(
											markerV3data[iCurrentID].lng,
											g_arrMarkersForMap[j].lng
										);
									} else {
										g_arrMarkersForMap[j].minLng = Math.min(
											markerV3data[iCurrentID].lng,
											g_arrMarkersForMap[j].minLng
										);
									}
									
									// Calculo de longitud maxima
									if ( typeof g_arrMarkersForMap[j].maxLng == 'undefined' ) {
										g_arrMarkersForMap[j].maxLng = Math.max(
											markerV3data[iCurrentID].lng,
											g_arrMarkersForMap[j].lng
										);
									} else {
										g_arrMarkersForMap[j].maxLng = Math.max(
											markerV3data[iCurrentID].lng,
											g_arrMarkersForMap[j].maxLng
										);
									}
								}
	
								bIsFarFromAll = bIsFarFromAll && bIsFarFromThis;
							}
	
							if(bIsFarFromAll){
								g_arrMarkersForMap.push( cloneObject(markerV3data[iCurrentID]) );
							}
						}
					}
					iCnt++;
            }
		
            // Referencio las keys de este array para las funciones que necesiten obtener data a traves de mo_id
            //-- g_arrMarkersForMap_keys de Moviles a Mostrar en el grupo 
			for(i = 0; i < g_arrMarkersForMap.length; i++){
				g_arrMarkersForMap_keys[g_arrMarkersForMap[i].ID] = i;
			}

            for (i = 0; i < g_arrMarkersForMap.length; i++ ) {
                var bGroup = false;
                if(g_arrMarkersForMap[i].grouped > 1 ) {
					bGroup = true;
					g_arrMarkersForMap[i].iconName = g_sGroupedIcon;
					//g_arrMarkersForMap[i].iconName = 'group_all.png';
					g_arrMarkersForMap[i].arrImages = arrTipoPto[i];
                    g_arrMarkersForMap[i].imgFolder = 'misc';
                    g_arrMarkersForMap[i].label = '(' + g_arrMarkersForMap[i].grouped + ')';
                    g_arrMarkersForMap[i].infoText = 
                        '<div style="width: 200px; height: 100px; overflow: auto;">' +
                            '<b>' + g_arrMarkersForMap[i].grouped + '</b> elementos agrupados en esta zona.' + '<br/>' +
                            '<br/>' +
                            'Haga <b>zoom</b> para ver detalles.' + 
                        '</div>';
                    
                    var fAvgLat = Math.average( [ g_arrMarkersForMap[i].minLat, g_arrMarkersForMap[i].maxLat] );
                    var fAvgLng = Math.average( [ g_arrMarkersForMap[i].minLng, g_arrMarkersForMap[i].maxLng] );
                    
                    g_arrMarkersForMap[i].lat = fAvgLat;
                    g_arrMarkersForMap[i].lng = fAvgLng;
                }
                
				//-- creamos icono para agrupar --//
				var arrItem = [];
				arrItem['id'] = g_arrMarkersForMap[i].ID;
				arrItem['lat'] = g_arrMarkersForMap[i].lat;
				arrItem['lng'] = g_arrMarkersForMap[i].lng;
				arrItem['iconId'] = g_arrMarkersForMap[i].iconID;
				arrItem['iconName'] = g_arrMarkersForMap[i].iconName;//Se define ICON del vehiculo en el mapa
				arrItem['label'] = g_arrMarkersForMap[i].label;
				arrItem['infoText'] = g_arrMarkersForMap[i].infoText;
				arrItem['imgFolder'] = g_arrMarkersForMap[i].imgFolder;
				arrItem['group'] = (bGroup ? {
                        minLat: g_arrMarkersForMap[i].minLat,
                        maxLat: g_arrMarkersForMap[i].maxLat,
                        minLng: g_arrMarkersForMap[i].minLng,
                        maxLng: g_arrMarkersForMap[i].maxLng
                    	}: null);
				arrItem['arrImages'] = g_arrMarkersForMap[i].arrImages;
				arrItem['tipo'] = g_arrMarkersForMap[i].tipo;
				arrItem['mtrs'] = g_arrMarkersForMap[i].mtrs;
				arrItem['coords'] = g_arrMarkersForMap[i].coords;
				arrItem['color'] = g_arrMarkersForMap[i].color;
				arrItem['id_evento'] = g_arrMarkersForMap[i].id_evento;
				arrItem['precision'] = g_arrMarkersForMap[i].precision;
				arrItem['um_grupo'] = g_arrMarkersForMap[i].um_grupo;
				
				markerV[g_arrMarkersForMap[i].ID] = addMarkerV3(arrItem);
				setMap(markerV[g_arrMarkersForMap[i].ID],g_arrMarkersForMap[i].ID);
				
				//-- Si el elemento Seleccionado (Circulo/Poligono/Recta) no se agrupo y se hab�a eliminado por una agrupacion anterior, se lo muestra
				if(g_arrMarkersForMap[i].ID == referenciaSelect['id'] && bGroup == false && referenciaSelect['objet'] == null){
					newTracer.click_referencia(arrItem);
				}
				//-- --//
            }
        }
        g_arr_bMutexActive['grouping'] = false;
    }
}

function centrarMovilesSeleccionados(strMoviles, iZoom){
    var url = "ajaxCentrarSeleccionados.php?strMoviles="+ strMoviles +"&p=0";
    if ( typeof iZoom != 'undefined' ) {
        url += "&zoom=" + iZoom;
    }
    //simple_ajax(url);
	ajax_sincronico(url);
}

function toogleGrupo(idGrupo){
    flagGuardarPreferencias = true;
    var divContenidoGrupo = document.getElementById('contenidoGrupo_'+idGrupo);
    var imgTituloGrupo = document.getElementById('img_'+idGrupo);
    var display = divContenidoGrupo.style.display;
    var estado=0;
    if(display == "block" || display==""){
        divContenidoGrupo.style.display="none";
        imgTituloGrupo.src="imagenes/raster/black/plus_12x12.png";
        estado = 0; // GRUPO CERRADO
    }else{
        divContenidoGrupo.style.display="block";
        imgTituloGrupo.src="imagenes/raster/black/minus_12x3.png";
        estado = 1; // GRUPO ABIERTO
    }

    url = "ajaxActualizarEstadoGrupo.php";
    $.ajax({
        "url": dominio+url,
        "data": {
            "idGrupo": idGrupo,
            "estado": estado,
            "p": 0
        },
        "type": "post",
        "success": function(data, status, jqxhr){}
    });
}

function zoomMoviles() {
	mapSetZoom(g_iZoomSpreadThreshold);
}

function addMarkerV3(arrItem) {
	
  	var idMovil = arrItem['id'];
	var lat = arrItem['lat'];
	var lng = arrItem['lng'];
	var idIcono	= arrItem['iconId'];
	var nameIcono = arrItem['iconName'];//Se define ICON del vehiculo en el mapa
	var label = arrItem['label'];
	var textoTab = arrItem['infoText'];
	var carpetaImg = arrItem['imgFolder'];
	var groupingInfo = arrItem['group'];
	var arrImages = arrItem['arrImages'];
	//var tipo = arrItem['tipo']; //hace referencia si idMovil < 0, a 1:Ciruclo(WP), 2:Poligonos(Zona), 3:Rectas(Rutas).
	//var mtrs = arrItem['mtrs']; //distancia del radio de un WP en metros.
	//var coords = arrItem['coords'];
				
   
    var srcIcono;
    if (idIcono == 1){
        switch (carpetaImg){
			case 'misc':
                srcIcono = "misc/" + nameIcono;
            break;
			default:
			    srcIcono = '1/'+carpetaImg+'/' + nameIcono;
            break;
        }
    }else if (idIcono == 2){
        srcIcono = "2/"+nameIcono;
    }else if (idIcono == 3){
        srcIcono = "3/"+nameIcono;
    }else{
        srcIcono = "4/"+nameIcono;
    }
	
	var arr = [];
	arr['lat'] = lat;
	arr['lng'] = lng;
	
	arr['icono'] = 'getImage.php?pathmode=rel&file='+srcIcono+'&caption='+decodeURI(label)+'&arrImage='+arrImages;
	var marker = mapMarker(arr);
	arrIconosMoviles[idMovil] = srcIcono;
	
    // Al hacer click sobre un icono ubicado en el mapa y Si no es un grupo de puntos
	if(groupingInfo === null){
		marker.events.register('click',this, function (e) { 
			crearTooltip(idMovil); 
                        g_iMovEnSeguimiento = idMovil;
            tempNomenclado='';
            lastInfoWindow = idMovil;
			
			if(referenciaSelect['id'] != idMovil){ //-- Es referencia --//
				if(arrItem['um_grupo'] == '-33'){
					//-- Pertenece al grupo de inteligentes, y se habilita popup para activar --//
					newTracer.wpInteligentePopup(arrItem, this);
				}
				else{
					newTracer.click_referencia(arrItem);	
				}
			}
        });
    }
	else{
		// Zoom maximo que encierra el grupo
	   	marker.events.register('click',this , function (e){
			var minLngLat = mapLatLng(groupingInfo.minLat, groupingInfo.minLng);
			var maxLngLat = mapLatLng(groupingInfo.maxLat, groupingInfo.maxLng);
			mapFitBounds(minLngLat.lon+','+minLngLat.lat+','+maxLngLat.lon+','+maxLngLat.lat, false);
			
			if(mapGetZoom() > 18){
				mapSetZoom(18);	
			}
       	});
    }
   
    return marker;
}

var lastInfoWindow;
function mostrarNomenclado(idMovil){
    if(document.getElementById("infoNomenclado_" + idMovil) != null){
        if(document.getElementById("spanNomenclado_" + idMovil) != null){
            document.getElementById("spanNomenclado_" + idMovil).innerHTML = document.getElementById("infoNomenclado_" + idMovil).innerHTML;
        }else{
            setTimeout("mostrarNomenclado("+idMovil+")",1000);
        }
    }else{
        setTimeout("mostrarNomenclado("+idMovil+")",1000);
    }
}
/*
var tempNomenclado ='';
function loadNomenclado(lat,lng,idMovil,callback) {
    var temp = {};
    var request = ajaxObject();
    var data_string = '';
    data_string += '[';
        data_string += '[';
            data_string += lat;
        data_string += ',';
            data_string += lng;
        data_string += ',';
            data_string += idMovil;
        data_string += ']';
    data_string += ']';
    url = dominio+'ajaxNomenclador.php?doble=false';
    request.open('POST', url, true);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=ISO-8859-1');
    request.onreadystatechange = function() {
        if(request.readyState == 4 && request.status == 200) {
            tempNomenclado = jQuery.parseJSON(request.responseText);
            callback();
        }
    };
    request.send('data=' + data_string);
}
*/
var ajaxActualizarArray;
function actualizarArray(){
	if(typeof(ajaxActualizarArray) != 'undefined'){
		ajaxActualizarArray.abort();
	}
	
	//-- Guardo valores seleccionados y en seguimiento --//
	guardarPreferencias();
	//-- --//
	
	ajaxActualizarArray = $.ajax( {
		url: dominio+"ajaxActualizarArrayRastreo.php",
		dataType: "json",
		type: "post",
		data: {
			"p": 0,
			"isUpdate": g_bIsDataUpdate,
			"conReferencias": g_bConReferencias,
			"criteria": g_iOrderingCriteria
		},
		error: function(){
			setTimeRefresh();
		},
		success: function(data, status, jqxhr){
			validateSession(data);
			actualizar(0,1);
		}
	});
}

var idUltimaAlerta = 0;
var g_bSoundPresent = false;
var g_oSoundObject = true;
if(permisos['alertas'] == true){//-- Si tiene Permiso de Alertas se activa Audio --//
	soundManager.setup( {
		"url": "swf/sm2/",
		"allowScriptAccess": "always",
		"useHTML5Audio": true,
		"preferFlash": false,
		
		"onready": function(){
			g_oSoundObject = soundManager.createSound({
				"id" : "mySound2",
				"url": "sounds/alertas/Sirena.wav",
				"volume": g_iAlertVolume,
				"autoLoad": true,
				"autoPlay": false,
				"onload": function() {
					g_bSoundPresent = true;
				}
			});
		}
	});
}


function setTimeRefresh(){
	setTimeout(actualizarArray, TRACE_REFRESH_INTERVAL);	
}

function actualizar(invertirOrden, primera){
    clearTimeout(mytime);
    if ( flagEnMedicion == 0){
        radSeleccionado = obtenerValorRadSeleccionado();
       var strMoviles = obtenerMovilesCheckeados();
            
        urlajax = "ajaxActualizarRastreo_new.php";
        $.ajax({
            url: dominio+urlajax,
            dataType: "json",
            type: "post",
            data: {
                "firstLoad": g_bIsFirstLoad,
                "strMoviles": strMoviles,
                "enSeguimiento": g_iMovEnSeguimiento,
                "radSeleccionado": radSeleccionado,
                "campo": campo,
                "orden": orden,
                "invertirOrden": invertirOrden,
                "p": 0 
            },
            success:  function(data, status, jqxhr){
			  	validateSession(data);
				newTracer.callback_success(data);
			},//Retorna Info a cargar panel IZQ
            error: function(){
				setTimeRefresh();
				newTracer.callback_error;
			},
			complete: function() {
				setTimeRefresh();
			} 
        });
    }
}

var timeTool;

function crearTooltip(idMovil){
	if(idMovil > 0){//-- Es Veh�culo --//
		$('#hidIdMovilConf').val(idMovil);
		if (g_bEmbedGPSPanel){
			$('#infoListado-upper').hide();
			$("#divScrollUltimosReportes").hide();
			$("#info").show();
		}
		else{// Mandar panel GPS a la derecha
			$("#main").addClass("info-gps-activo");
			$("#alertas").addClass("info-gps-activo");
			$("#infogps").show();
		}
		g_bGPSPanelActive = true;
		
		//-- Definir el alto del detalle de un movil (Solapa DER) --//
		var height_infogps = parseInt($("#rastreo_colIzqTabs").height())-52;
		//-- --//
		
		var urlInfoDatosGPS = "ajaxObtenerInfoDatosGps.php?idMovil=" + idMovil + "&p=0&height="+height_infogps;
		//simple_ajax(urlInfoDatosGPS);
		ajax_sincronico(urlInfoDatosGPS);
	}
	else{//-- Es referencia --//
		newTracer.cerrarPanelGPS();
		closeInfo();
	}
	
	return false;
}

function centrar(latitud,longitud,idMovil){
    if(ultimoCentrado==idMovil){
        flagAjustarZoom=1;
    }else{
        flagAjustarZoom=1;
        ultimoCentrado=idMovil;
    }
    simple_ajax("ajaxCentrar.php?latitud="+ latitud +"&longitud="+ longitud +"&idMovil="+idMovil+"&ajustarZoom="+ flagAjustarZoom +"&p=0");
}

function verificarCentrado(latMenor,latMayor,lonMenor,lonMayor){
	var arr = [latMenor,lonMenor,latMayor,lonMayor];
	var bounds = mapBounds(arr);
	mapFitBounds(bounds, false);
	if(mapGetZoom() > 14){
		mapSetZoom(14);
	}
}

function mostrarAlerta(msj){
    document.getElementById("divAlerta").innerHTML = msj;
    $('#divAlerta').fadeIn(250);
    setTimeout("ocultarAlerta()",3000);
}

function ocultarAlerta(){
    $('#divAlerta').fadeOut(250);
}

function showLoading(){
    $("#loading_logo").show();
}

function hideLoading(){
    $("#loading_logo").hide();
}

function seguirMovil(idMovil){
   	document.getElementById('divDatosInfoGps').innerHTML = "";
	document.getElementById('chk_' + idMovil).checked = true;
	flagGuardarPreferencias = true;
    flagChekeados = 0;
    modificarTextoCantidadSeleccionadosTodos();
    flagChekeados = 1;
    flagRad=1;
    modificarTextoCantidadSeleccionados(grupoPadre);
    pintarFila(idMovil);
    centrarMovilesSeleccionados(idMovil, g_iZoomSpreadThreshold);
    crearTooltip(idMovil);
    
    actualizar();
}


function ordenar(campoOrdenar){
    campo = campoOrdenar;
    actualizar(1,1);
}

function enviarHistorico(idMovil, matricula, bOpenPopup){
  
  $('#idMovil').val(idMovil);
  document.frmHistorico.target = "_self";
  document.frmHistorico.submit();
  
  /*if($('#hidIdMovilConf').val()){
        // Si matricula esta seteado es que se hizo click en el informe del dia
        // que aparece en el popup de rastreo.
        if (matricula != undefined) {
            $('#hidIdMovilConf').val(msj);
        }
        
        document.getElementById("idMoviles").value = $('#hidIdMovilConf').val();
        document.getElementById("movil_id").value = msj;
        if (typeof bOpenPopup != "undefined" && bOpenPopup )
        {
            document.frmHistorico.target = "newWindow";
            var win = window.open("", "newWindow", "toolbar=no,status=yes,location=yes");
            document.frmHistorico.action = dominio+"boot.php?c=historico&mode=popup&persistent=1";
            document.frmHistorico.submit();
		}
        else
        {
			document.frmHistorico.target = "_self";
            document.frmHistorico.submit();
        }
    }
    else{
        mostrarAlerta(msj);
    }*/
	
}

function guardarPreferencias(){
   // clearTimeout(timePreferencias);
    if(flagGuardarPreferencias == true){
        flagGuardarPreferencias = false;
        //SI HUBO ALGUN CAMBIO EL FLAG SE ACTIVA Y ME PERMITE GUARDAR SINO NO LLAMA AL AJAX
        idMovilSeguimiento = obtenerValorRadSeleccionado();
		var url = "ajaxGuardarMovilesSeleccionados.php";
        if(idMovilSeguimiento){
            simple_ajax(url+"?idMovilSeguimiento=" + idMovilSeguimiento + "&p=0");
        }
		else{
            strMoviles = obtenerMovilesCheckeados();
            $.ajax( {
                async:false,
				cache:false,
				"url": dominio+url,
                "type": "post",
                "data": {
                    "strMoviles": strMoviles,
                    "p": 0
                },
                "dataType": "json",
                "success": function( data ){
                    //timePreferencias = setTimeout( function(){ guardarPreferencias(); }, data.refreshInterval);
                }
            } );
        }
    }
}

function resaltarIcono(idMovil, latitud, longitud){
	var imgIcono = "";
	
	if (typeof(markerV[idMovil]) == 'object' && markerV[idMovil] != null) {
        imgIcono = "getImage.php?pathmode=rel&caption="+decodeURI(markerV3data[idMovil].label)+"&file="+arrIconosMoviles[idMovil];
		setMapIcon(idMovil,imgIcono);
	}
	else{
		var lat = (latitud!='')?latitud:markerV3data[idMovil].lat;
		var lng = (longitud!='')?longitud:markerV3data[idMovil].lng;
		
		var arrItem = [];
		arrItem['id'] = markerV3data[idMovil].ID;
		arrItem['lat'] = lat;
		arrItem['lng'] = lng;
		arrItem['iconId'] = markerV3data[idMovil].iconID;
		arrItem['iconName'] = markerV3data[idMovil].iconName;//Se define ICON del vehiculo en el mapa
		arrItem['label'] = markerV3data[idMovil].label;
		arrItem['infoText'] = markerV3data[idMovil].infoText;
		arrItem['imgFolder'] = markerV3data[idMovil].imgFolder;
		arrItem['group'] = null;
		arrItem['arrImages'] = markerV3data[idMovil].arrImages;
		arrItem['tipo'] = markerV3data[idMovil].tipo;
		arrItem['mtrs'] = markerV3data[idMovil].mtrs;
		arrItem['coords'] = markerV3data[idMovil].coords;
		arrItem['color'] = markerV3data[idMovil].color;
		arrItem['id_evento'] = markerV3data[idMovil].id_evento;
		arrItem['precision'] = markerV3data[idMovil].precision;
		arrItem['um_grupo'] = markerV3data[idMovil].um_grupo;
		markerV[idMovil] = addMarkerV3(arrItem);
		
		setMap(markerV[idMovil],idMovil);
		
	}
    
}

function a(e){
    if (window.event){
        if (window.event.keyCode==13) actualizar();
    }else if (e){
        if(e.which==13) actualizar();
    }
}

function ajaxObject() {
    var xmlhttp = false;
    try {
        xmlhttp = new XMLHttpRequest();
    }
    catch(e) {
        try {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        catch(e) {
            xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
        }
    }
    return xmlhttp;
}

//LISTA MOVILES

function cambiarEstado(idGrupo){
    
    showLoadingLeftCol();
    flagCambiarEstado = true;
    objCheck = document.getElementById("imgChkGrupo_" + idGrupo);
    var strMovilesChekeadosGrupo = 0;
    if(objCheck.name == "img1"){
        flagChekeados = 0;
        seleccionarChecksGrupo(idGrupo);
    }else{
        if(objCheck.name == "img2"){
            flagChekeados = 0;
            seleccionarChecksGrupo(idGrupo);
        }else{
            flagChekeados = 1;
            seleccionarChecksGrupo(idGrupo);
        }
    }

	// Que solo actualice el array cuando hay cambios.
	actualizarArray();
    verMovilesSeleccionados();
}

function seleccionarChecksGrupo(idGrupo){
    
    var $group = $("#contenidoGrupo_" + idGrupo);
    var $checkboxes = $("input[type='checkbox']", $group);
    var cantChecksGrupo = 0;
    var cantChekeados = 0;
    var mo_id = null;
    
    $.each( $checkboxes, function(ind, checkbox){
        
        cantChecksGrupo++;
        mo_id = checkbox.getAttribute("data-id");
        
        if ( flagChekeados == 0 )
        {
            checkbox.checked = true;
            $("#fakecheck_" + mo_id).css( { "visibility": "visible" } );
        }
        else 
        {
            checkbox.checked = false;
            $("#fakecheck_" + mo_id).css( { "visibility": "hidden" } );
        }
    } );
    
    if(flagChekeados == 0){
        flagCheckeados = 1;
    }
    else{
        flagChekeados = 0;
    }
    
    newTracer.actualizarTextoCantidadSeleccionados(idGrupo);
    cantChekeados = obtenerCantidadChekeados(idGrupo);
}

function actualizarCheckGrupo(idGrupo, cantChecksGrupo, cantChekeados){
    objCheck = document.getElementById("imgChkGrupo_" + idGrupo);

    if(cantChekeados <= 0){
        objCheck.src = "imagenes/check1.png";
        objCheck.name = 'img1';
    }else{
        if(cantChekeados < cantChecksGrupo){
            objCheck.src = "imagenes/check2.png";
            objCheck.name = 'img2';
        }else{
            objCheck.src = "imagenes/check3.png";
            objCheck.name = 'img3';
        }
    }
}

function obtenerCantidadChekeados(idGrupo){
    
    var $group = $("#contenidoGrupo_" + idGrupo);
    var $checkboxes = $("input[type='checkbox']", $group);
    var cantidadChekeados = 0;
    
    flag = false;
    $.each( $checkboxes, function( ind, checkbox ){
        if (!flag){
            flag = true;
        }
        if ( checkbox.checked ){
            cantidadChekeados++;
        }
    } );
    
    return cantidadChekeados;
}

function modificarTextoCantidadSeleccionados(idGrupo){
    var cant=0;
    if(flagRad == 0){
        cant = obtenerCantidadChekeados(idGrupo);
    } 
    else{
        cant = 1;
    }

    var cantTot = obtenerCantidadMovilesGrupo(idGrupo);
    
    if ( $("#spanCantMovilesSeleccionados_" + idGrupo) ){
        $("#spanCantMovilesSeleccionados_" + idGrupo).html(cant);
    }
}

function obtenerCantidadMovilesGrupo(idGrupo){
    var cantChecksGrupo = 0;
    
    if ( $("#contenidoGrupo_" + idGrupo) ){
        var $group = $("#contenidoGrupo_" + idGrupo);
        var $checkboxes = $("input[type='checkbox']", $group);
        
        cantChecksGrupo = $checkboxes.length;
    }
    
    return cantChecksGrupo;
}

function listado(id){
    $(".current").removeClass('current');
    if (id == 0) {
        document.getElementById("divScrollUltimosReportes").style.display = 'none';
        document.getElementById("info").style.display = 'block';
    } else if (id == 1){
        $(".tab2").removeClass('current');
        $(".tab3").removeClass('current');
        $(".tab4").removeClass('current');
        $(".tab1").addClass('current');
        $(".tab11").addClass('current');
    } else if (id == 3){
        document.getElementById("infoReferencias").style.display = 'block';
        document.getElementById("info").style.display = 'none';
        document.getElementById("infoViajesD").style.display = 'none';
        document.getElementById("infoListado").style.display = 'none';
        $(".tab2").removeClass('current');
        $(".tab1").removeClass('current');
        $(".tab3").removeClass('current');
        $(".tab4").addClass('current');
        $(".tab11").removeClass('current');
    } else {
        $(".tab2").removeClass('current');
        $(".tab1").addClass('current');
        $(".tab3").addClass('current');
        $(".tab4").removeClass('current');
        $(".tab11").removeClass('current');
    }
}

function ocultarPanel(){
    if ($("#colIzq").css('display') == 'block'){
        $("#colIzq").css('display', 'none');
        $("#main").addClass("seccion-izq-inactiva");
        $("#alertas").addClass("seccion-izq-inactiva");
		$('#imgPanel img').attr('src','imagenes/raster/black/flecha16b.png');
    }
    else{
        $("#colIzq").css('display', 'block');
		$("#alertas").removeClass("seccion-izq-inactiva");
        $("#main").removeClass("seccion-izq-inactiva");
		$('#imgPanel img').attr('src','imagenes/raster/black/flecha16i.png');
    }
	
    resize();	
}

function cambiarColorFondoGrupo(grupo, color){
    tituloGrupo = document.getElementById('tituloGrupo_' + grupo);
    if(tituloGrupo){
        tituloGrupo.style.backgroundColor = color;
    }
}

function cambiarFondoGrupoEnvioMail(grupo){
    imgMailGrupo = document.getElementById('imgMailGrupo_' + grupo);
    if(imgMailGrupo){
        imgMailGrupo.style.visibility = "visible";
    }
}

function actualizarCheckTotal(){
    var img = document.getElementById("divScrollUltimosReportes").getElementsByTagName('img');
    for(var i = 0; i < img.length; i++) {
        if(img[i].name=="img1" || img[i].name=="img2" || img[i].name=="img3"){
            if(flagChekeados == 1){
                img[i].src = 'imagenes/check3.png';
                img[i].name = 'img3';
            }else{
                img[i].src = 'imagenes/check1.png';
                img[i].name = 'img1';
            }
        }
    }
}

function modificarTextoCantidadSeleccionadosTodos(flag){
    return; 
    if (flag) {
        if (flagChekeados == 0){
            flagChekeados = 1;
        }
        else {
            flagChekeados = 0;
        }
    }
    var arrGrupos = strGrupos.split(",");
    if(arrGrupos.length > 0){
        for (grupo in arrGrupos){
            labelMoviles = document.getElementById("spanCantMovilesTotalGrupo_" + parseInt(arrGrupos[grupo]));
            labelMovilesSeleccionados = document.getElementById("spanCantMovilesSeleccionados_" + parseInt(arrGrupos[grupo]));
            if(flagChekeados == 0){
                labelMovilesSeleccionados.innerHTML = "0";
            }else{
                labelMovilesSeleccionados.innerHTML = labelMoviles.innerHTML;
            }
        }
    }
}

var grupoPadre = 0;
function almacenarGrupo(idGrupo){
    grupoPadre = idGrupo;
}

function mostrarAlertasTotales(cantidad){
    var span = 	document.getElementById("numeroAlertaGeneral");
    var div = document.getElementById("alertasGeneral");
    var img = document.getElementById("imgAlertaGeneral");
    span.innerHTML = cantidad;
    if(cantidad > 0){
        div.style.color = "#EBCD50";
        img.style.display = "block"
        span.style.fontWeight = "bold";
    }else{
        div.style.color = "#FFFFFF";
        img.style.display = "none"
        span.style.fontWeight = "300";
    }
}


var on = 0;
function alertaVisual(){
    var h1Rastreo = document.getElementById("h1Rastreo");
    if(on == 0){
        h1Rastreo.setAttribute ("class","h1Alerta");
        h1Rastreo.setAttribute ("className","h1Alerta");
        on = 1;
    }else{
        h1Rastreo.setAttribute ("class","");
        h1Rastreo.setAttribute ("className","");
        on = 0;
    }
}


var timeAlerta;
var fechaAlertaApagada = 0;
function mostrarAlertaVisual(){
    timeAlerta = setInterval("alertaVisual()",500);
}

function ocultarAlertaVisual(){
    clearInterval(timeAlerta);
    fecha = new Date().getTime();//milisegundos
    fechaAlertaApagada = fecha / 1000;
}

function ocultaTodo() {
    if (!buscaFiltro && !isDisplay && $('#auto_txtBuscar').val() == ''){
        for (marker in markerV){
            deleteMap(markerV[marker]);
        }
    buscaFiltro = false
	newTracer.deleteReferenciaSelect(); 
	}
}

//SI SE ESTA MOSTRANDO UNA ALERTA, AL CLICKEAR EN CUALQUIER PARTE DEL BODY SE OCULTA.
var buscarHabilitado = true;
var buscaFiltro = false;
var isDisplay;

function mostrarTooltip() {
    $("#" + idMovil).click(function() {
        var img = $(this);
        img.parents("tr").fadeOut(function()  {
            img.data("tooltip").hide();
        });
    });
		
}

function onload_rastreo(){	
	latDefecto = $('#hidLatDefecto').val();
    lngDefecto = $('#hidLngDefecto').val();
    zoomDefecto = $('#hidZoomDefecto').val();
    isDisplay = ($('#HidPopup').val() == 0) ? true : false;
	
    $('#agregarRef').click(function(){
        mostrarPopup('boot.php?c=abmReferencias&ref=abmViajes&action=popup&lt=0&lg=0&idMovil=0');
    })
	
    $('#btnProgramacion').click(function(){
        if ($('#hidIdMovilConf').val() > 0) {
            mostrarPopupRastreo('boot.php?c=verificarEquipo&popup=1&idM=' + $('#hidIdMovilConf').val());
        } else {
            alert("Debe seleccionar un movil del listado.")
        }
			
    })
	
	$('#btnArribos').click(function(){
        mostrarPopupRastreo('boot.php?c=tableroArribos&popup=1&method=popup');
    })
	
    $('#btnPartidas').click(function(){
        mostrarPopupRastreo('boot.php?c=tableroPartidas&popup=1&method=popup');
    })
	
    //setTimeout("mostrarAlertas()",7000);
}

function btnConf() {
    if ($('#hidIdMovilConf').val() > 0) {
		mostrarPopup('boot.php?c=abmMoviles&action=popup&ref=abmMoviles&idM=' + $('#hidIdMovilConf').val(),480,380);
    } else {
        alert("Debe seleccionar un movil del listado.");
    }
}

function btnHorario() {
    if ($('#hidIdMovilConf').val() > 0) {
        ModificarHorario($('#hidIdMovilConf').val());
    } else {
        alert("Debe seleccionar un movil del listado.")
    }
}

function btnProgramacionEquipos(){
    if ($('#hidIdMovilConf').val() > 0) {
        var urlVerificarEquipo;
        urlVerificarEquipo = 'boot.php?c=verificarEquipo&action=popup&idM='+$('#hidIdMovilConf').val();
        mostrarPopup(urlVerificarEquipo);
    } else {
        alert("Debe seleccionar un movil del listado.")
    }
}

function ModificarHorario(id_mo){
    mostrarPopup('boot.php?c=abmEquiposMoviles&action=popup&id='+id_mo,900,380);
}

function modificarGrupo(idG) {
    mostrarPopupRastreo('boot.php?c=abmGrupoMoviles&popup=1&idG='+idG,580,360);
}

function buscarDireccion() {
    var direccion = $('#txtBuscar').val();
	
	/*if (!direccion) {
        alert("Debe completar el campo antes de buscar.")
        return false;
    }*/
	
	geocodificarDireccion(direccion);
}

function agregarMarcadorDireccion(lat,lng) {
    
	cerrarPopup();
	var txt = '<a href="javascript:agregarRef('+lat+','+lng+',0);" >'+arrLang['agregar_geocerca']+'</a>'
	
	var arr = [];
	arr['lat'] = lat;
	arr['lng'] = lng;
	arr['icono'] = 'getImage.php?pathmode=rel&file=1/referencias/ref-zonaInteres.png&caption=';
	setMap(mapMarker(arr),false);
	
	mapSetZoom(g_iZoomSpreadThreshold);
	mapSetCenter(lat, lng);
	abrirPopup(lat, lng, txt);
}


function retornoPopup(data){}

var hy_id = 0

function cargarAlerta(i,nombre,id, movil,confirmacion) {
    var div = $('<div>');
    var div1 = $('<div>');
    var div2 = $('<div>');
    div.addClass('divAlertaRastreo_div');
    if (confirmacion == 1) {
        var divImg = $('<div>');
        divImg.css('text-align','right');
        divImg.css('font-size','10px');
        divImg.text('Cerrar');
        divImg.click(function() {
            ocultarAlertaDiv(id);
        });
        div.append(divImg);
    } else {
        div.css('background-color','#3869B1');
        setTimeout("ocultarAlertaDiv("+id+")",5000);
    }
	
    div1.text(nombre);
    div.append(div1)
    div2.text(movil);
    div.append(div2)
	
    div.attr('id','div_alerta_' + id)
    $('#divAlertaRastreo').show();
    $('#divAlertaRastreo').append(div);
}

function ocultarAlertaDiv(id){
    $('#div_alerta_' + id).fadeOut(250);
}

function closeInfo() {
	$("#hidIdMovilConf").val(0);
    $("#divScrollUltimosReportes").css('display', 'block');
    $("#infoListado-upper").css('display', 'block');
    $("#info").css('display', 'none');
    g_bGPSPanelActive = false;
}

function agrandarVisorAlertas() {
    var height = $("#alertas-contenido").css("height");
    var n = parseInt(height.substr(0, height.indexOf("px"))); 
    n = n + 75;
    if (n < 100) {
        n = 100;
    }
    if (n > 300) {
        n = 300;
    }
    $("#alertas-contenido").css("height", n + "px");
}
function confirmarAlerta(filaid, ids, mot_conf) {
    var url = "ajaxConfirmarAlerta.php";
    
    $.ajax( {
        "url": url,
        "type": "post",
        "dataType": "json",
        "data": {
            "ids": ids,
            "motivo": mot_conf
        },
        "success": function(data){
            if (data.msg == true){
                delete g_arrAlertas["alert_id_" + filaid];
                $("#alerta_" + filaid).css('display', 'none');
                g_iCantFilasAlertas--;
                $("#cantidad_de_alertas").html( g_iCantFilasAlertas );
				$("#notificationsCountValueAlertas").html( g_iCantFilasAlertas );
            }
        }
	});
}

function achicarVisorAlertas() {
    var height = $("#alertas-contenido").css("height");
    var n = parseInt(height.substr(0, height.indexOf("px"))); 
    n = n - 75;
    if (n < 50) {
        n = 0;
    }
    $("#alertas-contenido").css("height", n + "px");
}

function asignarGrupo(idG) {
    mostrarPopupRastreo('boot.php?c=aMovilesUsuariosMasivo&popup=1&idG='+idG,700,500);
}


function mostrarInfoPicture(idMovil){
	simple_ajax("ajaxObtenerInfoPicture.php?idMovil=" + idMovil + "&p=0");
}

function cerrarPicture(){
	radSeleccionado=0;
	$('#pictureInPicture').animate({
			opacity: "0"
		}, 500, function() {document.getElementById("pictureInPicture").style.visibility="hidden";} 
	);	
}

var pipMoviles = [];
var pipMap = [];
var pipMarker = [];
var flightPath = [];
var actualizarPip = false;

function cerrarPicture(idMovil) {
	$("#pictureInPicture"+idMovil).remove();
	if (pipMoviles.length > 0)
	{
		for (i = 0; i < pipMoviles.length; i++) {
			if (pipMoviles[i] == idMovil) {
				pipMoviles.splice(i,1);
			}
		}
	}
}

function actualizarPIP() {
	if (pipMoviles.length > 0)
	{
		for (i = 0; i < pipMoviles.length; i++) {
			if (i == 0) {
				strMoviles = pipMoviles[i];
			} else {
				strMoviles = strMoviles + "," + pipMoviles[i];
			}	
		}
		simple_ajax("ajaxObtenerInfoPicture.php?idMovil=" + strMoviles + "&p=0");
		setTimeout("actualizarPIP()", PIP_REFRESH_INTERVAL);
	}
	else {
		clearTimeout("actualizarPIP()");
		actualizarPip = false;
	}
}

function seguirMovilinPicture(idMovil, lat, lng){
	
	var strMoviles = "";
	for (i = 0; i < pipMoviles.length; i++) {
		if (pipMoviles[i] == idMovil) {
			return;
		}
	}
    
	pipMoviles.push(idMovil);

	for (i = 0; i < pipMoviles.length; i++) {
		if (i == 0) {
			strMoviles = pipMoviles[i];
		} else {
			strMoviles = strMoviles + "," + pipMoviles[i];
		}	
	}
	
	var str = '';
    
    str += '<div id="pictureInPicture'+idMovil+'" class="pictureInPicture ui-draggable">';
        str += '<p id="divTituloPicture'+idMovil+'" class="tituloPictureInPicture">';
            str += '<span id="divCerrarPicture">';
                str += '<a href="javascript:cerrarPicture('+idMovil+');" style="float:right;text-align:right;">';
                    str += '<img src="imagenes/cerrar_black.png">';
                str += '</a>';
            str += '</span>';
            
            var movilInfo = newTracer.getInfoByMovID( idMovil );
            var movil_label = '&lt;'+arrLang['movil']+'&gt;';

			movil_label = movilInfo.movil;
            str += arrLang['movil']+': '+movil_label;

        str += '</p>';
        str += '<div id="mapPicture' + idMovil + '" style="width:300px;height:200px"></div>';
        str += '<div id="infoPicture' + idMovil + '" class="infoPictureStatusbar">';
            str += '<table border="0" width="100%" cellspacing = 0 cellpadding = 0>';
                str += '<tr bgcolor="#000000">';
                    str += '<td class="tdTitInfoPicture" width="70">';
                        str += '<b>'+arrLang['fecha']+'</b>';
                    str += '</td>';
                    str += '<td class="tdTitInfoPicture" width="150">';
                        str += '<b>'+arrLang['evento']+'</b>';
                    str += '</td>';
                    str += '<td class="tdTitInfoPicture" width="">';
                        str += '<b>'+arrLang['velocidad']+'</b>';
                    str += '</td>';
                str += '</tr>';
            str += '</table>';
        str += '</div>';
    str += '</div>';
    
    $("div#main").append(str);
	
	div = document.getElementById('mapPicture'+idMovil);
	//-- --//
    pipMap[idMovil] = new OpenLayers.Map(div,{
		   restrictedExtent:null
		   ,allOverlays:true
		   ,maxResolution:"auto"
		   ,maxZoomLevel: 6 
		   ,projection:"EPSG:900913"  
		   ,controls:[] 
    });
	
	var osm = new OpenLayers.Layer.OSM("OpenLayers OSM",[urlOpenStreetMaps]);
	//var osm = new OpenLayers.Layer.OSM();
	pipMap[idMovil].addLayer(osm);
    pipMap[idMovil].zoomToMaxExtent();
	pipMap[idMovil].setCenter(mapLatLng(lat,lng));
	//-- --//
	simple_ajax("ajaxObtenerInfoPicture.php?idMovil=" + idMovil + "&p=0");
	
    $("#pictureInPicture"+idMovil).draggable({ handle: "p"});
    $("div, p").disableSelection();
    $("div, div").disableSelection();
	
	if (actualizarPip == false) {
		actualizarPIP();
		actualizarPip = true;
	}
}

function showLoadingLeftCol() {
    $("#leftcol-overlay").show();
    $("#leftcol-overlay-loading").show();
}

function hideLoadingLeftCol() {
    $("#leftcol-overlay").hide();
    $("#leftcol-overlay-loading").hide();
}

function limpiarMoviles(){
	for (key in markerV){
		if(typeof(markerV[key] != null)){
			deleteMap(markerV[key]);
			markerV[key] = null;
		}
    }
	
	//deleteMap(referenciaSelect['objet']); //No eliminar figura geometrica xq en los refresh elimina el elemento.
}
