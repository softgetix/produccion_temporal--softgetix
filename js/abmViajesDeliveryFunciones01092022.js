var $crossDocking;

$(document).ready(function() {
	var daysSearch = 31;
	
	if($('.date').length){
		$('.date.fdesde').datepicker({
			onSelect: function(){
				var dateStart = $(this).datepicker('getDate');
				var dateEnd = new Date(dateStart.getTime() + daysSearch*24*60*60*1000);
				$('.fhasta').datepicker('change', {minDate:dateStart,maxDate:dateEnd});
				
				if($('.date.fhasta').val() == ''){
					$('.fhasta').datepicker('setDate',dateStart);
				}
			}
		});
		
		$('.date.fhasta').datepicker({
			minDate:($('.date.fdesde').datepicker('getDate') != null)?$('.date.fdesde').datepicker('getDate'):new Date()
			,maxDate: ($('.date.fdesde').datepicker('getDate') != null)?new Date($('.date.fdesde').datepicker('getDate').getTime() +daysSearch*24*60*60*1000):new Date(new Date().getTime() +daysSearch*24*60*60*1000)
			/*minDate: '1'
			,maxDate: '31'*/
		});
	}
	
			
	$('.viewCarOnMap').live("click", function(){
		var idMovil = $(this).attr('attrIdMovil');
		var idRef = $(this).attr('attrIdRef');
		mostrarPopup('boot.php?c=abmViajesDeliveryMapa&action=popup&idMovil='+idMovil+'&idRef='+idRef,740,450);
	});
	
	$('.setNewcar').live("click", function(){
		var vdd_id = $(this).attr('attr_vdd_id');
		var vi_id = $(this).attr('attr_vi_id');
		
		if(typeof($(this).attr('attr_crossdocking')) != 'undefined'){
			$crossDocking = $(this).attr('attr_crossdocking');	
		}
		else if(typeof($('#cross-docking').val()) != 'undefined'){
			$crossDocking = $('#cross-docking').val();
		}
		else{
			$crossDocking = 0;
		}
		
		
		var cl_id = $(this).attr('attr_cl_id');
		var co_id = $(this).attr('attr_co_id');
		var mo_id = $(this).attr('attr_mo_id');
                                
		if($crossDocking == 1){
			if(vdd_id){
				agregarConductorDelivery(vdd_id, cl_id,co_id,mo_id);
			}
			else if(vi_id){
				agregarConductorViaje(vi_id, cl_id, co_id, mo_id);
			}
		}
		else{
			agregarConductorDeliveryCompleto(vi_id, cl_id, co_id, mo_id);
		}
	});

	$('.setNewcarArauco').live("click", function(){
		var vdd_id = $(this).attr('attr_vdd_id');
		var vi_id = $(this).attr('attr_vi_id');
		
		var cl_id = $(this).attr('attr_cl_id');
		var co_id = $(this).attr('attr_co_id');
		var mo_id = $(this).attr('attr_mo_id');
                                
		agregarConductorDeliveryArauco(vi_id, cl_id,co_id,mo_id);
	});
			
	$('#uploadFile').live("click", function(){
		$(this).addClass('clear disabled').html('<span class="float_l">'+arrLang['cargando_viajes']+'</span><img src="imagenes/ajax-loader.gif" class="float_r"></img><span class="clear"></span>').attr('onclick','javascript:;');
	});
	
	
});

function crossDocking(){
	var $value = $('#cross-docking').val();
	if($value == 1){
		$('a.setNewcar').not('.firts').hide();
	}
	else{
		$('a.setNewcar').show();
	}
}


function agregarConductorViaje(id_viaje, id_transportista, id_conductor, id_movil){
	var request = $.ajax({ 
		url: 'ajaxViajes.php', 
		type: "POST",
		cache: false, 
		data:({
				accion:'popup-agregar-movil',
				id_viaje:id_viaje,
				id_conductor:id_conductor,
				id_movil:id_movil,
				id_transportista:id_transportista,
				tipo:'viaje'
		})
	}); 
	
	request.done(function(msg) {
		$("#help-modal").html(msg);
		$("#help-modal").dialog("destroy");
		$("#help-modal").dialog({
			height: 290,
			draggable: false,
			width: 370,
			title: arrLang['asignar_conductor_movil'],
			modal: true
		});		
	});
}


function agregarConductorDelivery(id_delivery, id_transportista, id_conductor, id_movil){
	var request = $.ajax({ 
		url: 'ajaxViajes.php', 
		type: "POST",
		cache: false, 
		data:({
				accion:'popup-agregar-movil',
				id_delivery:id_delivery,
				id_conductor:id_conductor,
				id_movil:id_movil,
				id_transportista:id_transportista,
				tipo:'delivery'
		})
	}); 
	
	request.done(function(msg) {
		$("#help-modal").html(msg);
		$("#help-modal").dialog("destroy");
		$("#help-modal").dialog({
			height: 290,
			draggable: false,
			width: 370,
			title: arrLang['asignar_conductor_movil'],
			modal: true
		});		
	});
}

function agregarConductorDeliveryCompleto(id_viaje, id_transportista, id_conductor, id_movil){
	var request = $.ajax({ 
		url: 'ajaxViajes.php', 
		type: "POST",
		cache: false, 
		data:({
				accion:'popup-agregar-movil',
				id_viaje:id_viaje,
				id_conductor:id_conductor,
				id_movil:id_movil,
				id_transportista:id_transportista,
				tipo:'viajeCompleto'
		})
	}); 
	
	request.done(function(msg) {
		$("#help-modal").html(msg);
		$("#help-modal").dialog("destroy");
		$("#help-modal").dialog({
			height: 290,
			draggable: false,
			width: 370,
			title: arrLang['asignar_conductor_movil'],
			modal: true
		});		
	});
}

function agregarConductorDeliveryArauco(id_viaje, id_transportista, id_conductor, id_movil){
	var request = $.ajax({ 
		url: 'ajaxViajes.php', 
		type: "POST",
		cache: false, 
		data:({
				accion:'popup-agregar-movil-arauco',
				id_viaje:id_viaje,
				id_conductor:id_conductor,
				id_movil:id_movil,
				id_transportista:id_transportista,
				tipo:''
		})
	}); 
	
	request.done(function(msg) {
		$("#help-modal").html(msg);
		$("#help-modal").dialog("destroy");
		$("#help-modal").dialog({
			height: 540,
			draggable: false,
			width: 370,
			title: arrLang['asignar_conductor_movil'],
			modal: true
		});		
	});
}

function patenteChange($this, $iduser){
	var patente = $this.value;
	var txtSelectSemi = $('#Semi option:eq(0)').html();
	var txtSelectConfiguracion = $('#Configuracion option:eq(0)').html();
	var txtSelectCargabruta = $('#Cargabruta option:eq(0)').html();
	var txtSelectTara = $('#Tara option:eq(0)').html();
	
	$('#Semi').html('<option value="">'+txtSelectSemi+'</option>');
	$('#Configuracion').html('<option value="">'+txtSelectConfiguracion+'</option>');
	$('#Cargabruta').html('<option value="">'+txtSelectCargabruta+'</option>');
	$('#Tara').html('<option value="">'+txtSelectTara+'</option>');	

	$.ajax({
		type: "POST",
		url: "ajaxViajes.php",
		data:({
			  	accion:'get-patente-change',
				patente:patente,
				id_usuario: $iduser
			}),
		success: function(msg){
			var arr = jQuery.parseJSON(msg);
			var semi = arr.semi;
			var configuracion = arr.config;
			var cargabruta = arr.cargabruta;
			var tara = arr.tara;

			if(semi){
				for(i=0; i<semi.length; i++){						
					$('#Semi').append('<option value="'+semi[i]['second_vehicle_id']+'">'+semi[i]['second_vehicle']+'</option>');	
				}				
			}
			if(configuracion){
				for(i=0; i<configuracion.length; i++){						
					$('#Configuracion').append('<option value="'+configuracion[i]['configuration_id']+'">'+configuracion[i]['configuration_description']+'</option>');	
				}				
			}
			if(cargabruta){
				for(i=0; i<cargabruta.length; i++){						
					$('#Cargabruta').append('<option value="'+cargabruta[i]['load_id']+'">'+cargabruta[i]['load_description']+'</option>');	
				}				
			}
			if(tara){
				for(i=0; i<tara.length; i++){						
					$('#Tara').append('<option value="'+tara[i]['tara_id']+'">'+tara[i]['tara_description']+'</option>');	
				}				
			}
		}	
	});
}

function setVehiculoConductorDeliveryArauco(id_viaje, id_movil, id_semi, configuracion, cargabruta, tara, id_conductor, hora, motivo){
	$.ajax({
		type: "POST",
		url: "ajaxViajes.php",
		data:({
				accion:'popup-guardar-patente-arauco',
				id_viaje:id_viaje,
				id_movil:id_movil,
				id_semi:id_semi,
				configuracion:configuracion,
				cargabruta:cargabruta,
				tara:tara,
				id_conductor:id_conductor,
				hora:hora,
				motivo:motivo				
			}),
		success: function(msg){
			var arr = jQuery.parseJSON(msg); 
			if(arr.error == false){
				if(typeof($('#viajes-listado').val()) != 'undefined'){
					enviar('verDetalle',id_viaje)
				}
				else{
					enviar('index');
				}
			}
			else{
				$('#motivo_error').html(arr.msg);
				$('#motivo_error').show();
				//actualizarVistaMovilAsignado(0, id_viaje, null);
			}
		}	
	});	
}

function setRevocarAsignacionVehiculoArauco(id_viaje, motivo){
	$.ajax({
		type: "POST",
		url: "ajaxViajes.php",
		data:({
				accion:'popup-revocar-asingacion-arauco',
				id_viaje:id_viaje,
				motivo:motivo				
			}),
		success: function(msg){
			var arr = jQuery.parseJSON(msg); 
			if(arr.error == false){
				if(typeof($('#viajes-listado').val()) != 'undefined'){
					enviar('verDetalle',id_viaje)
				}
				else{
					enviar('index');
				}
			}
			else{
				$('#motivo_error').html(arr.msg);
				$('#motivo_error').show();
			}
		}	
	});	
}

function getConductoresTrasnportista(ide, id_usuario, id_transportista){
	var txtSelectConductor = $('#'+ide+' option:eq(0)').html(); 
	$('#'+ide).html('<option value="">'+txtSelectConductor+'</option>');	
	$.ajax({
		type: "POST",
		url: "ajaxViajes.php",
		data:({
			  	accion:'get-conductores',
				transportista:id_transportista,
				id_usuario:id_usuario
			}),
		success: function(msg){
			var conductores = jQuery.parseJSON(msg);
			if(conductores){
				for(i=0; i<conductores.length; i++){
					var co_apellido = "";
					if(conductores[i]['co_apellido'] != null){
						co_apellido = " "+conductores[i]['co_apellido'];
					}
						
					$('#'+ide).append('<option value="'+conductores[i]['co_id']+'">'+conductores[i]['co_nombre']+co_apellido+'</option>');	
				}				
			}
			getVehiculosRecomendado('popup_vehiculo',id_transportista, id_usuario, 0);
		}	
	});
}

function getPatentesConductoresArauco(id_usuario, id_transportista, id_viaje, idePatente, idConductor){
	var txtSelectPatente = $('#'+idePatente+' option:eq(0)').html(); 
	$('#'+idePatente).html('<option value="">'+txtSelectPatente+'</option>');	
	$.ajax({
		type: "POST",
		url: "ajaxViajes.php",
		data:({
			  	accion:'get-patentes-process',
				transportista:id_transportista,
				id_usuario:id_usuario,
				id_viaje:id_viaje
			}),
		success: function(msg){
			var result = jQuery.parseJSON(msg);
			if(result){
				for(i=0; i<result.length; i++){
					var select = "";
					/*if(result[i]['mo_default'] != 0){
						select = 'selected ="selected"';
					}*/
						
					$('#'+idePatente).append('<option value="'+result[i]['vehicle_id']+'"'+select+'>'+result[i]['vehicle']+'</option>');	
				}				
			}
		}	
	});

	var txtSelectConductor = $('#'+idConductor+' option:eq(0)').html(); 
	$('#'+idConductor).html('<option value="">'+txtSelectConductor+'</option>');	
	$.ajax({
		type: "POST",
		url: "ajaxViajes.php",
		data:({
			  	accion:'get-conductores-process',
				transportista:id_transportista,
				id_usuario:id_usuario,
				id_viaje:id_viaje
			}),
		success: function(msg){
			var conductores = jQuery.parseJSON(msg);
			if(conductores){
				for(i=0; i<conductores.length; i++){
					$('#'+idConductor).append('<option value="'+conductores[i]['driver_id']+'">'+conductores[i]['driver']+'</option>');	
				}				
			}
		}	
	});
}

function setVehiculoConductorDelivery(id_delivery, id_transportista, id_conductor, id_movil){
	var id_motivo = $('#motivo_name option:selected').val();
	if(parseInt(id_motivo) > 0){	
		$.ajax({
			type: "POST",
			url: "ajaxViajes.php",
			data:({
					accion:'popup-guardar-conductor-delivery',
					id_transportista:id_transportista,
					id_conductor:id_conductor,
					id_movil:id_movil,
					id_delivery:id_delivery,
					motivo_id:id_motivo
				}),
			success: function(msg){
				actualizarVistaMovilAsignado(msg, null, id_delivery);
			}	
		});	
	}
	else{
		$('#motivo_error').show();
	}
}

function setVehiculoConductorViaje(id_viaje, id_transportista, id_conductor, id_movil){
	var id_motivo = $('#motivo_name option:selected').val();
	if(parseInt(id_motivo) > 0){	
		$.ajax({
			type: "POST",
			url: "ajaxViajes.php",
			data:({
					accion:'popup-guardar-conductor-viaje',
					id_transportista:id_transportista,
					id_conductor:id_conductor,
					id_movil:id_movil,
					id_viaje:id_viaje,
					motivo_id:id_motivo
				}),
			success: function(msg){
				actualizarVistaMovilAsignado(msg, id_viaje, null);
			}	
		});	
	}
	else{
		$('#motivo_error').show();
	}
}

function setVehiculoConductorDeliveryCompleto(id_viaje, id_transportista, id_conductor, id_movil){
	var id_motivo = $('#motivo_name option:selected').val();
	if(parseInt(id_motivo) > 0){	
		$.ajax({
			type: "POST",
			url: "ajaxViajes.php",
			data:({
					accion:'popup-guardar-conductor-delivery-completo',
					id_transportista:id_transportista,
					id_conductor:id_conductor,
					id_movil:id_movil,
					id_viaje:id_viaje,
					motivo_id:id_motivo
				}),
			success: function(msg){
				actualizarVistaMovilAsignado(msg, id_viaje, null);
			}	
		});	
	}
	else{
		$('#motivo_error').show();
	}
}

function actualizarVistaMovilAsignado(msg, vi_id, vdd_id){
	if(msg == 1){
		$("#help-modal").dialog("destroy");
		
		var txtTransportista = "";			
		var txtMovil = "";
		var txtConductor = "";
		var idMovil = $('#popup_vehiculo option:selected').val();
							
		if($('#popup_transportista option:selected').val() != ""){
			txtTransportista = $('#popup_transportista option:selected').text();
		}
		if(idMovil != ""){
			txtMovil = $('#popup_vehiculo option:selected').text();
		}
		if($('#popup_conductor option:selected').val() != ""){
			txtConductor = $('#popup_conductor option:selected').text();
		}
		
		var $class = '';
		if(vi_id){
			$class = '.vi_'+vi_id;	
		}
		if($crossDocking == 1){
			if(vdd_id){
				 $class =  $class+'.vdd_'+vdd_id; 
			}
			else{
				$class =  $class+'.origen'; 	
			}
		}
		
		$('.block_info_movil'+$class).html('<span class="campo1">'+txtMovil+'</span><br><span class="campo2">'+txtConductor+'</span>');
		$('span.campo1.transportista'+$class).html(txtTransportista);
			
		if(idMovil != ""){
			$('a.viewCarOnMap'+$class).show();	
		}
		else{
			$('a.viewCarOnMap'+$class).hide();	
		}
		$('a.viewCarOnMap'+$class).attr('attrIdMovil',idMovil);
		
		$('.box_assign_datetime').remove();
	}
	else{
		$("#help-modal").html('<span class="block error">'+arrLang['guardar_datos_error']+'</span>');
	}	
}
/* Solapas */
function getContenido(tipo){
	$('.tipo-listado, .tipo-historico, .tipo-observaciones, .tipo-pod').removeClass('active');
	$('#hidSolapa').val(tipo);
	
	if(tipo == 'listado'){
		$('#viajes-observaciones').hide();
		$('#viajes-historico').hide();
		$('#viajes-pod').hide();
	}
	else if(tipo == 'historico'){
		$('#viajes-listado').hide();
		$('#viajes-observaciones').hide();
		$('#viajes-pod').hide();
	}
	else if(tipo == 'observaciones'){
		$('#viajes-listado').hide();
		$('#viajes-historico').hide();
		$('#viajes-pod').hide();
	}
	else if(tipo == 'pod'){
		$('#viajes-listado').hide();
		$('#viajes-observaciones').hide();
		$('#viajes-historico').hide();
	}
	
	$('.tipo-'+tipo).addClass('active');
	$('#viajes-'+tipo).show();
}
/**/


function assignIngresoDelivery(id_destino, id_delivery){
	var ID = id_destino.toString()+id_delivery.toString();
	var IDbis = id_destino.toString()+'equals';
	$.ajax({
		type: "POST",
		url: "ajaxViajes.php",
		data:({
			accion:'assign-ingreso',
			id_viaje:$('#id_viaje').val(),
			id_destino:id_destino,
			id_delivery:id_delivery,
			fecha:$('#assign_fecha_ingreso_'+ID).val()+' '+$('#assign_hora_ingreso_'+ID).val()+':'+$('#assign_min_ingreso_'+ID).val()
		}),
		success: function(msg){
			$('#assign-datetime-ingreso-'+ID).remove();	
			if(msg){
				$('#estado-fecha-ingreso-'+ID).html(msg);
				$('#estado-fecha-ingreso-'+IDbis).html(msg);
				$('#assign-datetime-egreso-'+ID).show();
			}
		}
	});
}

function assignEgresoDelivery(id_destino, id_delivery){
	var ID = id_destino.toString()+id_delivery.toString();
	var IDbis = id_destino.toString()+'equals';
	$.ajax({
		type: "POST",
		url: "ajaxViajes.php",
		data:({
			accion:'assign-egreso',
			id_viaje:$('#id_viaje').val(),
			id_destino:id_destino,
			id_delivery:id_delivery,
			fecha:$('#assign_fecha_egreso_'+ID).val()+' '+$('#assign_hora_egreso_'+ID).val()+':'+$('#assign_min_egreso_'+ID).val()
		}),
		success: function(msg){
			$('#assign-datetime-egreso-'+ID).remove();	
			if(msg){
				$('#estado-fecha-egreso-'+ID).html(msg);
				$('#estado-fecha-egreso-'+IDbis).html(msg);
			}
		}
	});
}

function podIngresoDelivery(id_destino, id_delivery){
	var ID = id_destino.toString()+id_delivery.toString();
	var IDbis = id_destino.toString()+'equals';
	$.ajax({
		type: "POST",
		url: "ajaxViajes.php",
		data:({
			accion:'pod-ingreso',
			id_viaje:$('#id_viaje').val(),
			id_destino:id_destino,
			//id_delivery:id_delivery,
			fecha:$('#pod_fecha_ingreso_'+ID).val()+' '+$('#pod_hora_ingreso_'+ID).val()+':'+$('#pod_min_ingreso_'+ID).val()
		}),
		success: function(msg){
			$('#pod-datetime-ingreso-'+ID).empty();	
			$('#pod-datetime-ingreso-'+ID).removeClass('box_assign_datetime')
			if(msg){
				$('#pod-datetime-ingreso-'+ID).html(msg);
			}
		}
	});
}


function rechazarDelivery(id_viaje, id_destino, id_delivery){
	var request = $.ajax({ 
		url: 'ajaxViajes.php', 
		type: "POST",
		cache: false, 
		data:({
				accion:'popup-rechazar-pedido',
				id_viaje:id_viaje,
				id_delivery:id_delivery,
				id_destino:id_destino
		})
	}); 
	
	request.done(function(msg) {
		$("#help-modal").html(msg);
		$("#help-modal").dialog("destroy");
		$("#help-modal").dialog({
			width: 370,
			height: 170,
			draggable: false,
			title: arrLang['msj_viajes_motivo_cambio'],
			modal: true
		});		
	});
}

function setRechazarDelivery(id_viaje, id_destino, id_delivery){
	var id_motivo = $('#motivo_name option:selected').val()
	var estado = 1;
	
	if(parseInt(id_motivo) > 0){	
		$.ajax({
			type: "POST",
			url: "ajaxViajes.php",
			data:({
					accion:'popup-guardar-rechazo-delivery',
					id_viaje:id_viaje,
					id_destino:id_destino,
					id_delivery:id_delivery,
					id_motivo:id_motivo,
					rechazado:estado
				}),
			success: function(msg){
				if(msg == 1){
					$("#help-modal").dialog("destroy");
					
					$('#button_rechazado_'+id_delivery).addClass('disabled').attr('href','javascript:;');
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

function resetIngresoDelivery($vd_id){
	var resp = confirm(arrLang['msj_viaje_reset_ingreso']);
	if(resp){
		$.ajax({
			type: "POST",
			url: "ajaxViajes.php",
			data:({
				accion:'reset-ingreso-delivery',
				vd_id:$vd_id,
				id_viaje:$('#id_viaje').val()
			}),
			success: function(msg){
				if(msg == true){
					$('.reset-ingreso-'+$vd_id).remove();
					$('.reset-egreso-'+$vd_id).remove();
					$('#btn-delete-'+$vd_id).removeClass('none');
					
					//---habilidarAssignFechas();	
				}
				else{
					$('.reset-ingreso-'+$vd_id).append('<span class="block error">'+arrLang['solicitud_enviada']+'.</span>');
				}
			}	
		});
	}
}


function resetEgresoDelivery($vd_id){
	var resp = confirm(arrLang['msj_viaje_reset_egreso']);
	if(resp){
		$.ajax({
			type: "POST",
			url: "ajaxViajes.php",
			data:({
				accion:'reset-egreso-delivery',
				vd_id:$vd_id,
				id_viaje:$('#id_viaje').val()
			}),
			success: function(msg){
				if(msg == true){
					$('.reset-egreso-'+$vd_id).remove();
					//----habilidarAssignFechas();
				}
				else{
					$('.reset-egreso-'+$vd_id).append('<span class="block" style="color:#FF2200">'+arrLang['solicitud_enviada']+'.</span>');
				}
			}	
		});
	}
}
