var marker = [];
var highlightCircle;
var markers = new Array();
var polyShapes;
var zoomDefecto = 11;
var imgW = 24;
var imgH = 35;

function Cargar() {
	var divMapa = document.getElementById("mapa25");
	resizeRef();
    if (divMapa){	
        CrearMapa(divMapa);
		mapControls();
		cargarOpcionesPermisosMapa();
		mapSetZoom(zoomDefecto);
		var cmbTipoReferencia=document.getElementById("cmbTipoReferencia"); 
        var valor=getValorReferencia();
		
		var puntos = document.getElementById("hidPuntos").value;
		if (valor == 1 && puntos != '' && puntos != ';'){
        	var lat = 0;
            var lng = 0;
            var arrPuntos = puntos.split(";");
            for (i = 0;i < arrPuntos.length;i++){
            	if(arrPuntos[i] != ";"){
                	var arrAux = arrPuntos[i].split(", ");
					if (arrAux.length == 2){
                    	lat = arrAux[0].substr(1,arrAux[0].length);
                        lng = arrAux[1].substr(0,arrAux[1].length-1);	
					}
				}
			}
            if (lat && lng){
            	mapSetZoom(11);
				mapSetCenter(lat, lng);
				marcador(lat,lng);
            }
		}
		else if (valor > 1){
			if (puntos != ""){
				markers = Array();
            	var arrPuntos = puntos.split(";");
                var iCentro = 0;
                for (i = 0;i < arrPuntos.length;i++){
                	if (arrPuntos[i] != ";"){
                    	var arrAux = arrPuntos[i].split(", ");
                        if (arrAux.length == 2){
                        	var lat = arrAux[0].substr(1,arrAux[0].length);
                            var lng = arrAux[1].substr(0,arrAux[1].length-1);
                            if (lat && lng){
                            	if (iCentro == 0){
                                	iCentro = 1;
                                    mapSetZoom(11);
									mapSetCenter(lat, lng);
								}
                                
								var arr = [];
								arr['lat'] = lat;
								arr['lon'] = lng;
								markers.push(arr);
							}
						}
					}	
				}
				drawingEnd();
			}
		}
		else{
        	mapSetZoom(11);
			mapSetCenter(latCentro,lngCentro);
        }
	}
}


function drawingEnd (evt){
	var valor = getValorReferencia();
	
	document.getElementById("hidPuntos").value = '';
	for(i=0; i<markers.length; i++){
		document.getElementById("hidPuntos").value+= markers[i]['lat']+', '+markers[i]['lon']+';';	
	}
	if(valor == 2){
		polyShapes = mapPolygon(markers);
		setMapObj(polygonLayer);
		controls['polygon'].deactivate(); //stops the drawing
	}
	else if(valor == 3 || valor == 4){
		polyShapes = mapPolyline(markers);
		setMapObj(lineLayer);
		controls['line'].deactivate(); //stops the drawing
	}
	
}   
	
function drawingPoint(e){
	var arr = Array();
	var clon = e.clone();
	clon.transform(new OpenLayers.Projection("EPSG:900913"),new OpenLayers.Projection("EPSG:4326"));
	arr['lat'] = clon.y;
	arr['lon'] = clon.x; 
	markers.push(arr);
}

function drawingPointMarker(e){
	polyShapes = e.feature;
	controls['point'].deactivate();
	var clon = e.feature.geometry.clone();
	clon.transform(new OpenLayers.Projection("EPSG:900913"),new OpenLayers.Projection("EPSG:4326"));
	
	var arr = Array();
	arr['lat'] = clon.y;
	arr['lon'] = clon.x; 
	markers.push(arr);
	highlightCurrentMarker("cmbRadioIngreso","#2529AC");		
}

function setChangePositionMarker(e){
	var clon = e.geometry.clone();
	clon.transform(new OpenLayers.Projection("EPSG:900913"),new OpenLayers.Projection("EPSG:4326"));
	markers[0]['lat'] = clon.y;
	markers[0]['lon'] = clon.x; 
	highlightCurrentMarker("cmbRadioIngreso","#2529AC");
	expandwidgetAuto();
}
/**/


function moverMapa(){
    document.getElementById("mapa25").style.display = "none";
    document.getElementById("mapa25").style.visibility = "visible";
    document.getElementById("mapa25").style.height = "300px";
}

function checkTipoReferencia(){
   	for(var key in controls){
	   controls[key].deactivate();
   	}
	deleteMap(highlightCircle);
	
	/**/
	var tipo=getValorReferencia();
   	if (tipo == 3 || tipo == 4){
   		document.getElementById("cmbRadioIngreso").disabled = false; 
		controls['line'].activate();
   	}
   	else if (tipo == 2){
        document.getElementById("cmbRadioIngreso").disabled = true; 
		controls['polygon'].activate();
   	}
   	else if (tipo == 1){
        document.getElementById("cmbRadioIngreso").disabled = false; 
		controls['point'].activate();
		controls['drag'].activate();
   	}
   	else{
	    document.getElementById("cmbRadioIngreso").disabled = false; 
	}
	/**/
}

function cambioRadio(){
    var valor=getValorReferencia();
    if (valor == 1 && markers[0]){
		highlightCurrentMarker("cmbRadioIngreso","#2529AC");
	}
	else if(valor == 3){
        //drawPoly();
    }
}

function highlightCurrentMarker(combo,color){// Punto Circular //
	deleteMap(highlightCircle);
	
	var cmbRadio=document.getElementById(combo);
	try {
        var valor=cmbRadio.options[cmbRadio.selectedIndex].value;
    } catch (e) {
        cmbRadio=document.getElementById("cmbRadioIngreso");
        var valor=cmbRadio.options[cmbRadio.selectedIndex].value;
    }
    
	highlightCircle = mapCircle(markers[0]['lat'], markers[0]['lon'], parseInt(valor), color, 0, 0.2);
    setMapObj(highlightCircle);
	document.getElementById("hidPuntos").value = markers[0]['lat']+', '+markers[0]['lon']+';';
}

	
function marcador(lat, lng){
	n = window.location.pathname.split("/",2);
	var dominioURL = n[1];
	
    var arr = [];
	arr['lat'] = lat;
	arr['lng'] = lng;
	arr['icono'] = '/'+dominioURL+'/imagenes/iconos/markersRastreo/1/referencias/ref-wp.png';
	arr['imgW'] = imgW;
    arr['imgH'] = imgH;
	marker['obj'] = mapMarkerGeometry(arr);
	marker['drag'] = mapCreateDragg(marker['obj']);
	
	if(perfilADT == true){
		mapSetDragg(marker['drag'],'off');
	}
	else{
		mapSetDragg(marker['drag'],'on');
	}
	
	var arr = [];
	arr['lat'] = lat;
	arr['lon'] = lng;
	markers.push(arr);
	highlightCurrentMarker("cmbRadioIngreso","#2529AC");
}


		
function IsNumeric(inputVal,sErrorMsg) {
    if (isNaN(parseFloat(inputVal))) {
        alert(sErrorMsg)
        return false;
    }
    return true
}
   		
function centrarDireccion(){
    var address = document.getElementById("txtDireccion_2").value;  																		
    var zoomCentrado = ( typeof zoomADT == 'undefined' )?13:16;	
	
	geocodificarDireccion(address);
}

function agregarMarcadorDireccion(lat,lng){
	limpiarMapa();
	checkTipoReferencia();
	controls['point'].deactivate();
	marcador(lat,lng);
	g_iZoomSpreadThreshold = 14;
	mapSetZoom(g_iZoomSpreadThreshold);
	mapSetCenter(lat, lng);
	expandwidgetAuto();
}
   	
function cambioReferencia(idReferencia){
    limpiarMapa();
	checkTipoReferencia();
    var valor=getValorReferencia();
    if(document.getElementById("spanNombreDeteccion")){
        if(valor==1){
            document.getElementById("spanNombreDeteccion").innerHTML=document.getElementById("hidNombreDeteccionCircular").value; 
    	}
		else{
            document.getElementById("spanNombreDeteccion").innerHTML=document.getElementById("hidNombreDeteccionOtros").value; 	
        }
	}
	
	document.getElementById("hidPuntos").value = '';
}	
   		
function limpiarMapa(){
    if(map){
		markers = new Array();
		/*if(polyShapes){
			if(typeof(polyShapes.layer) != 'undefined' && polyShapes.layer != null){
				polyShapes.layer.removeAllFeatures();
			}
			else{
				polyShapes.destroy();
			}
		}*/
		deleteMap_2(polyShapes);
		deleteMap(marker['drag']);
		deleteMap(marker['obj']);
    }	
}   		

$(document).ready(function(){
    Cargar();
	
	$('#btnNewPop').click(function(){
        mostrarPopup('boot.php?c=abmReferencias&action=popup');	
    });
});

function getValorReferencia(){
	var cmbTipoReferencia=document.getElementById("cmbTipoReferencia"); 
        
	if(typeof(cmbTipoReferencia.selectedIndex) == 'undefined'){// Si esta oculto el combo de tiporef
		var valor = cmbTipoReferencia.value;	
	}
	else{
		var valor=cmbTipoReferencia.options[cmbTipoReferencia.selectedIndex].value;
	}
	
	return valor;	
}

function setEnter(e){
	if(e.keyCode == 13){
		centrarDireccion();	
	}	
}

var ocultar_widget = false;
function expandwidgetAuto(){
	if(ocultar_widget == false && $('.link-grey span').attr('class').indexOf('widget-down') > 0){
		expandwidget();
	}
}
	
function expandwidget(){
	var $ide = '.link-grey span';
	var $ideA = '.link-grey';
	
	if($($ide).attr('class').indexOf('widget-down') > 0){ //Mostrar datos
		$($ideA).html($($ideA).html().replace(arrLang['mostrar_datos'],arrLang['ocultar_datos']))
		$($ide).attr('class',$($ide).attr('class').replace('widget-down','widget-up'));
		$('#DatosDeRefencia').show(500);
	}
	else{//Ocultar Datos
		$($ideA).html($($ideA).html().replace(arrLang['ocultar_datos'],arrLang['mostrar_datos']))
		$($ide).attr('class',$($ide).attr('class').replace('widget-up','widget-down'));
		$('#DatosDeRefencia').hide(500);
		ocultar_widget = true;
	}	
}



/** FUNCIONES PARA REFERENCIAS DE adt **/
function armarConsulta(){
	
	if($("#txtAltura").val()=="" || $("#txtAltura").val()==0){ 
		$("#txtAltura").addClass("pintadoAlerta");		
	}
	var provincia = '';
	if($("#cmbProvincia").val()!=0){ provincia = $("#cmbProvincia option:selected").text();}
	var consulta = $("#txtNombreDireccion").val() + ' ' + $("#txtAltura").val() + ' ' + $("#txtLocalidad").val() + ' ' + provincia + ' ' + $("#txtPais").val();
	$("#txtDireccion_2").val(consulta);
}

function despintarAltura(){
	$("#txtAltura").removeClass("pintadoAlerta");
}