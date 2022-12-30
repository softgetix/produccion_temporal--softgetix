var map;
var latDefecto;
var lngDefecto;
var zoomDefecto;

$(document).ready(function() {
	latDefecto = $('#hidLatDefecto').val();
	lngDefecto = $('#hidLngDefecto').val();
	zoomDefecto = $('#hidZoomDefecto').val();
	if(latDefecto == ''){latDefecto = -35.608418;}
	if(lngDefecto == ''){lngDefecto = -59.373161;}
	if(zoomDefecto == ''){zoomDefecto = 16;}
});
	
var markerV = [];
var imgW = 58;
var imgH = 78;

var pointLayer;
var lineLayer;
var polygonLayer;
	
var aux1 = (location.href).split('/');
if(aux1[0] == 'http:'){
	var urlOpenStreetMaps = "http://tile.openstreetmap.org/${z}/${x}/${y}.png";
}
else{
	var urlOpenStreetMaps = dominio+"openStreetMaps.php?zoom=${z}&x=${x}&y=${y}";
	//06012020
	var urlOpenStreetMaps = "https://tile.openstreetmap.org/${z}/${x}/${y}.png";


}
		
function CrearMapa(divMapa){
	if (typeof(map) == 'object' && map != null) { 
		map.destroy();
	} 
		
	//OpenLayers.ProxyHost = 'js/openLayers/proxy.php?url=';
	
	map = new OpenLayers.Map(
		divMapa,{
		   restrictedExtent:null
		   ,allOverlays:true
		   //,maxExtent: bounds 
		   ,maxResolution:"auto"
		   ,maxZoomLevel: 6 
		   ,projection:"EPSG:900913"
		   ,controls: [
		   		new OpenLayers.Control.Zoom()
				,new OpenLayers.Control.Navigation()
				//new OpenLayers.Control.TouchNavigation()
              	//new OpenLayers.Control.PanZoomBar()
				//new OpenLayers.Control.MousePosition()
              	//new OpenLayers.Control.Scale()
				//new OpenLayers.Control.ArgParser()
				//new OpenLayers.Control.Attribution()
		   	]  
    });
	
	var osm = new OpenLayers.Layer.OSM("OpenLayers OSM",[urlOpenStreetMaps]);
	//var osm = new OpenLayers.Layer.OSM();
	map.addLayer(osm);
    map.zoomToMaxExtent();
	mapSetCenter(latDefecto, lngDefecto);
	osm.setZIndex(-100); 
	
	/*****/
	var stylePoint = OpenLayers.Util.extend({
    	cursor:'inherit'
		,externalGraphic:'imagenes/iconos/markersRastreo/1/referencias/ref-wp.png'
		,fillOpacity:1
		,graphicWidth: 24
        ,graphicHeight: 35
	});
	
	var stylePolyLine = OpenLayers.Util.extend({
    	fillColor:'#0082d8'
		,fillOpacity:2
		,strokeColor:'#0082d8'
		,strokeWidth:2
	});
	
	var stylePolyGon = OpenLayers.Util.extend({
    	fillColor:'#0082d8'
		,fillOpacity:0.3
		,strokeColor:'#0082d8'
		,strokeOpacity:2
        ,strokeWidth:0.8
	});
	
	pointLayer = new OpenLayers.Layer.Vector("Point Layer",{style: stylePoint});
    lineLayer = new OpenLayers.Layer.Vector("Line Layer",{style: stylePolyLine});
    polygonLayer = new OpenLayers.Layer.Vector("Polygon Layer",{style: stylePolyGon});
    map.addLayer(pointLayer, lineLayer, polygonLayer);
	/*****/
}


function mapControls(){
	controls = {
		line: new OpenLayers.Control.DrawFeature(lineLayer,OpenLayers.Handler.Path,{eventListeners:{"featureadded": drawingEnd},callbacks:{"point":drawingPoint}})
       	,polygon: new OpenLayers.Control.DrawFeature(polygonLayer,OpenLayers.Handler.Polygon,{eventListeners:{"featureadded": drawingEnd},callbacks:{"point":drawingPoint}})
       	,point: new OpenLayers.Control.DrawFeature(pointLayer,OpenLayers.Handler.Point)
	    ,drag: new OpenLayers.Control.DragFeature(pointLayer ,{onComplete: function(e){setChangePositionMarker(e);}})
	};

	for(var key in controls) {
		map.addControl(controls[key]);
		
		if(key == 'point'){
			controls[key].events.register('featureadded', controls[key], function(f) {
				drawingPointMarker(f);	
			});
		}
	}
}

function mapGetZoom(){
	if(typeof(map) == 'object' && map != null){
		return map.getZoom();
	}
}

function mapSetZoom(arr){
	if(typeof(map) == 'object' && typeof(arr) == 'number' && map != null){
		map.zoomTo(arr);
	}
}

function mapGetBounds(){
	if(typeof(map) == 'object'){
		var bounds = map.calculateBounds();
		if(bounds != null){
			bounds.transform(new OpenLayers.Projection("EPSG:900913"),new OpenLayers.Projection("EPSG:4326"));
			var oMapBounds = {
				'lat1':	bounds.bottom,	//oSouthWest
				'lat2': bounds.top,		//oNorthEast
				'lng1': bounds.left,	//oSouthWest
				'lng2': bounds.right	//oNorthEast	
			 };
			return oMapBounds;
		}
	}
}

function mapFitBounds(arr,zoomExtent){
	var arr_bounds = arr.split(',');
	var bounds = new OpenLayers.Bounds(arr_bounds[0],arr_bounds[1],arr_bounds[2],arr_bounds[3]);
	
	arr_center = bounds.getCenterLonLat();
	map.setCenter(new OpenLayers.LonLat(arr_center.lon,arr_center.lat));
	zoomExtent = (zoomExtent == false)?false:true;
	map.zoomToExtent(bounds, zoomExtent);
}

function mapBounds(arr){//Busca el punto medio entre varios ptos
	var bounds = new OpenLayers.Bounds();
	for(i=0; i < arr.length; i=i+2){
		bounds.extend(mapLatLng(arr[i],arr[i+1]));
	}
	return bounds.toBBOX();
}

function mapLatLng(lat, lng){
	return new OpenLayers.LonLat(lng,lat).transform(
                new OpenLayers.Projection("EPSG:4326")
				,map.getProjectionObject() // to Spherical Mercator Projection
              );	
}

function setMap(arr, idMovil){
	if(idMovil === false){
		marker = new OpenLayers.Layer.Markers('Markers');
		setMapObj(marker);
		marker.addMarker(arr);
	}
	else if(typeof(markerV[idMovil]) == 'object'){
		markerV[idMovil] = new OpenLayers.Layer.Markers('Markers');
		setMapObj(markerV[idMovil]);
		markerV[idMovil].setZIndex(1001); 
		markerV[idMovil].addMarker(arr);
	}
}

function setMapObj(obj){
	map.addLayer(obj);
}

function setMapIcon(idMovil,imgIcono){
	if(typeof(markerV[idMovil]) == 'object'){
		markerV[idMovil].markers[0].setUrl(imgIcono);
	}
}

function deleteMap(obj){
	
	if(typeof(obj) == 'object' && obj != null){
		obj.destroy();
	}
}

function deleteMap_2(obj){
	if(typeof(obj) == 'object' && obj != null){
		if(typeof(obj.layer) != 'undefined' && obj.layer != null){
			obj.layer.removeAllFeatures();
		}
		else{
			deleteMap(obj);
		}
	}
}

function deleteMap_3(obj){
	if(typeof(obj) == 'object' && obj != null){
		if(typeof(obj.features) != 'undefined'){
			if(typeof(obj.features[0].layer) != 'undefined' && obj.features[0].layer != null){
				obj.features[0].layer.removeAllFeatures();
			}
		}
		else{
			deleteMap(obj);
		}
	}
}


function mapSetCenter(lat, lng){
	map.setCenter(mapLatLng(lat, lng));
}

function mapPanTo(lat, lng){
	map.panTo(mapLatLng(lat, lng));
}

function mapMarker(arr){
	var lat = arr['lat'];
	var lng = arr['lng'];
	var icono = arr['icono']?arr['icono']:'';
	/*
	var zIndexProcess = arr['zIndexProcess']?arr['zIndexProcess']:false;
	var draggable = arr['draggable']?arr['draggable']:false;
	var bouncy = arr['bouncy']?arr['bouncy']:false; 
    var dragCrossMove = arr['dragCrossMove']?arr['dragCrossMove']:false;
	var animacion = arr['animacion']?arr['animacion']:false;
	*/
	var size = new OpenLayers.Size(imgW,imgH);
	var offset = new OpenLayers.Pixel(-(size.w/2), -(size.h));
	var icon = new OpenLayers.Icon(icono,size,offset);
	return new OpenLayers.Marker(mapLatLng(lat,lng),icon);
}

function mapPolygon(path){
	var points = []
	for(i=0; i<path.length; i++){
		var LonLat = mapLatLng(path[i].lat,path[i].lon);
		points.push(new OpenLayers.Geometry.Point(LonLat.lon,LonLat.lat));
		//points.push(new OpenLayers.Geometry.Point(path[i].lon,path[i].lat));
	}
	
	var objetoGeometrico = polygonLayer;
	var polygon = new OpenLayers.Geometry.LinearRing(points);
	var ObjetoOpenlayer = new OpenLayers.Feature.Vector(polygon);
	objetoGeometrico.addFeatures([ObjetoOpenlayer]);
	//return objetoGeometrico;
	return ObjetoOpenlayer;
}

function mapPolyline(path, s_color){
	
	var stylePolyLine = OpenLayers.Util.extend({
    	fillColor:s_color?s_color:'#0082d8'
		,fillOpacity:2
		,strokeColor:s_color?s_color:'#0082d8'
		,strokeWidth:2
	});
	
	var points = []
	for(i=0; i<path.length; i++){
		var LonLat = mapLatLng(path[i].lat,path[i].lon);
		points.push(new OpenLayers.Geometry.Point(LonLat.lon,LonLat.lat));
	}
	
	var objetoGeometrico =  lineLayer;
	var line = new OpenLayers.Geometry.LineString(points);
	var ObjetoOpenlayer = new OpenLayers.Feature.Vector(line,null,stylePolyLine);
	objetoGeometrico.addFeatures([ObjetoOpenlayer]);
	return ObjetoOpenlayer;
}

function mapCircle(lat, lng , metros, s_color, s_weight, f_opacity){
	var estilo = OpenLayers.Util.extend({
    	fillColor: s_color?s_color:'#FF0000'
		,fillOpacity: f_opacity?f_opacity:0.2
		,strokeColor: s_color?s_color:'#FF0000'
		,strokeWidth: s_weight?s_weight:0
		,graphicWidth: imgW
        ,graphicHeight:imgH
	});
	
	var objetoGeometrico = new OpenLayers.Layer.Vector("Point Layer",{style: estilo});
	var LonLat = mapLatLng(lat,lng);
	var point = new OpenLayers.Geometry.Point(LonLat.lon,LonLat.lat);
	var sunpoly = OpenLayers.Geometry.Polygon.createRegularPolygon(point, metros, 100);
	var ObjetoOpenlayer = new OpenLayers.Feature.Vector(sunpoly);
	objetoGeometrico.addFeatures([ObjetoOpenlayer]);
	return objetoGeometrico;
}

/**/
function mapMarkerGeometry(arr){
	var estilo = OpenLayers.Util.extend({
    	cursor:'inherit'
		,externalGraphic:arr['icono']
		,fillOpacity:1
		,graphicWidth: arr['imgW']?arr['imgW']:imgW
        ,graphicHeight: arr['imgH']?arr['imgH']:imgH
	});

	var vectors = new OpenLayers.Layer.Vector('Marker Dragg', {style: estilo});
	map.addLayers([vectors]);
	
	var LonLat = mapLatLng(arr['lat'],arr['lng']);
	var point = new OpenLayers.Geometry.Point(LonLat.lon,LonLat.lat);
	vectors.addFeatures([new OpenLayers.Feature.Vector(point)]);
	return vectors;	
}

function mapCreateDragg(obj){
	drag = new OpenLayers.Control.DragFeature(obj, {
		//autoActivate: true,
		onComplete: function(e){
			setChangePositionMarker(e);
		}
	});
	
	map.addControl(drag);
	return drag;
}

function mapSetDragg(obj, action){
	if(action == 'on'){
		obj.activate();	
	}
	else{
		obj.deactivate()	
	}
}

function cargarOpcionesPermisosMapa(){
	 /* codigo comentado
	var url = 'ajaxPermisosMapa.php';
    $.ajax( { 
        "url": dominio+url,
        "type": "post",
        "dataType": "json",
        "success": function( data ){
			validateSession(data);
            if ( data.viewMapControls ){
                var mapOptions = {
                    "mapTypeControlOptions": {
                        "position": google.maps.ControlPosition.TOP_LEFT,
                        "mapTypeIds": ["OSM", google.maps.MapTypeId.HYBRID]
                    },
                    "mapTypeControl": true
                };
                
                map.setOptions( mapOptions );
            }
        },
        "error": function( jqxhr, status, error )
        {
            debug.warn("Error al llamar a '" + url + "'.");
        }
    } );
	*/
}

function geocodificarDireccion(direccion){
	var url = 'ajax.php';
    $.ajax({ 
        "url": dominio+url,
        "type": "post",
        "dataType": "json",
		"data":({accion:"get-nomenclado-OpenStreetMaps",nomenclar:direccion}),
        "success": this.geocodificarDireccionSuccess,
		"error": this.geocodificarDireccionFailure 
    });
}
function geocodificarDireccionSuccess(response){
	if(response['status']){
		agregarMarcadorDireccion(response['lat'], response['lon']);
	}
	else{
		alert(arrLang['dir_no_encontrada']);
	}
}
function geocodificarDireccionFailure(response){
	alert(arrLang['dir_no_encontrada']);
}

/*
function geocodificarDireccion(direccion){
	//OpenLayers.ProxyHost = 'js/openLayers/proxy.php?url=';
	//OpenLayers.ProxyHost = "/cgi-bin/proxy.cgi?url=";
	
	OpenLayers.Request.POST({
    	async:false,
		//cache:false,
		url: "http://www.openrouteservice.org/php/OpenLSLUS_Geocode.php",
        scope: this,
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
		data: "FreeFormAdress=" + encodeURIComponent(direccion) + "&MaxResponse=1",
		success: this.requestSuccess,
		failure: this.requestFailure
	});
}
function requestSuccess(response) {
	var format = new OpenLayers.Format.XLS();
	var output = format.read(response.responseText);
	if(output.responseLists[0]){
		if(typeof(output.responseLists[0].features[0]) != 'undefined'){
			var geometry = output.responseLists[0].features[0].geometry;
			console.info(geometry.x+","+geometry.y);
			//agregarMarcadorDireccion(geometry.y, geometry.x);
		}
		else{
			alert("Disculpe, la direcci\u00f3n no pudo ser ubicada.");
		}
	}
	else{
		alert("Disculpe, la direcci\u00f3n no pudo ser ubicada.");
	}
	
	/*
	if(typeof google.maps.Geocoder == 'function'){
		var geocoder = new google.maps.Geocoder();	
	}
	if (geocoder){
	    geocoder.geocode({'address': direccion}, function (results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				var point = results[0].geometry.location;
				agregarMarcadorDireccion(point.lat(), point.lng());
			}
			else{
				alert(arrLang['dir_no_encontrada']);			
			}
		});
    }
	/**/
/*				
}
function requestFailure(response){
   	alert("No se obtuvieron resultados.");
}*/

/***** POPUP *****///http://stackoverflow.com/questions/7456205/how-to-add-a-popup-box-to-a-vector-in-openlayers
function abrirPopup(lat, lng, txt){
	//var popup = new OpenLayers.Popup.FramedCloud("Popup", mapLatLng(lat, lng), new OpenLayers.Size(200,100), "Text", null, true, true);
	
	popup = new OpenLayers.Popup("popup",
		mapLatLng(lat, lng),
    	null,
		txt,
		true,
		closePopup
	);
	popup.autoSize = true;
    //popup.maxSize = new OpenLayers.Size(100,50);
    popup.fixedRelativePosition = true;
    map.addPopup(popup);
}

function closePopup(e){
	if(typeof(popup) != 'undefined'){
		map.removePopup(popup);
		if(typeof(marker) != 'undefined'){
			deleteMap(marker);
		}
	}
}