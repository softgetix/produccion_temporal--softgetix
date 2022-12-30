
var marker;
var markers = new Array();

var latCentro = -35.608418;
var lngCentro = -59.373161;
var zoomDefecto = 14;

$(document).ready(function(){
	geocoder = new google.maps.Geocoder();
	Cargar();
});

function Cargar() {
	var divMapa = document.getElementById("mapa25");
    if (divMapa){	
        
		CrearMapa(divMapa)
		cargarOpcionesPermisosMapa();
		     
		google.maps.event.addListener(map, 'click', function(event) {
			var myLatLng = event.latLng;
			var point = mapLatLng(myLatLng.lat(), myLatLng.lng());
			leftClick(point);
		});
			
		var puntos = document.getElementById("hidPuntos").value;
        if (puntos){
        	var lat = 0;
            var lng = 0;
            var arrAux = puntos.split(',');
            if (arrAux.length == 2){
            	lat = arrAux[0].replace(' ','');
                lng = arrAux[1].replace(' ','');	
			}
		
			if(lat && lng){
				mapSetZoom(16);
				mapSetCenter(lat, lng);
				point = mapLatLng(lat,lng);
				marcador(point);
			}
		
		}
		else{
			mapSetZoom(4);
			mapSetCenter(latCentro,lngCentro);
        }
	}
}

function marcador(punto){
    var arr = [];
	arr['myLatLng'] = punto;
	arr['icono'] = '/imagenes/raster/black/map_pin_fill_20x32.png';
	arr['draggable'] = true;
	marker = mapMarker(arr);
	setMap(marker);
	
	google.maps.event.addListener(marker, 'drag', function(event) {
		var myLatLng = event.latLng;
		dragPointer(myLatLng.lat(), myLatLng.lng());
	})
									
	point = mapGetPoint(marker);
	document.getElementById("hidPuntos").value = point.lat()+','+point.lng();
}

function leftClick(point) {
	limpiarMapa();
	if (!point){
		point = mapLatLng(latCentro,lngCentro);
	}
    marcador(point);
}

function dragPointer(lat, lng){
	document.getElementById("hidPuntos").value = lat+','+lng;
}

function limpiarMapa(){
    if(map){
		deleteMap(marker);
    }
}   		
   	
function centrarDireccion(){
    var address = document.getElementById("txtDireccion_2").value;  																		
    if (geocoder){
        geocoder.geocode({'address': address}, function (results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				var point = results[0].geometry.location;
				mapSetZoom(17);
				mapSetCenter(point.lat(), point.lng());
				deleteMap(marker);
				var latLng = mapLatLng(point.lat(), point.lng()); 
				marcador(latLng);
			}
			else{
				alert("La direcci\u00F3n '"+ address + "' no fue encontrada.");		  
			}
        });
    }
}