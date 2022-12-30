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
var mapLabel = [];

var typeMap = 'OSM';

function getTypeMap(){
	var osmMapType = new google.maps.ImageMapType({
		getTileUrl: function(coord, zoom) {
			if(document.domain == '200.32.10.148'){
				var urlOpenStreetMaps = "http://tile.openstreetmap.org/"+zoom+"/"+coord.x+"/"+coord.y+".png";
			}
			else{
				var urlOpenStreetMaps = 'https://www.localizar-t.com/localizart/openStreetMaps.php?zoom='+zoom+'&x='+coord.x+'&y='+coord.y;	
			}
			return urlOpenStreetMaps;
		},
		tileSize: new google.maps.Size(256, 256),
		isPng: true,
		alt: "OpenStreetMap layer",
		name: "OSM",
		minZoom: 2,
		maxZoom: 20
	});	
	
	return osmMapType;
}

function CrearMapa(divMapa){
	var myLatLng = new google.maps.LatLng(latDefecto,lngDefecto);
	var myOptions = {
        zoom: zoomDefecto * 1,
        center: myLatLng,
		mapTypeId:typeMap,
		mapTypeControl: false,
		disableDoubleClickZoom: true //cancelar Zoom dblClick
		
		,panControl: true,
		panControlOptions: {
			position: google.maps.ControlPosition.TOP_LEFT
		},
			
		zoomControl: true,
		zoomControlOptions: {
			style: google.maps.ZoomControlStyle.LARGE,
			position: google.maps.ControlPosition.TOP_LEFT//LEFT_CENTER
		}
	};	
	
	var osmMapType = getTypeMap();
	    
    map = new google.maps.Map(divMapa, myOptions);
	map.mapTypes.set(typeMap,osmMapType);
}

function mapGetZoom(){
	return map.getZoom();	
}

function mapSetZoom(arr){
	if(typeof(map) == 'object' && typeof(arr) == 'number'){
		map.setZoom(arr);	
	}
}


function mapGetBounds(){
	if(typeof(map) == 'object'){
		return map.getBounds();	
	}
}

function mapFitBounds(arr){
	map.fitBounds(arr);	
}

function mapBounds(arr){//Busca el punto medio entre varios ptos
	var bounds = new google.maps.LatLngBounds(arr);
	return bounds;
}

function mapLatLng(lat, lng){
	return new google.maps.LatLng(lat,lng)	
}

function setMap(arr){
	if(typeof(arr) == 'object'){
		arr.setMap(map);	
	}
}

function deleteMap(arr){
	if(typeof(arr) == 'object' && arr != null){
		arr.setMap(null);	
	}
}



function mapSetCenter(lat, lng){
	map.setCenter(mapLatLng(lat, lng));
}

function mapSetCenter2(arr){
	map.setCenter(arr);
}


function mapGetPoint(arr){
	return arr.getPosition();	
}


function mapSetPoint(pixelX,pixelY){
	return new google.maps.Point(pixelX,pixelY);
}

function mapPolygon(path, s_color, s_opacity, s_weight, f_color, f_opacity){
	s_color = s_color?s_color:'#0082d8';
	s_opacity = s_opacity?s_opacity:2;
	s_weight = s_weight?s_weight:0.8;
	f_color = f_color?f_color:'#0082d8';
	f_opacity = f_opacity?f_opacity:0.3;
	
	var polygon = new google.maps.Polygon({
		paths: [path],
		strokeColor: s_color,
		strokeOpacity: s_opacity,
		strokeWeight: s_weight,
		fillColor: f_color,
		fillOpacity: f_opacity
	});
	
	return polygon;
}

function mapPolyline(path, s_color, s_opacity, s_weight){
	s_color = s_color?s_color:'#0082d8';
	s_opacity = s_opacity?s_opacity:2;
	s_weight = s_weight?s_weight:2;
		
	var polyline = new google.maps.Polyline({
    	path: path,
   		strokeColor: s_color,
    	strokeOpacity: s_opacity,
    	strokeWeight: s_weight
  	});
	
	return polyline;
}

function mapCircle(ptoCenter, metros, s_color, s_opacity, s_weight, f_opacity){
	s_color = s_color?s_color:'#FF0000';
	s_opacity = s_opacity?s_opacity:2;
	s_weight = s_weight?s_weight:0;
	f_opacity = f_opacity?f_opacity:0.2;
	
	var circle = new google.maps.Circle({
      	strokeColor: s_color,
      	strokeOpacity: s_opacity,
      	strokeWeight: s_weight,
      	fillColor: s_color,
      	fillOpacity: f_opacity,
      	map: map,
      	center: ptoCenter,
	  	radius: metros
    });
	
    return circle;
	
}

function mapMarker(arr){
	var myLatLng = arr['myLatLng'];
	var icono = arr['icono']?arr['icono']:'';
	var zIndexProcess = arr['zIndexProcess']?arr['zIndexProcess']:false;
	var draggable = arr['draggable']?arr['draggable']:false;
	var bouncy = arr['bouncy']?arr['bouncy']:false; 
    var dragCrossMove = arr['dragCrossMove']?arr['dragCrossMove']:false;
	var animacion = arr['animacion']?arr['animacion']:false;
	
	var marker = new google.maps.Marker({
		position: myLatLng,
		icon: icono,
		zIndexProcess : zIndexProcess,
		draggable : draggable,
		bouncy : bouncy,
		dragCrossMove : dragCrossMove,
		animation: animacion

		
	});
	
	return marker;
}


function mapSetLabel(varLabel, pto, texto){
	varLabel = new Label({map: map});// Cargar etiqueta con la hora
	varLabel.bindTo('position',pto, 'position');
    
	var caractBlanco = '';
	var ie=(document.all)? true:false;
	if (ie){ 
		var hasta = texto.length;
		if(hasta < 10){hasta = 10;}
		for(i=0; i<=hasta; i++){
			caractBlanco+='&nbsp;';	
		}	
	}
	varLabel.set('text',caractBlanco.concat(texto));
	
	return varLabel;
		
}


function cargarOpcionesPermisosMapa(){
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
}

/***** DEFINIR FUNCIONES PARA ETIQUETA LABEL DEL MAPA *****/
function Label(opt_options) {
	
	this.setValues(opt_options);
	var span = this.span_ = document.createElement('span');
 	span.style.cssText = 'position: relative; left: -50%; top: 15px; fontSize:12px;  align:center; font-weight:bold; padding: 2px;  z-index:99999; white-space: nowrap; background-color: #FFFFFF;color:#333333;';//background-color: #666666;color:#FFFFFF; 
	
	var div = this.div_ = document.createElement('div');
 	div.appendChild(span);
	div.style.cssText = 'position: absolute; display: none';
	
};
/*
Label.prototype = new google.maps.OverlayView;

Label.prototype.onAdd = function() {
	var pane = this.getPanes().overlayLayer;
 	pane.appendChild(this.div_);

	var me = this;
 	this.listeners_ = [
   		google.maps.event.addListener(this, 'position_changed',
       	function() { me.draw(); }),
   		google.maps.event.addListener(this, 'text_changed',
       	function() { me.draw(); })
 	];
};

Label.prototype.onRemove = function() {
	this.div_.parentNode.removeChild(this.div_);

 	for (var i = 0, I = this.listeners_.length; i < I; ++i) {
   		google.maps.event.removeListener(this.listeners_[i]);
 	}
};

Label.prototype.draw = function() {
	var projection = this.getProjection();
 	var position = projection.fromLatLngToDivPixel(this.get('position'));

	var div = this.div_;
 	div.style.left = position.x + 'px';
 	div.style.top = position.y + 'px';
 	div.style.display = 'block';
	this.span_.innerHTML = this.get('text').toString();
};
/***** FIN *****/



/* V2
function addIcon(icon) { // Add icon attributes
    icon.iconSize = new GSize(11, 11);
    icon.dragCrossSize = new GSize(0, 0);
    icon.shadowSize = new GSize(11, 11);
    icon.iconAnchor = new GPoint(5, 5);
}*/