$(document).ready(function() {
	$(".date").datepicker({
		minDate: '-31',
        /*maxDate: '1',*/ 
	});
	
	 /* $(".date").live("focusin", function() { 
       $(this).datepicker({
          onSelect: function(objDatepicker){
				var fecha = $(this).val().replace('/','-');
                var fecha = fecha.replace('/','-');
				$(this).val(fecha);
            }
        });
    });*/
});


function agregarConductor(id_viaje, id_transportista, id_conductor, id_movil){
	var request = $.ajax({ 
		url: 'ajaxViajes.php', 
		type: "POST",
		cache: false, 
		data:({
				accion:'popup-agregar-conductor',
				id_viaje:id_viaje,
				id_transportista:id_transportista,
				id_conductor:id_conductor,
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
			title: arrLang['asignar_conductor_movil'],
			modal: true
		});		
	});
}

function setVehiculoConductor(id_viaje, id_conductor, id_movil){
	var id_motivo = $('#motivo_name option:selected').val();
	if(parseInt(id_motivo) > 0){	
		$.ajax({
			type: "POST",
			url: "ajaxViajes.php",
			data:({
					accion:'popup-guardar-conductor',
					id_conductor:id_conductor,
					id_movil:id_movil,
					id_viaje:id_viaje,
					motivo_id:id_motivo
				}),
			success: function(msg){
				if(msg == 1){
					$("#help-modal").dialog("destroy");
					
					var txtMovil = "";
					var txtConductor = "";
					if($('#popup_vehiculo option:selected').val() != ""){
						txtMovil = $('#popup_vehiculo option:selected').text();
					}
					if($('#popup_conductor option:selected').val() != ""){
						txtConductor = $('#popup_conductor option:selected').text();
					}
					$('#condMovil_'+id_viaje).html('<span class="campo1">'+txtMovil+'</span><br><span class="campo2">'+txtConductor+'</span>');
				}
				else{
					$("#help-modal").html('<span class="block error">'+arrLang['guardar_datos_error']+'</span>');
				}
			}	
		});	
	}
	else{
		$('#motivo_error').show();
	}	
}

function msgMotivoMail($id_viaje, $vi_codigo){
	$('#rueda-carga').html('<img src="imagenes/ajax-loader.gif" >');
	
	$.ajax({ 
		url: 'ajaxViajes.php', 
		type: "POST",
		cache: false, 
		data:({
				accion:'msg-motivo-mail',
				vi_codigo:$vi_codigo,
		}),
		success: function(msg){
			$('#rueda-carga').html('');
		}
	}); 
}
