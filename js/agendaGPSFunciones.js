function setVehiculo(id_viaje, id_transportista, id_movil){
	var request = $.ajax({ 
		url: 'ajaxViajes.php', 
		type: "POST",
		cache: false, 
		data:({
				accion:'popup-agregar-vehiculo',
				id_viaje:id_viaje,
				id_transportista:id_transportista,
				id_movil:id_movil
		})
	}); 
	
	request.done(function(msg) {
		$("#help-modal").html(msg);
		$("#help-modal").dialog("destroy");
		$("#help-modal").dialog({
			height: 250,
			draggable: false,
			width: 370,
			title: arrLang['asignar_movil'],
			modal: true
		});		
	});
}

function setVehiculoAssign(id_viaje){
	var id_movil = $('#popup_vehiculo option:selected').val();
	var observacion = $('#popup_observacion').val();
	
	$.ajax({
		type: "POST",
		url: "ajaxViajes.php",
		data:({
			accion:'popup-guardar-vehiculo',
			id_movil:id_movil,
			id_viaje:id_viaje,
			observacion:observacion
		}),
		success: function(msg){
			if(msg == 1){
				$("#help-modal").dialog("destroy");
				var txtMovil = "";
				if($('#popup_vehiculo option:selected').val() != ""){
					txtMovil = $('#popup_vehiculo option:selected').text();
				}
				$('#condMovil_'+id_viaje).html('<span class="campo1">'+txtMovil+'</span>');
			}
			else{
				$("#help-modal").html('<span class="block error">'+arrLang['guardar_datos_error']+'</span>');
			}
		}
	});	
}