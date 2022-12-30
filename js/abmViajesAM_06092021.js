
var urlAjax = 'ajaxViajes.php';
var img_reload = "./imagenes/ajax-loader.gif";
var eventCode = "";
var fechaServer = "";
var horaServer = "";
var minServer = "";
var validarAccionUser = false;
var validarIdGeozonas = "";
var estadia = 20; //en Minutos

$(function() {
	//--Inicio. Obtener fecha server --//
	$.ajax({
		async:false,
		cache:false,
		type: "POST",
		url: urlAjax,
		data:({
			accion:'get-fecha-server'
		}),
		success: function(msg){
			var arrFecha = msg.split(' ');
			var arrHora = arrFecha[1].split(':');
			
			fechaServer = arrFecha[0];
			horaServer = arrHora[0];
			minServer = arrHora[1];
		}	
	});
	
	$("#sortable").sortable({
		placeholder: "ui-state-highlight",
		handle: "#handle",
		axis: 'y',
		stop: function() {
			ruteoManual($("#sortable").sortable("serialize"));
		}
	});
	
	
	//-- inicio. Recarga de Combos --//
	if($('#hidSeccion').val() != 'agendaGPS'){//---FORZA	
		reloadComboTransportista();
	}
	reloadComboTipoMovil('hide',$("#temp_transportista").val());
	if($("#temp_transportista").val() != ""){
		reloadComboTipoMovil('show',$("#temp_transportista").val());
		reloadComboConductor($("#temp_transportista").val());}
	if($("#temp_movil_tipo").val() != ""){
		reloadComboMovil($("#temp_movil_tipo").val());}	
	//-- fin. Recarga de Combos --//
	
	//-- inicio. Autocomplete GEOZONAS --//
	$( "#geozona" ).autocomplete({
    	source: function( request, response ) {
        $.ajax({
        	type: "POST",
			url: urlAjax,
			dataType: "json",
			data:({
				accion:'autocomplete-geozonas',
				buscar:request.term,
				id_geozona:$('#id_geozonas').val()
			}),
			success: function(data){
            	response( $.map( data.geozonas, function( item ) {
                	return {
                    	label: item.etiqueta + ((typeof(item.re_numboca) != 'undefined')?' ('+ item.re_numboca +')':''),
                        value: item.re_nombre
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
	//-- fin. Autocomplete GEOZONAS --//
	
   validarIdGeozonas = $("#id_geozonas").val();
	
		
	
	/*
	$('#movil').editableSelect({
		bg_iframe: true,
      	onSelect: function(list_item) {
        	alert('List item text: '+ list_item.text());
        	// 'this' is a reference to the instance of EditableSelect
        	// object, so you have full access to everything there
        	alert('Input value: '+ this.text.val());
      	},
      		case_sensitive: false, // If set to true, the user has to type in an exact
                             // match for the item to get highlighted
      		items_then_scroll: 10 // If there are more than 10 items, display a scrollbar
    	}
  	);
 	 var select = $('#movil:first');
  	var instances = select.editableSelectInstances();
  	instances[0].addOption('Germany, value added programmatically');
  
	/******/
	
 });

function setRuteo(geozona){
	$("#btn-add-geozona").attr('onClick','return false');
	$.ajax({
		dataType:"html",
		type: "POST",
		url: urlAjax,
		data:({
			accion:'get-geozona-ruteo',
			geozona:geozona,
			id_geozona:$('#id_geozonas').val()
		}),
		success: function(msg){
			msg = limpiarTable(msg);
			var datos = msg.split("[##]");
			$('#sortable').append(datos[0]);
			$('#geozona').val('');
			var geozonasid = $('#id_geozonas').val();
			if(parseInt(datos[1]) > 0){
				if(geozonasid != ""){
					$('#id_geozonas').val(geozonasid.concat(",").concat(datos[1]));
				}
				else{
					$('#id_geozonas').val(datos[1]);
				}
			}
			
			if($('#ruteo-automatico').val() == 1){
				ruteoAutomatico();
                $('.handle').hide(); 
			}
			else{
				ruteoValidarDatos(null, true);
			}
			
			$("#btn-add-geozona").attr('onClick','return true');

			validarPallets();
		}	
	});

	
}

function ruteoManual(cadena){ 
	var array_orden = cadena.split('&');
	var coma = "";
	var orden = "";
	for(i=0; i < array_orden.length; i++){
		orden = orden.concat(coma).concat(array_orden[i].replace('n[]=',''));
		coma = ",";	
	}
	$("#id_geozonas").val(orden);
	ruteoValidarDatos(null, true);
}

function ruteoAutomatico(){
	$.ajax({
		async:false,
		cache:false,
		type: "POST",
		url: urlAjax,
		data:({
			accion:'ruteo-automatico',
			ids:$("#id_geozonas").val()
		}),
		success: function(msg){ 
			datos = jQuery.parseJSON(msg); 
			var rowsContent = "";

           	var d_fecha = new Array(datos.length);
           	var d_hora = new Array(datos.length);
           	var d_min = new Array(datos.length);
           	var d_duracion = new Array(datos.length);    
            
			var orden_geozonas = "";
			var coma = "";
			
           	for(i=0; i<datos.length; i++){ 
				d_fecha[i] = $('#fecha_'+datos[i].id).val();
                d_hora[i] = $('#hora_'+datos[i].id).val();
                d_min[i] = $('#min_'+datos[i].id).val();
                //R* d_duracion[i] = $('#duracion_'+datos[i].id).val();
                rowsContent+= '<tr id="n_'+datos[i].id+'">'+$('#n_'+datos[i].id).html()+"<tr>" ;
				
				orden_geozonas+=coma.concat(datos[i].id);
				coma=",";
            }
			
			if(orden_geozonas != ""){
				$("#id_geozonas").val(orden_geozonas);}
			
			if(datos.length > 1){
				$("#sortable").html(rowsContent);
				
				for(i=0; i<datos.length; i++){
					$('#fecha_'+datos[i].id).val(d_fecha[i]);
                    $('select#hora_'+datos[i].id+' option[value="'+d_hora[i]+'"]').attr("selected",true);
                    $('select#min_'+datos[i].id+' option[value="'+d_min[i]+'"]').attr("selected",true);
                    //R* $('select#duracion_'+datos[i].id+' option[value="'+d_duracion[i]+'"]').attr("selected",true);
				}
			}
		}	
	});
	ruteoValidarDatos(null, true);
}

function ruteoValidarDatos(id_ref, calcular_km){
	var ref = $("#id_geozonas").val().split(',');
	
	if(id_ref != "" && id_ref != null){// cuando se realiza un cambio de fecha, hora desde el listado, actualizo todos los datos hacia adelante
		i = 0;
		while(i == 0){
			if(ref[i] != id_ref){ 
				var id = $.inArray(ref[i],ref);
				if(id != -1) ref.splice(id,1);
			}
			else{
				i = 1;
			}
		}
	}
	
	calcularRuteo(ref, true, true, calcular_km);
}

function calcularRuteo(ref, calcular_f_ini, calcular_f_fin, calcular_km){
	var ref_ant = ref[0];
	var ref_sig = "";
	
	if(ref_ant != ''){
		calcularFecha(0,ref_ant,ref_ant);
	}
	
	if(calcular_km == true){
		var km = 0;
		$.ajax({
			async:true,
			cache:false,
			type: "POST",
			url: urlAjax,
			data:({accion:'formatear-distancia',km:km}),
				success: function(c){
					$('#kmTotal').html(c);
				}	
		});
	}
	
	for(i=1; i < ref.length; i++){
		ref_sig = ref[i];
		
		//-- Se calcula KM --//
		if(calcular_km == true){
			$.ajax({
				async:false,
				cache:false,
				type: "POST",
				url: urlAjax,
				data:({
					accion:'calculo-distancia',
					lat1:$('#lat_'+ref_ant).val(),
					long1:$('#long_'+ref_ant).val(),
					lat2:$('#lat_'+ref_sig).val(),
					long2:$('#long_'+ref_sig).val()
				}),
				success: function(dist){
					km = parseInt(km) + parseInt(dist);
					$.ajax({
						async:true,
						cache:false,
						type: "POST",
						url: urlAjax,
						data:({accion:'formatear-distancia',km:km}),
							success: function(c){
								$('#kmTotal').html(c);
							}	
					});
					estadia = parseInt((parseInt(dist)/$('#vel_promedio_'+ref_sig).val())*60);
				},
				beforeSend:function(){},
				error:function(objXMLHttpRequest){}	
			});
		}
		
		//-- Se define fecha de INGRESO --//
		if(calcular_f_ini == true){
			if(typeof(estadia) == 'undefined'){
				estadia = 0;
			}
			if(estadia < 20){estadia = 20;}
			calcularFecha(estadia,ref_ant,ref_sig);
		}
		
	   ref_ant = ref_sig;

	   
	}

    //-- Inicio. Asignar color al Origen --//
    $("#sortable tr").removeClass('refencia-origen');
    $("#sortable tr:eq(0)").addClass('refencia-origen');
    //-- Fin. Asignar color al Origen --//
	
	validarMovil();
}

function calcularFecha(estadia,ref_ant,ref_sig,$grupo){
	
	var $fecha = 'fecha_';
	var $hora = 'hora_';
	var $min = 'min_';	
	var $curso_horario = 'curso_horario_';
	
	if(typeof($grupo) != 'undefind'){
		if($grupo == 'assign_ingreso'){
			$fecha = 'assign_fecha_ingreso_';
			$hora = 'assign_hora_ingreso_';
			$min = 'assign_min_ingreso_';	
			$curso_horario = 'assign_curso_horario_ingreso_';
		}
		else if($grupo == 'assign_egreso'){
			$fecha = 'assign_fecha_egreso_';
			$hora = 'assign_hora_egreso_';
			$min = 'assign_min_egreso_';	
			$curso_horario = 'assign_curso_horario_egreso_';
		}
	}
	
	$.ajax({
		async:false,
		cache:false,
		type: "POST",
		url: urlAjax,
		data:({
			accion:'calculo-fecha',
			duracion: estadia+' min',
			fecha: $('#'+$fecha+ref_ant).val().concat(" ").concat($('#'+$hora+ref_ant).val()).concat(":").concat($('#'+$min+ref_ant).val())
		}),
		success: function(msg){
			var f_ingreso = msg.split(' ');
			$('#'+$fecha+ref_sig).val(f_ingreso[0]);
			var h_ingreso = f_ingreso[1].split(':');
			$('#'+$hora+ref_sig).val(h_ingreso[0]);
			$('#'+$min+ref_sig).val(getMin(h_ingreso[1]));
			$('#'+$curso_horario+ref_sig).html(f_ingreso[2]);
		},
		beforeSend:function(){},
		error:function(objXMLHttpRequest){}	
	});	
}

function modoRuteo(){
    if($('#ruteo-automatico').val() == 1){
        ruteoAutomatico();
        $('.handle').hide();   
    }
    else{
        $('.handle').show();   
    }
}

function deleteRow(id_ref){
    $('tr#n_'+id_ref).remove();
    var ref = $("#id_geozonas").val().split(','); 
    var new_geozonas = "";
    var coma = "";
    for(i=0; i<ref.length; i++){
        if(ref[i] != id_ref){
            new_geozonas+= coma.concat(ref[i]);
            coma = ','; 
        }

    }
    $("#id_geozonas").val(new_geozonas);
	ruteoValidarDatos(null, true);
}

function viewCamposOpcionales(){ 
	if($('#viewCamposOpcionales').hasClass('on')){
		$('#viewCamposOpcionales').addClass('off');
		$('#viewCamposOpcionales').removeClass('on');
		$('#viewCamposOpcionales').attr('title',arrLang['ver_campos_opcionales']);
		$('#DatosCamposOpcionales').hide(); 
	}
	else if($('#viewCamposOpcionales').hasClass('off')){
		$('#viewCamposOpcionales').addClass('on');
		$('#viewCamposOpcionales').removeClass('off');
		$('#viewCamposOpcionales').attr('title',arrLang['ocultar_campos_opcionales']);
		$('#DatosCamposOpcionales').show();	
	}
}

function getMin(minutes){
	var h_selec
	if(parseInt(minutes) < 10){h_selec = '00';}	
	else if(parseInt(minutes) < 20){h_selec = '10';}	
	else if(parseInt(minutes) < 30){h_selec = '20';}	
	else if(parseInt(minutes) < 40){h_selec = '30';}	
	else if(parseInt(minutes) < 50){h_selec = '40';}	
	else if(parseInt(minutes) < 60){h_selec = '50';}	

	return h_selec;
}

//-- RECARGA DE COMBOS --//
function reloadComboTransportista(){
	var ideCombo = 'select#transportista';
	$('#tr-reload').html('<img src="'+img_reload+'" border="0" class="float_l" style="margin:5px 0 0 5px;">');
	$(ideCombo).addClass('none');
	var txtSelect = $(ideCombo+' option:eq(0)').html();
	$(ideCombo+' option').remove();
	$(ideCombo).append('<option value="" class="placeholder">'+txtSelect+'</option>');

	$.ajax({
		async:false,
		cache:false,
		type: "POST",
		url: urlAjax,
		data:({
			accion:'reload-combo-transportista'
		}),
		success: function(msg){
			datos = jQuery.parseJSON(msg); 
			if(datos.length > 0){
				
				$idperfil = $('#idperfil').val();
				
				if($idperfil == 28){
					for(i=0; i<datos.length; i++){ 
						if($('#temp_transportista').val() == datos[i].cl_id){
							$(ideCombo).parent().removeClass('none');
							$(ideCombo).after('<input type="text" value="'+datos[i].cl_razonSocial+'" class="no_margin float_l mitad_largo" disabled="true">');
							$('#tr-reload').html('');
						}
					}
				}
				else{
					$(ideCombo).parent().removeClass('none');
					for(i=0; i<datos.length; i++){ 
						var selected = "";
						if($('#temp_transportista').val() == datos[i].cl_id){
							selected = 'selected = "selected"';	
						}
						$(ideCombo).append('<option value="'+datos[i].cl_id+'" '+selected+'>'+datos[i].cl_razonSocial+'</option>');
					}
					$('#tr-reload').html('');
					$(ideCombo).removeClass('none');
				}
			}
			else{
				$(ideCombo).parent().addClass('none');
			}
		}	
	});
}

function reloadComboTipoMovil(view,transportistaId){
	var ideCombo = 'select#movil_tipo';
	$('#tipo-mo-reload').html('<img src="'+img_reload+'" border="0" class="float_l" style="margin:5px 0 0 5px;">');
	$(ideCombo).addClass('none');
	
	var txtSelect = $('select#movil_tipo option:eq(0)').html();
	$(ideCombo+' option').remove();
	$(ideCombo).append('<option value="" class="placeholder">'+txtSelect+'</option>');

	$.ajax({
		async:false,
		cache:false,
		type: "POST",
		url: urlAjax,
		data:({
			accion:'reload-combo-movil-tipo'
			,id_transportista: transportistaId
		}),
		success: function(msg){
			datos = jQuery.parseJSON(msg);
			if(datos.length > 0){
				if(view == 'show'){
					$(ideCombo).parent().removeClass('none');
				}
				else{
					$(ideCombo).parent().addClass('none');
				}
				for(i=0; i<datos.length; i++){
					var selected = "";
					if($('#temp_movil_tipo').val() == datos[i].tv_id){
						selected = 'selected = "selected"';	
					}
					$(ideCombo).append('<option value="'+datos[i].tv_id+'" '+selected+'>'+datos[i].tv_nombre+'</option>');
				}
				$('#tipo-mo-reload').html('');
				$(ideCombo).removeClass('none');
			}
			else{
				$(ideCombo).parent().addClass('none');
			}	
		}	
	});
	
	if($('#temp_movil_tipo').val() == ""){
		$(ideCombo+' option:eq(0)').attr('selected','selected');
	}
	
	if(view == 'show'){
		$(ideCombo).children('option').show();
	}
	else{
		$(ideCombo).children('option').not('.placeholder').hide();}
	
	$('#tipo-mo-reload').html('');
	$(ideCombo).removeClass('none');
}

function reloadComboMovil(tipo_movil){
	var ideCombo = 'select#movil';
	$('#mo-reload').html('<img src="'+img_reload+'" border="0" class="float_l" style="margin:5px 0 0 5px;">');
	$(ideCombo).addClass('none');
	var txtSelect = $(ideCombo+' option:eq(0)').html();
	$(ideCombo+' option').remove();
	$(ideCombo).append('<option value="" class="placeholder">'+txtSelect+'</option>');

	$.ajax({
		type: "POST",
		url: urlAjax,
		data:({
			accion:'reload-combo-movil',
			tipo_movil:tipo_movil,
			transportista:$("#transportista").val()
		}),
		success: function(msg){
			datos = jQuery.parseJSON(msg);
			if(datos.length > 0){
				$(ideCombo).parent().removeClass('none');
				for(i=0; i<datos.length; i++){
					var selected = "";
					if($('#temp_movil').val() == datos[i].mo_id){
						selected = 'selected = "selected"';	
					}
					$(ideCombo).append('<option value="'+datos[i].mo_id+'" '+selected+'>'+datos[i].mo_matricula+'</option>');
				}
				$('#mo-reload').html('');
				$(ideCombo).removeClass('none');
			}
			else{
				$(ideCombo).parent().addClass('none');
			}		
		}	
	});
}

function reloadComboConductor(id_transportista){
	var ideCombo = 'select#conductor';
	$('#co-reload').html('<img src="'+img_reload+'" border="0" class="float_l" style="margin:5px 0 0 5px;">');
	$(ideCombo).addClass('none');
	var txtSelect = $('select#conductor option:eq(0)').html();
	$(ideCombo+' option').remove();
	$(ideCombo).append('<option value="" class="placeholder">'+txtSelect+'</option>');

	$.ajax({
		type: "POST",
		url: urlAjax,
		data:({
			accion:'reload-combo-conductor',
			id_transportista:parseInt(id_transportista)
		}),
		success: function(msg){
			datos = jQuery.parseJSON(msg);
			if(datos.length > 0){
				$(ideCombo).parent().removeClass('none');
				for(i=0; i<datos.length; i++){
					var selected = "";
					if($('#temp_conductor').val() == datos[i].co_id){
						selected = 'selected = "selected"';	
					}
					$(ideCombo).append('<option value="'+datos[i].co_id+'" '+selected+'>'+datos[i].co_nombre+' '+datos[i].co_apellido+'</option>');
				}
				$('#co-reload').html('');
				$(ideCombo).removeClass('none');
			}
			else{
				$(ideCombo).parent().addClass('none');
			}		
		}	
	});
}

function resetIngreso(id_ref){
	var resp = confirm(arrLang['msj_viaje_reset_ingreso']);
	if(resp){
		$.ajax({
			type: "POST",
			url: urlAjax,
			data:({
				accion:'reset-ingreso',
				id_ref:id_ref,
				id_viaje:$('#id_viaje').val()
			}),
			success: function(msg){
				if(msg == true){
					$('#reset-ingreso-'+id_ref).remove();
					$('#reset-egreso-'+id_ref).remove();
					$('#btn-delete-'+id_ref).removeClass('none');
					
					//-- Buscar Referencia Anterior para habilitar el reset de Ingreso y Egreso --//
					var arr_ref = $("#id_geozonas").val().split(','); 
					for(i=0; i < arr_ref.length; i++){
						if(arr_ref[i] == id_ref){
							if(typeof(arr_ref[i-1]) != 'undefined'){
								habilidarResetFechas(arr_ref[i-1]);
							}
						}	
					}
					//-- --//
					habilidarAssignFechas();	
				}
				else{
					$('#reset-ingreso-'+id_ref).append('<span class="block error">'+arrLang['solicitud_enviada']+'.</span>');
				}
			}	
		});
	}
}

function resetEgreso(id_ref){
	var resp = confirm(arrLang['msj_viaje_reset_egreso']);
	if(resp){
		$.ajax({
			type: "POST",
			url: urlAjax,
			data:({
				accion:'reset-egreso',
				id_ref:id_ref,
				id_viaje:$('#id_viaje').val()
			}),
			success: function(msg){
				if(msg == true){
					$('#reset-egreso-'+id_ref).remove();
					habilidarAssignFechas();
				}
				else{
					$('#reset-egreso-'+id_ref).append('<span class="block" style="color:#FF2200">'+arrLang['solicitud_enviada']+'.</span>');
				}
			}	
		});
	}
}

function assignIngreso(id_ref, id_destino){
	$.ajax({
		type: "POST",
		url: "ajaxViajes.php",
		data:({
			accion:'assign-ingreso',
			id_ref:id_ref,
			id_viaje:$('#id_viaje').val(),
			id_destino:id_destino,
			fecha:$('#assign_fecha_ingreso_'+id_ref).val()+' '+$('#assign_hora_ingreso_'+id_ref).val()+':'+$('#assign_min_ingreso_'+id_ref).val()
		}),
		success: function(msg){
			if(msg){
				$('#assign-datetime-ingreso-'+id_ref).removeClass('box_assign_datetime').addClass('box_reset_datetime').html('<span class="campo1">'+msg+'</span>');
				$('#assign-datetime-egreso-'+id_ref).show();
			}
			else{
				$('#assign-datetime-ingreso-'+id_ref).empty();	
			}
		}
	});
}

function assignEgreso(id_ref, id_destino){
	$.ajax({
		type: "POST",
		url: "ajaxViajes.php",
		data:({
			accion:'assign-egreso',
			id_ref:id_ref,
			id_viaje:$('#id_viaje').val(),
			id_destino:id_destino,
			fecha:$('#assign_fecha_egreso_'+id_ref).val()+' '+$('#assign_hora_egreso_'+id_ref).val()+':'+$('#assign_min_egreso_'+id_ref).val()
		}),
		success: function(msg){
			if(msg){
				$('#assign-datetime-egreso-'+id_ref).removeClass('box_assign_datetime').addClass('box_reset_datetime').html('<span class="campo1">'+msg+'</span>');
				//$('#assign-datetime-egreso-'+id_ref).show();
				habilidarAssignFechas();
			}
			else{
				$('#assign-datetime-egreso-'+id_ref).empty();	
			}
		}
	});
}

//se utiliza este método cuando unicamente se puede resetear las fechas de ingreso-egreso por orden de visita.
function habilidarResetFechas(id_ref){
	if($('#orden_visita_db').val() == 1){ //si respeta orden de visita se habilita reseteo del último ingreso-egreso detectado
		$('#reset-ingreso-'+id_ref+' span a').show();
		$('#reset-egreso-'+id_ref+' span a').show();
	}
	else{//Si NO respeta orden, se permite resetear todas las fechas de ingreso-egreso detectados
		$('span a.resetDates').show();
	}
}

//Permite asignar fecha de ingreso y egreso para moviles sin reportar.
function habilidarAssignFechas(){
	if($('#orden_visita_db').val() == 1){ //si respeta orden de visita se habilita al último ingreso-egreso detectado
		var egreseDetect = false;
		var showPrimerElemento = true;
		
		$(".box_assign_datetime").each(function(){ 
			if(!$(this).prev().hasClass('box_reset_datetime')){
				var verificarClase = $(this).attr('id').split('-');
				if(showPrimerElemento == true){
					showPrimerElemento = verificarClase[3];
				}
				if(verificarClase[2] == 'egreso'){
					if($('#reset-ingreso-'+verificarClase[3]+ ' a.resetDates').is(':visible')){ 
						$(this).show();
						showPrimerElemento = false;
					}
					else{
						$(this).hide();
					}
				}
				else if(egreseDetect == true){
					$(this).show();	
					showPrimerElemento = false;
				}
				else{
					$(this).hide();
				}
				egreseDetect = false;
			}
			else{
				var verificarClase = $(this).attr('id').split('-');
				if(verificarClase[2] == 'egreso'){
					egreseDetect = true;
				}
			}
	    });
		
		if(showPrimerElemento != true && showPrimerElemento != false){
			$('#assign-datetime-ingreso-'+showPrimerElemento).show();
		}
	}
	else{//Si NO respeta orden, se permite ingresar todas las fechas de ingreso-egreso de moviles sin reportar
		$(".box_assign_datetime").each(function(){ 
			if(!$(this).prev().hasClass('box_reset_datetime')){
			 	var verificarClase = $(this).attr('id').split('-');
				if(verificarClase[2] == 'egreso'){
					if($('#'+$(this).attr('id').replace('egreso','ingreso')).is(':hidden')){ 
						$(this).show();
					}
				}
				else{
					$('#'+$(this).attr('id').replace('ingreso','egreso')).hide();
					$(this).show();
				}
			}
        });
	}
}

function setBuscarGeozona(e){
	if(e.keyCode == 13 && e.keyCode != eventCode){
		setRuteo($('#geozona').val());
	}
	eventCode = e.keyCode;
}

function validarFecha(ide){
	var ids = $('#id_geozonas').val();
	var fila = ids.split(',');
	var desde = 0;
	for(i=1; i<fila.length; i++){
		if(fila == ide){desde = i;}	
	}
	
	for(i=desde; i<fila.length; i++){
		ide = fila[i];		
		var arrfechaSeleccionado = $('#fecha_'+ide).val().split('-');
		var fechaSeleccionado = new Date(arrfechaSeleccionado[2],arrfechaSeleccionado[1],arrfechaSeleccionado[0],$('#hora_'+ide).val(),$('#min_'+ide).val());
					
		var arrfechaServidor = fechaServer.split('-');
		var fechaServidor = new Date(arrfechaServidor[2],arrfechaServidor[1],arrfechaServidor[0],horaServer,minServer);
		
		if(fechaSeleccionado < fechaServidor){
			$('#error_'+ide).html(arrLang['msj_viaje_fecha_anterior']);
		}
		else{$('#error_'+ide).empty();}	
	}
	
	//R* validarFechaEgresoIngreso();
}

/*
function validarFechaEgresoIngreso(){
	var ids = $('#id_geozonas').val();
	var fila = ids.split(',');
	
	//R* var f_egreso = $('#fecha_egreso_'+fila[0]).html().replace(' hs','');
	for(i=1; i<fila.length; i++){
		
		var arr_egreso = f_egreso.split(' ');
		var arr_egreso_f = arr_egreso[0].split('-');
		var arr_egreso_h = arr_egreso[1].split(':');
		var fechaEgreso = new Date(arr_egreso_f[2],arr_egreso_f[1],arr_egreso_f[0],arr_egreso_h[0],arr_egreso_h[1]);
		
		var arr_ingreso = $('#fecha_'+fila[i]).val().split('-');
		var fechaIngreso = new Date(arr_ingreso[2],arr_ingreso[1],arr_ingreso[0],$('#hora_'+fila[i]).val(),$('#min_'+fila[i]).val());
		
		if(fechaIngreso < fechaEgreso){
			$('#error_'+fila[i]).append('<span id="error2_'+fila[i]+'" class="block">'+arrLang['msj_viaje_fecha_programada']+'.</span>');
		}
		else{$('#error_'+fila[i]+' span#error2_'+fila[i]).empty();}	
		
		//R* f_egreso = $('#fecha_egreso_'+fila[i]).html().replace(' hs','');
	}
}*/


function retornoPopup(data){
  	$('#geozona').val(data.nombre);
}

function validarVolver(){
	var resp = true;
	
	if(validarAccionUser == true || validarIdGeozonas != $("#id_geozonas").val()){
		resp = confirm(arrLang['msj_viajes_not_save']);
	}
	
	if(resp == true){
		enviar('volver');
	}
}

function onChanges(ide, opcion){
	validarAccionUser = true;
	if(opcion == 'comboFecha'){
		ruteoValidarDatos(ide, false);
		validarFecha(ide);
	}
	else if(opcion == 'comboTransportista'){
		$('select#movil_tipo option').removeAttr('selected');
		$('select#movil_tipo option:eq(0)').attr('selected','selected');
		$('select#movil_tipo').children('option').show();
		
		$('select#movil option:eq(0)').attr('selected','selected');
		$('select#movil').children('option').not('.placeholder').hide();
		
		reloadComboTipoMovil('show',ide.value);
		reloadComboConductor(ide.value);
		$('select#conductor option').removeAttr('selected');
		$('select#conductor option:eq(0)').attr('selected','selected');
	}
	else if(opcion == 'comboVehiculo'){
		reloadComboMovil(ide.value);
	} 
	else if(opcion == 'validarMovil'){
		validarMovil(ide.value)	;
	}
	else if(opcion == 'linkOnOff'){
		OnOff(ide);
	}
	else{
		validarPallets();
	}
}

function validarMovil(){ 
	$('.error-movil').remove();
 	var ref = $("#id_geozonas").val().split(','); 
 	var f_ini = "";
	var f_fin = "";
	
	if(ref.length > 0){
		var i = 0;
		if(ref.length > 1){
			i = (ref.length - 1);
		}
		
		f_ini = $('#fecha_'+parseInt(ref[0])).val()+' '+$('#hora_'+ref[0]).val()+':'+$('#min_'+ref[0]).val();	
		//R* f_fin = $('#fecha_egreso_'+parseInt(ref[i])).html().replace (' hs','');	
			
	 	if($('#movil').val() != "" && f_ini != "" && f_fin != ""){
			$.ajax({
				type: "POST",
				url: urlAjax,
				data:({
					accion:'validar-movil',
					id_movil:$('#movil').val(),
					id_viaje:$('#id_viaje').val(),
					f_ini:f_ini,
					f_fin:f_fin
				}),
				success: function(msg){
					if(msg > 0){
						$('table.listado-viajes.bottom-rows').before('<span class="error-movil block error gum">'+arrLang['msj_viajes_movil_asignado']+'.</span>');	
					}
				}	
			});
		}
	}
}

function validarCodigoViaje(id){
	$('#fieldValidarCodigo span.advertencia').remove();
	 
	valor = $('#'+id).val();
	if(valor != ""){
		$.ajax({
			type: "POST",
			url: urlAjax,
			data:({
				accion:'validar-codViaje',
				cod_viaje:valor,
				id_viaje:$('#id_viaje').val()
			}),
			success: function(msg){
				if(msg > 0){
					$('#fieldValidarCodigo span.obligatorio').after('<span class="advertencia block">'+arrLang['msj_viajes_existe_codigo']+'.</span>');
				}
			}
		});	
	}
}

function deleteViaje(){
	var resp = confirm(arrLang['msj_baja_viaje']);	
	if(resp == true){
		enviar('bajaViaje');
	}
}

//-- Solapas -->
function getContenido(tipo){
	$('.tipo-listado, .tipo-historico, .tipo-cotizaciones, .tipo-pod').removeClass('active');
	$('#hidSolapa').val(tipo);
	
	if(tipo == 'listado'){
		$('#viajes-historico').hide();
		$('#viajes-cotizaciones').hide();
		$('#viajes-pod').hide();
	}
	else if(tipo == 'historico'){
		$('#viajes-listado').hide();
		$('#viajes-cotizaciones').hide();
		$('#viajes-pod').hide();
	}
	else if(tipo == 'cotizaciones'){
		$('#viajes-listado').hide();
		$('#viajes-historico').hide();
		$('#viajes-pod').hide();
	}
	else if(tipo == 'pod'){
		$('#viajes-listado').hide();
		$('#viajes-historico').hide();
		$('#viajes-cotizaciones').hide();
	}
	
	$('.tipo-'+tipo).addClass('active');
	$('#viajes-'+tipo).show();
}
//-- -->

function limpiarTable(row){
	//SE AGREGO ESTA LINEA PORQ EL SERVER DE "CANELA" AGREGA ESTE TAG Y ROMPE LISTADO DE VIAJES.  
	row = row.replace("<head/>", "");
	return row;	
}

///-- popup motivos de cambio --///
function agregarMotivoCambio(id_viaje){
	var request = $.ajax({ 
		url: urlAjax, 
		type: "POST",
		cache: false, 
		data:({
				accion:'popup-motivosCambios',
				id_viaje:id_viaje
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

function setMotivoCambio(id_viaje){
	var id_motivo = $('#motivo_name option:selected').val()
	
	if(parseInt(id_motivo) > 0){	
		$('#id_motivo_cambio').val(id_motivo);
		$("#help-modal").dialog("destroy");
		enviar('guardarM');
	}
	else{
		$('#motivo_error').show();
	}
}
///-- --///

//--Ini. Validar pallets
function validarPallets(){
	if($('#idperfil').val() == 27 && $('#hidOperacion').val() != 'modificar'){
		var ref = $("#id_geozonas").val().split(',');

		$tipo_viaje = null;
		//if($('#tipo_viaje').val() == 29){//--Entrega
			$tipo_viaje = 119; //Fabicante
		//}
		/*else if($('#tipo_viaje').val() == 30){//--Retiro
			$tipo_viaje = 120; //Cliente
		}*/


		if($('#tipo_viaje').val() != 30){//--Si el tipo de viajes es Retiro no se puede editar cant. de pallets
			for(i=0; i<ref.length; i++){
				if($('#re_rg_id_'+ref[i]).val() == $tipo_viaje){
					$('#pallets_stock_'+ref[i]).attr('disabled',true);
				}
				else{
					$('#pallets_stock_'+ref[i]).attr('disabled',false);
				}
			}
		}
	}
}
//--Fin.